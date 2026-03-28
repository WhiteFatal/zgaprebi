<?php
	unset($_COOKIE['user']);
	setcookie("user", '', time()+3600*24*30, '/', 'zgaprebi.ge');
	session_regenerate_id();
	session_destroy();
	redirect(get_link('p=home'));
?>