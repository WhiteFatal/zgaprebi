<?php
if (!defined('FILE_SECURITY_KEY'))  die();
	
$structure = modules::getStructure($_GET['mod']);
include('../theme/functions.php');

if(isset($_GET['delId'])){
	ob_clean();
	sql::delete("DELETE FROM `_cn_mod_".$_GET['mod']."` WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id` = '".$_GET['delId']."'");
	redirect("?p=modules&mod=" .$_GET['mod']."&msg=delOk");
	die();
}

if(isset($_GET['delIds'])){
	ob_clean();
	sql::delete("DELETE FROM `_cn_mod_".$_GET['mod']."` WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id` IN (".$_GET['delIds'].")");
	redirect("?p=modules&mod=" .$_GET['mod']."&msg=delsOk");
	die();
}

if(isset($_GET['delfile'])){
	ob_clean();
	@unlink('../storage/uploads/'.$_GET['mod'].'/'.$_POST['file']);
	$files = sql::getElem("SELECT `".$_POST['field']."` FROM `_cn_mod_".$_GET['mod']."`
															WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id`=".$_GET['editId']);
	$files = explode(',',$files);
	foreach($files as $k => $v) { if($v==$_POST['file']) unset($files[$k]); }
	sql::update("UPDATE `_cn_mod_".$_GET['mod']."` SET `".$_POST['field']."`='".implode(',',$files)."'
															WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id`=".$_GET['editId']);
	die('ok');
}

if(isset($_POST['setRowactiveStatus'])){
	ob_clean();
	$sql = "UPDATE `_cn_mod_".$_GET['mod']."` SET `active`='".$_POST['activestatus']."', `modify_date` = now()
														WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id` = '".$_POST['Id']."'";
	sql::update($sql);
	die();
}

if(isset($_GET['clearfilter'])){
	ob_clean();
	@setcookie('mod_'.$_GET['mod'].'[filter]','',time()-3600*24*365);
	redirect("?p=modules&mod=".$_GET['mod']);
}

if((strlen((@$_COOKIE['mod_'.$_GET['mod']]['filter']))>0) && !isset($_GET['filter'])){
	$filter = urldecode($_COOKIE['mod_'.$_GET['mod']]['filter']);
	$msg = '';
	if(strlen(trim(@$_GET['msg']))>0) $msg = '&msg='.@$_GET['msg'];
	redirect("?p=modules&mod=".$_GET['mod']."&filter=".$filter.$msg);
}

if(modules::need_upload_folder($_GET['mod'])){
	$mod_up_dir = '../storage/uploads/'.$_GET['mod'];
	if(!is_dir($mod_up_dir)) {
		echo '<div class="nNote nFailure"><p><b>'.__('შეცდომა.').'!</b> <b>storage/uploads</b>-'.__('ში საქაღალდე').' <b>'.$_GET['mod'].'</b> '.__('ვერ ვიპოვე').'.</p></div>';
	} else {
		$perms = substr(sprintf('%o', fileperms($mod_up_dir)), -4);
		if($perms!='0777')
				echo '<div class="nNote nFailure"><p><b>'.__('შეცდომა').'!</b> <b>storage/uploads</b>-'.__('ში საქაღალდე').' <b>'.$_GET['mod'].'</b>-'.__('ს ააქვს უფლება').' <b>'.$perms.'</b>. '.__('სჭირდება').' <b>0777</b></p></div>';
	}
}

if(@$_GET['msg']=='modulecreatedok') echo '<div class="nNote nSuccess"><p><b>'.__('გმადლობთ').'!</b> '.__('მოდული შეიქმნა წარმატებით').'</p></div>';
if(@$_GET['msg']=='delOk') echo '<div class="nNote nSuccess"><p><b>'.__('გმადლობთ').'!</b> '.__('ჩანაწერი წაიშალა წარმატებით').'</p></div>';
if(@$_GET['msg']=='delsOk') echo '<div class="nNote nSuccess"><p><b></b> '.__('მონიშნული ჩანაწერები წაიშალა წარმატებით').'</p></div>';
if(@$_GET['msg']=='savesuccess') echo '<div class="nNote nSuccess"><p>'.__('ჩანაწერი წარმატებით შეინახა').'</p></div>';


	$editor_fields_show_on_page = modules::editor_fields_show_on_page($_GET['mod']);
	$fields = array_flip($editor_fields_show_on_page);
	$fields = '`'.implode('`,`',$fields).'`, `Id`, `cat_Id`,`active`,`modify_date`,`row_owner`';
	$ordsql = 'ORDER BY `Id` DESC';
	if(isset($_COOKIE['mod_'.$_GET['mod']]['ordby']) && isset($_COOKIE['mod_'.$_GET['mod']]['ordas'])) {
		$ordsql = 'ORDER BY `'.$_COOKIE['mod_'.$_GET['mod']]['ordby'].'`+0 '.$_COOKIE['mod_'.$_GET['mod']]['ordas'];
	}

		if(isset($_POST['filter'])){
			ob_clean();
			$url = array();
			foreach($_POST['_F'] as $k => $v) $url[] = $k.'/'.urlencode(trim($v));
			$filter = implode('/',$url);
			@setcookie('mod_'.$_GET['mod'].'[filter]',$filter,time()+3600*24*365);
			redirect("?p=modules&mod=".$_GET['mod']."&filter=".$filter);
		}
		if(isset($_GET['filter'])){
			$tmp = explode('/',$_GET['filter']);
			$i=0;
			foreach($tmp as $k => $v){
				if(@$tmp[$i+1]!=''){
					$_POST['_F'][$tmp[$i]] = urldecode($tmp[$i+1]);
				}
				$i = $i + 2;
			}
		}
			if(isset($_POST['_F'])) foreach($_POST['_F'] as $k => $v) $addsql[] = "`".$k."` LIKE '".$v."%'";
			if(count((array)@$addsql)>0) $addsql = ' AND '.implode(' AND ',(array)$addsql);
	
	$langsql = "";
	if($WAL=='1') $langsql = " AND `lang`='".$_ADM_CONTLNG."'";
	$sql = "SELECT ".$fields." FROM `_cn_mod_".$_GET['mod']."` WHERE active>=0 ".$langsql." ".@$addsql." ".$ordsql;
	
	$dataCNT = sql::cnt($sql);
	$data    = sql::getRows($sql." LIMIT ".(($_GET['pg']-1)*10).",10");
	$textlists = array();
	foreach($structure as $f => $v) {
		if($v['field_type']=='checkbox') {
			$textlists[$f] = sql::getCol("SELECT `field_value` FROM `cn_modules_textlists`
															WHERE `module_sys_name`='".$_GET['mod']."' AND
													  `field_name`='".$f."' AND `lang`='".$_GET['l']."' ORDER BY `field_value`+0");
		}
	}

?>
	<div class="widget">
	<div class="whead"><h6>&nbsp;</h6>
	<ul class="titleToolbar">
	    <li style="border-left:none"><span style="padding: 7px 12px 8px 12px;display: block; font-style:italic; color:#999; font-weight:normal"><?php _e('სულ:'); ?> <?php echo $dataCNT; ?> <?php _e('ჩანაწერი'); ?></span></li>
        <li class="maindelicon" style="display:none"><a title="<?php _e('ყველა მონიშნული ჩანაწერის წაშლა'); ?>" href="#" class="tipS" style="color:red"><?php _e('წაშლა'); ?></a></li>
        <?php if($_GET['mod']!='users'){ ?>
		<li><a title="<?php _e('ახალი ჩანაწერის დამატება'); ?>" href="?p=modules&mod=<?php echo $_GET['mod']; ?>&add" class="tipS"><?php _e('დამატება'); ?></a></li>
        <?php } ?>
		<li><a onclick="$('#dyn .tablePars').slideToggle()" title="Options"><img src="img/icons/options" alt="" style="margin-top:6px" /></a></li>
	</ul>
	<div class="clear"></div></div>
	<div id="dyn" class="hiddenpars">
	<div class="tablePars" style="display:<?php echo (isset($_GET['filter'])?'block':'none'); ?>">
	<form action="" method="POST">
		<?php
			$filters = sql::getRows("SELECT distinct field_name FROM `cn_modules_textlists`
															WHERE `module_sys_name`='".$_GET['mod']."'");
			$editor_fields_show_in_filter = modules::editor_fields_show_in_filter($_GET['mod']);
			foreach($structure as $m => $v){
				if(array_key_exists($m,$editor_fields_show_in_filter)){
					if ($v['field_type']=='combobox' || $v['field_type']=='checkbox'){
						echo '<div style="float:left; margin-left:10px">'.$v['field_name'].'<br>';
						echo '<select name="_F['.$m.']" class="select">';
						echo '<option value="">ყველა</option>';
						   $r = sql::getRows("SELECT `cat_id`,`field_value` FROM `cn_modules_textlists`
													WHERE  `lang`='".$_ADM_CONTLNG."' AND
														`module_sys_name`='".$_GET['mod']."' AND `field_name`='".$m."' ORDER BY `field_value`+0");
							foreach($r as $vv) echo '<option '.(($vv['cat_id']==@$_POST['_F'][$m])?'selected="selected"':'').' value="'.$vv['cat_id'].'">'.$vv['field_value'].'</option>';
						echo '</select></div>';
					}
					if ($v['field_type']=='submodule:combobox'){
						echo '<div style="float:left; margin-left:10px">'.$v['field_name'].'<br>';
						echo '<select name="_F['.$m.']" class="select">';
						echo '<option value="">'.__('ყველა').'</option>';
						   $r = sql::getRows("SELECT `cat_id`,`".$v['params']."` as `field_value` FROM `_cn_mod_".$v['field_type_params']."`
													WHERE  `lang`='".$_ADM_CONTLNG."' ORDER BY `".$v['params']."`+0");
							foreach($r as $vv) echo '<option '.(($vv['cat_id']==@$_POST['_F'][$m])?'selected="selected"':'').' value="'.$vv['cat_id'].'">'.$vv['field_value'].'</option>';
						echo '</select></div>';
					}
					if ($v['field_type']=='string'){
						echo '<div class="formRow" style="float:left; margin-left:10px; border-bottom:none; padding:0 4px">'.$v['field_name'].'<br>';
						echo '<input name="_F['.$m.']" type="text" value="'.@$_POST['_F'][$m].'">';
						echo '</div>';
					}
					if ($v['field_type']=='date'){
						echo '<div class="formRow" style="float:left; margin-left:10px; border-bottom:none; padding:0 4px">'.$v['field_name'].'<br>';
						echo '<input name="_F['.$m.']" class="date" type="text" value="'.@$_POST['_F'][$m].'">';
						echo '</div>';
					}
				}
			}
					echo '<div style="float:left; margin-left:10px">'.__('ავტორი').'<br>';
						echo '<select name="_F[row_owner]" class="select">';
						echo '<option value="">'.__('ყველა').'</option>';
							$rowOwnerIds = sql::getCol("SELECT DISTINCT `row_owner` FROM `_cn_mod_".$_GET['mod']."`");
							$rowOwnerNames = array();
							foreach(sql::getRows("SELECT Id, `flname` FROM `cn_adminusers`") as $v){
								$rowOwnerNames[$v['Id']] = $v['flname'];
							}
							foreach($rowOwnerIds as $v) {
								echo '<option '.(($v==@$_POST['_F']['row_owner'])?'selected="selected"':'').' value="'.$v.'">'.$rowOwnerNames[$v].'</option>';
							}
					echo '</select></div>';

				if(count($editor_fields_show_in_filter)>0)
					echo '<div style="float:left; margin-left:10px">&nbsp;<br>
							<input class="buttonM bDefault" value="'.__('ფილტრი').'" type="submit" name="filter" >
							<a href="?p=modules&mod='.$_GET['mod'].'&clearfilter" style="margin-left:20px">'.__('ფილტრის გაწმენდა').'</a>
						 </div>';
				if(count($editor_fields_show_in_filter)==0)
					echo '<div style="float:left; margin-left:10px; font-style:italic; color:#aaa">'.__('ფილტრები არ არის').'</div>';
		?>
	  </form>
	</div>
		<table cellpadding="0" cellspacing="0" width="100%" class="tLight noBorderT">
   <?php 
	echo '<thead><tr>';
	echo '<td style="width:12px"><input type="checkbox" class="selectall" /></td>';
	foreach($editor_fields_show_on_page as $f => $ev){
		$icn = '';
		if($f==@$_COOKIE['mod_'.$_GET['mod']]['ordby']) $icn = '<img src="img/elements/other/tableArrows.png">';
		echo '<td style="text-align:left"><a href="#" orderby="'.$f.'">'.$ev.'&nbsp;'.$icn.'</a></th>';
	}
	if($_GET['mod']=='users'){
		echo '<td style="text-align:left">ამჯამად ოქრო</th>';
		echo '<td style="text-align:left">სულ ოქრო</th>';
	}
	if($_GET['mod']=='tales'){
		echo '<td style="text-align:left">ყიდვები</th>';
	}
	echo '<td style="width:80px"><a href="#" orderby="active">'.__('აქტიური').'</a></td><td style="width:80px">'.__('ქმედება').'</td></tr></thead>';
	foreach($data as $v){
		echo '<tr>';
		echo '<td><input class="rowselector" type="checkbox" value="'.$v['cat_Id'].'" /></td>';
		foreach($v as $kk => $vv){
			if(array_key_exists($kk,$editor_fields_show_on_page)) {
				if(array_key_exists($kk,$textlists)){
					$vv = explode(',',$vv);
					$tmp = array();
					foreach($vv as $vvv) $tmp[] = $textlists[$kk][$vvv];
					$vv = implode(', ',$tmp);
				}
				if($structure[$kk]['field_type']=='submodule:checkbox'){
					$tmp = sql::getCol("SELECT `".$structure[$kk]['params']."` as field FROM `_cn_mod_".$structure[$kk]['field_type_params']."`
																			WHERE `cat_Id` in (".$v[$kk].") AND `lang`='".$_ADM_CONTLNG."'");
					$vv = implode('<br>',$tmp);
				}
				if(in_array($structure[$kk]['field_type'],array('checkbox','combobox'))){
					$tmp = array();
					if(trim(@$v[$kk])!=''){
					$tmp = sql::getCol("SELECT `field_value` as field FROM `cn_modules_textlists`
															WHERE `module_sys_name` = '".$_GET['mod']."'
															AND `cat_Id` 	 in (".$v[$kk].")
															AND `field_name` = '".$kk."'
																AND `lang`='".$_ADM_CONTLNG."'
														ORDER BY `field_value`+0");
					}
					$vv = implode('<br>',$tmp);
				}
				echo '<td>'.$vv.'</td>';
			}
			//cash_to_gold(check_balance(1000,'enji777@gmail.com'));
		}
		//$active = ($v['active']=='1') ? 'checked="checked"' : "";
		if($v['active']=='1'){
			$active = 'checked="checked"';
		}else if($v['active']=='2'){
			$active = 'disabled="disabled"';
		}else{
			$active = '';
		}
		
		if($_GET['mod']=='users'){
			$gold = cash_to_gold(check_balance($v['Id'],$v['email']));
			$all_cash = users_purchashes($v['Id']) + $gold;
			echo '<td>'.$gold.'</td>';
			echo '<td>'.$all_cash.'</td>';
		}
		if($_GET['mod']=='tales'){
			echo '<td>'.purchased_tales_cnt($v['cat_Id']).'</td>';
		}
		echo '<td>
					<div class="grid9 on_off">
						<div class="floatL mr10"><input type="checkbox" onchange="saveactiveRow(this,\''.$v['cat_Id'].'\')" class="activeRow" value="" '.$active.' name="chbox[]" /></div>
					</div>
			</td>';

		echo '<td class="tableActs">';
				echo '<a href="?p=modules&mod='.$_GET['mod'].'&editId='.$v['cat_Id'].'" class="tablectrl_small bDefault tipS" title="'.__('რედაქტირება').'"><span class="iconb" data-icon="&#xe1db;"></span></a>';
				echo '<a href="?p=modules&mod='.$_GET['mod'].'&delId='.$v['cat_Id'].'" onclick="return  confirm(\''.__('დარწმუნებული ხართ გსურთ ამ ჩანაწერის წაშლა').'?\')" class="tablectrl_small bDefault tipS" title="'.__('წაშლა').'"><span class="iconb" data-icon="&#xe136;"></span></a>';
			echo '</td>';
		echo '</tr>';
	}
?>
			</tbody>
		</table>
		</div>
		</div>
	</div>
<?php
	echo pagingGETvariables($dataCNT,10);
?>
<script type="text/javascript">
function openeditor(){
	$.fancybox.open({'type':'ajax', 'autoWidth':true, 'href':'?p=addmodules'});
}
function saveactiveRow(o,Id){
	var active = ($(o).is(":checked")) ? 1 : 0;
	$.post("<?php echo full_url(); ?>",{setRowactiveStatus:'1',activestatus:active,Id:Id},function(data){})
}
$('.selectall').click(function(){
	$('.rowselector').attr('checked',$(this).is(':checked'));
	enable_disable_delicon();
})
$('.rowselector').click(function(){
	$('.selectall').attr('checked',$('.rowselector').length==$('.rowselector:checked').length);
	enable_disable_delicon();
})
$('.rowselector').change(function(){ enable_disable_delicon(); })
function enable_disable_delicon(){
	if($('.rowselector:checked').length>0) { $('.maindelicon').show(); } else { $('.maindelicon').hide(); }
}
$('.maindelicon a').click(function(){
	if(confirm("<?php _e('დარწმუნებული ხართ გსურთ ყველა ჩანაწერის წაშლა?\nგაითვალისწინეთ რომ, დასტურის შემთხვევაში ვეღარ შეძლებთ წაშლილი ჩანაწერების აღდგენას'); ?>!")===true){
	ids = [];
	$('.rowselector:checked').each(function(index, element) {
       ids.push($(this).val()); 
    });
		document.location.href = '?p=modules&mod=<?php echo $_GET['mod']; ?>&delIds='+ids.join();
	}
})

$('table thead tr td a').click(function(){
	var ordas = ($.cookie('mod_<?php echo $_GET['mod'];?>[ordas]')=='DESC') ? 'ASC' : 'DESC';
	$.cookie('mod_<?php echo $_GET['mod'];?>[ordby]',$(this).attr('orderby'),{expires:10000});
	$.cookie('mod_<?php echo $_GET['mod'];?>[ordas]',ordas,{expires:10000});
	var url = document.location.href; url.toString();
	document.location.href = url;
})
</script>
