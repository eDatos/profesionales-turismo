<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/aloja/AlojaTempDao.class.php");
require_once(__DIR__."/classes/aloja/AlojaUTStat.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_ALOJA_INDEX, PAGE_ALOJA_SELECT_PAISES));

/// VISTA: (cliente):
///   1. Si se marca un grupo, se marcan todos las unidades que contiene...
///   2. El boton continuar va con un POST a PAGE_ALOJA_FORM, pasando la lista de paises seleccionados y el mes y año del cuestionario.
/// 3. si (esta establecida la variable de sesion (SESS_ALOJA_PAISES_SELECT))
///			marcar los paises de dicha variable.
///    sino
///         marcar los paises que tienen datos en el formulario (ultimo movimiento).
///

/// Parametros de la pagina
// ARG_MES_ENCUESTA: Mes de encuesta para la que hay que cargar los datos de la selección de países
define("ARG_MES_ENCUESTA", "mes_encuesta");
// ARG_ANYO_ENCUESTA: Año de encuesta para la que hay que cargar los datos de la selección de países
define("ARG_ANO_ENCUESTA", "ano_encuesta");


//Se recogen los parámetros para cargar la encuesta
$mes_encuesta = $page->request_post_or_get(ARG_MES_ENCUESTA, NULL);
$ano_encuesta = $page->request_post_or_get(ARG_ANO_ENCUESTA, NULL);

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

$establecimiento = $page->get_current_establecimiento();


/// 1. Obtener el establecimiento y mes/año con el que se va a trabajar
if($es_admin)
{
	if(($establecimiento == null) || ($mes_encuesta==NULL) || ($ano_encuesta==NULL))
	{
		if(isset($establecimiento))
			$original_params[ARG_ESTID] = $establecimiento->id_establecimiento;
		$original_params[ARG_MES_ENCUESTA] = $mes_encuesta;
		$original_params[ARG_ANO_ENCUESTA] = $ano_encuesta;
	
		$optit="Crear o modificar cuestionario";
		list($selected_estid,$mes_encuesta, $ano_encuesta) = $page->select_establecimiento_mes_ano($page->self_url($original_params, TRUE));
		$page->set_current_establecimiento($selected_estid);
		$establecimiento = $page->get_current_establecimiento();
	}

	//Reinicia camino de hormigas para no mostrar vinculo a aloja_index.
	$page->init_page_path(array(PAGE_ALOJA_ANTERIORES, PAGE_ALOJA_SELECT_PAISES));
}

//Se cargan los datos del establecimiento

$aloja_ctl = new AlojaController();
$aloja_estado = $aloja_ctl->cargar_estado_encuesta_alojamiento($establecimiento, $page->get_current_userid());

// No se ha especificado el mes y ano de la encuesta, redirigir a la página principal.
if ($mes_encuesta == NULL || $ano_encuesta == NULL)
{
	$page->client_redirect(PAGE_HOME,NULL,TRUE);
	$page->end_session();
	exit;
}

$aloja_dao = new AlojaTempDao();
$grupo_lista_UT = $aloja_dao->get_stats_UT($establecimiento->id_establecimiento,$mes_encuesta, $ano_encuesta);

$aloja_form_data = $page->get_sess_state(SESS_ALOJA_FORM_DATA);

// Recordamos la última selección de países usada. Los administradores NUNCA recuerda la última selección...
$selected_UT = (!$es_admin) ? $aloja_form_data['uts'] : NULL;
if($selected_UT == NULL || $aloja_form_data['mes'] != $mes_encuesta || $aloja_form_data['ano'] != $ano_encuesta) 
	$selected_UT = array();

$solo_lectura = (!$es_admin) && ($aloja_estado['encuesta']->esta_cerrado());

$variables = array(
	'cuestionario'			=>  $aloja_estado['encuesta'],
	'grupo_lista_paises'	=>  $grupo_lista_UT,
	'selected_UT'			=>	$selected_UT,
	'mes_encuesta'			=>  $mes_encuesta,
	'ano_encuesta'			=>  $ano_encuesta,
	'alojaformUrl'			=> $page->build_url(PAGE_ALOJA_FORM),
	'esAdmin'				=> $es_admin,
	'soloLectura'			=> $solo_lectura
);

//Si es hotel o apartamento
$variables['es_hotel'] = $establecimiento->es_hotel();

/// Render de la pagina
$page->render( "aloja_select_paises_view.php", $variables );

$page->end_session();
?>