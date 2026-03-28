<?php
if (!defined('FILE_SECURITY_KEY'))  die();
if (!has::role('dashboard')) die();
include('../theme/functions.php');
	$balance = sql::getElem("SELECT SUM(balance) FROM `account_balances`");
	$expend = purchashes();
	$premiums = premiums();
	$tales = tales();
?>
    <!-- Secondary nav -->
</div>
<!-- Sidebar ends -->

<!-- Content begins -->   
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-205"></span>&nbsp;</span>
    </div>
    <div class="breadLine"></div>    
    <!-- Main content -->

<div class="widget" style="width:80%; padding:20px; margin-left:10px">
  <div style="margin-bottom:30px">
  	<?php _e('მოგესალმებით'); ?> <?php echo $_SESSION['flname']; ?>
  	<ul style="margin-top:15px;">
    	<li>სულ არსებული: <b><?php echo cash_to_gold($balance); ?> ოქრო</b></li>
        <li>სულ დახარჯული: <b><?php echo $expend; ?> ოქრო</b></li>
        <li>დახარჯულიდან: <b><?php echo $premiums; ?> პრემიუმი</b> / <b><?php echo $tales; ?> ზღაპარი</b></li>
    </ul>  
  </div>
  <?php if(file_exists('../storage/admin-home.png')) echo '<img src="/storage/admin-home.png" />'; ?>
</div>


