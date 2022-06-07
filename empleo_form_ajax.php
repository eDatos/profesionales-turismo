<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");
require_once(__DIR__."/classes/EmpleoErrorCollection.class.php");
require_once(__DIR__."/classes/EmpleoController.class.php");
require_once(__DIR__."/lib/InputCleaner.class.php");

$page = PWETPageHelper::start_page_ajax(PERMS_ANY);

define('ARG_PAGE', 'pagina');
define('ARG_DATA', 'var_json');
define('ARG_EXTERNOS', 'externos');

define('OP_GETPAGE', 'gp');
define('OP_GUARDAR', 'save');
define('OP_CERRAR', 'send');
define('OP_SAVETEMP', 'backup');

define('OP_GUARDAR_EMPLEADORES_INTERNOS', 'save_emp_int');
define('OP_GUARDAR_EMPLEADORES_EXTERNOS', 'save_emp_ext');
define('OP_GUARDAR_INTERNOS', 'save_ext');
define('OP_GUARDAR_EXTERNOS', 'save_int');

// define("TIPO_ERROR_ESTRUCTURAL", 3);
// define("TIPO_ERROR_GRAVE", 2);
// define("TIPO_ERROR_DATOS", 1);


function err_detail($id_est,$mes,$anio)
{
    $salida="est=";
    if($id_est!=null)
        $salida.=$id_est;
    $salida.="mes=";
    if($mes!=null)
        $salida.=$mes;
    $salida.="anio=";
    if($anio!=null)
        $salida.=$anio;
    return $salida;
}

$est = $page->get_current_establecimiento();
$mes_encuesta = $page->request_post_or_get(ARG_MES, NULL);
$ano_encuesta = $page->request_post_or_get(ARG_ANO, NULL);


/// Comprobacion de parametros, respuesta vacía en caso de que alguno sea erroneo.
if ($est == NULL || $mes_encuesta == null || $ano_encuesta == null)
{
	$page->end_session();
	exit;
}

header("Content-Type: text/html; charset=UTF-8");

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

$accion=$page->request_post(ARG_OP);

if($accion == OP_GUARDAR || $accion == OP_CERRAR)
{
	try
	{
		// 1. Coger el objeto json y rellenar un cuestionario de empleo.
		$data = $page->request_post(ARG_DATA);
		
		$empleo_ctl = new EmpleoController();
		$empleo_estado = $empleo_ctl->cargar_encuesta($est, $page->get_current_userid(), $mes_encuesta, $ano_encuesta);
		$empleo_enc = $empleo_estado->encuesta;
		
		if($empleo_enc==null)
		{
			/// Crear uno nuevo si no existe, sin guardar en la BBDD.
			$empleo_enc = $empleo_ctl->crear_nuevo_cuestionario($mes_encuesta, $ano_encuesta, $est->id_establecimiento, $page->get_current_userid(), TIPO_CARGA_WEB, false);
		}
		
		// FIXME: Verificar que la condición es correcta. Ver qué pasa con los cuestionarios del mes anterior.
		// Por seguridad, comprobamos si el usuario está autorizado a modificar el cuestionario.
		if((!$es_admin) && ($empleo_enc->esta_cerrado()))
		{
			$errs = new EmpleoErrorCollection();
			$errs->log_error(ERROR_GENERAL, "Operación no autorizada: el cuestionario ya fue cerrado con anterioridad.");
			echo errores_to_json($errs, false);
			$page->end_session();
			exit;
		}

		$externos=($page->request_post(ARG_EXTERNOS)=='true') ? EMPLEADOR_EXTERNO : EMPLEADOR_INTERNO;
		
		// Aquí debemos borrar los datos previos existentes para los empleadores internos ó externos según corresponda.
		if(count($empleo_enc->numero_empleados)>0)
		{
		    foreach($empleo_enc->numero_empleados as $id_empleador => $empleados)
		    {
		        if($empleados->externo==$externos)
		        {
		            unset($empleo_enc->numero_empleados[$id_empleador]);
		        }
		    }
		}
		actualizar_cuestionario($empleo_enc, $data, $externos);
		
		$op_es_cerrar = ($accion == OP_CERRAR);
		
		// De momento, no validamos.
		//$errores = $empleo_ctl->valida_y_guardar($empleo_enc, $op_es_guardar, $page->get_current_userid(), false);
		
		$empleo_enc->fecha_cierre=null;
		if($op_es_cerrar)
		{
			//$empleo_enc->fecha_cierre=new DateTime();              // El cierre del cuestionario se produce en empleo_form_envio.php
			
			// Validamos completamente el cuestinario completo.
			$errores = $empleo_enc->validar();
			if($errores->num_errores()>0)
			{
			    echo errores_to_json($errores, false);
			    $page->end_session();
			    exit;
			}
		}
		
		
		
		if($empleo_ctl->guardar_cuestionario($empleo_enc))
		{
			// No hay errores (false) y se guardó (true)
			echo json_encode(array(false, true));
		    //echo json_encode(array(array(utf8_encode("Jajaja.")), false));
		}
		else
		{
			// No hay errores (false) y se guardó (true)
			echo json_encode(array(array(utf8_encode("Fallo al grabar el cuestionario.")), false));			
		}

		$page->end_session();
	}
	catch(Exception $excepcion)
	{
	    $errs = new EmpleoErrorCollection();
		$errs->log_error(ERROR_GENERAL, "Error no especificado. No se ha podido guardar el cuestionario.");
		echo errores_to_json($errs, false);
		$page->end_session();
		throw $excepcion;
	}
}
elseif($accion == OP_GUARDAR_EMPLEADORES_INTERNOS || $accion == OP_GUARDAR_EMPLEADORES_EXTERNOS)
{
    try {
        $dao=new EmpleadoresDao();
        $errs=array();
        
        // 1. Coger el objeto json con los datos de los empleadores.
        $data = $page->request_post(ARG_DATA);
        
        $datos = $data['datos'];
        for($i=0;$i<count($datos);$i++)
        {
            $idEmpleador=(($accion == OP_GUARDAR_EMPLEADORES_EXTERNOS) ? new NIF($datos[$i]['id_empleador']) : new NSS($datos[$i]['id_empleador']));
            $empleador = new Empleador($idEmpleador);
            $empleador->externo=(($accion == OP_GUARDAR_EMPLEADORES_EXTERNOS) ? EMPLEADOR_EXTERNO:EMPLEADOR_INTERNO);
            $empleador->id_establecimiento=$est->id_establecimiento;
            ////$empleador->descripcion=iconv("CP1252", "UTF-8", $datos[$i]['descripcion']);
            $empleador->descripcion=iconv("UTF-8", "CP1252", urldecode($datos[$i]['descripcion']));
            $empleador->estado=($datos[$i]['estado']=='A' ? 'A':'I');
            $res=$empleador->validar();
            if(empty($res))
            {
                $res=$dao->actualizar($empleador);
                if(!empty($res))
                {
                    $errs=array_merge($errs,$res);
                }
                else
                {
                    @AuditLog::log($page->get_current_userid(), $est->id_establecimiento, MODIFICAR_EMPLEADOR, SUCCESSFUL,array("Modificado empleador (".$empleador->formatear(FORMATO_IPF_GUIONES).") correctamente."));
                }
            }
            else
            {
                $errs=array_merge($errs,$res);
            }
        }
        if(empty($errs))
        {
            // No hay errores (false) y se guardó (true)
            echo json_encode(array(false, true));
        }
        else
        {
            // Hay errores (true) y no se guardó (false)
            $listaErrores=array();
            array_push($listaErrores,utf8_encode("Fallo al guardar la información."));
            for($i=0;$i<count($errs);$i++)
            {
                array_push($listaErrores,utf8_encode($errs[$i]));
            }
            echo json_encode(array($listaErrores,false));
            //echo json_encode(array(array_merge(array(utf8_encode("Fallo al guardar la información.")),$errs), false));
        }
        
        
    }
    catch(Exception $excepcion)
    {
        $errs = new EmpleoErrorCollection();
        $errs->log_error(ERROR_GENERAL, "Error no especificado. No se ha podido guardar la información.");
        echo errores_to_json($errs, false);
        $page->end_session();
        throw $excepcion;
    }
}


function errores_to_json(EmpleoErrorCollection $errores, $cuest_guardado)
{
	foreach($errores->errores as $error)
	{
		$error->mensaje = utf8_encode($error->mensaje);
	}
	
	return json_encode(array($errores, $cuest_guardado));
}

/**
 * Actualiza el cuestionario con los datos recibidos en data.
 * @param unknown_type $empleo_enc
 * @param unknown_type $data
 */
function actualizar_cuestionario($empleo_enc, $data, $externos)
{
	if (isset($data['datos']))
	{
		//$datos = json_decode($data['datos']);
		$datos = $data['datos'];
		
		for($i=0;$i<count($datos);$i++)
		{
		    // Rellenamos sólo los campos estrictamente necesarios para guardar la fila del cuestionario.
		    $empleo_fila_formulario=new EmpleoFilaFormulario();
		    $empleo_fila_formulario->id_empleador=$datos[$i]['cc'];
		    $empleo_fila_formulario->externo=$externos;
		    $empleo_fila_formulario->num_empleados=$datos[$i]['ne'];
		    //$empleo_fila_formulario->id_empleador_display=$empleador->id_empleador->toString();
		    //$empleo_fila_formulario->descripcion=$empleador->descripcion;
		    //$empleo_fila_formulario->num_empleados_anterior=-1;
		    $empleo_enc->numero_empleados[$datos[$i]['cc']]=$empleo_fila_formulario;
		    
		}
	}
		
	///Tipo de carga WEB, porque se está haciendo un guardar desde el formulario
	$empleo_enc->tipo_carga = TIPO_CARGA_WEB;
}
?>