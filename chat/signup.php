<?php

header("Content-type: text/html;charset=utf-8");

require_once('./Response.php');

require_once __DIR__ . '/chatdatabase/DatabaseAccount.php';
require_once __DIR__ . '/chatdatabase/Cookie.php';

$username = $_POST["username"];
$password = $_POST["password"];

if (empty($username) ||empty($password)){
	$arr = array(
		'username' => $username
	);
	Response::json(Response::$CODE_ERRO,'用户名或密码为空', $arr);
}

$result = ChatAccount::registerUser($username, $password);

error_log("user register '$username' '$password' $result");

if($result == ChatAccount::$CODE_OK){
	
	$arr = CookieTable::addCookie($username);
	
	Response::json($arr);
	
}else if($result == ChatAccount::$CODE_ALREADY_EXISTS){
	$arr = array(
			'result'=>Response::$CODE_ERRO,
			'desc'=>'用户名被占用',
			'username'=>$username
	);
	Response::json($arr);
}

?>