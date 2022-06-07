<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/aloja/AlojaErrorCollection.class.php");
require_once(__DIR__."/classes/aloja/AlojaDao.class.php");
require_once(__DIR__."/classes/aloja/AlojaUTMovimientos.class.php");
require_once(__DIR__."/classes/aloja/AlojaESP.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION), array(PAGE_ALOJA_FORM_ENVIO));

define("ARG_CESEACTIVIDAD", "ceseActividad");

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

$aloja_ctl = new AlojaController();
$aloja_dao = new AlojaDao();

$aloja_enc =  $aloja_dao->cargar_registro_cuestionario($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);

if ($aloja_enc == null)
{
	/// 2. No existe cuestionario para esa fecha, abortar la operacion.
	$page->abort_with_error(PAGE_HOME, "No existe cuestionario para el mes y año elegidos.");
}

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

// Mecanismo de seguridad redundante: Cuando un usuario quiere cerrar un cuestionario del mes en curso, exigimos que haya activado la casilla correspondiente en el formulario.
$permitir_cerrar_cuestionario_no_vencido=$es_admin;
if($permitir_cerrar_cuestionario_no_vencido==false)
{
	$ceseActividad = $page->request_post_or_get(ARG_CESEACTIVIDAD);
	if($ceseActividad!=NULL && $ceseActividad=='1')
		$permitir_cerrar_cuestionario_no_vencido=true;
}

//NOTA: Se espera que el cuestionario ya haya pasado los procesos de validacion y en la BBDD esta el estado actual.
/// 3. Cerrar el cuestionario
$ok = $aloja_ctl->cerrar_cuestionario($aloja_enc, $permitir_cerrar_cuestionario_no_vencido);
if ($ok !== true)
{
	/// 4. Fallo de cierre del cuestionario (no cumple las reglas de cierre o error interno).
    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array($ok),array("año" => $aloja_enc->ano, "mes" => $aloja_enc->mes));
	handle_errors($page, $establecimiento->es_hotel(), new AlojaErrorCollection(array($ok)));
	exit;
}

@$aloja_ctl->enviar_correo_confirmacion_cierre($establecimiento, $aloja_enc);

/// 5. Cierre del cuestionario completado.
@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, SUCCESSFUL,NULL,array("año" => $aloja_enc->ano, "mes" => $aloja_enc->mes));
$url_acuse = $page->build_url(PAGE_ALOJA_ACUSE, array('mes_encuesta'=>$aloja_enc->mes, 'ano_encuesta'=>$aloja_enc->ano));

$email = $establecimiento->email;
handle_ok($page, $establecimiento->es_hotel(), $url_acuse, $email);

function handle_errors($page, $es_hotel, AlojaErrorCollection $errordata, $global_msg = null)
{
	$vv['es_encuesta_web'] = true;
	$vv['es_hotel'] = $es_hotel;
	$vv['errors'] = $errordata;
	$vv['global_msg'] = $global_msg;
	$page->render("aloja_xml_errores_view.php", $vv);
	$page->end_session();
}

function handle_ok($page, $es_hotel, $url_acuse, $email)
{
	$vv['url_acuse'] = $url_acuse;
	$vv['es_encuesta_web'] = true;
	$vv['es_hotel'] = $es_hotel;
	$vv['errors'] = null;
	$vv['email'] = $email;
	$page->render("aloja_xml_errores_view.php", $vv);
	$page->end_session();
}


?>