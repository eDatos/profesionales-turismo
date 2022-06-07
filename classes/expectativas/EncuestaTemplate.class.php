<?php

class Bloque 
{
	var $identificador;
	var $titulo;
	var $comentario;
	var $preguntas;
	
	function __construct()
	{
		$this->preguntas = array();
	}
	
	public function addPregunta(Pregunta $preg)
	{
		$this->preguntas[] = $preg;
	}
}

class Pregunta 
{
	var $apartado;
	var $tipo;
	var $titulo;
	var $valores;
	var $opciones;
	var $cabecera;
	var $comentario;
	var $comentario_grabador;
	
	function __construct()
	{
		$this->valores = array();
		$this->opciones = array();
	}
	
	public function addValor(Valor $vlr)
	{
		$this->valores[] = $vlr;
	}
	
	public function addOpcion(Opcion $opc)
	{
		$this->opciones[] = $opc;
	}	
}

class Valor 
{
	var $num;
	var $texto;
}

class Opcion
{
	function __construct()
	{
		$this->requerida = false;
	}
	
	var $nombre_oracle;
	var $texto;
	var $requerida;
	var $sinvalor;
	/// Indice de la opcion en toda la encuesta.	
	var $index;
	
	/// Valor actual de la opcion (uno de los valores num de los tags valor).
	var $valor;
}

class EncuestaTemplate 
{
	var $bloques;
	
	
	public function validar_respuestas($respuestas)
	{
        $errores = array();
        
		if ($respuestas == null || !is_array($respuestas))
        {
            $errores[] = "No se ha especificado ninguna respuesta.";
            return $errores;
        }
        
		foreach($this->bloques as $bloque)
		{
			foreach ($bloque->preguntas as $preg)
			{
				foreach ($preg->opciones as $opc)
				{
					/// 1. Comprobar si es requerida y tiene valor, o es opcional y no tiene valor.
					if (!isset($respuestas[$opc->index]) || $respuestas[$opc->index] == null)
					{
						if ($opc->requerida)
                        {
                            $errores[] = "Se requiere una respuesta para la pregunta $opc->index.";
                        }
                        
						/// Las respuestas opcionales con valor nulo son aceptadas. 
						continue;	
					}
					/// 2. Comprobar que el valor para la respuesta está entre los valores aceptados.
					$res_valor = $respuestas[$opc->index];
					$ok = false;
					foreach ($preg->valores as $valor) 
					{
						if ($res_valor == $valor->num)
						{
							$ok = true;
							break;
						}
					}
					/// 3. Comprobar si el valor es el valor por defecto.
					if (!$ok && $res_valor != $opc->sinvalor)
					{
						$errores[] =  "La respuesta $res_valor no es válida para la pregunta $opc->index";
					}
				}
			}
		}
		/// Recorridas todas las respuestas y son validas (no hay error)
		if (count($errores) == 0)
            return null;
        return $errores;
	}
	
	public function get_opciones()
	{
		$opcs = array();
		foreach($this->bloques as $bloque)
		{
			foreach ($bloque->preguntas as $ppp)
			{
				foreach ($ppp->opciones as $opc)
				{
					$opcs[] = $opc;
				}
			}
		}
		return $opcs;
	}
	
	public function get_opcion_by_index($index)
	{
		foreach($this->bloques as $bloque)
		{
			foreach ($bloque->preguntas as $ppp)
			{
				foreach ($ppp->opciones as $opc)
				{
					if ($opc->index == $index)
						return $opc;
				}	
			}
		}	
		/// No encontrado.
		return null;
	}
	
	/**
	 * Aplica los valores pasados a las opciones.
	 * @param unknown_type $vars
	 */
	public function set_variables($vars)
	{
		foreach($this->bloques as $bloque)
		{
			foreach ($bloque->preguntas as $ppp)
			{
				foreach ($ppp->opciones as $opc)
				{	
					if (isset($vars[strtoupper($opc->nombre_oracle)]))
					{
						$opc->valor = $vars[strtoupper($opc->nombre_oracle)];
					}
				}	
			}
		}	
	}
	
	/**
	 * Genera un json con la estrucutra de objetos necesaria para validacion de cliente de las respuestas.
	 * Dicha estructura se genera a partir de los datos cargados de esta plantilla.  
	 */
	public function get_js_validation_code()
	{
		$array_salida = array();
		foreach($this->bloques as $bloque)
		{
			foreach ($bloque->preguntas as $preg)
			{
				$valores_preg = array();
				foreach($preg->valores as $valor)
				{
					$valores_preg[] = $valor->num;
				}
				
				foreach($preg->opciones as $opc)
				{
					$regla = array('req' => $opc->requerida);

					$v = $valores_preg;
					if ($opc->sinvalor != null)
					{
						$v[] = $opc->sinvalor;
					}
					$regla['vals']= $v;
					
					$array_salida[$opc->index] = $regla;
				}
			}
		}
		return json_encode($array_salida);
	}
	
	/**
	 * Sustituir las ocurrencias de las variables [xxx] por los valores indicados.
	 * @param unknown_type $vars un array con entradas en la forma ("[xxx]" => valor)
	 */
	public function sustituir_textos($vars)
	{
		foreach ($vars as $varname => $varvalue)
		{
			foreach($this->bloques as $bloque)
			{
				$bloque->comentario = str_replace($varname, $varvalue, $bloque->comentario);
				$bloque->titulo = str_replace($varname, $varvalue, $bloque->titulo);
		
				foreach ($bloque->preguntas as $ppp)
				{
					$ppp->cabecera = str_replace($varname, $varvalue, $ppp->cabecera);
					$ppp->titulo = str_replace($varname, $varvalue, $ppp->titulo);
					$ppp->comentario = str_replace($varname, $varvalue, $ppp->comentario);
					$ppp->comentario_grabador = str_replace($varname, $varvalue, $ppp->comentario_grabador);
					
					foreach ($ppp->opciones as $opc)
					{
						$opc->texto = str_replace($varname, $varvalue, $opc->texto);
					}
					foreach ($ppp->valores as $vlr)
					{
						$vlr->texto = str_replace($varname, $varvalue, $vlr->texto);
					}
				}
			}
		}		
	}
}

?>