<?php

define('FILTRO_ALFABETO', ' abcdefghijklmnopqrstuvwxyzñçABCDEFGHIJKLMNOPQRSTUVWXYZÑÇáéíóúÁÉÍÓÚäëïöüÄËÏÖÜ');
define('FILTRO_ALFABETO_EXTENDIDO', '_$€#@ºª');
define('FILTRO_NUMERO_NATURAL', '0123456789');
define('FILTRO_NUMERO_REAL', '0123456789.,+-');
define('FILTRO_HEXADECIMAL', '0123456789abcdefxABCDEFX');
define('FILTRO_ARITMETICO', '()=+-*/');
define('FILTRO_NUMERICO', '0123456789.,()=+-*/');
define('FILTRO_NUMERICO_EXTENDIDO', '<>[]%^~');
define('FILTRO_PUNTUACION_TEXTO', '.,;:¿?¡!()[]{}');
define('FILTRO_COMILLAS', '\"\'');
define('FILTRO_MULTILINEA', "\r\n");
define('FILTRO_ESPECIALES', '<>\\$&');

class InputCleaner
{
	public static function clean($texto,$filtros)
	{
		$salida="";
		
		for($i=0;$i<strlen($texto);$i++)
		{
			foreach($filtros as $filtro)
			{
				if(strpos($filtro,$texto[$i])!==false)
				{
					$salida.=$texto[$i];
					break;
				}
			}
		}
		
		return $salida;
	}
	
	public static function cleanText($texto)
	{
		$filtros=array(FILTRO_ALFABETO,FILTRO_PUNTUACION_TEXTO,FILTRO_NUMERICO);
		return InputCleaner::clean($texto,$filtros);
	}
	
	public static function oracle($texto)
	{
		$filtros=array(FILTRO_ALFABETO,FILTRO_ALFABETO_EXTENDIDO,FILTRO_PUNTUACION_TEXTO,FILTRO_NUMERICO,FILTRO_NUMERICO_EXTENDIDO,FILTRO_MULTILINEA);
		return InputCleaner::clean($texto,$filtros);
	}
}

?>