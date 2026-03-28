<?php
if(!defined('FILE_SECURITY_KEY')) die();
if(!has::role('others')) 	  die();

$_GET['act'] = (!isset($_GET['act'])) ? 'users' : $_GET['act'];

$_cms_files = array();
function getphpfiles($path = '.', $level = 0){ 
	global $_cms_files;
	$ignore = array( 'cgi-bin', '.', '..' , 'storage', 'theme');
	$exclude_files = array();
    $dh = @opendir($path); 
    while(false !== ($file = readdir($dh))){
		$fullfile = $path.'/'.$file;
        if( !in_array( $file, $ignore ) ){ 
            $spaces = str_repeat('&nbsp;', ($level * 4)); 
            if(is_dir($fullfile)){ 
                getphpfiles($fullfile, ($level+1)); 
            } else {
				if(!in_array($fullfile,$exclude_files)){
					 $_cms_files[$fullfile] = date ("Y-m-d H:i:s", filemtime($fullfile));
				}
            }         
        }
    }
    closedir($dh); 
}


if(isset($_POST['saveRowId'])){
	ob_clean();
	sql::update("UPDATE `cn_langs` SET `value`='".$_POST['transed']."' WHERE Id=".$_POST['saveRowId']);
	die('Ok');
}

?>
    <!-- Secondary nav -->
    <div class="secNav">
        <div class="secWrapper">
            <div class="secTop">
                <div class="balance">
                    <div class="balInfo"><?php _e('სხვადასხვა'); ?><span>&nbsp;</span></div>
                </div>
          </div>
            <div class="divider" style="margin:0 0 50px 0"><span></span></div>            
          <!-- Tabs container -->
            <div id="tab-container" class="tab-container">
              <div id="general">
                    <ul class="subNav">
                        <li><a href="?p=others&act=users" <?php echo (($_GET['act']=='users')?'class="this"':''); ?> title=""><span class="icos-info"></span><?php _e('მომხმარებლები'); ?></a></li>
                        <li><a href="?p=others&act=phpinfo" <?php echo (($_GET['act']=='phpinfo')?'class="this"':''); ?> title=""><span class="icos-info"></span><?php _e('PHP ინფორმაცია'); ?></a></li>
                        <li><a href="?p=others&act=htaccess" <?php echo (($_GET['act']=='htaccess')?'class="this"':''); ?> title=""><span class="icos-list"></span>.htaccess</a></li>
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
        <span class="pageTitle"><span class="icon-link"></span><?php echo $_GET['act']; ?></span>
        <div class="clear"></div>
    </div>
    <div class="breadLine"></div>    
    <!-- Main content -->

<div style="width:98%; margin-left:10px" id="phpinfo">
<?php
	if($_GET['act'] == 'phpinfo'){
		ob_start();
		phpinfo();
		$info = ob_get_contents();
		ob_end_clean();
		$info = get_string_between($info,'<body>','</body>');
		$info = str_replace('<table','<table class="lastbordered"',$info);
		echo $info;
		echo '<style type="text/css">
				#phpinfo { padding-top:10px }
				#phpinfo * {font-family: sans-serif}
				#phpinfo td, #phpinfo th, #phpinfo h1, #phpinfo h2 {font-family: sans-serif; padding:3px }
				#phpinfo pre {margin: 0px; font-family: monospace}
				#phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff}
				#phpinfo a:hover {text-decoration: underline}
				#phpinfo table {border-collapse: collapse}
				#phpinfo .center {text-align: center}
				#phpinfo .center table { margin-left: auto; margin-right: auto; text-align: left}
				#phpinfo .center th { text-align: center !important}
				#phpinfo td, #phpinfo th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline}
				#phpinfo h1 {font-size: 150%}
				#phpinfo h2 {font-size: 125%}
				#phpinfo .p {text-align: left}
				#phpinfo .e {background-color: #ccccff; font-weight: bold; color: #000000; font-size:12px}
				#phpinfo .h {background-color: #9999cc; font-weight: bold; color: #000000; font-size:12px}
				#phpinfo .v {background-color: #cccccc; color: #000000; font-size:12px}
				#phpinfo .vr {background-color: #cccccc; text-align: right; color: #000000; font-size:12px}
				#phpinfo img {float: right; border: 0px}
				#phpinfo hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000}
			</style>';
	}
	if($_GET['act'] == 'htaccess'){
		echo '<div class="widget" style="width:500px; margin-top:5px">
            <div class="whead"><h6>. htaccess</h6><div class="clear"></div></div>
            <div id="dyn" class="hiddenpars" style="padding:10px">'.str_replace("\n","<br>",file_get_contents('../.htaccess')).'</div>
            <div class="clear"></div> 
        </div>';
	}
	##############################################
	if($_GET['act'] == 'cmscheck'){
			$lastversion = file_get_contents('http://cms.about.ge?seckey=123123');
			$lastversion = unserialize($lastversion);
			getphpfiles("..");
			$currentversion = $_cms_files;
			//echo '<textarea>'.(serialize($currentversion)).'</textarea>';
			$cashashlelia  = array_diff_key($currentversion,$lastversion);
			$dasamatebelia = array_diff_key($lastversion,$currentversion);
			$cvlilebebi = array_diff($lastversion,$currentversion);
		if(count($cashashlelia)>0){
			echo '<div class="widget grid9" style="margin-left:20px; width:500px">
					<div class="whead"><h6 style="color:red">წაშალელი ფაილები</h6><div class="clear"></div></div>
					<table cellpadding="0" cellspacing="0" width="100%" class="tLight noBorderT editors">
					<thead><tr><td>ფაილი</td><td style="width:130px" title="ბოლო მოდიფიცირების თარიღი">ბოლო მოდ. თარიღი</td></tr><thead>
					<tbody>';
			foreach($cashashlelia as $k => $v) echo '<tr><td>'.$k.'</td><td>'.$v.'</td></tr>';
			echo '</tbody></table></div>';
		}
		if(count($dasamatebelia)>0){
			echo '<div class="widget grid9" style="margin-left:20px; width:500px">
					<div class="whead"><h6 style="color:green">ახალი ფაილები</h6><div class="clear"></div></div>
					<table cellpadding="0" cellspacing="0" width="100%" class="tLight noBorderT editors">
					<thead><tr><td>ფაილი</td><td style="width:130px" title="ბოლო მოდიფიცირების თარიღი">ბოლო მოდ. თარიღი</td></tr><thead>
					<tbody>';
			foreach($dasamatebelia as $k => $v) echo '<tr><td>'.$k.'</td><td>'.$v.'</td></tr>';
			echo '</tbody></table></div>';
		}
		if(count($cvlilebebi)>0){
			echo '<div class="widget grid9" style="margin-left:20px; width:500px">
					<div class="whead"><h6 style="color:orange">ცვლილებები</h6><div class="clear"></div></div>
					<table cellpadding="0" cellspacing="0" width="100%" class="tLight noBorderT editors">
					<thead><tr><td>ფაილი</td><td style="width:130px">ლოკალური თარიღი</td><td style="width:130px">განახლების თარიღი</td></tr><thead>
					<tbody>';
			foreach($cvlilebebi as $k => $v) echo '<tr><td>'.$k.'</td><td>'.$v.'</td><td>'.$currentversion[$k].'</td></tr>';
			echo '</tbody></table></div>';
		}
	}
	#############################################
	if($_GET['act'] == 'users'){
		
		if(@$_GET['msg']=='addOk'){
			echo '<div class="nNote nSuccess" style="margin:15px 0 15px 0;"><p>'.__('ახალი მომხმარებელი დაემატა წარმატებით').'</p></div>';	
		}
		if(@$_GET['msg']=='updateOk'){
			echo '<div class="nNote nSuccess" style="margin:15px 0 15px 0;"><p>'.__('მომხმარებელი დარედაქტირდა წარმატებით').'</p></div>';	
		}
		
	?>
		<div class="widget grid9" style="margin-left:20px; width:950px">
		<div class="whead"><h6><?php _e('სისტემის მომხმარებლები'); ?></h6><ul class="titleToolbar">
		<li><a title="<?php _e('ახალი მომხმარებლის დამატება'); ?>" href="?p=others&act=editusers&Id=-1" class="tipS"><?php _e('დამატება'); ?></a></li>
		</ul><div class="clear"></div></div>
		<table cellpadding="0" cellspacing="0" width="100%" class="tLight noBorderT">
		<thead><tr><td style="width:30px">&nbsp;</td><td><?php _e('მომხმარებელი'); ?></td><td><?php _e('სახელი და გვარი'); ?></td><td><?php _e('ელ-ფოსტა'); ?></td><td><?php _e('კომენტარი'); ?></td><td><?php _e('უფლებები'); ?></td><td style="width:80px"><?php _e('ქმედება'); ?></td></tr></thead>
   <?php
		foreach(sql::getRows("SELECT * FROM cn_adminusers") as $v){
			$icon = '../storage/avatars/'.$v['Id'].'.jpg';
			if(!file_exists($icon)) $icon = 'img/user.jpg';
			echo '<tr>';
			echo '<td><img src="'.$icon.'" width="30" /></td><td>'.$v['user'].'</td><td>'.$v['flname'].'</td><td>'.$v['email'].'<td>'.$v['comment'].'</td>';
			echo '<td>'.has::rolePercent($v['Id']).'%</td>';

			echo '<td class="tableActs">
				<a href="index.php?p=others&act=editusers&Id='.$v['Id'].'" class="tablectrl_small bDefault tipS" title="'.__('რედაქტირება').'"><span class="iconb" data-icon="&#xe1db;"></span></a>
				<a href="#" onclick="return  confirm(\''.__('დარწმუნებული ხართ გსურთ ამ მომხმარებლის წაშლა').'?\')" class="tablectrl_small bDefault tipS" title="'.__('წაშლა').'"><span class="iconb" data-icon="&#xe136;"></span></a>
			</td>
			</tr>';
		}
	echo '	</tbody>
		</table>';
	echo '</div>';
	}
	if($_GET['act'] == 'editusers'){
		if(isset($_POST['editUser'])){
			$pass = ($_POST['pass']!='') ? "`pass`='".md5($_POST['pass'])."'," : "";
			if($_GET['Id']=='-1'){
				sql::update("INSERT INTO `cn_adminusers` SET ".$pass."
						`user` 		= '".$_POST['user']."',
						`flname`	= '".$_POST['flname']."',
						`comment`	= '".$_POST['comment']."',
						`email`		= '".$_POST['email']."',
						`roles`		= '".implode(',',$_POST['roles'])."'");
				redirect("?p=others&act=users&msg=addOk");
			} else {
			sql::update("UPDATE `cn_adminusers` SET ".$pass."
								`user` 		= '".$_POST['user']."',
								`flname`	= '".$_POST['flname']."',
								`comment`	= '".$_POST['comment']."',
								`email`		= '".$_POST['email']."',
								`roles`		= '".implode(',',$_POST['roles'])."'
							WHERE `Id`		= ".$_GET['Id']);
				redirect("?p=others&act=users&msg=updateOk");
			}
		}
		$_USER = sql::getRow("SELECT * FROM cn_adminusers WHERE Id=".$_GET['Id']);
		$_USER['roles'] = explode(',',$_USER['roles']);
	?>
    <form method="post" action="" class="main">
     <fieldset>
    <div class="widget grid9" style="margin-left:auto; margin-right:auto; float:none; width:500px">

	<div class="whead"><h6><?php _e('მომხმარებლის'); ?> <?php echo (($_GET['Id']=='-1')?__('დამატება'):__('რედაქტირება')); ?></h6><div class="clear"></div></div>

	<div class="formRow"><div class="grid3"><label><?php _e('მომხმარებელი'); ?>:</label></div>
	<div class="grid5"><input type="text" name="user" value="<?php echo @$_USER['user']; ?>" placeholder="<?php _e('მომხმარებელი'); ?>" /></div>
	<div class="clear"></div>
	</div>
	<div class="formRow"><div class="grid3"><label><?php _e('სახელი და გვარი'); ?>:</label></div>
	<div class="grid5"><input type="text" name="flname" value="<?php echo @$_USER['flname']; ?>" placeholder="<?php _e('სახელი და გვარი'); ?>" /></div>
	<div class="clear"></div>
	</div>
	<div class="formRow"><div class="grid3"><label><?php _e('ელ-ფოსტა'); ?>:</label></div>
	<div class="grid5"><input type="text" name="email" value="<?php echo @$_USER['email']; ?>" placeholder="<?php _e('ელ-ფოსტა'); ?>" /></div>
	<div class="clear"></div>
	</div>
	<div class="formRow"><div class="grid3"><label><?php _e('თანამდებობა'); ?>:</label></div>
	<div class="grid5"><input type="text" name="comment" value="<?php echo @$_USER['comment']; ?>" placeholder="<?php _e('თანამდებობა'); ?>" /></div>
	<div class="clear"></div>
	</div>
	<div class="formRow"><div class="grid3"><label><?php _e('პაროლი'); ?>:<span style="color:#999;font-style:italic; font-size:10px">(<?php _e('თუ არ გსურთ შეცვლა დატოვეთ ცარიელი'); ?>)</span></label></div>
	<div class="grid5"><input type="password" name="pass" autocomplete="off" value="" placeholder="<?php _e('პაროლი'); ?>" /></div>
	<div class="clear"></div>
	</div>
	<div class="formRow"><div class="grid3"><label><?php _e('უფლებები'); ?>:</label></div>
	<div class="grid5">
    <?php
    	foreach($_CFG['roles'] as $k => $v){
			$checked = (in_array($k,$_USER['roles'])) ? 'checked="checked"' : ''; 
			echo '<label><input type="checkbox" value="'.$k.'" '.$checked.' name="roles[]" />&nbsp;'.$v.'</label><br>';
			if($k=='modules'){
				 foreach(sql::getRows("SELECT * FROM cn_modules") as $m){
					 $checked = (in_array('module:'.$m['sys_name'],$_USER['roles'])) ? 'checked="checked"' : ''; 
					echo nbsp(7).'<label><input type="checkbox" name="roles[]" '.$checked.' value="module:'.$m['sys_name'].'" />&nbsp;'.$m['name'].'</label><br>';
				 }
//					 $checked = (in_array('module:polls',$_USER['roles'])) ? 'checked="checked"' : ''; 
//					echo nbsp(7).'<label><input type="checkbox" name="roles[]" '.$checked.' value="module:polls" />&nbsp;'.__('გამოკითხვა').'</label><br>';
			}
		}
	?>
    </div>
	<div class="clear"></div>
	</div>
	<div class="formRow"><div class="grid3"><label>&nbsp;</label></div>
	<div class="grid5"><input type="submit" value="<?php _e('შენახვა'); ?>" class="buttonS bLightBlue" name="editUser" /></div>
	<div class="clear"></div>
	</div>
  </fieldset>
</form>	
<style type="text/css">
.formRow [class*="grid"] > label { float:none; display:inline }
</style>
    <?php
	}
?> 
</div>


