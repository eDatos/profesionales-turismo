<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaDao.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/RepositoriesDao.class.php");
require_once(__DIR__."/classes/Noticias.class.php");

define('ARG_MES_ENCUESTA', 'mes_encuesta');
define('ARG_ANO_ENCUESTA', 'ano_encuesta');

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_ALOJA_ANTERIORES));

/// Si es admin, puede seleccionar el establecimiento de trabajo.
if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC)))
{
	/// OPERACION 1: No se ha indicado ningun establecimiento, mostrar la pagina de busqueda.
	$optit="Ver encuestas presentadas alojamiento";
	$selected_estid = $page->select_establecimiento($page->self_url(NULL, TRUE));	
	$page->set_current_establecimiento($selected_estid);		
	//list($mes_trabajo, $ano_trabajo) = $page->select_mes_ano($page->self_url(NULL, TRUE));
}

/// Array de variables pasadas a la vista
$variables = array();

/// Datos del establecimiento del usuario.
$establecimiento = $page->get_current_establecimiento();

/// Datos de la encuesta de alojamiento en curso
$aloja_ctl = new AlojaController();
$aloja_estado = $aloja_ctl->cargar_estado_encuesta_alojamiento($establecimiento, $page->get_current_userid());

$variables['aloja_encuesta_abierta'] = true;
$variables['aloja_dias_rellenos'] = $aloja_estado['dias_rellenos'];
$variables['aloja_cuestionario'] = $aloja_estado['encuesta'];
$variables['aloja_plazo'] = $aloja_estado['plazo'];


/// Listado de avisos (informacion de interes).
$avisos = new AvisoDao();
$listAvisos = $avisos->filtrarByIdGrupo($establecimiento->get_grupo());

$variables['listAvisos'] = $listAvisos;

/// Listado de encuestas anteriores
$enc_anteriores = array();
$aloja_dao = new AlojaDao();
$ea_lista = $aloja_dao->obtener_encuestas_anteriores_admin($establecimiento->id_establecimiento, $aloja_estado['encuesta']->mes, $aloja_estado['encuesta']->ano);
foreach($ea_lista as $enc_ant)
{
	$ne = array();
	$ne['url'] = $page->build_url(PAGE_ALOJA_FORM, array(ARG_MES_ENCUESTA=>$enc_ant['mes'], ARG_ANO_ENCUESTA=>$enc_ant['ano'],'rdonly' => true));
	if($enc_ant['cerrada']==true)
	    $ne['url_acuse'] = $page->build_url(PAGE_ALOJA_ACUSE, array(ARG_MES_ENCUESTA=>$enc_ant['mes'], ARG_ANO_ENCUESTA=>$enc_ant['ano']));
	else
	    $ne['url_acuse'] = null;
	$ne['mes'] = $enc_ant['mes'];
	$ne['ano'] = $enc_ant['ano'];
	$enc_anteriores[] = $ne;
}

$variables['enc_anteriores'] = $enc_anteriores;

/// Resultados comparativos anteriores
$rep_dao = new RepositoriesDao();
$variables['rc_urls'] = $rep_dao->getUrls($page);

//Si es hotel o apartamento
$variables['es_hotel'] = $establecimiento->es_hotel();

/// Render de la pagina
$page->render("admin_aloja_anteriores_view.php", $variables );

$page->end_session();

?>