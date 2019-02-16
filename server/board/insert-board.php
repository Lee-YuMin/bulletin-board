<?php
require_once '../db/db_connetion.php';

// $id = $_POST['id'];	
// $password = $_POST['password'];
// var_dump($id, $password)

var_dump($connect->query('SELECT * FROM board'));
?>