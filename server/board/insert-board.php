<?php
require_once '../db/db_connetion.php';

// 최대 시퀀스 구하기
$stmt = $connect->prepare('SELECT * FROM (SELECT MAX(seqeunce)+1 as seqeunce FROM board) tmp');
$stmt->execute();
$result = $stmt->get_result();

$seqeunce = $result->fetch_assoc()['seqeunce'];

// 데이터 삽입
$sql = 'INSERT INTO mysite.board';
$sql.= '(id, email, password, title, content, file_name, ip_add, view_count, re_group, re_depth, parent, created_at, updated_at)';
$sql.= 'VALUES(?, ?, ?, ?, ?, "", ?, 0, ?, 0, 0, sysdate(), sysdate());';

$stmt = $connect->prepare($sql);

$stmt->bind_param("ssssssi", $_GET['id'],$_GET['password'],$_GET['email'],$_GET['title'],$_GET['contents'],$_GET['ip'],$seqeunce);

$stmt->execute();

if($connect->error)
    echo(json_encode(array("status" => 'fail'))); 
else 
    echo(json_encode(array("status" => 'ok'))); 
?>