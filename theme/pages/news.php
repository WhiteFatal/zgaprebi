<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	if(isset($_GET['story'])){
		get::block('news_detail.php');
	}else{
		get::block('news.php');
	}
?>