<?php

class modules {
	public static function getName($name){
		return sql::getElem("SELECT `name` FROM `cn_modules` WHERE `sys_name`='".$name."'");
	}
	public static function editor_fields_show_on_page($name){
		$r = array();
		$rows = sql::getRows("SELECT * FROM `cn_modules_structures` WHERE `module_sys_name`='".$name."' AND `show_on_page`=1  ORDER BY `ord`");
		foreach($rows as $v) $r[$v['field_sys_name']] = $v['field_name'];
		return $r;
	}
	public static function editor_fields_show_in_editor($name){
		$r = array();
		$rows = sql::getRows("SELECT * FROM `cn_modules_structures` WHERE `module_sys_name`='".$name."' AND `show_in_editor`=1");
		foreach($rows as $v) $r[$v['field_sys_name']] = $v['field_name'];
		return $r;
	}
	public static function editor_fields_show_in_filter($name){
		$r = array();
		$rows = sql::getRows("SELECT * FROM `cn_modules_structures` WHERE `module_sys_name`='".$name."' AND `show_in_filter`=1");
		foreach($rows as $v) $r[$v['field_sys_name']] = $v['field_name'];
		return $r;
	}
	public static function getStructure($name){
		$r = array();
		$rows = sql::getRows("SELECT * FROM `cn_modules_structures` WHERE `module_sys_name`='".$name."' ORDER BY `ord`");
		foreach($rows as $v){
			$r[$v['field_sys_name']]['field_type']		= $v['field_type'];
			$r[$v['field_sys_name']]['field_type_params']	= $v['field_type_params'];
			$r[$v['field_sys_name']]['field_name']		= $v['field_name'];
			if(($v['params'])!=''){
				$tmp = explode(";",$v['params']);
				foreach($tmp as $vv){
					$tmp2 = explode('=',$vv);
					if(trim(@$tmp2[1])!='') $r[$v['field_sys_name']]['params'][$tmp2[0]] = $tmp2[1];
				}
				if(!is_array(@$r[$v['field_sys_name']]['params'])) $r[$v['field_sys_name']]['params'] = $v['params'];
			}
		}
		return $r;
	}
	public static function need_upload_folder($name){
		$rows = sql::cnt("SELECT * FROM `cn_modules_structures` WHERE `module_sys_name`='".$name."' AND field_type in ('file','files')");
		return ($rows>0) ? true : false;
	}
	public static function has_submobule($mod){
		foreach(self::getStructure($mod) as $k => $v){
			if($v['field_type']=='submodule') return array('field2union'=>$k,'modname'=>$v['field_type_params'],'field'=>$v['params']);
		}
		return false;
	}
	public static function getRow($mod,$cat_id,$lang){
		$has_submobule = self::has_submobule($mod);
		if($has_submobule===false){
			return sql::getRow("SELECT * FROM `_cn_mod_".$mod."` WHERE `cat_id`='".$cat_id."' AND lang='".$lang."'");
		} else {
			$smt = sql::getCol("SELECT concat_ws('','smt.','`',`field_sys_name`,'`') FROM `cn_modules_structures`
																				WHERE `module_sys_name`='".$has_submobule['modname']."'");
			echo $sql = "SELECT mt.*, ".implode(',',$smt)." FROM `_cn_mod_".$mod."` as mt
												LEFT JOIN `_cn_mod_".$has_submobule['modname']."` as smt
												ON mt.`".$has_submobule['field2union']."`=smt.`cat_id`
												WHERE mt.`cat_id`='".$cat_id."' AND mt.`lang`='".$lang."' AND smt.`lang`='".$lang."'";
			return sql::getRow($sql);	
		}
	}
}
function parse_params($ini){
	$r = array();
	if(($ini)!=''){
		$tmp = explode("\n",$ini);
		foreach($tmp as $vv){
			if(trim($vv)!=''){
				$tmp2 = explode('=',$vv);
				$r[$tmp2[0]] = $tmp2[1];
			}
		}
	}
	return $r;
}

class poles {
	public static function getName($pol_id){
		return sql::getElem("SELECT `name` FROM `cn_pols` WHERE `cat_id`=".$pol_id." AND `lang`='ka'");
	}
}

class albums {
	public static function getName($id,$lang){
		return sql::getElem("SELECT `name` FROM `cn_albums` WHERE `cat_id`='".$id."' and lang='".$lang."'");
	}
	public static function getMainPhoto($id,$lang){
		return sql::getElem("SELECT `mainphoto` FROM `cn_albums` WHERE `cat_id`='".$id."' and lang='".$lang."'");
	}
	public static function getPhotos($id,$lang){
		return sql::getRows("SELECT `file`, `title` FROM `cn_albums_photos` WHERE `album_id`='".$id."' and lang='".$lang."'");
	}
}


class image{
	public static function check($photo){
		$c = @getimagesize($photo);
		return (count($c)>2) ? true : false;
	}
	public static function isequalsize($photo,$w,$h){
		if(@$w<1 || @$h<1) return true;
		$photo_need = $w.$h;
		$inf = self::imagecreatefromx($photo);
		@$photo_is = imagesx($inf).imagesy($inf);
		return ($photo_need!=$photo_is) ? false : true;
	}
	public static function resize($file, $newfile, $path, $maxwidth, $maxheight){
		GD2::resize($file, $newfile, $path, $maxwidth, $maxheight);
	}
	public static function crop($file, $x, $y, $w, $h){
		GD2::crop($file, $x, $y, $w, $h);
	}
	public static function imagecreatefromx($img){
		$ext = strtolower(end(@explode('.',$img)));
		if($ext=='jpg')  return @imagecreatefromjpeg($img);
		if($ext=='jpeg') return @imagecreatefromjpeg($img);
		if($ext=='gif')  return @imagecreatefromgif($img);
		if($ext=='png')  return @imagecreatefrompng($img);
	}
	public static function geometry($img){
		if(!file_exists($img)) return 'error_find_file';
		$s = getimagesize($img);
		if($s[0]<$s[1]) return 'vertical';
		if($s[0]>$s[1]) return 'horizontal';
		if($s[0]==$s[1]) return 'square';
	}
}

class is {
	public static function imagick(){
		$check_1 = $check_2 = $check_3 = false;
		$check_1 = class_exists('Imagick');
		$check_2 = class_exists('ImagickDraw');
		$check_3 = class_exists('ImagickPixel');
		return (($check_1===true) && ($check_2===true) && ($check_3===true));
	}
	public static function adminIp(){
		$ips = settings::get('adminIps');
		return ($ips==$_SERVER['REMOTE_ADDR'] || @in_array($_SERVER['REMOTE_ADDR'],$ips)) ? true : false;
	}
	public static function developerIp(){
		global $_CFG;
		return (@in_array($_SERVER['REMOTE_ADDR'],$_CFG['display_errors'])) ? true : false;
	}
}

class has{
	public static function role($role){
		global $_CFG;
		$r = sql::getElem("SELECT `roles` FROM `cn_adminusers` WHERE Id=".$_SESSION['userId']);
		$r = explode(',',$r);
		return in_array($role,$r);
	}
	public static function rolePercent($userId){
		global $_CFG;
		$r = sql::getElem("SELECT `roles` FROM `cn_adminusers` WHERE Id=".$userId);
		$r = explode(',',$r);
		$cr = count($r);
		$mainroles = count($_CFG['roles']);
		$mainroles = $mainroles + sql::cnt("SELECT * FROM cn_modules");
		return round((100/$mainroles)*$cr);
	}
}

class GD2{
	public static function resize($file, $newfile, $path, $maxw, $maxh){
		$image_type = end(explode(".", strtolower($file)));
		$source = image::imagecreatefromx($path.$file);
		$fullpath = $path.$newfile;
		list($width, $height) = getimagesize($path.$file);
		if(($width==$maxw && $height==$maxh) || ($maxw>$width && $maxh>$height)) {
			copy($path.$file,$fullpath);
			return true;
		}
		if (image::geometry($path.$file)=='horizontal') {
			$nh = $maxh;
			$nw = $width * ($nh/$height);
			if($nw<$maxw) {
				$nw = $maxw;
				$nh = $height * ($nw/$width);
			}
		}else if (image::geometry($path.$file)=='vertical') {
			$ratio = $maxw / $width;
			$nw = $maxw;
			$nh = $height * $ratio;
		}else if (image::geometry($path.$file)=='square') {
			if($maxw>$maxh){
				$nw = $maxw;
            	$nh = $nw;
			}else{
				$nw = $maxh;
            	$nh = $nw;
			}
		}
		$nw = ceil($nw); $nh = ceil($nh);
		$thumb = imagecreatetruecolor($nw, $nh);
		imagecopyresampled($thumb, $source, 0, 0, 0, 0, $nw, $nh, $width, $height);
		imagejpeg($thumb, $fullpath, 100);
		return true;
	}
	public static function crop($filename, $x, $y, $w, $h){
		list($current_width, $current_height) = getimagesize($filename);
		$left = ceil($x);
		$top = ceil($y);
		$crop_width = ceil($w);
		$crop_height = ceil($h);
		$canvas = imagecreatetruecolor($crop_width, $crop_height);
		$current_image = imagecreatefromjpeg($filename);
		imagecopy($canvas, $current_image, 0, 0, $left, $top, $current_width, $current_height);
		imagejpeg($canvas, $filename, 100);
		return true;
	}
}


$LANG = array(
	'sitetitle' => 'ANI ადმინპანელი',
	'fullversion' => 'CONNECT CMS 2008 &copy; ვერსია '.$_CFG['version'],
	'version' => $_CFG['version'],
	'logout' => 'გასვლა',
	'anicopyright' => '<table border="0" cellspacing="0" id="versionTable" cellpadding="0" style="height:17px; cursor:pointer; float:left"><tr><td style="background:url(img/sign.png) left top; color:#FFF;  font-size:12px; padding-top:1px; font-family:\'Courier New\', Courier, monospace; padding-left:5px; font-weight:bold" width="29">ANI</td><td style="background:url(img/sign.png) right top; font-size:12px; font-family:\'Courier New\', Courier, monospace; color:#FFF; padding-right:5px">Admin '.$_CFG['version'].'</td></tr></table>', 
);


function checklang($lang){
	$avalLangs = array('ka','en','ru');
	return (in_array($lang, $avalLangs)) ? $lang : 'en';
}

function lang($str,$k='default'){
	global $_LANG;
	return (isset($_LANG[$k][$str])) ? $_LANG[$k][$str] : '';
}

function chmonth($date,$format){
	$tfgeo = array(
		'01'=>'იანვარი', '02'=>'თებერვალი', '03'=>'მარტი', '04'=>'აპრილი',
		'05'=>'მაისი', '06'=>'ივნისი', '07'=>'ივლისი', '08'=>'აგვისტო',
		'09'=>'სექტემბერი', '10'=>'ოქტომბერი', '11'=>'ნოემბერი','12'=>'დეკემბერი');
	$tsgeo = array(
		'01'=>'იან', '02'=>'თებ', '03'=>'მარ', '04'=>'აპრ',
		'05'=>'მაი', '06'=>'ივნ', '07'=>'ივლ', '08'=>'აგვ',
		'09'=>'სექ', '10'=>'ოქტ', '11'=>'ნოე','12'=>'დეკ' );
	$en2ge = array(
		'Jan'=>'იან', 'Feb'=>'თებ', 'Mar'=>'მარ', 'Apr'=>'აპრ',
		'May'=>'მაი', 'Jun'=>'ივნ', 'Jul'=>'ივლ', 'Aug'=>'აგვ',
		'Sep'=>'სექ', 'Oct'=>'ოქტ', 'Nov'=>'ნოე','Dec'=>'დეკ' );
	$tfeng = array(
		'01'=>'January', '02'=>'February', '03'=>'March', '04'=>'April',
		'05'=>'May', '06'=>'June', '07'=>'July', '08'=>'August',
		'09'=>'September', '10'=>'October', '11'=>'November','12'=>'December' );
	$tfrus = array(
		'01'=>'Январь', '02'=>'Февраль', '03'=>'Март', '04'=>'Апрель',
		'05'=>'Май', '06'=>'Июнь', '07'=>'Июль', '08'=>'Август',
		'09'=>'Сентябрь', '10'=>'Октябрь', '11'=>'Ноябрь','12'=>'Декабрь' );
	$seng = array(
		'01'=>'Jan', '02'=>'Feb', '03'=>'Mar', '04'=>'Apr',
		'05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Aug',
		'09'=>'Sep', '10'=>'Oct', '11'=>'Nov','12'=>'Dec' );
	if($format=='ka'){
		return strtr($date, $tfgeo);
	}elseif($format=='en'){
		return strtr($date, $tfeng);
	}elseif($format=='ru'){
		return strtr($date, $tfrus);
	}elseif($format=='fgeo'){
		return strtr($date, $tfgeo);
	} elseif($format=='sgeo') {
		return strtr($date, $tsgeo);		
	} elseif($format=='en2ge') {
		return strtr($date, $en2ge);		
	} elseif($format=='seng') {
		return strtr($date, $seng);
	} elseif($format=='feng') {
		return strtr($date, $tfeng);		
	} elseif($format=='tmfka') {
		$dates = explode('-',$date);
		return $dates[2].' '.strtr($dates[1], $tfgeo).' '.$dates[0];
	} elseif($format=='tmsgeo') {
		$dates = explode('-',$date);
		return $dates[2].'-'.strtr($dates[1], $tsgeo).'-'.$dates[0];
	} elseif($format=='tmfen') {
		$dates = explode('-',$date);
		return $dates[2].' '.strtr($dates[1], $tfeng).' '.$dates[0];
	} elseif($format=='tmfru') {
		$dates = explode('-',$date);
		return $dates[2].' '.strtr($dates[1], $tfrus).' '.$dates[0];
	} elseif($format=='tmfgeowoy') {
		$dates = explode('-',$date);
		return $dates[2].'-'.strtr($dates[1], $tfgeo);
	}
}

function setlangurl($chlang)  {
	global $_REQUEST;
	global $_SERVER;
	if (isset($_REQUEST['lang'])) {
		$langvar = 'lang='.$_REQUEST['lang'];
		return '?lang='.$chlang.str_replace($langvar,'',$_SERVER["QUERY_STRING"]);
	} else {
		return '?'.$chlang.'&'.$_SERVER["QUERY_STRING"];	
	}
}


function cropDate($date){
	$dates = explode(' ',$date);
	return $dates[0];
}

function langDate($date){
	global $LANG;
	$dates = explode(' ',$date);
	list($y,$m,$d) = explode('-',$dates[0]);
	return $d . "-" . $LANG[$m] . "-" . $y;
}

function date_add_min($givendate,$min=0) {
      $cd = strtotime($givendate);
    return date('Y-m-d H:i:s', mktime(date('h',$cd), date('i',$cd)+$min, date('s',$cd), date('m',$cd), date('d',$cd), date('Y',$cd)));
}

function mail_ip_add_request($email=false){
	global $_CFG;
	$email = ($email!=false) ? $email : $_CFG['adminEmail'];
	$ip = getenv("REMOTE_ADDR");
	$tscode = md5(date('YmdHis'));
		settings::save('addIpRequest',$tscode);
		$link = $_CFG['siteURL'].'admin/login.php?tscode='.$tscode.'&ip='.$ip.'&m='.$email;
		$messageText  = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
		$messageText .= '<body>';
		$messageText .= '<div style="font-size:12px">';
		$messageText .= 'ვებ-გვერდზე <b>'.$_CFG['siteURL'].'</b> '.date('Y-M-d H:i:s').' დროს იყო ადმინპანელში შესვლის მცდელობა, რომელიც ვერ განხორციელდა IP უსაფრთხოების გამო.<br>';
		$messageText .= 'თუ ეს თქვენ იყავით და გსურთ ეს IP (<b>'.$ip.'</b>) დაამატოთ სისტემაში უფლებადაშვებული IP-თა შორის მიყევით ქვემოთ მოცემულ ბმულს.<br>';
		$messageText .= '<a href="'.$link.'">'.$link.'</a><br>';
		$messageText .= 'და თუ ეს თქვენ არ იყავით უბრალოდ უგულებელყავით ეს წერილი. <br><br>';
		$messageText .= '<span style="color:#655">საიტის მართვის სისტემა<br>CONNECT CMS</span></div>';
		$messageText .= '</body></html>';
		$title = 'adminshi dashvebis moTxovna';
		return smtpmail::send($email,$title, $messageText);
}
function get_admin_ips(){
		$ips = sql::getCol("SELECT `paramValue` FROM `cn_settings` WHERE `paramName`='adminIps'");
		return (is_array($ips))? $ips : array();
}
function get_admin_ips_by_value($value){
	$tmp = array();
	$ips = unserialize($value);
	if(is_array($ips)){
		foreach($ips as $v){ $tmp[] = $v['ip']; }
	}
	return $tmp;
}

function add_admin_ip($email, $ip, $comment){
	sql::insert("INSERT INTO `cn_settings` SET `paramName` = 'adminIps',
												`paramValue`='".$ip."',
												`meta`='".$comment."',
												`meta_2`='".date('Y-m-d H:i:s')."'");
}

function is_valid_admin_ip(){
	$allowIps = get_admin_ips();
	$tmp = array();
	foreach($allowIps as $k => $v){ $tmp[$v] = $v; }
	return allowIp($tmp);
}

function is_valid_admin(){
	$COUNT = sql::cnt("SELECT * FROM `cn_adminusers`
			 		WHERE `Id`='".@$_SESSION['userId']."' AND `currsesid`='".@$_SESSION['sessionId']."'");
	return ($COUNT==1 && is_valid_admin_ip()==true) ? true : false;
}

function is_valid_admin_session(){
	global $_CFG;
	$COUNT = sql::cnt("SELECT * FROM `cn_adminusers`
			 		WHERE `Id`='".@$_SESSION['userId']."' AND `currsesid`='".@$_SESSION['sessionId']."'");
	if ($COUNT==1 && is_valid_admin_ip()==true) {
		return true;
	} else {
		ob_clean();
		header('Location: '.$_CFG['rootAddress'].'/'.$_CFG['adminDir'].'login.php');
		die();
	}
}

function siteEditHeadHtml(){
	if(is_valid_admin()==true){
	$str = '<style type="text/css">
		#adminSector{
			position:fixed; right:-1px; top:0; width:130px; height:150px; z-index:999;
			background:url(admin/img/siteEditor.png) no-repeat; padding-top:38px; color:#FFF;
		}
		#adminSector div{ text-decoration:none; font-size:9pt; padding:35px 0 0 30px; text-align:center; color:#FFF; margin-bottom:27px }
		#adminSector a{ color:#FFF; text-decoration:none; margin-left:60px; font-size:12px; font-weight:bold }
		#adminSector a:hover{ text-decoration:underline }
		</style>
		<div id="adminSector"><div>რედაქტირება</div><a href="javascript:void(0)" onclick="logOutAdmin()">გასვლა</a>&nbsp;&raquo;</div>
		<script type="text/javascript">
		function logOutAdmin(){
			if(confirm("დარწმუნებული ხართ გსურთ გასვლა?")==true){
				$.post("admin/login.php?act=logout", {empty:"empty"}, function(data){
					if(data!="") document.location.reload();
				});
			}
		} </script>';
		} else {
			$str = '';
		}
	return $str;
}


function siteEditPages($id,$pos='0'){
	global $_CFG;
	if (is_valid_admin()==true) {
		return "<img src=\"".$_CFG['siteURL'].$_CFG['adminDir']."img/editPage.png\" style=\"margin:".$pos."; cursor:pointer\" class=\"editIcon\" onclick=\"$.winPopup('".$_CFG['siteURL'].$_CFG['adminDir']."ajax/page_edit.php?Id=".$id."&l=".$_GET['l']."','750','650')\" />";
	} else { return ""; }
}

function siteEditNews($id,$pos='20px 0 0 20px'){
	global $_CFG;
	if (is_valid_admin()==true) {
		return "<img src=\"".$_CFG['adminDir']."/img/editPage.png\" style=\"margin:".$pos."; cursor:pointer\" class=\"editIcon\" onclick=\"$.winPopup('".$_CFG['adminDir']."ajax/news_edit.php?Id=".$id."&l=".$_GET['l']."',850,680)\" />";
	} else { return ""; }
}


 function U2U($str) {
	$translate = array(	'ა'=>'&#4304;','ბ'=>'&#4305;','გ'=>'&#4306;','დ'=>'&#4307;','ე'=>'&#4308;','ვ'=>'&#4309;',
						'ზ'=>'&#4310;','თ'=>'&#4311;','ი'=>'&#4312;','კ'=>'&#4313;','ლ'=>'&#4314;','მ'=>'&#4315;',
						'ნ'=>'&#4316;','ო'=>'&#4317;','პ'=>'&#4318;','ჟ'=>'&#4319;','რ'=>'&#4320;','ს'=>'&#4321;',
						'ტ'=>'&#4322;','უ'=>'&#4323;','ფ'=>'&#4324;','ქ'=>'&#4325;','ღ'=>'&#4326;','ყ'=>'&#4327;',
						'შ'=>'&#4328;','ჩ'=>'&#4329;','ც'=>'&#4330;','ძ'=>'&#4331;','წ'=>'&#4332;','ჭ'=>'&#4333;',
						'ხ'=>'&#4334;','ჯ'=>'&#4335;','ჰ'=>'&#4336;');
 return strtr($str, $translate);
}


function setMenuOrder($position,$minpos,$maxpos){
	if ($position==$minpos) { echo "<div style='width:30px' align='right'>&nbsp;<img src='img/downarrow.png' /></div>"; } elseif ($position==$maxpos) { echo "<div style='width:30px' align='left'><img src='img/uparrow.png' />&nbsp;</div>"; } else { echo "<div style='width:30px' align='center'><img src='img/uparrow.png' />&nbsp;<img src='img/downarrow.png' />"; }
}


function loginStatus($status) {
	if ($status==1) { $tmp="<span style='color:green'><b>Success!</b></span>"; }
	elseif ($status==2) { $tmp="<span style='color:red;'><b>Error!</b></span>"; }
	else { $tmp="<span style='color:red;'><b>Not Success!</b></span>"; }
return $tmp;
}

function getTableSize($table,$size) {
	$sql = "SHOW TABLE STATUS LIKE '".$table."'";
	$result = sql::getRow($sql);
	return $result[$size];
}


function checkFunction($funcs) {
	return (!function_exists($funcs)) ? 'no' : 'yes';
}

function geo_date($date,$sep = ' ') {
		$arr = array ("jan"=>"იან","feb"=>"თებ","mar"=>"მარ","apr"=>"აპრ","may"=>"მაი","jun"=>"ივნ",
					  "jul"=>"ივლ","aug"=>"აგვ","sep"=>"სექ","oct"=>"ოქტ","nov"=>"ნოე","dec"=>"დეკ");
  return str_replace(' ',$sep,strtr(strtolower($date),$arr));
}

function logout() {
	session_destroy();
	redirect('login.php?msg=logoutOk');
}


function timeout() {
	$_SESSION['lastUrl']="";
	if (!isset($_SESSION['lasttime'])) { $_SESSION['lasttime']=date('d/m/Y H:i:s'); }
	$nexttime=time() - ($_SESSION['Timeout']*60);
	$musha=date('d/m/Y H:i:s', $nexttime);
	if ($_SESSION['lasttime']<=$musha) {
		$_SESSION['lastUrl']=full_url();
		header("location: login.php?act=timeout");
	} else {
		$_SESSION['lasttime']=date('d/m/Y H:i:s');
	}
}


function pageFound($pagetype,$page){
	switch($pagetype) {
		case 'FILE':
			if ( (strlen(trim($page))>0) && (file_exists("./../pages/".$page)) ){
				return '<span style="color:green">"<b>pages</b>"-ში  ფაილი სახელად '.$page.' შექმნილია</span>';
			} else {
				return '<span style="color:red">"<b>pages</b>"-ში შექმენით ფაილი სახელად <b>'.$page.'</b></span>';
			}
		break;
		default:
			return "Error page type!";
		break;
	}
}

function array_rv($array,$string){
	foreach($array as $key => $value) {
		if($value == $string) { unset($array[$key]); }
	}
	return array_values($array);
}


function adminLoginLog($user,$allowIpRule,$status,$log=''){
	sql::insert("INSERT INTO `cn_adminlog` SET
				`logDate`=now(), `IP`='".getenv("REMOTE_ADDR")."', `status`=".$status.",
				`user`='".$user."', `log`='".$log."', comment='".$allowIpRule."'");
}


function adminLog($log=''){
	sql::insert("INSERT INTO `cn_adminlog` SET
				`logDate`=now(), `IP`='".getenv("REMOTE_ADDR")."', `status`=1,
				`user`='".$_SESSION['user']."', `log`='".$log."'");
}


function IS_SECURITY_SESSION(){
	$sqlSec = sql_select("SELECT * FROM `cn_adminusers` where `Id`='".$_SESSION['userId']."'");
	while ($row_Sec = mysql_fetch_object($sqlSec)){ 
		if ($row_Sec->currsesid!=$_SESSION['Id']) {
			unset($_SESSION);
			header("location: login.php");
			die();
		}
	}
return;
}


################## TEMPLATE ####################

function tableStyle($th,$title='',$attr=''){
	global $_CFG;
	$align = (str_replace('align=','',$attr)==$attr) ? 'align="center"' : '' ;
	if($th=='header'){
		return '<table border="0" cellspacing="0" cellpadding="0" '.$align.' class="trumbTable" '.$attr.'><thead><tr><th class="th1" valign="top"><img src="'.$_CFG['siteURL'].'/'.$_CFG['adminDir'].'/img/corner_tl.png" /></th><th class="th2">&nbsp;</th><th class="th1" valign="top"><img src="'.$_CFG['siteURL'].'/'.$_CFG['adminDir'].'/img/corner_tr.png" /></th></tr><tr><th class="th4">&nbsp;</th><th class="th5">'.$title.'</th><th class="th6">&nbsp;</th></tr></thead><tfoot><tr><td class="tf1"  valign="bottom"><img src="'.$_CFG['siteURL'].'/'.$_CFG['adminDir'].'/img/corner_bl.png" /></td><td class="tf2">&nbsp;</td><td class="tf1" valign="bottom"><img src="'.$_CFG['siteURL'].'/'.$_CFG['adminDir'].'/img/corner_br.png" /></td></tr></tfoot><tbody><tr><td class="tb1">&nbsp;</td><td class="tb2" valign="top">';	
	} elseif($th=='footer'){
		return '</td><td class="tb3">&nbsp;</td></tr></tbody></table>';
	} 
}

function documentDomBottom(){
	return '$(document).ready( function() {
	$("input:text, input:password, textarea").css(\'border\',\'solid 1px #A5ACB2\');
	$("table.redSolid > tbody > tr:even").addClass(\'redSolid_tr_even\')
	$("table.redSolid > tbody > tr > td").css({\'border-bottom\':\'solid 1px #E5E5E5\',\'border-right\':\'solid 1px #E5E5E5\'})
	$("table.redSolid").css({\'border-right\':\'none\'})
	$("table.redSolid > tbody > tr").tableRowHover(\'#EEE\');
	$(".imgOpasOn").iconImg();
	$("a[rel*=facebox]").facebox();
	$("input:not(:disabled),textarea:not(:disabled)").focus(function () {
		$(this).css({"background-color":"#FFFFDD","border-color":"#D2AB60","outline":"solid 1px #EEC77C"});
	});
	 $("input:not(:disabled),textarea").blur(function () {
		$(this).css({"background-color":"#FFFFFF","border-color":"#C0C0C0","outline":"none"});
	});
 	GeoKBD.map({fields: [\'.geo\']});
  });';
}


function get_client_country(){
	$ip  = getenv("REMOTE_ADDR");
	$tmp = 'EN';
	$sql = sql_select("SELECT country_code2 country FROM `cn_iptoc`
									WHERE IP_FROM<=inet_aton('".$ip."') AND IP_TO>=inet_aton('".$ip."') ");
	while ($row = @mysql_fetch_object($sql)){
		$tmp = $row->country;
	}
 return strtolower($tmp);
}

function get_new_cat_id($table){
	$newId = 1;
	$sql = sql::getElem("SELECT max(cat_id) cat_id FROM `".$table."`");
	if(isset($sql)) $newId = $sql+1;
 return $newId;
}


function get_newPid(){
	$newId = 1;
	$sql = sql_select("SELECT max(pid) pid FROM `cn_pages`");
	while ($row = @mysql_fetch_object($sql)){
		$newId = $row->pid+1;
	}
 return $newId;
}


function get_pageTypeByPID($pid){
	$sql = sql_select("SELECT pagetype FROM `cn_pages` where `pid`=".$pid);
	while ($row = mysql_fetch_object($sql)){
		$pageType = $row->pagetype;
	}
	return $pageType;
}


function get_parameter($p){
	return @trim(sql::getElem("SELECT paramValue FROM `cn_settings` where `paramName`='".$p."'"));
}

class settings {
	public static function save($p,$v){
		$exits = sql::cnt("SELECT * FROM `cn_settings` where `paramName`='".$p."'");
		if($exits<1){
			sql::insert("INSERT INTO `cn_settings` SET `paramName`='".$p."', `paramValue`='".$v."'");
		} else {
			sql::update("UPDATE `cn_settings` SET `paramValue`='".$v."' WHERE `paramName`='".$p."'");
		}
	}
	public static function add($p,$v){
		sql::insert("INSERT INTO `cn_settings` SET `paramName`='".$p."', `paramValue`='".$v."'");
	}
	public static function get($p){
		$d = sql::getCol("SELECT paramValue FROM `cn_settings` where `paramName`='".$p."'");
		return (count($d)==1) ? $d[0] : $d;
	}
	public static function remove($p,$v){
		$d = sql::delete("DELETE FROM `cn_settings` where `paramName`='".$p."' AND `paramValue`='".$v."'");
	}
	
	public static function first_run(){
		#############################
		if(!is_numeric(settings::get('photo_thumb_w'))) 		self::save('photo_thumb_w','180');
		if(!is_numeric(settings::get('photo_thumb_h'))) 		self::save('photo_thumb_h','120');
		if(!is_numeric(settings::get('photo_w')))	 			self::save('photo_w','1024');
		if(!is_numeric(settings::get('photo_h'))) 				self::save('photo_h','768');
		if(!is_numeric(settings::get('albums_thumb_w'))) 		self::save('albums_thumb_w','200');
		if(!is_numeric(settings::get('albums_thumb_h')))	 	self::save('albums_thumb_h','100');
		if(settings::get('adminIps')=='' || count(settings::get('adminIps'))==0)	self::add('adminIps','127.0.0.1');
		#############################	
	}
}
function get_terminology($lang){
	$tmp = array();
	$tr = array('default'=>'d','personal'=>'p');
	$sql = sql::getRows("SELECT * FROM `cn_terminologys` WHERE `lang`='".$lang."'");
	foreach($sql as $row){
		$tmp[strtr($row['type'],$tr)][$row['desc']] = $row['text'];
	}
	return $tmp;
}


function get_tables_with_langs(){
	global $_CFG;
	get_db_connection();
	$langTables = array();
	$tables = mysql_list_tables($_CFG['mysqlDB']);
	for($i = 0; $i < mysql_num_rows($tables); $i++){
		$table = mysql_tablename ($tables, $i);
			$records = mysql_query('SHOW FIELDS FROM `'.$table.'`');
			while ( $record = mysql_fetch_assoc($records) ) {
					if($record['Field']=='lang') $langTables[] = $table;
			}
	}
	return $langTables;
}


function del_langs_all_tables($lang){
	foreach(get_tables_with_langs() as $key => $table){
		sql_delete("DELETE FROM `".$table."` WHERE `lang`='".$lang."'");
	}
}


function get_google_analistic(){
	return htmlspecialchars_decode(get_parameter('googleAnalytics'));
}


function act_icon($src,$t='',$attr=''){
	return '<img src="'.$src.'" title="'.$t.'" '.$attr.' class="imgOpasOn" align="absmiddle" />';
}


function getPageByCatId($id){
	global $_CFG;
	global $_LANG;
	global $_MENU;
	$sql = sql::getRows("SELECT * FROM `cn_sitemap` WHERE  enabled=1 AND `lang`='".$_GET['l']."' AND cat_id =".$id);
	foreach ($sql as $row){
		if($row['pagetype']=='FILE') {
			$tmp = explode('?',$row['source']);
			$file = $tmp[0];
			$_PARAMS = isset($tmp[1])?$tmp[1]:'';
			if(file_exists('theme/'.$file)){
				include('theme/'.$file);
			} else { echo '<div style="padding:100px">theme/'.$file.' not found</div>'; }
			return;
		} elseif($row['pagetype']=='TEXT') {
				$_PAGE['title'] = ''; $_PAGE['text'] = ''; $_PAGE['editor'] = siteEditPages($id);
			$sql = sql::getRows("SELECT * FROM `cn_pages` WHERE `lang`='".$_GET['l']."' AND pid =".$id );
			foreach ($sql as $row){
				$_PAGE['title'] = $row['title'];
				$_PAGE['text'] = $row['text'];
				$_PAGE['editor'] = siteEditPages($row['pid']);
			}
				include('theme/text.php');
			return;
		}
	}
		$sql = sql::getRows("SELECT * FROM `cn_pages` WHERE `lang`='".$_GET['l']."' AND pid =53" );
		foreach($sql as $row){
			$_PAGE['title'] = $row['title'];
			$_PAGE['text'] = $row['text'];
			$_PAGE['editor'] = siteEditPages($row['pid']);
			include('theme/text.php');
		}
		return;
}

function lang_select(){
	global $_get_langs;
	$tmp = '<select onchange="$.go(\'?p='.$_GET['p'].'&l=\'+this.value)" id="lang" name="lang">';
	foreach($_get_langs as $k => $v) {
		$tmp .= '<option '.(($_GET['l']==$k) ? 'selected="seleted"' : '').' value="'.$k.'">'.$v.'</option>';
	}
	$tmp .= '</select>';
return $tmp;
}

function getTextPageByCatId($id){
	$sql = sql_select("SELECT * FROM `cn_pages` WHERE `lang`='".$_GET['l']."' AND `pid`=".$id);
	while ($row = mysql_fetch_object($sql)){
		$txt = htmlspecialchars_decode($row->text);
	}
		return @$txt.siteEditPages($id,'0 0 0 10px');
}


function manuStyle($id){
	if($id==$_GET['cat'] && $_GET['cat']==4){
		return 'blackWhite';
	} else if($id==$_GET['cat'] && $_GET['cat']!=4) {
		return 'whiteBlack';
	}
}


function checkTrumbs($path){
	$_IMG = array();
	$_TRM = array();
	$dirhandle = opendir($path);
	while ($file = readdir($dirhandle)) {
		if (strtolower(end(explode(".", $file)))=='jpg') {
			if(current(explode("_", $file))=='trm'){
				$_TRM[] = $file;
			} else {
				$_IMG[] = $file;
			}
		}
	}
	if(count($_IMG)!=count($_TRM)) {
		if(count($_IMG>0)){
			foreach ($_TRM as $value) {
				@unlink($path.'/'.$value);
			}
		}
		foreach ($_IMG as $value) {
			mod_imageResize($value, $path.'/');
		}
		echo 'successfully finished any actions in this photos';
	}
}


/*function galleryStr2array($string){	
	$res = array();
	$rows = explode('[rowend]', $string);
	foreach($rows as $key => $row){
		list($folder,$langsStr) = explode('[descr]', $row);
		$strends = explode('[strend]', $langsStr);
		foreach($strends as $k => $v){
			list($lang,$val) = explode('=', $v);
			$res[$folder][$lang] = $val;
		}
	}
return $res;
}
*/

/*function get_all_terminologys(){
	$tmp = array();
	$lang = isset($_GET['l']) ? $_GET['l'] : $_SESSION['l'];
	$result = sql::getRows("SELECT * FROM `cn_terminologys` WHERE `lang`='".$lang."'");
	foreach($result as $row) { $tmp[$row['type']][$row['desc']] = $row['text']; }
	return $tmp;
}
*/
function add_zero($num){
	return ($num>0 && $num<=9) ? '0'.$num : $num;
}

function enable_sheduleNews(){
	sql::update("UPDATE `cn_news` SET `status`= 'public' WHERE `status`= 'shedule' AND addDate<=now()");
}

class get {
	public static function block($m){
		include('theme/blocks/'.$m);
	}
	public static function newcatId($table){
		$cat_id = sql::getElem("SELECT max(cat_Id) FROM `".$table."`");
		return (isset($cat_id)) ? ($cat_id+1) : 1;
	}	
	public static function version(){
		global $_CFG;
		return $_CFG['version']."(".$_CFG['build'].")";
	}
	
	public static function new_version_status(){
		global $_CFG;
		$t1 = str_replace('.','',$_CFG['version']);
		$l1 = strlen($t1);
		$t2 = str_replace('.','',get::last_version());
		$l2 = strlen($t2);
		if($l1>$l2){
			$t2 .= get::zeros($l2-$l1);
		} elseif($l1<$l2){
			$t1 .= get::zeros($l1-$l2);
		}
		return ($t2>$t1) ? ("თქვენი მიმდინარე ვერსიაა ".$_CFG['version']."\nბოლო ვერსიაა ".get::last_version()) : false;
	}
	
	private static function zeros($z){
		$s = '';
		for($i=0; $i<$z; $i++){
			$s .= '0';
		}
		return $s;
	}

	public static function last_version(){
		global $_CFG;
		$v = @file_get_contents('http://about.ge/cadmin/');
		$v = @unserialize($v);
		if(!is_array($v)) $_CFG['version'].'^';
		return $v['version'];
	}

	public static function url(){
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
	}
	
	public static function permalink($Id = -1){
		if($Id=='-1') return self::url();
		global $_CFG;
		if($type!='VOID'){
			return ($p==NULL) ? $_CFG['siteURL'] : ($_CFG['siteURL'].$p);
		} else {
			return "&raquo;&raquo;&raquo;&nbsp;&nbsp;";	
		}
	}

	public static function tpl($tpl){
		global $_CFG;
		include("theme/tpl/".$tpl);
	}
	
	public static function news_cat_name($c){
		//return $c;
	}
	
	public static function news($lim='-1'){
		$addSQL  =($lim>=0) ? ("LIMIT 0, ".$lim) : "";
		$n = sql::getRows("SELECT * FROM `cn_news` WHERE lang='".$_GET['l']."' ORDER BY addDate DESC ".$addSQL );
		foreach($n as $k => $v){
			$n[$k]['cf'] = unserialize(@$v['customFields']);
			$ntext = html_entity_decode($n[$k]['text']);
			$tmp = preg_split("/<hr.+>/", $ntext);
			if(count($tmp)==2){
				$n[$k]['stext'] = $tmp[0];
				$n[$k]['text'] = $tmp[1];			
			} else {
				$n[$k]['stext'] = "";
				$n[$k]['text'] = $ntext;
			}
		}
		return $n;
	}
	public static function pageByCatId($id,$a = false){
		global $_CFG, $_TPL;
		$t = sql::getRow("SELECT * FROM ani_sitemap WHERE lang='".$_GET['l']."' AND cat_id=".$id);
		$t['source'] = htmlspecialchars_decode($t['source']);
		$t['source'] = strtr($t['source'],$_TPL);
		$t['source'] = preg_replace('/style="white-space:pre;">/','style="white-space:pre">'.nbsp(7).'</span>',htmlspecialchars_decode($t['source']));
		if(is_valid_admin()) {
			$t['source'] = '*'.$t['source'];
		}
		return ($a==false) ? $t : $t[$a];
	}
	
	public static function pageById($id,$a = false){
		global $_CFG, $_TPL;
		$t = sql::getRow("SELECT * FROM ani_sitemap WHERE lang='ka' AND Id=".$id);
		$t['source'] = htmlspecialchars_decode($t['source']);
		$t['source'] = strtr($t['source'],$_TPL);
		$t['source'] = preg_replace('/style="white-space:pre;">/','style="white-space:pre">'.nbsp(7).'</span>',htmlspecialchars_decode($t['source']));
		return ($a==false) ? $t : $t[$a];
	}
	public static function pagebypermalink($p,$a=false){
		global $_CFG, $_TPL;
		$ret = array();
		if(!empty($p)){
			$row = sql::getRow("SELECT * FROM ani_sitemap WHERE lang='".$_GET['l']."' AND permalink='".$p."'");
		} else {
			$row = sql::getRow("SELECT * FROM ani_sitemap WHERE lang='".$_GET['l']."' AND homepage=1");
		}
		if(count($row)<2) $row = sql::getRow("SELECT * FROM ani_sitemap WHERE lang='".$_GET['l']."' AND permalink='404'");
		$ret['Id'] = $row['Id'];
		$ret['cat_id'] = $row['cat_id'];
		$ret['parent'] = $row['parent'];
		$ret['title'] = $row['name'];
		$ret['pagetype'] = $row['pagetype'];
		$ret['metatags'] = $row['metatags'];
		$ret['source'] = htmlspecialchars_decode(strtr($row['source'],$_TPL));
		return ($a == false) ? $ret : $ret[$a];
	}
	# ****************************************************** #
	# ****************************************************** #
	# ****************************************************** #
	public static function langs($l=''){
		global $_CFG;
		return ($l=='') ? $_CFG['availLangs'] : $_CFG['availLangs'][$l];
	}
}

function initial_language(){
	$_GET['l']  = (isset($_GET['l'] )) ? $_GET['l']  : 'ka';
	return $_SESSION['l'] = (isset($_SESSION['l']) && !isset($_GET['l'])) ? $_SESSION['l'] : $_GET['l'] ;
}


function so($a, $b) { return (strcmp_numeric($a['ord'],$b['ord'])); }

function menu_childs($_MENU, $el_id){
	$has_subcats = false;
	$arr = array();
	foreach ( $_MENU as $k => $arrs ){ if ($arrs['parent']==$el_id) { $arr[] = $arrs; } }
	uasort($arr, 'so');
	$out = '<ul class="sub-menu">';
	foreach ( $arr as $k => $arrs ){
			$target = "";
			$has_subcats = true;
			switch ($arrs['type']) {
				case 'VOID':
					$link = 'javascript:void(0)';
					break;
				case 'REMOTE_LINK':
					$link = $arrs['source'];
					$target = 'target="_blank"';
					break;
				default:
					$link = '/'.$arrs['lang'].'/'.$arrs['permalink'];
			}
			$out .= '<li><a class="a-href" href="'.$link.'"'.$target.' >'.$arrs['name'].'</a>';
			$out .= menu_childs($_MENU, $arrs['cat_id']);
			$out .= '</li>';
	}
	$out .= '</ul>';
	return ($has_subcats) ? $out : false;
}

function sitemap(){
	$marr = array();
	$_MENUItems = 0;
	$out = '<ul id="main-menu" class="menu">';
	$sql   = sql::getRows("SELECT * FROM `cn_sitemap` WHERE `lang`='".$_GET['l']."' AND enabled=1 AND menuId>0");
	foreach($sql as $row){
		$marr[$row['cat_id']] = array(
								 'text'=>$row['name'],
								 'parent'=>$row['parent'],
								 'permalink'=>$row['permalink'],
								 'lang'=>$row['lang'],
								 'name'=>$row['title'],
								 'level'=>$row['level'],
								 'enabled'=>$row['enabled'],
								 'type'=>$row['pagetype'],
								 'position'=>$row['position'],
								 'source'=>$row['source'],
								 'cat_id'=>$row['cat_id'],
								 'ord'=>$row['ord'],
								 'add_date'=>$row['add_date']
								 );
		$_MENUItems = ($_MENUItems<$row['cat_id']) ? $row['cat_id'] : $_MENUItems;
	}
	$arr = array();
	for ( $i=1; $i<=$_MENUItems; $i++ ){
		if(isset($marr[$i]) && $marr[$i]['parent']==0) { $arr[] = $marr[$i]; }
	}
	//print_m($arr);
	uasort($arr, 'so');
	foreach ($arr as $key => $arrs){
			$target = "";
			switch ($arrs['type']) {
					case 'VOID':
						$link = 'javascript:void(0)';
						break;
					case 'REMOTE_LINK':
						$link = $arrs['source'];
						$target = 'target="_blank"';
						break;
					default:
						$link = '/'.$arrs['lang'].'/'.$arrs['permalink'];
				}
				$out .= '<li><a class="a-href" href="'.$link.'" '.$target.' >'.@$addTopSpace.$arrs['name'].'</a>';
				$out .= menu_childs($marr, $arrs['cat_id']);
				$out .= '</li>';
	}
	$out .= '</ul>';
	return $out;
}

function change_getvars($var,$value=false){
	$gets = $_GET;
	if($value===false) {
		unset($gets[$var]);
	} else {
		$gets[$var] = $value;	
	}
	return '/'.implode('/',$gets);
}

function in_set_string($string,$strings,$act){
	$strings = explode(',',$strings);
	foreach($strings as $k => $v){
		if((trim($v)=='') || (($string==$v) && ($act=='remove'))){
			unset($strings[$k]);
		} else {
		$strings[$k] = trim($v);	
		}
	}
	if($act=='add') $strings[] = $string;
	return implode(',',$strings);
}

function adminlangchooser(){
	global $_ADM_CONTLNG, $_get_langs;
	if(count($_get_langs)>1){
		$html = '<select class="setcontentlang select" id="setcontentlang">';
		foreach($_get_langs as $l => $ln) {
			$html .= '<option '.((@$_ADM_CONTLNG==$l)?'selected="selected"':'').' value="'.$l.'">'.$ln.'</option>';
		}
		$html .= '</select>';
	} else {
		$html = '<div class="selector" style="cursor:default"><span style="background:none;padding:0px 0px 0px 2px;cursor:default">'.current($_get_langs).'</span></div>';	
	}
	return $html;
}

?>
