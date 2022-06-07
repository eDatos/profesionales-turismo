<?php

///
/// Parte de RESUMEN de la factura eléctrica.
///

global $variablesPart;

define('FASE_RECOGIDA_FACTURA',5);

define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO','1111.5000.2003');

define('ELECTRICIDAD_IMPUESTOS_ELECTRICIDAD','ELECTRICIDAD');
define('ELECTRICIDAD_IMPUESTOS_IGIG_NORMAL','IGIG_NORMAL');
define('ELECTRICIDAD_IMPUESTOS_IGIG_REDUCIDO_3','IGIG_REDUCIDO_3');
define('ELECTRICIDAD_IMPUESTOS_IGIG_REDUCIDO_0','IGIG_REDUCIDO_0');

/////// ENTRADA ///////////////
// oids (atributos del apartado de datos de la energía facturada)
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

// Importe potenia facturada: Importe correspondiente a la potencia contratada
define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL','1111.6000.1001.1001.1001.1002');

// Importe energía facturada: Importe correspondiente a la energía facturada
define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL','1111.6000.1001.1002.1001.1003');


define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_ENERGIA','1111_6000_1001_1002_1001_0001_1001');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_PRECIO','1111_6000_1001_1002_1001_0001_1002');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_IMPORTE','1111_6000_1001_1002_1001_0001_1003');

define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_ENERGIA','1111_6000_1001_1002_1001_0002_1001');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_PRECIO','1111_6000_1001_1002_1001_0002_1002');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_IMPORTE','1111_6000_1001_1002_1001_0002_1003');

define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_ENERGIA','1111_6000_1001_1002_1001_0003_1001');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_PRECIO','1111_6000_1001_1002_1001_0003_1002');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_IMPORTE','1111_6000_1001_1002_1001_0003_1003');

define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_ENERGIA','1111_6000_1001_1002_1001_0004_1001');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_PRECIO','1111_6000_1001_1002_1001_0004_1002');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_IMPORTE','1111_6000_1001_1002_1001_0004_1003');

define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_ENERGIA','1111_6000_1001_1002_1001_0005_1001');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_PRECIO','1111_6000_1001_1002_1001_0005_1002');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_IMPORTE','1111_6000_1001_1002_1001_0005_1003');

define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_ENERGIA','1111_6000_1001_1002_1001_0006_1001');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_PRECIO','1111_6000_1001_1002_1001_0006_1002');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_IMPORTE','1111_6000_1001_1002_1001_0006_1003');


define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL','1111_6000_1001_1001_1001_1002');
define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL','1111_6000_1001_1002_1001_1003');

define('FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA','1111.3000.1001');
define('FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS','1111.3000.1002');
define('FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR','1111.3000.1100');

define('VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA','1111_3000_1001');
define('VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS','1111_3000_1002');
define('VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR','1111_3000_1100');



function recogerDatosEntrada()
{
    global $page;
    
    $valores=array();
    
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_ENERGIA]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_ENERGIA, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_PRECIO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_PRECIO, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_IMPORTE]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_IMPORTE, NULL);

    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_ENERGIA]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_ENERGIA, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_PRECIO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_PRECIO, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_IMPORTE]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_IMPORTE, NULL);
    
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_ENERGIA]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_ENERGIA, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_PRECIO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_PRECIO, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_IMPORTE]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_IMPORTE, NULL);
    
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_ENERGIA]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_ENERGIA, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_PRECIO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_PRECIO, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_IMPORTE]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_IMPORTE, NULL);
    
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_ENERGIA]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_ENERGIA, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_PRECIO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_PRECIO, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_IMPORTE]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_IMPORTE, NULL);
    
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_ENERGIA]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_ENERGIA, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_PRECIO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_PRECIO, NULL);
    $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_IMPORTE]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_IMPORTE, NULL);
    
    return $valores;
}

function procesarDatosEntrada()
{
    global $page,$factura_ctl,$factura;
    
    if($page->is_post()==false)
    {
        return true;
    }
    
    /// Recogemos los datos de entrada (FACTURACIÓN ENERGÍA)
    $valores=recogerDatosEntrada();
    
    /// Guardamos los datos del apartado rellenado (DATOS DE DETALLES DE LA FACTURACIÓN)
    if($factura_ctl->cargarDatosEntradaEnFactura($factura,$valores,FASE_RECOGIDA_FACTURA)==false)
    {
        return "Error durante la grabación. Operación interrumpida.";
    }
    
    return true;
}
        
function getUltimaFactura()
{
    global $factura_ctl,$factura,$ult_factura_buscada,$ult_factura;
    
    if($ult_factura_buscada==false)
    {
        $ult_factura=$factura_ctl->cargar_ultima_factura($factura->id_establecimiento,$factura->tipo,$factura->num_factura);
        $ult_factura_buscada=true;
    }
    return $ult_factura;
}

function getUltimosAtributos()
{
    global $factura_ctl,$factura,$ult_att;
    
    $ult_att=$factura_ctl->cargarAtributosPrevios($factura);
    return $ult_att;
}

function precargaValor($oid)
{
    global $ult_att,$ult_factura;
    
    $valor=null;
    if(array_key_exists($oid,$ult_att))
    {
        $valor=$ult_att[$oid]->valor;
    }
    else
    {
        if(USAR_HISTORIAL_SUGERIR_CONSUMO=='S')
        {
            getUltimaFactura();
            if($ult_factura!=null)
            {
                if(array_key_exists($oid,$ult_factura->detalles))
                {
                    $valor=$ult_factura->detalles[$oid]->valor;
                }
            }
        }
    }
    
    return $valor;
}

// Esta función obtiene el valor de precarga de un atributo cuando se quiere rescartar el valor previo (modificación) pero no sugerencias de facturas previas.
function precargaValorLimitado($oid)
{
    global $ult_att,$ult_factura;
    
    $valor=null;
    if(array_key_exists($oid,$ult_att))
    {
        $valor=$ult_att[$oid]->valor;
    }
    
    return $valor;
}

function precargarDatosFactura()
{
    getUltimosAtributos();
    
    $predatos=array();
    
    //$predatos[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA]=precargaValor(FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA);
    //$predatos[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS]=precargaValor(FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS);
    $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL);
    $predatos[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL]=precargaValorLimitado(FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL);
    $predatos[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR]=precargaValorLimitado(FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR);
    
    return $predatos;
}

function procesarParteFactura()
{
    global $page,$factura_ctl,$variablesPart,$numero_factura;
    
    $res=procesarDatosEntrada();
    if($res!==true)
    {
        return $res;
    }
    
    $errores=null;
    /*
    if($res==false)
    {
        /// TODO: Se grabó mal los datos. Debemos recargar el mismo trozo de factura, mostrando el error.
        $subview="consumo_form_part_electricidad_datossuministros_view.php";
        $errores=array("No se han grabado los datos correctamente.");
    }
    */
    
    /// Presentar nueva pantalla.
    $subview=__DIR__ ."/views/consumo_form_part_electricidad_resumen_view.php";
    $url_next=$page->self_url(array(ARG_PARTE=>OP_ENVIO_FACTURA, ARG_NUMERO_FACTURA=>$numero_factura));
    $url_prev=$page->self_url(array(ARG_PARTE=>'3', ARG_NUMERO_FACTURA=>$numero_factura));
    
    $impuestos=$factura_ctl->leerListaOpciones('ELECTRICIDAD_IMPUESTOS');
    
    $precarga=precargarDatosFactura();
    
    $color='green';
    $variablesPart = array(
        'subview'               => $subview,                /// Indicamos qué vista va asociada a este formulario parcial se debe incluir
        'urlNext'               => $url_next,               /// Indica la url a la que se debe saltar al pulsar el botón Siguiente
        'urlPrev'               => $url_prev,               /// Indica la url a la que se debe saltar al pulsar el botón Anterior
        'ColorFondo'            => $color,
        'NumeroFactura'         => $numero_factura,
        'ImpuestoElectricidad'  => $impuestos[ELECTRICIDAD_IMPUESTOS_ELECTRICIDAD]->desc_corta,
        'ImpuestoIgicNormal'    => $impuestos[ELECTRICIDAD_IMPUESTOS_IGIG_NORMAL]->desc_corta,
        'ImpuestoIgicReducido'  => $impuestos[ELECTRICIDAD_IMPUESTOS_IGIG_REDUCIDO_3]->desc_corta,
        'ImpuestoIgicSuperreducido'  => $impuestos[ELECTRICIDAD_IMPUESTOS_IGIG_REDUCIDO_0]->desc_corta,
        //'TotalImporteEnergia'   => $precarga[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA],
        //'TotalImporteServicios' => $precarga[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS],
        'TotalImportePotenciaFacturada'   => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL],
        'TotalImporteEnergiaFacturada'   => $precarga[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL],
        'TotalPagar'            => $precarga[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR],
        'errores'               => $errores
    );
    
    foreach($precarga as $clave => $valor)
    {
        $variablesPart[$clave]=$valor;
    }
    
    return true;
}

?>