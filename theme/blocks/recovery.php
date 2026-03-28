<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	global $_CFG;
	######################################################################################################
	if(isset($_GET['recoverykey'])){
		if(strlen($_GET['recoverykey'])!=20) redirect($_CFG['siteURL'].'home/recovery/error');
		
		$_GET['recoverykey'] = sql::safe($_GET['recoverykey']);
		
		$check_user = sql::CNT("SELECT * FROM `_cn_mod_users` WHERE `active` = 1 AND `actkey` = '".$_GET['recoverykey']."'");
		if($check_user!=1) redirect($_CFG['siteURL'].'home/recovery/error');
		
		$user = sql::getRow("SELECT * FROM `_cn_mod_users` WHERE `actkey` = '".$_GET['recoverykey']."'");
		
		$check_key = sql::CNT("SELECT
									t1.`Id`,
									t1.`user_Id`,
									t1.`action`,
									t1.`ip`,
									t1.`date`
								FROM
									(
										SELECT
											*
										FROM
											`_cn_mod_users_log`
										WHERE
											`user_Id` = '".$user['Id']."'
										AND `action` = 'recovery_request'
										ORDER BY
											`Id` DESC
										LIMIT 1
									) AS t1
								WHERE NOW() < t1.`date` + INTERVAL 3 HOUR");
								
		if($check_key!=1) redirect($_CFG['siteURL'].'home/recovery/error');
		
		$pass = getUniqueCode('6');
		
		sql::update("UPDATE `_cn_mod_users` SET `password` = '".md5($pass)."', `actkey` = NULL, `session_Id` = NULL WHERE `actkey` = '".$_GET['recoverykey']."'");
		users_log($user['Id'], 'recovery', false);
		
		$message = __('პაროლის აღდგენა წარმატებით დასრულდა!').'<br/><br/>';
		$message .= __('ახალი პაროლი').': <b>'.$pass.'</b>';
		
		smtpmail::send($user['email'],__('პაროლის აღდგენა'),$message,$_CFG['smtp']['mail']);
		
		redirect($_CFG['siteURL'].'home/recovery/success');
	}
	######################################################################################################
	if(@$_POST['recovery']==1){
		ob_clean();
		
		if(is_logined()){
			$status = array('res'=>"error",'txt'=>__('თქვენი უკვე ავტორიზირებული ხართ!'));
			echo json_encode($status); die();	
		}
		
		if(!filter_var(@$_POST['email'], FILTER_VALIDATE_EMAIL)){
			$status = array('res'=>'email','txt'=>__('ჩაწერეთ სწორი ელ. ფოსტა!'));
			echo json_encode($status); die();
		}
		///////////////////
		$_POST['email'] = sql::safe($_POST['email']);
		///////////////////
		$user = sql::CNT("SELECT * FROM `_cn_mod_users` WHERE `email` = '".$_POST['email']."' AND `active` = 1");
		
		if($user!=1){
			$status = array('res'=>"error",'txt'=>__('ასეთი ალ. ფოსტა არ მოიძებნა!'));
			echo json_encode($status); die();	
		}
		
		$user_row = sql::getRow("SELECT * FROM `_cn_mod_users` WHERE `email` = '".$_POST['email']."' AND `active` = 1");
		
		if($user_row['actkey']!=''){
			$lifetime = sql::CNT("SELECT
									t1.`Id`,
									t1.`user_Id`,
									t1.`action`,
									t1.`ip`,
									t1.`date`
								FROM
									(
										SELECT
											*
										FROM
											`_cn_mod_users_log`
										WHERE
											`user_Id` = '".$user_row['Id']."'
										AND `action` = 'recovery_request'
										ORDER BY
											`Id` DESC
										LIMIT 1
									) AS t1
								WHERE NOW() > t1.`date` + INTERVAL 3 HOUR");
			
			if($lifetime!=1){
				$status = array('res'=>"error",'txt'=>__('აქტივაციის კოდი უკვე გამოგზავნილია!'));
				echo json_encode($status); die();
			}
		}
		//////
		$recoverykey = getUniqueCode('20');
		
		sql::update("UPDATE `_cn_mod_users` SET `actkey` = '".$recoverykey."' WHERE `email` = '".$_POST['email']."'");
		users_log($user_row['Id'], 'recovery_request', false);
		
		$message = __('თქვენ მოითხოვეთ პაროლის აღდგენა საიტზე:');
		$message .= ' <b>'.$_CFG['siteName'].'</b><br/><br/>';
		$message .= __('პაროლის აღსადგენად გადადით შემდეგ ბმულზე:');
		$message .= ' <b><a href="'.$_CFG['siteURL'].'home/recoverykey/'.$recoverykey.'">'.$_CFG['siteURL'].'home/recoverykey/'.$recoverykey.'</a></b><br/><br/>';
		$message .= '<u>'.__('აღნიშნული ბმული აქტიური იქნება 3 საათის განმავლობაში!').'</u>';
		
		smtpmail::send($_POST['email'],__('პაროლის აღდგენა'),$message,$_CFG['smtp']['mail']);
		//////
		$status = array('res'=>'success','txt'=>__('პაროლის აღსადგენად ელ. ფოსტაზე გამოგეგზავნათ ბმული, რომელიც აქტიური იქნება 3 საათის განმავლობაში!'));
		echo json_encode($status); die();
	}
	######################################################################################################
?>
<div id="recovery" class="recovery">
    <a class="close" href="javascript:void(0);" onclick="hide_popup('recovery');">&nbsp;</a>
    <h1><?php _e('პაროლის აღდგენა'); ?></h1>
    <form id="recovery-form" action="<?php echo full_url(); ?>" method="post">
        <span class="status">&nbsp;</span>
        <input type="hidden" name="recovery" value="1"/>
        <input type="text" name="email" placeholder="<?php _e('ელ. ფოსტა'); ?>" value=""/>
        <input type="submit" name="submit" value="<?php _e('აღდგენა'); ?>"/>
        <div class="clb"></div>
    </form>
</div>