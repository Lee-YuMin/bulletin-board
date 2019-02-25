<?php
require_once '../management/header_info.php';
header("Access-Control-Allow-Methods: GET");

session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

echo json_encode(array('token'=>$token));
?>
