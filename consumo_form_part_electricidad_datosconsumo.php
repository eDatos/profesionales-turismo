<?php

///
/// Parte de DATOS GENERALES de la factura eléctrica.
///

global $variablesPart;

define('FASE_RECOGIDA_FACTURA',3);

/////// ENTRADA ///////////////
// oids (atributos del apartado de datos del suministro)
define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO','1111.1000.1001');
define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL','1111.1000.1002');
define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO','1111.1000.1010');
define('FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE','1111.1000.0100.1001');
define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA','1111.5000.1002');
define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO','1111.5000.1003');
define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS','1111.5000.1004');
define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO','1111.5000.2003');
define('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO','1111.5000.2004');

// variables de entrada del formulario
define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO','1111_1000_1001');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL','1111_1000_1002');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO','1111_1000_1010');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE','1111_1000_0100_1001');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA','1111_5000_1002');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO','1111_5000_1003');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS','1111_5000_1004');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO','1111_5000_2003');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO','1111_5000_2004');

define('FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P1','1111.5000.2002.0001');
define('FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P2','1111.5000.2002.0002');
define('FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P3','1111.5000.2002.0003');
define('FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P4','1111.5000.2002.0004');
define('FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P5','1111.5000.2002.0005');
define('FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P6','1111.5000.2002.0006');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P1','1111_5000_2002_0001');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P2','1111_5000_2002_0002');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P3','1111_5000_2002_0003');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P4','1111_5000_2002_0004');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P5','1111_5000_2002_0005');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_CONTRATADA_P6','1111_5000_2002_0006');

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

define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_POTENCIA','1111_6000_1001_1001_1001_0001_1001');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_DIAS','1111_6000_1001_1001_1001_0001_1002');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_PRECIO','1111_6000_1001_1001_1001_0001_1003');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_IMPORTE','1111_6000_1001_1001_1001_0001_1004');

define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_POTENCIA','1111_6000_1001_1001_1001_0002_1001');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_DIAS','1111_6000_1001_1001_1001_0002_1002');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_PRECIO','1111_6000_1001_1001_1001_0002_1003');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_IMPORTE','1111_6000_1001_1001_1001_0002_1004');

define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_POTENCIA','1111_6000_1001_1001_1001_0003_1001');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_DIAS','1111_6000_1001_1001_1001_0003_1002');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_PRECIO','1111_6000_1001_1001_1001_0003_1003');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_IMPORTE','1111_6000_1001_1001_1001_0003_1004');

define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_POTENCIA','1111_6000_1001_1001_1001_0004_1001');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_DIAS','1111_6000_1001_1001_1001_0004_1002');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_PRECIO','1111_6000_1001_1001_1001_0004_1003');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_IMPORTE','1111_6000_1001_1001_1001_0004_1004');

define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_POTENCIA','1111_6000_1001_1001_1001_0005_1001');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_DIAS','1111_6000_1001_1001_1001_0005_1002');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_PRECIO','1111_6000_1001_1001_1001_0005_1003');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_IMPORTE','1111_6000_1001_1001_1001_0005_1004');

define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_POTENCIA','1111_6000_1001_1001_1001_0006_1001');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_DIAS','1111_6000_1001_1001_1001_0006_1002');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_PRECIO','1111_6000_1001_1001_1001_0006_1003');
define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_IMPORTE','1111_6000_1001_1001_1001_0006_1004');

$ult_factura_buscada=false;
$ult_factura=null;
$ult_att=null;


function recogerDatosEntrada()
{
    global $page;
    
    $valores=array();
    
    $valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO, NULL);
    $valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO, NULL);
    $valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL, NULL);
    
    $valores[FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE, NULL);
    //$valores[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA, NULL);
    //$valores[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO, NULL);
    $valores[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS, NULL);
    $valores[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO, NULL);
    //$valores[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO, NULL);
    
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
    
    $listaOids=array();
    if($valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO]!=null)
    {
        $listaOids[]=FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO;
    }
    if($valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO]!=null)
    {
        $listaOids[]=FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO;
        $factura->fecha_inicio=DateTime::createFromFormat('d/m/Y',$valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO]);
    }
    if($valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL]!=null)
    {
        $listaOids[]=FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL;
        $factura->fecha_final=DateTime::createFromFormat('d/m/Y',$valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL]);
    }
    if($valores[FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE]!=null)
    {
        $listaOids[]=FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE;
    }
    /*
    if($valores[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA]!=null)
    {
        $listaOids[]=FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA;
    }
    */
    if($valores[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS]!=null)
    {
        $listaOids[]=FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS;
    }
    if($valores[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]!=null)
    {
        $listaOids[]=FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO;
    }
    
    $OidDao=new OIDDao($factura_ctl->dao->db);
    $atributos=$OidDao->leerListaOids($listaOids);
    foreach($atributos as $atributo)
    {
        $factura->detalles[$atributo->oid]=AtributoFactura::crear($atributo->oid,$atributo->tipo,$valores[$atributo->oid],FASE_RECOGIDA_FACTURA);
    }
    
    /// Guardamos los datos del apartado rellenado (DATOS DE LA FACTURA)
    //// TODO: Gestionar bien los errores.
    $factura->fase=FASE_RECOGIDA_FACTURA;
    if(($factura_ctl->actualizar_periodo_factura($factura)==false)||($factura_ctl->agregar_detalles($factura,$factura->detalles)==false))
    {
        return "Error durante la grabación. Operación interrumpida.";
    }
    
    return true;
}

/// TODO: Precargar los campos con los valores de la factura más reciente del mismo tipo.

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
function precargaValorLimitado($oid, $sugerir)
{
    global $ult_att,$ult_factura;
    
    if($sugerir)
        return precargaValor($oid);
    
    $valor=null;
    if(array_key_exists($oid,$ult_att))
    {
        $valor=$ult_att[$oid]->valor;
    }
    
    return $valor;
}

function precargaValorDefecto($oid,$defecto=null)
{
    global $ult_att;
    
    $valor=null;
    if(array_key_exists($oid,$ult_att))
    {
        $valor=$ult_att[$oid]->valor;
    }
    else
    {
        $valor=$defecto;
    }
    
    return $valor;
}

// Datos para la precarga de campos. Se usan los datos de la factura actual ó los de la última factura similar si existe.
function precargarDatosFactura()
{
    global $factura,$ult_factura;
    
    getUltimosAtributos();
    
    $predatos=array();
    
    $nperiodos=0;
    
    $ndias=1+$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO]->valor->diff($factura->detalles[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL]->valor)->days;
    
    $predatos[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]->valor;
    
    // Si estamos modificando los valores de la potencia facturada (no es la primera vez que pasamos por esta pantalla), no admitimos sugerencias de rellenado en
    // los campos relacionados con la potencia contratada.
    $sugerir=(array_key_exists(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_POTENCIA,$factura->detalles)==false);
    
    // La factura debe existir y tener informado el tipo de peaje.
    switch($predatos[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO])
    {
        // 6 periodos
        case '6.1A':
        case '6.1B':
        case '6.2':
        case '6.3':
        case '6.4':
        case '6.5':
            $nperiodos+=3;
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_POTENCIA]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_POTENCIA, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_DIAS]=precargaValorDefecto(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_DIAS, $ndias);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_PRECIO]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_PRECIO, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_IMPORTE]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_IMPORTE, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_POTENCIA]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_POTENCIA, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_DIAS]=precargaValorDefecto(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_DIAS, $ndias);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_PRECIO]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_PRECIO, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_IMPORTE]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_IMPORTE, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_POTENCIA]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_POTENCIA, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_DIAS]=precargaValorDefecto(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_DIAS, $ndias);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_PRECIO]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_PRECIO, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_IMPORTE]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_IMPORTE, $sugerir);
            // 3 periodos
        case '2.0DHS':
        case '2.1DHS':
        case '3.0A':
        case '3.1A':
            $nperiodos+=1;
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_POTENCIA]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_POTENCIA, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_DIAS]=precargaValorDefecto(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_DIAS, $ndias);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_PRECIO]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_PRECIO, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_IMPORTE]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_IMPORTE, $sugerir);
            // 2 periodos
        case '2.0DHA':
        case '2.1DHA':
            $nperiodos+=1;
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_POTENCIA]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_POTENCIA, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_DIAS]=precargaValorDefecto(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_DIAS, $ndias);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_PRECIO]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_PRECIO, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_IMPORTE]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_IMPORTE, $sugerir);
        // Sin discriminación horaria (1 periodo).
        case '2.0A':
        case '2.1A':
            $nperiodos+=1;
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_POTENCIA]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_POTENCIA, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_DIAS]=precargaValorDefecto(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_DIAS, $ndias);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_PRECIO]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_PRECIO, $sugerir);
            $predatos[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_IMPORTE]=precargaValorLimitado(FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_IMPORTE, $sugerir);
            break;
    }
    
    $predatos['fin_periodo']=$factura->fecha_final->format('d/m/Y');
    
    $predatos['numero_periodos']=$nperiodos;
    
    return $predatos;
}

function procesarParteFactura()
{
    global $page,$variablesPart,$numero_factura;
    
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
    $subview=__DIR__ ."/views/consumo_form_part_electricidad_datosconsumo_view.php";
    $url_next=$page->self_url(array(ARG_PARTE=>'3', ARG_NUMERO_FACTURA=>$numero_factura));
    $url_prev=$page->self_url(array(ARG_PARTE=>'1', ARG_NUMERO_FACTURA=>$numero_factura));
    
    $precarga=precargarDatosFactura();
    
    $mensaje="Está ud. viendo la parte #2";
    $color='green';
    $variablesPart = array(
        'subview'               => $subview,                /// Indicamos qué vista va asociada a este formulario parcial se debe incluir
        'urlNext'               => $url_next,               /// Indica la url a la que se debe saltar al pulsar el botón Siguiente
        'urlPrev'               => $url_prev,               /// Indica la url a la que se debe saltar al pulsar el botón Anterior
        'ColorFondo'            => $color,
        'NumeroFactura'         => $numero_factura,
        'Peaje'                 => $precarga[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO],
        
        'NumeroPeriodos'        => $precarga['numero_periodos'],
        'FechaFinalPeriodo'     => $precarga['fin_periodo'],
        /*
        'P1_potencia'           => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_POTENCIA],
        'P2_potencia'           => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_POTENCIA],
        'P3_potencia'           => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_POTENCIA],
        'P4_potencia'           => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_POTENCIA],
        'P5_potencia'           => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_POTENCIA],
        'P6_potencia'           => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_POTENCIA],
        
        'P1_dias'               => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_DIAS],
        'P2_dias'               => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_DIAS],
        'P3_dias'               => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_DIAS],
        'P4_dias'               => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_DIAS],
        'P5_dias'               => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_DIAS],
        'P6_dias'               => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_DIAS],
        
        'P1_precio'             => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_PRECIO],
        'P2_precio'             => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_PRECIO],
        'P3_precio'             => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_PRECIO],
        'P4_precio'             => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_PRECIO],
        'P5_precio'             => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_PRECIO],
        'P6_precio'             => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_PRECIO],
        
        'P1_importe'            => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_IMPORTE],
        'P2_importe'            => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_IMPORTE],
        'P3_importe'            => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_IMPORTE],
        'P4_importe'            => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_IMPORTE],
        'P5_importe'            => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_IMPORTE],
        'P6_importe'            => $precarga[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_IMPORTE],
        */
        'texto'                 => $mensaje,
        'errores'               => $errores
    );
    
    $listaOids=array(0,FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_POTENCIA,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_POTENCIA,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_POTENCIA,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_POTENCIA,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_POTENCIA,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_POTENCIA);
    for($i=1;$i<=6;$i++)
    {
        if(array_key_exists($listaOids[$i],$precarga))
        {
            $variablesPart['P'.$i.'_potencia']=$precarga[$listaOids[$i]];
        }
    }
    $listaOids=array(0,FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_DIAS,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_DIAS,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_DIAS,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_DIAS,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_DIAS,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_DIAS);
    for($i=1;$i<=6;$i++)
    {
        if(array_key_exists($listaOids[$i],$precarga))
        {
            $variablesPart['P'.$i.'_dias']=$precarga[$listaOids[$i]];
        }
    }
    $listaOids=array(0,FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_PRECIO,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_PRECIO,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_PRECIO,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_PRECIO,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_PRECIO,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_PRECIO);
    for($i=1;$i<=6;$i++)
    {
        if(array_key_exists($listaOids[$i],$precarga))
        {
            $variablesPart['P'.$i.'_precio']=$precarga[$listaOids[$i]];
        }
    }
    $listaOids=array(0,FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P1_IMPORTE,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P2_IMPORTE,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P3_IMPORTE,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P4_IMPORTE,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P5_IMPORTE,
        FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_P6_IMPORTE);
    for($i=1;$i<=6;$i++)
    {
        if(array_key_exists($listaOids[$i],$precarga))
        {
            $variablesPart['P'.$i.'_importe']=$precarga[$listaOids[$i]];
        }
    }
    
    return true;
}

/*
function presentarParteFactura()
{
    $variables = array(
        'urlNext'               => $url_next,
        'ColorFondo'            => $color,
        'NumeroFactura'         => $numero_factura,
        'texto'                 => $mensaje
    );
    //$page->render_ajax($view, $variables);
}
*/

?>