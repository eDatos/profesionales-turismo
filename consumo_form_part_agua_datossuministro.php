<?php

///
/// Parte de DATOS GENERALES de la factura elйctrica.
///

global $variablesPart;

define('FASE_RECOGIDA_FACTURA',2);

define('FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO','2222.1000.1001');
define('FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL','2222.1000.1002');
define('FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO','2222.1000.1010');

define('VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO','2222_1000_1001');
define('VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL','2222_1000_1002');
define('VAR_FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO','2222_1000_1010');


/////// ENTRADA ///////////////
// oids (atributos del apartado de datos del suministro)

define('FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA','2222.5000.1002');
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
define('FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD','2222.5000.2003');

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

$ult_factura_buscada=false;
$ult_factura=null;
$ult_att=null;


function recogerDatosEntrada()
{
    global $page;
    
    $valores=array();
    $valores[FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO, NULL);
    $valores[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO, NULL);
    $valores[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL]=$page->request_post_or_get(VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL, NULL);
    return $valores;
}

function procesarDatosEntrada()
{
    global $page,$factura_ctl,$factura;
    
    if($page->is_post()==false)
    {
        /*
        $factura->fase=FASE_RECOGIDA_FACTURA;
        
        // La factura es del mismo. La abrimos para modificar.
        if($factura_ctl->abrir_modificar_factura($factura)==false)
        {
            return "Error al reabrir la factura para modificarla.";
        }
        */
        return true;
    }
    
    /// Recogemos los datos de entrada (DATOS DE LA FACTURA)
    $valores=recogerDatosEntrada();
    
    $listaOids=array();
    if($valores[FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO]!=null)
    {
        $listaOids[]=FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO;
    }
    if($valores[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO]!=null)
    {
        $listaOids[]=FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO;
        $factura->fecha_inicio=DateTime::createFromFormat('d/m/Y',$valores[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO]);
    }
    if($valores[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL]!=null)
    {
        $listaOids[]=FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL;
        $factura->fecha_final=DateTime::createFromFormat('d/m/Y',$valores[FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL]);
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
        return "Error durante la grabaciуn. Operaciуn interrumpida.";
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
    $predatos[FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA]=precargaValor(FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA);
    //$predatos[FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA]=precargaValor(FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA);
    $predatos[FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR]=precargaValor(FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR);
    //$predatos[FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE]=precargaValor(FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE);
    $predatos[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR]=precargaValorLimitado(FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR);
    $predatos[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL]=precargaValorLimitado(FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL);
    //$predatos[FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA]=precargaValor(FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA);
    //$predatos[FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD]=precargaValor(FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD);
    //$predatos[FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA]=precargaValor(FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA);
    //$predatos[FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR]=precargaValor(FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR);
    
    // De estos dos sуlo uno aparecerб
    $predatos[FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL]=precargaValorLimitado(FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL);
    $predatos[FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA]=precargaValorLimitado(FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA);
    
    return $predatos;
}

function procesarParteFactura()
{
    global $page,$factura_ctl,$variablesPart,$factura,$numero_factura;
    
    $res=procesarDatosEntrada();
    if($res!==true)
    {
        return $res;
    }
    
    $errores=null;
    if($res==false)
    {
        /// TODO: Se grabу mal los datos. Debemos recargar el mismo trozo de factura, mostrando el error.
        $subview="consumo_form_part_agua_datosgenerales_view.php";
        $errores=array("No se han grabado los datos correctamente.");
    }
    
    /// Presentar nueva pantalla.
    $subview=__DIR__ ."/views/consumo_form_part_agua_datossuministros_view.php";
    $url_next=$page->self_url(array(ARG_PARTE=>'3', ARG_NUMERO_FACTURA=>$numero_factura));
    $url_prev=$page->self_url(array(ARG_PARTE=>'1', ARG_NUMERO_FACTURA=>$numero_factura));
    
    $distribuidoras=$factura_ctl->leerListaOpciones('AGUA_DISTRIBUIDORAS');
    //$calibres=$factura_ctl->leerListaOpciones('AGUA_CALIBRES');
    //$tiposSuministro=$factura_ctl->leerListaOpciones('AGUA_TIPOS_SUMINISTRO');
    //$actividades=$factura_ctl->leerListaOpciones('AGUA_ACTIVIDADES');
    //$tarifas=$factura_ctl->leerListaOpciones('AGUA_TARIFAS');
    //$tiposContador=$factura_ctl->leerListaOpciones('AGUA_TIPO_CONTADOR');
    $precarga=precargarDatosFactura();
    
    if($precarga[FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA]!=null)
    {
        $distribuidoras[$precarga[FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA]]->seleccionado=true;
    }
    /*
    if($precarga[FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE]!=null)
    {
        $calibres[$precarga[FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE]]->seleccionado=true;
    }
    if($precarga[FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA]!=null)
    {
        $tiposSuministro[$precarga[FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA]]->seleccionado=true;
    }
    if($precarga[FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD]!=null)
    {
        $actividades[$precarga[FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD]]->seleccionado=true;
    }
    if($precarga[FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA]!=null)
    {
        $tarifas[$precarga[FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA]]->seleccionado=true;
    }
    if($precarga[FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR]!=null)
    {
        $tiposContador[$precarga[FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR]]->seleccionado=true;
    }
    */
    
    $mensaje="Estб ud. viendo la parte #2";
    $color='green';
    $variablesPart = array(
        'subview'               => $subview,                /// Indicamos quй vista va asociada a este formulario parcial se debe incluir
        'urlNext'               => $url_next,               /// Indica la url a la que se debe saltar al pulsar el botуn Siguiente
        'urlPrev'               => $url_prev,               /// Indica la url a la que se debe saltar al pulsar el botуn Anterior
        'distribuidoras'        => $distribuidoras,         /// Lista de compaснas distribuidoras
        /*
        'calibres'              => $calibres,               /// Lista de calibres de los contadores de agua
        'tiposSuministros'      => $tiposSuministro,        /// Lista de las categorнas (tipos de suministros)
        'actividades'           => $actividades,            /// Lista de las actividades (tipos de usuarios segъn su actividad)
        'tarifas'               => $tarifas,                /// Lista de las tarifas aplicables (costes de los servicios)
        'tiposContador'         => $tiposContador,          /// Lista de los tipos de contadores
        */
        'esLecturaReal'         => ($precarga[FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL]!=null),          /// La lectura es real
        'esLecturaEstimada'     => ($precarga[FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA]!=null),      /// La lectura es estimada
        'ColorFondo'            => $color,
        'NumeroFactura'         => $numero_factura,
        'FechaPeriodoInicial'   => ($factura->fecha_inicio!=null) ? $factura->fecha_inicio->format('d/m/Y') : null,
        'FechaPeriodoFinal'     => ($factura->fecha_final!=null) ? $factura->fecha_final->format('d/m/Y') : null,
        /*
        'NumeroContrato'        => $precarga[FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA],
        */
        'NumeroContador'        => $precarga[FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR],
        'LecturaAnterior'       => $precarga[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR],
        'LecturaActual'         => $precarga[FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL],
        'texto'                 => $mensaje,
        'errores'               => $errores
    );
    
    return true;
}

?>