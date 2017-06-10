<?php
	header("Content-type: text/html;charset=utf-8");
	require_once('./Response.php');
	$arr = array(
		'id' => 1,
		'name' => 'singwa'
	);
	
	Response::json(20,'数据请求成功',$arr);
?>