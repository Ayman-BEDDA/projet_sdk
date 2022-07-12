<?php
    namespace App;
    use App\Callback;

    class Discord implements Callback{
        public function getUser()
        {
            $payload = [
                'code' => $_GET['code'],
                'client_id' => CLIENT_ID_DS,
                'client_secret' => CLIENT_SECRET_DS,
                'grant_type' => 'authorization_code',
                'redirect_uri' => REDIRECT_URI_DS,
                'scope' => 'identify guids',
            ];
            $payload_str = http_build_query($payload);
            $discord_token_url = "https://discord.com/api/oauth2/token";
        
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
            $result = json_decode($result, true);

            //GET USER
            $access_token = $result['access_token'];

            $user_url = 'https://discord.com/api/users/@me';

            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => 'Authorization: Bearer ' . $access_token
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($user_url, false, $context);
            $result = json_decode($result, true);
            return $result["username"];
        }
    }