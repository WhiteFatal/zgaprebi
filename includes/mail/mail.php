<?php

//require "class.phpmailer.php";
//require "class.smtp.php";

class smtpmail {
	public static function send($to,$subject,$message,$from='-1'){
		global $_CFG;
		$from = ($from=='-1') ? $_CFG['smtp']['mail'] : $from;
		
		$headers = array(
			"MIME-Version: 1.0",
			"Content-type: text/html; charset=utf-8'",
			"From: noreply@zgaprebi.ge",
			"Reply-To: noreply@zgaprebi.ge",
			"X-Mailer: PHP/" . PHP_VERSION
		);
		
		$headers = implode("\r\n", $headers);
		
		mail($to, $subject, $message, $headers);
	}
	
	/*public static function send2($to,$subject,$text,$from='-1'){
		global $_CFG;
		$from = ($from=='-1') ? $_CFG['smtp']['mail'] : $from;
		$fromName = ($from=='-1') ? '' : $from;
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->Host      = $_CFG['smtp']['host'];
		$mail->SMTPAuth  = false;
		$mail->Username  = $_CFG['smtp']['mail'];
		$mail->Password  = $_CFG['smtp']['pass'];
		$mail->From     = $from;
		$mail->FromName = $fromName;
		$mail->SMTPDebug = false;
		$ms = explode(';',$to);
		foreach($ms as $v){
			$v = trim($v);
			if($v!="") $mail->AddAddress($v,$v);
		}
		$mail->CharSet  = 'UTF-8';
		$mail->Subject  =  $subject;
		$mail->Body     = $text;
		return $mail->Send();
	}*/
}


    

?>