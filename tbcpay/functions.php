<?php
ob_start();

if(!in_array(@$_GET['sid'],$_CONFIG['sids'])) die(returnErrorResponse('-2','bad parameters [sid]'));

################# /*  GLOBAL FUNCTIONS  */ ######################################

function err_log($log){
	$filename = 'errorlog.log';
	$content = date('Y-m-d H:i:s')."	".$log . "\n";
	if (is_writable($filename)) {
		if (!$handle = fopen($filename, 'a')) { exit; }
		if (fwrite($handle, $content) === FALSE) { exit; }
		fclose($handle);
	}
}

function implode_keys($s,$a){
	$t = array();
	foreach($a as $k => $v){ $t[] = $k; }
	return implode($s,$t);
}

function returnErrorResponse($code='',$description=''){
	return 	"<response>\n<code>".$code."</code>\n<description>".$description."</description>\n</response>";
}

function allowIp(){
	global $_CONFIG;
	$_ALLOWIPStmp = array();
	$guestIp = getenv("REMOTE_ADDR");
	$gip = explode('.',$guestIp);
	$guestIpLastClass = end($gip);
	if(count($_CONFIG['allowIps'])>0){
		foreach($_CONFIG['allowIps'] as $key => $aips){
			$tmpIps = explode('.',$aips);
			$_ALLOWIPStmp[$key] = (end($tmpIps)=='*') ? str_replace('*',$guestIpLastClass,$aips) : $aips;
			}
	}
	return (in_array($guestIp,$_ALLOWIPStmp)) ? true : false;
}


function is_valid_GET($var){
	global $_GET;
	if($var == 'txn_id') {
		if(isset($_GET['txn_id']) && @is_numeric($_GET['txn_id'])) { return true; }  else { return false; }
	} 
	elseif($var == 'amount') {
		if(isset($_GET['amount']) && @is_numeric($_GET['amount'])) { return true; }  else { return false; }
	} 
	elseif ($var == 'account'){
		if(isset($_GET['account']) && @trim($_GET['account']!='')) { return true; }  else { return false; }
	}
	elseif ($var == 'sid'){
		if(isset($_GET['sid']) && @trim($_GET['account']!='')) { return true; }  else { return false; }
	} else return false;
}

function transaction_log($logtype,$txt,$status){
		global $_CONFIG;
		$txt = str_replace("\n","",$txt);
		$_GET['amount'] = (isset($_GET['amount'])) ? $_GET['amount'] : 0;
		$now = ($_CONFIG[$_GET['sid']]['type']=='mysql') ? 'now()' : 'getdate()';
		$query = "INSERT INTO ".$_CONFIG['tbl_prefix']."log
					(txn_id, log_date, ip, action, logtype, logtext, status, amount)
			  VALUES ('".@$_GET['txn_id']."',".$now.", '".getenv("REMOTE_ADDR")."','".$_SERVER['REQUEST_URI']."',
			  		  '".$logtype."','".$txt."', ".$status.",".$_GET['amount'].")";
		sql::insert($query);
}

function ip_access_denide(){
	return "<response>access Dedine By pay.php AllowIp\t".date('d/m/Y h:i:s')."\t".getenv("REMOTE_ADDR")."\t".htmlspecialchars($_SERVER['REQUEST_URI'])."</response>";
}

function print_m($a){
	echo "<pre>";
	print_r($a);
	echo "/<pre>";
}



function utf2lat($s){
        $a = array('ა'=>'a','ბ'=>'b','გ'=>'g','დ'=>'d','ე'=>'e','ვ'=>'v','ზ'=>'z','თ'=>'T',
				   'ი'=>'i','კ'=>'k','ლ'=>'l','მ'=>'m','ნ'=>'n','ო'=>'o','პ'=>'p','ჟ'=>'dj',
				   'რ'=>'r','ს'=>'s','ტ'=>'t','უ'=>'u','ფ'=>'f','ქ'=>'q','ღ'=>'g','ყ'=>'y',
				   'შ'=>'sh','ჩ'=>'ch','ც'=>'c','ძ'=>'dz','წ'=>'ts','ჭ'=>'ch','ხ'=>'x','ჯ'=>'j','ჰ'=>'h');
        return strtr($s,$a);        
}

######################################################

class ZPAGREBI {
	public static function account_exists($acc_name){
		$exists = sql::CNT("SELECT * FROM `_cn_mod_users` WHERE `Id` = '".$acc_name."'");
		return ($exists>0) ? true : false;
	}
	
	public static function get_account_data($acc_name){
		$query = "SELECT * FROM `_cn_mod_users` WHERE `Id` = '".$acc_name."'";

		$result = sql::getRows($query);
		$_ACCOUNT = array();
		foreach ($result as $row){
			$_ACCOUNT['Id'] = $row['Id'];
			$_ACCOUNT['flname'] = $row['flname'];
			$_ACCOUNT['email'] = $row['email'];
		}
		return $_ACCOUNT;
	}

	public static function get_account_data_xml($acc_name){
		$_ACCOUNT = self::get_account_data($acc_name);
		$_RESPONSE  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$_RESPONSE .= "<response>\n";
		$_RESPONSE .= "<code>1</code>\n";
		$_RESPONSE .= "<account_name>".$_ACCOUNT['flname']."</account_name>\n";
		$_RESPONSE .= "<account_email>".$_ACCOUNT['email']."</account_email>\n";
		$_RESPONSE .= "</response>";
		return $_RESPONSE;
	}
	
	public static function insert_pay($acc_id,$acc_name,$txn_id,$amount){
		global $_CONFIG;
		$sql = "INSERT INTO ".$_CONFIG['tbl_prefix']."pay (uid,account, txn_id, amount, pay_date) VALUES
				('".@$acc_id."','".$acc_name."',".$txn_id.",".$amount.", now())";
		return @sql::insert($sql,true);
	}
}

###################################################################################

function account_exists($acc_name){
	global $_CONFIG;
	if($_CONFIG[$_GET['sid']]['game']=='ZPAGREBI')  return ZPAGREBI::account_exists($acc_name);
}

function get_account_data($acc_name){
	global $_CONFIG;
	if($_CONFIG[$_GET['sid']]['game']=='ZPAGREBI')  return ZPAGREBI::get_account_data($acc_name);
}

function get_account_data_xml($acc_name){
	global $_CONFIG;
	if($_CONFIG[$_GET['sid']]['game']=='ZPAGREBI')  return ZPAGREBI::get_account_data_xml($acc_name);
}

function insert_pay($acc_id,$acc_name,$txn_id,$amount){
	global $_CONFIG;
	if($_CONFIG[$_GET['sid']]['game']=='ZPAGREBI')  return ZPAGREBI::insert_pay($acc_id,$acc_name,$txn_id,$amount);
}

?>
