<?php
namespace App;
use App\Discord;
use App\Serveur;

session_start();
require_once 'src/config.php';
require_once 'src/oauthtwitch.php';

$link = $oauth->get_link_connect();



include 'conf.inc.php';

function myAutoloader($class)
{
    $class = str_ireplace('App\\',  '',$class);//On supprime "App\" de App\exemple\class.class.php
    $class = str_ireplace('\\', '/', $class);// 
    $class .= '.class.php';
    if(file_exists($class)){
        include $class;//On utilise include car plus rapide, et on a déjà vérifier son existance
    }else{
        die('Le fichier n\'existe pas');
    }
} 

spl_autoload_register('App\myAutoloader');

function login()
{
    //url pour accéder à la connection discord
    echo "
        <form action='callback' method='POST'>
            <input type='text' name='username'>
            <input type='text' name='password'>
            <input type='submit' value='Login'>
        </form>
    ";

    global $link;

    echo "<br/><a href=".$link.">Se connecter via Twitch</a>";


    //lien de connection à discord
    foreach(PROVIDER as $key => $val){
        $url = array_keys($val)[0];
        $queryParams = http_build_query($val[$url]);
        $url = $url . $queryParams;
        echo "<a href=\"" . $url . "\">Connection via " . $key . "</a><br>";
    }

}

function callback(){
    $serv = new Serveur();
    echo "hello " . $serv->getUser();
}

function fbcallback(){
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

function twcallback(){
    if(!empty($_GET['code'])){

        global $oauth;
        $code = htmlspecialchars($_GET['code']);
        

        $token = $oauth->get_token($code);
    
        $_SESSION['token'] = $token;
        header('Location: callback.php');
        die();
    }
}
    
function dscallback(){
    //Verif si le code de discord est présent
    if(!isset($_GET['code'])){
        echo 'no code';
        die();
    }
    $ds = new Discord();
    echo "hello " . $ds->getUser();
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
    case '/ds_callback':
        dscallback();
        break;
    default:
        echo '404';
        break;
}