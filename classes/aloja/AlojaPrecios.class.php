<?php

/**
 * Tipos de clientes
 */
define("TO_TRADICIONAL","TOUROPERADOR_TRADICIONAL");
define("TO_ONLINE", "TOUROPERADOR_ONLINE");
define("EMPRESAS", "EMPRESAS");
define("AGENCIA_TRADICIONAL", "AGENCIA_DE_VIAJE_TRADICIONAL");
define("AGENCIA_ONLINE", "AGENCIA_DE_VIAJE_ONLINE");
define("PARTICULARES", "PARTICULARES");
define("GRUPOS", "GRUPOS");
define("INTERNET", "INTERNET");
define("OTROS", "OTROS");


class AlojaPrecios
{

	
	var $revpar_mensual;
	var $adr_mensual;
	
	var $adr;
	var $pct;
	var $num;
	
	public function __construct()
	{
		$this->adr = array();
		$this->pct = array();
		$this->num = array();
	}
}
?>