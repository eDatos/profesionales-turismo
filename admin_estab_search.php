<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/Establecimiento.class.php");

isset($page) OR $page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR));
$page->set_page_path(array(PAGE_ESTAB_SEARCH));

/// Redirigir a la pagina HOME si no se tienen permisos de acceso a esta pagina.
//$page->start_session_check(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR));

define("ARG_TIPO_BUSQUEDA", "tipo_busqueda");
define("ARG_NOMBRE_EST", "nombre");
define("ARG_CODIGO_EST", "codigo");
define('OP_SEARCH', $page->is_post());

/// Obtener la lista de establecimientos según su nombre o codigo.
$resultset = NULL;
if (OP_SEARCH && isset($_POST[ARG_TIPO_BUSQUEDA]) 
		      && (isset($_POST[ARG_NOMBRE_EST]) || isset($_POST[ARG_CODIGO_EST])))
{
	$query_tipo_busqueda = $page->request_post(ARG_TIPO_BUSQUEDA, '');
	
	$e = new Establecimiento();
	if($query_tipo_busqueda=='nombre')
	{
		$query_estab_nombre = $page->request_post(ARG_NOMBRE_EST, '');
		/// Busqueda por nombre de establecimiento
		$resultset = $e->searchByNombre($query_estab_nombre);
	}
	else 
	{
		$query_estab_codigo = $page->request_post(ARG_CODIGO_EST, '');
		/// Busqueda por codigo de establecimiento
		$resultset = $e->searchByCodigo($query_estab_codigo);	
	}
}

/// Preparacion de la vista de la pagina
$variables = array(
	'optit' => $optit,
	'estab_nombre' => isset($query_estab_nombre)?$query_estab_nombre:'',
	'estab_codigo' => isset($query_estab_codigo)?$query_estab_codigo:'',
	'data' => $resultset,
	'navToUrl' => (isset($navToUrl))? $page->build_url($navToUrl) : $page->self_url()
);

/// Render de la pagina
$page->render( "admin_estab_search_view.php", $variables );


$page->end_session();
?>