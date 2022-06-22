<?php

function login()
{
    //url pour accéder à la connection discord
    $discord_url = "https://discord.com/api/oauth2/authorize?client_id=988725359518294016&redirect_uri=http%3A%2F%2Flocalhost%3A8081%2Fds_callback&response_type=code&scope=identify%20guilds";
    $queryParams= http_build_query(array(
        "client_id" => "621e3b8d1f964",
        "redirect_uri" => "http://localhost:8081/callback",
        "response_type" => "code",
        "scope" => "read,write",
        "state" => bin2hex(random_bytes(16))
    ));
    echo "
        <form action='callback' method='POST'>
            <input type='text' name='username'>
            <input type='text' name='password'>
            <input type='submit' value='Login'>
        </form>
    ";
    echo "<a href=\"http://localhost:8080/auth?{$queryParams}\">Se connecter via Oauth Server</a><br/>";
    $queryParams= http_build_query(array(
        "client_id" => "1010755216459252",
        "redirect_uri" => "http://localhost:8081/fb_callback",
        "response_type" => "code",
        "scope" => "public_profile,email",
        "state" => bin2hex(random_bytes(16))
    ));
    echo "<a href=\"https://www.facebook.com/v2.10/dialog/oauth?{$queryParams}\">Se connecter via Facebook</a><br>";
    //lien de connection à discord
    echo "<a href=\"" . $discord_url . "\">Connection via Discord</a>";
}

function callback()
{
    if ($_SERVER["REQUEST_METHOD"] === 'POST') {
        $specifParams = [
            "grant_type" => "password",
            "username" => $_POST["username"],
            "password" => $_POST["password"]
        ];
    } else {
        $specifParams = [
            "grant_type" => "authorization_code",
            "code" => $_GET["code"],
        ];
    }
    $clientId = "621e3b8d1f964";
    $clientSecret = "621e3b8d1f966";
    $redirectUri = "http://localhost:8081/callback";
    $data = http_build_query(array_merge([
        "redirect_uri" => $redirectUri,
        "client_id" => $clientId,
        "client_secret" => $clientSecret
    ], $specifParams));
    $url = "http://oauth-server:8080/token?{$data}";
    $result = file_get_contents($url);
    $result = json_decode($result, true);
    $accessToken = $result['access_token'];

    $url = "http://oauth-server:8080/me";
    $options = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $accessToken
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result, true);
    echo "Hello {$result['lastname']}";
}

function fbcallback()
{
    $specifParams = [
            "grant_type" => "authorization_code",
            "code" => $_GET["code"],
        ];
    $clientId = "1010755216459252";
    $clientSecret = "b0c27b63308d46ae5d236d2bd691921b";
    $redirectUri = "http://localhost:8081/fb_callback";
    $data = http_build_query(array_merge([
        "redirect_uri" => $redirectUri,
        "client_id" => $clientId,
        "client_secret" => $clientSecret
    ], $specifParams));
    $url = "https://graph.facebook.com/v2.10/oauth/access_token?{$data}";
    $result = file_get_contents($url);
    $result = json_decode($result, true);
    $accessToken = $result['access_token'];

    $url = "https://graph.facebook.com/v2.10/me";
    $options = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $accessToken
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result, true);
    echo "Hello {$result['name']}";
}

function dscallback(){
    //Verif si le code de discord est présent
    if(!isset($_GET['code'])){
        echo 'no code';
        die();
    }
    $ds_code = $_GET['code'];

    $payload = [
        'code' => $ds_code,
        'client_id' => '988725359518294016',
        'client_secret' => 'woE63_1JETD4Y3XXyKK6c5SXvJTJYEIx',
        'grant_type' => 'authorization_code',
        'redirect_uri' => 'http://localhost:8081/ds_callback',
        'scope' => 'identify guids',
    ];

    $payload_str = http_build_query($payload);
    $discord_token_url = "https://discord.com/api/v10/oauth2/token";

    $options = [
        'http' => [
            'header'  => "Accept: application/json\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($payload_str) . "\r\n",
            'method'  => 'POST',
            'content' => $payload_str
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($discord_token_url, false, $context);
    if ($result === FALSE) { 
        echo "une erreur est survenue";
    }
}

$route = $_SERVER['REQUEST_URI'];
switch (strtok($route, "?")) {
    case '/login':
        login();
        break;
    case '/callback':
        callback();
        break;
    case '/fb_callback':
        fbcallback();
        break;
    case '/ds_callback':
        dscallback();
        break;
    default:
        echo '404';
        break;
}
