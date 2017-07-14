<?php

header("Content-type: text/html;charset=utf-8");

require_once('./Response.php');

require_once __DIR__ . '/chatdatabase/DatabaseAccount.php';
require_once __DIR__ . '/chatdatabase/Cookie.php';

$username = "zhangsan";
$password = "mnbadf";

if (empty($username) ||empty($password)){
	$arr = array(
			'username' => $username,
			'password' => $password
	);
	Response::json(Response::$CODE_ERRO,'用户名或密码为空', $arr);
}

$result = ChatAccount::registerUser($username, $password);

error_log("user register '$username' '$password' $result");

if($result == ChatAccount::$CODE_OK){
	$arr = CookieTable::addCookie($username);
	Response::json(Response::$CODE_OK,'注册成功', $arr);
}else if($result == ChatAccount::$CODE_ALREADY_EXISTS){
	$arr = array(
			'username' => $username,
			'password' => $password
	);
	Response::json(Response::$CODE_ERRO,'用户名被占用', $arr);
}

?>