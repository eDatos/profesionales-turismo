<?php /// Controlador de multiples operaciones referentes a encuestas de alojamiento.
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaPlazosDao.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC), array(PAGE_ALOJA_INDEX));

define("OP_GUARDAR_PLAZOS", "save");
define("OP_CAMBIAR_GRUPO", "chggrp");

$dao = new AlojaPlazosDao();
$grupo_elegido = 0; //Por defecto, el grupo '0' (Todos).
$view_vars = array('show_ok' => false);

if ($page->request_post(ARG_OP) == OP_CAMBIAR_GRUPO)
{
	$grupo_elegido = $page->request_post('grupo');
}
else if ($page->request_post(ARG_OP) == OP_GUARDAR_PLAZOS)
{
	$grupo_elegido = $page->request_post('grupo'); 
	$nuevos_plazos = $page->request_post('pl');
	$original_plazos = $page->request_post('orig_pl');

	$validos = validar_plazos($nuevos_plazos);
	
	if ($validos === true)
	{
		$ok = $dao->actualizar_plazos($grupo_elegido, $original_plazos, $nuevos_plazos);
		if ($ok)
		{
			$view_vars['show_ok'] = true;
		}
		else 
		{
			$view_vars['errs'] = array('No se han podido guardar los cambios.');
		}
	}
	else 
	{
		// validos contiene mensajes de error en caso de que los haya
		$view_vars['errs'] = $validos;
	}
}

/// Grupos de establecimiento para rellenar la combobox.
$view_vars['grupos'] = $dao->cargar_grupos_establecimiento();

//Asegurar que el grupo existe en la BBDD y marcarlos como seleccionado, sino seleccionar el primero.
$existe = false;
for ($i = 0; $i < count($view_vars['grupos']); $i++)
{
	if ($view_vars['grupos'][$i]['id'] == $grupo_elegido)
	{
		$view_vars['grupos'][$i]['sel'] = true;
		$existe = true;
		break;
	}
}
if (!$existe)
{
	/// 0 no existe como grupo, seleccionar el primero
	$grupo_elegido = $view_vars['grupos'][0]['id'];
}

/// Plazos para el grupo seleccionado.
$view_vars['plazos'] = $dao->cargar_plazos_por_grupo($grupo_elegido);

$page->render("admin_aloja_plazos_view.php", $view_vars);
$page->end_session();

function is_integer2($v) {
	$i = intval($v);
	if ("$i" == "$v") {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Validacion de servidor de los datos posteados por el formulario.
 * @param unknown_type $plazos
 * @return boolean|multitype:string
 */
function validar_plazos($plazos)
{
	/// para enero (1) son 29 (los dias de febrero), para febrero (2) 31 (los dias de marzo), ...
	$max_dias = array(0,29,31,30,31,30,31,31,30,31,30,31,31);
	$valido = array();
	for($i=1;$i<=12;$i++)
	{
		if (isset($plazos[$i]) && strlen($plazos[$i]) > 0)
		{
			if (!is_integer2($plazos[$i]))
			{
				$valido[] = "El valor para la encuesta de " . DateHelper::mes_tostring($i, 'M') . " debe ser un número entero";
				continue;
			}
			$dia_mes = (int)$plazos[$i];
			if (!(1 <= $dia_mes && $dia_mes <= $max_dias[$i]))
			{
				$valido[] = "El valor para la encuesta de " . DateHelper::mes_tostring($i, 'M') . " debe estar entre 1 y {$max_dias[$i]}";
			}
			//Plazo valido
		}
	}
	if (empty($valido))
		return true;
	return $valido;
}

?>