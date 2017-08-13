package com.sean.chat;

import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.telecom.Call;
import android.widget.Toast;

import com.sean.chat.activity.ChatActivity;
import com.sean.chat.util.DebugUtil;

import org.json.JSONObject;

public class MasterUser {

	public String username;
	public String password;
    public String userid;
    public String friends;

	private MessageReader mReader;
	private MessageSender mSender;

	private static MasterUser sIntance = new MasterUser();
    private Callback mCallback;
	private MasterUser(){

	}
    private Handler mHandler = new Handler(){
        @Override
        public void handleMessage(Message msg) {
            switch (msg.what){
                case MessageReader.MSG_ID_FROM_SERVER:
                    Bundle data = msg.getData();
                    if(data != null){
                        String result = data.getString("msg");
                        if(mCallback != null){
                            mCallback.onResult(result);
                            mCallback = null;
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    };
	public static MasterUser getInstanace(){
		return sIntance;
	}

	public void init(MessageReader reader, MessageSender sender){
		mReader = reader;
		mSender = sender;
        mReader.setHandler(mHandler);
	}

	public void login(String username, String password, Callback callback){
        JSONObject jsonObject = new JSONObject();
        try {
            jsonObject.put("username", username);
            jsonObject.put("password", password);
            jsonObject.put("action", "login");
            mCallback = callback;
            MessageSender sender = MessageSender.getInstance();
            sender.send(jsonObject);
        } catch (Exception e) {
            e.printStackTrace();
        }
	}

    public void queryOnline(Callback callback){
        JSONObject jsonObject = new JSONObject();
        try {
            MasterUser masterUser = MasterUser.getInstanace();
            jsonObject.put("userid", masterUser.userid);
            jsonObject.put("action", "query");
            mCallback = callback;
            MessageSender sender = MessageSender.getInstance();
            sender.send(jsonObject);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void addFriend(String friendId, Callback callback){
        JSONObject jsonObject = new JSONObject();
        try {
            MasterUser masterUser = MasterUser.getInstanace();
            jsonObject.put("userid", masterUser.userid);
            jsonObject.put("action", "addfriend");
            jsonObject.put("friend_id", friendId);
            mCallback = callback;
            MessageSender sender = MessageSender.getInstance();
            sender.send(jsonObject);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public interface Callback{
        public void onResult(String result);
    }
}
