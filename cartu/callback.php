<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ob_start();
	
    include("config.php");
	include('library/mail/mail.php');
    include("library/mysql_lib.php");
    include('library/cartu.php');
	
    $cartu = new PaymentCartu();
	
    $res = $cartu->checkvalidate();
	
    $cartu->savelog($res['xml']);
	
    if($res['status']){
		
        /*
        [xml] => SimpleXMLElement Object
        (
        [TransactionId] => 2157
        [PaymentId] => 409405020088
        [PaymentDate] => 04.04.2014 04:57:14
        [Amount] => 200
        [CardType] => CRTU!478763******4584
        [Status] => N
        [Reason] => Declined by merchant
        */
		
       // file_put_contents($res['xml']->TransactionId.'_'.$res['xml']->Status.'.txt',$_REQUEST['ConfirmRequest'].$res['xml']);
		
        if($res['xml']->Status=='C'){
            $cartu->checkPayment($res['xml']);
        }else{
            $cartu->registerPayment($res['xml']);
        }
    }

    die('not verify');
?>