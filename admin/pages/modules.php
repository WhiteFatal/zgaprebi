<?php
if (!defined('FILE_SECURITY_KEY'))  die();
if(!has::role('modules'))  		    die();

$mods = sql::getRows("SELECT * FROM `cn_modules` ORDER BY `ord`");
$_GET['mod'] = (isset($_GET['mod'])) ? $_GET['mod'] : @$mods[0]['sys_name'];

if(strlen($_GET['mod'])>0) if(!has::role('module:'.$_GET['mod'])) die();

$structure = modules::getStructure($_GET['mod']);
$WAL = sql::getElem("SELECT `write_all_langs` FROM `cn_modules` WHERE `sys_name`='".$_GET['mod']."'");

sql::delete("DELETE FROM `_cn_mod_".$_GET['mod']."` WHERE `active`='-1' AND DATE_SUB(`modify_date`, INTERVAL 1 DAY)>now()");

############################################################################################
if(isset($_POST['photoOrds'])){
	ob_clean();
	$_POST['photoOrds'] = explode(',',$_POST['photoOrds']);
	foreach($_POST['photoOrds'] as $k => $v){
		$sql = "UPDATE `cn_modules_photos` SET `ord`=".$k." WHERE `cat_id`=".$v;
		sql::update($sql);
	}
	die('ok');
}
############################################################################################
if(isset($_GET['delphotocatId'])){
	ob_clean();
	$ids = sql::getElem("SELECT `".$_GET['field']."` FROM `_cn_mod_".$_GET['mod']."`
														WHERE cat_Id='".$_GET['editId']."' AND `lang`='".$_ADM_CONTLNG."'");
	$ids = in_set_string($_GET['delphotocatId'],$ids,'remove');
	sql::update("UPDATE `_cn_mod_".$_GET['mod']."` SET `".$_GET['field']."`='".$ids."'
														WHERE cat_Id='".$_GET['editId']."' AND `lang`='".$_ADM_CONTLNG."'");
	die();
}
############################################################################################
if(isset($_POST['set_photo_name'])){
	ob_clean();
	foreach($_POST['vals'] as $lang => $v){
		echo $sql = "UPDATE `cn_modules_photos` SET `title`='".sql::safe($v)."' WHERE
								 `cat_id`='".$_POST['set_photo_name']."' AND `lang`='".$lang."'";
		sql::insert($sql);
	}
	die('ok');
}
############################################################################################
if(isset($_GET['photonamer'])){
	ob_clean();
	$photo = sql::getElem("SELECT `file` FROM `cn_albums_photos` WHERE `cat_id`=".$_GET['photo_cat_id']);
	$photo = '../storage/uploads/'.@$_GET['album_id'].'/thumb_'.$photo;
	echo '
			<form action="" method="POST" onsubmit="return false">
		<input type="hidden" name="set_photo_name" id="set_photo_name" value="'.$_GET['photo_cat_id'].'">
		<div class="widget" style="margin:0; width:500px">
            <table cellpadding="0" cellspacing="0" width="100%" class="tDark">';
	echo '<thead><tr>';
	foreach($_get_langs as $v) echo '<td>'.$v.'</td>';
	echo '</tr></thead><tbody>';
		echo '<tr>';
		foreach($_get_langs as $l => $ln) {
			$phototitle = sql::getElem("SELECT `title` FROM `cn_albums_photos` WHERE `lang`='".$l."' AND `cat_id`=".$_GET['photo_cat_id']);
			echo '<td style="padding:0"><div class="formRow" style="border:none; padding:4px 16px;"><span class="grid12"><input type="text" name="vals['.$l.']" value="'.$phototitle.'"></span><div class="clear"></div></div></td>';
		}
		echo '</tr>';
		echo '</tbody><tfoot><tr>';
		echo '<td colspan="'.count($_get_langs).'" style="text-align:center"><a href="javascript:void(0)" title="" onclick="savethis()" class="sideB bLightBlue">'.__('შენახვა').'</a></td>';
		echo '</tr></tfoot>';
	echo '</table></div></form>
	<script type="text/javascript">
	function savethis(){
		$.post("'.full_url().'",$(\'.fancybox-inner form\').serializeArray(),function(data){
			reloader(\''.$_GET['type'].'\');
			$.fancybox.close();
		})
	}
	</script>';
	die();
}
############################################################################################
if(isset($_POST['save_manage_textlists'])){
	ob_clean();
	$cat_id=1;	
	sql::delete("DELETE FROM `cn_modules_textlists` WHERE
									 `module_sys_name`='".$_GET['mod']."' AND
									 `field_name`='".$_GET['manage_textlists']."'");
	foreach($_POST['vals'] as $k => $v){
		if(implode('',$v)=='') unset($_POST['vals'][$k]);
	}
	foreach($_POST['vals'] as $v){
		foreach($v as $l => $vv){
			$vv = sql::safe($vv);
			sql::insert("INSERT INTO `cn_modules_textlists` SET
									 `cat_id`='".$cat_id."',
									 `field_value`='".$vv."',
									 `lang`='".$l."',
									 `module_sys_name`='".$_GET['mod']."',
									 `field_name`='".$_GET['manage_textlists']."'");
		}
		$cat_id++;
	}
	die('ok');
}
############################################################################################
if(isset($_GET['manage_textlists'])){
	ob_clean();
	$cats = array();
	$last_cat_id = 0;
	$rows = sql::getRows("SELECT * FROM `cn_modules_textlists`
								WHERE module_sys_name='".$_GET['mod']."' AND field_name='".$_GET['manage_textlists']."'");
	foreach($rows as $v) {
		$last_cat_id = ($last_cat_id<$v['cat_id']) ? $v['cat_id'] : $last_cat_id;
		$cats[$v['cat_id']][$v['lang']] = $v['field_value'];
	}
	echo '
		<form action="" method="POST" onsubmit="return false">
		<input type="hidden" name="save_manage_textlists" id="save_manage_textlists" value="'.$last_cat_id.'">
		<div class="widget" style="margin:0">
            <table cellpadding="0" cellspacing="0" width="100%" class="tDark">';
	echo '<thead><tr>';
	foreach(get::langs() as $v) echo '<td>'.$v.'</td>';
	echo '</tr></thead><tbody>';
	foreach($cats as $cid => $v){
		echo '<tr>';
		foreach(get::langs() as $l => $ln) echo '<td style="padding:0"><div class="formRow" style="border:none; padding:4px 16px;"><span class="grid6"><input type="text" name="vals['.$cid.']['.$l.']" value="'.$v[$l].'"></span><div class="clear"></div></div></td>';
		echo '</tr>';
	}
		echo '</tbody><tfoot><tr>';
		echo '<td colspan="'.count(get::langs()).'" style="text-align:center"><a href="javascript:void(0)" onclick="addmanage_textlists()">'.__('დამატება').'</a><br><br><input type="button" onclick="savethis()" value="'.__('შენახვა').'"></td>';
		echo '</tr></tfoot>';
	echo '</table></div></form>
	<script type="text/javascript">
	function addmanage_textlists(){
		var smt = $("#save_manage_textlists").val();
		smt++;
		var html = "<tr>";';
		  foreach(get::langs() as $l => $ln) {
			  echo 'html += "<td style=\"padding:0\"><div class=\"formRow\" style=\"border:none; padding:4px 16px;\"><span class=\"grid5\"><input name=\"vals["+smt+"]['.$l.']\" type=\"text\"></span><div class=\"clear\"></div></div></td>";';
		  }
	echo 'html += "</tr>";
		$("#save_manage_textlists").val(smt);
		$(".fancybox-inner table tbody").append(html);
		$.fancybox.update();
	};

	addmanage_textlists();
	function savethis(){
		$.post("'.full_url().'",$(\'.fancybox-inner form\').serializeArray(),function(data){
			document.location.reload();
		})
	}
	</script>';
	die();
}
############################################################################################

?>
    <!-- Secondary nav -->
    <div class="secNav">
        <div class="secWrapper">
            <div class="secTop">
                <div class="balance">
                    <div class="balInfo"><?php _e('არსებული მოდულები'); ?><span><?php _e('სულ:'); ?> <?php echo sql::cnt("SELECT * FROM `cn_modules`"); ?> <?php _e('მოდული'); ?></span></div>
                </div>
                <?php if (is::developerIp()) echo '<a href="javascript:void(0)" onclick="openeditor()" class="triangle-red"></a>'; ?>
          </div>
            <div class="divider" style="margin-top:0"><span></span></div>            
          <!-- Tabs container -->
            <div id="tab-container" class="tab-container">
              <div id="general">
               <center>
              	<?php if($WAL=='1') echo adminlangchooser(); ?><br><br>
              </center>
                    <ul class="subNav">
						<?php
                            foreach($mods as $v){
								if(has::role('module:'.$v['sys_name'])){
									$thisclass = (($v['sys_name']==$_GET['mod'])?'class="this"':'');
									$icos = ($v['type']=='module') ? 'icos-cog2' : 'icos-arrowup';
									$icoscolor = ($v['type']=='submodule') ? 'style="color:#999"' : '';
									echo '<li>';
									echo '<a href="?p=modules&mod='.$v['sys_name'].'" '.$thisclass.' '.$icoscolor.' title="">';
									echo '<span class="'.$icos.'"></span>'.$v['name'].'</a>';
									echo '</li>';
								}
                            }
							if(has::role('module:polls')){
								$thisclass = (('polls'==$_GET['mod'])?'class="this"':'');
								echo '<li>';
								echo '<a '.$thisclass.' href="?p=modules&mod=polls" title="'.__('გამოკითხვები').'">';
								echo '<span class="icos-cog2"></span>'.__('გამოკითხვები').'</a>';
								echo '</li>';
							}
                        ?>
                    </ul>
                </div>
                
          </div>
            
            <div class="divider"><span></span></div>            
      </div> 
       <div class="clear"></div>
   </div>
</div>
<!-- Sidebar ends -->
<!-- Content begins -->   
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-link"></span><?php _e('მოდული'); ?> &raquo; <?php echo ($_GET['mod']=='polls') ? __('გამოკითხვა') : modules::getName($_GET['mod']); ?></span>
    </div>
    <div class="breadLine"></div>    
    <!-- Main content -->
    <div class="wrapper">
        <div class="fluid">
<?php
	if(!isset($_GET['editId']) && !isset($_GET['add'])) {
			if($_GET['mod']=='polls'){
				include('modules-polls.php');
			} else {
				include('modules-list.php');			
			}
	} else {
		if(!isset($_GET['editgalId'])){
			include('modules-editor.php');
		} else {
			include('modules-gallery.php');	
		}
	}
?>
  </div>
  <div class="divider"><span></span></div>
</div>
<link rel="stylesheet" media="all" type="text/css" href="/common/jquery-ui.css" />
<link rel="stylesheet" media="all" type="text/css" href="/common/datetimepicker/jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="/common/datetimepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="/common/datetimepicker/localization/jquery-ui-timepicker-ka.js"></script>
<script type="text/javascript">
$('input:text.date').datetimepicker({ dateFormat: "yy-mm-dd", timeFormat: '', showMinute: false, showHour: false, showTime: false });
$('input:text.datetime').datetimepicker({
	showSecond: true,
	dateFormat: "yy-mm-dd",
	timeFormat: 'HH:mm:ss'
});
</script>
