<?php
if (!defined('FILE_SECURITY_KEY'))  die ();

function sitemap_get_childIds($cat_id){
	global $_ADM_CONTLNG;
	$ids = array();
	$sql = sql::getRows("SELECT * FROM `cn_sitemap` WHERE lang='".$_ADM_CONTLNG."' AND parent=".$cat_id);
	foreach($sql as $row){
			$ids[] = $row['cat_id'];
			$tmp = sitemap_get_childIds($row['cat_id']);
			$ids = array_merge($tmp,$ids);
	}
	$ids[] = $cat_id;
	return $ids;
}

if(isset($_GET['disablecatId']) || isset($_GET['enablecatId'])){
 	$ids   = sitemap_get_childIds(($_GET['disablecatId'])?$_GET['disablecatId']:$_GET['enablecatId']);
	$enabled = (isset($_GET['disablecatId'])) ? 0 : 1;
	sql::update("UPDATE `cn_sitemap` SET `enabled`=".$enabled." WHERE cat_id IN (".implode(', ',$ids).") AND `lang`='".$_ADM_CONTLNG."'");
	adminLog('გვერდის '.(($_GET['disablecatId']=='disableId') ? 'დამალვა' : 'გამოჩენა').' (ID:'.implode(', ',$ids).')');
	get_page_reload('?p=structure');
}

if(isset($_GET['hidecatId']) || isset($_GET['showcatId'])){
 	$ids   = sitemap_get_childIds(($_GET['hidecatId'])?$_GET['hidecatId']:$_GET['showcatId']);
	$menuId = (isset($_GET['hidecatId'])) ? '-1' : 1;
	$sql = "UPDATE `cn_sitemap` SET `menuId`=".$menuId." WHERE cat_id IN (".implode(', ',$ids).") AND `lang`='".$_ADM_CONTLNG."'";
	sql::update($sql);
	adminLog('sitemap-დან გვერდის '.(($_GET['disablecatId']=='disableId') ? 'დამალვა' : 'გამოჩენა').' (ID:'.implode(', ',$ids).')');
	get_page_reload('?p=structure');
}

if(isset($_GET['setashomepage'])){
	sql::update("UPDATE `cn_sitemap` SET `homepage`='0' WHERE `lang`='".$_ADM_CONTLNG."'");
	sql::update("UPDATE `cn_sitemap` SET `homepage`='1' WHERE cat_id = '".$_GET['setashomepage']."' AND `lang`='".$_ADM_CONTLNG."'");
	adminLog('მთავარი გვერდის შეცვლა (cat_id:'.$_GET['setashomepage'].')');
	get_page_reload('?p=structure');
}


function pageCheck($type,$src,$id){
	if($type=='VOID') return "";
	if($type=='FILE') {
		$src = current(explode('?',$src));
		return ((!file_exists('../theme/pages/'.$src)) ? '<a href="javascript:void(0)" class="tipS" title="ფაილი '.$src.' არ არსებობს!<br>theme/pages/ საქაღალდეში ატვირთეთ '.$src.' ფაილი!"><img src="img/error.png" style="cursor:help" /></a>' : '' );
	}
	return (trim($src)=='') ? '<a href="javascript:void(0)" class="tipS" title="ფაილი '.$src.' არ არსებობს!<br>theme/ საქაღალდეში ატვირთეთ '.$src.' ფაილი!"><img src="img/error.png" style="cursor:help" onclick="$.winPopup(\'ajax/page_edit.php?Id='.$id.'&l='.$_GET['l'].'\',850,680)" title="ეს გვერდი ცარიელია (დააჭირეთ რედაქტირებისთვის)" align="absmiddle" /></a>' : '';
}


function menuItemSH($m,$id){
	if($m==1){
		return '<a href="?p=structure&sp=sitemap&disableId='.$id.'" class="loadthis" title="დარწმუნებული ხართ გსურთ ამ გვერდის გაუქმება?">'.act_icon('img/flagYes.png','გვერდი ჩვენებადია').'</a>';
	} else {
		return '<a href="?p=structure&sp=sitemap&enableId='.$id.'" class="loadthis" title="დარწმუნებული ხართ გსურთ ამ გაუქმებული გვერდის აღდგენა?">'.act_icon('img/deleteCorner.png','გვერდი გამორთულია').'</a>';
	}
}


function editMenu($cat_id,$enabled,$D){
	$html = '<div style="float:right" class="tableToolbar">
			<ul class="btn-group toolbar">
				<li><a href="#" class="tablectrl_small bDefault" data-toggle="dropdown"><span class="iconb" data-icon="&#xe1f7;"></span></a>
				<div class="dropdown-menu pull-right">';
if($D['type']=='TEXT'){				
	$html .= '<div><a href="#" onclick="opencontenteditor('.$cat_id.')" class=""><span class="icos-create2"></span>კონტენტის რედაქტირება</a></div>';
}
	$html .= '<div><a href="#" onclick="openeditor('.$cat_id.')" ><span class="icos-pencil"></span>სეგმენტის რედაქტირება</a></div>';
	if($D['depth']==1){
		$html .= '<div><a href="?p=structure&setashomepage='.$cat_id.'"><span class="icos-home"></span>მთავარ გვერდზე დაყენება</a></div>';
	}
	if($enabled==1){
		$html .= '<div><a href="?p=structure&disablecatId='.$cat_id.'" onClick="return confirm(\'დარწმუნებული ხართ გსურთ ამ სეგმენტზე წვდომის გაუქმება?\')" class=""><span class="icos-block"></span>წვდომის გაუქმება</a></div>';
	} else {
		$html .= '<div><a href="?p=structure&enablecatId='.$cat_id.'" onClick="return confirm(\'დარწმუნებული ხართ გსურთ ამ სეგმენტზე წვდომის გააქტიურება?\')" class=""><span class="icos-check"></span>წვდომის გააქტიურება</a></div>';	
	}
	if($D['menuId']>0){
		$html .= '<div><a href="?p=structure&hidecatId='.$cat_id.'" onClick="return confirm(\'დარწმუნებული ხართ გსურთ ამ სეგმენტის დამალვა?\')" class=""><span class="icos-outbox"></span>დამალვა</a></div>';
	} else {
		$html .= '<div><a href="?p=structure&showcatId='.$cat_id.'" onClick="return confirm(\'დარწმუნებული ხართ გსურთ ამ სეგმენტის გამოჩენა?\')" class=""><span class="icos-inbox"></span>გამოჩენა</a></div>';	
	}
	$html .= '<div><a href="?p=structure&sp=sitemap&del_cat_Id='.$cat_id.'" onClick="return confirm(\'დარწმუნებული ხართ გსურთ ამ კატეგორიის წაშლა?\')"><span class="icos-trash"></span>წაშლა</a></div>
					</div>
				</li>
			</ul> 
		</div>';
	if($D['enabled']==0)  $html .= '<div style="float:right"><span class="icos-block"></span></div>';
	if($D['enabled']==1)  $html .= '<div style="float:right"><span class="icos-check"></span></div>';
	if($D['homepage']==1) $html .= '<div style="float:right"><span class="icos-home"></span></div>';
	return $html;
}

function get_childs($el_id){
	global $_MENU, $_ADM_CONTLNG,$_CFG;
	$out = '';
	$arr = array();
	$has_subcats = false;
	foreach ( $_MENU as $k => $arrs ){ if ($arrs['parent']==$el_id) { $arr[] = $arrs; } }
	uasort($arr, 'so');
	if(count($arr)>0) $out .= ' <ol class="sortable">';
	foreach ( $arr as $k => $arrs ){
		if($arrs['menuId']>0){
			$has_subcats = true;
			$disstyle = ($arrs['enabled']==0)?'color:#ccc;font-style:italic':'';
			$out .= '<li id="list_'.$arrs['cat_id'].'" style="margin-top:5px"><div class="main"><div class="draghandler" style="float:left;width:24px">:::</div><div style="float:left;width:345px;'.$disstyle.'">';
			if($arrs['type']=='TEXT') {
				$out .= '<span class="icos-list2"></span>';
			} else {
				$out .= '<span class="icos-imac"></span>';				
			}
			$out .= '&nbsp;'.$arrs['title'].'&nbsp;</div>'; //pageCheck($arrs['type'],$arrs['source'],$arrs['cat_id'])
			$out .= '<div style="float:left;'.$disstyle.'">';
				$out .=  ($arrs['type']=='REMOTE_LINK') ? $arrs['source'] :  $_CFG['siteURL'].$_ADM_CONTLNG.$arrs['permalink'].$arrs['permaend'];
			$out .= '</div>';
			$out .= editMenu($arrs['cat_id'],$arrs['enabled'],$arrs);
			$out .= '<div style="clear:both"></div></div>';
			$out .= get_childs($arrs['cat_id']);
			$out .= '</li>';
		}

	}
	if(count($arr)>0) $out .= ' </ol>';
	return ($has_subcats) ? $out : false;
}

function get_childs2Select($el_id,$plink){
	global $_MENU, $D;
	$out = '';
	$arr = array();
	$has_subcats = false;
	foreach ( $_MENU as $k => $arrs ){ if ($arrs['parent']==$el_id) { $arr[] = $arrs; } }
	uasort($arr, 'so');
	foreach ( $arr as $k => $arrs ){
		$has_subcats = true;
		$nbsp = round2even($arrs['depth'])*3;
		$value = $arrs['depth'].'_'.$arrs['cat_id'];
		$out .= '<option '.sPOST($D['parent'],$arrs['cat_id']).' link="'.$arrs['permalink'].'" value="'.$value.'">'.nbsp($nbsp).'- '.$arrs['title'].'</option>';
		$out .= get_childs2Select($arrs['cat_id'],$arrs['permalink']);
	}
	return ($has_subcats) ? $out : false;
}

############################## 
#### ACTION ON GET METODS #### 
##############################


if(isset($_GET['disableId'])){ disable_menu_item('disableId'); }

if(isset($_GET['enableId'])) { disable_menu_item('enableId');  }


if(isset($_GET['del_cat_Id'])){
	sql::delete("DELETE FROM `cn_sitemap` WHERE lang='".$_ADM_CONTLNG."' AND cat_id=".$_GET['del_cat_Id']);
	adminLog('გვერდის წაშლა (კატეგორია:'.$_GET['del_cat_Id'].', ენა:'.$_ADM_CONTLNG.')');
	redirect('?p=structure&sp=sitemap');
}

if (isset($_POST['editPage'])){
	$addedLangs = array();
	foreach($_POST as $key => $value){
		if(current(explode('_',$key))=='addOnLang') $addedLangs[] = $value;
	}
	$depth  = (@$_POST['menuType']=='l') ? (current(explode('_',$_POST['catLevel']))+1) : 1;
	$parent = (@$_POST['menuType']=='l') ? (end(explode('_',$_POST['catLevel']))) : 0;
	$SQL = "UPDATE `cn_sitemap` SET
			`name`= '".$_POST['title']."',
			`depth` = ".$depth.",
			`parent` = ".$parent.",
			`position` = '".@$_POST['menuType']."',
			`pagetype` = '".$_POST['pagetype']."',
			`source` = '".@$_POST['source']."',
			`permalink` = '".$_POST['permalink']."',
			`metatags` = '".@$_POST['metatags']."'
			WHERE 
			`Id`= ".@$_GET['editId'];
	sql::update($SQL);
	adminLog('გვერდის რედაქტირება(სახელი: '.$_POST['title'].'; Id:'.@$_GET['editId'].')');
	//post_page_reload();
}


if(isset($_POST['addPage'])){
	$addedLangs = array();
	foreach($_POST as $key => $value){
		if(current(explode('_',$key))=='addOnLang') $addedLangs[] = $value;
	}
		$_POST['depth']  = (current(explode('_',$_POST['catLevel']))+1);
		$_POST['parent'] = (end(explode('_',$_POST['catLevel'])));
		$ord = 99999999999;
		$ord = (@$_POST['menuType']=='h') ? 999999999 : ($ord+1);
		$new_cat_id = sql::getElem('SELECT max(cat_id) FROM `cn_sitemap`');
		$new_cat_id = (is_numeric($new_cat_id)) ? ($new_cat_id+1) : 1;
		if($_POST['pagetype']=='TEXT')   { $_POST['source'] = 'content.php'; }
		if($_POST['pagetype']=='FILE')   { $_POST['source'] = $_POST['source-file']; }
		if($_POST['pagetype']=='MODULE') { $_POST['source'] = $_POST['source-module']; }
		foreach($_get_langs as $k => $v){
				 $SQL = "INSERT INTO `cn_sitemap` SET
						`menuId`= '1',
						`cat_id`= ".$new_cat_id.",
						`lang`= '".$k."',
						`title`= '".$_POST['title']."',
						`depth` = ".$_POST['depth'].",
						`parent` = ".$_POST['parent'].",
						`ord` = ".$ord.",
						`enabled` = 1,
						`source` = '".$_POST['source']."',
						`extra_source` = '".@$_POST['extra_source']."',
						`pagetype` = '".$_POST['pagetype']."',
						`permalink` = '".$_POST['permalink']."',
						`metatags` = '".@$_POST['metatags']."',
						`itemstyle` = '".@$_POST['itemstyle']."',
						`add_date` = now()";
			$ins_id = sql::insert($SQL,true);
			adminLog('ახალი გვერდის შექმნა(სახელი: '.@$_POST['title_'.@$lang].'; ID:'.$ins_id.')');
		}
	//post_page_reload();
}
if(isset($_POST['editPage'])){
		$_POST['depth']  = (current(explode('_',$_POST['catLevel']))+1);
		$_POST['parent'] = (end(@explode('_',$_POST['catLevel'])));
		if($_POST['pagetype']=='TEXT')   { $_POST['source'] = 'content.php'; }
		if($_POST['pagetype']=='FILE')   { $_POST['source'] = $_POST['source-file']; }
		if($_POST['pagetype']=='MODULE') { $_POST['source'] = $_POST['source-module']; }
		if($_POST['pagetype']=='REMOTE_LINK') { $_POST['source'] = $_POST['extUrl']; }
		if($_POST['pagetype']=='INTERNAL_LINK')  { $_POST['source'] = $_POST['extUrl']; }
		$ord = (@$_POST['menuType']=='h') ? 999999999 : (@$ord+1);
		$SQL = "UPDATE `cn_sitemap` SET
						`title`= '".$_POST['title']."',
						`depth` = ".$_POST['depth'].",
						`parent` = ".$_POST['parent'].",
						`source` = '".$_POST['source']."',
						`extra_source` = '".@$_POST['extra_source']."',
						`pagetype` = '".$_POST['pagetype']."',
						`permalink` = '".$_POST['permalink']."',
						`metatags` = '".@$_POST['metatags']."',
						`itemstyle` = '".@$_POST['itemstyle']."',
						`add_date` = now()
				WHERE `lang`= '".$_ADM_CONTLNG."' AND cat_id=".$_POST['editId'];
		sql::update($SQL);
		adminLog('გვერდის რედაქტირება(ენა:'.$_ADM_CONTLNG.'; კატეგორია:'.$_POST['editId']);
	//post_page_reload();
}
//die();

############################## 
#### ACTION ON GET METODS #### 
##############################
##########################################################################################

$sql = sql::getRows("SELECT * FROM `cn_sitemap` WHERE `lang`='".$_ADM_CONTLNG."' ORDER BY ord");
$_last_childs = sql::getCol("SELECT cat_id FROM `cn_sitemap`
										WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_id` NOT IN (SELECT `parent` FROM `cn_sitemap`)");
$_MENUItems = 0;
$_parents = array();
foreach ($sql as $row){
	$_MENU[$row['cat_id']] = array(
							 'Id'		=>	$row['Id'],
							 'title'	=>	$row['title'],
							 'link'		=>	$row['cat_id'],
							 'parent'	=>	$row['parent'],
							 'depth'	=>	$row['depth'],
							 'enabled'	=>	$row['enabled'],
							 'type'		=>	$row['pagetype'],
							 'source'	=>	$row['source'],
							 'cat_id'	=>	$row['cat_id'],
							 'homepage'	=>	$row['homepage'],
							 'ord'		=>	$row['ord'],
							 'add_date'	=>	$row['add_date'],
							 'permalink'=>	$row['permalink'],
							 'menuId'	=>	$row['menuId']
							 );
	$_MENUItems = ($_MENUItems<$row['cat_id']) ? $row['cat_id'] : $_MENUItems;
}
foreach($_MENU as $cat_id => $v){
	$html = '';
	if(in_array($cat_id,$_last_childs)) $html = '';
	$_MENU[$cat_id]['permaend'] = $html;
	if($v['parent']>$cat_id){
		$_MENU[$cat_id]['permalink'] = $v['permalink'].'/'.$_MENU[$v['parent']]['permalink'];
	} else {
		@$_MENU[$cat_id]['permalink'] = @$_MENU[$v['parent']]['permalink'].'/'.$v['permalink'];
	}
}

$arr = array();
for ( $i=1; $i<=$_MENUItems; $i++ ){
	if(isset($_MENU[$i])){
		if (@$_MENU[$i]['parent']==0 && @$_MENU[$i]['position']!='h') {
			$arr[] = $_MENU[$i];
		}
	}
}

uasort($arr, 'so');

?>

<!-- Content begins -->   
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-link"></span>SITEMAP</span>
        <div class="clear"></div>
    </div>
    <div class="breadLine"></div>    
    <!-- Main content -->

<div class="fluid" style="margin-left:10px; width:98%">
	<div class="widget" style="margin-top:15px">
	<div class="whead"><h6>ვებ გვერდის სტრუქტურა</h6>
    <ul class="titleToolbar">
            <li><a  onclick="saveTreeOrder(this)" title="განლაგების შენახვა" class="tipE" href="#">შენახვა</a></li>
            <li><a  onclick="openeditor(0)" title="ახალი გვერდის დამატება" class="tipE" href="#">დამატება</a></li>
        </ul>	
    <div class="clear"></div></div>
    <div style="padding:30px">
	    <ol class="sortable">
<?php
foreach ($arr as $key => $arrs){
	if($arrs['menuId']>0){
		echo '<li id="list_'.$arrs['cat_id'].'" style="margin-top:5px">';
		echo '<div class="main"><div class="draghandler" style="float:left;width:24px">:::</div>';
		echo '<div style="float:left;width:380px">';
		if($arrs['type']=='TEXT') {
			echo '<span class="icos-list2"></span>';
		} else {
			echo '<span class="icos-imac"></span>';				
		}
		$disstyle = ($arrs['enabled']==0)?'color:#ccc;font-style:italic':'';
		echo '&nbsp;<span class="name" style="'.$disstyle.'">'.$arrs['title'].'</span>&nbsp;';
		echo '</div>';
			echo '<div style="float:left;'.$disstyle.'">';
			echo ($arrs['type']=='REMOTE_LINK') ? $arrs['source'] :  $_CFG['siteURL'].$_ADM_CONTLNG.$arrs['permalink'].$arrs['permaend'];
			echo '</div>';
			echo editMenu($arrs['cat_id'],$arrs['enabled'],$arrs);
		echo '<div style="clear:both"></div></div>';
		echo get_childs($arrs['cat_id']);
		echo '</li>';
	}
}
?>
  </ol>
  </div>
  <hr>
    <div style="padding:30px">
	    <ol class="sortable">
<?php
if(isset($_MENU)){
	foreach (@$_MENU as $key => $arrs){
		if($arrs['menuId']<0){
			echo '<li id="list_'.$arrs['cat_id'].'" style="margin-top:5px">';
			echo '<div class="main"><div class="draghandler" style="float:left;width:24px">:::</div>';
			echo '<div style="float:left;width:380px">';
			if($arrs['type']=='TEXT') {
				echo '<span class="icos-list2"></span>';
			} else {
				echo '<span class="icos-imac"></span>';				
			}
			$disstyle = ($arrs['enabled']==0)?'color:#ccc;font-style:italic':'';
			echo '&nbsp;<span class="name" style="'.$disstyle.'">'.$arrs['title'].'</span>&nbsp;';
			echo '</div>';
				echo '<div style="float:left;'.$disstyle.'">';
				echo ($arrs['type']=='REMOTE_LINK') ? $arrs['source'] :  $_CFG['siteURL'].$_ADM_CONTLNG.$arrs['permalink'].$arrs['permaend'];
				echo '</div>';
				echo editMenu($arrs['cat_id'],$arrs['enabled'],$arrs);
			echo '<div style="clear:both"></div></div>';
			echo get_childs($arrs['cat_id']);
			echo '</li>';
		}
	}
}
?>
  </ol>
  </div>

</div>
</div>
<script type="text/javascript" src="js_css/plugins/others/jquery.mjs.nestedSortable.js"></script>

<?php

if(isset($_GET['getdepthlink'])){
	ob_clean();
	$links = array();
	function getlinks($Id){
		$r = sql::getRow("SELECT * FROM cn_sitemap WHERE Id=".$Id);
	}
	die();
}

if(isset($_GET['saveSortedTree'])){
	ob_clean();
	foreach($_POST['treedata'] as $k => $v){
		unset($_POST['treedata'][$k]['left']);
		unset($_POST['treedata'][$k]['right']);
		if($v['item_id']=='null') unset($_POST['treedata'][$k]);
		if($v['parent_id']=='null') $_POST['treedata'][$k]['parent_id']=0;
	}
	foreach($_POST['treedata'] as $k => $v){
		sql::update("UPDATE `cn_sitemap` SET `ord`=".$k.", `depth`=".$v['depth'].", `parent`=".$v['parent_id']."
												WHERE `cat_id`=".$v['item_id']);
	}
	adminLog('საიტის სტრუქტურის გადალაგება');
	sleep(1);
	die();
}

if(isset($_POST['contenteditsave'])){
	ob_clean();
	sql::update("UPDATE `cn_sitemap` SET `extra_source`='".$_POST['contenteditsave']."'
												WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_id`='".$_GET['Id']."' ");
	die();
}

if(isset($_GET['contenteditorform'])){
	ob_clean();
	$type = sql::getElem("SELECT `pagetype` FROM `cn_sitemap` WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_id`='".$_GET['Id']."'");
	if($type!='TEXT'){
		echo '<div class="fluid" style="margin-left:10px; width:400px">
				<div class="widget" style="margin-top:0">
					<div class="whead"><h6>შეცდომა</h6><div class="clear"></div></div>
					<div style="padding:10px">
						ეს გვერდი არ არის ტექსტური ტიპის.<br>შესაბამისად ამ გვერდის რედაქტირება შეუძლებელია.
					</div>
				</div>
		   </div>';
		die();
	}
	$html = sql::getElem("SELECT `extra_source` FROM `cn_sitemap` WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_id`='".$_GET['Id']."'");
?>
   <div class="fluid" style="margin-left:10px; width:822px">
        <div class="widget" style="margin-top:0">
            <div class="whead"><h6>ტექსტური გვერდის კონტენტის რედაქტირება</h6>
            <ul class="titleToolbar">
            <li><a id="saveButton" href="javascript:void(0)"><span class="icos-check"></span>&nbsp;&nbsp;<span class="text">შენახვა</span></a></li>
        </ul>
            <div class="clear"></div></div>
			<form method="POST" action="">
				<textarea name="_E_TEXT" id="wysiwyg_text" style="width:820px; height:500px"><?php echo $html; ?></textarea>
			</form>
        </div>
   </div>
<script type="text/javascript">
tinyMCE_init('wysiwyg_text');
$('#saveButton').click(function(){
	var o = $(this).find('span.text');
	$(o).html('მოითმინეთ...');
	var c = tinyMCE.get('wysiwyg_text').getContent();
	$.post("<?php echo full_url() ?>",{'contenteditsave':c},function(data){
			$(o).html('შენახვა');
	  });
})

</script>
<style type="text/css"> #wysiwyg_text_parent table#wysiwyg_text_tbl { border-left:none; border-right:none } </style>
<?php
	die();
}

if(isset($_GET['editorform'])){
	ob_clean();
	$ADD['div']['display'] = 'none';
	$ADD['button']['name'] = 'addPage';
	$ADD['button']['value'] = 'დამატება';

	if(@$_GET['Id']>0){
		$ADD['div']['display'] = 'block';
		$ADD['button']['name'] = 'editPage';
		$ADD['button']['value'] = 'რედაქტირება';
	}

	$_menuclasses = array();
	
	foreach (glob("../theme/js_css/*.css") as $filename) {
		$csscontent = explode("\n",file_get_contents($filename));
		foreach($csscontent as $line) {
			if(preg_match("/\/\*showinsitemap\*\//",$line)) $_menuclasses[] = preg_replace("/\/\*showinsitemap\*\//","",$line);
		}
	}

	if($_GET['Id']>0) $D = sql::getRow("SELECT * FROM `cn_sitemap` WHERE cat_id='".$_GET['Id']."' AND `lang`='".$_ADM_CONTLNG."'");
?>
   <div class="fluid" style="margin-left:10px; width:800px">
        <div class="widget" style="margin-top:0">
        <div class="whead"><h6>დამატება</h6>
        <div class="clear"></div></div>
        <form method="POST" action="">
        <input type="hidden" value="<?php echo @$_GET['Id']; ?>" name="editId" />
        <table border="0" cellspacing="0" cellpadding="3" style="width:100%">
            <tr>
                <td style="text-align:right; width:130px">ზეკატეგორია: </td>
                <td style="text-align:left" class="formRow">
                <select style="font-size:12px; width:260px" id="catLevel" name="catLevel">
                <option value="0">სულ ზედა კატეგორია</option>
                <?php
                foreach ($arr as $key => $arrs){
                    $nbsp = ($arrs['depth']>1) ? (round2even($arrs['depth'])*3) : 0;
                    $value = $arrs['depth'].'_'.$arrs['cat_id'];
                    echo '<option '.sPOST($D['parent'],$arrs['cat_id']).' link="'.$arrs['permalink'].'" value="'.$value.'" >'.nbsp($nbsp).'- '.$arrs['title'].'</option>';
                    echo get_childs2Select($arrs['cat_id'],$arrs['permalink']);
                }
                ?>
                </select>
               </td>
              </tr>
              <tr>
                <td style="text-align:right">გვერდის ტიპი: </td>
                <td style="text-align:left" class="formRow">
                 <select name="pagetype" id="pagetype" style="font-size:12px; width:260px">
                    <option value="TEXT" <?php echo sPOST(@$D['pagetype'],'TEXT'); ?> >TEXT - წაკითხვა ხდება ბაზიდან</option>
                    <option value="FILE" <?php echo sPOST(@$D['pagetype'],'FILE'); ?>>FILE - წაკითხვა ხდება ფაილიდან</option>
                    <option value="MODULE" <?php echo sPOST(@$D['pagetype'],'MODULE'); ?>>MODULE - წაკითხვა ხდება მოდულიდან</option>
                    <option value="VOID" <?php echo sPOST(@$D['pagetype'],'VOID'); ?>>უბრალოდ ბმული void(0)</option>
                    <option value="REMOTE_LINK" <?php echo sPOST(@$D['pagetype'],'REMOTE_LINK'); ?>>გარე ბმული</option>
                    <option value="INTERNAL_LINK" <?php echo sPOST(@$D['pagetype'],'INTERNAL_LINK'); ?>>შიდა ბმული</option>
                </select></td>
              </tr>
              <tr>
                <td style="text-align:right">სათაური: </td>
                <td style="text-align:left" class="formRow">
                    <input type="text" name="title" id="title" style="font-size:12px; float:left; width:250px; margin-bottom:1px" value="<?php echo @$D['title']; ?> " />
                </td>
              </tr>
              <?php
              	if(count($_menuclasses)>0){
			  ?>
              <tr>
                <td style="text-align:right">სტილი: </td>
                <td style="text-align:left" class="formRow">
                <select style="width:250px" name="itemstyle">
                <option value="">აირჩიეთ სტილი...</option>
                <?php
					foreach($_menuclasses as $v) {
						echo '<option '.sPOST(@$D['itemstyle'],$v).' value="'.$v.'">'.$v.'</option>';
					}
				?>
                </select>
                </td>
              </tr>
              <?php } ?>
              <tr  class="pageparameter" id="sourceTR">
                <td style="text-align:right">ფაილი: </td>
                <td style="text-align:left" valign="top" class="formRow">
                <select style="width:250px" name="source-file">
                <option value="">აირჩიეთ ფაილი...</option>
                <?php
                	$folder = '../theme/pages/';
					$dirhandle = opendir($folder);
						while ($files = readdir($dirhandle)) {
							if(is_file($folder.'/'.$files) && !in_array($files,array('.','..')))
							echo '<option '.sPOST(@$D['source'],$files).' value="'.$files.'">'.$files.'</option>';
						}
					closedir($dirhandle);

				?>
                </select>
                </td>
              </tr>
              <tr  class="pageparameter" id="sourceModuleTR">
                <td style="text-align:right">მოდული: </td>
                <td style="text-align:left" valign="top" class="formRow">
                <select style="width:250px" name="source-module" id="modulename">
                <option value="">აირჩიეთ მოდული...</option>
                <?php
					$mods = sql::getRows("SELECT * FROM `cn_modules` ORDER BY `ord`");
					foreach($mods as $v){ echo '<option '.sPOST(@$D['source'],$v['sys_name']).' value="'.$v['sys_name'].'">'.$v['name'].'</option>'; }
				?>
                </select>
                <div style="padding:3px 0 3px 0"><div>
                <?php
                	if(isset($_POST['getModulesValues'])){
						ob_clean();
						echo '<select style="width:250px" name="extra_source" id="moduleValue">
								<option value="">აირჩიეთ მოდულის ჩანაწერი...</option>';
								$field = sql::getElem("SELECT `field_sys_name` FROM `cn_modules_structures`
																		WHERE `module_sys_name`='".$_POST['getModulesValues']."'
																		  AND `show_in_sitemap_editor`=1");
								$vals = sql::getRows("SELECT `cat_id`, `".$field."` as `field` FROM `_cn_mod_".$_POST['getModulesValues']."`
																					WHERE `lang`='".$_ADM_CONTLNG."'");
								foreach($vals as $v){
									echo '<option '.sPOST(@$D['extra_source'],$v['cat_id']).' value="'.$v['cat_id'].'">'.$v['field'].'</option>'; 
								}
						echo '</select>';
						die();
					}
				?>
                </td>
              </tr>
              <tr  class="pageparameter" id="seoTR">
                <td style="text-align:right">Link: </td>
                <td style="text-align:left; font-size:11px; color:#666; font-family:'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif;" class="formRow">
                            <?php echo $_CFG['siteURL']; ?><span id="sublink">&nbsp;</span>
                        <input type="text" name="permalink" id="permalink" style="font-size:12px; width:110px; margin-bottom:1px" value="<?php echo @$D['permalink']; ?>" />
                </td>
              </tr>
              <tr  class="pageparameter" id="extUrlTR">
                <td style="text-align:right">URL: </td>
                <td style="text-align:left; font-size:11px; color:#666; font-family:'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif;" class="formRow">
                        <input type="text" name="extUrl" style="font-size:12px; width:250px; margin-bottom:1px" value="<?php echo @$_POST['extUrl']; ?>" />
                </td>
              </tr>
              <tr>
                <td colspan="2" align="center"><div id="addPageStatus"></div>
                <input type="submit" name="<?php echo $ADD['button']['name']?>" id="<?php echo $ADD['button']['name']?>" value="<?php echo $ADD['button']['value']?>" class="sideB bLightBlue" style="margin:10px 0 10px 0" /></td>
              </table>
              </form>
              <script type="text/javascript">
                $('#modulename').change(function(){
					$.post("<?php echo full_url(); ?>", {getModulesValues:$(this).val()}, function(data){
						$('#sourceModuleTR div').html(data);
					});
				})
				
				$('.addPageLangs').click(function(){
                    if(this.checked==true){
                        $(this).parent().css({'color':'black','font-weight':'bold'});
                    } else {
                        $(this).parent().css({'color':'#999','font-weight':'normal'});
                    }
                })
				
				$('#catLevel').change(function(){
						l = $(this).find('option:selected').attr('link');
						l = (l!=undefined && l!='') ? (l+'/') : '';
						l = '<?php echo $_ADM_CONTLNG; ?>'+l;
						$('#sublink').html(l);
				})
				
				$("#pagetype").change(function(){
					$('.pageparameter').hide();
					if($(this).val()=='TEXT'){
						$('#seoTR').show();
						$('#metatagsTR').show();
					}
					if($(this).val()=='FILE'){
						$('#seoTR').show();
						$('#sourceTR').show();
						$('#metatagsTR').show();
					}
					if($(this).val()=='MODULE'){
						$('#seoTR').show();
						$('#sourceModuleTR').show();
						$('#metatagsTR').show();
					}
					if($(this).val()=='VOID'){ }
					if($(this).val()=='REMOTE_LINK' || $(this).val()=='INTERNAL_LINK'){
						$('#extUrlTR').show();
					}
				})
				$("#pagetype").trigger('change');
				$('#modulename').trigger('change');
                </script>
        </div>
    </div>
<?php 
	die();
}
?>
<script type="text/javascript" src="/common/tinymce_v3/tiny_mce.js"></script>
<script type="text/javascript">

function saveTreeOrder(o){
	$(o).html('მოითმინეთ...');
	arraied = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});
	$.post("<?php echo full_url(); ?>&saveSortedTree",{'treedata':arraied},function(data){
		$(o).html('შენახვა');
	})
}

$(document).ready(function(){
	$('.sortable').nestedSortable({
		forcePlaceholderSize: true,
		handle: 'div.draghandler',
		helper:	'clone',
		items: 'li',
		opacity: .6,
		placeholder: 'placeholder',
		revert: 250,
		tabSize: 25,
		tolerance: 'pointer',
		toleranceElement: '> div'
	});
});

function openeditor(id){
	url = [];
	url.push('?p=structure');
	url.push('sp=sitemap');
	url.push('editorform');
	url.push('Id='+id);
	$.fancybox.open({'type':'ajax', 'autoWidth':true, 'href':url.join('&')});
}

function opencontenteditor(id){
	url = [];
	url.push('?p=structure');
	url.push('sp=sitemap');
	url.push('contenteditorform');
	url.push('Id='+id);
	url.push(Math.random().toString());
	$.fancybox.open({'type':'ajax', 'autoWidth':true, 'href':url.join('&')});
}

</script>

<style type="text/css">
.pageparameter{ display:none }
.tLight tbody td{ padding:3px 16px; }
ol {
	margin: 0;
	padding: 0;
	padding-left: 30px;
}

ol.sortable, ol.sortable ol {
	margin: 0 5px 0 5px;
	padding: 0;
	list-style-type: none;
}

ol.sortable {
	margin: 4em 0;
}

ol.sortable, ol.sortable ol {
	margin: 0 5px 0 5px;
	padding: 0;
	list-style-type: none;
}
ol.sortable ol {
	margin: 0 0 0 35px;
}
.sortable div.draghandler{
		cursor: move;	
}

.placeholder {
	outline: 1px dashed #4183C4;
	/*-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	margin: -1px;*/
}
.sortable li div.main  {
	border: 1px solid #d4d4d4;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	border-color: #D4D4D4 #D4D4D4 #BCBCBC;
	padding: 2px 6px 2px 6px ;
	margin: 0;
	background: #f6f6f6;
	background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #ededed 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(47%,#f6f6f6), color-stop(100%,#ededed));
	background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
	background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
	background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
	background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed',GradientType=0 );
}

.sortable li.mjs-nestedSortable-branch div {
	background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #f0ece9 100%);
	background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#f0ece9 100%);

}

.sortable li.mjs-nestedSortable-leaf div {
	background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #bcccbc 100%);
	background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#bcccbc 100%);

}

li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div {
	border-color: #999;
	background: #fafafa;
}

.disclose {
	cursor: pointer;
	width: 10px;
	display: none;
}

.sortable li.mjs-nestedSortable-collapsed > ol {
	display: none;
}

.sortable li.mjs-nestedSortable-branch > div > .disclose {
	display: inline-block;
}


.sortable li div.main:hover{
	background:#ededed 
}
	
</style>