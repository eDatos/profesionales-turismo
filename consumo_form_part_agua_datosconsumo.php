<?php

///
/// Parte de DATOS GENERALES de la factura eléctrica.
///

global $variablesPart;

define('FASE_RECOGIDA_FACTURA',3);

define('FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO','2222.1000.1001');
define('FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL','2222.1000.1002');

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






define('FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES','2222.6000.1001.1000.');
define('FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES','2222.6000.1002.1000.');
define('FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES','2222.6000.1003.1000.');
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
define('VAR_FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES','2222_6000_1002_1000_');
define('VAR_FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES','2222_6000_1003_1000_');
define('VAR_ID_BLOQUE_LIMITE','_1001');
define('VAR_ID_BLOQUE_PRECIO','_1002');
define('VAR_ID_BLOQUE_CONSUMO_REALIZADO','_1003');
define('VAR_ID_BLOQUE_IMPORTE','_1004');




$ult_factura_buscada=false;
$ult_factura=null;
$ult_att=null;
$overflowLeido=null;

function recogerDatosEntrada()
{
    global $page,$overflow;
    
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

function calcularComplementoLectura($valor)
{
    $capacidad=10;
    while($capacidad<=$valor)
        $capacidad=$capacidad * 10;
    return ($capacidad-$valor);
}

function procesarDatosEntrada()
{
    global $page,$factura_ctl,$factura,$overflowLeido;
    
    if($page->is_post()==false)
    {
        return true;
    }
    
    /// Recogemos los datos de entrada (FACTURACIÓN ENERGÍA)
    $valores=recogerDatosEntrada();
    
    $lecturaActual=(int)$valores[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL];
    $lecturaAnterior=(int)$valores[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR];
    
    if($lecturaActual<$lecturaAnterior)
    {
        $valores[FACTURA_AGUA_DATOS_CONSUMO_CONTADOR_REBASADO]='S';
        $valores[FACTURA_AGUA_DATOS_CONSUMO_M3_CALCULADOS]=(calcularComplementoLectura($lecturaAnterior) + $lecturaActual);
    }
    else
    {
        $valores[FACTURA_AGUA_DATOS_CONSUMO_CONTADOR_REBASADO]='N';
        $valores[FACTURA_AGUA_DATOS_CONSUMO_M3_CALCULADOS]= ($lecturaActual - $lecturaAnterior);
    }
    
    
    /// Guardamos los datos del apartado rellenado (DATOS DE DETALLES DE LA FACTURACIÓN)
    if($factura_ctl->cargarDatosEntradaEnFactura($factura,$valores,FASE_RECOGIDA_FACTURA)==false)
    {
        return "Error durante la grabación. Operación interrumpida.";
    }
    
    return true;
}

/// TODO: Precargar los campos con los valores de la factura más reciente del mismo tipo.

function getUltimosAtributos()
{
    global $factura_ctl,$factura,$ult_att;
    
    $ult_att=$factura_ctl->cargarAtributosPrevios($factura);
    return $ult_att;
}

/////// FUNCIONES NO USADAS ///////
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
/////// FUNCIONES NO USADAS ///////

// Datos para la precarga de campos. Se usan los datos de la factura actual ó los de la última factura similar si existe.
function precargarDatosFactura()
{
    global $factura,$ult_att;
    
    getUltimosAtributos();
    
    $predatos=array();
    
    // Estos datos deben existir ya en la factura
    $lecturaActual=(int)$factura->detalles[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL]->valor;
    $lecturaAnterior=(int)$factura->detalles[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR]->valor;
    //$M3Consumidos=($lecturaActual - $lecturaAnterior);
    $M3Consumidos=(int)$factura->detalles[FACTURA_AGUA_DATOS_CONSUMO_M3_CALCULADOS]->valor;
    $Overflow=$factura->detalles[FACTURA_AGUA_DATOS_CONSUMO_CONTADOR_REBASADO]->valor;
    
    
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
        if(array_key_exists($prefijo.ID_BLOQUE_LIMITE,$ult_att))
        {
            $nTramosConsumo=$i;
            $predatos[$prefijo.ID_BLOQUE_LIMITE]=$ult_att[$prefijo.ID_BLOQUE_LIMITE]->valor;
        }
        if(array_key_exists($prefijo.ID_BLOQUE_PRECIO,$ult_att))
        {
            $nTramosConsumo=$i;
            $predatos[$prefijo.ID_BLOQUE_PRECIO]=$ult_att[$prefijo.ID_BLOQUE_PRECIO]->valor;
        }
        if(array_key_exists($prefijo.ID_BLOQUE_CONSUMO_REALIZADO,$ult_att))
        {
            $nTramosConsumo=$i;
            $predatos[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]=$ult_att[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]->valor;
        }
        if(array_key_exists($prefijo.ID_BLOQUE_IMPORTE,$ult_att))
        {
            $nTramosConsumo=$i;
            $predatos[$prefijo.ID_BLOQUE_IMPORTE]=$ult_att[$prefijo.ID_BLOQUE_IMPORTE]->valor;
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
//         if(array_key_exists($prefijo.ID_BLOQUE_LIMITE,$ult_att))
//         {
//             $nTramosSaneamiento=$i;
//             $predatos[$prefijo.ID_BLOQUE_LIMITE]=$ult_att[$prefijo.ID_BLOQUE_LIMITE]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_PRECIO,$ult_att))
//         {
//             $nTramosSaneamiento=$i;
//             $predatos[$prefijo.ID_BLOQUE_PRECIO]=$ult_att[$prefijo.ID_BLOQUE_PRECIO]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_CONSUMO_REALIZADO,$ult_att))
//         {
//             $nTramosSaneamiento=$i;
//             $predatos[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]=$ult_att[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_IMPORTE,$ult_att))
//         {
//             $nTramosSaneamiento=$i;
//             $predatos[$prefijo.ID_BLOQUE_IMPORTE]=$ult_att[$prefijo.ID_BLOQUE_IMPORTE]->valor;
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
//         if(array_key_exists($prefijo.ID_BLOQUE_LIMITE,$ult_att))
//         {
//             $nTramosDepuracion=$i;
//             $predatos[$prefijo.ID_BLOQUE_LIMITE]=$ult_att[$prefijo.ID_BLOQUE_LIMITE]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_PRECIO,$ult_att))
//         {
//             $nTramosDepuracion=$i;
//             $predatos[$prefijo.ID_BLOQUE_PRECIO]=$ult_att[$prefijo.ID_BLOQUE_PRECIO]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_CONSUMO_REALIZADO,$ult_att))
//         {
//             $nTramosDepuracion=$i;
//             $predatos[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]=$ult_att[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO]->valor;
//         }
//         if(array_key_exists($prefijo.ID_BLOQUE_IMPORTE,$ult_att))
//         {
//             $nTramosDepuracion=$i;
//             $predatos[$prefijo.ID_BLOQUE_IMPORTE]=$ult_att[$prefijo.ID_BLOQUE_IMPORTE]->valor;
//         }
//     }
        
    $predatos['M3Consumidos']=$M3Consumidos;
    $predatos['Overflow']=$Overflow;
    $predatos['nTramosConsumo']=$nTramosConsumo;
    //$predatos['nTramosSaneamiento']=$nTramosSaneamiento;
    //$predatos['nTramosDepuracion']=$nTramosDepuracion;
    $predatos['fin_periodo']=$factura->fecha_final->format('d/m/Y');
    $predatos['nDiasPeriodo']=1+$factura->detalles[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO]->valor->diff($factura->detalles[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL]->valor)->days;

    return $predatos;
}

function procesarParteFactura()
{
    global $page,$factura,$variablesPart,$numero_factura;
    
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
    $subview=__DIR__ ."/views/consumo_form_part_agua_datosconsumo_view.php";
    $url_next=$page->self_url(array(ARG_PARTE=>'4', ARG_NUMERO_FACTURA=>$numero_factura));
    $url_prev=$page->self_url(array(ARG_PARTE=>'2', ARG_NUMERO_FACTURA=>$numero_factura));
    
    $precarga=precargarDatosFactura();
    
    $mensaje="Está ud. viendo la parte #2";
    $color='green';
    $variablesPart = array(
        'subview'               => $subview,                /// Indicamos qué vista va asociada a este formulario parcial se debe incluir
        'urlNext'               => $url_next,               /// Indica la url a la que se debe saltar al pulsar el botón Siguiente
        'urlPrev'               => $url_prev,               /// Indica la url a la que se debe saltar al pulsar el botón Anterior
        'ColorFondo'            => $color,
        'NumeroFactura'         => $numero_factura,
        'FechaPeriodoInicial'   => ($factura->fecha_inicio!=null) ? $factura->fecha_inicio->format('d/m/Y') : null,
        'FechaPeriodoFinal'     => ($factura->fecha_final!=null) ? $factura->fecha_final->format('d/m/Y') : null,
        
        'M3Consumidos'          => $precarga['M3Consumidos'],
        'Overflow'              => $precarga['Overflow'],
        'nTramosConsumo'        => $precarga['nTramosConsumo'],
        //'nTramosSaneamiento'    => $precarga['nTramosSaneamiento'],
        //'nTramosDepuracion'     => $precarga['nTramosDepuracion'],
        'FechaFinalPeriodo'     => $precarga['fin_periodo'],
        'nDiasPeriodo'          => $precarga['nDiasPeriodo'],

        'texto'                 => $mensaje,
        'errores'               => $errores
    );
    
    for($i=0;$i<=$precarga['nTramosConsumo'];$i++)
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
        $variablesPart['T'.$i.'_consumo_limite']=@$precarga[$prefijo.ID_BLOQUE_LIMITE];
        //$variablesPart['T'.$i.'_consumo_precio']=@$precarga[$prefijo.ID_BLOQUE_PRECIO];
        $variablesPart['T'.$i.'_consumo_realizado']=@$precarga[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO];
        //$variablesPart['T'.$i.'_consumo_importe']=@$precarga[$prefijo.ID_BLOQUE_IMPORTE];
    }
    
//     for($i=0;$i<=$precarga['nTramosSaneamiento'];$i++)
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
//         $variablesPart['T'.$i.'_saneamiento_limite']=@$precarga[$prefijo.ID_BLOQUE_LIMITE];
//         $variablesPart['T'.$i.'_saneamiento_precio']=@$precarga[$prefijo.ID_BLOQUE_PRECIO];
//         $variablesPart['T'.$i.'_saneamiento_realizado']=@$precarga[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO];
//         $variablesPart['T'.$i.'_saneamiento_importe']=@$precarga[$prefijo.ID_BLOQUE_IMPORTE];
//     }
    
//     for($i=0;$i<=$precarga['nTramosDepuracion'];$i++)
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
//         $variablesPart['T'.$i.'_depuracion_limite']=@$precarga[$prefijo.ID_BLOQUE_LIMITE];
//         $variablesPart['T'.$i.'_depuracion_precio']=@$precarga[$prefijo.ID_BLOQUE_PRECIO];
//         $variablesPart['T'.$i.'_depuracion_realizado']=@$precarga[$prefijo.ID_BLOQUE_CONSUMO_REALIZADO];
//         $variablesPart['T'.$i.'_depuracion_importe']=@$precarga[$prefijo.ID_BLOQUE_IMPORTE];
//     }
    
    return true;
}

?>