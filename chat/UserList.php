<?php

require_once __DIR__ . '/chatdatabase/UserInfo.php';

class Users{
	private static $_instance;
	private $list = array();
	private function __construct(){}
	
	public static function getInstance(){
		if(self::$_instance == null){
			self::$_instance = new Users();
		}
		return self::$_instance;
	}
	
	public function add(User $user){
		$username = $user->getUserName();
		$name = array_search($username, $this->list);
		if (empty($userid)){
			//array_push($this->list, $username, $user);
			$this->list[$username] = $user;
		}else{
			
		}
	}
	
	public function dump(){
		var_dump($this->list);
	}
}
?>