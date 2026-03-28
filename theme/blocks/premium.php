<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	
	$premium = sql::getRows("SELECT * FROM `_cn_mod_premium` WHERE `active` = 1 ORDER BY `day`");
	
	if(@$_POST['premium']==1){
		ob_clean();
		
		if(!is_logined()){
			$status = array('res'=>"error",'txt'=>__('თქვენ არ ხართ ავტორიზირებული!'));
			echo json_encode($status); die();	
		}
		
		if(!is_numeric($_POST['type'])){
			$status = array('res'=>'error','txt'=>__('აირჩიეთ პრემიუმ პაკეტი!'));
			echo json_encode($status); die();
		}
		
		if(check_premium_packet($_POST['type'])!=1){
			$status = array('res'=>'error','txt'=>__('აირჩიეთ პრემიუმ პაკეტი!'));
			echo json_encode($status); die();
		}
		
		if(is_premium()==true){
			$status = array('res'=>'error','txt'=>__('თქვენ უკვე ჩართული გაქვთ პრემიუმ პაკეტი!'));
			echo json_encode($status); die();
		}
		
		$info = check_premium_price($_POST['type']);
		
		$balance = check_balance($_SESSION['user']['Id'], $_SESSION['user']['username']);
		$gold = cash_to_gold($balance);
		
		if($gold<$info['price']){
			$status = array('res'=>'error','txt'=>__('თქვენ არ გაქვთ საკმარისი ოქრო!'));
			echo json_encode($status); die();
		}
		
		$price = gold_to_cash($info['price']);
		
		$new_balance = $balance - $price;
		
		balance_update($new_balance);
		add_premium($info['day']);
		
		$array = array('old_balance'=>$balance,'new_balance'=>$new_balance,'price'=>$price,'premium_days'=>$info['day']);
		users_log($_SESSION['user']['Id'], 'add_premium', json_encode($array));
		
		$audio = sql::getElem("SELECT `audio` FROM `_cn_mod_tales` WHERE `active` = 1 AND `cat_Id` = ".$_GET['story']);
		
		$html = '<audio id="audio" preload="auto" controls>';
			$html .= '<source src="/storage/uploads/tales/'.$audio.'">';
			//$html .= '<source src="/storage/uploads/tales/'.$tale['audio_ogg'].'">';
			//$html .= '<source src="/storage/uploads/tales/'.$tale['audio_wav'].'">';
		$html .= '</audio>';
		
		$status = array('res'=>'success','html'=>$html,'txt'=>__('თქვენ წარმატებით ჩაგერთოთ პრემიუმ პაკეტი! ამიერიდან '.$info['day'].' დღის განმავლობაში, შეგიძლიათ მოუსმინოთ ნებისმიერ ზღაპარს!'));
		echo json_encode($status); die();
	}
?>
<div id="premium" class="premium">
    <a class="close" href="javascript:void(0);" onclick="hide_popup('premium');">&nbsp;</a>
    <h1><?php _e('პრემიუმ პაკეტი'); ?></h1>
    <span class="status">&nbsp;</span>
    <form id="premium-form" action="<?php echo full_url(); ?>" method="post">
		<?php
            if(count($premium)>0){
                foreach($premium as $v){
                    echo '<div style="width:362px;float:none;margin:0 auto 10px auto;" class="box">';
                        echo '<div style="float:left;width:135px;" class="price">';
                            echo '<span style="float:right;" class="gold">&nbsp;</span>';
                            echo '<span style="float:right;" class="cash">'.$v['price'].'</span>';
                            echo '<div class="clb"></div>';
                        echo '</div>';
                        echo '<button class="start-premium-btn" data-Id="'.$v['cat_Id'].'" style="width:155px;margin:auto;float:right;margin-top:8px;margin-right:10px;" type="button"/>'.$v['title'].'</button>';
                        echo '<div class="clb"></div>';
                    echo '</div>';
                }
            }
        ?>
    </form>
    <div class="line">&nbsp;</div>
    <a class="premium-btn" href="javascript:void(0);" onClick="hide_popup('premium'),setTimeout(function(){show_popup('buy',false,false);}, 300);"><?php _e('ზღაპრის შეძენა'); ?></a>
    <div style="height:20px;"></div>
</div>