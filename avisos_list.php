<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/lib/DateHelper.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_AVISOS_LIST));

/// Parametros de la pagina
// ARG_FECHA_CREACION: Fecha de creación del aviso. Considerada PK de la tabla.
define("ARG_FECHA_CREACION", "fecha_creacion");
// ARG_ID_GRUPO: Id. de grupo de establecimiento para el que se muestran los avisos
define("ARG_ID_GRUPO", "id_grupo");

$fecha_creacion = $page->request_post_or_get(ARG_FECHA_CREACION, NULL);
$id_grupo = $page->request_post_or_get(ARG_ID_GRUPO, NULL);
$avisoDao = new AvisoDao();

//Si se ha establecido fecha de creación se mostrará sólo el aviso cuya fecha de creación se ha pasado
if($fecha_creacion != '')
{	
	$aviso_unico_mostrar = $avisoDao->cargar($fecha_creacion);
}
elseif ($id_grupo != '') //En caso contrario, se mostrarán los avisos para el grupo de establecimiento solicitado
{
	$avisos_mostrar = $avisoDao->filtrarByIdGrupo($id_grupo);
}

$variables = array(
		'aviso_unico_mostrar'	=>  isset($aviso_unico_mostrar) ? $aviso_unico_mostrar : NULL,
		'avisos_mostrar' 		=> 	isset($avisos_mostrar) ? $avisos_mostrar : NULL,
		'index_url'				=>	$page->build_url(PAGE_HOME, NULL)
);

/// Render de la pagina
$page->render( "avisos_list_view.php", $variables );

$page->end_session();
?>