<?php
ob_start();

include('config.php');
include('functions.php');
include('includes/mail/mail.php');

@include($_CONFIG[@$_GET['sid']]['type'].'_lib.php');

header ("content-type: text/xml");
################################################################################
if (allowIp()==false) {
	$_RESPONSE = ip_access_denide();
	err_log($_RESPONSE);
	die($_RESPONSE);
}
################################################################################
if(!is_valid_GET('txn_id') || !is_valid_GET('account') || !is_valid_GET('sid') || !is_valid_GET('amount')){
	$_RESPONSE = returnErrorResponse('-2','bad parameters');
	transaction_log('pay',$_RESPONSE,-2);
	die($_RESPONSE);
}
################################################################################

$_GET['amount'] = round($_GET['amount'],2);

if(account_exists($_GET['account'])){
	$_ACCOUNT = get_account_data($_GET['account']);
	$insert  = insert_pay($_ACCOUNT['Id'],$_ACCOUNT['email'],$_GET['txn_id'],$_GET['amount']);	
		if(@$insert>0){
			$_RESPONSE  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
			$_RESPONSE .= "<response>\n";
			$_RESPONSE .= "<code>1</code>\n";
			$_RESPONSE .= "<description>Ok</description>\n";
			$_RESPONSE .= "</response>";
			transaction_log('pay',$_RESPONSE,1);
			$message = 'თანხის ჩარიცხვა!<br/><br/>';
			$message .=	'ანგარიში: <b>'.$_ACCOUNT['Id'].'</b><br/>';
			$message .=	'ელ. ფოსტა: <b>'.$_ACCOUNT['email'].'</b><br/>';
			$message .=	'თანხა: <b>'.$_GET['amount'].'</b><br/><br/>';
			smtpmail::send('support@zgaprebi.ge','თანხის ჩარიცხვა tbcpay',$message,$_CONFIG['smtp']['mail']);
		} else {
			 $_RESPONSE = returnErrorResponse(-5,'Dublicate txn_id in pay');
			transaction_log('pay',$_RESPONSE,-5);
		}
		die($_RESPONSE);
} else {
	$_RESPONSE = returnErrorResponse(-1,'unknown account');
	transaction_log('pay',$_RESPONSE,-1);
	die($_RESPONSE);	
}
################################################################################

ob_end_flush();
?>