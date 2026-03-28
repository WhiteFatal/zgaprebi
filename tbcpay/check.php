<?php

ob_start();
include('config.php');
include('functions.php');
@include($_CONFIG[@$_GET['sid']]['type'].'_lib.php');


header ("content-type: text/xml");
################################################################################
if (allowIp()==false) {
	$_RESPONSE = ip_access_denide();
	err_log($_RESPONSE);
	die($_RESPONSE);
}
################################################################################
if(/*!is_valid_GET('txn_id') || */!is_valid_GET('account')){
	$_RESPONSE = returnErrorResponse('-2','bad parameters');
	transaction_log('check',$_RESPONSE,-2);
	die($_RESPONSE);
}
################################################################################
if(account_exists($_GET['account'])){
	$_RESPONSE  = get_account_data_xml($_GET['account']);
	transaction_log('check',$_RESPONSE,1);
	die($_RESPONSE);
} else {
	$_RESPONSE = returnErrorResponse(-1,'unknown account');
	transaction_log('check',$_RESPONSE,-1);
	die($_RESPONSE);
}
################################################################################

ob_end_flush();
?>