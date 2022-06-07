<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/Noticias.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC), array(PAGE_NOTICIAS));

/// Parametros de la pagina
// ARG_ID: Id. unico de la noticia.
define("ARG_ID", "id");
// ARG_TITULO: Titulo del canal de noticias.
define("ARG_TITULO", "titulo");
// ARG_URL: URl del canal de noticias.
define("ARG_URL", "url");
// ARG_NUM_ENTRADAS: Numero de entradas a mostrar de la noticia.
define("ARG_NUM_ENTRADAS", "num_entradas");
// ARG_ACTIVADO: Flag de activación del canal.
define("ARG_ACTIVADO", "activado");
// ARG_ES_ISTAC: Flag que indica si el canal es del istac o no.
define("ARG_ES_ISTAC", "esistac");

// BTN_OPERATION: Nombre del botón que define la operación.
define("BTN_OPERATION", "operationBtn");
// OP_INSERTAR: Valor de botón que indica que se trata de una inserción de un aviso.
define("OP_INSERTAR" , "Insertar");
// OP_Modificar: Valor de botón que indica que se trata de una modificación de un aviso.
define("OP_MODIFICAR" , "Modificar");
// OP_Eliminar: Valor de botón que indica que se trata de una eliminación de un aviso.
define("OP_ELIMINAR" , "Eliminar");

$noticiasDao = new NoticiasDao();

$error=$page->request_post_or_get("error", NULL);
$operacion=$page->request_post_or_get(ARG_OP, NULL);

if(isset($_POST[BTN_OPERATION]))
{
	$operacion=$_POST[BTN_OPERATION];
	switch($_POST[BTN_OPERATION])
	{
		case OP_INSERTAR:
		case OP_MODIFICAR:
			$canal = new NoticiaFeed();
			$canal->titulo = $page->request_post_or_get(ARG_TITULO, NULL);
			$canal->url = $page->request_post_or_get(ARG_URL, NULL);
			$canal->max = $page->request_post_or_get(ARG_NUM_ENTRADAS, NULL);
			$canal->activado = $page->request_post_or_get(ARG_ACTIVADO, NULL) != null;
			$canal->hasPriority = $page->request_post_or_get(ARG_ES_ISTAC, NULL) != null;
			
			//Se comprueba si es una modificación y se establece la fecha de creación como PK
			if(strcmp($_POST[BTN_OPERATION],OP_MODIFICAR)==0)
			{
				$canal->id = $page->request_post_or_get(ARG_ID, NULL);
				$error = !$noticiasDao->update($canal);
			}
			else 
			{
				$error = !$noticiasDao->insert($canal);
			}
			break;
		case OP_ELIMINAR:
			$canal = new NoticiaFeed();
			$canal_id = $page->request_post_or_get(ARG_ID, NULL);
			$error = !$noticiasDao->delete($canal_id);
			break;
	}

}

$noticias = $noticiasDao->obtenerTodos();

$variables = array(
		'noticias' 		=> 	$noticias,
		'operacion'		=>	$operacion, 
		'error'			=> 	$error,
);

/// Render de la pagina
$page->render( "admin_noticias_view.php", $variables );

$page->end_session();
?>