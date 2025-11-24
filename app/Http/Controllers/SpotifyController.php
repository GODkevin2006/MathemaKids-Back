<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class SpotifyController extends Controller
{
    // ================================================
    // 1. LOGIN → URL DE AUTORIZACIÓN
    // ================================================
    public function login()
    {
        $clientId = config('spotify.client_id');
        $redirectUri = config('spotify.redirect_uri');
        $scopes = implode(' ', config('spotify.scopes'));

        $state = bin2hex(random_bytes(16));
        Session::put('spotify_state', $state);

        $authUrl = "https://accounts.spotify.com/authorize?" . http_build_query([
            'response_type' => 'code',
            'client_id'     => $clientId,
            'scope'         => $scopes,
            'redirect_uri'  => $redirectUri,
            'state'         => $state,
        ]);

        return response()->json(['url' => $authUrl]);
    }

    // ================================================
    // 2. CALLBACK DESDE SPOTIFY
    // ================================================
    public function callback(Request $request)
    {
        if ($request->state !== Session::get('spotify_state')) {
            return response()->json(['error' => 'Invalid state'], 400);
        }

        if ($request->has('error')) {
            return response()->json(['error' => $request->error], 400);
        }

        try {
            $client = new Client();

            $response = $client->post('https://accounts.spotify.com/api/token', [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'code'          => $request->code,
                    'redirect_uri'  => config('spotify.redirect_uri'),
                    'client_id'     => config('spotify.client_id'),
                    'client_secret' => config('spotify.client_secret'),
                ],
            ]);

            $tokens = json_decode($response->getBody(), true);

            Session::put('spotify_access_token', $tokens['access_token']);
            Session::put('spotify_refresh_token', $tokens['refresh_token']);

            // 🔥 Este redirecciona a tu frontend (React)
            return redirect('http://localhost:3000/spotify/success');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token exchange failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // ================================================
    // 3. RENOVAR EL TOKEN AUTOMÁTICAMENTE
    // ================================================
    private function refreshToken()
    {
        $refreshToken = Session::get('spotify_refresh_token');
        if (!$refreshToken) return false;

        try {
            $client = new Client();

            $response = $client->post('https://accounts.spotify.com/api/token', [
                'form_params' => [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'client_id'     => config('spotify.client_id'),
                    'client_secret' => config('spotify.client_secret'),
                ],
            ]);

            $tokens = json_decode($response->getBody(), true);

            if (isset($tokens['access_token'])) {
                Session::put('spotify_access_token', $tokens['access_token']);
                return $tokens['access_token'];
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    // ================================================
    // 4. OBTENER TOKEN VÁLIDO
    // ================================================
    private function getToken()
    {
        $token = Session::get('spotify_access_token');
        return $token ?: null;
    }

    // ================================================
    // 5. ESTADO DE SESIÓN
    // ================================================
    public function getSessionStatus()
    {
        return response()->json([
            'logged_in' => Session::has('spotify_access_token'),
        ]);
    }

    // ================================================
    // 6. PERFIL DEL USUARIO
    // ================================================
    public function profile()
    {
        return $this->spotifyGet('https://api.spotify.com/v1/me');
    }

    // ================================================
    // 7. DASHBOARD (player, email, imagen)
    // ================================================
    public function dashboard()
    {
        return $this->spotifyGet('https://api.spotify.com/v1/me');
    }

    // Utilidad general para GET con renovación automática
    private function spotifyGet($url)
    {
        $token = $this->getToken();
        if (!$token) return response()->json(['error' => 'No token'], 401);

        try {
            $client = new Client();
            $response = $client->get($url, [
                'headers' => ['Authorization' => "Bearer $token"],
            ]);

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), '401')) {
                if ($this->refreshToken()) {
                    return $this->spotifyGet($url);
                }
            }
            return response()->json(['error' => 'Spotify API failed'], 500);
        }
    }

    // ================================================
    // 8. PLAYLISTS
    // ================================================
    public function getPlaylists()
    {
        return $this->spotifyGet('https://api.spotify.com/v1/me/playlists');
    }

    public function getPlaylist($id)
    {
        return $this->spotifyGet("https://api.spotify.com/v1/playlists/$id");
    }

    public function getPlaylistTracks($playlistId)
    {
        return $this->spotifyGet("https://api.spotify.com/v1/playlists/$playlistId/tracks");
    }

    // ================================================
    // 9. CREAR / EDITAR / AGREGAR CANCIONES
    // ================================================
    public function createPlaylist(Request $request)
    {
        return $this->spotifyPost(
            'https://api.spotify.com/v1/me/playlists',
            ['name' => $request->name]
        );
    }

    public function updatePlaylist($id, Request $request)
    {
        return $this->spotifyPut(
            "https://api.spotify.com/v1/playlists/$id",
            ['name' => $request->name]
        );
    }

    public function addTracksToPlaylist(Request $request, $playlistId)
    {
        return $this->spotifyPost(
            "https://api.spotify.com/v1/playlists/$playlistId/tracks",
            ['uris' => $request->uris]
        );
    }

    public function removeTracksFromPlaylist(Request $request, $playlistId)
    {
        return $this->spotifyDelete(
            "https://api.spotify.com/v1/playlists/$playlistId/tracks",
            ['tracks' => $request->tracks]
        );
    }

    // HTTP helpers
    private function spotifyPost($url, $json)
    {
        return $this->spotifyRequest('POST', $url, $json);
    }

    private function spotifyPut($url, $json)
    {
        return $this->spotifyRequest('PUT', $url, $json);
    }

    private function spotifyDelete($url, $json)
    {
        return $this->spotifyRequest('DELETE', $url, $json);
    }

    private function spotifyRequest($method, $url, $json)
    {
        $token = $this->getToken();
        if (!$token) return response()->json(['error' => 'No token'], 401);

        try {
            $client = new Client();
            $response = $client->request($method, $url, [
                'headers' => ['Authorization' => "Bearer $token"],
                'json'    => $json
            ]);

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Spotify API error'], 500);
        }
    }

    // ================================================
    // 10. LOGOUT
    // ================================================
    public function logout()
    {
        Session::forget(['spotify_access_token', 'spotify_refresh_token']);
        return response()->json(['message' => 'Logged out']);
    }
}
