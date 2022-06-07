<?php

/**
 * Metodos de ayuda para accesos a la base de datos.
 * @author 
 *
 */
class DbHelper 
{
	/**
	 * Ayuda a preparar un query que utilice parametros.
	 * Este método sustituye las cadenas indicadas como keys en params, por los valores almacenados en params.
	 * Si el tipo de un valor es string, se añadirán automaticamente las comillas y se ejecutará addslashes sobre el
	 * para escapar los caracteres.. 
	 * @param string $sql query con posibles parametros a sustituir.
	 * @param array $params Array con pares k => (tipo)v. 
	 * @return la query preparada para ser ejecutada por el driver de BD.
	 */
	public static function prepare_sql($sql, $params)
	{
		foreach($params as $k => $v)
		{
			if (!isset($v) || is_null($v) || strlen($v) == 0)
				$sql = str_replace($k, 'null', $sql);
			else if (is_string($v))
				$sql = str_replace($k, DbHelper::prepare_string($v), $sql);
			else
				$sql = str_replace($k, $v, $sql);
		}
		return $sql;
	}
	
	/**
	 * Prepara un string siguiendo las reglas de escapado de oracle.
	 * @param unknown_type $value
	 */
	public static function prepare_string($v)
	{
		return "'".str_replace("'","''",$v)."'";
	}
	
	/**
	 * Obtiene el siguiente valor en la secuencia de oracle con el nombre dado.
	 * @param string $seqname el nombre de la secuencia de BD a consultar.
	 * @return El valor devuelto por la secuencia, o FALSE en caso de que no se haya podido obtener.
	 */
	public static function newid_from_seq($oci_db, $seqname)
	{
		$oci_db->query("select ".$seqname.".nextval from dual");
		if (!$oci_db->next_record())
		{
			Log::error("newid_from_seq: No se ha podido obtener un nuevo identificador de la secuencia ".$seqname.".");
			return FALSE;
		}
		return $oci_db->f('nextval');
	}
}

?>