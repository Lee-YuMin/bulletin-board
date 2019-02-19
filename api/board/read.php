<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
 
require_once '../db/db_connetion.php';
require_once '../model/bulletin-board.php';
 
$database = new Database();
$db = $database->getConnection();

$board = new BulletinBoard($db);

$stmt = $board->read();
$num = $stmt->rowCount();

// 리턴되는 값 : 1. 총 보드의 개수, 2. 페이징 숫자 만큼의 보드 리스트
$board_arr=array();
$board_arr['count'] = $board->count();

if($num>0){
    $board_arr['list']=array();
 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        extract($row);
 
        $board_item=array(
            'sequence'  => $sequence,
            'id'        => $id,
            'title'     => $title,
            'view_count'=> $view_count,
            'created_at'=> $created_at,
            're_group'  => $re_group,
            're_depth'  => $re_depth,
            'parent'    => $parent
        );
 
        array_push($board_arr['list'], $board_item);
    }
    
    http_response_code(200);
    echo json_encode($board_arr);
} else {
    $board_arr['list'] = [];
    http_response_code(404);
    echo json_encode($board_arr);
}
?>