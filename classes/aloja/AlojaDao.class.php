<?php

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../../lib/DateHelper.class.php");
require_once(__DIR__."/../../lib/DbHelper.class.php");
require_once(__DIR__."/AlojaCuestionario.class.php");
require_once(__DIR__."/AlojaUTStat.class.php");


/** Valores de estado de validación del cuestionario (variable validacion) */ 
define("EV_CUESTIONARIO_COMPLETO", "1");
define("EV_CUESTIONARIO_INCOMPLETO", "2");
define("EV_VALIDADO_CRUZADAS", "3");
define("EV_VALIDADO_CON_AVISOS", "4");
define("EV_VALIDADO_COMPLETO", "5");

class AlojaDao
{
        
    /**
     * Devuelve un objeto cuestionario si ha cargado los datos (se ha encontrado el registro), nulo en caso contrario (no se ha encontrado).
     */
    public function cargar_registro_cuestionario($est_id, $mes, $ano)
    {
        $sql = DbHelper::prepare_sql("SELECT ID,ID_USUARIO,ID_TIPO_CARGA,CODIGO_REGISTRO, TO_CHAR(FECHA_RECEPCION,'dd/mm/yyyy HH24:MI:SS') as fr, TO_CHAR(FECHA_CIERRE,'dd/mm/yyyy HH24:MI:SS') as fc, MODO_ESP, MODO_HORIZONTAL, MODO_PORCENTAJE, DIAS_ABIERTO, VALIDACION, ID_MOTIVO_EXCESO_PLAZAS, ID_MOTIVO_EXCESO_HABIT, DETALLE_MOTIVO_EXCESO_PLAZAS, DETALLE_MOTIVO_EXCESO_HABIT FROM tb_aloja_cuestionarios
                                        WHERE id_establecimiento=:estid
                                        AND mes=:mes
                                        AND ano=:ano", array(':estid' => (string)$est_id,
                                                             ':mes' => (int)$mes,
                                                             ':ano' => (int)$ano));
        $db = new Istac_Sql();
        $db->query($sql);
        
        if($db->next_record())
        {
        	$aloja_cuestionario = new AlojaCuestionario($mes, $ano, $est_id);
            $aloja_cuestionario->id = $db->f("id");
            $aloja_cuestionario->userid_declarante = $db->f("id_usuario");
            $aloja_cuestionario->tipo_carga = $db->f("id_tipo_carga");
            $aloja_cuestionario->codigo_registro = $db->f("codigo_registro");
            $fr = DateTime::createFromFormat('!d/m/Y H:i:s', $db->f("fr"));
            $aloja_cuestionario->fecha_recepcion = ($fr === false)? null : $fr;
            $fc = DateTime::createFromFormat('!d/m/Y H:i:s', $db->f("fc"));
            $aloja_cuestionario->fecha_cierre = ($fc === false)? null: $fc;
            $aloja_cuestionario->modo_introduccion= $db->f("modo_esp");
            $aloja_cuestionario->modo_cumplimentado = $db->f("modo_horizontal");
            $aloja_cuestionario->modo_porcentaje = $db->f("modo_porcentaje");
            if ($db->f("dias_abierto") != null)
            	$aloja_cuestionario->dias_abierto = (int)$db->f("dias_abierto");
            $aloja_cuestionario->validacion = $db->f("validacion");
            
            $aloja_cuestionario->excesoPlazas = $db->f("id_motivo_exceso_plazas");
            $aloja_cuestionario->excesoPlazasDetalle = $db->f("detalle_motivo_exceso_plazas");
            $aloja_cuestionario->excesoHabitaciones = $db->f("id_motivo_exceso_habit");
            $aloja_cuestionario->excesoHabitacionesDetalle = $db->f("detalle_motivo_exceso_habit");
            
            return $aloja_cuestionario;
        }
        return null;
    }
    
    /**
     * Obtiene el dia de plazo (dia del mes siguiente al mes preguntado) de la encuesta, o false si no existe plazo. 
     * @param unknown_type $grupo_est
     * @param unknown_type $mes
     * @return number|boolean
     */
    public function cargar_dia_plazo($estid, $grupo_est, $mes,$ano)
    {
    	$db = new Istac_Sql();
    	
    	$sql = DbHelper::prepare_sql("select dia_mes_siguiente from TB_ALOJA_PLAZOS_ESTAB
    	where ID_ESTABLECIMIENTO = :estid and MES=:mes and ANO=:ano",
    			array(':estid' => (string)$estid,
    					':mes' => (int)$mes,
    					':ano' => (int)$ano));
    	
    	$db->query($sql);
    	if ($db->next_record())
    	{
    		return (int)$db->f("dia_mes_siguiente");
    	}
    	
    	$sql = DbHelper::prepare_sql("select * from tb_aloja_plazos_mes
									  where (id_grupo = :grupo or id_grupo = 0) and mes = :mes
									  order by id_grupo desc", 
    			array(':mes' => (int)$mes, 
    				':grupo' => (int)(($grupo_est != null)? $grupo_est : 0)));
    	
    	
    	$db->query($sql);
    	
    	if ($db->next_record())
    	{
    		return (int)$db->f("dia_mes_siguiente");
    	}
    	return false;
    }
    
    /**
     * Obtiene los ultimos modos de introduccion y cumplimentado utilizados por el establecimineto antes del mes y año indicados.
     * @param unknown_type $estab_id
     * @param unknown_type $mes_antes
     * @param unknown_type $ano_antes
     */
    public function cargar_ultimos_modos($estab_id, $mes_antes, $ano_antes)
    {
    	// Modos por defecto en caso de no encontrarse ninguno.
    	$modo_entrada_datos = MODO_INTRO_ES;
    	$modo_cumplimentado = MODO_CUMPL_VER;
    	$modo_porcentaje = MODO_PORC_NUM;
    
    	// FIXME: Posible error en el select interno? Los campos usados en la ordenación deben estar entre los devueltos por el SELECT.
    	$sql = DbHelper::prepare_sql( "select modo_esp, modo_horizontal, modo_porcentaje from
    			(select modo_esp,modo_horizontal, modo_porcentaje from tb_aloja_cuestionarios
    			where id_establecimiento = :estid and not(mes = :mes and ano = :ano) order by ano desc, mes desc)
    			where rownum < 2", array(':estid' => $estab_id, ':mes' => $mes_antes, ':ano' => $ano_antes));
    
    	$db = new Istac_Sql();
    	$db->query($sql);
    
    	if ($db->next_record())
    	{
    		$mesp = $db->f("modo_esp");
    		if ($mesp != null)
    			$modo_entrada_datos = $mesp;
    		$mcum = $db->f("modo_horizontal");
    		if ($mcum != null)
    			$modo_cumplimentado = $mcum;
    		$mpor = $db->f("modo_porcentaje");
    		if ($mpor != null)
    			$modo_porcentaje = $mpor;    		
    	}
    	return array($modo_entrada_datos, $modo_cumplimentado, $modo_porcentaje);
    }
 
    /**
     *  Cargar todas las unidades territoriales para las que un cuestionario tenga datos.
     *  Debe existir el cuestionario.
     * @param id_establecimiento $estid
     * @param mes $mes_encuesta
     * @param ano $ano_encuesta
     */
    public function cargar_uts_rellenados($estid, $mes_encuesta, $ano_encuesta)
    {
    	$result = array();
    
    	$sql = DbHelper::prepare_sql("SELECT DISTINCT
    			esp.ID_UNIDAD_TERRIT,cfg_grupos.orden, cfg.orden
    			FROM TB_ALOJA_CUESTIONARIOS c
    			INNER JOIN TB_ALOJA_ESP_DIARIOS esp
    			ON c.ID = esp.ID_CUESTIONARIO
    			INNER JOIN TB_UNIDADES_TERRITORIALES ut
    			on ut.id = esp.id_unidad_territ
    			INNER JOIN TB_CONFIG_UNID_TERRIT cfg
    			on ut.id = cfg.id_unidad_territ
    			INNER JOIN TB_CONFIG_GRUPOS_UNID_TERRIT cfg_grupos
    			ON cfg_grupos.ID = cfg.ID_GRUPO_UNID_TERRIT
    			WHERE c.ID_ESTABLECIMIENTO         = :estid
    			AND c.MES                          = :mes
    			and c.ano                          = :ano
    			and cfg_grupos.fecha_alta <= to_date(:fecha_referencia,'dd-mm-yyyy')
    			AND (cfg_grupos.fecha_baja is null or
    			cfg_grupos.fecha_baja>to_date(:fecha_referencia, 'dd-mm-yyyy'))
    			ORDER BY cfg_grupos.orden, cfg.orden",
    			array(':estid' => (string)$estid,
    					':mes' => (int)$mes_encuesta,
    					':ano' => (int)$ano_encuesta,
    					':fecha_referencia' => (string)sprintf("%02d-%02d-%04d", 1,
    							$mes_encuesta, $ano_encuesta) ));
    
    	$db = new Istac_Sql();
    	$db->query($sql);
    
    	while ($db->next_record())
    	{
    		$result[]=$db->f('id_unidad_territ');
    	}
    	return $result;
    }
    
    /**
     * Carga los presentes a comienzo de mes almacenados para las unidades territoriales indicadas.
     * @param unknown_type $estid
     * @param unknown_type $mes_encuesta
     * @param unknown_type $ano_encuesta
     * @param unknown_type $id_uts
     */
    private function cargar_presentes_uts($estid, $mes_encuesta, $ano_encuesta, $id_uts=NULL)
    {
    	$sql = DbHelper::prepare_sql("SELECT pres.id_unidad_territ unid_territ, cuest.mes mes, cuest.ano ano, pres.presentes_comienzo_mes presentes
    			FROM tb_aloja_cuestionarios cuest
    			INNER JOIN tb_aloja_presentes_mes pres
    			ON cuest.id = pres.id_cuestionario
    			WHERE cuest.id_establecimiento=:estid
    			AND cuest.mes=:mes_encuesta AND cuest.ano=:ano_encuesta".
    			($id_uts==NULL ? "" : " AND pres.id_unidad_territ in (".implode($id_uts,",").")")
    			." ORDER BY unid_territ, ano, mes",
    			array(':estid' => (string)$estid,
    					':mes_encuesta' => (int)$mes_encuesta,
    					':ano_encuesta' => (int)$ano_encuesta));
    	    	    	
    	$db = new Istac_Sql();  	
    	$db->query($sql);
    	
    	$result = array();
    	//Como la query está ordenada por meses, el valor de pernoct tendrá en primer lugar el valor de presentes a final de mes
    	//anterior (si existe) y luego presentes a comienzo de mes (si existe)
    	while ($db->next_record())
    	{
	    	$id = $db->f('unid_territ');
	    	$result[$id] = $db->f('presentes');
    	}
    	return $result;
    	 
    }

    /**
     * Carga los presentes a fin de mes anterior dado un mes y ano de encuesta.
     * @param unknown_type $estid
     * @param unknown_type $mes_encuesta
     * @param unknown_type $ano_encuesta
     */
    public function cargar_presentes_fin_mes_uts($estid, $mes_encuesta, $ano_encuesta)
    {
    	list($mes_anterior, $ano_anterior) = DateHelper::mes_anterior($mes_encuesta, $ano_encuesta);
    	 
    	$sql = DbHelper::prepare_sql("SELECT pres.id_unidad_territ unid_territ, pres.presentes_fin_mes presentes
    			FROM tb_aloja_cuestionarios cuest
    			INNER JOIN tb_aloja_presentes_mes pres
    			ON cuest.id = pres.id_cuestionario
    			WHERE cuest.id_establecimiento=:estid
    			AND cuest.mes=:mes_anterior AND cuest.ano=:ano_anterior
    			AND pres.presentes_fin_mes<>0",
    			array(':estid' => (string)$estid,
    					':mes_anterior' => (int)$mes_anterior,
    					':ano_anterior' => (int)$ano_anterior));
    	 
    	$db = new Istac_Sql();
    	$db->query($sql);
    	 
    	$result = array();
    	//Como la query está ordenada por meses, el valor de pernoct tendrá en primer lugar el valor de presentes a final de mes
    	//anterior (si existe) y luego presentes a comienzo de mes (si existe)
    	while ($db->next_record())
    	{
    		$id = $db->f('unid_territ');
    		$result[$id] = $db->f('presentes');
    	}
    	return $result;
    
    }
    
    public function cargar_lookup_paises()
    {
    	//TODO: Revisar esta query
    	$sql = "SELECT ut.ID,ut.LITERAL FROM TB_UNIDADES_TERRITORIALES ut";
    	
    	$db = new Istac_Sql();
    	$db->query($sql);
    	 
    	$result = array();
    	while ($db->next_record())
    	{
    		$id_ut = (int)$db->f("id");
    		$result[$id_ut] = $db->f("literal");
    	}
    	return $result;
    }
    
    /**
     * Carga todos los movimientos para el cuestionario dado.
     * @param unknown_type $estid
     * @param unknown_type $mes_encuesta
     * @param unknown_type $ano_encuesta
     * @return multitype:AlojaUTMovimientos
     */
    public function cargar_todos_esp($id_cuestionario)
    {
    	$sql = DbHelper::prepare_sql("SELECT esp.ID_UNIDAD_TERRIT,esp.DIA,esp.ENTRADAS,esp.SALIDAS,esp.PERNOCTACIONES
										FROM TB_ALOJA_CUESTIONARIOS c
										  inner join tb_aloja_esp_diarios esp
										  ON c.ID = esp.ID_CUESTIONARIO
										WHERE c.ID         = :cuestid
										  ORDER BY esp.id_unidad_territ, esp.dia",
    			array(':cuestid' => (string)$id_cuestionario));
    	 
    	$db = new Istac_Sql();
    	$db->query($sql);
    	
    	$result = array();
    	$id_uts = array();
    	while ($db->next_record())
    	{
    		$id = $db->f('id_unidad_territ');
    		$id_uts[] = $id;
    		
    		if (!isset($result[$id]))
    			$result[$id] = new AlojaUTMovimientos();
    		$mov_ut = $result[$id];
    		
    		$esp = new AlojaESP();
    		$esp->entradas = $db->f('entradas');
    		$esp->salidas = $db->f('salidas');
    		$esp->pernoctaciones = $db->f('pernoctaciones');
    		$dia = $db->f('dia');
    		$mov_ut->movimientos[$dia] = $esp;
    	}
    	
    	//Obtener los presentes a comienzo de mes
    	$pres_com_mes = $this->cargar_presentes_mes($id_cuestionario);
    	foreach($pres_com_mes as $id => $pcm)
    	{
    		/// Caso en el que haya presentes pero no movimientos.
    		if (!isset($result[$id]))
    			$result[$id] = new AlojaUTMovimientos();
    		
    		$result[$id]->presentes_comienzo_mes = $pcm[0];
    	}
    	
    	return $result;
    }
    
    /**
     * Cargas las entradas, salidas y pernoctaciones para las unidades territoriales 
     * @param unknown_type $estid
     * @param unknown_type $mes_encuesta
     * @param unknown_type $ano_encuesta
     * @param unknown_type $id_uts
     * @return multitype:|Ambigous <multitype:multitype:NULL number AlojaUTMovimientos  , unknown>
     */
    public function cargar_esp_uts($estid, $mes_encuesta, $ano_encuesta, $id_uts)
    {
    	$result = array();
    	if ($id_uts!==NULL && count($id_uts) == 0)
    		return $result;
    	
    	$db = new Istac_Sql();
    	//Si no se pasan los id de las UTs, se entiende que se quieren todas las UTs del cuestionario
    	//A partir de ese momento, el procedimiento es el mismo que cuando se pasa el parámetro
    	if($id_uts===NULL)
    	{
    		$sql = DbHelper::prepare_sql("SELECT DISTINCT esp.ID_UNIDAD_TERRIT, cfg_grupos.orden, cfg.orden
    				FROM TB_ALOJA_CUESTIONARIOS c
    				INNER JOIN TB_ALOJA_ESP_DIARIOS esp
    				ON c.ID = esp.ID_CUESTIONARIO
					INNER JOIN TB_CONFIG_UNID_TERRIT cfg
					on esp.ID_UNIDAD_TERRIT = cfg.id_unidad_territ
					INNER JOIN TB_CONFIG_GRUPOS_UNID_TERRIT cfg_grupos
					ON cfg_grupos.ID = cfg.ID_GRUPO_UNID_TERRIT
    				WHERE c.ID_ESTABLECIMIENTO         = :estid
    				AND c.MES                          = :mes
    				and c.ano                          = :ano
    				and cfg_grupos.fecha_alta <= to_date(:fecha_referencia,'dd-mm-yyyy') 
					AND (cfg_grupos.fecha_baja is null or cfg_grupos.fecha_baja>to_date(:fecha_referencia, 'dd-mm-yyyy'))
					ORDER BY cfg_grupos.orden, cfg.orden",
    				array(':estid' => (string)$estid, 
    					':mes' => (int)$mes_encuesta,
    					':ano' => (int)$ano_encuesta,
    					':fecha_referencia' => (string)sprintf("%02d-%02d-%04d", 1, $mes_encuesta, $ano_encuesta) ));
    				    		
    		$db->query($sql);
    		
    		//Se obtienen las ids de UTs del cuestionario y se guardan en $id_uts
    		$id_uts=array();
    		while ($db->next_record())
    		{
				$id_uts[]=$db->f('id_unidad_territ');		
    		}
    	}
    	$ids = implode($id_uts,",");
    	
    	$sql = DbHelper::prepare_sql("SELECT esp.ID_UNIDAD_TERRIT,esp.DIA,esp.ENTRADAS,esp.SALIDAS,esp.PERNOCTACIONES,ut.LITERAL,cfg_grupos.ES_NACIONAL
									FROM TB_ALOJA_CUESTIONARIOS c
									INNER JOIN TB_ALOJA_ESP_DIARIOS esp
									ON c.ID = esp.ID_CUESTIONARIO
									INNER JOIN TB_UNIDADES_TERRITORIALES ut
									on ut.id = esp.id_unidad_territ
									INNER JOIN TB_CONFIG_UNID_TERRIT cfg
									on ut.id = cfg.id_unidad_territ
									INNER JOIN TB_CONFIG_GRUPOS_UNID_TERRIT cfg_grupos
									ON cfg_grupos.ID = cfg.ID_GRUPO_UNID_TERRIT
									WHERE c.ID_ESTABLECIMIENTO         = :estid
									AND c.MES                          = :mes
									and c.ano                          = :ano
									and cfg_grupos.fecha_alta <= to_date(:fecha_referencia,'dd-mm-yyyy') 
									AND (cfg_grupos.fecha_baja is null or cfg_grupos.fecha_baja>to_date(:fecha_referencia, 'dd-mm-yyyy'))
				AND ut.id in ($ids) order by esp.id_unidad_territ, esp.dia",
    			array(':estid' => (string)$estid, 
    					':mes' => (int)$mes_encuesta,
    					':ano' => (int)$ano_encuesta,
    					':fecha_referencia' => (string)sprintf("%02d-%02d-%04d", 1, $mes_encuesta, $ano_encuesta) ));
    	
    	
    	$db->query($sql);
    	
    	
    	foreach($id_uts as $id)
    	{
    		$result[$id] = array('nombre' => null, 'presentes_comienzo_mes' => 0, 'filas' => new AlojaUTMovimientos());
    	}
    	
    	while ($db->next_record())
    	{
    		$id = $db->f('id_unidad_territ');
    		$result[$id]['nombre'] = $db->f('literal');
    		$es_nacional = $db->f('es_nacional');
    		$result[$id]['es_nacional'] = $es_nacional;
    		if ($es_nacional=='1' || $es_nacional=='3')
    			$result[$id]['nombre'] = "España > " . $result[$id]['nombre'];
    		
    		$esp = new AlojaESP();
    		$esp->entradas = $db->f('entradas');
    		$esp->salidas = $db->f('salidas');
    		$esp->pernoctaciones = $db->f('pernoctaciones');
    		$dia = $db->f('dia');
    		$result[$id]['filas']->movimientos[$dia] = $esp;
    	}
    	
    	// Si hay alguna ut sin registros, no se inicializara su nombre, hacerlo ahora.
    	$ut_sin_nombre = array();
    	foreach($result as $id_ut => $info_ut)
    	{
    		if ($info_ut['nombre'] == null)
    		{
    			$ut_sin_nombre[]  = $id_ut;
    		}
    	}
    	if (count($ut_sin_nombre) > 0)
    	{
    		$ut_v = implode($ut_sin_nombre, ',');
    		$sql = DbHelper::prepare_sql("SELECT ut.ID, ut.LITERAL, cfg_grupos.ES_NACIONAL
										FROM TB_CONFIG_UNID_TERRIT cfg
										INNER JOIN TB_CONFIG_GRUPOS_UNID_TERRIT cfg_grupos
										ON cfg_grupos.ID = cfg.ID_GRUPO_UNID_TERRIT
										INNER JOIN TB_UNIDADES_TERRITORIALES ut
										on ut.id = cfg.id_unidad_territ
										and cfg_grupos.fecha_alta <= to_date(:fecha_referencia,'dd-mm-yyyy') 
										AND (cfg_grupos.fecha_baja is null or cfg_grupos.fecha_baja>to_date(:fecha_referencia, 'dd-mm-yyyy')) AND ut.ID IN ($ut_v)",
    		array(':fecha_referencia' => (string)sprintf("%02d-%02d-%04d", 1, $mes_encuesta, $ano_encuesta) ));
    		$db->query($sql);
    		while ($db->next_record())
    		{
    			$id = $db->f('id');
    			$result[$id]['nombre'] = $db->f('literal');
    			$es_nacional = $db->f('es_nacional');
    			$result[$id]['es_nacional'] = $es_nacional;
    			if ($es_nacional=='1' || $es_nacional=='3')
    				$result[$id]['nombre'] = "España > " . $result[$id]['nombre'];    			
    		}
    	}

    	//Obtener los presentes a comienzo de mes
		$pres_com_mes = $this->cargar_presentes_uts($estid, $mes_encuesta, $ano_encuesta, $id_uts);   	
		foreach($pres_com_mes as $id => $pcm)
		{
    		$result[$id]['presentes_comienzo_mes'] = $pcm;
    	}
    	return $result;
    	
    }
    
    /**
     * Calcula los totales de entradas, salidas y pernoctaciones con los datos almacenaods en BBDD.
     * @param unknown_type $estid
     * @param unknown_type $mes_encuesta
     * @param unknown_type $ano_encuesta
     */
    public function calcular_esp_totales($estid, $mes_encuesta, $ano_encuesta)
    {
    	$result = new AlojaUTMovimientos();
    	 
    	$sql = DbHelper::prepare_sql("SELECT mov.dia, SUM(mov.entradas) entradas, SUM(mov.salidas) salidas, SUM(mov.pernoctaciones) pernoctaciones
										FROM tb_aloja_esp_diarios mov
										INNER JOIN tb_aloja_cuestionarios c
										ON mov.id_cuestionario=c.id
    			INNER JOIN tb_unidades_territoriales ut
    			ON ut.ID = mov.id_unidad_territ
										WHERE c.ID_ESTABLECIMIENTO         = :estid
										AND c.MES                          = :mes
										and c.ano                          = :ano
    									and upper(ut.literal) <> 'ESPAÑA'
    							      GROUP BY mov.dia
    								  ORDER BY mov.dia",
    			array(':estid' => (string)$estid, 
    					':mes' => (int)$mes_encuesta,
    					':ano' => (int)$ano_encuesta));
    	 
    	$db = new Istac_Sql();
    	$db->query($sql);
    	   	 
    	while ($db->next_record())
    	{
    		$dia = $db->f("dia");
    		$result->movimientos[$dia]=new AlojaESP();
    		$result->movimientos[$dia]->entradas = $db->f('entradas');
    		$result->movimientos[$dia]->salidas = $db->f('salidas');
    		$result->movimientos[$dia]->pernoctaciones = $db->f('pernoctaciones');
    	}
    	
		$pres_com_mes = $this->cargar_presentes_uts($estid, $mes_encuesta, $ano_encuesta);
		$result->presentes_comienzo_mes=0;
		foreach($pres_com_mes as $id => $pcm)
		{
    		$result->presentes_comienzo_mes+= $pcm;
    	}    	
    	 
    	return $result;
    }  

    /**
     * Obtiene el listado de encuestas anteriores a la fecha indicada, hasta un año hacia atrás.
     * @param unknown_type $estid
     * @param unknown_type $mes_actual
     * @param unknown_type $ano_actual
     * @return multitype:multitype:NULL
     */
    public function obtener_encuestas_anteriores($estid, $mes_actual, $ano_actual)
    {
    	$fecha_fin = new DateTime();
    	$fecha_fin->setDate($ano_actual, $mes_actual, 1);
    	$fecha_ini = new DateTime();
    	$fecha_ini->setDate($ano_actual, $mes_actual, 1);
    	$fecha_ini->sub(new DateInterval("P1Y"));
    	
    	$sql = DbHelper::prepare_sql("SELECT c.ID_ESTABLECIMIENTO, c.MES, c.ANO, c.FECHA_CIERRE
									  from tb_aloja_cuestionarios c
									  WHERE c.ID_ESTABLECIMIENTO = :estid AND c.FECHA_CIERRE IS NOT NULL AND
    			to_date(:fecha_ini, 'dd/mm/yyyy') <= to_date('01/'||mes||'/'||ano, 'dd/mm/yyyy') AND 
    			to_date('01/'||mes||'/'||ano, 'dd/mm/yyyy') <= to_date(:fecha_fin, 'dd/mm/yyyy')
    			ORDER BY ano DESC, mes DESC",
    			array(':estid'=> $estid, 
    				  ':fecha_ini'=> (string)$fecha_ini->format('d/m/Y'), 
    				  ':fecha_fin'=> $fecha_fin->format('d/m/Y') ));
    	
    	$db = new Istac_Sql();
    	$db->query($sql);
    	
    	$result = array();
    	while ($db->next_record())
    	{
    		$un_result = array();
    		$un_result['mes'] = $db->f("mes");
    		$un_result['ano'] = $db->f("ano");
    		$result[] = $un_result;
    	}
    	
    	return $result;
    }
    
    /**
     * Obtiene el listado de encuestas anteriores a la fecha indicada (cerradas o no), hasta un año hacia atrás.
     * @param unknown_type $estid
     * @param unknown_type $mes_actual
     * @param unknown_type $ano_actual
     * @return multitype:multitype:NULL
     */
    public function obtener_encuestas_anteriores_admin($estid, $mes_actual, $ano_actual)
    {
        $fecha_fin = new DateTime();
        $fecha_fin->setDate($ano_actual, $mes_actual, 1);
        $fecha_ini = new DateTime();
        $fecha_ini->setDate($ano_actual, $mes_actual, 1);
        $fecha_ini->sub(new DateInterval("P1Y"));
        
        $sql = DbHelper::prepare_sql("SELECT c.ID_ESTABLECIMIENTO, c.MES, c.ANO, c.FECHA_CIERRE
									  from tb_aloja_cuestionarios c
									  WHERE c.ID_ESTABLECIMIENTO = :estid AND
    			to_date(:fecha_ini, 'dd/mm/yyyy') <= to_date('01/'||mes||'/'||ano, 'dd/mm/yyyy') AND
    			to_date('01/'||mes||'/'||ano, 'dd/mm/yyyy') <= to_date(:fecha_fin, 'dd/mm/yyyy')
    			ORDER BY ano DESC, mes DESC",
            array(':estid'=> $estid,
                ':fecha_ini'=> (string)$fecha_ini->format('d/m/Y'),
                ':fecha_fin'=> $fecha_fin->format('d/m/Y') ));
            
            $db = new Istac_Sql();
            $db->query($sql);
            
            $result = array();
            while ($db->next_record())
            {
                $un_result = array();
                $un_result['mes'] = $db->f("mes");
                $un_result['ano'] = $db->f("ano");
                $un_result['cerrada'] = ($db->f('fecha_cierre') != null);
                $result[] = $un_result;
            }
            
            return $result;
    }
    
    
    /**
     * Obtiene el primer ý último día que contiene datos de movimientos diarios para un cuestionario dado
     * @param $id_cuestionario
     * @return
     */
    public function get_dias_rellenos($id_cuestionario)
    {
    	$minimo = NULL; $maximo = NULL;
    	 
    	//Se calcula el mínimo y máximo día rellenados de un cuestionario
    	$sql = DbHelper::prepare_sql("SELECT MIN(dia) minimo, MAX(dia) maximo
    			FROM tb_aloja_esp_diarios
    			WHERE id_cuestionario=:id_cuest",
    			array(':id_cuest' => $id_cuestionario));
    	 
    	$db = new Istac_Sql();
    	$db->query($sql);
    	 
    	if ($db->next_record())
    	{
    		$minimo = $db->f("minimo");
    		$maximo = $db->f("maximo");
    	}
    	 
    	return array($minimo, $maximo);
    }
    
    /**
     * Obtiene un array de arrays con las estadísticas correspondientes al mes y año pasados por parámetros
     * frente a la del mes anterior
     * @param $id_estab
     * @param $mes
     * @param $ano
     * @return
     */
    public function get_stats_UT($id_estab, $mes, $ano)
    {
    	$lista_UT=array();
    	$grupo_lista_UT=array();
    
    	$fecha_referencia = '01-' . sprintf("%02d",$mes) . "-" . $ano;
    	$sql = DbHelper::prepare_sql("SELECT cfg_grupos.literal grupo, cfg_grupos.es_nacional, ut.id id_ut, ut.literal unidad_territorial
    			FROM tb_config_grupos_unid_territ cfg_grupos
    			INNER JOIN tb_config_unid_territ cfg_ut
    			ON cfg_grupos.id=cfg_ut.id_grupo_unid_territ
    			INNER JOIN tb_unidades_territoriales ut
    			ON cfg_ut.id_unidad_territ=ut.id
    			WHERE cfg_grupos.fecha_alta <= to_date(:fecha_referencia,'dd-mm-yyyy') AND (cfg_grupos.fecha_baja is null or cfg_grupos.fecha_baja>to_date(:fecha_referencia, 'dd-mm-yyyy'))
    			ORDER BY cfg_grupos.orden, cfg_ut.orden",
    			array(':fecha_referencia' =>(string)$fecha_referencia));
    
    	$db = new Istac_Sql();
    	$db->query($sql);
    
    	while($db->next_record())
    	{
    		$id_UT = $db->f("id_ut");
    		$a = new AlojaUTStat();
    		$nombre_grupo = $db->f("es_nacional") . $db->f("grupo");
    		$a->nombre = $db->f("unidad_territorial");
    		if (!isset($grupo_lista_UT[$nombre_grupo]))
    		{
    			$grupo_lista_UT[$nombre_grupo] = array();
    			if($db->f("es_nacional")=='1' || $db->f("es_nacional")=='3') $UT_nacionales[]=$nombre_grupo;
    		}
    		$grupo_lista_UT[$nombre_grupo][$id_UT] = $a;
    		$lista_UT[$id_UT] = $nombre_grupo;
    	}
    
    	list($mes_anterior, $ano_anterior) = DateHelper::mes_anterior($mes, $ano);
    	$cuestionario_actual = $this->cargar_registro_cuestionario($id_estab, $mes, $ano);
    	$cuestionario_anterior = $this->cargar_registro_cuestionario($id_estab, $mes_anterior, $ano_anterior);
    
    
    	//Cálculo último movimiento, entradas y salidas
    	if ($cuestionario_actual!=NULL)
    	{
    		$sql = DbHelper::prepare_sql("SELECT id_unidad_territ, MAX(dia) maximo, SUM(entradas) sum_entradas, SUM(salidas) sum_salidas
    				FROM tb_aloja_esp_diarios
    				WHERE id_cuestionario=:id_cuestionario AND (entradas<>0 OR salidas<>0 OR pernoctaciones<>0)
    				GROUP BY id_unidad_territ",
    				array(':id_cuestionario' => $cuestionario_actual->id));
    		$db->query($sql);
    
    		while($db->next_record())
    		{
    			$id_UT = $db->f("id_unidad_territ");
    			//Sólo inserta la estadística si la unidad territorial existe en la lista
    			if(array_key_exists($id_UT, $lista_UT))
    			{
    				$a = $grupo_lista_UT[ $lista_UT[$id_UT] ][$id_UT];
    				$a->ultimo_movimiento = sprintf("%02d-%02d-%04d",$db->f("maximo"),$mes,$ano);
    				$a->entradas = $db->f("sum_entradas");
    				$a->salidas = $db->f("sum_salidas");
    			}
    		}
    
    		$sql = DbHelper::prepare_sql("SELECT id_unidad_territ, presentes_comienzo_mes, presentes_fin_mes
    				FROM tb_aloja_presentes_mes
    				WHERE id_cuestionario=:id_cuestionario",
    				array(':id_cuestionario' => $cuestionario_actual->id));
    		$db->query($sql);
    
    		while($db->next_record())
    		{
    			$id_UT = $db->f("id_unidad_territ");
    			//Sólo inserta la estadística si la unidad territorial existe en la lista
    			if(array_key_exists($id_UT, $lista_UT))
    			{
    				$a = $grupo_lista_UT[ $lista_UT[$id_UT] ][$id_UT];
    				$a->presentes_comienzo_mes = $db->f("presentes_comienzo_mes");
    				$a->presentes_fin_mes = $db->f("presentes_fin_mes");
    			}
    		}
    	}
    
    	if ($cuestionario_anterior!=NULL)
    	{
    		$sql = DbHelper::prepare_sql("SELECT id_unidad_territ, presentes_fin_mes
    				FROM tb_aloja_presentes_mes
    				WHERE id_cuestionario=:id_cuestionario",
    				array(':id_cuestionario' => $cuestionario_anterior->id));
    		$db->query($sql);
    
    		while($db->next_record())
    		{
    			$id_UT = $db->f("id_unidad_territ");
    			//Sólo inserta la estadística si la unidad territorial existe en la lista
    			if(array_key_exists($id_UT, $lista_UT))
    			{
    				$a = $grupo_lista_UT[ $lista_UT[$id_UT] ][$id_UT];
    				$a->presentes_fin_mes_anterior = $db->f("presentes_fin_mes");
    			}
    		}
    	}
    
    	// Comprobar los movimientos de meses anteriores. Si forma parte de los países no habituales y tiene movimientos en alguno de estos meses
    	// tiene que aparecer directamente en la lista de países no habituales
    	$meses_anteriores = MESES_SEL_PAISES;
    	$mes_cur = $mes;
    	$ano_cur = $ano;
    	while($meses_anteriores > 0 )
    	{
    		list($mes_cur, $ano_cur) = DateHelper::mes_anterior($mes_cur, $ano_cur);    		
    		$meses_anteriores--;
    	}
    	
    	// Contar movimientos de x meses anteriores para el establecimiento, mes y año dados
    	$sql = DbHelper::prepare_sql("select ESP.ID_UNIDAD_TERRIT id_unidad_territ, count(*) entradas
									from TB_ALOJA_CUESTIONARIOS C
									INNER JOIN TB_ALOJA_ESP_DIARIOS ESP
									on C.id = ESP.ID_CUESTIONARIO
									where C.ID_ESTABLECIMIENTO = :id_establecimiento
									AND ((ESP.ENTRADAS <> 0 and ESP.ENTRADAS is not null) 
									or (ESP.SALIDAS <> 0 and ESP.SALIDAS is not null) 
									or (ESP.PERNOCTACIONES <> 0 and ESP.PERNOCTACIONES is not null))
									and ((C.ANO > :ano) or (C.ANO = :ano and C.MES >= :mes))
    								and ESP.ID_UNIDAD_TERRIT IN (SELECT cfg_ut.id_unidad_territ
					    			FROM tb_config_grupos_unid_territ cfg_grupos
					    			INNER JOIN tb_config_unid_territ cfg_ut
					    			ON cfg_grupos.id=cfg_ut.id_grupo_unid_territ
					    			WHERE cfg_grupos.fecha_alta <= to_date(:fecha_referencia,'dd-mm-yyyy') AND (cfg_grupos.fecha_baja is null or cfg_grupos.fecha_baja>to_date(:fecha_referencia, 'dd-mm-yyyy'))
					    			AND cfg_grupos.es_nacional=2)
									GROUP BY ESP.ID_UNIDAD_TERRIT",
    			array(':id_establecimiento' => $id_estab,
    					':mes' => $mes_cur, 
    					':ano' => $ano_cur,
    					':fecha_referencia' =>(string)$fecha_referencia));
    	$db->query($sql);
    	
    	while($db->next_record())
    	{
    		$id_UT = $db->f("id_unidad_territ");
    		//Sólo inserta la estadística si la unidad territorial existe en la lista
    		if(array_key_exists($id_UT, $lista_UT))
    		{
   				$a = $grupo_lista_UT[ $lista_UT[$id_UT] ][$id_UT];
   				$a->mov_meses_anteriores = true;
    		}
    	}
    	
    	return $grupo_lista_UT;
    }
     
    
    /**
     * Obtiene un codigo de registro unico desde la BBDD para asignarlo a una encuesta.
     */
    public function obtener_codigo_registro($estid)
    {
    	///TODO: determinar metodo para generar un cod. registro unico por cuestionario.
    	return date('YmdHi') . $estid;
    }
     
    /**
     * Guarda el registro maestro del cuestinanrio pasado. Si el cuestionario no existia en la BBDD, le asigna un nuevo ID, inicializando el campo.
     * 
     * @param AlojaCuestionario $cuestionario
     */
    public function guardar_cuestionario(AlojaCuestionario $cuestionario)
    {
    	if ($cuestionario->es_nuevo())
    	{
    		return $this->insertar_registro_cuestionario($cuestionario);
    	}
    	else 
    	{
    		return $this->actualizar_registro_cuestionario($cuestionario);
    	}
    }
      
    /**
     * Inserta un nuevo registor en la tabla tb_aloja_cuestionario.
     * @param AlojaCuestionario $cuestionario
     */
    private function insertar_registro_cuestionario(AlojaCuestionario $cuestionario)
    {
    	if ($cuestionario->codigo_registro == null)
    		$cuestionario->codigo_registro = $this->obtener_codigo_registro($cuestionario->estabid_declarado);
    	
    	if ($cuestionario->fecha_recepcion == null)
    		$cuestionario->fecha_recepcion = new DateTime('now');
    	
    	$params = array();
    	$params[':id'] = $cuestionario->codigo_registro;
    	$params[':estid'] = $cuestionario->estabid_declarado;
    	$params[':userid'] = $cuestionario->userid_declarante;
    	$params[':tipo_carga'] = $cuestionario->tipo_carga;
    	$params[':cod_reg'] = (string)$cuestionario->codigo_registro;
    	$params[':mes'] = $cuestionario->mes;
    	$params[':ano'] = $cuestionario->ano;
    	$params[':fecha_recepcion'] = (string)$cuestionario->fecha_recepcion->format('d/m/Y H:i:s');
    	$params[':modo_esp'] = (string)$cuestionario->modo_introduccion;
    	$params[':modo_horizontal'] = (string)$cuestionario->modo_cumplimentado;
    	$params[':modo_porcentaje'] = (string)$cuestionario->modo_porcentaje;
    	$params[':validacion'] = (string)EV_CUESTIONARIO_INCOMPLETO;
    	$params[':datos_movim'] = ($cuestionario->tiene_datos_movimientos)?'1':'0';
    	$params[':datos_habit'] = ($cuestionario->tiene_datos_habitaciones)?'1':'0';
    	$params[':datos_pers'] = ($cuestionario->tiene_datos_personal)?'1':'0';
    	$params[':datos_precios'] = ($cuestionario->tiene_datos_precios)?'1':'0';
    	
    	if ($cuestionario->dias_abierto != null)
    	{
    		$params[':dias_abierto'] = $cuestionario->dias_abierto;

	    	$sql = DbHelper::prepare_sql("insert into tb_aloja_cuestionarios
				  (id,id_establecimiento,id_usuario,id_tipo_carga,codigo_registro,mes,ano,
				  fecha_recepcion,modo_esp,modo_horizontal,modo_porcentaje,dias_abierto, validacion, datos_movim, datos_habit, datos_pers, datos_precios) values
				  (:id,:estid,:userid,:tipo_carga,:cod_reg,:mes,:ano,to_date(:fecha_recepcion, 'dd/mm/yyyy HH24:MI:SS'),:modo_esp,:modo_horizontal,:modo_porcentaje,:dias_abierto,
				   :validacion,:datos_movim,:datos_habit,:datos_pers,:datos_precios)",
	    			$params);
    	}
    	else 
    	{
    		$sql = DbHelper::prepare_sql("insert into tb_aloja_cuestionarios
    				(id,id_establecimiento,id_usuario,id_tipo_carga,codigo_registro,mes,ano,
    				fecha_recepcion,modo_esp,modo_horizontal,modo_porcentaje, validacion, datos_movim, datos_habit, datos_pers, datos_precios) values
    				(:id,:estid,:userid,:tipo_carga,:cod_reg,:mes,:ano,to_date(:fecha_recepcion, 'dd/mm/yyyy HH24:MI:SS'),:modo_esp,:modo_horizontal,:modo_porcentaje,:validacion,
    				:datos_movim,:datos_habit,:datos_pers,:datos_precios)",
    				$params);
    	}
    	$db = new Istac_Sql();
    	$db->query($sql);
    	if ($db->affected_rows() > 0)
    	{
    		$cuestionario->id = $cuestionario->codigo_registro;
    		return true;
    	}
    	return false;
    }
    
    /*
     * Actualiza el registro del cuestionario con la información relativa a los avisos de exceso de plazas y/o habitaciones.
     */
    public function actualizar_info_exceso_cuestionario(AlojaCuestionario $cuestionario)
    {
    	$params = array();
    	$params[':id'] = $cuestionario->id;
    	$params[':motivo_exceso_plazas'] = $cuestionario->excesoPlazas;
    	$params[':detalle_motivo_exceso_plazas'] = $cuestionario->excesoPlazasDetalle;
    	$params[':motivo_exceso_habit'] = $cuestionario->excesoHabitaciones;
    	$params[':detalle_motivo_exceso_habit'] = $cuestionario->excesoHabitacionesDetalle;
    	$sql = DbHelper::prepare_sql("UPDATE tb_aloja_cuestionarios
   				SET
    			id_motivo_exceso_plazas = :motivo_exceso_plazas,
    			detalle_motivo_exceso_plazas = :detalle_motivo_exceso_plazas,
    			id_motivo_exceso_habit = :motivo_exceso_habit,
    			detalle_motivo_exceso_habit = :detalle_motivo_exceso_habit
   				WHERE id = :id",
   				$params);
    	
    	$db = new Istac_Sql();
    	$db->query($sql);
    	return ($db->affected_rows() > 0);
    }
    
    /*
     * Actualiza el registro del cuestionario como pendiente de informar sobre exceso de plazas 
     */
    public function marcar_cuestionario_pendiente_informar_exceso(AlojaCuestionario $cuestionario)
    {
    	if(isset($cuestionario->excesoInfoObj))
    	{
    		if($cuestionario->excesoInfoObj->hayExcesoPlazas || $cuestionario->excesoInfoObj->hayExcesoPlazasMes || $cuestionario->excesoInfoObj->hayExcesoHabitaciones)
    		{
    			if($cuestionario->excesoInfoObj->hayExcesoPlazas || $cuestionario->excesoInfoObj->hayExcesoPlazasMes)
    				$cuestionario->excesoPlazas=99;
    			if($cuestionario->excesoInfoObj->hayExcesoHabitaciones)
    				$cuestionario->excesoHabitaciones=99;
    						
		    	$params = array();
		    	$params[':id'] = $cuestionario->id;
		    	$params[':motivo_exceso_plazas'] = $cuestionario->excesoPlazas;
		    	$params[':motivo_exceso_habit'] = $cuestionario->excesoHabitaciones;
		    	$sql = DbHelper::prepare_sql("UPDATE tb_aloja_cuestionarios
		   				SET
		    			id_motivo_exceso_plazas = :motivo_exceso_plazas,
		    			id_motivo_exceso_habit = :motivo_exceso_habit
		   				WHERE id = :id",
		   				$params);
		    	
		    	$db = new Istac_Sql();
		    	$db->query($sql);
		    	return ($db->affected_rows() > 0);
    		}
    	}
    	return true;
    }
    
    /**
     * Actualiza un registro existente de la tabla tb_aloja_cuestionario.
     * @param AlojaCuestionario $cuestionario
     * @return boolean
     */
    private function actualizar_registro_cuestionario(AlojaCuestionario $cuestionario)
    {
    	$params = array();
    	$params[':id'] = $cuestionario->id;
    	$params[':userid'] = $cuestionario->userid_declarante;
    	$params[':tipo_carga'] = $cuestionario->tipo_carga;
    	$params[':fecha_recepcion'] = (string)$cuestionario->fecha_recepcion->format('d/m/Y H:i:s');
    	$params[':modo_esp'] = (string)$cuestionario->modo_introduccion;
    	$params[':modo_horizontal'] = (string)$cuestionario->modo_cumplimentado;
    	$params[':modo_porcentaje'] = (string)$cuestionario->modo_porcentaje;
    	$params[':datos_movim'] = ($cuestionario->tiene_datos_movimientos)?'1':'0';
    	$params[':datos_habit'] = ($cuestionario->tiene_datos_habitaciones)?'1':'0';
    	$params[':datos_pers'] = ($cuestionario->tiene_datos_personal)?'1':'0';
    	$params[':datos_precios'] = ($cuestionario->tiene_datos_precios)?'1':'0';
    	$params[':motivo_exceso_plazas'] = $cuestionario->excesoPlazas;
    	$params[':detalle_motivo_exceso_plazas'] = $cuestionario->excesoPlazasDetalle;
    	$params[':motivo_exceso_habit'] = $cuestionario->excesoHabitaciones;
    	$params[':detalle_motivo_exceso_habit'] = $cuestionario->excesoHabitacionesDetalle;
    	 
    	 
    	if ($cuestionario->dias_abierto != null)
    	{
    		$params[':dias_abierto'] = $cuestionario->dias_abierto;
	    	$sql = DbHelper::prepare_sql("UPDATE tb_aloja_cuestionarios
	    			SET 
	    			id_usuario = :userid,
	    			id_tipo_carga = (CASE WHEN id_tipo_carga<>'".TIPO_CARGA_XML."' THEN :tipo_carga ELSE id_tipo_carga END),
	    			fecha_recepcion = to_date(:fecha_recepcion, 'dd/mm/yyyy HH24:MI:SS'),
	    			modo_esp = :modo_esp,
	    			modo_horizontal = :modo_horizontal,
	    			modo_porcentaje = :modo_porcentaje,
	    			dias_abierto = :dias_abierto,
	    			datos_movim = :datos_movim,
	    			datos_habit = :datos_habit,
	    			datos_pers = :datos_pers,
	    			datos_precios = :datos_precios,
	    			id_motivo_exceso_plazas = :motivo_exceso_plazas,
	    			detalle_motivo_exceso_plazas = :detalle_motivo_exceso_plazas,
	    			id_motivo_exceso_habit = :motivo_exceso_habit,
	    			detalle_motivo_exceso_habit = :detalle_motivo_exceso_habit
	    			WHERE id = :id",
	    			$params);
    	}
    	else 
    	{
    		$sql = DbHelper::prepare_sql("UPDATE tb_aloja_cuestionarios
    				SET
    				id_usuario = :userid,
    				id_tipo_carga = (CASE WHEN id_tipo_carga<>'".TIPO_CARGA_XML."' THEN :tipo_carga ELSE id_tipo_carga END),
    				fecha_recepcion = to_date(:fecha_recepcion, 'dd/mm/yyyy HH24:MI:SS'),
    				modo_esp = :modo_esp,
    				modo_horizontal = :modo_horizontal,
    				modo_porcentaje = :modo_porcentaje,
	    			datos_movim = :datos_movim,
	    			datos_habit = :datos_habit,
	    			datos_pers = :datos_pers,
	    			datos_precios = :datos_precios,
	    			id_motivo_exceso_plazas = :motivo_exceso_plazas,
	    			detalle_motivo_exceso_plazas = :detalle_motivo_exceso_plazas,
	    			id_motivo_exceso_habit = :motivo_exceso_habit,
	    			detalle_motivo_exceso_habit = :detalle_motivo_exceso_habit
    				WHERE id = :id",
    				$params);
    	}
    	
    	$db = new Istac_Sql();
    	$db->query($sql);
    	return ($db->affected_rows() > 0);
    } 

    /**
     * Elimina un registro de la tabla tb_aloja_cuestionario y todos sus detalles de las tablas relacionadas.
     * @param unknown_type $id_cuestionario
     */
    public function eliminar_cuestionario($id_cuestionario)
    {
    	$sql = DbHelper::prepare_sql("delete from tb_aloja_cuestionarios where id = :id", array(':id' => $id_cuestionario));
    	$db = new Istac_Sql();
    	$db->query($sql);
    	return ($db->affected_rows() > 0);
    }
    
    /**
     * Actualiza el registro de cuestionario estableciendo su fecha de cierre a la fecha indicada.
     * El cuestionario debe existir previamente.
     * @param unknown_type $id_cuestionario
     * @param unknown_type $fecha_cierre
     * @return boolean
     */
    public function cerrar_cuestionario($id_cuestionario, $fecha_cierre)
    {
    	$sql = DbHelper::prepare_sql("update tb_aloja_cuestionarios set fecha_cierre = to_date(:fecha_cierre, 'dd/mm/yyyy HH24:MI:SS') where id = :id", array(':id' => $id_cuestionario, ':fecha_cierre' => $fecha_cierre->format('d/m/Y H:i:s')));
    	$db = new Istac_Sql();
    	$db->query($sql);
    	return ($db->affected_rows() > 0);
    }
    
    /**
     * Actualiza el registro de cuestionario estableciendo su fecha de cierre a nulo (indicando que esta abierto).
     * El cuestionario debe existir previamente.
     * @param unknown_type $id_cuestionario
     * @param unknown_type $fecha_cierre
     * @return boolean
     */
    public function abrir_cuestionario($id_cuestionario)
    {
    	$sql = DbHelper::prepare_sql("update tb_aloja_cuestionarios set fecha_cierre = null where id = :id", array(':id' => $id_cuestionario));
    	$db = new Istac_Sql();
    	$db->query($sql);
    	return ($db->affected_rows() > 0);
    }   

    /**
     * Carga los registros de habitaciones relacionados con el cuestionario cuyo id se indica.
     * @param unknown_type $id_cuestionario
     */
    public function cargar_habitaciones($id_cuestionario)
    {
    	$sql = DbHelper::prepare_sql("select id_cuestionario,dia,uso_doble,uso_individual,otras, plazas_supletorias 
    			from tb_aloja_habitaciones where id_cuestionario = :id_cuestionario", 
    			array(':id_cuestionario' => $id_cuestionario));
    	$db = new Istac_Sql();
    	$db->query($sql);
    	
    	$habs = array();
    	while ($db->next_record())
    	{
    		$dia = $db->f('dia');
    		$hab = new AlojaHabitaciones();
    		if ($db->f('uso_doble') != null)
    			$hab->uso_doble = (int)$db->f('uso_doble');
    		if ($db->f('uso_individual') != null)
    			$hab->uso_individual = (int)$db->f('uso_individual');
    		if ($db->f('otras') != null)
    			$hab->otras = (int)$db->f('otras');
    		if ($db->f('plazas_supletorias') != null)
    			$hab->supletorias = (int)$db->f('plazas_supletorias'); 
    		
    		$habs[ (int)$dia ] = $hab;
    	}
    	return $habs;
    }
    
    /**
     * Guarda los detalles de habitaciones para el cuestionario indicado, sobreescribiendo los datos existentes.
     * Al finaliza la operacion, los datos en la BBDD serán solo los indicados.
     * @param unknown_type $id_cuestionario
     * @param unknown_type $habitaciones
     */
    public function guardar_habitaciones($id_cuestionario, $habitaciones)
    {
    	$db = new Istac_Sql();
    	
    	$ok = true;
    	
    	if ($habitaciones == null)
    	{
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_habitaciones where id_cuestionario = :id_cuestionario",
    				array(':id_cuestionario' => $id_cuestionario));
    		$db->query($sql);
    		return ($db->affected_rows() > 0);
    	}
    	
    	/// guardar primero intenta actualizar los que existen, si no se actualiza insertarlos y luego borrar los que sobran.
    	$dias_actualizados = array();
    	foreach($habitaciones as $dia => $hab)
    	{
    		$params = array(':id_cuestionario' => $id_cuestionario, ':dia'=> $dia,
    				':uso_doble'=> $hab->uso_doble,
    				':uso_individual'=> $hab->uso_individual,
    				':otras'=> $hab->otras, 
    				':plazas_supletorias'=> $hab->supletorias);
    		/// 1. actualizar los existentes
    		$sql = DbHelper::prepare_sql("update tb_aloja_habitaciones set
    				uso_doble = :uso_doble, 
    				uso_individual = :uso_individual,
    				otras = :otras,
    				plazas_supletorias = :plazas_supletorias 
    				where id_cuestionario = :id_cuestionario and dia = :dia", $params);
    		
    		$db->query($sql);
    		
    		$updated = $db->affected_rows() > 0;
    		if (!$updated)
    		{
    			/// 2. insertamos los nuevos
		    	$sql = DbHelper::prepare_sql("insert into tb_aloja_habitaciones 
		    			(id_cuestionario,dia,uso_doble,uso_individual,otras,plazas_supletorias) 
		    	 values (:id_cuestionario, :dia, :uso_doble, :uso_individual, :otras, :plazas_supletorias)", 
		    			$params);
		    	
		    	$db->query($sql);
		    	
		    	$updated = $db->affected_rows() > 0;
    		}
    		
    		$dias_actualizados[] = $dia;
	    	$ok &= ($updated);
    	}
    	
    	/// 3. Eliminar los que no existen ya.
    	if (count($dias_actualizados) > 0)
    	{
	    	$dias = implode(",", $dias_actualizados);
	    	$sql = DbHelper::prepare_sql("delete from tb_aloja_habitaciones where id_cuestionario = :id_cuestionario and dia not in ($dias)", 
	    			array(':id_cuestionario' => $id_cuestionario));
	    	
	    	$db->query($sql);
	    	$ok &= ($db->affected_rows() > 0);
    	}
    	return $ok;
    } 

    /**
     * Carga los registros de personale y precios relacionados con el cuestionario cuyo id se indica.
     * @param unknown_type $id_cuestionario
     */
    public function cargar_personal_precios($id_cuestionario)
    {
    	$pers = new AlojaPersonal();
    	$precios = new AlojaPrecios();
    	
    	$db = new Istac_Sql();
    	 
    	$sql = DbHelper::prepare_sql("select no_remunerado,remunerado_fijo,remunerado_eventual,
    			revpar_mensual,adr_mensual,
    			adr_to_tradicional,num_habocup_to_tradicional,pctn_habocup_to_tradicional,
    			adr_empresas,num_habocup_empresas,pctn_habocup_empresas,
    			adr_ag_tradicional,num_habocup_ag_tradicional,pctn_habocup_ag_tradicional,
    			adr_particulares,num_habocup_particulares,pctn_habocup_particulares,
    			adr_grupos,num_habocup_grupos,pctn_habocup_grupos,
    			adr_internet,num_habocup_internet,pctn_habocup_internet,
    			adr_ag_online,num_habocup_ag_online,pctn_habocup_ag_online,
    			adr_to_online,num_habocup_to_online,pctn_habocup_to_online,
    			adr_otros,num_habocup_otros,pctn_habocup_otros 
    			from tb_aloja_personal_precios where id_cuestionario = :id_cuestionario", 
    			array(':id_cuestionario' => $id_cuestionario));
    	$db->query($sql);
    	 
    	if ($db->next_record())
    	{
    		/** PERSONAL **/
    		if ($db->f('no_remunerado') != null)
    			$pers->no_remunerado = (int)$db->f('no_remunerado');
    		if ($db->f('remunerado_fijo') != null)
    			$pers->remunerado_fijo = (int)$db->f('remunerado_fijo'); 
    		if ($db->f('remunerado_eventual') != null)
    			$pers->remunerado_eventual = (int)$db->f('remunerado_eventual');  

    		/** PRECIOS **/
    		if ($db->f('revpar_mensual') != null)
    			$precios->revpar_mensual =  (float)$db->f('revpar_mensual');
    		if ($db->f('adr_mensual') != null)
    			$precios->adr_mensual =  (float)$db->f('adr_mensual');
    		
    		/** AGENCIA ONLINE **/
    		if ($db->f('adr_ag_online') != null)
    			$precios->adr[AGENCIA_ONLINE] =  (float)$db->f('adr_ag_online');
    		if ($db->f('num_habocup_ag_online') != null)
    			$precios->num[AGENCIA_ONLINE] =  (float)$db->f('num_habocup_ag_online');
    		if ($db->f('pctn_habocup_ag_online') != null)
    			$precios->pct[AGENCIA_ONLINE] =  (float)$db->f('pctn_habocup_ag_online');
    		
    		/** AGENCIA TRADICIONAL **/
    		if ($db->f('adr_ag_tradicional') != null)
    			$precios->adr[AGENCIA_TRADICIONAL] =  (float)$db->f('adr_ag_tradicional');
    		if ($db->f('num_habocup_ag_tradicional') != null)
    			$precios->num[AGENCIA_TRADICIONAL] =  (float)$db->f('num_habocup_ag_tradicional');
    		if ($db->f('pctn_habocup_ag_tradicional') != null)
    			$precios->pct[AGENCIA_TRADICIONAL] =  (float)$db->f('pctn_habocup_ag_tradicional');    

    		/** EMPRESAS **/
    		if ($db->f('adr_empresas') != null)
    			$precios->adr[EMPRESAS] =  (float)$db->f('adr_empresas');
    		if ($db->f('num_habocup_empresas') != null)
    			$precios->num[EMPRESAS] =  (float)$db->f('num_habocup_empresas');
    		if ($db->f('pctn_habocup_empresas') != null)
    			$precios->pct[EMPRESAS] =  (float)$db->f('pctn_habocup_empresas');  

    		/** GRUPOS **/
    		if ($db->f('adr_grupos') != null)
    			$precios->adr[GRUPOS] =  (float)$db->f('adr_grupos');
    		if ($db->f('num_habocup_grupos') != null)
    			$precios->num[GRUPOS] =  (float)$db->f('num_habocup_grupos');
    		if ($db->f('pctn_habocup_grupos') != null)
    			$precios->pct[GRUPOS] =  (float)$db->f('pctn_habocup_grupos'); 

    		/** INTERNET **/
    		if ($db->f('adr_internet') != null)
    			$precios->adr[INTERNET] =  (float)$db->f('adr_internet');
    		if ($db->f('num_habocup_internet') != null)
    			$precios->num[INTERNET] =  (float)$db->f('num_habocup_internet');
    		if ($db->f('pctn_habocup_internet') != null)
    			$precios->pct[INTERNET] =  (float)$db->f('pctn_habocup_internet');

    		/** OTROS **/
    		if ($db->f('adr_otros') != null)
    			$precios->adr[OTROS] =  (float)$db->f('adr_otros');
    		if ($db->f('num_habocup_otros') != null)
    			$precios->num[OTROS] =  (float)$db->f('num_habocup_otros');
    		if ($db->f('pctn_habocup_otros') != null)
    			$precios->pct[OTROS] =  (float)$db->f('pctn_habocup_otros');

    		/** PARTICULARES **/
    		if ($db->f('adr_particulares') != null)
    			$precios->adr[PARTICULARES] =  (float)$db->f('adr_particulares');
    		if ($db->f('num_habocup_particulares') != null)
    			$precios->num[PARTICULARES] =  (float)$db->f('num_habocup_particulares');
    		if ($db->f('pctn_habocup_particulares') != null)
    			$precios->pct[PARTICULARES] =  (float)$db->f('pctn_habocup_particulares');

    		/** TO_ONLINE **/
    		if ($db->f('adr_to_online') != null)
    			$precios->adr[TO_ONLINE] =  (float)$db->f('adr_to_online');
    		if ($db->f('num_habocup_to_online') != null)
    			$precios->num[TO_ONLINE] =  (float)$db->f('num_habocup_to_online');
    		if ($db->f('pctn_habocup_to_online') != null)
    			$precios->pct[TO_ONLINE] =  (float)$db->f('pctn_habocup_to_online');

    		/** TO_TRADICIONAL **/
    		if ($db->f('adr_to_tradicional') != null)
    			$precios->adr[TO_TRADICIONAL] =  (float)$db->f('adr_to_tradicional');
    		if ($db->f('num_habocup_to_tradicional') != null)
    			$precios->num[TO_TRADICIONAL] =  (float)$db->f('num_habocup_to_tradicional');
    		if ($db->f('pctn_habocup_to_tradicional') != null)
    			$precios->pct[TO_TRADICIONAL] =  (float)$db->f('pctn_habocup_to_tradicional');    		
       	}
       	
       	return array($pers,$precios);
    }
    
    /**
     * Guarda los detalles de personal y precio para el cuestionario indicado, sobreescribiendo los datos existentes.
     * Al finaliza la operacion, los datos en la BBDD serán solo los indicados.
     * @param unknown_type $id_cuestionario
     * @param unknown_type $habitaciones
     */
    public function guardar_personal_precios($id_cuestionario, $personal, $precios)
    {
    	
    	$db = new Istac_Sql();
    	$ok = true;
    	 
    	if ($personal == null && $precios == null)
    	{
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_personal_precios where id_cuestionario = :id_cuestionario", 
    				array(':id_cuestionario' => $id_cuestionario));
    		$db->query($sql);
    		return ($db->affected_rows() > 0);
    	}
    	
    	$personal_2 = ($personal != null)? $personal : new AlojaPersonal(); 
    	$precios_2 = ($precios != null)? $precios : new AlojaPrecios();
    	
    	$params = array(':id_cuestionario' => $id_cuestionario,
    			':no_remunerado' => $personal_2->no_remunerado,
    			':remunerado_fijo'=> $personal_2->remunerado_fijo,
    			':remunerado_eventual'=> $personal_2->remunerado_eventual,
    			':revpar_mensual' => $precios_2->revpar_mensual,
    			':adr_mensual' => $precios_2->adr_mensual,
    			
    			':adr_to_tradicional'=> isset($precios_2->adr[TO_TRADICIONAL])?$precios_2->adr[TO_TRADICIONAL]: null,
    			':num_habocup_to_tradicional'=> isset($precios_2->num[TO_TRADICIONAL])?$precios_2->num[TO_TRADICIONAL]:null,
    			':pctn_habocup_to_tradicional'=> isset($precios_2->pct[TO_TRADICIONAL])?$precios_2->pct[TO_TRADICIONAL]:null,
    			
    			':adr_empresas'=> isset($precios_2->adr[EMPRESAS])? $precios_2->adr[EMPRESAS]: null,
    			':num_habocup_empresas'=> isset($precios_2->num[EMPRESAS])?$precios_2->num[EMPRESAS]: null,
    			':pctn_habocup_empresas'=> isset($precios_2->pct[EMPRESAS])?$precios_2->pct[EMPRESAS]: null,
    			
    			':adr_ag_tradicional'=> isset($precios_2->adr[AGENCIA_TRADICIONAL])?$precios_2->adr[AGENCIA_TRADICIONAL]:null,
    			':num_habocup_ag_tradicional'=> isset($precios_2->num[AGENCIA_TRADICIONAL])?$precios_2->num[AGENCIA_TRADICIONAL]:null,
    			':pctn_habocup_ag_tradicional'=> isset($precios_2->pct[AGENCIA_TRADICIONAL])?$precios_2->pct[AGENCIA_TRADICIONAL]:null,
    			
    			':adr_particulares'=> isset($precios_2->adr[PARTICULARES])?$precios_2->adr[PARTICULARES]: null,
    			':num_habocup_particulares'=> isset($precios_2->num[PARTICULARES])?$precios_2->num[PARTICULARES]: null,
    			':pctn_habocup_particulares'=> isset($precios_2->pct[PARTICULARES])?$precios_2->pct[PARTICULARES]: null,
    			
    			':adr_grupos'=> isset($precios_2->adr[GRUPOS])?$precios_2->adr[GRUPOS]: null,
    			':num_habocup_grupos'=> isset($precios_2->num[GRUPOS])?$precios_2->num[GRUPOS]: null,
    			':pctn_habocup_grupos'=> isset($precios_2->pct[GRUPOS])?$precios_2->pct[GRUPOS]: null,
    			
    			':adr_internet'=> isset($precios_2->adr[INTERNET])?$precios_2->adr[INTERNET]: null,
    			':num_habocup_internet'=> isset($precios_2->num[INTERNET])?$precios_2->num[INTERNET]: null,
    			':pctn_habocup_internet'=> isset($precios_2->pct[INTERNET])?$precios_2->pct[INTERNET]: null,
    			
    			':adr_ag_online'=> isset($precios_2->adr[AGENCIA_ONLINE])?$precios_2->adr[AGENCIA_ONLINE]: null,
    			':num_habocup_ag_online'=> isset($precios_2->num[AGENCIA_ONLINE])?$precios_2->num[AGENCIA_ONLINE]: null,
    			':pctn_habocup_ag_online'=> isset($precios_2->pct[AGENCIA_ONLINE])?$precios_2->pct[AGENCIA_ONLINE]: null,
    			
    			':adr_to_online'=> isset($precios_2->adr[TO_ONLINE])?$precios_2->adr[TO_ONLINE]: null,
    			':num_habocup_to_online'=> isset($precios_2->num[TO_ONLINE])?$precios_2->num[TO_ONLINE]: null,
    			':pctn_habocup_to_online'=> isset($precios_2->pct[TO_ONLINE])?$precios_2->pct[TO_ONLINE]: null,
    			
    			':adr_otros'=> isset($precios_2->adr[OTROS])?$precios_2->adr[OTROS]: null,
    			':num_habocup_otros'=> isset($precios_2->num[OTROS])?$precios_2->num[OTROS]: null,
    			':pctn_habocup_otros'=> isset($precios_2->pct[OTROS])?$precios_2->pct[OTROS]: null
    	);
    	/// guardar primero intenta actualizar los que existen, si no se actualiza insertarlos.
    	
    	/// 1. actualizar los existentes
    	$sql = DbHelper::prepare_sql("update tb_aloja_personal_precios set
    			no_remunerado = :no_remunerado,
    			remunerado_fijo = :remunerado_fijo,
    			remunerado_eventual = :remunerado_eventual,
    			revpar_mensual = :revpar_mensual,
    			adr_mensual = :adr_mensual,
    			adr_to_tradicional = :adr_to_tradicional,
    			num_habocup_to_tradicional = :num_habocup_to_tradicional,
    			pctn_habocup_to_tradicional = :pctn_habocup_to_tradicional,
    			adr_empresas = :adr_empresas,
    			num_habocup_empresas = :num_habocup_empresas,
    			pctn_habocup_empresas = :pctn_habocup_empresas,
    			adr_ag_tradicional = :adr_ag_tradicional,
    			num_habocup_ag_tradicional = :num_habocup_ag_tradicional,
    			pctn_habocup_ag_tradicional = :pctn_habocup_ag_tradicional,
    			adr_particulares = :adr_particulares,
    			num_habocup_particulares = :num_habocup_particulares,
    			pctn_habocup_particulares = :pctn_habocup_particulares,
    			adr_grupos = :adr_grupos,
    			num_habocup_grupos = :num_habocup_grupos,
    			pctn_habocup_grupos = :pctn_habocup_grupos,
    			adr_internet = :adr_internet,
    			num_habocup_internet = :num_habocup_internet,
    			pctn_habocup_internet = :pctn_habocup_internet,
    			adr_ag_online = :adr_ag_online,
    			num_habocup_ag_online = :num_habocup_ag_online,
    			pctn_habocup_ag_online = :pctn_habocup_ag_online,
    			adr_to_online = :adr_to_online,
    			num_habocup_to_online = :num_habocup_to_online,
    			pctn_habocup_to_online = :pctn_habocup_to_online,
    			adr_otros = :adr_otros,
    			num_habocup_otros = :num_habocup_otros,
    			pctn_habocup_otros = :pctn_habocup_otros
    			where id_cuestionario = :id_cuestionario", $params);
    	
    	$db->query($sql);
    	
    	$updated = $db->affected_rows() > 0;
    	if (!$updated)
    	{
    			/// 2. insertamos los nuevos
    			$sql = DbHelper::prepare_sql("insert into tb_aloja_personal_precios
    					(id_cuestionario, 
    					 no_remunerado, remunerado_fijo, remunerado_eventual,
    					revpar_mensual,adr_mensual,
    			adr_to_tradicional,num_habocup_to_tradicional,pctn_habocup_to_tradicional,
    			adr_empresas,num_habocup_empresas,pctn_habocup_empresas,
    			adr_ag_tradicional,num_habocup_ag_tradicional,pctn_habocup_ag_tradicional,
    			adr_particulares,num_habocup_particulares,pctn_habocup_particulares,
    			adr_grupos,num_habocup_grupos,pctn_habocup_grupos,
    			adr_internet,num_habocup_internet,pctn_habocup_internet,
    			adr_ag_online,num_habocup_ag_online,pctn_habocup_ag_online,
    			adr_to_online,num_habocup_to_online,pctn_habocup_to_online,
    			adr_otros,num_habocup_otros,pctn_habocup_otros)
    	values (:id_cuestionario, 
    			:no_remunerado,:remunerado_fijo, :remunerado_eventual, 
    			:revpar_mensual, :adr_mensual, 
    			:adr_to_tradicional, :num_habocup_to_tradicional, :pctn_habocup_to_tradicional, 
    			:adr_empresas, :num_habocup_empresas, :pctn_habocup_empresas,
    			:adr_ag_tradicional, :num_habocup_ag_tradicional, :pctn_habocup_ag_tradicional, 
    			:adr_particulares, :num_habocup_particulares, :pctn_habocup_particulares, 
    			:adr_grupos, :num_habocup_grupos, :pctn_habocup_grupos, 
    			:adr_internet, :num_habocup_internet, :pctn_habocup_internet, 
    			:adr_ag_online, :num_habocup_ag_online, :pctn_habocup_ag_online, 
    			:adr_to_online, :num_habocup_to_online, :pctn_habocup_to_online, 
    			:adr_otros, :num_habocup_otros, :pctn_habocup_otros)",
    			$params);
    			 
    		$db->query($sql);
    			 
    		$updated = $db->affected_rows() > 0;
    	}
    	
    	$ok = ($updated);
    	
    	return $ok;
    }
      
    /**
     * Guarda los detalles de movimientos para el cuestionario indicado y la unidad territorial, sobreescribiendo los datos existentes.
     * Al finaliza la operacion, los datos en la BBDD serán solo los indicados.
     * @param unknown_type $id_cuestionario
     * @param unknown_type $habitaciones
     */
    public function guardar_esp_ut($id_cuestionario, $id_ut, $ut_mov)
    {
    	$db = new Istac_Sql();
    	
    	if ($ut_mov == null || count($ut_mov->movimientos) == 0)
    	{
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_esp_diarios where id_cuestionario = :id_cuestionario and id_unidad_territ = :id_unidad_territ",
    				array(':id_cuestionario' => $id_cuestionario,
    				':id_unidad_territ' => $id_ut));
    		$db->query($sql);
    		return true;
    	}
    	
    	$ok = true;
    	$dias_actualizados = array();
    	
    	foreach($ut_mov->movimientos as $dia => $esp)
    	{
    		$param = array(':id_cuestionario' => $id_cuestionario,
    				':id_unidad_territ' => $id_ut,
    				':dia' => $dia,
    				':entradas' => $esp->entradas,
    				':salidas' => $esp->salidas,
    				':pernoctaciones' => $esp->pernoctaciones);
    		
    		$sql = DbHelper::prepare_sql("update tb_aloja_esp_diarios 
    				set entradas = :entradas, 
    				salidas = :salidas, 
    				pernoctaciones = :pernoctaciones 
    				where id_cuestionario = :id_cuestionario and id_unidad_territ = :id_unidad_territ and dia = :dia",
    				$param);
    		
    		$db->query($sql);
    		
    		if ($db->affected_rows() == 0)
    		{
    			$sql = DbHelper::prepare_sql("insert into tb_aloja_esp_diarios 
    					(id_cuestionario, id_unidad_territ, dia, entradas, salidas, pernoctaciones) values (:id_cuestionario, :id_unidad_territ, :dia, :entradas, :salidas, :pernoctaciones)",
    					$param);
    			
    			$db->query($sql);
    		}
    		
    		$dias_actualizados[] = $dia;
    	}
    	
    	if (count($dias_actualizados) > 0)
    	{
    		$dias = implode(",", $dias_actualizados);
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_esp_diarios 
    				where id_cuestionario = :id_cuestionario and id_unidad_territ = :id_unidad_territ and dia not in ($dias)",
    			$param);
    		$db->query($sql);
    	}
    	
    	return true;
    }  

    /**
     * Carga los registros de presentes a comienzo de mes y presentas a fin de mes
     * relacionados con el cuestionario cuyo id se indica.
     * @param unknown_type $id_cuestionario
     */
    public function cargar_presentes_mes($id_cuestionario)
    {
    	$sql = DbHelper::prepare_sql("select id_unidad_territ, presentes_comienzo_mes, presentes_fin_mes 
    			                      from tb_aloja_presentes_mes 
    			                      where id_cuestionario = :id_cuestionario",
    			array(':id_cuestionario'  => $id_cuestionario));
    	 
    	$db = new Istac_Sql();
    	 
    	$db->query($sql);
    	
    	$result = array();
    	while ($db->next_record())
    	{
    		$id_ut = $db->f('id_unidad_territ');
    		$com_mes = (int)$db->f('presentes_comienzo_mes');
    		$fin_mes_ant = (int)$db->f('presentes_fin_mes');
    		$result[$id_ut] = array($com_mes, $fin_mes_ant);
    	}
    	
    	return $result;
    }
    
    /**
     * Guarda los detalles de presentas a comieno de mes y presentas mes anterior para el cuestionario indicado, sobreescribiendo los datos existentes.
     * Al finaliza la operacion, los datos en la BBDD serán solo los indicados.
     * @param unknown_type $id_cuestionario
     * @param unknown_type $habitaciones
     */
    public function guardar_presentes_mes($id_cuestionario, $id_ut, $com_mes, $fin_mes_ant)
    {
        $db = new Istac_Sql();
    	
    	if (!isset($com_mes) && !isset($fin_mes_ant))
    	{
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_presentes_mes 
    				where id_cuestionario = :id_cuestionario and id_unidad_territ = :id_unidad_territ",
    				array(':id_cuestionario' => $id_cuestionario,
    				':id_unidad_territ' => $id_ut));
    		$db->query($sql);
    		return true;
    	}
    	
    	$sql = DbHelper::prepare_sql("update tb_aloja_presentes_mes set 
    			presentes_comienzo_mes = :presentes_comienzo_mes, 
    			presentes_fin_mes = :presentes_fin_mes
    			where id_cuestionario = :id_cuestionario and id_unidad_territ = :id_unidad_territ",
    			array(':id_cuestionario'  => $id_cuestionario,
    					':id_unidad_territ' => $id_ut,
    					':presentes_comienzo_mes' => $com_mes,
    					':presentes_fin_mes' => $fin_mes_ant));
    	$db->query($sql);
    	 
    	if ($db->affected_rows() == 0)
    	{
    		$sql = DbHelper::prepare_sql("insert into tb_aloja_presentes_mes 
    				(id_cuestionario, id_unidad_territ, presentes_comienzo_mes, presentes_fin_mes)
    			values (:id_cuestionario, :id_unidad_territ, :presentes_comienzo_mes, :presentes_fin_mes)",
    			array(':id_cuestionario'  => $id_cuestionario,
    					':id_unidad_territ' => $id_ut,
    					':presentes_comienzo_mes' => $com_mes,
    					':presentes_fin_mes' => $fin_mes_ant));
    		
    		$db->query($sql);    		
    	}
    	
    	return true;
    }  

    /**
     * Obtiene el estado de validacion del cuestionario almacenado en la BBDD.
     * @param unknown_type $id_cuestionario
     */
    public function obtener_estado_validacion($id_cuestionario)
    {
    	$db = new Istac_Sql();
    	 
    	$sql = DbHelper::prepare_sql("select validacion from tb_aloja_cuestionarios
    			where id = :id_cuestionario",
    			array(':id_cuestionario' => $id_cuestionario));
    	$db->query($sql);
    	if ($db->next_record())
    	{
    		return (int)$db->f('validacion');
    	}
    	// No existe el cuestionario o no hay estado de validacion.
    	return false;
    }
    
    /**
     * Se establece el estado de validación en el que se encuentra un cuestionario
     * @param unknown_type $id_cuestionario
     * @param unknown_type $nuevo_estado
     */    
    public function establecer_estado_validacion($id_cuestionario, $nuevo_estado)
    {
        $db = new Istac_Sql();
    	
   		$sql = DbHelper::prepare_sql("update tb_aloja_cuestionarios
   				set validacion = :nuevo_estado
   				where id = :id_cuestionario",
   				array(':id_cuestionario' => $id_cuestionario,
   				':nuevo_estado' => $nuevo_estado));
   		$db->query($sql);
   		return ($db->affected_rows()>0);
    }
    
    /**
     * Devuelve los límites percentuales máximos para las invitaciones de los distintos tipos de clientes.
     * @return mixed[]
     */
    public function cargar_limite_invitaciones()
    {
    	$sql = "SELECT ID_TIPO_CLIENTE idcliente, LIMITE_PERCT perct FROM TB_ALOJA_PERCT_INVITACIONES WHERE ACTIVADO='S'";
    	$db = new Istac_Sql();
    	$db->query($sql);
    
    	$result = array();
    	while($db->next_record())
    	{
    		$cod = $db->f('idcliente');
    		$perct = $db->f('perct');
    		$result[$cod]=$perct;
    	}
    	return $result;
    }
    
}

/**
 * Objeto serializado a JSON que representa una unidad territorial.
 *
 */
class UT
{
	var $Pos;
	var $Id;
	var $Title;
	var $PresComMes;
	var $EPSLines;

	function __construct($pos, $id, $title, $pcm)
	{
		$this->Pos = $pos;
		$this->Id = $id;
		$this->Title = $title;
		$this->PresComMes = $pcm;
	}
}

/**
 *  Objeto serializado a JSON que representa un movimiento diario de entradas, salidas y pernoctaciones.
 *
 */
class EPS_line
{
	var $Dia;
	var $E;
	var $S;
	var $P;

	function __construct($d, $e, $s, $p)
	{
		$this->Dia = $d;
		$this->E = $e;
		$this->S = $s;
		$this->P = $p;
	}
}

?>