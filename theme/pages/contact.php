<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	$is_logined = is_logined();
	
	$popular_tales = sql::getRows("SELECT `cat_Id`, `title`, `cover`, `color`, `category`, `price_four`, `duration` FROM `_cn_mod_tales` WHERE `active` = 1 ORDER BY `views` DESC LIMIT 18");
	
	global $_CFG;
	
	if(@$_POST['contact']==1){
		ob_clean();

		// reCAPTCHA v3 verification
		$recaptcha_token = @$_POST['recaptcha_token'];
		if(empty($recaptcha_token)){
			$status = array('res'=>'error','txt'=>__('reCAPTCHA შემოწმება ვერ მოხერხდა!'));
			echo json_encode($status); die();
		}

		$recaptcha_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.RECAPTCHA_SECRET.'&response='.$recaptcha_token.'&remoteip='.$_SERVER['REMOTE_ADDR']);
		$recaptcha_data = json_decode($recaptcha_response, true);

		$is_localhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

		if(!$is_localhost){
			if(!$recaptcha_data['success'] || $recaptcha_data['score'] < 0.5){
				$status = array('res'=>'error','txt'=>__('სპამის დაცვის შემოწმება ვერ გაიარა!'));
				echo json_encode($status); die();
			}
		}
		
		if(trim(@$_POST['flname'])==''){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ სახელი და გვარი!'));
			echo json_encode($status); die();
		}
		
		if(trim(@$_POST['email'])==''){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ ელ. ფოსტა!'));
			echo json_encode($status); die();
		}
		
		if(!filter_var(@$_POST['email'], FILTER_VALIDATE_EMAIL)){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ სწორი ელ. ფოსტა!'));
			echo json_encode($status); die();
		}
		
		if(trim(@$_POST['subject'])==''){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ სათაური!'));
			echo json_encode($status); die();
		}
		
		if(trim(@$_POST['message'])==''){
			$status = array('res'=>'error','txt'=>__('ჩაწერეთ ტექსტი!'));
			echo json_encode($status); die();
		}
		
		$message = file_get_contents("theme/tpl/contact.tpl");
		
		$message = strtr($message,array(
			'{flname}'	=>	$_POST['flname'],
			'{email}'	=>	$_POST['email'],
			'{subject}'	=>	$_POST['subject'],
			'{datetime}'=>	date('Y-m-d H:i:s'),
			'{ip}'		=>	$_SERVER['REMOTE_ADDR'],
			'{message}'	=>	$_POST['message']
		));
		
		smtpmail::send($_CFG['adminEmail'],__('საკონტაქტო ფორმა').' - '.$_CFG['siteName'],$message);
		
		$status = array('res'=>'success','txt'=>__('თქვენი შეტყობინება წარმატებით გაიგზავნა!'));
		echo json_encode($status); die();
	}
?>
<ogmeta>
	<title><?php echo $_GET['ptitle'].' | '.$_CFG['siteName']; ?></title>
</ogmeta>

<!-- reCAPTCHA v3 -->
<script src="https://www.google.com/recaptcha/api.js?render=6LfOBJ0sAAAAABdctjr0j5vFv3up0pJoMw5vEZCz"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('contact-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        grecaptcha.ready(function() {
            grecaptcha.execute('6LfOBJ0sAAAAABdctjr0j5vFv3up0pJoMw5vEZCz', {action: 'contact'}).then(function(token) {
                document.getElementById('recaptcha_token').value = token;
                form.submit();
            });
        });
    });
});
</script>

<div class="popup">
    <?php
		if($is_logined==false){
			get::block('registration.php');
			get::block('recovery.php');
		}else{
			get::block('balance.php');
			get::block('settings.php');
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
        <?php
			if($is_logined==false){
				get::block('login.php');
			}else{
				get::block('logged.php');
			}
			get::block('search.php');
		?>
        <div class="fb-like" data-href="https://www.facebook.com/zgaprebi.ge" data-layout="box_count" data-action="like" data-show-faces="true" data-share="true"></div>
        <a class="zgaprebi" href="http://zgaprebi.ge">&nbsp;</a>
        <div class="frame_1">&nbsp;</div>
        <div class="stand_with_out_tales">
            <?php echo menu(); ?>
            <span class="currency">&nbsp;</span>
            <a class="filling_balance"<?php echo ($is_logined==false?'onClick="show_popup(\'confirm\',\'ავტორიზაციის შეცდომა\',\'ბალანსის შესავსებად საჭიროა გაიაროთ ავტორიზაცია!\');"':' onClick="show_popup(\'balance\',false,false)"'); ?>><?php _e('ბალანსის შევსება'); ?></a>
        </div>
        <div class="contact_stand">
        	<span class="contact_stand_text"><?php _e('კონტაქტი ადმინისტრაციასთან'); ?></span>
        	<form id="contact-form" action="<?php echo full_url(); ?>" method="post">
            	<input type="hidden" name="contact" value="1"/>
            	<input type="hidden" name="recaptcha_token" id="recaptcha_token" value=""/>
                <div class="inputs">
                    <input type="text" name="flname" autocomplete="off" placeholder="<?php _e('სახელი და გვარი'); ?>" value="<?php echo @$_SESSION['user']['flname']; ?>"/>
                    <input type="text" name="email" autocomplete="off" placeholder="<?php _e('ელ. ფოსტა'); ?>" value="<?php echo @$_SESSION['user']['username']; ?>"/>
                    <input type="text" name="subject" autocomplete="off" placeholder="<?php _e('სათაური'); ?>" value=""/>
                </div>
                <div class="contact_info">
                	<span class="title"><?php _e('შპს ლიტერატურა'); ?></span>
                    <span class="direction"><?php _e('აუდიო ზღაპრები'); ?></span>
                    <div class="block">
                        <span class="title"><?php _e('ტელეფონი'); ?>:</span>
                        <span class="phone"><?php _e('+995 599 33 88 32'); ?></span>
                    </div>
                    <div class="block">
                        <span class="title"><?php _e('ელ. ფოსტა'); ?>:</span>
                        <span class="email"><?php _e('support@zgaprebi.ge'); ?></span>
                    </div>
                    <div class="clb"></div>
                </div>
                <div class="clb"></div>
                <div class="depth">
                	<textarea name="message" placeholder="<?php _e('შეტყობინება'); ?>"></textarea>
                </div>
                <span class="status">&nbsp;</span>
                <input type="submit" value="<?php _e('გაგზავნა'); ?>"/>
                <div class="clb"></div>
            </form>
        </div>
        <div style="margin-top:96px;" class="popular_stand">
        	<div class="frame_2">&nbsp;</div>
            <span class="popular_tale_text"><?php _e('პოპულარული ზღაპრები'); ?></span>
            <ul class="tales">
                <?php
                    if(count($popular_tales)>0){
                        foreach($popular_tales as $v){
							echo '<li style="background:url(/theme/images/left_tale_shadow.png) no-repeat left center,url(/storage/uploads/tales/'.$v['cover'].') no-repeat right center '.$v['color'].';background-size:contain, 100px, 121px;">';
								echo '<span class="hover">&nbsp;</span>';
								if($v['price_four']==0) echo '<span class="free">&nbsp;</span>';
								echo '<span class="category c_'.$v['category'].'">&nbsp;</span>';
								echo '<a class="tooltip" title="<span style=\'display:block;text-align:center;line-height:140%;\'>'.$v['title'].'<br>'.$v['duration'].'<br/>" href="'.get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">&nbsp;</a>';
							echo '</li>';
                        }
                    }
                ?>
            </ul>
            <div class="clb"></div>
        </div>
        <div class="stand_strut_shadow">&nbsp;</div>
    </div>
</div>
<?php get::block('footer.php'); ?>
