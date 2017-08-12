<?php
    error_reporting(E_ALL);

    set_time_limit(0);

    echo "<h2>TCP/IP Connection</h2>\n";

    $port = 11109;
    $ip = "192.168.1.103";

    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    if ($socket < 0) {
        echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
    }else {
        echo "OK.\n";
    }

    echo "Connecting '$ip' port '$port'...\n";
    $result = socket_connect($socket, $ip, $port);
    if ($result < 0) {
        echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
    }else {
        echo "Connected!!!\n";
    }

    /*register
    $arr = array('username' => '22222', 'password' => '123456', 'action' => 'signup');
    $in = json_encode($arr);
    socket_write($socket, $in, strlen($in));
    $logon_result = '';
    while($logon_result = socket_read($socket, 8192)) {
    	echo "signup return: $logon_result\n";
    	break;
    }
    
    while(TRUE){
    	sleep(5);
    };*/
    
    $arr = array('username' => '22222', 'password' => '123456', 'action' => 'login');
    $in = json_encode($arr);
    socket_write($socket, $in, strlen($in));
    $logon_result = '';
    while($logon_result = socket_read($socket, 8192)) {
    	echo "login return: $logon_result \n";
    	break;
    }
    $login_result = json_decode($logon_result, true);
    $userid = $login_result['userid'];
    $friends = $login_result['friends'];
    echo "userid: " .$userid . "\n";
    $arr = array('userid' => $userid, 'password' => '123456', 'action' => 'query');
    $in = json_encode($arr);
    socket_write($socket, $in, strlen($in));
    $logon_result = '';
    $friends = '';
    while($logon_result = socket_read($socket, 8192)) {
    	echo "query return: $logon_result\n";
    	$data = json_decode($logon_result, true);
    	$friends = $data['users'];
    	break;
    }
 
    	sleep(5);

    
    //add friends
    $arrmy = explode(':', $friends);
    $arr = array('userid' => $userid, 'password' => '123456', 'action' => 'addfriend', 'friend_id' => $arrmy[0]);
    $in = json_encode($arr);
    socket_write($socket, $in, strlen($in));
    $logon_result = '';
    while($logon_result = socket_read($socket, 8192)) {
    	echo "add friends return: $logon_result\n";
    	break;
    }
    
    while(TRUE){
    	sleep(6);
    }
    
    do{
        fwrite(STDOUT,"client_a:");
        $input_msg = trim(fgets(STDIN));
        $msg = array('userid' => $userid, 'token' => '123456789', 'action' => 'send', 'targetuser'=>$friends, 'msg' => "$input_msg");
        $out = json_encode($msg);
        if(!socket_write($socket, $out, strlen($out))) {
            echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
            break;
        }
        $out = '';
        while($out = socket_read($socket, 8192)) {
            echo "reply...: $out\n";
            
            break;
        }
        
    }while($input_msg != 'exit');

    echo "Close SOCKET...\n";
    socket_close($socket);
    echo "socket_select() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    echo "OK\n";
?>