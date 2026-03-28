<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	if(!is_numeric($_GET['story'])) redirect(get_link('p=news'));

	global $_CFG;
	
	$news = sql::getRow("SELECT `title`, `desc`, `short_desc` FROM `_cn_mod_news` WHERE `cat_Id` = ".$_GET['story']." AND `active` = 1");
	
	if(!$news) redirect(get_link('p=news'));
?>
<ogmeta>
	<meta property="og:url" content="<?php echo full_url(); ?>" />
    <meta property="og:title" content="<?php echo $news['title']; ?>" />
    <meta property="og:description" content="<?php echo $news['short_desc']; ?>" />
    <meta property="og:image" content="http://zgaprebi.ge/theme/images/logo.png" />
	<title><?php echo $news['title'].' | '.$_GET['ptitle'].' | '.$_CFG['siteName']; ?></title>
</ogmeta>
<div class="content">
    <div class="container">
        <div class="candle"></div>
        <?php echo menu(); ?>
        <h1 class="title"><?php echo $news['title']; ?></h1>
        <div class="full_desc">
            <?php echo $news['desc']; ?>
        	<div class="clb"></div>
        </div>
    </div>
</div>