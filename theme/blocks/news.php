<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	global $_CFG;

	$news = sql::getRows("SELECT `cat_Id`, `title`, `short_desc`, `date` FROM `_cn_mod_news` WHERE `lang` = '".$_GET['l']."' AND `active` = 1 ORDER BY `date` DESC, `Id` DESC");
?>
<ogmeta>
	<title><?php echo $_GET['ptitle'].' | '.$_CFG['siteName']; ?></title>
</ogmeta>
<div class="content">
    <div class="container">
        <div class="candle"></div>
        <?php echo menu(); ?>
        <h1 class="title"><?php echo $_GET['ptitle']; ?></h1>
        <ul class="news">
        	<?php
				foreach($news as $v){
					$date = explode('-',$v['date']); 
					echo '<li>';
						echo '<a class="title" href="'.get_link('p=news&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">'.$v['title'].'</a>';
						echo '<span class="date">'.$date[2].'.'.$date[1].'.'.$date[0].'</span>';
						echo '<div class="clb"></div>';
						echo '<div class="desc">'.$v['short_desc'].'</div>';
					echo '</li>';
				}
			?>
        </ul>
    </div>
</div>