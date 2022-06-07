<?php

//TODO: Controlador form. alojamieento.

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/aloja/AlojaESP.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_ALOJA_INDEX, PAGE_ALOJA_FORM));

/// Parametros de la pagina
// ARG_MES_ENCUESTA: Mes de encuesta para la que hay que cargar los datos de la selección de países
define('ARG_MES_ENCUESTA', 'mes_encuesta');
// ARG_ANYO_ENCUESTA: Año de encuesta para la que hay que cargar los datos de la selección de países
define('ARG_ANO_ENCUESTA', 'ano_encuesta');

//Se recogen los parámetros para cargar la encuesta
$mes_encuesta = $page->request_post_or_get(ARG_MES_ENCUESTA, NULL);
$ano_encuesta = $page->request_post_or_get(ARG_ANO_ENCUESTA, NULL);

//Se cargan los datos del establecimiento
$establecimiento = $page->get_current_establecimiento();
if ($establecimiento == null)
{
	$page->abort_with_error(PAGE_ALOJA_INDEX, "No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

if($page->have_any_perm(array(PERM_USER,PERM_RECEPCION)))
{
	/// 1b. Los usuarios no pueden rellenar una encuesta si el establecimiento está dado de baja.
	$page->abort_si_estado_baja($establecimiento);
}

/// Si no estan definidos por parametros...
if ($mes_encuesta==NULL && $ano_encuesta==NULL)
{
	$page->abort_with_error(PAGE_ALOJA_INDEX, "No se ha definido el mes y año para el que mostrar el cuestionario.");
}

$aloja_ctl = new AlojaController();

/// Cargar estado de la encuesta de alojamiento actualmente en curso para comprobar si es del mes y año pedidos.
/// Comprobar si la encuesta debe mostrarse en modo de solo lectura.
/// Cargar los datos del cuestionario para el mes y año
/// Carga los datos del establecimiento valido en el mes y ano de la encuesta.
/// el criterio seguido es que los datos a dia 1 del mes de la encuesta son los correctos.
$aloja_estado = $aloja_ctl->cargar_encuesta($establecimiento, $page->get_current_userid(), $mes_encuesta, $ano_encuesta);
$aloja_enc = $aloja_estado['encuesta'];
if ($aloja_enc == NULL)
{
	/// No existe cuestionario para esa fecha, abortar la operacion.
	$page->abort_with_error(PAGE_HOME, "No existe cuestionario para el mes y año elegidos.");
}

// EN IMPRESION SIEMPRE SE VAN A MOSTRAR TODOS LOS DIAS DEL MES.
/// Se calcula el número de días abierto del cuestionario
/// Se calcula el número de días abierto del cuestionario
$fecha_helper = new DateHelper();

if($aloja_enc->mes == $fecha_helper->mes_sistema && $aloja_enc->ano == $fecha_helper->anyo_sistema && !$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC)))
	$dias_rellenados = $fecha_helper->dia_sistema;
else
	$dias_rellenados = cal_days_in_month(CAL_GREGORIAN, $aloja_enc->mes, $aloja_enc->ano);

/// Aumentar el dia mostrado hasta el dia relleno, en caso de que admin haya metido más días (de los que se les puede mostrar al usuario).
$dias_rellenados = max($aloja_estado['dias_rellenos'][1],$dias_rellenados);

$num_dias_mes = cal_days_in_month(CAL_GREGORIAN, $aloja_enc->mes, $aloja_enc->ano);
$dias_mostrar_formulario = $num_dias_mes;

/// Se calcula la fecha del ulitmo dia del mes anterior.
$ult_mes_ant = new DateTime();
$ult_mes_ant->setDate($aloja_enc->ano, $aloja_enc->mes, 1);
$ult_mes_ant->sub(new DateInterval('P1D'));

//Obtener los datos de ent/sal/pern
$aloja_dao = new AlojaDao();
$ent_sal_pern = $aloja_dao->cargar_esp_uts($establecimiento->id_establecimiento, $aloja_enc->mes, $aloja_enc->ano, NULL);

//Obtener los datos de las habitaciones y convertirlos a JSON
$habitac = $aloja_estado['hab'];

//Obtener los datos de personal y precios y convertirlos a JSON
$pers_prec = $aloja_estado['pers_prec'];

$variables = array(
		'dias_rellenados'		=> $dias_rellenados,
		'ult_mes_ant'			=> $ult_mes_ant,
		'dias_mostrar_formulario'=> $dias_mostrar_formulario,
		'num_dias_mes'			=>	$num_dias_mes,
		'cuestionario' 			=> 	$aloja_enc,
		'habitac'				=>  $habitac,
		'pers_prec'				=>  $pers_prec,
		'mes_encuesta'			=>  $aloja_enc->mes,
		'ano_encuesta'			=>  $aloja_enc->ano,		
        'ent_sal_pern'          =>  $ent_sal_pern,
		'datos_estab'			=>  $aloja_estado['datos_estab'],
		'navpage_url'			=>  $page->build_url(PAGE_ALOJA_FORM_AJAX, array(ARG_MES=>$aloja_enc->mes, ARG_ANO=>$aloja_enc->ano)),
		'urlSelectPaises' 		=>  $page->build_url(PAGE_ALOJA_SELECT_PAISES, array(ARG_MES_ENCUESTA=>$aloja_enc->mes, ARG_ANO_ENCUESTA=>$aloja_enc->ano)),
		'aloja_plazo'			=>  $aloja_estado['plazo'],
		//Si es hotel o apartamento
		'es_hotel'              => $establecimiento->es_hotel()
);

/// Render de la pagina
$page->render( "aloja_form_print_view.php", $variables, false);

$page->end_session();

?>