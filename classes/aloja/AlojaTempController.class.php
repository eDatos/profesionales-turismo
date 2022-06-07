<?php

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../audit/AuditLog.class.php");
require_once(__DIR__."/../../lib/DateHelper.class.php");
require_once(__DIR__."/../Establecimiento.class.php");
require_once(__DIR__."/AlojaDao.class.php");
require_once(__DIR__."/AlojaTempDao.class.php");
require_once(__DIR__."/AlojaESP.class.php");
require_once(__DIR__."/../../lib/email.class.php");
require_once(__DIR__."/AlojaController.class.php");
require_once (__DIR__.'/AlojaErrorCollection.class.php');

/**
 * Operaciones comunes al cuestionario de alojamiento.
 * 
 */
class AlojaTempController extends AlojaController
{	
	/**
	 * Guarda los datos del cuestionario sin realizar validaciones.
	 * No se modifica el estado de validación del cueestionario (ni se establece si el cuestionario es creado).
	 * No se auditan estos accesos.
	 * @param AlojaCuestionario $aloja_cuest Cuestionario a guardar, con datos parciales.
	 */
	public function guardar_temp(AlojaCuestionario $aloja_cuest, $user_id)
	{
		//AQUI ESTADO IMPLICITO: EDITADO
		
		/// 2. Guardar cada uno de los items recibidos.
		return $this->guardar_cuestionario_temp($aloja_cuest);
	}
	
	/** 
	 * Guarda el cuestionario y todos sus detalles.
	 * @param AlojaCuestionario $cuestionario
	 */
	public function guardar_cuestionario_temp(AlojaCuestionario $cuestionario)
	{
		$dao = new AlojaTempDao();

		// Registro maestro
		$ok = $dao->guardar_cuestionario_temp($cuestionario);
		if (!$ok)
			return false;
		
		// TODO: Creo que esto sobraría. El identificador ya lo ha tomado del cuestionario...
		// guardar ha puesto un id al cuestionario
		$id = $cuestionario->id;
		
		// Registro de detalle movimientos
		if ($cuestionario->mov_por_ut != null)
		{
			foreach($cuestionario->mov_por_ut as $id_ut => $ut_mov)
			{
				$dao->guardar_esp_ut_temp($id, $id_ut, $ut_mov);
				
				// Calcular presentes_fin_mes
				$presentes_fin_mes = $ut_mov->get_presentes_ultimo_dia();
				$dao->guardar_presentes_mes_temp($id, $id_ut, $ut_mov->presentes_comienzo_mes, $presentes_fin_mes);
			}
		}
		
		// Registro de detalle habitaciones
		if ($cuestionario->habitaciones != null)
			$dao->guardar_habitaciones_temp($id, $cuestionario->habitaciones);
		
		// Registro de detalles personal y precios
		if ($cuestionario->personal != null || $cuestionario->precios != null)
			$dao->guardar_personal_precios_temp($id, $cuestionario->personal, $cuestionario->precios);
		
		return true;
	}

	/**
	 * Función que actualiza las tablas con la información de un cuestionario (también las tablas temporales)
	 * {@inheritDoc}
	 * @see AlojaController::valida_y_guardar()
	 */
	public function valida_y_guardar(AlojaCuestionario $aloja_cuest, $op_es_guardar, $user_id, $permitirCaso001)
	{
		// FIXME: Es posible que existan datos temporales de unidades territoriales no visibles.
		// En caso de envío ($op_es_guardar==false), hay que reescribir la función valida_y_guardar para que tras realizar las validaciones previas a la grabación
		// en las tablas de los datos recibidos, se compruebe la existencia de más datos temporales (correspondientes a otras unidades territoriales no visibles) y proceder a realizar esta misma validación.
		// Sólo cuando esta validación sea correcta, seguir con la grabación a las tablas definitivas. Si todo es correcto, se deben eliminar todos los datos temporales.
		// Si la validación previa de algunos de los datos temporales falla, se debe mostrar un aviso al usuario ofreciéndole saltar a una página con las UTs erróneas
		// o permanecer en la pantalla actual.
		
		// En caso de un envío, debemos considerar posibles datos temporales de unidades territoriales no visibles.
		// En caso de guardar, se entiende que el usuario quiere grabar y validar sólo los datos introducidos de las unidades territoriales visibles.
		if(!$op_es_guardar)
		{
			if(!$this->validar_temp($aloja_cuest, $permitirCaso001))
				return array($aloja_cuest->val_errors, false);
		}
		
		$errores = parent::valida_y_guardar($aloja_cuest, $op_es_guardar, $user_id, $permitirCaso001);
		
		if($errores[1] === true)
		{
			// Preparamos una lista de las UTs recibidas para eliminar la copia temporal preexistente. Si es una operación de envio de cuestionario, la lista es NULL para borrar todas.
			$id_uts=null;
			if($op_es_guardar)
			{
				$id_uts=array();
				if ($aloja_cuest->mov_por_ut != null)
				{
					foreach($aloja_cuest->mov_por_ut as $id_ut => $ut_mov)
						$id_uts[]=$id_ut;
				}
			}
			
			// Se grabaron los datos del cuestionario, debemos eliminar los datos temporales.
			$daoTemp = new AlojaTempDao();
			$daoTemp->borrar_datos_cuestionario($aloja_cuest->id,$op_es_guardar,$id_uts);
			
			// En caso de una operación guardar, debemos actualizar la información del cuestionario temporal con la nueva información recibida.
			if($op_es_guardar)
				$daoTemp->guardar_cuestionario_temp($aloja_cuest);
		}
		
		return $errores;
	}
	
	private function cargar_esp_uts_no_visibles(AlojaCuestionario $aloja_cuest)
	{
		// Comprobamos si existen datos de unidades territoriales no visibles al usuario que requieren validación previa antes del envío.
		
		// Paso 1: Obtenemos los identificadores de las unidades territoriales visibles en el formulario de entrada.
		$id_uts=array();
		if ($aloja_cuest->mov_por_ut != null)
		{
			foreach($aloja_cuest->mov_por_ut as $id_ut => $ut_mov)
				$id_uts[]=$id_ut;
		}
		
		// Paso 2: Obtenemos la lista de identificadores de unidades territoriales, no visibles en el formulario, para los que hay datos temporales.
		$db = new Istac_Sql();
		$sql = DbHelper::prepare_sql("SELECT id_unidad_territ FROM tb_aloja_presentes_mes_temp
				WHERE id_cuestionario=:id AND id_unidad_territ NOT IN (".implode($id_uts,",").") ORDER BY id_unidad_territ",
				array(':id' => (string)$aloja_cuest->id));
		$db->query($sql);
		
		$id_uts_novisibles=array();
		while ($db->next_record())
			$id_uts_novisibles[]=$db->f('id_unidad_territ');
		
		// Paso 3: Añadimos a la información cargada en memoria del cuestionario, la información de las unidades territoriales no visibles.
		if(count($id_uts_novisibles)>0)
		{
			// TODO: Creo que no es necesaria la información del nombre del país ni si es nacional o no. Comprobar.
			
			$result_uts=array();
			
			// Cargamos en el cuestionario la información temporal de las unidades territoriales no visibles.
			
			// Creamos los objetos que van a contener la información de las unidades territoriales no visibles.
			foreach($id_uts_novisibles as $id)
			{
				$result_uts[$id] = new AlojaUTMovimientos();
			}
			
			// Recuperamos de la BDD, la información de los movimientos diarios de ESP de las UTs no visibles.
			$comando="SELECT esp.id_unidad_territ, esp.dia, esp.entradas, esp.salidas, esp.pernoctaciones, ut.literal, cfg_grupos.es_nacional
			 FROM TB_ALOJA_ESP_DIARIOS_TEMP esp
			 INNER JOIN TB_UNIDADES_TERRITORIALES ut ON ut.id = esp.id_unidad_territ
			 INNER JOIN TB_CONFIG_UNID_TERRIT cfg ON ut.id = cfg.id_unidad_territ
			 INNER JOIN TB_CONFIG_GRUPOS_UNID_TERRIT cfg_grupos ON cfg_grupos.ID = cfg.ID_GRUPO_UNID_TERRIT
			 WHERE id_cuestionario=:id AND esp.id_unidad_territ IN (".implode($id_uts_novisibles,",").")";
			$sql = DbHelper::prepare_sql($comando,array(':id' => (string)$aloja_cuest->id));
			$db->query($sql);
			while ($db->next_record())
			{
				$id = $db->f('id_unidad_territ');
				$esp = new AlojaESP();
				$esp->entradas = $db->f('entradas');
				$esp->salidas = $db->f('salidas');
				$esp->pernoctaciones = $db->f('pernoctaciones');
				$dia = $db->f('dia');
				$result_uts[$id]->movimientos[$dia] = $esp;
			}
			$db->disconnect();			

			// Obtener los presentes a comienzo de mes
			$sql = DbHelper::prepare_sql("SELECT id_unidad_territ, presentes_comienzo_mes
    			FROM tb_aloja_presentes_mes_temp WHERE id_cuestionario=:id".
				($id_uts_novisibles==NULL ? "" : " AND id_unidad_territ in (".implode($id_uts_novisibles,",").")"),
					array(':id' => (string)$aloja_cuest->id));
	
			$db->query($sql);
	
			while ($db->next_record())
			{
				$id = $db->f('id_unidad_territ');
				$result_uts[$id]->presentes_comienzo_mes = $db->f('presentes_comienzo_mes');
			}
			
			// Finalmente agregamos al objeto cuestionario, toda la información de las unidades territoriales recuperada de la BDD.
			foreach($result_uts as $id_ut => $info_ut)
			{
				$aloja_cuest->mov_por_ut[$id_ut]=$info_ut;
			}
		}
		
		//$aloja_cuest->$id_uts_novisibles=$id_uts_novisibles;
		return (count($id_uts_novisibles)>0);
	}
	
	private function validar_temp(AlojaCuestionario $aloja_cuest, $permitirCaso001)
	{
		// Comprobamos si existen datos de unidades territoriales no visibles al usuario que requieren validación previa antes del envío.
		if($this->cargar_esp_uts_no_visibles($aloja_cuest))
		{
			// FIXME: Esto debería cargarse como parte de la configuración de la aplicación.
			$revisar_uts_novisibles=false;
			
			if($revisar_uts_novisibles)
			{
				// Se requiere que el usuario sea informado de que hay información temporal sobre unidades territoriales no mostradas en la página de paises actual.
				// Se detiene el envío del cuestionario y se invita al usuario a saltar a la página donde se muestra el primer país con información temporal a revisar.
				$aloja_cuest->val_errors->log_error(ERROR_UTS_NOREVISADA, "Unidades territoriales con datos no revisados.");
				
				return false;
			}
		}
			
		return true;
	}
	
	// Debido a que el cuestionario se ha creado necesariamente al entrar al formulario,supuestamente esta función nunca será llamada...
	public function crear_nuevo_cuestionario($mes, $ano, $est_id, $user_id, $tipo_carga, $modo_porcentaje, $guardar)
	{
		$cuestionario=parent::crear_nuevo_cuestionario($mes, $ano, $est_id, $user_id, $tipo_carga, $modo_porcentaje, $guardar);
		if($guardar)
		{
			$dao = new AlojaTempDao();
			$dao->guardar_cuestionario_temp($cuestionario);
		}
		return $cuestionario;
	}
	
	/**
	 * Actualiza el cuestionario con la información temporal si la hay.
	 * @param AlojaCuestionario $cuestionario
	 */
	public function actualizar_cuestionario(AlojaCuestionario $cuestionario)
	{
		$daotemp = new AlojaTempDao();
		$daotemp->actualizar_cuestionario($cuestionario);
	}
	
	public function cargar_encuesta_temp(AlojaCuestionario $cuestionario, & $aloja_estado)
	{
		$daotemp = new AlojaTempDao();
		$habs = $daotemp->cargar_habitaciones_temp($cuestionario->id);
		if(!empty($habs))
			$aloja_estado['hab'] = $habs;
		$pers_prec = $daotemp->cargar_personal_precios_temp($cuestionario->id);
		if(!empty($pers_prec))
			$aloja_estado['pers_prec'] = $pers_prec;
	}
}

?>