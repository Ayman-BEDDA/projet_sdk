<?php
    //url
    define("PROVIDER", [
        "Oauth Server" => [
            "http://localhost:8080/auth?" =>  [
                "client_id" => "621e3b8d1f964",
                "redirect_uri" => "http://localhost:8081/callback",
                "response_type" => "code",
                "scope" => "read,write",
                "state" => bin2hex(random_bytes(16))
            ]          
        ],
        "Facebook" => [
            "https://www.facebook.com/v2.10/dialog/oauth?" =>  [
                "client_id" => "1010755216459252",
                "redirect_uri" => "http://localhost:8081/fb_callback",
                "response_type" => "code",
                "scope" => "public_profile,email",
                "state" => bin2hex(random_bytes(16))
            ]          
        ],
        "discord" => [
            "https://discord.com/api/oauth2/authorize?" =>  [
                "client_id" => "988725359518294016",
                "redirect_uri" => "http://localhost:8081/ds_callback",
                "response_type" => "code",
                "scope" => "identify guilds",
                "state" => bin2hex(random_bytes(16))
            ]          
        ]
    ]);

    //REDIRECT URI
    define('REDIRECT_URI', 'http://localhost:8081/callback');
    //DISCORD
    define('CLIENT_ID_DS', '988725359518294016');
    define('CLIENT_SECRET_DS', 'woE63_1JETD4Y3XXyKK6c5SXvJTJYEIx');
    define('REDIRECT_URI_DS', 'http://localhost:8081/ds_callback');
    //SERVEUR
    define('CLIENT_ID_SERV', '621e3b8d1f964');
    define('CLIENT_SECRET_SERV', '621e3b8d1f966');
    define('REDIRECT_URI_SERV', 'http://localhost:8081/ds_callback');
