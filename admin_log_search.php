<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLogConfig.class.php");

/// Redirigir a la pagina HOME si no se tienen permisos de acceso a esta pagina.
$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC), array(PAGE_LOG_SEARCH));

define('ARG_BEGINDATE', 'from');
define('ARG_ENDDATE', 'to');
define('ARG_ACTIONTYPE', 'foraction');
define('ARG_USER', 'foruser');
define('ARG_USER_TYPE', 'usertype');
define('OP_USER_SEARCH_START',$page->is_post() && $page->request_post(ARG_OP, '')=='searchuser');
define('OP_USER_SEARCH_DO',$page->is_post() && $page->request_post(ARG_OP, '')=='suser');
define('OP_USER_SEARCH_RETURN',$page->is_get() && isset($_GET['foruser']));
define('OP_CANCEL', $page->is_get() && $page->request_get(ARG_OP, '')=='cancel');
define('FORM_STATE','log_search_form_state');


if (OP_CANCEL && isset($_GET['ref']))
{
	// CASO 0: Se pulsa en otro vinculo de la pagina, cancelando la posible operacion sobre el log que hubiera.
	$page->unset_sess_state(FORM_STATE);
	$page->client_redirect($_GET['ref']);
	exit;
}
/// OPERACION 1 y 2: Busqueda de usuario.
if (OP_USER_SEARCH_START || OP_USER_SEARCH_DO)
{
	if (OP_USER_SEARCH_START)
	{
		/// Guardar estado formulario en sesion
		$estado_form[ARG_BEGINDATE] = $page->request_post(ARG_BEGINDATE);
		$estado_form[ARG_ENDDATE] = $page->request_post(ARG_ENDDATE);
		$estado_form[ARG_ACTIONTYPE] = $page->request_post(ARG_ACTIONTYPE);
		$estado_form[ARG_USER] = $page->request_post(ARG_USER);
		$estado_form[ARG_USER_TYPE] = $page->request_post(ARG_USER_TYPE);
		$page->set_sess_state(FORM_STATE, $estado_form);
	}
	/// Redirigir esta peticion a la pagina de seleccion de usuario.
	$navToUrl = $page->self_url(NULL, TRUE);
	require_once(PAGE_USER_SEARCH);
	$page->end_session();
	exit();
}

// OPERACION 3: Obtener criterios de consulta
$estado_form = $page->get_sess_state(FORM_STATE);
if ($estado_form == null) 
{
	/// Valores por defecto
	$now = new DateTime();
	$lastweek = new DateTime();
	$lastweek->sub(new DateInterval('P1W'));
	$estado_form[ARG_BEGINDATE] = $lastweek->format('d/m/Y');
	$estado_form[ARG_ENDDATE] = $now->format('d/m/Y');
	$estado_form[ARG_ACTIONTYPE] = '';
	$estado_form[ARG_USER] = '';
	$estado_form[ARG_USER_TYPE] = 'TODOS';
	$page->set_sess_state(FORM_STATE, $estado_form);
}

if (OP_USER_SEARCH_RETURN)
{
	//unset($_SESSION[FORM_STATE]);
	$estado_form[ARG_USER] = $page->request_get('foruser',$estado_form[ARG_USER]);
}

$config = new AuditLogConfig();
$actiontypes = $config->loadActionTypes();

$page->set_nav_handler(PAGE_LOG_SEARCH, array(ARG_OP => 'cancel'), array(PAGE_LOG_LIST));

$viewvars = array(
		'defuser'	  => $estado_form[ARG_USER],
		'defbegindate'=> $estado_form[ARG_BEGINDATE],
		'defenddate'  => $estado_form[ARG_ENDDATE],
		'defacttype'  => $estado_form[ARG_ACTIONTYPE],
		'defusertype' => $estado_form[ARG_USER_TYPE],
		'actionUrl'	  => $page->build_url(PAGE_LOG_LIST),
		'actiontypes' => $actiontypes
		);

$page->render( "admin_log_search_view.php", $viewvars);
$page->end_session();

?>
