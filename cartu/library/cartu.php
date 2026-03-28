<?php

	class PaymentCartu{
        //*******//
        var $errorCodes = array(
			0 => 'Ok',
            1 => 'Duplicate transaction',
            -1 => 'Technical problem',
            -2 => 'Order has been cancelled',
            -3 => 'Error in parameter(s)'
		);
        //*********//

        var $merchantID;
        var $CurrencyCode;
        var $CountryCode;
        var $MerchantName;
        var $MerchantURL;
        var $MerchantCity;
        var $Language;

        public function PaymentCartu(){
            $this->merchantID   = '000000008000958-00000001';
            $this->CurrencyCode = '981';
            $this->CountryCode  = '268';
            $this->MerchantURL  = "http://zgaprebi.ge";
            $this->MerchantName = 'LTD Literatura!zgaprebi.ge';
            $this->MerchantCity = 'Tbilisi';
            $this->Language     = '01';
        }

        public function checkvalidate() {
            if (isset($_REQUEST['ConfirmRequest']) && isset($_REQUEST['signature'])) {
                $res = array();
                $confirmrequest = $_REQUEST['ConfirmRequest'];
                $signature = $_REQUEST['signature'];
                $fp = fopen("/home/zgaprebi/domains/zgaprebi.ge/public_html/cartu/library/skey/CartuBankKEY.pem", "r");
                $key= fread($fp, 8192);
                fclose($fp);
				
                $pubkeyid = openssl_get_publickey($key);
				
                $verify = openssl_verify('ConfirmRequest='.$confirmrequest, base64_decode($signature), $pubkeyid, OPENSSL_ALGO_SHA1);
				
                if($verify==1){
                    $res['status'] = 1;
                    $res['xml'] =  simplexml_load_string($confirmrequest); 
                }else{
                    $res['status'] = 0;
                }
                openssl_free_key($pubkeyid);
                return $res;
            } else {
                die('param count  error');
            }
        }

        public function checkPayment($xml){
            $orderid = $xml->TransactionId;
            $PaymentId = $xml->PaymentId;
            $amount = $xml->Amount;
			
            $check_order_sql = "SELECT * FROM `cartu_orders` WHERE `TransactionId` = '".$orderid."' AND `status` = 0";
            $check_order = sql::CNT($check_order_sql);
			
            if($check_order==0) $this->returnResult($orderid, $PaymentId, 'DECLINED');

            $query = "SELECT `amount` FROM `cartu_orders` WHERE `TransactionId` = '".$orderid."'";
            $result = sql::getRow($query);

            $this->returnResult($orderid, $PaymentId, 'ACCEPTED');
        }

        public function registerPayment($xml){
            $orderid = $xml->TransactionId;
            $PaymentId = $xml->PaymentId;
            $amount = $xml->Amount;
			
            $query = "SELECT * FROM `cartu_orders` WHERE `TransactionId` = '".$orderid."' AND `status` = 0";
			$result_cnt = sql::CNT($query);
            $result = sql::getRow($query);
			
            if($result_cnt==0) $this->returnResult($orderid, $PaymentId, 'DECLINED');
			
			if($xml->Status=='Y'){
				$this->updateOrder($orderid,1);
				$this->addBalance($orderid,$amount);
				$this->returnResult($orderid, $PaymentId, 'ACCEPTED');
			}
            if($xml->Status=='N'){
				$this->updateOrder($orderid,2);
				$this->returnResult($orderid, $PaymentId, 'DECLINED');
			}
			if($xml->Status=='U'){
				$this->updateOrder($orderid,3);
				$this->returnResult($orderid, $PaymentId, 'DECLINED');
			}
        }
		
		public function updateOrder($orderid,$status){
            $query = "UPDATE `cartu_orders` SET `status` = '".$status."' WHERE `TransactionId` = '".$orderid."'";
			
            sql::update($query);

            if($status==1){
                //$this->sendemail($orderid);
            }
			
            return 1;
        }
		
		/*public function sendemail($orderid){
			
        }*/
		
		public function addBalance($orderid,$amount){
			$amount = number_format($amount*0.01,2);
			$arr = explode('-',$orderid);
			$Id = $arr[0];
			$email = sql::getElem("SELECT `email` FROM `_cn_mod_users` WHERE `Id` = ".$Id);
			
			$checkRow = sql::CNT("SELECT * FROM `account_balances` WHERE `uid` = '".$Id."' AND `account` = '".$email."'");
			
			if($checkRow==1){
				sql::update("UPDATE `account_balances` SET `balance` = `balance` + '".$amount."' WHERE `uid` = '".$Id."' AND `account` = '".$email."'");
			}else{
				sql::insert("INSERT INTO `account_balances` (`uid`,`account`,`balance`) VALUES('".$Id."','".$email."','".$amount."')");
			}
			
			global $_CONFIG;
			
			$message = 'თანხის ჩარიცხვა!<br/><br/>';
			$message .=	'ანგარიში: <b>'.$Id.'</b><br/>';
			$message .=	'ელ. ფოსტა: <b>'.$email.'</b><br/>';
			$message .=	'თანხა: <b>'.$amount.'</b><br/><br/>';
			
			smtpmail::send('support@zgaprebi.ge','თანხის ჩარიცხვა cartu',$message,$_CONFIG['smtp']['mail']);
		}

        public function savelog($log){
            $ip = $_SERVER['REMOTE_ADDR'];
			
            $TransactionId = $log->TransactionId;
            $PaymentId = $log->PaymentId;
            $PaymentDate = $log->PaymentDate;
            $Amount = $log->Amount;
            $CardType = $log->CardType;
            $Status = $log->Status;
            $Reason = $log->Reason;
			
            $query = "INSERT INTO `cartu_log` (
						`TransactionId`,
						`PaymentId`,
						`PaymentDate`,
						`Amount`,
						`CardType`,
						`Status`,
						`Reason`,
						`ip`,
						`date`
					)
					VALUES
					(
						'".$TransactionId."',
						'".$PaymentId."',
						'".$PaymentDate."',
						'".$Amount."',
						'".$CardType."',
						'".$Status."',
						'".$Reason."',
						'".$ip."',
						NOW()
					)";
					
            sql::insert($query);
        }
		
        public function saveresponcelog($TransactionId, $PaymentId, $Status){
            $ip = $_SERVER['REMOTE_ADDR'];
			
            $query="INSERT INTO `cartu_responce_log` (
						`TransactionId`,
						`PaymentId`,
						`Status`,
						`createdate`,
						`ip`
					)
					VALUES
					(
						'".$TransactionId."',
						'".$PaymentId."',
						'".$Status."',
						NOW(),
						'".$ip."'
					)";
					
            sql::insert($query);
        }
        public function returnResult($TransactionId, $PaymentId, $Status) {
            $this->saveresponcelog($TransactionId, $PaymentId, $Status);
            $xml = "<ConfirmResponse><TransactionId>$TransactionId</TransactionId><PaymentId>$PaymentId</PaymentId ><Status>$Status</Status></ConfirmResponse>";
            header('Content-type: text/xml');
            die($xml);
        }
    }
?>