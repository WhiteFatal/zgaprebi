<?php
ob_start();
session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

include ('includes/config.php');
include ('includes/functions.db.php');
include ('includes/functions.init.php');

//if(in_array($_SERVER['REMOTE_ADDR'], array('185.163.200.22'))) die('Under reconstruction...');

$vars = explode('/',@$_SERVER['REDIRECT_URL']);

foreach($vars as $k => $v) {
	$vars[$k] = trim($v);
	if($vars[$k]=='') unset($vars[$k]);
}

$vars = array_values($vars);

if(count($_CFG['availLangs'])>1){
$_GET['l'] = current($vars);

unset($vars[key($vars)]);
	$_GET['lvar'] = '/'.$_GET['l'];
} else {
	$_GET['l'] = key($_CFG['availLangs']);
	$_GET['lvar'] = '';
}

//SELECT * FROM cn_world_ips
//WHERE ip_from <= INET_ATON('176.221.141.71') and ip_to >=INET_ATON('176.221.141.71');

$vars = array_values($vars);
$_GET = array_merge(permalink2querystring($vars),$_GET);

//$_GET['p'] = current($vars);

unset($vars[key($vars)]);
foreach($vars as $k => $v){
	if(isset($vars[$k+1])) {
		$t = $vars[$k+1];
		if(substr($t,0,5)!='page-') $_GET[$v] = $t;
	}
}

if(isset($_GET['story'])){
	$_GET['story'] = current(explode('-',$_GET['story']));
}

$_GET['pg'] = (isset($_GET['pg'])) ? $_GET['pg'] : 1;	

include ('includes/global-tpl.php');
include ('includes/functions.lib.php');

include ('includes/functions.php');
include ('includes/mail/mail.php');
@include ('theme/functions.php');

$_get_langs = get::langs();

if(!@array_key_exists($_GET['l'],$_get_langs)){
	$_GET['l'] = key($_CFG['availLangs']);
}

$_GET['p'] = (@$_GET['p']!='') ? $_GET['p'] : 'index';	


$tmp = sql::getRows('SELECT * FROM `cn_langs` WHERE lang="'.$_GET['l'].'"');
foreach($tmp as $v) $_LANG[$v['name']] = $v['value'];

ob_start();
include('theme/index.php');
$html = ob_get_contents();
ob_end_clean();
$ogmeta = get_string_between($html,'<ogmeta>','</ogmeta>');
$html = str_replace('<meta http-equiv="ogmeta" />',$ogmeta,$html);
$html = str_replace('<ogmeta>'.$ogmeta.'</ogmeta>','',$html);
echo $html;
	
ob_end_flush();
?>