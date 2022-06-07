<?php
require_once(__DIR__."/../lib/DbHelper.class.php");

class NoticiaFeed
{
	var $id;
	var $titulo;
	var $url;
	var $max;
	var $hasPriority;	
	var $activado;
}


/**
 * Acceso a los datos almacenados relativos a canales de noticias (RSS o ATOM).
 * @author himar
 *
 */
class NoticiasDao 
{
	/**
	 * Obtiene la lista de fuentes de noticias ACTIVDADAS almacenadas en la BBDD
	 */
	public function getNoticiasFeeds()
	{		
		$sql = "SELECT id, titulo, url, num_entradas, es_istac FROM TB_CANALES_NOTICIAS WHERE upper(ACTIVADO)='S'";
		
		$db = new Istac_Sql();
		
		$db->query($sql);
		
		$result = array();
		
		while ($db->next_record()) 
		{
			$n1 = new NoticiaFeed();
			//$n1->id				= $db->f('id');
			$n1->titulo      		= $db->f('titulo');
			$n1->url      		= $db->f('url');
			$n1->max      		= (int)$db->f('num_entradas');
			$n1->hasPriority	= ($db->f('es_istac') == 'S');
			$result[] = $n1;
		}
		return $result;		
	}
	
	/**
	 * Obtiene la lista completa de fuentes de noticias almacenadas en la BBDD
	 */
	public function obtenerTodos()
	{
		$sql = "SELECT id, titulo, url, num_entradas, activado, es_istac FROM TB_CANALES_NOTICIAS";
	
		$db = new Istac_Sql();
	
		$db->query($sql);
	
		$result = array();
	
		while ($db->next_record())
		{
			$n1 = new NoticiaFeed();
			$n1->id				= $db->f('id');
			$n1->titulo      		= $db->f('titulo');
			$n1->url      		= $db->f('url');
			$n1->max      		= (int)$db->f('num_entradas');
			$n1->hasPriority	= ($db->f('es_istac') == 'S');
			$n1->activado	= ($db->f('activado') == 'S');
			$result[] = $n1;
		}
		return $result;
	}
		
	/**
	 * Inserta un nuevo canal en la BBDD.
	 * @param NoticiaFeed $nuevoCanal
	 * @param bool $activado
	 */
	public function insert(NoticiaFeed $nuevoCanal)
	{
		$sql = DbHelper::prepare_sql("INSERT INTO TB_CANALES_NOTICIAS (titulo, url, num_entradas, activado, es_istac) values (:titulo, :url, :num_entradas, :activado, :es_istac)",
				array ( ':titulo' => (string)$nuevoCanal->titulo,
						':url' => (string)$nuevoCanal->url,
						':num_entradas' => (int)$nuevoCanal->max,
						':activado' => (string)($nuevoCanal->activado ? 'S': 'N'),
						':es_istac' => (string)($nuevoCanal->hasPriority ? 'S' : 'N')));
		$db = new Istac_Sql();
		$db->query($sql);
		return ($db->affected_rows() > 0);
	}
	
	/**
	 * ACtualiza los datos almacenados de un canal.
	 * @param NoticiaFeed $canal
	 * @param bool $activado
	 */
	public function update(NoticiaFeed $canal)
	{
		$sql = DbHelper::prepare_sql("UPDATE TB_CANALES_NOTICIAS SET url = :url, titulo = :titulo, num_entradas = :num_entradas, activado = :activado, es_istac = :es_istac WHERE id = :id",
				array ( ':id' => $canal->id,
						':titulo' => (string)$canal->titulo,
						':url' => (string)$canal->url,
						':num_entradas' => $canal->max,
						':activado' => (string)($canal->activado ? 'S': 'N'),
						':es_istac' => (string)($canal->hasPriority ? 'S' : 'N')));
		$db = new Istac_Sql();
		$db->query($sql);
		return ($db->affected_rows() > 0);		
	}
	
	/**
	 * Elimina de la BBDD el canal indicado por su id.
	 * @param unknown_type $canalid
	 */
	public function delete($canalid)
	{
		$sql = DbHelper::prepare_sql("DELETE FROM TB_CANALES_NOTICIAS WHERE id = :id",
				array (':id' => $canalid));
		$db = new Istac_Sql();
		$db->query($sql);
		return ($db->affected_rows() > 0);
	}	
}

?>