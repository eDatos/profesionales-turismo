<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/EmpleadoresDao.class.php");

//$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR));
$page = PWETPageHelper::start_page(PERMS_ANY);
$page->set_page_path(array(PAGE_ADMIN_EMPLEADORES));

define("ARG_DIA","dia_mes");
define("ARG_NOTAS","notas");



define("ARG_CUENTA_COTIZACION","cuentaCotizacion");
define("ARG_ID_EMPLEADOR","nif");
define("ARG_NOMBRE_EMPRESA","nombreEmpresa");
define("ARG_DESCRIPCION","descripcion");
define("ARG_ES_EXT","esETT");
define("ARG_ACTIVA","activa");



// BTN_OPERATION: Nombre del botón que define la operación.
define("BTN_OPERATION", "operationBtn");
// OP_CONTAR: Valor de botón que indica que se quiere contar el número de cuestionarios donde aparece este empleador.
define("OP_CONTAR" , "#?");
// OP_INSERTAR: Valor de botón que indica que se trata de una inserción.
define("OP_INSERTAR" , "Insertar nuevo empleador");
// OP_Modificar: Valor de botón que indica que se trata de una modificación.
define("OP_MODIFICAR" , "Guardar");
// OP_Eliminar: Valor de botón que indica que se trata de una eliminación.
define("OP_ELIMINAR" , "Eliminar");
// OP_Eliminar_Todos: Valor de botón que indica que se trata de una eliminación múltiple.
define("OP_ELIMINAR_TODOS" , "Eliminar todos");


/*
$query_id_est = $page->request_post_or_get(ARG_ESTID, NULL);

$optit="Gestionar empleadores";

/// OPERACION 1: No se ha indicado ningun establecimiento, mostrar la pagina de busqueda.
if ($query_id_est == '')
{
    /// - Si no se ha llamado con el argumento est_id (el id. de establecimiento), convertir esta pagina en una pagina de busqueda.
    $navToUrl = $page->self_url(NULL, TRUE);
    require_once(PAGE_ESTAB_SEARCH);
    $page->end_session();
    exit();
}
*/

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

if($es_admin)
{
    /// OPERACION 1: No se ha indicado ningun establecimiento, mostrar la pagina de busqueda.
    //$optit="Gestionar empleadores";
    $selected_estid = $page->select_establecimiento($page->self_url(NULL, TRUE));
    $page->set_current_establecimiento($selected_estid);
    //list($mes_trabajo, $ano_trabajo) = $page->select_mes_ano($page->self_url(NULL, TRUE));
}

/// Datos del establecimiento del usuario.
$establecimiento = $page->get_current_establecimiento();

$error=$page->request_post_or_get("error", NULL);
$operacion=$page->request_post_or_get(ARG_OP, NULL);

$dao = new EmpleadoresDao();

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
		    $exts=($page->request_post_or_get(ARG_ES_EXT, EMPLEADOR_INTERNO)==EMPLEADOR_EXTERNO);
		    if($exts)
		    {
		        $idEmpleador=new NIF($page->request_post_or_get(ARG_ID_EMPLEADOR, NULL));
		        $empleador = new Empleador($idEmpleador);
		        $empleador->externo=EMPLEADOR_EXTERNO;
		        $empleador->descripcion=$page->request_post_or_get(ARG_NOMBRE_EMPRESA, NULL);
		    }
		    else
		    {
		        $idEmpleador=new NSS($page->request_post_or_get(ARG_CUENTA_COTIZACION, NULL));
		        $empleador = new Empleador($idEmpleador);
		        $empleador->externo=EMPLEADOR_INTERNO;
		        $empleador->descripcion=$page->request_post_or_get(ARG_DESCRIPCION, NULL);
		    }
		    $empleador->id_establecimiento=$establecimiento->id_establecimiento;
		    $empleador->estado='A';
			
		    $errs=$empleador->validar();
			if(!empty($errs))
			{
			    $error = TRUE;
			    break;
			}
			
			$res=$dao->crear($empleador);
			if(!empty($res))
			{
			    $errs=array_merge($errs,$res);
			    $error = TRUE;
			    break;
			}
			else
			{
			    @AuditLog::log($page->get_current_userid(), $selected_estid, INSERTAR_NUEVO_EMPLEADOR, SUCCESSFUL,array("Nuevo empleador (".$empleador->formatear(FORMATO_IPF_GUIONES).") registrado."));
			}
			break;
		case OP_MODIFICAR:
		    $exts=($page->request_post_or_get(ARG_ES_EXT, EMPLEADOR_INTERNO)==EMPLEADOR_EXTERNO);
		    if($exts)
		    {
		        $idEmpleador=new NIF($page->request_post_or_get(ARG_ID_EMPLEADOR, NULL));
		        $empleador = new Empleador($idEmpleador);
		        $empleador->externo=EMPLEADOR_EXTERNO;
		        $empleador->descripcion=$page->request_post_or_get(ARG_NOMBRE_EMPRESA, NULL);
		    }
		    else
		    {
		        $idEmpleador=new NSS($page->request_post_or_get(ARG_CUENTA_COTIZACION, NULL));
		        $empleador = new Empleador($idEmpleador);
		        $empleador->externo=EMPLEADOR_INTERNO;
		        $empleador->descripcion=$page->request_post_or_get(ARG_DESCRIPCION, NULL);
		    }
		    $empleador->id_establecimiento=$establecimiento->id_establecimiento;
		    $estaActivo=$page->request_post_or_get(ARG_ACTIVA, false);
		    $estaActivo=($estaActivo ? EMPLEADOR_ACTIVO:EMPLEADOR_INACTIVO);
		    $empleador->estado=$estaActivo;
		    
		    $errs=$empleador->validar();
		    if(!empty($errs))
		    {
		        $error = TRUE;
		        break;
		    }
		    $res=$dao->actualizar($empleador);
		    if(!empty($res))
		    {
		        $errs=array_merge($errs,$res);
		        $error = TRUE;
		        break;
		    }
		    else
		    {
		        @AuditLog::log($page->get_current_userid(), $selected_estid, MODIFICAR_EMPLEADOR, SUCCESSFUL,array("Modificado empleador (".$empleador->formatear(FORMATO_IPF_GUIONES).") correctamente."));
		    }
		    break;
		case OP_ELIMINAR:
		    $exts=($page->request_post_or_get(ARG_ES_EXT, EMPLEADOR_INTERNO)==EMPLEADOR_EXTERNO);
		    if($exts)
		    {
		        $idEmpleador=new NIF($page->request_post_or_get(ARG_ID_EMPLEADOR, NULL));
		        $empleador = new Empleador($idEmpleador);
		    }
		    else
		    {
		        $idEmpleador=new NSS($page->request_post_or_get(ARG_CUENTA_COTIZACION, NULL));
		        $empleador = new Empleador($idEmpleador);
		    }
		    $empleador->id_establecimiento=$establecimiento->id_establecimiento;
		    
		    $res=$dao->borrar($empleador);
		    if(!empty($res))
		    {
		        $errs=array_merge($errs,$res);
		        $error = TRUE;
		        break;
		    }
		    else
		    {
		        @AuditLog::log($page->get_current_userid(), $selected_estid, BORRAR_EMPLEADOR, SUCCESSFUL,array("Empleador borrado (".$empleador->formatear(FORMATO_IPF_GUIONES).") correctamente."));
		    }
		    break;
		case OP_CONTAR:
		    $exts=($page->request_post_or_get(ARG_ES_EXT, EMPLEADOR_INTERNO)==EMPLEADOR_EXTERNO);
		    if($exts)
		    {
		        $idEmpleador=new NIF($page->request_post_or_get(ARG_ID_EMPLEADOR, NULL));
		        $empleador = new Empleador($idEmpleador);
		    }
		    else
		    {
		        $idEmpleador=new NSS($page->request_post_or_get(ARG_CUENTA_COTIZACION, NULL));
		        $empleador = new Empleador($idEmpleador);
		    }
		    $empleador->id_establecimiento=$establecimiento->id_establecimiento;
		    
		    $res=$dao->getNumeroEncuestas($empleador->id_establecimiento,$empleador->id_empleador->getId());
		    if(is_int($res))
		    {
		        $errs=array();
		        $numero=intval($res);
		        $mensaje="El empleador identificado por ".$empleador->id_empleador->toString();
		        $mensaje.=($numero==0) ? " no se ha encontrado " : " se ha encontrado ".$numero." veces ";
		        $mensaje.=" entre los cuestionarios almacenados para este establecimiento";
		        array_push($errs,$mensaje);
		        $error = FALSE;
		    }
		    else
		    {
		        $errs=array_merge($errs,$res);
		        $error = TRUE;
		    }
		    break;
	}

}

$resultset = $dao->listarEmpleadores($establecimiento->id_establecimiento);

/// Preparacion de la vista de la pagina
$variables = array(
    'es_admin' => $es_admin,
	'data' => $resultset,
    'navToUrl' => $page->build_url(PAGE_ADMIN_EMPLEADORES, array(ARG_ESTID => $establecimiento->id_establecimiento)),
    'query_id_est' => $establecimiento->id_establecimiento,
    'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
	'error'			=> 	$error,
	'listaErrores'	=> $errs,
	'operacion'		=>	$operacion
);

/// Render de la pagina
$page->render( "admin_empleadores_view.php", $variables );


$page->end_session();
?>