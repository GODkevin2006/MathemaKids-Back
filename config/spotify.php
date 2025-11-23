<?php

return [

    'client_id' => env('SPOTIFY_CLIENT_ID'),
    'client_secret' => env('SPOTIFY_CLIENT_SECRET'),

    'redirect_uri' => env('SPOTIFY_REDIRECT_URI', 'http://127.0.0.1:8000/spotify/callback'),

    'enabled' => env('SPOTIFY_ENABLED', true),

    'scopes' => [
        'user-read-email',
        'user-read-private',
        'playlist-read-private',
        'playlist-read-collaborative',
        'playlist-modify-public',
        'playlist-modify-private',
    ],

    'api_url' => 'https://api.spotify.com/v1',
    'accounts_url' => 'https://accounts.spotify.com',
];