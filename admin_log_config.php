<?php 
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLogConfig.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC), array(PAGE_LOG_CONFIG));

define('ARG_BEFOREDATE', 'beforeDate');
define('ARG_OPTIONS', 'opt');
define('ORIG_OPTIONS', 'opt_orig_values');
define('OP_DELETE_ENTRIES', $page->is_post() && $page->request_post(ARG_OP, '')=='del');
define('OP_UPDATE_CONFIG', $page->is_post() && $page->request_post(ARG_OP, '')=='save');
define('OP_CANCEL', $page->is_get() && $page->request_get(ARG_OP, '')=='cancel');


$config = new AuditLogConfig();
$showOkMsg = FALSE;
$showOkDelMsg = FALSE;
$deletedOk = TRUE;

if (OP_CANCEL && isset($_GET[ARG_REF]))
{
	// CASO 0: Se pulsa en otro vinculo de la pagina, cancelando la posible operacion sobre el log que hubiera.
	unset($_SESSION[ORIG_OPTIONS]);
	$page->client_redirect($_GET[ARG_REF]);
	$page->end_session();
	exit;
}
if (OP_DELETE_ENTRIES && isset($_POST[ARG_BEFOREDATE]))
{
	// CASO 1: OPERACION DE ELIMINACION DE REGISTROS DEL LOG
	$pv = trim($page->request_post(ARG_BEFOREDATE, ''));
	$befDate = DateHelper::parseDate($pv);
	if ($befDate !== FALSE)
	{
		$showOkDelMsg = $config->clearEntriesBeforeDate($befDate);
		$deletedOk = TRUE;
	}
	else 
	{
		$deletedOk = FALSE;
	}
}
else if (OP_UPDATE_CONFIG && isset($_POST[ARG_OPTIONS]) && isset($_SESSION[ORIG_OPTIONS]))
{
	// CASO 2: OPERACION DE ACTUALIZACION DE ESTADOS DE OPCIONES DE CONFIGURACION
	$s_originales = $_SESSION[ORIG_OPTIONS];
	unset($_SESSION[ORIG_OPTIONS]);
	
	$p_nuevos = $page->request_post(ARG_OPTIONS,$s_originales);
	
	$opt_cambiadas=array();
	foreach($s_originales as $opt_id => $orig_estado)
	{
		if (array_key_exists($opt_id, $p_nuevos))
		{
			if ($orig_estado == 0)
				$opt_cambiadas[$opt_id] = 1;
		}
		else 
		{
			if ($orig_estado == 1)
				$opt_cambiadas[$opt_id] = 0;
		}
	}
	
	$config->updateConfigFlags($opt_cambiadas);
	
	$showOkMsg = TRUE;
}

// EN AMBOS CASOS: LECTURA DE LAS OPCIONES DE CONFIGURACION
$iter = $config->loadConfigOptions();

$grupos = array();
$s_originales = array();
foreach ($iter as $row)
{
	$s_originales[$row['id']] = $row['estado'];
	$grupos[$row['grupo']][$row['id']] = array( $row['descripcion'], $row['estado'] );
}

/// Almacenar en sesion el estado original de las opciones
$_SESSION[ORIG_OPTIONS]=$s_originales;

/// Controlar que si se pulsa en otro vinvulo de la app se eliminen los datos de sesion.
$page->set_nav_handler(PAGE_LOG_CONFIG, array(ARG_OP => 'cancel'));

$vars = array(
		'opcionesExp' => $grupos[1],		
		'opcionesAloja' => $grupos[2],
		'opcionesOtras' => $grupos[3],
		'showOkOptsMsg' => $showOkMsg,
		'showOkDelMsg' => $showOkDelMsg,
		'deletedOk' => $deletedOk
		);

$page->render( "admin_log_config_view.php", $vars );
$page->end_session();

?>