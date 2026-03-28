<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	
	global $_CFG;
	
	$is_logined = is_logined();
	
	$tales = sql::getRows("SELECT `cat_Id`, `title`, `cover`, `color`, `category`, `price_four`, `duration` FROM `_cn_mod_tales` WHERE `active` = 1 ORDER BY `Id` DESC LIMIT 15");
	
	$recommend_tales = sql::getRows("SELECT `cat_Id`, `title`, `cover`, `color`, `category`, `price_four`, `duration` FROM `_cn_mod_tales` WHERE `active` = 1 AND `recommend` = '1' ORDER BY `Id` DESC LIMIT 18");
	
	$popular_tales = sql::getRows("SELECT `cat_Id`, `title`, `cover`, `color`, `category`, `price_four`, `duration` FROM `_cn_mod_tales` WHERE `active` = 1 ORDER BY `views` DESC LIMIT 18");
?>
<ogmeta>
	<meta property="og:url" content="<?php echo full_url(); ?>" />
    <meta property="og:title" content="ზღაპრები" />
    <meta property="og:description" content="აუდიო ზღაპრების ბიბლიოთეკა" />
    <meta property="og:image" content="http://zgaprebi.ge/theme/images/logo.png" />
	<title><?php echo $_CFG['siteName']; ?></title>
</ogmeta>
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
        <div class="new_stand">
        	<?php if(@$tales[0]['cat_Id']){ ?>
        	<div class="new_tale" style="background:url(/theme/images/big_left_tale_shadow.png) no-repeat left center,url(/storage/uploads/tales/<?php echo $tales[0]['cover']; ?>) no-repeat right center <?php echo $tales[0]['color']; ?>;">
            	<a class="tooltip" title="<?php echo '<span style=\'display:block;text-align:center;line-height:140%;\'>'.$tales[0]['title'].'<br>'.$tales[0]['duration'].'</span>'; ?>" href="<?php echo get_link('p=tale&story='.$tales[0]['cat_Id'].'&seotitle='.$tales[0]['title']); ?>"><span>&nbsp;</span></a>
            </div>
            <?php } ?>
            <span class="new_tale_text"><?php _e('ახალი ზღაპარი'); ?></span>
            <?php echo menu(); ?>
            <span class="currency">&nbsp;</span>
            <a class="filling_balance"<?php echo ($is_logined==false?'onClick="show_popup(\'confirm\',\'ავტორიზაციის შეცდომა\',\'ბალანსის შესავსებად საჭიროა გაიაროთ ავტორიზაცია!\');"':' onClick="show_popup(\'balance\',false,false)"'); ?>><?php _e('ბალანსის შევსება'); ?></a>
            <ul class="tales">
				<?php
                    if(count($tales)>1){
                        foreach($tales as $k => $v){
                            if($k!=0){
                                echo '<li style="background:url(/theme/images/left_tale_shadow.png) no-repeat left center,url(/storage/uploads/tales/'.$v['cover'].') no-repeat right center '.$v['color'].';background-size:contain, 100px, 121px;">';
                                    echo '<span class="hover">&nbsp;</span>';
									if($v['price_four']==0) echo '<span class="free">&nbsp;</span>';
									echo '<span class="category c_'.$v['category'].'">&nbsp;</span>';
                                    echo '<a class="tooltip" title="<span style=\'display:block;text-align:center;line-height:140%;\'>'.$v['title'].'<br>'.$v['duration'].'</span>" href="'.get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">&nbsp;</a>';
                                echo '</li>';
                            }
                        }
                    }
                ?>
            </ul>
            <div class="clb"></div>
        </div>
        <div class="recommended_stand">
        	<div class="frame_2">&nbsp;</div>
            <span class="recommended_tale_text"><?php _e('რეკომენდირებული'); ?></span>
            <ul class="tales">
				<?php
                    if(count($recommend_tales)>0){
                        foreach($recommend_tales as $v){
							echo '<li style="background:url(/theme/images/left_tale_shadow.png) no-repeat left center,url(/storage/uploads/tales/'.$v['cover'].') no-repeat right center '.$v['color'].';background-size:contain, 100px, 121px;">';
								echo '<span class="hover">&nbsp;</span>';
								if($v['price_four']==0) echo '<span class="free">&nbsp;</span>';
								echo '<span class="category c_'.$v['category'].'">&nbsp;</span>';
								echo '<a class="tooltip" title="<span style=\'display:block;text-align:center;line-height:140%;\'>'.$v['title'].'<br>'.$v['duration'].'</span>" href="'.get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">&nbsp;</a>';
							echo '</li>';
                        }
                    }
                ?>
            </ul>
            <div class="clb"></div>
        </div>
        <div class="popular_stand">
            <span class="popular_tale_text"><?php _e('პოპულარული ზღაპრები'); ?></span>
            <ul class="tales">
                <?php
                    if(count($popular_tales)>0){
                        foreach($popular_tales as $v){
							echo '<li style="background:url(/theme/images/left_tale_shadow.png) no-repeat left center,url(/storage/uploads/tales/'.$v['cover'].') no-repeat right center '.$v['color'].';background-size:contain, 100px, 121px;">';
								echo '<span class="hover">&nbsp;</span>';
								if($v['price_four']==0) echo '<span class="free">&nbsp;</span>';
								echo '<span class="category c_'.$v['category'].'">&nbsp;</span>';
								echo '<a class="tooltip" title="<span style=\'display:block;text-align:center;line-height:140%;\'>'.$v['title'].'<br>'.$v['duration'].'</span>" href="'.get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">&nbsp;</a>';
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
<?php if(isset($_GET['activation']) or isset($_GET['recovery'])){ ?>
<script type="text/javascript">
    $(document).ready(function(e) {
		<?php
			if(@$_GET['activation']=='success'){
				echo 'show_popup("confirm","'.__('აქტივაცია').'","'.__('თქვენი ანგარიში წარმატებით გააქტიურდა! უკვე შეგიძლიათ გაიაროთ ავტორიზაცია!').'");';	
			}
			if(@$_GET['activation']=='error'){
				echo 'show_popup("confirm","'.__('აქტივაცია').'","'.__('ანგარიშის აქტივაცია ვერ მოხერხდა! გადაამოწმეთ აქტივაციის ბმული!').'");';	
			}
			if(@$_GET['recovery']=='success'){
				echo 'show_popup("confirm","'.__('პაროლის აღდგენა').'","'.__('პაროლის აღდგენა წარმატებით დასრულდა! ახალი პაროლი გამოგეგზავნათ ელ. ფოსტაზე').'");';	
			}
			if(@$_GET['recovery']=='error'){
				echo 'show_popup("confirm","'.__('პაროლის აღდგენა').'","'.__('პაროლის აღდგენა ვერ მოხერხდა! აღდგენის ბმული არასწორი ან ვადაგასულია!').'");';	
			}
		?>
    });
</script>
<?php } ?>