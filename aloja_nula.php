<?php /// Controlador del formulario de envío de solicitudes de envío de cuestionarios de alojamiento vacíos.
/**
 La clase que filtra la entrada para evitar el XSS funciona mal y se cuelga cuando se introduce una combinación de caracteres especiales.
 La solución más sencilla es evitar el filtrado en esta página en concreto usando la constante NO_FILTER_XSS.
 
 Un ejemplo de entrada para reproducir el problema:

Prueba de envío: ñÑçÇáéíóúÁÉÍÓÚàèìòùÀÈÌÒÙ¿?~^âêîôûÂÊÎÔÛäëïöüÄËÏÖÜãõÃÕ€%&'
Línea #1.
HTML entities: &oacute;
<>
<
>
Línea #2.
fin
*/
define('NO_FILTER_XSS', true);

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/lib/email.class.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/lib/DbHelper.class.php");
require_once(__DIR__."/lib/RowDbIterator.class.php");
require_once(__DIR__."/classes/aloja/AlojaDao.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_ALOJA_NULA));

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

if($es_admin)
{
    /// Parametros de la pagina
    // ARG_MES_ENCUESTA: Mes de encuesta para la que hay que cargar los datos de la selección de países
    define("ARG_MES_ENCUESTA", "mes_encuesta");
    // ARG_ANYO_ENCUESTA: Año de encuesta para la que hay que cargar los datos de la selección de países
    define("ARG_ANO_ENCUESTA", "ano_encuesta");
    
    
    //Se recogen los parámetros para cargar la encuesta
    $mes_encuesta = $page->request_post_or_get(ARG_MES_ENCUESTA, NULL);
    $ano_encuesta = $page->request_post_or_get(ARG_ANO_ENCUESTA, NULL);   
}

define("OP_ENVIAR_SOLICITUD", "es");
define("ARG_DETALLE","motivo_detalle");
define("ARG_MOTIVO", "motivo_sel");

$motivos=obtenerMotivos();

if ($page->request_post_or_get(ARG_OP) == OP_ENVIAR_SOLICITUD)
{
	$res=false;
	$errorop="";
	
	// (1) Creamos el cuestionario vacío en la BDD (sólo en el caso de 'No hay ocupación'). Comprobar que se genera correctamente el pdf de acuse de recibo.
	// (2) Registramos la operación.
	// (3) Enviamos correo de solicitud si no es el administrador.
	foreach ($motivos as $row)
	{
		if($row['idmot']==$_POST[ARG_MOTIVO])
		{
			// Creamos el cuestionario vacío en la BDD (sólo en el caso de 'No hay ocupación'). Comprobar que se genera correctamente el pdf de acuse de recibo.
			$establecimiento = $page->get_current_establecimiento();
			$estUser = $page->load_current_user_data();
			
			$aloja_ctl = new AlojaController();
			if($es_admin)
			{
			    // Los administradores aporta la fecha de la encuesta para la que se quiere crear el cuestionario vacío.
			    $aloja_enc = $aloja_ctl->crear_nuevo_cuestionario_vacio($establecimiento, $page->get_current_userid(), $mes_encuesta, $ano_encuesta);
			}
			else 
			{
			    // Los usuarios normales, no aportan fecha y el cuestionario vacío a crear es asignado a la encuesta actual.
			    $aloja_enc = $aloja_ctl->crear_nuevo_cuestionario_vacio($establecimiento, $page->get_current_userid());
			}
			if ($aloja_enc == false)
			{
			    $res=false;
			    $errorop=($es_admin) ? "Ya existe un cuestionario. Verifique los datos.":"Ya existe un cuestionario. Contacte con el personal del ISTAC para obtener ayuda.";
			    break;
			}
			$msg_log="(Motivo: ".$row['descmot'].")";
			if(isset($_POST[ARG_DETALLE]))
			    $msg_log=$msg_log." ".$_POST[ARG_DETALLE];
			    
		    if($row['crear']=='S')
		    {
		        //Se guarda el cuestionario en bbdd de datos según los presentes a fin de mes anterior
		        $aloja_ctl->inicializar_cuestionario_pres_esp($establecimiento->id_establecimiento, $aloja_enc); /* ¿necesario? */
		        
		        $fecha_cierre = new DateTime('now');
		        
		        $dao = new AlojaDao();
		        if((!$dao->guardar_cuestionario($aloja_enc)) || (!$dao->cerrar_cuestionario($aloja_enc->id, $fecha_cierre)))
		        {
		            @AuditLog::log($estUser->id, $aloja_enc->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, FAILED, array($msg_log),array("año" => $aloja_enc->ano, "mes" => $aloja_enc->mes));
		            $res=false;
		            $errorop="Error interno.";
		            break;
		        }
		        else
		        {
		            @AuditLog::log($estUser->id, $aloja_enc->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, SUCCESSFUL, array($msg_log),array("año" => $aloja_enc->ano, "mes" => $aloja_enc->mes));
		        }
		    }
		    else
		    {
		        @AuditLog::log($estUser->id, $aloja_enc->estabid_declarado, ENVIA_CUESTIONARIO_ALOJAMIENTO_MANUAL, SUCCESSFUL, array($msg_log),array("año" => $aloja_enc->ano, "mes" => $aloja_enc->mes));
		    }
			
			// Enviamos correo de solicitud.
			$email = new Email();
			$asunto = "PWET:"." (".$establecimiento->id_establecimiento.")";
			$texto="El establecimiento " .$estUser->username." (".$establecimiento->id_establecimiento.") ha enviado una solicitud de creación de cuestionario vacío.\r\nMotivo: ".$row['descmot'].".\r\n";
			if(isset($_POST[ARG_DETALLE]))
			    $texto=$texto."Detalle: ".$_POST[ARG_DETALLE];
			if($row['crear']=='S')
				$texto=$texto."\nSe ha creado un nuevo cuestionario vacío en BDD.";
			$email->send($asunto, nl2br(htmlentities(iconv("ISO-8859-1", "UTF-8", $texto))));
			
			$res=true;
			break;
		}
	}
	$motivos->rewind();
}

/// Preparacion de la vista de la pagina
$view_vars['es_admin']=$es_admin;
$view_vars['motivos']=$motivos;
if(isset($res))
	$view_vars['res']=$res;
if(isset($errorop))
	$view_vars['errorop']=$errorop;
if(isset($mes_encuesta))
    $view_vars['mes_encuesta']=$mes_encuesta;
if(isset($ano_encuesta))
    $view_vars['ano_encuesta']=$ano_encuesta;

$page->render("aloja_nula_view.php", $view_vars);
$page->end_session();

function obtenerMotivos()
{
	$sql = "SELECT ID_MOTIVO idmot,DESCRIPCION_MOTIVO descmot,DETALLE_OBLIGATORIO oblig,AYUDA_DETALLE ayuda,CREAR_CUESTIONARIO crear FROM TB_ALOJA_CUEST_NULOS_MOTIVOS WHERE ACTIVADO='S' ORDER BY ORDEN";

	$db = new Istac_Sql();
	$db->query($sql);
	return new RowDbIterator($db, array('idmot', 'descmot', 'oblig', 'ayuda', 'crear'));
}
?>