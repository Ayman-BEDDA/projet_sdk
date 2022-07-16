<?php
session_start();
require_once 'src/config.php';
require_once 'src/oauthtwitch.php';

$oauth->set_headers($_SESSION['token']);

$query = $oauth->search_channel('hahaha931');
var_dump($query);