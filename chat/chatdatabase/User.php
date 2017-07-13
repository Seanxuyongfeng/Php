<?php
class User{
	private $username;
	private $userid;
	private $token;
	
	public function __construct($name){
		$this->username = $name;
	}
	
	public function setUserId($userid){
		$this->userid = $userid;
	}
	
	public function setToken($token){
		$this->token = $token;
	}
	
	public function getUserId(){
		return $this->userid;
	}
	
	public function getToken(){
		return $this->token;
	}
	
	public function getUserName(){
		return $this->username;
	}
}
?>