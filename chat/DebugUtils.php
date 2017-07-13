<?php
header("Content-type: text/html;charset=utf-8");

class DebugUtils{
	private static $TAG_MAIN = "PHP-SERVER";
	private static $DEBUG = FALSE;
	
	public static function i($TAG, $msg){
		if(DebugUtils::$DEBUG){
			error_log(DebugUtils::$TAG_MAIN . ' ' . $TAG .' ' .$msg);
		}
	}
}
?>