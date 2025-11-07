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
        $this->clientId = '5c0cce52ee954bdbb93807a38414a453';
        $this->clientSecret = 'ee71aca6e5934b2f9b49c066ff8e4a42';
        $this->redirectUri = 'http://127.0.0.1:8000/spotify/callback';

        logger('=== SPOTIFY CONTROLLER INICIADO ===');
        logger('Client ID: ' . $this->clientId);
        logger('Redirect URI: ' . $this->redirectUri);
    }

    /**
     * Redirige al usuario a la página de autorización de Spotify
     */
    public function login(Request $request)
    {
        $scope = 'user-read-private user-read-email playlist-read-private playlist-read-collaborative playlist-modify-public playlist-modify-private';
        
        $queryParams = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => $scope,
            'state' => bin2hex(random_bytes(16)),
            'show_dialog' => true
        ];

        $url = 'https://accounts.spotify.com/authorize?' . http_build_query($queryParams);

        // Para Postman/API: devolver JSON con la URL
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'auth_url' => $url,
                'message' => '🔑 Copia esta URL y ábrela en tu NAVEGADOR',
                'instructions' => [
                    '1' => 'Copia la URL de "auth_url"',
                    '2' => 'Pégala en tu navegador',
                    '3' => 'Inicia sesión en Spotify y autoriza la app',
                    '4' => 'Serás redirigido automáticamente al callback',
                    '5' => 'Revisa la respuesta en el navegador o usa /session-status'
                ],
                'debug' => [
                    'client_id' => $this->clientId,
                    'redirect_uri' => $this->redirectUri
                ]
            ]);
        }

        return redirect($url);
    }

    /**
     * Endpoint específico para obtener solo la URL de auth (sin redirección)
     */
    public function getAuthUrl(Request $request)
    {
        $scope = 'user-read-private user-read-email playlist-read-private playlist-read-collaborative playlist-modify-public playlist-modify-private';
        
        $url = 'https://accounts.spotify.com/authorize?' . http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => $scope,
            'state' => bin2hex(random_bytes(16)),
            'show_dialog' => $request->get('show_dialog', true)
        ]);

        return response()->json([
            'auth_url' => $url,
            'scopes' => explode(' ', $scope),
            'redirect_uri' => $this->redirectUri,
            'client_id' => $this->clientId,
            'note' => 'Usa esta URL en tu frontend o redirige al usuario a esta URL'
        ]);
    }

    /**
     * Maneja el callback de Spotify y obtiene el access token
     */
    public function callback(Request $request)
    {
        logger('=== CALLBACK INICIADO ===');
        
        $code = $request->get('code');
        $error = $request->get('error');
        
        if ($error) {
            return response()->json([
                'error' => 'Error de Spotify: ' . $error,
                'description' => 'El usuario denegó el acceso o hubo un error'
            ], 400);
        }
        
        if (!$code) {
            return response()->json([
                'error' => 'No se recibió código de autorización',
                'received_params' => $request->all()
            ], 400);
        }

        try {
            logger('Intercambiando código por token...');
            $token = $this->getAccessToken($code);
            
            // Guardar token en sesión
            Session::put('spotify_token', $token['access_token']);
            Session::put('spotify_refresh_token', $token['refresh_token']);
            Session::put('spotify_expires_at', now()->addSeconds($token['expires_in']));

            logger('✅ Token obtenido exitosamente');

            return response()->json([
                'success' => true,
                'message' => '🎉 ¡Autenticación con Spotify exitosa!',
                'token_info' => [
                    'access_token' => substr($token['access_token'], 0, 20) . '...',
                    'token_type' => $token['token_type'],
                    'expires_in' => $token['expires_in'],
                    'scope' => $token['scope'] ?? 'default'
                ],
                'next_steps' => [
                    'check_session' => 'GET /api/spotify/session-status',
                    'get_profile' => 'GET /api/spotify/me',
                    'get_playlists' => 'GET /api/spotify/playlists'
                ],
                'session_verified' => Session::has('spotify_token')
            ]);

        } catch (\Exception $e) {
            logger('❌ Error en callback: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'No se pudo obtener el token de acceso',
                'message' => $e->getMessage(),
                'possible_causes' => [
                    'El código expiró (vuelve a generar uno nuevo)',
                    'Credenciales de Spotify incorrectas',
                    'Problema de conexión con Spotify'
                ],
                'solution' => 'Ejecuta /api/spotify/login nuevamente para obtener un nuevo código'
            ], 500);
        }
    }

    /**
     * Intercambia el código por un access token
     */
    private function getAccessToken($code)
    {
        $client = new Client(['timeout' => 30]);
        
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
     * Refresha el access token cuando expira
     */
    private function refreshAccessToken()
    {
        $refreshToken = Session::get('spotify_refresh_token');

        if (!$refreshToken) {
            return null;
        }

        try {
            $client = new Client();
            
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

            $token = json_decode($response->getBody(), true);
            
            Session::put('spotify_token', $token['access_token']);
            Session::put('spotify_expires_at', now()->addSeconds($token['expires_in']));

            // Actualizar refresh_token si viene uno nuevo
            if (isset($token['refresh_token'])) {
                Session::put('spotify_refresh_token', $token['refresh_token']);
            }

            return $token['access_token'];
        } catch (\Exception $e) {
            logger('Error refrescando token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene el token válido (refresha si es necesario)
     */
    private function getValidToken()
    {
        if (!Session::has('spotify_token')) {
            return null;
        }

        // Verificar si el token expiró
        if (Session::has('spotify_expires_at') && now()->greaterThan(Session::get('spotify_expires_at'))) {
            return $this->refreshAccessToken();
        }

        return Session::get('spotify_token');
    }

    /**
     * Verifica que el usuario esté autenticado
     */
    private function checkAuth($returnJson = false)
    {
        $token = $this->getValidToken();
        
        if (!$token) {
            if ($returnJson) {
                return response()->json(['error' => 'Debes iniciar sesión primero con Spotify'], 401);
            }
            return redirect()->route('spotify.login')->with('error', 'Debes iniciar sesión primero');
        }

        return $token;
    }

    /**
     * Retorna el estado de la sesión (para API)
     */
    public function getSessionStatus()
    {
        $hasToken = Session::has('spotify_token');
        $expiresAt = Session::get('spotify_expires_at');
        $expired = $expiresAt ? now()->greaterThan($expiresAt) : true;
        
        return response()->json([
            'authenticated' => $hasToken && !$expired,
            'has_token' => $hasToken,
            'has_refresh_token' => Session::has('spotify_refresh_token'),
            'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : null,
            'expired' => $expired,
            'message' => $hasToken && !$expired ? 
                '✅ Sesión de Spotify activa' : 
                '❌ No hay sesión activa de Spotify'
        ]);
    }

    /**
     * Muestra el perfil del usuario
     */
    public function profile(Request $request)
    {
        $token = $this->checkAuth($request->expectsJson());
        
        if ($token instanceof \Illuminate\Http\JsonResponse) {
            return $token;
        }

        try {
            $client = new Client();
            
            $response = $client->get('https://api.spotify.com/v1/me', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            $user = json_decode($response->getBody(), true);

            return response()->json([
                'profile' => $user,
                'message' => 'Perfil obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener el perfil',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene todas las playlists del usuario
     */
    public function getPlaylists(Request $request)
    {
        $token = $this->checkAuth($request->expectsJson());
        
        if ($token instanceof \Illuminate\Http\JsonResponse) {
            return $token;
        }

        try {
            $client = new Client();
            
            $response = $client->get('https://api.spotify.com/v1/me/playlists', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ],
                'query' => [
                    'limit' => $request->get('limit', 50),
                    'offset' => $request->get('offset', 0)
                ]
            ]);

            $playlists = json_decode($response->getBody(), true);

            return response()->json([
                'playlists' => $playlists,
                'total' => count($playlists['items'] ?? [])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener playlists',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene los detalles de una playlist específica
     */
    public function getPlaylist(Request $request, $id)
    {
        $token = $this->checkAuth($request->expectsJson());
        
        if ($token instanceof \Illuminate\Http\JsonResponse) {
            return $token;
        }

        try {
            $client = new Client();
            
            $response = $client->get("https://api.spotify.com/v1/playlists/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            $playlist = json_decode($response->getBody(), true);

            return response()->json([
                'playlist' => $playlist
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener la playlist',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea una nueva playlist
     */
    public function createPlaylist(Request $request)
    {
        $token = $this->checkAuth($request->expectsJson());
        
        if ($token instanceof \Illuminate\Http\JsonResponse) {
            return $token;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'public' => 'boolean'
        ]);

        try {
            $client = new Client();
            
            // Primero obtener el user_id
            $userResponse = $client->get('https://api.spotify.com/v1/me', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            $user = json_decode($userResponse->getBody(), true);
            $userId = $user['id'];

            // Crear la playlist
            $response = $client->post("https://api.spotify.com/v1/users/{$userId}/playlists", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => $request->name,
                    'description' => $request->description ?? '',
                    'public' => $request->public ?? false
                ]
            ]);

            $playlist = json_decode($response->getBody(), true);

            return response()->json([
                'message' => 'Playlist creada exitosamente',
                'playlist' => $playlist
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear la playlist',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza una playlist existente
     */
    public function updatePlaylist(Request $request, $id)
    {
        $token = $this->checkAuth($request->expectsJson());
        
        if ($token instanceof \Illuminate\Http\JsonResponse) {
            return $token;
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'public' => 'nullable|boolean'
        ]);

        try {
            $client = new Client();
            
            $response = $client->put("https://api.spotify.com/v1/playlists/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => array_filter([
                    'name' => $request->name,
                    'description' => $request->description,
                    'public' => $request->public
                ])
            ]);

            return response()->json(['message' => 'Playlist actualizada exitosamente']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar la playlist',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca canciones en Spotify
     */
    public function searchTracks(Request $request)
    {
        $token = $this->checkAuth($request->expectsJson());
        
        if ($token instanceof \Illuminate\Http\JsonResponse) {
            return $token;
        }

        $query = $request->get('q');

        if (!$query) {
            return response()->json(['error' => 'Query parameter "q" is required'], 400);
        }

        try {
            $client = new Client();
            
            $response = $client->get('https://api.spotify.com/v1/search', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ],
                'query' => [
                    'q' => $query,
                    'type' => 'track',
                    'limit' => $request->get('limit', 20),
                    'offset' => $request->get('offset', 0),
                    'market' => $request->get('market', 'US')
                ]
            ]);

            $results = json_decode($response->getBody(), true);

            return response()->json([
                'tracks' => $results['tracks'],
                'query' => $query,
                'total_results' => $results['tracks']['total'] ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en la búsqueda',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agrega canciones a una playlist
     */
    public function addTracksToPlaylist(Request $request, $playlistId)
    {
        $token = $this->checkAuth($request->expectsJson());
        
        if ($token instanceof \Illuminate\Http\JsonResponse) {
            return $token;
        }

        $request->validate([
            'uris' => 'required|array',
            'uris.*' => 'required|string|regex:/^spotify:track:[a-zA-Z0-9]+$/'
        ]);

        try {
            $client = new Client();
            
            $response = $client->post("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'uris' => $request->uris,
                    'position' => $request->get('position', 0)
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            return response()->json([
                'message' => 'Canciones agregadas exitosamente',
                'snapshot_id' => $result['snapshot_id']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al agregar canciones',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina canciones de una playlist
     */
    public function removeTracksFromPlaylist(Request $request, $playlistId)
    {
        $token = $this->checkAuth($request->expectsJson());
        
        if ($token instanceof \Illuminate\Http\JsonResponse) {
            return $token;
        }

        $request->validate([
            'uris' => 'required|array',
            'uris.*' => 'required|string|regex:/^spotify:track:[a-zA-Z0-9]+$/'
        ]);

        try {
            $client = new Client();
            
            $tracks = array_map(function($uri) {
                return ['uri' => $uri];
            }, $request->uris);

            $response = $client->delete("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'tracks' => $tracks
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            return response()->json([
                'message' => 'Canciones eliminadas exitosamente',
                'snapshot_id' => $result['snapshot_id']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar canciones',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene las canciones de una playlist
     */
    public function getPlaylistTracks(Request $request, $playlistId)
    {
        $token = $this->checkAuth($request->expectsJson());
        
        if ($token instanceof \Illuminate\Http\JsonResponse) {
            return $token;
        }

        try {
            $client = new Client();
            
            $response = $client->get("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ],
                'query' => [
                    'limit' => $request->get('limit', 50),
                    'offset' => $request->get('offset', 0),
                    'market' => $request->get('market', 'US')
                ]
            ]);

            $tracks = json_decode($response->getBody(), true);

            return response()->json([
                'tracks' => $tracks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener las canciones',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cierra la sesión de Spotify
     */
    public function logout(Request $request)
    {
        Session::forget('spotify_token');
        Session::forget('spotify_refresh_token');
        Session::forget('spotify_expires_at');

        return response()->json([
            'message' => 'Sesión de Spotify cerrada exitosamente',
            'session_cleared' => !Session::has('spotify_token')
        ]);
    }

    /**
     * Método de debug para verificar configuración
     */
    public function debugConfig()
    {
        return response()->json([
            'forced_values' => [
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
            ],
            'environment' => [
                'env_client_id' => env('SPOTIFY_CLIENT_ID'),
                'env_redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
            ],
            'configuration' => [
                'config_client_id' => config('spotify.client_id'),
                'config_redirect_uri' => config('spotify.redirect_uri'),
            ],
            'files' => [
                'config_file_exists' => file_exists(config_path('spotify.php')),
                'env_file_exists' => file_exists(base_path('.env')),
            ]
        ]);
    }
}