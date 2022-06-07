<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/aloja/AlojaDao.class.php");
require_once(__DIR__."/classes/aloja/AlojaTempController.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION), array(PAGE_ALOJA_INDEX, PAGE_ALOJA_FORM));

/// Parametros de la pagina
// ARG_MES_ENCUESTA: Mes de encuesta para la que hay que cargar los datos de la selección de países
define('ARG_MES_ENCUESTA', 'mes_encuesta');
// ARG_ANYO_ENCUESTA: Año de encuesta para la que hay que cargar los datos de la selección de países
define('ARG_ANO_ENCUESTA', 'ano_encuesta');
// ARG_TAB_INICIAL: Tab inicial a presentar (0=movimientos,1=habitaciones,2=personal y precios).
define('ARG_TAB_INICIAL', 'tab');
// ARG_RDONLY: Indica que el cuestionario ha de abrirse en modo sólo lectura aunque sea el adminitrador. Opción del menú principal "Ver encuestas presentadas".
define('ARG_RDONLY','rdonly');

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

//Se recogen los parámetros para cargar la encuesta
$mes_encuesta = $page->request_post_or_get(ARG_MES_ENCUESTA, NULL);
$ano_encuesta = $page->request_post_or_get(ARG_ANO_ENCUESTA, NULL);
$rdonly = ($page->request_post_or_get(ARG_RDONLY, NULL)!=NULL);
$tab_inicial = $page->request_post_or_get(ARG_TAB_INICIAL, 0);
if(isset($tab_inicial))
{
	if(($tab_inicial!="0")&&($tab_inicial!="1")&&($tab_inicial!="2"))
		$tab_inicial=0;
}

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

$establecimiento = $page->get_current_establecimiento();

/// 1. Obtener el establecimiento y mes/año con el que se va a trabajar
if($es_admin)
{
	if(($establecimiento == null) || ($mes_encuesta==NULL) || ($ano_encuesta==NULL))
	{
		/// Mostrar las pagina de busqueda de establecimiento y mes y año de trabajo.
		list($selected_estid,$mes_encuesta, $ano_encuesta) = $page->select_establecimiento_mes_ano($page->self_url(NULL, TRUE));
		$page->set_current_establecimiento($selected_estid);
		$establecimiento = $page->get_current_establecimiento();
	}
}

if ($establecimiento == null)
{
	$page->abort_with_error(PAGE_ALOJA_INDEX, "No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

if($page->have_any_perm(array(PERM_USER,PERM_RECEPCION)))
{
	/// 1b. Los usuarios no pueden rellenar una encuesta si el establecimiento está dado de baja.
	$page->abort_si_estado_baja($establecimiento);
}

$aloja_ctl = new AlojaController();

/// Cargar estado de la encuesta de alojamiento actualmente en curso para comprobar si es del mes y año pedidos.
/// Comprobar si la encuesta debe mostrarse en modo de solo lectura.
/// Cargar los datos del cuestionario para el mes y año
/// Establecer los paises de la encuestas en $selected_UT
/// Carga los datos del establecimiento valido en el mes y ano de la encuesta.
/// el criterio seguido es que los datos a dia 1 del mes de la encuesta son los correctos.
$aloja_estado = $aloja_ctl->cargar_encuesta($establecimiento, $page->get_current_userid(), $mes_encuesta, $ano_encuesta, (($rdonly) && ($es_admin)));

$aloja_enc = $aloja_estado['encuesta'];
if ($aloja_enc == NULL)
{
	if ($es_admin)
	{
		$aloja_enc = $aloja_ctl->crear_nuevo_cuestionario($mes_encuesta, $ano_encuesta, $establecimiento->id_establecimiento, $page->get_current_userid(), TIPO_CARGA_WEB, null, false);
		
		$aloja_estado['encuesta'] = $aloja_enc;
		
		$aloja_estado['selected_uts'] = $page->request_post_or_get('ut', NULL);
		
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
		
		$aloja_estado['hab'] = array();
		$pers = new AlojaPersonal();
		$precios = new AlojaPrecios();
		$aloja_estado['pers_prec'] = array($pers,$precios);		
	}
	else
	{
		/// No existe cuestionario para esa fecha, abortar la operacion.
		$page->abort_with_error(PAGE_HOME, "No existe cuestionario para el mes y año elegidos.");
	}
}
//Si la encuesta no tiene id inicializado, se considera encuesta nueva y hay que guardar la encuesta en BBDD con 
//los presentes a comienzo de mes con los datos de los presentes a fin de mes anterior. También se guardan los datos para los movimientos diarios
if(!isset($aloja_enc->id))
{
	//Se guarda el cuestionario en bbdd de datos según los presentes a fin de mes anterior
	$aloja_ctl->inicializar_cuestionario_pres_esp($establecimiento->id_establecimiento, $aloja_enc);
	$aloja_ctl->guardar_cuestionario($aloja_enc);
	
	//Se carga el cuestionario para inicializar el resto de variables
	$aloja_estado = $aloja_ctl->cargar_encuesta($establecimiento, $page->get_current_userid(), $mes_encuesta, $ano_encuesta);
	$aloja_enc = $aloja_estado['encuesta'];	
}

/// EXC: Admin siempre entre en modo RW excepto cuando se entra por la opción de "Ver encuestas presentadas".
$solo_lectura = (($rdonly) && ($es_admin)) || ((!$es_admin) && ($aloja_estado['es_encuesta_anterior']));


//Se establecen las unidades territoriales elegidas en el formulario de seleccion de paises
$selected_UT = $page->request_post_or_get('ut', NULL);
if($selected_UT == NULL)
{
	$desde_sel_ut = $page->request_post_or_get('desde_sel_ut','0');
	if($desde_sel_ut==1)
	{
		$selected_UT = array();
	}
	else 
	{
		/// Comprobar si en sesion se tiene la seleccion de paises.
		$sess_aloja_form_data = $page->get_sess_state(SESS_ALOJA_FORM_DATA);
		if($sess_aloja_form_data['uts'] != NULL && $sess_aloja_form_data['mes']==$aloja_enc->mes && $sess_aloja_form_data['ano'] == $aloja_enc->ano)
		{
			$selected_UT = $sess_aloja_form_data['uts'];
		}
	}
}

/// Se establecen la UT de la encuesta elegida en caso de que sea otra que no esta en curso.
/// Para el administrador, en caso de que no se haya cambiado la lista de paises, se cogen inicialmente los paises guardados.
//if ($aloja_estado['es_encuesta_anterior'])
if ($solo_lectura || ($es_admin && $selected_UT == NULL))
{
    if(!isset($aloja_estado['selected_uts']))
    {
        $detalles=err_detail($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
        @log::error("Indice 'selected_uts' no definido. $detalles");
    }
	$selected_UT = $aloja_estado['selected_uts'];
}

/// Se almacenan los datos de la encuesta en sesion para que la pagina de ajax pueda encontrarlos.
$aloja_form_data = array(
		'uts' => $selected_UT,
		'mes' => $aloja_enc->mes, 
		'ano' => $aloja_enc->ano);
$page->set_sess_state(SESS_ALOJA_FORM_DATA, $aloja_form_data);

/// POST-CONDICION: Cargado registro aloja_enc con los datos del mes/año, un flag para indicar RW/readonly y el establecimiento cargado para la fecha del cuestionario.

/// Se calcula el número de días abierto del cuestionario
$fecha_helper = new DateHelper();

if($aloja_enc->mes == $fecha_helper->mes_sistema && $aloja_enc->ano == $fecha_helper->anyo_sistema && !$es_admin)
	$dias_mostrar_formulario = $fecha_helper->dia_sistema;
else
	$dias_mostrar_formulario = cal_days_in_month(CAL_GREGORIAN, $aloja_enc->mes, $aloja_enc->ano);

/// Aumentar el dia mostrado hasta el dia relleno, en caso de que admin haya metido más días (de los que se les puede mostrar al usuario).
$dias_mostrar_formulario = max($aloja_estado['dias_rellenos'][1],$dias_mostrar_formulario);

$num_dias_mes = cal_days_in_month(CAL_GREGORIAN, $aloja_enc->mes, $aloja_enc->ano);

/// Se calcula la fecha del último dia del mes anterior.
$ult_mes_ant = new DateTime();
$ult_mes_ant->setDate($aloja_enc->ano, $aloja_enc->mes, 1);
$ult_mes_ant->sub(new DateInterval('P1D'));

// Actualizamos la información del cuestionario rescatada de la BDD con la información de las grabaciones automáticas contenidas en las tablas temporales.
// NOTA: El administrador también puede ver estos datos temporales (aunque no generarlos).
$alojatempctl = new AlojaTempController();
$alojatempctl->actualizar_cuestionario($aloja_enc);

// Si existe, obtenemos la información de habitaciones y precios de las tablas temporales.
$alojatempctl->cargar_encuesta_temp($aloja_enc,$aloja_estado);

/// Obtener los datos de las habitaciones y convertirlos a JSON para procesarlos en javascript.
$json_habitaciones = hab_to_json($aloja_estado['hab']);

/// Obtener los datos de personal y precios y convertirlos a JSON para procesarlos en javascript.
$json_pers_prec = pers_prec_to_json($aloja_estado['pers_prec']);

$mes_encuesta_finalizado=(date("Ym")!=$aloja_enc->ano.$aloja_enc->mes);

$variables = array(
		'ult_mes_ant'			 => $ult_mes_ant,
		'dias_mostrar_formulario'=> $dias_mostrar_formulario,
		'num_dias_mes'			=>	$num_dias_mes,
		'cuestionario' 			=> 	$aloja_enc,
		'json_habitaciones'		=>  $json_habitaciones,
		'json_pers_prec'		=>  $json_pers_prec,
		'mes_encuesta'			=>  $aloja_enc->mes,
		'ano_encuesta'			=>  $aloja_enc->ano,
		'mes_encuesta_finalizado'		=>	$mes_encuesta_finalizado,
		'nombre_establecimiento'=>	$establecimiento->nombre_establecimiento,
        'selected_UT'           =>  $selected_UT,
		'tipo_establecimiento'	=> 	$aloja_estado['datos_estab']->id_tipo_establecimiento,
		'num_plazas'			=>  $aloja_estado['datos_estab']->num_plazas,
		'num_plazas_supletorias'=>  $aloja_estado['datos_estab']->num_plazas_supletorias,
		'num_habitaciones'		=>  $aloja_estado['datos_estab']->num_habitaciones,
		'navpage_url'			=> $page->build_url(PAGE_ALOJA_FORM_AJAX, array(ARG_MES=>$aloja_enc->mes, ARG_ANO=>$aloja_enc->ano)),
		'urlSelectPaises' 		=> ($solo_lectura)? null : $page->build_url(PAGE_ALOJA_SELECT_PAISES, $es_admin ? array(ARG_MES_ENCUESTA=>$aloja_enc->mes, ARG_ANO_ENCUESTA=>$aloja_enc->ano, 'estid'=>$establecimiento->id_establecimiento) : array(ARG_MES_ENCUESTA=>$aloja_enc->mes, ARG_ANO_ENCUESTA=>$aloja_enc->ano)),
		'urlPrintForm' 			=> $page->build_url(PAGE_ALOJA_PRINT, array(ARG_MES_ENCUESTA=>$aloja_enc->mes, ARG_ANO_ENCUESTA=>$aloja_enc->ano)),
		'aloja_plazo'			=> ($solo_lectura || $es_admin)? null : $aloja_estado['plazo'],
		'solo_lectura'			=> $solo_lectura,
		//Si es hotel o apartamento
		'es_hotel'              => $establecimiento->es_hotel(), 
		'urlBack'				=> ($solo_lectura)? $page->build_url(PAGE_HOME) : $page->build_url(PAGE_ALOJA_SELECT_PAISES, $es_admin ? array(ARG_MES_ENCUESTA=>$aloja_enc->mes, ARG_ANO_ENCUESTA=>$aloja_enc->ano, 'estid'=>$establecimiento->id_establecimiento) : array(ARG_MES_ENCUESTA=>$aloja_enc->mes, ARG_ANO_ENCUESTA=>$aloja_enc->ano)),
		'es_admin'				=> $es_admin,
		'tab'					=> $tab_inicial,
		'codMotivoExcesoPlazas'	=> $aloja_enc->excesoPlazas,
		'detalleMotivoExcesoPlazas' => $aloja_enc->excesoPlazasDetalle,
		'codMotivoExcesoHabitaciones'	=> $aloja_enc->excesoHabitaciones,
		'detalleMotivoExcesoHabitaciones'	=> $aloja_enc->excesoHabitacionesDetalle,
		'motivosExcesos'		=> obtenerMotivos(),
		'limiteInvitaciones'	=> obtenerLimitesInvitaciones()
);


/// Render de la pagina
$page->render( "aloja_form_view.php", $variables);

$page->end_session();

function hab_to_json($habitaciones)
{
	if ($habitaciones == null)
		return null;
	
	$j_s = array();
	foreach($habitaciones as $dia => $hab)
	{
		$j_s[] = array(
				'Dia' => $dia,
				'Sup' => $hab->supletorias,
				'Dob' => $hab->uso_doble,
				'Ind' => $hab->uso_individual,
				'Otr' => $hab->otras
				); 
	}
	
	return json_encode($j_s);	
}

function pers_prec_to_json($pers_prec)
{
	if ($pers_prec == null)
		return null;

	list($personal, $precios) = $pers_prec;
	
	$pers_prec_json = array();
	if($personal!=null)
	{
		$pers_prec_json["NoRem"] = $personal->no_remunerado;
		$pers_prec_json["Fijo"] = $personal->remunerado_fijo;
		$pers_prec_json["Event"] = $personal->remunerado_eventual;
	}
	
	if($precios!=null)
	{
		$pers_prec_json["IngDispMen"] = $precios->revpar_mensual;
		$pers_prec_json["TarMedHab"] = $precios->adr_mensual;
		if($precios->adr!=null)
		{
			$adr = array();
			foreach($precios->adr as $tipo => $valor)
			{
				$adr[] = array(
						'Tipo'	=> $tipo,
						'Valor'	=> $valor  
						);
			}
			$pers_prec_json["ADR"]=$adr;
		}

		if($precios->num!=null)
		{
			$num = array();
			foreach($precios->num as $tipo => $valor)
			{
				$num[] = array(
						'Tipo'	=> $tipo,
						'Valor'	=> $valor
				);
			}
			$pers_prec_json["NumHabOcup"]=$num;
		}
		
		if($precios->pct!=null)
		{
			$pct = array();
			foreach($precios->pct as $tipo => $valor)
			{
				$pct[] = array(
						'Tipo'	=> $tipo,
						'Valor'	=> $valor
				);
			}
			$pers_prec_json["PctHabOcup"]=$pct;
		}	

		return json_encode($pers_prec_json);
	}
}


function obtenerMotivos()
{
	global $establecimiento;
	
	$sql = "SELECT ID_MOTIVO idmot,DESCRIPCION_MOTIVO descmot,DETALLE_OBLIGATORIO oblig,AYUDA_DETALLE ayuda FROM TB_ALOJA_EXCESO_MOTIVOS WHERE ACTIVADO='S' AND ES_HOTEL='".(($establecimiento->es_hotel())?"S":"N")."' ORDER BY ORDEN";

	$db = new Istac_Sql();
	$db->query($sql);
	return new RowDbIterator($db, array('idmot', 'descmot', 'oblig', 'ayuda'));
}

function obtenerLimitesInvitaciones()
{
	global $establecimiento;
	
	$sql = "SELECT ID_TIPO_CLIENTE idcliente, LIMITE_PERCT perct FROM TB_ALOJA_PERCT_INVITACIONES WHERE ACTIVADO='S'";

	$db = new Istac_Sql();
	$db->query($sql);
	return new RowDbIterator($db, array('idcliente', 'perct'));
}
?>