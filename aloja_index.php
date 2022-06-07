<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaDao.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/RepositoriesDao.class.php");
require_once(__DIR__."/classes/Noticias.class.php");
require_once(__DIR__."/classes/EmpleoController.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_ALOJA_INDEX));

/// Si es admin, puede seleccionar el establecimiento de trabajo.
if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC)))
{
	/// OPERACION 1: No se ha indicado ningun establecimiento, mostrar la pagina de busqueda.	
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

// Comprobamos si el cuestionario en curso ya está cerrado (se ha cerrado prematuramente por cese de actividad, no ocupación, etc.).
if($aloja_estado['encuesta']->esta_cerrado())
	$variables['aloja_encuesta_abierta'] = false;

$variables['aloja_dias_rellenos'] = $aloja_estado['dias_rellenos'];
$variables['aloja_cuestionario'] = $aloja_estado['encuesta'];
$variables['aloja_plazo'] = $aloja_estado['plazo'];

if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC)))
{
	$variables['encuestaUrl'] = $page->build_url(PAGE_ALOJA_SELECT_PAISES,
			array('mes_encuesta'	=>	$aloja_estado['encuesta']->mes,
					'ano_encuesta'	=>	$aloja_estado['encuesta']->ano,
					'estid' => $selected_estid));
	$variables['alojaXmlUrl'] = $page->build_url(PAGE_ALOJA_XML, 
			array('mes_sel'	=>	$aloja_estado['encuesta']->mes,
					'ano_sel'	=>	$aloja_estado['encuesta']->ano,
					'estid' => $selected_estid));
}
else
{
	$variables['encuestaUrl'] = $page->build_url(PAGE_ALOJA_SELECT_PAISES,
			array('mes_encuesta'	=>	$aloja_estado['encuesta']->mes,
					'ano_encuesta'	=>	$aloja_estado['encuesta']->ano));
	$variables['alojaXmlUrl'] = $page->build_url(PAGE_ALOJA_XML);	
	
}

/// Listado de avisos (informacion de interes).
$avisos = new AvisoDao();
$listAvisos = $avisos->filtrarByIdGrupo($establecimiento->get_grupo());

$variables['listAvisos'] = $listAvisos;

/// Noticias
$ndao = new NoticiasDao();
$variables['noticias'] = json_encode($ndao->getNoticiasFeeds());

/// Listado de encuestas anteriores
$enc_anteriores = array();
$aloja_dao = new AlojaDao();
$ea_lista = $aloja_dao->obtener_encuestas_anteriores($establecimiento->id_establecimiento, $aloja_estado['encuesta']->mes, $aloja_estado['encuesta']->ano);
foreach($ea_lista as $enc_ant)
{
	$ne = array();
	$ne['url'] = $page->build_url(PAGE_ALOJA_FORM, array('mes_encuesta'=>$enc_ant['mes'], 'ano_encuesta'=>$enc_ant['ano']));
	$ne['url_acuse'] = $page->build_url(PAGE_ALOJA_ACUSE, array('mes_encuesta'=>$enc_ant['mes'], 'ano_encuesta'=>$enc_ant['ano']));
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


// Vemos si el cuestionario de empleo se ha cerrado o está pendiente.
$empleo_ctl = new EmpleoController();

$empleo_estado = $empleo_ctl->cargar_estado_encuesta($establecimiento, $page->get_current_userid());
$empleo_enc = $empleo_estado->encuesta;

$variables['empleo_enc'] = $empleo_enc;

$variables['empleo_encuesta_abierta'] = true;

// Comprobamos si el cuestionario en curso ya está cerrado (se ha cerrado prematuramente por cese de actividad, no ocupación, etc.).
if($empleo_enc->esta_cerrado())
    $variables['empleo_encuesta_abierta'] = false;
else
{
    $variables['empleo_fecha_recepcion'] = ($empleo_enc->es_nuevo()==false)?$empleo_enc->fecha_recepcion:null;
}
    

/// Render de la pagina
$page->render("aloja_index_view.php", $variables );

$page->end_session();

?>