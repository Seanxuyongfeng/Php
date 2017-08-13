package com.sean.chat.activity;

import android.os.Handler;
import android.os.Message;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.ListView;

import com.sean.chat.MasterUser;
import com.sean.chat.MessageReader;
import com.sean.chat.MessageSender;
import com.sean.chat.adapter.ChatEntity;
import com.sean.chat.adapter.ChatMessageAdapter;
import com.sean.chat.view.TitleBarView;

import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import sean.com.chatapp1.R;

public class ChatActivity extends AppCompatActivity {

    private String mFriendName = "张三";
    private TitleBarView mTitleBarView;
    private ListView mListView;
    private Button mBtnSend;
    private ImageButton emotionButton;
    private EditText inputEdit;
    private ChatMessageAdapter mMessageAdapter;

    private List<ChatEntity> mChatList = new ArrayList<ChatEntity>();

    private Handler mHandler = new Handler(){
        @Override
        public void handleMessage(Message msg) {
            switch (msg.what) {
                case MessageReader.MSG_ID_FROM_SERVER: {
                    Bundle data = msg.getData();
                    String istring = data.getString("msg");
                    ChatEntity chatMessage = new ChatEntity();
                    chatMessage.setMessageType(ChatEntity.RECEIVE);
                    chatMessage.setContent(istring);
                    Date date = new Date();
                    SimpleDateFormat sdf = new SimpleDateFormat("MM-dd hh:mm:ss");
                    String sendTime = sdf.format(date);
                    chatMessage.setSendTime(sendTime);
                    mChatList.add(chatMessage);
                    mMessageAdapter.notifyDataSetChanged();
                    mListView.setSelection(mChatList.size());
                }
                break;
                default:
                    break;
            }
        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_chat);
        initView();
        initEvent();
        MessageReader reader = MessageReader.getInstance();
        reader.setHandler(mHandler);
    }

    protected void initView(){
        mTitleBarView = (TitleBarView) findViewById(R.id.title_bar);
        mTitleBarView.setCommonTitle(View.GONE, View.VISIBLE, View.GONE);
        mTitleBarView.setTitleText("与" + mFriendName + "对话");
        mListView = (ListView) findViewById(R.id.chat_Listview);
        mBtnSend = (Button) findViewById(R.id.chat_btn_send);
        emotionButton = (ImageButton) findViewById(R.id.chat_btn_emote);
        inputEdit = (EditText) findViewById(R.id.chat_edit_input);
    }

    private void initEvent(){
        mMessageAdapter = new ChatMessageAdapter(ChatActivity.this, mChatList);
        mListView.setAdapter(mMessageAdapter);
        mBtnSend.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v) {
                String content = inputEdit.getText().toString();
                ChatEntity chatMessage = new ChatEntity();
                chatMessage.setMessageType(ChatEntity.SEND);
                chatMessage.setContent(content);
                Date date = new Date();
                SimpleDateFormat sdf = new SimpleDateFormat("MM-dd hh:mm:ss");
                String sendTime = sdf.format(date);
                chatMessage.setSendTime(sendTime);
                mChatList.add(chatMessage);
                mMessageAdapter.notifyDataSetChanged();
                mListView.setSelection(mChatList.size());
                MasterUser masterUser = MasterUser.getInstanace();
                JSONObject jsonObject = new JSONObject();
                try {
                    jsonObject.put("userid", masterUser.username);
                    jsonObject.put("token", "123456789");
                    jsonObject.put("action", "send");
                    jsonObject.put("targetuser", "client_b");
                    jsonObject.put("msg", content);
                } catch (Exception e) {
                    e.printStackTrace();
                }

                MessageSender sender = MessageSender.getInstance();
                sender.send(jsonObject);
                inputEdit.setText("");
            }
        });
    }
}
