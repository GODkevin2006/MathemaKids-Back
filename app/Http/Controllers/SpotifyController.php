<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class SpotifyController extends Controller
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;

    public function __construct()
    {
        // Usar valores del archivo de configuración (mejor práctica)
        $this->clientId = config('spotify.client_id');
        $this->clientSecret = config('spotify.client_secret');
        $this->redirectUri = config('spotify.redirect_uri');

        // Log solo en desarrollo
        if (config('app.debug')) {
            logger('=== SPOTIFY CONTROLLER INICIADO ===');
            logger('Client ID: ' . $this->clientId);
            logger('Redirect URI: ' . $this->redirectUri);
        }
    }

    /**
     * Redirige al usuario a la página de autorización de Spotify
     */
    public function login(Request $request)
    {
        $scope = 'user-read-private user-read-email playlist-read-private playlist-read-collaborative playlist-modify-public playlist-modify-private';
        
        $state = bin2hex(random_bytes(16));
        Session::put('spotify_state', $state); // Guardar el state para verificarlo después
        
        $queryParams = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => $scope,
            'state' => $state,
            'show_dialog' => $request->get('show_dialog', false)
        ];

        $url = 'https://accounts.spotify.com/authorize?' . http_build_query($queryParams);

        // Para API/JSON: devolver la URL
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'auth_url' => $url,
                'message' => '🔑 Copia esta URL y ábrela en tu NAVEGADOR',
                'instructions' => [
                    '1. Copia la URL de "auth_url"',
                    '2. Pégala en tu navegador',
                    '3. Inicia sesión en Spotify y autoriza la app',
                    '4. Serás redirigido automáticamente al callback',
                    '5. Usa GET /api/spotify/session-status para verificar'
                ],
                'endpoints' => [
                    'check_session' => 'GET /api/spotify/session-status',
                    'get_profile' => 'GET /api/spotify/me',
                    'get_playlists' => 'GET /api/spotify/playlists'
                ]
            ]);
        }

        // Para web: redirigir directamente
        return redirect($url);
    }

    /**
     * Maneja el callback de Spotify y obtiene el access token
     */
    public function callback(Request $request)
    {
        if (config('app.debug')) {
            logger('=== CALLBACK INICIADO ===');
            logger('Parámetros recibidos: ' . json_encode($request->all()));
        }
        
        $code = $request->get('code');
        $error = $request->get('error');
        $state = $request->get('state');
        
        // Verificar el state para prevenir CSRF
        $savedState = Session::get('spotify_state');
        if ($state !== $savedState) {
            logger('❌ State no coincide. CSRF detectado o sesión expirada');
            return $this->respondWithError('Estado de seguridad inválido. Por favor, inicia el proceso de nuevo.', 400);
        }
        
        // Manejo de errores de Spotify
        if ($error) {
            $errorMessage = match($error) {
                'access_denied' => 'El usuario canceló la autorización',
                'invalid_scope' => 'Los permisos solicitados no son válidos',
                'invalid_request' => 'La solicitud es inválida',
                default => 'Error de Spotify: ' . $error
            };

            return $this->respondWithError($errorMessage, 400);
        }
        
        if (!$code) {
            return $this->respondWithError('No se recibió código de autorización', 400);
        }

        try {
            if (config('app.debug')) {
                logger('Intercambiando código por token...');
            }

            $tokenData = $this->getAccessToken($code);
            
            // Guardar token en sesión con timestamps
            $expiresAt = now()->addSeconds($tokenData['expires_in']);
            
            Session::put('spotify_token', $tokenData['access_token']);
            Session::put('spotify_refresh_token', $tokenData['refresh_token']);
            Session::put('spotify_expires_at', $expiresAt);
            Session::put('spotify_token_type', $tokenData['token_type']);
            Session::put('spotify_scope', $tokenData['scope'] ?? '');
            Session::forget('spotify_state'); // Limpiar el state usado

            if (config('app.debug')) {
                logger('✅ Token guardado exitosamente');
                logger('Expira en: ' . $expiresAt->toDateTimeString());
            }

            // Si es petición API, retornar JSON
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => '🎉 ¡Autenticación con Spotify exitosa!',
                    'token_info' => [
                        'expires_at' => $expiresAt->toDateTimeString(),
                        'expires_in_seconds' => $tokenData['expires_in'],
                        'token_type' => $tokenData['token_type'],
                        'scope' => $tokenData['scope'] ?? 'all requested scopes'
                    ],
                    'next_steps' => [
                        '1. Verificar sesión' => 'GET /api/spotify/session-status',
                        '2. Obtener perfil' => 'GET /api/spotify/me',
                        '3. Ver playlists' => 'GET /api/spotify/playlists'
                    ]
                ], 200);
            }

            // Para web, redirigir a una página de éxito o dashboard
            return redirect()->route('spotify.dashboard')->with('success', '¡Autenticación exitosa con Spotify!');

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $errorBody = json_decode($response->getBody()->getContents(), true);

            logger('❌ Error HTTP ' . $statusCode . ': ' . json_encode($errorBody));
            
            $errorMessage = 'Error al obtener el token de acceso. ' . 
                           ($errorBody['error_description'] ?? 'Por favor, intenta nuevamente.');
            
            return $this->respondWithError($errorMessage, 400);

        } catch (\Exception $e) {
            logger('❌ Error general en callback: ' . $e->getMessage());
            logger('Stack trace: ' . $e->getTraceAsString());
            
            return $this->respondWithError('Error inesperado al procesar la autenticación', 500);
        }
    }

    /**
     * Responde con un error de forma consistente
     */
    private function respondWithError($message, $code = 400)
    {
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'error' => $message,
                'solution' => 'Intenta iniciar sesión nuevamente: GET /api/spotify/login'
            ], $code);
        }

        return redirect()->route('home')->with('error', $message);
    }

    /**
     * Intercambia el código por un access token
     */
    private function getAccessToken($code)
    {
        $client = new Client([
            'timeout' => 30,
            'connect_timeout' => 10
        ]);
        
        $response = $client->post('https://accounts.spotify.com/api/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ],
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Refresca el access token cuando expira
     */
    private function refreshAccessToken()
    {
        $refreshToken = Session::get('spotify_refresh_token');

        if (!$refreshToken) {
            logger('❌ No hay refresh token disponible');
            return null;
        }

        try {
            if (config('app.debug')) {
                logger('🔄 Refrescando access token...');
            }

            $client = new Client(['timeout' => 30]);
            
            $response = $client->post('https://accounts.spotify.com/api/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ],
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ]);

            $tokenData = json_decode($response->getBody(), true);
            
            // Actualizar token en sesión
            $expiresAt = now()->addSeconds($tokenData['expires_in']);
            Session::put('spotify_token', $tokenData['access_token']);
            Session::put('spotify_expires_at', $expiresAt);

            // Spotify a veces envía un nuevo refresh_token
            if (isset($tokenData['refresh_token'])) {
                Session::put('spotify_refresh_token', $tokenData['refresh_token']);
            }

            if (config('app.debug')) {
                logger('✅ Token refrescado exitosamente');
            }

            return $tokenData['access_token'];

        } catch (\Exception $e) {
            logger('❌ Error refrescando token: ' . $e->getMessage());
            
            // Limpiar sesión si el refresh token es inválido
            Session::forget(['spotify_token', 'spotify_refresh_token', 'spotify_expires_at']);
            
            return null;
        }
    }

    /**
     * Obtiene el token válido (refresca automáticamente si es necesario)
     */
    private function getValidToken()
    {
        if (!Session::has('spotify_token')) {
            return null;
        }

        $expiresAt = Session::get('spotify_expires_at');
        
        // Si no hay fecha de expiración, asumir que expiró
        if (!$expiresAt) {
            return $this->refreshAccessToken();
        }

        // Si expira en menos de 5 minutos, refrescar preventivamente
        if (now()->addMinutes(5)->greaterThan($expiresAt)) {
            if (config('app.debug')) {
                logger('⚠️ Token próximo a expirar, refrescando...');
            }
            return $this->refreshAccessToken();
        }

        return Session::get('spotify_token');
    }

    /**
     * Verifica que el usuario esté autenticado
     */
    private function checkAuth()
    {
        $token = $this->getValidToken();
        
        if (!$token) {
            throw new \Exception('No authenticated. Please login first: GET /api/spotify/login');
        }

        return $token;
    }

    /**
     * Maneja errores de la API de Spotify
     */
    private function handleSpotifyError(\Exception $e)
    {
        if ($e instanceof \GuzzleHttp\Exception\ClientException) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $errorBody = json_decode($response->getBody()->getContents(), true);

            $message = match($statusCode) {
                401 => 'Token inválido o expirado. Por favor inicia sesión nuevamente.',
                403 => 'No tienes permisos para realizar esta acción.',
                404 => 'Recurso no encontrado.',
                429 => 'Demasiadas peticiones. Intenta más tarde.',
                default => $errorBody['error']['message'] ?? 'Error de Spotify'
            };

            return response()->json([
                'error' => $message,
                'status_code' => $statusCode,
                'spotify_error' => $errorBody
            ], $statusCode);
        }

        return response()->json([
            'error' => 'Error inesperado',
            'message' => $e->getMessage()
        ], 500);
    }

    /**
     * Retorna el estado de la sesión
     */
    public function getSessionStatus()
    {
        $hasToken = Session::has('spotify_token');
        $hasRefreshToken = Session::has('spotify_refresh_token');
        $expiresAt = Session::get('spotify_expires_at');
        $expired = $expiresAt ? now()->greaterThan($expiresAt) : true;
        
        $status = [
            'authenticated' => $hasToken && !$expired,
            'has_token' => $hasToken,
            'has_refresh_token' => $hasRefreshToken,
            'token_type' => Session::get('spotify_token_type'),
            'scope' => Session::get('spotify_scope'),
            'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : null,
            'expired' => $expired,
        ];

        if ($expiresAt && !$expired) {
            $status['expires_in_minutes'] = now()->diffInMinutes($expiresAt);
            $status['expires_in_seconds'] = now()->diffInSeconds($expiresAt);
        }

        $status['message'] = $hasToken && !$expired ? 
            '✅ Sesión de Spotify activa' : 
            '❌ No hay sesión activa. Usa GET /api/spotify/login';

        return response()->json($status);
    }

    /**
     * Muestra el dashboard después de autenticarse
     */
    public function dashboard()
    {
        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            $response = $client->get('https://api.spotify.com/v1/me', [
                'headers' => ['Authorization' => 'Bearer ' . $token]
            ]);

            $user = json_decode($response->getBody(), true);

            return view('spotify.dashboard', ['user' => $user]);

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Debes iniciar sesión primero');
        }
    }

    /**
     * Obtiene el perfil del usuario
     */
    public function profile()
    {
        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            $response = $client->get('https://api.spotify.com/v1/me', [
                'headers' => ['Authorization' => 'Bearer ' . $token]
            ]);

            $user = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'profile' => $user
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'No authenticated. Please login first: GET /api/spotify/login') {
                return response()->json([
                    'error' => $e->getMessage(),
                    'authenticated' => false
                ], 401);
            }
            return $this->handleSpotifyError($e);
        }
    }

    /**
     * Obtiene todas las playlists del usuario
     */
    public function getPlaylists(Request $request)
    {
        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            $response = $client->get('https://api.spotify.com/v1/me/playlists', [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'query' => [
                    'limit' => $request->get('limit', 50),
                    'offset' => $request->get('offset', 0)
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'playlists' => $data['items'],
                'total' => $data['total'],
                'limit' => $data['limit'],
                'offset' => $data['offset'],
                'has_more' => $data['next'] !== null
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'No authenticated. Please login first: GET /api/spotify/login') {
                return response()->json(['error' => $e->getMessage()], 401);
            }
            return $this->handleSpotifyError($e);
        }
    }

    /**
     * Obtiene una playlist específica
     */
    public function getPlaylist($id)
    {
        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            $response = $client->get("https://api.spotify.com/v1/playlists/{$id}", [
                'headers' => ['Authorization' => 'Bearer ' . $token]
            ]);

            $playlist = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'playlist' => $playlist
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'No authenticated. Please login first: GET /api/spotify/login') {
                return response()->json(['error' => $e->getMessage()], 401);
            }
            return $this->handleSpotifyError($e);
        }
    }

    /**
     * Crea una nueva playlist
     */
    public function createPlaylist(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:300',
            'public' => 'boolean'
        ]);

        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            // Obtener user_id
            $userResponse = $client->get('https://api.spotify.com/v1/me', [
                'headers' => ['Authorization' => 'Bearer ' . $token]
            ]);
            $user = json_decode($userResponse->getBody(), true);

            // Crear playlist
            $response = $client->post("https://api.spotify.com/v1/users/{$user['id']}/playlists", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? '',
                    'public' => $validated['public'] ?? false
                ]
            ]);

            $playlist = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'message' => '✅ Playlist creada exitosamente',
                'playlist' => $playlist
            ], 201);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'No authenticated. Please login first: GET /api/spotify/login') {
                return response()->json(['error' => $e->getMessage()], 401);
            }
            return $this->handleSpotifyError($e);
        }
    }

    /**
     * Actualiza una playlist
     */
    public function updatePlaylist(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:300',
            'public' => 'nullable|boolean'
        ]);

        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            $client->put("https://api.spotify.com/v1/playlists/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => array_filter($validated)
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Playlist actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'No authenticated. Please login first: GET /api/spotify/login') {
                return response()->json(['error' => $e->getMessage()], 401);
            }
            return $this->handleSpotifyError($e);
        }
    }

    /**
     * Busca canciones
     */
    public function searchTracks(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1'
        ]);

        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            $response = $client->get('https://api.spotify.com/v1/search', [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'query' => [
                    'q' => $request->get('q'),
                    'type' => 'track',
                    'limit' => $request->get('limit', 20),
                    'offset' => $request->get('offset', 0),
                    'market' => $request->get('market', 'US')
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'query' => $request->get('q'),
                'tracks' => $data['tracks']['items'],
                'total' => $data['tracks']['total'],
                'limit' => $data['tracks']['limit'],
                'offset' => $data['tracks']['offset']
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'No authenticated. Please login first: GET /api/spotify/login') {
                return response()->json(['error' => $e->getMessage()], 401);
            }
            return $this->handleSpotifyError($e);
        }
    }

    /**
     * Agrega canciones a una playlist
     */
    public function addTracksToPlaylist(Request $request, $playlistId)
    {
        $validated = $request->validate([
            'uris' => 'required|array|min:1',
            'uris.*' => 'required|string|regex:/^spotify:track:[a-zA-Z0-9]+$/',
            'position' => 'nullable|integer|min:0'
        ]);

        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            $response = $client->post("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'uris' => $validated['uris'],
                    'position' => $validated['position'] ?? 0
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'message' => '✅ ' . count($validated['uris']) . ' canción(es) agregada(s) exitosamente',
                'snapshot_id' => $result['snapshot_id']
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'No authenticated. Please login first: GET /api/spotify/login') {
                return response()->json(['error' => $e->getMessage()], 401);
            }
            return $this->handleSpotifyError($e);
        }
    }

    /**
     * Elimina canciones de una playlist
     */
    public function removeTracksFromPlaylist(Request $request, $playlistId)
    {
        $validated = $request->validate([
            'uris' => 'required|array|min:1',
            'uris.*' => 'required|string|regex:/^spotify:track:[a-zA-Z0-9]+$/'
        ]);

        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            $tracks = array_map(fn($uri) => ['uri' => $uri], $validated['uris']);

            $response = $client->delete("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => ['tracks' => $tracks]
            ]);

            $result = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'message' => '✅ ' . count($validated['uris']) . ' canción(es) eliminada(s) exitosamente',
                'snapshot_id' => $result['snapshot_id']
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'No authenticated. Please login first: GET /api/spotify/login') {
                return response()->json(['error' => $e->getMessage()], 401);
            }
            return $this->handleSpotifyError($e);
        }
    }

    /**
     * Obtiene las canciones de una playlist
     */
    public function getPlaylistTracks($playlistId, Request $request)
    {
        try {
            $token = $this->checkAuth();
            $client = new Client();
            
            $response = $client->get("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'query' => [
                    'limit' => $request->get('limit', 50),
                    'offset' => $request->get('offset', 0),
                    'market' => $request->get('market', 'US')
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'tracks' => $data['items'],
                'total' => $data['total'],
                'limit' => $data['limit'],
                'offset' => $data['offset']
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'No authenticated. Please login first: GET /api/spotify/login') {
                return response()->json(['error' => $e->getMessage()], 401);
            }
            return $this->handleSpotifyError($e);
        }
    }

    /**
     * Cierra la sesión
     */
    public function logout()
    {
        Session::forget(['spotify_token', 'spotify_refresh_token', 'spotify_expires_at', 'spotify_token_type', 'spotify_scope', 'spotify_state']);

        return response()->json([
            'success' => true,
            'message' => '✅ Sesión de Spotify cerrada exitosamente'
        ]);
    }
}