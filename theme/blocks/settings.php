<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	$user = sql::getRow("SELECT `flname`, `email` FROM `_cn_mod_users` WHERE `Id` = ".$_SESSION['user']['Id']);
	
	if(@$_POST['settings']==1){
		ob_clean();
		
		if(!is_logined()){
			$status = array('res'=>"error",'txt'=>__('თქვენ არ ხართ ავტორიზირებული!'));
			echo json_encode($status); die();	
		}
		
		if(trim(@$_POST['flname'])==''){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ სახელი და გვარი!'));
			echo json_encode($status); die();
		}
		
		if(trim(@$_POST['old_password'])==''){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ ძველი პაროლი!'));
			echo json_encode($status); die();
		}
		
		///////////////////
		$_POST['old_password'] = sql::safe($_POST['old_password']);
		///////////////////
		
		$check_password = sql::CNT("SELECT * FROM `_cn_mod_users` 
										WHERE 
											`active` = 1 
										AND `Id` = '".$_SESSION['user']['Id']."' 
										AND `email` = '".$_SESSION['user']['username']."' 
										AND `password` = '".md5($_POST['old_password'])."'");
		
		if($check_password!=1){
			$status = array('res'=>'error','txt'=>__('ძველი პაროლი არასწორია!'));
			echo json_encode($status); die();
		}
		
		if(strlen(utf8_decode(@$_POST['new_password']))<4 || strlen(utf8_decode(@$_POST['new_password']))>16){
			$status = array('res'=>'error','txt'=>__('ახალი პაროლი უნდა შედგებოდეს მინიმუმ 4 და მაქსიმუმ 16 სიმბოლოსგან!'));
			echo json_encode($status); die();
		}
		
		if(alphanum($_POST['new_password'],true,true,false)==false){
			$status = array('res'=>'error','txt'=>__('ახალ პაროლში დაშვებულია მხოლოდ ლათინური ასოები და რიცხვები!'));
			echo json_encode($status); die();
		}
		
		if($_POST['new_password']!=@$_POST['re_password']){
			$status = array('res'=>'error','txt'=>__('პაროლები არ ემთხვევა!'));
			echo json_encode($status); die();
		}
		
		///////////////////
		$_POST['flname'] = sql::safe($_POST['flname']);
		$_POST['new_password'] = sql::safe($_POST['new_password']);
		///////////////////
		
		sql::update("UPDATE `_cn_mod_users` SET `flname` = '".$_POST['flname']."', `password` = '".md5($_POST['new_password'])."' 
									WHERE `active` = 1 AND `Id` = '".$_SESSION['user']['Id']."' AND `email` = '".$_SESSION['user']['username']."'");
		users_log($_SESSION['user']['Id'],'change_settings',false);
		
		$_SESSION['user']['flname'] = $_POST['flname'];
		
		$status = array('res'=>'success','txt'=>__('პარამეტრები წარმატებით შეიცვალა!'));
		echo json_encode($status); die();
	}
?>
<div id="settings" class="settings">
    <a class="close" href="javascript:void(0);" onclick="hide_popup('settings');">&nbsp;</a>
    <h1><?php _e('პარამეტრები'); ?></h1>
    <form id="settings-form" action="<?php echo full_url(); ?>" method="post">
        <span class="status">&nbsp;</span>
        <input type="hidden" name="settings" value="1"/>
        <input type="text" name="flname" placeholder="<?php _e('სახელი და გვარი'); ?>" value="<?php echo $user['flname']; ?>"/>
        <input readonly type="text" name="email" placeholder="<?php _e('ელ. ფოსტა'); ?>" value="<?php echo $user['email']; ?>"/>
        <input type="password" name="old_password" placeholder="<?php _e('ძველი პაროლი'); ?>" value=""/>
        <input type="password" name="new_password" placeholder="<?php _e('ახალი პაროლი'); ?>" value=""/>
        <input type="password" name="re_password" placeholder="<?php _e('გაიმეორეთ პაროლი'); ?>" value=""/>
        <input type="submit" name="submit" value="<?php _e('შეცვლა'); ?>"/>
        <div class="clb"></div>
    </form>
</div>