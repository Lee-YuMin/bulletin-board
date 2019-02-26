<?php
require_once '../db/db_connetion.php';
require_once '../model/bulletin-board.php';
require_once '../management/header_info.php';
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents('php://input'));

if($_SESSION['token'] != $data->token) {
    http_response_code(511);
    return;
}

$database = new Database();
$conn = $database->getConnection();

$board = new BulletinBoard($conn);

$board->re_group = $data->re_group;
$board->re_order = $data->re_order;
$board->re_depth = $data->re_depth;

$status = array();

if($board->delete()) {
    http_response_code(200);
    echo json_encode(array('status'=> 'ok'));
}
?>