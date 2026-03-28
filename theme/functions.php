<?php
	function floor_dec($number,$precision,$separator){
		if($number>0){
			$numberpart=explode($separator,$number);
			@$numberpart[1]=substr_replace($numberpart[1],$separator,$precision,0);
			if($numberpart[0]>=0){
				$numberpart[1]=floor((float)$numberpart[1]); // Added (float)
			}else{
				$numberpart[1]=ceil((float)$numberpart[1]);  // Added (float)
			}
			$ceil_number= array($numberpart[0],$numberpart[1]);
			
			return implode($separator,$ceil_number);
		}else{
			return 0;
		}
	}
	
	function alphanum($sstr, $num=true, $alpha=true, $extra="") {
		$exp="/^[";
		if ($alpha==true) { $exp.="a-zA-Z"; }
		if ($num==true) { $exp.="0-9"; }
		$exp.=$extra;
		$exp.="]{".strlen($sstr).",".strlen($sstr)."}$";
		$exp.="/";
		
		return preg_match($exp,$sstr);
	}
	
	function unset_cookies(){
		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			foreach($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);
				setcookie($name, '', time()-1000);
				setcookie($name, '', time()-1000, '/');
			}
		}
	}
	
	function is_logined(){
		if(isset($_COOKIE['user'])){
			$cookie = json_decode($_COOKIE['user']);
			
			$cookie->Id = sql::safe($cookie->Id);
			$cookie->username = sql::safe($cookie->username);
			$cookie->session_Id = sql::safe($cookie->session_Id);
			
			$check_session = sql::CNT("SELECT * FROM `_cn_mod_users` 
												WHERE 
													`Id` = '".@$cookie->Id."' 
												AND `email` = '".@$cookie->username."' 
												AND `session_Id` = '".$cookie->session_Id."' 
												AND `active` = 1");
												
			if($check_session==1) {
				$_SESSION['user']['Id'] = $cookie->Id;
				$_SESSION['user']['username'] = $cookie->username;
				$_SESSION['user']['flname'] = $cookie->flname;
				return true;
			}else{
				unset($_COOKIE['user']);
				setcookie("user", '', time()+3600*24*30, '/', 'zgaprebi.ge');
				session_destroy();
				session_regenerate_id();
				return false;
			}
		}else{
			if(isset($_SESSION['user'])){
				$_SESSION['user']['Id'] = sql::safe($_SESSION['user']['Id']);
				$_SESSION['user']['username'] = sql::safe($_SESSION['user']['username']);
				
				$check_session = sql::CNT("SELECT * FROM `_cn_mod_users` 
												WHERE 
													`Id` = '".@$_SESSION['user']['Id']."' 
												AND `email` = '".@$_SESSION['user']['username']."' 
												AND `session_Id` = '".session_id()."' 
												AND `active` = 1");
												
				if($check_session==1) {
					return true;
				}else{
					sql::update("UPDATE `_cn_mod_users` SET `session_Id` = NULL WHERE `Id` = '".@$_SESSION['user']['Id']."' AND `email` = '".$_SESSION['user']['username']."'");
					unset($_COOKIE['user']);
					setcookie("user", '', time()+3600*24*30, '/', 'zgaprebi.ge');
					session_destroy();
					session_regenerate_id();
					return false;
				}
			}else{
				return false;
			}
		}
	}
	
	function is_premium(){
		if(isset($_SESSION['user'])){
			$check_premium = sql::CNT("SELECT * FROM `_cn_mod_users` WHERE `active` = 1 AND `Id` = ".@$_SESSION['user']['Id']." AND NOW() < `premium_date` + INTERVAL `premium_days` DAY");
											
			if($check_premium==1) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function check_tale($Id){
		if(isset($_SESSION['user'])){
			$check_tale = sql::CNT("SELECT * FROM `_cn_mod_purchased_tales` WHERE `active` = 1 AND `tale_Id` = ".$Id." AND `user_Id` = ".@$_SESSION['user']['Id']." AND NOW() < `purchased_date` + INTERVAL `hours` HOUR");
							
			if($check_tale==1) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function users_log($Id, $action, $comment = false){
		sql::insert("INSERT INTO `_cn_mod_users_log` SET 
						`user_Id` = ".$Id.",
						`action` = '".$action."',
						`ip` = '".$_SERVER['REMOTE_ADDR']."',
						`comment` = '".$comment."',
						`date` = NOW()");
	}

	function menu(){
		$menu_sql = "SELECT * FROM `cn_sitemap` WHERE `lang` = '".$_GET['l']."' AND `enabled` > 0 AND `menuId` > 0 ORDER BY `ord` ASC";
		$menu = sql::getRows($menu_sql);
		
		$html  = '<div class="menu">';
        	$html .= '<ul>';
				foreach($menu as $v){
					$html .= '<li><a'.($_GET['pname']==$v['permalink']?' class="active"':'').' href="'.get_link('p='.$v['permalink']).'">'.$v['title'].'</a></li>';
				}
			$html .= '</ul>';
			$html .= '<div class="clb"></div>';
		$html .= '</div>';
		
		return $html;
	}
	
	function check_purchased($Id){
		$tale_sql = "SELECT * FROM `_cn_mod_purchased_tales` WHERE `user_Id` = ".@$_SESSION['user']['Id']." AND `tale_Id` = '".$Id."'";
		
		$tale = sql::CNT($tale_sql);
		
		return $tale;
	}
	
	function add_tale($Id, $hour){
		if(check_purchased($Id)==0){
			$cat_Id = get_new_cat_id('_cn_mod_purchased_tales');
			
			$sql = "INSERT INTO `_cn_mod_purchased_tales` SET `cat_Id` = '".$cat_Id."', `user_Id` = '".@$_SESSION['user']['Id']."', `tale_Id` = '".$Id."', `hours` = '".$hour."', `purchased_date` = NOW(), `lang` = 'ka'";
			
			sql::insert($sql);	
		}else{
			$sql = "UPDATE `_cn_mod_purchased_tales` SET `hours` = '".$hour."', `purchased_date` = NOW() WHERE `user_Id` = '".@$_SESSION['user']['Id']."' AND `tale_Id` = '".$Id."'";
			
			sql::update($sql);
		}
	}
	
	function add_premium($day){
		$sql = "UPDATE `_cn_mod_users` SET `premium_days` = '".$day."', `premium_date` = NOW()
					WHERE `Id` = '".$_SESSION['user']['Id']."' AND `email` = '".$_SESSION['user']['username']."'";
					
		sql::update($sql);
	}
	
	function check_balance($Id, $email){
		$balance_sql = "SELECT `balance` FROM `account_balances` WHERE `uid` = ".$Id." AND `account` = '".$email."'";
		
		$balance = sql::getElem($balance_sql);
		
		return (isset($balance)?$balance:0);
	}
	
	function check_premium_packet($Id){
		$sql = "SELECT * FROM `_cn_mod_premium` WHERE `active` = 1 AND `cat_Id` = ".$Id;
		
		$premium  = sql::CNT($sql);
		
		return $premium;
	}
	
	function check_premium_price($Id){
		$sql = "SELECT * FROM `_cn_mod_premium` WHERE `active` = 1 AND `cat_Id` = ".$Id;
		
		$premium  = sql::getRow($sql);
		
		return $premium;
	}
	
	function balance_update($cash){
		$balance_sql = "UPDATE `account_balances` SET `balance` = '".$cash."' 
							WHERE `uid` = '".$_SESSION['user']['Id']."' AND `account` = '".$_SESSION['user']['username']."'";
							
		sql::update($balance_sql);
	}
	
	
	
	function cash_to_gold($cash){
		global $_CFG;
		
		$gold = $_CFG['gold_rate'] * floor_dec($cash,1,".") / $_CFG['cash_rate'];
		
		return $gold;
	}
	
	function gold_to_cash($gold){
		global $_CFG;
		
		$cash = $gold * $_CFG['cash_rate'] / $_CFG['gold_rate'];
		
		return $cash;
	}
	
	function users_purchashes($Id){
		$purchases = sql::getRows("SELECT `comment` FROM `_cn_mod_users_log` WHERE `user_Id` = ".$Id." AND `action` IN ('purchase','add_premium')");
		$arr = array();
		
		foreach($purchases as $v){
			$str = json_decode($v['comment']);
			$arr[] = $str->price;
		}
		
		return cash_to_gold(array_sum($arr));
	}
	
	function purchashes(){
		$purchases = sql::getRows("SELECT `comment` FROM `_cn_mod_users_log` WHERE `action` IN ('purchase','add_premium')");
		$arr = array();
		
		foreach($purchases as $v){
			$str = json_decode($v['comment']);
			$arr[] = $str->price;
		}
		
		return cash_to_gold(array_sum($arr));
	}
	
	function premiums(){
		$premiums = sql::getRows("SELECT `comment` FROM `_cn_mod_users_log` WHERE `action` = 'add_premium'");
		$arr = array();
		
		foreach($premiums as $v){
			$str = json_decode($v['comment']);
			$arr[] = $str->price;
		}
		
		return cash_to_gold(array_sum($arr));
	}
	
	function tales(){
		$purchases = sql::getRows("SELECT `comment` FROM `_cn_mod_users_log` WHERE `action` = 'purchase'");
		$arr = array();
		
		foreach($purchases as $v){
			$str = json_decode($v['comment']);
			$arr[] = $str->price;
		}
		
		return cash_to_gold(array_sum($arr));
	}
	
	function purchased_tales_cnt($Id){
		$purchases = sql::getRows("SELECT `comment` FROM `_cn_mod_users_log` WHERE `action` = 'purchase'");
		$arr = array();
		
		foreach($purchases as $k => $v){
			$str = json_decode($v['comment']);
			if($str->tale_Id==$Id){
				$arr[$k] = $str;
			}
		}
		
		return count($arr);
	}
?>