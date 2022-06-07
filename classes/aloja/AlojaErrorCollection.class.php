<?php


/**
 * Nivel de error (importancia)
 */
define ("NIVEL_ERROR", "ERROR");
define ("NIVEL_ADVERTENCIA", "AVISO");

/**
 * Categoria de error/advertencia
 */
define ("ERROR_GENERAL", 0);
define ("ERROR_DATO_GLOBAL", 1);
define ("ERROR_MOVIMIENTOS", 2);
define ("ERROR_HABITACIONES", 3);
define ("ERROR_PERSONAL", 4);
define ("ERROR_PRECIOS", 5);
define ("ERROR_UTS_NOREVISADA", 6);
define ("ERROR_EXCESO_PLAZAS", 7);
define ("ERROR_EXCESO_PLAZAS_MES", 8);
define ("ERROR_EXCESO_HABITACIONES", 9);

//define ("ERROR_INDETERMINADO", 0);
//define ("ERROR_ESTRUCTURA", 1);
//define ("ERROR_ESQUEMA", 2);
//define ("ERROR_DATOS_GRAVE", 3);
//define ("ERROR_DATOS", 4);
//define ("ERROR_SOLO_AVISO", 5);

class AlojaError
{
	var $nivel;
	var $categoria;
	var $mensaje;
	var $detalles;
	
	public function __construct($nivel, $categoria, $mensaje, $dets = null)
	{
		$this->nivel = $nivel;
		$this->categoria = $categoria;
		$this->mensaje = $mensaje;
		$this->detalles = $dets;
	} 
}

class AlojaErrorCollection
{
	var $errores;
	
	public function __construct(array $errs = null)
	{
		$this->errores = array();
		
		if ($errs != null)
		{
			foreach($errs as $err)
			{
				if (is_string($err))
				{
					$a = new AlojaError(NIVEL_ERROR, ERROR_GENERAL, $err);
				}
				else if (is_a($err, "AlojaError"))
				{
					$a = $err;
				}
				else 
				{
					$a = new AlojaError(NIVEL_ERROR, ERROR_GENERAL, $err);
				}
				$this->errores[] = $a;
			}
		}
	}

	public function num_errores()
	{
		return count($this->errores);
	}
	
	
	public function hay_solo_avisos()
	{
		foreach($this->errores as $error)
		{
			if ($error->nivel != NIVEL_ADVERTENCIA)
				return false;
		}
		return true;
	}
	
	public function hay_error()
	{
		return $this->num_errores() > 0;
	}
	
	public function hay_error_de_categorias($filtro_categorias)
	{
		foreach($this->errores as $error)
		{
			if (in_array($error->categoria, $filtro_categorias))
				return true;
		}
		return false;
	}
	
	public function log_error($categoria, $err_msg, $err_detalles = null)
	{
		$this->errores[] = new AlojaError(NIVEL_ERROR, $categoria, $err_msg, $err_detalles);
	}
	
	public function log_aviso($categoria, $err_msg, $err_detalles = null)
	{
		$this->errores[] = new AlojaError(NIVEL_ADVERTENCIA, $categoria, $err_msg, $err_detalles);
	}

}

?>