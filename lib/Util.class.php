<?php

require_once (__DIR__."/email.class.php");


class Util
{
	function enviar_mail_error($uname, $id_establecimiento)
	{
		ini_set ( "SMTP", SMTP_SERVER);     
		$texto  = "¡ALERTA!: Es imposible calcular la categoría del establecimiento ".$uname." (".$id_establecimiento."). Los datos de la tabla tb_establecimientos_historico contienen alguna incoherencia.";
		$to     = DIRECCION_MAIL;
		$eemail = new Email();
		$funciona=@$eemail->send("Modificacion de datos", $texto, $to);
		return $funciona;
	}
}

?>