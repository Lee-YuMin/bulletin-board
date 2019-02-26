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
$db = $database->getConnection();

$board = new BulletinBoard($db);

// NOT NULL 데이터 체크
if( empty($data->id) || empty($data->password) || empty($data->title)) {
    // 데이터 유효성 검사 실패 시 리턴
    http_response_code(400);
    echo json_encode(array('status' => 'fail'));
}
    
// 유효성 검사 통과시 넘어온 데이터로 board 객체 생성
$board->id        = $data->id;
$board->password  = $data->password;
$board->email     = $data->email;
$board->title     = $data->title;
$board->content   = $data->content;
$board->ip_add    = $data->ip_add;
$board->parentSeq = property_exists($data, 'parentSeq') ? $data->parentSeq : null;  // parentSeq이 있다면 답변 글, 없다면 원글

if($board->create()){
    http_response_code(201);
    echo json_encode(array('status' => 'ok'));
}
?>