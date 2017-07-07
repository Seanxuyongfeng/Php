<?php
class Response{
	public static $CODE_OK = 0;
	public static $CODE_ERRO = 1;
	public static $CODE_USER_NOT_EXITS = 2;
	/**
	* result 返回的提示码
	* desc 返回的提示信息
	* $data 返回的信息
	*/
	public static function json($code, $message='', $data = array()){
		if(!is_numeric($code)){
			return '';
		}
		
		$result = array(
			'result'=>$code,
			'desc'=>$message,
			'data'=>$data
		);
		
		echo json_encode($result,JSON_UNESCAPED_UNICODE);
		exit;
	}
}
?>