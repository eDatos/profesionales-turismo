<?php

/**
 * Ayuda para gestionar mensajes y calculos sobre trimestres.
 *
 */
class Trimestre
{
	var $trimestre; //1,2,3 o 4
	var $anyo; 

	public function __construct($trimestre, $anyo)
	{
		$this->trimestre = $trimestre;
		$this->anyo = $anyo;
	}
	
    public static function create_from_date(DateTime $fecha)
    {
        $me = $fecha->format('m');
        $ano = $fecha->format('Y');
        $trime = 1 + floor(($me -1) / 3);
        return new Trimestre($trime, $ano);
    }
    
	public static function actual()
	{
		list($mesactual, $anyo) = explode('-',date('m-Y'));
		$trimestre = floor(($mesactual - 1) / 3) + 1;
		return new Trimestre($trimestre, $anyo);
	}
    
	public function tostring($s = null)
	{
        $nombres_meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
        
		$m0 = $nombres_meses[($this->trimestre -1) * 3];
		$m1 = $nombres_meses[($this->trimestre -1) * 3 +1];
		$m2 = $nombres_meses[($this->trimestre -1) * 3 +2];
		
		$m = "$m0, $m1 y $m2";
		
		switch($s)
		{
			case 'm1': return $m0;
			case 'm2': return $m1;
			case 'm3': return $m2;
			case 'y' : return $m . " de " . $this->anyo;
			default: return $m;
		}
	}
	
	public function siguiente()
	{
		$t = ($this->trimestre % 4) + 1;
		if ($t < $this->trimestre)
			return new Trimestre($t, $this->anyo+1);
		return new Trimestre($t, $this->anyo);
	}
	
	public function anterior()
	{
		$t = 1 + (($this->trimestre -1 + 3) % 4);
		if ($t > $this->trimestre)
			return new Trimestre($t, $this->anyo-1);
		return new Trimestre($t, $this->anyo);
	}
	
	public function anyosiguiente()
	{
		return new Trimestre($this->trimestre, $this->anyo + 1);
	}
	
	public function anyoanterior()
	{
		return new Trimestre($this->trimestre, $this->anyo - 1);
	}

    public function igual(Trimestre $tr)
    {
        return ($this->trimestre == $tr->trimestre && $this->anyo == $tr->anyo);
    }
}


?>