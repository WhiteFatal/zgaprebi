<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	global $_CFG;
	$s = sql::getRow("SELECT * FROM `cn_sitemap` WHERE `lang` = '".$_GET['l']."' AND `permalink` = '".$_GET['pname']."'");
	//$childs = sql::getRows("SELECT * FROM `cn_sitemap` WHERE `lang` = '".$_GET['l']."' AND parent = 2 AND enabled > 0 AND menuId > 0 ORDER BY ord ASC ");
?>
<ogmeta>
	<meta property="og:url" content="<?php echo full_url(); ?>" />
    <meta property="og:title" content="<?php echo @$s['title']; ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars(strip_tags(@$s['extra_source'])); ?>" />
	<title><?php echo $_GET['ptitle'].' | '.$_CFG['siteName']; ?></title>
</ogmeta>
<div class="content">
    <div class="container">
        <div class="candle"></div>
        <?php echo menu(); ?>
        <h1 class="title"><?php echo $s['title']; ?></h1>
        <div class="full_desc">
            <?php echo $s['extra_source']; ?>
            <div class="clb"></div>
        </div>
    </div>
</div>