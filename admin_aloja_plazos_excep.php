<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaPlazosExcepcionalDao.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR));
$page->set_page_path(array(PAGE_ALOJA_PLAZOS_EXCEP));

define("ARG_DIA","dia_mes");
define("ARG_NOTAS","notas");

// BTN_OPERATION: Nombre del botón que define la operación.
define("BTN_OPERATION", "operationBtn");
// OP_INSERTAR: Valor de botón que indica que se trata de una inserción.
define("OP_INSERTAR" , "Insertar nuevo plazo");
// OP_Modificar: Valor de botón que indica que se trata de una modificación.
define("OP_MODIFICAR" , "Modificar");
// OP_Eliminar: Valor de botón que indica que se trata de una eliminación.
define("OP_ELIMINAR" , "Eliminar");
// OP_Eliminar_Todos: Valor de botón que indica que se trata de una eliminación múltiple.
define("OP_ELIMINAR_TODOS" , "Eliminar todos");

$error=$page->request_post_or_get("error", NULL);
$operacion=$page->request_post_or_get(ARG_OP, NULL);

$dao = new AlojaPlazosExcepcionalDao();

$errs=array();

if(isset($_POST[BTN_OPERATION]))
{
	$operacion=$_POST[BTN_OPERATION];
	$notas=$page->request_post_or_get(ARG_NOTAS, NULL);
	if($notas != NULL)
	    $notas=iconv("CP1252", "UTF-8", $notas);
	switch($_POST[BTN_OPERATION])
	{
		case OP_INSERTAR:
		    $errs=$dao->insertarMultiple($page->request_post_or_get(ARG_ESTID, NULL),$page->request_post_or_get(ARG_ANO, NULL),$page->request_post_or_get(ARG_MES, NULL),$page->request_post_or_get(ARG_DIA, NULL),$notas);
			if(!empty($errs))
				$error = TRUE;
			break;
		case OP_MODIFICAR:
			$plazoGuardar = new AlojaPlazoExcepcional();
			$plazoGuardar->id_estab = $page->request_post_or_get(ARG_ESTID, NULL);
			$plazoGuardar->mes = $page->request_post_or_get(ARG_MES, NULL);
			$plazoGuardar->ano = $page->request_post_or_get(ARG_ANO, NULL);
			$plazoGuardar->dia_mes_sig = $page->request_post_or_get(ARG_DIA, NULL);
			$plazoGuardar->notas = $notas;
				
			if (!$dao->guardar($plazoGuardar))
				$error = TRUE;
			break;	
		case OP_ELIMINAR:
			$errs=$dao->eliminarMultiple($page->request_post_or_get(ARG_ESTID, NULL),$page->request_post_or_get(ARG_ANO, NULL),$page->request_post_or_get(ARG_MES, NULL));
			if(!empty($errs))
				$error = TRUE;
			break;					
		case OP_ELIMINAR_TODOS:
			$errs=$dao->eliminarMultiple($page->request_post_or_get(ARG_ESTIDS, NULL),$page->request_post_or_get(ARG_ANO, NULL),$page->request_post_or_get(ARG_MES, NULL));
			if(!empty($errs))
				$error = TRUE;
			break;					
	}

}

$resultset = $dao->cargar_plazos();

/// Preparacion de la vista de la pagina
$variables = array(
	'data' => $resultset,
	'navToUrl' => $page->self_url(),
	'error'			=> 	$error,
	'listaErrores'	=> $errs,
	'operacion'		=>	$operacion
);

/// Render de la pagina
$page->render( "admin_aloja_plazos_excep_view.php", $variables );


$page->end_session();
?>