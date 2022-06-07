<?php

class AlojaUTStat
{
	var $nombre;
	var $ultimo_movimiento;
	var $presentes_fin_mes_anterior;
	var $presentes_comienzo_mes;
	var $entradas;
	var $salidas;
	var $presentes_fin_mes;
	
	/**
	 * Flag para indicar que la UT tiene movimientos en algun mes anterior (hasta un m�ximo de meses anteriores definido por configuraci�n).
	 * @var unknown_type
	 */
	var $mov_meses_anteriores;
}

?>