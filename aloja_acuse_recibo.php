<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/lib/ext/PDF.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");
require_once(__DIR__."/classes/aloja/AlojaDao.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION), array(PAGE_ALOJA_ACUSE));

/// Parametros de la pagina
// ARG_MES_ENCUESTA: Mes de encuesta para la que hay que cargar los datos de la selección de países
define("ARG_MES_ENCUESTA", "mes_encuesta");
// ARG_ANYO_ENCUESTA: Año de encuesta para la que hay que cargar los datos de la selección de países
define("ARG_ANO_ENCUESTA", "ano_encuesta");

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


//Se recogen los parámetros para cargar la encuesta
$mes_encuesta = $page->request_post_or_get(ARG_MES_ENCUESTA, NULL);
$ano_encuesta = $page->request_post_or_get(ARG_ANO_ENCUESTA, NULL);

$establecimiento = $page->get_current_establecimiento();

/// 1. Obtener el establecimiento y mes/año con el que se va a trabajar
if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC)))
{
	if(($establecimiento == null) || ($mes_encuesta==NULL) || ($ano_encuesta==NULL))
	{
		list($selected_estid,$mes_encuesta, $ano_encuesta) = $page->select_establecimiento_mes_ano($page->self_url(NULL, TRUE));
		$page->set_current_establecimiento($selected_estid);
		$establecimiento = $page->get_current_establecimiento();
	}
}

if ($establecimiento == null)
{
	$page->abort_with_error(PAGE_ALOJA_INDEX, "No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

if($page->have_any_perm(array(PERM_USER,PERM_RECEPCION)))
{
	/// 1b. Los usuarios no pueden rellenar una encuesta si el establecimiento está dado de baja.
	$page->abort_si_estado_baja($establecimiento);
}

$dao = new AlojaDao();
$enc =  $dao->cargar_registro_cuestionario($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
if ($enc == null)
{
    $detalles=err_detail($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
    $page->abort_with_error(PAGE_HOME, "No existe cuestionario para el establecimiento, el mes y el año especificados.".$detalles);
}

// NO SE PUEDE IMPRIMIR ACUSE SI EL CUESTIONARIO NO ESTA CERRADO
if (!$enc->esta_cerrado())
{
    $detalles=err_detail($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
	$page->abort_with_error(PAGE_HOME, "El cuestionario para el establecimiento, el mes y el año especificados no está cerrado.".$detalles);
}


//Creación del archivo pdf de acuse.

$fechas = new DateHelper();
$id_cuestionario = $enc->id;
$nombre_hotel = $establecimiento->nombre_establecimiento;
$mes =  DateHelper::mes_tostring($mes_encuesta);
$ano = $ano_encuesta;

$nombre_fichero="alojamiento_istac_".$establecimiento->id_establecimiento."_".$ano_encuesta."_".$mes_encuesta.".pdf";

header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-type: application/pdf');
Header("Content-Disposition", "inline;filename=".$nombre_fichero);

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Cell(0,10,$fechas->dia_sistema."-".$fechas->mes_sistema."-".$fechas->anyo_sistema,0,1,'R');
$pdf->Cell(0,10,"Ref: $id_cuestionario",0,1,'R');
$pdf->WriteHTML("<br><br><br><br><br><b><u><i>Encuesta de Alojamiento Turístico</i></u></b>");
$pdf->SetFont('Arial','',12);
$pdf->WriteHTML("<br><br>El Instituto Canario de Estadística ha recibido correctamente la información de la encuesta correspondiente al mes de ". $mes ." de ".$ano." del establecimiento ".$nombre_hotel." en sus sistemas de bases de datos.");
$pdf->SetFont('Arial','B',12);
$pdf->WriteHTML("<br><br><b>Muchas gracias por su colaboración.</b>");

$pdf->SetFont('Arial','',7);

$pdf->WriteHTML("<br><br><br><br><br><br><br><br><br><br><br><br><br><br>Dirección Las Palmas: Luis Doreste Silva, 101 - Planta 7ª. 35004 Las Palmas de Gran Canaria");
$pdf->WriteHTML("<br>Dirección Santa Cruz de Tenerife: Rambla de Santa Cruz, nº 149. Planta baja. 38001 Santa Cruz de Tenerife");

$pdf->Output($nombre_fichero,"I");

$page->end_session();
?>