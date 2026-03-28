<?php
require "class.phpmailer.php";
require "class.smtp.php";
class smtpmail {
	public static function gmail($to,$subject,$text,$from='-1'){
		global $_CONFIG;
		require 'PHPMailerAutoload.php';
		$from = ($from=='-1') ? $_CONFIG['smtp']['gmail'] : $from;
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Host = $_CONFIG['smtp']['ghost'];
		$mail->Port = $_CONFIG['smtp']['gport'];
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->Username = $_CONFIG['smtp']['gmail'];
		$mail->Password = $_CONFIG['smtp']['gpass'];
		$mail->addReplyTo($_CONFIG['smtp']['gmail'],$_CONFIG['smtp']['gmail']);
		$mail->setFrom($_CONFIG['smtp']['mail'],$_CONFIG['smtp']['mail']);
		$mail->CharSet  = 'UTF-8';
		$mail->Subject = $subject;
		$mail->msgHTML($text);
		$mail->addAddress($to,$to);
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');
		return $mail->Send();
	}
	public static function reportSend($to,$subject,$text,$from,$replyTo){
		global $_CONFIG;
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->Host      = $_CONFIG['smtp']['host'];
		$mail->SMTPAuth  = true;
		$mail->Username  = $_CONFIG['smtp']['mail'];
		$mail->Password  = $_CONFIG['smtp']['pass'];
		$mail->AddReplyTo($replyTo,$replyTo);
		$mail->SetFrom($from.'@imperium.ge');
		$ms = explode(';',$to);
		foreach($ms as $v){
			$v = trim($v);
			if($v!="") $mail->AddAddress($v,$v);
		}
		$mail->CharSet  = 'UTF-8';
		$mail->Subject  =  $subject;
		$mail->Body     = $text;
		return $mail->Send();
	}
	
	public static function reportSendFile($to,$subject,$text,$from,$replyTo,$files){	
		global $_CONFIG;
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->Host      = $_CONFIG['smtp']['host'];
		$mail->SMTPAuth  = TRUE;
		$mail->Username  = $_CONFIG['smtp']['mail'];
		$mail->Password  = $_CONFIG['smtp']['pass'];
		$mail->AddReplyTo($replyTo);
		$mail->SetFrom($from.'@imperium.ge');
		$mail->AddAddress($to); 
		foreach($files as $value){
			$name = explode("/",$value);
			$dname = end($name);
			$mail->addAttachment($value, $dname);
		}
		$mail->CharSet  = 'UTF-8';
		$mail->Subject  =  $subject;
		$mail->Body     = $text;
		$mail->Send();
	}
	
	public static function send($to,$subject,$text,$from='-1'){
		global $_CONFIG;
		$from = ($from=='-1') ? $_CONFIG['smtp']['mail'] : $from;
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->Host      = $_CONFIG['smtp']['host'];
		$mail->SMTPAuth  = true;
		$mail->Username  = $_CONFIG['smtp']['mail'];
		$mail->Password  = $_CONFIG['smtp']['pass'];
		$mail->AddReplyTo($from);
		$mail->SetFrom($_CONFIG['smtp']['mail'],$_CONFIG['smtp']['mail']);
		$ms = explode(';',$to);
		foreach($ms as $v){
			$v = trim($v);
			if($v!="") $mail->AddAddress($v,$v);
		}
		$mail->CharSet  = 'UTF-8';
		$mail->Subject  =  $subject;
		$mail->Body     = $text;
		return $mail->Send();
	}
	
	public static function sendfile($to,$from,$subject,$text,$file){	
		global $_CONFIG;
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->Host      = $_CONFIG['smtp']['host'];
		$mail->SMTPAuth  = TRUE;
		$mail->Username  = $_CONFIG['smtp']['mail'];
		$mail->Password  = $_CONFIG['smtp']['pass'];
		$mail->AddReplyTo($from);
		$mail->SetFrom($_CONFIG['smtp']['sent']);
		$mail->AddAddress($to); 
		$name = explode("/",$file);
		$dname = end($name);
		$mail->AddEmbeddedImage($file,'driu',$dname,'base64','application/octet-stream');
		$mail->CharSet  = 'UTF-8';
		$mail->Subject  =  $subject;
		
		$mail->Body     = $text;
		$mail->Send();
	}
	
	public static function sendfiles($to,$from,$subject,$text,$files){	
		global $_CONFIG;
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->Host      = $_CONFIG['smtp']['host'];
		$mail->SMTPAuth  = TRUE;
		$mail->Username  = $_CONFIG['smtp']['mail'];
		$mail->Password  = $_CONFIG['smtp']['pass'];
		$mail->AddReplyTo($from);
		$mail->SetFrom($_CONFIG['smtp']['sent']);
		$mail->AddAddress($to);
		
		foreach($files as $value){
			$name = explode("/",$value);
			$dname = end($name);
			$mail->addAttachment($value, $dname);
		}
		
		$mail->CharSet  = 'UTF-8';
		$mail->Subject  =  $subject;
		
		$mail->Body     = $text;
		$mail->Send();
	}
}
?>