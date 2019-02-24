<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
 
require_once '../db/db_connetion.php';
require_once '../model/bulletin-board.php';
 
$database = new Database();
$conn = $database->getConnection();

$board = new BulletinBoard($conn);

// 검색 조건을 위한 파라미터
$board->type = $_GET['type'];
$board->typeContent = $_GET['typeContent'];
$board->pageNum = $_GET['pageNum'];

// 리턴되는 값 : 1. 총 보드의 개수, 2. 페이징 숫자 만큼의 보드 리스트
$board_arr = array();
$board_arr['count'] = $board->count();
$board_arr['list'] = [];

$stmt = $board->read();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);

    $board_item = array(
        'sequence'  => $sequence,
        'id'        => $id,
        'title'     => $title,
        'view_count'=> $view_count,
        'created_at'=> $created_at,
        're_order'  => $re_order,
        're_depth'  => $re_depth,
        're_group'  => $re_group
    );

    array_push($board_arr['list'], $board_item);
}

http_response_code(200);
echo json_encode($board_arr);
?>