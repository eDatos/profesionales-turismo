<?php

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../audit/AuditLog.class.php");
require_once(__DIR__."/../../lib/DateHelper.class.php");
require_once(__DIR__."/../Establecimiento.class.php");
require_once(__DIR__."/AlojaDao.class.php");
require_once(__DIR__."/AlojaTempDao.class.php");
require_once(__DIR__."/AlojaESP.class.php");
require_once(__DIR__."/../../lib/email.class.php");

/**
 * Operaciones comunes al cuestionario de alojamiento.
 * 
 */
class AlojaController
{
	
	/**
	 * Carga los datos de la encuesta en curso o los datos correspondientes al mes y año indicados.
	 * @param establecimiento $establecimiento
	 * @param id_usuario $user_id
	 * @param mes_encuesta $mes_encuesta
	 * @param ano_encuesta $ano_encuesta
	 */
    public function cargar_encuesta($establecimiento, $user_id, $mes_encuesta = NULL, $ano_encuesta = NULL, $rdonly = FALSE)
	{
		$dao = new AlojaDao();		
		/// Cargar estado de la encuesta de alojamiento actualmente en curso para comprobar si es del mes y año pedidos.
		$aloja_estado = $this->cargar_estado_encuesta_alojamiento($establecimiento, $user_id);
		$aloja_enc = $aloja_estado['encuesta'];
		
		/// Si no estan definidos por parametros, se definen automaticamente segun el calculo hecho en cargar_estado_encuesta_alojamiento.
		if ($mes_encuesta==NULL && $ano_encuesta==NULL)
		{
			$mes_encuesta = $aloja_enc->mes;
			$ano_encuesta = $aloja_enc->ano;
		}		
		
		///... 1. si la fecha mes/ano pasada por param coincide con la fecha de $aloja_enc, (significa que es el cuestionario en curso.
		///... 2. el cuestionario está abierto.	
		/// Si no se cumple para el USER, entra en modo READONLY (esta viendo un cuestionario anterior).
		$solo_lectura = ($rdonly || $aloja_enc->mes != $mes_encuesta || $aloja_enc->ano != $ano_encuesta || $aloja_enc->esta_cerrado());
		$aloja_estado['es_encuesta_anterior'] = $solo_lectura;
		
		/// Cargar los datos del cuestionario para el mes y año
		if ($aloja_estado['es_encuesta_anterior'])
		{
			$aloja_enc = $dao->cargar_registro_cuestionario($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
			$aloja_estado['encuesta'] = $aloja_enc;
			
			if ($aloja_enc == null)
			{
				$aloja_estado['dias_rellenos'] = null;
				$aloja_estado['selected_uts'] = null;
				/// No existe cuestionario para esa fecha, abortar la operacion ($aloja_estado['encuesta'] sera nulo)
				return $aloja_estado;
			}
		
			/// Establecer los paises de la encuestas en $selected_UT
			/// aserto: ($aloja_enc != null)
			$selected_UT = $dao->cargar_uts_rellenados($establecimiento->id_establecimiento, $aloja_enc->mes, $aloja_enc->ano);
			$aloja_estado['selected_uts'] = $selected_UT;
			
			/// B: Cargar los dias rellenados
			$aloja_estado['dias_rellenos'] = $dao->get_dias_rellenos($aloja_estado['encuesta']->id);
		}
		
		/// Carga los datos del establecimiento valido en el mes y ano de la encuesta.
		/// el criterio seguido es que los datos a dia 1 del mes de la encuesta son los correctos.
		$fecha_efectiva_datos = DateHelper::parseDate(sprintf("%02d-%02d-%04d", 1, $aloja_enc->mes, $aloja_enc->ano));
		if ($fecha_efectiva_datos < $establecimiento->fecha_alta)
		{
			// Carga los datos del establecimiento valido en el mes y ano de la encuesta.
			$est_id = $establecimiento->id_establecimiento;
			$est2 = new Establecimiento();
			$est2->cargar_por_fecha($est_id, $fecha_efectiva_datos);
			$aloja_estado['datos_estab'] = $est2;
		}
		else 
		{
			/// Los datos actuales son valido para el mes/año de la encuesta.
			$aloja_estado['datos_estab'] = $establecimiento;
		}
		
		/// POST-CONDICION: Cargado registro aloja_enc con los datos del mes/año, un flag para indicar RW/readonly y el establecimiento cargado para la fecha del cuestionario.		
		/// Obtener los datos de las habitaciones y convertirlos a JSON
		$aloja_estado['hab'] = $dao->cargar_habitaciones($aloja_enc->id);	
		/// Obtener los datos de personal y precios y convertirlos a JSON
		$aloja_estado['pers_prec'] = $dao->cargar_personal_precios($aloja_enc->id);	
		return $aloja_estado;
	}
	
	/**
	 * Obtiene la encuesta de alojamiento en curso.
	 * @param unknown_type $establecimiento
	 * @param unknown_type $user_id
	 */
	public function cargar_estado_encuesta_alojamiento($estab, $user_id)
	{
		/// 0: Calcula que encuesta está en curso según la fecha de plazo y el estado de la misma.
		 
		/// Calcula el mes de la encuesta y despues carga los datos de la encuesta.
		// 1. Solo pueden ser dos meses, el mes actual o el mes anterior.
		// 2. Puede ser el mes anterior si:
		//		- El establecimiento no ha cerrado ya la encuesta.
		//		- Con la encuesta abierta, si se está dentro del plazo.
		
		$dao = new AlojaDao();
		
		$dt = new DateHelper();
		$dia_actual = $dt->dia_sistema;
		$mes_actual = $dt->mes_sistema;
		$ano_actual = $dt->anyo_sistema;
		 
		list($mes_anterior, $ano_anterior) = DateHelper::mes_anterior($mes_actual, $ano_actual);
		 
		$aloja_cuest = $dao->cargar_registro_cuestionario($estab->id_establecimiento, $mes_anterior, $ano_anterior);
		
		$es_encuesta_mes_anterior = false;
		 
		if ($aloja_cuest == null || !$aloja_cuest->esta_cerrado())
		{
			/// Comprobar si se esta en plazo del mes anterior todavia, pero con el tipo de establecimiento efectivo el mes anterior.
			//Cargar el tipo de establecimiento para el mes_anterior (evitar hacerlo si los datos disponibles son iguales.
			/// Se considera que la fecha efectiva para el mes anterior son los datos del día 1.
			$fecha_efectiva_datos = DateHelper::parseDate(sprintf("%02d-%02d-%04d", 1, $mes_anterior, $ano_anterior));
			if ($estab->fecha_alta <= $fecha_efectiva_datos)
			{
				$est2 = $estab;
			}
			else
			{
				$est2 = new Establecimiento();
				$est2->cargar_por_fecha($estab->id_establecimiento, $fecha_efectiva_datos);
			}
	
			//2. Comprobar si dentro de plazo.
			
			$dia_plazo = $dao->cargar_dia_plazo($estab->id_establecimiento,$est2->get_grupo(),$mes_anterior,$ano_anterior);
			if ($dia_actual <= $dia_plazo)
			{
				$es_encuesta_mes_anterior = true;
			}
		}
		 
		$aloja_estado = array();
				
		/// A: Cargar o inicializar la encuesta en curso
		if ($es_encuesta_mes_anterior)
		{
			/// Inicializar un nuevo cuestionario si no existe. 
			if ($aloja_cuest == null)
			{
				$aloja_cuest = $this->crear_nuevo_cuestionario($mes_anterior, $ano_anterior, $estab->id_establecimiento, $user_id, TIPO_CARGA_WEB, null, false);
			}
			// Se puede rellenar todavia la encuesta anterior
			$aloja_estado['plazo'] = new DateTime();
			$aloja_estado['plazo']->setDate($ano_actual, $mes_actual, $dia_plazo);
			$aloja_estado['encuesta'] =  $aloja_cuest;
		}
		else 
		{
			$aloja_cuest = $dao->cargar_registro_cuestionario($estab->id_establecimiento, $mes_actual, $ano_actual);
			if ($aloja_cuest == null)
			{
				$aloja_cuest = $this->crear_nuevo_cuestionario($mes_actual, $ano_actual, $estab->id_establecimiento, $user_id, TIPO_CARGA_WEB, null, false);	
			}
			
			/// El mes anterior esta ya cerrado, solo puede ser el mes actual.
			/// o El mes anterior esta ya fuera de plazo, solo puede ser el mes actual.
			$dia_plazo = $dao->cargar_dia_plazo($estab->id_establecimiento,$estab->get_grupo(),$mes_actual,$ano_actual);
			list($mes_siguiente, $ano_siguiente) = DateHelper::mes_siguiente($mes_actual, $ano_actual);
			$aloja_estado['plazo'] = new DateTime();
			$aloja_estado['plazo']->setDate($ano_siguiente, $mes_siguiente, $dia_plazo);
			
			$aloja_estado['encuesta']  = $aloja_cuest;
		}
		 
		/// B: Cargar los dias rellenados
		if (!$aloja_estado['encuesta']->es_nuevo())
			$aloja_estado['dias_rellenos'] = $dao->get_dias_rellenos($aloja_estado['encuesta']->id);
		else 
			$aloja_estado['dias_rellenos'] = null;
		return $aloja_estado;
	}
	
	
	/**
	 * Crea un nuevo cuestionario, guardando en la BBDD para obtener un id.
	 * @param unknown_type $estab
	 * @param unknown_type $user_id
	 * @param unknown_type $tipo_carga
	 * @param unknown_type $guardar TRUE para guardar antes de devolver el nuevo cuestionario, false para no hacerlo (se devolvera sin Id asignado).
	 */
	public function crear_nuevo_cuestionario($mes, $ano, $est_id, $user_id, $tipo_carga, $modo_porcentaje, $guardar)
	{	
		$dao = new AlojaDao();
		$cuestionario = new AlojaCuestionario($mes, $ano, $est_id, $user_id);
		
		$cuestionario->tipo_carga = $tipo_carga;
		$cuestionario->codigo_registro = $dao->obtener_codigo_registro($est_id);
		$cuestionario->fecha_recepcion = new DateTime('now');
		
		//  1. Obtener ultimo modo introduccion y ultimo modo cumplimentacion
		list($cuestionario->modo_introduccion, $cuestionario->modo_cumplimentado, $cuestionario->modo_porcentaje) = $dao->cargar_ultimos_modos($est_id, $mes, $ano);
		
		if ($modo_porcentaje != null)
		{
			$cuestionario->modo_porcentaje = $modo_porcentaje;
		}
		
		// 2. Obtener el numero de dias abierto por defecto
		$cuestionario->dias_abierto = cal_days_in_month(CAL_GREGORIAN, $cuestionario->mes, $cuestionario->ano);
		
		if ($guardar)
		{	
			$dao->guardar_cuestionario($cuestionario);
		}
		return $cuestionario;
	}
	
	/**
	 * Crea un nuevo cuestionario vacío para el mes y año indicados sin guardar en la BDD.
	 * Si el cuestionario ya existe para el mes y año, se devuelve false.
	 * @param unknown $establecimiento
	 * @param unknown $user_id
	 * @param unknown $mes_encuesta NULL si se quiere crear un cuestionario vacío para la encuesta en curso
	 * @param unknown $ano_encuesta NULL si se quiere crear un cuestionario vacío para la encuesta en curso
	 * @return boolean|AlojaCuestionario
	 */
	public function crear_nuevo_cuestionario_vacio($establecimiento, $user_id, $mes_encuesta = NULL, $ano_encuesta = NULL)
	{
	    $dao = new AlojaDao();
	    
	    if (($mes_encuesta==NULL) || ($ano_encuesta==NULL))
	    {
	        // Crear un cuestionario vacío para la encuesta en curso.
	        // Primero hemos de determinar que encuesta es la actual.
	        // Si la encuesta del mes anterior no existe aún o no está cerrada y aún estamos en plazo de entrega de la del mes anterior ==> la actual es la del mes anterior.
	        // En otro caso, la actual es la del mes en curso.
	        $dt = new DateHelper();
	        $dia_actual = $dt->dia_sistema;
	        $mes_encuesta = $dt->mes_sistema;
	        $ano_encuesta = $dt->anyo_sistema;
	        list($mes_anterior, $ano_anterior) = DateHelper::mes_anterior($mes_encuesta, $ano_encuesta);
	        $aloja_cuest = $dao->cargar_registro_cuestionario($establecimiento->id_establecimiento, $mes_anterior, $ano_anterior);
	        $es_encuesta_mes_anterior = false;
	        if ($aloja_cuest == null || !$aloja_cuest->esta_cerrado())
	        {
	            // Puede que la encuenta en curso sea la del mes anterior...
	            $fecha_efectiva_datos = DateHelper::parseDate(sprintf("%02d-%02d-%04d", 1, $mes_anterior, $ano_anterior));
	            if ($establecimiento->fecha_alta <= $fecha_efectiva_datos)
	            {
	                $est2 = $establecimiento;
	            }
	            else
	            {
	                $est2 = new Establecimiento();
	                $est2->cargar_por_fecha($establecimiento->id_establecimiento, $fecha_efectiva_datos);
	            }
	            
	            //2. Comprobar si dentro de plazo.           
	            $dia_plazo = $dao->cargar_dia_plazo($establecimiento->id_establecimiento,$est2->get_grupo(),$mes_anterior,$ano_anterior);
	            if ($dia_actual <= $dia_plazo)
	            {
	                $es_encuesta_mes_anterior = true;
	                $mes_encuesta=$mes_anterior;
	                $ano_encuesta=$ano_anterior;
	            }
	        }
    	    if ($es_encuesta_mes_anterior==false)
    	    {
    	        $aloja_cuest = $dao->cargar_registro_cuestionario($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
    	    }
	    }
	    else
	    {
	        // Crear un cuestionario vacío para la encuesta del mes y año especificados.
	        $aloja_cuest = $dao->cargar_registro_cuestionario($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
	    }
	    
	    if ($aloja_cuest != null)
	    {
	        // Error => el cuestionario ya existe (abierto o ya cerrado).
	        return false;
	    }
	    
	    return $this->crear_nuevo_cuestionario($mes_encuesta, $ano_encuesta, $establecimiento->id_establecimiento, $user_id, TIPO_CARGA_WEB, null, false);
	}
	
	/**
	 * Inicializa los presentes a comienzo de mes a partir de los presentes a mes anterior. También inicializa todos los movimientos 
	 * de ut de las mismas ut que habia en el mes anterior y todas las pernoctaciones de los dias abiertos del cuestionario. 
	 * @param AlojaCuestionario $cuestionario
	 * @param unknown_type $est_id identificador del establecimiento.
	 */
	public function inicializar_cuestionario_pres_esp($est_id, AlojaCuestionario $cuestionario)
	{
		$dao = new AlojaDao();
		
		/// Se calcula el número de días abierto del cuestionario
		$fecha_helper = new DateHelper();
		
		if($cuestionario->mes == $fecha_helper->mes_sistema && $cuestionario->ano == $fecha_helper->anyo_sistema)
			$dias_mostrar_formulario = $fecha_helper->dia_sistema;
		else
			$dias_mostrar_formulario = cal_days_in_month(CAL_GREGORIAN, $cuestionario->mes, $cuestionario->ano);
		
		
		//Rellenar los presentes de comienzo de mes y los movimientos en EPS
		//1. Obtener los presentes a fin de mes anterior para todas las UTs con presentes a mes anterior
		//2. Guardar presentes a comienzo de mes con los presentes
		//3. Arrastrar los EPS
		$presentes_anterior = $dao->cargar_presentes_fin_mes_uts($est_id, $cuestionario->mes, $cuestionario->ano);
		$datos = array();
		foreach ($presentes_anterior as $id_ut => $presentes) 
		{
			$datos[$id_ut] = new AlojaUTMovimientos();
			$datos[$id_ut]->presentes_comienzo_mes = $presentes;
				
			//for($dia=1;$dia<=$cuestionario->dias_abierto;$dia++)
			for($dia=1;$dia<=$dias_mostrar_formulario;$dia++)
			{
				$aloja_esp = new AlojaESP();
				$aloja_esp->pernoctaciones=$presentes;
				$aloja_esp->entradas=0;
				$aloja_esp->salidas=0;
				$datos[$id_ut]->movimientos[$dia]=$aloja_esp;
			}
		}
		$cuestionario->mov_por_ut = $datos;
	}
	
	
	/** 
	 * Guarda el cuestionario y todos sus detalles.
	 * @param AlojaCuestionario $cuestionario
	 */
	public function guardar_cuestionario(AlojaCuestionario $cuestionario)
	{
		$dao = new AlojaDao();

		$cuestionario->hay_datos_movimientos();
		$cuestionario->hay_datos_habitaciones();
		$cuestionario->hay_datos_precios();
		$cuestionario->hay_datos_personal();
		
		// Registro maestro
		$ok = $dao->guardar_cuestionario($cuestionario);
		if (!$ok)
			return false;
		
		// guardar ha puesto un id al cuestionario
		$id = $cuestionario->id;
		
		// Registro de detalle movimientos
		if ($cuestionario->mov_por_ut != null)
		{
			foreach($cuestionario->mov_por_ut as $id_ut => $ut_mov)
			{
				$dao->guardar_esp_ut($id, $id_ut, $ut_mov);
				
				// Calcular presentes_fin_mes
				$presentes_fin_mes = $ut_mov->get_presentes_ultimo_dia();
				$dao->guardar_presentes_mes($id, $id_ut, $ut_mov->presentes_comienzo_mes, $presentes_fin_mes);
			}
		}
		
		// Registro de detalle habitaciones
		if ($cuestionario->habitaciones != null)
			$dao->guardar_habitaciones($id, $cuestionario->habitaciones);
		
		// Registro de detalles personal y precios
		if ($cuestionario->personal != null || $cuestionario->precios != null)
			$dao->guardar_personal_precios($id, $cuestionario->personal, $cuestionario->precios);
		
		return true;
	}
	
	/**
	 * Cierra un cuestionario (un cuestionario cerrado no puede editarse).
	 * @param AlojaCuestionario $cuestionario
	 */
	public function cerrar_cuestionario(AlojaCuestionario $cuestionario, $permitirCerrarActual = false)
	{	
		if ($cuestionario->esta_cerrado())
		{
			$res = "El cuestionario ya está cerrado.";
			return $res;
		}
		$dao = new AlojaDao();
		
		// Solo se permiten cerrar los cuestionarios con estado de validacion = EV_VALIDADO_COMPLETO o EV_VALIDADO_CON_AVISOS
		$estado_validacion = $dao->obtener_estado_validacion($cuestionario->id);
		if ($estado_validacion === false || ($estado_validacion != EV_VALIDADO_COMPLETO && $estado_validacion != EV_VALIDADO_CON_AVISOS))
		{
			$res = "El estado de validación del cuestionario no permite cerrarlo.";
			return $res;
		}
		
		// No se permite cerrar un cuestionario del mes en curso
		if(!$permitirCerrarActual)
		{
			/*
			/// COVID-19: Comentado para permitir el cierre de una encuesta en el mes en curso.
			 * REVISANDO...
			*/			
			$dt = new DateHelper();
			if (($cuestionario->mes == $dt->mes_sistema) && ($cuestionario->ano == $dt->anyo_sistema))
			{
				$res = "No se puede cerrar la encuesta para el mes en curso. Si es un envío de datos diario estos ya se han guardado.";
				return $res;
			}
			/**/			
		}
		
		// Cerrar el cuestionario poniendo una fecha de cierre.
		$fecha_cierre = new DateTime('now');
		
		$ok = $dao->cerrar_cuestionario($cuestionario->id, $fecha_cierre);
		if (!$ok)
		{
			$res = "No se ha podido realizar la operación de cierre sobre el cuestionario dado. Ningún registro se ha visto afectado.";
			return $res;
		}
		
		return true;
	}
	
	/**
	 * Abre un cuestionario que estaba cerrado (un cuestionario cerrado no puede editarse).
	 * @param AlojaCuestionario $enc
	 */
	public function abrir_cuestionario(AlojaCuestionario $enc)
	{
		$dao = new AlojaDao();
		if ($enc->esta_cerrado())
		{
			$ok = $dao->abrir_cuestionario($enc->id);
			if (!$ok)
			{
				$res = "No se ha podido realizar la operación de apertura sobre el cuestionario dado. Ningún registro se ha visto afectado";
				return $res;
			}
		}
		else
		{
			$res = "El cuestionario ya está abierto";
			return $res;
		}
		return true;		
	}
	
	/**
	 * Elimina todos los datos del cuestionario de alojamiento indicado.
	 * @param AlojaCuestionario $enc
	 */
	public function borrar_cuestionario(AlojaCuestionario $enc)
	{
		$dao = new AlojaDao();
		$ok = $dao->eliminar_cuestionario($enc->id);
		if (!$ok)
		{
			return "No se ha podido realizar la operación de eliminación sobre el cuestionario dado. Ningún registro se ha visto afectado.";
		}
		// Para que se considere como nuevo.	
		$enc->id = null;
		return true;
	}
	
	/**
	 * Establecer el estado de un cuestionario
	 * @param AlojaCuestionario $cuestionario
	 * @param int $nuevo_estado Un valor de la enumeracion EV_xxx (declarada en AlojaDao.class.php)
	 */
	private function establecer_estado_validacion(AlojaCuestionario $cuestionario, $nuevo_estado)
	{
		if ($cuestionario->esta_cerrado())
		{
			$res = "El cuestionario ya está cerrado.";
				
			return $res;
		}
	
		$dao = new AlojaDao();
		$ok = $dao->establecer_estado_validacion($cuestionario->id, $nuevo_estado);
		if (!$ok)
		{
			$res = "No se ha podido realizar la operación de cambio de estado al cuestionario dado. Ningún registro se ha visto afectado.";
			return $res;
		}
	
		return true;
	}	
	
// 	public function valida_cuestionario(AlojaCuestionario $aloja_cuest)
// 	{
// 		//**** VALIDACION ***/
// 		// Carga los datos del establecimiento para la fecha del cuestionario.
// 		$fecha_efectiva_datos = DateHelper::parseDate(sprintf("%02d-%02d-%04d", 1, $aloja_cuest->mes, $aloja_cuest->ano));
// 		$est_mes_encuesta = new Establecimiento();
// 		$est_mes_encuesta->cargar_por_fecha($aloja_cuest->estabid_declarado, $fecha_efectiva_datos);
		
// 		// Valida los datos cargados.
// 		$dao = new AlojaDao();
// 		$lookup_paises = $dao->cargar_lookup_paises();
// 		$aloja_cuest->valida_movimientos($lookup_paises);
// 		$aloja_cuest->valida_habitaciones($est_mes_encuesta->max_plazas_por_dia(), $est_mes_encuesta->es_hotel());
// 		$aloja_cuest->valida_precios();
		
// 		return !$aloja_cuest->tiene_errores();
// 	}

	/**
	 * Realiza la validacion de los datos del cuestionario para las operaciones de guardar y enviar,
	 * guarda los datos si se cumplen las reglas de validacion internas,
	 * y valida cruzadas si la operación es la de enviar ($op_es_guardar == false).
	 * 
	 * La operacion de enviar realiza más validaciones que las que realiza guardar.
	 * 
	 * Establece el estado del cuestionario en la bbdd a uno de los valores definidos en AlojaDao.
	 * Devuelve un array con dos posiciones: 
	 * 		- [0] una colección AlojaErrorCollection si hay errores o false si no los hay.
	 * 		- [1] false si no se ha guardado el cuestionario, true si se ha guardado.
	 * @param AlojaCuestionario $aloja_cuest Cuestionario a validar y guardar, con datos parciales.
	 * @param boolean $op_es_guardar Verdadero para indicar que se esta haciendo una operacion de guardar, Falso para indicar que la operacion es enviar.
	 */
	public function valida_y_guardar(AlojaCuestionario $aloja_cuest, $op_es_guardar, $user_id, $permitirCaso001)
	{
		//AQUI ESTADO IMPLICITO: EDITADO
		
		// Los avisos de excesos los vamos a generar "fuera de banda"...
		$aloja_cuest->avisos_excesos=new AlojaErrorCollection();
		
		// Carga los datos del establecimiento para la fecha del cuestionario.
		$fecha_efectiva_datos = DateHelper::parseDate(sprintf("%02d-%02d-%04d", 1, $aloja_cuest->mes, $aloja_cuest->ano));
		$est_mes_encuesta = new Establecimiento();
		$est_mes_encuesta->cargar_por_fecha($aloja_cuest->estabid_declarado, $fecha_efectiva_datos);
		
		// Carga lookup de paises para mensajes de error.
		$dao = new AlojaDao();
		$lookup_paises = $dao->cargar_lookup_paises();
		
		$errores =$this->valida_previa($aloja_cuest, $est_mes_encuesta, $lookup_paises, $op_es_guardar, $user_id, $permitirCaso001);
		if($errores[1]===false)
			return $errores;
				
		/// 2. Guardar cada uno de los items recibidos.
		$guardado_ok =  $this->guardar_cuestionario($aloja_cuest);
		
		if (!$guardado_ok)
		{
			// Devolver error de "no se pudo guardar...".
			$errs = new AlojaErrorCollection();
			$errs->log_error(ERROR_GENERAL, "No se ha podido guardar el cuestionario.");

			@AuditLog::log($user_id, $aloja_cuest->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, FAILED, NULL, array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
			// false indica que no se ha guardado.
			return array($errs, false);
		}
		
		return $this->valida_final_y_guardar($aloja_cuest, $est_mes_encuesta, $lookup_paises, $op_es_guardar, $user_id, $permitirCaso001);
	}

	public function valida_previa(AlojaCuestionario $aloja_cuest, Establecimiento $est_mes_encuesta, $lookup_paises, $op_es_guardar, $user_id, $permitirCaso001)
	{
		//AQUI ESTADO IMPLICITO: EDITADO
	
		/// Proceso de validacion de guardar
		$errores = $aloja_cuest->valida_internas($lookup_paises, $est_mes_encuesta->max_plazas_por_dia(), $est_mes_encuesta->num_habitaciones, $est_mes_encuesta->es_hotel(), $op_es_guardar, $permitirCaso001);
		if ($errores)
		{
		    @AuditLog::log($user_id, $aloja_cuest->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, FAILED, $errores->errores, array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
			//AQUI ESTADO IMPLICITO: EDITADO, en BBDD el estado que hubiera (no ha habido cambios persistentes).
			// Devolver error validacion.
			// false indica que no se ha guardado.
			return array($errores, false);
		}
		
		return array($errores, true);
	}
	
	public function valida_final_y_guardar(AlojaCuestionario $aloja_cuest, Establecimiento $est_mes_encuesta, $lookup_paises, $op_es_guardar, $user_id, $permitirCaso001)
	{
		//AQUI ESTADO IMPLICITO: EDITADO
	
		// El cuestionario completo ya ha sido guardado en BD.
	
		if ($aloja_cuest->hay_datos_precios() && $op_es_guardar)
		{
			$errs = $aloja_cuest->valida_avisos_alguardar($est_mes_encuesta->es_hotel(), $permitirCaso001);
			if ($errs !== false)
			{
				// Se ha guardado, pero con errores
			    @AuditLog::log($user_id, $aloja_cuest->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, FAILED, $errs->errores,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
				// true indica que se ha guardado.
				return array($errs, true);
			}
		}
	
		/// Comprobar si hay datos para validacion cruzadas
		if (!$aloja_cuest->hay_datos_mov_hab_precios())
		{
			///Marcar en BBDD "CUESTIONARIO INCOMPLETO"
			$this->establecer_estado_validacion($aloja_cuest, EV_CUESTIONARIO_INCOMPLETO);
			if ($op_es_guardar)
			{
			    @AuditLog::log($user_id, $aloja_cuest->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
				return array(false, true);
			}
		}
	
		$errores = $aloja_cuest->valida_cruzadas($est_mes_encuesta->es_hotel(), $op_es_guardar, $lookup_paises, $permitirCaso001);
		if ($errores)
		{
			$this->establecer_estado_validacion($aloja_cuest, EV_CUESTIONARIO_COMPLETO);
			@AuditLog::log($user_id, $aloja_cuest->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, FAILED, $errores->errores,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
			return array($errores, true);
		}
		else
		{
			// Marcar en BBDD "VALIDADO CRUZADAS"
			$this->establecer_estado_validacion($aloja_cuest, EV_VALIDADO_CRUZADAS);
			if ($op_es_guardar)
			{
			    @AuditLog::log($user_id, $aloja_cuest->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
				return array(false, true);
			}
		}
	
		// Cargar todo el cuestionario desde la BBDD para la validación de avisos
		$dao = new AlojaDao();
		$aloja_cuest->mov_por_ut = $dao->cargar_todos_esp($aloja_cuest->id);
	
		$avisos = $aloja_cuest->valida_avisos($lookup_paises);
		if ((!$op_es_guardar) && ($aloja_cuest->es_cuestionario_parcial()==false))
			$avisos = $aloja_cuest->valida_pernoctaciones_no_arrastradas($lookup_paises);
		if($avisos === false)
		{
			///Marcar en BBDD "VALIDADO COMPLETO"
			$this->establecer_estado_validacion($aloja_cuest, EV_VALIDADO_COMPLETO);
		}
		else
		{
			// Marcar en BBDD "VALIDADO CON AVISOS"
			$this->establecer_estado_validacion($aloja_cuest, EV_VALIDADO_CON_AVISOS);
		}
		@AuditLog::log($user_id, $aloja_cuest->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, SUCCESSFUL, ($avisos!==false) ? $avisos->errores:null,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
		return array($avisos, true);
	}
	
	/**
	 * Devuelve el contenido XML almacenado en la base de datos para el establecimiento, mes y año dados.
	 * 
	 * Devuelve nulo si no hay cuestionario para ese mes y año y establecimiento, o hay cuestionario pero no datos XML.
	 * @param unknown_type $estab_id Id del establecimiento.
	 * @param unknown_type $mes Mes de los datos a descargar (1..12).
	 * @param unknown_type $ano Año de los datos a descargar (4 digitos).
	 */
	public function descargar_xml($estab_id, $mes, $ano)
	{
		// Comprobar si hay datos actualmente.
		$dao = new AlojaDao();
		$aloja_cuest = $dao->cargar_registro_cuestionario($estab_id, $mes, $ano);
		
		/// No hay cuestionario para el mes y año indicado, por lo tanto, no puede haber xml subido para ese cuestionario.
		if ($aloja_cuest == null)
			return null;
		
		$dao_xml = new AlojaXmlDao();
		return $dao_xml->cargar_xml($aloja_cuest->id);
	}
	
	/**
	 * Realiza el proceso de subida de un archivo XML:
	 *    1. Comprobar que la fecha de referencia del archivo es la que se puede cargar (coincide con la fecha de la encuesta abierta).
	 *    2. Validar el XML contra el XSD (content/alojamiento.xsd).
	 *    3. Guardar el xml, aunque no sea valido en XSD.
	 *    4. Generar el cuestionario a partir del xml y validar los datos
	 *    5. Guardar los datos de la encuesta (validos o no).
	 *    6. Comprobar si hay cambios en datos de cabecera y guardarlos para notificarlos.
	 *    
	 * Se sobreescriben los datos del cuestionario si ya existían para el mes y año del xml dados.
	 *  
	 * @param AlojaXmlReader $x
	 * @param unknown_type $estab_id
	 * @param unknown_type $user_id
	 * @param unknown_type $mes_subido
	 * @param unknown_type $ano_subido
	 */
	public function subir_xml(AlojaXmlReader $x, $estab_id, $user_id, $mes_subido, $ano_subido, $ignorar_avisos = false, $cuestionario_parcial=false)
	{	
		/// 1. Comprobar que la fecha de referencia del archivo es la que se puede cargar (coincide con la fecha de la encuesta abierta).
		$fecha_xml = $x->obtenerFechaReferencia();
		
		$mes_correcto = $mes_subido == $fecha_xml['mes'];
		$ano_correcto = $ano_subido == $fecha_xml['ano'];
		if (!($mes_correcto && $ano_correcto))
		{
			$errs = new AlojaErrorCollection();
			if (!$mes_correcto) $errs->log_error(ERROR_GENERAL, "Código de mes distinto del esperado.");
			if (!$ano_correcto) $errs->log_error(ERROR_GENERAL, "Código de año distinto del esperado.");
			/// En caso de error al no ser de la fecha correcta, muestra los errores que ha ocurrido terminando el proceso.
			// EL ultimo false indica que no se ha guardado el cuestionario.
			return array(false, $errs, false);
		}
		
		/// 2. Validar el XML contra el XSD.
		$ok_esvalido_xsd = $x->validarEsquemaXml(ALOJA_XML_ESQUEMA);
		
		/// 3. Guardar el xml, aunque no sea valido en XSD.
		$dao_xml = new AlojaXmlDao();
		
		// Comprobar si hay datos actualmente.
		$dao = new AlojaDao();
		$aloja_cuest = $dao->cargar_registro_cuestionario($estab_id, $mes_subido, $ano_subido);
		
		/// Si se sube el XML, se sobreescriben los datos existentes
		if ($aloja_cuest != null && !$aloja_cuest->es_nuevo())
		{
			$id_cuestionario=$aloja_cuest->id;
			$this->borrar_cuestionario($aloja_cuest);
			
			// En caso de existir datos temporales también debemos borrarlos.
			$daoTemp = new AlojaTempDao();
			$daoTemp->eliminar_cuestionario_temp($id_cuestionario);
		}
		
		/// Crear el nuevo, guardando en la BBDD.
		$aloja_cuest = $this->crear_nuevo_cuestionario($mes_subido, $ano_subido, $estab_id, $user_id, TIPO_CARGA_XML, MODO_PORC_PORC, true);
		$aloja_cuest->cuestionario_parcial = $cuestionario_parcial;
		$aloja_cuest->ceseActividad=false;
		global $ceseActividad;
		if(isset($ceseActividad))
			$aloja_cuest->ceseActividad=($ceseActividad=='1');
		
		
		///ASERTO: cuest.id != null. (debe haber registro en la tabla maestra d TB_ALOJA_CUESTIONARIO para subir el XML, ya que éste se almacena en una tabla detalle.
		$fecha_grabacion = date("Y-m-d H:i:s");
		$err_xml_guardado=$dao_xml->guardar_xml($aloja_cuest->id, $fecha_grabacion,$ok_esvalido_xsd,$x->archivoXml);
		if($err_xml_guardado!=null)
		{
			/// En caso de haber errores al almacenar el contenido XML, muestra los errores, terminando el proceso.
			$x->errors->log_error(ERROR_GENERAL, $err_xml_guardado);
			return array(false, $x->errors, false);
		}
		
		/// Terminar si no es valido segun el esquema.
		if (!$ok_esvalido_xsd)
		{
			/// En caso de haber errores de validacion de esquema, muestra los errores, terminando el proceso.
			return array(false, $x->errors, false);
		}
		
		//ASERTO: El XML es correcto estructuralmente y valido contra el XSD, y corresponde al mes y año indicados.
		
		/// 4. Generar el cuestionario a partir del xml y validar los datos
		$lookup_xml = $dao_xml->cargar_lookup_UT_XML();
		$x->generarCuestionario($aloja_cuest, $lookup_xml);
		
		//**** VALIDACION ***/
		/// 5. Validar y guardar los datos de la encuesta (guarda solo si se cumplen las reglas de valida_internas).
		$aloja_cuest->calcular_totales();  //Se calculan los totales a partir de los datos de las UTs. Hay reglas de validación que lo emplean
		$errores = $this->valida_y_guardar($aloja_cuest, false, $user_id, true);
		/*
		 * Devuelve un array con dos posiciones: 
	 	 * 		- [0] una colección AlojaErrorCollection si hay errores o false si no los hay.
	 	 *		- [1] false si no se ha guardado el cuestionario, true si se ha guardado.
		 */		
		
		if($aloja_cuest->avisos_excesos->num_errores()>0)
			$aloja_cuest->val_errors->errores=array_merge($aloja_cuest->val_errors->errores,$aloja_cuest->avisos_excesos->errores);
		
		$es_valido=($aloja_cuest->val_errors->num_errores()==0);
		
		if ((!$es_valido) && ($ignorar_avisos))
		{
			if ($aloja_cuest->val_errors->hay_solo_avisos())
				$es_valido = true;
		}
		
		/// En este punto, la variable $errores[0] puede false (la operación fue ok) o ser la colección de los errores y avisos del cuestionario o una colección
		/// de errores generados no relacionados con los datos del cuestionario (por ejemplo, errores al guardar el cuestionario en la BDD).
		/// Todo muy enrevesado...afortunadamente ya no se usa para nada.
		
		/// 6. Comprobar si hay cambios en datos de cabecera y guardarlos para notificarlos.
		$mod_cabecera = $x->obtenerDatosCabecera();
		
		$faltan_personal_precios=(
				((isset($aloja_cuest->tiene_datos_personal) ? $aloja_cuest->tiene_datos_personal : false)==false) ||
				((isset($aloja_cuest->tiene_datos_precios) ? $aloja_cuest->tiene_datos_precios : false)==false)
				);

		/// Devolver errores si el contenido no es valido.
		if (!$es_valido)
		{
			///
			// Si hay excesos detectados, marcamos inmediatamente como pendiente de información porque muchos usuarios no llegan a cerrar los cuestionarios
			// y para los administradores es importante saber si existe exceso o no.
			$ok=$dao->marcar_cuestionario_pendiente_informar_exceso($aloja_cuest);
			if (!$ok)
			    @AuditLog::log($user_id, $estab_id, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array("Error durante el marcado de pendiente de información sobre excesos."), array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));

			// En vez de devolver errores se devuelve el array del cuestionario por si hay errores
			// que se han puesto en otro método que no sea valida_y_guardar.
			return array(false, $aloja_cuest->val_errors, $errores[1], $aloja_cuest->excesoInfoObj, $faltan_personal_precios);
		}
		
		/// Devolver exito de la operacion.
		return array(true, $aloja_cuest, $mod_cabecera, $errores[1], $faltan_personal_precios);		
	}
	
	public function enviar_correo_confirmacion_cierre(Establecimiento $estab, AlojaCuestionario $cuestionario)
	{
		try 
		{
			$destinatario = $estab->email;
			if (!isset($destinatario))
				return;
			
			$mes = DateHelper::mes_tostring($cuestionario->mes, "m");
			$ano = $cuestionario->ano;
			$nombre_estab = $estab->nombre_largo();
			$asunto = "Acuse de recibo de la encuesta de alojamiento turístico: ". $mes . "/" . $ano . " - " . $nombre_estab;
			
			@$cuerpo = $this->obtener_cuerpo_desde_configuracion();
			
			$fechas = new DateHelper();
			$id_cuestionario = $cuestionario->id;
			$fecha_cuestionario = "Fecha de recepción: " . $fechas->dia_sistema."-".$fechas->mes_sistema."-".$fechas->anyo_sistema;
			$referencia_texto = "Referencia: " . $id_cuestionario;
			
			$cuerpo_final = $fecha_cuestionario . "<br/>" . $referencia_texto . "<br/><br/>" . $cuerpo; 
			
			$email = new Email();
			$email->send($asunto, $cuerpo_final, $destinatario);
		}
		catch (Exception $e)
		{
			log::error($e->getMessage());
		}	
	}
	
	private function obtener_cuerpo_desde_configuracion() 
	{
		$db = new Istac_Sql();
		$db->query("SELECT aloja_cierre_mailbody FROM TB_CONFIGURATION");
		
		if ($db->next_record())
		{
			return $db->f('aloja_cierre_mailbody');
		}
		return null;
	}
		
}

?>