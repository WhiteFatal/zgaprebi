<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	
	/*
	$dir = dirname($_SERVER['DOCUMENT_ROOT'])."/public_html/storage/uploads/tales";
	$filename = '150201120016bdbe541191dd.mp3';
	$file = $dir."/".$filename;
	
	$extension = "mp3";
	$mime_type = "audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3";
	
	if(file_exists($file)){
		header('Content-type: {$mime_type}');
		header('Content-length: ' . filesize($file));
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('X-Pad: avoid browser bug');
		header('Cache-Control: no-cache');
		readfile($file);
	}else{
		header("HTTP/1.0 404 Not Found");
	}
	*/
	
	$tale = sql::getRow("SELECT `cat_Id`,`title`,`img`,`audio`, `price_four`, `duration`, `author` FROM `_cn_mod_tales` WHERE `active` = 1 AND `cat_Id` = ".@$_GET['story']);
	
	if(!$tale) redirect(get_link('p=tales'));
	
	$is_logined = is_logined();
	$is_premium = is_premium();
	$is_purchased = check_tale($_GET['story']);
	
	if(isset($_POST['tale_listen'])){
		ob_clean();
		if(!in_array($_GET['story'],$_SESSION['tale_listen'])){
			sql::update("UPDATE `_cn_mod_tales` SET `views` = `views` + 1 WHERE `active` = 1 AND `cat_Id` = ".@$_GET['story']);
			$_SESSION['tale_listen'][] = $_GET['story'];
		}
		die();
	}
?>
<ogmeta>
	<meta property="og:url" content="<?php echo full_url(); ?>" />
    <meta property="og:title" content="<?php echo $tale['title']; ?>" />
    <meta property="og:description" content="ხანგრძლივობა: <?php echo $tale['duration']; ?>" />
    <meta property="og:image" content="http://zgaprebi.ge/storage/uploads/tales/<?php echo $tale['img']; ?>" />
	<title><?php echo $tale['title'].' | '.$_GET['ptitle'].' | '.$_CFG['siteName']; ?></title>
</ogmeta>
<div class="popup">
    <?php
		if($is_logined==true){
			get::block('buy.php');
			get::block('premium.php');
		}
	?>
    <div id="confirm" class="confirm">
        <a class="close" href="javascript:void(0);" onclick="hide_popup('confirm');">&nbsp;</a>
        <h1>&nbsp;</h1>
        <div class="text">&nbsp;</div>
    </div>
</div>
<div class="content">
    <div class="container">
        <?php echo menu(); ?>
        <div class="tale">
            <div style="background:url(/storage/uploads/tales/<?php echo $tale['img']; ?>);" class="img"><span class="title"><?php echo $tale['title']; ?></span></div>
            <div class="player">
            	<?php 
					if($tale['price_four']==0){
						if(file_exists("./storage/uploads/tales/".$tale['audio']) && trim($tale['audio']!='')){
							echo '<audio data-Id="'.$tale['cat_Id'].'" id="audio" preload="none" controls>';
								echo '<source src="/storage/uploads/tales/'.$tale['audio'].'">';
								//echo '<source src="/storage/uploads/tales/'.$tale['audio_ogg'].'">';
                        		//echo '<source src="/storage/uploads/tales/'.$tale['audio_wav'].'">';
							echo '</audio>';
						}
					}else{
						if($is_logined==true){
							if($is_premium==true || $is_purchased==true){
								if(file_exists("./storage/uploads/tales/".$tale['audio']) && trim($tale['audio']!='')){
									echo '<audio data-Id="'.$tale['cat_Id'].'" id="audio" preload="none" controls>';
										echo '<source src="/storage/uploads/tales/'.$tale['audio'].'">';
										//echo '<source src="/storage/uploads/tales/'.$tale['audio_ogg'].'">';
										//echo '<source src="/storage/uploads/tales/'.$tale['audio_wav'].'">';
									echo '</audio>';
								}
							}else{
								echo '<button onClick="show_popup(\'buy\');" type="button">&nbsp;</button>';
							}
						}else{
							echo '<button onClick="show_popup(\'confirm\',\'ავტორიზაციის შეცდომა\',\'ფასიანი ზღაპრის მოსასმენად, პირველ რიგში საჭიროა დაბრუნდეთ მთავარ გვერდზე და გაიაროთ ავტორიზაცია!\');" type="button">&nbsp;</button>';
						}
					}
				 ?>
            </div>
        </div>
        <?php if(trim($tale['author'])!='') echo '<span class="author">'.$tale['author'].'</span>'; ?>
        <div class="fb-like" data-href="<?php echo full_url(); ?>" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
        <div class="clb"></div>
        <h1 class="title"><?php _e('კომენტარები'); ?></h1>
        <div class="comments">
            <div class="box">
                <div class="fb-comments" data-href="<?php echo full_url(); ?>" data-width="660" data-numposts="4" data-colorscheme="light"></div>
            </div>
        </div>
        <div class="candle">&nbsp;</div>
    </div>
</div>