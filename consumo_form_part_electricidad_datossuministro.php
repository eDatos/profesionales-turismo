<?php

///
/// Parte de DATOS GENERALES de la factura elйctrica.
///

global $variablesPart;

define('FASE_RECOGIDA_FACTURA',2);

define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO','1111.1000.1001');
define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL','1111.1000.1002');
define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO','1111.1000.1010');

define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO','1111_1000_1001');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL','1111_1000_1002');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO','1111_1000_1010');



/////// ENTRADA ///////////////
// oids (atributos del apartado de datos del suministro)
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA','1111.5000.1002');
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO','1111.5000.1003');
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS','1111.5000.1004');
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO','1111.5000.2003');
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO','1111.5000.2004');

// variables de entrada del formulario
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA','1111_5000_1002');
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO','1111_5000_1003');
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS','1111_5000_1004');
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO','1111_5000_2003');
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO','1111_5000_2004');


$ult_factura_buscada=false;
$ult_factura=null;
$ult_att=null;


//------- ESTA PARTE TIENE QUE IR EN consumo_form_part_electricidad_datos_consumo
function recogerDatosEntrada()
{
    global $page;
    
    $valores=array();
    $valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO, NULL);
    $valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO, NULL);
    $valores[FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL, NULL);
    return $valores;
}

//------- ESTA PARTE TIENE QUE IR EN consumo_form_part_electricidad_datos_consumo
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

function precargarDatosFactura()
{
    getUltimosAtributos();
    
    $predatos=array();
    $predatos[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA]=precargaValor(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA);
    //$predatos[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO]=precargaValor(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO);
    //$predatos[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS]=precargaValor(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS);
    $predatos[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]=precargaValor(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO);
    //$predatos[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO]=precargaValor(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO);
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
    if($res==false)
    {
        /// TODO: Se grabу mal los datos. Debemos recargar el mismo trozo de factura, mostrando el error.
        $subview="consumo_form_part_electricidad_datosgenerales_view.php";
        $errores=array("No se han grabado los datos correctamente.");
    }
    
    /// Presentar nueva pantalla.
    $subview=__DIR__ ."/views/consumo_form_part_electricidad_datossuministros_view.php";
    $url_next=$page->self_url(array(ARG_PARTE=>'3', ARG_NUMERO_FACTURA=>$numero_factura));
    $url_prev=$page->self_url(array(ARG_PARTE=>'1', ARG_NUMERO_FACTURA=>$numero_factura));
    
    $distribuidoras=$factura_ctl->leerListaOpciones('ELECTRICIDAD_DISTRIBUIDORAS');
    $peajes=$factura_ctl->leerListaOpciones('ELECTRICIDAD_PEAJE_ACCESO');
    
    $precarga=precargarDatosFactura();
    
    if($precarga[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA]!=null)
    {
        $distribuidoras[$precarga[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA]]->seleccionado=true;
    }
    if($precarga[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]!=null)
    {
        $peajes[$precarga[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]]->seleccionado=true;
    }
    
    
    $mensaje="Estб ud. viendo la parte #2";
    $color='green';
    $variablesPart = array(
        'subview'               => $subview,                /// Indicamos quй vista va asociada a este formulario parcial se debe incluir
        'urlNext'               => $url_next,               /// Indica la url a la que se debe saltar al pulsar el botуn Siguiente
        'urlPrev'               => $url_prev,               /// Indica la url a la que se debe saltar al pulsar el botуn Anterior
        'distribuidoras'        => $distribuidoras,         /// Lista de compaснas distribuidoras
        'peajes'                => $peajes,                 /// Lista de tipos de peajes de acceso a la red elйctrica
        'ColorFondo'            => $color,
        'NumeroFactura'         => $numero_factura,
        //'NumeroContrato'        => $precarga[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO],
        //'Cups'                  => $precarga[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS],
        //'Vencimiento'           => $precarga[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO],
        'texto'                 => $mensaje,
        'errores'               => $errores
    );
    
    return true;
}

?>