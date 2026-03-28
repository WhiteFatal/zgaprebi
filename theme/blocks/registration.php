<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	global $_CFG;
	######################################################################################################
	if(isset($_GET['actkey'])){
		if(strlen($_GET['actkey'])!=20) redirect($_CFG['siteURL'].'home/activation/error');
		
		$_GET['actkey'] = sql::safe($_GET['actkey']);
		$activation = sql::CNT("SELECT * FROM `_cn_mod_users` WHERE `active` = 2 AND `actkey` = '".$_GET['actkey']."'");	
		if($activation!=1) redirect($_CFG['siteURL'].'home/activation/error');
		
		$userId = sql::getElem("SELECT `Id` FROM `_cn_mod_users` WHERE `active` = 2 AND `actkey` = '".$_GET['actkey']."'");
		
		sql::update("UPDATE `_cn_mod_users` SET `active` = 1, `actkey` = NULL WHERE `active` = 2 AND `actkey` = '".$_GET['actkey']."'");
		users_log($userId, 'activation', false);
		
		redirect($_CFG['siteURL'].'home/activation/success');
	}
	######################################################################################################
	if(@$_POST['registration']==1){
		ob_clean();
		
		if(trim(@$_POST['flname'])==''){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ სახელი და გვარი!'));
			echo json_encode($status); die();
		}
		
		if(trim(@$_POST['email'])==''){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ ელ. ფოსტა!'));
			echo json_encode($status); die();
		}
		
		if(!filter_var(@$_POST['email'], FILTER_VALIDATE_EMAIL)){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ სწორი ელ. ფოსტა!'));
			echo json_encode($status); die();
		}
		
		///////////////////
		$_POST['flname'] = sql::safe($_POST['flname']);
		$_POST['email'] = sql::safe($_POST['email']);
		$_POST['password'] = sql::safe($_POST['password']);
		///////////////////
		
		$user = sql::CNT("SELECT * FROM `_cn_mod_users` WHERE `email` = '".$_POST['email']."'");
		
		if($user!=0){
			$status = array('res'=>'user','txt'=>__('ასეთი ელ. ფოსტა უკვე დარეგისტრირებულია!'));
			echo json_encode($status); die();
		}
		
		if(strlen(utf8_decode(@$_POST['password']))<4 || strlen(utf8_decode(@$_POST['password']))>16){
			$status = array('res'=>'error','txt'=>__('პაროლი უნდა შედგებოდეს მინიმუმ 4 და მაქსიმუმ 16 სიმბოლოსგან!'));
			echo json_encode($status); die();
		}
		
		if(alphanum($_POST['password'],true,true,false)==false){
			$status = array('res'=>'error','txt'=>__('პაროლში დაშვებულია მხოლოდ ლათინური ასოები და რიცხვები!'));
			echo json_encode($status); die();
		}
		
		if($_POST['password']!=@$_POST['re_password']){
			$status = array('res'=>'error','txt'=>__('პაროლები არ ემთხვევა!'));
			echo json_encode($status); die();
		}
		
		/*$ip = sql::CNT("SELECT * FROM `_cn_mod_users_log` WHERE `action` = 'registration' AND `ip` = '".$_SERVER['REMOTE_ADDR']."'");
		
		if($ip>=10){
			$status = array('res'=>'user','txt'=>__('თქვენი IP მისამართიდან რეგისტრაციის რაოდენობა ამოიწურა!'));
			echo json_encode($status); die();
		}*/
		
		$newCatId = get::newcatId("_cn_mod_users");
		$actkey = getUniqueCode(20);
		
		$insert_Id = sql::insert("INSERT INTO `_cn_mod_users` SET
						`cat_Id`	= '".$newCatId."',
						`email` 	= '".$_POST['email']."',
						`password` 	= '".md5($_POST['password'])."',
						`flname` 	= '".$_POST['flname']."',
						`actkey`	= '".$actkey."',
						`lang`		= 'ka',
						`active` 	= '2'", true);
						
		users_log($insert_Id, 'registration', false);
						
		$message = __('თქვენ გაიარეთ რეგისტრაცია საიტზე:');
		$message .= ' <b>zgaprebi.ge</b><br/><br/>';
		$message .= __('ანგარიშის გასააქტიურებლად გადადით შემდეგ ბმულზე:');
		$message .= ' <b><a href="'.$_CFG['siteURL'].'home/actkey/'.$actkey.'">'.$_CFG['siteURL'].'home/actkey/'.$actkey.'</a></b>';
		
		smtpmail::send($_POST['email'],__('რეგისტრაცია'),$message,$_CFG['smtp']['mail']);
		
		$status = array('res'=>'success','txt'=>__('თქვენ წარმატებით გაიარეთ რეგისტრაცია! ანგარიშის გასააქტიურებლად ელ. ფოსტაზე გამოგეგზავნათ ბმული!'));
		echo json_encode($status); die();	
	}
	######################################################################################################
?>
<div id="registration" class="registration">
    <a class="close" href="javascript:void(0);" onclick="hide_popup('registration');">&nbsp;</a>
    <h1><?php _e('რეგისტრაცია'); ?></h1>
    <form id="registration-form" action="<?php echo full_url(); ?>" method="post">
        <span class="status">&nbsp;</span>
        <input type="hidden" name="registration" value="1"/>
        <input type="text" name="flname" placeholder="<?php _e('სახელი და გვარი'); ?>" value=""/>
        <input type="text" name="email" placeholder="<?php _e('ელ. ფოსტა'); ?>" value=""/>
        <input type="password" name="password" placeholder="<?php _e('პაროლი'); ?>" value=""/>
        <input type="password" name="re_password" placeholder="<?php _e('გაიმეორეთ პაროლი'); ?>" value=""/>
        <input type="submit" name="submit" value="<?php _e('რეგისტრაცია'); ?>"/>
        <div class="clb"></div>
    </form>
</div>