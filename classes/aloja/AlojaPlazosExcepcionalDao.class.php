<?php

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../../lib/DateHelper.class.php");
require_once(__DIR__."/../../lib/DbHelper.class.php");
require_once(__DIR__."/../../lib/RowDbIterator.class.php");

class AlojaPlazoExcepcional
{
	var $id_estab;
	var $mes;
	var $ano;
	var $dia_mes_sig;
	var $notas;
	
	public function validar()
	{
		list($mes_siguiente, $ano_siguiente) = DateHelper::mes_siguiente($this->mes, $this->ano);
		$n_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_siguiente, $ano_siguiente);
		return (isset($this->dia_mes_sig) && 0 < $this->dia_mes_sig && $this->dia_mes_sig <= $n_dias_mes);
	}
	
	public static function validarPlazo($ano,$mes,$dia)
	{
		list($mes_siguiente, $ano_siguiente) = DateHelper::mes_siguiente($mes, $ano);
		$n_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_siguiente, $ano_siguiente);
		return (isset($dia) && 0 < $dia && $dia <= $n_dias_mes);
	}
}

/**
 * Gestion de DAO de configuracion de plazos excepcionales para la encuesta de alojamiento.
 *
 */
class AlojaPlazosExcepcionalDao
{
	var $db;
	
	public function __construct()
	{
	 	$this->db = new Istac_Sql();
	}
	
	/**
	 * Obtiene la lista de grupos de establecimiento (tabla tb_grupo_establecimientos)
	 */
	public function cargar_plazos()
	{
		$sql = "SELECT TB_ALOJA_PLAZOS_ESTAB.ID_ESTABLECIMIENTO,
		TB_ALOJA_PLAZOS_ESTAB.MES,
		TB_ALOJA_PLAZOS_ESTAB.ANO,
		TB_ALOJA_PLAZOS_ESTAB.DIA_MES_SIGUIENTE,
		TB_ALOJA_PLAZOS_ESTAB.NOTAS,
		TB_ESTABLECIMIENTOS_UNICO.NOMBRE_ESTABLECIMIENTO
		FROM TB_ALOJA_PLAZOS_ESTAB
		INNER JOIN TB_ESTABLECIMIENTOS_UNICO
		on TB_ESTABLECIMIENTOS_UNICO.ID_ESTABLECIMIENTO = TB_ALOJA_PLAZOS_ESTAB.ID_ESTABLECIMIENTO
		ORDER BY TB_ALOJA_PLAZOS_ESTAB.ANO DESC, TB_ALOJA_PLAZOS_ESTAB.MES, TB_ESTABLECIMIENTOS_UNICO.ID_ESTABLECIMIENTO";
	
		$db = new Istac_Sql();
	
		$db->query($sql);
	
		$result = array();
		while ($db->next_record())
		{
			$r = array();
			$r['id_est'] = $db->f('id_establecimiento');
			$r['nombre_est'] = $db->f('nombre_establecimiento');
			$r['mes'] = $db->f('mes');
			$r['ano'] = $db->f('ano');
			$r['dia_mes_sig'] = $db->f('dia_mes_siguiente');
			$r['notas'] = $db->f('notas');
			$result[] = $r;
		}
		return $result;
	}
	
	public function insertar($plazoGuardar)
	{
		if (!$plazoGuardar->validar()) return false;
		
		$estabDao = new Establecimiento();
		$estabRows = $estabDao->searchByCodigo($plazoGuardar->id_estab);
		if(!$estabRows->has_rows()) return false;
		
		$sql = DbHelper::prepare_sql("INSERT INTO tb_aloja_plazos_estab (id_establecimiento, mes, ano, dia_mes_siguiente, notas) VALUES
				(:id_estab, :mes, :ano, :dia_mes_sig, :notas)",
				array(":id_estab"	=>	$plazoGuardar->id_estab,
						":mes"		=>	$plazoGuardar->mes,
						":ano"		=>	$plazoGuardar->ano,
						":dia_mes_sig"			=>	$plazoGuardar->dia_mes_sig,
				        ":notas"    => isset($plazoGuardar->notas)?$plazoGuardar->notas:'NULL'));
		@$this->db->query($sql);
		return ($this->db->affected_rows()!=0);
	}
	
	public function insertarMultiple($estids,$ano,$mes,$dia,$notas)
	{
		$errores=array();
		
		if($estids==null || $ano==null || $mes==null || $dia==null)
		{
			array_push($errores,"La información proporcionada es incompleta.");
			return $errores;
		}
		
		if(!AlojaPlazoExcepcional::validarPlazo($ano,$mes,$dia))
		{
			array_push($errores,"El plazo indicado no es válido.");
			return $errores;
		}

		$ids=explode(",",$estids);
		foreach ($ids as $estid)
		{
			$sql = DbHelper::prepare_sql("INSERT INTO tb_aloja_plazos_estab (id_establecimiento, mes, ano, dia_mes_siguiente, notas) VALUES
					(:id_estab, :mes, :ano, :dia_mes_sig, :notas)",
					array(":id_estab"	=>	$estid,
							":mes"		=>	$mes,
							":ano"		=>	$ano,
							":dia_mes_sig"			=>	$dia,
					        ":notas"    => isset($notas)?$notas:'NULL'));
			@$this->db->query($sql);
			if($this->db->affected_rows()==0)
				array_push($errores,"El código de establecimiento '".$estid."' no es válido.");
		}
		
		return $errores;
	}
	
	public function guardar($plazoGuardar)
	{
		if (!$plazoGuardar->validar()) return false;
		
		/*
		 * // De momento, no modificamos el campo NOTAS...
		$sql = DbHelper::prepare_sql("UPDATE tb_aloja_plazos_estab SET dia_mes_siguiente = :dia_mes_sig, notas = :notas WHERE
				id_establecimiento = :id_estab AND mes = :mes AND ano = :ano",
				array(":id_estab"	=>	$plazoGuardar->id_estab,
						":mes"		=>	$plazoGuardar->mes,
						":ano"		=>	$plazoGuardar->ano,
						":dia_mes_sig"			=>	$plazoGuardar->dia_mes_sig,
				        ":notas"    => isset($plazoGuardar->notas)?$plazoGuardar->notas:'NULL'));
		*/
		$sql = DbHelper::prepare_sql("UPDATE tb_aloja_plazos_estab SET dia_mes_siguiente = :dia_mes_sig WHERE
				id_establecimiento = :id_estab AND mes = :mes AND ano = :ano",
		    array(":id_estab"	=>	$plazoGuardar->id_estab,
		        ":mes"		=>	$plazoGuardar->mes,
		        ":ano"		=>	$plazoGuardar->ano,
		        ":dia_mes_sig"			=>	$plazoGuardar->dia_mes_sig));
		    @$this->db->query($sql);
		return ($this->db->affected_rows()!=0);
	}
	
	public function guardarNotas($plazoGuardar)
	{
	    $sql = DbHelper::prepare_sql("UPDATE tb_aloja_plazos_estab SET notas = :notas WHERE
				id_establecimiento = :id_estab AND mes = :mes AND ano = :ano",
	        array(":id_estab"	=>	$plazoGuardar->id_estab,
	            ":mes"		=>	$plazoGuardar->mes,
	            ":ano"		=>	$plazoGuardar->ano,
	            ":notas"    => isset($plazoGuardar->notas)?$plazoGuardar->notas:'NULL'));
	        @$this->db->query($sql);
	        return ($this->db->affected_rows()!=0);
	}
	
	public function eliminar($plazoGuardar)
	{
		$sql = DbHelper::prepare_sql("DELETE FROM tb_aloja_plazos_estab WHERE id_establecimiento = :id_estab AND mes = :mes AND ano = :ano",
				array(":id_estab"	=>	$plazoGuardar->id_estab,
						":mes"		=>	$plazoGuardar->mes,
						":ano"		=>	$plazoGuardar->ano));
	
		@$this->db->query($sql);
		return ($this->db->affected_rows()!=0);
	}

	public function eliminarMultiple($estids,$ano,$mes)
	{
		$errores=array();
	
		if($estids==null || $ano==null || $mes==null)
		{
			array_push($errores,"La información proporcionada es incompleta.");
			return $errores;
		}
	
		$ids=explode(",",$estids);
		foreach ($ids as $estid)
		{
			$sql = DbHelper::prepare_sql("DELETE FROM tb_aloja_plazos_estab WHERE id_establecimiento = :id_estab AND mes = :mes AND ano = :ano",
					array(":id_estab"	=>	$estid,
							":mes"		=>	$mes,
							":ano"		=>	$ano));
			@$this->db->query($sql);
			if($this->db->affected_rows()==0)
				array_push($errores,"El plazo excepcional para el establecimiento con código '".$estid."' no ha podido ser eliminado.");
		}
	
		return $errores;
	}
	
	/*public function guardar($plazoGuardar)
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
	}*/
}

?>