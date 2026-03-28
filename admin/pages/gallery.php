<?php
if (!defined('FILE_SECURITY_KEY'))  die();
if(!has::role('gallery'))  		  die();

$albums = sql::getRows("SELECT * FROM `cn_albums`");
$_GET['album_id'] = (isset($_GET['album_id'])) ? $_GET['album_id'] : $albums[0]['cat_id'];

$albumini = sql::getElem("SELECT `params` FROM `cn_albums` WHERE `cat_id`=".$_GET['album_id']);
$_ALBUM['params'] = parse_params($albumini);


if(isset($_POST['photoOrds'])){
	ob_clean();
	$_POST['photoOrds'] = explode(',',$_POST['photoOrds']);
	foreach($_POST['photoOrds'] as $k => $v){
		sql::update("UPDATE `cn_albums_photos` SET `ord`=".$k." WHERE `cat_id`=".$v);
	}
	die('ok');
}

if(isset($_GET['del_albummainphoto'])){
	ob_clean();
	$mainphoto = sql::getElem("SELECT `mainphoto` FORM `cn_albums` WHERE `cat_id`=".$_GET['album_id']);
	@unlink('../storage/albums/'.$_GET['album_id'].'/'.$mainphoto);
	sql::update("UPDATE `cn_albums` SET mainphoto=NULL WHERE cat_id=".$_GET['album_id']);
	redirect('?p=gallery&album_id='.$_GET['album_id']);
}

if(isset($_GET['saveAlbumName'])){
	ob_clean();
	foreach($_POST as $k => $v){
		$e = explode('_',$k);
		if($e[0]=='albumtitle'){
			sql::update("UPDATE `cn_albums` SET `name`='".$v."' WHERE `lang`='".$e[1]."' AND cat_id=".$_GET['album_id']);
		}
	}
	die('ok');
}

if(isset($_GET['uploadscript'])){
	ob_clean();
	if (!empty($_FILES)) {
		$ext = end(explode('.',$_FILES['Filedata']['name']));
		$targetFolder = '/storage/albums/'.$_POST['album_id']; // Relative to the root
		$tempFile = $_FILES['Filedata']['tmp_name'];
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
		if($_POST['type']=='albummainphoto'){
			$newfilename = 'mainphoto_'.date('Ymdhis').'.'.$ext;
		} else {
			if(!file_exists(rtrim($targetPath,'/') . '/'.$_FILES['Filedata']['name'])){
				$newfilename = $_FILES['Filedata']['name'];
			} else {
				$newfilename = date('Ymdhis').'_'.$_FILES['Filedata']['name'];			
			}
		}
		$newfilename = strtr($newfilename,array(','=>'_',' '=>'_'));
		$targetFile  = rtrim($targetPath,'/') . '/'.$newfilename;
		$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
		$fileParts = pathinfo($_FILES['Filedata']['name']);
		
		if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
			move_uploaded_file($tempFile,$targetFile);
			if($_POST['type']=='albummainphoto'){
				sql::update("UPDATE `cn_albums` SET `mainphoto`='".$newfilename."' WHERE `cat_id`=".$_POST['album_id']);
				image::resize($newfilename,$newfilename,
								'..'.$targetFolder.'/',
								$_ALBUM['params']['albums_thumb_w'],
								$_ALBUM['params']['albums_thumb_h']);
			}
			if($_POST['type']=='albumphotos'){
				$cat_id = sql::getElem("SELECT max(cat_id) FROM `cn_albums_photos`");
				$cat_id = (isset($cat_id)) ? ($cat_id+1) : 1;
				foreach($_get_langs as $l => $ln){
					sql::insert("INSERT INTO `cn_albums_photos` SET
													`cat_id` = '".$cat_id."',
													`ord` 	 = '".$cat_id."',
													`album_id` = '".$_POST['album_id']."',
													`file` = '".$newfilename."',
													`lang` = '".$l."'");
				}
				image::resize($newfilename,'thumb_'.$newfilename,
								'..'.$targetFolder.'/',
								$_ALBUM['params']['photo_thumb_w'],
								$_ALBUM['params']['photo_thumb_h']);
				image::resize($newfilename,$newfilename,
								'..'.$targetFolder.'/',
								$_ALBUM['params']['photo_w'],
								$_ALBUM['params']['photo_h']);
			}			
			echo 'ok';
		} else {
			echo 'Invalid file type.';
		}
	}
	die();
}

if(isset($_POST['photoCropSubmit'])){
	ob_clean();
	image::crop($_POST['photoCropSubmit'], $_POST['x'], $_POST['y'], $_POST['w'], $_POST['h']);
	die('ok');
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
				alert("გთხოვთ მონიშნეთ სურათზე სასურველი ამოსაჭრელი ზონა.");
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
							console.log(\''.$_GET['type'].'\');
							reloadPhotos(\''.$_GET['type'].'\');
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

if(isset($_GET['del_cat_id'])){
	$filename = sql::getElem("SELECT * FROM `cn_albums_photos` WHERE album_id='".$_GET['album_id']."' AND cat_id='".$_GET['del_cat_id']."'");
	@unlink('../storage/albums/'.$_GET['album_id'].'/'.$filename);
	@unlink('../storage/albums/'.$_GET['album_id'].'/thumb_'.$filename);
	sql::delete("DELETE FROM `cn_albums_photos` WHERE cat_id='".$_GET['del_cat_id']."'");
	redirect('?p=gallery&album_id='.$_GET['album_id']);
}

if(isset($_POST['set_photo_name'])){
	ob_clean();
	foreach($_POST['vals'] as $lang => $v){
		sql::insert("UPDATE `cn_albums_photos` SET
								 `title`='".$v."' WHERE
								 `cat_id`='".$_POST['set_photo_name']."' AND `lang`='".$lang."'");
	}
	die('ok');
}


if(isset($_POST['add_new_album'])){
	ob_clean();
	$new_cat_id = sql::getElem("SELECT max(cat_id) FROM `cn_albums`");
	$new_cat_id = (is_numeric($new_cat_id)) ? ($new_cat_id+1) : 1;
	foreach($_POST['vals'] as $lang => $v){
		sql::insert("INSERT INTO `cn_albums` SET
								 `cat_id`='".$new_cat_id."',
								 `name`='".$v."',
								 `lang`='".$lang."'");
	}
	die('ok');
}

if(isset($_GET['uploadform'])){
	ob_clean();
	echo "<div style=\"width:400px; height:200px\">
		<form>
		<div id=\"queue\"></div>
		<input id=\"file_upload\" name=\"file_upload\" type=\"file\" multiple=\"true\">
	</form></div>
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
</style>
	<script type=\"text/javascript\">
		$(function() {
			$('#file_upload').uploadify({
				'formData'     : {
					'album_id' : '".$_GET['album_id']."',
					'type'     : '".$_GET['type']."'
				},
				'swf'      : 'img/uploadify.swf',
				'buttonImage' : 'img/browse-btn.png',
				'uploader' : '?p=gallery&album_id=1&uploadscript',
				'onQueueComplete'	: function(queueData) {
					reloadPhotos('".$_GET['type']."');
					$.fancybox.close();
				}
			});
		});
	</script>";
	//'fileTypeDesc' : 'Image Files',
//		        'fileTypeExts' : '*.jpg',
	die();
}


if(isset($_GET['createalbum'])){
	ob_clean();
	echo '
		<form action="" method="POST" onsubmit="return false">
		<input type="hidden" name="add_new_album" id="add_new_album" value="1">
		<div class="widget" style="margin:0">
            <table cellpadding="0" cellspacing="0" width="100%" class="tDark">';
	echo '<thead><tr>';
	foreach($_get_langs as $v) echo '<td>'.$v.'</td>';
	echo '</tr></thead><tbody>';
		echo '<tr>';
		foreach($_get_langs as $l => $ln) echo '<td style="padding:0"><div class="formRow" style="border:none; padding:4px 16px;"><span class="grid6"><input type="text" name="vals['.$l.']" value=""></span><div class="clear"></div></div></td>';
		echo '</tr>';
		echo '</tbody><tfoot><tr>';
		echo '<td colspan="'.count($_get_langs).'" style="text-align:center"><a href="javascript:void(0)" title="" onclick="savethis()" class="sideB bLightBlue">შენახვა</a></td>';
		echo '</tr></tfoot>';
	echo '</table></div></form>
	<script type="text/javascript">
	function savethis(){
		$.post("'.full_url().'",$(\'.fancybox-inner form\').serializeArray(),function(data){
			document.location.reload();
		})
	}
	</script>';
	die();
}

if(isset($_GET['photonamer'])){
	ob_clean();
	$photo = sql::getElem("SELECT `file` FROM `cn_albums_photos` WHERE `cat_id`=".$_GET['photo_cat_id']);
	$photo = '../storage/albums/'.$_GET['album_id'].'/thumb_'.$photo;
	echo '
		<center>
		<div class="gallery">
                        <ul>
                        <li style="height:120px">
                            <div style="background:url('.$photo.') no-repeat center center; width:180px; height:120px"></div>
							</li>
							</ul>
							</div>
		</center>
		<form action="" method="POST" onsubmit="return false">
		<input type="hidden" name="set_photo_name" id="set_photo_name" value="'.$_GET['photo_cat_id'].'">
		<div class="widget" style="margin:0; width:500px">
            <table cellpadding="0" cellspacing="0" width="100%" class="tDark">';
	echo '<thead><tr>';
	foreach($_get_langs as $v) echo '<td>'.$v.'</td>';
	echo '</tr></thead><tbody>';
		echo '<tr>';
		foreach($_get_langs as $l => $ln) {
			$phototitle = sql::getElem("SELECT `title` FROM `cn_albums_photos` WHERE `lang`='".$l."' AND `cat_id`=".$_GET['photo_cat_id']);
			echo '<td style="padding:0"><div class="formRow" style="border:none; padding:4px 16px;"><span class="grid12"><input type="text" name="vals['.$l.']" value="'.$phototitle.'"></span><div class="clear"></div></div></td>';
		}
		echo '</tr>';
		echo '</tbody><tfoot><tr>';
		echo '<td colspan="'.count($_get_langs).'" style="text-align:center"><a href="javascript:void(0)" title="" onclick="savethis()" class="sideB bLightBlue">შენახვა</a></td>';
		echo '</tr></tfoot>';
	echo '</table></div></form>
	<script type="text/javascript">
	function savethis(){
		$.post("'.full_url().'",$(\'.fancybox-inner form\').serializeArray(),function(data){
			reloadPhotos(\''.$_GET['type'].'\');
			$.fancybox.close();
		})
	}
	</script>';
	die();
}

?>
    <!-- Secondary nav -->
    <div class="secNav">
        <div class="secWrapper">
            <div class="secTop">
                <div class="balance">
                    <div class="balInfo">ალბომები<span><?php echo sql::cnt("SELECT * FROM `cn_albums` WHERE lang='".$_ADM_CONTLNG."'"); ?> ალბომი</span></div>
                </div>
                <a href="javascript:void(0)" onclick="openeditor()" class="triangle-red"></a>
          </div>
            <div class="divider" style="margin-top:0"><span></span></div>            
          <!-- Tabs container -->
            <div id="tab-container" class="tab-container">
              <div id="general">
              <center>
              	<select class="setcontentlang select" id="setcontentlang"><?php foreach($_get_langs as $l => $ln) echo '<option '.((@$_ADM_CONTLNG==$l)?'selected="selected"':'').' value="'.$l.'">'.$ln.'</option>'; ?></select><br><br>
              </center>
                    <ul class="subNav">
						<?php
							$galls = sql::getRows("SELECT * FROM `cn_albums` WHERE `lang`='".$_ADM_CONTLNG."'");
                            foreach($galls as $v){
								$thisclass = (($v['cat_id']==$_GET['album_id'])?'class="this"':'');
								echo '<li>';
								echo '<a href="?p=gallery&album_id='.$v['cat_id'].'" '.$thisclass.' title="">';
								echo '<span class="icos-cog2"></span>'.$v['name'].'</a>';
								echo '</li>';
                            }
                        ?>
                    </ul>
                </div>
                
          </div>
            <div class="divider"><span></span></div>
            <div class="widget">
                <div class="whead"><h6>ალბომის პარამეტრები</h6>
                <div class="clear"></div></div>
                <div class="body fluid">
                <h6>ალბომის მთავარი ხედი</h6>
					<div class="formRow" style="margin-top:5px">
                        <div class="grid6"><label style="font-family:Tahoma">width x height:</label></div>
                        <div class="grid3"><?php echo @$_ALBUM['params']['albums_thumb_w']; ?></div>
                        <div class="grid3"><?php echo @$_ALBUM['params']['albums_thumb_h']; ?></div>
                    </div>
					<div style="clear:both"></div>
                    <div style="border-top:1px solid #d5d5d5; margin-top:5px"></div>
                       <h6>სურათების ხედი</h6>
					<div class="formRow" style="margin-top:5px">
                        <div class="grid6"><label style="font-family:Tahoma">width x height:</label></div>
                        <div class="grid3"><?php echo @$_ALBUM['params']['photo_thumb_w']; ?></div>
                        <div class="grid3"><?php echo @$_ALBUM['params']['photo_thumb_h']; ?></div>
                    </div>
					<div style="clear:both"></div>
                    <div style="border-top:1px solid #d5d5d5; margin-top:5px"></div>
                       <h6>სურათების ზომა</h6>
					<div class="formRow" style="margin-top:5px">
                        <div class="grid6"><label style="font-family:Tahoma">width x height:</label></div>
                        <div class="grid3"><?php echo @$_ALBUM['params']['photo_w']; ?></div>
                        <div class="grid3"><?php echo @$_ALBUM['params']['photo_h']; ?></div>
                    </div>
                    <div style="clear:both"></div>
                </div>
            </div>
          <?php		  	
			if(is::imagick()){
				//echo '<div class="nNote nSuccess" style="margin:15px 0 15px 0;"><p>ალბომი იყენებს<br>Image Magick ბიბლიოთეკას</p></div>';
			} else {
				//echo '<div class="nNote nWarning" style="margin:15px 0 15px 0;"><p>ალბომი იყენებს<br>GD2 ბიბლიოთეკას</p></div>';
			}
	 ?>
      </div> 
       <div class="clear"></div>
    <script type="text/javascript">
	function savethis(){
		$.post("<?php echo full_url(); ?>",$('#albumparams').serializeArray(),function(data){
			document.location.reload();
		})
	}
	</script>
   </div>
</div>
<!-- Sidebar ends -->

<?php 
	if(isset($_GET['album_id'])){
?>
<!-- Content begins -->   
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-link"></span>ალბომი &raquo; <?php echo albums::getName($_GET['album_id'],$_ADM_CONTLNG); ?></span>
    </div>
    <div class="breadLine"></div>    
    <!-- Main content -->    
    <div style="width:98%; margin-left:auto; margin-right:auto">
	<?php
		$mod_up_dir = '../storage/albums/'.$_GET['album_id'];
		if(!is_dir($mod_up_dir)) {
			echo '<div class="nNote nFailure"><p>
					<b>შეცდომა:</b> <b>storage/albums</b>-ში საქაღალდე <b>'.$_GET['album_id'].'</b> ვერ ვიპოვე.
				</p></div>';
		} else {
			$perms = substr(sprintf('%o', fileperms($mod_up_dir)), -4);
			if($perms!='0777')
			echo '<div class="nNote nFailure"><p>
					<b>შეცდომა:</b> <b>storage/albums</b>-ში საქაღალდე <b>'.$_GET['album_id'].'</b>-ს ააქვს უფლება <b>'.$perms.'</b>. სჭირდება <b>0777</b>
				</p></div>';
		}
     ?>
     </div>
    <div class="fluid" style="margin-left:10px; width:98%">
        <div class="widget" style="margin-top:15px">
        <div class="whead"><h6>მთავარი სურათი და სათაური</h6><ul class="titleToolbar">
            <li><a  onclick="openuploader('albummainphoto')" title="ალბომის ქოვერის ატვირთვა" class="tipE" href="javascript:void(0)">ატვირთვა</a></li>
        </ul>
		<div class="clear"></div></div>
        	<div style="padding:15px">
	        	<div id="albummainphoto">
                	<?php if(isset($_GET['reloadalbummainphoto'])) ob_clean();
						$mainphoto = albums::getMainPhoto($_GET['album_id'],$_ADM_CONTLNG);
						$albummainphoto = '../storage/albums/'.$_GET['album_id'].'/'.$mainphoto; 
						$hasmainphoto = false;
						if(file_exists($albummainphoto) && @$mainphoto!=''){
							$hasmainphoto = true;
							$isequalsize = image::isequalsize($albummainphoto,$_ALBUM['params']['albums_thumb_w'],$_ALBUM['params']['albums_thumb_h']);
							if($isequalsize===false) $needcrop = '<div class="thumb-inline-comment">ხედი საჭიროებს კორექციას</div>';
						} else {
							$hasmainphoto = false;
						}
					?>
                    <table style="margin-left:auto; margin-right:auto">
                    <tr>
                    <td valign="top" style="vertical-align:top">
                    <div class="gallery">
                        <ul>
                        <li style="height:<?php echo @$_ALBUM['params']['albums_thumb_h']; ?>px">
                            <div style="background:url(<?php echo $albummainphoto; ?>) no-repeat; width:<?php echo @$_ALBUM['params']['albums_thumb_w']; ?>px; height:<?php echo @$_ALBUM['params']['albums_thumb_h']; ?>px"><?php echo @$needcrop; ?></div>
                        </span>
                        <?php if($hasmainphoto===true) { ?>
                            <div class="actions" style="width:<?php echo @$_ALBUM['params']['albums_thumb_w']; ?>px; height:<?php echo @$_ALBUM['params']['albums_thumb_h']; ?>px">
                                <a href="javascript:void(0)" onclick="opencropper('albummainphoto','<?php echo $albummainphoto; ?>','<?php echo @$_ALBUM['params']['albums_thumb_w']; ?>', '<?php echo @$_ALBUM['params']['albums_thumb_h']; ?>')" title="" class="edit"><img src="img/icons/crop-thumb.png" alt="" /></a>
                                <a href="?p=gallery&album_id=<?php echo $_GET['album_id']; ?>&del_albummainphoto" onclick="return confirm('დარწმუნებული ხართ გსურთ ალბომის მთავარი სურათის წაშლა?')" title="წაშლა" class="remove tipS"><img src="img/icons/delete.png" alt="" /></a>
                            </div>
                        <?php } ?>
                        </li>
                        </ul>
                    </div>
                    </td>
                    <td valign="top">
                    <?php
                    	foreach($_get_langs as $l => $ln) {
							$name = sql::getElem("SELECT `name` FROM `cn_albums` WHERE cat_id=".$_GET['album_id']." AND lang='".$l."'");
							echo '<div class="formRow" style="width:300px"><span class="grid12"><input type="text" class="albumname" name="albumtitle_'.$l.'" value="'.$name.'" placeholder="'.$ln.'"></span><div class="clear"></div></div>';
						}
                    ?>
						<div class="formRow" style="width:100px;border-bottom:none"><span class="grid12" style="margin-left:100px"><div class="sidePad"><a href="#" onclick="saveAlbumName()" class="sideB bLightBlue">შენახვა</a></div></span><div class="clear"></div></div>
	                    </td>
                    </tr></table>
                    <?php if(isset($_GET['reloadalbummainphoto'])) die(); ?>
                </div>
                <div style="float:left; padding-left:10px">
                </div>
              <div style="clear:both"></div>
  			</div>
        </div>
    </div>


    <div class="fluid" style="margin-left:10px; width:98%">
        <div class="widget" style="margin-top:15px;">
        <div class="whead"><h6>ალბომის კონტენტი</h6>
        <ul class="titleToolbar">
            <li><a id="saveButton" style="display:none" title="სურათების განლაგების შენახვა" href="javascript:void(0)" class="tipS">შენახვა</a></li>
            <li><a onclick="openuploader('albumphotos')" title="ალბომის სურათების ატვირთვა" class="tipE" href="javascript:void(0)">ატვირთვა</a></li>
        </ul>
		<div class="clear"></div>
         </div>
        	    <!-- start photothumb -->
                <ul id="albumphotos" class="gallery">
                <?php
				if(isset($_GET['reloadalbumphotos'])) ob_clean();
				$photos = sql::getRows('SELECT * FROM `cn_albums_photos`
												WHERE album_id='.$_GET['album_id']." AND lang='".$_ADM_CONTLNG."' ORDER BY `ord`");
				foreach($photos as $v) {
					$alboringumphoto = '../storage/albums/'.$v['album_id'].'/'.$v['file']; 
					$albumphoto = '../storage/albums/'.$v['album_id'].'/thumb_'.$v['file']; 
					$isequalsizet = image::isequalsize($albumphoto,$_ALBUM['params']['photo_thumb_w'],$_ALBUM['params']['photo_thumb_h']);
					$isequalsizem = image::isequalsize($alboringumphoto,$_ALBUM['params']['photo_w'],$_ALBUM['params']['photo_h']);
					$needcrop = ''; $nc2 = array();
					if($isequalsizet===false) $nc2[] = 'ხედი';
					if($isequalsizem===false) $nc2[] = 'სურათი';
					if(count($nc2)>0) $needcrop = '<div class="thumb-inline-comment">'.implode(' და ',$nc2).' საჭიროებს კორექციას</div>';

				echo '<li photo_cat_id="'.$v['cat_id'].'"><div class="imgdiv" style="background-image:url('.$albumphoto.')">'.$needcrop;
				?>
                    <div class="actions" style="width:<?php echo @$_ALBUM['params']['photo_thumb_w']; ?>px; height:<?php echo @$_ALBUM['params']['photo_thumb_h']; ?>px">
                    <div class="tools" style="width:<?php echo @$_ALBUM['params']['photo_thumb_w']+8; ?>px; margin-left:-5px">
                        <a href="javascript:void(0)" onclick="opennamer('albumphotos','<?php echo $v['cat_id']; ?>')" class="edit"><img src="img/icons/title.png" alt="" title="სახელის დარქმევა" class="tipS" /></a>
                        <a href="<?php echo $alboringumphoto.'?'.date('Ymdhis'); ?>" title="<?php echo $v['title']; ?>" class="fancybox" rel="gallery<?php echo $_GET['album_id']; ?>"><img src="img/icons/view.png" alt="" title="ნახვა" class="tipS" /></a>
                        <a href="javascript:void(0)" onclick="opencropper('albumphotos','<?php echo $alboringumphoto; ?>','<?php echo @$_ALBUM['params']['photo_w']; ?>', '<?php echo @$_ALBUM['params']['photo_h']; ?>')" class="edit"><img src="img/icons/crop-main.png" alt="" title="სრული სურათის მოჭრა" class="tipS" /></a>
                        <a href="javascript:void(0)" onclick="opencropper('albumphotos','<?php echo $albumphoto; ?>','<?php echo @$_ALBUM['params']['photo_thumb_w']; ?>', '<?php echo @$_ALBUM['params']['photo_thumb_h']; ?>')" class="edit"><img src="img/icons/crop-thumb.png" alt="" title="ხედის მოჭრა" class="tipS" /></a>
                        <a href="?p=gallery&album_id=<?php echo $_GET['album_id']; ?>&del_cat_id=<?php echo $v['cat_id']; ?>" class="remove" onclick="return confirm('დარწმუნებული ხართ გსურთ ამ სურათის წაშლა?')"><img src="img/icons/delete.png" title="წაშლა" class="tipS" alt="" /></a>
                        </div>
                     <a href="javascript:void(0)" style="cursor:move" class="draga"><img src="img/icons/move.png" alt="" title="გადაადგილება" class="tipS" /></a>
                    </div>
                <?php
					echo '</div>';
					if(trim($v['title'])=='') {
						echo '<div class="thumb-inline-comment" style="text-align:center">დაუსათაურებელი</div>';
					} else {
						echo '<div class="thumb-inline-comment-ok" style="text-align:center">'.$v['title'].'</div>';					
					}
					echo '</li>';
					}
				if(isset($_GET['reloadalbumphotos'])) die();
				?>
                </ul>
                <div style="clear:both"></div> <br/><br/>
                </span>
                <!-- end photothumb -->
			<div style="clear:both"></div>
        </div>
    </div>
	<div style="clear:both"></div>
<?php } ?>
<link href="js_css/uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js_css/plugins/others/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="js_css/plugins/others/jquery.Jcrop.min.js"></script>
<style type="text/css">
		#albumphotos{ list-style-type:none; margin:0px;  }
		#albumphotos li{
				float:left;
				width:<?php echo @$_ALBUM['params']['photo_thumb_w']; ?>px;
				height:<?php echo @$_ALBUM['params']['photo_thumb_h']+4; ?>px;
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
		#albumphotos div.imgdiv { width:<?php echo @$_ALBUM['params']['photo_thumb_w']; ?>px; height:<?php echo @$_ALBUM['params']['photo_thumb_h']; ?>px }
		.placeHolder div { background-color:white !important; border:dashed 1px gray !important;height:<?php echo @$_ALBUM['params']['photo_thumb_h']; ?>px }
	</style>
<script type="text/javascript">

//$("ul:first").dragsort();

$("#albumphotos").dragsort({ dragSelector: "a.draga", dragBetween: true, dragEnd: saveShow, placeHolderTemplate: "<li class='placeHolder'><div></div></li>" });
		
function saveShow() {
	$('#saveButton').show();
};

function saveAlbumName(){
	$.post("<?php echo full_url(); ?>&saveAlbumName",$('.albumname').serializeArray(),function(data){
		if(data=='ok') reloadPhotos('albummainphoto');
	});
}

$('#saveButton').click(function(){
	var ord = $("#albumphotos>li").map(function() { return $(this).attr('photo_cat_id'); }).get().join();
	$.post("<?php echo full_url(); ?>",{photoOrds:ord},function(data){
		if(data=='ok') $('#saveButton').hide();
	});
});

function openeditor(){
	$.fancybox.open({'type':'ajax', 'autoWidth':true, 'href':'?p=gallery&createalbum'});
}

function opennamer(type,cat_id){
	url = [];
	url.push('?p=gallery');
	url.push('album_id=<?php echo $_GET['album_id']; ?>');
	url.push('photonamer');
	url.push('photo_cat_id='+cat_id);
	url.push('type='+type);
	$.fancybox.open({'type':'ajax', 'autoWidth':true, 'href':url.join('&')});
}

function opencropper(type,photo,thumb_w,thumb_h){
	photo = encodeURIComponent(photo);
	url = [];
	url.push('?p=gallery');
	url.push('album_id=<?php echo $_GET['album_id']; ?>');
	url.push('photoeditor');
	url.push('photo='+photo);
	url.push('type='+type);
	url.push('thumb_w='+thumb_w);
	url.push('thumb_h='+thumb_h);
	$.fancybox.open({'type':'ajax', 'autoWidth':true, 'href':url.join('&')});

}

function openuploader(type){
	url = [];
	url.push('?p=gallery');
	url.push('album_id=<?php echo $_GET['album_id']; ?>');
	url.push('uploadform');
	url.push('type='+type);
	$.fancybox.open({'type':'ajax', 'autoWidth':true, 'href':url.join('&')});
}
function reloadPhotos(type){
	$.post("<?php echo full_url(); ?>&reload"+type, {'a':'a'},function(html){
		$('#'+type).html(html);
		$(".gallery ul li").hover(
			function() { $(this).children(".actions").show("fade", 200); },
			function() { $(this).children(".actions").hide("fade", 200); }
		);
	})
}

$(".fancybox").fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
});
    
</script>
