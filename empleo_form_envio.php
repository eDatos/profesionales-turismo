<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");
require_once(__DIR__."/classes/EmpleoErrorCollection.class.php");
require_once(__DIR__."/classes/EmpleoController.class.php");

// ARG_FASE: Indica en qué fase del cuestionario de empleo estamos.
// FASE 0: Mostrar la pantalla de empleadores internos (cuentas de cotización)
// FASE 1: Encuesta de empleo para empleadores internos
// FASE 2: Mostrar la pantalla de empleadores externos (ETTs y otros)
// FASE 3: Encuesta de empleo para empleadores externos
define('ARG_FASE','f');

$page = PWETPageHelper::start_page(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION), array(PAGE_EMPLEO_FORM_ENVIO));

/// A partir de los parametros mes y año pasados (POST) y el establecimiento utilizado en la sesion, 
/// realiza la operacion de cierre del cuestionario siempre que su estado de validacion lo permita.

$establecimiento = $page->get_current_establecimiento();
$mes_encuesta = $page->request_post(ARG_MES);
$ano_encuesta = $page->request_post(ARG_ANO);


/// Comprobacion de parametros, respuesta vacía en caso de que alguno sea erroneo.
if ($establecimiento == NULL || $mes_encuesta == null || $ano_encuesta == null)
{
	$page->logout();
	$page->client_redirect(PAGE_HOME, NULL, FALSE);
	exit;
}

if($page->have_any_perm(array(PERM_USER,PERM_RECEPCION)))
{
	/// 1. Los usuarios no pueden rellenar una encuesta si el establecimiento está dado de baja.
	$page->abort_si_estado_baja($establecimiento);
}

$empleo_ctl = new EmpleoController();

$empleo_cuest = $empleo_ctl->dao->cargar_cuestionario($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);

if ($empleo_cuest == null)
{
	/// 2. No existe cuestionario para esa fecha, abortar la operacion.
	$page->abort_with_error(PAGE_HOME, "No existe cuestionario para el mes y año elegidos.");
}


$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

//NOTA: Se espera que el cuestionario ya haya pasado los procesos de validacion y en la BBDD esta el estado actual.
/// 3. Cerrar el cuestionario
if($es_admin)
    $empleo_cuest->fecha_cierre=null;
$res = $empleo_ctl->cerrar_cuestionario($empleo_cuest);
if ($res !== true)
{
    /// 4. Fallo de cierre del cuestionario (no cumple las reglas de cierre o error interno).
    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_EMPLEO, FAILED, array($res),array("año" => $empleo_cuest->ano, "mes" => $empleo_cuest->mes));
    handle_errors($page, $establecimiento->es_hotel(), new EmpleoErrorCollection(array($res)));
    exit;
}




/// 4. Cierre del cuestionario completado.
@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_EMPLEO, SUCCESSFUL,NULL,array("año" => $empleo_cuest->ano, "mes" => $empleo_cuest->mes));
// Cuando se trate del administrador hay que añadir también el id del establecimiento??
if($es_admin)
{
    $url_acuse = $page->build_url(PAGE_EMPLEO_ACUSE, array(ARG_MES=>$empleo_cuest->mes, ARG_ANO=>$empleo_cuest->ano));
}
else
{
    $url_acuse = $page->build_url(PAGE_EMPLEO_ACUSE, array(ARG_MES=>$empleo_cuest->mes, ARG_ANO=>$empleo_cuest->ano));
}

$email = $establecimiento->email;
handle_ok($page, $establecimiento->es_hotel(), $url_acuse, $email);

function handle_errors($page, $es_hotel, EmpleoErrorCollection $errordata, $global_msg = null)
{
    global $empleo_cuest;
    
	$vv['es_hotel'] = $es_hotel;
	$vv['errors'] = $errordata;
	$vv['global_msg'] = $global_msg;
	if($es_admin)
	{
	    // Cuando se trate del administrador hay que añadir también el id del establecimiento??
	    $vv['urlVolver'] = $page->build_url(PAGE_EMPLEO_FORM, array(ARG_MES=>$empleo_cuest->mes, ARG_ANO=>$empleo_cuest->ano, ARG_FASE => 3));
	}
	else
	{
	    $vv['urlVolver'] = $page->build_url(PAGE_EMPLEO_FORM, array(ARG_FASE => 3));
	}
	$page->render("empleo_xml_errores_view.php", $vv);
	$page->end_session();
}

function handle_ok($page, $es_hotel, $url_acuse, $email)
{
	$vv['url_acuse'] = $url_acuse;
	$vv['es_hotel'] = $es_hotel;
	$vv['errors'] = null;
	$vv['email'] = $email;
	$page->render("empleo_xml_errores_view.php", $vv);
	$page->end_session();
}


?>