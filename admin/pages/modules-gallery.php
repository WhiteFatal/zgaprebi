<?php
if (!defined('FILE_SECURITY_KEY'))  die();

$mod_up_dir = '../storage/uploads/'.$_GET['mod'];
if(!is_dir($mod_up_dir)) {
	echo '<div style="color:red">
			<b>შეცდომა:</b> <b>storage/uploads/</b>-'.__('ში საქაღალდე').' <b>'.$_GET['mod'].'</b> '._('ვერ ვიპოვე').'.
		</div>';
} else {
	$perms = substr(sprintf('%o', fileperms($mod_up_dir)), -4);
	if($perms!='0777')
	echo '<div style="color:red">
			<b>'.__('შეცდომა').':</b> <b>storage/uploads/</b>-'.__('ში საქაღალდე').' <b>'.$_GET['mod'].'</b>-'.__('ს ააქვს უფლება').' <b>'.$perms.'</b>. '.__('სჭირდება').' <b>0777</b>
		</div>';
}
if(is_numeric($_GET['editId'])) {
	$_tw = @$s['params']['twidth'];
	$_th = @$s['params']['theight'];
	$_w  = @$s['params']['width'];
	$_h  = @$s['params']['height'];	
	echo '<div id="queue_'.$t.'"></div>
			  <input contId="'.$t.'" id="file_upload_'.$t.'" bg="uploads-bg" for="gallery" type="file" multi="true" name="_E_'.$t.'" />';
	if(@$_GET['reload']==$t)  echo '<script type="text/javascript"> init_uploadify(); </script>';
	$photos = $gphotos;
	echo '<ul id="albumphotos" class="gallery">';
		foreach($photos as $v) {
					$alboringumphoto = '../storage/uploads/'.$_GET['mod'].'/'.$v['file'];
					$albumphoto = '../storage/uploads/'.$_GET['mod'].'/thumb_'.$v['file'];
					$albumphotoURL = '../storage/uploads/'.$_GET['mod'].'/thumb_'.rawurlencode($v['file']);
					$isequalsizet = image::isequalsize($albumphoto,$_tw,$_th);
					$isequalsizem = image::isequalsize($alboringumphoto,$_w,$_h);
					$needcrop = ''; $nc2 = array();
					if($isequalsizet===false) $nc2[] = __('ხედი');
					if($isequalsizem===false) $nc2[] = __('სურათი');
					if(count($nc2)>0) $needcrop = '<img src="img/alert.png" style="cursor:help" title="'.implode(' '.__('და').' ',$nc2).' '.__('საჭიროებს კორექციას').'." class="tipS">&nbsp;';

			echo '<li photo_cat_id="'.$v['cat_id'].'" style="width:'.$_tw.'px;height:'.($_th+5).'px; cursor:move" class="draga"><div class="imgdiv" style="background-image:url('.$albumphotoURL.'?'.date('Ymdhis').'); background-repeat:no-repeat; width:'.$_tw.'px;height:'.$_th.'px;">';
			echo '</div>';
			echo '<div style="margin-top:10px"><div class="btn-group dropup" style="display: inline-block; margin-bottom: -4px; float:left">
                        <a class="buttonS bDefault" data-toggle="dropdown" href="#"><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="'.$alboringumphoto.'?'.date('Ymdhis').'" class="fancybox" rel="gallery'.@$_GET['album_id'].'">'.__('ნახვა').'</a></li>';
			if($isequalsizem===false){
 					echo '<li><a href="#" onclick="opencropper(\'albumphotos\',\''.$alboringumphoto.'\',\''.$t.'\',\''.@$_w.'\', \''.@$_h.'\')" class="edit">'.__('სრული სურათის მოჭრა').'</a></li>';
			}
			if($isequalsizet===false){
				echo '<li><a href="#" onclick="opencropper(\'albumphotos\',\''.$albumphoto.'\',\''.$t.'\',\''.@$_tw.'\', \''.@$_th.'\')" class="edit">'.__('ხედის მოჭრა').'</a></li>';
			}
			echo '<li><a href="#" class="remove" onclick="removeGalleryFile(\'?p=modules&mod='.$_GET['mod'].'&editId='.$_GET['editId'].'&field='.$t.'&delphotocatId='.$v['cat_id'].'\',\''.$t.'\')">'.__('წაშლა').'</a></li>
                        </ul>
                    </div>';
			/*
			if(trim($v['title'])=='') {
				echo '<div class="thumb-inline-comment" style="text-align:center; float:left">'.$needcrop.' '.__('უსათაურო').'</div>';
			} else {
				$ct = utf8_substr($v['title'],0,10);
				if(utf8_strlen($ct)<utf8_strlen($v['title'])) $ct = $ct.'...';
				echo '<div class="thumb-inline-comment-ok" style="text-align:center; float:left">'.$needcrop.$ct.'</div>';					
			}
			*/
			echo '</div>';
			echo '</li>';
			}

} else {
		echo '<div style="font-size:10px; font-style:italic">'.__('ფაილის ატვირთვისთვის საჭიროა ჯერ გააკეთოთ შენახვა').'</div>';				
}
echo '</ul>';
?>

<style type="text/css">
.placeHolder div { background-color:white !important; border:dashed 1px gray !important;height:<?php echo @$s['params']['theight']; ?>px }
</style>
<?php
echo '<script type="text/javascript"> init_drag("'.$t.'"); </script>';
?>
<script type="text/javascript">
	$('.tipS').tipsy({gravity: 's',fade: true, html:true});
</script>