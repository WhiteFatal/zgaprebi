<?php

if (!defined('FILE_SECURITY_KEY'))  die ();

	if (isset($_REQUEST['act']) && ($_REQUEST['act']=='deleteLog') ) {
		  sql::delete("truncate `cn_adminlog`");
			adminLog('ლოგების გაწმენდა');
			header ("Location: ?p=adminLog");
		$truncStat = "<div style='color:red; font-size:10px; margin-left:3px; height:21px'>ლოგების ცხრილი გაიწმინდა</div>";
	} else { $truncStat = "<br>"; }

	

	if (!isset($_REQUEST['lim'])) { $_REQUEST['lim']=0; }

	$numRows = sql::cnt("SELECT * FROM `adminlog`");

	

?>

<div align="left" style="margin:5px 0px 0px 0px; font-size:13px; color:#c64934; font-weight:bold"><img src="img/user.png" align="absmiddle" />&nbsp;&nbsp;&nbsp;Web რესურსზე ადმინისტრატორის შემოსვლის ლოგ ჩანაწერი</div>

<?php echo @$truncStat; ?>

<?php echo tableStyle('header','<span style="color:#3B7FCA">სისტემაში შემოსვლა</span>'); ?>

    <table border="0" cellspacing="0" cellpadding="3px" style="border:solid 1px #e5e5e5;" class="redSolid">
    <thead>
      <tr style="background:url(img/tableTitle.png)">
        <td>თარიღი</td>
        <td>IP</td>
        <td>რეზულტატი</td>
        <td>ქმედება</td>
        <td>მომხმარებელი</td>
      </tr>
    </thead>
    <tbody>
   <?php 
if($numRows>0){
	$sqlLimit = ($_GET['pg']==1) ?  ("0,20") : ($_GET['pg']."0,20");
	   $sqlLog = sql::getRows("SELECT * FROM `adminlog` order by logDate DESC LIMIT ".$sqlLimit);
		foreach($sqlLog as $row_Log){
	?>
      <tr>
        <td><?php echo $row_Log['logDate']; ?></td>
        <td><a onclick="return ripenet('<?php echo $row_Log['IP']; ?>')" href="http://www.db.ripe.net/whois?searchtext=<?php echo $row_Log['IP']; ?>" target="_blank" title="<?php echo getCompOwner($row_Log['IP'],$row_Log['comment']); ?>"><?php echo $row_Log['IP']; ?></a></td>
        <td align="center"><?php echo loginStatus($row_Log['status']); ?></td>
        <td align="center"><?php echo $row_Log['log']; ?></td>
        <td><?php echo $row_Log['user']; ?></td>
      </tr>
      <?php 
	}
}
?>
      <tr>
        <td colspan="6" style="text-align:right">
        	<a href="?p=adminLog&act=deleteLog" onclick="return confirm('დარწმუნებული ხართ გსურთ ადმინისტრატორის ლოგის გაწმენდა?')" style="color:#930">გაწმენდა</a></span>
    	<span style="margin-left:10px">
		        <?php echo paging($numRows,20); ?>
        </span>
</td>
      </tr>
<tr>
<td colspan="6" style="background:url(img/tableBottom.png); background-repeat:repeat-x; font-size:10px">&nbsp;</td>
</tr>
<tbody>
</table>
<?php echo tableStyle('footer'); ?>
<script type="text/javascript">
function ripenet(ip){
	$.facebox('<div style="text-align:right"><a href="#" class="close">დახურვა</a></div><iframe src=\'http://www.db.ripe.net/whois?searchtext='+ip+'\' width="850" height="500" frameborder="0" style="border:solid 1px #CCC"></iframe>');
	return false;
}
</script>