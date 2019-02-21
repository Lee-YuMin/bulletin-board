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
$data = json_decode(file_get_contents('php://input'));

// NOT NULL 데이터 체크
if( !empty($data->id) && !empty($data->password) && !empty($data->title)) {
    
    // 유효성 검사 통과시 넘어온 데이터로 board 객체 생성
    $board->id       = $data->id;
    $board->password = $data->password;
    $board->email    = $data->email;
    $board->title    = $data->title;
    $board->content =  $data->content;
    $board->ip_add   = $data->ip_add;

    if($board->create()){
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