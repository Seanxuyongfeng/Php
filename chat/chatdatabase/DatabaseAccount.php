<?php

require_once __DIR__ . '/../Response.php';

header("Content-type: text/html;charset=utf-8");

$dbms='mysql';     //数据库类型
$host='localhost'; //数据库主机名
$dbName='chat';  //使用的数据库
$user='root';      //数据库连接用户名
$pass='123456';     //对应的密码
$dsn="$dbms:host=$host;dbname=$dbName";
$tablename_user = "users";

class ChatAccount{
	private static $TAG = "Account";
	public static $CODE_OK = 0;
	public static $CODE_ALREADY_EXISTS = 1;
	public static $CODE_ERRO = 2;
	public static $CODE_NO_USER = 3;
	public static $CODE_ERROR_PWD = 4;

	public static function registerUser($name, $password){
		global $dsn,$user,$pass,$tablename_user;

		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sqlCheck = "select * from $tablename_user where username='$name'";
			$result = $conn->query($sqlCheck);
			$rows = $result->fetchAll();
			$rowCount = count($rows);
			if($rowCount > 0){
				$conn = null;
				return self::$CODE_ALREADY_EXISTS;
			}else{
				$userid = md5(uniqid());
				$register_time = "".date("Y/m/d");
				
				$sqlInsert = "INSERT INTO $tablename_user (userid, username, password, birthday, register_time, nickname)
				VALUES ('$userid', '$name', '$password', '', '$register_time', '')";
				error_log("registerUser '$userid'");
				$conn->exec($sqlInsert);
			}
		}catch(PDOException $e){
			error_log($sqlInsert . "<br>" . $e->getMessage());
		}
		$conn = null;
		return self::$CODE_OK;
	}
	
	public static function queryUserId($username){
		global $dsn,$user,$pass,$tablename_user;
		$sql = "select * from $tablename_user where username='$username'";
		$userid = '';
		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result = $conn->query($sql);
			while($row = $result->fetch()){
				$userid = $row['userid'];
				break;
			}
		}catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
		}
		$conn = null;
		return $userid;
	}
	
	public static function userExists($username, $password){
		global $dsn,$user,$pass,$tablename_user;

		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
			$sqlCheck = "select * from $tablename_user where username='$username'";
			$result = $conn->query($sqlCheck);
			$rows = $result->fetchAll();
			$rowCount = count($rows);
			if($rowCount > 0){
				$sqlCheck = "select * from $tablename_user where username='$username' and password='$password'";
				$result = $conn->query($sqlCheck);
				$rows = $result->fetchAll();
				$rowCount = count($rows);
				if($rowCount > 0){
					$conn = null;
					return self::$CODE_OK;
				}else{
					$conn = null;
					return self::$CODE_ERROR_PWD;
				}

			}else{
				return self::$CODE_NO_USER;
			}
		}catch(PDOException $e){
			echo $sqlInsert . "<br>" . $e->getMessage();
		}
		$conn = null;
		return self::$CODE_NO_USER;
	}

	public static function checkUser($friend_id){
		global $dsn,$user,$pass,$tablename_user;
		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sqlCheck = "select * from $tablename_user where userid='$friend_id'";
			$result = $conn->query($sqlCheck);
			$rows = $result->fetchAll();
			$rowCount = count($rows);
			if($rowCount > 0){
				$conn = null;
				return self::$CODE_OK;
			}
		}catch(PDOException $e){
			error_log($sqlInsert . "<br>" . $e->getMessage());
		}
		$conn = null;
		return self::$CODE_ERRO;
	}
	
	public static function insertFriend($userid, $friend_id){
		global $dsn,$user,$pass,$tablename_user;
		
		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sqlCheck = "select * from $tablename_user where userid='$userid'";
			$result = $conn->query($sqlCheck);
			$friends = '';
			while($row = $result->fetch()){
				$friends = $row['friends'];
			}
			$friends = $friends. ':' . $friend_id;
			$updateSql = "UPDATE '$tablename_user' SET friends = '$friends' WHERE userid = '$userid'";
			$conn->exec($updateSql);
			return self::$CODE_OK;
		}catch(PDOException $e){
			error_log($sqlInsert . "<br>" . $e->getMessage());
		}
		$conn = null;
		return self::$CODE_ERRO;
	}
	
	public static function query($username){
		global $dsn,$user,$pass,$tablename_user;
		$sql = "select * from $tablename_user where username='$username'";
		$alluser = array();
		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result = $conn->query($sql);
			while($row = $result->fetch()){
				echo "query " .$username . "\n";
				$alluser = array('username'=>$row['username'],"userid"=> $row['userid'],"nickname"=>$row['nickname'],"friends"=>$row['friends']);
			}
		}catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
		}
		$conn = null;
		return $alluser;
	}
	
	public static function friendExits($userid, $friend_userid){
		global $dsn,$user,$pass,$tablename_user;
		$sql = "select * from $tablename_user where userid='$userid'";
		try{
			$conn = new PDO($dsn, $user, $pass);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result = $conn->query($sql);
			while($row = $result->fetch()){
				$friends = $row['friends'];
				echo "friends :" . $friends . ", user:" . $userid."\n";
				$pos = stripos($friends, $friend_userid);
				echo "pos :".$pos . "\n";
				if ($pos !== false){
					return TRUE;
				}
			}
		}catch(PDOException $e){
			echo $sql . "<br>" . $e->getMessage();
		}
		$conn = null;
		return FALSE;
	}
}
?>