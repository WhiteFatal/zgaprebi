<?php
if (!defined('FILE_SECURITY_KEY'))  die();

if(isset($_POST['photoCropSubmit'])){
	ob_clean();
	image::crop($_POST['photoCropSubmit'], $_POST['x'], $_POST['y'], $_POST['w'], $_POST['h']);
	die('ok');
}

if($_GET['mod']=='users'){
	$is_premium = sql::CNT("SELECT * FROM `_cn_mod_users` WHERE `active` = 1 AND `cat_Id` = ".$_GET['editId']." AND NOW() < `premium_date` + INTERVAL `premium_days` DAY");	
}

if(isset($_GET['add'])){
	ob_clean();
	$new_cat_id = get::newcatId("_cn_mod_".$_GET['mod']);
	$get_langs = get::langs();
	if($WAL=='0') $get_langs = array('ka'=>'ka'); 
	foreach($get_langs as $l => $v){
		echo $sql = "INSERT INTO `_cn_mod_".$_GET['mod']."`
					SET `lang` = '".$l."' ,
						`cat_Id` = '".$new_cat_id."' ,
						`active` = '-1' ,
						`row_owner`='".$_SESSION['userId']."',
						`modify_date` = now()";
		sql::insert($sql);
	}
	header("Location: ?p=modules&mod=".$_GET['mod']."&editId=".$new_cat_id.'&from_1'.$filter);
	die();
}


if(isset($_GET['photoeditor'])){
	ob_clean();
	$_GET['photo'] = urldecode($_GET['photo']);
	$pinfo = getimagesize($_GET['photo']);
	echo '<div id="editor-image">';
	echo '<div style="width:'.($pinfo[0]).'px"></div>';
	echo '<img id="image4edit" src="'.$_GET['photo'].'?'.date('Ymdhis').'" />';
	echo '</div>';
	echo '<div id="editor-buttons">
			<input type="hidden" id="x" name="x" /><input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" /><input type="hidden" id="h" name="h" />
			<input type="button" value="მოჭრა" class="buttonS bLightBlue submit" name="submit">
		  </div>';
	echo '<script type="text/javascript">
			function updateCoords(c){
				$("#x").val(c.x); $("#y").val(c.y);
				$("#w").val(c.w); $("#h").val(c.h);
			};

			function checkCoords(){
				if (parseInt($("#w").val())) return true;
				alert("'.__('გთხოვთ მონიშნეთ სურათზე სასურველი ამოსაჭრელი ზონა').'.");
				return false;
			};
			$("#image4edit").Jcrop({
				onChange: updateCoords,
				onSelect: updateCoords,';
		if($_GET['thumb_h']>0 && $_GET['thumb_w']>0){
		echo	'aspectRatio: '.($_GET['thumb_w']/$_GET['thumb_h']).',
				minSize: ['.$_GET['thumb_w'].', '.$_GET['thumb_h'].'],
				maxSize: ['.$_GET['thumb_w'].', '.$_GET['thumb_h'].'],
				setSelect: [0,0,'.$_GET['thumb_w'].','.$_GET['thumb_h'].'],';
		}
		echo	'addClass: "jcrop-light",
				bgOpacity: 0.5,
      			bgColor: "white"
			});
			$("#editor-buttons .submit").click(function(){
				$.post("'.full_url().'", 
					{photoCropSubmit:"'.$_GET['photo'].'","x":$("#x").val(),"y":$("#y").val(),"w":$("#w").val(),"h":$("#h").val()},
					function(data){
						if(data=="ok"){
							reloader("'.$_GET['field'].'");
							$.fancybox.close();
						} else {
							$("#editor-buttons #status").html(data);
						}
					}
				 )
			 });
		  </script>';
	die();
}

if(isset($_POST['upl_field'])){
    ob_clean();
    if (!empty($_FILES)) {
        // Fix for PHP 8.1: end() requires a variable
        $file_name = $_FILES['Filedata']['name'];
        $temp_ext = explode('.', $file_name);
        $ext = strtolower(end($temp_ext));

        $targetFolder = '/storage/uploads/'.$_GET['mod'];
        $tempFile = $_FILES['Filedata']['tmp_name'];
        
        // Use rtrim to prevent double slashes
        $targetPath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $targetFolder;
        
        // Ensure directory exists
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }

        $newfilename = date('ymdhis').getUniqueCode(12).'.'.$ext;
        $targetFile  = $targetPath . '/' . $newfilename;
        $fileParts = pathinfo($file_name);	
		if (1==1) {
			move_uploaded_file($tempFile,$targetFile);
			if($_POST['multi']=='no'){
				if(@$_POST['uplfor']!='gallery'){
					sql::update("UPDATE `_cn_mod_".$_GET['mod']."` SET `".$_POST['upl_field']."`='".$newfilename."'
									WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id`=".$_GET['editId']);
				}
			} else {
				if(@$_POST['uplfor']!='gallery'){
					$tmp = sql::getElem("SELECT `".$_POST['upl_field']."` FROM `_cn_mod_".$_GET['mod']."`
																		WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id`=".$_GET['editId']);
					$tmp = explode(',',$tmp);
					array_push($tmp,$newfilename);
					foreach($tmp as $k => $v) if(trim($v)=='') unset($tmp[$k]);
					sql::update("UPDATE `_cn_mod_".$_GET['mod']."` SET `".$_POST['upl_field']."`='".implode(',',$tmp)."'
										WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id`=".$_GET['editId']);
				}
			}
			if(@$structure[$_POST['upl_field']]['params']['filetypes']=="photos"){
				if(is_numeric($structure[$_POST['upl_field']]['params']['width']) &&
				   is_numeric($structure[$_POST['upl_field']]['params']['height']))
				  {
						image::resize($newfilename,$newfilename, '..'.$targetFolder.'/',
										$structure[$_POST['upl_field']]['params']['width'],
										$structure[$_POST['upl_field']]['params']['height']);
				   }
				if(@$_POST['uplfor']=='gallery'){
					$newcatId = get::newcatId('cn_modules_photos');
					foreach(get::langs() as $l => $v){
						sql::insert("INSERT INTO `cn_modules_photos` SET
														`module_sys_name`='".$_GET['mod']."',
														`field_name`='".$_POST['upl_field']."',
														`cat_id` = '".$newcatId."',
														`file`='".$newfilename."',
														`lang`='".$l."'");
					}
					$oldfld = sql::getElem("SELECT `".$_POST['upl_field']."` FROM `_cn_mod_".$_GET['mod']."`
														WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id`=".$_GET['editId']);
					$oldfld = explode(',',$oldfld); $oldfld[] = $newcatId;
					foreach($oldfld as $ok => $ov) if(trim($ov)=='') unset($oldfld[$ok]);
					sql::update("UPDATE `_cn_mod_".$_GET['mod']."` SET
										`".$_POST['upl_field']."` = '".implode(',',$oldfld)."'
										WHERE `lang`='".$_ADM_CONTLNG."' AND `cat_Id`=".$_GET['editId']);
				if(is_numeric($structure[$_POST['upl_field']]['params']['twidth']) &&
				   is_numeric($structure[$_POST['upl_field']]['params']['theight']))
				  {
					image::resize($newfilename,'thumb_'.$newfilename, '..'.$targetFolder.'/',
									$structure[$_POST['upl_field']]['params']['twidth'],
									$structure[$_POST['upl_field']]['params']['theight']);
				  } else {
					copy('..'.$targetFolder.'/'.$newfilename,'..'.$targetFolder.'/thumb_'.$newfilename);  
				  }
				}
			}
			echo 'ok';
		} else {
			echo 'Invalid file type.';
		}
	}
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

if(isset($_POST['edit_button_submit'])){
	$ins = array();
	foreach($_POST as $k => $v){
		if(substr($k,0,3)=='_E_'){
			$v = sql::safe(trim($v));
			$k = sql::safe(substr($k,3));
			$ins[] = "`".$k."` = '".$v."'";
		}
		if(substr($k,0,6)=='_Earr_'){
			foreach($v as $kk => $vv) $v[$kk] = sql::safe(trim($vv));
			$k = sql::safe(substr($k,6));
			$ins[] = "`".$k."` = '".implode(',',$v)."'";
		}
	}
	$checkboxes = sql::getCol("SELECT `field_sys_name` FROM `cn_modules_structures`
													WHERE `module_sys_name`='".$_GET['mod']."' AND `field_type` LIKE '%checkbox%'");
	foreach($checkboxes as $v){
		if(!isset($_POST['_Earr_'.$v])) $ins[] = "`".$v."` = NULL";
	}
	$langloops = isset($_GET['from_1']) ? get::langs() : array($_ADM_CONTLNG => $_ADM_CONTLNG);
	
	if($WAL=='0') $langloops = array('ka'=>'ka');

	foreach($langloops as $l => $v){
		$slang = (($l!='')?"`lang`='".$l."' AND":"");
		$active = sql::getElem("SELECT `active` FROM `_cn_mod_".$_GET['mod']."` WHERE ".$slang." `cat_Id`=".$_GET['editId']);
		$active = ($active>=0) ? $active : '1';
		foreach($checkboxes as $v) 
		$ins[] = '`modify_date`=now()';
		$ins[] = "`row_owner`='".$_SESSION['userId']."'";
		$ins[] = "`active`='".$active."'";
		sql::insert("UPDATE `_cn_mod_".$_GET['mod']."` SET ".implode(', ',$ins)."
											WHERE `lang`='".$l."' AND `cat_Id`=".$_GET['editId']);
	}
	redirect('?p=modules&mod='.$_GET['mod'].'&msg=savesuccess');
}

$str = modules::getStructure($_GET['mod']);

if(@$_GET['msg']=='saveok'){
	echo '<div class="nNote nSuccess"><p><b>'.__('გმადლობთ').'!</b> '.__('ჩანაწერი შენახულია').'</p></div>';
}
?>
<script type="text/javascript">
function init_drag(t){
	$(".gallery").dragsort({ dragSelector: "li.draga",
							 dragBetween: true,
							 dragEnd: saveShow,
							 placeHolderTemplate: "<li class='placeHolder'><div></div></li>" });
}
function saveShow() {
	var ord = $("#albumphotos>li").map(function() { return $(this).attr('photo_cat_id'); }).get().join();
	console.log(ord);
	$.post("<?php echo full_url(); ?>",{photoOrds:ord},function(data){
		if(data=='ok') $('#saveButton').hide();
	});
};

</script>
    <form method="post" action="" class="main">
     <fieldset>
    <div class="widget grid10" style="margin-left:20px">
    <div class="whead"><h6><?php if(isset($_GET['from_1'])) { _e('ჩანაწერის დამატება'); } else { _e('ჩანაწერის რედაქტირება'); } ?></h6><div class="clear"></div></div>
  <?php
  $editor_fields_show_in_editor = modules::editor_fields_show_in_editor($_GET['mod']);
  $DATA = @sql::getRow("SELECT * FROM `_cn_mod_".$_GET['mod']."` WHERE ".(($WAL==1)?"`lang`='".$_ADM_CONTLNG."' AND":"")."
  																										cat_id=".$_GET['editId']);
  
  $wysiwygs = array();
  
  foreach($str as $t => $s){
	  if(!array_key_exists($t,$editor_fields_show_in_editor)) continue;
			echo '<div class="formRow">';
				   #########################################################################
				   if($s['field_type']=='string') {
							if($_GET['mod']=='users' && $t=='premium_days'){
								if($is_premium==1){
									echo '<div class="grid3"><label>'.$s['field_name'].' :(<b>ჩართულია</b>)</label></div>';
									echo '<div class="grid9">';
									echo '<input type="text" name="_E_'.$t.'" value="'.@$DATA[$t].'" class="'.$s['field_type'].'" />';
									echo '</div><div class="clear"></div>';
								}else{
									echo '<div class="grid3"><label>'.$s['field_name'].' :</label></div>';
									echo '<div class="grid9">';
									echo '<input type="text" name="_E_'.$t.'" value="" class="'.$s['field_type'].'" />';
									echo '</div><div class="clear"></div>';
								}
							}else{
								echo '<div class="grid3"><label>'.$s['field_name'].' :</label></div>';
								echo '<div class="grid9">';
								echo '<input type="text" name="_E_'.$t.'" value="'.@$DATA[$t].'" class="'.$s['field_type'].'" />';
								echo '</div><div class="clear"></div>';
							}
				   }
				   #########################################################################
				   if($s['field_type']=='textarea') {
						echo '<div class="grid3"><label>'.$s['field_name'].' :</label></div>';
						echo '<div class="grid9">';
						echo '<textarea name="_E_'.$t.'" class="'.$s['field_type'].'">'.@$DATA[$t].'</textarea>';
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
				   if($s['field_type']=='wysiwyg') {
						echo '<div class="grid3"><label>'.$s['field_name'].' :</label></div>';
						echo '<div class="grid9">';
						echo '<textarea name="_E_'.$t.'" id="wysiwyg_'.$t.'" class="'.$s['field_type'].'">'.@$DATA[$t].'</textarea>';
						echo '</div><div class="clear"></div>';
						$wysiwygs[] = 'wysiwyg_'.$t;
				   }
				   #########################################################################
				   if($s['field_type']=='date') {
					   $DATA[$t] = (trim($DATA[$t])=='' || $DATA[$t]=='0000-00-00') ? (date('Y-m-d')) : $DATA[$t];
						echo '<div class="grid3"><label>'.$s['field_name'].' :</label></div>';
						echo '<div class="grid9">';
						echo '<input type="text" name="_E_'.$t.'" value="'.@$DATA[$t].'" class="'.$s['field_type'].'" style="width:100px" />';
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
				   if($s['field_type']=='datetime') {
					   	if($_GET['mod']=='users'){
							if($is_premium==1){
								$DATA[$t] = (trim($DATA[$t])=='' || $DATA[$t]=='0000-00-00 00:00:00') ? (date('Y-m-d H:i:s')) : $DATA[$t];
								echo '<div class="grid3"><label>'.$s['field_name'].' :(<b>ჩართულია</b>)</label></div>';
								echo '<div class="grid9">';
								echo '<input type="text" name="_E_'.$t.'" value="'.@$DATA[$t].'" class="'.$s['field_type'].'" style="width:150px" />';
								echo '</div><div class="clear"></div>';
							}else{
								$DATA[$t] = date('Y-m-d H:i:s');
								echo '<div class="grid3"><label>'.$s['field_name'].' :</label></div>';
								echo '<div class="grid9">';
								echo '<input type="text" name="_E_'.$t.'" value="'.@$DATA[$t].'" class="'.$s['field_type'].'" style="width:150px" />';
								echo '</div><div class="clear"></div>';
							}
						}else{
							$DATA[$t] = (trim($DATA[$t])=='' || $DATA[$t]=='0000-00-00 00:00:00') ? (date('Y-m-d H:i:s')) : $DATA[$t];
							echo '<div class="grid3"><label>'.$s['field_name'].' :</label></div>';
							echo '<div class="grid9">';
							echo '<input type="text" name="_E_'.$t.'" value="'.@$DATA[$t].'" class="'.$s['field_type'].'" style="width:150px" />';
							echo '</div><div class="clear"></div>';
						}
						
				   }
				   #########################################################################
				   if($s['field_type']=='file') {
					   $params = (!is_array(@$s['params'])) ? '' : ('('.$s['params']['width'].'x'.$s['params']['height'].')');
						echo '<div class="grid3"><label>'.$s['field_name'].' : '.$params.'</label></div>';
						echo '<div class="grid9" id="container_'.$t.'">';
						if(@$_GET['reload']==$t) ob_clean();
						if($DATA[$t]==''){
							if(is_numeric($_GET['editId'])) {
								echo '<div id="queue_"'.$t.'"></div>
									  <input contId="'.$t.'" id="file_upload_'.$t.'" bg="upload-bg" type="file" for="files" multi="false" name="_E_'.$t.'" />';
								if(@$_GET['reload']==$t)  echo '<script type="text/javascript"> init_uploadify(); </script>';
							} else {
								echo '<div style="font-size:10px; font-style:italic">'.__('ფაილის ატვირთვისთვის საჭიროა ჯერ გააკეთოთ შენახვა').'</div>';				
							}
						} else {
							echo '<span class="tableActs" align="center">';
							echo '<a href="#" onclick="removeFile(\''.$t.'\',\''.$DATA[$t].'\')" class="tablectrl_small bDefault"><span class="iconb" data-icon="&#xe136;"></span></a>';
							$alert = '';
							if(in_array(strtolower(end(@explode(".",$DATA[$t]))),array('jpg','gif','png'))){
								if(@$s['params']['width']>0 && @$s['params']['height']>0){
									$notneedcrop = image::isequalsize('../storage/uploads/'.$_GET['mod'].'/'.$DATA[$t],$s['params']['width'],$s['params']['height']);				$alert = '<img src="img/alert.png">&nbsp;';
								} else {
									$notneedcrop = false;
									$alert = '';
								}
								if($notneedcrop==false){								
								echo '<a href="#" onclick="opencropper(\'modulephoto\',\'../storage/uploads/'.$_GET['mod'].'/'.$DATA[$t].'\',\''.$t.'\',\''.@$s['params']['width'].'\', \''.@$s['params']['height'].'\')" class="tablectrl_small bDefault">'.$alert.'<span class="iconb" data-icon="&#xe257;"></span></a>';
								}
							}
							echo '</span>'.nbsp(7);
							echo '<a href="/storage/uploads/'.$_GET['mod'].'/'.$DATA[$t].'" target="_blank">';
							echo '&nbsp;<img src="img/exts/16x16/'.strtolower(end(@explode('.',$DATA[$t]))).'.png">'.$DATA[$t].'</a>';
							echo '</a>';
						}
						if(@$_GET['reload']==$t) die();
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
				   if($s['field_type']=='files') {
						 $params = (!is_array(@$s['params'])) ? '' : ('('.$s['params']['width'].'x'.$s['params']['height'].')');
						echo '<div class="grid3"><label>'.$s['field_name'].' : '.$params.'</label></div>';
						if(@$_GET['reload']==$t) ob_clean();
						echo '<div class="grid9" id="container_'.$t.'">';
						echo '<div id="queue_"'.$t.'"></div>
							  <input contId="'.$t.'" id="file_upload_'.$t.'" bg="uploads-bg" type="file" multi="true" for="files" name="_E_'.$t.'" />';
							if(@$_GET['reload']==$t)  echo '<script type="text/javascript"> init_uploadify(); </script>';
						if($DATA[$t]!=''){
							echo '<div class="uplfilesicon">';
							foreach(explode(',',$DATA[$t]) as $file){
								$fullfile = '/storage/uploads/'.$_GET['mod'].'/'.$file;
								if(in_array(strtolower(end(explode('.',$file))),array('jpg','png'))){
									$icon = '/storage/uploads/'.$_GET['mod'].'/'.$file;
								} else {
									$icon = 'img/exts/64x64/'.end(explode('.',$file)).'.png';
								}
								if(image::check('../'.$fullfile)){
									$notneedcrop = image::isequalsize('../'.$fullfile,$s['params']['width'],$s['params']['height']);
								} else {
									$notneedcrop = true;
								}
								echo '<div style="float:left; margin:5px"><img src="'.$icon.'" width="60">';
								echo '<div class="btn-group dropup" style="display:inline-block; position:absolute; margin:5px -55px">';
								echo '<a class="buttonS bDefault" data-toggle="dropdown" href="#"><span class="caret"></span></a>
                        			  <ul class="dropdown-menu">
                           <li><a href="/storage/uploads/'.$_GET['mod'].'/'.$file.'" target="_blank" class="tablectrl_small bDefault">ნახვა</a></li>';
						   if($notneedcrop==false){
                           		echo '<li><a href="#" onclick="opencropper(\'modulephoto\',\'../storage/uploads/'.$_GET['mod'].'/'.$file.'\',\''.$t.'\',\''.@$s['params']['width'].'\', \''.@$s['params']['height'].'\')" class="tablectrl_small bDefault">'.__('მოჭრა').'</a></li>';
						   }
							echo '<li><a href="#" onclick="removeFile(\''.$t.'\',\''.$file.'\')" class="tablectrl_small bDefault">'.__('წაშლა').'</a></li>
                        </ul>';
								echo '</div></div>';
							}
							echo '<div style="clear:both"></div>';
							echo '</div>';
							}
						if(@$_GET['reload']==$t) die();
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
				   if($s['field_type']=='combobox') {
						echo '<div class="grid3"><label>'.$s['field_name'].':</div>';
						echo '<div class="grid9">';
					   echo '<select name="_E_'.$t.'" class="'.$s['field_type'].'">';
					   echo '<option value="">'.__('აირჩიეთ').'</option>';
					   $r = sql::getRows("SELECT `cat_id`,`field_value` FROM `cn_modules_textlists`
												WHERE  `lang`='".$_ADM_CONTLNG."' AND
													`module_sys_name`='".$_GET['mod']."' AND `field_name`='".$t."'
													ORDER BY `field_value`+0");
						foreach($r as $v) echo '<option '.(($v['cat_id']==@$DATA[$t])?'selected="selected"':'').' value="'.$v['cat_id'].'">'.$v['field_value'].'</option>';
					   echo '</select>';
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
				   if($s['field_type']=='submodule:combobox') {
						echo '<div class="grid3"><label>'.$s['field_name'].': <a href="?p=modules&mod='.$s['field_type_params'].'" class="tablectrl_small bDefault tipS" title="ქვემოდულის მართვა"><span class="iconb" data-icon=""></span></a></label></div>';
						echo '<div class="grid9">';
						echo '<select name="_E_'.$t.'" class="'.$s['field_type'].'">';
						echo '<option value="">'.__('აირჩიეთ').'</option>';
						$r = sql::getRows("SELECT `cat_id`, `".$s['params']."` as field FROM `_cn_mod_".$s['field_type_params']."`
					   																			WHERE `lang`='".$_ADM_CONTLNG."'
																								ORDER BY `".$s['params']."`+0");
						foreach($r as $v) {
							$selected = (($v['cat_id']==@$DATA[$t]) ? 'selected="selected"' : '');
							echo '<option '.$selected.' value="'.$v['cat_id'].'">'.$v['field'].'</option>';
						}
						echo '</select>';
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
				   if($s['field_type']=='sitemap') {
						echo '<div class="grid3"><label>'.$s['field_name'].'</label></div>';
						echo '<div class="grid9">';
						echo '<select name="_E_'.$t.'" class="'.$s['field_type'].'">';
						echo '<option value="">'.__('აირჩიეთ').'</option>';							
						$r = sql::getRows("SELECT concat_ws('',REPEAT('&nbsp;', (c.depth-1)*4),c.title) as field, c.cat_id
											FROM `cn_sitemap` as c
												WHERE lang='".$_ADM_CONTLNG."' ORDER BY `ord`");
						foreach($r as $v) {
							$selected = (($v['cat_id']==@$DATA[$t]) ? 'selected="selected"' : '');
							echo '<option '.$selected.' value="'.$v['cat_id'].'">'.$v['field'].'</option>';
						}
						echo '</select>';
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
				   if($s['field_type']=='submodule:checkbox') {
						echo '<div class="grid3"><label>'.$s['field_name'].': <a href="?p=modules&mod='.$s['field_type_params'].'" class="tablectrl_small bDefault tipS" title="ქვემოდულის მართვა"><span class="iconb" data-icon=""></span></a></label></div>';
						echo '<div class="grid9">';
						$r = sql::getRows("SELECT `cat_id`, `".$s['params']."` as field FROM `_cn_mod_".$s['field_type_params']."`
					   																			WHERE `lang`='".$_ADM_CONTLNG."'
																								ORDER BY `".$s['params']."`+0");
						foreach($r as $v){
							$tmp = explode(',',@$DATA[$t]);
							echo '<div><label><input type="checkbox" value="'.$v['cat_id'].'" '.((in_array($v['cat_id'],$tmp))?'checked="checked"':'').' name="_Earr_'.$t.'[]"> '.$v['field'].'</label></div>';
						}
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
					if($s['field_type']=='checkbox') {
						echo '<div class="grid3"><label>'.$s['field_name'].' :</label></div>';
						echo '<div class="grid9" style="max-height:150px;overflow:auto">';
					   $r = sql::getRows("SELECT `cat_id`,`field_value` FROM `cn_modules_textlists`
												WHERE  `lang`='".$_ADM_CONTLNG."' AND
													`module_sys_name`='".$_GET['mod']."' AND `field_name`='".$t."' ORDER BY `field_value`+0");
						$tmp = explode(',',@$DATA[$t]);
						foreach($r as $v){
									echo '<div><label><input type="checkbox" value="'.$v['cat_id'].'" '.((in_array($v['cat_id'],$tmp))?'checked="checked"':'').' name="_Earr_'.$t.'[]"> '.$v['field_value'].'</label></div>';
						}
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
					if($s['field_type']=='gallery') {
						echo '<div class="grid3"><label>'.$s['field_name'].' :';
						if (is::developerIp()){
							echo '<div style="font-style:italic;color:#ccc;font-size:11px"><br>'.__('პარამეტრები').'<br>';
							foreach($s['params'] as $pk => $pv) echo $pk.' = '.$pv.'<br>';
							echo '</div>';
						}
						echo '</label></div>';
						echo '<div class="grid9" id="container_'.$t.'">';
						if(@$_GET['reload']==$t) ob_clean();
					    $gphotos = sql::getRows("SELECT * FROM `cn_modules_photos`
												WHERE  `lang`='".$_ADM_CONTLNG."'
													AND `module_sys_name`='".$_GET['mod']."'
													AND `field_name`='".$t."'
													AND `cat_Id` IN (".@$DATA[$t].") ORDER BY ord");
						$ids = array();
						foreach($gphotos as $v) $ids[] = $v['cat_id'];
						echo '<input type="hidden" name="_E_'.$t.'" value="'.implode(',',$ids).'" />';
						echo '<a href="#" onclick="$(this).next().slideToggle(\'fast\')">'.count($gphotos).'  '.__('სურათი').'</a>';
						echo '<div style="display:none1">';
							include("modules-gallery.php");
						if(@$_GET['reload']==$t) die();
						echo '</div>';
						echo '</div><div class="clear"></div>';
				   }
				   #########################################################################
				echo '</div>';
		  }
				echo '<br><center><input class="buttonM bDefault" value="'.__('შენახვა').'" type="submit" name="'.(isset($_GET['add'])?'add_button_submit':'edit_button_submit').'"></center><br>';
				echo '<div class="clear"></div>';

  ?>
  </div>
  </fieldset>
<form>	

<style type='text/css'>

.uploadify-button {
	background-color: transparent;
	border: none;
	padding: 0;
}

.uploadify:hover .uploadify-button {
	background-color: transparent;
}

#file_upload{ padding-left:130px }

fieldset textarea.textarea{
	width:555px; height:100px;
	font-size:14px;
	padding:5px
}
fieldset textarea.wysiwyg{
	width:555px; height:400px;
	font-size:14px;
	padding:5px
}
.gallery{ list-style-type:none; margin:0px;  }
.gallery > li{
		float:left;
		border:solid 1px red;
		margin-left:5px;
		display: inline-block;
		margin: 14px 6px 15px 6px;
		position: relative;
		background: white;
		padding: 4px 4px 0px 4px;
		-webkit-box-shadow: 0 0px 2px #ddd;
		-moz-box-shadow: 0 0px 2px #ddd;
		box-shadow: 0 0px 2px #ddd;
		border: 1px solid #ccc;
		border-radius: 2px;
		-webkit-border-radius: 2px;
		-moz-border-radius: 2px;
}
.phototools{
	float: left;
	border-left: none;
	border-right: none;
}

.btn-group.dropup a.buttonS.bDefault{
	padding:0px 11px;
}
.btn-group.dropup a.buttonS span{
	margin-left:-4px;
}

.dropdown-menu li a {
	padding:0px 10px;
}

.dropdown-menu{ min-width:50px }
.dropdown-menu li a{ border:none }
.uplfilesicon{ float:left; margin-left:5px; margin-bottom:5px }
</style>

<link href="js_css/uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js_css/plugins/others/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="js_css/plugins/others/jquery.Jcrop.min.js"></script>
<script type="text/javascript" src="/common/datetimepicker/jquery-ui-sliderAccess.js"></script>

<script type="text/javascript" src="/common/tinymce_v3/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE_init('<?php echo implode(', ',$wysiwygs); ?>');
</script>

<script type="text/javascript">
function removeFile(t,val){
	if(confirm('<?php _e('დარწმუნებული ხართ გსურთ ამ ფაილის წაშლა'); ?>?')===false) return false;
	$.post("<?php echo full_url(); ?>&delfile", {field:t,file:val},function(html){
		reloader(t);
	});
}
function opennamer(type,cat_id){
	url = [];
	url.push('?p=modules');
	url.push('mod=<?php echo $_GET['mod']; ?>');
	url.push('photonamer');
	url.push('photo_cat_id='+cat_id);
	url.push('type='+type);
	$.fancybox.open({'type':'ajax', 'autoWidth':true, 'href':url.join('&')});
}


function removeGalleryFile(req,t){
	if(confirm('<?php _e('დარწმუნებული ხართ გსურთ ამ ფაილის წაშლა'); ?>?')===false) { reloader(t); return true; }
	$.post(req, {'cache':'cache'},function(html){
		reloader(t);
	});
}

function init_uploadify(){
	$('input:file').each(function(i,e) {
		var update = $(this).attr('contId');
		var uplfor = $(this).attr('for');
		var multiple = ($(this).attr('multi')=='true') ? true : false;
		if(multiple) {
			formdata = {'upl_field':update, 'multi':'yes', 'uplfor':uplfor,'<?php echo session_name();?>':'<?php echo session_id();?>'};
		} else {
			formdata = {'upl_field':update, 'multi':'no',  'uplfor':uplfor,'<?php echo session_name();?>':'<?php echo session_id();?>'};
		}

		buttonImage = 'img/'+$(this).attr('bg')+'.png';
		$(e).uploadify({
			formData  : formdata,
			swf	      : 'img/uploadify.swf',
			height	  : 29,
			width	  : 252,
			multi     : multiple,
			buttonImage : buttonImage,
			uploader : '<?php echo full_url(); ?>&uploadscript',
			onError : function(event, queueID, fileObj, errorObj) { alert(errorObj.type + ' ' + errorObj.info ); },
			onQueueComplete	: function(queueData) { reloader(update); }
		});
	});
}

init_uploadify();

function reloader(update){
	$.post("<?php echo full_url(); ?>&reload="+update, {'a':'a'},function(html){
		$('#container_'+update).html(html);
	});
}

function opencropper(type,photo,field,thumb_w,thumb_h){
	photo = encodeURIComponent(photo);
	url = [];
	url.push('?p=modules');
	url.push('mod=<?php echo $_GET['mod']; ?>');
	url.push('photoeditor');
	url.push('photo='+photo);
	url.push('field='+field);
	url.push('editId=<?php echo @$_GET['editId']; ?>');
	url.push('thumb_w='+thumb_w);
	url.push('thumb_h='+thumb_h);
	$.fancybox.open({'type':'ajax', 'autoWidth':true, 'href':url.join('&')});

}

function controlcategories(field){
	$.fancybox.open({'type':'ajax',
					 'autoWidth':true,
					 'href':'?p=modules&mod=<?php echo $_GET['mod']; ?>&manage_textlists='+field});
}


$(".fancybox").fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
});


</script>
