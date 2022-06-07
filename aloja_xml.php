<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaDao.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/aloja/AlojaXmlDao.class.php");
require_once(__DIR__."/classes/aloja/AlojaXmlReader.class.php");
require_once(__DIR__."/classes/aloja/AlojaErrorCollection.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");
require_once(__DIR__."/lib/InputCleaner.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_ALOJA_XML));

define("OP_SUBIR_XML", "sx");
define("OP_SUBIR_XML_FORZADO", "sxf");
define("OP_SUBIR_XML_EXCESO", "sxfe");
define("OP_RESUBIR_XML", "rsx");
define("OP_DESCARGAR_XML", "dwl");

define("ARG_CESEACTIVIDAD", "ceseActividad");

define("ARG_TIPO_MOTIVO_EXCESO_PLAZAS", "tmep");
define("ARG_TIPO_MOTIVO_EXCESO_HABITACIONES", "tmeh");
define("ARG_DETALLE_MOTIVO_EXCESO_PLAZAS", "dmep");
define("ARG_DETALLE_MOTIVO_EXCESO_HABITACIONES", "dmeh");

define("FORMULARIO_BOTON_SUBIR_XML", 1);
define("FORMULARIO_BOTON_RESUBIR_XML", 2);
define("FORMULARIO_BOTON_DESCARGAR_XML", 4);
define("FORMULARIO_ENLACE_MANUAL", 8);


/// Esta pagina implementa varias operaciones:

///ADMIN: El administrador puede elegir el establecimiento, el mes y año del cuestionario al que corresponden los datos para todas las operaciones.

/// A. SUBIR XML
/// La pagina acepta un archivo XML subido y realiza la carga de los datos en la base de datos, 
/// realizando la validación de los mismos y cerrando el cuestionario en caso de que los datos sean validos.
/// Si se detectan modificaciones de los datos de cabecera del establecimineto, se registran en la base de datos.

/// B. RESUBIR XML (solo ADMIN)
/// Con un xml almacenado previamente en la base de datos, lo extrae de la bae de datos y realiza el proceso de subida (A).

/// C. DESCARGAR XML
/// Extrae el XML de la base de datos y lo devuelve como respuesta de la peticion de la pagina.

/// D. Mostrar formulario de envio de XML
/// Si la petición de la pagina no indica otro tipo de operación, se muestra un formulario para poder subir el archivo XML.

$mes_trabajo = null;
$ano_trabajo = null;
$selected_UT = null;

function err_detail($op,$id_est,$mes,$anio)
{
    $salida="op=";
    if($op!=null)
        $salida.=$op;
    $salida.="est=";
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

function handle_errors($page, AlojaErrorCollection $errordata, $es_hotel, $botones, $global_msg = null)
{
	global $mes_trabajo;
	global $ano_trabajo;
	global $selected_UT;
	
	// Botones: 0=no hay botones, 1=botón subir (escoger otro archivo), 2=botón resubir (mismo fichero anterior), 4=botón descargar (descargar fichero subido anteriormente), 8=botón rellenado manual
	$vv = array();
	$vv['es_hotel'] = $es_hotel;
	$vv['titulo'] = "Envío de archivo de datos";
	$vv['errors'] = $errordata;
	$vv['botones'] = $botones;
	$vv['global_msg'] = $global_msg;
	$vv[ARG_MES] = $mes_trabajo;
	$vv[ARG_ANO] = $ano_trabajo;
	if (isset($botones) && (($botones & 8)!=0))
	{
		$params=array();
		$params['mes_encuesta']=$mes_trabajo;
		$params['ano_encuesta']=$ano_trabajo;
		$params['tab']=2;
		if(isset($selected_UT))
		{
			$aloja_form_data = array(
					'uts' => $selected_UT,
					'mes' => $mes_trabajo,
					'ano' => $ano_trabajo);
			$page->set_sess_state(SESS_ALOJA_FORM_DATA, $aloja_form_data);
		}
		$vv['urlFormAloja'] = $page->build_url(PAGE_ALOJA_FORM, $params);
	}
	$page->render("aloja_xml_errores_view.php", $vv);
	$page->end_session();
}

/*
 * Función para gestionar el caso de un cuestionario con avisos por exceso de plazas o habitaciones.
 * NOTA: Permite cerrar el cuestionario a pesar de contener avisos.
 * 
 * PARAMETROS: 
 * page: instancia de la página en ejecución.
 * mes: mes de la encuesta.
 * ano: año de la encuesta.
 * errordata: Lista de avisos generados en la subida del cuestionario.
 * es_hotel: true si el establecimiento es un hotel.
 * botones: Máscara binaria que indica qué botones deben ser visibles al usuario.
 * 	    1000 => Enlace al formulario de alojamiento para el rellenado manual de la encuesta.
 * 	    0100 => Botón de "Descargar fichero de datos".
 * 	    0010 => Botón de "Resubir fichero de datos".
 * 	    0001 => Botón de "Subir fichero de datos".
 * global_msg: Mensaje de texto indicando al usuario el resultado general de la operación.
 */
function handle_avisos($page, $mes, $ano, AlojaErrorCollection $errordata, $hayExcesoPlazasMes, $hayExcesoPlazas, $hayExcesoHabitaciones, $es_hotel, $botones, $global_msg = null)
{
	global $mes_trabajo;
	global $ano_trabajo;
	global $selected_UT;
	global $ceseActividad;
	global $cuestionario_parcial;
	
	$vv = array();
	$vv['es_hotel'] = $es_hotel;	
	$vv['titulo'] = "Envío de archivo de datos";
	$vv['hayExcesoPlazasMes'] = $hayExcesoPlazasMes;
	$vv['hayExcesoPlazas'] = $hayExcesoPlazas;
	$vv['hayExcesoHabitaciones'] = $hayExcesoHabitaciones;
	
	if(($hayExcesoPlazasMes==true) || ($hayExcesoPlazas==true) || ($hayExcesoHabitaciones==true))
	{
		$vv['motivosExcesos'] = obtenerMotivos($es_hotel);
		$vv['limiteInvitaciones'] = obtenerLimitesInvitaciones();
	}
	$vv['errors'] = $errordata;
	$vv['botones'] = $botones;
	$vv['global_msg'] = $global_msg;
	$vv[ARG_MES] = $mes;
	$vv[ARG_ANO] = $ano;
	if (isset($botones) && (($botones & 8)!=0))
	{
		$params=array();
		$params['mes_encuesta']=$mes_trabajo;
		$params['ano_encuesta']=$ano_trabajo;
		$params['tab']=2;
		if(isset($selected_UT))
		{
			$aloja_form_data = array(
					'uts' => $selected_UT,
					'mes' => $mes_trabajo,
					'ano' => $ano_trabajo);
			$page->set_sess_state(SESS_ALOJA_FORM_DATA, $aloja_form_data);
		}
		$vv['urlFormAloja'] = $page->build_url(PAGE_ALOJA_FORM, $params);
	}
	
	
	$vv['ceseActividad'] = ($ceseActividad!=NULL && $ceseActividad=='1');
	$vv['cuestionario_parcial'] = $cuestionario_parcial;
	
	$page->render("aloja_xml_avisos_view.php", $vv);
	$page->end_session();
}

function handle_ok($page, $url_acuse, $es_hotel, $email = null)
{
	$vv = array();
	$vv['es_hotel'] = $es_hotel;	
	$vv['url_acuse'] = $url_acuse;
	$vv['titulo'] = "Envío de archivo de datos";
	$vv['errors'] = null;
	if($email!=null)
		$vv['email'] = $email;
	$page->render("aloja_xml_errores_view.php", $vv);
	$page->end_session();
}

$aloja_ctl = new AlojaController();

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));
$operacion=$page->request_post_or_get(ARG_OP);

if ($es_admin)
{
    $selected_estid = $page->request_post_or_get(ARG_ESTID);
	$mes_trabajo = $page->request_post_or_get(ARG_MES);
	$ano_trabajo = $page->request_post_or_get(ARG_ANO);
	
	if(($selected_estid==NULL) || ($mes_trabajo==NULL) || ($ano_trabajo==NULL))
	{
		$original_params = null;
		if ($operacion != null)
			$original_params[ARG_OP] = $operacion;
		

		$optit=null;
		switch($operacion)
		{
			case OP_SUBIR_XML:
				$optit="Subir XML";
				break;
			case OP_SUBIR_XML_FORZADO:
				$optit="Subir XML";
				break;
			case OP_SUBIR_XML_EXCESO:
				$optit="Subir XML con excesos";
				break;
			case OP_RESUBIR_XML:
				$optit="Resubir XML";
				break;
			case OP_DESCARGAR_XML:
				$optit="Descargar XML";
				break;
			default:
				$optit="Subir XML";
				break;
		}
				
		if($operacion != OP_SUBIR_XML)
		{
			list($selected_estid,$mes_trabajo, $ano_trabajo) = $page->select_establecimiento_mes_ano($page->self_url($original_params, TRUE));
		}
		else
		{
			// En la operación OP_SUBIR_XML no hace falta establecer el mes y año. Ya vienen en el fichero XML.
			$selected_estid = $page->select_establecimiento($page->self_url($original_params, TRUE));
		}
	}
	$page->set_current_establecimiento($selected_estid);
	$establecimiento = $page->get_current_establecimiento();
}
else 
{
	/// Datos del establecimiento del usuario y de la encuesta en curso
	$establecimiento = $page->get_current_establecimiento();
	$aloja_estado = $aloja_ctl->cargar_estado_encuesta_alojamiento($establecimiento, $page->get_current_userid());
	
	$mes_trabajo = $aloja_estado['encuesta']->mes;
	$ano_trabajo = $aloja_estado['encuesta']->ano;
	$hay_datos = !$aloja_estado['encuesta']->es_nuevo();
}

if ($establecimiento == null)
{
	$page->abort_with_error(PAGE_ALOJA_XML, "No se ha definido el establecimiento con el que se va a operar.");
}

$ceseActividad = $page->request_post_or_get(ARG_CESEACTIVIDAD);

// Mecanismo de seguridad redundante: Cuando un usuario quiere cerrar un cuestionario del mes en curso, exigimos que haya activado la casilla correspondiente en el formulario.
$permitir_cerrar_cuestionario_no_vencido=$es_admin;
if($permitir_cerrar_cuestionario_no_vencido==false)
{
	if($ceseActividad!=NULL && $ceseActividad=='1')
		$permitir_cerrar_cuestionario_no_vencido=true;
}

// Se trata de la grabación sin cierre, de un cuestionario para admitir los datos del mes actual. COVID-19
$cuestionario_parcial=false;
$fecha_helper = new DateHelper();
if(($mes_trabajo == $fecha_helper->mes_sistema) && ($ano_trabajo == $fecha_helper->anyo_sistema))
{
	if(($ceseActividad==NULL) || ($ceseActividad!='1'))
		$cuestionario_parcial=true;
}

/// ASERTO: Definidos $establecimiento, $mes_trabajo, $ano_trabajo

/// OPERACION POST PARA SUBIR XML.
if ($operacion == OP_SUBIR_XML)
{
	// 1. Subir archivo XML. IMPORTANTE!! Hay que comprobar que el contenido XML viene codificado en UTF-8
	// Error detectado en ocasiones: "ORA-19202: Error occurred in XML processing // LPX-00283: document encoding is UTF-8-based but default input encoding is not".
	$x = new AlojaXmlReader();
	$ok = $x->read_xml_desde_request($_FILES['userfile']);
	if (!$ok)
	{
		/// En caso de error al no poder leer el xml, muestra los errores que han ocurrido terminando el proceso.
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, FAILED, $x->errors->errores,array("año" => $ano_trabajo, "mes" => $mes_trabajo));
		handle_errors($page, $x->errors, $establecimiento->es_hotel(), ($es_admin) ? 1:0);
		exit;
	}
	
	$subido_ok = $aloja_ctl->subir_xml($x, $establecimiento->id_establecimiento, $page->get_current_userid(), $mes_trabajo, $ano_trabajo, false, $cuestionario_parcial);
	
	/// SI $subido_ok[0] == TRUE
	///      $subido_ok[1] <= Encuesta de alojamiento
	///      $subido_ok[2] <= Tabla con datos de cabecera modificados.
	//		 $subido_ok[3] <= verdadero si se ha guardado el cuestionario
	/// SI $subido_ok[0] == FALSE
	///      $subido_ok[1] <= Array con errores
	///      $subido_ok[2] <= verdadero si se ha guardado el cuestionario
	///      $subido_ok[3] <= Información de excesos (si existe) o null
	
	$faltan_personal_precios=(isset($subido_ok[4]) ? $subido_ok[4] : false);
	$botones=(($es_admin) ? (FORMULARIO_BOTON_SUBIR_XML+FORMULARIO_BOTON_RESUBIR_XML+FORMULARIO_BOTON_DESCARGAR_XML):0);
	
	if ($subido_ok[0] !== true)
	{
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, FAILED, $subido_ok[1]->errores,array("año" => $ano_trabajo, "mes" => $mes_trabajo));
		if ($subido_ok[2])
		{
			// El cuestionario ha sido guardado con errores y/o avisos.
			// En caso de faltar datos de personal y/o precios, debemos mostrar un botón para saltar directamente al formulario de la encuesta de alojamiento.
			if($faltan_personal_precios)
			{
				// Si hay falta de datos de personal y/o precios siempre debemos mostrar un enlace que le permita saltar directamente al formulario manual para introducirlos.
				$botones += FORMULARIO_ENLACE_MANUAL;
				
				/// Precargamos la lista de países del cuestionario...
				$dao = new AlojaDao();
				$selected_UT = $dao->cargar_uts_rellenados($establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);
			}
			
			if ($subido_ok[1]->hay_solo_avisos())
			{
				$excesos=$subido_ok[3];
				$msg="El archivo de datos se ha subido y <strong>los datos se han guardado</strong>, pero no ha sido posible cerrar el cuestionario debido a avisos generados en la validación.";
				if(($excesos->hayExcesoPlazasMes==true) || ($excesos->hayExcesoPlazas==true) || ($excesos->hayExcesoHabitaciones==true))
				{
					$msg .= "Debe justificar el exceso de ocupación o habitaciones para cerrar la encuesta.";
				}
				else
				{
					if($cuestionario_parcial==false)
						$msg .= " Pulse el botón <strong>Cerrar Encuesta</strong> para cerrar la encuesta aunque tenga avisos.";
				}
				
				handle_avisos($page, $mes_trabajo, $ano_trabajo, $subido_ok[1], $excesos->hayExcesoPlazasMes, $excesos->hayExcesoPlazas, $excesos->hayExcesoHabitaciones, $establecimiento->es_hotel(), $botones, $msg);
			}
			else
			{
				$botones = ($botones | FORMULARIO_ENLACE_MANUAL);
				handle_errors($page, $subido_ok[1], $establecimiento->es_hotel(), $botones, "El archivo de datos se ha subido y <strong>los datos se han guardado</strong>, pero no ha sido posible cerrar el cuestionario debido a errores de validación o falta de datos. El cuestionario debe ser rellenado manualmente desde la aplicación para que el cuestionario sea válido.");
			}
		}
		else
		{
			handle_errors($page,$subido_ok[1], $establecimiento->es_hotel(), $botones, "<strong>Los datos no han sido guardados</strong>. El archivo de datos se ha subido, pero no ha sido posible obtener los datos del cuestionario debido a errores de validación.");
		}
		exit;
	}
	else
	{
		// En caso de que todo haya ido bien, nos queda comprobar que no existan excesos de plazas/habitaciones.
		$aloja_cuest=$subido_ok[1];
		if(isset($aloja_cuest->excesoInfoObj))
		{
			$excesos=$aloja_cuest->excesoInfoObj;
			if($excesos->hayExcesoPlazas || $excesos->hayExcesoPlazasMes || $excesos->hayExcesoHabitaciones)
			{
				if($excesos->hayExcesoPlazas || $excesos->hayExcesoPlazasMes)
					$aloja_cuest->excesoPlazas=99;
				if($excesos->hayExcesoHabitaciones)
					$aloja_cuest->excesoHabitaciones=99;
					
				$dao = new AlojaDao();
				$ok = $dao->actualizar_info_exceso_cuestionario($aloja_cuest);
				if (!$ok)
				{
				    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array("Error durante la grabación de la información de excesos."),array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
					handle_errors($page, new AlojaErrorCollection(array("Error durante la grabación de la información.")), $establecimiento->es_hotel(), $botones, "No ha sido posible cerrar el cuestionario. Error durante la grabación de los datos.");
					exit;
				}

				// Hay avisos de exceso, el cuestionario queda pendiente de cierre.
				handle_avisos($page, $mes_trabajo, $ano_trabajo, $aloja_cuest->val_errors, $excesos->hayExcesoPlazasMes, $excesos->hayExcesoPlazas, $excesos->hayExcesoHabitaciones, $establecimiento->es_hotel(), $botones, "El archivo de datos se ha subido y <strong>los datos se han guardado</strong>, pero no ha sido posible cerrar el cuestionario debido a avisos generados en la validación. Debe justificar el exceso de ocupación o habitaciones para cerrar la encuesta.");
				exit;
			}
		}
	}

	// El cuestionario ya está en BDD.
	
	//Añadir las modificaciones de cabecera en caso de que las hubiera.
	$mod_cabecera = isset($subido_ok[2])? $subido_ok[2] : null;
	if ($mod_cabecera != null)
	{
		$mods = obtener_modificaciones($establecimiento, $mod_cabecera);
		if (count($mods) > 0)
		{
			$fecha_reg = $establecimiento->registrar_modificacion($establecimiento->id_establecimiento, $page->get_current_userid(), $mods);
		}
	}
	
	@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
	
	$ok = $aloja_ctl->cerrar_cuestionario($subido_ok[1], $permitir_cerrar_cuestionario_no_vencido);
	if ($ok !== true)
	{
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array($ok),array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
		handle_errors($page, new AlojaErrorCollection(array($ok)), $establecimiento->es_hotel(), $botones, "El archivo de datos se ha subido y los datos se han cargado, pero no ha sido posible cerrar el cuestionario debido a errores de validación.");
		exit;
	}
	else 
	{
		@$aloja_ctl->enviar_correo_confirmacion_cierre($establecimiento, $subido_ok[1]);
		
		@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
		$url_acuse = $page->build_url(PAGE_ALOJA_ACUSE, array('mes_encuesta'=>$aloja_cuest->mes, 'ano_encuesta'=>$aloja_cuest->ano));
		handle_ok($page, $url_acuse, $establecimiento->es_hotel(), $establecimiento->email);
	}
}
elseif ($operacion == OP_SUBIR_XML_EXCESO)
{
	$botones=(($es_admin) ? (FORMULARIO_BOTON_SUBIR_XML+FORMULARIO_BOTON_RESUBIR_XML+FORMULARIO_BOTON_DESCARGAR_XML):0);
	
	// Comprobar si hay datos actualmente.
	$dao = new AlojaDao();
	$aloja_cuest = $dao->cargar_registro_cuestionario($establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);
	if($aloja_cuest!=null)
	{
		//iconv("UTF-8", "CP1252", urldecode())
		$aloja_cuest->excesoPlazas=$page->request_post_or_get(ARG_TIPO_MOTIVO_EXCESO_PLAZAS);
		$aloja_cuest->excesoPlazasDetalle=InputCleaner::oracle(iconv("UTF-8", "CP1252", urldecode($page->request_post_or_get(ARG_DETALLE_MOTIVO_EXCESO_PLAZAS))));
		$aloja_cuest->excesoHabitaciones=$page->request_post_or_get(ARG_TIPO_MOTIVO_EXCESO_HABITACIONES);
		$aloja_cuest->excesoHabitacionesDetalle=InputCleaner::oracle(iconv("UTF-8", "CP1252", urldecode($page->request_post_or_get(ARG_DETALLE_MOTIVO_EXCESO_HABITACIONES))));
		
		$ok = $dao->actualizar_info_exceso_cuestionario($aloja_cuest);
		if (!$ok)
		{
		    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array("Error durante la grabación de la información de excesos."),array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
			handle_errors($page, new AlojaErrorCollection(array("Error durante la grabación de la información de excesos.")), $establecimiento->es_hotel(), $botones, "No ha sido posible cerrar el cuestionario. Error durante la grabación de los datos.");
			exit;
		}
	
		@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
		
		$ok = $aloja_ctl->cerrar_cuestionario($aloja_cuest, $permitir_cerrar_cuestionario_no_vencido);
		if ($ok !== true)
		{
		    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array($ok),array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
			handle_errors($page, new AlojaErrorCollection(array($ok)), $establecimiento->es_hotel(), $botones, "El archivo de datos se ha subido y los datos se han cargado, pero no ha sido posible cerrar el cuestionario debido a errores de validación.");
			exit;
		}
		else
		{
			@$aloja_ctl->enviar_correo_confirmacion_cierre($establecimiento, $aloja_cuest);
		
			@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
			$url_acuse = $page->build_url(PAGE_ALOJA_ACUSE, array('mes_encuesta'=>$aloja_cuest->mes, 'ano_encuesta'=>$aloja_cuest->ano));
			handle_ok($page, $url_acuse, $establecimiento->es_hotel(), $establecimiento->email);
		}
	}
	else
	{
		// Cuestionario no encontrado, procesar error...
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array("Error al registrar la información de exceso. Cuestionario no encontrado."),array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
		handle_errors($page, new AlojaErrorCollection(array("Error al registrar la información enviada. Cuestionario no encontrado.")), $establecimiento->es_hotel(), $botones, "No ha sido posible cerrar el cuestionario. Cuestionario no encontrado.");
		exit;
	}
}
elseif ($operacion == OP_SUBIR_XML_FORZADO)
{
	//Para atender la peticion de cerrar encuesta con avisos.
	$xml = $aloja_ctl->descargar_xml($establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);

	if ($xml == null)
	{
	    $detalles=err_detail($operacion,$establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);
		$page->abort_with_error(PAGE_ALOJA_XML, "No existe cuestionario para la fecha indicada.".$detalles);
	}
	
	$x = new AlojaXmlReader();
	$ok = $x->read_xml($xml);
	if (!$ok)
	{
		/// En caso de error al no poder leer el xml, muestra los errores que ha ocurrido terminando el proceso.
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, FAILED, $x->errors->errores,array("año" => $ano_trabajo, "mes" => $mes_trabajo));
		handle_errors($page, $x->errors, $establecimiento->es_hotel(), ($es_admin) ? 7:0);
		exit;
	}
	
	$subido_ok = $aloja_ctl->subir_xml($x, $establecimiento->id_establecimiento, $page->get_current_userid(), $mes_trabajo, $ano_trabajo, true);
	
	/// SI $subido_ok[0] == TRUE
	///      $subido_ok[1] <= Encuesta de alojamiento
	///      $subido_ok[2] <= Tabla con datos de cabecera modificados.
	//		 $subido_ok[3] <= verdadero si se ha guardado el cuestionario
	/// SI $subido_ok[0] == FALSE
	///      $subido_ok[1] <= Array con errores
	///      $subido_ok[2] <= NO DEFINIDO.
	//		 $subido_ok[3] <= verdadero si se ha guardado el cuestionario
	
	$faltan_personal_precios=(isset($subido_ok[4]) ? $subido_ok[4] : false);
	
	// Si solo hay avisos, cierra el cuestionario.
	if ($subido_ok[0] !== true)
	{
		$botones=(($es_admin) ? (FORMULARIO_BOTON_SUBIR_XML+FORMULARIO_BOTON_RESUBIR_XML+FORMULARIO_BOTON_DESCARGAR_XML):0);
		
		@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, FAILED, $subido_ok[1]->errores,array("año" => $ano_trabajo, "mes" => $mes_trabajo));
		if ($subido_ok[2])
		{
			// El cuestionario ha sido guardado con errores y/o avisos.
			// En caso de faltar datos de personal y/o precios, debemos mostrar un botón para saltar directamente al formulario de la encuesta de alojamiento.
			if($faltan_personal_precios)
			{
				// Si hay falta de datos de personal y/o precios siempre debemos mostrar un enlace que le permita saltar directamente al formulario manual para introducirlos.
				$botones += FORMULARIO_ENLACE_MANUAL;
				
				$dao = new AlojaDao();
				$selected_UT = $dao->cargar_uts_rellenados($establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);
			}
			
			handle_errors($page, $subido_ok[1], $establecimiento->es_hotel(), $botones, "El archivo de datos se ha subido y <strong>los datos se han guardado</strong>, pero no ha sido posible cerrar el cuestionario debido a errores de validación o falta de datos. El cuestionario debe ser rellenado manualmente desde la aplicación para que el cuestionario sea válido.");
		}
		else
		{
			handle_errors($page,$subido_ok[1], $establecimiento->es_hotel(), $botones, "<strong>Los datos no han sido guardados</strong>. El archivo de datos se ha subido, pero no ha sido posible obtener los datos del cuestionario debido a errores de validación.");
		}
		exit;
	}
	$aloja_cuest=$subido_ok[1];
	@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
	
	$ok = $aloja_ctl->cerrar_cuestionario($aloja_cuest, $permitir_cerrar_cuestionario_no_vencido);
	if ($ok !== true)
	{
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array($ok),NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
		handle_errors($page, new AlojaErrorCollection(array($ok)), $establecimiento->es_hotel(), 0, "El archivo de datos se ha subido y los datos se han cargado, pero no ha sido posible cerrar el cuestionario debido a errores de validación.");
		exit;
	}
	else
	{
		@$aloja_ctl->enviar_correo_confirmacion_cierre($establecimiento, $aloja_cuest);
		
		@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
		$url_acuse = $page->build_url(PAGE_ALOJA_ACUSE, array('mes_encuesta'=>$aloja_cuest->mes, 'ano_encuesta'=>$aloja_cuest->ano));
		handle_ok($page, $url_acuse, $establecimiento->es_hotel(), $establecimiento->email);
	}
}
elseif ($es_admin && $operacion == OP_RESUBIR_XML)
{
	$xml = $aloja_ctl->descargar_xml($establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);
	
	if ($xml == null)
	{
	    $detalles=err_detail($operacion,$establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);
		$page->abort_with_error(PAGE_ALOJA_XML, "No existe cuestionario para la fecha indicada.".$detalles);
	}
	
	$x = new AlojaXmlReader();
	$ok = $x->read_xml($xml);
	if (!$ok)
	{
		/// En caso de error al no poder leer el xml, muestra los errores que ha ocurrido terminando el proceso.
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, FAILED, $x->errors->errores,array("año" => $ano_trabajo, "mes" => $mes_trabajo));
		handle_errors($page, $x->errors, $establecimiento->es_hotel(), 7);
		exit;
	}
	
	$subido_ok = $aloja_ctl->subir_xml($x, $establecimiento->id_establecimiento, $page->get_current_userid(), $mes_trabajo, $ano_trabajo);
	
	/// SI $subido_ok[0] == TRUE
	///      $subido_ok[1] <= Encuesta de alojamiento
	///      $subido_ok[2] <= Tabla con datos de cabecera modificados.
	//		 $subido_ok[3] <= verdadero si se ha guardado el cuestionario
	/// SI $subido_ok[0] == FALSE
	///      $subido_ok[1] <= Array con errores
	///      $subido_ok[2] <= verdadero si se ha guardado el cuestionario
	///      $subido_ok[3] <= Información de excesos (si existe) o null
	
	$faltan_personal_precios=(isset($subido_ok[4]) ? $subido_ok[4] : false);
	$botones=(FORMULARIO_BOTON_SUBIR_XML+FORMULARIO_BOTON_RESUBIR_XML+FORMULARIO_BOTON_DESCARGAR_XML);
	
	if ($subido_ok[0] !== true)
	{
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, FAILED, $subido_ok[1]->errores,array("año" => $ano_trabajo, "mes" => $mes_trabajo));
		if ($subido_ok[2])
		{
			// El cuestionario ha sido guardado con errores y/o avisos.
			// En caso de faltar datos de personal y/o precios, debemos mostrar un botón para saltar directamente al formulario de la encuesta de alojamiento.
			if($faltan_personal_precios)
			{
				// Si hay falta de datos de personal y/o precios siempre debemos mostrar un enlace que le permita saltar directamente al formulario manual para introducirlos.
				$botones += FORMULARIO_ENLACE_MANUAL;

				/// Precargamos la lista de países del cuestionario...
				$dao = new AlojaDao();
				$selected_UT = $dao->cargar_uts_rellenados($establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);
			}

			if ($subido_ok[1]->hay_solo_avisos())
			{
				$excesos=$subido_ok[3];
				$msg="El archivo de datos se ha subido y <strong>los datos se han guardado</strong>, pero no ha sido posible cerrar el cuestionario debido a avisos generados en la validación.";
				if(($excesos->hayExcesoPlazasMes==true) || ($excesos->hayExcesoPlazas==true) || ($excesos->hayExcesoHabitaciones==true))
				{
					$msg .= "Debe justificar el exceso de ocupación o habitaciones para cerrar la encuesta.";
				}
				else
				{
					$msg .= "Pulse el botón <strong>Cerrar Encuesta</strong> para cerrar la encuesta aunque tenga avisos.";
				}
				
				handle_avisos($page, $mes_trabajo, $ano_trabajo, $subido_ok[1], $excesos->hayExcesoPlazasMes, $excesos->hayExcesoPlazas, $excesos->hayExcesoHabitaciones, $establecimiento->es_hotel(), $botones, $msg);
			}
			else
			{
				$botones = ($botones | FORMULARIO_ENLACE_MANUAL);
				handle_errors($page, $subido_ok[1], $establecimiento->es_hotel(), $botones, "El archivo de datos se ha subido y <strong>los datos se han guardado</strong>, pero no ha sido posible cerrar el cuestionario debido a errores de validación o falta de datos. El cuestionario debe ser rellenado manualmente desde la aplicación para que el cuestionario sea válido.");
			}
		}
		else
		{
			handle_errors($page,$subido_ok[1], $establecimiento->es_hotel(), $botones, "<strong>Los datos no han sido guardados</strong>. El archivo de datos se ha subido, pero no ha sido posible obtener los datos del cuestionario debido a errores de validación.");
		}
		exit;
	}
	else
	{
		// En caso de que todo haya ido bien, nos queda comprobar que no existan excesos de plazas/habitaciones.
		$aloja_cuest=$subido_ok[1];
		if(isset($aloja_cuest->excesoInfoObj))
		{
			$excesos=$aloja_cuest->excesoInfoObj;
			if($excesos->hayExcesoPlazas || $excesos->hayExcesoPlazasMes || $excesos->hayExcesoHabitaciones)
			{
				if($excesos->hayExcesoPlazas || $excesos->hayExcesoPlazasMes)
					$aloja_cuest->excesoPlazas=99;
				if($excesos->hayExcesoHabitaciones)
					$aloja_cuest->excesoHabitaciones=99;
						
				$dao = new AlojaDao();
				$ok = $dao->actualizar_info_exceso_cuestionario($aloja_cuest);
				if (!$ok)
				{
				    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array("Error durante la grabación de la información de excesos."),array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
					handle_errors($page, new AlojaErrorCollection(array("Error durante la grabación de la información.")), $establecimiento->es_hotel(), $botones, "No ha sido posible cerrar el cuestionario. Error durante la grabación de los datos.");
					exit;
				}

				// Hay avisos de exceso, el cuestionario queda pendiente de cierre.
				handle_avisos($page, $mes_trabajo, $ano_trabajo, $aloja_cuest->val_errors, $excesos->hayExcesoPlazasMes, $excesos->hayExcesoPlazas, $excesos->hayExcesoHabitaciones, $establecimiento->es_hotel(), $botones, "El archivo de datos se ha subido y <strong>los datos se han guardado</strong>, pero no ha sido posible cerrar el cuestionario debido a avisos generados en la validación. Debe justificar el exceso de ocupación o habitaciones para cerrar la encuesta.");
				exit;
			}
		}
	}

	// El cuestionario ya está en BDD.
	
	//Añadir las modificaciones de cabecera en caso de que las hubiera.
	$mod_cabecera = isset($subido_ok[2])? $subido_ok[2] : null;
	if ($mod_cabecera != null)
	{
		$mods = obtener_modificaciones($establecimiento, $mod_cabecera);
		if (count($mods) > 0)
		{
			$fecha_reg = $establecimiento->registrar_modificacion($establecimiento->id_establecimiento, $page->get_current_userid(), $mods);
		}
	}
	
	@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, ENVIA_CUESTIONARIO_ALOJAMIENTO_XML, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
	
	$ok = $aloja_ctl->cerrar_cuestionario($subido_ok[1], $permitir_cerrar_cuestionario_no_vencido);
	if ($ok !== true)
	{
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, FAILED, array($ok),NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
		handle_errors($page, new AlojaErrorCollection(array($ok)), $establecimiento->es_hotel(), $botones, "El archivo de datos se ha subido y los datos se han cargado, pero no ha sido posible cerrar el cuestionario debido a errores de validación.");
		exit;
	}
	else
	{
	    @AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, CIERRA_CUESTIONARIO_ALOJAMIENTO, SUCCESSFUL,NULL,array("año" => $aloja_cuest->ano, "mes" => $aloja_cuest->mes));
		$url_acuse = $page->build_url(PAGE_ALOJA_ACUSE, array('mes_encuesta'=>$aloja_cuest->mes, 'ano_encuesta'=>$aloja_cuest->ano));
		handle_ok($page, $url_acuse, $establecimiento->es_hotel());
	}
}
elseif ($es_admin && $operacion == OP_DESCARGAR_XML)
{
	$xml = $aloja_ctl->descargar_xml($establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);

	if ($xml == null)
	{
	    $detalles=err_detail($operacion,$establecimiento->id_establecimiento, $mes_trabajo, $ano_trabajo);
		$page->abort_with_error(PAGE_ALOJA_XML, "No existe cuestionario para la fecha indicada.".$detalles);
	}
	$nombre_fich = $establecimiento->id_establecimiento . "_" . $ano_trabajo . "_" . $mes_trabajo;
	header("Content-Disposition: attachment; filename=\"$nombre_fich.xml\"");
	header("Content-Type: application/xml");
	echo $xml;
}
else 
{
	/// Mostar el formulario de petición del archivo XML.
	/// 1. Comprobar si se permite subir el xml: solo se permite subir un xml para el mes anterior al actual, ya que si es valido se cerrará
	///	   la encuesta, y solo se pueden cerrar a mes pasado (el mes en curso no ha terminado, y por lo tanto no se permite cerrar).
	
	/// NOTA: Para permitir el envío de cuestionarios a mitad de mes por cese de actividad, relajamos esta condición.

	$vv = array();
	$vv["es_hotel"] = $establecimiento->es_hotel();
	if (isset($hay_datos))
		$vv['hay_datos'] = $hay_datos;
	$vv['mes_trabajo'] = $mes_trabajo;
	$vv['ano_trabajo'] = $ano_trabajo;
	$vv['mes_encuesta_finalizado'] = (date("Ym") != $ano_trabajo.$mes_trabajo);
	/// Render de la pagina
	$page->render("aloja_xml_view.php", $vv );
	
}

$page->end_session();

function obtener_muni_isla_por_nombre($nombre_municipio)
{
	$sql = DbHelper::prepare_sql("SELECT id_municipio, id_isla FROM tb_municipios WHERE UPPER(nombre_municipio)=UPPER(:municipio)",
			array(':municipio' => (string)$nombre_municipio));
	
	$db = new Istac_Sql();
	$db->query($sql);
	
	$id_mun = 999; //Valor que indica un caso especial ( en este caso no se ha encontrado el municipio)
	$id_isla = null;
	
	if ($db->next_record())
	{
		$id_mun = $db->f("id_municipio");
		$id_isla = $db->f("id_isla");
	}

	if ($db->next_record())
	{
		// Caso improbable de que la búsqueda obtenga más de un municipio, ya que el nombre no es clave primaria
		$id_mun = 999;
	}
	
	if ($id_mun == 999)
		$id_isla = null;
	
	return array($id_mun, $id_isla);
}

function obtener_modificaciones($establecimiento, AlojaXmlHeader $aloja_cabecera)
{
	$mods = array();
	
	iff_change( $mods,  'nombre_establecimiento',$aloja_cabecera->nombre_establecimiento ,  $establecimiento->nombre_establecimiento);
	iff_change( $mods, 'num_plazas', $aloja_cabecera->plazas_disponibles_sin_supletorias , $establecimiento->num_plazas);
	iff_change( $mods,  'num_habitaciones', $aloja_cabecera->habitaciones , $establecimiento->num_habitaciones);
	iff_change( $mods, 'direccion',$aloja_cabecera->direccion , $establecimiento->direccion);
	iff_change( $mods, 'localidad',$aloja_cabecera->localidad , $establecimiento->localidad);
	
	iff_change( $mods, 'codigo_postal',$aloja_cabecera->codigo_postal , $establecimiento->codigo_postal);
	iff_change( $mods,  'telefono1',$aloja_cabecera->telefono_1 , $establecimiento->telefono);
	iff_change( $mods,  'telefono2',$aloja_cabecera->telefono_2 , $establecimiento->telefono2);
	iff_change( $mods,  'fax', $aloja_cabecera->fax_1 , $establecimiento->fax);
	iff_change( $mods, 'fax2', $aloja_cabecera->fax_2, $establecimiento->fax2);
	
	iff_change( $mods, 'url',$aloja_cabecera->url, $establecimiento->url);
	iff_change( $mods, 'provincia', $aloja_cabecera->provincia, $establecimiento->provincia);
	iff_change( $mods, 'razon_social',$aloja_cabecera->razon_social , $establecimiento->razon_social);
	iff_change( $mods, 'cif_nif',$aloja_cabecera->cif_nif , $establecimiento->cif_nif);
	
	iff_change( $mods, 'numero_registro',$aloja_cabecera->numero_registro , $establecimiento->num_registro);
	
	$mi = iff_cambio_municipio($aloja_cabecera->municipio, $establecimiento->municipio);
	if ($mi != null)
	{
		$mi = obtener_muni_isla_por_nombre($aloja_cabecera->municipio);
		if ($mi[0] != null) 
			$mods['id_municipio'] = $mi[0];
		if ($mi[1] != null)
			$mods['id_isla'] = $mi[1];
	}
	
	$nuevo_tipo = iff_cambio_tipo($aloja_cabecera->tipo, $establecimiento->texto_tipo_establecimiento);
	if ($nuevo_tipo != null)
	{
		$mods['id_tipo_establecimiento'] = $nuevo_tipo;
	}
	
	$nueva_cat = iff_cambio_categoria($aloja_cabecera->categoria, $establecimiento->id_categoria);
	if ($nueva_cat != null)
	{
		$mods['id_categoria'] = $nueva_cat;
	}

	return $mods;
}


function iff_cambio_municipio($newval, $oldval)
{
	if ($newval != null && strcasecmp(trim($newval), $oldval) != 0)
	{
		return obtener_muni_isla_por_nombre($newval);
	}	
	return null;
}

/**
 * Añade una nueva entrada en la tabla mods a partir del parametro post cno nombre postname en caso de que está definido y su valor no coincida con orvalue.
 * @param unknown_type $page
 * @param unknown_type $mods
 * @param unknown_type $postname
 * @param unknown_type $modname
 * @param unknown_type $orvalue
 */
function iff_change(& $mods, $modname, $newval, $oldval)
{
	if ($newval != null && $newval != $oldval)
		$mods[$modname] = $newval;
}

function iff_cambio_categoria($newval, $oldval)
{
	if (isset($newval))
	{
		$categoria = trim($newval);
	
		if (!empty($categoria))
		{
			$sql = DbHelper::prepare_sql("SELECT id_categoria FROM tb_categorias_xml WHERE UPPER(categoria) =UPPER(:cat)",
					array(':cat'=>(string)$categoria));
	
			$db = new Istac_Sql();
			$db->query($sql);
	
			if ($db->next_record())
			{
				if (strcasecmp($db->f("id_categoria"),$oldval))
				{
					return $db->f("id_categoria");
				}
			}
		}
	}
	return null;
}

function iff_cambio_tipo($newval, $oldval)
{
	if (isset($newval))
	{
		$tipoc = trim($newval);
	
		if (!empty($tipoc))
		{
			$sql = DbHelper::prepare_sql("SELECT c.id_tipo_establecimiento
			FROM(   SELECT a.descripcion,a.id_tipo_establecimiento
			FROM tb_tipo_establecimientos a
			WHERE UPPER(a.descripcion)=UPPER(:tipoc)
			UNION
			SELECT b.descripcion,b.id_tipo_establecimiento
			FROM tb_tipo_establecimientos_xml b
			WHERE UPPER(b.descripcion)=UPPER(:tipoc)
			) c", array(':tipoc'=> (string)$tipoc));
			
			$db = new Istac_Sql();
			$db->query($sql);
	
			if (!$db->next_record())
			{
				return substr($tipoc,0,20);
			}
			else
			{
				if ((int)$db->f("id_tipo_establecimiento")!=(int)($oldval))
				{
					return $db->f("id_tipo_establecimiento");
				}
			}
		}
	}
	return null;
}

function obtenerMotivos($es_hotel)
{
	$sql = "SELECT ID_MOTIVO idmot,DESCRIPCION_MOTIVO descmot,DETALLE_OBLIGATORIO oblig,AYUDA_DETALLE ayuda FROM TB_ALOJA_EXCESO_MOTIVOS WHERE ACTIVADO='S' AND ES_HOTEL='".($es_hotel ? "S":"N")."' ORDER BY ORDEN";

	$db = new Istac_Sql();
	$db->query($sql);
	return new RowDbIterator($db, array('idmot', 'descmot', 'oblig', 'ayuda'));
}

function obtenerLimitesInvitaciones()
{
	$sql = "SELECT ID_TIPO_CLIENTE idcliente, LIMITE_PERCT perct FROM TB_ALOJA_PERCT_INVITACIONES WHERE ACTIVADO='S'";

	$db = new Istac_Sql();
	$db->query($sql);
	return new RowDbIterator($db, array('idcliente', 'perct'));
}

?>