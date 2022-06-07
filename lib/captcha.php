<?php
/*
 *	Componente de llamada al WS de generación de 
 *	CAPTCHAs para PHP
 */
date_default_timezone_set('Europe/London');

require_once(__DIR__."/../config.php");
require_once(__DIR__."/ext/nusoap/nusoap.php");


function captcha_getkeys()
{
	//TODO: COMENTAR PARA FUNCIONAR CON EL SERVICIO DE CAPTCHA AUTENTICO
	//return "MEQTA";
	//TODO: FIN COMENTAR PARA FUNCIONAR CON EL SERVICIO DE CAPTCHA AUTENTICO
	
	/// Generacion de la cadena aleatoria de 5 letras.
	$mapletras =  array("A","B","C","D","E","F","G","H","J","K","M","N","O","P","Q","R","S","T","U","V","X","Y","Z",);
	$keyword="";
	
	for($i=0;$i<5;$i++)
	{
		$keyword.=$mapletras[rand(0,count($mapletras)-1)];
	}
	return $keyword;
}

function captcha_getimage($keyword)
{
	//TODO: COMENTAR PARA FUNCIONAR CON EL SERVICIO DE CAPTCHA AUTENTICO
	//$im = file_get_contents(__DIR__.'/captcha_fake.jpg');
	//$imdata = base64_encode($im);
	//return $imdata;
	//TODO: FIN COMENTAR PARA FUNCIONAR CON EL SERVICIO DE CAPTCHA AUTENTICO
	
	// crea el cliente
	$client = new nusoap_client(CAPTCHA_SERVICE, 'wsdl',
					'', '', '','');
	$err = $client->getError();
	if ($err) 
	{
		log::error("No se ha podido obtener la imagen CAPTCHA: " . $err);
		//echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
		return false;
	}
			
	// Llamada al servicio web para generacion de la imagen a partir de las letras.
	$param = array(
			'nWidth' => 300, 'nHeight' => 100, 
			'sKeyword' => $keyword, 'sFontName' => 'Arial Narrow', 'fFontSize' => 45);
	
	$result = $client->call('CaptchaImageString', array('parameters' => $param), '', '', false, true);
	
	// Check for a fault
	if ($client->fault) 
	{
		//echo '<h2>Fault</h2><pre>';
		//print_r($result);
		//echo '</pre>';
		log::error("No se ha podido obtener la imagen CAPTCHA: " . $result);
		return false;
	} 
	else 
	{
		// Check for errors
		$err = $client->getError();
		if ($err) 
		{
			// Error
			//echo '<h2>Error</h2><pre>' . $err . '</pre>';
			log::error("No se ha podido obtener la imagen CAPTCHA: " . $err);
			return false;
		} 
		else 
		{
			///Devolver al cliente la imagen pedida.
			return implode($result);
		}
	}
}
?>
