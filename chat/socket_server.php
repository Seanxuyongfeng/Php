 <?php     
    set_time_limit(0);     

    $address='127.0.0.1';     
    $port=11109;    

    $sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
    if($sock == false){   
        echo "socket_create() Failed:".socket_strerror($sock);     
    }else{
        echo "socket_create() Sucess \n";   
    }
    // set the option to reuse the port
    socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
    //socket_set_nonblock($sock);

    if(($ret=socket_bind($sock,$address,$port))<0){   
        echo "socket_bind() Failed:".socket_strerror($ret);     
    }else{
        echo "socket_bind() Sucess\n";     
    }

    if(($ret=socket_listen($sock,4))<0){     
        echo "socket_listen() Failed:\n".socket_strerror($ret);     
    }else{
        echo "socket_listen() Sucess\n";     
    }

    $clients = array($sock);
    $write = array();
    $except = array();
    var_dump($clients);
    while(true){
        // create a copy, so $clients doesn't get modified by socket_select()
        $read = $clients;
        
        // get a list of all the clients that have data to be read from
        // if there are no clients with data, go to next iteration
        if (socket_select($read, $write, $except, 0) < 1){
            continue;
        }
        echo "socket_select triggered\n";
        //var_dump($read);
        // check if there is a new client trying to connect
        if (in_array($sock, $read)) {
            // accept the client, and add him to the $clients array
            $clients[] = $newsock = socket_accept($sock);
             echo "New client connected $newsock \n";
            $arr = array('username' => "sean", 'token' => "adbsdfsk23kd",'msg' => "aaaaa");

            $msg = json_encode($arr);
            
            $sent = socket_write($newsock, $msg, strlen($msg));
            if ($sent === false) {
                $errorcode = socket_last_error();
                echo "errorcode $errorcode\n";
                echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($errorcode)) . "\n";
                break;
            }
            // remove the listening socket from the clients-with-data array
            $key = array_search($sock, $read);
            unset($read[$key]);
            continue;
        }

        // loop through all the clients that have data to read from
        foreach ($read as $read_sock) {
            echo "foreach read_sock.\n";
            // read until newline or 1024 bytes
            // socket_read while show errors when the client is disconnected, so silence the error messages
            $data = socket_read($read_sock, 1024);
            
            // check if the client is disconnected
            if ($data === false) {
                echo "data is false.\n";
                // remove client for $clients array
                $key = array_search($read_sock, $clients);
                unset($clients[$key]);
                echo "client disconnected $read_sock.\n";
                // continue to the next client to read from, if any
                continue;
            }
            
            // trim off the trailing/beginning white spaces
            $data = trim($data);
            echo "data $data\n";
            // check if there is any data after trimming off the spaces
            if (!empty($data)) {
                // send this to all the clients in the $clients array 
                //(except the first one, which is a listening socket)
                echo "read client ", $data, $read_sock, "\n";
                socket_write($read_sock, $data."\n");
                
            }
        }
    }

    socket_close($sock);
    
    /*
    do {
        
        if (($connection = socket_accept($sock)) < 0){
            echo "socket_accept() failed: reason: " . socket_strerror($connection) . "/n";   
            echo "The Server is Stop....\n";
            break;     
        }

        do{
            if (false === ($buf = socket_read($connection, 8192))) {
                $errorcode = socket_last_error();
                echo "errorcode $errorcode\n";
                echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($errorcode)) . "\n";
                break;
            }

            echo "recv client ", $buf, "\n";

            $arr = array('username' => "sean", 'token' => "adbsdfsk23kd",'msg' => $buf);

            $msg = json_encode($arr);
            
            $sent = socket_write($connection, $msg, strlen($msg));
            if ($sent === false) {
                $errorcode = socket_last_error();
                echo "errorcode $errorcode\n";
                echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($errorcode)) . "\n";
                break;
            }
            sleep(5);
        }while(true);
     
        socket_close($connection);

    } while (true);

    socket_close($sock);
    */
?>    