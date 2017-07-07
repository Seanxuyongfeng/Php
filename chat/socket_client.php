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

    $arr = array('username' => "sean", 'token' => "adbsdfsk23kd",'msg' => "request from client");
    $in = json_encode($arr);

    do{
        echo "write....\n";
        if(!socket_write($socket, $in, strlen($in))) {
            echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
            break;
        }else {
            echo "send msg to server $in\n";
        }
        $out = '';
        echo "waiting....\n";
        while($out = socket_read($socket, 8192)) {
            echo "read from server $out\n";
            break;
        }
        sleep(3);
    }while(true);

    echo "Close SOCKET...\n";
    socket_close($socket);
    echo "OK\n";
?>