<?php
ob_start();
session_name("managment");
session_start();
include ('../includes/config.php');
include ('../includes/functions.db.php');
include ('../includes/functions.lib.php');
include ('../includes/functions.php');
include ('../includes/mail/mail.php');

$tmp = sql::getRows('SELECT * FROM `cn_adminlangs` WHERE lang="'.$_CFG['adminlang'].'"');
foreach($tmp as $v) $_LANG[$v['name']] = $v['value'];

if(count($_GET)>0)  $_GET = sql::safeArray($_GET);
if(count($_POST)>0) $_POST = sql::safeArray($_POST);

if(isset($_GET['tscode'])){ 
	$addIpRequests = settings::get('addIpRequest');
	if($addIpRequests==$_GET['tscode'] || @in_array($_GET['tscode'],$addIpRequests)){
		add_admin_ip($_GET['m'], $_GET['ip'], __('დამატებულია ელ-ფოსტიდან'));
		settings::remove('addIpRequest',$_GET['tscode']);
		redirect("login.php?msg=ipaddsuccess");
	} else {
		redirect("login.php?msg=errorduringaddip");
	}
	die();
}


if (is::adminIp()) {
	if (@$_GET['act']=='logout') logout();
	if (isset($_POST['submit'])) {
		$status = array();
		if(trim($_POST['user'])=='' || trim($_POST['pass'])=='') redirect('login.php?msg=emptyFields');
		##########
		$num_users = sql::cnt("SELECT * FROM `cn_adminusers` WHERE user='".$_POST['user']."' AND pass='".md5($_POST['pass'])."'");
	
			if ($num_users==0) {
				adminLoginLog($_POST['user'],getenv("REMOTE_ADDR"),0,__('არასწორი მომხმარებელი ან პაროლი'));
				redirect('login.php?msg=incorrectuserpass');
			} elseif ($num_users==1) {
					adminLoginLog($_POST['user'],getenv("REMOTE_ADDR"),1,__('სისტემაში შემოსვლა'));
					$row_AdminUser = sql::getRow("SELECT * FROM `cn_adminusers`
												WHERE `user`='".$_POST['user']."' AND `pass`='".md5($_POST['pass'])."'");
						$_SESSION['userId']		= $row_AdminUser['Id'];
						$_SESSION['user'] 		= $row_AdminUser['user'];
						$_SESSION['email'] 		= $row_AdminUser['email'];
						$_SESSION['flname']		= $row_AdminUser['flname'];
						$_SESSION['Timeout']	= $row_AdminUser['timeout'];
						$_SESSION['isLoggedIn'] = true;
						$_SESSION['lasttime']	= date('d/m/Y H:i:s');
						session_regenerate_id();
						$_SESSION['sessionId']	= session_id();
	
					sql::update("UPDATE `cn_adminusers` SET `currsesid`='".$_SESSION['sessionId']."'
											WHERE `Id`=".$_SESSION['userId']);
					 header("Location: index.php");
			} else {
				adminLoginLog($_POST['user'],getenv("REMOTE_ADDR"),2,__('სისტემური შეცდომა ავტორიზაციის დროს'));
				redirect('login.php?msg=error');
			}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>CMS</title>
<?php echo '
<link href="js_css/styles.css" rel="stylesheet" type="text/css" />
<!--[if IE]> <link href="js_css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script> 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script type="text/javascript" src="js_css/plugins/ui/jquery.fancybox.js"></script>
<script type="text/javascript" src="js_css/plugins/ui/jquery.tipsy.js"></script>
<script type="text/javascript" src="js_css/plugins/forms/jquery.uniform.js"></script>
<script type="text/javascript" src="js_css/files/bootstrap.js"></script>
<script type="text/javascript" src="js_css/files/functions.js"></script>';
?>
</head>
<body>
<div id="separator">&nbsp;</div>
<?php
if (is::adminIp()) {
?>
	<form action="" method="post">	
        <div class="fluid" style="margin-left:auto; margin-right:auto; width:450px">
        <?php
			if(@$_GET['msg']=='logoutOk') {
				echo '<div class="nNote nSuccess"><p><b>'.__('გმადლობთ').': </b>'.__('თქვენ წარმატებით გამოხვედით სისტემიდან!').'</p></div>';
			}
			if(@$_GET['msg']=='emptyFields') {
				echo '<div class="nNote nFailure"><p><b>'.__('შეცდომა.').': </b>'.__('გთხოვთ შეავსოთ მომხმარებელი და პაროლი').'</p></div>';
			}
			if(@$_GET['msg']=='incorrectuserpass') {
				echo '<div class="nNote nFailure"><p><b>'.__('შეცდომა').': </b>'.__('არასწორი მომხმარებელი ან პაროლი').'</p></div>';
			}
			if(@$_GET['msg']=='error') {
				echo '<div class="nNote nFailure"><p><b>'.__('შეცდომა').': </b>'.__('სისტემაში მომხმარებელთა წვდომის შეცდომა').'</p></div>';
			}
			if(@$_GET['msg']=='ipaddsuccess') {
				echo '<div class="nNote nSuccess"><p><b>'.__('გმადლობთ').': </b>'.__('IP მისამართი დაემატა წარმატებით!<br>გთხოვთ გაიაროთ ავტორიზაცია').'</p></div>';
			}
			if(file_exists('../storage/admin-login.png')) echo '<center><img src="/storage/admin-login.png" /></center>';
		?>
            <div class="widget" style="margin-top:20px">
                <div class="whead"><h6><?php _e('ავტორიზაცია'); ?></h6><div class="clear"></div></div>
            <div class="formRow">
                <div class="grid3"><label><?php _e('მომხმარებელი'); ?> :</label></div>
                <div class="grid7"><input type="text" name="user" value=""  /></div><div class="clear"></div>
            </div>        
            <div class="formRow">
                <div class="grid3"><label><?php _e('პაროლი'); ?> :</label></div>
                <div class="grid7"><input type="password" name="pass" value=""  /></div><div class="clear"></div>
            </div>        
            <div class="formRow">
                <div class="grid3">&nbsp;</div>
                <div class="grid5"><input type="submit" name="submit" class="buttonS bLightBlue" value="CONNECT" /></div><div class="clear"></div>
            </div>
         </div>        
        </div>        
    </form>
<?php } else {
	if(isset($_POST['secKey'])){
	ob_clean();
	if(trim($_POST['mail'])=='' || trim($_POST['secKey'])=='') {
		die(__('გთხოვთ შეავსოთ ორივე ველი'));
	} else {
		$exists = sql::CNT("SELECT * FROM `cn_adminusers` WHERE `email`='".$_POST['mail']."'");
		if($exists<1){
			adminLoginLog($_POST['mail'],getenv("REMOTE_ADDR"),-1,__('არასწორი ელ-ფოსტით ავტორიზაციის მცდელობა!'));
			die(__('არასწორი ელ-ფოსტა'));
		} else {
			mail_ip_add_request($_POST['mail']);
			adminLoginLog($_POST['mail'],getenv("REMOTE_ADDR"),-1,__('ელ-ფოსტით ავტორიზაციის მცდელობა'));
			die("ok");	
		}
	}
	}
?>
	<form action="" method="post" id="formRequestMailAccess">	
        <div class="fluid" style="margin-left:auto; margin-right:auto; width:450px">
        <?php echo @implode('',$status); ?>
            <div class="widget">
                <div class="whead"><h6><?php _e('ელ-ფოსტით ავტორიზაციის მოთხოვნა'); ?></h6><div class="clear"></div></div>
            <div class="formRow input">
                <div class="grid3"><label><?php _e('ელ-ფოსტა'); ?> :</label></div>
                <div class="grid5"><input type="text" name="mail" value="" autocomplete="off"  /></div><div class="clear"></div>
            </div>        
            <div class="formRow input">
                <div class="grid3"><label><?php _e('კოდი'); ?> :</label></div>
                <div class="grid5"><input type="text" name="secKey" id="secKey" autocomplete="off" style="width:95px;padding-left:10px;" maxlength="5" />
	    <img src="img/captcha.php" style="cursor:pointer; vertical-align:middle" onClick="$(this).attr('src','img/captcha.php?'+(new Date().getTime()))" /></div><div class="clear"></div>
            </div>        
            <div class="formRow">
                <div class="grid3">&nbsp;</div>
                <div class="grid5"><input type="submit" name="submitRequestMailAccess" class="buttonS bLightBlue" alt="Request CONNECT" value="Request CONNECT" /></div><div class="clear"></div>
            </div>
         </div>        
        </div>        
    </form>
<script type="text/javascript">
$('#formRequestMailAccess').submit(function(){
	$('input[name="submitRequestMailAccess"]').val('<?php _e('მოითმინეთ...'); ?>');
	$.post("<?php echo full_url(); ?>",$('#formRequestMailAccess').serializeArray(),function(data){
		if(data=='ok') {
			$('div.formRow.input').remove();
			$('div.formRow div.grid3').remove();
			$('.formRow .grid5').css('width','300px').html('<?php _e('წერილი გამოიგზავნა ელ-ფოსტაზე.'); ?>');
		} else {
			$('input[name="submitRequestMailAccess"]').val($('input[name="submitRequestMailAccess"]').attr('alt'));
			alert(data);
		}
	});
	return false;
})
</script>
<?php } ?>
<script type="text/javascript">
var t = ($(document).height()/2) - ($('.fluid').height()/2)-100;
	$('#separator').css("height",t);
	
$(document).resize(function(){
	var t = ($(document).height()/2) - ($('.fluid').height()/2)-100;
	$('#separator').css("height",t);
});

</script>
</body>
</html>
<?php
ob_end_flush();
?>
