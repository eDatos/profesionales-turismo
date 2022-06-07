<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/EstablishmentUserDao.class.php");

isset($page) OR $page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR), array(PAGE_USER_SEARCH));

define("ARG_NOMBRE_USER", "nombre");
define('OP_SEARCH', $page->is_post() && $page->request_post(ARG_OP, '')=='suser');


/// Obtener la lista de establecimientos según su nombre o codigo.
$resultset = NULL;
if (OP_SEARCH && isset($_POST[ARG_NOMBRE_USER])) 
{
	$query_nombre = $page->request_post(ARG_NOMBRE_USER, '');
	
	$e = new EstablishmentUserDao();
	$resultset = $e->search_users_by_name($query_nombre);
}

/// Preparacion de la vista de la pagina
$variables = array(
	'page' => $page,
	'user_nombre' => isset($query_nombre)?$query_nombre:'',
	'data' => $resultset,
	'navToUrl' => (isset($navToUrl))? $navToUrl : $page->self_url()
);

/// Render de la pagina
$page->render( "admin_user_search_view.php", $variables );


$page->end_session();
?>