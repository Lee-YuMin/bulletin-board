<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$hostname = 'localhost';
$username = 'root';
$password = 'password123';
$database = 'mysite';
$port = '3306';
$charset = 'utf8';

$connect = new mysqli($hostname, $username, $password, $database, $port);

// DB 커넥션
if($connect->connect_errno){
	echo '[연결실패] : '.$connect->connect_error.''; 
} else {
	// echo '[연결성공]';
}

// 문자셋 지정
if(! $connect->set_charset($charset))
{
	echo '[문자셋 지정 실패] : '.$connect->connect_error;
}

// 메모리 정리
// $result->free();

// 연결 종료
// $connect->close();
?>