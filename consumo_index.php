<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasController.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasFormData.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/classes/Noticias.class.php");
require_once(__DIR__."/classes/FacturaController.class.php");

define('ARG_MES_ENCUESTA', 'mes_encuesta');
define('ARG_ANO_ENCUESTA', 'ano_encuesta');

define('ARG_PARTE', 'part');
define('ARG_NUMERO_FACTURA', 'numero_factura');


$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_CONSUMO_INDEX));

$establecimiento = null;

if(!$page->user_can_do(OP_SUMINISTROS))
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
    $selected_estid = $page->select_establecimiento($page->build_url(PAGE_CONSUMO_FORM));
	///NOTA: select_establecimiento muestra un contenido diferente y termina con exit. Y como 
    /// la pagina a la que se redirige una vez se termine la seleccion es PAGE_CONSUMO_FORM, nunca se pasa por aquí.
    $page->set_current_establecimiento($selected_estid);	
	$establecimiento = $page->get_current_establecimiento();
}
else
{
	$establecimiento = $page->get_current_establecimiento();
	$page->abort_si_estado_baja($establecimiento);
}

$viewvars = array();

$factura_ctl = new FacturaController();

$establecimiento = $page->get_current_establecimiento();

if($establecimiento == null)
{
    //// TODO: Cambiar esta dirección de error.
    gestionarError("No se ha definido el establecimiento correctamente.");
}


$facturas_recientes=$factura_ctl->buscarFacturasRecientes($establecimiento->id_establecimiento);

$viewvars['facturasRecientes']=$facturas_recientes;

//Si es hotel o apartamento
$viewvars['es_hotel'] = $establecimiento->es_hotel();

/// Render de la pagina
$page->render( "consumo_index_view.php", $viewvars );


$page->end_session();


function gestionarError($mensaje)
{
    global $page;
    
    $page->abort_with_error(PAGE_HOME, $mensaje);
}
?>
