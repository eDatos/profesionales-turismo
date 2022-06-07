<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/aloja/AlojaTempController.class.php");
require_once(__DIR__."/classes/aloja/AlojaErrorCollection.class.php");
require_once(__DIR__."/classes/aloja/AlojaDao.class.php");
require_once(__DIR__."/classes/aloja/AlojaUTMovimientos.class.php");
require_once(__DIR__."/classes/aloja/AlojaESP.class.php");
require_once(__DIR__."/lib/InputCleaner.class.php");

$page = PWETPageHelper::start_page_ajax(PERMS_ANY);

define('ARG_PAGE', 'pagina');
define('ARG_DATA', 'var_json');

define('OP_GETPAGE', 'gp');
define('OP_GUARDAR', 'save');
define('OP_CERRAR', 'send');
define ('OP_SAVETEMP', 'backup');

// define("TIPO_ERROR_ESTRUCTURAL", 3);
// define("TIPO_ERROR_GRAVE", 2);
// define("TIPO_ERROR_DATOS", 1);


function err_detail($id_est,$mes,$anio)
{
    $salida="est=";
    if($id_est!=null)
        $salida.=$id_est;
    $salida.="mes=";
    if($mes!=null)
        $salida.=$mes;
    $salida.="anio=";
    if($anio!=null)
        $salida.=$anio;
    return $salida;
}

$est = $page->get_current_establecimiento();
$mes = $page->request_post_or_get(ARG_MES);
$ano = $page->request_post_or_get(ARG_ANO);


/// Comprobacion de parametros, respuesta vacía en caso de que alguno sea erroneo.
if ($est == NULL || $mes == null || $ano == null)
{
	$page->end_session();
	exit;
}

header("Content-Type: text/html; charset=UTF-8");

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

$accion=$page->request_post(ARG_OP);

if ($accion == OP_GETPAGE)
{
	/// Peticion de una pagina de unidades territoriales: Una pagina de unidades territoriales (UT) esta formada por:
	///   - De 1 a 4 columnas de datos ESP, cada columna para una unidad territorial.
	///   - DE 1 columna de ESP de totales completos para todas las UTS almacenadas.
	$v = $page->request_post(ARG_PAGE);

	/// El catalogo de uts que se paginas se almacena en sesion (guardado por aloja_form).
	$sess_aloja_form_data = $page->get_sess_state(SESS_ALOJA_FORM_DATA);
	$selected_UT = $sess_aloja_form_data['uts'];
	if($selected_UT != NULL && $sess_aloja_form_data['mes']==$mes && $sess_aloja_form_data['ano'] == $ano)
	{
		$id_uts = obtener_uts_pagina($selected_UT, $v - 1);
		
		$dao = new AlojaTempDao();
		$uts = $dao->cargar_esp_uts($est->id_establecimiento, $mes, $ano, $id_uts);
		$tots = $dao->calcular_esp_totales($est->id_establecimiento, $mes, $ano);
		echo convert_to_json($uts, $tots);
	}
	$page->end_session();
}
else if($accion == OP_GUARDAR || $accion == OP_CERRAR)
{
	try
	{
		/// Guardar datos parciales de uts enviadas como json desde la app web.
		
		//
		// ATENCIÓN: La grabación de datos por parte del administrador borrará los datos temporales que haya podido introducir el usuario.
		//
		
		// 1. Coger el objeto json y rellenar un aloja cuestionario.
		$data = $page->request_post(ARG_DATA);
		$ctl = new AlojaTempController();
		$dao = new AlojaDao();
		$aloja_cuest = $dao->cargar_registro_cuestionario($est->id_establecimiento, $mes, $ano);
		if ($aloja_cuest == null)
		{
			// Esto no debería suceder...
		    $detalles=err_detail($est->id_establecimiento, $mes, $ano);
			Log::warning("La petición AJAX $accion no ha encontrado el cuestionario. Se crea uno nuevo.".$detalles);
		
			// Los cuestionarios ya se crean si no existen en aloja_form.php.
			/// Crear uno nuevo si no existe, sin guardar en la BBDD.
			$aloja_cuest = $ctl->crear_nuevo_cuestionario($mes, $ano, $est->id_establecimiento, $page->get_current_userid(), TIPO_CARGA_WEB, null, false);
		}
		
		// FIXME: Verificar que la condición es correcta. Ver qué pasa con los cuestionarios del mes anterior.
		// Por seguridad, comprobamos si el usuario está autorizado a modificar el cuestionario.
		if((!$es_admin) && ($aloja_cuest->esta_cerrado()))
		{
			$errs = new AlojaErrorCollection();
			$errs->log_error(ERROR_GENERAL, "Operación no autorizada: el cuestionario ya fue cerrado con anterioridad.");
			echo errores_to_json($errs, false);
			$page->end_session();
			exit;
		}
		
		// TODO: Manejar el caso de que no exista el cuestionario temporal.
		// Un caso es cuando es cuando se envía un cuestionario que ha sido guardado (y por tanto eliminada la copia temporal) pero ha fallado la validación posterior,
		// se sigue dentro del formulario de recogida de datos y el usuario pulsa el botón Guardar ó el botón Enviar antes de que se lance una grabación automática.
		// De momento, no es necesario hacer nada...
		
		actualizar_cuestionario($aloja_cuest, $data);
		
		$op_es_guardar = ($accion == OP_GUARDAR);
		
		$errores = $ctl->valida_y_guardar($aloja_cuest, $op_es_guardar, $page->get_current_userid(), false);
		//Los avisos tienen como nivel en el error NIVEL_ADVERTENCIA.
		
		// $errores[0] ==> colección de errores/avisos si la operación se completó (null si no hay errores). False si no hay error.
		// $errores[1] ==> true/false indicando si el cuestionario se grabó o no.
		
		//Se devuelve un array con los errores/avisos que hayan surgido durante la validación. Si no hay errores se devuelve un array vacío.
		if ($errores[0] === false)
		{
			if($aloja_cuest->val_errors->num_errores()>0)
				echo errores_to_json($aloja_cuest->val_errors, $errores[1]);
			else
				echo json_encode(array(false, $errores[1]));
		}
		else
			echo errores_to_json($aloja_cuest->val_errors, $errores[1]);
		$page->end_session();
	}
	catch(Exception $excepcion)
	{
		$errs = new AlojaErrorCollection();
		$errs->log_error(ERROR_GENERAL, "Error no especificado. No se ha podido guardar el cuestionario.");
		echo errores_to_json($errs, false);
		$page->end_session();
		throw $excepcion;
	}
}
else if($accion == OP_SAVETEMP)
{
	/// Guardar datos temporales de la entrada de usuario
	if ($es_admin)
	{
		// Esta operación no está permitida para los administradores.
		echo json_encode(false);
		$page->end_session();
		exit;
	}

	// FIXME: Para evitar peticiones maliciosas se debe prohibir las grabaciones temporales en caso de haber abierto el cuestionario en modo sólo lectura.
	// TODO: Buscar una forma sencilla y poco costosa de comprobar si estamos en modo sólo lectura.
	/*
	$aloja_ctl = new AlojaController();
	$aloja_estado = $aloja_ctl->cargar_encuesta($est, $page->get_current_userid(), $mes, $ano);
	$solo_lectura = $aloja_estado['es_encuesta_anterior'];
	if($solo_lectura)
	{
		// Esta operación no está permitida para los administradores.
		echo json_encode(false);
		$page->end_session();
		exit;
	}
	*/

	$data = $page->request_post(ARG_DATA);
	$ctl = new AlojaTempController();
	$dao = new AlojaTempDao();
	$aloja_cuest = $dao->cargar_registro_cuestionario($est->id_establecimiento, $mes, $ano);

	if ($aloja_cuest == null)
	{
		// Esto no debería suceder...
		// Los cuestionarios ya se crean si no existen en aloja_form.php.
	    $detalles=err_detail($est->id_establecimiento, $mes, $ano);
		Log::warning("La petición AJAX $accion no ha encontrado el cuestionario.".$detalles);
		
		echo json_encode(false);
		$page->end_session();
		exit;
	}
	
	// FIXME: Verificar que la condición es correcta. Ver qué pasa con los cuestionarios del mes anterior.
	// Por seguridad, comprobamos si el usuario está autorizado a modificar el cuestionario.
	if($aloja_cuest->esta_cerrado())
	{
		echo json_encode(false);
		$page->end_session();
		exit;
	}
	
	actualizar_cuestionario($aloja_cuest, $data);
	
	/// TODO: Estudiar la posibilidad de devolver HTTP response 204 NO CONTENT para indicar que la operación se realizó correctamente.
	
	// NOTA: En el caso de que no existiese información temporal del cuestionario, se crea en este punto.
	//       Un caso es cuando es cuando se envía un cuestionario que ha sido guardado (y por tanto eliminada la copia temporal) pero ha fallado la validación posterior
	//		 y se sigue dentro del formulario de recogida de datos generándose la actual llamada.
	echo json_encode($ctl->guardar_temp($aloja_cuest, $page->get_current_userid()));
	
	$page->end_session();
}

/**
 * Obtiene la lista de ids de unidades territoriales de la pagina número pag.
 * @param unknown_type $pag
 */
function obtener_uts_pagina($vector_ut, $pag, $pag_size = 4)
{
	$ret = array();
	$current = $pag * $pag_size;
	
	$i = 0;
	while ($i < $pag_size && $current < count($vector_ut))
	{
		$ret[] = $vector_ut[$current];
		$current++;
		$i++;
	}
	return $ret;
}

/**
 * Convierte los DTO a la estructura JSON que se serializa y envia al cliente.
 * @param unknown_type $uts_movs
 * @param unknown_type $totales_movs
 * @return multitype:UT multitype:UT
 */
function convert_to_json($uts_movs, $totales_movs)
{
	$UT_columns = array();
	$totales = array();
	
	$pos = 0;
	foreach($uts_movs as $ut_id => $ut_info)
	{
		$UT_columns[] = utmovimiento_to_json($ut_info['filas'], $pos, $ut_id, $ut_info['presentes_comienzo_mes'], $ut_info['nombre']);
		$pos++;
	}
	$totales = utmovimiento_to_json($totales_movs,0,0,$totales_movs->presentes_comienzo_mes);
	
	return json_encode(array( 'columnas' => $UT_columns,'totales' => $totales));
}

function errores_to_json(AlojaErrorCollection $errores, $cuest_guardado)
{
	foreach($errores->errores as $error)
	{
		$error->mensaje = utf8_encode($error->mensaje);
	}
	
	return json_encode(array($errores, $cuest_guardado));
}

/** Convierte un objeto de tipo AlojaUTMovimientos a un objeto UT para transferencia json. */
function utmovimiento_to_json(AlojaUTMovimientos $ut_mov, $pos = 0, $id = 0, $pres_com_mes=0, $nombre = null)
{
	$ut_col = new UT($pos, $id, utf8_encode($nombre),$pres_com_mes);
	$ut_col->EPSLines = array();
	foreach($ut_mov->movimientos as $dia => $esp)
	{
		$ut_col->EPSLines[] = new EPS_line($dia, $esp->entradas, $esp->salidas, $esp->pernoctaciones);
	}
	return $ut_col;
}

/** Convierte un objeto de tipo UT a un objeto AlojaUTMovimientos para recepcion json. */
function json_to_utmovimiento($ut_data)
{
	if($ut_data==null)
		return null;
	$aut = new AlojaUTMovimientos();
	$aut->presentes_comienzo_mes = isset($ut_data->PresComMes) ? $ut_data->PresComMes : null;
	if ($aut->presentes_comienzo_mes == null)
		$aut->presentes_comienzo_mes = 0;
	
	foreach($ut_data->EPSLines as $dia_esp)
	{
		$dia = $dia_esp->Dia;
		$aesp = new AlojaESP();
		$aesp->entradas = $dia_esp->E;
		if ($aesp->entradas == null)
			$aesp->entradas = 0;
		
		$aesp->salidas = $dia_esp->S;
		if ($aesp->salidas == null)
			$aesp->salidas = 0;
		
		$aesp->pernoctaciones = $dia_esp->P;
		if ($aesp->pernoctaciones == null)
			$aesp->pernoctaciones = 0;
		
		$aut->movimientos[$dia] = $aesp;
	}
	return $aut;
}

/** Convierte un objeto de tipo Linea_Habitacion a un objeto AlojaHabitaciones para recepcion json. */
function json_to_habitaciones($hab_data)
{
	$hab = new AlojaHabitaciones();
	$hab->uso_doble = $hab_data->Dob;
	$hab->uso_individual = $hab_data->Ind;
	$hab->supletorias = $hab_data->Sup;
	$hab->otras = $hab_data->Otr;
	return $hab;
}

/**
 * Actualiza el cuestionario con los datos recibidos en data.
 * @param unknown_type $aloja_cuest
 * @param unknown_type $data
 */
function actualizar_cuestionario($aloja_cuest, $data)
{
	if (isset($data['mc']))
		$aloja_cuest->modo_cumplimentado = $data['mc'] == 'H' ? MODO_CUMPL_HOR : MODO_CUMPL_VER;
	if (isset($data['mi']))
		$aloja_cuest->modo_introduccion = $data['mi'] == 'EP' ? MODO_INTRO_EP : MODO_INTRO_ES;
	if (isset($data['da']))
		$aloja_cuest->dias_abierto = (int)$data['da'];
	else
		$aloja_cuest->dias_abierto = null;
	
	if (isset($data['mp']))
		$aloja_cuest->modo_porcentaje = ($data['mp'] == 'N' ? MODO_PORC_NUM : MODO_PORC_PORC);
	
	if (isset($data['excesoPlazas']))
		$aloja_cuest->excesoPlazas = $data['excesoPlazas'];
	if (isset($data['excesoHabitaciones']))
		$aloja_cuest->excesoHabitaciones = $data['excesoHabitaciones'];
	if (isset($data['excesoPlazasDetalle']))
		$aloja_cuest->excesoPlazasDetalle = InputCleaner::oracle(iconv("UTF-8", "CP1252", urldecode($data['excesoPlazasDetalle'])));
	if (isset($data['excesoHabitacionesDetalle']))
		$aloja_cuest->excesoHabitacionesDetalle = InputCleaner::oracle(iconv("UTF-8", "CP1252", urldecode($data['excesoHabitacionesDetalle'])));
		
	///Tipo de carga WEB, porque se está haciendo un guardar desde el formulario
	$aloja_cuest->tipo_carga = TIPO_CARGA_WEB;
	
	/// Movimientos de unidades territoriales
	$map = json_decode($data['uts']);
	$ut_map = array();
	foreach($map as $ut_in)
	{
		$ut_map[$ut_in->Id] = json_to_utmovimiento($ut_in);
	}
	$aloja_cuest->mov_por_ut = $ut_map;
	
	//Habitaciones
	$map = json_decode($data['hab']);
	$hab_map = array();
	foreach ($map as $hab_lin)
	{
		$hab_map[$hab_lin->Dia] = json_to_habitaciones($hab_lin);
	}
	$aloja_cuest->habitaciones = $hab_map;
	
	//Personal y precios
	$map = json_decode($data['pp']);
	$pers = new AlojaPersonal();
	$pers->no_remunerado = $map->NoRem;
	$pers->remunerado_fijo = $map->Fijo;
	$pers->remunerado_eventual = $map->Event;
	$aloja_cuest->personal = $pers;
	
	$prec = new AlojaPrecios();
	$prec->adr_mensual = $map->TarMedHab;
	$prec->revpar_mensual = $map->IngDispMen;
	if(isset($map->ADR))
	{
		$prec->adr = array();
		foreach ($map->ADR as $adr_lin)
		{
			$prec->adr[$adr_lin->Tipo]=$adr_lin->Valor;
		}
	}
	if(isset($map->NumHabOcup))
	{
		$prec->num = array();
		foreach ($map->NumHabOcup as $num_lin)
		{
			$prec->num[$num_lin->Tipo]=$num_lin->Valor;
		}
	}
	if(isset($map->PctHabOcup))
	{
		$prec->pct = array();
		foreach ($map->PctHabOcup as $pct_lin)
		{
			$prec->pct[$pct_lin->Tipo]=$pct_lin->Valor;
		}
	}	
	$aloja_cuest->precios = $prec;
	
	/// Totales de movimientos de unidades territoriales
	$map = json_decode($data['totales']);
	$aloja_cuest->totales = json_to_utmovimiento($map);
}
?>