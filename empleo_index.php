<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/classes/Noticias.class.php");
require_once(__DIR__."/classes/EmpleoController.class.php");

define('ARG_MES_ENCUESTA', 'mes_encuesta');
define('ARG_ANO_ENCUESTA', 'ano_encuesta');


$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_EXP_INDEX));

$establecimiento = null;

if (!$page->user_can_do(OP_EXPECTATIVAS))
{
	$page->client_redirect(PAGE_HOME, null, true);
}

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

if($es_admin)
{
	$selected_estid = $page->select_establecimiento($page->self_url(NULL, TRUE));	
	$page->set_current_establecimiento($selected_estid);
	$establecimiento = $page->get_current_establecimiento();
	//Admins no se comprueba que este dado de baja el estab.
}
elseif($page->have_any_perm(PERM_GRABADOR))
{
	//$selected_estid = $page->select_establecimiento($page->self_url(NULL, TRUE));
    $selected_estid = $page->select_establecimiento($page->build_url(PAGE_EMPLEO_FORM));
	///NOTA: select_establecimiento muestra un contenido diferente y termina con exit. Y como 
    /// la pagina a la que se redirige una vez se termine la seleccion es PAGE_EMPLEO_FORM, nunca se pasa por aquí.
    $page->set_current_establecimiento($selected_estid);	
	$establecimiento = $page->get_current_establecimiento();
}
else
{
	$establecimiento = $page->get_current_establecimiento();
	$page->abort_si_estado_baja($establecimiento);
}

$viewvars = array();

$empleo_ctl = new EmpleoController();

$empleo_estado = $empleo_ctl->cargar_estado_encuesta($establecimiento, $page->get_current_userid());
$empleo_enc = $empleo_estado->encuesta;

$viewvars['empleo_enc'] = $empleo_enc;

$viewvars['empleo_encuesta_abierta'] = true;

// Comprobamos si el cuestionario en curso ya está cerrado (se ha cerrado prematuramente por cese de actividad, no ocupación, etc.).
if($empleo_enc->esta_cerrado())
    $viewvars['empleo_encuesta_abierta'] = false;
else
{
    $viewvars['empleo_fecha_recepcion'] = ($empleo_enc->es_nuevo()==false)?$empleo_enc->fecha_recepcion:null;
}

/// Listado de encuestas anteriores
$enc_anteriores = array();
$dao=new EmpleadoresDao();
$ea_lista = $dao->obtener_encuestas_anteriores($establecimiento->id_establecimiento, $empleo_estado->encuesta->mes, $empleo_estado->encuesta->ano);
foreach($ea_lista as $enc_ant)
{
    $ne = array();
    $ne['url'] = $page->build_url(PAGE_EMPLEO_FORM, array(ARG_MES_ENCUESTA=>$enc_ant->mes, ARG_ANO_ENCUESTA=>$enc_ant->ano));
    $ne['url_acuse'] = $page->build_url(PAGE_EMPLEO_ACUSE, array(ARG_MES=>$enc_ant->mes, ARG_ANO=>$enc_ant->ano));
    $ne['mes'] = $enc_ant->mes;
    $ne['ano'] = $enc_ant->ano;
    $enc_anteriores[] = $ne;
}

$viewvars['enc_anteriores'] = $enc_anteriores;

$viewvars['empleo_plazo'] = ($es_admin)? null : $empleo_estado->plazo;

//Si es hotel o apartamento
$viewvars['es_hotel'] = $establecimiento->es_hotel();

if($es_admin)
{
    $viewvars['url_encuesta'] = $page->build_url(PAGE_EMPLEO_FORM, array(
        ARG_MES_ENCUESTA => $empleo_estado->encuesta->mes,
        ARG_ANO_ENCUESTA => $empleo_estado->encuesta->ano,
        ARG_ESTID => $establecimiento->id_establecimiento));
}
else 
{
    $viewvars['url_encuesta'] = PAGE_EMPLEO_FORM;
    /*
     $viewvars['url_encuesta'] = $page->build_url(PAGE_EMPLEO_FORM, array(
        ARG_MES_ENCUESTA => $empleo_estado->encuesta->mes,
        ARG_ANO_ENCUESTA => $empleo_estado->encuesta->ano));
    */
}
$viewvars['urlVolver'] = ($es_admin)? PAGE_HOME : PAGE_ALOJA_INDEX;

/// Render de la pagina
$page->render( "empleo_index_view.php", $viewvars );


$page->end_session();
?>
