<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/FacturaController.class.php");

//$page = PWETPageHelper::start_page(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION), array(PAGE_ALOJA_INDEX, PAGE_ALOJA_FORM));
$page = PWETPageHelper::start_page(PERMS_ANY);


define('ARG_NUMERO_FACTURA', 'numero_factura');
$numero_factura = $page->request_post_or_get(ARG_NUMERO_FACTURA, NULL);

define('LOCAL_VAR_FACTURA_TIPO_SUMINISTRO','0000_1001');

//// TODO: Gestionar correctamente el error ==> hay que identificar si el error se ha producido en un POST o en el GET inicial.

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

if($es_admin)
{
    $selected_estid = $page->request_post_or_get(ARG_ESTID, NULL);
    if($selected_estid==NULL)
    {
        $establecimiento = $page->get_current_establecimiento();
        if($establecimiento == null)
        {
            /// Mostrar las pagina de busqueda de establecimiento.
            $selected_estid = $page->select_establecimiento($page->self_url(NULL, TRUE));
            $page->set_current_establecimiento($selected_estid);
        }
    }
    else
    {
        $page->set_current_establecimiento($selected_estid);
    }
}

$establecimiento = $page->get_current_establecimiento();

if ($establecimiento == null)
{
    //// TODO: Cambiar esta dirección de error.
    gestionarError("No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

if ($numero_factura == null)
{
    //// TODO: Cambiar esta dirección de error.
    gestionarError("No se ha definido el número de factura.");
}

$factura_ctl = new FacturaController();

$factura=$factura_ctl->cargar_factura($numero_factura,$establecimiento->id_establecimiento);
if($factura==null)
{
    // Factura no encontrada.
    gestionarError("Factura Nº ".$numero_factura." no encontrada.");
}

$res=false;

if(!$factura->esta_cerrado())
{
    gestionarError("Factura Nº ".$numero_factura." no cerrada.");
}

if($page->is_get()==false)
{
    gestionarError("Error en la entrada de datos. Petición incorrecta.");
}


$tipo_factura=$factura->tipo;

switch ($tipo_factura)
{
    case 'ELECTRICIDAD':
        facturaElectrica();
        break;
    case 'AGUA':
        facturaAgua();
        break;
    case 'PROPANO':
        facturaPropano();
        break;
    case 'BUTANO':
        facturaButano();
        break;
    case 'GASOIL':
        facturaGasoil();
        break;
    default:
        gestionarError("Error en la entrada de datos. Tipo de factura no reconocida.");
        break;
}

function str2Float($valor)
{
    return floatval(str_replace(',','.',$valor));
}

function facturaElectrica()
{
    global $page,$es_admin,$factura,$factura_ctl;
    
    define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO','1111.1000.1001');
    define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL','1111.1000.1002');
    define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO','1111.1000.1010');
    define('FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE','1111.1000.0100.1001');
    define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA','1111.5000.1002');
    define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO','1111.5000.1003');
    define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS','1111.5000.1004');
    define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO','1111.5000.2003');
    define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO','1111.5000.2004');
    
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_POTENCIA','1111.6000.1001.1001.1001.0001.1001');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_DIAS','1111.6000.1001.1001.1001.0001.1002');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_PRECIO','1111.6000.1001.1001.1001.0001.1003');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_IMPORTE','1111.6000.1001.1001.1001.0001.1004');
    
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_POTENCIA','1111.6000.1001.1001.1001.0002.1001');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_DIAS','1111.6000.1001.1001.1001.0002.1002');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_PRECIO','1111.6000.1001.1001.1001.0002.1003');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_IMPORTE','1111.6000.1001.1001.1001.0002.1004');
    
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_POTENCIA','1111.6000.1001.1001.1001.0003.1001');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_DIAS','1111.6000.1001.1001.1001.0003.1002');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_PRECIO','1111.6000.1001.1001.1001.0003.1003');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_IMPORTE','1111.6000.1001.1001.1001.0003.1004');
    
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_POTENCIA','1111.6000.1001.1001.1001.0004.1001');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_DIAS','1111.6000.1001.1001.1001.0004.1002');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_PRECIO','1111.6000.1001.1001.1001.0004.1003');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_IMPORTE','1111.6000.1001.1001.1001.0004.1004');
    
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_POTENCIA','1111.6000.1001.1001.1001.0005.1001');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_DIAS','1111.6000.1001.1001.1001.0005.1002');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_PRECIO','1111.6000.1001.1001.1001.0005.1003');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_IMPORTE','1111.6000.1001.1001.1001.0005.1004');
    
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_POTENCIA','1111.6000.1001.1001.1001.0006.1001');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_DIAS','1111.6000.1001.1001.1001.0006.1002');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_PRECIO','1111.6000.1001.1001.1001.0006.1003');
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_IMPORTE','1111.6000.1001.1001.1001.0006.1004');
    
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_ENERGIA','1111.6000.1001.1002.1001.0001.1001');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_PRECIO','1111.6000.1001.1002.1001.0001.1002');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_IMPORTE','1111.6000.1001.1002.1001.0001.1003');
    
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_ENERGIA','1111.6000.1001.1002.1001.0002.1001');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_PRECIO','1111.6000.1001.1002.1001.0002.1002');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_IMPORTE','1111.6000.1001.1002.1001.0002.1003');
    
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_ENERGIA','1111.6000.1001.1002.1001.0003.1001');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_PRECIO','1111.6000.1001.1002.1001.0003.1002');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_IMPORTE','1111.6000.1001.1002.1001.0003.1003');
    
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_ENERGIA','1111.6000.1001.1002.1001.0004.1001');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_PRECIO','1111.6000.1001.1002.1001.0004.1002');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_IMPORTE','1111.6000.1001.1002.1001.0004.1003');
    
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_ENERGIA','1111.6000.1001.1002.1001.0005.1001');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_PRECIO','1111.6000.1001.1002.1001.0005.1002');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_IMPORTE','1111.6000.1001.1002.1001.0005.1003');
    
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_ENERGIA','1111.6000.1001.1002.1001.0006.1001');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_PRECIO','1111.6000.1001.1002.1001.0006.1002');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_IMPORTE','1111.6000.1001.1002.1001.0006.1003');
    
    define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL','1111.6000.1001.1001.1001.1002');
    define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL','1111.6000.1001.1002.1001.1003');
    
    define('FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA','1111.3000.1001');
    define('FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS','1111.3000.1002');
    define('FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR','1111.3000.1100');
    
    
    /*
        '1' => __DIR__."/consumo_form_part_electricidad_datosgenerales.php",
        '2' => __DIR__."/consumo_form_part_electricidad_datossuministro.php",
        '3' => __DIR__."/consumo_form_part_electricidad_datosconsumo.php",
        '4' => __DIR__."/consumo_form_part_electricidad_datosconsumo2.php",
        '5' => __DIR__."/consumo_form_part_electricidad_resumen.php"
     */
    
    $variablesView=array();
    $variablesView['suministradoras']=$factura_ctl->listarSuministradoras();
    $variablesView['suministradora']=$factura->suministradora->nombre;
    $variablesView['NumeroFactura']=$factura->num_factura;
    //$variablesView['ReferenciaContrato']=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO]->valor;
    $variablesView['FechaInicioPeriodo']=$factura->fecha_inicio->format('d/m/Y');
    if($factura->fecha_final!=null)
    {
        $variablesView['FechaFinalPeriodo']=$factura->fecha_final->format('d/m/Y');
    }
    
    $comercializadoras=$factura_ctl->leerListaOpciones('ELECTRICIDAD_COMERCIALIZADORAS');
    $comercializadoras[$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE]->valor]->seleccionado=true;
    $variablesView['comercializadoras']=$comercializadoras;
    
    /*
    $distribuidoras=$factura_ctl->leerListaOpciones('ELECTRICIDAD_DISTRIBUIDORAS');
    $distribuidoras[$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA]->valor]->seleccionado=true;
    $variablesView['distribuidoras']=$distribuidoras;
    */
    
    $peajes=$factura_ctl->leerListaOpciones('ELECTRICIDAD_PEAJE_ACCESO');
    $peajes[$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]->valor]->seleccionado=true;
    $variablesView['peajes']=$peajes;
    
    //$variablesView['NumeroContrato']=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO]->valor;
    $variablesView['Cups']=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS]->valor;
    //$variablesView['Vencimiento']=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO]->valor->format('d/m/Y');
    
    $numeroPeriodos=0;
    $variablesView['Peaje']=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]->valor;
    switch($variablesView['Peaje'])
    {
        // 6 periodos
        case '6.1A':
        case '6.1B':
        case '6.2':
        case '6.3':
        case '6.4':
        case '6.5':
            $variablesView['NumeroPeriodos']=6;
            $numeroPeriodos=6;
            break;
            // 3 periodos
        case '2.0DHS':
        case '2.1DHS':
        case '3.0A':
        case '3.1A':
            $variablesView['NumeroPeriodos']=3;
            $numeroPeriodos=3;
            break;
            // 2 periodos
        case '2.0DHA':
        case '2.1DHA':
            $variablesView['NumeroPeriodos']=2;
            $numeroPeriodos=2;
            break;
            // Sin discriminación horaria (1 periodo).
        case '2.0A':
        case '2.1A':
            $variablesView['NumeroPeriodos']=1;
            $numeroPeriodos=1;
            break;
    }
    $variablesView['NumeroPeriodos']=$numeroPeriodos;
    
    if($numeroPeriodos>=1)
    {
        $variablesView['P1_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_POTENCIA]->valor;
        //$variablesView['P1_dias']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_DIAS]->valor;
        //$variablesView['P1_precio_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_PRECIO]->valor;
        //$variablesView['P1_importe_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_IMPORTE]->valor;
        $variablesView['P1_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_ENERGIA]->valor;
        //$variablesView['P1_precio_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_PRECIO]->valor;
        //$variablesView['P1_importe_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_IMPORTE]->valor;
    }
    if($numeroPeriodos>=2)
    {
        $variablesView['P2_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_POTENCIA]->valor;
        //$variablesView['P2_dias']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_DIAS]->valor;
        //$variablesView['P2_precio_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_PRECIO]->valor;
        //$variablesView['P2_importe_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_IMPORTE]->valor;
        $variablesView['P2_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_ENERGIA]->valor;
        //$variablesView['P2_precio_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_PRECIO]->valor;
        //$variablesView['P2_importe_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_IMPORTE]->valor;
    }
    if($numeroPeriodos>=3)
    {
        $variablesView['P3_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_POTENCIA]->valor;
        //$variablesView['P3_dias']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_DIAS]->valor;
        //$variablesView['P3_precio_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_PRECIO]->valor;
        //$variablesView['P3_importe_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_IMPORTE]->valor;
        $variablesView['P3_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_ENERGIA]->valor;
        //$variablesView['P3_precio_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_PRECIO]->valor;
        //$variablesView['P3_importe_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_IMPORTE]->valor;
        
    }
    if($numeroPeriodos>=4)
    {
        $variablesView['P4_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_POTENCIA]->valor;
        //$variablesView['P4_dias']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_DIAS]->valor;
        //$variablesView['P4_precio_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_PRECIO]->valor;
        //$variablesView['P4_importe_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_IMPORTE]->valor;
        $variablesView['P4_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_ENERGIA]->valor;
        //$variablesView['P4_precio_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_PRECIO]->valor;
        //$variablesView['P4_importe_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_IMPORTE]->valor;
    }
    if($numeroPeriodos>=5)
    {
        $variablesView['P5_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_POTENCIA]->valor;
        //$variablesView['P5_dias']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_DIAS]->valor;
        //$variablesView['P5_precio_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_PRECIO]->valor;
        //$variablesView['P5_importe_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_IMPORTE]->valor;
        $variablesView['P5_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_ENERGIA]->valor;
        //$variablesView['P5_precio_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_PRECIO]->valor;
        //$variablesView['P5_importe_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_IMPORTE]->valor;
    }
    if($numeroPeriodos>=6)
    {
        $variablesView['P6_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_POTENCIA]->valor;
        //$variablesView['P6_dias']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_DIAS]->valor;
        //$variablesView['P6_precio_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_PRECIO]->valor;
        //$variablesView['P6_importe_potencia']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_IMPORTE]->valor;
        $variablesView['P6_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_ENERGIA]->valor;
        //$variablesView['P6_precio_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_PRECIO]->valor;
        //$variablesView['P6_importe_energia']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_IMPORTE]->valor;
    }

    $variablesView['TotalImportePotenciaFacturada']=$factura->detalles[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL]->valor;
    $variablesView['TotalImporteEnergiaFacturada']=$factura->detalles[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL]->valor;
    
    //$variablesView['TotalImporteEnergia']=$factura->detalles[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA]->valor;
    //$variablesView['TotalImporteServicios']=$factura->detalles[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS]->valor;
    $variablesView['TotalPagar']=$factura->detalles[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR]->valor;
    
    $variablesView['VolverUrl']=($es_admin)?PAGE_HOME:PAGE_CONSUMO_INDEX;
    
    $iconoSuministro=$factura_ctl->leerOpcion('ICONOS_SUMINISTRO',$factura->tipo);
    $variablesView['iconoSuministro']=(array_key_exists($factura->tipo,$iconoSuministro))?$iconoSuministro[$factura->tipo]:null;       /// Icono del tipo de suministro
    
    $page->render( "consumo_print_ELECTRICIDAD_view.php", $variablesView);
    
    $page->end_session();
    exit(0);
}

function facturaAgua()
{
    global $page,$es_admin,$factura,$factura_ctl;
    
    define ('FACTURA_EMPRESA_SUMINISTRADORA','0000.1000');
    define ('FACTURA_TIPO_SUMINISTRO','0000.1001');
    define ('FACTURA_NUMERO_FACTURA','0000.1002');
    define('FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO','2222.1000.1001');
    define('FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL','2222.1000.1002');
    define('FACTURA_AGUA_DATOS_GENERALES_NUMERO_FACTURA','2222.1000.1003');
    define('FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO','2222.1000.1010');
    define('FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA','2222.5000.1002');
    //define('FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA','2222.5000.1003');
    define('FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR','2222.5000.1004');
    //define('FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE','2222.5000.1014');
    //define('FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR','2222.5000.1024');
    define('FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR','2222.6000.1001.1001');
    define('FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL','2222.6000.1001.1002');
    define('FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL','2222.1000.1006');
    define('FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA','2222.1000.1007');
    define('FACTURA_AGUA_DATOS_CONSUMO_M3_CALCULADOS','2222.6000.1001.1004');           // 'Consumo calculado','Consumo calculado a partir de las lecturas (en m3).'
    define('FACTURA_AGUA_DATOS_CONSUMO_CONTADOR_REBASADO','2222.6000.1001.1005');       // 'Contador rebasado','El contador de consumo del aparato de medida ha sido rebasado.'
    //define('FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA','2222.5000.2001');
    //define('FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA','2222.5000.2002');
    //define('FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD','2222.5000.2003');
    /*define('FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA','2222.5000.1002');
    define('FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA','2222.5000.1003');
    define('FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR','2222.5000.1004');
    define('FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE','2222.5000.1014');
    define('FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR','2222.5000.1024');
    define('FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR','2222.6000.1001.1001');
    define('FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL','2222.6000.1001.1002');
    define('FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL','2222.1000.1006');
    define('FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA','2222.1000.1007');
    define('FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA','2222.5000.2001');
    define('FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA','2222.5000.2002');
    define('FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD','2222.5000.2003');*/
    
    // variables de entrada del formulario
    define('VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA','2222_5000_1002');
    //define('VAR_FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA','2222_5000_1003');
    define('VAR_FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR','2222_5000_1004');
    //define('VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE','2222_5000_1014');
    //define('VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR','2222_5000_1024');
    define('VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR','2222_6000_1001_1001');
    define('VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL','2222_6000_1001_1002');
    define('VAR_FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL','2222_1000_1006');
    define('VAR_FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA','2222_1000_1007');
    //define('VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA','2222_5000_2001');
    //define('VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA','2222_5000_2002');
    //define('VAR_FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD','2222_5000_2003');
    define('VAR_TIPO_LECTURA_CONTADOR','tipoLectura');
    
    define('FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES','2222.6000.1001.1000.');
    //define('FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES','2222.6000.1002.1000.');
    //define('FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES','2222.6000.1003.1000.');
    define('ID_INDICE_PRIMER_BLOQUE',1001);
    define('ID_BLOQUE_LIMITE','.1001');
    define('ID_BLOQUE_PRECIO','.1002');
    define('ID_BLOQUE_CONSUMO_REALIZADO','.1003');
    define('ID_BLOQUE_IMPORTE','.1004');
    define('FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE','2222.7000.1000.1001');
    define('FACTURA_AGUA_RESUMEN_TOTAL_PAGAR','2222.6000.1000.1002');
    
    define('VAR_FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES','2222_6000_1001_1000_');
    //define('VAR_FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES','2222_6000_1002_1000_');
    //define('VAR_FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES','2222_6000_1003_1000_');
    define('VAR_ID_BLOQUE_LIMITE','_1001');
    
    //define('VAR_ID_BLOQUE_PRECIO','_1002');
    //define('VAR_ID_BLOQUE_CONSUMO_REALIZADO','_1003');
    //define('VAR_ID_BLOQUE_IMPORTE','_1004');
    
    $variablesView=array();
    $variablesView['suministradoras']=$factura_ctl->listarSuministradoras();
    $variablesView['suministradora']=$factura->suministradora->nombre;
    $variablesView['NumeroFactura']=$factura->num_factura;
    $variablesView['ReferenciaContrato']=$factura->detalles[FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO]->valor;
    $variablesView['FechaInicioPeriodo']=$factura->fecha_inicio->format('d/m/Y');
    if($factura->fecha_final!=null)
    {
        $variablesView['FechaFinalPeriodo']=$factura->fecha_final->format('d/m/Y');
    }
    
    $distribuidoras=$factura_ctl->leerListaOpciones('AGUA_DISTRIBUIDORAS');
    //$calibres=$factura_ctl->leerListaOpciones('AGUA_CALIBRES');
    //$tiposSuministro=$factura_ctl->leerListaOpciones('AGUA_TIPOS_SUMINISTRO');
    //$actividades=$factura_ctl->leerListaOpciones('AGUA_ACTIVIDADES');
    //$tarifas=$factura_ctl->leerListaOpciones('AGUA_TARIFAS');
    //$tiposContador=$factura_ctl->leerListaOpciones('AGUA_TIPO_CONTADOR');
    
    if(array_key_exists(FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA,$factura))
    {
        $distribuidoras[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA]->valor]->seleccionado=true;
    }
    //$calibres[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE]->valor]->seleccionado=true;
    //$tiposSuministro[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA]->valor]->seleccionado=true;
    //$actividades[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD]->valor]->seleccionado=true;
    //$tarifas[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA]->valor]->seleccionado=true;
    //$tiposContador[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR]->valor]->seleccionado=true;
    
    $variablesView['distribuidoras']=$distribuidoras;
    //$variablesView['calibres']=$calibres;
    //$variablesView['tiposSuministros']=$tiposSuministro;
    //$variablesView['actividades']=$actividades;
    //$variablesView['tarifas']=$tarifas;
    //$variablesView['tiposContador']=$tiposContador;
    
    if(array_key_exists(FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA,$factura))
    {
        $variablesView['distribuidoras_sel']=$distribuidoras[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA]->valor]->desc_corta;
    }
    //$variablesView['calibres_sel']=$calibres[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE]->valor]->desc_corta;
    //$variablesView['tiposSuministros_sel']=$tiposSuministro[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA]->valor]->desc_corta;
    //$variablesView['actividades_sel']=$actividades[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD]->valor]->desc_corta;
    //$variablesView['tarifas_sel']=$tarifas[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA]->valor]->desc_corta;
    //$variablesView['tiposContador_sel']=$tiposContador[$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR]->valor]->desc_corta;
    
    
    
    $lecturaActual=str2Float($factura->detalles[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL]->valor);
    $lecturaAnterior=str2Float($factura->detalles[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR]->valor);
    $M3Consumidos=str2Float($factura->detalles[FACTURA_AGUA_DATOS_CONSUMO_M3_CALCULADOS]->valor);
    //$M3Consumidos=($lecturaActual - $lecturaAnterior);
    

    //$variablesView['NumeroContrato']=$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA]->valor;
    $variablesView['NumeroContador']=$factura->detalles[FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR]->valor;
    
    $variablesView['esLecturaReal']=(array_key_exists(FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL,$factura->detalles));
    $variablesView['esLecturaEstimada']=(array_key_exists(FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA,$factura->detalles));
    $variablesView['LecturaAnterior']=$lecturaAnterior;
    $variablesView['LecturaActual']=$lecturaActual;
    $variablesView['M3Consumidos']=$M3Consumidos;
    
    $nTramosConsumo=-1;
    for($i=0;$i<4;$i++)
    {
        $prefijo=FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$i);
        
        // Datos de la facturación del consumo de agua
        /*
         * Los bloques correspondientes al consumo de agua se forman así (con $indice siendo 0,1,2,3...):
         *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_LIMITE
         *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_PRECIO
         *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_CONSUMO_REALIZADO
         *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_IMPORTE
         */
        if(array_key_exists($prefijo.ID_BLOQUE_LIMITE,$factura->detalles))
        {
            $nTramosConsumo=$i;
            $variablesView['T'.$i.'_consumo_limite']=@$factura->detalles[$prefijo.ID_BLOQUE_LIMITE]->valor;
        }
        if(array_key_exists($prefijo.ID_BLOQUE_PRECIO,$factura->detalles))
        {
            $nTramosConsumo=$i;
            $variablesView['T'.$i.'_consumo_precio']=@$factura->detalles[$prefijo.ID_BLOQUE_PRECIO]->valor;
        }
        if(array_key_exists($prefijo.ID_BLOQUE_CONSUMO_REALIZADO,$factura->detalles))
        {
            $nTramosConsumo=$i;
            $variablesView['T'.$i.'_consumo_realizado']=@$factura->detalles[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]->valor;
        }
        if(array_key_exists($prefijo.ID_BLOQUE_IMPORTE,$factura->detalles))
        {
            $nTramosConsumo=$i;
            $variablesView['T'.$i.'_consumo_importe']=@$factura->detalles[$prefijo.ID_BLOQUE_IMPORTE]->valor;
        }
    }
    
//     $nTramosSaneamiento=-1;
//     for($i=0;$i<4;$i++)
//     {
//         $prefijo=FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$i);
        
//         // Datos de la facturación del saneamiento de agua
//         /*
//          * Los bloques correspondientes al saneamiento de agua se forman así (con $indice siendo 0,1,2,3...):
//          *      FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_LIMITE
//          *      FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_PRECIO
//          *      FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_CONSUMO_REALIZADO
//          *      FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_IMPORTE
//          */
//         if(array_key_exists($prefijo.ID_BLOQUE_LIMITE,$factura->detalles))
//         {
//             $nTramosSaneamiento=$i;
//             $variablesView['T'.$i.'_saneamiento_limite']=@$factura->detalles[$prefijo.ID_BLOQUE_LIMITE]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_PRECIO,$factura->detalles))
//         {
//             $nTramosSaneamiento=$i;
//             $variablesView['T'.$i.'_saneamiento_precio']=@$factura->detalles[$prefijo.ID_BLOQUE_PRECIO]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_CONSUMO_REALIZADO,$factura->detalles))
//         {
//             $nTramosSaneamiento=$i;
//             $variablesView['T'.$i.'_saneamiento_realizado']=@$factura->detalles[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_IMPORTE,$factura->detalles))
//         {
//             $nTramosSaneamiento=$i;
//             $variablesView['T'.$i.'_saneamiento_importe']=@$factura->detalles[$prefijo.ID_BLOQUE_IMPORTE]->valor;
//         }
//     }
    
//     $nTramosDepuracion=-1;
//     for($i=0;$i<4;$i++)
//     {
//         $prefijo=FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$i);
        
//         // Datos de la facturación de la depuración del agua
//         /*
//          * Los bloques correspondientes a la depuración de agua se forman así (con $indice siendo 0,1,2,3...):
//          *      FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_LIMITE
//          *      FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_PRECIO
//          *      FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_CONSUMO_REALIZADO
//          *      FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_IMPORTE
//          */
//         if(array_key_exists($prefijo.ID_BLOQUE_LIMITE,$factura->detalles))
//         {
//             $nTramosDepuracion=$i;
//             $variablesView['T'.$i.'_depuracion_limite']=@$factura->detalles[$prefijo.ID_BLOQUE_LIMITE]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_PRECIO,$factura->detalles))
//         {
//             $nTramosDepuracion=$i;
//             $variablesView['T'.$i.'_depuracion_precio']=@$factura->detalles[$prefijo.ID_BLOQUE_PRECIO]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_CONSUMO_REALIZADO,$factura->detalles))
//         {
//             $nTramosDepuracion=$i;
//             $variablesView['T'.$i.'_depuracion_realizado']=@$factura->detalles[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_IMPORTE,$factura->detalles))
//         {
//             $nTramosDepuracion=$i;
//             $variablesView['T'.$i.'_depuracion_importe']=@$factura->detalles[$prefijo.ID_BLOQUE_IMPORTE]->valor;
//         }
//     }
    
    $variablesView['nTramosConsumo']=$nTramosConsumo;
    //$variablesView['nTramosSaneamiento']=$nTramosSaneamiento;
    //$variablesView['nTramosDepuracion']=$nTramosDepuracion;
    $variablesView['fin_periodo']=$factura->fecha_final->format('d/m/Y');
    $variablesView['nDiasPeriodo']=1+$factura->detalles[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO]->valor->diff($factura->detalles[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL]->valor)->days;
    
    $variablesView['TotalImporteFactura']=$factura->detalles[FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE]->valor;
    $variablesView['TotalPagar']=$factura->detalles[FACTURA_AGUA_RESUMEN_TOTAL_PAGAR]->valor;
    
    $variablesView['VolverUrl']=($es_admin)?PAGE_HOME:PAGE_CONSUMO_INDEX;
    
    $iconoSuministro=$factura_ctl->leerOpcion('ICONOS_SUMINISTRO',$factura->tipo);
    $variablesView['iconoSuministro']=(array_key_exists($factura->tipo,$iconoSuministro))?$iconoSuministro[$factura->tipo]:null;       /// Icono del tipo de suministro
    
    $page->render( "consumo_print_AGUA_view.php", $variablesView);
    
    $page->end_session();
    exit(0);
}

function facturaPropano()
{
    global $factura,$factura_ctl;
    
}

function facturaButano()
{
    global $factura,$factura_ctl;
    
}

function facturaGasoil()
{
    global $factura,$factura_ctl;
    
}

function gestionarError($mensaje)
{
    global $page;
    
    $page->abort_with_error(PAGE_HOME, $mensaje);
}

?>