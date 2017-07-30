package com.sean.chat;

public class MasterUser {

	public String username;
	public String password;

	private static MasterUser sIntance = new MasterUser();

	private MasterUser(){

	}

	public static MasterUser getInstanace(){
		return sIntance;
	}

}
