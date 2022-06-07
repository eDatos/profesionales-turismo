<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/classes/Establecimiento.class.php");
require_once(__DIR__."/lib/DateHelper.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC), array(PAGE_AVISOS));

/// Parametros de la pagina
// ARG_FECHA_INI: Fecha de inicio del aviso.
define("ARG_FECHA_INI", "fechaInicio");
// ARG_FECHA_FIN: Fecha de finalización del aviso.
define("ARG_FECHA_FIN", "fechaFin");
// ARG_TITULO: Título del aviso.
define("ARG_TITULO", "titulo");
// ARG_TEXTO: Texto del aviso.
define("ARG_TEXTO", "texto");
// ARG_ID_GRUPO: Id. de grupo del aviso.
define("ARG_ID_GRUPO", "grupo");
// ARG_FECHA_CREACION: Fecha de creación del aviso. Considerada PK de la tabla.
define("ARG_FECHA_CREACION", "fechaCreacion");
// BTN_OPERATION: Nombre del botón que define la operación.
define("BTN_OPERATION", "operationBtn");
// OP_INSERTAR: Valor de botón que indica que se trata de una inserción de un aviso.
define("OP_INSERTAR" , "Insertar");
// OP_Modificar: Valor de botón que indica que se trata de una modificación de un aviso.
define("OP_MODIFICAR" , "Modificar");
// OP_Eliminar: Valor de botón que indica que se trata de una eliminación de un aviso.
define("OP_ELIMINAR" , "Eliminar");

$avisoDao = new AvisoDao();
$estab = new Establecimiento();

$error=$page->request_post_or_get("error", NULL);
$operacion=$page->request_post_or_get(ARG_OP, NULL);

if(isset($_POST[BTN_OPERATION]))
{
	$operacion=$_POST[BTN_OPERATION];
	switch($_POST[BTN_OPERATION])
	{
		case OP_INSERTAR:
		case OP_MODIFICAR:
			$avisoGuardar = new Aviso();
			$avisoGuardar->fecha_ini = $page->request_post_or_get(ARG_FECHA_INI, NULL);
			$avisoGuardar->fecha_fin = $page->request_post_or_get(ARG_FECHA_FIN, NULL);
			$avisoGuardar->titulo = $page->request_post_or_get(ARG_TITULO, NULL);
			$avisoGuardar->texto = $page->request_post_or_get(ARG_TEXTO, NULL);
			$avisoGuardar->id_grupo = $page->request_post_or_get(ARG_ID_GRUPO, NULL);
			//Se comprueba si es una modificación y se establece la fecha de creación como PK
			if(strcmp($_POST[BTN_OPERATION],OP_MODIFICAR)==0)
			{
				$avisoGuardar->fecha_creacion = $page->request_post_or_get(ARG_FECHA_CREACION, NULL);
			}
			if(!$avisoDao->guardar($avisoGuardar))
				$error = TRUE;
			break;
		case OP_ELIMINAR:
			$avisoEliminar = new Aviso();
			$avisoEliminar->fecha_creacion = $page->request_post_or_get(ARG_FECHA_CREACION, NULL);
			if(!$avisoDao->eliminar($avisoEliminar))
				$error = TRUE;
			break;
	}

}

$avisos = $avisoDao->obtenerTodos();
$grupos = $estab->load_all_grupos();

$variables = array(
		'avisos' 		=> 	$avisos,
		'grupos'		=>  $grupos,
		'operacion'		=>	$operacion, 
		'error'			=> 	$error,
);

/// Render de la pagina
$page->render( "admin_avisos_view.php", $variables );

$page->end_session();
?>