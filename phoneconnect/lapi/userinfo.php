<?php

require_once('./DatabaseAccount.php');

header("Content-type: text/html;charset=utf-8");

$username = $_POST["username"];


$userinfo = Account::queryUserInfo($username);
Response::json(Response::$CODE_OK,'成功', $userinfo);
?>