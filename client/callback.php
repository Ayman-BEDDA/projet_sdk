<?php
session_start();
require_once 'src/config.php';
require_once 'src/oauthtwitch.php';

$oauth->set_headers($_SESSION['token']);

$query = $oauth->get_channel_info('803248979');
//$query = $oauth->get_id('hahaha931');
var_dump($query);
echo "test";


