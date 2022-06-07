<?php

use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;

require_once(__DIR__."/../config.php");
require_once(__DIR__."/ext/PHPMailer.php");
require_once(__DIR__."/ext/SMTP.php");
require_once(__DIR__."/ext/Exception.php");

class Email
{
	private $mailer;
		
	public function __construct($smtpServer = SMTP_SERVER, 
			$smtpPort = MAIL_SMTP_PORT, 
			$useSSL = MAIL_USE_SSL, 
			$smtpUser = USUARIO_MAIL, 
			$smtpPass = PASS_MAIL)
	{
		$this->mailer = new PHPMailer(true);
		$this->mailer->Mailer = "smtp";
		$this->mailer->Host = $smtpServer;
		$this->mailer->Port = $smtpPort;
		if ($useSSL)
		{
			$this->mailer->SMTPSecure = "ssl";
		}
		$this->mailer->Username = $smtpUser; 
		$this->mailer->Password = $smtpPass;
		$this->mailer->SMTPAuth = ($this->mailer->Password!="");

		$this->mailer->Timeout=30;
		/// Flag para depuracion.
		$this->mailer->SMTPDebug     = false;
		
		$this->mailer->SingleTo = MAIL_SEPARADOS;
	}
	
	function send($asunto, $texto, $to = RECEPTOR_MAILS, $from = DIRECCION_MAIL, $fromName = NOMBRE_USUARIO_MAIL)
	{
	    if (ENABLE_MAIL != true)
	        return true;
	    
		$exito = false;
		$intentos = 0;
		
		$this->mailer->From = $from;
		$this->mailer->FromName = $fromName;
		$direcciones=explode(",",str_replace(";", ",", $to));
		foreach ($direcciones as $dir)
		    $this->mailer->AddAddress($dir);		
		$this->mailer->Subject = $asunto;
		$this->mailer->Body = $texto;
		$this->mailer->IsHTML();
		
		do 
		{
		 	try
			{		
				$exito = $this->mailer->Send();
		
				if ($exito || $intentos >= 5)
					break;
			} 
			catch (PHPMailer\PHPMailer\Exception $e) 
			{
				log::error($e->errorMessage());
			} 
			catch (Exception $e) 
			{
				log::error($e->getMessage());
			}
			/// Si no hubo exito esperar antes de reintentar.
			$intentos++;
			sleep(15);
		} while (!$exito);
		
		return $exito;
	}
}
?>