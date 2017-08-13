package sean.com.chatapp1;

import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import com.sean.chat.MasterUser;
import com.sean.chat.activity.ChatActivity;
import com.sean.chat.util.DebugUtil;

import org.json.JSONObject;

public class TestActivity extends AppCompatActivity {

    private TextView mTextView;
    MasterUser mMaterUser = MasterUser.getInstanace();
    private String mOnLineUsers;
    private String mChatUserid;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_test);
        mTextView = (TextView) findViewById(R.id.show);
        mTextView.setText(mMaterUser.friends);
        if(!TextUtils.isEmpty(mMaterUser.friends)){
            String[] users = (mMaterUser.friends).split(":");
            mChatUserid = users[0];
            DebugUtil.i("TestActivity", "onCreate " + mChatUserid);
        }

        Button queryBtn = (Button) findViewById(R.id.search);
        queryBtn.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v) {
                MasterUser masterUser = MasterUser.getInstanace();
                masterUser.queryOnline(new MasterUser.Callback() {
                    @Override
                    public void onResult(String result) {
                        Toast.makeText(TestActivity.this, result, Toast.LENGTH_SHORT).show();
                        DebugUtil.i("query",result);
                        try{
                            JSONObject obj = new JSONObject(result);
                            if(obj.has("users")){
                                MasterUser master = MasterUser.getInstanace();
                                mOnLineUsers = obj.getString("users");
                                mTextView.setText(mOnLineUsers);
                            }
                        }catch (Throwable e){
                            e.printStackTrace();
                        }
                    }
                });
            }
        });
        Button addBtn = (Button) findViewById(R.id.add_friend);
        addBtn.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v) {
                if(!TextUtils.isEmpty(mOnLineUsers)){
                    String[] users = mOnLineUsers.split(":");
                    final MasterUser master = MasterUser.getInstanace();
                    master.addFriend(users[0], new MasterUser.Callback() {
                        @Override
                        public void onResult(String result) {
                            try{
                                JSONObject obj = new JSONObject(result);
                                if(obj.has("friend_id")){
                                    master.friends += obj.getString("friend_id");
                                    mChatUserid = obj.getString("friend_id");
                                }
                            }catch (Throwable e){
                                e.printStackTrace();
                            }
                            DebugUtil.i("add friend", result);
                        }
                    });
                }
            }
        });

        Button chatBtn = (Button)findViewById(R.id.chat);
        chatBtn.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v) {
                if(TextUtils.isEmpty(mChatUserid)){
                    return;
                }
                Intent intent = new Intent();
                intent.setClass(TestActivity.this, ChatActivity.class);
                intent.putExtra("userid", mChatUserid);
                startActivity(intent);
            }
        });
    }
}
