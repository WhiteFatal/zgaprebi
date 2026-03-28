<?php
ob_start();
session_name("managment");
session_start();
$DOCstartTime = round(microtime(true), 3);
include ('../includes/config.php');

$_ADM_CONTLNG = (isset($_COOKIE['admin_contentlng'])) ? $_COOKIE['admin_contentlng'] : key($_CFG['availLangs']);

include ('../includes/functions.db.php');
include ('../includes/functions.lib.php');
include ('../includes/functions.php');
settings::first_run();
is_valid_admin_session();

//timeout();
$_get_langs = get::langs();

$tmp = sql::getRows('SELECT * FROM `cn_adminlangs` WHERE lang="'.$_CFG['adminlang'].'"');
foreach($tmp as $v) $_LANG[$v['name']] = $v['value'];


define('SECURITY','aDRgh04a3gdTT3frsd213');

foreach($_CFG['roles'] as $k => $v) {
	if(has::role($k)) {
		$firstpage = $k;
		break;
	}
}

$_GET['p'] = (!isset($_GET['p'])) ? $firstpage : $_GET['p'];

$page='pages/'.$_GET['p'].".php";
$_GET['pg'] = (isset($_GET['pg'])) ? $_GET['pg'] : 1;

$_GET['l'] = initial_language();

if (!file_exists($page) || $page=='index.php') {
	$page="pages/404.php";
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>CMS</title>
<?php
echo '
<link href="js_css/styles.css" rel="stylesheet" type="text/css" />
<!--[if IE]> <link href="js_css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->
<script type="text/javascript" src="../common/jquery-1.7.min.js"></script> 
<script type="text/javascript" src="../common/jquery-ui.1.8.min.js"></script>
<script type="text/javascript" src="../common/screenfull.min.js"></script>
<script type="text/javascript" src="js_css/plugins/ui/jquery.fancybox.js"></script>
<script type="text/javascript" src="js_css/plugins/ui/jquery.tipsy.js"></script>
<script type="text/javascript" src="js_css/plugins/forms/jquery.uniform.js"></script>
<script type="text/javascript" src="js_css/plugins/forms/jquery.ibutton.js"></script>
<script type="text/javascript" src="/common/dragsort/jquery.dragsort-0.5.1.min.js"></script>
<script type="text/javascript" src="js_css/files/bootstrap.js"></script>
<script type="text/javascript" src="js_css/files/functions.js"></script>';
?>
</head>
<body>
<div id="top">
    <div class="wrapper">
        <a href="#" title="" class="logo"><img src="img/logo.png" alt="" /></a>  
        <div class="clear"></div>
    </div>
</div>
<!-- Top line ends -->

<!-- Sidebar begins -->
<div id="sidebar">
    <div class="mainNav">
        <div class="user">
    		<?php
            	$userphoto = (file_exists('../storage/avatars/'.$_SESSION['userId'].'.jpg')) ? 
								('../storage/avatars/'.$_SESSION['userId'].'.jpg') :  'img/user.png';
            ?>
            <a title="" class="leftUserDrop"><img src="<?php echo $userphoto; ?>" alt="" width="72" height="70" /></a></span>
            <ul class="leftUser">
                <li><a href="?p=profile" title="" class="sProfile"><?php _e('პროფილი'); ?></a></li>
                <li><a href="?p=others&act=users" title="" class="sSettings"><?php _e('პარამეტრები'); ?></a></li>
                <li><a href="login.php?act=logout" title="" onclick="return confirm('<?php _e('დარწმუნებული ხართ გსურთ გასვლა?'); ?>')" class="sLogout"> <?php _e('გასვლა'); ?></a></li>
            </ul>
        </div>
                
        <!-- Main nav -->
        <ul class="nav">
            <?php 
			foreach($_CFG['roles'] as $k => $v){
				if(has::role($k)) echo '<li><a href="?p='.$k.'" 		title="" '.((($_GET['p']==$k))?'class="active"':'').' ><img  src="img/icons/mainnav/'.$k.'.png" alt="" /><span>'.$v.'</span></a></li>';
			}
			?>
        </ul>
    </div>
    
<?php include($page); ?>

</div>
<script type="text/javascript">
$('a[href=#]').attr('href','javascript:void(0)');
</script>
</body>
</html>
<?php
ob_end_flush();
?>
