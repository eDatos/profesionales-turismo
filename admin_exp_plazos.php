<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/lib/Trimestre.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasPlazos.class.php");

/// Redirigir a la pagina HOME si no se tienen permisos de acceso a esta pagina.
$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC), array(PAGE_EXP_PLAZOS));

define("OP_GUARDAR_PLAZOS", "save");

$viewvars = array();
$viewvars['show_ok'] = false;

if ($page->request_post(ARG_OP) == OP_GUARDAR_PLAZOS)
{
	$plazos = array_fill(0,5,null);
	$ok = validar($page, $plazos);
	$errs = array();
	if (!$ok)
	{
		for ($i = 1; $i <= 4; $i++)
		{
			if (!$plazos[ $i ]['valido'])
			{
				$errs[] = "El plazo indicado para el trimestre {$i} no es válido.";
			}
		}	
		$viewvars['errs'] = $errs;
	}
	else 
	{
		$pl_2 = array();
		for ($i = 1; $i <= 4; $i++)
		{
			$pl_2 [ $i ] = $plazos[ $i ]['plazo'];
		}
		
		ExpectativasPlazos::guardar_plazos($pl_2);
		unset($viewvars['errs']);
		$viewvars['show_ok'] = true;
	} 
}

$viewvars['plazos'] = ExpectativasPlazos::cargar_plazos();

$page->render( "admin_exp_plazos_view.php", $viewvars);
$page->end_session();


/**
 * Validar la entrada de plazos a partir de los parametros del POST.
 * @param unknown_type $page
 * @param unknown_type $nuevos_plazos
 * @return boolean
 */
function validar($page,& $nuevos_plazos)
{
	//PARAMS: trim{$i}_check, trim{$i}_ini, trim{$i}_fin, donde _check es un checkbox (1 o no definido), _ini y _fin , fechas incio y fin respectivamente.
	for ($i = 1; $i <= 4; $i++)
	{
		$np = null;
		$nuevos_plazos[ $i ]['valido'] = false;
		if ($page->request_post("trim{$i}_check") == "1")
		{
			$np = new ExpectativasPlazos();
			$np->trimestre = new Trimestre( $i , date('Y'));
			$np->fecha_inicio = DateHelper::parseDate($page->request_post("trim{$i}_ini"));
			$np->fecha_fin = DateHelper::parseDate($page->request_post("trim{$i}_fin"));
			
			if ($np->fecha_inicio !== false && $np->fecha_fin !== false)
			{
				if (isset($np->fecha_inicio) && isset($np->fecha_fin) && $np->fecha_inicio < $np->fecha_fin)
				{
					$nuevos_plazos[ $i ]['valido'] = true;
				}
			}
		}
		else 
		{
			//PLAZO VACIO
			$nuevos_plazos[ $i ]['valido'] = true;
			$nuevos_plazos[ $i ]['plazo'] = null;
		}
		if ($nuevos_plazos[ $i ]['valido'])
			$nuevos_plazos[ $i ]['plazo'] = $np;
	}
	
	return $nuevos_plazos[ 1 ]['valido'] && $nuevos_plazos[ 2 ]['valido'] 
	&& $nuevos_plazos[ 3 ]['valido'] && $nuevos_plazos[ 4 ]['valido'];
}

?>