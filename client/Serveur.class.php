<?php
    namespace App;
    use App\Callback;

    class Serveur implements Callback{
        public function getUser()
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
            $clientId = CLIENT_ID_SERV;
            $clientSecret = CLIENT_SECRET_SERV;
            $redirectUri = REDIRECT_URI;
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
            return $result["firstname"];
        }
    }