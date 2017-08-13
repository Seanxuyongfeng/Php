package com.sean.chat;

import android.os.Handler;

import org.json.JSONObject;

public class MasterUser {

	public String username;
	public String password;
    public String userid;

	private MessageReader mReader;
	private MessageSender mSender;

	private static MasterUser sIntance = new MasterUser();

	private MasterUser(){

	}

	public static MasterUser getInstanace(){
		return sIntance;
	}

	public void init(MessageReader reader, MessageSender sender){
		mReader = reader;
		mSender = sender;
	}

    public void setHandler(Handler handler){
        mReader.setHandler(handler);
    }

	public void login(String username, String password){
        JSONObject jsonObject = new JSONObject();
        try {
            jsonObject.put("username", username);
            jsonObject.put("password", password);
            jsonObject.put("action", "login");
            MessageSender sender = MessageSender.getInstance();
            sender.send(jsonObject);
        } catch (Exception e) {
            e.printStackTrace();
        }
	}

}
