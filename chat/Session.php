<?php
require_once __DIR__ . '/chatdatabase/DatabaseAccount.php';

class Session{
    
    private $socket;
    private $userid;
    private $username;
    private $targetuser;
    private $friends;
    
    public function __construct($socket){
        $this->socket = $socket;
        $this->userid = '';
        $this->username = '';
        $this->targetuser = array();
        $this->friends = array();
    }
    
    public function getSocket(){
        return $this->socket;
    }
    
    public function getUserid(){
        return $this->userid;
    }
    
    public function onConncted(){
       echo "New client connected " . $this->socket . "\n";
    }
    
    public function onRead($sessions){
        // read until newline or 1024 bytes
        // socket_read while show errors when the client is disconnected, so silence the error messages
        $data = socket_read($this->socket, 1024);
        // check if the client is disconnected
        if ($data === false) {
            echo "socket_read failed: reason: " . socket_strerror(socket_last_error($this->socket)) . "\n";
            return false;;
        }
        // trim off the trailing/beginning white spaces
        $data = trim($data);
        if (empty($data)) {
            return false;
        }
        echo "data " . $data . "\n";
        $action = $this->getAction($data);
        $this->handle($sessions,$action,$data);
        return true;
    }

    public function response($msg){
        $sent = socket_write($this->socket, $msg);
        if($sent === false){
            echo "socket_write failed: reason: " . socket_strerror(socket_last_error($this->socket)) . "\n";
        }
    }

    private function findTargetSession($sessions, $targetuser){
    	$result = ChatAccount::friendExits($this->userid, $targetuser);
    	if($result === false){
    		echo "not exists in databse \n";
    		return null;
    	}else{
    		echo "find ".$targetuser. " in database. \n";
    	}

        for ($i= 0; $i< count($sessions); $i++){
            $session = $sessions[$i];
            if($session){
                if($session->getUserid() == $targetuser){
                    return $session;
                }
            }
        }
        echo "friend " . $targetuser . " not on line \n";
        return null;
    }
    
    private function onLogin($username,$password){
    	$result = ChatAccount::userExists($username, $password);
    	
    	echo "user login ". $result ." \n";
    	if($result == ChatAccount::$CODE_OK){
    		$arr = ChatAccount::query($username);
    		$this->userid = $arr['userid'];
    		$this->username = $arr['username'];
    		
    		echo "login " . $this->userid . ":" . $this->username."\n";
    		$responce = json_encode($arr,JSON_UNESCAPED_UNICODE);
			$this->response($responce);
    	}else if($result == ChatAccount::$CODE_NO_USER){
    		$arr = array(
    				'result'=>Response::$CODE_USER_NOT_EXITS,
    				'desc'=>'用户不存在',
    				'username'=>$username
    		);
    		$responce = json_encode($arr,JSON_UNESCAPED_UNICODE);
    		$this->response($responce);
    	}else if($result == ChatAccount::$CODE_ERROR_PWD){
    		$arr = array(
    				'result'=>Response::$CODE_ERRO,
    				'desc'=>'密码错误',
    				'username'=>$username
    		);
    		$responce = json_encode($arr,JSON_UNESCAPED_UNICODE);
    		$this->response($responce);
    	}
    }
    
    private function getAction($msg){
        $data = json_decode($msg, true);
        return $data['action'];
    }
    
    private function handle($sessions, $action, $msg){
        if($action == 'login'){
            echo "action login \n";
            $data = json_decode($msg, true);
            $username = $data['username'];
            $password = $data['password'];
            $this->onLogin($username, $password);
        }else if($action == 'send'){
            echo "action send, sessions: " . count($sessions) . "\n";
            $data = json_decode($msg, true);
            $targetuser = $data['targetuser'];
            $sendmsg = $data['msg'];
            if(in_array($targetuser, $this->targetuser) == false){
                $this->targetuser[$targetuser] = $sendmsg;
            }
            $target = $this->findTargetSession($sessions, $targetuser);
            if($target){
                $target->response($sendmsg);
            }else{
                echo "cannot find target " . $targetuser . "\n";
            }
            
        }
    }
    
}
?>