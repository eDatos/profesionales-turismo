<?php
require_once (__DIR__.'/../EstablishmentUser.class.php');
require_once (__DIR__.'/../Establecimiento.class.php');
require_once (__DIR__.'/AlojaUTMovimientos.class.php');
require_once (__DIR__.'/UnidadTerritorial.class.php');
require_once (__DIR__.'/AlojaHabitaciones.class.php');
require_once (__DIR__.'/AlojaPersonal.class.php');
require_once (__DIR__.'/AlojaPrecios.class.php');
require_once (__DIR__.'/AlojaErrorCollection.class.php');

/** Modos de introduccion */
define("MODO_INTRO_ES", "1");
define("MODO_INTRO_EP", "0");

/** Modos de cumplimentado */
define("MODO_CUMPL_HOR", "H");
define("MODO_CUMPL_VER", "V");

/** Modos de porcentaje en ADR */
define("MODO_PORC_PORC", "1");
define("MODO_PORC_NUM", "0");

@define("TIPO_CARGA_XML", "1");
@define("TIPO_CARGA_WEB", "2");
@define("TIPO_CARGA_FAX", "3");

class AlojaExcesoPlazasInfo
{
	/*
	 * Día del mes (de la encuesta) que presenta un exceso de plazas.
	 */
	var $dia;
	
	/*
	 * Número total de pernoctaciones del día y que supera la capacidad declarada del establecimiento.
	 */
	var $pernoctaciones;
}

class AlojaExcesoHabitacionesInfo
{
	/*
	 * Día del mes (de la encuesta) que presenta un exceso de habitaciones ocupadas.
	 */
	var $dia;

	/*
	 * Número total de habitaciones ocupadas del día y que supera la capacidad declarada del establecimiento.
	 */
	var $habOcupadas;
}

/*
 * Almacena la información sobre los avisos de exceso de ocupación (plazas) ó capacidad (habitaciones) presentes en el cuestionario.
 * Se carga con cada validación del cuestionario (llamada al método "valida_internas").
 */
class AlojaExcesoInfo
{
	// Regla 4: El número de pernoctaciones totales del mes supera el límite teórico del establecimiento.
	// Regla 112: El número de pernoctaciones del día supera el límite teórico del establecimiento.
	// Regla 113: El número de habitaciones ocupadas supera la capacidad declarada del establecimiento.
	
	/*
	 * Booelano: Indica si existe o no exceso de plazas(ocupación) en el cuestionario. True = existe exceso.
	 */
	var $hayExcesoPlazas;
	
	/*
	 * Booelano: Indica si existe o no exceso de habitaciones(capacidad) en el cuestionario. True = existe exceso.
	 */
	var $hayExcesoHabitaciones;
	
	/*
	 * Booelano: Indica si existe o no exceso de plazas(ocupación) totales del mes en el cuestionario. True = existe exceso.
	 */
	var $hayExcesoPlazasMes;
	
	public function __construct()
	{
    		$this->hayExcesoPlazas=false;
    		$this->hayExcesoHabitaciones=false;
    		$this->hayExcesoPlazasMes=false;
	}
}

/**
 * Contiene los datos relacionados con un cuestionario de alojamiento.
 */
class AlojaCuestionario
{

	var $id;
	var $codigo_registro;
    
    var $userid_declarante;
	var $estabid_declarado;
	var $mes;
	var $ano;
    
	/**
	 * Fecha en la que el usuario envia el cuestionario (cierra el cuestionario).
	 */
	var $fecha_cierre;
	/**
	 * Fecha de recepción del cuestionario.
	 */
	var $fecha_recepcion;
	/**
	 * Numero total de dias naturales abiertos durante el mes de la encuesta.
	 */
	var $dias_abierto;
	
	var $tipo_carga;
	var $modo_introduccion;
	var $modo_cumplimentado;
	var $modo_porcentaje;
	
    var $validacion;
	
	/**
	 * Tabla de movimientos diarios por unidad territorial
	 * @var Diccionario (id_ut => AlojaUTMovimientos);
	 */
	var $mov_por_ut;
	var $totales;
	/**
	 * Tabla de habitaciones ocupadas por día
	 * @var Diccionario (dia => AlojaHabitaciones).
	 */
	var $habitaciones;
	/**
	 * Una instancia de la clase AlojaPersonal
	 * @var unknown_type
	 */
	var $personal;
	/**
	 * Una instancia de la clase AlojaPrecios
	 * @var unknown_type
	 */
	var $precios;
	
	/**
	 * Indicadores de presencia de datos
	 */
	var $tiene_datos_movimientos;
	var $tiene_datos_habitaciones;
	var $tiene_datos_personal;
	var $tiene_datos_precios;
	
	/** Contenedor de errores de validacion */
	var $val_errors;
	
	/** Avisos de excesos */
	var $avisos_excesos;
	
	var $tipo_cliente_cadena = array(
		'TOUROPERADOR_TRADICIONAL' => 'Turoperador tradicional',
		'EMPRESAS' => 'Empresas',
		'AGENCIA_DE_VIAJE_TRADICIONAL' => 'Agencia de viaje tradicional',
		'PARTICULARES' => 'Particulares',
		'GRUPOS' => 'Grupos',
		'INTERNET' => 'Contratación directa del hotel online',
		'AGENCIA_DE_VIAJE_ONLINE' => 'Agencias de viaje on-line',
		'TOUROPERADOR_ONLINE' => 'Turoperador on-line',
		'OTROS' => 'Otros'
	);
	
	/*	NOTA: Estos campos no están presentes en la tabla TB_ALOJA_CUESTIONARIOS_TEMP. */
	/* Código del motivo de exceso de plazas o null si no hay exceso. */
	var $excesoPlazas;
	/* Detalle del motivo de exceso de plazas o null si no hay exceso. */
	var $excesoPlazasDetalle;
	
	/* Código del motivo de exceso de habitaciones o null si no hay exceso. */
	var $excesoHabitaciones;
	/* Detalle del motivo de exceso de plazas o null si no hay exceso. */
	var $excesoHabitacionesDetalle;
	
	var $excesoInfoObj;
	
	/* Cuando este valor está definido y es true, se ha cargado del cuestionario con datos parciales para ser guardado sin cerrar. */
	var $cuestionario_parcial;
	
	/* Cuando el cuestionario recibido haya sido marcado como cese de actividad */
	var $ceseActividad;
	
	/**
	 * Inicialza el objeto cuestionario con los valores validos de inicio.
	 * @param estab
	 * @param mes
	 * @param ano
	 */
	public function __construct($mes, $ano, $estab_id = null, $userid_declarante = null)
	{
        $this->userid_declarante = $userid_declarante;
        $this->estabid_declarado = $estab_id;
        $this->mes = $mes;
        $this->ano = $ano;
        
        /// Dias abiertos
        //$this->dias_abierto = cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->ano);
        
        $this->val_errors = new AlojaErrorCollection();
        
        $tiene_datos_movimientos=false;
        $tiene_datos_habitaciones=false;
        $tiene_datos_personal=false;
        $tiene_datos_precios=false;
        
        $excesoPlazas=null;
        $excesoPlazasDetalle=null;
        $excesoHabitaciones=null;
        $excesoHabitacionesDetalle=null;
	}
	
	public function es_cuestionario_parcial()
	{
		return ((isset($this->cuestionario_parcial)) && ($this->cuestionario_parcial==true));
	}
	
	public function es_cese_actividad()
	{
		return ((isset($this->ceseActividad)) && ($this->ceseActividad==true));
	}
        
	public function es_nuevo()
	{
		return $this->id == null;	
	}
	
    public function esta_cerrado()
    {
        return ($this->fecha_cierre != null);
    }
    
    
    public function tiene_errores()
    {
    	return $this->val_errors->hay_error();
    }

    public function contiene_errores()
    {
    	return (!$this->val_errors->hay_solo_avisos());
    }
    
    
    /**
     * Calcula los totales de movimientos, sumando todas las cantidades por unidad territorial.
     */
	public function calcular_totales()
    {
    	$this->totales = new AlojaUTMovimientos();
    	$this->totales->presentes_comienzo_mes = 0;
    	foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		$this->totales->presentes_comienzo_mes += $movs->presentes_comienzo_mes;
    		foreach($movs->movimientos as $dia => $esp)
    		{
    			if (!isset($this->totales->movimientos[$dia]))
    			{
    				$this->totales->movimientos[$dia] = new AlojaESP();
    			}
    			$this->totales->movimientos[$dia]->sumar($esp);
    		}
    	}
    }
    
    
    /**
     * Comprueba si el cuestionario tiene datos de habitacion y precios
     * @return boolean
     */
    public function hay_datos_mov_hab_precios()
    {
    	$hay_mov = (isset($this->mov_por_ut) && isset($this->totales) && count($this->mov_por_ut) != 0 && count($this->totales->movimientos)!=0);
    	$hay_hab = isset($this->habitaciones) && count($this->habitaciones) > 0;
    	$hay_precios = $this->hay_datos_precios(); 
    	
    	return $hay_mov && $hay_hab && $hay_precios;
    }
            
    /************** REGLAS DE VALIDACION ********************************/
    
    /**
     * Calcula el revpar mensual a partir del adr mensual.
     * @param unknown_type $num_habitaciones
     */
    private function calculo_revpar_mensual_desde_adr_mensual($num_habitaciones)
    {
    	$total_hab = 0;
    	 
   		// El total de habitaciones ocupadas se saca de las habitaciones.
   		//suma diaria de habitaciones ocupadas
   		//Se evita que dé error si no hay desglose de número de habitaciones. Se asume número total de habitaciones = 0.
   		if(isset($this->habitaciones))
   		{
    		foreach($this->habitaciones as $dia => $hab)
    		{
    			$total_hab += ($hab->uso_individual + $hab->uso_doble + $hab->otras);
    		}
   		}
		//Modificado para que no falle el cálculo del Revpar cuando son datos ficticios y no salía 0.01 sino menor y además daba error en las validaciones.
		if ( $this->precios->adr_mensual == 0.01)
		{
			$revpar_mensual_calculado = 0.01;
		}
		else
		{
			$revpar_mensual_calculado = ($this->precios->adr_mensual * $total_hab) / ($num_habitaciones * $this->dias_abierto);
		}
    
    	return $revpar_mensual_calculado;
    }
    
    /**
     * Calcula el RevPar a partir del desglose de precios.
     * @param unknown_type $num_habitaciones
     * @return number
     */
    private function calculo_revpar_desde_desglose($num_habitaciones)
    {
    	$total_hab = 0;
    	 
    	if ($this->modo_porcentaje == MODO_PORC_NUM)
    	{
    		foreach($this->precios->num as $tc => $num_hab)
    			$total_hab += $num_hab;
    	}
    	else
    	{
    		// Si el modo es porcentaje el total de habitaciones ocupadas se saca de las habitaciones.
    		//suma diaria de habitaciones ocupadas
    		foreach($this->habitaciones as $dia => $hab)
    		{
    			$total_hab += ($hab->uso_individual + $hab->uso_doble + $hab->otras);
    		}
    	}
    
    	$adr_mensual_calculado = $this->calcula_adr_desde_desglose();
    	
    	return ($adr_mensual_calculado * $total_hab) / ($num_habitaciones * $this->dias_abierto);
    }
    
    /**
     * Calcula el ADR a partir del desglose de precios.
     * @return number
     */
    private function calcula_adr_desde_desglose()
    {
    	$total_hab = 0;
    
    	if ($this->modo_porcentaje == MODO_PORC_NUM)
    	{
    		foreach($this->precios->num as $tc => $num_hab)
    			$total_hab += $num_hab;
    	}
    
    	// Calcular adr mensual a partir del desglose de precios dado.
    	$adr_mensual_calculado = 0.0;
    	foreach ($this->precios->adr as $tipo_cliente => $adr_cliente)
    	{
    		if ($this->modo_porcentaje == MODO_PORC_NUM)
    		{
    			if (!isset($this->precios->num[$tipo_cliente]))
    				$num = 0;
    			else
    				$num = $this->precios->num[$tipo_cliente];
    			$adr_mensual_calculado += ($adr_cliente * ($num/$total_hab));
    		}
    		else
    		{
    			if (!isset($this->precios->pct[$tipo_cliente]))
    				$num = 0;
    			else
    				$num = $this->precios->pct[$tipo_cliente];
    			$adr_mensual_calculado += $adr_cliente * 0.01 * $num;
    		}
    	}
    	 
    	return $adr_mensual_calculado;
    }
    
    /**
     * Comprueba si los datos suministrados tienen desglose de precios o no.
     *
     * Condiciones hay desglose si
     * 	  1. alguno de los adr asociados a tipo de cliente es distinto de cero, salvo caso especial 0.01.
     *
     * No hay desglose si:
     *    1. No está definido precios
     *    2. No está definido precios->adr
     *    3. Para cada linea, adr es 0 o nulo, y porcentaje/numero es 0 o nulo.
     *    3. [Caso especial 0.01] Para cada linea, adr es 0.01 y porcentaje distinto a 0.
     *    4. No está definido el array de num. de habitaciones en modo numero.
     *    5. No está definido el array de porcentaje en modo porcentaje.
     * @param unknown_type $permitirCaso001
     */
    private function hay_desglose_precios($permitirCaso001)
    {
    	if (!isset($this->precios)) return false; //1
    	if (!isset($this->precios->adr)) return false; //2
    	if ($this->modo_porcentaje == MODO_PORC_PORC && !isset($this->precios->pct)) return false; //4
    	if ($this->modo_porcentaje == MODO_PORC_NUM && !isset($this->precios->num)) return false; //5
    	 
    	// Regla 3
    	$hay_desglose = false;
    	foreach($this->tipo_cliente_cadena as $tipo_cliente => $tipo_cadena)
    	{
    		$adr_tc = isset($this->precios->adr[$tipo_cliente]) ? $this->precios->adr[$tipo_cliente] : 0;
    		$otro_tc = ($this->modo_porcentaje == MODO_PORC_PORC)
    		? (isset($this->precios->pct[$tipo_cliente]) ? $this->precios->pct[$tipo_cliente] : 0)
    		: (isset($this->precios->num[$tipo_cliente]) ? $this->precios->num[$tipo_cliente] : 0);
    
    		// adr = 0    Y otr = 0  => No hay desglose
    		// adr = 0.01 Y otr <> 0 => No hay desglose (solo caso especial 0.01)
    		if (!(($adr_tc == 0 && $otro_tc == 0) || ($permitirCaso001 && $adr_tc == 0.01 && $otro_tc != 0)))
    		{
    			$hay_desglose = true;
    			break;
    		}
    	}
    	return $hay_desglose;
    }
    
    private function hay_revpar_mensual()
    {
    	if (!isset($this->precios)) return false;
    	if (!isset($this->precios->revpar_mensual)) return false;
    	if ($this->precios->revpar_mensual == 0) return false;
    	return true;
    }
    
    private function hay_adr_mensual()
    {
    	if (!isset($this->precios)) return false;
    	if (!isset($this->precios->adr_mensual)) return false;
    	if ($this->precios->adr_mensual == 0) return false;
    	return true;
    }
    
    /**
     * Comprueba si hay datos de precios.
     */
    public function hay_datos_precios()
    {
    	$this->tiene_datos_precios = ($this->hay_adr_mensual() || $this->hay_revpar_mensual() || $this->hay_desglose_precios(false));
    	return $this->tiene_datos_precios;
    }
    
    public function hay_datos_movimientos()
    {
    	$this->tiene_datos_movimientos=true;
    	if (!isset($this->mov_por_ut) || !isset($this->totales) || count($this->mov_por_ut) == 0 
    			|| (count($this->totales->movimientos)==0 && $this->totales->presentes_comienzo_mes == 0))
    		$this->tiene_datos_movimientos=false;
    	return $this->tiene_datos_movimientos;
    }
    
    public function hay_datos_personal()
    {
    	$this->tiene_datos_personal=true;
    	if (!isset($this->personal)
    			|| ((!isset($this->personal->no_remunerado) || $this->personal->no_remunerado == false)
    					&& (!isset($this->personal->remunerado_fijo) || $this->personal->remunerado_fijo == false)
    					&& (!isset($this->personal->remunerado_eventual) || $this->personal->remunerado_eventual == false)))
    	{
    		$this->tiene_datos_personal=false;
    	}
    	return $this->tiene_datos_personal;
    }
    
    public function hay_datos_habitaciones()
    {
    	$this->tiene_datos_habitaciones=true;
    	if (!isset($this->habitaciones) || (count($this->habitaciones) == 0))
    		$this->tiene_datos_habitaciones=false;
    	return $this->tiene_datos_habitaciones;
    }
    
    /************** REGLAS DE VALIDACION DE MOVIMIENTOS *****************/
    
    /**
     * REGLA 101: El número de dias abierto no puede superar el número de dias del mes.
     * @return boolean false y mensaje de error en val_errors si no se cumple, verdadero en caso contrario.
     */
    public function valida_dias_abierto()
    {
    	$n_dias_mes = cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->ano);
    	
    	if (isset($this->dias_abierto) && ($this->dias_abierto < 1))
    	{
    	    $this->val_errors->log_error(ERROR_DATO_GLOBAL, sprintf("Los días abiertos (%d) deben ser mayor o igual a uno. Si el establecimiento está cerrado, informe al ISTAC.", $this->dias_abierto));
    	    return false;
    	}
    	
    	if (isset($this->dias_abierto) && $this->dias_abierto > $n_dias_mes)
    	{
    		$this->val_errors->log_error(ERROR_DATO_GLOBAL, sprintf("Los días abiertos (%d) superan el número de días del mes de la encuesta (%02d).", $this->dias_abierto, $n_dias_mes));
    		return false;
    	}
    	return true;
    }
    
    
    /**
     * REGLA 102: No puede haber movimientos de más días que los que se indiquen en <dias_abierto>.
     * PRE: Totales debe estar inicializado con los totales de todas las UTs.
     */
    public function valida_num_movimientos()
    {
    	$hay_error = false;
    
    	$n_dias_mes = 31; //Maximo de dias de un mes.

    	if (isset($this->totales) && isset($this->totales->movimientos) && isset($this->dias_abierto))
    	{
	    	$num_dias_con_movs = count($this->totales->movimientos);
	    	
	    	if ($num_dias_con_movs > $this->dias_abierto)
	    	{
	    		$this->val_errors->log_error(ERROR_MOVIMIENTOS, "El número de días con movimientos supera el número de días abierto.");
	    		$hay_error = true;
	    	}
    	}
    	return $hay_error;
    }
    	
    /**
     * REGLA 3: (Solo XML, desde form. no es posible que no sea así) Los dias de los movimientos van desde 01 hasta el número de días del mes.
     */
    public function valida_dia_movimiento_en_rango()
    {
    	$hay_error = false;
    	
    	$max_dia = cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->ano);
    	
    	foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		foreach($movs->movimientos as $dia => $esp)
    		{
    			if ($dia > $max_dia)
    			{
    				$this->val_errors->log_error(ERROR_MOVIMIENTOS, sprintf("El mes %02d no tiene día %d", $this->mes, $dia));
    				$hay_error = true;
    			}
    		}
    	}
    	return $hay_error;
    }

    /**
     * NOTA: Reglas 104, 105, 6 se validan en la carga del XML, en el momento de hacer la correspondencia de los paises cargados en xml contra los paises configurados.
     */
    
    /**
     * REGLA 106: Los valores de E, S, P, plazas supletorias y habitaciones deben ser enteros no negativos.
     */
    public function valida_movimientos_no_negativos($lookup_paises, $es_hotel)
    {
    	
    	$hay_error = false;
    	
    	///NOTA: Solo es necesario revisar las UTs cargadas, ya que las que existen en la BBDD ya han pasado la validacion
    	foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		foreach($movs->movimientos as $dia => $esp)
    		{
    			if ($esp->entradas<0 || $esp->salidas<0 || $esp->pernoctaciones<0)
    			{
    				$this->val_errors->log_error(ERROR_MOVIMIENTOS, sprintf( "Para el día %02d de %s: Los valores de E, S, P deben ser enteros no negativos.", (int)$dia, $lookup_paises[$id_ut]));
    				$hay_error = true;
    			}
    		}
    	}
    	
    	// Es seguro ignorar la no existencia de información de habitaciones porque en la función 'valida_cruzadas' se comprueba.
    	if($this->habitaciones!=null)
    	{
	    	foreach ($this->habitaciones as $linea) 
	    	{
	    		if($linea->uso_doble<0 || $linea->uso_individual<0 || $linea->otras<0 || $linea->supletorias<0)
	    		{
	    			$this->val_errors->log_error(ERROR_HABITACIONES, sprintf( "Para el día %02d: Los valores de plazas supletorias y %s deben ser enteros no negativos.", (int)$dia, $es_hotel ? "habitaciones" : "apartamentos"));
	   				$hay_error = true;
	    		}
	    	}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 107: El num. de pernoctaciones para un dia debe ser mayor o igual que el numero de entradas.
     */
    public function valida_pernoctaciones_mayor_entradas($lookup_paises)
    {
    	$hay_error = false;
    	///NOTA: Solo es necesario revisar las UTs cargadas, ya que las que existen en la BBDD ya han pasado la validacion
    	foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		foreach($movs->movimientos as $dia => $esp)
    		{
    			if ($esp->pernoctaciones < $esp->entradas)
    			{
    				$this->val_errors->log_error(ERROR_MOVIMIENTOS, sprintf( "Para el día %02d de %s: Las pernoctaciones de un día deben ser mayores o iguales al número de entradas de dicho día.", (int)$dia, $lookup_paises[$id_ut]));
    				$hay_error = true;
    			}
    		}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 108: Las pernoctaciones para el dia d deben ser igual a las pernoctaciones para el dia d-1, mas las entradas menos las salidas.
     */
    public function valida_pernoctaciones($lookup_paises)
    {
    	// NOTA: Para las encuestas del mes en curso solo se valida hasta el dia actual.
    	$fecha_helper = new DateHelper();
    	if($this->mes == $fecha_helper->mes_sistema && $this->ano == $fecha_helper->anyo_sistema)
    		$n_dias_mes = $fecha_helper->dia_sistema;
    	else
    		$n_dias_mes = cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->ano);
    	    	
    	$hay_error = false;
    	
    	///NOTA: Solo es necesario revisar las UTs cargadas, ya que las que existen en la BBDD ya han pasado la validacion
    	foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		$p_dia_ant = 0;
    		if (isset($movs->movimientos[1]))
    		{
    			$esp = $movs->movimientos[1];
    			$p_dia_ant = $esp->pernoctaciones;
    		}    		
    		for ($dia = 2; $dia <= $n_dias_mes; $dia++)
    		{
    			if (isset($movs->movimientos[$dia]))
    			{
    				$esp = $movs->movimientos[$dia];
    				$p_esperado = $p_dia_ant + $esp->entradas - $esp->salidas;
    				$p_real = $esp->pernoctaciones;
    				$p_dia_ant = $esp->pernoctaciones;
    			}
    			else 
    			{
    				if(($this->es_cuestionario_parcial()) && ($p_dia_ant!=0))
    				{
    					if($dia<$n_dias_mes)
    					{
	    					$this->val_errors->log_aviso(ERROR_MOVIMIENTOS,sprintf( "No existen datos para el día %02d de %s. Deberá aportar esta información posteriormente para poder cerrar el cuestionario a principios del próximo mes.", (int)$dia, $lookup_paises[$id_ut]));
    					}
	    				$hay_error = true;
	    				break;
    				}
    				$p_esperado = $p_dia_ant;
    				$p_dia_ant = 0;
    				$p_real = 0;
    			}
    			if ($p_esperado != $p_real)
    			{
    				$this->val_errors->log_error(ERROR_MOVIMIENTOS,sprintf( "Para el día %02d de %s: Las pernoctaciones para los días 2º al 31º deben ser iguales al número de pernoctaciones del día anterior más los viajeros entrados (ENTRADAS) en ese día menos las SALIDAS.", (int)$dia, $lookup_paises[$id_ut]));
    				$hay_error = true;
    				//NOTA: Una vez hay fallo para un dia, ¿fallaran todos los dias siguientes?
    				break;
    			}
    			
    		}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 112: El número de plazas ocupadas (PERNOCTACIONES) en un día no puede superar al numero de plazas disponibles más las plazas supletorias.
     * PRE: Totales debe estar inicializado con los totales de todas las UTs.
     * @param int Número maximo de plazas disponibles en un dia (num plazas hotel + num plazas supletorias o definido por el llamador).
     */
    public function valida_plazas_ocupadas($max_plazas, $es_hotel, $op_es_guardar)
    {
    	$hay_error = false;
    
    	//NOTA: Se supone que totales contiene los totales cargados.
    	if (isset($this->totales))
    	{
    		$max_plazas_efectivo=ceil(($max_plazas * (100.0 + ALOJA_EXCESO_PLAZAS))/100.0);
    		foreach ($this->totales->movimientos as $dia => $total)
    		{
    			if ($total->pernoctaciones > $max_plazas_efectivo)
    			{
    			    //NOTA: En el guardado se relaja la esta restricción porque, en muchas ocasiones, esto impide el guardado cuando presentes a principio de mes en paises que no se muestran en el formulario.
    			    // Ejemplo: empiezo a rellenar un nuevo cuestionario por los 4 primeros paises (UT1...UT4) e introduzco información de ESP.
    			    //    Cuando intento grabar (no enviar), se producen errores de exceso de ocupación debido a que hay presentes a principio de mes de otros paises no visibles (UT5) que aún no he 'sacado' del establecimiento.
    			    //    El resultado es que debo salir del formulario sin guardar para entrar nuevamente con otros paises (UT5) y sacar a los presentes a principio de mes antes de volver a interntar introducir los datos de los paises iniciales (UT1...UT4).
    			    //
    				if($op_es_guardar)
    				    $this->val_errors->log_aviso(ERROR_MOVIMIENTOS,sprintf( "El número de pernoctaciones (%d) en el día %02d supera el número de plazas disponibles más las plazas supletorias (%d).", $total->pernoctaciones, $dia, $max_plazas));
    				else
    				{
    				    $this->val_errors->log_error(ERROR_MOVIMIENTOS,sprintf( "El número de pernoctaciones (%d) en el día %02d supera el número de plazas disponibles más las plazas supletorias (%d).", $total->pernoctaciones, $dia, $max_plazas));
    				    $hay_error = true;
    				}
    			}
    			else
    			{
    				if ($total->pernoctaciones > $max_plazas)
    				{
    					//$this->avisos_excesos->log_aviso(ERROR_EXCESO_PLAZAS, sprintf("El número de pernoctaciones (%d) en el día %02d supera el número de plazas disponibles más las plazas supletorias (%d) pero queda por debajo del porcentaje de exceso permitido (%01.2f%% => %d plazas).", $total->pernoctaciones, $dia, $max_plazas, ALOJA_EXCESO_PLAZAS, $max_plazas_efectivo), array($total->pernoctaciones,$max_plazas,$max_plazas_efectivo,$dia,ALOJA_EXCESO_PLAZAS));
    					$this->avisos_excesos->log_aviso(ERROR_EXCESO_PLAZAS, sprintf("El número de pernoctaciones (%d) en el día %02d supera el número de plazas disponibles más las plazas supletorias (%d). Si el dato es correcto, proceda a cerrar la encuesta e indique el motivo del exceso.", $total->pernoctaciones, $dia, $max_plazas), array($total->pernoctaciones,$max_plazas,$max_plazas_efectivo,$dia,ALOJA_EXCESO_PLAZAS));
    						
    					$this->excesoInfoObj->hayExcesoPlazas=true;
    				}
    			}
    		}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 211: Faltan los datos de alojamiento.
     */
    public function valida_faltan_datos_movimientos()
    {
    	if(!$this->hay_datos_movimientos())
    	{
    		$this->val_errors->log_error(ERROR_MOVIMIENTOS, "Faltan los datos de movimientos. Si está tratando de enviar el cuestionario es necesario seleccionar al menos un país, provincia o isla en pantalla.");
    		return true;
    	}
    	return false;
    }
    
    /**
     * REGLA 212: Falta el número de días abierto.
     */
    public function valida_falta_dias_abierto()
    {
    	if (!isset($this->dias_abierto) || $this->dias_abierto == null)
    	{
    		$this->val_errors->log_error(ERROR_DATO_GLOBAL, "Falta el número de días abierto.");
    		return true;
    	}
    	return false;
    }
    
    
    /**
     * REGLA 4: El número de pernoctaciones excede el límite.
     */
    public function valida_pernoctaciones_excede_limite($max_plazas)
    {
    	if (isset($this->totales) && isset($this->totales->movimientos) && isset($this->dias_abierto))
    	{
    		$tot_pern = 0;
    		//Sumar pernoctaciones
    		foreach($this->totales->movimientos as $esp)
    		{
    			$tot_pern += $esp->pernoctaciones;
    		}
    
    		$total_limite = $max_plazas * $this->dias_abierto;
    		$total_limite_efectivo=ceil(($total_limite * (100.0 + ALOJA_EXCESO_PLAZAS))/100.0);
    		if ($tot_pern > $total_limite_efectivo)
    		{
    			$this->val_errors->log_error(ERROR_MOVIMIENTOS, sprintf("El número de pernoctaciones (%d) excede el límite (%d).", $tot_pern, $total_limite));
    			return true;
    		}
    		else
    		{
    			if ($tot_pern > $total_limite)
    			{
    				//$this->avisos_excesos->log_aviso(ERROR_EXCESO_PLAZAS_MES, sprintf("El número de pernoctaciones mensuales (%d) supera el límite de plazas (%d) pero queda por debajo del porcentaje de exceso permitido (%01.2f%% => %d plazas).", $tot_pern, $total_limite, ALOJA_EXCESO_PLAZAS, $total_limite_efectivo), array($tot_pern,$total_limite,$total_limite_efectivo,ALOJA_EXCESO_PLAZAS));
    				$this->avisos_excesos->log_aviso(ERROR_EXCESO_PLAZAS_MES, sprintf("El número de pernoctaciones mensuales (%d) supera el límite de plazas (%d). Si el dato es correcto, proceda a cerrar la encuesta e indique el motivo del exceso.", $tot_pern, $total_limite), array($tot_pern,$total_limite,$total_limite_efectivo,ALOJA_EXCESO_PLAZAS));
    				
    				$this->excesoInfoObj->hayExcesoPlazasMes=true;
    			}
    		}
    	}
    	return false;
    }
    
    /************** REGLAS DE VALIDACION DE HABITACIONES *****************/
 
    /**
     * REGLA 3: (Solo XML, desde form. no es posible que no sea así) Los dias de las habitaciones van desde 01 hasta el número de días del mes.
     */
    public function valida_dia_habitaciones_en_rango()
    {
    	$hay_error = false;
    
    	$max_dia = cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->ano);
    
    	if (isset($this->habitaciones))
    	{
	    	foreach($this->habitaciones as $dia => $hab)
	    	{
	    		if ($dia > $max_dia)
	    		{
	    			$this->val_errors->log_error(ERROR_HABITACIONES,sprintf("El mes %02d no tiene día %d", $this->mes, $dia));
	    			$hay_error = true;
	    		}
	    	}
    	}
    	return $hay_error;
    }

    
    /**
     * REGLA 109: Si hay habitaciones ocupadas en un dia, deberán existir plazas ocupadas (<PERNOCTACIONES>) en dicho día y viceversa.
     * PRE: Totales debe estar inicializado con los totales de todas las UTs.
     */
    public function valida_habitaciones_ocupadas_existen_pernoctaciones($es_hotel)
    {
    	$hay_error = false;
    	 
    	//NOTA: Se supone que totales contiene los totales cargados.
    	if (isset($this->habitaciones) && isset($this->totales) && isset($this->totales->movimientos))
    	{
    		$max_dia = cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->ano);
    		for ($dia = 1; $dia <= $max_dia; $dia++)
    		{
    			if (isset($this->totales->movimientos[$dia]) || isset($this->habitaciones[$dia]))
    			{
    				$pern_dia = 0;
    				if (isset($this->totales->movimientos[$dia]) && isset($this->totales->movimientos[$dia]->pernoctaciones))
    					$pern_dia = $this->totales->movimientos[$dia]->pernoctaciones;
    				
    				$ocupadas = 0;
    				if (isset($this->habitaciones[$dia]))
    				{
	    				$hab = $this->habitaciones[$dia];
	    				if (isset($hab->uso_doble))
	    					$ocupadas = $hab->uso_doble;
	    				if (isset($hab->uso_individual))
	    					$ocupadas += $hab->uso_individual;
	    				if (isset($hab->otras))
	    					$ocupadas += $hab->otras;    	
    				}
    				if($pern_dia == 0 && $ocupadas != 0)
    				{
    					$err_msg = ($es_hotel)
    					? sprintf("Existen habitaciones ocupadas para el día %02d, pero no existen pernoctaciones para dicho día.",$dia)
    					: sprintf("Existen apartamentos ocupados para el día %02d, pero no existen pernoctaciones para dicho día.",$dia);
    					$this->val_errors->log_error(ERROR_HABITACIONES,$err_msg);
    					$hay_error = true;
    				}
    				elseif ($pern_dia != 0 && $ocupadas == 0)
    				{
    					$err_msg = ($es_hotel)
    					? sprintf("Existen pernoctaciones para el día %02d, pero no existen habitaciones ocupadas para dicho día.",$dia)
    					: sprintf("Existen pernoctaciones para el día %02d, pero no existen apartamentos ocupados para dicho día.",$dia);
    					$this->val_errors->log_error(ERROR_HABITACIONES,$err_msg);
    					$hay_error = true;
    				}    				
    			}
    		}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 110: El número de habitaciones ocupadas en un día deberá ser menor o igual al de plazas ocupadas (<PERNOCTACIONES>) en dicho día.
     * PRE: Totales debe estar inicializado con los totales de todas las UTs.
     * @param unknown_type $es_hotel
     */
    public function valida_habitaciones_ocupadas_menorigual_pernoctaciones($es_hotel)
    {
    	$hay_error = false;
    
    	//NOTA: Se supone que totales contiene los totales cargados.
    	if (isset($this->habitaciones) && isset($this->totales) && isset($this->totales->movimientos))
    	{
	    	foreach($this->habitaciones as $dia => $hab)
	    	{
	    		$pern_dia = 0;
	    		if (isset($this->totales->movimientos[$dia]))
	    			$pern_dia = $this->totales->movimientos[$dia]->pernoctaciones;
	    		 
	    		$ocupadas = $hab->uso_doble + $hab->uso_individual + $hab->otras;
	    		if($ocupadas > $pern_dia)
	    		{
	    			$err_msg = ($es_hotel)
	    			? sprintf("El número de habitaciones ocupadas (%d) para el día %02d debería ser menor o igual que el número de pernoctaciones (%d) para ese día.",$ocupadas, $dia, $pern_dia)
	    			: sprintf("El número de apartamentos ocupados (%d) para el día %02d debería ser menor o igual que el número de pernoctaciones (%d) para ese día.",$ocupadas, $dia, $pern_dia);
	    			$this->val_errors->log_error(ERROR_HABITACIONES,$err_msg);
	    			$hay_error = true;
	    		}
	    	}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 111: Si el número de <PERNOCTACIONES> día a día coincide con el número de habitaciones ocupadas día a día (esto implica que todas las habitaciones o son simples
     * o están ocupadas con uso sencillo), no pueden existir habitaciones ocupadas dobles con uso doble.
     * PRE: Totales debe estar inicializado con los totales de todas las UTs.
     * @param unknown_type $es_hotel
     */
    public function valida_habitaciones_ocupadas_sin_dobles($es_hotel)
    {
    	$hay_error = false;
    	 
    	//NOTA: Se supone que totales contiene los totales cargados.
    	if (isset($this->habitaciones) && isset($this->totales) && isset($this->totales->movimientos))
    	{
	    	foreach($this->habitaciones as $dia => $hab)
	    	{
	    		$pern_dia = 0;
	    		if (isset($this->totales->movimientos[$dia]))
	    			$pern_dia = $this->totales->movimientos[$dia]->pernoctaciones;
	    		 
	    		$ocupadas = $hab->uso_doble + $hab->uso_individual + $hab->otras;
	    		if($ocupadas == $pern_dia && $hab->uso_doble != 0)
	    		{
	    			$err_msg = "";
	    			if ($es_hotel)
	    				$err_msg = sprintf("Si el número de pernoctaciones del día %02d coincide con el número de habitaciones ocupadas ese día (esto implica que todas las habitaciones o son simples o están ocupadas con uso sencillo), no pueden existir habitaciones ocupadas dobles con uso doble.",$dia);
	    			else
	    				$err_msg = sprintf("Si el número de pernoctaciones del día %02d coincide con el número de apartamentos ocupados ese día (esto implica que todos los apartamentos o son simples o están ocupados con uso sencillo), no pueden existir apartamentos ocupados dobles con uso doble.",$dia);
	    			
	    			$this->val_errors->log_error(ERROR_HABITACIONES,$err_msg);
	    			$hay_error = true;
	    		}
	    	}
    	}
    	return $hay_error;
    }
            
    /**
     * REGLA 113: Las habitaciones ocupadas cada día ha de ser menor o igual al número de habitaciones del establecimiento.
     * @param unknown_type $max_habitaciones num. máximo de habitaciones (propiedad del establecimiento).
     * @param boolean $es_hotel Verdadero para obtener mensaje de error para hotel, falso para apartamentos.
     */
    public function valida_habitaciones_ocupadas($max_habitaciones, $es_hotel)
    {
    	$hay_error = false;
    	if (isset($this->habitaciones))
    	{
    		$max_habitaciones_efectivo=ceil(($max_habitaciones * (100.0 + ALOJA_EXCESO_HABIT))/100.0);
	    	foreach($this->habitaciones as $dia => $hab)
	    	{
	    		$ocupadas = $hab->uso_doble + $hab->uso_individual + $hab->otras;
	    		
	    		if ($ocupadas > $max_habitaciones_efectivo)
	    		{
	    			$err_msg = ($es_hotel) 
	    				? sprintf("La suma de habitaciones dobles uso doble, habitaciones dobles uso individual y habitaciones otras (%d) para el día %02d supera el número de habitaciones del establecimiento (%d).", $ocupadas, $dia, $max_habitaciones)
	    				: sprintf("La suma de apartamentos de 4 a 6 personas, apartamentos estudio de 2 a 4 personas y otros tipos de apartamentos (%d) para el día %02d supera el número de apartamentos del establecimiento (%d).", $ocupadas, $dia, $max_habitaciones);
	    			$this->val_errors->log_error(ERROR_HABITACIONES,$err_msg);
	    			$hay_error = true;
	    		}
	    		else
	    		{
	    			if ($ocupadas > $max_habitaciones)
	    			{
	    				//$this->avisos_excesos->log_aviso(ERROR_EXCESO_HABITACIONES, sprintf("La suma de habitaciones dobles uso doble, habitaciones dobles uso individual y habitaciones otras (%d) para el día %02d supera el número de habitaciones del establecimiento (%d) pero queda por debajo del porcentaje de exceso permitido (%01.2f%% => %d habitaciones).", $ocupadas, $dia, $max_habitaciones, ALOJA_EXCESO_HABIT, $max_habitaciones_efectivo), array($ocupadas,$max_habitaciones,$max_habitaciones_efectivo,$dia,ALOJA_EXCESO_HABIT));
	    				$this->avisos_excesos->log_aviso(ERROR_EXCESO_HABITACIONES, sprintf("La suma de habitaciones dobles uso doble, habitaciones dobles uso individual y habitaciones otras (%d) para el día %02d supera el número de habitaciones del establecimiento (%d). Si el dato es correcto, proceda a cerrar la encuesta e indique el motivo del exceso.", $ocupadas, $dia, $max_habitaciones), array($ocupadas,$max_habitaciones,$max_habitaciones_efectivo,$dia,ALOJA_EXCESO_HABIT));
	    				
	    				$this->excesoInfoObj->hayExcesoHabitaciones=true;
	    			}
	    		}
	    	}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 203: Si hay plazas supletorias ocupadas, tiene que haber habitaciones ocupadas.
     */
    public function valida_plazas_supletorias_sin_habitaciones_ocupadas($es_hotel)
    {
    	$hay_error = false;
    	if (isset($this->habitaciones))
    	{
    		foreach($this->habitaciones as $dia => $hab)
    		{
    			$hay_supl = isset($hab->supletorias) && $hab->supletorias > 0;
    			$hay_usoind = isset($hab->uso_individual) && $hab->uso_individual > 0;
    			$hay_usodoble = isset($hab->uso_doble) && $hab->uso_doble > 0;
    			$hay_usootras = isset($hab->otras) && $hab->otras > 0;
    			
    			if ($hay_supl && !($hay_usoind || $hay_usodoble || $hay_usootras))
    			{
    				$this->val_errors->log_error(ERROR_HABITACIONES,sprintf( "Para el día %02d, si hay plazas supletorias ocupadas, tiene que haber %s.", $dia, $es_hotel ? "habitaciones ocupadas" : "apartamentos ocupados"));
    				$hay_error = true;
    			}
    		}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 210: Faltan los datos de habitaciones.
     */
    public function valida_faltan_datos_habitaciones($es_hotel)
    {
    	if(!$this->hay_datos_habitaciones())
    	{
    		$this->val_errors->log_error(ERROR_HABITACIONES,$es_hotel ? "Faltan los datos de habitaciones." : "Faltan los datos de apartamentos.");
    		return true;
    	}
    	return false;
    }
    
    /************** REGLAS DE VALIDACION DE PRECIOS *****************/

    /**
     * REGLA 202: Los datos de precios deben ser no negativos.
     */
    public function valida_precios_no_negativos($es_hotel)
    {
    	$hay_error = false;
    	if (isset($this->precios))
    	{
    		if (isset($this->precios->adr_mensual) && $this->precios->adr_mensual < 0)
    		{
    			$this->val_errors->log_error(ERROR_PRECIOS,"ADR mensual tiene un valor negativo.");
    			$hay_error = true;
    		}
    		if (isset($this->precios->revpar_mensual) && $this->precios->revpar_mensual < 0)
    		{
    			$this->val_errors->log_error(ERROR_PRECIOS,"REVPAR mensual tiene un valor negativo.");
    			$hay_error = true;
    		}    		
    		if (isset($this->precios->adr))
    		{
	    		foreach($this->precios->adr as $tipo_cliente => $adr_valor)
	    		{
	    			if ($adr_valor < 0)
	    			{
	    				$this->val_errors->log_error(ERROR_PRECIOS,sprintf("ADR de '%s' tiene un valor negativo.", $this->tipo_cliente_cadena[$tipo_cliente]));
	    				$hay_error = true;
	    			}
	    		}  
    		}
    		if ($this->modo_porcentaje == MODO_PORC_PORC)
    		{
    			if (isset($this->precios->pct))
    			{
		    		foreach($this->precios->pct as $tipo_cliente => $adr_valor)
		    		{
		    			if ($adr_valor < 0)
		    			{
		    				$this->val_errors->log_error(ERROR_PRECIOS,sprintf("El porcentaje de '%s' tiene un valor negativo.", $this->tipo_cliente_cadena[$tipo_cliente]));
		    				$hay_error = true;
		    			}
		    		}
    			}
    		}
    		else
    		{
    			if (isset($this->precios->num))
    			{
		    		foreach($this->precios->num as $tipo_cliente => $adr_valor)
		    		{
		    			if ($adr_valor < 0)
		    			{
		    				$this->val_errors->log_error(ERROR_PRECIOS,sprintf("El número de %s de '%s' tiene un valor negativo.", $es_hotel? "habitaciones":"apartamentos", $this->tipo_cliente_cadena[$tipo_cliente]));
		    				$hay_error = true;
		    			}
		    		}
    			}
    		}		
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 114: La suma de los porcentajes debe ser 100, si alguno de ellos es distinto de cero.
     */
    public function valida_precios_porcentajes_100($es_hotel)
    {
    	$hay_error = false;
    
    	$total_pct = 0;
    	if (isset($this->precios) && $this->modo_porcentaje == MODO_PORC_PORC && isset($this->precios->pct))
    	{
	    	foreach($this->precios->pct as $tipo_cliente => $pct_valor)
	    	{
	    		$total_pct += $pct_valor;
	    	}
	    
	    	if ($total_pct > 0 && abs($total_pct - 100) >= 0.001)
	    	{
	    		if ($es_hotel)
	    			$this->val_errors->log_error(ERROR_PRECIOS,"La suma de los porcentajes de las habitaciones por tipo de cliente es ". number_format($total_pct, 2, ",", ".") ."% y debe ser igual a 100%, si alguno de los porcentajes es distinto de cero.");
	    		else
	    			$this->val_errors->log_error(ERROR_PRECIOS,"La suma de los porcentajes de los apartamentos por tipo de cliente es ". number_format($total_pct, 2, ",", ".") ."% y debe ser igual a 100%, si alguno de los porcentajes es distinto de cero.");
	    		$hay_error = true;
	    	}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 115: Si el ADR por tipo de cliente es mayor que cero, entonces el porcentaje de ocupación por tipo de cliente debe ser mayor que cero.
     */
    public function valida_precios_existe_porcentaje_o_numero($es_hotel)
    {
    	
    	$hay_error = false;
    	
    	if (isset($this->precios) && isset($this->precios->adr))
    	{
	    	foreach($this->precios->adr as $tipo_cliente => $adr_valor)
	    	{
	    		if ($adr_valor > 0)
	    		{
	    			if ($this->modo_porcentaje == MODO_PORC_PORC)
	    			{ 
	    				if (!isset($this->precios->pct) || !isset($this->precios->pct[$tipo_cliente]) || $this->precios->pct[$tipo_cliente] <= 0)
		    			{
		    				if ($es_hotel)
		    					$this->val_errors->log_error(ERROR_PRECIOS,sprintf("Si ADR de '%s' es mayor que cero, su porcentaje o número de habitaciones ocupadas debe ser también mayor que cero.", $this->tipo_cliente_cadena[$tipo_cliente]));
		    				else
		    					$this->val_errors->log_error(ERROR_PRECIOS,sprintf("Si ADR de '%s' es mayor que cero, su porcentaje o número de apartamentos ocupados debe ser también mayor que cero.", $this->tipo_cliente_cadena[$tipo_cliente]));
		    				$hay_error = true;    				
		    			}
	    			}
	    			else 
	    			{
	    				if (!isset($this->precios->num) || !isset($this->precios->num[$tipo_cliente]) || $this->precios->num[$tipo_cliente] <= 0)
	    				{
	    					if ($es_hotel)
	    						$this->val_errors->log_error(ERROR_PRECIOS,sprintf("Si ADR de '%s' es mayor que cero, su porcentaje o número de habitaciones ocupadas debe ser también mayor que cero.", $this->tipo_cliente_cadena[$tipo_cliente]));
	    					else
	    						$this->val_errors->log_error(ERROR_PRECIOS,sprintf("Si ADR de '%s' es mayor que cero, su porcentaje o número de apartamentos ocupados debe ser también mayor que cero.", $this->tipo_cliente_cadena[$tipo_cliente]));
	    					$hay_error = true;	    					
	    				}
	    			}
	    		}
	    	}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 116: Si el ADR por tipo de cliente es cero, entonces el porcentaje de ocupación o el numero de habitaciones por tipo de cliente debe ser tambien cero.
     * @return boolean
     */
    public function valida_precios_porcentaje_o_numero_cero($es_hotel, $num_habitaciones)
    {
    	   	
    	$hay_error = false;
    	 
    	if (isset($this->precios))
    	{
    		$vals = ($this->modo_porcentaje == MODO_PORC_PORC)? $this->precios->pct : $this->precios->num;
    		
    		$n_dias_mes = cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->ano);
    		
    		$dao = new AlojaDao();
    		$limites=$dao->cargar_limite_invitaciones();
    		
    		foreach($this->tipo_cliente_cadena as $tipo_cliente => $tipo_cadena)
    		{
    			if ((!isset($this->precios->adr[$tipo_cliente]) || $this->precios->adr[$tipo_cliente] == 0) && (isset($vals[$tipo_cliente]) && $vals[$tipo_cliente]!=0))
    			{
    				$limite=(isset($limites[$tipo_cliente]) ? $limites[$tipo_cliente] : 0.0);
    				if($this->modo_porcentaje == MODO_PORC_NUM)
    					$limite=ceil((($num_habitaciones * $n_dias_mes) * $limite) / 100.0);
    				if($vals[$tipo_cliente] > $limite)
    				{
	    				if ($es_hotel)
	    					$this->val_errors->log_error(ERROR_PRECIOS,sprintf("Si ADR de '%s' es igual que cero, el porcentaje o número de habitaciones ocupadas debe ser también cero.", $this->tipo_cliente_cadena[$tipo_cliente]));
	    				else
	    					$this->val_errors->log_error(ERROR_PRECIOS,sprintf("Si ADR de '%s' es igual que cero, el porcentaje o número de apartamentos ocupados debe ser también cero.", $this->tipo_cliente_cadena[$tipo_cliente]));
	    				$hay_error = true;
    				}
    			}
    		}
    	}
    	return $hay_error;
    } 
    
    public function valida_hay_datos_precios()
    {
    	if (!$this->hay_datos_precios())
    	{
    		$this->val_errors->log_error(ERROR_PRECIOS,"Faltan los datos de precios.");
    		return true;
    	}
    	return false;
    }
    
    public function valida_falta_adr_o_revpar()
    {
    	$hay_error = false;
    	if (!$this->hay_adr_mensual())
    	{
    		$this->val_errors->log_error(ERROR_PRECIOS,"Falta el ADR.");
    		$hay_error = true;
    	}
    	if (!$this->hay_revpar_mensual())
    	{
    		$this->val_errors->log_error(ERROR_PRECIOS,"Falta el REVPAR.");  
    		$hay_error = true;
    	}  	
    	return $hay_error;
    }
    
//     /**
//      * REGLA 209: Falta el ADR o REVPAR.
//      */
//     public function valida_falta_adr_o_revpar()
//     {
//     	if ((!isset($this->precios) || !isset($this->precios->adr_mensual) || !isset($this->precios->revpar_mensual)) && (isset($this->precios->adr) && count($this->precios->adr) > 0))
//     	{
//     		$this->val_errors->log_error(ERROR_PRECIOS,"Falta el ADR o REVPAR");
//     		return true;
//     	}
//     	return false;
//     }
    
    /**
     * REGLA 214: Faltan los precios del desglose de precios.
     */
    public function valida_falta_desglose_precios($permitirCaso001)
    {
    	if (!$this->hay_desglose_precios($permitirCaso001))
    	{
    		$this->val_errors->log_error(ERROR_PRECIOS,"Faltan los precios del desglose de precios.");
    		return true;
    	}
    	return false;
    }
        
//     public function desglose_a_cero()
//     {
//     	if (!isset($this->precios) || !isset($this->precios->adr) || !isset($this->precios->pct))
//     		return true;
    	
//     	// Comprobar el caso especial de el precio a 0.01 para las entradas de tipo de cliente con porcentaje distinto a 0.
//     	$casoespecial = true;
//     	foreach($this->precios->adr as $tipo_cliente => $valor)
//     	{
//     		if ($valor != 0.01 && isset($this->precios->pct[$tipo_cliente]) && $this->precios->pct[$tipo_cliente] != 0)
//     		{
//     			$casoespecial = false;
//     			break;
//     		}
//     	} 
//     	return $casoespecial;
//     }
    
    /**
     * REGLA 215: Faltan los % de habitaciones o el número de habitaciones del desglose de precios.
     */
    public function valida_falta_desglose_porcentaje_numero_precios($es_hotel)
    {
    	if (!$this->hay_desglose_precios(false))	
    	{
    		if ($es_hotel)
    			$this->val_errors->log_error(ERROR_PRECIOS,"Faltan los porcentajes de habitaciones o el número de habitaciones del desglose de precios.");
    		else
    			$this->val_errors->log_error(ERROR_PRECIOS,"Faltan los porcentajes de apartamentos o el número de apartamentos del desglose de precios.");
    		return true;
    	}
    	return false;    	
    }
    
    /**
     * REGLA 222: La suma de habitaciones de precios no coincide con la suma diaria de habitaciones ocupadas.
     */
    public function valida_suma_habs_precios_no_coincide_total_habitaciones($es_hotel)
    {
    	if ($this->modo_porcentaje == MODO_PORC_NUM)
    	{
    		// Faltan los datos necesarios.
    		if (!isset($this->precios) || !isset($this->habitaciones))
    			return false;
    		
	    	// suma de habitaciones de precios
    		$suma_hab_precios = 0;
    		foreach($this->precios->num as $num)
    		{
    			$suma_hab_precios += $num;
    		}
    		
    		//suma diaria de habitaciones ocupadas
    		$suma_diaria_habs = 0;
    		foreach($this->habitaciones as $dia => $hab)
    		{
    			$suma_diaria_habs += ($hab->uso_individual + $hab->uso_doble + $hab->otras); 
    		}
    		
    		if ($suma_hab_precios != $suma_diaria_habs)
    		{
    			if ($es_hotel)
    				$this->val_errors->log_error(ERROR_PRECIOS,"La suma de habitaciones de precios no coincide con la suma diaria de habitaciones ocupadas.");
    			else
    				$this->val_errors->log_error(ERROR_PRECIOS,"La suma de apartamentos de precios no coincide con la suma diaria de apartamentos ocupados.");
    			return true;
    		}
    	}
    	return false;
    }
    

    
//     /**
//      * REGLA 223: El ADR mensual no coincide con el calculado a partir de los desgloses por tipo de cliente.
//      */
//     public function valida_calculo_suma_adr_mensual()
//     {
//     	if (($this->modo_porcentaje == MODO_PORC_PORC && (!isset($this->precios) || !isset($this->precios->pct) || count($this->precios->pct) == 0) )
//     	 || ($this->modo_porcentaje == MODO_PORC_NUM && (!isset($this->precios) || !isset($this->precios->num) || count($this->precios->num) == 0) )
//     	 || (!isset($this->precios) || !isset($this->precios->adr) || count($this->precios->adr) == 0)
//     	 || !isset($this->precios->adr_mensual))
//     	{
//     		return false;	
//     	}
    	    	
//     	// Calcular adr mensual a partir del desglose de precios dado.
//     	$adr_mensual_calculado = $this->calcula_adr_desde_desglose();
    	
    	
//     	$red_adr_mensual = round($this->precios->adr_mensual,2);
//     	$red_adr_calculado = round($adr_mensual_calculado, 2);
    	
//     	if (abs($red_adr_mensual - $red_adr_calculado) > ($red_adr_calculado * 0.1))
//     	{
//     		$this->val_errors->log_error(ERROR_PRECIOS,sprintf("El ADR mensual (%04.2f) no coincide con el calculado a partir de los desgloses por tipo de cliente (%04.2f).", $this->precios->adr_mensual, $adr_mensual_calculado));
//     		return true;    		
//     	}
//     	return false;
//     }
    
   
    
//     /**
//      * REGLA 224: El RevPar mensual no coincide con el calculado a partir de los desgloses por tipo de cliente.
//      */
//     public function valida_calculo_suma_revpar_mensual($num_habitaciones)
//     {
//         if (($this->modo_porcentaje == MODO_PORC_PORC && (!isset($this->precios) || !isset($this->precios->pct) || count($this->precios->pct) == 0) )
//     	 || ($this->modo_porcentaje == MODO_PORC_NUM && (!isset($this->precios) || !isset($this->precios->num) || count($this->precios->num) == 0) )
//     	 || (!isset($this->precios) || !isset($this->precios->adr) || count($this->precios->adr) == 0)
//          || !isset($this->precios->adr_mensual) || !isset($this->precios->revpar_mensual)
//          || !isset($this->dias_abierto)
//          || !isset($this->habitaciones))
//     	{
//     		return false;	
//     	}
    	
//     	$revpar_mensual_calculado = $this->calculo_revpar_desde_desglose($num_habitaciones);
    	
//     	$red_revpar_mensual = round($this->precios->revpar_mensual,2);
//     	$red_revpar_calculado = round($revpar_mensual_calculado, 2);
    	
//     	if (abs($red_revpar_mensual - $red_revpar_calculado) > ($red_revpar_calculado * 0.1))
//     	{
//     		$this->val_errors->log_error(ERROR_PRECIOS,sprintf("El RevPar mensual (%04.2f) no coincide con el calculado a partir de los desgloses por tipo de cliente (%04.2f).", $this->precios->revpar_mensual, $revpar_mensual_calculado));
//     		return true;
//     	}
//     	return false;
//     }

    
//     /**
//      * REGLA 225: El RevPar calculado a partir del ADR mensual no coincide con el RevPar que está grabado.
//      */
//     public function valida_calculo_revpar_mensual($num_habitaciones)
//     {
//         if (($this->modo_porcentaje == MODO_PORC_PORC && (!isset($this->precios)) )
//     	 || ($this->modo_porcentaje == MODO_PORC_NUM && (!isset($this->precios) || !isset($this->precios->num) || count($this->precios->num) == 0) )
//          || !isset($this->precios->adr_mensual) || !isset($this->precios->revpar_mensual)
//          || !isset($this->dias_abierto)
//          || !isset($this->habitaciones))
//     	{
//     		return false;	
//     	}
    	
//     	$revpar_mensual_calculado = $this->calculo_revpar_mensual_desde_adr_mensual($num_habitaciones);

//     	$red_revpar_mensual = round($this->precios->revpar_mensual,2);
//     	$red_revpar_calculado = round($revpar_mensual_calculado, 2);
    	 
    	
//     	if (abs($red_revpar_mensual - $red_revpar_calculado) > ($red_revpar_calculado * 0.1))
//     	{
//     		$this->val_errors->log_error(ERROR_PRECIOS,"El RevPar calculado a partir del ADR mensual no coincide con el RevPar facilitado por el establecimiento.");
//     		return true;
//     	}
//     	return false;
//     }
    
    /************** REGLAS DE VALIDACION DE PERSONAL *****************/
    
    /**
     * REGLA 201: Los datos de personal deben ser enteros no negativos.
     */
    public function valida_personal_no_negativo()
    {
    	if (isset($this->personal))
    	{
    		if ($this->personal->no_remunerado < 0 || $this->personal->remunerado_eventual < 0 || $this->personal->remunerado_fijo < 0)
    		{
    			$this->val_errors->log_error(ERROR_PERSONAL, "Los datos de personal deben ser números no negativos.");
    			return true;
    		}
    		return false;
    	}
    }
    
    /**
     * REGLA 216: Faltan los datos de personal.
     */
    public function valida_personal_haydatos()
    {
    	if(!$this->hay_datos_personal())
    	{
    		$this->val_errors->log_error(ERROR_PERSONAL, "Faltan los datos de personal.");
    		return true;
    	}
    	return false;
    }
    
    public function valida_presentes_comienzo($lookup_paises)
    {
    	$hay_error = false;
    	$dao = new AlojaDao();
    	
    	$presentes_mes_anterior = $dao->cargar_presentes_fin_mes_uts($this->estabid_declarado, $this->mes, $this->ano);

    	//Recorrer presentes a mes anterior del cuestionario anterior para comprobar que haya desaparecido alguno en el actual
    	foreach ($presentes_mes_anterior as $ut => $presentes)
    	{
    		if (!isset($this->mov_por_ut[$ut]))
    		{
    			$this->val_errors->log_aviso(ERROR_MOVIMIENTOS, sprintf("No hay presentes a comienzos de este mes para %s y los presentes a fin del mes anterior eran %s.", $lookup_paises[$ut], $presentes));
    			$hay_error = true;
    		}
    	}
    	//Recorrer presentes a comienzo de mes del cuestionario actual para comprobar si no ha aparecido nuevas ut y si no coinciden los valores
		foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		$pcm = $movs->presentes_comienzo_mes;
    		/*
    		if (!isset($presentes_mes_anterior[$id_ut]) && $pcm != 0)
    		{
    			$this->val_errors->log_aviso(ERROR_MOVIMIENTOS, sprintf("Los presentes a comienzos del mes %s para %s son %s y el mes anterior no había presentes a final de mes.",$this->mes,  $lookup_paises[$id_ut], $pcm));
    			$hay_error = true;
    		}
    		if (isset($presentes_mes_anterior[$id_ut]) && $pcm != $presentes_mes_anterior[$id_ut]) 
    		{
    			$this->val_errors->log_aviso(ERROR_MOVIMIENTOS, sprintf("Los presentes a comienzos del mes %s (%s), no coinciden con los del final del mes anterior para %s (%s).",$this->mes,  $pcm, $lookup_paises[$id_ut], $presentes_mes_anterior[$id_ut]));
    			$hay_error = true;
    		}
    		*/
    	}
    	
    	return $hay_error;
    }
    
    /************** REGLAS DE VALIDACION DE AVISOS *****************/
    
    /**
     * REGLA 10: Hay pernoctaciones y no se han registrado entradas ni salidas en el mes.
     */
    public function valida_pernoctaciones_sin_entradas_salidas()
    {
    	if (!isset($this->totales))
    		return false;
    	
    	if (!isset($this->totales->movimientos->presentes_comienzo_mes) || $this->totales->presentes_comienzo_mes == 0)
    		return false;
    	
    	if (isset($this->totales->movimientos) && count($this->totales->movimientos) != 0)
    	{
	    	foreach($this->totales->movimientos as $esp)
	    	{
	    		if ($esp->entradas != 0 || $esp->salidas != 0)
	    			return false;
	    	}
    	}
    	
    	$this->val_errors->log_aviso(ERROR_MOVIMIENTOS, "Hay pernoctaciones y no se han registrado entradas ni salidas en el mes.");
    	return true;
    }    
    
    
    private function calcular_estancia_media($ut)
    {
    	$estmedia = 0;
    	
    	$tot_ent = 0;
    	$tot_per = 0;
    	foreach( $ut->movimientos as $esp)
    	{
    		$tot_ent += $esp->entradas;
    		$tot_per += $esp->pernoctaciones;
    	}
    	
    	$tot_ent += $ut->presentes_comienzo_mes;
    	
    	if ($tot_ent != 0)
    		$estmedia = $tot_per / $tot_ent;
    	
    	return $estmedia;
    }
    
    /**
     * REGLA 12: La estancia media por nacionalidad es superior a 20 días.
     */
    public function valida_estancia_media_supera_20dias($lookup_paises)
    {
//     	est_med=0;
//     	if(tot_col_ent+parseInt(utColumns[cl].PresComMes)!=0)
//     		est_med = tot_col_per / (tot_col_ent+parseInt(utColumns[cl].PresComMes));
    	
    	$hay_error = false;
        foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		$est_media_ut = $this->calcular_estancia_media($movs);
    		if ($est_media_ut > 20)
    		{
    			$this->val_errors->log_aviso(ERROR_MOVIMIENTOS, sprintf( "Para la unidad territorial %s: La estancia media por nacionalidad es superior a 20 días.", $lookup_paises[$id_ut]));
    			$hay_error = true;
    		}
    	}
    	return $hay_error;
    }    
    
    /**
     * REGLA 13: Las salidas son inferiores a los presentes a principio de mes.
     */
    public function valida_salidas_inferiores_presentes_principio_mes($lookup_paises)
    {
    	$hay_error = false;
        foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		$tot_salidas = 0;
    		foreach( $movs->movimientos as $esp)
    		{
    			$tot_salidas += $esp->salidas;
    		}
    		
    		if ($tot_salidas < $movs->presentes_comienzo_mes)
    		{
    			$this->val_errors->log_aviso(ERROR_MOVIMIENTOS, sprintf( "Para la unidad territorial %s: Las salidas son inferiores a los presentes a principio de mes.", $lookup_paises[$id_ut]));
    			$hay_error = true;
    		}
    	}
    	return $hay_error;
    }  

    /**
     * REGLA 14: La estancia media por nacionalidad es superior a 30 días.
     */
    public function valida_estancia_media_supera_30dias($lookup_paises)
    {
    	$hay_error = false;
        foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		$est_media_ut = $this->calcular_estancia_media($movs);
    		if ($est_media_ut > 30)
    		{
    			$this->val_errors->log_aviso(ERROR_MOVIMIENTOS, sprintf( "Para la unidad territorial %s: La estancia media por nacionalidad es superior a 30 días.", $lookup_paises[$id_ut]));
    			$hay_error = true;
    		}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA 209: Falta el ADR o REVPAR.
     * 
     * REGLA 223: El ADR mensual no coincide con el calculado a partir de los desgloses por tipo de cliente.
     * 
     * REGLA 224: El RevPar mensual no coincide con el calculado a partir de los desgloses por tipo de cliente.
     * 
     * REGLA 225: El RevPar calculado a partir del ADR mensual no coincide con el RevPar que está grabado.
     */
    public function valida_adr_revpar($num_habitaciones, $es_hotel, $permitirCaso001)
    {
    	$hay_error = false;
    	$adr_mensual = (isset($this->precios->adr_mensual) ? $this->precios->adr_mensual : 0);
    	$revpar_mensual = ((isset($this->precios->revpar_mensual)) ? $this->precios->revpar_mensual : 0);
    	if ($this->hay_datos_precios())
    	{
    		if ($this->hay_desglose_precios($permitirCaso001))
    		{
    			// Hay datos de desglose.
    			
    			if($adr_mensual == 0)
    			{
    				// No hay ADR informado.
    				
    				if($revpar_mensual == 0)
    				{
    					// No hay REVPAR informado.
    					
    					//CASO 3: (11)
    					//La comprobación de error se hace en valida cruzadas, después de guardar en la bbdd.
    				}
    				else
    				{
    					// Hay REVPAR informado.
    					
    					$revpar_calculado = $this->calculo_revpar_desde_desglose($num_habitaciones);
    					$red_revpar_mensual = round($revpar_mensual,2);
    					$red_revpar_calculado = round($revpar_calculado, 2);
    					
    					if (abs($red_revpar_mensual - $red_revpar_calculado) > ($red_revpar_calculado * 0.1))
    					{
    						// El REVPAR informado no coincide al 90% con el calculado => ERROR.
    						
    						//En este caso, se debe indicar explicitamente que falta el adr, ya que no se llega a valida cruzadas.
    						$this->val_errors->log_error(ERROR_PRECIOS,"Falta el ADR.");
    						/**
    						 * REGLA 224: El RevPar mensual no coincide con el calculado a partir de los desgloses por tipo de cliente.
    						 */
    						//CASO 2: (16)
    						// Devolver condicion de error
    						$this->val_errors->log_error(ERROR_PRECIOS,sprintf("El RevPar mensual facilitado (%04.2f) no coincide con el calculado a partir de los desgloses por tipo de cliente (%04.2f). El error puede deberse al desglose de porcentajes o número de %s por tipo de cliente o que el número de %s (%d) de su establecimiento en nuestra base de datos sea incorrecto. Por favor, contacte con el ISTAC para verificar esta información.",
    								$red_revpar_mensual, $red_revpar_calculado, $es_hotel ? "habitaciones ocupadas" : "apartamentos ocupados", $es_hotel ? "habitaciones" : "apartamentos", $num_habitaciones));
    					
    						$hay_error = true;
    					}
    					else
    					{
    						// El REVPAR informado es correcto.
    						
    						//CASO 3: (15)
    						//La comprobación de error se hace en valida cruzadas, después de guardar en la bbdd.
    					}
    				}
    			}
    			else
    			{
    				// Hay ADR informado.
    				
    				$adr_mensual_calculado = $this->calcula_adr_desde_desglose();
    				$red_adr_mensual = round($adr_mensual,2);
    				$red_adr_calculado = round($adr_mensual_calculado, 2);
    					
    				if (abs($red_adr_mensual - $red_adr_calculado) > ($red_adr_calculado * 0.1))
    				{
    					// El ADR informado no coincide al 90% con el calculado => ERROR.
    					
    					/**
    					 * REGLA 223: El ADR mensual no coincide con el calculado a partir de los desgloses por tipo de cliente.
    					 */
    					//CASO 2 (14)
    					$this->val_errors->log_error(ERROR_PRECIOS,sprintf("El ADR mensual facilitado (%04.2f) no coincide con el calculado a partir de los desgloses por tipo de cliente (%04.2f).",
    							$red_adr_mensual, $red_adr_calculado));
    					
    					$hay_error = true;
    				}
    				else
    				{
    					// El ADR informado es correcto.
    					
    					if ($revpar_mensual == 0)
    					{
    						// No hay REVPAR informado.
    						
    						//CASO 3 (12)
    						//La comprobación de error se hace en valida cruzadas, después de guardar en la bbdd.
    					}
    					else
    					{
    						// Hay REVPAR informado.
    						
    						$revpar_mensual_calculado = $this->calculo_revpar_mensual_desde_adr_mensual($num_habitaciones);
    						$red_revpar_mensual = round($revpar_mensual,2);
    						$red_revpar_calculado = round($revpar_mensual_calculado, 2);
    						if (abs($red_revpar_mensual - $red_revpar_calculado) > ($red_revpar_calculado * 0.1))
    						{
    							// El REVPAR informado no coincide al 90% con el calculado => ERROR.
    							
    							/**
    							 * REGLA 225: El RevPar calculado a partir del ADR mensual no coincide con el RevPar que está grabado.
    							 */
    							//CASO 2 (13)
    							$this->val_errors->log_error(ERROR_PRECIOS,sprintf("El RevPar mensual (%04.2f) no coincide con el calculado a partir del ADR mensual facilitado (%04.2f). Tenga en cuenta que el error puede deberse a que el número de %s (%d) de su establecimiento en nuestra base de datos sea incorrecto. Por favor, contacte con el ISTAC para verificar esta información.",
    									$red_revpar_mensual, $red_revpar_calculado, $es_hotel ? "habitaciones" : "apartamentos", $num_habitaciones));
    							
    							$hay_error = true;
    						}
    						else
    						{
    							// El ADR informado es coherente con el REVPAR informado => Todo OK.
    							
    							//CASO 1 (1). No hay errores ni en validacion cruzadas, se sigue la ejecucion normal.
    						}
    					}	// REVPAR informado.
    				}	// ADR informado incorrecto.
    			}	// ADR informado.
    		}	// Hay desglose.
    		else
    		{
    			//No hay desglose, o esta a cero, o es caso especial 0.01 (solo xml)
    			
    			if ($adr_mensual == 0)
    			{
    				// No hay ADR informado.
    				
    				if ($revpar_mensual == 0)
    				{
    					// No hay REVPAR informado.
    					
    					//CASO 3 (6)
    					//La comprobación de error se hace en valida cruzadas, después de guardar en la bbdd.
    				}
    				else
    				{
    					// Hay REVPAR informado pero no hay desglose, ni ADR => ERROR.
    					
    					/**
    					 * REGLA 209: Falta el ADR o REVPAR.
    					 */
    					//CASO 2 (5)(10)
    					$this->val_errors->log_error(ERROR_PRECIOS,"Faltan los precios del desglose de precios.");
    					$this->val_errors->log_error(ERROR_PRECIOS,"Si RevPar no es cero, el ADR mensual no puede ser cero.");
    					
    					$hay_error = true;
    				}	// REVPAR informado.
    			}	// ADR no informado.
    			else
    			{
    				// ADR informado.
    				
    				if ($revpar_mensual == 0)
    				{
    					// No hay REVPAR informado.
    					
    					//CASO 3 (4)(9)
    					//La comprobación de error se hace en valida cruzadas, después de guardar en la bbdd.
    				}
    				else
    				{
    					// REVPAR informado.
    					
    					$revpar_mensual_calculado = $this->calculo_revpar_mensual_desde_adr_mensual($num_habitaciones);
    					$red_revpar_mensual = round($revpar_mensual,2);
    					$red_revpar_calculado = round($revpar_mensual_calculado, 2);
    					if (abs($red_revpar_mensual - $red_revpar_calculado) > ($red_revpar_calculado * 0.1))
    					{
    						// El REVPAR informado no coincide al 90% con el calculado => ERROR.
    						
    						/**
    						 * REGLA 225: El RevPar calculado a partir del ADR mensual no coincide con el RevPar que está grabado.
    						 */
    						//CASO 2 (3)(8)
    						$this->val_errors->log_error(ERROR_PRECIOS,"Faltan los precios del desglose de precios.");
    						$this->val_errors->log_error(ERROR_PRECIOS,sprintf("El RevPar mensual (%04.2f) no coincide con el calculado a partir del ADR mensual facilitado (%04.2f). Tenga en cuenta que el error puede deberse a que el número de %s (%d) de su establecimiento en nuestra base de datos sea incorrecto. Por favor, contacte con el ISTAC para verificar esta información.",
    								$red_revpar_mensual, $red_revpar_calculado, $es_hotel ? "habitaciones" : "apartamentos", $num_habitaciones));
    						
    						$hay_error = true;
    					}
    					else
    					{
    						// El ADR y el REVPAR informados son coherentes. => Todo OK.
    						
    						//CASO 3 (2)(7)
    						//La comprobación de error se hace en valida cruzadas, después de guardar en la bbdd.
    					}	// REVPAR incorrecto.
    				}	// REVPAR informado.
    			}	// ADR informado.
    		}	// No hay desglose.
    	}	// Hay datos de precios.
    	else
    	{
    		// No hay datos de precios => Nada que hacer.
    		
    		//CASO 3 (17)(18)
    		//La comprobación de error se hace en valida cruzadas, después de guardar en la bbdd.
    	}
    	return $hay_error;
    }
    
    
    /*** VALIDACIONES ***/
    public function valida_internas($lookup_paises, $max_plazas, $num_habitaciones, $es_hotel, $op_es_guardar, $permitirCaso001 = false)
    {
    	// Limpiamos la información sobre excesos de plazas/habitaciones
    	unset($this->excesoInfoObj);
    	$this->excesoInfoObj=new AlojaExcesoInfo();
    	
    	/// REGLAS: 212, 101, 102, 106, 107, 108,
    	$this->valida_falta_dias_abierto();
    	$this->valida_dias_abierto();
    	$this->valida_num_movimientos();
    	$this->valida_movimientos_no_negativos($lookup_paises, $es_hotel);
    	$this->valida_pernoctaciones_mayor_entradas($lookup_paises);
    	$this->valida_pernoctaciones($lookup_paises);
    
    	/// REGLAS: 209, 223, 224, 225
    	if((!isset($this->cuestionario_parcial)) || ($this->cuestionario_parcial==false))
    	{
    		$this->valida_adr_revpar($num_habitaciones, $es_hotel, $permitirCaso001);
    		$this->valida_precios_existe_porcentaje_o_numero($es_hotel);
    		$this->valida_precios_porcentaje_o_numero_cero($es_hotel, $num_habitaciones);
    		$this->valida_precios_porcentajes_100($es_hotel);
    	}
    	
    	/// REGLAS: 112, 113, 209, 114, 115, 116
    	$this->valida_plazas_ocupadas($max_plazas, $es_hotel, $op_es_guardar);
    	$this->valida_habitaciones_ocupadas($num_habitaciones, $es_hotel);
    	//$this->valida_falta_adr_o_revpar();
    	   
    	/// REGLAS: 201, 202, 203
    	$this->valida_personal_no_negativo();
    	if((!isset($this->cuestionario_parcial)) || ($this->cuestionario_parcial==false))
    		$this->valida_precios_no_negativos($es_hotel);
    	$this->valida_plazas_supletorias_sin_habitaciones_ocupadas($es_hotel);
    	
    	//$this->valida_calculo_suma_adr_mensual();
    	//$this->valida_calculo_suma_revpar_mensual($num_habitaciones);
    	//$this->valida_calculo_revpar_mensual($num_habitaciones);
    
    	/// REGLAS: 4
    	if(!$op_es_guardar)
    		$this->valida_pernoctaciones_excede_limite($max_plazas);
    	
    	/// NOTA: Estas reglas solo es aplicable a XML, y no afecta a WEB.
    	$this->valida_dia_movimiento_en_rango();
    	$this->valida_dia_habitaciones_en_rango();
    	
    	/// NOTA: En este punto los únicos avisos que pueden existir son los relacionados con exceso, dentro del límite fijado, de plazas y/o habitaciones.
    	/// El resto son errores que deben detener el proceso del cuestionario.
    	
    	if(!$this->contiene_errores())
    		return false;
    
    	return $this->val_errors;
    }
    
    public function valida_avisos_alguardar($es_hotel, $permitirCaso001)
    {
    	$this->valida_falta_desglose_precios($permitirCaso001);
    	$this->valida_falta_desglose_porcentaje_numero_precios($es_hotel);
    	if (!$this->contiene_errores())
    		return false;
    
    	return $this->val_errors;
    }
    
    public function valida_cese_actividad($lookup_paises)
    {
    	if(isset($this->ceseActividad))
    	{
    		if($this->ceseActividad==false)
    			return false;
    	}
		
		// COVID-19: Esto es lo que vale normalmente para que se ejecuten las validaciones.
    	$hay_error=false;
    	$fecha_helper = new DateHelper();
    	if($this->mes == $fecha_helper->mes_sistema && $this->ano == $fecha_helper->anyo_sistema)
    	{
    		$hoy=$fecha_helper->dia_sistema;
    		if($this->dias_abierto>$hoy)
    		{
    			$this->val_errors->log_error(ERROR_MOVIMIENTOS, sprintf("El número de días de actividad del establecimiento (%d días) no es compatible con la fecha de cese de actividad (%02d/%02d/%04d).",$this->dias_abierto,$hoy,$this->mes,$this->ano));
    			$hay_error = true;
    		}
    		
    		foreach($this->mov_por_ut as $id_ut => $movs)
    		{
   		    	foreach($movs->movimientos as $dia => $esp)
   				{
   					if ($dia > $hoy)
   					{
   						if (($esp->entradas!=0) || ($esp->salidas!=0) || ($esp->pernoctaciones!=0))
   						{
	   						$this->val_errors->log_error(ERROR_MOVIMIENTOS, sprintf("Para la unidad territorial %s: Existen movimientos posteriores a la fecha de cese de actividad (%02d/%02d/%04d).", $lookup_paises[$id_ut],$hoy,$this->mes,$this->ano));
	   						$hay_error = true;
	   						break;
   						}
   					}
   					elseif (($dia==$hoy) && (($esp->entradas!=0) || ($esp->pernoctaciones!=0)))
   					{
   						$this->val_errors->log_error(ERROR_MOVIMIENTOS, sprintf("Para la unidad territorial %s: Existen movimientos posteriores a la fecha de cese de actividad (%02d/%02d/%04d).", $lookup_paises[$id_ut],$hoy,$this->mes,$this->ano));
   						$hay_error = true;
   						break;
   					}
   				}
     		}
     		
     		foreach($this->habitaciones as $dia => $hab)
     		{
     			if (($dia >= $hoy) && (($hab->uso_doble!=0) || ($hab->uso_individual!=0) || ($hab->supletorias!=0) || ($hab->otras!=0)))
     			{
     				$this->val_errors->log_error(ERROR_MOVIMIENTOS, sprintf("Existen habitaciones ocupadas posteriores a la fecha de cese de actividad (%02d/%02d/%04d).",$hoy,$this->mes,$this->ano));
     				$hay_error = true;
     				break;
     			}
     		}
    	}
    	return $hay_error;
    }
    
    /**
     * REGLA ???(nueva): Las pernoctaciones no pueden desaparecer a mitad de periodo. O se anulan mediante salidas o deben aparecer hasta el último día del cuestionario (final de mes ó final de actividad).
     * PRERREQUISITO: Esta validación sólo debe realizarse en el envío final del cuestionario.
     */
    public function valida_pernoctaciones_no_arrastradas($lookup_paises)
    {
    	$fecha_helper = new DateHelper();
    	$ultimo_dia = ($this->mes == $fecha_helper->mes_sistema && $this->ano == $fecha_helper->anyo_sistema) ? $fecha_helper->dia_sistema : cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->ano);
    	foreach($this->mov_por_ut as $id_ut => $movs)
    	{
    		$ne=count($movs->movimientos);
    		$dia=array_keys($movs->movimientos)[$ne-1];
    		if($dia < $ultimo_dia)
    		{
    			if($movs->movimientos[$dia]->pernoctaciones!=0)
    			{
    				$this->val_errors->log_error(ERROR_MOVIMIENTOS, sprintf( "Para la unidad territorial %s: La información de pernoctaciones está incompleta.", $lookup_paises[$id_ut]));
    			}
    		}
    	}
    	
    	return (!$this->contiene_errores()) ? false : $this->val_errors;
   	}
        
    public function valida_cruzadas($es_hotel, $op_es_guardar, $lookup_paises, $permitirCaso001)
    {
    	/// REGLAS: 210
    	$this->valida_faltan_datos_habitaciones($es_hotel);
    	/// REGLAS: 109, 110, 111
    	$this->valida_habitaciones_ocupadas_existen_pernoctaciones($es_hotel);
    	$this->valida_habitaciones_ocupadas_menorigual_pernoctaciones($es_hotel);
    	/// La regla 111 no se realiza para validacion de apartamentos.
    	if ($es_hotel)
    		$this->valida_habitaciones_ocupadas_sin_dobles($es_hotel);
    	
    	/// REGLAS: 211, 214
    	$this->valida_faltan_datos_movimientos();
    	$this->valida_hay_datos_precios();
    	$this->valida_falta_adr_o_revpar();
	    $this->valida_falta_desglose_precios($permitirCaso001);
    	
    	/// REGLAS: 215, 216, 222
    	$this->valida_falta_desglose_porcentaje_numero_precios($es_hotel);
    	$this->valida_personal_haydatos();
    	$this->valida_suma_habs_precios_no_coincide_total_habitaciones($es_hotel);
    	
    	// Se valida que no hay actividad después de la fecha de cese de actividad.
    	if(!$op_es_guardar)
    		$this->valida_cese_actividad($lookup_paises);
    
    	if(!$this->contiene_errores())
    		return false;
    
    	return $this->val_errors;
    }
    
    /**
     * Valida que cumple reglas de avisos
     * PRE: Deben estar cargados todos los datos de movimientos.
     * @return boolean
     */
    public function valida_avisos($lookup_paises)
    {
    	/// Nueva regla para comprobar presentes mes actual y anterior.
    	$this->valida_presentes_comienzo($lookup_paises);
    	
    	/// REGLAS: 10, 12, 13, 14
    	$this->valida_pernoctaciones_sin_entradas_salidas();
    	$this->valida_estancia_media_supera_20dias($lookup_paises);
    	$this->valida_salidas_inferiores_presentes_principio_mes($lookup_paises);
    	$this->valida_estancia_media_supera_30dias($lookup_paises);

    	if(!$this->contiene_errores())
    		return false;
    
    	return $this->val_errors;
    }
}
?>