<?php

include_once('fix_mysql.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

$_CFG = array (
	'version' => '3.0.8',

	'mysqlServer'		=> 'localhost',
	'mysqlUser'			=> 'zgaprebi_admin',
	'mysqlPass'			=> 'y1fs1Jhp',
	'mysqlDB'			=> 'zgaprebi_base',
	'siteURL'			=> 'http://zgaprebi.ge/',
	'siteName'			=> 'zgaprebi.ge',
	'subDir'			=> '',
	'rootAddress'		=> 'http://zgaprebi.ge',
	'adminDir'			=> 'admin/',
	'display_errors'	=> array('92.51.101.242'),  # ips in full format
	'contactEmail'		=> 'enji777@gmail.com',
	'adminEmail'		=> 'support@zgaprebi.ge',
	'GETs'				=> array('l','p','sp'),
	'availLangs'		=> array('ka'=>'ქართული'),  //,'en'=>'English'
	'adminlang'			=> 'ka',
	'allow_ips'			=> array('82.211.155.53','176.73.185.73','149.3.31.211','92.51.101.242','46.49.0.102','185.26.182.30','46.49.51.216','78.133.192.36'),
	'gold_rate'			=> 1,
	'cash_rate'			=> 0.1,
	);

$_CFG['smtp']['host'] = 'mail.zgaprebi.ge';
$_CFG['smtp']['mail'] = 'noreply@zgaprebi.ge';
$_CFG['smtp']['pass'] = 'bp6WfcSm';

$_CFG['roles']['dashboard'] = 'Dashboard';
$_CFG['roles']['modules'] = 'მოდულები';
$_CFG['roles']['structure'] = ' სტრუქტურა';
$_CFG['roles']['texts'] = 'ტექსტები';
//$_CFG['roles']['gallery'] = 'გალერეა';
//$_CFG['roles']['statistics'] = 'სტატისტიკა';
$_CFG['roles']['others'] = 'სხვადასხვა';

########################################################################################
define('FILE_SECURITY_KEY','sddfgjnvkdsgbnesnvbkjbfas');

if(in_array($_SERVER['REMOTE_ADDR'],$_CFG['display_errors'])) error_reporting(-1);
ini_set("display_errors",(in_array($_SERVER['REMOTE_ADDR'],$_CFG['display_errors'])));


?>