<?php 

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/lib/RowDbIterator.class.php");
require_once(__DIR__."/classes/Establecimiento.class.php");
require_once(__DIR__."/classes/EstablishmentUser.class.php");
require_once(__DIR__."/classes/EstablishmentUserDao.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR), array(PAGE_ESTAB_ADMIN));


/// Parametros de la pagina
define("ARG_USER_ID" , "userid");
define("OP_CAMBIA_ESTADO" , $page->is_post() && isset($_POST[ARG_OP]));

$query_id_est = $page->request_post_or_get(ARG_ESTID, NULL);

$optit="Modificar establecimiento";

/// OPERACION 1: No se ha indicado ningun establecimiento, mostrar la pagina de busqueda.
if ($query_id_est == '')
{
	/// - Si no se ha llamado con el argumento est_id (el id. de establecimiento), convertir esta pagina en una pagina de busqueda.
	$navToUrl = $page->self_url(NULL, TRUE);
	require_once(PAGE_ESTAB_SEARCH);
	$page->end_session();
	exit();
}

/// OPERACION 2: Se da de baja o de alta al establecimiento indicado por argumentos en la llamada.
if (OP_CAMBIA_ESTADO)
{
	$query_op = $page->request_post(ARG_OP);
	
	$establecimiento = $page->load_establecimiento($query_id_est);

// 	if ($query_op == 'alta')
// 	{
// 		$establecimiento->dar_de_alta();
// 	}
// 	else if ($query_op == 'baja')
// 	{
// 		$establecimiento->dar_de_baja();
// 	}
}
else 
{
	/// OPERACION 3: Mostrar los datos del establecimiento indicado y sus usuarios.
	$establecimiento = $page->load_establecimiento($query_id_est);
}

/// OPERACION 2 y 3: Obtener los usuarios del establecimiento con id = $query_id_est.
$estDao = new EstablishmentUserDao();
$users = $estDao->search( $query_id_est );

$variables = array(
  'page'					=>	$page,
  'actionEditUserUrl' 		=> 	$page->build_url(PAGE_USER_ADMIN, array(ARG_ESTID => $establecimiento->id_establecimiento, ARG_OP => 'edit')),
  'actionNewUserUrl'		=>  $page->build_url(PAGE_USER_ADMIN, array(ARG_ESTID => $establecimiento->id_establecimiento, ARG_OP => 'new')),
  'establecimiento' 		=> 	$establecimiento,
  'usersData'				=>	new ArrayIterator( $users ),
  'userProfiles'			=>  array(PERM_USER		=>	PERMISOS_USUARIO_TEXT,
                                    PERM_RECEPCION	=>	PERMISOS_RECEPCION_TEXT,
                                    PERM_CONSUMOS	=>	PERMISOS_CONSUMOS_TEXT)
  );

/// Render de la pagina
$page->render( "admin_estab_edit_view.php", $variables );
$page->end_session();

?>