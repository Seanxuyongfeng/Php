<?php

class Session{
    
    private $socket;
    private $userid;
    private $targetuser;
    
    public function __construct($socket){
        $this->socket = $socket;
        $this->userid = '';
        $this->targetuser = array();
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
            //socket_close($this->socket);
            return false;;
        }

        // trim off the trailing/beginning white spaces
        $data = trim($data);
        $action = $this->getAction($data);
        $this->handle($sessions,$action,$data);
        return true;
    }

    public function response($msg){
        socket_write($this->socket, $msg);
    }
    
    private function findTargetSession($sessions, $targetuser){
        for ($i= 0; $i< count($sessions); $i++){
            $session = $sessions[$i];
            if($session){
                echo "find target " . $session->getUserid() . "\n";
                if($session->getUserid() == $targetuser){
                    return $session;
                }
            }
        }
        return null;
    }
    
    private function getAction($msg){
        $data = json_decode($msg, true);
        return $data['action'];
    }
    
    private function handle($sessions, $action, $msg){
        if($action == 'login'){
            echo "action lgoin \n";
            $data = json_decode($msg, true);
            $this->userid = $data['userid'];
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