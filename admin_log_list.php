<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC), array(PAGE_LOG_SEARCH, PAGE_LOG_LIST));

define('ARG_BEGINDATE', 'from');
define('ARG_ENDDATE', 'to');
define('ARG_ACTIONTYPE', 'foraction');
define('ARG_USER', 'foruser');
define('ARG_USER_TYPE', 'usertype');
define('FORM_STATE','log_search_form_state');

/// Guardar estado formulario en sesion
$estado_form[ARG_BEGINDATE] =  $page->request_post_or_get(ARG_BEGINDATE, NULL);
$estado_form[ARG_ENDDATE] = $page->request_post_or_get(ARG_ENDDATE, NULL);
$estado_form[ARG_ACTIONTYPE] = $page->request_post_or_get(ARG_ACTIONTYPE, NULL);
$estado_form[ARG_USER] = $page->request_post_or_get(ARG_USER, NULL);
$estado_form[ARG_USER_TYPE] = $page->request_post_or_get(ARG_USER_TYPE, NULL);
$page->set_sess_state(FORM_STATE, $estado_form);

$actid = $estado_form[ARG_ACTIONTYPE];
$username =$estado_form[ARG_USER];
$usertype = $estado_form[ARG_USER_TYPE];

$logdata = RowDbIterator::zero();

$begindate = DateHelper::parseDate($estado_form[ARG_BEGINDATE]);
$enddate = DateHelper::parseDate($estado_form[ARG_ENDDATE]);

$errorlist = array();
if ($usertype == 'SEL' && $username == NULL)
	$errorlist[] = "No se ha indicado ningún usuario.";
if ($begindate === FALSE)
	$errorlist[] = "La fecha de inicio no es correcta.";
if ($enddate === FALSE)
	$errorlist[] = "La fecha de fin no es correcta.";

if ($usertype == 'TODOS')
	$username = NULL;

if (isset($actid) && $actid == '')
	$actid = NULL;
		
if (isset($begindate) && isset($enddate) && $begindate < $enddate)
{
	if ($usertype == 'TODOS' || ($usertype == 'SEL' && $username != NULL))
		$logdata = AuditLog::listByFecha($begindate->format('d/m/Y'), $enddate->format('d/m/Y'), $actid, $username);
}
else 
{
	$errorlist[] = "La fecha de fin debe ser superior a la fecha de inicio.";
}

$viewvars = array(
		'logdata'=> $logdata,  
		'errors' => $errorlist, 
		'detailUrl' => $page->build_url(PAGE_LOG_DETAIL));

$page->render( "admin_log_list_view.php", $viewvars);
$page->end_session();

?>
