<?php

//TODO: Controlador form. alojamieento.

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/EmpleoController.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_HOME, PAGE_EMPLEO_FORM));

//Se recogen los parámetros para cargar la encuesta
$mes_encuesta = $page->request_post_or_get(ARG_MES, NULL);
$ano_encuesta = $page->request_post_or_get(ARG_ANO, NULL);

//Se cargan los datos del establecimiento
$establecimiento = $page->get_current_establecimiento();
if ($establecimiento == null)
{
	$page->abort_with_error(PAGE_HOME, "No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

if($page->have_any_perm(array(PERM_USER,PERM_RECEPCION)))
{
	/// 1b. Los usuarios no pueden rellenar una encuesta si el establecimiento está dado de baja.
	$page->abort_si_estado_baja($establecimiento);
}

/// Si no estan definidos por parametros...
if ($mes_encuesta==NULL && $ano_encuesta==NULL)
{
	$page->abort_with_error(PAGE_HOME, "No se ha definido el mes y año para el que mostrar el cuestionario.");
}

$empleo_ctl = new EmpleoController();

/// Cargar estado de la encuesta de alojamiento actualmente en curso para comprobar si es del mes y año pedidos.
/// Comprobar si la encuesta debe mostrarse en modo de solo lectura.
/// Cargar los datos del cuestionario para el mes y año
/// Carga los datos del establecimiento valido en el mes y ano de la encuesta.
/// el criterio seguido es que los datos a dia 1 del mes de la encuesta son los correctos.
$empleo_estado = $empleo_ctl->cargar_encuesta($establecimiento, $page->get_current_userid(), $mes_encuesta, $ano_encuesta);
$empleo_enc = $empleo_estado->encuesta;
if ($empleo_enc == NULL)
{
	/// No existe cuestionario para esa fecha, abortar la operacion.
	$page->abort_with_error(PAGE_HOME, "No existe cuestionario para el mes y año elegidos.");
}

// EN IMPRESION SIEMPRE SE VAN A MOSTRAR TODOS LOS DIAS DEL MES.
/// Se calcula el número de días abierto del cuestionario
/// Se calcula el número de días abierto del cuestionario
$fecha_helper = new DateHelper();

//Obtener los datos...
$lista_empleadores=EmpleoFilaFormulario::getFilasFromListaEmpleadores($empleo_ctl->listarEmpleadores($establecimiento->id_establecimiento));
if($empleo_enc->numero_empleados!=null)
{
    foreach($empleo_enc->numero_empleados as $id_empleador => $empleados)
    {
        //if(isset($lista_empleadores[$id_empleador]))
        //{
            //$lista_empleadores[$id_empleador]->num_empleados=$empleados;
            $lista_empleadores[$id_empleador]=$empleados;
        //}
    }
}


$variables = array(
        'data'                  =>  $lista_empleadores,
        'cuestionario' 			=> 	$empleo_enc,
		'mes_encuesta'			=>  $empleo_enc->mes,
		'ano_encuesta'			=>  $empleo_enc->ano,
		'datos_estab'			=>  $empleo_estado->datos_estab,
		'navpage_url'			=>  $page->build_url(PAGE_EMPLEO_FORM_AJAX, array(ARG_MES=>$empleo_enc->mes, ARG_ANO=>$empleo_enc->ano)),
		'aloja_plazo'			=>  $empleo_estado->plazo,
		//Si es hotel o apartamento
		'es_hotel'              => $establecimiento->es_hotel()
);

/// Render de la pagina
$page->render( "empleo_form_print_view.php", $variables, false);

$page->end_session();

?>