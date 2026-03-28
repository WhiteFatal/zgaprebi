<?php
if (!defined('FILE_SECURITY_KEY'))  die();

if ( isset($_POST['editUser'])) {
		$oldp = sql::cnt("SELECT * FROM `cn_adminusers` WHERE `Id`='".$_SESSION['userId']."' AND `pass`='".md5($_POST['oldpass'])."'");
		if(@$oldp!=1) redirect('?p=profile&msg=erroroldpass');
		if($_POST['pass']!=$_POST['pass2']) redirect('?p=profile&msg=mismatchpass');
		sql::update("UPDATE `cn_adminusers` SET `pass`='".md5($_POST['pass'])."'  WHERE `Id`='".$_SESSION['userId']."'");
		$row = sql::getRow("SELECT * FROM `cn_adminusers` WHERE `Id`=".$_SESSION['userId']);
	adminLog(__('საკუთარი მომხმარებლის').' ('.$_SESSION['user'].') '.__('პაროლის რედაქტირება'));
	redirect('?p=profile&msg=changesaved');
} 


if(isset($_GET['uploadscript'])){
	ob_clean();
	if (!empty($_FILES)) {
		$ext = end(explode('.',$_FILES['Filedata']['name']));
		$targetFolder = '/storage/avatars/'; // Relative to the root
		$tempFile = $_FILES['Filedata']['tmp_name'];
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
		$newfilename = $_SESSION['userId'].'.jpg';
		$targetFile  = rtrim($targetPath,'/') . '/'.$newfilename;
		// Validate the file type
		$fileTypes = array('jpg'); // File extensions
		$fileParts = pathinfo($_FILES['Filedata']['name']);	
		if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
			@unlink($targetFile);
			move_uploaded_file($tempFile,$targetFile);		
			@chmod($targetFile,0777);
			echo 'ok';
		} else {
			echo 'Invalid file type.';
		}
	}
	die();
}


?>
<!-- Secondary nav -->    
</div>
<!-- Sidebar ends -->

<!-- Content begins -->   
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-link"></span><?php _e('პროფილი'); ?></span>
        <div class="clear"></div>
    </div>
    <div class="breadLine"></div>    
    <!-- Main content -->
<center>
<div class="fluid">
<?php
if(@$_GET['msg']=='erroroldpass') echo '<div class="nNote nFailure" style="width:600px"><p><b>'.__('შეცდომა').'!</b> '.__('არასწორი ძველი პაროლი').'.</p></div>';
if(@$_GET['msg']=='mismatchpass') echo '<div class="nNote nFailure" style="width:600px"><p><b>'.__('შეცდომა').'!</b> '.__('გთხოვთ სწორად გაიმეოროთ ახალი პაროლი').'.</p></div>';
if(@$_GET['msg']=='changesaved') echo '<div class="nNote nSuccess" style="width:600px"><p><b>'.__('გმადლობთ').'!</b> '.__('პაროლი შეიცვალა').'</p></div>';
?>
     <form method="post" action="" class="main">
     <fieldset>
    <div class="widget grid9" style="margin-left:auto; margin-right:auto; float:none; width:500px">
    
    		<?php
            	$userphoto = (file_exists('../storage/avatars/'.$_SESSION['userId'].'.jpg')) ? 
								('../storage/avatars/'.$_SESSION['userId'].'.jpg') :  'img/user.png';
            ?>
         <div class="widget grid9" style="width:110px; position:absolute; margin-left:-130px; margin-top:0">
            <div class="formRow">
            <div class="grid5" style="width:72px; height:77px">
            <div id="queue_1"></div>
	            <input contId="1" id="file_upload_1" bg="<?php echo $userphoto; ?>" type="file" multi="false" name="_E_1" /></div>
            <div class="clear"></div>
            </div>
    </div>

	<div class="whead"><h6><?php _e('პაროლის შეცვლა'); ?></h6><div class="clear"></div></div>
	<div class="formRow"><div class="grid3"><label><?php _e('ძველი პაროლი'); ?></label></div>
	<div class="grid5"><input type="password" name="oldpass" value="" placeholder="<?php _e('პაროლი'); ?>" /></div>
	<div class="clear"></div>
	</div>
	<div class="formRow"><div class="grid3"><label><?php _e('ახალი პაროლი'); ?></label></div>
	<div class="grid5"><input type="password" name="pass" value="" placeholder="<?php _e('პაროლი'); ?>" /></div>
	<div class="clear"></div>
	</div>
	<div class="formRow"><div class="grid3"><label><?php _e('გაიმეორეთ ახალი პაროლი'); ?></label></div>
	<div class="grid5"><input type="password" name="pass2" value="" placeholder="<?php _e('გაიმეორეთ პაროლი'); ?>" /></div>
	<div class="clear"></div>
	</div>
	<div class="formRow"><div class="grid3"><label>&nbsp;</label></div>
	<div class="grid5"><input type="submit" value="<?php _e('შენახვა'); ?>" class="buttonS bLightBlue" name="editUser" /></div>
	<div class="clear"></div>
	</div>
  </fieldset>
</form>	
</div>
</center>

<style type="text/css">
.fluid .grid3{ width:200px }
.fluid .grid5{ width:200px; }
.uploadify{ height:60px; }
.uploadify-button { border:solid 1px #999999 }
.uploadify-queue .cancel, .uploadify-queue .fileName, .uploadify-queue .uploadify-progress{ display:none }
.uploadify-queue .uploadify-queue-item { width:70px }
</style>

<link href="js_css/uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js_css/plugins/others/jquery.uploadify.min.js"></script>
<script type="text/javascript">
$('#file_upload_1').uploadify({
	swf	      : 'img/uploadify.swf',
	width	  : 72,
	height	  : 70,
	multi     : false,
	fileTypeDesc : 'Only JPG Files',
    fileTypeExts : '*.jpg',
	buttonImage : $('#file_upload_1').attr('bg'),
	uploader : '<?php echo full_url(); ?>&uploadscript',
	onQueueComplete	: function(queueData) {
		document.location.href="<?php echo full_url(); ?>";
	}
});

</script>

