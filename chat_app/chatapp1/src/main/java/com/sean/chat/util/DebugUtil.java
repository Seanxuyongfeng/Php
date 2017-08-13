package com.sean.chat.util;

import android.util.Log;

/**
 * Created by Sean on 2017/8/13.
 */
public class DebugUtil {

    public static final String TAG = "MY-CHAT";

    public static void i(String tag, String msg){
        Log.i(TAG, tag + " " +msg);
    }
}
