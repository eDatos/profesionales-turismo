<?php

require_once (__DIR__ . "/config.php");
require_once (__DIR__ . "/classes/PWETPageHelper.class.php");
require_once (__DIR__ . "/classes/EnlaceAyuda.class.php");

$page = PWETPageHelper::start_page ( array (
		PERM_ADMIN,
		PERM_ADMIN_ISTAC 
), array (
		PAGE_ENLACES_AYUDA 
) );

// / Parametros de la pagina
// ARG_ID: Id del enlace
define ( "ARG_ID", "id" );
// ARG_COD_ENLACE: Código del enlace
define ( "ARG_COD_ENLACE", "cod_enlace" );
// ARG_TITULO: Título del enlace
define ( "ARG_TITULO", "titulo" );
// ARG_DESC_CORTA: Descripción corta
define ( "ARG_DESC_CORTA", "desc_corta" );
// ARG_DESC_LARGA: Descripción larga
define ( "ARG_DESC_LARGA", "desc_larga" );
// ARG_TIPO: Tipo
define ( "ARG_TIPO", "tipo" );
// ARG_CONTENIDO_AYUDA: Contenido ayuda
define ( "ARG_CONTENIDO_AYUDA", "contenido_ayuda" );
// ARG_URL_ENLACE_EXTERNO: Enlace externo
define ( "ARG_URL_ENLACE_EXTERNO", "url_enlace_externo" );
// ARG_POSX_POPUP: Posición relativa en X con respecto al enlace de ayuda
define ( "ARG_POSX_POPUP", "posx_popup" );
// ARG_POSY_POPUP: Posición relativa en Y con respecto al enlace de ayuda
define ( "ARG_POSY_POPUP", "posy_popup" );
// ARG_ANCHO_POPUP: Ancho de la ventana flotante
define ( "ARG_ANCHO_POPUP", "ancho_popup" );
// ARG_ALTO_POPUP: Alto de la ventana flotante
define ( "ARG_ALTO_POPUP", "alto_popup" );

// OP_GET: Operación por GET
define ( "OP_GET", "opg" );
// OP_CONSULTAR: Operación de consulta de un enlace existente para poderlo modificar o eliminar
define ( "OP_CONSULTAR", "1" );

// BTN_OPERATION: Nombre del botón que define la operación.
define ( "BTN_OPERATION", "operationBtn" );
// OP_INSERTAR: Valor de botón que indica que se trata de una inserción de un
// enlace
define ( "OP_INSERTAR", "Insertar" );
// OP_Modificar: Valor de botón que indica que se trata de una modificación de
// un enlace
define ( "OP_MODIFICAR", "Modificar" );
// OP_Eliminar: Valor de botón que indica que se trata de una eliminación de un
// enlace
define ( "OP_ELIMINAR", "Eliminar" );

$enlaceDao = new EnlaceAyudaDao ();

$error = FALSE;
$operacion = NULL;
$enlace_mostrar = NULL;
//Da prioridad a la operación por POST, si no ocurre comprueba operación Consultar por GET
if(isset ( $_POST [BTN_OPERATION] ))
{
	$operacion = $_POST [BTN_OPERATION];
	switch ($_POST [BTN_OPERATION]) {
		case OP_INSERTAR :
		case OP_MODIFICAR :
			$enlaceGuardar = new EnlaceAyuda ();
			$enlaceGuardar->cod_enlace = $page->request_post_or_get ( ARG_COD_ENLACE, NULL );
			$enlaceGuardar->titulo = $page->request_post_or_get ( ARG_TITULO, NULL );
			$enlaceGuardar->desc_corta = $page->request_post_or_get ( ARG_DESC_CORTA, NULL );
			$enlaceGuardar->desc_larga = $page->request_post_or_get ( ARG_DESC_LARGA, NULL );
			$enlaceGuardar->tipo = $page->request_post_or_get ( ARG_TIPO, NULL );
			$enlaceGuardar->contenido_ayuda = $page->request_post_or_get ( ARG_CONTENIDO_AYUDA, NULL );
			$enlaceGuardar->url_enlace_externo = $page->request_post_or_get ( ARG_URL_ENLACE_EXTERNO, NULL );
			$enlaceGuardar->posX_popup = $page->request_post_or_get ( ARG_POSX_POPUP, NULL );
			$enlaceGuardar->posY_popup = $page->request_post_or_get ( ARG_POSY_POPUP, NULL );
			$enlaceGuardar->ancho_popup = $page->request_post_or_get ( ARG_ANCHO_POPUP, NULL );
			$enlaceGuardar->alto_popup = $page->request_post_or_get ( ARG_ALTO_POPUP, NULL );

			// Se comprueba si es una modificación y se establece el id como PK
			if (strcmp ( $_POST [BTN_OPERATION], OP_MODIFICAR ) == 0) {
				$enlaceGuardar->id = $page->request_post_or_get ( ARG_ID, NULL );
			}
			if (! $enlaceDao->guardar ( $enlaceGuardar ))
				$error = TRUE;
			break;
		case OP_ELIMINAR :
			$enlaceEliminar = new EnlaceAyuda ();
			$enlaceEliminar->id = $page->request_post_or_get ( ARG_ID, NULL );
			if (! $enlaceDao->eliminar ( $enlaceEliminar ))
				$error = TRUE;
			break;
	}
	$page->client_redirect ( PAGE_ENLACES_AYUDA, array (
			ARG_OP => $operacion,
			"error" => $error
	), true );
	$page->end_session ();
	exit ();
}
else 
{
	if(isset ($_GET [OP_GET]) && $_GET [OP_GET]==OP_CONSULTAR)
	{
		$enlace_mostrar = $enlaceDao->cargar($page->request_post_or_get ( ARG_COD_ENLACE, NULL ), $page->encoding);
	}
}

$enlaces = $enlaceDao->obtenerTodos ();

$variables = array (
		'enlace_mostrar' => $enlace_mostrar,
		'enlaces' => $enlaces,
		'actionEditEnlace' => $page->build_url(PAGE_ENLACES_AYUDA, array(OP_GET => OP_CONSULTAR)),
		'operacion' => $page->request_post_or_get ( ARG_OP, NULL ),
		'error' => $page->request_post_or_get ( "error", NULL ) 
);

// / Render de la pagina
$page->render ( "admin_enlaces_ayuda_view.php", $variables );

$page->end_session ();
?>