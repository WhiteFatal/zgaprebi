<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	
	$info = sql::getRow("SELECT * FROM `_cn_mod_tales` WHERE `active` = 1 AND `cat_Id` = ".$_GET['story']);
	
	if(@$_POST['buy']==1){
		ob_clean();
		
		if(!is_logined()){
			$status = array('res'=>"error",'txt'=>__('თქვენ არ ხართ ავტორიზირებული!'));
			echo json_encode($status); die();	
		}
		
		$types = array('price_four','price');
		
		if(!in_array(@$_POST['type'],$types)){
			$status = array('res'=>'error','txt'=>__('შეძენის ტიპი არასწორია!'));
			echo json_encode($status); die();
		}
		
		$check_tale = check_tale($_GET['story']);
		
		if($check_tale==true){
			$status = array('res'=>'error','txt'=>__('თქვენ უკვე შეძენილი გაქვთ ეს ზღაპარი!'));
			echo json_encode($status); die();
		}
		
		$balance = check_balance($_SESSION['user']['Id'], $_SESSION['user']['username']);
		$gold = cash_to_gold($balance);
		
		if($gold<$info[$_POST['type']]){
			$status = array('res'=>'error','txt'=>__('თქვენ არ გაქვთ საკმარისი ოქრო!'));
			echo json_encode($status); die();
		}
		
		$price = gold_to_cash($info[$_POST['type']]);
		
		$new_balance = $balance - $price;
		
		balance_update($new_balance);
		add_tale($_GET['story'],($_POST['type']=='price_four'?4:24*365*10));
		
		$array = array('old_balance'=>$balance,'new_balance'=>$new_balance,'price'=>$price,'purchase_type'=>$_POST['type'],'tale_Id'=>$_GET['story']);
		users_log($_SESSION['user']['Id'], 'purchase', json_encode($array));
		
		$html = '<audio data-Id="'.$tale['cat_Id'].'" id="audio" preload="auto" controls>';
			$html .= '<source src="/storage/uploads/tales/'.$info['audio'].'">';
			//$html .= '<source src="/storage/uploads/tales/'.$tale['audio_ogg'].'">';
			//$html .= '<source src="/storage/uploads/tales/'.$tale['audio_wav'].'">';
		$html .= '</audio>';
		
		sql::update("UPDATE `_cn_mod_tales` SET `views` = `views` + 1 WHERE `active` = 1 AND `cat_Id` = ".@$_GET['story']);
		$_SESSION['tale_listen'][] = $_GET['story'];
		
		$status = array('res'=>'success','html'=>$html,'txt'=>__('თქვენ წარმატებით შეიძინეთ ზღაპარი!'));
		echo json_encode($status); die();
	}
?>
<div id="buy" class="buy">
    <a class="close" href="javascript:void(0);" onclick="hide_popup('buy');">&nbsp;</a>
    <h1><?php _e('ზღაპრის შეძენა'); ?></h1>
    <span class="status">&nbsp;</span>
    <form id="buy-form" action="<?php echo full_url(); ?>" method="post">
        <input readonly type="text" name="tale" placeholder="<?php _e('ზღაპრის სახელი'); ?>" value="<?php echo $info['title']; ?>"/>
        <div class="box">
            <div class="price">
                <span class="prc"><?php _e('ფასი'); ?></span>
                <span class="cash"><?php echo $info['price_four']; ?></span>
                <span class="gold">&nbsp;</span>
                <div class="clb"></div>
            </div>
            <button id="price_four" class="purchase-btn" type="button"/><?php _e('ერთჯერადი მოსმენა'); ?></button>
            <div class="clb"></div>
        </div>
        <div class="line">&nbsp;</div>
        <a id="price" class="purchase-btn" href="javascript:void(0);"><?php _e('ზღაპრის ყიდვა'); ?> <b><?php echo $info['price']; ?></b></a>
        <a class="premium-btn" href="javascript:void(0);" onClick="hide_popup('buy'),setTimeout(function(){show_popup('premium',false,false);}, 300);"><?php _e('პრემიუმ პაკეტის ჩართვა'); ?></a>
    </form>
    <div style="height:20px;"></div>
</div>