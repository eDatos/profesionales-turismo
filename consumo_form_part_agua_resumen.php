<?php

///
/// Parte de RESUMEN de la factura de agua.
///

global $variablesPart;

define('FASE_RECOGIDA_FACTURA',4);

/////// ENTRADA ///////////////
// oids (atributos del apartado de detalle de la facturación)

define('FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES','2222.6000.1001.1000.');
//define('FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES','2222.6000.1002.1000.');
//define('FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES','2222.6000.1003.1000.');
define('ID_INDICE_PRIMER_BLOQUE',1001);
define('ID_BLOQUE_LIMITE','.1001');
define('ID_BLOQUE_PRECIO','.1002');
define('ID_BLOQUE_CONSUMO_REALIZADO','.1003');
define('ID_BLOQUE_IMPORTE','.1004');
/*
 * Los bloques correspondientes al consumo de agua se forman así (con $indice siendo 0,1,2,3...):
 *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_LIMITE
 *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_PRECIO
 *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_CONSUMO_REALIZADO
 *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_IMPORTE
 */
/*
 * Los bloques correspondientes al saneamiento de agua se forman así (con $indice siendo 0,1,2,3...):
 *      FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_LIMITE
 *      FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_PRECIO
 *      FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_CONSUMO_REALIZADO
 *      FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_IMPORTE
 */
/*
 * Los bloques correspondientes a la depuración de agua se forman así (con $indice siendo 0,1,2,3...):
 *      FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_LIMITE
 *      FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_PRECIO
 *      FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_CONSUMO_REALIZADO
 *      FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_IMPORTE
 */
define('VAR_FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES','2222_6000_1001_1000_');
//define('VAR_FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES','2222_6000_1002_1000_');
//define('VAR_FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES','2222_6000_1003_1000_');
define('VAR_ID_BLOQUE_LIMITE','_1001');
define('VAR_ID_BLOQUE_PRECIO','_1002');
define('VAR_ID_BLOQUE_CONSUMO_REALIZADO','_1003');
define('VAR_ID_BLOQUE_IMPORTE','_1004');


/// Correspondientes a la pantalla de datos de suministro (se ha eliminado la pantalla de consumos)
/////// ENTRADA ///////////////
// oids (atributos del apartado de datos del suministro)
define('FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA','2222.5000.1002');
//define('FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA','2222.5000.1003');
define('FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR','2222.5000.1004');
//define('FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE','2222.5000.1014');
//define('FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR','2222.5000.1024');
define('FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR','2222.6000.1001.1001');
define('FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL','2222.6000.1001.1002');
define('FACTURA_AGUA_DATOS_CONSUMO_M3_CALCULADOS','2222.6000.1001.1004');           // 'Consumo calculado','Consumo calculado a partir de las lecturas (en m3).'
define('FACTURA_AGUA_DATOS_CONSUMO_CONTADOR_REBASADO','2222.6000.1001.1005');       // 'Contador rebasado','El contador de consumo del aparato de medida ha sido rebasado.'
define('FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL','2222.1000.1006');
define('FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA','2222.1000.1007');
//define('FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA','2222.5000.2001');
//define('FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA','2222.5000.2002');
//define('FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD','2222.5000.2003');


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
define('VAR_OVERFLOW_CONTADOR','overflow');
/// Correspondientes a la pantalla de datos de suministro (se ha eliminado la pantalla de consumos)



define('FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE','2222.7000.1000.1001');
define('FACTURA_AGUA_RESUMEN_TOTAL_PAGAR','2222.6000.1000.1002');

define('VAR_FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE','2222_7000_1000_1001');
define('VAR_FACTURA_AGUA_RESUMEN_TOTAL_PAGAR','2222_6000_1000_1002');


$overflowLeido=null;

function recogerDatosEntrada()
{
    global $page;
    
    $valores=array();
    
    $valores[FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA, NULL);
    //$valores[FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA, NULL);
    $valores[FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR, NULL);
    //$valores[FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE, NULL);
    //$valores[FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR, NULL);
    $valores[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR, NULL);
    $valores[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL, NULL);
    //$valores[FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA, NULL);
    //$valores[FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA, NULL);
    //$valores[FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD, NULL);
    
    /*
     $valores[FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL, NULL);
     $valores[FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA, NULL);
     */
    $tipoLectura=$page->request_post_or_get(VAR_TIPO_LECTURA_CONTADOR, NULL);
    if($tipoLectura!=null)
    {
        // $tipoLectura debe ser '2222.1000.1006' ó '2222.1000.1007'.
        $valores[$tipoLectura]='S';
    }
    
    $overflowLeido=$page->request_post_or_get(VAR_OVERFLOW_CONTADOR, NULL);
    
    return $valores;
}

function recogerDatosEntrada_VIEJA()
{
    global $page;
    
    $valores=array();
    
    for($i=0;$i<4;$i++)
    {
        $prefijo=FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$i);
        $prefijo2=VAR_FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$i);
        
        // Datos de la facturación del consumo de agua
        /*
         * Los bloques correspondientes al consumo de agua se forman así (con $indice siendo 0,1,2,3...):
         *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_LIMITE
         *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_PRECIO
         *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_CONSUMO_REALIZADO
         *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_IMPORTE
         */
        $valores[$prefijo.ID_BLOQUE_LIMITE]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_LIMITE, NULL);
        $valores[$prefijo.ID_BLOQUE_PRECIO]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_PRECIO, NULL);
        $valores[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_CONSUMO_REALIZADO, NULL);
        $valores[$prefijo.ID_BLOQUE_IMPORTE]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_IMPORTE, NULL);
    }
    
//     for($i=0;$i<4;$i++)
//     {
//         $prefijo=FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$i);
//         $prefijo2=VAR_FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$i);
        
//         // Datos de la facturación del consumo de agua
//         /*
//          * Los bloques correspondientes al consumo de agua se forman así (con $indice siendo 0,1,2,3...):
//          *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_LIMITE
//          *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_PRECIO
//          *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_CONSUMO_REALIZADO
//          *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_IMPORTE
//          */
//         $valores[$prefijo.ID_BLOQUE_LIMITE]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_LIMITE, NULL);
//         $valores[$prefijo.ID_BLOQUE_PRECIO]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_PRECIO, NULL);
//         $valores[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_CONSUMO_REALIZADO, NULL);
//         $valores[$prefijo.ID_BLOQUE_IMPORTE]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_IMPORTE, NULL);
//     }
    
//     for($i=0;$i<4;$i++)
//     {
//         $prefijo=FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$i);
//         $prefijo2=VAR_FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$i);
        
//         // Datos de la facturación del consumo de agua
//         /*
//          * Los bloques correspondientes al consumo de agua se forman así (con $indice siendo 0,1,2,3...):
//          *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_LIMITE
//          *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_PRECIO
//          *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_CONSUMO_REALIZADO
//          *      FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES.(ID_INDICE_PRIMER_BLOQUE+$indice).ID_BLOQUE_IMPORTE
//          */
//         $valores[$prefijo.ID_BLOQUE_LIMITE]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_LIMITE, NULL);
//         $valores[$prefijo.ID_BLOQUE_PRECIO]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_PRECIO, NULL);
//         $valores[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_CONSUMO_REALIZADO, NULL);
//         $valores[$prefijo.ID_BLOQUE_IMPORTE]=$page->request_post_or_get($prefijo2.VAR_ID_BLOQUE_IMPORTE, NULL);
//     }
    
    return $valores;
}

function calcularComplementoLectura($valor)
{
    $capacidad=10;
    while($capacidad<=$valor)
        $capacidad=$capacidad * 10;
        return ($capacidad-$valor);
}

function str2Float($valor)
{
    return floatval(str_replace(',','.',$valor));
}

function float2Str($valor)
{
    return str_replace('.',',',strval($valor));
}

/// TODO: En esta página se recogen datos opcionales. Por tanto, antes de cargar los nuevos, hay que eliminar todos los anteiores para evitar que si no llegan nuevos valores, permanezcan los antiguos.
// Si se navega hacia atrás usando el botón 'Anterior', es posible que valores opcionales de la pasada anterior no sean borrados porque no son enviados en la pasada actual.
function procesarDatosEntrada()
{
    global $page,$factura_ctl,$factura,$overflowLeido;
    
    if($page->is_post()==false)
    {
        return true;
    }
    
    /// Recogemos los datos de entrada (FACTURACIÓN ENERGÍA)
    $valores=recogerDatosEntrada();
    
    /// Correspondientes a la pantalla de datos de suministro (se ha eliminado la pantalla de consumos)
    $lecturaActual=str2Float($valores[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL]);
    $lecturaAnterior=str2Float($valores[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR]);
    
    if($lecturaActual<$lecturaAnterior)
    {
        $valores[FACTURA_AGUA_DATOS_CONSUMO_CONTADOR_REBASADO]='S';
        $valores[FACTURA_AGUA_DATOS_CONSUMO_M3_CALCULADOS]=float2Str(calcularComplementoLectura($lecturaAnterior) + $lecturaActual);
    }
    else
    {
        $valores[FACTURA_AGUA_DATOS_CONSUMO_CONTADOR_REBASADO]='N';
        $valores[FACTURA_AGUA_DATOS_CONSUMO_M3_CALCULADOS]= float2Str($lecturaActual - $lecturaAnterior);
    }
    /// Correspondientes a la pantalla de datos de suministro (se ha eliminado la pantalla de consumos)
    
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
    
    // TODO: Precalcular el importe a partir de la pantalla anterior (si no hay datos históricos).
    $predatos[FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE]=precargaValorLimitado(FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE);
    $predatos[FACTURA_AGUA_RESUMEN_TOTAL_PAGAR]=precargaValorLimitado(FACTURA_AGUA_RESUMEN_TOTAL_PAGAR);
    
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
    $subview=__DIR__ ."/views/consumo_form_part_agua_resumen_view.php";
    $url_next=$page->self_url(array(ARG_PARTE=>OP_ENVIO_FACTURA, ARG_NUMERO_FACTURA=>$numero_factura));
    //$url_prev=$page->self_url(array(ARG_PARTE=>'3', ARG_NUMERO_FACTURA=>$numero_factura));
    $url_prev=$page->self_url(array(ARG_PARTE=>'2', ARG_NUMERO_FACTURA=>$numero_factura));
    
    $precarga=precargarDatosFactura();
    
    $color='green';
    $variablesPart = array(
        'subview'               => $subview,                /// Indicamos qué vista va asociada a este formulario parcial se debe incluir
        'urlNext'               => $url_next,               /// Indica la url a la que se debe saltar al pulsar el botón Siguiente
        'urlPrev'               => $url_prev,               /// Indica la url a la que se debe saltar al pulsar el botón Anterior
        'ColorFondo'            => $color,
        'NumeroFactura'         => $numero_factura,
        'TotalImporteFactura'   => $precarga[FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE],
        'TotalPagar'            => $precarga[FACTURA_AGUA_RESUMEN_TOTAL_PAGAR],
        'errores'               => $errores
    );
    
    foreach($precarga as $clave => $valor)
    {
        $variablesPart[$clave]=$valor;
    }
    
    return true;
}

?>