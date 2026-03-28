<?php

function permalink2querystring($array){
	$_PAGE = array();
	$_P	   = array();
    // Fix for foreach on null/non-array
    $array = is_array($array) ? $array : array();
	foreach($array as $k => $v){
		$tmp = explode('-', (string)$v);
		if(isset($tmp[0]) && is_numeric($tmp[0])) $_P['story'] = $tmp[0];
		if(isset($tmp[0]) && $tmp[0]=='page') 	$pageids = isset($tmp[1]) ? $tmp[1] : null;
		}
        
	$page = sql::getRow("SELECT * FROM `cn_sitemap` WHERE `lang`='".(@$_GET['l'])."' AND `permalink`='".(@$array[0])."' AND `enabled`=1");
    // Fix: initialize as empty array if query fails to avoid offset errors
    $page = is_array($page) ? $page : array();

	$subpage = sql::getRow("SELECT * FROM `cn_sitemap`
									WHERE `lang`='".(@$_GET['l'])."' AND `permalink`='".(@$array[1])."' AND `parent`='".(@$page['cat_id'])."' AND `enabled`=1");
    $subpage = is_array($subpage) ? $subpage : array();

	$subpage2 = sql::getRow("SELECT * FROM `cn_sitemap`
								WHERE `lang`='".(@$_GET['l'])."' AND `permalink`='".(@$array[2])."' AND `parent`='".(@$subpage['cat_id'])."' AND `enabled`=1");
    $subpage2 = is_array($subpage2) ? $subpage2 : array();

		if(count((array)$page)>2){
			$_PAGE[] = array('pagetype'=>$page['pagetype'],
							 'source'=>$page['source'],
							 'extra_source'=>$page['extra_source'],
							 'permalink'=>$page['permalink'],
							 'title'=>$page['title'],
							 'pname'=>@$array[0]);
		}
	if(count((array)$subpage)>2){
		$_PAGE[] = array('pagetype'=>$subpage['pagetype'],
						 'source'=>$subpage['source'],
						 'extra_source'=>$subpage['extra_source'],
						 'permalink'=>$subpage['permalink'],
						 'title'=>$subpage['title'],
						 'pname'=>@$array[1]);
	}
	if(count((array)$subpage2)>2){
		$_PAGE[] = array('pagetype'=>$subpage2['pagetype'],
						 'source'=>$subpage2['source'],
						 'extra_source'=>$subpage2['extra_source'],
						 'permalink'=>@$page['permalink'],
						 'title'=>@$page['title'],
						 'pname'=>@$array[2]);
	}
	foreach($_PAGE as $k => $p){
		if($k=='0') $k='';
		if($p['pagetype']=='MODULE'){
			$_P['p'.$k] = $p['extra_source'];
			$_P['p'.$k.'name'] = $p['pname'];
		} elseif($p['pagetype']=='TEXT'){
			$_P['p'] = 'content';
			$_P['p'.$k.'name'] = $p['pname'];
		} elseif($p['pagetype']=='FILE'){
			$_P['p'.$k] = str_replace('.php','',$p['source']);
			$_P['p'.$k.'name'] = $p['pname'];
		} else {
			$t = trim(str_replace('.php','',$p['source']));
			if($t!='') $_P['p'.$k] = str_replace('.php','',$p['source']);
		}
		$_P['p'.$k.'title'] = $p['title'];

	}
	if(@is_numeric($pageids)) $_P['pg'] = $pageids;
	return $_P;
	}

function generate_homepagevars(){
	$_PAGE = array();
	$_P	   = array();
	$page = sql::getRow("SELECT * FROM `cn_sitemap` WHERE `lang`='".(@$_GET['l'])."' AND `enabled`=1 AND `homepage`='1'");
    $page = is_array($page) ? $page : array();

	$subpage  = sql::getRow("SELECT * FROM `cn_sitemap` WHERE `lang`='".(@$_GET['l'])."' AND `cat_id`='".@$page['parent']."'   AND `enabled`=1");
    $subpage = is_array($subpage) ? $subpage : array();

	$subpage2 = sql::getRow("SELECT * FROM `cn_sitemap` WHERE `lang`='".(@$_GET['l'])."' AND `cat_id`='".@$subpage['parent']."' AND `enabled`=1");
    $subpage2 = is_array($subpage2) ? $subpage2 : array();

	if(count((array)$page)>2){
		$_PAGE[] = array('pagetype'=>$page['pagetype'],'source'=>$page['source'],'extra_source'=>$page['extra_source'],'permalink'=>$page['permalink'],'pname'=>@$array[0]);
		}
	if(count((array)$subpage)>2){
		$_PAGE[] = array('pagetype'=>$subpage['pagetype'],'source'=>$subpage['source'],'extra_source'=>$subpage['extra_source'],'permalink'=>@$page['permalink'],'pname'=>@$array[1]);
	}
	if(count((array)$subpage2)>2){
		$_PAGE[] = array('pagetype'=>$subpage2['pagetype'],'source'=>$subpage2['source'],'extra_source'=>$subpage2['extra_source'],'permalink'=>@$page['permalink'],'pname'=>@$array[2]);
	}
	foreach($_PAGE as $k => $p){
		if($k=='0') $k='';
		if($p['pagetype']=='MODULE'){
			$_P['p'.$k] = $p['extra_source'];
			$_P['p'.$k.'name'] = $p['pname'];
		} elseif($p['pagetype']=='TEXT'){
			$_P['p'] = 'content';
			$_P['p'.$k.'name'] = $p['pname'];
		} elseif($p['pagetype']=='FILE'){
			$_P['p'.$k] = str_replace('.php','',$p['source']);
			$_P['p'.$k.'name'] = $p['pname'];
		} else {
			$t = trim(str_replace('.php','',$p['source']));
			if($t!='') $_P['p'.$k] = str_replace('.php','',$p['source']);
		}
	}
	return $_P;
}

?>