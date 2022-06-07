<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/RepositoriesDao.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION), array(PAGE_ALOJA_RESULTADOS));

$tabla_num = $page->request_post_or_get("tabla", 1);

if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC)))
{
	/// Administradores pueden elegirlo mediante formularios.
	/// Si el establecimiento ya ha sido establecido en la pagina aloja_index, no es necesario volver a elegirlo.
	if ($page->get_current_establecimiento() == NULL)
	{
		$optit="Consultar resultados";
		$estid = $page->select_establecimiento($page->self_url(NULL, TRUE));
		$page->set_current_establecimiento($estid);
	}
}

$establecimiento = $page->get_current_establecimiento();
if ($establecimiento == null)
{
	$page->abort_with_error(PAGE_ALOJA_RESULTADOS, "No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

if($page->have_any_perm(array(PERM_USER,PERM_RECEPCION)))
{
	/// 1b. Los usuarios no pueden rellenar una encuesta si el establecimiento está dado de baja.
	$page->abort_si_estado_baja($establecimiento);
}


$tabla_tipo1 = array(VIAJEROS_ENTRADOS, VIAJEROS_ALOJADOS, PERNOCTACIONES, 
					 ESTANCIA_MEDIA, INDICE_OCUPACION, INDICE_OCUPACION_POR_HABITACIONES, TARIFA_MEDIA_POR_HABITACION_MENSUAL);

$tabla_tipo2 = array(VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA, VIAJEROS_ALOJADOS_POR_PAIS,PERNOCTACIONES_POR_PAIS, ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA);

$dao = new RepositoriesDao();

$result = $dao->getTablaResultados($page->get_current_userid(), $establecimiento->id_establecimiento, $tabla_num);

$resultados = array();


if (in_array($tabla_num, $tabla_tipo1))
{
	$last_ano = "";
	foreach($result as $row)
	{
		$result_procesado = array();
		
		list($a, $m) = explode(" ", $row['mes_anio']);
		
		$result_procesado['ano'] = (strcasecmp($last_ano, $a) != 0)? $a : "";
		$result_procesado['mes'] = $m;
		$result_procesado['estab'] =$row['estab_dato'];
		$result_procesado['munic'] =$row['municipio_dato'];
		
		if (!isset($row['id_municipio_turistico']) || $row['id_municipio_turistico'] == "000")
			$view_vars['mun_cabecera'] =  "Resto de municipios";
		else {
			if ($row['pie_grupo'] == "")
				$view_vars['mun_cabecera'] =  "Su municipio";
			else
				$view_vars['mun_cabecera'] =  "Su municipio **";
		}
		
		$view_vars['cab_grupo'] = $row['cabecera_grupo'];
		
		$result_procesado['isla'] =$row['isla_dato'];
		$result_procesado['canarias'] =$row['canarias_dato'];
		
		$view_vars['pie_grupo'] = $row['pie_grupo'];
		
		if (isset($row['tipo']))
		{
			$view_vars['tipo'] = ' en establecimientos '.$row['tipo'];
		}
		
		$resultados[] = $result_procesado;
		$last_ano = $a;
	}
} 
else 
{
	foreach($result as $row)
	{
		$result_procesado = array();
	
		$result_procesado['lugar_residencia'] = $row['lugar_residencia'];
		$result_procesado['estab'] =$row['estab_dato'];
		$result_procesado['munic'] =$row['municipio_dato'];

		if (!isset($row['id_municipio_turistico']) || $row['id_municipio_turistico'] == "000")
			$view_vars['mun_cabecera'] =  "Resto de municipios";
		else {
			if ($row['pie_grupo'] == "")
				$view_vars['mun_cabecera'] =  "Su municipio";
			else
				$view_vars['mun_cabecera'] =  "Su municipio **";
		}
	
		$view_vars['cab_grupo'] = $row['cabecera_grupo'];
	
		$result_procesado['isla'] =$row['isla_dato'];
		$result_procesado['canarias'] =$row['canarias_dato'];
	
		$view_vars['pie_grupo'] = $row['pie_grupo'];
	
		if (isset($row['tipo']))
		{
			$view_vars['tipo'] = ' en establecimientos '.$row['tipo'];
		}
	
		if (isset($row['mes']))
		{
			$view_vars['tipo_est'] = $row['tipo'];
			$view_vars['mes'] = $row['mes'];
			$view_vars['ano'] = $row['anyo'];
			
			if ($row['tipo']=='extrahoteleros'){
				 
				$nota_extrahoteleros="<b>nota:</b> Para establecimientos extrahoteleros se publican todas las categorías agrupadas";
				$view_vars['nota_extrahoteleros'] = $nota_extrahoteleros;
			}
		}
		
		$resultados[] = $result_procesado;
	}
}
$view_vars['result'] = $resultados;


$view_vars['tabla_tit'] = constant("TIT_".$tabla_num);
$view_vars['tabla_num'] = $tabla_num;
$view_vars['tabla_tipo'] = in_array($tabla_num, $tabla_tipo1)? 1 : 2;

$view_vars['descarga_url'] = $page->build_url('aloja_resultados_descarga.php', array('tabla' => $tabla_num));

$view_vars['establecimiento'] = $establecimiento;

$page->render("aloja_resultados_view.php", $view_vars);
$page->end_session();

?>