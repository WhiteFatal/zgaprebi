<?php
    session_start();
	ob_start();
	
	error_reporting(E_ALL);
    ini_set('display_errors', 1);
	
	if(!isset($_SESSION["user"]["Id"]) || empty($_SESSION["user"]["Id"])){    
        header("Location:http://zgaprebi.ge/home/#auth_error"); die();
	}
	
	if(!isset($_GET['amount'])){
		header("Location:http://zgaprebi.ge/home/#amount_error"); die();
	}
	
	if((!is_numeric($_GET['amount']) && !is_float($_GET['amount'])) || ($_GET['amount']<=0)){
		header("Location:http://zgaprebi.ge/home/#amount_error"); die();
	}
	
	include('config.php');
    include("library/mysql_lib.php");
    include('library/cartu.php');
	
    $cartu = new PaymentCartu();
	
	$amount = $_GET['amount'];
	$sumamount = number_format($amount,2);
	
	$insert_Id = sql::insert("INSERT INTO `cartu_orders` (`amount`,`status`,`date`,`ip`) VALUES ('".$sumamount."','0',NOW(),'".$_SERVER['REMOTE_ADDR']."')", true);
	$ordercode = $_SESSION['user']['Id'].'-'.$insert_Id.'-'.date('Ymdhis');
	sql::update("UPDATE `cartu_orders` SET `TransactionId` = '".$ordercode."' WHERE `id` = ".$insert_Id);
		
	$merchantID = $cartu->merchantID;
    $CurrencyCode = $cartu->CurrencyCode;
    $CountryCode = $cartu->CountryCode;
    $MerchantName = $cartu->MerchantName;
    $MerchantURL = $cartu->MerchantURL;
    $MerchantCity = $cartu->MerchantCity;
    $Language = $cartu->Language;
	
?>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
    <body onload='document.frm.submit()'>
        <form action="https://e-commerce.cartubank.ge/servlet/Process3DSServlet/3dsproxy_init.jsp" method=post id="frm" name="frm">
            <input name="PurchaseDesc" type="hidden" value="<?php echo $ordercode;?>" />
            <input name="PurchaseAmt" type="hidden" value="<?php echo $sumamount;?>" />
            <input name="CountryCode" type="hidden" value="<?php echo $CountryCode;?>" />
            <input name="CurrencyCode" type="hidden" value="<?php echo $CurrencyCode;?>" />
            <input name="MerchantName" type="hidden" value="<?php echo $MerchantName;?>" />
            <input name="MerchantURL" type="hidden" value="<?php echo $MerchantURL;?>" />
            <input name="MerchantCity" type="hidden" value="<?php echo $MerchantCity;?>" />
            <input name="MerchantID" type="hidden" value="<?php echo $merchantID;?>" />
            <input name="xDDDSProxy.Language" type="hidden" value="<?php echo $Language;?>" />
            <noscript><input type="submit" value="Buy" /></noscript>
        </form>
    </body>
</html>