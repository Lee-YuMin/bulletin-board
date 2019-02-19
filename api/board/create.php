<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
 
require_once '../db/db_connetion.php';
require_once '../model/bulletin-board.php';
 
$database = new Database();
$db = $database->getConnection();

$board = new BulletinBoard($db);

// POST 데이터 받기
$data = json_decode(file_get_contents("php://input"));


echo var_dump($data);
// NOT NULL 데이터 체크
if(
    !empty($data->id) &&
    !empty($data->pasword) &&
    !empty($data->title)
) {
    $board->id = $data->id;
    $board->password = $data->password;
    $board->email = $data->email;
    $board->title = $data->title;
    $board->contents = $data->contents;
    $board->ip = $data->ip;

    if($board.create()){
        // INSERT 성공
        echo json_encode(array('status' => 'ok')); 
        http_response_code(201);
    } else { 
        // INSERT 실패
        http_response_code(503);
        echo json_encode(array('status' => 'fail'));
    }
} else {
    // 데이터 유효성 검사 실패 시
    http_response_code(400);
    echo json_encode(array('status' => 'fail'));
}

?>