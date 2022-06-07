<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/lib/Trimestre.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/expectativas/EncuestaTemplate.class.php");
require_once(__DIR__."/classes/expectativas/EncuestaTemplateXmlReader.class.php");
require_once(__DIR__."/classes/expectativas/EncuestaTemplateFormReader.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasFormData.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasPlazos.class.php");
require_once(__DIR__."/classes/Noticias.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasController.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_EXP_INDEX, PAGE_EXP_FORMULARIO));


$estid = $page->request_post_or_get(ARG_ESTID);
$query_trim = $page->request_post_or_get(ARG_TRIM);
$query_ano = $page->request_post_or_get(ARG_ANO);


/// 0. Si en la peticion se ha indicado trimestre, se trabaja con un cuestionario concreto.
$sel_trim = null;
if ($query_ano != null && $query_trim != null)
{
	$sel_trim = new Trimestre($query_trim, $query_ano);
}

$es_admin=$page->have_any_perm(array(PERM_ADMIN, PERM_ADMIN_ISTAC));

/// 1. Obtener el establecimiento y trimestre con el que se va a trabajar
if($page->have_any_perm(array(PERM_GRABADOR,PERM_ADMIN,PERM_ADMIN_ISTAC)))
{
	if(($estid == null) || ($query_trim == null) || ($query_ano == null))
	{
		$original_params = null;
		if ($page->request_get(ARG_OP) != null)
		{
			$original_params[ARG_OP] = $page->request_get(ARG_OP);
		}
		$original_params[ARG_ESTID] = $estid;
		$original_params[ARG_TRIM] = $query_trim;
		$original_params[ARG_ANO] = $query_ano;
		
		if($page->request_post(ARG_OP) != 'save')
		{
			$optit="Crear o modificar cuestionario";
			list($estid,$sel_trim) = $page->select_establecimiento_trimestre($page->self_url($original_params, TRUE));
		}
		else
		{
			// En la operación OP_SUBIR_XML no hace falta establecer el mes y año. Ya vienen en el fichero XML.
			$estid = $page->select_establecimiento($page->self_url($original_params, TRUE));
		}
	}
	
	$sel_trim=new Trimestre($query_trim, $query_ano);
	$page->set_current_establecimiento($estid);

	//Reinicia camino de hormigas para no mostrar vinculo a exp_index.
	$page->init_page_path(array(PAGE_EXP_ANTERIORES, PAGE_EXP_FORMULARIO));
}

$establecimiento = $page->get_current_establecimiento();
if ($establecimiento == null)
{
	$page->abort_with_error(PAGE_EXP_INDEX, "No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

if($page->have_any_perm(array(PERM_USER,PERM_RECEPCION)))
{
    /// 1b. Los usuarios no pueden rellenar una encuesta si el establecimiento está dado de baja.
	$page->abort_si_estado_baja($establecimiento);
}

/// 2. Comprobar si el cuestionario debe mostrarse para rellenarlo o en modo de solo lectura.
$date_actual = new DateTime('now');
$exp_plazo = ExpectativasPlazos::cargar_plazo_abierto($date_actual);

$modificable = true;
if ($sel_trim != null && $exp_plazo != null)
{
    /// Si se esta dentro del plazo, dejar modificar el cuestionario.
    if ($sel_trim->igual( $exp_plazo->trimestre))
    {
        $modificable = true;
    }
    else
    {
    	if (!$es_admin)
    	{
        	$modificable = false;
    	}
    }
}

/// 3. Solo administradores pueden modificar el cuestionario fuera de plazo.
if($exp_plazo == null && $page->have_any_perm(array(PERM_USER,PERM_RECEPCION)))
{
    $modificable = false;
}


/// 4. Cargar el objeto EncuestaTemplate que representa al cuestionario.
///NOTA: El metodo empleado sufre de problemas de versionado de expectativas (si se cambia el form, no se podran leer las antiguas??)
$reader = new EncuestaTemplateXmlReader();
@$template = $reader->load(__DIR__.FORM_EXPECTATIVAS);
if ($template === false)
{
	@log::error("Error al leer la plantilla de expectativas: " . $reader->error);
	if ($es_admin)
	{
		$page->abort_with_error(PAGE_EXP_INDEX, $reader->error);
	}
	else 
		$page->abort_with_error(PAGE_EXP_INDEX, "Ahora mismo, el cuestionario de expectativas no está disponible. Disculpe las molestias.");	
}

/// 4A. OPERACION GUARDAR
$op = $page->request_post(ARG_OP);
if (($op != null) && ($op=='save') && ($modificable==true))
{
    $trimestre_real = ($es_admin) ? $sel_trim->trimestre : $exp_plazo->trimestre->trimestre;
    $anyo_real = ($es_admin) ? $sel_trim->anyo : $exp_plazo->trimestre->anyo;
    $infoextra = array(
        "año" => $anyo_real,
        "trimestre" => $trimestre_real
    );
    $res_guardar = guardar($page, $establecimiento, $trimestre_real, $anyo_real, $template);
	if ($res_guardar === true)
	{
		if ($page->have_any_perm(array(PERM_GRABADOR,PERM_ADMIN,PERM_ADMIN_ISTAC)))
		    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_EXPECTATIVAS, SUCCESSFUL, array('Establecimiento: '.$establecimiento->id_establecimiento ), $infoextra);
		else {
			
			$exp_ctl = new ExpectativasController();
			$exp_ctl->enviar_correo_confirmacion_cierre($establecimiento, $exp_plazo->trimestre );
			
			@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_EXPECTATIVAS, SUCCESSFUL, NULL, $infoextra);
		}
	}
	else 
	{
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_EXPECTATIVAS, FAILED, $res_guardar, $infoextra);
	}
	
	$vars = array('res' => $res_guardar);	
	$page->render("exp_formulario_guardar_view.php", $vars);
	$page->end_session();
	exit();
}

/// 4B. OPERACION RELLENAR (nueva o modificar).
/// Cargar o crear los datos de la encuesta (si existe se cargaran, 
/// si no existe se creara un objeto nuevo con los datos pasados.
$exp_dao = new ExpectativasFormDao();
// Elige el trimestre segun prioridad: seleccionado primero, despues por plazo.
$trimestre = null;
if ($sel_trim != null)
    $trimestre = $sel_trim;
else if ($exp_plazo != null)
    $trimestre = $exp_plazo->trimestre;
else
{
    @log::error("Formulario de expectativas: No se ha indicado el trimestre y se aborta con error");
    $page->abort_with_error(PAGE_EXP_INDEX, "El cuestionario de expectativas no está disponible.");
}

$exp = $exp_dao->cargar($establecimiento->id_establecimiento,$trimestre->trimestre, $trimestre->anyo);
if ($exp->es_nuevo)
{
    if ($modificable)
    {
	    $exp->fecha_grabacion = new DateTime("now");
    }
    else
    {
        /// Un cuestionario nuevo no se puede consultar, se termina con error.
        $page->abort_with_error(PAGE_EXP_INDEX, "El cuestionario de expectativas no está disponible.");
    }
}

/// Poner el valor de los objetos opciones según los datos cargados o nuevos.
$template->set_variables($exp->variables);
	
/// Resolver variables de plantilla
$vars = array();
$vars["[RECOGIDA_TRES_MESES_ANTERIOR]"] = $trimestre->anterior()->tostring();
$vars["[RECOGIDA_TRES_MESES_ANTERIOR_AÑO]"] = $trimestre->anterior()->tostring('y');
$vars["[RECOGIDA_TRES_MESES_ANTERIOR_AÑO_ANTERIOR]"] = $trimestre->anterior()->anyoanterior()->tostring('y');
$vars["[RECOGIDA_TRES_MESES_ANTERIOR_ANTERIOR]"] = $trimestre->anterior()->anterior()->tostring();
$vars["[RECOGIDA_TRES_MESES_ANTERIOR_ANTERIOR_AÑO]"] = $trimestre->anterior()->anterior()->tostring('y');
$vars["[RECOGIDA_TRES_MESES_AÑO_ANTERIOR]"] = $trimestre->anyoanterior()->tostring();
$vars["[RECOGIDA_TRES_MESES]"] = $trimestre->tostring();
$vars["[RECOGIDA_TRES_MESES_AÑO]"] = $trimestre->tostring('y');
$vars["[PROXIMOS_TRES_MESES]"] = $trimestre->tostring();
$vars["[PROXIMOS_TRES_MESES_AÑO]"] = $trimestre->tostring('y');
$vars["[PROXIMOS_TRES_MESES_AÑO_ANTERIOR]"] = $trimestre->anyoanterior()->tostring('y');
$vars["[PRIMER_MES]"] = strtoupper($trimestre->tostring('m1'));
$vars["[SEGUNDO_MES]"] = strtoupper($trimestre->tostring('m2'));
$vars["[TERCER_MES]"] = strtoupper($trimestre->tostring('m3'));

/// Sustituir las variables del tipo [xxx] en los textos de la encuesta por los valores. 
$template->sustituir_textos($vars);
	
$vars_para_vista = array(
        'modificable'  => $modificable,
		'es_admin'		=> $es_admin,
		'navpage_url'	=> $page->build_url(PAGE_EXP_FORMULARIO_AJAX),
		'establecimiento' => $establecimiento,
		'encuesta' => $exp,
		'trimestre_encuesta' => str_replace('Y','y', ucwords($trimestre->tostring())),
		'template' => $template,
		'correo_dudas' => DIRECCION_MAIL,
		'modDatosUrl' => $page->build_url(PAGE_ESTAB_CHANGE)
		);

/// Render de la pagina
$page->render( "exp_formulario_view.php", $vars_para_vista );
$page->end_session();


/**
 * Operacion GUARDAR de la encuesta, se llama cuando se pulsa el boton Enviar en el formulario.
 * Recoge los datos enviados por el cliente (POST), los valida y los introduce en la BBDD.
 * @param unknown_type $page
 * @param unknown_type $establecimiento
 * @param unknown_type $template
 * @return boolean Devuelve TRUE si la operacion se ha completado con exito.
 */
function guardar($page, $establecimiento, $trim, $anyo, $template)
{
    $estid = $establecimiento->id_establecimiento;
    
	$trim_doc = $page->request_post("pregunta_trimestre");
	$anyo_doc = $page->request_post("pregunta_anyo");
    $estid_doc = $page->request_post("pregunta_signatura");
    
    if(($estid!=$estid_doc)||($anyo_doc!=$anyo_doc)||($trim!=$trim_doc))
    {
        $page->abort_with_error(PAGE_EXP_INDEX, "El identificador del establecimiento, el trimestre o el año NO son correctos.");
    }
    
    $exp_dao = new ExpectativasFormDao();
    $exp = $exp_dao->cargar_cabecera($estid, $trim, $anyo);
    
    /// 2. Actualizar/rellenar los datos de la cabecera segun lo recibido.
    /// A. Fecha de grabacion
    if ($page->have_any_perm(PERM_GRABADOR))
    {
        /// Grabador puede modificar la fecha.
        $dia = $page->request_post('pregunta_fecha_dia');
        $mes = $page->request_post('pregunta_fecha_mes');
        $yyy = $page->request_post('pregunta_fecha_agno');
        $fecha = DateHelper::parseDate("$dia-$mes-$yyy");
        /// Validacion 1: La fecha introducida no es correcta.
        if ($fecha === false)
            return "La fecha introducida no es correcta.";
            $exp->fecha_grabacion = $fecha;
    }
    else
    {
        /// Los usuarios no pueden modificar la fecha y se coge la fecha actual.
        $exp->fecha_grabacion = new DateTime("now");
    }
    
    /// B. Campos director y otro_cargo
    $esDirector = ($page->request_post('pregunta00') == 'Director') || ($page->request_post('pregunta00') == '1');
    $exp->es_director = $esDirector;
    if ($esDirector)
    {
        $exp->otra_persona_cargo = null;
    }
    else
    {
        /// Validacion 2: Si se indica otro cargo, que se haya indicado algo en el campo de texto del mismo.
        $oc_radio = $page->request_post('pregunta00');
        if($oc_radio == null || ( $oc_radio != "Otra persona" && $oc_radio != '6' ))
            return "No se ha indicado qué persona o cargo rellena la encuesta, o el valor indicado es incorrecto.";
            
            $oc = trim($page->request_post('pregunta00_texto'));
            if ($oc == null)
                return "No se ha indicado qué persona o cargo rellena la encuesta, o el valor indicado es incorrecto.";
                $exp->otra_persona_cargo = $oc;
    }
    
    /// C. Usuario grabador
    $exp->usuario_grabador = $page->get_auth_data('uname');
    
    /// 3. Actualizar/rellenar los datos de las variables de la encuesta segun lo recibido.
    $respuestas = $page->request_post("respuestas");
    
    /// Validacion 3: Todas las respuestas tienen valor en rango y las obligatorias no estan vacias.
    $errores = $template->validar_respuestas($respuestas);
    if ($errores != null)
    {
        return $errores;
    }
    
    foreach($template->get_opciones() as $opc)
    {
        if (isset($respuestas[$opc->index]) && $respuestas[$opc->index] != null)
        {
            $exp->variables[$opc->nombre_oracle] = $respuestas[$opc->index];
        }
        else
        {
            //NOTA: Solo se puede llegar aqui si la opcion no es requerida.
            $exp->variables[$opc->nombre_oracle] = $opc->sinvalor;
        }
    }
    
    /// 4. Insertar/actualizar el cuestionario en la BBDD.
    $exp_dao->guardar($exp);
    
    return true;
}




?>
