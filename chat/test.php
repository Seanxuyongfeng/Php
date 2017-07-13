<?php
header("Content-type: text/html;charset=utf-8");

require_once('./Response.php');
require_once('./UserList.php');
require_once __DIR__ . '/chatdatabase/DatabaseAccount.php';
require_once __DIR__ . '/chatdatabase/UserInfo.php';
/*
$user = new User("sdfsdf");
$userlist = Users::getInstance();
$userlist->add($user);
$userlist->dump();*/
$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
$lastmonth = mktime(date("H"), date("i"), date("s"), date("m")+1, date("d"),   date("Y"));
$nextyear  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")+1);
$aa = date('Y-m-d H:i:s');
echo 'aaa ' . $aa . '========';
//echo 'tomorrow ' . $tomorrow;
//echo 'lastmonth ' . $lastmonth;
//echo '$nextyear ' . $nextyear;
//echo date("M-d-Y", $lastmonth);
//echo strtotime($lastmonth);
echo date('Y-m-d H:i:s',strtotime($aa));
?>