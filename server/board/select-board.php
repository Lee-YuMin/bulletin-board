<?php
require_once '../db/db_connetion.php';

$SELECT_LIMIT = 10;
$type = $_GET['type'];
$content = $_GET['content'];
$pageNum = $_GET['pageNum'];
$typeCondition;
$returnArr = [];

// 검색 조건이 있을시 조건 계산
if(isset($content)){
  if($type == 'title_content')
    $template = 'AND (title like "%$content%" OR content like "%$content%") ';
  else
    $template = 'AND $type like "%$content%" ';
  
  $match = array('$type'=>$type, '$content'=>$content);
  $typeCondition = strtr($template, $match);
}

// PAGING을 위한 카운트 개수
$pageSql= 'SELECT count(*) as count ';
$pageSql.= 'FROM board ';
$pageSql.= 'WHERE 1=1 ';
if(isset($typeCondition))
  $pageSql.= $typeCondition;

$stmt = $connect->prepare($pageSql);
$stmt->execute();
$countResult = $stmt->get_result();

$returnArr = array('count'=>$countResult->fetch_assoc()['count']);

// 몇 번째의 페이지 인지 계산 : (페이지 번호 - 1) * 한 페이지의 총 개수
$page = ($pageNum - 1) * $SELECT_LIMIT;

$sql = 'SELECT sequence, id, title, view_count, DATE_FORMAT(created_at, "%Y-%m-%d") as created_at, re_group, re_depth, parent ';
$sql.= 'FROM board ';
$sql.= 'WHERE 1=1 ';
if(isset($typeCondition))
  $sql.= $typeCondition;
$sql.= 'ORDER BY sequence DESC LIMIT ?, ?;';

$stmt = $connect->prepare($sql);

// TODO 매핑으로 수정해야함
$stmt->bind_param('ii', $page, $SELECT_LIMIT);

$stmt->execute();
$result = $stmt->get_result();

while($data = $result->fetch_assoc()){
    $arr[] = array('sequence'  =>$data['sequence'],
                   'id'        =>$data['id'],
                   'title'     =>$data['title'],
                   'view_count'=>$data['view_count'],
                   'created_at'=>$data['created_at'],
                   're_group'  =>$data['re_group'],
                   're_depth'  =>$data['re_depth'],
                   'parent'    =>$data['parent']);
}

$returnArr['list'] = $arr;

echo(json_encode($returnArr));
?>