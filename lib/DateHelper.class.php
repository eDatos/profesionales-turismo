<?php

/**
 * Helper para formato y validacion de fechas
 *
 */
class DateHelper 
{
	var $fecha_sistema;
	var $dia_sistema;
	var $mes_sistema;
	var $anyo_sistema;
	
	public function __construct()
	{
		$this->fecha_sistema = date("Y-m-d");
		list($this->anyo_sistema,$this->mes_sistema,$this->dia_sistema) = explode("-",$this->fecha_sistema);
	}
    
	public static function mes_anterior($mes, $anyo)
	{
		if($mes=='1')
		{
			$mes  = "12";
			$anyo = $anyo - 1;
		}
		else
		{
			$mes  = $mes-1;
		}
		return (array($mes, $anyo));
	}
	
	public static function mes_siguiente($mes, $anyo)
	{
		if($mes=='12')
		{
			$mes  = "1";
			$anyo = $anyo + 1;
		}
		else
		{
			$mes  = $mes+1;
		}
		return (array($mes, $anyo));
	}
	
	/**
	 * Intenta crear un objeto DateTime a partir de una cadena en el formato "dd-mm-yyyy" o "dd/mm/yyyy".
	 * Devuelve false si la cadena pasada no se puede convertir a una fecha, o el objeto fecha en caso correcto.
	 * @param unknown_type $pv
	 */
	public static function parseDate($pv)
	{
		$validDate = FALSE;
		
		$pv = str_replace("-", "/", $pv);
		if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $pv))
		{
			$validDate = DateTime::createFromFormat('!d/m/Y', $pv);
		}
		
		return $validDate;
	}
	
	public static function getDateFormat($tipo)
	{
		switch($tipo)
		{
			/// Si es para jQuery datepicker
			case "datepicker":
				return str_replace('/', DATE_SEPARATOR, "dd/mm/yy");				
			/// Si es para mostrarlo.
			case "show":
			default:
				return str_replace('/', DATE_SEPARATOR, "dd/mm/yyyy");
		}
	}

    public static function mes_tostring($num_mes, $format = null)
    {
        $nombres_meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        
        if ($format == null)
            return $nombres_meses[ $num_mes - 1 ];
        
        switch($format)
        {
            case 'm': 
                return strtolower($nombres_meses[ $num_mes - 1 ]);
            case 'M':
                return ucfirst(strtolower($nombres_meses[ $num_mes - 1 ]));
        }
    }
    
    public static function fecha_tostring($fecha, $includeyear = false)
    {
        $rr = $fecha->format('j') . " de " . DateHelper::mes_tostring($fecha->format('m'), "m");
        if ($includeyear)
        {
            $rr .= " de " . $fecha->format('Y');
        }
        return $rr;
    }
}

?>