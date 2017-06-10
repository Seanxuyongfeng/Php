<?php

require_once('./Response.php');
require_once('./DatabaseAccount.php');

header("Content-type: text/html;charset=utf-8");

$username = $_POST["username"];
$password = $_POST["password"];
$age = $_POST["age"];
$nickname = $_POST["nickname"];
$bithday = $_POST["birthday"];

$arr = array(
		'username' => $username,
		'password' => $password
);

Account::AddUser($username, $password, $age, $nickname, $bithday);
Response::json($CODE_OK,'注册成功', $arr);

?>