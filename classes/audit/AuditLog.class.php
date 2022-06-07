<?php

require_once(__DIR__."/../../lib/RowDbIterator.class.php");
require_once(__DIR__."/../../lib/DbHelper.class.php");
require_once(__DIR__."/../../lib/DateHelper.class.php");

// TIPOS DE ACCIONES QUE SER REGISTRAN EN EL LOG, DEPENDIENDO DE LA CONFIGURACIÓN

// ACCIONES ASOCIADAS AL CUESTIONARIO DE EXPECTATIVAS
define("CONSULTA_TABLA_EMPLEO",                    "CONSULTA_TABLA_EMPLEO");
define("CONSULTA_TABLA_EVOLUCION_TURISMO",         "CONSULTA_TABLA_EVOLUCION_TURISMO");
define("CONSULTA_TABLA_EXPECTATIVAS_TRIMESTRE",    "CONSULTA_TABLA_EXPECTATIVAS_TRIMESTRE");
define("CONSULTA_TABLA_INVERSION_PRIVADA",         "CONSULTA_TABLA_INVERSION_PRIVADA");
define("CONSULTA_TABLA_PREVISION_GRADO_OCUPACION", "CONSULTA_TABLA_PREVISION_GRADO_OCUPACION");
define("CONSULTA_TABLA_TENDENCIA_PRECIOS",         "CONSULTA_TABLA_TENDENCIA_PRECIOS");
define("ENVIA_CUESTIONARIO_EXPECTATIVAS",          "ENVIA_CUESTIONARIO_EXPECTATIVAS");
define("IMPRIME_CUESTIONARIO_EXPECTATIVAS",        "IMPRIME_CUESTIONARIO_EXPECTATIVAS");
define("BORRA_CUESTIONARIO_EXPECTATIVAS",          "BORRA_CUESTIONARIO_EXPECTATIVAS");

// ACCIONES ASOCIADAS AL CUESTIONARIO DE ALOJAMIENTO
define("CIERRA_CUESTIONARIO_ALOJAMIENTO",          "CIERRA_CUESTIONARIO_ALOJAMIENTO");
define("ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL",    "ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL");
define("ENVIA_CUESTIONARIO_ALOJAMIENTO_XML",       "ENVIA_CUESTIONARIO_ALOJAMIENTO_XML");
define("IMPRIME_CUESTIONARIO_ALOJAMIENTOS",        "IMPRIME_CUESTIONARIO_ALOJAMIENTOS");
define("BORRA_CUESTIONARIO_ALOJAMIENTO",           "BORRA_CUESTIONARIO_ALOJAMIENTO");

// ACCIONES ASOCIADAS A LA CONSULTA DE RESULTADOS DE ALOJAMIENTO
define("CONSULTA_TABLA_VIAJEROS_ALOJADOS",         "CONSULTA_TABLA_VIAJEROS_ALOJADOS");
define("CONSULTA_TABLA_VIAJEROS_ENTRADOS",         "CONSULTA_TABLA_VIAJEROS_ENTRADOS");
define("CONSULTA_TABLA_VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA",         "CONSULTA_TABLA_VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA");
define("CONSULTA_TABLA_PERNOCTACIONES",            "CONSULTA_TABLA_PERNOCTACIONES");
define("CONSULTA_TABLA_INDICE_OCUPACION",          "CONSULTA_TABLA_INDICE_OCUPACION");
define("CONSULTA_TABLA_INDICE_OCUPACION_POR_HABITACIONES",          "CONSULTA_TABLA_INDICE_OCUPACION_POR_HABITACIONES");
define("CONSULTA_TABLA_ESTANCIA_MEDIA",            "CONSULTA_TABLA_ESTANCIA_MEDIA");
define("CONSULTA_TABLA_ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA",            "CONSULTA_TABLA_ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA");
define("CONSULTA_TABLA_VIAJEROS_ALOJADOS_POR_PAIS","CONSULTA_TABLA_VIAJEROS_ALOJADOS_POR_PAIS");
define("CONSULTA_TABLA_PERNOCTACIONES_POR_PAIS",   "CONSULTA_TABLA_PERNOCTACIONES_POR_PAIS");
define("CONSULTA_TABLA_TARIFA_MEDIA_POR_HABITACION_MENSUAL",   "CONSULTA_TABLA_TARIFA_MEDIA_POR_HABITACION_MENSUAL");

// ACCIONES ASOCIADAS A LOS EMPLEADORES
define('INSERTAR_NUEVO_EMPLEADOR',                  'INSERTAR_NUEVO_EMPLEADOR');
//define('DESACTIVAR_CONTRATO',                       'DESACTIVAR_CONTRATO');
define('BORRAR_EMPLEADOR',                          'BORRAR_EMPLEADOR');
define('MODIFICAR_EMPLEADOR',                       'MODIFICAR_EMPLEADOR');
define('CIERRA_CUESTIONARIO_EMPLEO',                'CIERRA_CUESTIONARIO_EMPLEO');

// ACCIONES ASOCIADAS A OTRAS OPCIONES DE LOG
define("CAMBIA_CLAVE",    "CAMBIA_CLAVE");
define("CONSULTA_DIRECCIONES_DE_INTERES", "CONSULTA_DIRECCIONES_DE_INTERES");
define("CONSULTA_NOTAS_DE_PRENSA", "CONSULTA_NOTAS_DE_PRENSA");
define("INICIA_SESION",   "INICIA_SESION");
define("FINALIZA_SESION", "FINALIZA_SESION");


/// Estados de ejecucion de las acciones.
define("SUCCESSFUL", "0");
define("FAILED",     "1");

/**
 * Controla el acceso y registro a los mensajes de auditoria (LOG).
 * Para realizar un registro de acciones, emplear el metodo estatico log
 */
class AuditLog 
{
	
	private $db;
	 
	protected function __construct ()
	{
		$this->db = new Istac_Sql();	
	}
	
	/**
	 * Registra una accion de una sola entrada y 0 o mas mensajes.
	 * @param unknown $user_id el id de usuario, si es nulo, no se realiza una entrada y solo se registra la accion.
	 * @param unknown $estab_id id del establecimiento
	 * @param unknown $actionTypeName nombre de la accion (ver defines en este archivo)
	 * @param unknown $executionState Estado de ejecucion de la accion que registrar (ver defines en este archivo)
	 * @param array $messages Lista de mensajes a registrar para la accion.
	 * @param unknown $infoExtra Información sobre trimestre/mes y año del cuestionario asociado a la nueva entrada del log
	 * @return boolean|el
	 */
	public static function log($user_id, $estab_id, $actionTypeName, $executionState, $messages = array(), $infoExtra=NULL)
	{
		$al = new AuditLog();
		
		/// 1. Crear registro de accion
		$new_action_id = $al->insert_accion($actionTypeName, $executionState);
		// FALSE si el registro deshabilitado o no existe el tipo de accion indicado.
		if ($new_action_id === FALSE)
			return FALSE;
		
		/// 2. Crear registro de entrada
		if (isset($user_id))
		{
			$fecha = date("d/m/Y H:i:s");
			$new_entry_id = $al->insert_entrada($user_id, $estab_id, $new_action_id, $fecha, $infoExtra);
			if ($new_entry_id === FALSE)
				return FALSE;
		}
		
		/// 3. Crear 0 o mas registros de mensajes
		if (isset($messages) && is_array($messages))
		{
			foreach ($messages as $msg)
			{
				if (is_string($msg))
					$al->insert_mensaje($new_action_id, $msg);
				else if (is_a($msg, "AlojaError"))
				{
					$mensaje = $msg->mensaje;
					if ($msg->detalles != null)
					{
						foreach($msg->detalles as $i => $msg_det)
						{
							$mensaje .= "<br>-" . $msg_det;
						}
					}
					$al->insert_mensaje($new_action_id, $mensaje);	
				}
				else 
					$al->insert_mensaje($new_action_id, $msg);
			}		
		}
		
		return $new_action_id;
	}
		
	/**
	 * Ejecuta un insert sobre la tabla de mensajes.
	 * 
	 * @param unknown_type $action_id
	 * @param unknown_type $msg
	 * @return boolean
	 */
	private function insert_mensaje($action_id, $msg )
	{
		$sql = DbHelper::prepare_sql("INSERT INTO tb_mensajes_accion(mensaje,accion_id) VALUES(:msg, :actid)",
				array(":actid" 	=> (int)$action_id,
					  ":msg" 	=> (string)	$msg));
	
		$this->db->query($sql);
		/// 0 si la opcion esta deshabilitada para registro.
		if ($this->db->affected_rows() == 0)
			return FALSE;
		return TRUE;
	}

	/**
	 * Ejecuta un insert sobre la tabla de entradas del log
	 * @param unknown $user_id Id del usuario asociado a la nueva entrada de log a crear.
	 * @param unknown $estab_id Id del establecimiento asociado a la nueva entrada de log a crear.
	 * @param unknown $action_id Id de la acción asociada a la nueva entrada de log a crear.
	 * @param unknown $date Fecha y hora de la nueva entrada de log a crear.
	 * @param unknown $infoExtra Información adicional (trimestre/mes y año) del cuestionario asociado a la nueva entrada de log a crear.
	 * @return boolean El identificador único de la nueva entrada de log creada o FALSE si hay fallo.
	 */
	private function insert_entrada($user_id, $estab_id, $action_id, $date, $infoExtra=NULL)
	{
		$new_id = DbHelper::newid_from_seq($this->db, 'entradas_log_id_seq');
		if ($new_id === FALSE)
			return FALSE;

		$trim_mes=null;
		$anio=null;
		if(isset($infoExtra))
		{
		    if(isset($infoExtra['mes']))
		        $trim_mes=$infoExtra['mes'];
	        else if(isset($infoExtra['trimestre']))
	            $trim_mes=$infoExtra['trimestre'];
	        if(isset($infoExtra['año']))
	            $anio=$infoExtra['año'];
		}
		$sql = DbHelper::prepare_sql("INSERT INTO tb_entradas_log(id, usuario_id, id_establecimiento, accion_id, fecha, trim_mes, anio) 
				VALUES(:id, :userid, :estabid, :actid, TO_DATE(:fecha, 'DD/MM/YYYY HH24:MI:SS'), :trim_mes, :anio)",
				array(":id"		=> (int)	$new_id,
					  ":userid" => (string)	$user_id,
					  ":estabid" => (string) $estab_id,
					  ":actid" 	=> (int)	$action_id,
					  ":fecha" 	=> (string)	$date,
		              ":trim_mes" 	=> $trim_mes,
				    ":anio" => $anio
				)
		    );
		
		$this->db->query($sql);
		/// 0 si la opcion esta deshabilitada para registro.
		if ($this->db->affected_rows() == 0)
			return FALSE;
		return $new_id;		
	}
	
	/**
	 * Inserta una nueva accion del tipo indicado y el estado indicado si está habilitado el registro
	 * de dichas acciones (ver TB_OPCIONES_CONFIG para ver valores de actionTypeName validos.).
	 * 
	 * @param string $actionTypeName Nombre del tipo de accion.
	 * @param bool $executionState Estado de ejecucion de la accion.
	 * @return el id de la accion nueva, o FALSE si no se pudo hacer la operacion (deshabilitado el registro o un error). 
	 */
	private function insert_accion($actionTypeName, $executionState)
	{
		$new_id = DbHelper::newid_from_seq($this->db, 'acciones_id_seq');
		if ($new_id === FALSE)
			return FALSE;
		
		// NOTA: solo se coge el primer elto, ya que como la BD permite que se repitan los nombres de 
		// los tipos de accion, se podrían intentar insertar multiples acciones (dando error de unique key).
		$sql = DbHelper::prepare_sql("INSERT INTO tb_acciones_log (id, tipo_id, estado_ejecucion)
				SELECT :id, ta.ID, :state
				FROM tb_tipos_de_acciones ta, tb_opciones_config op
				WHERE ta.opcion_config_id = op.id AND op.estado = 1 AND
				ta.nombre = :actname AND rownum < 2",
				array(":id" 	=> (int)$new_id,
					  ":state" 	=> (string)$executionState,
					  ":actname"=> (string)	$actionTypeName));
		
		$this->db->query($sql);
		/// 0 si la opcion esta deshabilitada para registro (o da error).
		if ($this->db->affected_rows() == 0)
			return FALSE;
		return $new_id;
	}
	
	public static function listEntriesForAction($actid)
	{
		$al = new AuditLog();
		
		$sql = "select mensaje from tb_mensajes_accion where accion_id = :actid";
		
		$fsql = DbHelper::prepare_sql($sql,array(':actid' => $actid));
		
		$al->db->query($fsql);
		return new RowDbIterator($al->db, array('mensaje'));
		
	}
	
	public static function listByFecha($begindate, $enddate, $actid, $username)
	{
		$al = new AuditLog();
		
		
		$sql = "select * from (select a.id,u.username,DECODE(est.id_establecimiento,NULL,'','(' || est.id_establecimiento || ') ' || est.nombre_establecimiento) as nombre_est,
		ta.descripcion, a.estado_ejecucion, to_char(e.fecha, 'dd/mm/yyyy') as fecha, to_char(e.fecha, 'hh24:mi:ss') as hora, count(ma.mensaje) as nummsgs,
		e.trim_mes as trim_mes, e.anio as anio
		from tb_entradas_log e left join tb_auth_user_md5 u on e.usuario_id=u.user_id left join tb_establecimientos_unico est on
		e.id_establecimiento=est.id_establecimiento,
		tb_acciones_log a left join tb_mensajes_accion ma on a.id=ma.accion_id, tb_tipos_de_acciones ta
		where e.accion_id=a.id and a.tipo_id=ta.id AND
		trunc(e.fecha) >= to_date(:begindate, 'dd/mm/yyyy') AND trunc(e.fecha) <= to_date(:enddate, 'dd/mm/yyyy')";
		
		if (isset($actid))
				$sql .= " AND a.tipo_id = :actid";
		if (isset($username))
				$sql .= " AND u.username = :username";
			
		$sql .=" group by (a.id, u.username, est.id_establecimiento, est.nombre_establecimiento, ta.descripcion, a.estado_ejecucion, e.fecha, e.trim_mes, e.anio) ORDER BY e.fecha desc) where rownum < :maxrows ";
		
		$fsql = DbHelper::prepare_sql($sql, array(
						":dateformat"=>(string)DateHelper::getDateFormat("show"),
						":maxrows"  => (int)(MAX_LOG_LIST_ITEMS+1),
						":begindate"=> (string)$begindate,
						":enddate" 	=> (string)$enddate,
						":actid" 	=> (string)$actid,
					    ":username"	=> (string)$username));
		
		$al->db->query($fsql);
		return new RowDbIterator($al->db, array('id','username','nombre_est','descripcion','estado_ejecucion','fecha','hora', 'nummsgs', 'trim_mes', 'anio'));
	}
}

?>