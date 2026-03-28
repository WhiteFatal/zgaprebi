<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	$is_logined = is_logined();
	
	$to_3_sql = "SELECT `cat_Id`, `title`, `cover`, `color`, `category`, `price_four`, `duration` FROM `_cn_mod_tales` WHERE `active` = 1 AND `category` = '1'";
	$to_3_cnt = sql::CNT($to_3_sql);
	$to_3 = sql::getRows($to_3_sql." ORDER BY `Id` DESC LIMIT 0, 18");
	$pages_c1 = ceil($to_3_cnt/18);
	
	$from_4_to_6_sql = "SELECT `cat_Id`, `title`, `cover`, `color`, `category`, `price_four`, `duration` FROM `_cn_mod_tales` WHERE `active` = 1 AND `category` = '2'";
	$from_4_to_6_cnt = sql::CNT($from_4_to_6_sql);
	$from_4_to_6 = sql::getRows($from_4_to_6_sql." ORDER BY `Id` DESC LIMIT 0, 18");
	$pages_c2 = ceil($from_4_to_6_cnt/18);
	
	$from_7_sql = "SELECT `cat_Id`, `title`, `cover`, `color`, `category`, `price_four`, `duration` FROM `_cn_mod_tales` WHERE `active` = 1 AND `category` = '3'";
	$from_7_cnt = sql::CNT($from_7_sql);
	$from_7 = sql::getRows($from_7_sql." ORDER BY `Id` DESC LIMIT 0, 18");
	$pages_c3 = ceil($from_7_cnt/18);
	
	######################################################################################
	if(isset($_POST['page'])){
		ob_clean();
		if(!is_numeric($_POST['page'])){
			$status = array('res'=>'error','txt'=>__('არასწორი გვერდის ნომერი!'));
			echo json_encode($status); die();	
		}
		
		$types = array('to_3'=>1,'from_4_to_6'=>2,'from_7'=>3);
		if(!array_key_exists($_POST['type'], $types)){
			$status = array('res'=>'error','txt'=>__('არასწორი კატეგორიის ტიპი!'));
			echo json_encode($status); die();	
		}
		
		$_POST['page'] = sql::safe($_POST['page']);
		
		$sql = "SELECT `cat_Id`, `title`, `cover`, `color`, `category`, `price_four`, `duration` FROM `_cn_mod_tales` 
					WHERE `active` = 1 AND `category` = '".$types[$_POST['type']]."'";
					
		$result = sql::getRows($sql." ORDER BY `Id` DESC LIMIT ".($_POST['page']*18-18).", 18");
		
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
		?>
        <div class="fb-like" data-href="https://www.facebook.com/zgaprebi.ge" data-layout="box_count" data-action="like" data-show-faces="true" data-share="true"></div>
        <a class="zgaprebi" href="http://zgaprebi.ge">&nbsp;</a>
        <div class="frame_1">&nbsp;</div>
        <div style="top:778px;" class="frame_2">&nbsp;</div>
        <div class="big_stand">
            <?php echo menu(); ?>
            <span class="currency">&nbsp;</span>
            <a class="filling_balance"<?php echo ($is_logined==false?'onClick="show_popup(\'confirm\',\'ავტორიზაციის შეცდომა\',\'ბალანსის შესავსებად საჭიროა გაიაროთ ავტორიზაცია!\');"':' onClick="show_popup(\'balance\',false,false)"'); ?>><?php _e('ბალანსის შევსება'); ?></a>
            <div class="years_category">
            	<span class="to_3"><?php _e('3 წლამდე'); ?></span>
                <span class="from_4_to_6"><?php _e('4-6 წელთათვის'); ?></span>
                <span class="from_7"><?php _e('7 წლიდან'); ?></span>
                <div class="clb"></div>
            </div>
            <ul class="tales to_3">
                <?php
                    if(count($to_3)>0){
                        foreach($to_3 as $v){
							echo '<li style="background:url(/theme/images/left_tale_shadow.png) no-repeat left center,url(/storage/uploads/tales/'.$v['cover'].') no-repeat right center '.$v['color'].';background-size:contain, 100px, 121px;">';
								echo '<span class="hover">&nbsp;</span>';
								if($v['price_four']==0) echo '<span class="free">&nbsp;</span>';
								//echo '<span class="category c_'.$v['category'].'">&nbsp;</span>';
								echo '<a class="tooltip" title="<span style=\'display:block;text-align:center;line-height:140%;\'>'.$v['title'].'<br>'.$v['duration'].'</span>" href="'.get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">&nbsp;</a>';
							echo '</li>';
                        }
                    }
                ?>
            </ul>
            <div class="clb"></div>
            <ul class="tales from_4_to_6">
                <?php
                    if(count($from_4_to_6)>0){
                        foreach($from_4_to_6 as $v){
							echo '<li style="background:url(/theme/images/left_tale_shadow.png) no-repeat left center,url(/storage/uploads/tales/'.$v['cover'].') no-repeat right center '.$v['color'].';background-size:contain, 100px, 121px;">';
								echo '<span class="hover">&nbsp;</span>';
								if($v['price_four']==0) echo '<span class="free">&nbsp;</span>';
								//echo '<span class="category c_'.$v['category'].'">&nbsp;</span>';
								echo '<a class="tooltip" title="<span style=\'display:block;text-align:center;line-height:140%;\'>'.$v['title'].'<br>'.$v['duration'].'</span>" href="'.get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">&nbsp;</a>';
							echo '</li>';
                        }
                    }
                ?>
            </ul>
            <div class="clb"></div>
            <ul class="tales from_7">
                <?php
                    if(count($from_7)>0){
                        foreach($from_7 as $v){
							echo '<li style="background:url(/theme/images/left_tale_shadow.png) no-repeat left center,url(/storage/uploads/tales/'.$v['cover'].') no-repeat right center '.$v['color'].';background-size:contain, 100px, 121px;">';
								echo '<span class="hover">&nbsp;</span>';
								if($v['price_four']==0) echo '<span class="free">&nbsp;</span>';
								//echo '<span class="category c_'.$v['category'].'">&nbsp;</span>';
								echo '<a class="tooltip" title="<span style=\'display:block;text-align:center;line-height:140%;\'>'.$v['title'].'<br>'.$v['duration'].'</span>" href="'.get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">&nbsp;</a>';
							echo '</li>';
                        }
                    }
                ?>
            </ul>
            <div class="clb"></div>
            <div class="arrows">
            	<div class="to_3">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages_c1; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
                <div class="from_4_to_6">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages_c2; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
                <div class="from_7">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages_c3; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
                <div class="clb"></div>
            </div>
            <div style="top:1143px;" class="arrows">
            	<div class="to_3">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages_c1; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
                <div class="from_4_to_6">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages_c2; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
                <div class="from_7">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages_c3; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
                <div class="clb"></div>
            </div>
            <div style="top:1633px;" class="arrows">
            	<div class="to_3">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages_c1; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
                <div class="from_4_to_6">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages_c2; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
                <div class="from_7">
                	<a id="next" href="javascript:void(0);" class="arrow right">&nbsp;</a>
                    <div class="pages">
                    	<span id="from">1</span>/<span id="to"><?php echo $pages_c3; ?></span>
                    </div>
                    <a id="prev" href="javascript:void(0);" class="arrow left">&nbsp;</a>
                    <div class="clb"></div>
                </div>
                <div class="clb"></div>
            </div>
        </div>
        <div class="stand_strut_shadow">&nbsp;</div>
    </div>
</div>
<?php get::block('footer.php'); ?>