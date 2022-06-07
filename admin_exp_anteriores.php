<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasController.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasFormData.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/classes/Noticias.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_EXP_ANTERIORES));

$establecimiento = null;

if (!$page->user_can_do(OP_EXPECTATIVAS))
{
	$page->client_redirect(PAGE_HOME, null, true);
}

$optit="Ver encuestas presentadas expectativas";
if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC)))
{
	$selected_estid = $page->select_establecimiento($page->self_url(NULL, TRUE));	
	$page->set_current_establecimiento($selected_estid);
	$establecimiento = $page->get_current_establecimiento();
	//Admins no se comprueba que este dado de baja el estab.
}
elseif($page->have_any_perm(PERM_GRABADOR))
{
	//$selected_estid = $page->select_establecimiento($page->self_url(NULL, TRUE));
	$selected_estid = $page->select_establecimiento($page->build_url(PAGE_EXP_FORMULARIO));
	///NOTA: select_establecimiento muestra un contenido diferente y termina con exit. Y como 
    /// la pagina a la que se redirige una vez se termine la seleccion es PAGE_EXP_FORMULARIO, nunca se pasa por aquí.
    $page->set_current_establecimiento($selected_estid);	
	$establecimiento = $page->get_current_establecimiento();
}
else
{
	$establecimiento = $page->get_current_establecimiento();
	$page->abort_si_estado_baja($establecimiento);
}

$viewvars = array();

/// Datos de la encuesta de expectativas
$exp_ctl = new ExpectativasController();
$exp_estado = $exp_ctl->cargar_estado_encuesta_expectativas($establecimiento, new DateTime('now'));
$viewvars['exp_encuesta_abierta'] = $exp_estado['plazo_esta_abierto'];
$viewvars['exp_fecha_presentada'] = isset($exp_estado['fecha_presentacion'])?$exp_estado['fecha_presentacion']:null;
$viewvars['exp_trimestre'] = (isset($exp_estado['trimestre']))?$exp_estado['trimestre']->tostring() : null;
$viewvars['exp_plazo'] = isset($exp_estado['fecha_limite'])?$exp_estado['fecha_limite']:null;


$avisos = new AvisoDao();
$listAvisos = $avisos->filtrarByIdGrupo($establecimiento->get_grupo());
$viewvars['listAvisos'] =  $listAvisos;			

/// Listado de encuestas anteriores
$enc_anteriores = array();
$exp_dao = new ExpectativasFormDao();
$ea_lista = $exp_dao->obtener_encuestas_anteriores($establecimiento->id_establecimiento, new Datetime('now'), 
		isset($exp_estado['trimestre'])?$exp_estado['trimestre'] : null);

foreach($ea_lista as $enc_ant)
{
	$ne = array();
	if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC)))
	{
		$ne['url'] = $page->build_url(PAGE_EXP_FORMULARIO, array(ARG_TRIM=>$enc_ant['trim'], ARG_ANO=>$enc_ant['ano'], ARG_ESTID => $establecimiento->id_establecimiento));
	}
	else
	{
		$ne['url'] = $page->build_url(PAGE_EXP_FORMULARIO, array(ARG_TRIM=>$enc_ant['trim'], ARG_ANO=>$enc_ant['ano']));
	}
	$tr = new Trimestre($enc_ant['trim'], $enc_ant['ano']);
	$ne['nombre'] = $tr->tostring('y');
	$enc_anteriores[] = $ne;
}
$viewvars['enc_anteriores'] =  $enc_anteriores;

//Si es hotel o apartamento
$viewvars['es_hotel'] = $establecimiento->es_hotel();

/// Noticias
$ndao = new NoticiasDao();
$viewvars['noticias'] = json_encode($ndao->getNoticiasFeeds());

/// Render de la pagina
$page->render( "admin_exp_anteriores_view.php", $viewvars );


$page->end_session();
?>
