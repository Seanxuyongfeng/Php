<?php
    error_reporting(E_ALL);

    set_time_limit(0);

    echo "<h2>TCP/IP Connection</h2>\n";

    $port = 11109;
    $ip = "127.0.0.1";

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

    $arr = array('userid' => 'client_a', 'token' => '123456789', 'action' => 'login');
    $in = json_encode($arr);
    socket_write($socket, $in, strlen($in));
    sleep(3);
    do{
        fwrite(STDOUT,"client_a:");
        $input_msg = trim(fgets(STDIN));
        $msg = array('userid' => 'client_a', 'token' => '123456789', 'action' => 'send', 'targetuser'=>'client_b', 'msg' => "$input_msg");
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