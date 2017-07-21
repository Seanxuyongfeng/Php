<?php

require_once __DIR__ . '/../DebugUtils.php';
require_once __DIR__ . '/../Response.php';
require_once __DIR__ . '/../Token.php';
require_once __DIR__ . '/DatabaseAccount.php';

header("Content-type: text/html;charset=utf-8");

$dbms='mysql';     //数据库类型
$host='localhost'; //数据库主机名
$dbName='chat';  //使用的数据库
$user='root';      //数据库连接用户名
$pass='123456';     //对应的密码
$dsn="$dbms:host=$host;dbname=$dbName";
$tablename_cookie = "cookie";

class CookieTable{
	private static $TAG = "Account";
	public static $CODE_OK = 0;
	public static $CODE_ALREADY_EXISTS = 1;
	public static $CODE_ERRO = 2;
	public static $CODE_NO_USER = 3;
	public static $CODE_ERROR_PWD = 4;

	public static function addCookie($name){
		global $dsn,$user,$pass,$tablename_cookie;
		$userid = ChatAccount::queryUserId($name);
		if(empty($userid)){
			return array();
		}
		error_log("addCookie '$userid'");
		$expiretime = date('Y-m-d H:i:s');
		$token = Token::generateToken($userid);
		$result = self::addCookieImpl($name, $token, $expiretime, $userid);
		if($result === self::$CODE_OK){
			$arr = array(
					'result'=>Response::$CODE_OK,
					'desc'=>'注册成功',
					'username' => $name,
					'userid' => $userid,
					'token' => $token,
					'expiretime' => $expiretime,
			);
			return $arr;
		}else{
			return array();
		}
	}
	
	private static function addCookieImpl($name, $token, $expiretime, $userid){
		global $dsn,$user,$pass,$tablename_cookie;

		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sqlCheck = "select * from $tablename_cookie where username='$userid'";
			$result = $conn->query($sqlCheck);
			$rows = $result->fetchAll();
			$rowCount = count($rows);
			if($rowCount > 0){
				$conn = null;
				return self::$CODE_ALREADY_EXISTS;
			}else{
				$sqlInsert = "INSERT INTO $tablename_cookie (username, token, expiretime, userid)
				VALUES ('$name', '$token', '$expiretime', '$userid')";
				$conn->exec($sqlInsert);
			}
		}catch(PDOException $e){
			echo $sqlInsert . "<br>" . $e->getMessage();
		}
		$conn = null;
		return self::$CODE_OK;
	}

	public static function query($username){
		global $dsn,$user,$pass,$tablename_cookie;
		$sql = "select * from $tablename_cookie where username='$username'";
		$alluser = array();
		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result = $conn->query($sql);
			while($row = $result->fetch()){
				array_push($alluser, "result", Response::$CODE_OK);
				array_push($alluser, "desc", '登录成功');
				array_push($alluser, 'username', $row['username']);
				array_push($alluser, "token", $row['token']);
				array_push($alluser, "expiretime", $row['expiretime']);
				array_push($alluser, "userid", $row['userid']);
			}
		}catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
		}
		$conn = null;
		return $alluser;
	}
}
?>