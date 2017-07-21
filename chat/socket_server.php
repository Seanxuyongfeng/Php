 <?php
    require_once __DIR__ . '/Session.php';
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
    $sessions = array();
    var_dump($clients);

    while(true){
        // create a copy, so $clients doesn't get modified by socket_select()
        $read = $clients;
        
        // get a list of all the clients that have data to be read from
        // if there are no clients with data, go to next iteration
        if (socket_select($read, $write, $except, 0) < 1){
            continue;
        }
        echo "socket_select triggered.\n";
        //var_dump($read);
        // check if there is a new client trying to connect
        if (in_array($sock, $read)) {
            // accept the client, and add him to the $clients array
            $clients[] = $newsock = socket_accept($sock);
            $newsession = new Session($newsock);
            $sessions[] = $newsession;
            $newsession->onConncted();

            // remove the listening socket from the clients-with-data array
            $key = array_search($sock, $read);
            unset($read[$key]);
            continue;
        }

        // loop through all the clients that have data to read from
        
        foreach ($read as $read_sock) {
            echo "size " . count($sessions) . "\n";
            foreach ($sessions as $session){
               if($session){
                   if($session->getSocket() == $read_sock){
                       $result = $session->onRead($sessions);
                       if($result == false){
                           $key = array_search($session, $sessions);
                           if($key !== false){
                               echo "remove client ";
                               echo $session->getSocket();
                               echo " \n";
                               unset($sessions[$key]);

                               // remove client for $clients array
                               $key1 = array_search($read_sock, $clients);
                               unset($clients[$key1]);
                               echo "client disconnected $read_sock.\n";
                               // continue to the next client to read from, if any
                               continue;
                           }
                       }
                   }
               }
            }
        }
        
    }

    socket_close($sock);
?>    