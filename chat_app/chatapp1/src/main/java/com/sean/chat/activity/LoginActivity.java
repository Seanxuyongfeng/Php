package com.sean.chat.activity;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Handler;
import android.os.Message;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import com.sean.chat.MasterUser;
import com.sean.chat.MessageReader;
import com.sean.chat.MessageSender;
import com.sean.chat.net.Connection;
import com.sean.chat.util.DebugUtil;

import org.json.JSONObject;

import sean.com.chatapp1.R;
import sean.com.chatapp1.TestActivity;

public class LoginActivity extends AppCompatActivity {
    private Button mLoginButton;
    private Button mRegisterButton;
    private EditText mUsername;
    private EditText mPassword;

    private Handler mHandler = new Handler(){
        @Override
        public void handleMessage(Message msg) {
            switch (msg.what){
                case MessageReader.MSG_ID_FROM_SERVER:
                    Bundle data = msg.getData();
                    if(data != null){
                        String result = data.getString("msg");
                        Toast.makeText(LoginActivity.this, result, Toast.LENGTH_SHORT).show();
                        DebugUtil.i("login", result);
                        try{
                            JSONObject obj = new JSONObject(result);
                            if(obj.has("userid")){
                                MasterUser master = MasterUser.getInstanace();
                                master.userid = obj.getString("userid");
                                Intent intent = new Intent();
                                intent.setClass(LoginActivity.this, ChatActivity.class);
                                startActivity(intent);
                            }
                        }catch (Throwable e){
                            e.printStackTrace();
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    };

    private MasterUser.Callback mCallback = new MasterUser.Callback() {
        @Override
        public void onResult(String result) {
            try{
                JSONObject obj = new JSONObject(result);
                if(obj.has("userid")){
                    MasterUser master = MasterUser.getInstanace();
                    master.userid = obj.getString("userid");
                    if(obj.has("friends")){
                        master.friends = obj.getString("friends");
                    }
                    Intent intent = new Intent();
                    intent.setClass(LoginActivity.this, TestActivity.class);
                    startActivity(intent);
                }
            }catch (Throwable e){
                e.printStackTrace();
            }
        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        initView();
    }

    private void initView(){
        mLoginButton = (Button) findViewById(R.id.login);
        mRegisterButton = (Button) findViewById(R.id.register);
        mUsername = (EditText) findViewById(R.id.account);
        mPassword = (EditText) findViewById(R.id.password);

        mLoginButton.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v) {
                String username = mUsername.getText().toString().trim();
                String password = mPassword.getText().toString().trim();
                if (username.equals("")) {
                    Toast.makeText(LoginActivity.this, "用户名为空", Toast.LENGTH_SHORT).show();
                    return;
                }

                if (password.equals("")) {
                    Toast.makeText(LoginActivity.this, "密码为空", Toast.LENGTH_SHORT).show();
                    return;
                }
                MasterUser masterUser = MasterUser.getInstanace();
                masterUser.username = username;
                masterUser.password = password;
                login(username,password);
            }
        });
    }

    public void login(final String username, final String password){
        try{

            new AsyncTask<Void,Void,Integer>(){
                @Override
                protected Integer doInBackground(Void... params) {
                    Connection connection = Connection.getInstance();
                    connection.connect();
                    MessageReader reader = MessageReader.getInstance();
                    reader.init(connection.getSocket());
                    MessageSender sender = MessageSender.getInstance();
                    sender.init(connection.getSocket());
                    MasterUser masterUser = MasterUser.getInstanace();
                    masterUser.init(reader, sender);
                    masterUser.login(username, password, mCallback);
                    return null;
                }

                @Override
                protected void onPostExecute(Integer integer) {
                    //Intent intent = new Intent();
                    //intent.setClass(LoginActivity.this, ChatActivity.class);
                    // startActivity(intent);
                }
            }.execute();

        }catch (Exception e){
            e.printStackTrace();
        }
    }

}
