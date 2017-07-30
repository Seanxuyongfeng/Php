package com.sean.chat;

import android.os.Handler;
import android.os.HandlerThread;

import org.json.JSONObject;

import java.io.BufferedWriter;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.net.Socket;

/**
 * Created by Sean on 2017/7/30.
 */
public class MessageSender {
    private HandlerThread mWorker = new HandlerThread("sender");
    {
        mWorker.start();
    }

    private Handler mHandler = new Handler(mWorker.getLooper());

    private static MessageSender sInstance;

    private BufferedWriter mWriter;

    private MessageSender(){

    }

    public void init(Socket socket){
        try {
            mWriter = new BufferedWriter(new OutputStreamWriter(socket.getOutputStream()));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public static MessageSender getInstance(){
        if(sInstance == null){
            sInstance = new MessageSender();
        }
        return sInstance;
    }

    public void send(final String msg){
        mHandler.post(new Runnable() {
            @Override
            public void run() {
                send(msg, mWriter);
            }
        });
    }

    public void send(final JSONObject object){
        mHandler.post(new Runnable() {
            @Override
            public void run() {
                try {
                    mWriter.write(object.toString()+"\n");
                    mWriter.flush();
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        });
    }

    private void send(String msg, BufferedWriter writer){
        JSONObject jsonObject = new JSONObject();
        try {
            jsonObject.put("userid", "client_a");
            jsonObject.put("token", "123456789");
            jsonObject.put("action", "send");
            jsonObject.put("targetuser", "client_b");
            jsonObject.put("msg", msg);
            writer.write(jsonObject.toString()+"\n");
            writer.flush();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
