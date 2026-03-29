<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	if(isset($_GET['p'])) $_GET['p'] = sql::safe($_GET['p']);
	if(isset($_GET['p1'])) $_GET['p1'] = sql::safe($_GET['p1']);
	if(isset($_GET['story'])) $_GET['story'] = sql::safe($_GET['story']);
	
	$home = sql::getRow("SELECT `permalink`, `source`, `title` FROM `cn_sitemap` WHERE `parent` = 0 AND `enabled` = 1 AND `homepage` = 1");
	
	if($_GET['p']=='index' and !isset($_GET['pname'])){
		if(isset($home)){
			$_GET['p'] = str_replace('.php','',$home['source']);
			$_GET['pname'] = $home['permalink'];
			$_GET['ptitle'] = $home['title'];
		}else{
			$_GET['p'] = 'index';
			$_GET['pname'] = 'home';
		}
	}
	
	$body_class = array(
		'home' => 'home',
		'news' => 'news',
		'tales' => 'home',
		'purchased' => 'home',
		'contact' => 'home',
		'tale' => 'tale'
	);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" type="image/png" href="/theme/images/logo.png">
<link rel="stylesheet" type="text/css" href="/theme/js_css/styles.min.css">
<link rel="stylesheet" type="text/css" href="/theme/js_css/jquery-ui.min.css">
<script type="text/javascript" src="/theme/js_css/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="/theme/js_css/jquery-ui.min.js"></script>
<script type="text/javascript" src="/theme/js_css/audioplayer.min.js"></script>
<script type="text/javascript" src="/theme/js_css/jquery.nicescroll.min.js"></script>
<script type="text/javascript" src="/theme/js_css/jquery.tooltipster.min.js"></script>
<script type="text/javascript" src="/theme/js_css/jquery.alphanum.min.js"></script>
<script type="text/javascript" src="/theme/js_css/script.min.js"></script>
<!--<meta name="author" content="Nika Jangveladze, enji777@gmail.com" />-->
<meta name="robots" content="noimageindex">
<meta name="description" content="ზღაპრები">
<meta name="keywords" content="ზღაპრები, მეზღაპრე, ზღაპარი">
<meta property="fb:app_id" content="790277907718676"/>
<meta property="og:type" content="website" />
<meta http-equiv="ogmeta" />
</head>
<body class="<?php echo (array_key_exists($_GET['pname'],$body_class)?$body_class[$_GET['pname']]:'content_page'); ?>">
	<?php
		//content
		include('pages/'.$_GET['p'].'.php');
	?>
    <div id="fb-root"></div>
</body>
</html>