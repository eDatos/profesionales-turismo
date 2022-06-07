<?php

///
/// Parte de DATOS GENERALES de la factura agua.
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

define('FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO','2222.1000.1001');
define('FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL','2222.1000.1002');
define('FACTURA_AGUA_DATOS_GENERALES_NUMERO_FACTURA','2222.1000.1003');
define('FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO','2222.1000.1010');

define('VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO','2222_1000_1001');
define('VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL','2222_1000_1002');
define('VAR_FACTURA_AGUA_DATOS_GENERALES_NUMERO_FACTURA','2222_1000_1003');
define('VAR_FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO','2222_1000_1010');

//$es_nueva=false;
$ult_factura_buscada=false;
$ult_factura=null;


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
    
    if($page->is_post()==false)
    {
        return true;
    }
    
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
            $factura->suministradora=new Suministradora($valores[FACTURA_EMPRESA_SUMINISTRADORA],$valores[FACTURA_TIPO_SUMINISTRO]);
            $factura->num_factura=$valores[FACTURA_NUMERO_FACTURA];
            $factura->tipo=$valores[FACTURA_TIPO_SUMINISTRO];
            if($factura_ctl->abrir_modificar_factura($factura)==false)
            {
                return "Error al reabrir la factura para modificarla.";
            }
            
            // En este punto, la factura es una nueva copia de la existente y no tiene atributos. Generamos los básicos.
            $atributos=array(
                FACTURA_EMPRESA_SUMINISTRADORA => $valores[FACTURA_EMPRESA_SUMINISTRADORA],		// Empresa suministradora
                FACTURA_TIPO_SUMINISTRO => $valores[FACTURA_TIPO_SUMINISTRO],			        // Tipo de suministro
                FACTURA_NUMERO_FACTURA => $valores[FACTURA_NUMERO_FACTURA]					    // Número de factura
            );
            
            $atts_factura=$factura_ctl->generarAtributos($atributos,FASE_RECOGIDA_FACTURA);
            if($factura_ctl->agregar_detalles($factura,$atts_factura)==false)
            {
                return "Error al reabrir la factura para modificarla.";
            }
            
            /+*
            // En este punto, la nueva factura tiene el mismo número y tipo.
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
            *+/
        }
    }
    else
    {
        // La factura no existe, es completamente nueva.
        $es_nueva=true;
    }
    
    /// Recogemos los datos de entrada (DATOS DE LA FACTURA). Sólo si la factura es nueva.
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
            FACTURA_NUMERO_FACTURA => $valores[FACTURA_NUMERO_FACTURA]					    // Número de factura
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

$ult_att=null;

function getUltimosAtributos()
{
    global $factura_ctl,$factura,$ult_att;
    
    $ult_att=$factura_ctl->cargarAtributosPrevios($factura);
    return $ult_att;
}

// Datos para la precarga de campos. Se usan los datos de la factura actual ó los de la última factura similar si existe.
function precargarDatosFactura()
{
    global $factura,$ult_factura,$ult_att;//,$es_nueva;

    $predatos=new \stdClass;
    /*
     $predatos->suministradora=null;
     $predatos->tipoFactura=null;
     */
    $predatos->referencia_contrato=null;
    $predatos->fecha_inicio=null;
    $predatos->fecha_final=null;
    
    /*
    if($factura->suministradora!=null)
    {
        if(($factura->suministradora->nombre!=null)&&($factura->suministradora->tipo!=null))
        {
            $predatos->suministradora$factura->suministradora->nombre;
            $predatos->tipoFactura=$factura->suministradora->tipo;
        }
    }
    */
    
    // Rellenamos los datos con los valores en la factura (si ya existía)
    //if($es_nueva==false)
    if($factura->es_creada==false)
    {
        if($factura->detalles!=null)
        {
            if(array_key_exists(FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO,$factura->detalles))
                $predatos->referencia_contrato=$factura->detalles[FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO]->valor;
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
    
    // Completamos los datos con la última factura.   
    if(USAR_HISTORIAL_SUGERIR_CONSUMO=='S')
    {
        if($predatos->referencia_contrato==null)
        {
            getUltimosAtributos();
            if(array_key_exists(FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO,$ult_att))
            {
                $predatos->referencia_contrato=$ult_att[FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO]->valor;
            }
            else
            {
                getUltimaFactura();
                if(($ult_factura!=null)&&($ult_factura->detalles!=null)&&($ult_factura->detalles[FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO]!=null))
                {
                    $predatos->referencia_contrato=$ult_factura->detalles[FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO]->valor;
                }
            }
        }
        
        /*
        if($predatos->fecha_inicio==null)
        {
            getUltimaFactura();
            if(($ult_factura!=null)&&($ult_factura->fecha_inicio!=null))
            {
                $predatos->fecha_inicio=$ult_factura->fecha_inicio->format('d/m/Y');
            }
        }
        
        if($predatos->fecha_final==null)
        {
            getUltimaFactura();
            if(($ult_factura!=null)&&($ult_factura->fecha_final!=null))
            {
                $predatos->fecha_final=$ult_factura->fecha_final->format('d/m/Y');
            }
        }
        */
    }
    else
    {
        if($predatos->referencia_contrato==null)
        {
            getUltimosAtributos();
            if(array_key_exists(FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO,$ult_att))
                $predatos->referencia_contrato=$ult_att[FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO]->valor;
        }
    }
    
    return $predatos;
}

function procesarParteFactura()
{
    global $variablesPart,$page,$factura;
    
    
    $res=procesarDatosEntrada();
    if($res!==true)
        return $res;
        
    $numero_factura=$factura->num_factura;
    
    $precarga=precargarDatosFactura();
    
    
    $subview=__DIR__ ."/views/consumo_form_part_agua_datosgenerales_view.php";
    $url_next=$page->self_url(array(ARG_PARTE=>'2', ARG_NUMERO_FACTURA=>$numero_factura));
    $url_prev=$page->self_url(array(ARG_NUMERO_FACTURA=>$numero_factura));
    $color='red';
    $variablesPart = array(
        'subview'               => $subview,                /// Indicamos qué vista va asociada a este formulario parcial se debe incluir
        'urlNext'               => $url_next,               /// Indica la url a la que se debe saltar al pulsar el botón Siguiente
        'urlPrev'               => $url_prev,               /// Indica la url a la que se debe saltar al pulsar el botón Anterior
        'ColorFondo'            => $color,
        'NumeroFactura'         => $numero_factura,
        'ReferenciaContrato'    => $precarga->referencia_contrato,
        'FechaInicioPeriodo'    => $precarga->fecha_inicio,
        'FechaFinalPeriodo'     => $precarga->fecha_final
    );
    
    
    return true;
}

?>