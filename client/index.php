<?php
session_start();
require_once 'src/config.php';
require_once 'src/oauthtwitch.php';

$link = $oauth->get_link_connect();




function login()
{
    
    echo "
        <form action='callback' method='POST'>
            <input type='text' name='username'>
            <input type='text' name='password'>
            <input type='submit' value='Login'>
        </form>
    ";
    //echo "<a href=\"http://localhost:8080/auth?{$queryParams}\">Se connecter via Oauth Server</a><br/>";



    $url= http_build_query(array(
        "client_id" => "703009470765061",
        "redirect_uri" => "http://localhost:8081/fb_callback",
        "response_type" => "code",
        "scope" => "public_profile,email",
        "state" => bin2hex(random_bytes(16))
    ));
    echo "<a href=\"https://www.facebook.com/v2.10/dialog/oauth?" . $url . "\">Se connecter via Facebook</a>";


    global $link;

    echo "<br/><a href=".$link.">Se connecter via Twitch</a>";


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
    $clientId = "703009470765061";
    $clientSecret = "4caf6586eab19e1f015f6165a79fbb57";
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

function twcallback()
{
    if(!empty($_GET['code'])){

        global $oauth;
        $code = htmlspecialchars($_GET['code']);
        

        $token = $oauth->get_token($code);
    
        $_SESSION['token'] = $token;
        header('Location: callback.php');
        die();
    
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
    case '/tw_callback':
        twcallback();
        break;
    default:
        echo '404';
        break;
}
