<?php
if (!defined('FILE_SECURITY_KEY'))  die();
if (!is::developerIp()) die();
ob_clean();
if(isset($_POST['ftype'])){
	$sqlins = array();
	$fields = array();
	$fields[] = '`Id` int NOT NULL AUTO_INCREMENT';
	$fields[] = '`cat_Id` int';
	 foreach($_POST['ftype'] as $k => $v){
		if($v=='string')		$fields[] = "`".$_POST['fname'][$k]."` varchar(255) COMMENT '".$_POST['flabel'][$k]."'";
		if($v=='textarea')		$fields[] = "`".$_POST['fname'][$k]."` text  		COMMENT '".$_POST['flabel'][$k]."'";
		if($v=='wysiwyg')		$fields[] = "`".$_POST['fname'][$k]."` text  		COMMENT '".$_POST['flabel'][$k]."'";
		if($v=='file')			$fields[] = "`".$_POST['fname'][$k]."` varchar(255)	COMMENT '".$_POST['flabel'][$k]."'";
		if($v=='files')			$fields[] = "`".$_POST['fname'][$k]."` text  		COMMENT '".$_POST['flabel'][$k]."'";
		if($v=='date')			$fields[] = "`".$_POST['fname'][$k]."` date  		COMMENT '".$_POST['flabel'][$k]."'";
		if($v=='datetime')		$fields[] = "`".$_POST['fname'][$k]."` datetime		COMMENT '".$_POST['flabel'][$k]."'";
		if($v=='combobox')		$fields[] = "`".$_POST['fname'][$k]."` varchar(255)	COMMENT '".$_POST['flabel'][$k]."'";
		if($v=='checkbox')		$fields[] = "`".$_POST['fname'][$k]."` varchar(255)	COMMENT '".$_POST['flabel'][$k]."'";
		if($v=='sitemap')		$fields[] = "`".$_POST['fname'][$k]."` varchar(255)	COMMENT '".$_POST['flabel'][$k]."'";
		$sqlins[] =  "insert into `cn_modules_structures` SET `field_type` 		  = '".$v."',
															   `field_name`		  = '".$_POST['flabel'][$k]."',
															   `field_sys_name`	  = '".$_POST['fname'][$k]."',
															   `module_sys_name`  = '".$_POST['modulename']."',
													   		   `show_on_page`     = '".$_POST['editor_fields_show_on_page'][$k]."',
													   		   `show_in_editor`   = '".$_POST['editor_fields_show_in_editor'][$k]."'";
	}

	$fields[] = '`lang` varchar(255)';
	$fields[] = '`modify_date` datetime';
	$fields[] = '`row_owner` int(11)';
	$fields[] = "`active` int(2)  NOT NULL DEFAULT '1'";
	$fields[] = 'PRIMARY KEY (`Id`)';
	sql::insert("INSERT INTO `cn_modules` SET `sys_name`='".$_POST['modulename']."', `name`='".$_POST['modulelabel']."';");
	sql::insert("CREATE TABLE `_cn_mod_".$_POST['modulename']."` (".implode(",",$fields).") ENGINE=`MyISAM`;");
	if(in_array('file',$_POST['ftype']) || in_array('files',$_POST['ftype'])) {
		@mkdir('../storage/uploads/'.$_POST['modulename']);
		@chmod('../storage/uploads/'.$_POST['modulename'],0777);
	}
	foreach($sqlins as $s) sql::insert($s);
	die('ok');
}

?>


    <form action="" method="post" onsubmit="return false" id="generatorform">
    <div class="formRow" style="text-align:center"><input type="text" name="modulelabel" placeholder="<?php _e('ქართული სახელი'); ?>" style="width:200px" /><input type="text" name="modulename" id="modulename" placeholder="<?php _e('სისტემური სახელი'); ?>" style="width:200px; margin-left:5px" /></div>
  <table cellpadding="0" cellspacing="0" width="100%" class="tLight noBorderT" id="generator">
    <thead>
        <tr><td colspan="3"><?php _e('ველების აღწერა'); ?></td><td class="tipS" title="<?php _e('ჩანდეს თუ არა მოდულის გვერდზე'); ?>"><?php _e('გამოჩენა'); ?><sup>*</sup></td><td class="tipS" title="<?php _e('ჩანდეს თუ არა მოდულის რედაქტორში'); ?>"><?php _e('გამოჩენა'); ?><sup>**</sup></td><td><?php _e('წაშლა'); ?></td></tr>
    <thead>
    <tbody>
    </tbody>
    <tfoot>
    <tr>
    <td colspan="5" style="text-align:center"><a href="javascript:void(0)" onclick="add_row()"><?php _e('ახალი ველის დამატება'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php _e('შექმნა'); ?>" class="buttonS bLightBlue" onclick="saveGenerated()" name="submit" /></td>
    </tr>
<tr>
<td colspan="5">&nbsp;</td>
</tr>
</tfoot>
</table>
</form>
<script type="text/javascript">
function add_row(){	
	var html  = '<tr>';
        html += '<td class="formRow"><input type="text" placeholder="<?php _e('ქართული სახელი'); ?>" class="flabel" name="flabel[]" /></td>';
        html += '<td class="formRow"><input type="text" placeholder="<?php _e('სისტემური სახელი'); ?>" class="fname" name="fname[]" /></td>';
        html += '<td>';
        html += '<select name="ftype[]">';
        html += '<option value="string">String</option>';
        html += '<option value="textarea">Textarea</option>';
        html += '<option value="wysiwyg">Wysiwyg</option>';
        html += '<option value="date">Date</option>';
        html += '<option value="datetime">Datetime</option>';
        html += '<option value="combobox">Combobox</option>';
        html += '<option value="checkbox">Checkboxes</option>';
        html += '<option value="file">File</option>';
        html += '<option value="files">Files</option>';
        html += '<option value="files">Sitemap</option>';
        html += '</select>';
        html += '</td>';
        html += '<td><select name="editor_fields_show_on_page[]"><option value="1"><?php _e('დიახ'); ?></option><option value="0" selected="selected"><?php _e('არა'); ?></option></select></td>';
        html += '<td><select name="editor_fields_show_in_editor[]"><option value="1" selected="selected"><?php _e('დიახ'); ?></option><option value="0"><?php _e('არა'); ?></option></select></td>';
        html += '<td class="tableActs"><a class="tablectrl_small bDefault tipS" title="<?php _e('წაშლა'); ?>" href="javascript:void(0)" onClick="remove_row(this)" ><span class="iconb" data-icon="&#xe136;"></span></a></td>';
        html += '</tr>';
	$('#generator tbody').append(html);
	$('#generator tbody .flabel:last').focus();
	tablestyleregeneration();
	$('.tipS').tipsy({gravity: 's',fade: true, html:true});
}
add_row();

function tablestyleregeneration(){
	
}

function saveGenerated(){
	$.post("<?php echo full_url(); ?>",$('#generatorform').serializeArray(),function(data){
			if(!data) return false;
			if(data=='ok') document.location.href='?p=modules&mod='+$('#modulename').val()+'&msg=modulecreatedok';
	   });
}

function remove_row(o){
	if(confirm('<?php _e('დარწმუნებული ხართ გსურთ ამ ველის წაშლა'); ?>?')===true){
		$(o).parent().parent().remove();
		tablestyleregeneration();
	}
}

</script>

<?php  die(); ?>


