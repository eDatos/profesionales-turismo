<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/lib/ext/PDF.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");
require_once(__DIR__."/classes/EmpleadoresDao.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION), array(PAGE_EMPLEO_ACUSE));

function err_detail($id_est,$mes,$anio)
{
    $salida="est=";
    if($id_est!=null)
        $salida.=$id_est;
    $salida.="mes=";
    if($mes!=null)
        $salida.=$mes;
    $salida.="anio=";
    if($anio!=null)
        $salida.=$anio;
    return $salida;
}


//Se recogen los parámetros para cargar la encuesta
$mes_encuesta = $page->request_post_or_get(ARG_MES, NULL);
$ano_encuesta = $page->request_post_or_get(ARG_ANO, NULL);

$establecimiento = $page->get_current_establecimiento();

/// 1. Obtener el establecimiento y mes/año con el que se va a trabajar
$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));
if($es_admin)
{
    //if($page->request_post_or_get(ARG_ESTID, NULL)!=NULL)
	if(($establecimiento == null) || ($mes_encuesta==NULL) || ($ano_encuesta==NULL))
	{
		$optit="Acuse de recibo";
		list($selected_estid,$mes_encuesta, $ano_encuesta) = $page->select_establecimiento_mes_ano($page->self_url(NULL, TRUE));
		$page->set_current_establecimiento($selected_estid);
		$establecimiento = $page->get_current_establecimiento();
	}
}

if ($establecimiento == null)
{
    $page->abort_with_error(PAGE_HOME, "No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

if($page->have_any_perm(array(PERM_USER,PERM_RECEPCION)))
{
	/// 1b. Los usuarios no pueden rellenar una encuesta si el establecimiento está dado de baja.
	$page->abort_si_estado_baja($establecimiento);
}

$dao = new EmpleadoresDao();
$enc =  $dao->cargar_cuestionario($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
if ($enc == null)
{
    $detalles=err_detail($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
    $page->abort_with_error(PAGE_HOME, "No existe cuestionario para el establecimiento, el mes y el año especificados.".$detalles);
}

// NO SE PUEDE IMPRIMIR ACUSE SI EL CUESTIONARIO NO ESTA CERRADO
if (!$enc->esta_cerrado())
{
    $detalles=err_detail($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
	$page->abort_with_error(PAGE_HOME, "El cuestionario para el establecimiento, el mes y el año especificados no está cerrado.".$detalles);
}

$page->render("empleo_acuse_ok_view.php", array("mes_encuesta"=>$mes_encuesta, "ano_encuesta"=>$ano_encuesta, "urlVolver" => ($es_admin)?PAGE_HOME:PAGE_EMPLEO_INDEX));
$page->end_session();
?>