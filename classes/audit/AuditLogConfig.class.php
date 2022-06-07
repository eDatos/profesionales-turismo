<?php

require_once(__DIR__."/../../lib/RowDbIterator.class.php");
require_once(__DIR__."/../../lib/DbHelper.class.php");

/**
 * Suministra los métodos de acceso a los datos sobre la configuracion del registro.
 *
 */
class AuditLogConfig 
{
	/**
	 * Obtiene el listado de tipos de acciones configurado (almacenado en la BBDD).
	 * @return RowDbIterator Iterador sobre el listado.
	 */
	public function loadActionTypes()
	{
		$db = new Istac_Sql();
		
		$sql = "SELECT id, descripcion FROM tb_tipos_de_acciones ORDER BY descripcion";
		
		$db->query($sql);
		return new RowDbIterator($db, array('id','descripcion'));
	}
	
	/**
	 * Obtiene el listados de los grupos de operaciones, obteniendo el nombre, su descripción y el estado de activacion.
	 * @return RowDbIterator
	 */
	public function loadConfigOptions()
	{
		$db = new Istac_Sql();
		
		$sql = "SELECT id,grupo,descripcion,estado FROM tb_opciones_config ORDER BY grupo";
		
		$db->query($sql);
		return new RowDbIterator($db, array('id','grupo','descripcion','estado'));
	}
	
	/**
	 * Elimina las acciones registradas en el log cuya fecha de registro sea ANTERIOR (exclusiva) a la fecha indicada.
	 * @param DateTime $befDate Fecha HASTA la que borrar el registro (en formato dd/mm/yyyy).
	 */
	public function clearEntriesBeforeDate($befDate)
	{
		$db = new Istac_Sql();
		$sql = DbHelper::prepare_sql("DELETE FROM TB_ACCIONES_LOG WHERE tb_acciones_log.id IN (SELECT accion_Id FROM tb_entradas_log WHERE fecha < to_date(:bef_fecha,'dd/mm/yyyy'))", 
					array(':bef_fecha' => (string)$befDate->format("d/m/Y")));
		
		$db->query($sql);
		
		return $db->affected_rows();
	}
	
	/**
	 * Actualiza la lista de flags de estado para los grupos de operaciones dados. 
	 * @param array $opt_cambiadas: Array asociativo con la lista que modificar en formato [id opcion] = nuevo valor de flag. 
	 * @return number Numero total de filas afectadas.
	 */
	public function updateConfigFlags($opt_cambiadas)
	{
		$affected_rows = 0;
		$db = new Istac_Sql();
		foreach ($opt_cambiadas as $opt_id => $nuevo_estado)
		{
			$sql = DbHelper::prepare_sql("UPDATE tb_opciones_config SET estado = :est WHERE id = :id",
					array(":est"=> (string)$nuevo_estado, ":id" => (int)$opt_id));
		
			Log::trace("Query: ".$sql);
			$db->query($sql);
		
			$affected_rows +=$db->affected_rows();
			//$db->disconnect();
		}
		return $affected_rows;
	}
}

?>