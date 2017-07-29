package sean.com.chatapp2;

import android.os.Handler;
import android.os.HandlerThread;
import android.os.Message;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.Socket;
import java.nio.Buffer;

public class MainActivity extends AppCompatActivity {
    private static final String TAG = "MySocket";
    private static final int MSG_ID_FROM_SERVER = 1;

    private TextView mTextView;
    private Button mBtn;
    private EditText mEditText;
    private Socket mSocket ;

    private  static BufferedWriter mWriter;
    private HandlerThread mHandlerThread = new HandlerThread("chat2");
    {
        mHandlerThread.start();
    }

    private Handler mWorker = new Handler(mHandlerThread.getLooper());

    private Handler mHandler = new Handler(){
        @Override
        public void handleMessage(Message msg) {
            switch (msg.what){
                case MSG_ID_FROM_SERVER:{
                    Bundle data = msg.getData();
                    String istring = data.getString("msg");
                    mTextView.setText(istring);
                    Log.i(TAG, "handle message:"+istring);
                    Toast.makeText(MainActivity.this, istring, Toast.LENGTH_SHORT).show();
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
        setContentView(R.layout.activity_main);

        mTextView = (TextView)findViewById(R.id.txt1);
        mBtn = (Button)findViewById(R.id.send);
        mEditText = (EditText)findViewById(R.id.ed1);
        mBtn.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v) {
                final String text = mEditText.getText().toString();
                try {
                    mWorker.post(new Runnable() {
                        @Override
                        public void run() {
                            try {
                                send(text, mWriter);
                            } catch (Exception e) {
                                e.printStackTrace();
                            }

                        }
                    });
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        });

        new Thread(new Runnable(){
            @Override
            public void run() {
                try {
                    mSocket = new Socket("192.168.1.103",11109);
                    InputStream inputStream = mSocket.getInputStream();
                    mWriter = new BufferedWriter(new OutputStreamWriter(mSocket.getOutputStream()));
                    login(mWriter);
                    Log.i(TAG, "app2 mWriter = " +mWriter);
                    String msg;

                    byte buffer[] = new byte[1024*4];
                    int temp = 0;
                    while(true) {
                        Log.i(TAG, "while read...");
                        while ((temp = inputStream.read(buffer)) != -1){
                            String a = new String(buffer, 0, temp);
                            Log.i(TAG, "app2 msg: "+a);
                            Message message = Message.obtain();
                            message.what = MSG_ID_FROM_SERVER;
                            Bundle data = new Bundle();
                            data.putString("msg", a.toString());
                            message.setData(data);
                            mHandler.sendMessage(message);
                        }
                    }
                }catch (Exception e){
                    e.printStackTrace();
                }
            }
        }).start();
    }

    private void login(BufferedWriter writer){
        JSONObject jsonObject = new JSONObject();
        try {
            jsonObject.put("userid", "client_b");
            jsonObject.put("token", "123456789");
            jsonObject.put("action", "login");
            mWriter.write(jsonObject.toString()+"\n");
            mWriter.flush();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void send(String msg, BufferedWriter writer){
        JSONObject jsonObject = new JSONObject();
        try {
            jsonObject.put("userid", "client_b");
            jsonObject.put("token", "123456789");
            jsonObject.put("action", "send");
            jsonObject.put("targetuser", "client_a");
            jsonObject.put("msg", msg);
            writer.write(jsonObject.toString()+"\n");
            writer.flush();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
