<?php
namespace App;
use App\Discord;
use App\Serveur;
use App\Facebook;

session_start();
require_once 'src/config.php';
require_once 'src/oauthtwitch.php';



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
        <form class='form' action='callback' method='POST'>
            <input class='input' type='text' name='username' placeholder='Votre email'>
            <input class='input' type='text' name='password' placeholder='Votre mot de passe'>
            <input type='submit' value='Login'>
        </form>
    ";


    //lien de connection à discord
    
    foreach(PROVIDER as $key => $val){
        $url = array_keys($val)[0];
        $queryParams = http_build_query($val[$url]);
        $url = $url . $queryParams;
        echo "<div class='container'>
                <a class='button' href=\"" . $url . "\">Connection via " . $key . "</a><br>
            </div>";
            
    }

}

function callback(){
    $serv = new Serveur();
    echo "hello " . $serv->getUser();
}

function fbcallback(){
    $fb = new Facebook();
    echo "hello " . $fb->getUser();
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
?>
<style>
    .form , .container{
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .button{
        margin: 10px;
    }

    .form input{
        margin: 10px;
        border: 1px solid black;
    }
    
    .form{
        margin-top: 10rem;
    }

    input[type=submit]{
        background-color: #000080;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    input[type=submit]:hover {
        background-color:  	#4682B4;
    }

    .input{
        width: 30%;
        height: 40px;
    }

    .button{
        justify-content: center;
        border: black solid 2px;
        border-radius: 3%;
        padding: 10px;
        text-decoration: none;
        color: white;
        background-color: #000080;
        width: 14%;
        text-align: center;
    }

    .button:hover {
        background-color:  	#4682B4;
    }


</style> 