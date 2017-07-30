package com.sean.chat;

import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;

import java.io.InputStream;
import java.net.Socket;

public class MessageReader extends Thread{
    private InputStream mReader;
    private boolean mWorking = true;
    private Handler mHandler;
    public static final int MSG_ID_FROM_SERVER = 1;

    private static MessageReader sInstance;

    public void init(Socket socket){
        try {
            mReader = socket.getInputStream();
            start();
        }catch (Exception e){
            e.printStackTrace();
        }
    }

    public void setHandler(Handler handler){
        mHandler = handler;
    }

    private MessageReader(){}

    public static MessageReader getInstance(){
        if(sInstance == null){
            sInstance = new MessageReader();
        }
        return sInstance;
    }

    @Override
    public void run() {
        byte buffer[] = new byte[1024*4];
        while(true){
            try{
                if(mWorking){
                    int len = 0;
                    while ((len = mReader.read(buffer)) != -1){
                        String a = new String(buffer, 0, len);
                        Message message = Message.obtain();
                        message.what = MSG_ID_FROM_SERVER;
                        Bundle data = new Bundle();
                        data.putString("msg", a);
                        message.setData(data);
                        mHandler.sendMessage(message);
                    }
                }
            }catch (Exception e){
                e.printStackTrace();
            }
        }
    }

    public void exit(){
        mWorking = false;
    }

}
