<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/lib/Trimestre.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasFormData.class.php");

/// Redirigir a la pagina HOME si no se tienen permisos de acceso a esta pagina.
$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC), array(PAGE_EXP_ADMIN_CUESTIONARIOS));

define ("OP_BORRAR_CUEST", "delete");

$original_params = null;
if ($page->request_get(ARG_OP) == OP_BORRAR_CUEST)
{
    $original_params[ARG_OP] = OP_BORRAR_CUEST;
}

/// Mostrar las pagina de busqueda de establecimiento y trimestre y año de trabajo.
$optit=get_title($page->request_get(ARG_OP));
list($selected_estid,$selected_trimestre) = $page->select_establecimiento_trimestre($page->self_url($original_params, TRUE));
$page->set_current_establecimiento($selected_estid);
$establecimiento = $page->get_current_establecimiento();

if ($page->request_get(ARG_OP) == OP_BORRAR_CUEST)
{
    $exp_dao = new ExpectativasFormDao();
    $ok_o_errormsg = $exp_dao->eliminar($establecimiento->id_establecimiento, $selected_trimestre->trimestre, $selected_trimestre->anyo);
    if ($ok_o_errormsg !== true)
        @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, BORRA_CUESTIONARIO_EXPECTATIVAS, FAILED, array($ok_o_errormsg),array("año" => $selected_trimestre->anyo, "trimestre" => $selected_trimestre->trimestre));
    else
        @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, BORRA_CUESTIONARIO_EXPECTATIVAS, SUCCESSFUL,NULL,array("año" => $selected_trimestre->anyo, "trimestre" => $selected_trimestre->trimestre));

    $viewvars = array( 'ok_o_errormsg' => $ok_o_errormsg);

    $page->render("admin_exp_cuestionarios_view.php", $viewvars);
    $page->end_session();
}

function get_title($op)
{
	switch($op)
	{
		case OP_BORRAR_CUEST: return "Borrar cuestionario";
		default: return "";
	}
}

?>