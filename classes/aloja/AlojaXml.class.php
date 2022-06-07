<?php

class AlojaXml
{
	/**
	 * String con el contenido completo del XML.
	 * @var string
	 */
	var $content;
	var $fecha_registro;
	var $es_valido;

	public function __construct()
	{
		$this->es_valido = false;
	}
}
?>