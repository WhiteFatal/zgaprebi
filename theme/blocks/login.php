<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');

	if(@$_POST['authorisation']==1){
		ob_clean();
		
		if(is_logined()){
			$status = array('res'=>"error",'txt'=>__('თქვენი უკვე ავტორიზირებული ხართ!'));
			echo json_encode($status); die();	
		}
		
		if(trim(@$_POST['username'])==''){
			$status = array('res'=>'user','txt'=>__('ჩაწერეთ ელ. ფოსტა!'));
			echo json_encode($status); die();
		}
		
		if(trim(@$_POST['password'])==''){
			$status = array('res'=>'password','txt'=>__('ჩაწერეთ თქვენი პაროლი!'));
			echo json_encode($status); die();
		}
		//////////////////////////////
		$_POST['username'] = sql::safe($_POST['username']);
		$_POST['password'] = sql::safe($_POST['password']);
		//////////////////////////////
		$user = sql::getRow("SELECT * FROM `_cn_mod_users` WHERE `email` = '".$_POST['username']."' AND `password` = '".md5($_POST['password'])."'");
		
		if(!isset($user)){
			$status = array('res'=>"error",'txt'=>__('ელ. ფოსტა ან პაროლი არასწორია!'));
			echo json_encode($status); die();
		}
		
		if($user['active']==0){
			$status = array('res'=>"error",'txt'=>__('თქვენი ანგარიში დაბლოკილია ადმინისტრაციის მიერ!'));
			echo json_encode($status); die();
		}
		
		if($user['active']==2){
			$status = array('res'=>"error",'txt'=>__('თქვენი ანგარიში გასააქტიურებელია!'));
			echo json_encode($status); die();
		}
		
		if($user['active']!=1){
			$status = array('res'=>"error",'txt'=>__('სისტემური შეცდომა!'));
			echo json_encode($status); die();
		}
		
		session_regenerate_id();
		$session_Id = session_id();
		
		sql::update("UPDATE `_cn_mod_users` SET `session_Id` = '".$session_Id."' WHERE `email` = '".$_POST['username']."' AND `active` = 1");
		users_log($user['Id'],'login',false);
		
		if(@$_POST['remember']==1){
			$user_cookie = array(
				'Id' => $user['Id'],
				'username' => $user['email'],
				'flname' => $user['flname'],
				'session_Id' => $session_Id
			);
			
			setcookie("user", json_encode($user_cookie), time()+3600*24*30, '/', 'zgaprebi.ge');
		}
		
		$_SESSION['user']['Id'] = $user['Id'];
		$_SESSION['user']['username'] = $user['email'];
		$_SESSION['user']['flname'] = $user['flname'];
		
		$status = array('res'=>'success','txt'=>__('წარმატებით შეხვედით სისტემაში!'));
		echo json_encode($status); die();
	}
	
?>
<div class="authorisation">
    <form id="authorisation-form" action="<?php echo full_url(); ?>" method="post">
    	<input type="hidden" name="authorisation" value="1"/>
        <input type="text" name="username" placeholder="<?php _e('ელ. ფოსტა'); ?>"/>
        <input type="password" name="password" placeholder="<?php _e('პაროლი'); ?>"/>
        <input type="checkbox" id="remember" name="remember" value="1"/>
		<label for="remember"><span title="<?php _e('დამახსოვრება'); ?>"></span></label>
        <input type="submit" name="submit" value="<?php _e('შესვლა'); ?>"/>
        <div class="clb"></div>
    </form>
    <a onClick="show_popup('registration',false,false)" href="javascript:void(0);"><?php _e('რეგისტრაცია'); ?></a>
    <span class="divisor">&nbsp;</span>
    <a onClick="show_popup('recovery',false,false)" href="javascript:void(0);"><?php _e('პაროლის აღდგენა'); ?></a>
    <div class="clb"></div>
    <span class="status">&nbsp;</span>
    <div class="clb"></div>
</div>