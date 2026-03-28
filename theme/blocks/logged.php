<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	
	$balance = check_balance($_SESSION['user']['Id'], $_SESSION['user']['username']);
	
	$gold = cash_to_gold($balance);
	
	$is_premium = is_premium();
	
	if($is_premium==true){
		$premium = sql::getElem("SELECT (`premium_date` + INTERVAL `premium_days` DAY) AS premium FROM `_cn_mod_users` WHERE `active` = 1 AND `Id` = ".$_SESSION['user']['Id']);	
	}
	
	//$cash = gold_to_cash($gold);
?>
<div class="authorised">
    <a<?php echo ($is_premium==true?' title="<span style=\'text-align:center;display:block\'>პრმეიუმის ამოწურვის თარიღი:<br/>'.$premium.'</span>"':''); ?> id="user" class="user<?php echo ($is_premium==true?' premium_on':''); ?>" href="javascript:void(0);"><?php echo $_SESSION['user']['flname']; ?></a>
    <span class="uid">თქვენი ID: <b><?php echo $_SESSION['user']['Id']; ?></b></span>
    <div class="clb"></div>
    <div class="balance">
        <span class="gold">&nbsp;</span>
        <span class="cash"><?php echo $gold; ?></span>
        <div class="clb"></div>
    </div>
    <div id="user_menu" class="menu">
        <ul>
            <li><a href="javascript:void(0);" onClick="show_popup('balance',false,false)"><?php _e('ბალანსის შევსება'); ?></a></li>
            <li><a href="javascript:void(0);" onClick="show_popup('settings',false,false)"><?php _e('პარამეტრები'); ?></a></li>
            <li><a href="<?php echo get_link('p=logout'); ?>" onClick="return confirm('<?php _e('დარწმუნებული ხართ, რომ გსურთ გასვლა?'); ?>')"><?php _e('გასვლა'); ?></a></li>
        </ul>
    </div>
    <a class="myTales" href="<?php echo get_link('p=purchased'); ?>"><?php _e('ჩემი ზღაპრები'); ?></a>
    <div class="clb"></div>
</div>