<?php

require_once (__DIR__."/../../lib/DbHelper.class.php");
require_once (__DIR__."/../../lib/DateHelper.class.php");

define("ES_DIRECTOR", 1);
define("NO_ES_DIRECTOR", 6);

class ExpectativasFormData
{
	var $establecimiento_id;
	var $trimestre;
	var $anyo;
	
	/// bool que indica si el formulario ha sido enviado o no.
	var $es_nuevo;	
	
	/// Nombre usuario que ha grabada la encuesta.
	var $usuario_grabador;
	
	/// Fecha de cuando se grabo.
	var $fecha_grabacion;
	
	// Flag que indica si el grabador es el director (true) u otro cargo (false).
	var $es_director;
	
	/// Cargo o nombre de la presona que lo ha grabado.
	var $otra_persona_cargo;
	
	/// Tabla asociativa de valores de variables.
	var $variables;
	
	public function __construct($id_est = null, $trimestre= null, $anyo= null)
	{
		$this->establecimiento_id = $id_est;
		$this->trimestre = $trimestre;
		$this->anyo = $anyo;
	}

}


class ExpectativasFormDao
{
	/**
	 * Carga un registro de formaulario desde la base de datos. 
     * En caso de que no exista un formulario con la pk ($id_est, $trimestre, $anyo), devuelve un objeto con es_nuevo a true.
     * En caso de que  exista un formulario con la pk ($id_est, $trimestre, $anyo), devuelve un objeto con es_nuevo a false y rellenados todos los campos.
	 * 
	 * En el primer caso, (no existe formulario), inicializa las propiedades $establecimiento_id, $trimestre y $anyo con los valores pasados.
	 *  
	 * @param unknown_type $id_est
	 * @param unknown_type $trimestre
	 * @param unknown_type $anyo
	 */
	public function cargar($id_est, $trimestre, $anyo)
	{
		$db = new Istac_Sql();
		$sql= DbHelper::prepare_sql("SELECT e.*, TO_CHAR(e.fecha_grabacion, 'dd-mm-yyyy') fecha_grabacion  FROM tb_expectativas e WHERE id_establecimiento = :estid AND trimestre=:trimestre AND anyo=:anyo",
				array(':estid' => (string)$id_est, ':trimestre' => (string)$trimestre, ':anyo'=>(string)$anyo) );
			
		$db->query($sql);
		if ($db->Error != null)
		{
			@log::error("Error al intentar cargar una encuesta de expectativas: " + $db->Error);
			return false;
		}
		
		if ($db->next_record($sql))
		{
			$exp = new ExpectativasFormData();
				
			$exp->variables =  $db->Record;
			
			$exp->establecimiento_id = $exp->variables['id_establecimiento'];
			$exp->trimestre = $exp->variables['trimestre'];
			$exp->anyo = $exp->variables['anyo'];
			$exp->usuario_grabador = $exp->variables['usuario_grabador'];
			$exp->fecha_grabacion = DateHelper::parseDate($exp->variables['fecha_grabacion']);
			$exp->otra_persona_cargo = $exp->variables['otra_persona_cargo'];
			$exp->es_director = ($exp->variables['director'] == ES_DIRECTOR);
			
			$exp->es_nuevo = false;
			
			unset($exp->variables['id_establecimiento']);
			unset($exp->variables['trimestre']);
			unset($exp->variables['anyo']);
			unset($exp->variables['usuario_grabador']);
			unset($exp->variables['fecha_grabacion']);
			unset($exp->variables['otra_persona_cargo']);
			unset($exp->variables['director']);
			
			/// Asegurar que las variables siempre estaran case insensitive
			$exp->variables = array_change_key_case($exp->variables, CASE_UPPER);
		}
		else 
		{
			$exp = new ExpectativasFormData();
			$exp->establecimiento_id = $id_est;
			$exp->trimestre = $trimestre;
			$exp->anyo = $anyo;
			$exp->es_nuevo = true;	
		}
		return $exp;
	}
	
	/**
	 * Carga un registro de cuestionario cargando solo los datos de cabecera.
	 * 
	 * @param unknown_type $id_est
	 * @param unknown_type $trimestre
	 * @param unknown_type $anyo
	 */
	public function cargar_cabecera($id_est, $trimestre, $anyo)
	{
		$db = new Istac_Sql();
		$sql= DbHelper::prepare_sql("SELECT id_establecimiento, trimestre, anyo, usuario_grabador, TO_CHAR(e.fecha_grabacion, 'dd-mm-yyyy') fecha_grabacion, otra_persona_cargo, director FROM tb_expectativas e WHERE id_establecimiento = :estid AND trimestre=:trimestre AND anyo=:anyo",
				array(':estid' => (string)$id_est, ':trimestre' => (string)$trimestre, ':anyo'=>(string)$anyo) );
			
		$db->query($sql);
		if ($db->Error != null)
		{
			@log::error("Error al intentar cargar la cabecera de una encuesta de expectativas: " + $db->Error);
			return false;
		}
		
		if ($db->next_record($sql))
		{
			$exp = new ExpectativasFormData();
	
			$exp->establecimiento_id = $db->Record['id_establecimiento'];
			$exp->trimestre = $db->Record['trimestre'];
			$exp->anyo = $db->Record['anyo'];
			$exp->usuario_grabador = $db->Record['usuario_grabador'];
			$exp->fecha_grabacion = DateHelper::parseDate($db->Record['fecha_grabacion']);
			$exp->otra_persona_cargo = $db->Record['otra_persona_cargo'];
			$exp->es_director = ($db->Record['director'] == ES_DIRECTOR);
				
			$exp->es_nuevo = false;
		}
		else
		{
			$exp = new ExpectativasFormData();
			$exp->establecimiento_id = $id_est;
			$exp->trimestre = $trimestre;
			$exp->anyo = $anyo;
			$exp->es_nuevo = true;
		}
		return $exp;
	}
	
	/**
	 * Eliminar un unico cuestionario identificado por ($id_est, $trimestre, $anyo)
	 * @param unknown_type $id_est Establecimiento_id
	 * @param unknown_type $trimestre trimestre (1,2,3,4)
	 * @param unknown_type $anyo año (4 digitos).
	 */
	public function eliminar($id_est, $trimestre, $anyo)
	{
		$db = new Istac_Sql();
		$sql= DbHelper::prepare_sql("DELETE FROM tb_expectativas e WHERE id_establecimiento = :estid AND trimestre=:trimestre AND anyo=:anyo",
				array(':estid' => (string)$id_est, ':trimestre' => (string)$trimestre, ':anyo'=>(string)$anyo) );
			
		$db->query($sql);
		if ($db->Error != null)
		{
            $msg = "Error al intentar eliminar una encuesta de expectativas: " + $db->Error;
			@log::error($msg);
            return $msg;
		}
        if ($db->affected_rows() == 0)
        {
            $msg = "No existe encuesta para el establecimiento y el trimestre indicados.";
			@log::warning($msg);
            return $msg;         
        }
        
        return true;
	}
	
	/**
     * Guarda un cuestionario en la base de datos, marcandolo como !es_nuevo si la operacion tiene exito.
	 * @param ExpectativasFormData $exp
	 */
	public function guardar(ExpectativasFormData $exp)
	{
		if(!$exp->es_nuevo)
		{
			/// Actualizar los datos
			return $this->actualizar($exp);
		}
		else 
		{
			/// Insertar una nuevo.
			return $this->insertar($exp);
		}
	}
	
	private function actualizar(ExpectativasFormData $exp)
	{
		/// DATOS DE CABECERA EXPECTATIVAS
		$nombres = array('fecha_grabacion','usuario_grabador','director');
		$valores = array();
		
		$valores[] = "to_date('".$exp->fecha_grabacion->format('Y-m-d')."', 'YYYY-MM-DD')";
		$valores[] = DbHelper::prepare_string($exp->usuario_grabador);
		$valores[] = $exp->es_director? ES_DIRECTOR : NO_ES_DIRECTOR;
		
		if ($exp->es_director)
		{
			$nombres[] = 'otra_persona_cargo';
			$valores[] = 'NULL';
		}
		else 
		{
			$nombres[] = 'otra_persona_cargo';
			$valores[] = DbHelper::prepare_string($exp->otra_persona_cargo);
		}		
		
		/// VARIABLES DEL FORMULARIO.
		if (isset($exp->variables))
		{
			foreach ($exp->variables as $nv => $vv)
			{
				$nombres[] = $nv;
				$valores[] = DbHelper::prepare_string($vv); ///NOTA: Aqui se supone que todas las variables son NUMBER.
			}
		}

		$campos_arr = array();
		for ($i = 0; $i < count($nombres); $i++)
		{
			$campos_arr[] = "$nombres[$i] = $valores[$i]";
		}
		$campos = implode("," , $campos_arr);
		
		$sql= DbHelper::prepare_sql("UPDATE TB_EXPECTATIVAS SET $campos WHERE id_establecimiento = :estid AND trimestre=:trimestre AND anyo=:anyo",
				array(':estid' => (string)$exp->establecimiento_id, 
						':trimestre' => (string)$exp->trimestre, 
						':anyo'=>(string)$exp->anyo) );
		
		$db = new Istac_Sql();
		$db->query($sql);
		
		if ($db->Error != null)
		{
			@log::error("Error al intentar actualizar una encuesta de expectativas: " + $db->Error);
			return "Ha ocurrido un error interno al guardar la encuesta.";
		}
		
		if ($db->affected_rows() > 0)
			$exp->es_nuevo = false;	
		return true;
	}
	
	private function insertar(ExpectativasFormData $exp)
	{
		/// DATOS DE CABECERA EXPECTATIVAS
		$nombres = array('id_establecimiento','trimestre','anyo','fecha_grabacion','usuario_grabador','director');
		$valores = array(
				DbHelper::prepare_string($exp->establecimiento_id),
				DbHelper::prepare_string($exp->trimestre),
				DbHelper::prepare_string($exp->anyo)
				);
		
		$valores[] = "to_date('".$exp->fecha_grabacion->format('Y-m-d')."', 'YYYY-MM-DD')";
		$valores[] = DbHelper::prepare_string($exp->usuario_grabador);
		$valores[] = $exp->es_director? ES_DIRECTOR : NO_ES_DIRECTOR;
		
		if ($exp->es_director)
		{
			$nombres[] = 'otra_persona_cargo';
			$valores[] = 'NULL';
		}
		else 
		{
			$nombres[] = 'otra_persona_cargo';
			$valores[] = DbHelper::prepare_string($exp->otra_persona_cargo);
		}
		
		/// VARIABLES DEL FORMULARIO.
		if (isset($exp->variables))
		{
			foreach ($exp->variables as $nv => $vv) 
			{
				$nombres[] = $nv;
				$valores[] = DbHelper::prepare_string($vv);
			}
		}
		
		$campos = implode(",", $nombres);
		$campos_valores = implode(",", $valores);
		$sql = "INSERT INTO tb_expectativas ($campos) VALUES ($campos_valores)";
		
		$db = new Istac_Sql();
		$db->query($sql);
		
		if ($db->Error != null)
		{
			@log::error("Error al intentar insertar una encuesta de expectativas: " + $db->Error);
			return "Ha ocurrido un error interno al guardar la encuesta.";
		}	
		
		if ($db->affected_rows() > 0)
			$exp->es_nuevo = false;
		return true;
	}	
	
	public function obtener_encuestas_anteriores($estid, $fecha_fin, $trimestre_abierto = null)
	{
		$fecha_ini = date_sub(clone $fecha_fin, new DateInterval("P1Y"));
		
		$params = array(':estid' => $estid,
				      ':fecha_ini'=> $fecha_ini->format('d/m/Y'), 
    				  ':fecha_fin'=> $fecha_fin->format('d/m/Y'));
		
		if ($trimestre_abierto != null)
		{
			$params[':trim'] = $trimestre_abierto->trimestre;
			$params[':ano'] = $trimestre_abierto->anyo;
		}
		else
		{
			$params[':trim'] = 0;
			$params[':ano'] = 0;
		}
		
		$sql = DbHelper::prepare_sql("select * from (select e.id_establecimiento, e.trimestre, e.anyo 
									  from tb_expectativas e
									  where e.id_establecimiento = :estid
								      and to_date(:fecha_ini, 'dd/mm/yyyy') <= e.fecha_grabacion AND e.fecha_grabacion <= to_date(:fecha_fin, 'dd/mm/yyyy') order by e.anyo desc, e.trimestre desc) where rownum < 8", 
				$params);
		
		$db = new Istac_Sql();
		$db->query($sql);
		
		$result = array();
		while ($db->next_record())
		{
			$nr = array();
			$nr['trim'] = $db->f('trimestre');
			$nr['ano'] = $db->f('anyo');
			$result[] = $nr;
		}
		return $result;
	}
	
	/***
	 * Carga el cuerpo para el correo de confirmación de envío desde la tabla configuración.
	 */
	public function obtener_cuerpo_correo_desde_configuracion()
	{
		$db = new Istac_Sql();
		$db->query("SELECT expectativas_cierre_mailbody FROM TB_CONFIGURATION");
	
		if ($db->next_record())
		{
			return $db->f('expectativas_cierre_mailbody');
		}
		return null;
	}
}

?>