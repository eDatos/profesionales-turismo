<?php

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../../lib/DateHelper.class.php");
require_once(__DIR__."/../../lib/DbHelper.class.php");
require_once(__DIR__."/AlojaCuestionario.class.php");
require_once(__DIR__."/AlojaUTStat.class.php");
require_once(__DIR__."/AlojaDao.class.php");
require_once(__DIR__."/AlojaTempDao.class.php");


/** Valores de estado de validación del cuestionario (variable validacion) *+/ 
define("EV_CUESTIONARIO_COMPLETO", "1");
define("EV_CUESTIONARIO_INCOMPLETO", "2");
define("EV_VALIDADO_CRUZADAS", "3");
define("EV_VALIDADO_CON_AVISOS", "4");
define("EV_VALIDADO_COMPLETO", "5");
*/

class AlojaTempDao extends AlojaDao
{
    /**
     * Guarda los detalles de habitaciones para el cuestionario indicado, sobreescribiendo los datos existentes.
     * Al finaliza la operacion, los datos en la BBDD serán solo los indicados.
     * @param unknown_type $id_cuestionario
     * @param unknown_type $habitaciones
     */
    public function guardar_habitaciones_temp($id_cuestionario, $habitaciones)
    {
    	$db = new Istac_Sql();
    	
    	$ok = true;
    	
    	if ($habitaciones == null)
    	{
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_habitaciones_temp where id_cuestionario = :id_cuestionario",
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
    		$sql = DbHelper::prepare_sql("update tb_aloja_habitaciones_temp set
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
		    	$sql = DbHelper::prepare_sql("insert into tb_aloja_habitaciones_temp 
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
	    	$sql = DbHelper::prepare_sql("delete from tb_aloja_habitaciones_temp where id_cuestionario = :id_cuestionario and dia not in ($dias)", 
	    			array(':id_cuestionario' => $id_cuestionario));
	    	
	    	$db->query($sql);
	    	$ok &= ($db->affected_rows() > 0);
    	}
    	return $ok;
    } 
    
    /**
     * Guarda los detalles de personal y precio para el cuestionario indicado, sobreescribiendo los datos existentes.
     * Al finaliza la operacion, los datos en la BBDD serán solo los indicados.
     * @param unknown_type $id_cuestionario
     * @param unknown_type $habitaciones
     */
    public function guardar_personal_precios_temp($id_cuestionario, $personal, $precios)
    {
    	
    	$db = new Istac_Sql();
    	$ok = true;
    	 
    	if ($personal == null && $precios == null)
    	{
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_personal_precios_temp where id_cuestionario = :id_cuestionario", 
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
    	$sql = DbHelper::prepare_sql("update tb_aloja_personal_precios_temp set
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
    			$sql = DbHelper::prepare_sql("insert into tb_aloja_personal_precios_temp
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
    public function guardar_esp_ut_temp($id_cuestionario, $id_ut, $ut_mov)
    {
    	$db = new Istac_Sql();
    	
    	if ($ut_mov == null || count($ut_mov->movimientos) == 0)
    	{
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_esp_diarios_temp where id_cuestionario = :id_cuestionario and id_unidad_territ = :id_unidad_territ",
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
    		
    		$sql = DbHelper::prepare_sql("update tb_aloja_esp_diarios_temp 
    				set entradas = :entradas, 
    				salidas = :salidas, 
    				pernoctaciones = :pernoctaciones 
    				where id_cuestionario = :id_cuestionario and id_unidad_territ = :id_unidad_territ and dia = :dia",
    				$param);
    		
    		$db->query($sql);
    		
    		if ($db->affected_rows() == 0)
    		{
    			$sql = DbHelper::prepare_sql("insert into tb_aloja_esp_diarios_temp 
    					(id_cuestionario, id_unidad_territ, dia, entradas, salidas, pernoctaciones) values (:id_cuestionario, :id_unidad_territ, :dia, :entradas, :salidas, :pernoctaciones)",
    					$param);
    			
    			$db->query($sql);
    		}
    		
    		$dias_actualizados[] = $dia;
    	}
    	
    	if (count($dias_actualizados) > 0)
    	{
    		$dias = implode(",", $dias_actualizados);
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_esp_diarios_temp 
    				where id_cuestionario = :id_cuestionario and id_unidad_territ = :id_unidad_territ and dia not in ($dias)",
    			$param);
    		$db->query($sql);
    	}
    	
    	return true;
    }
    
    /**
     * Actualiza un objeto cuestionario con la información disponible en la tabla temporal de cuestionarios.
     * @param AlojaCuestionario $cuestionario
     */
    public function actualizar_cuestionario(AlojaCuestionario $cuestionario)
    {
    	$sql = DbHelper::prepare_sql("SELECT * FROM tb_aloja_cuestionarios_temp WHERE id=:id", array(':id' => $cuestionario->id));

    	$db = new Istac_Sql();
    	$db->query($sql);
    	
    	if($db->next_record())
    	{
    		$cuestionario->modo_introduccion= $db->f("modo_esp");
    		$cuestionario->modo_cumplimentado = $db->f("modo_horizontal");
    		$cuestionario->modo_porcentaje = $db->f("modo_porcentaje");
    		if ($db->f("dias_abierto") != null)
    			$cuestionario->dias_abierto = (int)$db->f("dias_abierto");
    		
    		return true;
    	}
    	
    	return false;
    }
    
    public function cargar_habitaciones_temp($id_cuestionario)
    {
    	$sql = DbHelper::prepare_sql("select id_cuestionario,dia,uso_doble,uso_individual,otras, plazas_supletorias
    			from tb_aloja_habitaciones_temp where id_cuestionario = :id_cuestionario",
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
    
    public function cargar_personal_precios_temp($id_cuestionario)
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
    			from tb_aloja_personal_precios_temp where id_cuestionario = :id_cuestionario",
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
    		
    		return array($pers,$precios);
    	}
    
    	return null;
    }
    
    /**
     * Borra los datos temporales asociados al cuestionario indicado.
     * @param unknown $id_cuestionario
     * @param unknown $op_es_guardar
     * @param unknown $id_uts
     */
    public function borrar_datos_cuestionario($id_cuestionario, $op_es_guardar, $id_uts=NULL)
    {
    	// NOTA: Existe una condición de borrado en cascado de las tablas temporales cuando se borra un cuestionario de la tabla TB_ALOJA_CUESTIONARIOS_TEMP.
    	// Cuando la operación es "enviar cuestionario", podemos aprovechar este borrado. Cuando la operación es "guardar cuestionario", debemos preservar la información de las UTs no mostradas en pantalla.
    	
    	$db = new Istac_Sql();
    	
    	if(!$op_es_guardar)
    	{
    		// Eliminamos toda la información temporal asociada al cuestionario aprovechando el borrado en cascada de la BDD.
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_cuestionarios_temp where id = :id_cuestionario",
    				array(':id_cuestionario' => $id_cuestionario));
    		$db->query($sql);
    		$registrosAfectados=$db->affected_rows();
    	}
    	else
    	{
    		// Eliminamos la información temporal existente de entradas, salidas y pernoctaciones de las unidades territoriales indicadas.
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_esp_diarios_temp where id_cuestionario = :id_cuestionario".($id_uts==NULL ? "" : " AND id_unidad_territ in (".implode($id_uts,",").")"),
    				array(':id_cuestionario' => $id_cuestionario));
    		$db->query($sql);
    		$registrosAfectados=$db->affected_rows();
    		
    		// Eliminamos la información temporal existentes de habitaciones.
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_habitaciones_temp where id_cuestionario = :id_cuestionario",
    				array(':id_cuestionario' => $id_cuestionario));
    		$db->query($sql);
    		$registrosAfectados=$db->affected_rows();
    		
    		// Eliminamos la información temporal existentes de personal y precios.
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_personal_precios_temp where id_cuestionario = :id_cuestionario",
    				array(':id_cuestionario' => $id_cuestionario));
    		$db->query($sql);
    		$registrosAfectados=$db->affected_rows();
    		
    		// Eliminamos la información temporal existentes de presentes a comienzos de mes de las unidades territoriales indicadas.
    		$sql = DbHelper::prepare_sql("delete from tb_aloja_presentes_mes_temp where id_cuestionario = :id_cuestionario".($id_uts==NULL ? "" : " AND id_unidad_territ in (".implode($id_uts,",").")"),
    				array(':id_cuestionario' => $id_cuestionario));
    		$db->query($sql);
    		$registrosAfectados=$db->affected_rows();
    	}
		
		return $registrosAfectados;
    }
    
    /**
     * Elimina completamente el cuestionario temporal de todas las tablas asociadas.
     * @param unknown $id_cuestionario
     */
    public function eliminar_cuestionario_temp($id_cuestionario)
    {
    	// Se supone que existe borrado en cascada de la tabla tb_aloja_cuestionarios_temp hacia el resto de tablas temporales.
    	$db = new Istac_Sql();
    	$sql = DbHelper::prepare_sql("delete from tb_aloja_cuestionarios_temp where id = :id_cuestionario",
    			array(':id_cuestionario' => $id_cuestionario));
    	$db->query($sql);
    	return ($db->affected_rows()>0);
    }
    
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
    		
    		
    		// Ahora agregamos la información temporal (reemplazando la información de las unidades territoriales que existan)
    		$sql = DbHelper::prepare_sql(
    				"SELECT esp.id_unidad_territ id_unidad_territ, ".
    				"cfg_grupos.literal grupo, cfg_grupos.orden grupo_orden, cfg_grupos.es_nacional nacional, ut.literal unidad_territorial, ".
    				"cfg_ut.ORDEN ut_orden, MAX(esp.dia) maximo, SUM(esp.entradas) sum_entradas, SUM(esp.salidas) sum_salidas, ".
    				"pres.presentes_comienzo_mes, pres.presentes_fin_mes ".
    				"FROM TB_ALOJA_ESP_DIARIOS_TEMP esp ".
    				"INNER JOIN TB_ALOJA_PRESENTES_MES_TEMP pres ON esp.ID_CUESTIONARIO=pres.ID_CUESTIONARIO AND esp.id_unidad_territ=pres.id_unidad_territ ".
    				"INNER JOIN TB_UNIDADES_TERRITORIALES ut ON esp.id_unidad_territ=ut.ID ".
    				"INNER JOIN TB_CONFIG_UNID_TERRIT cfg_ut ON esp.id_unidad_territ=cfg_ut.ID_UNIDAD_TERRIT ".
    				"INNER JOIN TB_CONFIG_GRUPOS_UNID_TERRIT cfg_grupos ON cfg_ut.ID_GRUPO_UNID_TERRIT=cfg_grupos.ID ".
    				"WHERE esp.ID_CUESTIONARIO=:id_cuestionario AND (entradas<>0 OR salidas<>0 OR pernoctaciones<>0) ".
    				"GROUP BY esp.id_unidad_territ, cfg_grupos.literal, cfg_grupos.orden, cfg_grupos.es_nacional, ut.literal, cfg_ut.ORDEN,pres.presentes_comienzo_mes, pres.presentes_fin_mes ".
    				"ORDER BY grupo_orden, ut_orden",
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
	
	public function cargar_esp_uts($estid, $mes_encuesta, $ano_encuesta, $id_uts)
	{
		// Este método es ligeramente menos eficiente que el de la clase base pero se supone que esta clase se usa sólo cuando es pertinente.
    	$result_uts = array();
    	if ($id_uts!==NULL && count($id_uts) == 0)
    		return $result_uts;
    		 
		// Como la información puede provenir de datos guardados o de datos temporales, generamos dos vectores con los identificadores territoriales...
		
		$id_uts1 = array();		// Identificadores de las unidades territoriales con datos temporales (tienen preferencia)
		$id_uts2 = array();		// Identificadores de las unidades territoriales con datos guardados
		
		$db = new Istac_Sql();		
		//Si no se pasan los id de las UTs, se entiende que se quieren todas las UTs del cuestionario
		//A partir de ese momento, el procedimiento es el mismo que cuando se pasa el parámetro
		if($id_uts===NULL)
		{
			// Se solicita la información de TODAS las unidades territoriales.
			
			// Leemos todas las unidades territoriales de las tablas definitivas.
			$sql = DbHelper::prepare_sql("SELECT DISTINCT esp.ID_UNIDAD_TERRIT, cfg_grupos.orden as ORDEN_GRUPO, cfg.orden as ORDEN
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
			
			// Se obtienen las ids de UTs del cuestionario y se guardan en $id_uts2.
			while ($db->next_record())
			{
				$id_uts2[]=array('id_unidad_territ' => $db->f('id_unidad_territ'), 'orden_grupo' => $db->f('orden_grupo'), 'orden' => $db->f('orden'));
			}
			$db->disconnect();
			
			// Leemos todas las unidades territoriales de las tablas temporales.
			$sql = DbHelper::prepare_sql("SELECT DISTINCT esp.ID_UNIDAD_TERRIT, cfg_grupos.orden as ORDEN_GRUPO, cfg.orden as ORDEN
					FROM TB_ALOJA_CUESTIONARIOS c
					INNER JOIN TB_ALOJA_ESP_DIARIOS_TEMP esp
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
			
			// Se obtienen las ids de UTs del cuestionario y se guardan en $id_uts1.
			while ($db->next_record())
			{
				$id_uts1[]=array('id_unidad_territ' => $db->f('id_unidad_territ'), 'orden_grupo' => $db->f('orden_grupo'), 'orden' => $db->f('orden'));
			}
		}
		else
		{
			// Se solicita la información de una lista explícita de unidades territoriales.
			
			foreach($id_uts as $id)
			{
				$result_uts[$id] = array('nombre' => null, 'presentes_comienzo_mes' => 0, 'filas' => new AlojaUTMovimientos());
			}
			
			$ids = implode($id_uts,",");
			
			// Leemos todas las unidades territoriales indicadas presentes en las tablas definitivas.
			$sql = DbHelper::prepare_sql("SELECT DISTINCT esp.ID_UNIDAD_TERRIT, cfg_grupos.orden as ORDEN_GRUPO, cfg.orden as ORDEN
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
					AND esp.ID_UNIDAD_TERRIT in ($ids)
					and cfg_grupos.fecha_alta <= to_date(:fecha_referencia,'dd-mm-yyyy') 
					AND (cfg_grupos.fecha_baja is null or cfg_grupos.fecha_baja>to_date(:fecha_referencia, 'dd-mm-yyyy'))
					ORDER BY cfg_grupos.orden, cfg.orden",
					array(':estid' => (string)$estid, 
						':mes' => (int)$mes_encuesta,
						':ano' => (int)$ano_encuesta,
						':fecha_referencia' => (string)sprintf("%02d-%02d-%04d", 1, $mes_encuesta, $ano_encuesta) ));
								
			$db->query($sql);
			
			// Se obtienen las ids de UTs del cuestionario y se guardan en $id_uts2.
			while ($db->next_record())
			{
				$id_uts2[]=array('id_unidad_territ' => $db->f('id_unidad_territ'), 'orden_grupo' => $db->f('orden_grupo'), 'orden' => $db->f('orden'));
			}
			$db->disconnect();
			
			
			// Leemos todas las unidades territoriales indicadas presentes en las tablas temporales.
			$sql = DbHelper::prepare_sql("SELECT DISTINCT esp.ID_UNIDAD_TERRIT, cfg_grupos.orden as ORDEN_GRUPO, cfg.orden as ORDEN
					FROM TB_ALOJA_CUESTIONARIOS c
					INNER JOIN TB_ALOJA_ESP_DIARIOS_TEMP esp
					ON c.ID = esp.ID_CUESTIONARIO
					INNER JOIN TB_CONFIG_UNID_TERRIT cfg
					on esp.ID_UNIDAD_TERRIT = cfg.id_unidad_territ
					INNER JOIN TB_CONFIG_GRUPOS_UNID_TERRIT cfg_grupos
					ON cfg_grupos.ID = cfg.ID_GRUPO_UNID_TERRIT
					WHERE c.ID_ESTABLECIMIENTO         = :estid
					AND c.MES                          = :mes
					and c.ano                          = :ano
					AND esp.ID_UNIDAD_TERRIT in ($ids)
					and cfg_grupos.fecha_alta <= to_date(:fecha_referencia,'dd-mm-yyyy') 
					AND (cfg_grupos.fecha_baja is null or cfg_grupos.fecha_baja>to_date(:fecha_referencia, 'dd-mm-yyyy'))
					ORDER BY cfg_grupos.orden, cfg.orden",
					array(':estid' => (string)$estid, 
						':mes' => (int)$mes_encuesta,
						':ano' => (int)$ano_encuesta,
						':fecha_referencia' => (string)sprintf("%02d-%02d-%04d", 1, $mes_encuesta, $ano_encuesta) ));
								
			$db->query($sql);
			
			// Se obtienen las ids de UTs del cuestionario y se guardan en $id_uts1.
			while ($db->next_record())
			{
				$id_uts1[]=array('id_unidad_territ' => $db->f('id_unidad_territ'), 'orden_grupo' => $db->f('orden_grupo'), 'orden' => $db->f('orden'));
			}
		}
		
		// Apartir de aquí, ya tenemos los identificadores territoriales pedidos separados por temporales y guardados.
		// Debemos preservar el orden de los identificadores de entrada.
		$nids=count($id_uts1)+count($id_uts2);
		
		
		// Ahora procedemos a hacer un merge de ambos vectores ordenados para obtener un único vector ya ordenado.
		// Para obtener el vector resultante, vamos avanzando en ambos vectores a la vez comparando sus elementos.
		// En cada comparación, se toman los elementos en orden ascendente.
		// Si son iguales, se toma el elemento perteneciente a la lista de identificadores temporales y se descarta el de la lista de guardados.
		
		// En lugar de añadir comprobaciones de rango, marcamos el final de cada vector con un elemento mayor que cualquier otro.
		// Sabiendo el número total de elementos, pararemos cuando hayan sido comparados todos los elementos.
		$id_uts1[]=array('id_unidad_territ' => 9999, 'orden_grupo' => 9999, 'orden' => 9999);
		$id_uts2[]=array('id_unidad_territ' => 9999, 'orden_grupo' => 9999, 'orden' => 9999);
		
		
		// Además vamos a generar los vectores con sólo los identificadores de las unidades territoriales para su posterior uso.
		$id_uts3 = array();		// Identificadores de las unidades territoriales con datos temporales.
		$id_uts4 = array();		// Identificadores de las unidades territoriales con datos guardados y SIN datos temporales.
		
		$i1=0;
		$i2=0;
		while($nids>0)
		{
			// Suposición inicial: el elemento del vector $id_uts1 es menor...
			$sufijoTabla="_TEMP";
			$id=$id_uts1[$i1]['id_unidad_territ'];
			
			if($id_uts1[$i1]['orden_grupo']>$id_uts2[$i2]['orden_grupo'])
			{
				$id=$id_uts2[$i2]['id_unidad_territ'];
				$sufijoTabla="";
				$id_uts4[]=$id_uts2[$i2]['id_unidad_territ'];
				$i2++;
			}
			else
			{
				if(($id_uts1[$i1]['orden_grupo']==$id_uts2[$i2]['orden_grupo']) && ($id_uts1[$i1]['orden']>$id_uts2[$i2]['orden']))
				{
					$id=$id_uts2[$i2]['id_unidad_territ'];
					$sufijoTabla="";
					$id_uts4[]=$id_uts2[$i2]['id_unidad_territ'];
					$i2++;
				}
				else
				{
					// Debemos consumir los datos del primer vector.
					
					if(($id_uts1[$i1]['orden_grupo']==$id_uts2[$i2]['orden_grupo']) && ($id_uts1[$i1]['orden']==$id_uts2[$i2]['orden']))
					{
						// Caso de existir datos guardados y datos temporales para la misma unidad territorial, ignoramos completamente los guardados.
						// Debemos consumir los datos del segundo vector.
						$i2++;
						$nids--;
					}
					
					$id_uts3[]=$id_uts1[$i1]['id_unidad_territ'];
					$i1++;
				}
			}
			
			// Recuperamos los datos de la unidad territorial...
			$sql = DbHelper::prepare_sql("SELECT esp.ID_UNIDAD_TERRIT,esp.DIA,esp.ENTRADAS,esp.SALIDAS,esp.PERNOCTACIONES,ut.LITERAL,cfg_grupos.ES_NACIONAL
										FROM TB_ALOJA_CUESTIONARIOS c
										INNER JOIN TB_ALOJA_ESP_DIARIOS".$sufijoTabla." esp
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
										and ut.id                         = :id
										and cfg_grupos.fecha_alta <= to_date(:fecha_referencia,'dd-mm-yyyy') 
										AND (cfg_grupos.fecha_baja is null or cfg_grupos.fecha_baja>to_date(:fecha_referencia, 'dd-mm-yyyy'))
					AND ut.id in ($ids) order by esp.id_unidad_territ, esp.dia",
					array(':estid' => (string)$estid, 
							':mes' => (int)$mes_encuesta,
							':ano' => (int)$ano_encuesta,
							':id' => (int)$id,
							':fecha_referencia' => (string)sprintf("%02d-%02d-%04d", 1, $mes_encuesta, $ano_encuesta) ));
			
			
			$db->query($sql);
			
			while ($db->next_record())
			{
				$id = $db->f('id_unidad_territ');
				$result_uts[$id]['nombre'] = $db->f('literal');
				$es_nacional = $db->f('es_nacional');
				$result_uts[$id]['es_nacional'] = $es_nacional;
				if ($es_nacional=='1' || $es_nacional=='3')
					$result_uts[$id]['nombre'] = "España > " . $result_uts[$id]['nombre'];
				
				$esp = new AlojaESP();
				$esp->entradas = $db->f('entradas');
				$esp->salidas = $db->f('salidas');
				$esp->pernoctaciones = $db->f('pernoctaciones');
				$dia = $db->f('dia');
				$result_uts[$id]['filas']->movimientos[$dia] = $esp;
			}
			$db->disconnect();
			
			$nids--;
		}
		
		// Si hay alguna ut sin registros, no se inicializara su nombre, hacerlo ahora.
		$ut_sin_nombre = array();
		foreach($result_uts as $id_ut => $info_ut)
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
				$result_uts[$id]['nombre'] = $db->f('literal');
				$es_nacional = $db->f('es_nacional');
				$result_uts[$id]['es_nacional'] = $es_nacional;
				if ($es_nacional=='1' || $es_nacional=='3')
					$result_uts[$id]['nombre'] = "España > " . $result_uts[$id]['nombre'];    			
			}
		}
	
		// Obtener los presentes a comienzo de mes
		$pres_com_mes = $this->cargar_presentes_mes_uts($estid, $mes_encuesta, $ano_encuesta, $id_uts4, $id_uts3);
		foreach($pres_com_mes as $id => $pcm)
		{
			$result_uts[$id]['presentes_comienzo_mes'] = $pcm;
		}
    		
		return $result_uts;
	}
	
	private function cargar_presentes_mes_uts($estid, $mes_encuesta, $ano_encuesta, $id_uts=NULL, $id_uts_temp=NULL)
	{
		$result = array();
		
		if($id_uts===NULL || (count($id_uts) > 0))
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
			 
			//Como la query está ordenada por meses, el valor de pernoct tendrá en primer lugar el valor de presentes a final de mes
			//anterior (si existe) y luego presentes a comienzo de mes (si existe)
			while ($db->next_record())
			{
				$id = $db->f('unid_territ');
				$result[$id] = $db->f('presentes');
			}
			
			$db->disconnect();
		}
		
		if($id_uts_temp===NULL || (count($id_uts_temp) > 0))
		{
			$sql = DbHelper::prepare_sql("SELECT pres.id_unidad_territ unid_territ, cuest.mes mes, cuest.ano ano, pres.presentes_comienzo_mes presentes
    			FROM tb_aloja_cuestionarios_temp cuest
    			INNER JOIN tb_aloja_presentes_mes_temp pres
    			ON cuest.id = pres.id_cuestionario
    			WHERE cuest.id_establecimiento=:estid
    			AND cuest.mes=:mes_encuesta AND cuest.ano=:ano_encuesta".
					($id_uts_temp==NULL ? "" : " AND pres.id_unidad_territ in (".implode($id_uts_temp,",").")")
					." ORDER BY unid_territ, ano, mes",
					array(':estid' => (string)$estid,
							':mes_encuesta' => (int)$mes_encuesta,
							':ano_encuesta' => (int)$ano_encuesta));
					 
			$db = new Istac_Sql();
			$db->query($sql);
			 
			//Como la query está ordenada por meses, el valor de pernoct tendrá en primer lugar el valor de presentes a final de mes
			//anterior (si existe) y luego presentes a comienzo de mes (si existe)
			while ($db->next_record())
			{
				$id = $db->f('unid_territ');
				$result[$id] = $db->f('presentes');
			}
		}
		
		return $result;
	}
    
    /**
     * Calcula los totales de entradas, salidas y pernoctaciones con los datos almacenados en BBDD.
     * @param unknown_type $estid
     * @param unknown_type $mes_encuesta
     * @param unknown_type $ano_encuesta
     */
    public function calcular_esp_totales($estid, $mes_encuesta, $ano_encuesta)
    {
    	$result = new AlojaUTMovimientos();

    	$comando="SELECT esp_dia.dia, SUM(esp_dia.entradas) entradas, SUM(esp_dia.salidas) salidas, SUM(esp_dia.pernoctaciones) pernoctaciones
    	  FROM (
    			  SELECT mov.dia, mov.entradas , mov.salidas , mov.pernoctaciones
		    	  FROM tb_aloja_esp_diarios_temp mov
		    	  INNER JOIN tb_aloja_cuestionarios c ON mov.id_cuestionario=c.id
		    	  INNER JOIN tb_unidades_territoriales ut ON ut.ID=mov.id_unidad_territ
		    	  WHERE c.ID_ESTABLECIMIENTO=:estid
		    	   AND c.MES=:mes
		    	   AND c.ano=:ano
		    	   AND upper(ut.literal)<>'ESPAÑA'
		    	UNION ALL		    	  
		    	  SELECT mov.dia, mov.entradas, mov.salidas, mov.pernoctaciones
		    	  FROM tb_aloja_esp_diarios mov
		    	  INNER JOIN tb_aloja_cuestionarios c ON mov.id_cuestionario=c.id
		    	  INNER JOIN tb_unidades_territoriales ut ON ut.ID=mov.id_unidad_territ
		    	  WHERE mov.id_unidad_territ NOT IN (SELECT DISTINCT id_unidad_territ FROM tb_aloja_esp_diarios_temp mov2 INNER JOIN tb_aloja_cuestionarios c2 ON mov2.id_cuestionario=c2.id INNER JOIN tb_unidades_territoriales ut2 ON ut2.ID=mov2.id_unidad_territ WHERE c2.ID_ESTABLECIMIENTO=:estid AND c2.MES=:mes AND c2.ano=:ano AND upper(ut2.literal)<>'ESPAÑA')
		    	   AND c.ID_ESTABLECIMIENTO=:estid
		    	   AND c.MES=:mes
		    	   AND c.ano=:ano
    			   AND upper(ut.literal)<>'ESPAÑA'
    			) esp_dia
    	  GROUP BY esp_dia.dia
    	  ORDER BY esp_dia.dia";
    	
    	$sql = DbHelper::prepare_sql($comando,array(':estid' => (string)$estid, ':mes' => (int)$mes_encuesta, ':ano' => (int)$ano_encuesta));
    
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
    	$db->disconnect();
    	
    	$comando="SELECT SUM(presentes_comienzo_mes) presentes
					FROM (
					  SELECT presentes_comienzo_mes FROM tb_aloja_cuestionarios_temp c
					  INNER JOIN tb_aloja_presentes_mes_temp pres ON c.id=pres.id_cuestionario
		    			WHERE c.id_establecimiento=:estid
		    			AND c.mes=:mes
		    			AND c.ano=:ano
					  UNION ALL
					  SELECT presentes_comienzo_mes	FROM tb_aloja_cuestionarios c
					  INNER JOIN tb_aloja_presentes_mes pres ON c.id=pres.id_cuestionario
		    			WHERE pres.id_unidad_territ NOT IN (SELECT DISTINCT id_unidad_territ FROM tb_aloja_cuestionarios_temp c2 INNER JOIN tb_aloja_presentes_mes_temp pres2 ON c2.id=pres2.id_cuestionario WHERE c2.id_establecimiento=:estid AND c2.MES=:mes AND c2.ano=:ano)
		    			AND c.id_establecimiento=:estid
		    			AND c.mes=:mes
		    			AND c.ano=:ano)";
    	
    	$sql = DbHelper::prepare_sql($comando,array(':estid' => (string)$estid, ':mes' => (int)$mes_encuesta, ':ano' => (int)$ano_encuesta));
      	$db->query($sql);

    	$result->presentes_comienzo_mes=0;
    	if($db->next_record())
    	{
    		$result->presentes_comienzo_mes+=$db->f('presentes');
     	}
    	
    	return $result;
    }
    
    public function guardar_cuestionario_temp(AlojaCuestionario $cuestionario)
    {
    	if ($cuestionario->es_nuevo())
    		return false;
    	
    	$db = new Istac_Sql();
    	
    	$params = array();
    	$params[':id'] = $cuestionario->id;
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
    	$params[':validacion'] = (string)$cuestionario->validacion;
    	
    	if ($cuestionario->dias_abierto != null)
    	{
    		$params[':dias_abierto'] = $cuestionario->dias_abierto;
    		
    		$sql = DbHelper::prepare_sql("update tb_aloja_cuestionarios_temp set
    				id_establecimiento=:estid,
    			    id_usuario=:userid,
    			    id_tipo_carga=:tipo_carga,
    			    codigo_registro=:cod_reg,
    			    mes=:mes,
    			    ano=:ano,
    				fecha_recepcion = to_date(:fecha_recepcion, 'dd/mm/yyyy HH24:MI:SS'),
    			    modo_esp=:modo_esp,
    			    modo_horizontal=:modo_horizontal,
    			    modo_porcentaje=:modo_porcentaje,
    			    dias_abierto=:dias_abierto,
    			    validacion=:validacion
    				where id=:id",$params);
    	}
    	else {
     		$sql = DbHelper::prepare_sql("update tb_aloja_cuestionarios_temp set
    				id_establecimiento=:estid,
    			    id_usuario=:userid,
    			    id_tipo_carga=:tipo_carga,
    			    codigo_registro=:cod_reg,
    			    mes=:mes,
    			    ano=:ano,
    				fecha_recepcion = to_date(:fecha_recepcion, 'dd/mm/yyyy HH24:MI:SS'),
    			    modo_esp=:modo_esp,
    			    modo_horizontal=:modo_horizontal,
    			    modo_porcentaje=:modo_porcentaje,
    			    dias_abierto=NULL,
    			    validacion=:validacion
    				where id=:id",$params);   		
    	}
    	
    	$db->query($sql);
    	$updated = $db->affected_rows() > 0;
    	
    	if(!$updated)
    	{
    		if ($cuestionario->dias_abierto != null)
    		{
    			$sql = DbHelper::prepare_sql("insert into tb_aloja_cuestionarios_temp
				  (id,id_establecimiento,id_usuario,id_tipo_carga,codigo_registro,mes,ano,
				  fecha_recepcion,modo_esp,modo_horizontal,modo_porcentaje,dias_abierto, validacion) values
				  (:id,:estid,:userid,:tipo_carga,:cod_reg,:mes,:ano,to_date(:fecha_recepcion, 'dd/mm/yyyy HH24:MI:SS'),:modo_esp,:modo_horizontal,:modo_porcentaje,:dias_abierto, :validacion)",
    					$params);
    		}
    		else
    		{
    			$sql = DbHelper::prepare_sql("insert into tb_aloja_cuestionarios_temp
    				(id,id_establecimiento,id_usuario,id_tipo_carga,codigo_registro,mes,ano,
    				fecha_recepcion,modo_esp,modo_horizontal,modo_porcentaje, validacion) values
    				(:id,:estid,:userid,:tipo_carga,:cod_reg,:mes,:ano,to_date(:fecha_recepcion, 'dd/mm/yyyy HH24:MI:SS'),:modo_esp,:modo_horizontal,:modo_porcentaje, :validacion)",
    					$params);
    		}
    	
	    	$db->query($sql);
	    	$updated = $db->affected_rows() > 0;
    	}
    	
    	return $updated;
    }

    public function guardar_presentes_mes_temp($id_cuestionario, $id_ut, $com_mes, $fin_mes_ant)
    {
        $db = new Istac_Sql();
        
        $sql = DbHelper::prepare_sql("delete from tb_aloja_presentes_mes_temp
    				where id_cuestionario = :id_cuestionario and id_unidad_territ = :id_unidad_territ",
        		array(':id_cuestionario' => $id_cuestionario,
        				':id_unidad_territ' => $id_ut));
        $db->query($sql);
        
    	
    	if (!isset($com_mes) && !isset($fin_mes_ant))
    		return true;
    	
    	$sql = DbHelper::prepare_sql("insert into tb_aloja_presentes_mes_temp
    				(id_cuestionario, id_unidad_territ, presentes_comienzo_mes, presentes_fin_mes)
    			values (:id_cuestionario, :id_unidad_territ, :presentes_comienzo_mes, :presentes_fin_mes)",
    				array(':id_cuestionario'  => $id_cuestionario,
    						':id_unidad_territ' => $id_ut,
    						':presentes_comienzo_mes' => $com_mes,
    						':presentes_fin_mes' => $fin_mes_ant));
    		
    	$db->query($sql);
    	return ($db->affected_rows() > 0);
    }
}


?>