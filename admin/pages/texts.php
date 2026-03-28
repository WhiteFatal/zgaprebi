<?php
if(!defined('FILE_SECURITY_KEY')) die();
if(!has::role('texts'))   		  die();


if(!array_key_exists(@$_GET['lang'],$_get_langs)) $_GET['lang'] = key($_CFG['availLangs']);

if(isset($_POST['rootlang'])){
	ob_clean();
	settings::save("rootlang",$_POST['rootlang']);
	settings::save("def_lang_country",$_POST['def_lang_country']);
	die('ok');
}

if(isset($_POST['saveRowId'])){
	ob_clean();
	sql::update("UPDATE `cn_langs` SET `value`='".$_POST['transed']."' WHERE Id=".$_POST['saveRowId']);
	die('Ok');
}


if(isset($_GET['reindex'])){
	$now = date('Y-m-d h:i:s');
	$strings = array();
	$parsed_files = LNG::parse_theme_files();
	foreach($_get_langs as $l => $ln){
		$dbastrings = sql::getCol("SELECT `name` FROM `cn_langs` WHERE `lang`='".$l."'");
		foreach($parsed_files as $k => $v){
			if(!in_array($k,$dbastrings)){
				sql::insert("INSERT INTO `cn_langs` SET
											`name`='".$k."',
											`value`='".$k."',
											`dest`='".implode('\n',$v)."',
											`lastupdate`='".$now."',
											`lang`='".$l."'");
			} else {
				sql::insert("UPDATE `cn_langs` SET
								`lastupdate`='".$now."' WHERE `name`='".$k."'");
	
			}
		}
	}
	sql::delete("DELETE FROM `cn_langs` WHERE `lastupdate`<'".$now."'");
	redirect('?p=texts&reindexok');
}

if(isset($_GET['reindexadmin'])){
	$now = date('Y-m-d h:i:s');
	$strings = array();
	$parsed_files = LNG::parse_admin_files();
	foreach($_get_langs as $l => $ln){
		$dbastrings = sql::getCol("SELECT `name` FROM `cn_adminlangs` WHERE `lang`='".$l."'");
		foreach($parsed_files as $k => $v){
			if(!in_array($k,$dbastrings)){
				sql::insert("INSERT INTO `cn_adminlangs` SET
											`name`='".$k."',
											`value`='".$k."',
											`dest`='".implode('\n',$v)."',
											`lastupdate`='".$now."',
											`lang`='".$l."'");
			} else {
				sql::insert("UPDATE `cn_adminlangs` SET
								`lastupdate`='".$now."' WHERE `name`='".$k."'");
	
			}
		}
	}
	sql::delete("DELETE FROM `cn_adminlangs` WHERE `lastupdate`<'".$now."'");
	redirect('?p=texts&reindexadminok');
}

?>
    <!-- Secondary nav -->
    <div class="secNav">
        <div class="secWrapper">
            <div class="secTop">
                <div class="balance">
                    <div class="balInfo"><?php _e('არსებული ენები'); ?><span><?php echo count($_get_langs); ?> <?php _e('ენა'); ?></span></div>
                </div>
          </div>
            <div class="divider" style="margin:0 0 50px 0"><span></span></div>            
          <!-- Tabs container -->
            <div id="tab-container" class="tab-container">
              <div id="general">
                    <ul class="subNav">
						<?php
							foreach($_get_langs as $k => $v){
								$thisclass = (($k==$_GET['lang'])?'class="this"':'');
								echo '<li>';
								echo '<a href="?p=texts&lang='.$k.'" '.$thisclass.' title="">';
								echo '<span class="icos-lng"><img src="../common/flags/16x16/'.$k.'.png" /></span>'.$v.'</a>';
								echo '</li>';
							}
                        ?>
                    </ul>
                </div>
                
          </div>
            <div class="divider"><span></span></div>
            <?php
            	if(isset($_GET['reindexok'])){
					echo '<div class="nNote nSuccess" style="margin:15px 0 15px 0;"><p>'.__('რეინდექსაცია შესრულდა წარმატებით').'</p></div>
						  <div class="divider"><span></span></div>';
				}
			?>
            <div class="sidePad"><a href="?p=texts&reindex" title="" onclick="return confirm('<?php _e('დარწმუნებული ხართ გსურთ რეინდექსაცია?'); ?>')" class="sideB bLightBlue"><?php _e('ენების რეინდექსაცია'); ?></a></div>
            <div class="divider"><span></span></div>
            <?php /* 
            <div class="widget">
                <div class="whead"><h6><?php _e('ძირითადი ენა'); ?></h6><span class="headLoad" id="loadingrootlang" style="display:none"><img src="img/elements/loaders/6s.gif" alt=""></span><div class="clear"></div></div>
                <div class="body">
                <?php
					$rootlang = settings::get('rootlang');
					$def_lang_country = settings::get('def_lang_country');
                    foreach($_get_langs as $l => $ln){
						$sel = ($rootlang==$l)?'checked="checked"':'';
                        echo '<label><input type="radio" name="rootlang" '.$sel.' value="'.$l.'" />&nbsp;'.$ln.'</label><br>';
                    }
                    echo '<label><input type="radio" '.(($rootlang=='bycountry')?'checked="checked"':'').' name="rootlang" value="bycountry" />&nbsp;'.__('ქვეყნის მიხედვით').'</label><br>';
                ?>
                   <div id="rootlang" style="display:<?php echo (($rootlang=='bycountry')?'block':'none'); ?>">
                   	<label><?php _e('ძირითადი'); ?>: <select id="def_lang_country"><option value="-1"><?php _e('აირჩიეთ...'); ?></option><?php foreach($_get_langs as $l => $ln) echo '<option '.(($def_lang_country==$l)?'selected="selected"':'').' value="'.$l.'">'.$ln.'</option>'; ?></select></label>
                   </div>
                </div>
            </div>
			*/ ?>
		</div> 
       <div class="clear"></div>
   </div>
</div>
<!-- Sidebar ends -->
<script type="text/javascript">
$('#def_lang_country').change(function(){
	if($(this).val()=='-1') return false;
	$('input:radio[name="rootlang"][value="bycountry"]').trigger('click');
});

$('input:radio[name="rootlang"]').click(function(){
	$('#loadingrootlang').show(100);
	if($(this).val()=='bycountry'){ $('#rootlang').show(); } else { $('#rootlang').hide(); }
	$.post("<?php echo full_url(); ?>", {rootlang:$(this).val(),def_lang_country:$('#def_lang_country').val()}, function(data){
		if(data=='ok'){
			$('#loadingrootlang').hide(100);
		} else {
			alert("ERROR!!!\n"+data);
		}
	});
})
</script>


<!-- Content begins -->   
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-link"></span><?php _e('ენა'); ?> &raquo; <?php echo $_get_langs[$_GET['lang']]; ?></span>
        <div class="clear"></div>
    </div>
    <div class="breadLine"></div>    
    <!-- Main content -->

<div class="widget" style="width:98%; margin-left:10px">
 <table cellpadding="0" cellspacing="0" width="100%" class="tLight noBorderT editors">
    <thead><tr><td><?php _e('ტექსტი'); ?></td><td><?php echo $_get_langs[$_GET['lang']]; ?> <?php _e('თარგმანი'); ?></td></tr><thead>
    <tbody>
    <?php
		foreach(sql::getRows("SELECT * FROM `cn_langs` WHERE lang='".$_GET['lang']."'") as $v){
			echo '<tr id="row_'.$v['Id'].'">';
			echo '<td valign="top"><textarea class="noneditable" readonly="readonly" >'.$v['name'].'</textarea></td>';
			echo '<td valign="top"><textarea class="editable">'.$v['value'].'</textarea><div class="saveDiv"><a href="javascript:void(0)" style="font-weight:bold" onclick="saveTrans('.$v['Id'].')">Save</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onClick="close_editor($(this))">Cancel</a></div></td>';
			echo '</tr>';
		}
?>
</tbody>
</table>
</div>
<style type="text/css">
table.editors textarea{ resize:none; height:24px; color:#000 }
table.editors textarea.noneditable{ background:#F6F6F6 }
.saveDiv{ display:none }
</style>
<script type="text/javascript">
//$('table.editors textarea')
</script>

<script type="text/javascript">
function saveTrans(id){
	var params = {saveRowId:id,transed:$('#row_'+id).find('textarea.editable').val()};
	$.post("index.php?p=texts&lang=<?php echo $_GET['lang']; ?>", params, function(data){
		if(data=='Ok'){
			document.location.href = "<?php echo full_url(); ?>";
		}
	});
	return false;
}

	function close_editor(obj){
		$(obj).parent().parent().parent().find('textarea').animate({'height':'22px'},200);
		$(obj).parent().hide();
	}
	
	var $lastEditRow = "row_0";

$('table.editors textarea.editable').click(function(){
	$('.saveDiv').hide();
	var $thisEditRow = $(this).parent().parent().attr('Id');
	var $textareas = $(this).parent().parent().find('textarea');
	$('#' + $lastEditRow+'[id!="' + $thisEditRow + '"]').find('textarea').animate({'height':'22px'},100);
	$('#' + $lastEditRow+'[id!="' + $thisEditRow + '"]').find('.textarea').attr('readonly',true);
	$(this).parent().parent().find('.saveDiv').show();
	if(parseInt($($textareas).css('height'))<50){
		$($textareas).animate({'height':'100px'},200);
		$("#" + $thisEditRow).find('.textarea').attr('readonly',false).focus();
		$lastEditRow = $(this).parent().parent().attr('Id');
	}
})

</script>

