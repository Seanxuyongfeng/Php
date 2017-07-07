<?php

header("Content-type: text/html;charset=utf-8");

require_once('./Response.php');

require_once __DIR__ . '/chatdatabase/DatabaseAccount.php';

$username = $_POST["username"];
$password = $_POST["password"];

$result = ChatAccount::userExists($username, $password);

error_log("user register $result", 0);
if($result == ChatAccount::$CODE_OK){
	$arr = array(
			'username' => $username,
			'password' => $password
	);
	Response::json(Response::$CODE_OK,'登录成功', $arr);
}else if($result == ChatAccount::$CODE_NO_USER){
	$arr = array(
			'username' => $username,
			'password' => $password
	);
	Response::json(Response::$CODE_USER_NOT_EXITS,'用户不存在', $arr);
}else if($result == ChatAccount::$CODE_ERROR_PWD){
	$arr = array(
			'username' => $username,
			'password' => $password
	);
	Response::json(Response::$CODE_ERRO,'密码错误', $arr);	
}

?>