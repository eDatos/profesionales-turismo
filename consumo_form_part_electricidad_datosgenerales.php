<?php

///
/// Parte de DATOS GENERALES de la factura elйctrica.
///

global $variablesPart;

define('FASE_RECOGIDA_FACTURA',1);

/*
/////// ENTRADA ///////////////
// oids (atributos comunes a todas las facturas)
define ('FACTURA_EMPRESA_SUMINISTRADORA','0000.1000');
define ('FACTURA_TIPO_SUMINISTRO','0000.1001');
define ('FACTURA_NUMERO_FACTURA','0000.1002');

// variables de entrada del formulario
define ('VAR_FACTURA_EMPRESA_SUMINISTRADORA','0000_1000');
define ('VAR_FACTURA_TIPO_SUMINISTRO','0000_1001');
define ('VAR_FACTURA_NUMERO_FACTURA','0000_1002');
*/

define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO','1111.1000.1001');
define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL','1111.1000.1002');
define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_NUMERO_FACTURA','1111.1000.1003');
define('FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO','1111.1000.1010');

define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO','1111_1000_1001');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL','1111_1000_1002');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_NUMERO_FACTURA','1111_1000_1003');
define('VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO','1111_1000_1010');

/////// ENTRADA ///////////////
// oids (atributos del apartado de datos del suministro)
define ('FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE','1111.1000.0100.1001');
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA','1111.5000.1002');
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO','1111.5000.1003');
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS','1111.5000.1004');
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO','1111.5000.2003');
define ('FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO','1111.5000.2004');

// variables de entrada del formulario
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE','1111_1000_0100_1001');
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA','1111_5000_1002');
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO','1111_5000_1003');
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS','1111_5000_1004');
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO','1111_5000_2003');
define ('VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO','1111_5000_2004');

//$es_nueva=false;
$ult_factura_buscada=false;
$ult_factura=null;
$ult_att=null;


/*
function recogerDatosEntrada()
{
    global $page;
    
    $valores=array();
    $valores[FACTURA_EMPRESA_SUMINISTRADORA]=$page->request_post_or_get(VAR_FACTURA_EMPRESA_SUMINISTRADORA, NULL);
    $valores[FACTURA_TIPO_SUMINISTRO]=$page->request_post_or_get(VAR_FACTURA_TIPO_SUMINISTRO, NULL);
    $valores[FACTURA_NUMERO_FACTURA]=$page->request_post_or_get(VAR_FACTURA_NUMERO_FACTURA, NULL);
    return $valores;
}
*/

function procesarDatosEntrada()
{
    global $factura_ctl,$factura;
    
    if($factura!=null)
    {
        return $factura_ctl->iniciarCapturaFactura($factura);
    }
    else
    {
        $res=$factura_ctl->crearNuevaFactura();
        if(is_a($res,'Factura'))
        {
            $factura=$res;
            return true;
        }
        else
            return $res;
    }
    
    
    /*
    global $page,$factura_ctl,$factura;

    /+*
    if($page->is_post()==false)
    {
        return true;
    }
    *+/
    /+*
    if($page->is_post()==false)
    {
        if($factura==null)
        {
            // Error catastrуfico.
            return "Factura no encontrada.";
        }
        
        $es_nueva=false;
        
        $factura->fase=FASE_RECOGIDA_FACTURA;
        
        // La factura es del mismo. La abrimos para modificar.
        if($factura_ctl->abrir_modificar_factura($factura)==false)
        {
            return "Error al reabrir la factura para modificarla.";
        }
        
        return true;
    }
    *+/
    
    $valores=recogerDatosEntrada();
    
    $id_consumo_factura=null;
    $es_nueva=false;
    
    if($factura!=null)
    {
        // La factura ya existe. Debemos comprobar si ha cambiado de tipo o no.
        if(($factura->suministradora!=null)&&($valores[FACTURA_TIPO_SUMINISTRO]!=null)&&($factura->suministradora->tipo!=$valores[FACTURA_TIPO_SUMINISTRO]))
        {
            // Ha cambiado de tipo. Debemos descartar completamente la factura actual y comenzar una nueva.
            $es_nueva=true;
        }
        else
        {
            $factura->fase=FASE_RECOGIDA_FACTURA;
            
            // La factura es del mismo. La abrimos para modificar.
            if($factura_ctl->abrir_modificar_factura($factura)==false)
            {
                return "Error al reabrir la factura para modificarla.";
            }
            
            // En este punto, la nueva factura tiene el mismo nъmero y tipo.
            // Hemos de refrescar el suministrador.
            if(($factura->suministradora!=null)&&($valores[FACTURA_EMPRESA_SUMINISTRADORA]!=null)&&($factura->suministradora->nombre!=$valores[FACTURA_EMPRESA_SUMINISTRADORA]))
            {
                $atributos=array(
                    FACTURA_EMPRESA_SUMINISTRADORA => $valores[FACTURA_EMPRESA_SUMINISTRADORA]		// Empresa suministradora
                );
                
                $atts_factura=$factura_ctl->generarAtributos($atributos,FASE_RECOGIDA_FACTURA);
                if($factura_ctl->update_atributos($factura,$atts_factura)==false)
                {
                    return "Error al reabrir la factura para modificarla.";
                }
            }
        }
    }
    else
    {
        // La factura no existe, es completamente nueva.
        $es_nueva=true;
    }
    
    /// Recogemos los datos de entrada (DATOS DE LA FACTURA). Sуlo si la factura es nueva.
    if($es_nueva)
    {
        $establecimiento = $page->get_current_establecimiento();
        
        $factura=new Factura();
        $factura->id_consumo=$id_consumo_factura;
        $factura->id_establecimiento=$establecimiento->id_establecimiento;
        $factura->id_usuario=$page->get_current_userid();
        $factura->fecha = new DateTime('now');
        $factura->fecha_cierre=null;
        $factura->fecha_inicio=new DateTime('now');
        $factura->fecha_final=null;
        $factura->fase=FASE_RECOGIDA_FACTURA;
        $factura->validada='I';
        
        $factura->suministradora=new Suministradora($valores[FACTURA_EMPRESA_SUMINISTRADORA],$valores[FACTURA_TIPO_SUMINISTRO]);
        $factura->num_factura=$valores[FACTURA_NUMERO_FACTURA];
        $factura->tipo=$valores[FACTURA_TIPO_SUMINISTRO];
        
        $atributos=array(
            FACTURA_EMPRESA_SUMINISTRADORA => $valores[FACTURA_EMPRESA_SUMINISTRADORA],		// Empresa suministradora
            FACTURA_TIPO_SUMINISTRO => $valores[FACTURA_TIPO_SUMINISTRO],			        // Tipo de suministro
            FACTURA_NUMERO_FACTURA => $valores[FACTURA_NUMERO_FACTURA]					    // Nъmero de factura
        );
        
        $factura->detalles=$factura_ctl->generarAtributos($atributos,FASE_RECOGIDA_FACTURA);
        
        if($factura_ctl->guardarFactura($factura)==false)
        {
            /// TODO: Controlar de manera adecuada el error.
            return "Fallo al crear la factura.";
        }
    }
    
    return true;
    */
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

// Datos para la precarga de campos. Se usan los datos de la factura actual у los de la ъltima factura similar si existe.
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
    global $factura,$ult_factura,$ult_att;//,$es_nueva;

    $predatos=new \stdClass;
    /*
     $predatos->suministradora=null;
     $predatos->tipoFactura=null;
     $predatos->referencia_contrato=null;
     */
    $predatos->fecha_inicio=null;
    $predatos->fecha_final=null;
    $predatos->comercializadora=null;
    $predatos->distribuidora=null;
    $predatos->cups=null;
    $predatos->peaje=null;
    
    /*
    if($factura->suministradora!=null)
    {
        if(($factura->suministradora->nombre!=null)&&($factura->suministradora->tipo!=null))
        {
            $predatos->suministradora=$factura->suministradora->nombre;
            $predatos->tipoFactura=$factura->suministradora->tipo;
        }
    }
    */
    
    // Rellenamos los datos con los valores en la factura (si ya existнa)
    //if($es_nueva==false)
    if($factura->es_creada==false)
    {
        if($factura->detalles!=null)
        {
            /*
            if(array_key_exists(FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO,$factura->detalles))
                $predatos->referencia_contrato=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO]->valor;
            */ 
            if(array_key_exists(FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE,$factura->detalles))
                $predatos->comercializadora=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE]->valor;
            if(array_key_exists(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA,$factura->detalles))
                $predatos->distribuidora=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA]->valor;
            if(array_key_exists(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS,$factura->detalles))
                $predatos->cups=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS]->valor;
            if(array_key_exists(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO,$factura->detalles))
                $predatos->peaje=$factura->detalles[FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO]->valor;
        }
        
        if($factura->fecha_inicio!=null)
        {
            $predatos->fecha_inicio=$factura->fecha_inicio->format('d/m/Y');
        }
        
        if($factura->fecha_final!=null)
        {
            $predatos->fecha_final=$factura->fecha_final->format('d/m/Y');
        }
    }
    
    // Completamos los datos con la ъltima factura.
    //if(($predatos->referencia_contrato==null) || ($predatos->distribuidora==null) || ($predatos->cups==null) || ($predatos->peaje==null))
    if(($predatos->distribuidora==null) || ($predatos->cups==null) || ($predatos->peaje==null))
    {
        getUltimosAtributos();
        //$predatos->referencia_contrato=precargaValor(FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO);
        $predatos->comercializadora=precargaValor(FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE);
        $predatos->distribuidora=precargaValor(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA);
        $predatos->cups=precargaValor(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS);
        $predatos->peaje=precargaValor(FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO);
    }
    
    return $predatos;
}

function procesarParteFactura()
{
    global $variablesPart,$page,$factura_ctl,$factura;
    
    
    $res=procesarDatosEntrada();
    if($res!==true)
        return $res;
        
    $numero_factura=$factura->num_factura;
    
    $comercializadoras=$factura_ctl->leerListaOpciones('ELECTRICIDAD_COMERCIALIZADORAS');
    //$distribuidoras=$factura_ctl->leerListaOpciones('ELECTRICIDAD_DISTRIBUIDORAS');
    $peajes=$factura_ctl->leerListaOpciones('ELECTRICIDAD_PEAJE_ACCESO');
    
    $precarga=precargarDatosFactura();
    
    if($precarga->comercializadora!=null)
    {
        $comercializadoras[$precarga->comercializadora]->seleccionado=true;
    }
    /*
    if($precarga->distribuidora!=null)
    {
        $distribuidoras[$precarga->distribuidora]->seleccionado=true;
    }
    */
    if($precarga->peaje!=null)
    {
        $peajes[$precarga->peaje]->seleccionado=true;
    }
    
    
    $subview=__DIR__ ."/views/consumo_form_part_electricidad_datosgenerales_view.php";
    $url_next=$page->self_url(array(ARG_PARTE=>'2', ARG_NUMERO_FACTURA=>$numero_factura));
    $url_prev=$page->self_url(array(ARG_NUMERO_FACTURA=>$numero_factura));
    $color='red';
    $variablesPart = array(
        'subview'               => $subview,                /// Indicamos quй vista va asociada a este formulario parcial se debe incluir
        'urlNext'               => $url_next,               /// Indica la url a la que se debe saltar al pulsar el botуn Siguiente
        'urlPrev'               => $url_prev,               /// Indica la url a la que se debe saltar al pulsar el botуn Anterior
        'comercializadoras'     => $comercializadoras,      /// Lista de compaснas comercializadoras
        //'distribuidoras'        => $distribuidoras,         /// Lista de compaснas distribuidoras
        'peajes'                => $peajes,                 /// Lista de tipos de peajes de acceso a la red elйctrica
        'ColorFondo'            => $color,
        'NumeroFactura'         => $numero_factura,
        //'ReferenciaContrato'    => $precarga->referencia_contrato,
        'Cups'                  => $precarga->cups,
        'FechaInicioPeriodo'    => $precarga->fecha_inicio,
        'FechaFinalPeriodo'     => $precarga->fecha_final
    );
    
    
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