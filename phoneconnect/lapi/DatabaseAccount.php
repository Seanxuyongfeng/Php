<?php

require_once('./DebugUtils.php');
require_once('./Response.php');

header("Content-type: text/html;charset=utf-8");

$dbms='mysql';     //数据库类型
$host='localhost'; //数据库主机名
$dbName='accounts';  //使用的数据库
$user='root';      //数据库连接用户名
$pass='123456';     //对应的密码
$dsn="$dbms:host=$host;dbname=$dbName";
$tablename_user = "userinfo";

class Account{
	private static $TAG = "Account";
	public static function addUserIntoDatabase1($name, $user_pass){
		global $dsn,$user,$pass,$tablename_user;
		$sql = "INSERT INTO $tablename_user (id, name, age, nickname, birthday, register_time, password)
	    		VALUES ('1', '$name', '', '', '', '', '$user_pass')";
		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->exec($sql);
		}catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
		}
		$conn = null;
	}
	
	public static function addUserIntoDatabase($name, $user_pass, $age, $nickname, $bithday){
		global $dsn,$user,$pass,$tablename_user;
		$sql = "INSERT INTO $tablename_user (name, age, nickname, birthday, register_time, password)
		VALUES ('$name', '$age', '$nickname', '', '', '$user_pass')";
		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->exec($sql);
		}catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
		}
		$conn = null;
	}
	
	public static function AddUser($username, $password, $age, $nickname, $bithday){
		self::addUserIntoDatabase($username, $password, $age, $nickname, $bithday);
	}
	
	public static function queryUserInfo($username){
		global $dsn,$user,$pass,$tablename_user;
		$sql = "select * from $tablename_user where name='$username'";
		$alluser = array();
		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result = $conn->query($sql);
			while($row = $result->fetch()){
				array_push($alluser, 'username', $row['name']);
				array_push($alluser, "age", $row['age']);
				array_push($alluser, "nickname", $row['nickname']);
				array_push($alluser, "birthday", $row['birthday']);
				DebugUtils::i(Account::$TAG, 'username '.$row['name']);
				DebugUtils::i(Account::$TAG, "age ".$row['age']);
				DebugUtils::i(Account::$TAG, "nickname ".$row['nickname']);
				DebugUtils::i(Account::$TAG, "birthday ".$row['birthday']);
			}
		}catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
		}
		$conn = null;
		return $alluser;
	}
}
?>