<?php
$db = mysqli_connect('localhost:3306','root','root','freshdish');

if(mysqli_connect_error()){
  echo 'Database Connection failed with following errors: ' .mysqli_connect_error();
  die();
}

session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/freshdish/config.php';
require_once BASEURL.'helpers/helpers.php';

$cart_id = '';
if(isset($_COOKIE[CART_COOKIE])){
  $cart_id = sanitize($_COOKIE[CART_COOKIE]);
}

if(isset($_SESSION['success_flash'])){
  echo '<div class="bg-success"><p class="text-success text-center">'.$_SESSION['success_flash'].'</p></div>';
  unset($_SESSION['success_flash']);
}

if(isset($_SESSION['error_flash'])){
  echo '<div class="bg-danger"><p class="text-danger text-center">'.$_SESSION['error_flash'].'</p></div>';
  unset($_SESSION['error_flash']);
}
?>
