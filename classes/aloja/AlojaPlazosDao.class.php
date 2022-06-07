<?php

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../../lib/DateHelper.class.php");
require_once(__DIR__."/../../lib/DbHelper.class.php");

/**
 * Gestion de DAO de configuracion de plazos para la encuesta de alojamiento. 
 *
 */
class AlojaPlazosDao
{
	
	/**
	 * Obtiene la lista de grupos de establecimiento (tabla tb_grupo_establecimientos)
	 */
	public function cargar_grupos_establecimiento()
	{
		$sql = "select id_grupo, descripcion from tb_grupo_establecimientos order by id_grupo";
		
		$db = new Istac_Sql();
		
		$db->query($sql);
		
		$result = array();
		while ($db->next_record())
		{
			$r = array();
			$r['id'] = $db->f('id_grupo');
			$r['nombre'] = $db->f('descripcion');
			$result[] = $r;
		}
		return $result;
	}
	
	/**
	 * Obtiene la lista de plazos almacenados en la BBDD para el grupo de establecimiento indicado.
	 * @param unknown_type $grupo_id
	 */
	public function cargar_plazos_por_grupo($grupo_id)
	{
		$sql = DbHelper::prepare_sql("select mes, dia_mes_siguiente from tb_aloja_plazos_mes where id_grupo = :grupo_id order by mes",
				array(':grupo_id' => (string)$grupo_id));
		
		$db = new Istac_Sql();
		
		$db->query($sql);
		
		$result = array();
		while ($db->next_record())
		{
			$mes = (int)$db->f('mes');
			$result[ $mes ] = $db->f('dia_mes_siguiente');
		}
		return $result;
	}
	
	/**
	 * Actualiza los plazos almacenados por mes para el grupo indicado.
	 * @param unknown_type $grupo_id
	 * @param unknown_type $original_plazos
	 * @param unknown_type $nuevos_plazos
	 */
	public function actualizar_plazos($grupo_id, $original_plazos, $nuevos_plazos)
	{
		// Comparar plazos originales y nuevos y generar array de cambios
		$cambios = array();
		for($i=1;$i<=12;$i++)
		{
			$cambio = $this->check_cambio($nuevos_plazos[$i], $original_plazos[$i]);
			if ($cambio != null)
			{
				$cambios[$i] = $cambio;
			}
		}
		
		/// Si no hay cambio, no hace nada.
		if ($cambios == null)
			return true;
		
		$insert_sql = "insert into tb_aloja_plazos_mes (ID_GRUPO,MES,DIA_MES_SIGUIENTE) VALUES (:grupo_id,:mes,:dia_mes)";
		$delete_sql = "delete from tb_aloja_plazos_mes where id_grupo = :grupo_id and mes = :mes";
		$update_sql = "update tb_aloja_plazos_mes set DIA_MES_SIGUIENTE=:dia_mes where id_grupo = :grupo_id and mes = :mes";
		
		$ok = true;
		$db = new Istac_Sql();
		foreach($cambios as $mes=>$cambio)
		{
			if ($cambio[0] == 'd')
			{
				$sql = DbHelper::prepare_sql($delete_sql, array(':grupo_id'=>(string)$grupo_id, ':mes'=>(string)$mes));
			} 
			else if ($cambio[0] == 'i')
			{
				$sql = DbHelper::prepare_sql($insert_sql, array(':grupo_id'=>(string)$grupo_id, ':mes'=>(string)$mes, ':dia_mes' => (string)$cambio[1]));
			}
			else if ($cambio[0] == 'u')
			{
				$sql = DbHelper::prepare_sql($update_sql, array(':grupo_id'=>(string)$grupo_id, ':mes'=>(string)$mes, ':dia_mes' => (string)$cambio[1]));
			}
			
			
			$db->query($sql);
				
			$ok &= ($db->affected_rows() > 0);
		}
		
		return $ok;
	}
	
	/**
	 * Funcion para detectar de que tipo de cambio se trata. Devuelve nulo si no hay cambio, o un array con la posicion 0 el tipo cambio (i,d,u) y en la posicion 1 el nuevo valor.
	 * @param unknown_type $n nuevo valor
	 * @param unknown_type $o valor anterior
	 * @return multitype:string NULL |NULL|multitype:string unknown 
	 */
	private function check_cambio($n, $o)
	{
		if ($n == null)
		{
			if ($o != null)
				return array('d',null); //Operacion de borrado
			else
				return null; // No se hace nada
		}
		else
		{
			if ($o == null)
			{
				return array('i', $n); /// Insertar el nuevo valor.
			}
			else if ($n != $o)
			{
				return array('u', $n); /// Actualizar el nuevo valor.
			}
			else
			{
				return null; // No se hace nada
			}
		}
	}
}


?>