<?php
	if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');
	
	if(@$_POST['balance']==1){
		ob_clean();
		
		if(!is_logined()){
			$status = array('res'=>"error",'txt'=>__('თქვენ არ ხართ ავტორიზირებული!'));
			echo json_encode($status); die();	
		}
		
		if(trim($_POST['amount'])==''){
			$status = array('res'=>"error",'txt'=>__('მიუთითეთ თანხა!'));
			echo json_encode($status); die();	
		}
		
		if((!is_numeric($_POST['amount']) && !is_float($_POST['amount'])) || ($_POST['amount']<=0)){
			$status = array('res'=>"error",'txt'=>__('თანხა უნდა იყოს 0-ზე მეტი!'));
			echo json_encode($status); die();	
		}
		
		$status = array('res'=>'success','url'=>'http://zgaprebi.ge/cartu/payment.php?amount='.number_format($_POST['amount'],2));
		echo json_encode($status); die();
	}
?>
<div id="balance" class="balance">
    <a class="close" href="javascript:void(0);" onclick="hide_popup('balance');">&nbsp;</a>
    <h1><?php _e('ბალანსის შევსება'); ?></h1>
    <form id="balance-form" action="<?php echo full_url(); ?>" method="post">
        <span class="status">&nbsp;</span>
        <input type="hidden" name="balance" value="1"/>
        <div style="width:376px;margin:auto;">
        	<span class="gel"><?php _e('ლარი'); ?></span>
            <span class="curr"><?php _e('0.1 ლარი = 1 ოქრო'); ?></span>
            <div class="clb"></div>
            <input id="amount" style="width:144px;float:left;" type="text" name="amount" placeholder="<?php _e('თანხა'); ?>" value=""/>
            <span class="arrows">&nbsp;</span>
            <input id="gold" style="width:116px;float:right;padding-right:40px;background:url(/theme/images/small_gold.png) no-repeat 134px center rgba(76,50,22,0.3);" type="text" name="gold" placeholder="<?php _e('ოქრო'); ?>" value=""/>
            <div class="clb"></div>
            <input class="cardsubmit" type="submit" name="submit" value="<?php _e('ბარათით გადახდა'); ?>"/>
        </div>
        <div class="line">&nbsp;</div>
        <a class="balance-btn" href="<?php echo get_link('p=tbc-pay'); ?>"><?php _e('TBC PAY ჩარიცხვა'); ?></a>
    </form>
    <div style="height:20px;"></div>
</div>