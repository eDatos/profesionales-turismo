<?php /// Controlador de multiples operaciones referentes a encuestas de alojamiento.
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/aloja/AlojaTempDao.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/lib/DateHelper.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC), array(PAGE_ALOJA_CONTROL_MULTIPLE));

define ("OP_ABRIR_CUEST", "ac");
define ("OP_BORRAR_CUEST", "bc");

$original_params = null;

$op = $page->request_get(ARG_OP);
if ($op == null)
{
	$page->abort_with_error(PAGE_ALOJA_CONTROL, "No se ha definido la operación a realizar.");
}

$original_params[ARG_OP] = $op;

/// Mostrar las pagina de busqueda de establecimiento y mes y año de trabajo.
$optit=get_title($op);
list($selected_estids,$mes_trabajo, $ano_trabajo) = $page->select_establecimiento_mes_ano_multiple($page->self_url($original_params, TRUE));

$ids=explode(",", $selected_estids);

if(empty($ids))
{
	$page->abort_with_error(PAGE_ALOJA_CONTROL, "No se ha definido los establecimientos afectados.");
}

$view_vars = array();

$view_vars[ARG_OP] = $op;
$view_vars['op_titulo'] = get_title($op);
$res = true;

$errores=array();

$ctl = new AlojaController();
$dao = new AlojaDao();

foreach ($ids as $selected_estid)
{
	if ($op == OP_ABRIR_CUEST)
	{
		$enc =  $dao->cargar_registro_cuestionario($selected_estid, $mes_trabajo, $ano_trabajo);
		if ($enc == null)
		{
			$res=false;
			array_push($errores,"No existe cuestionario para el establecimiento, el mes y el año especificados (código establecimiento='".$selected_estid."',mes='".DateHelper::mes_tostring($mes_trabajo)."', año='".$ano_trabajo."').");
		}
		else {
			$r = $ctl->abrir_cuestionario($enc);
			if ($r !== true)
			{
				$res=false;
				array_push($errores,$r." (código establecimiento='".$selected_estid."',mes='".DateHelper::mes_tostring($mes_trabajo)."', año='".$ano_trabajo."').");
			}
		}
	}
	else if ($op == OP_BORRAR_CUEST)
	{
		$enc =  $dao->cargar_registro_cuestionario($selected_estid, $mes_trabajo, $ano_trabajo);
		if ($enc == null)
		{
			$res=false;
			array_push($errores,"No existe cuestionario para el establecimiento, el mes y el año especificados (código establecimiento='".$selected_estid."',mes='".DateHelper::mes_tostring($mes_trabajo)."', año='".$ano_trabajo."').");
		}
		else {
			$id_cuestionario=$enc->id;
			$r = $ctl->borrar_cuestionario($enc);
			if ($r !== true)
			{
				$res=false;
				array_push($errores,$r);
				@AuditLog::log($page->get_current_userid(), $selected_estid, BORRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array($r),array("año" => $enc->ano, "mes" => $enc->mes));
			}
			else
			{
			    @AuditLog::log($page->get_current_userid(), $selected_estid, BORRA_CUESTIONARIO_ALOJAMIENTO, SUCCESSFUL,NULL,array("año" => $enc->ano, "mes" => $enc->mes));
			}
			
			// En caso de existir datos temporales también debemos borrarlos.
			$daoTemp = new AlojaTempDao();
			$daoTemp->eliminar_cuestionario_temp($id_cuestionario);
		}
	}
}

$view_vars['res'] = $res;
$view_vars['errores'] = $errores;
$page->render("admin_aloja_control_view.php", $view_vars);
$page->end_session();


function get_title($op)
{
	switch($op)
	{
		case OP_ABRIR_CUEST: return "Abrir cuestionario";
		case OP_BORRAR_CUEST: return "Borrar cuestionario";
		case OP_CERRAR_CUEST: return "Cerrar cuestionario";
		default: return "";
	}
}

?>