<?php

    define('API_LINK', 'https://api.twitch.tv/helix');
    
    class OAuthTwitch{
        private $_client_id;
        private $_client_secret;
        private $_redirect_uri;
        private $_scope;
        private $_token;
        private $_headers = [];

        public function __construct($client_id, $client_secret, $redirect_uri, $scope){
            $this->_client_id = $client_id;
            $this->_client_secret = $client_secret;
            $this->_redirect_uri = $redirect_uri;
            $this->_scope = $scope;
        }

        public function get_token($code){
            // Lien pour avoir le token
            $link = "https://id.twitch.tv/oauth2/token?client_id=".$this->_client_id."&client_secret=".$this->_client_secret."&code=".$code."&grant_type=authorization_code&redirect_uri=".$this->_redirect_uri;
            // Request cURL POST pour get le token
            $ch = curl_init($link);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $res = curl_exec($ch);
            curl_close($ch);

            // Decode
            $token = json_decode($res);
            // On place le token en attribut privée 
            $this->_token = $token;

            // On return le token
            return $token->access_token;
        }
        public function set_headers($token){
            $this->_headers = [
                'Authorization: Bearer '.$token,
                'Client-Id: '.$this->_client_id
            ];
        }

        public function get_channel_info($id){
            $url = API_LINK.'/users?id='.$id;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($ch);
            curl_close($ch);
            $channel = json_decode($res);
            return $channel;
        } 

        public function get_id($pseudo){
            $url = API_LINK.'/users?login='.$pseudo;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($ch);
            curl_close($ch);
            $user = json_decode($res);
            return $user;
        }
    }