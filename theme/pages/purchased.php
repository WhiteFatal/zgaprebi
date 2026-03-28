<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	
	global $_CFG;
	
	$is_logined = is_logined();
	
	if(!$is_logined) redirect(get_link('p=home'));
	
	$tales_sql = "SELECT
						t.`cat_Id`,
						t.`title`,
						t.`cover`,
						t.`color`,
						t.`category`,
						t.`price_four`,
						t.`duration`,
						pt.`hours`,
						pt.`purchased_date`,
						pt.`purchased_date` + INTERVAL pt.`hours` HOUR AS `expire_date`
					FROM
						`_cn_mod_purchased_tales` AS pt
					LEFT JOIN `_cn_mod_tales` AS t ON t.`cat_Id` = pt.`tale_Id`
					WHERE
						pt.`user_Id` = ".$_SESSION['user']['Id']."
					AND t.`active` = 1
					AND NOW() < pt.`purchased_date` + INTERVAL pt.`hours` HOUR";
					
	$tales_cnt = sql::CNT($tales_sql);			
	$tales = sql::getRows($tales_sql." LIMIT 18");
	$pages = ceil($tales_cnt/18);
		
	$popular_tales = sql::getRows("SELECT `cat_Id`, `title`, `cover`, `color`, `category`, `price_four`, `duration` FROM `_cn_mod_tales` WHERE `active` = 1 ORDER BY `views` DESC LIMIT 18");
	######################################################################################
	if(isset($_POST['page'])){
		ob_clean();
		if(!is_numeric($_POST['page'])){
			$status = array('res'=>'error','txt'=>__('არასწორი გვერდის ნომერი!'));
			echo json_encode($status); die();	
		}
		
		$_POST['page'] = sql::safe($_POST['page']);
		
		$sql = "SELECT
					t.`cat_Id`,
					t.`title`,
					t.`cover`,
					t.`color`,
					t.`category`,
					t.`price_four`,
					t.`duration`,
					pt.`hours`,
					pt.`purchased_date`,
					pt.`purchased_date` + INTERVAL pt.`hours` HOUR AS `expire_date`
				FROM
					`_cn_mod_purchased_tales` AS pt
				LEFT JOIN `_cn_mod_tales` AS t ON t.`cat_Id` = pt.`tale_Id`
				WHERE
					pt.`user_Id` = ".$_SESSION['user']['Id']."
				AND t.`active` = 1
				AND NOW() < pt.`purchased_date` + INTERVAL pt.`hours` HOUR";
					
		$result = sql::getRows($sql." ORDER BY t.`Id` DESC LIMIT ".($_POST['page']*18-18).", 18");
		
		$html = "";
		foreach($result as $v){
			$html .= '<li style="background:url(/theme/images/left_tale_shadow.png) no-repeat left center,url(/storage/uploads/tales/'.$v['cover'].') no-repeat right center '.$v['color'].';background-size:contain, 100px, 121px;opacity:0;">';
				$html .= '<span class="hover">&nbsp;</span>';
				if($v['price_four']==0) $html .= '<span class="free">&nbsp;</span>';
				//echo '<span class="category c_'.$v['category'].'">&nbsp;</span>';
				$html .= '<a class="tooltip" title="<span style=\'display:block;text-align:center;line-height:140%;\'>'.$v['title'].'<br>'.$v['duration'].'</span>" href="'.get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">&nbsp;</a>';
			$html .= '</li>';
		}
		
		$status = array('res'=>'success','html'=>$html);
		echo json_encode($status); die();
	}
?>
<ogmeta>
	<title><?php echo $_GET['ptitle'].' | '.$_CFG['siteName']; ?></title>
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
        <div class="purchased_stand">
            <?php echo menu(); ?>
            <span class="currency">&nbsp;</span>
            <a class="filling_balance"<?php echo ($is_logined==false?'onClick="show_popup(\'confirm\',\'ავტორიზაციის შეცდომა\',\'ბალანსის შესავსებად საჭიროა გაიაროთ ავტორიზაცია!\');"':' onClick="show_popup(\'balance\',false,false)"'); ?>><?php _e('ბალანსის შევსება'); ?></a>
            <ul class="tales purchased">
				<?php
                    if(count($tales)>1){
                        foreach($tales as $v){
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
            <div class="arrows">
            	<div class="purchased">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
            </div>
        </div>
        <div class="popular_stand">
        	<div style="top:-87px" class="frame_2">&nbsp;</div>
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