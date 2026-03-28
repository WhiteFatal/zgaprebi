<?php
if (!defined('FILE_SECURITY_KEY'))  die ();
	mysql_connect($_CFG['mysqlServer'], $_CFG['mysqlUser'], $_CFG['mysqlPass']);
	mysql_select_db($_CFG['mysqlDB']);
?>

<table border="0" style="margin-left:auto; margin-right:auto">
  <tr>
    <td colspan="2" valign="top">
	    <div align="left" style="margin:5px 0px 10px 0px; font-size:13px; color:#c64934; font-weight:bold"><img src="img/data_table.png" align="absmiddle" />&nbsp;&nbsp;&nbsp;Web რესურსზე მონაცემთა ბაზის შესახებ</div>
    </td>
    <td valign="top" id="TableActResult2">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">
<?php echo tableStyle('header','<span style="color:#3B7FCA">MySQL სერვერი</span>'); ?>
    <table width="280" border="0" cellpadding="2" cellspacing="0" style="border:solid 1px #e5e5e5" class="redSolid">
<thead>
       <tr style="background:url(img/tableTitle.png)">
        <td>მნიშვნელობა</td>
        <td>სტატუსი</td>
  </tr>
  </thead>
<tbody>
     <tr>
       <td><img src="img/s_host.png" align="absmiddle" style="padding:3px" /> სერვერის ვერსია</td>
       <td><?php echo mysql_get_server_info(); ?></td>
     </tr>
     <tr>
       <td><img src="img/s_host.png" align="absmiddle"  style="padding:3px" /> MySQL client version:&nbsp;</td>
       <td><?php echo mysql_get_client_info(); ?></td>
     </tr>
     <tr>
       <td><img src="img/s_host.png" align="absmiddle"  style="padding:3px" /> სერვერი</td>
       <td><?php echo mysql_get_host_info(); ?></td>
     </tr>
     <tr>
       <td><img src="img/s_host.png" align="absmiddle"  style="padding:3px" /> Protocol version:</td>
       <td><?php echo mysql_get_proto_info(); ?></td>
     </tr>
     <tr>
       <td><img src="img/s_rights.png" align="absmiddle"  style="padding:3px" /> SESSION USER<br><img src="img/s_rights.png" align="absmiddle"  style="padding:3px" />&nbsp;CURRENT USER</td>
       <td>
         <?php
            $result = mysql_query("SELECT SESSION_USER(), CURRENT_USER();");
            $row = mysql_fetch_row($result);
            echo $row[0]."<br>".$row[1];
            ?></td>
     </tr>
     <tr>
  <td colspan="2" style="background:url(img/tableBottom.png); background-repeat:repeat-x; height:19px"></td>
</tr>
</tbody>
</table>
<?php echo tableStyle('footer'); ?>
</td>
    <td valign="top" style="text-align:right">
  <?php echo tableStyle('header','<span style="color:#3B7FCA">MySQL ცხრილები</span>'); ?>
      <div id="backupParams" style="display:none">
        <?php echo tableStyle('header','<span style="color:#3B7FCA">მარქაფის პარამეტრები</span>','id="backupParamsT"'); ?>
        <div style="text-align:left; margin-left:20px">
<input type="checkbox" name="structure" id="structure" value="1" checked="checked" /> <label for="structure">სტრუქტურა</label><br>
<input type="checkbox" name="data" id="data" value="1" checked="checked" /> <label for="data">მონაცემები</label><br>
<input type="checkbox" name="zip" id="zip"  value="1" checked="checked" /> <label for="zip">დაარქივება (zip)</label><br><br>
<input type="button" onclick="tableToolsB()" name="saveGoogleScript" value="დაარქივება" class="button b90_22" align="absmiddle" />
<a href="javascript:void(0)" onclick="backupToolsSH()" style="text-decoration:underline; margin:0 40px 10px 20px;">უარი</a>
        </div>
  <?php echo tableStyle('footer'); ?>
      </div><span style="clear:both"></span>
      <table border="0" align="center" cellspacing="0" cellpadding="3" style="border:solid 1px #e5e5e5;" class="redSolid">
        <thead>
          <tr>
            <td colspan="5" style="text-align:left">
              <a href="javascript:void(0)" onclick="tableTools('optimize')">ოპტიმიზაცია</a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="tableTools('check')">შემოწმება</a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="tableTools('repair')">გამართვა</a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="tableTools('analyze')">ანალიზი</a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="backupToolsSH()">დამარქაფება</a>
              <input type="hidden" name="actTableList" id="actTableList" />
            </td>
          </tr>
          <tr style="background:url(img/tableTitle.png)">
            <td style="width:16px"><input type="checkbox" id="checkAllcheckbox" onclick="checkAll('.table_checkbox', this)" checked="checked" /></td>
            <td>ცხრილი</td>
            <td>ჩანაწერი</td>
            <td>ინფორმაცია</td>
            <td>ინდექსი</td>
          </tr>
        </thead>
        <tbody>
          <?php
				$result = sql::getRows("SHOW TABLES IN `".$_CFG['mysqlDB']."`");
				$tblCNT=0;
				$tables = array();
				$tableCNT['row'] = 0;
				$tableCNT['data'] = 0;
				$tableCNT['index'] = 0;
				foreach ($result as $row) {
					foreach ($row as $v) {
						$tables[] = '`'.$v.'`';
					}
			?>
          <tr>
            <td style="text-align:left; font-weight:bold">
            	<input type="checkbox" name="tables_<?php echo $row[0]?>" value="<?php echo $v; ?>" class="table_checkbox" checked="checked" /></td>
            <td width="33%" style="text-align:left; font-weight:bold"><?php echo $v; ?></td>
            <td width="33%" align="center"><?php
         $sqlNum = "SELECT * FROM `".$v."`";
		 $resultNum = sql::getRows($sqlNum);
		 echo $resultNumm = count($resultNum);
		 $tableCNT['row'] = $tableCNT['row']+ $resultNumm;
		 $tableCNT['data'] = $tableCNT['data'] + getTableSize($v,'Data_length');
		 $tableCNT['index'] = $tableCNT['index'] + getTableSize($v,'Index_length');
		?></td>
            <td width="33%" align="center"><?php echo B2KB(getTableSize($v,'Data_length')); ?></td>
            <td width="33%" align="center"><?php echo B2KB(getTableSize($v,'Index_length')); ?></td>
          </tr>
  <?php
 $tblCNT++;
}
?>
          <tr style="font-weight:bold">
            <td>&nbsp;</td>
            <td style="text-align:right">სულ:</td>
            <td align="center"><?php echo $tableCNT['row']?></td>
            <td align="center"><?php echo B2KB($tableCNT['data']); ?></td>
            <td align="center"><?php echo B2KB($tableCNT['index']); ?></td>
          </tr>
  <tr>
  <td colspan="5" style="background:url(img/tableBottom.png); background-repeat:repeat-x; font-size:10px">&nbsp;</td>
  </tr>
  </tbody>
  </table>
  <?php echo tableStyle('footer'); ?>
</td>
    <td valign="top" id="TableActResult" style="width:372px;">&nbsp;</td>
  </tr>
</table>
<script type="text/javascript">

var backupToolsStatus = 0;
function backupToolsSH(){
	if(backupToolsStatus==0){
		$("#backupParams").slideDown("fast");
		backupToolsStatus = 1;
	} else {
		$("#backupParams").slideUp("fast");
		backupToolsStatus = 0;
	}
}


function tableToolsB(){
	backupToolsSH();
	params = new Array();
	params.push('tables='+$('#actTableList').val());
	if($('#structure').is(':checked')) params.push('structure=1');
	if($('#data').is(':checked')) params.push('data=1');
	if($('#zip').is(':checked')) params.push('zip=1');
	$('#TableActResult').html('<b>ბაზის მონაცემების დამარქაფება...</b><br>');
	$('#TableActResult').append('<iframe src="ajax/backup.php?'+params.join('&')+'" width="100" height="100" frameborder="0"></iframe>')
}

function tableTools(acts){
	$('#TableActResult').html('<b>იტვირთება...</b>');
	var params = {act:acts,tables:$('#actTableList').val()};
	$.post("ajax/dbaTables.php", params, function(data){
		$('#TableActResult').html(data);
	});
}

var tables = new Array();
$('.table_checkbox').each(function() {
		if($(this).is(':checked')) tables.push('`'+$(this).val()+'`');
})
$('#actTableList').val(tables.join(','));

function checkAll(allobj, thisobj){
	var tables = new Array();
	if($(thisobj).is(':checked')){
		$(allobj).attr('checked',true)
	} else {
		$(allobj).attr('checked',false)
	}
	$('.table_checkbox').each(function() {
			if($(this).is(':checked')) tables.push('`'+$(this).val()+'`');
	})
	$('#actTableList').val(tables.join(','));
}

$('.table_checkbox').change(function() {
	var tables = new Array();
	var i;
	$('.table_checkbox').each(function() {
		if($(this).is(':checked')) tables.push('`'+$(this).val()+'`');
		i++;
	})
	if(i!=<?php echo $tblCNT?>) {
		$('#checkAllcheckbox').attr('checked',false)
	} else {
		$('#checkAllcheckbox').attr('checked',true)
	}
	$('#actTableList').val(tables.join(','));
})
</script>
<style type="text/css">
#backupParams{
	margin-left:110px;
	margin-top:15px;
	text-align:center;
	position:absolute;
	padding:5px;
	z-index:999
}
#backupParamsT .tb1, #backupParamsT .tb3, #backupParamsT .tb2{
	background:#FFF
}
</style>