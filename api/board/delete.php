<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
 
require_once '../db/db_connetion.php';
require_once '../model/bulletin-board.php';
 
$database = new Database();
$conn = $database->getConnection();

$board = new BulletinBoard($conn);

$data = json_decode(file_get_contents('php://input'));

$board->sequence = $data->sequence;

$status = array();

if($board->delete()) {
    http_response_code(200);
    $status = array('status'=> 'ok');
} else {
    http_response_code(503);
    $status = array('status'=> 'fail');
}

echo json_encode($status);
?>