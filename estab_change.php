<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/Establecimiento.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_ESTAB_CHANGE));

$establecimiento = $page->get_current_establecimiento();
$view_vars = array('establecimiento' => $establecimiento);


if ($page->request_post(ARG_OP) == 'ce')
{
	/// Registrar la modificacion a los datos del establecimiento.
    $fecha_aplicacion=$page->request_post('f_aplicacion');
	$mods = array();
	iff_post($page, $mods, 'den', 'nombre_establecimiento', $establecimiento->nombre_establecimiento);
	iff_post($page, $mods, 'nplazas', 'num_plazas', $establecimiento->num_plazas);
	iff_post($page, $mods, 'nhab', 'num_habitaciones', $establecimiento->num_habitaciones);
	iff_post($page, $mods, 'dir', 'direccion', $establecimiento->direccion);
	iff_post($page, $mods, 'loc', 'localidad', $establecimiento->localidad);
	
	iff_post($page, $mods, 'cp', 'codigo_postal', $establecimiento->codigo_postal);
	iff_post($page, $mods, 'tel1', 'telefono1', $establecimiento->telefono);
	iff_post($page, $mods, 'tel2', 'telefono2', $establecimiento->telefono2);
	iff_post($page, $mods, 'fax1', 'fax', $establecimiento->fax);
	iff_post($page, $mods, 'fax2', 'fax2', $establecimiento->fax2);
	
	iff_post($page, $mods, 'web', 'url', $establecimiento->url);
	iff_post($page, $mods, 'prov', 'provincia', $establecimiento->provincia);
	iff_post($page, $mods, 'rs', 'razon_social', $establecimiento->razon_social);
	iff_post($page, $mods, 'cif', 'cif_nif', $establecimiento->cif_nif);
	iff_post($page, $mods, 'email1', 'email', $establecimiento->email);
	
	iff_post($page, $mods, 'email2', 'email2', $establecimiento->email2);
	iff_post($page, $mods, 'director', 'nombre_director', $establecimiento->director);
	iff_post($page, $mods, 'nsup', 'num_plazas_supletorias', $establecimiento->num_plazas_supletorias);
	iff_post($page, $mods, 'contacto', 'nombre_contacto', $establecimiento->nombre_contacto);
	iff_post($page, $mods, 'expl', 'nombre_explotacion', $establecimiento->nombre_explotacion);
	
	iff_post($page, $mods, 'nreg', 'numero_registro', $establecimiento->num_registro);
	iff_post($page, $mods, 'mun', 'id_municipio', $establecimiento->municipio);
	iff_post($page, $mods, 'isla', 'id_isla', $establecimiento->nombre_isla);
	iff_post($page, $mods, 'cat', 'id_categoria', $establecimiento->id_categoria);
	iff_post($page, $mods, 'tipo', 'id_tipo_establecimiento', $establecimiento->texto_tipo_establecimiento);
	
	$res_op = true;
	if (count($mods) > 0)
	{
	    $fecha_reg = $establecimiento->registrar_modificacion($establecimiento->id_establecimiento, $page->get_current_userid(), $mods, $fecha_aplicacion);
		
		$estUser = $page->load_current_user_data();
		 
		$email = new Email();
		 
		$asunto = "PWET: Modificación de datos "." (".$establecimiento->id_establecimiento.")";
		$texto  = " \n\n\n El establecimiento ".$establecimiento->nombre_establecimiento." (".$establecimiento->id_establecimiento.") ha enviado una solicitud para modificar sus datos.\n\n";
		$texto .= "ID:" .$establecimiento->id_establecimiento."\n";
		foreach ($mods as $clave=>$valor)
		{
			$texto .= strtoupper($clave) . " : $valor\n";
		}
		
		if ($fecha_reg !== false)
		{
		    $texto .= "\nLa modificación se ha registrado en la base de datos con fecha: ".$fecha_reg.".\nFecha de entrada en vigor de los cambios: ".$fecha_aplicacion.".\n";
		}
		else 
		{
			$res_op = false;
		}
		$res_op = $email->send($asunto, $texto);
	}
	$view_vars['res'] = $res_op;
	
}

$page->render("estab_change_view.php", $view_vars);
$page->end_session();

/**
 * Añade una nueva entrada en la tabla mods a partir del parametro post cno nombre postname en caso de que está definido y su valor no coincida con orvalue.
 * @param unknown_type $page
 * @param unknown_type $mods
 * @param unknown_type $postname
 * @param unknown_type $modname
 * @param unknown_type $orvalue
 */
function iff_post($page, & $mods, $postname, $modname, $orvalue)
{
	$den = $page->request_post($postname);
	if ($den != null && $den != $orvalue)
		$mods[$modname] = $den;
}

?>