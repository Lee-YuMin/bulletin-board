<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
 
require_once '../db/db_connetion.php';
require_once '../model/bulletin-board.php';
 
if($_SESSION['token'] != $_GET['token']) {
    http_response_code(511);
    return;
}

$database = new Database();
$conn = $database->getConnection();

$board = new BulletinBoard($conn);

$board->sequence = $_GET['sequence'] ? $_GET['sequence'] : die();

$board->viewCountIncrease();
$board->detail();

if(!is_null($board->sequence)) {
    $board_detail = array(
        'sequence'   => $board->sequence,
        'id'         => $board->id,
        'email'      => $board->email,
        'title'      => $board->title,
        'content'    => $board->content,
        'ip_add'     => $board->ip_add,
        're_group'   => $board->re_group,
        're_order'   => $board->re_order,
        're_depth'   => $board->re_depth,
        'created_at' => $board->created_at
    );
    
    http_response_code(200);
    echo json_encode($board_detail);
} else {
    http_response_code(404);
    echo json_encode(array("status"=>"fail"));
}
?>