<?php

header("Content-type: text/html;charset=utf-8");

require_once('./Response.php');

require_once __DIR__ . '/chatdatabase/DatabaseAccount.php';
require_once __DIR__ . '/chatdatabase/Cookie.php';

$username = $_POST["username"];
$password = $_POST["password"];

$result = ChatAccount::userExists($username, $password);

error_log("user register $result", 0);
if($result == ChatAccount::$CODE_OK){
	$arr = CookieTable::query($username);
	Response::json($arr);
}else if($result == ChatAccount::$CODE_NO_USER){
	$arr = array(
			'result'=>Response::$CODE_USER_NOT_EXITS,
			'desc'=>'用户不存在',
			'username'=>$username
	);
	Response::json($arr);
}else if($result == ChatAccount::$CODE_ERROR_PWD){
	$arr = array(
			'result'=>Response::$CODE_ERRO,
			'desc'=>'密码错误',
			'username'=>$username
	);
	Response::json($arr);	
}

?>