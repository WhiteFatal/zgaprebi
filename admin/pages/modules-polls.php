<?php
if (!defined('FILE_SECURITY_KEY'))  die();

if(isset($_GET['polls_add'])){
	ob_clean();
	$new_cat_id = get::newcatId("cn_polls");
	foreach(get::langs() as $l => $v){
		echo $sql = "INSERT INTO `cn_polls`
					SET `lang` = '".$l."' ,
						`cat_Id` = '".$new_cat_id."' ,
						`active` = '-1'";
		sql::insert($sql);
	}
	header("Location: ?p=modules&mod=polls&polls_editId=".$new_cat_id.'&from_1');
	die();
}

if(isset($_POST['savepoll'])){
	$langloops = isset($_GET['from_1']) ? get::langs() : array($_ADM_CONTLNG => $_ADM_CONTLNG);
	foreach($langloops as $l => $v){
		$active = sql::getElem("SELECT `active` FROM `_cn_polls` WHERE `lang`='".$l."' AND `cat_Id`=".$_GET['polls_editId']);
		$active = ($active>=0) ? $active : '0';
		echo "UPDATE `cn_polls` SET `question`='".$_POST['_E_question']."', `active`=".$active."
												WHERE cat_id='".$_GET['polls_editId']."'  AND `lang`='".$l."'";
		sql::update("UPDATE `cn_polls` SET `question`='".$_POST['_E_question']."', `active`=".$active."
												WHERE cat_id='".$_GET['polls_editId']."'  AND `lang`='".$l."'");
		foreach($_POST['_E_answer'] as $k => $a){
			sql::update("UPDATE `cn_polls_answers` SET `answer`='".$a."'
													WHERE poll_id='".$_GET['polls_editId']."' AND
														`cat_id` = '".$k."' AND
														`lang`='".$l."'");
		}
	}
	//redirect("?p=modules&mod=polls&polls_editId=".$_GET['polls_editId']);
}

if(isset($_GET['addQuestion'])){
	$new_cat_id = get::newcatId("cn_polls_answers");
	foreach(get::langs() as $l => $v){		
		sql::insert("INSERT INTO `cn_polls_answers` SET
									poll_id='".$_GET['polls_editId']."',
									`cat_id`='".$new_cat_id."',
									`lang`='".$l."'");
	}
	redirect(str_replace('&addQuestion','',full_url()));
}

if(!isset($_GET['polls_editId'])) {
?>
	<div class="widget">
	<div class="whead"><h6><?php _e('არსებული გამოკითხვები'); ?></h6>
	<ul class="titleToolbar">
		<li><a title="<?php _e('ახალი ჩანაწერის დამატება'); ?>" href="?p=modules&mod=polls&polls_add" class="tipS"><?php _e('დამატება'); ?></a></li>
		<?php /*?><li><a onclick="$('#dyn .tablePars').slideToggle()" title="Options"><img src="img/icons/options" alt="" style="margin-top:6px" /></a></li><?php */?>
	</ul>
	<div class="clear"></div></div>
	<div id="dyn" class="hiddenpars">
	<div class="tablePars" style="display:<?php echo (isset($_GET['filter'])?'block':'none'); ?>">
	</div>
		<table cellpadding="0" cellspacing="0" width="100%" class="tLight noBorderT">
   <?php 
	$sql = "SELECT * FROM `cn_polls` WHERE active>=0 AND `lang`='".$_ADM_CONTLNG."' ORDER BY Id DESC";
	$dataCNT = sql::cnt($sql);
	$data    = sql::getRows($sql." LIMIT ".(($_GET['pg']-1)*10).",10");
	$textlists = array();
		foreach($data as $v){
			echo '<tr>';
			echo '<td>'.$v['question'].'</td>';
		$active = ($v['active']=='1') ? 'checked="checked"' : "";
		echo '<td style="width:50px">
					<div class="grid9 on_off">
						<div class="floatL mr10"><input type="checkbox" onchange="saveactiveRow(this,\''.$v['cat_Id'].'\')" class="activeRow" value="" '.$active.' name="chbox[]" /></div>
					</div>
			</td>';
		echo '<td class="tableActs" style="width:60px">
				<a href="?p=modules&mod=polls&polls_editId='.$v['cat_id'].'" class="tablectrl_small bDefault tipS" title="'.__('რედაქტირება').'"><span class="iconb" data-icon="&#xe1db;"></span></a>
				<!--<a href="?p=modules&mod=polls&delId='.$v['cat_id'].'" onclick="return  confirm(\''.__('დარწმუნებული ხართ გსურთ ამ ჩანაწერის წაშლა').'?\')" class="tablectrl_small bDefault tipS" title="წაშლა"><span class="iconb" data-icon="&#xe136;"></span></a>-->
			</td>
</tr>';
	}
?>
			</tbody>
		</table>
		</div>
		</div>
	</div>
<?php
} else {
?>
   <form method="post" action="" class="main">
     <fieldset>
    <div class="widget grid9" style="margin-left:20px">
    <div class="whead"><h6><?php _e('გამოკითხვა'); ?></h6><div class="clear"></div></div>
    <div class="formRow">
		<div class="grid3"><label><?php _e('კითხვა'); ?>:</label></div>
        <div class="grid9">
        <input type="text" name="_E_question" value="<?php echo sql::getElem("SELECT `question` FROM `cn_polls` WHERE `cat_id`=".$_GET['polls_editId']." AND `lang`='".$_ADM_CONTLNG."'"); ?>" />
        </div><div class="clear"></div>
	</div>
    <?php
    	$answers = sql::getRows("SELECT * FROM `cn_polls_answers` WHERE poll_id=".$_GET['polls_editId']." AND lang='".$_ADM_CONTLNG."'");
		foreach($answers as $v){
			echo '<div class="formRow">
					<div class="grid3"><label>'.__('პასუხი').':</label></div>
					<div class="grid9">
					<input type="text" name="_E_answer['.$v['cat_id'].']" value="'.$v['answer'].'" />
					</div><div class="clear"></div>
				</div>';
		}
    ?>
 <a href="<?php echo full_url().'&addQuestion'; ?>">&nbsp;&nbsp;&nbsp;<?php _e('კითხვის დამატება'); ?></a>
<br><center><input class="buttonM bDefault" value="<?php _e('შენახვა'); ?>" type="submit" name="savepoll"></center><br>
<div class="clear"></div>    

  </div>
  </fieldset>
<form>	
<?php
}
?>
