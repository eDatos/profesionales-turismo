<?php
    require_once(__DIR__."/../lib/DbHelper.class.php");
    require_once(__DIR__."/audit/AuditLog.class.php");
    
    /**
     *  TABLAS DE LOS RESULTADOS DE ALOJAMIENTOS REALES
     **/
    
    define("VIAJEROS_ENTRADOS", 1);
    define("VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA", 2);
    define("VIAJEROS_ALOJADOS", 3);
    define("VIAJEROS_ALOJADOS_POR_PAIS", 4);
    define("PERNOCTACIONES"  ,  5);
    define("PERNOCTACIONES_POR_PAIS"  ,  6);
    define("ESTANCIA_MEDIA",    7);
    define("ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA",    8);
    define("INDICE_OCUPACION",  9);
    define("INDICE_OCUPACION_POR_HABITACIONES",  10);
    define("TARIFA_MEDIA_POR_HABITACION_MENSUAL", 11);
    
    define("TIT_".VIAJEROS_ENTRADOS, "Viajeros entrados");
    define("TIT_".VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA, "Viajeros entrados por lugar de residencia");
    define("TIT_".VIAJEROS_ALOJADOS, "Viajeros alojados");
    define("TIT_".VIAJEROS_ALOJADOS_POR_PAIS, "Viajeros alojados por lugar de residencia");
    define("TIT_".PERNOCTACIONES, "Pernoctaciones");
    define("TIT_".PERNOCTACIONES_POR_PAIS, "Pernoctaciones por lugar de residencia");
    define("TIT_".ESTANCIA_MEDIA, "Estancia media");
    define("TIT_".ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA, "Estancia media por lugar de residencia");
    define("TIT_".INDICE_OCUPACION, "Índice censal de ocupación por plazas");
    define("TIT_".INDICE_OCUPACION_POR_HABITACIONES, "Índice censal de ocupación por habitaciones");
    define("TIT_".TARIFA_MEDIA_POR_HABITACION_MENSUAL, "Tarifa media por habitación mensual");
    
    class RepositoriesDao  
    {        

    	function getUrls($page)
    	{
    		$ret = array();
    		for($i = VIAJEROS_ENTRADOS; $i <= TARIFA_MEDIA_POR_HABITACION_MENSUAL; $i++)
    		{
    			$ret[] = array('tit' => constant("TIT_".$i), 
    					'url' => $page->build_url(PAGE_ALOJA_RESULTADOS, array('tabla' => $i)));
    		}
    		return $ret;
    	}
    	
    	function getTablaResultados($user_id, $establishmentId,  $table)
    	{
    		$datos = array();
    	    switch ($table) {
	            case VIAJEROS_ALOJADOS          : 
	                $datos = $this->getDatosViajerosAlojados($establishmentId);  
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_VIAJEROS_ALOJADOS, SUCCESSFUL);
	                break;
	            case VIAJEROS_ENTRADOS    : 
	                $datos = $this->getDatosViajerosEntrados($establishmentId);   
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_VIAJEROS_ENTRADOS, SUCCESSFUL);
	                break;
	            case VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA    : 
	                $datos = $this->getDatosViajerosEntradosPorLugarDeResidencia($establishmentId);   
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA, SUCCESSFUL);
	                break;
	            case PERNOCTACIONES             : 
	                $datos = $this->getDatosPernoctaciones($establishmentId);   
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_PERNOCTACIONES, SUCCESSFUL);
	                break;
	            case INDICE_OCUPACION           : 
	                $datos = $this->getDatosIndiceCensalOcupacionPorPlazas($establishmentId);  
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_INDICE_OCUPACION, SUCCESSFUL);
	                break;
	            case INDICE_OCUPACION_POR_HABITACIONES          : 
	                $datos = $this->getDatosIndiceCensalOcupacionPorHabitaciones($establishmentId);  
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_INDICE_OCUPACION_POR_HABITACIONES, SUCCESSFUL);
	                break;
	            case ESTANCIA_MEDIA             : 
	                $datos = $this->getDatosEstanciaMedia($establishmentId);    
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_ESTANCIA_MEDIA, SUCCESSFUL);
	                break;
	            case ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA    : 
	                $datos = $this->getDatosEstanciaMediaPorLugarDeResidencia($establishmentId);    
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA, SUCCESSFUL);
	                break;
	            case VIAJEROS_ALOJADOS_POR_PAIS : 
	                $datos = $this->getDatosViajerosAlojadosPorPais($establishmentId); 
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_VIAJEROS_ALOJADOS_POR_PAIS, SUCCESSFUL);
	                break;
	            case PERNOCTACIONES_POR_PAIS    : 
	                $datos = $this->getDatosPernoctacionesPorPais($establishmentId);   
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_PERNOCTACIONES_POR_PAIS, SUCCESSFUL);
	                break;
	           case TARIFA_MEDIA_POR_HABITACION_MENSUAL    : 
	                $datos = $this->getDatosTarifaMediaPorHabitacionMensual($establishmentId);   
	                @AuditLog::log($user_id, $establishmentId, CONSULTA_TABLA_TARIFA_MEDIA_POR_HABITACION_MENSUAL, SUCCESSFUL);
	                break;
	        }
	        
	        return $datos;
    	}
              
//------------------------------------------------------------------------------------------------------------------------------                

        function getDatosViajerosAlojados($establishmentId) {
            
        	$result = array();
                     
		    $sql = DbHelper::prepare_sql("SELECT * FROM (
		
						SELECT a.anyo||' '||decode(a.mes,'1','Enero','2','Febrero','3','Marzo',
						       '4','Abril','5','Mayo','6','Junio','7','Julio','8','Agosto',
						       '9','Septiembre','10','Octubre','11','Noviembre',
						       '12','Diciembre') as mes_anio, 
						       
						       decode(p.estab_dato,null,'*',trim(to_char(p.estab_dato,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.'''))) as estab_dato,       
							   trim(to_char(a.dato_municipio_turistico,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
							   trim(to_char(a.dato_isla,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
							   trim(to_char(a.dato_canarias,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato, 
		    				   t.id_municipio_turistico,
							   
						
						       DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros',
						       '2','hoteleros','6','hoteleros') AS tipo,
						       
						       c.descripcion AS cabecera_grupo,
							   decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
						       
						FROM tb_sas_alojamiento a,
						     tb_municipios m,
						     tb_establecimientos_unico eu, 
						     tb_establecimientos_historico eh,
						     tb_cabeceras_pies_grupos_cat c, 
							 tb_municipios_turisticos t,
						
						     (SELECT h.anyo, h.mes, h.alojados as estab_dato      
						     FROM tb_estab_datos_publicacion h
						     /* @parametro id_establecimiento */
						     WHERE h.id_establecimiento = :estid) p 
						
						      /* @parametro id_establecimiento */
						WHERE eh.id_establecimiento = :estid 
						
						      /* join */
						      AND eh.id_establecimiento = eu.id_establecimiento
						
						      /* tipo de establecimiento */      
						      AND a.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
						            
						      /* variable Viajeros alojados*/
						      AND a.id_variable = 6 
						      
						      /* establecimiento no de baja */
						      AND eh.fecha_baja IS NULL      
						      
							  /* isla y municipio turistico */
							  AND t.id_municipio_turistico = a.id_municipio_turistico
							  AND t.id_isla = a.id_isla
							  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

							  /* isla y municipio del establecimiento */
							  AND m.id_municipio = eu.id_municipio
							  AND m.id_isla = eu.id_isla
							  AND m.id_municipio = t.id_municipio
							  AND m.id_isla = t.id_isla  
						      
						      /*  categoria del establecimiento */
						      AND a.id_categoria = eh.id_categoria
						      
						      /* cabecera y pie del grupo */
						      AND a.id_isla = c.id_isla
						      AND eh.id_categoria = c.id_categoria
						      AND a.id_tipo_publicacion = c.id_tipo_publicacion
							  AND a.id_municipio_turistico = c.id_municipio_turistico
						      
						      /* mes y año de publicación del dato */
						      AND p.anyo(+) = a.anyo
						      AND p.mes(+) = a.mes 
						      
						ORDER BY a.anyo desc, to_number(a.mes) desc)
						
					WHERE rownum < 26", array(":estid" => (string)$establishmentId));

		    $db = new Istac_Sql();
		    
            $db->query($sql);
                                                
            while ($db->next_record()) {                                                 
                $mes_anio      = $db->f('mes_anio');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                
                $result[] = array('mes_anio'  => $mes_anio,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato, 
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);               
            }

            if (sizeof($result)>0)
            {
            	$ultimo_indice=sizeof($result)-1;
            	$result[$ultimo_indice]['tipo']=$db->f('tipo');
            }

            
            return $result;
        }
        
//------------------------------------------------------------------------------------------------------------------------------                        

        function getDatosPernoctaciones($establishmentId) {
            $result = array();
            
	        
		    $sql =  DbHelper::prepare_sql("SELECT * FROM (
		
						SELECT a.anyo||' '||decode(a.mes,'1','Enero','2','Febrero','3','Marzo',
						       '4','Abril','5','Mayo','6','Junio','7','Julio','8','Agosto',
						       '9','Septiembre','10','Octubre','11','Noviembre',
						       '12','Diciembre') as mes_anio, 
						       
						       /* dato del establecimiento */ 
						       decode(p.estab_dato,null,'*',trim(to_char(p.estab_dato,'9G999G999G990'))) as estab_dato,
						       
						      /* datos del municipio, isla y canarias */       
							   trim(to_char(a.dato_municipio_turistico,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
							   trim(to_char(a.dato_isla,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
							   trim(to_char(a.dato_canarias,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato, 							   
						       t.id_municipio_turistico, 
		    		
						       DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros',
						       '2','hoteleros','6','hoteleros') AS tipo,
						       
						       c.descripcion AS cabecera_grupo,
							   decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
						       
						FROM   tb_sas_alojamiento a,
						       tb_municipios m,
						       tb_establecimientos_unico eu,
						       tb_establecimientos_historico eh,
						       tb_cabeceras_pies_grupos_cat c,
							   tb_municipios_turisticos t,
						     
						       (SELECT h.anyo, h.mes, h.pernoctaciones as estab_dato       
						       FROM tb_estab_datos_publicacion h
						       /* @parametro id_establecimiento */
						       WHERE h.id_establecimiento = :estid) p 
						      
						WHERE /* @parametro id_establecimiento */ 
						      eh.id_establecimiento = :estid 
						
						      /* tipo de publicación según el tipo de establecimiento */
						      AND a.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
						      
						      /* variable Pernoctaciones */
						      AND a.id_variable = 2 
						
						      /* establecimiento no de baja */
						      AND eh.fecha_baja IS NULL      
						
						      /* join historico con unico */
						      AND eh.id_establecimiento = eu.id_establecimiento
						  
							  /* isla y municipio turistico */
							  AND t.id_municipio_turistico = a.id_municipio_turistico
							  AND t.id_isla = a.id_isla
							  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

							  /* isla y municipio del establecimiento */
							  AND m.id_municipio = eu.id_municipio
							  AND m.id_isla = eu.id_isla
							  AND m.id_municipio = t.id_municipio
							  AND m.id_isla = t.id_isla    
						      
						      /*  categoria del establecimiento */
						      AND a.id_categoria = eh.id_categoria
						      
						      /* cabecera y pie del grupo */
						      AND a.id_isla = c.id_isla
						      AND eh.id_categoria = c.id_categoria
						      AND a.id_tipo_publicacion = c.id_tipo_publicacion
							  AND a.id_municipio_turistico = c.id_municipio_turistico
						      
						      /* mes y año de publicación del dato */
						      AND p.anyo(+) = a.anyo
						      AND p.mes(+) = a.mes 
						      
						ORDER BY a.anyo desc, to_number(a.mes) desc)
						
					WHERE rownum < 26", array(":estid" => (string)$establishmentId));

		    $db = new Istac_Sql();
		    
            $db->query($sql);
            
            while ($db->next_record()) {                                                 
                $mes_anio      = $db->f('mes_anio');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                
                $result[] = array('mes_anio'  => $mes_anio,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato, 
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);           
            }
			
			if (sizeof($result)>0)
            {
            	$ultimo_indice=sizeof($result)-1;
            	$result[$ultimo_indice]['tipo']=$db->f('tipo');
            }
            return $result;
        } 
        
//------------------------------------------------------------------------------------------------------------------------------                        
        
        function getDatosIndiceCensalOcupacionPorPlazas($establishmentId) {
            $result = array();
            
			$sql = DbHelper::prepare_sql("SELECT * FROM (
		
						SELECT a.anyo||' '||decode(a.mes,'1','Enero','2','Febrero','3','Marzo',
						      '4','Abril','5','Mayo','6','Junio','7','Julio','8','Agosto',
						      '9','Septiembre','10','Octubre','11','Noviembre',
						      '12','Diciembre') as mes_anio,
						      
						      /* dato del establecimiento */ 
						      decode(p.estab_dato,null,'*',trim(to_char(p.estab_dato,'9G999G999G990D99','NLS_NUMERIC_CHARACTERS = '',.'''))) as estab_dato,
						      
						      /* datos del municipio, isla y canarias */
						      trim(to_char(a.dato_municipio_turistico,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
						      trim(to_char(a.dato_isla,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
						      trim(to_char(a.dato_canarias,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato, 
							  t.id_municipio_turistico,
					
						      DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros',
						      '2','hoteleros','6','hoteleros') AS tipo,
						      
						      c.descripcion AS cabecera_grupo,
							  decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
						      
						FROM  tb_sas_alojamiento a,
						      tb_municipios m,
						      tb_establecimientos_unico eu,
						      tb_establecimientos_historico eh,
						      tb_cabeceras_pies_grupos_cat c,
							  tb_municipios_turisticos t,
						      
						      (SELECT h.anyo, h.mes, h.io_plazas as estab_dato       
						      FROM tb_estab_datos_publicacion h
						      /* @parametro id_establecimiento */
						      WHERE h.id_establecimiento = :estid) p 
						      
						WHERE /* @parametro id_establecimiento */
						      eh.id_establecimiento = :estid 
						
						      /* tipo de publicación según el tipo de establecimiento */
						      AND a.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
						
						      /* variable Indice censal de ocupación por plazas*/
						      AND a.id_variable = 4 
						
						      /* establecimiento no de baja */
						      AND eh.fecha_baja IS NULL 
						           
						      /* join historico con unico */      
						      AND eh.id_establecimiento = eu.id_establecimiento
						      
							  /* isla y municipio turistico */
							  AND t.id_municipio_turistico = a.id_municipio_turistico
							  AND t.id_isla = a.id_isla
							  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

							  /* isla y municipio del establecimiento */
							  AND m.id_municipio = eu.id_municipio
							  AND m.id_isla = eu.id_isla
							  AND m.id_municipio = t.id_municipio
							  AND m.id_isla = t.id_isla  
						            
						      /*  categoria del establecimiento */
						      AND a.id_categoria = eh.id_categoria
						      
						      /* cabecera y pie del grupo */
						      AND a.id_isla = c.id_isla
						      AND eh.id_categoria = c.id_categoria
						      AND a.id_tipo_publicacion = c.id_tipo_publicacion
							  AND a.id_municipio_turistico = c.id_municipio_turistico							  
						      
						      /* mes y año de publicación del dato */      
						      AND p.anyo(+) = a.anyo
						      AND p.mes(+) = a.mes
						       
						ORDER BY a.anyo desc, to_number(a.mes) desc)
						
					WHERE rownum < 26", array(":estid" => (string)$establishmentId));

			$db = new Istac_Sql();
            $db->query($sql);
            
            while ($db->next_record()) {                                                 
                $mes_anio      = $db->f('mes_anio');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                
                $result[] = array('mes_anio'  => $mes_anio,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato,
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);  
                                                 
            }
			
	        if (sizeof($result)>0)
	        {
	           	$ultimo_indice=sizeof($result)-1;
	           	$result[$ultimo_indice]['tipo']=$db->f('tipo');
	        }

            return $result;                     
        } 

//------------------------------------------------------------------------------------------------------------------------------                        
        
        function getDatosIndiceCensalOcupacionPorHabitaciones($establishmentId) {
            $result = array();
            
			
		    $sql = DbHelper::prepare_sql("SELECT * FROM (
		
						SELECT a.anyo||' '||decode(a.mes,'1','Enero','2','Febrero','3','Marzo',
						      '4','Abril','5','Mayo','6','Junio','7','Julio','8','Agosto',
						      '9','Septiembre','10','Octubre','11','Noviembre',
						      '12','Diciembre') as mes_anio,
						      
						      /* dato del establecimiento */ 
						      decode(p.estab_dato,null,'*',trim(to_char(p.estab_dato,'9G999G999G990D99','NLS_NUMERIC_CHARACTERS = '',.'''))) as estab_dato,
						      
						      /* datos del municipio, isla y canarias */
						      trim(to_char(a.dato_municipio_turistico,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
						      trim(to_char(a.dato_isla,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
						      trim(to_char(a.dato_canarias,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato, 
		    				  t.id_municipio_turistico,
						
						      DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros',
						      '2','hoteleros','6','hoteleros') AS tipo,
						      
						      c.descripcion AS cabecera_grupo,
							  decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
						      
						FROM  tb_sas_alojamiento a,
						      tb_municipios m,
						      tb_establecimientos_unico eu,
						      tb_establecimientos_historico eh,
						      tb_cabeceras_pies_grupos_cat c,
							  tb_municipios_turisticos t,
						      
						      (SELECT h.anyo, h.mes, h.io_habitaciones as estab_dato       
						      FROM tb_estab_datos_publicacion h
						      /* @parametro id_establecimiento */
						      WHERE h.id_establecimiento = :estid) p 
						      
						WHERE /* @parametro id_establecimiento */
						      eh.id_establecimiento = :estid 
						
						      /* tipo de publicación según el tipo de establecimiento */
						      AND a.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
						
						      /* variable Indice censal de ocupación por habitaciones*/
						      AND a.id_variable = 7 
						
						      /* establecimiento no de baja */
						      AND eh.fecha_baja IS NULL 
						           
						      /* join historico con unico */      
						      AND eh.id_establecimiento = eu.id_establecimiento
						      
							  /* isla y municipio turistico */
							  AND t.id_municipio_turistico = a.id_municipio_turistico
							  AND t.id_isla = a.id_isla
							  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

							  /* isla y municipio del establecimiento */
							  AND m.id_municipio = eu.id_municipio
							  AND m.id_isla = eu.id_isla
							  AND m.id_municipio = t.id_municipio
							  AND m.id_isla = t.id_isla  
						            
						      /*  categoria del establecimiento */
						      AND a.id_categoria = eh.id_categoria
						      
						      /* cabecera y pie del grupo */
						      AND a.id_isla = c.id_isla
						      AND eh.id_categoria = c.id_categoria
						      AND a.id_tipo_publicacion = c.id_tipo_publicacion 
							  AND a.id_municipio_turistico = c.id_municipio_turistico							  
						      
						      /* mes y año de publicación del dato */      
						      AND p.anyo(+) = a.anyo
						      AND p.mes(+) = a.mes
						       
						ORDER BY a.anyo desc, to_number(a.mes) desc)
						
					WHERE rownum < 26", array(":estid" => (string)$establishmentId));
     
		    $db = new Istac_Sql();
            $db->query($sql);
            
            while ($db->next_record()) {                                                 
                $mes_anio      = $db->f('mes_anio');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                
                $result[] = array('mes_anio'  => $mes_anio,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato,
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);  
                                                 
            }
			
	        if (sizeof($result)>0)
	        {
	           	$ultimo_indice=sizeof($result)-1;
	           	$result[$ultimo_indice]['tipo']=$db->f('tipo');
	        }

            return $result;                     
        } 

//------------------------------------------------------------------------------------------------------------------------------                        
        
        function getDatosEstanciaMedia($establishmentId) {
            $result = array();
            
			$sql = DbHelper::prepare_sql("SELECT * FROM (
		
						SELECT a.anyo||' '||decode(a.mes,'1','Enero','2','Febrero','3','Marzo',
						      '4','Abril','5','Mayo','6','Junio','7','Julio','8','Agosto',
						      '9','Septiembre','10','Octubre','11','Noviembre',
						      '12','Diciembre') as mes_anio,
						      
						      /* dato del establecimiento */ 
						      decode(p.estab_dato,null,'*',trim(to_char(p.estab_dato,'9G999G999G990D99'))) as estab_dato,
						      
						      /* datos del municipio, isla y canarias */
						      trim(to_char(a.dato_municipio_turistico,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
						      trim(to_char(a.dato_isla,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
						      trim(to_char(a.dato_canarias,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato,
					          t.id_municipio_turistico, 
						
						      DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros',
						      '2','hoteleros','6','hoteleros') AS tipo,
						      
						      c.descripcion AS cabecera_grupo,
							  decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
						      
						FROM  tb_sas_alojamiento a,
						      tb_municipios m,
						      tb_establecimientos_unico eu,
						      tb_establecimientos_historico eh,
						      tb_cabeceras_pies_grupos_cat c,
							  tb_municipios_turisticos t,
						      
						      (SELECT h.anyo, h.mes, h.em as estab_dato       
						      FROM tb_estab_datos_publicacion h
						      /* @parametro id_establecimiento */
						      WHERE h.id_establecimiento = :estid) p 
						      
						WHERE /* @parametro id_establecimiento */
						      eh.id_establecimiento = :estid 
						
						      /* tipo de publicación según el tipo de establecimiento */
						      AND a.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
						
						      /* variable Estancia media*/
						      AND a.id_variable = 9 
						
						      /* establecimiento no de baja */
						      AND eh.fecha_baja IS NULL 
						           
						      /* join historico con unico */      
						      AND eh.id_establecimiento = eu.id_establecimiento
						      
							  /* isla y municipio turistico */
							  AND t.id_municipio_turistico = a.id_municipio_turistico
							  AND t.id_isla = a.id_isla
							  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

							  /* isla y municipio del establecimiento */
							  AND m.id_municipio = eu.id_municipio
							  AND m.id_isla = eu.id_isla
							  AND m.id_municipio = t.id_municipio
							  AND m.id_isla = t.id_isla  
						            
						      /*  categoria del establecimiento */
						      AND a.id_categoria = eh.id_categoria
						      
						      /* cabecera y pie del grupo */
						      AND a.id_isla = c.id_isla
						      AND eh.id_categoria = c.id_categoria
						      AND a.id_tipo_publicacion = c.id_tipo_publicacion      
							  AND a.id_municipio_turistico = c.id_municipio_turistico
						      
						      /* mes y año de publicación del dato */      
						      AND p.anyo(+) = a.anyo
						      AND p.mes(+) = a.mes
						       
						ORDER BY a.anyo desc, to_number(a.mes) desc)
						
					WHERE rownum < 26", array(":estid" => (string)$establishmentId));
   
			$db = new Istac_Sql();
            $db->query($sql);
                                    
            while ($db->next_record()) {                                                 
                $mes_anio      = $db->f('mes_anio');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                                                
                $result[] = array('mes_anio'  => $mes_anio,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato, 
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);                         
            }
			
	        if (sizeof($result)>0)
		    {
		        $ultimo_indice=sizeof($result)-1;
		        $result[$ultimo_indice]['tipo']=$db->f('tipo');
		    }                        

                                    
            return $result;                             
        }

//------------------------------------------------------------------------------------------------------------------------------                        
        
        function getDatosEstanciaMediaPorLugarDeResidencia($establishmentId) {
            $result = array();
            
	          
		    $sql = DbHelper::prepare_sql("SELECT l.lugar_residencia AS lugar_residencia,
		
					       /* dato del establecimiento */
					       DECODE(p.estab_dato,NULL,'*',trim(to_char(p.estab_dato,'9G999G999G990D99'))) AS estab_dato,
					       
					       /* datos del municipio, isla y canarias */
					       trim(to_char(r.dato_municipio_turistico,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
					       trim(to_char(r.dato_isla,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
					       trim(to_char(r.dato_canarias,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato,           
					       t.id_municipio_turistico,
		    		
					       DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros',
					       '2','hoteleros','6','hoteleros') AS tipo,       
					       eu.id_isla, eh.id_categoria,
					       r.anyo,
					       DECODE(r.mes,'1','Enero','2','Febrero','3','Marzo','4','Abril',
					       '5','Mayo','6','Junio','7','Julio','8','Agosto','9','Septiembre',
					       '10','Octubre','11','Noviembre','12','Diciembre') AS mes,
					       
					       c.descripcion AS cabecera_grupo,
						   decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
					
					FROM tb_sas_alojamiento_lres r, 
					     tb_establecimientos_unico eu, 
					     tb_establecimientos_historico eh,
					     tb_municipios m, 
					     tb_lugar_residencia_pub_perso l,
					     tb_cabeceras_pies_grupos_cat c,
						 tb_municipios_turisticos t,
					     
					     /* mes y año de la última publicación según el tipo de publicación que corresponda */
					     (SELECT MAX(TO_NUMBER(a.anyo||LPAD(a.mes,2,'0'))) AS ultimo
					      FROM tb_establecimientos_historico eh, tb_sas_alojamiento_lres a
					      WHERE eh.id_establecimiento = :estid
					      AND eh.fecha_baja IS NULL
					      AND a.id_tipo_publicacion = DECODE(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)),
					   
					     (SELECT a.em AS estab_dato, a.id_lugar_residencia as estab_lugar, 
					      a.mes as estab_mes, a.anyo as estab_anyo
					      FROM tb_estab_datos_publicacionlres a      
					      WHERE a.id_establecimiento = :estid) p /* @parametro id_establecimiento */
					                            
					WHERE /* @parametro id_establecimiento */
					      eu.id_establecimiento = :estid 
					      
					      /* variable Estancia media*/
					      AND r.id_variable = 9
					       
					      /* datos del establecimiento actuales */
					      AND eh.fecha_baja is null
					      
					      /* tipo de publicacion según el tipo de establecimiento*/       
					      AND r.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
					            
						  /* isla y municipio turistico */
						  AND t.id_municipio_turistico = r.id_municipio_turistico
						  AND t.id_isla = r.id_isla
						  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

						  /* isla y municipio del establecimiento */
						  AND m.id_municipio = eu.id_municipio
						  AND m.id_isla = eu.id_isla
						  AND m.id_municipio = t.id_municipio
						  AND m.id_isla = t.id_isla  
					      
					      /* join de histórico con unico */
					      AND eh.id_establecimiento = eu.id_establecimiento 
					      
					      /* categoría del establecimiento */
					      AND r.id_categoria = eh.id_categoria 
					      
					      /* último año/mes disponible */
					      AND r.anyo||LPAD(r.mes,2,'0') = ultimo 
					      
					      /* para obtener el nombre del lugar de residencia */
					      AND r.id_lugar_residencia = l.id_lugar_residencia 
					      
					      /* lugar de residencia */
					      AND p.estab_lugar(+) = r.id_lugar_residencia
					      
					      /* cabecera y pie del grupo */
					      AND r.id_isla = c.id_isla
					      AND eh.id_categoria = c.id_categoria
					      AND r.id_tipo_publicacion = c.id_tipo_publicacion          
						  AND r.id_municipio_turistico = c.id_municipio_turistico
					      
					      /* mes y año de la publicación del dato */
					      AND p.estab_mes(+) = r.mes
					      AND p.estab_anyo(+) = r.anyo 
					      
					ORDER BY l.orden", array(":estid" => (string)$establishmentId));
 
		    $db = new Istac_Sql();
            $db->query($sql);
            
            while ($db->next_record()) {                                                 
                $lugar_residencia          = $db->f('lugar_residencia');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                         
                $result[] = array('lugar_residencia'  => $lugar_residencia,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato, 
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);       
            }
            if (sizeof($result)>0)
            { 
            	$ultimo_indice=sizeof($result)-1;
            	$result[$ultimo_indice]['mes']=$db->f('mes');
            	$result[$ultimo_indice]['anyo']=$db->f('anyo');
            	$result[$ultimo_indice]['tipo']=$db->f('tipo');
            	$result[$ultimo_indice]['id_isla']=$db->f('id_isla');
            	$result[$ultimo_indice]['id_categoria']=$db->f('id_categoria');
            }
            return $result;                            
        }
        
//------------------------------------------------------------------------------------------------------------------------------                        
        
        function getDatosViajerosAlojadosPorPais($establishmentId) {
            $result = array(); 
                         
		    $sql = DbHelper::prepare_sql("SELECT l.lugar_residencia AS lugar_residencia,
					       DECODE(p.estab_dato,NULL,'*',trim(to_char(p.estab_dato,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.'''))) AS estab_dato,
						   trim(to_char(r.dato_municipio_turistico,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
						   trim(to_char(r.dato_isla,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
						   trim(to_char(r.dato_canarias,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato, 
		    			   t.id_municipio_turistico,			   
					       DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros','2','hoteleros','6','hoteleros') AS tipo,
					       eu.id_isla,
					       eh.id_categoria,
					       r.anyo,
					       DECODE(r.mes,'1','Enero','2','Febrero','3','Marzo','4','Abril',
					       '5','Mayo','6','Junio','7','Julio','8','Agosto','9','Septiembre',
					       '10','Octubre','11','Noviembre','12','Diciembre') AS mes,  
					       
					       c.descripcion AS cabecera_grupo,
						   decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
					
					FROM tb_sas_alojamiento_lres r, 
					     tb_establecimientos_unico eu, 
					     tb_establecimientos_historico eh,
					     tb_municipios m, 
					     tb_lugar_residencia_pub_perso l,
					     tb_cabeceras_pies_grupos_cat c,
						 tb_municipios_turisticos t,
					     
					     /* mes y año de la última publicación según el tipo de publicación que corresponda */
					     (SELECT MAX(TO_NUMBER(a.anyo||LPAD(a.mes,2,'0'))) AS ultimo
					      FROM tb_establecimientos_historico eh, tb_sas_alojamiento_lres a
					      WHERE eh.id_establecimiento = :estid
					      AND eh.fecha_baja IS NULL
					      AND a.id_tipo_publicacion = DECODE(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)),
					   
					     (SELECT a.alojados AS estab_dato, a.id_lugar_residencia as estab_lugar, 
					      a.mes as estab_mes, a.anyo as estab_anyo
					      FROM tb_estab_datos_publicacionlres a      
					      WHERE a.id_establecimiento = :estid) p /* @parametro id_establecimiento */
					                            
					WHERE /* @parametro id_establecimiento */
					      eu.id_establecimiento = :estid 
					      
					      /* variable Alojados*/
					      AND r.id_variable = 6
					       
					      /* datos del establecimiento actuales */
					      AND eh.fecha_baja is null
					      
					      /* tipo de publicacion según el tipo de establecimiento*/       
					      AND r.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
					            
						  /* isla y municipio turistico */
						  AND t.id_municipio_turistico = r.id_municipio_turistico
						  AND t.id_isla = r.id_isla
						  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

						  /* isla y municipio del establecimiento */
						  AND m.id_municipio = eu.id_municipio
						  AND m.id_isla = eu.id_isla
						  AND m.id_municipio = t.id_municipio
						  AND m.id_isla = t.id_isla  
					      
					      /* join de histórico con unico */
					      AND eh.id_establecimiento = eu.id_establecimiento 
					      
					      /* categoría del establecimiento */
					      AND r.id_categoria = eh.id_categoria 
					      
					      /* último año/mes disponible */
					      AND r.anyo||LPAD(r.mes,2,'0') = ultimo 
					      
					      /* para obtener el nombre del lugar de residencia */
					      AND r.id_lugar_residencia = l.id_lugar_residencia 
					      
					      /* lugar de residencia */
					      AND p.estab_lugar(+) = r.id_lugar_residencia
					      
					      /* cabecera y pie del grupo */
					      AND r.id_isla = c.id_isla
					      AND eh.id_categoria = c.id_categoria
					      AND r.id_tipo_publicacion = c.id_tipo_publicacion
						  AND r.id_municipio_turistico = c.id_municipio_turistico
					      
					      /* mes y año de la publicación del dato */
					      AND p.estab_mes(+) = r.mes
					      AND p.estab_anyo(+) = r.anyo 
					      
					ORDER BY l.orden", array(":estid" => (string)$establishmentId));

		    $db = new Istac_Sql();
            $db->query($sql);
            
            while ($db->next_record()) {                                                 
                $lugar_residencia          = $db->f('lugar_residencia');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                         
                $result[] = array('lugar_residencia'  => $lugar_residencia,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato, 
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);       
            }
            if (sizeof($result)>0)
            { 
            	$ultimo_indice=sizeof($result)-1;
            	$result[$ultimo_indice]['mes']=$db->f('mes');
            	$result[$ultimo_indice]['anyo']=$db->f('anyo');
            	$result[$ultimo_indice]['tipo']=$db->f('tipo');
            	$result[$ultimo_indice]['id_isla']=$db->f('id_isla');
            	$result[$ultimo_indice]['id_categoria']=$db->f('id_categoria');
            }
            return $result;
        }
        
//------------------------------------------------------------------------------------------------------------------------------                        

        function getDatosPernoctacionesPorPais($establishmentId) {
            $result = array(); 
                       
			$sql = DbHelper::prepare_sql("SELECT l.lugar_residencia AS lugar_residencia,
					       DECODE(p.estab_dato,NULL,'*',trim(to_char(p.estab_dato,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.'''))) AS estab_dato,
						   trim(to_char(r.dato_municipio_turistico,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
						   trim(to_char(r.dato_isla,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
						   trim(to_char(r.dato_canarias,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato, 
						   t.id_municipio_turistico,
					       DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros','2','hoteleros','6','hoteleros') AS tipo,
					       eu.id_isla,
					       eh.id_categoria,
					       r.anyo,
					       DECODE(r.mes,'1','Enero','2','Febrero','3','Marzo','4','Abril',
					       '5','Mayo','6','Junio','7','Julio','8','Agosto','9','Septiembre',
					       '10','Octubre','11','Noviembre','12','Diciembre') AS mes,
					       
					       c.descripcion AS cabecera_grupo,
						   decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
					
					FROM tb_sas_alojamiento_lres r, 
					     tb_establecimientos_unico eu, 
					     tb_establecimientos_historico eh,
					     tb_municipios m, 
					     tb_lugar_residencia_pub_perso l,
					     tb_cabeceras_pies_grupos_cat c,
						 tb_municipios_turisticos t,
					     
					     /* mes y año de la última publicación según el tipo de publicación que corresponda */
					     (SELECT MAX(TO_NUMBER(a.anyo||LPAD(a.mes,2,'0'))) AS ultimo
					      FROM tb_establecimientos_historico eh, tb_sas_alojamiento_lres a
					      WHERE eh.id_establecimiento = :estid
					      AND eh.fecha_baja IS NULL
					      AND a.id_tipo_publicacion = DECODE(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)),
					   
					     (SELECT a.pernoctaciones AS estab_dato, a.id_lugar_residencia as estab_lugar, 
					      a.mes as estab_mes, a.anyo as estab_anyo
					      FROM tb_estab_datos_publicacionlres a      
					      WHERE a.id_establecimiento = :estid) p /* @parametro id_establecimiento */
					                            
					WHERE /* @parametro id_establecimiento */
					      eu.id_establecimiento = :estid 
					      
					      /* variable Pernoctaciones*/
					      AND r.id_variable = 2
					       
					      /* datos del establecimiento actuales */
					      AND eh.fecha_baja is null
					      
					      /* tipo de publicacion según el tipo de establecimiento*/       
					      AND r.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
					            
						  /* isla y municipio turistico */
						  AND t.id_municipio_turistico = r.id_municipio_turistico
						  AND t.id_isla = r.id_isla
						  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

						  /* isla y municipio del establecimiento */
						  AND m.id_municipio = eu.id_municipio
						  AND m.id_isla = eu.id_isla
						  AND m.id_municipio = t.id_municipio
						  AND m.id_isla = t.id_isla  
					      
					      /* join de histórico con unico */
					      AND eh.id_establecimiento = eu.id_establecimiento 
					      
					      /* categoría del establecimiento */
					      AND r.id_categoria = eh.id_categoria 
					      
					      /* último año/mes disponible */
					      AND r.anyo||LPAD(r.mes,2,'0') = ultimo 
					      
					      /* para obtener el nombre del lugar de residencia */
					      AND r.id_lugar_residencia = l.id_lugar_residencia 
					      
					      /* lugar de residencia */
					      AND p.estab_lugar(+) = r.id_lugar_residencia
					      
					      /* cabecera y pie del grupo */
					      AND r.id_isla = c.id_isla
					      AND eh.id_categoria = c.id_categoria
					      AND r.id_tipo_publicacion = c.id_tipo_publicacion      
						  AND r.id_municipio_turistico = c.id_municipio_turistico						  
					      
					      /* mes y año de la publicación del dato */
					      AND p.estab_mes(+) = r.mes
					      AND p.estab_anyo(+) = r.anyo 
					      
					ORDER BY l.orden", array(":estid" => (string)$establishmentId));

			$db = new Istac_Sql();
            $db->query($sql);
            
            while ($db->next_record()) {                                                 
                $lugar_residencia          = $db->f('lugar_residencia');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                         
                $result[] = array('lugar_residencia'  => $lugar_residencia,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato, 
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'cabecera_grupo' => $cabecera_grupo,
								  'pie_grupo' => $pie_grupo);       
            }
            
            if (sizeof($result)>0)
            { 
            	$ultimo_indice=sizeof($result)-1;
            	$result[$ultimo_indice]['mes']=$db->f('mes');
            	$result[$ultimo_indice]['anyo']=$db->f('anyo');
            	$result[$ultimo_indice]['tipo']=$db->f('tipo');
            	$result[$ultimo_indice]['id_isla']=$db->f('id_isla');
            	$result[$ultimo_indice]['id_categoria']=$db->f('id_categoria');
            }
            return $result;        
        }          
    
//------------------------------------------------------------------------------------------------------------------------------                        

        function getDatosViajerosEntrados($establishmentId) {
            $result = array(); 
                       
		    $sql = DbHelper::prepare_sql("SELECT * FROM (

						SELECT a.anyo||' '||decode(a.mes,'1','Enero','2','Febrero','3','Marzo',
						       '4','Abril','5','Mayo','6','Junio','7','Julio','8','Agosto',
						       '9','Septiembre','10','Octubre','11','Noviembre',
						       '12','Diciembre') as mes_anio, 
						       
						       decode(p.estab_dato,null,'*',trim(to_char(p.estab_dato,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.'''))) as estab_dato,       
						       trim(to_char(a.dato_municipio_turistico,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
						       trim(to_char(a.dato_isla,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
							   trim(to_char(a.dato_canarias,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato, 
		    				   t.id_municipio_turistico,						
						       DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros',
						       '2','hoteleros','6','hoteleros') AS tipo,
						       
						       c.descripcion AS cabecera_grupo,
							   decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
						       
						FROM tb_sas_alojamiento a,
						     tb_municipios m,
						     tb_establecimientos_unico eu, 
						     tb_establecimientos_historico eh,
						     tb_cabeceras_pies_grupos_cat c,
							 tb_municipios_turisticos t,
						
						     (SELECT h.anyo, h.mes, h.entrados as estab_dato      
						     FROM tb_estab_datos_publicacion h
						     /* @parametro id_establecimiento */
						     WHERE h.id_establecimiento = :estid) p 
						
						      /* @parametro id_establecimiento */
						WHERE eh.id_establecimiento = :estid 
						
						      /* join */
						      AND eh.id_establecimiento = eu.id_establecimiento
						
						      /* tipo de establecimiento */      
						      AND a.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
						            
						      /* variable Viajeros entrados */
						      AND a.id_variable = 1 
						      
						      /* establecimiento no de baja */
						      AND eh.fecha_baja IS NULL      
													  
							  /* isla y municipio turistico */
							  AND t.id_municipio_turistico = a.id_municipio_turistico
							  AND t.id_isla = a.id_isla
							  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

							  /* isla y municipio del establecimiento */
							  AND m.id_municipio = eu.id_municipio
							  AND m.id_isla = eu.id_isla
							  AND m.id_municipio = t.id_municipio
							  AND m.id_isla = t.id_isla  
						      
						      /*  categoria del establecimiento */
						      AND a.id_categoria = eh.id_categoria
						      
						      /* cabecera y pie del grupo */
						      AND a.id_isla = c.id_isla
						      AND eh.id_categoria = c.id_categoria
						      AND a.id_tipo_publicacion = c.id_tipo_publicacion
							  AND a.id_municipio_turistico = c.id_municipio_turistico
						            
						      /* mes y año de publicación del dato */
						      AND p.anyo(+) = a.anyo
						      AND p.mes(+) = a.mes            
						      
						ORDER BY a.anyo desc, to_number(a.mes) desc)
						
					WHERE rownum < 26", array(":estid" => (string)$establishmentId));

		    $db = new Istac_Sql();
            $db->query($sql);
                                                
            while ($db->next_record()) {                                                 
                $mes_anio      = $db->f('mes_anio');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                
                $result[] = array('mes_anio'  => $mes_anio,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato, 
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);               
            }

            if (sizeof($result)>0)
            {
            	$ultimo_indice=sizeof($result)-1;
            	$result[$ultimo_indice]['tipo']=$db->f('tipo');
            }

            
            return $result;
        }        
        
//------------------------------------------------------------------------------------------------------------------------------                        

        function getDatosViajerosEntradosPorLugarDeResidencia($establishmentId) {
            $result = array(); 
                       
			$sql = DbHelper::prepare_sql("SELECT l.lugar_residencia AS lugar_residencia,
						DECODE(p.estab_dato,NULL,'*',trim(to_char(p.estab_dato,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.'''))) AS estab_dato,
						trim(to_char(r.dato_municipio_turistico,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
					    trim(to_char(r.dato_isla,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
					    trim(to_char(r.dato_canarias,'9G999G999G990','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato,
						t.id_municipio_turistico, 
					    DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros','2','hoteleros','6','hoteleros') AS tipo,
						eu.id_isla,
						eh.id_categoria,
						r.anyo,
						DECODE(r.mes,'1','Enero','2','Febrero','3','Marzo','4','Abril',
						'5','Mayo','6','Junio','7','Julio','8','Agosto','9','Septiembre',
						'10','Octubre','11','Noviembre','12','Diciembre') AS mes,
				       
						c.descripcion AS cabecera_grupo,
						decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
				
					FROM tb_sas_alojamiento_lres r, 
					     tb_establecimientos_unico eu, 
					     tb_establecimientos_historico eh,
					     tb_municipios m, 
					     tb_lugar_residencia_pub_perso l,
					     tb_cabeceras_pies_grupos_cat c,
						 tb_municipios_turisticos t,
					     
					     /* mes y año de la última publicación según el tipo de publicación que corresponda */
					     (SELECT MAX(TO_NUMBER(a.anyo||LPAD(a.mes,2,'0'))) AS ultimo
					      FROM tb_establecimientos_historico eh, tb_sas_alojamiento_lres a
					      WHERE eh.id_establecimiento = :estid
					      AND eh.fecha_baja IS NULL
					      AND a.id_tipo_publicacion = DECODE(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)),
					   
					     (SELECT a.entrados AS estab_dato, a.id_lugar_residencia as estab_lugar, 
					      a.mes as estab_mes, a.anyo as estab_anyo
					      FROM tb_estab_datos_publicacionlres a      
					      WHERE a.id_establecimiento = :estid) p /* @parametro id_establecimiento */
					                            
					WHERE /* @parametro id_establecimiento */
					      eu.id_establecimiento = :estid 
					      
					      /* variable Entrados */
					      AND r.id_variable = 1
					       
					      /* datos del establecimiento actuales */
					      AND eh.fecha_baja is null
					      
					      /* tipo de publicacion según el tipo de establecimiento*/       
					      AND r.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
					            
						  /* isla y municipio turistico */
						  AND t.id_municipio_turistico = r.id_municipio_turistico
						  AND t.id_isla = r.id_isla
						  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

						  /* isla y municipio del establecimiento */
						  AND m.id_municipio = eu.id_municipio
						  AND m.id_isla = eu.id_isla
						  AND m.id_municipio = t.id_municipio
						  AND m.id_isla = t.id_isla  
					      
					      /* join de histórico con unico */
					      AND eh.id_establecimiento = eu.id_establecimiento 
					      
					      /* categoría del establecimiento */
					      AND r.id_categoria = eh.id_categoria 
					      
					      /* último año/mes disponible */
					      AND r.anyo||LPAD(r.mes,2,'0') = ultimo 
					      
					      /* para obtener el nombre del lugar de residencia */
					      AND r.id_lugar_residencia = l.id_lugar_residencia 
					      
					      /* lugar de residencia */
					      AND p.estab_lugar(+) = r.id_lugar_residencia
					      
					      /* cabecera y pie del grupo */
					      AND r.id_isla = c.id_isla
					      AND eh.id_categoria = c.id_categoria
					      AND r.id_tipo_publicacion = c.id_tipo_publicacion
						  AND r.id_municipio_turistico = c.id_municipio_turistico					
					      
					      /* mes y año de la publicación del dato */
					      AND p.estab_mes(+) = r.mes
					      AND p.estab_anyo(+) = r.anyo 
					      
					ORDER BY l.orden", array(":estid" => (string)$establishmentId));

			$db = new Istac_Sql();
            $db->query($sql);
            
            while ($db->next_record()) {                                                 
                $lugar_residencia          = $db->f('lugar_residencia');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                         
                $result[] = array('lugar_residencia'  => $lugar_residencia,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato, 
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);       
            }
            if (sizeof($result)>0)
            { 
            	$ultimo_indice=sizeof($result)-1;
            	$result[$ultimo_indice]['mes']=$db->f('mes');
            	$result[$ultimo_indice]['anyo']=$db->f('anyo');
            	$result[$ultimo_indice]['tipo']=$db->f('tipo');
            	$result[$ultimo_indice]['id_isla']=$db->f('id_isla');
            	$result[$ultimo_indice]['id_categoria']=$db->f('id_categoria');
            }
            return $result;
        }   

//------------------------------------------------------------------------------------------------------------------------------                        

        function getDatosTarifaMediaPorHabitacionMensual($establishmentId) {
            $result = array(); 
                       
			$sql = DbHelper::prepare_sql("SELECT * FROM (
		
						SELECT a.anyo||' '||decode(a.mes,'1','Enero','2','Febrero','3','Marzo',
						      '4','Abril','5','Mayo','6','Junio','7','Julio','8','Agosto',
						      '9','Septiembre','10','Octubre','11','Noviembre',
						      '12','Diciembre') as mes_anio,
						      
						      /* dato del establecimiento */ 
						      decode(p.estab_dato,null,'*',trim(to_char(p.estab_dato,'9G999G999G990D99','NLS_NUMERIC_CHARACTERS = '',.'''))) as estab_dato,
						      
						      /* datos del municipio, isla y canarias */
						      trim(to_char(a.dato_municipio_turistico,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as municipio_dato, 
						      trim(to_char(a.dato_isla,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as isla_dato, 
						      trim(to_char(a.dato_canarias,'9G990D99','NLS_NUMERIC_CHARACTERS = '',.''')) as canarias_dato, 
							  t.id_municipio_turistico,
					
						      DECODE(eh.id_tipo_establecimiento,'3','extrahoteleros','1','hoteleros',
						      '2','hoteleros','6','hoteleros') AS tipo,
						      
						      c.descripcion AS cabecera_grupo,
							  decode(c.descripcion_pie,null,c.descripcion_pie,'** '||c.descripcion_pie) AS pie_grupo
						      
						FROM  tb_sas_alojamiento a,
						      tb_municipios m,
						      tb_establecimientos_unico eu,
						      tb_establecimientos_historico eh,
						      tb_cabeceras_pies_grupos_cat c,
							  tb_municipios_turisticos t,
						      
						      (SELECT h.anyo, h.mes, h.adr as estab_dato       
						      FROM tb_estab_datos_publicacion h
						      /* @parametro id_establecimiento */
						      WHERE h.id_establecimiento = :estid) p 
						      
						WHERE /* @parametro id_establecimiento */
						      eh.id_establecimiento = :estid 
						
						      /* tipo de publicación según el tipo de establecimiento */
						      AND a.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)
						
						      /* variable Tarifa media por habitación mensual */
						      AND a.id_variable = 5 
						
						      /* establecimiento no de baja */
						      AND eh.fecha_baja IS NULL 
						           
						      /* join historico con unico */      
						      AND eh.id_establecimiento = eu.id_establecimiento
						      
							  /* isla y municipio turistico */
							  AND t.id_municipio_turistico = a.id_municipio_turistico
							  AND t.id_isla = a.id_isla
							  AND t.id_tipo_publicacion = decode(eh.id_tipo_establecimiento,'3',2,'4',3,'5',3,1)   

							  /* isla y municipio del establecimiento */
							  AND m.id_municipio = eu.id_municipio
							  AND m.id_isla = eu.id_isla
							  AND m.id_municipio = t.id_municipio
							  AND m.id_isla = t.id_isla  
						            
						      /*  categoria del establecimiento */
						      AND a.id_categoria = eh.id_categoria
						      
						      /* cabecera y pie del grupo */
						      AND a.id_isla = c.id_isla
						      AND eh.id_categoria = c.id_categoria
						      AND a.id_tipo_publicacion = c.id_tipo_publicacion
							  AND a.id_municipio_turistico = c.id_municipio_turistico							  
						      
						      /* mes y año de publicación del dato */      
						      AND p.anyo(+) = a.anyo
						      AND p.mes(+) = a.mes
						       
						ORDER BY a.anyo desc, to_number(a.mes) desc)
						
					WHERE rownum < 26", array(":estid" => (string)$establishmentId)); 

			$db = new Istac_Sql();
            $db->query($sql);
                                                
            while ($db->next_record()) {                                                 
                $mes_anio      = $db->f('mes_anio');
                $estab_dato    = $db->f('estab_dato');
                $municipio_dato     = $db->f('municipio_dato');
                $isla_dato     = $db->f('isla_dato');
                $canarias_dato = $db->f('canarias_dato');
                $id_municipio_turistico = $db->f('id_municipio_turistico');
                $cabecera_grupo = $db->f('cabecera_grupo');
				$pie_grupo = $db->f('pie_grupo');
                
                $result[] = array('mes_anio'  => $mes_anio,  'estab_dato' => $estab_dato, 'municipio_dato' => $municipio_dato, 
                				  'isla_dato' => $isla_dato, 'canarias_dato' => $canarias_dato,'id_municipio_turistico' => $id_municipio_turistico,
                				  'cabecera_grupo' => $cabecera_grupo, 'pie_grupo' => $pie_grupo);             
            }

            if (sizeof($result)>0)
            {
            	$ultimo_indice=sizeof($result)-1;
            	$result[$ultimo_indice]['tipo']=$db->f('tipo');
            }

            
            return $result;
        }        
    }            

?>
