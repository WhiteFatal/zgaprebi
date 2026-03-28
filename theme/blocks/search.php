<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	
	if(isset($_POST['keyword'])){
		ob_clean();
		$_POST['keyword'] = sql::safe($_POST['keyword']);
		
		$sql = "SELECT `cat_Id`, `title` FROM `_cn_mod_tales` WHERE `active` = 1 AND `title` LIKE '%".$_POST['keyword']."%'";
		$search = sql::getRows($sql);
		
		if(count($search)==0){
			$status = array('res'=>'error', 'txt'=>__('არაფერი მოიძებნა!'));
			echo json_encode($status); die();	
		}
		
		$html = '';
		
		foreach($search as $v){
			$html .= '<li><a href="'.get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title']).'">'.$v['title'].'</a></li>';
		}
		
		
		$status = array('res'=>'success','html'=>$html);
		echo json_encode($status); die();	
	}
?>
<div class="search">
	<form id="search-form" action="<?php echo full_url(); ?>" method="post">
		<input id="search" type="text" name="keyword" autocomplete="off" placeholder="<?php _e('ზღაპრის ძებნა'); ?>"/>
    </form>
    <div id="search-result" class="result">
    	<ul id="searched"></ul>
    </div>
</div>