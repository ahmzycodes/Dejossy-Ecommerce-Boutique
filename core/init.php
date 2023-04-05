<?php

error_reporting(0);

$host = '127.0.0.1';
$user = 'root';
$pwd = '';
$dbname = 'dbtutorial';

$db = new mysqli($host, $user, $pwd, $dbname);

if($db->connect_error){
    die('Connection Failed: '.$db->connect_error);
}
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/config.php';
require_once BASEURL.'helpers/helpers.php';
require BASEURL.'vendor/autoload.php';



$cart_id ='';

if (isset($_COOKIE[CART_COOKIE])) {
    $cart_id = sanitize($_COOKIE[CART_COOKIE]);
}

if (isset($_SESSION['DJuser'])) {
    $user_id = $_SESSION['DJuser'];
    $query =$db->query("SELECT * FROM users WHERE id = '$user_id'");
    $user_data = $query->fetch_assoc();
    $fn = explode(' ',$user_data['full_name']);
    $user_data['first'] = $fn[0];
    $user_data['last'] = $fn[1];

}

if (isset($_SESSION['success_flash'])) {
    echo '<div class="bg-success"><p class="text-success text-center">'.$_SESSION['success_flash'].'</p></div>';
    unset($_SESSION['success_flash']);
}

if (isset($_SESSION['error_flash'])) {
    echo '<div class="bg-danger"><p class="text-danger text-center">'.$_SESSION['error_flash'].'</p></div>';
    unset($_SESSION['error_flash']);
}


?>