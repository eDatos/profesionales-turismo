<?php


/**
 * @version 1.0
 * @created 30-nov-2012 19:45:31
 */
class AlojaESP
{
	var $entradas;
	var $salidas;
	var $pernoctaciones;
	
	public function sumar(AlojaESP $addESP)
	{
		$this->entradas += $addESP->entradas;
		$this->salidas += $addESP->salidas;
		$this->pernoctaciones += $addESP->pernoctaciones;
	}
}
?>