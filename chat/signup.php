<?php

header("Content-type: text/html;charset=utf-8");

require_once('./Response.php');

require_once __DIR__ . '/chatdatabase/DatabaseAccount.php';

$username = $_POST["username"];
$password = $_POST["password"];

$result = ChatAccount::addUserIntoDatabase($username, $password);
error_log("user register $result", 0);
if($result == ChatAccount::$CODE_OK){
	$arr = array(
			'username' => $username,
			'password' => $password
	);
	Response::json(Response::$CODE_OK,'注册成功', $arr);
}else if($result == ChatAccount::$CODE_ALREADY_EXISTS){
	$arr = array(
			'username' => $username,
			'password' => $password
	);
	Response::json(Response::$CODE_ERRO,'用户名被占用', $arr);
}

?>