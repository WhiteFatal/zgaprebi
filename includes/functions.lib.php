<?php

#####################################################
class LNG {
	public static function get_st_line($text,$string){
		$tmp = explode($string,$text);
		return count(explode("\n",$tmp[0]));
	}
	public static function getphpfiles($path = '../theme', $level = 0){
	global $phpfiles;
	$ignore = array( 'cgi-bin', '.', '..');
	$exclude_files = array( './exp.php',
							'./checksecurity.php'
							);
	$exclude_folders = array('./libs' );
	$dh = @opendir($path); 
	while(false !== ($file = readdir($dh))){
		$fullfile = $path.'/'.$file;
		if(!in_array($file, $ignore) && !in_array($fullfile, $exclude_folders)){
            $spaces = str_repeat('&nbsp;', ($level * 4));
            if(is_dir($fullfile)){
                self::getphpfiles($fullfile, ($level+1));
            } else {
				if(substr($fullfile,-4)=='.php') $phpfiles[] = $fullfile;
            }
        }
    }
    closedir($dh);
	 return $phpfiles; 
}
	public static function parse_theme_files($type='full'){
		$po = array();
		//print_m(self::getphpfiles());
		foreach (self::getphpfiles('../theme') as $filename){
			$tmp = file_get_contents($filename);
			//$tmp = str_replace('); ? >',"); ? >\n",$tmp);
			preg_match_all("/_e+\('.+?'\)/",   $tmp, $strings1);
			preg_match_all('/_e+\(".+?"\)/',   $tmp, $strings2);
			preg_match_all("/__+\('.+?'\)/",   $tmp, $strings3);
			preg_match_all('/__+\(".+?"\)/',   $tmp, $strings4);
			//print_m($strings1); die();
			$stringsmerged1 = array_merge($strings1,$strings2);
			$stringsmerged2 = array_merge($stringsmerged1,$strings3);
			$strings 		= array_merge($stringsmerged2,$strings4);
			//print_m($strings);
			foreach($strings as $v){
				foreach($v as $s){
					$lineid = self::get_st_line($tmp,$s);
					$s = substr($s,4);
					$s = substr($s,0,-2);
					if($type=='full'){
						$po[$s][] = "#: ".substr($filename,2).":".$lineid;
					} else {
						$po[] = $s;
					}
				}

			}
		}
		return $po;
	}
	public static function parse_admin_files($type='full'){
		$po = array();
		//print_m(self::getphpfiles());
		$files 	 = self::getphpfiles('../admin/pages');
		$files[] = '../admin/login.php';
		$files[] = '../admin/index.php';
		$files[] = '../includes/config.php';
		foreach ($files as $filename){
			$tmp = file_get_contents($filename);
			//$tmp = str_replace('); ? >',"); ? >\n",$tmp);
			preg_match_all("/_e+\('.+?'\)/",   $tmp, $strings1);
			preg_match_all('/_e+\(".+?"\)/',   $tmp, $strings2);
			preg_match_all("/__+\('.+?'\)/",   $tmp, $strings3);
			preg_match_all('/__+\(".+?"\)/',   $tmp, $strings4);
			//print_m($strings1); die();
			$stringsmerged1 = array_merge($strings1,$strings2);
			$stringsmerged2 = array_merge($stringsmerged1,$strings3);
			$strings 		= array_merge($stringsmerged2,$strings4);
			//print_m($strings);
			foreach($strings as $v){
				foreach($v as $s){
					$lineid = self::get_st_line($tmp,$s);
					$s = substr($s,4);
					$s = substr($s,0,-2);
					if($type=='full'){
						$po[$s][] = "#: ".substr($filename,2).":".$lineid;
					} else {
						$po[] = $s;
					}
				}

			}
		}
		return $po;
	}
}
#####################################################

/*
class GD2 {
        public static function is($img,$act){
                if($act=='v_img'){
                        $s = getimagesize($img);
                        return ($s[0]<$s[1]) ? true : false;
                }
                else if($act=='h_img'){
                        $s = getimagesize($img);
                        return ($s[0]>$s[1]) ? true : false;
                }
                else if($act=='eq_img'){
                        $s = getimagesize($img);
                        return ($s[0]==$s[1]) ? true : false;
                }
        }
}
*/

function err_log($log){
	$filename = 'sitelog.log';
	$somecontent = date('d/m/y h:i:s')." - ".getenv("REMOTE_ADDR")." - "." - ".full_url()." - ".$log."\n";
	if (is_writable($filename)) {
		if (!$handle = fopen($filename, 'a')) {
			 exit;
		}
		if (fwrite($handle, $somecontent) === FALSE) {
			exit;
		}
		fclose($handle);
	}
}

function mail_via_tpl($tplFile,$trans,$email,$from, $title){
	$mailtpl = fileGetContent($tplFile);
	$messageText = strtr($mailtpl, $trans);
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	$headers .= 'From: '.$from.' <'.$from.'>' . "\r\n";
	return mail($email,$title, $messageText, $headers);
}

function full_url(){
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}

function error2file($error){
	$log = date('d/m/Y h:i:s')." - ".$_SERVER["REMOTE_ADDR"]." - ".$error." - ".full_url()." \n";
	//$filename = 'sitelog.log';
	//$handle = fopen($filename, 'a+');
	//fwrite($handle, $log);
	//fclose($handle);	
}
function thisLang(){
	global $_GET;
	return 'lang='.$_GET['lang'];
}

function print_m($array){
	print "<pre>";
	print_r($array);
	print "</pre>";
}

function fileGetContent($file){
	if(!file_exists($file)) return;
	$handle = fopen($file, "rb");
	$contents = fread($handle, filesize($file));
	fclose($handle);
	return $contents;
}

function sPOST($POSTVAR=false,$value){
	if(isset($POSTVAR) && $POSTVAR==$value) {
		return 'selected="selected"';
	} else {
		return '';
	}
}
function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

function explode_between($content, $start, $end, $max) {
	if ((isset($max)) && ($max == 0)) {return FALSE;}
	if (isset($max)) { $max=$max–1; }
	@$temparray = explode($start,$content,2);
	if (count($temparray) < 2) {return FALSE;}
	@$temparray = explode($end,$temparray[1],2);
	if (count($temparray) < 2) {return FALSE;}
	$returned = explode_between($temparray[1], $start, $end, $max);
	if ($returned === FALSE) {$outarray = array();}
	else {$outarray = $returned;}
	array_unshift($outarray,$temparray[0]);
	return $outarray;
};

function getUniqueCode($length = ""){
  $code = md5(uniqid(time().rand(), true));
  if ($length != "") return substr($code, 0, $length);
  else return $code;
}


function smtpSendMail($to,$from,$subject, $message){
	$SMTP = array ('' => '77.74.40.3', 'port' => '25', 'from' => 'support@predator.ge',);
	$SMTPIN = fsockopen ($SMTP[''], $SMTP['port']);
	fputs ($SMTPIN, "HELO \r\n");  
	fputs ($SMTPIN, "MAIL FROM:".$from."\r\n");  
	fputs ($SMTPIN, "RCPT TO:".$to."\r\n");  
	fputs($SMTPIN, "DATA\r\n");
	fputs($SMTPIN, "To: <".$to.">\r\nFrom: <".$from.">\r\nContent-type: text/html; charset=utf-8; format=flowed\r\nContent-Transfer-Encoding: 7bit\r\nSubject:".$subject."\r\n\r\n\r\n".$message."\r\n.\r\n");
	fputs ($SMTPIN, "QUIT\r\n");  
	fclose($SMTPIN);
	return "ok";
}
## Start UTF Functions
function utf8_strlen($string){
 if(!defined('UTF8_NOMBSTRING') && function_exists('mb_strlen')) 
 return mb_strlen($string,'utf-8');
 $uni = utf8_to_unicode($string);
 return count($uni);
}

function utf8_to_unicode($str) {
	 $unicode = array();  
	 $values = array();
	 $lookingFor = 1;
	 for ($i = 0; $i < strlen($str); $i++ ) {
	   $thisValue = ord( $str[ $i ] );
	   if ( $thisValue < 128 ) $unicode[] = $thisValue;
	   else {
		 if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
		 $values[] = $thisValue;
		 if ( count( $values ) == $lookingFor ) {
	 $number = ( $lookingFor == 3 ) ?
	   ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
	  ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
	 $unicode[] = $number;
	 $values = array();
	 $lookingFor = 1;
		 }
	   }
	 }
	 return $unicode;
}

function unicode_to_utf8( $str ) {
 $utf8 = '';
 foreach( $str as $unicode ) {
   if ( $unicode < 128 ) {
     $utf8.= chr( $unicode );
   } elseif ( $unicode < 2048 ) {
     $utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
     $utf8.= chr( 128 + ( $unicode % 64 ) );
   } else {
     $utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
     $utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
     $utf8.= chr( 128 + ( $unicode % 64 ) );
   }
 }
 return $utf8;
}
	
function utf8_substr($str, $start, $length=null){
 if(!defined('UTF8_NOMBSTRING') && function_exists('mb_substr'))
   return mb_substr($str,$start,$length,'utf-8');
 $uni = utf8_to_unicode($str);
 return unicode_to_utf8(array_slice($uni,$start,$length));
}

###
function nbsp($cnt,$char='&nbsp;'){
	$nbsp = '';
	for($i=0; $i<=$cnt; $i++){
		$nbsp.=$char;
	}
	return $nbsp;
}
function br($cnt){
	$br = '';
	for($i=0; $i<=$cnt; $i++){ $br.='<br>'; }
	return $br;
}

function B2KB($bytes) {
	if ($bytes>0) {	
		$symbols = array('ბ', 'კბ', 'მგბ', 'გგბ', 'ტბ', 'PB', 'EB', 'ZB', 'YB');
		$exp = floor(log($bytes)/log(1024));
		$status = sprintf('%.2f '.$symbols[$exp], ($bytes/pow(1024, floor($exp))));
		$status = str_replace('.00' , '' , $status);
	} else {
		$status = $bytes." ბ";
	}
return $status ;
}

function allowIp($allowIps){
        $tmp = array();
        $guestIp = $_SERVER['REMOTE_ADDR'];
		$tmp = explode('.',$guestIp);
        $guestIpLastClass = end($tmp);
        foreach($allowIps as $key => $v){
				$tmp2 = explode('.',$key);
                $tmp[$key] = (end($tmp2)=='*') ? str_replace('*',$guestIpLastClass,$key) : $key;
        }
        if (in_array($guestIp,$tmp)) {
                return array_search($guestIp,$tmp);
        } else {
                return false;
        }
}

function post_page_reload(){
	ob_clean();
	header("Location: ".full_url());
	die();
}
function get_page_reload($vars){
	ob_clean();
	header("Location: ".$vars);
	die();
}
function redirect($vars){
	ob_clean();
	header("Location: ".$vars);
	die();
}
function date_formats($d,$f){
	$ret = $d;
	if($f=='geobr'){
		$dts = explode(':',$d);
		$ret = geo_month(str_replace(' ','<br>',($dts[0].':'.$dts[1])));
	}
return $ret;
}
function jscrt_log($value,$type="alert"){
	if($type=='alert'){
		return '<scrt type="text/javascrt">alert("'.$value.'")</scrt>';
	} else {
		return '<scrt type="text/javascrt">console.log("'.$value.'")</scrt>';
	}
}
function mailTo($t){
	$t = str_replace('@','[dzagli]',$t);
	return '<a href="mailTo:'.$t.'" class="mailTo" onclick="mailTo(this)" onmouseout="mailToOver(this)"><img src="theme/timg.php?t='.base64_encode($t).'" align="absmiddle" /></a>';
}

function is_even($num){
	return (($num/2)==round($num/2)) ? true : false;
}
function round2even($num){
	return is_even($num) ? $num : $num+1;
}
function strcmp_numeric($str1,$str2){
	if ($str1<$str2) return -1;
	if ($str1==$str2) return 0;
	if ($str1>$str2) return 1;
}
if(!function_exists('htmlspecialchars_decode')){
	function htmlspecialchars_decode($str){
		$strtr = array('&lt;'=>'<', '&quot;' => '"');
		return strtr($str,$strtr);
	}
}
function array_values_trim($array){
	$tmp = array();
	foreach($array as $k => $v){
		$tmp[$k] = trim($v);
	}
	return $tmp;
}

function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}

function getSimpleText($str){
	global $_CFG;
	$tmp1 = array();
	$tmp3 = array();
	$str = trim(substr( (strip_html_tags($str)) ,0,999999999));
	$tmp1 = explode(' ',$str);
	$kk = explode(' ',$str);
	$tmp2 = end($kk);
	foreach ($tmp1 as $row => $value) {
		$order   = array("\n"=>" ", "\r"=>" ");
		$value = trim(strtr($value,$order));
		$value = str_replace(' ','', $value);
		if ( ($value != $tmp2) && (trim($value)!='') ) { $tmp3[] = trim($value); }
	}
	$str = implode(' ',$tmp3);
	$str = str_replace('&nbsp;',' ',$str);
	return $str;
}

function arrayToJSObject($array, $varname, $sub = false ) { 
    $jsarray = $sub ? $varname . "{" : $varname . " = {\n"; 
    $varname = "\t$varname"; 
    reset($array);
    while (list($key, $value) = each($array)) { 
        $jskey = "'" . $key . "':"; 
        if (is_array($value)) { 
            $temp[] = arrayToJSObject($value, $jskey, true); 
        } else { 
            if (is_numeric($value)) { 
                $jskey .= "$value"; 
            } elseif (is_bool($value)) { 
                $jskey .= ($value ? 'true' : 'false') . ""; 
            } elseif ($value === NULL) { 
                $jskey .= "null"; 
            } else { 
                static $pattern = array("\\", "'", "\r", "\n"); 
                static $replace = array('\\', '\\\'', '\r', '\n'); 
                $jskey .= "'" . str_replace($pattern, $replace, $value) . "'"; 
            } 
            $temp[] = $jskey; 
        } 
    } 
    $jsarray .= implode(', ', $temp); 

    $jsarray .= "}\n"; 
    return $jsarray; 
} 

function _e($s){
	global $_LANG;
	echo (trim(@$_LANG[$s])!="") ? $_LANG[$s] : $s;
}

function __($s){
	global $_LANG;
	return (isset($_LANG[$s])) ? $_LANG[$s] : $s;
}

function utf2lat($s){
	$a = array('ა'=>'a','ბ'=>'b','გ'=>'g','დ'=>'d','ე'=>'e','ვ'=>'v','ზ'=>'z','თ'=>'T','ი'=>'i','კ'=>'k','ლ'=>'l','მ'=>'m','ნ'=>'n','ო'=>'o',
		  'პ'=>'p','ჟ'=>'J','რ'=>'r','ს'=>'s','ტ'=>'t','უ'=>'u','ფ'=>'f','ქ'=>'q','ღ'=>'R','ყ'=>'y','შ'=>'S','ჩ'=>'C','ც'=>'c','ძ'=>'Z',
		  'წ'=>'w','ჭ'=>'W','ხ'=>'x','ჯ'=>'j','ჰ'=>'h');
	return preg_replace("/[^a-zA-Z\d=.-_]/", "", strtr($s,$a));
}
	
function utf2lat4($s){
       $a = array('ა'=>'a','ბ'=>'b','გ'=>'g','დ'=>'d','ე'=>'e','ვ'=>'v','ზ'=>'z','თ'=>'t','ი'=>'i','კ'=>'k','ლ'=>'l','მ'=>'m',
		   'ნ'=>'n','ო'=>'o','პ'=>'p','ჟ'=>'zh','რ'=>'r','ს'=>'s','ტ'=>'t','უ'=>'u','ფ'=>'p','ქ'=>'q','ღ'=>'gh','ყ'=>'kh',
		   'შ'=>'sh','ჩ'=>'ch','ც'=>'ts', 'ძ'=>'dz', 'წ'=>'ts','ჭ'=>'tch','ხ'=>'kh','ჯ'=>'j','ჰ'=>'h');
		return strtr($s,$a);
}

function BBCode ($string) {
	$search = array(
			'@\[(?i)b\](.*?)\[/(?i)b\]@si',
			'@\[(?i)i\](.*?)\[/(?i)i\]@si',
			'@\[(?i)u\](.*?)\[/(?i)u\]@si',
			'@\[(?i)img\](.*?)\[/(?i)img\]@si',
			'@\[(?i)url=(.*?)\](.*?)\[/(?i)url\]@si',
			'@\[(?i)code\](.*?)\[/(?i)code\]@si'
	);
	$replace = array(
			'<b>\\1</b>',
			'<i>\\1</i>',
			'<u>\\1</u>',
			'<img src="\\1">',
			'<a href="\\1">\\2</a>',
			'<code>\\1</code>'
	);
	return preg_replace($search , $replace, $string);
}

function changelang($l){
	$str = $_SERVER['REQUEST_URI'];
	$str = str_replace('/'.$_GET['l'].'/','/'.$l.'/',$str);
	if($str=='/') $str = '/'.$l.'/';
	return $str;
}


function changeGETvarP($GETvar,$GETvalue){
	unset($_GET['lvar']);
	$_g = $_GET;
	$_g[$GETvar] = $GETvalue;
	return get_link4paging(arraytostring($_g));
}

//function changeGETvarP($GETvar,$GETvalue){
//	$_GET[$GETvar] = $GETvalue;
//	return get_link4paging(arraytostring($_GET));
//}

function toSlug($str){
	$str = utf2lat4($str);    
	$str = strtolower($str);
	$str = preg_replace('/[^a-zA-Z0-9]/i',' ', $str);
	$str = trim($str);
	$str = preg_replace('/\s+/', ' ', $str);
	$str = preg_replace('/\s+/', '-', $str);
	return $str;
}

/*old
function seotitleconvert($s){
	$s = utf2lat4($s);
	$s = strtr($s,array(' '=>'-','&'=>'_'));
	return  $s;
}
*/
function seotitleconvert($s){
	$s = toSlug($s);
	//$s = strtr($s,array(' '=>'-','&'=>'_'));
	return $s;
}

function get_link4paging($v,$l=false){
	global $_CFG;
	parse_str($v,$tmp);
	if(isset($tmp['story'])){
		$tmpst = $tmp['story'].'-'.seotitleconvert(@$tmp['seotitle']);
		$v = preg_replace('/story=\d+&seotitle=.+/',('story='.$tmpst),$v);
	}
	parse_str($v,$url);
	unset($url['l']);
	$f = current($url);
	unset($url['p']);
	unset($url['pname']);
	unset($url['ptitle']);
	unset($url['p1title']);
	foreach($url as $k => $v){
		if((substr($k,0,1)=='p') && (is_numeric(substr($k,1,strlen($k))))) {
			unset($url[$k]);
		}
	}
	if(trim($f)!='') $f = '/'.$f;
	foreach($url as $k => $v) if(trim($v)=='') unset($url[$k]);
	//print_m($url);
	if($l==false){
		$addlangvar = (count($_CFG['availLangs'])>1) ? ('/'.$_GET['l']) : '';
		return $addlangvar.$f.'/'.fullarraytostring($url);
	} else {
		return $f.'/'.fullarraytostring($url);
	}
}

function get_link($v,$l=false){
	global $_CFG;
	parse_str($v,$tmp);
	if(isset($tmp['story'])){
		$tmpst = $tmp['story'].'-'.seotitleconvert($tmp['seotitle']);
		$v = preg_replace('/story=\d+&seotitle=.+/',('story='.$tmpst),$v);
	}
	parse_str($v,$url);
	unset($url['l']);
	$f = current($url);
	unset($url['p']);
	unset($url['pname']);
	if(isset($url['pg'])) $url['pg'] = 'page-'.$url['pg'];
	if($l==false){
		$addlangvar = (count($_CFG['availLangs'])>1) ? ('/'.$_GET['l']) : '';
		return $addlangvar.'/'.$f.'/'.fullarraytostring($url);
	} else {
		return '/'.$f.'/'.fullarraytostring($url);
	}
}

function changeGETvar($GETvar,$GETvalue){
	global $_GET;
	$URL = (count(explode('pg=',full_url()))>1) ? full_url() : (full_url().'&pg=1');
	$oldString = '&'.$GETvar.'='.$_GET[$GETvar];
	$newString = '&'.$GETvar.'='.$GETvalue;
	return str_replace($oldString, $newString, $URL);
}

function changeGETvariables($GETvar,$GETvalue){
	global $_GET;
	$URL = (count(explode('pg=',full_url()))>1) ? full_url() : (full_url().'&pg=1');
	$oldString = '&'.$GETvar.'='.$_GET[$GETvar];
	$newString = '&'.$GETvar.'='.$GETvalue;
	return str_replace($oldString, $newString, $URL);
}

function paging($dataCNT,$dispRow){
	 $arr = paging2array($dataCNT,$dispRow);
	 if(count($arr)==1) return '';
	 $html  = '<!-- Pagination -->';
     $html .= '<div class="pagination">';
     $html .= '<ul class="pages">';
		foreach($arr as $v){
			switch ($v['type']) {
				case 'go2firstpage':
					$html .= '<li><a href="'.changeGETvarP('pg','page-'.$v['p']).'">'.__('პირველი გვერდი').'</a></li>';
				break;
				case 'link':
					$html .= '<li><a href="'.changeGETvarP('pg','page-'.$v['p']).'">'.$v['p'].'</a></li>';
				break;
				case 'current':
					$html .= '<li><a href="#" class="active">'.$v['p'].'</a></li>';
				break;
				case 'back':
					$html .= '<li><a href="'.changeGETvarP('pg','page-'.$v['p']).'">&laquo;</a></li>';
				break;
				case 'next':
					$html .= '<li><a href="'.changeGETvarP('pg','page-'.$v['p']).'">&raquo;</a></li>';
				break;
				case '...':
					$html .= '<li class="points">...</li>';
				break;
				case 'go2last':
					$html .= '<li><a href="'.changeGETvarP('pg','page-'.$v['p']).'">'.__('ბოლო გვერდი').'</a></li>';
				break;
			}
		}
        $html .= '</ul>';
		$html .= '<div class="clb"></div>';
        $html .= '</div>';
		$html .= '<!-- Pagination -->';
	return $html;
}

function pagingGETvariables($dataCNT,$dispRow){
	 $arr = paging2array($dataCNT,$dispRow);
	 if(count($arr)==1) return '';
	 $html  = '<!-- Pagination -->';
     $html .= '<div class="pagination">';
     $html .= '<ul class="pages">';
		foreach($arr as $v){
			switch ($v['type']) {
				case 'go2firstpage':
					$html .= '<li><a href="'.changeGETvariables('pg',$v['p']).'">'.__('პირველი გვერდი').'</a></li>';
				break;
				case 'link':
					$html .= '<li><a href="'.changeGETvariables('pg',$v['p']).'">'.$v['p'].'</a></li>';
				break;
				case 'current':
					$html .= '<li><a href="#" class="active">'.$v['p'].'</a></li>';
				break;
				case 'back':
					$html .= '<li><a href="'.changeGETvariables('pg',$v['p']).'">&laquo;</a></li>';
				break;
				case 'next':
					$html .= '<li><a href="'.changeGETvariables('pg',$v['p']).'">&raquo;</a></li>';
				break;
				case '...':
					$html .= '<li class="points">...</li>';
				break;
				case 'go2last':
					$html .= '<li><a href="'.changeGETvariables('pg',$v['p']).'">'.__('ბოლო გვერდი').'</a></li>';
				break;
			}
		}
        $html .= '</ul>';
        $html .= '</div>';
		$html .= '<!-- Pagination -->';
	return $html;
}

function paging2array($rowCNT,$dispRow){
	$keys = array();
	$paging = ceil($rowCNT/$dispRow);
	if($paging<=11){
		for($i=1; $i<=$paging; $i++ ){
			$keys[] = ($i==$_GET['pg']) ? array('type'=>'current','p'=>$i) : array('type'=>'link','p'=>$i);
		}
	} else {
		if ($_GET['pg']>6) $keys[] = array('type'=>'go2firstpage', 'p'=>1);
		if ($_GET['pg']>6) $keys[] = array('type'=>'...', 'p'=>'...');
		if ($_GET['pg']>1) $keys[] = array('type'=>'back', 'p'=>$_GET['pg']-1);
		$minp = (($_GET['pg']-5)<1) ? 1 : ($_GET['pg']-5);
		$maxp = $minp+9;
		$minp = ($minp>$paging) ? $paging : $minp;
		$maxp = ($maxp >= $paging) ? $paging : $maxp;
		for($i=$minp; $i<=$maxp; $i++ ){
			$t = ($i!=$_GET['pg']) ? 'link' : 'current';
			$keys[] =  array('type'=>$t, 'p'=>$i);
		}
		if ($_GET['pg']<$paging) { $keys[] =array('type'=>'next','p'=>($_GET['pg']+1)); }
		if (($paging-6)>=$_GET['pg']) {
				$keys[] = array('type'=>'...', 'p'=>'...');
				$keys[] = array('type'=>'go2last', 'p'=>$paging);
}
}
	return $keys;
}

function fullarraytostring($a){
	$str = array();
	foreach($a as $k => $v) $str[] = $v; //$k.'/'.$v;
	return implode('/',$str);
}
function arraytostring($a){
	$str = array();
	foreach($a as $k => $v) $str[] = $k.'='.$v;
	return implode('&',$str);
}


class parse {
 	public static function yturl($url){
		$pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
		preg_match($pattern, $url, $matches);
		return (isset($matches[1])) ? $matches[1] : false;
	}
	
	public static function yturlthumb($url){
		$uid = parse::yturl($url);
		$img_url = "http://img.youtube.com/vi/".$uid."/0.jpg";
		return(@$img_url);
	}
}

function romanic_number($integer, $upcase = true) { 
    $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1, '0'=>0); 
	if($integer==0) return '0';
    $return = ''; 
    while($integer > 0) { 
        foreach($table as $rom=>$arb){ 
            if($integer >= $arb) { 
                $integer -= $arb; 
                $return .= $rom; 
                break; 
            } 
        } 
    } 
	return $return; 
} 

function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
} 

function empty_folder($dst) { 
    $dir = opendir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            @unlink($dst . '/' . $file);
        } 
    } 
    closedir($dir); 
} 


?>