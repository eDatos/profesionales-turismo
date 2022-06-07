<?php

/**
  * @version 1.0
 * @created 30-nov-2012 19:44:58
 */
class AlojaUTMovimientos
{

	var $presentes_comienzo_mes;
	var $movimientos;

	public function __construct()
	{
		$this->movimientos = array();
	}
	
	function get_presentes_mes_anterior()
	{
		
    }
    
    /**
     * Obtiene las pernoctaciones del ultimo dia con movimientos.
     */
    function get_presentes_ultimo_dia() 
    {
    	if (!isset($this->movimientos) || count($this->movimientos)==0)
    		return 0;
    	 
    	$last_elem = array_slice( $this->movimientos, -1, 1 );
    	return $last_elem[0]->pernoctaciones;
//    	if (!isset($this->movimientos[count($this->movimientos)]))
//    		return 0;
//    	return $this->movimientos[count($this->movimientos)]->pernoctaciones;
    }
}
?>