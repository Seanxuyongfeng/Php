package sean.com.chatapp1;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.os.Handler;
import android.os.HandlerThread;
import android.os.Message;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.PopupWindow;
import android.widget.TextView;

public class MainActivity extends FragmentActivity {
    private static final String TAG = "MySocket";
    private Context mContext;
    private ImageButton mNews,mConstact,mDeynaimic,mSetting;
    private View mPopView;
    private View currentButton;

    private TextView app_cancle;
    private TextView app_exit;
    private TextView app_change;

    private PopupWindow mPopupWindow;
    private LinearLayout buttomBarGroup;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        mContext=this;
        findView();
        init();
    }

    private void findView(){
        mPopView= LayoutInflater.from(mContext).inflate(R.layout.app_exit, null);
        buttomBarGroup=(LinearLayout) findViewById(R.id.buttom_bar_group);
        mNews=(ImageButton) findViewById(R.id.buttom_news);
        mConstact=(ImageButton) findViewById(R.id.buttom_constact);
        mDeynaimic=(ImageButton) findViewById(R.id.buttom_deynaimic);
        mSetting=(ImageButton) findViewById(R.id.buttom_setting);

        app_cancle=(TextView) mPopView.findViewById(R.id.app_cancle);
        app_change=(TextView) mPopView.findViewById(R.id.app_change_user);
        app_exit=(TextView) mPopView.findViewById(R.id.app_exit);
    }

    private void init(){
        mNews.setOnClickListener(newsOnClickListener);
        mConstact.setOnClickListener(constactOnClickListener);
        mDeynaimic.setOnClickListener(deynaimicOnClickListener);
        mSetting.setOnClickListener(settingOnClickListener);

        mConstact.performClick();

        mPopupWindow=new PopupWindow(mPopView, ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT, true);

        app_cancle.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mPopupWindow.dismiss();
            }
        });

        app_change.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

            }
        });

        app_exit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

            }
        });
    }

    private View.OnClickListener newsOnClickListener=new View.OnClickListener() {
        @Override
        public void onClick(View v) {

        }
    };

    private View.OnClickListener constactOnClickListener=new View.OnClickListener() {
        @Override
        public void onClick(View v) {

        }
    };

    private View.OnClickListener deynaimicOnClickListener=new View.OnClickListener() {
        @Override
        public void onClick(View v) {


        }
    };

    private View.OnClickListener settingOnClickListener=new View.OnClickListener() {
        @Override
        public void onClick(View v) {

        }
    };

    private void setButton(View v){
        if(currentButton!=null&&currentButton.getId()!=v.getId()){
            currentButton.setEnabled(true);
        }
        v.setEnabled(false);
        currentButton=v;
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if(keyCode==KeyEvent.KEYCODE_MENU){
            mPopupWindow.setBackgroundDrawable(new ColorDrawable(Color.parseColor("#b0000000")));
            mPopupWindow.showAtLocation(buttomBarGroup, Gravity.BOTTOM, 0, 0);
            mPopupWindow.setAnimationStyle(R.style.app_pop);
            mPopupWindow.setOutsideTouchable(true);
            mPopupWindow.setFocusable(true);
            mPopupWindow.update();
        }
        return super.onKeyDown(keyCode, event);

    }


}
