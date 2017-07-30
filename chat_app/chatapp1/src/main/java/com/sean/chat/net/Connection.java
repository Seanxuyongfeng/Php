package com.sean.chat.net;

import java.net.Socket;

/**
 * Created by Sean on 2017/7/30.
 */
public class Connection {
    private static final String IP = "192.168.1.103";
    private static final int PORT = 11109;

    private Socket mSocket;

    private static Connection sInstance;
    private Connection(){

    }

    public static Connection getInstance(){
        if(sInstance == null){
            sInstance = new Connection();
        }
        return sInstance;
    }

    public void connect(){
        try{
            mSocket = new Socket(IP, PORT);
        }catch (Exception e){
            e.printStackTrace();
        }
    }

    public Socket getSocket(){
        return mSocket;
    }
}
