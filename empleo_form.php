<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/EmpleoController.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER),array(PAGE_EMPLEO_INDEX,PAGE_EMPLEO_FORM));

/// Parametros de la pagina
// ARG_MES: Mes de encuesta
// ARG_ANO: Año de encuesta
// ARG_EXTS: Indica si el cuestionario debe preguntar por empleadores externos o no.
define('ARG_EXTS','exts');
// ARG_FASE: Indica en qué fase del cuestionario de empleo estamos.
// FASE 0: Mostrar la pantalla de empleadores internos (cuentas de cotización)
// FASE 1: Encuesta de empleo para empleadores internos
// FASE 2: Mostrar la pantalla de empleadores externos (ETTs y otros)
// FASE 3: Encuesta de empleo para empleadores externos
define('ARG_FASE','f');
// BTN_OPERATION: Nombre del botón que define la operación (pantalla de manejo de empleadores).
define("BTN_OPERATION", "operationBtn");
// ARG_RDONLY: Indica que el cuestionario ha de abrirse en modo sólo lectura aunque sea el adminitrador. Opción del menú principal "Ver encuestas presentadas".
define('ARG_RDONLY','rdonly');
// ARG_DATA: datos del cuestionario recogidos del formulario.
define('ARG_DATA', 'var_json');

define("ARG_ID_EMPLEADOR","idEmpleador");
define("ARG_NOMBRE_EMPRESA","nombreEmpresa");
define("ARG_ACTIVA","activa");


// OP_GUARDAR: Se guarda el cuestionario a medias. Se guarda en BDD sin validar y sin cerrar (fecha_cierre=null).
define('OP_GUARDAR', 'save');
// OP_CERRAR: Se guarda el cuestionario completado. Se valida y se guarda en BDD cerrado (fecha_cierre=now).
define('OP_CERRAR', 'send');


/// OPERACIONES AJAX
define('OP_GUARDAR_EMPLEADORES_INTERNOS', 'save_emp_int');
define('OP_GUARDAR_EMPLEADORES_EXTERNOS', 'save_emp_ext');
define('OP_GUARDAR_INTERNOS', 'save_ext');
define('OP_GUARDAR_EXTERNOS', 'save_int');


// BTN_OPERATION: Nombre del botón que define la operación.
//define("BTN_OPERATION", "operationBtn");
// OP_INSERTAR: Valor de botón que indica que se trata de una inserción.
define("OP_INSERTAR" , "Insertar");
// OP_Modificar: Valor de botón que indica que se trata de una modificación.
define("OP_MODIFICAR" , "Guardar");
//// OP_Eliminar: Valor de botón que indica que se trata de una eliminación.
//define("OP_ELIMINAR" , "Eliminar");
//// OP_Eliminar_Todos: Valor de botón que indica que se trata de una eliminación múltiple.
//define("OP_ELIMINAR_TODOS" , "Eliminar todos");


$exts=FALSE;
$original_params = null;

//Se recogen los parámetros para cargar la encuesta
$fase = $page->request_post_or_get(ARG_FASE, NULL);
if ($fase == null)
{
    // TODO: Cambiar la url de retorno PAGE_ALOJA_INDEX por algo más pertinente.
    //$page->abort_with_error(PAGE_ALOJA_INDEX, "Error en los parámetros de la petición.");
    $fase=0;
}
$original_params[ARG_FASE]=$fase;
/*
 * Fases:
 *  0 = manejo de empleadores internos (cuentas de cotización)
 *  1 = cuestionario de empleados de empleadores internos
 *  2 = manejo de empleadores externos (ETTs, etc.)
 *  3 = cuestionario de empleados de empleadores externos
 */
switch($fase)
{
    case '0':
        $exts=FALSE;
        break;
    case '1':
        $exts=FALSE;
        break;
    case '2':
        $exts=TRUE;
        break;
    case '3':
        $exts=TRUE;
        break;
    default:
        // TODO: Cambiar la url de retorno PAGE_ALOJA_INDEX por algo más pertinente.
        $page->abort_with_error(PAGE_ALOJA_INDEX, "Error en los parámetros de la petición.");
        break;
}
$fase=(int)$fase;

$mes_encuesta = $page->request_post_or_get(ARG_MES, NULL);
$ano_encuesta = $page->request_post_or_get(ARG_ANO, NULL);
$rdonly = $page->request_post_or_get(ARG_RDONLY, NULL);
if($rdonly!=NULL)
{
    $original_params[ARG_RDONLY]=$rdonly;
}
$rdonly = ($rdonly!=NULL);


$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

$establecimiento = $page->get_current_establecimiento();

/// 1. Obtener el establecimiento y mes/año con el que se va a trabajar
if($es_admin)
{
	if(($establecimiento == null) || ($mes_encuesta==NULL) || ($ano_encuesta==NULL))
	{
	    /// Sanity check
	    if(false)
	    //if($fase!=0)
	    {
	        // TODO: Cambiar la url de retorno PAGE_ALOJA_INDEX por algo más pertinente.
	        $page->abort_with_error(PAGE_ALOJA_INDEX, "Error en los parámetros de la petición.");
	    }
	    
		/// Mostrar las pagina de busqueda de establecimiento y mes y año de trabajo.
	    list($selected_estid, $mes_encuesta, $ano_encuesta) = $page->select_establecimiento_mes_ano($page->self_url($original_params, TRUE));
		$page->set_current_establecimiento($selected_estid);
		$establecimiento = $page->get_current_establecimiento();
	}
}

if ($establecimiento == null)
{
    // TODO: Cambiar la url de retorno PAGE_ALOJA_INDEX por algo más pertinente.
	$page->abort_with_error(PAGE_ALOJA_INDEX, "No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

//$original_params[ARG_MES]=$mes_encuesta;
//$original_params[ARG_ANO]=$ano_encuesta;

$operacion='';

$empleo_ctl = new EmpleoController();
$empleo_estado = getCuestionario($mes_encuesta, $ano_encuesta, (($rdonly) && ($es_admin)));
$empleo_enc = $empleo_estado->encuesta;

$mes_encuesta=$empleo_enc->mes;
$ano_encuesta=$empleo_enc->ano;
$original_params[ARG_MES]=$mes_encuesta;
$original_params[ARG_ANO]=$ano_encuesta;


/// EXC: Admin siempre entre en modo RW excepto cuando se entra por la opción de "Ver encuestas presentadas".
$solo_lectura = (($rdonly) && ($es_admin)) || ((!$es_admin) && ($empleo_estado->es_rdonly));

$error=FALSE;
$errs=array();
if(($fase==0)||($fase==2))
{
    // Manejo de empleadores: internos (f==0) ó externos (f==2)
    
    $dao=$empleo_ctl->dao;
    //$dao=new EmpleadoresDao();
    if(isset($_POST[BTN_OPERATION]))
    {
        $operacion=$_POST[BTN_OPERATION];
        switch($operacion)
        {
            case OP_INSERTAR:
                $idEmpleador=($exts ? new NIF($page->request_post_or_get(ARG_ID_EMPLEADOR, NULL)) : new NSS($page->request_post_or_get(ARG_ID_EMPLEADOR, NULL)));
                $empleador = new Empleador($idEmpleador);
                $empleador->externo=($exts ? EMPLEADOR_EXTERNO:EMPLEADOR_INTERNO);
                $empleador->id_establecimiento=$establecimiento->id_establecimiento;
                $empleador->descripcion=$page->request_post_or_get(ARG_NOMBRE_EMPRESA, NULL);
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
                    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, INSERTAR_NUEVO_EMPLEADOR, SUCCESSFUL,array("Nuevo empleador (".$empleador->formatear(FORMATO_IPF_GUIONES).") registrado."));
                }
                break;
            case OP_MODIFICAR:
                $idEmpleador=($exts ? new NIF($page->request_post_or_get(ARG_ID_EMPLEADOR, NULL)) : new NSS($page->request_post_or_get(ARG_ID_EMPLEADOR, NULL)));
                $empleador = new Empleador($idEmpleador);
                $empleador->externo=($exts ? EMPLEADOR_EXTERNO:EMPLEADOR_INTERNO);
                $empleador->id_establecimiento=$establecimiento->id_establecimiento;
                $empleador->descripcion=$page->request_post_or_get(ARG_NOMBRE_EMPRESA, NULL);
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
                    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, MODIFICAR_EMPLEADOR, SUCCESSFUL,array("Modificado empleador (".$empleador->formatear(FORMATO_IPF_GUIONES).") correctamente."));
                }
                break;
        }
    }
    
    $resultset = $dao->listarEmpleadores($establecimiento->id_establecimiento, null, $exts ? EMPLEADOR_EXTERNO:EMPLEADOR_INTERNO);
    
    /// Preparacion de la vista de la pagina
    $navToUrl=$page->self_url($original_params, TRUE);
    $navToUrlAlt=$page->self_url($original_params, TRUE);
    
    $variables = array(
        'fase' => $fase,
        'mes_encuesta' => $mes_encuesta,
        'ano_encuesta' => $ano_encuesta,
        'empleo_enc' => $empleo_enc,
        'data' => $resultset,
        'exts' => $exts,
        'solo_lectura' => $solo_lectura,
        //Si es hotel o apartamento
        'es_hotel' => $establecimiento->es_hotel(),
        'urlBack' => ($es_admin)?PAGE_HOME:PAGE_EMPLEO_INDEX,     //$page->build_url(PAGE_HOME),
        'navToUrl' => $navToUrl,
        'navToUrlAlt' => $navToUrlAlt,
        'query_id_est' => $establecimiento->id_establecimiento,
        'nombre_establecimiento' => $establecimiento->nombre_establecimiento,
        'error'			=> 	$error,
        'listaErrores'	=> $errs,
        'operacion'		=>	$operacion,
        'navSgteURL'    => $page->build_url($_SERVER['REQUEST_URI'],array(ARG_FASE => ($fase + 1))),
        'navpage_url'	=> $page->build_url('empleo_form_ajax.php', array(ARG_MES=>$mes_encuesta, ARG_ANO=>$ano_encuesta)),
        'navPrevURL'    => $page->build_url($_SERVER['REQUEST_URI'],array(ARG_FASE => ($fase - 1)))
    );
    
    /// Render de la pagina
    //$page->render( "empleadores_view.php", $variables );
    $page->render( "empleo_form_view.php", $variables);
    
    exit();
}

//Se guarda el cuestionario en bbdd de datos según los presentes a fin de mes anterior
$empleo_ctl->inicializar_cuestionario_ult_detalles($empleo_enc);

// Obtenemos la lista completa de empleadores activos (internos o externos)
$lista_empleadores=EmpleoFilaFormulario::getFilasFromListaEmpleadores($empleo_ctl->listarEmpleadores($establecimiento->id_establecimiento,null,$exts ? EMPLEADOR_EXTERNO : EMPLEADOR_INTERNO));

// Completamos la información de número de empleados con lo que tiene el cuestionario (por si existieran datos previos).
if($empleo_enc->numero_empleados!=null)
{
    foreach($empleo_enc->numero_empleados as $id_empleador => $empleados)
    {
        if(isset($lista_empleadores[$id_empleador]))
        {
            $lista_empleadores[$id_empleador]=$empleados;
        }
    }
}
if($empleo_enc->numero_empleados_anterior!=null)
{
    foreach($empleo_enc->numero_empleados_anterior as $id_empleador => $empleados)
    {
        if(isset($lista_empleadores[$id_empleador]))
        {
            $lista_empleadores[$id_empleador]->num_empleados_anterior=$empleados->num_empleados;
        }
    }
}

$variables = array(
    'fase'                  => $fase,
    'data'                  => $lista_empleadores,
    'empleo_enc'            => $empleo_enc,
    'nombre_establecimiento'=>	$establecimiento->nombre_establecimiento,
    'tipo_establecimiento'	=> 	$empleo_estado->datos_estab->id_tipo_establecimiento,
    'urlPrintForm' 			=> $page->build_url(PAGE_EMPLEO_PRINT, array(ARG_MES=>$empleo_enc->mes, ARG_ANO=>$empleo_enc->ano)),
    'empleo_plazo'		    => ($solo_lectura || $es_admin)? null : $empleo_estado->plazo,
    'exts'                  => $exts,
    'solo_lectura'			=> $solo_lectura,
    //Si es hotel o apartamento
    'es_hotel'              => $establecimiento->es_hotel(),
    'urlBack'				=> ($es_admin)?PAGE_HOME:PAGE_EMPLEO_INDEX,     //$page->build_url(PAGE_HOME),
    'es_admin'				=> $es_admin,
    'navpage_url'			=> $page->build_url('empleo_form_ajax.php', array(ARG_MES=>$empleo_enc->mes, ARG_ANO=>$empleo_enc->ano)),
    'navSgteURL'            => $page->build_url($_SERVER['REQUEST_URI'],array(ARG_FASE => ($fase + 1))),
    'navPrevURL'            => $page->build_url($_SERVER['REQUEST_URI'],array(ARG_FASE => ($fase - 1)))
);

/// Render de la pagina
$page->render( "empleo_form_view.php", $variables);

$page->end_session();

function getCuestionario($mes,$anio,$lectura)
{
    global $es_admin;
    global $empleo_ctl;
    global $establecimiento;
    global $page;
    
    /// Cargar estado de la encuesta de empleo actualmente en curso para comprobar si es del mes y año pedidos.
    /// Comprobar si la encuesta debe mostrarse en modo de solo lectura.
    /// Cargar los datos del cuestionario para el mes y año
    /// Carga los datos del establecimiento valido en el mes y ano de la encuesta.
    /// el criterio seguido es que los datos a dia 1 del mes de la encuesta son los correctos.
    $estado = $empleo_ctl->cargar_encuesta($establecimiento, $page->get_current_userid(), $mes, $anio, $lectura);
    
    $enc = $estado->encuesta;
    
    if ($enc == NULL)
    {
        if ($es_admin)
        {
            $enc = $empleo_ctl->crear_nuevo_cuestionario($mes, $anio, $establecimiento->id_establecimiento, $page->get_current_userid(), TIPO_CARGA_WEB, false);
            
            $estado->encuesta = $enc;
            
            /// Carga los datos del establecimiento valido en el mes y ano de la encuesta.
            /// el criterio seguido es que los datos a dia 1 del mes de la encuesta son los correctos.
            $fecha_efectiva_datos = DateHelper::parseDate(sprintf("%02d-%02d-%04d", 1, $enc->mes, $enc->ano));
            if ($fecha_efectiva_datos < $establecimiento->fecha_alta)
            {
                // Carga los datos del establecimiento valido en el mes y ano de la encuesta.
                $est_id = $establecimiento->id_establecimiento;
                $est2 = new Establecimiento();
                $est2->cargar_por_fecha($est_id, $fecha_efectiva_datos);
                $estado->datos_estab = $est2;
            }
            else
            {
                /// Los datos actuales son valido para el mes/año de la encuesta.
                $estado->datos_estab = $establecimiento;
            }
        }
        else
        {
            /// No existe cuestionario para esa fecha, abortar la operacion.
            $page->abort_with_error(PAGE_HOME, "No existe cuestionario para el mes y año elegidos.");
        }
    }
    
    return $estado;
}

?>