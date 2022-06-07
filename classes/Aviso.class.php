<?php
require_once(__DIR__."/../lib/DateHelper.class.php");
require_once(__DIR__."/../lib/DbHelper.class.php");
require_once(__DIR__."/../lib/RowDbIterator.class.php");

class Aviso 
{
	var $fecha_creacion;
	var $fecha_ini;
	var $fecha_fin;
	var $titulo;
	var $texto;
	var $id_grupo;
	
	public static function esValido($fecha_ini, $fecha_fin, $titulo, $aviso) 
	{
		if (DateHelper::parseDate($fecha_ini) === FALSE) return FALSE;
		if (DateHelper::parseDate($fecha_fin) === FALSE) return FALSE;
		
		if ($titulo == NULL) return FALSE;
		if ($aviso == NULL)  return FALSE;
	
		return TRUE;
	}
}

class AvisoDao
{
	var $db;
	
	public function __construct()
	{
	 	$this->db = new Istac_Sql();
	}
	
	public function cargar($fecha_creacion)
	{
		$sql = DbHelper::prepare_sql("SELECT TO_CHAR(fecha_creacion, 'dd-mm-yyyy HH24:MI:SS') fecha_creacion, 
				TO_CHAR(fecha_ini, 'DD-MM-YYYY') fecha_ini, TO_CHAR(fecha_fin, 'DD-MM-YYYY') fecha_fin, titulo, aviso, id_grupo
				FROM tb_avisos WHERE TO_CHAR(fecha_creacion, 'dd-mm-yyyy HH24:MI:SS')=:fecha_creacion",
				array(":fecha_creacion"   => (string)$fecha_creacion));		
		
		$this->db->query($sql);

		$aviso = NULL;
		if($this->db->next_record())
		{
			$aviso = new Aviso();
			$aviso->fecha_creacion = $this->db->f("fecha_creacion");
			$aviso->fecha_ini = $this->db->f("fecha_ini");
			$aviso->fecha_fin = $this->db->f("fecha_fin");
			$aviso->titulo = $this->db->f("titulo");
			$aviso->texto = $this->db->f("aviso");
			$aviso->id_grupo = $this->db->f("id_grupo");
		}
		return $aviso;
	}
	
	public function guardar($avisoGuardar)
	{
		if($avisoGuardar->fecha_creacion != NULL)
		{
		    $sql = DbHelper::prepare_sql("UPDATE tb_avisos SET
		            fecha_ini=TO_DATE(:fecha_ini,'DD-MM-YYYY'), 
		            fecha_fin=TO_DATE(:fecha_fin,'DD-MM-YYYY'), 
		            titulo=:titulo, aviso=:aviso, id_grupo=:id_grupo
		            where fecha_creacion=TO_DATE(:fecha_creacion,' dd-mm-yyyy HH24:MI:SS')",
		    		array(":fecha_ini"		=>	(string)$avisoGuardar->fecha_ini,
		    			  ":fecha_fin"		=>	(string)$avisoGuardar->fecha_fin,
		    			  ":titulo"			=>	(string)$avisoGuardar->titulo,
		    			  ":aviso"			=>	(string)$avisoGuardar->texto,
		    			  ":id_grupo"		=>	(int)$avisoGuardar->id_grupo,
		    			  ":fecha_creacion"	=>	(string)$avisoGuardar->fecha_creacion));			
		}
		else {
			$avisoGuardar->fecha_creacion = date("d-m-Y H:i:s");
			$sql = DbHelper::prepare_sql("INSERT INTO tb_avisos (fecha_creacion, fecha_ini, fecha_fin, aviso, titulo, id_grupo) VALUES 
					(TO_DATE(:fecha_creacion,'dd-mm-yyyy HH24:MI:SS'), TO_DATE(:fecha_ini,'DD-MM-YYYY'),
					TO_DATE(:fecha_fin,'DD-MM-YYYY'),:texto, :titulo, :id_grupo)",
					array(":fecha_creacion"	=>	(string)$avisoGuardar->fecha_creacion,
						  ":fecha_ini"		=>	(string)$avisoGuardar->fecha_ini,
		    			  ":fecha_fin"		=>	(string)$avisoGuardar->fecha_fin,
						  ":texto"			=>	(string)$avisoGuardar->texto,
						  ":titulo"			=>	(string)$avisoGuardar->titulo,
		    			  ":id_grupo"		=>	(int)$avisoGuardar->id_grupo)); 
		}
		$this->db->query($sql);
		return ($this->db->affected_rows()!=0);
	}
	
	public function eliminar($avisoEliminar)
	{
		$sql = "DELETE FROM tb_avisos WHERE fecha_creacion=TO_DATE('" . $avisoEliminar->fecha_creacion. "','dd-mm-yyyy HH24:MI:SS')";
		
		$this->db->query($sql);
		return ($this->db->affected_rows()!=0);
	}	
	
	public function filtrarByIdGrupo($id_grupo)
	{
		$fecha = new DateHelper();
		$sql = DbHelper::prepare_sql("SELECT TO_CHAR(fecha_creacion, 'dd-mm-yyyy HH24:MI:SS') fecha_creacion,
				TO_CHAR(fecha_ini, 'dd-mm-yyyy') fecha_ini, TO_CHAR(fecha_fin, 'dd-mm-yyyy') fecha_fin,
				titulo, aviso, id_grupo
				 FROM tb_avisos WHERE (id_grupo=:id_grupo OR id_grupo=0)
				 AND (:fecha_sistema >= to_char(fecha_ini,'yyyy-mm-dd')
                      AND :fecha_sistema <= to_char(fecha_fin,'yyyy-mm-dd'))
				 ORDER BY fecha_ini DESC",
			   array(":id_grupo"		=>	(int)$id_grupo,
					 ":fecha_sistema" 	=>  (string)$fecha->fecha_sistema));
		
		$avisos = array();
		$this->db->query($sql);
		return new RowDbIterator($this->db, array('fecha_creacion', 'fecha_ini', 'fecha_fin', 'titulo', 'aviso', 'id_grupo'));
	}
		
	public function obtenerTodos()
	{
		$sql = "SELECT TO_CHAR(fecha_creacion, 'dd-mm-yyyy HH24:MI:SS') fecha_creacion,
				TO_CHAR(fecha_ini, 'dd-mm-yyyy') fecha_ini_t, TO_CHAR(fecha_fin, 'dd-mm-yyyy') fecha_fin,
				titulo, aviso, id_grupo
				FROM tb_avisos ORDER BY fecha_ini DESC";

		$avisos = array();
		$this->db->query($sql);
		return new RowDbIterator($this->db, array('fecha_creacion', 'fecha_ini_t', 'fecha_fin', 'titulo', 'aviso', 'id_grupo'));		
	}
}

?>