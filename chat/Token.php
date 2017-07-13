<?php
class Token
{
	public static function generateToken($userid)
	{
		if (empty($userid)){
			return "";
		}else{
			$token = md5($userid . date('Y-m-d', time()));
			return $token;
		}
	}
}
?>