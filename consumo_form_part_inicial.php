<?php

///
/// Parte de DATOS GENERALES de la factura elйctrica.
///

global $variablesPart;

// oids (atributos comunes a todas las facturas)
define ('FACTURA_EMPRESA_SUMINISTRADORA','0000.1000');
define ('FACTURA_TIPO_SUMINISTRO','0000.1001');
define ('FACTURA_NUMERO_FACTURA','0000.1002');

// variables del formulario
define ('VAR_FACTURA_EMPRESA_SUMINISTRADORA','0000_1000');
define ('VAR_FACTURA_TIPO_SUMINISTRO','0000_1001');
define ('VAR_FACTURA_NUMERO_FACTURA','0000_1002');

function procesarParteFactura()
{
    global $subview,$factura_ctl,$factura,$variablesPart,$page;
    
    $suministradora=null;
    $tipoFactura=null;
    $numero_factura=null;
    if($factura!=null)
    {
        // Si la factura ya existe, precargamos campos.
        if($factura->suministradora!=null)
        {
            if(($factura->suministradora->nombre!=null)&&($factura->suministradora->tipo!=null))
            {
                $suministradora=$factura->suministradora->nombre;
                $tipoFactura=$factura->suministradora->tipo;
            }
        }
        $numero_factura=$factura->num_factura;
    }
    
    // Preparamos los datos necesarios para la vista del formulario parcial
    $subview=__DIR__ ."/views/consumo_form_part_inicial_view.php";              // Indicamos quй vista va asociada a este formulario parcial
    $argumentos=array(ARG_PARTE=>'1');
    if(isset($numero_factura))
    {
        $argumentos[ARG_NUMERO_FACTURA]=$numero_factura;
    }
    $url_next=$page->self_url($argumentos);
    
    $suministradoras=$factura_ctl->listarSuministradoras();
    $mensaje="Estб ud. viendo la parte #1";
    $color='red';
    
    $variablesPart = array(
        'subview'               => $subview,                /// Indicamos quй vista va asociada a este formulario parcial se debe incluir
        'urlNext'               => $url_next,               /// Indica la url a la que se debe saltar al pulsar el botуn Siguiente
        'suministradoras'       => $suministradoras,        /// Lista de compaснas suministradoras y los tipos de suministro que proveen
        'empresaSuministradora' => $suministradora,         /// Nombre de la empresa suministradora (si la factura ya existe)
        'NumeroFactura'         => $numero_factura,         /// Nъmero de la factura (si la factura ya existe)
        'tipoFactura'           => $tipoFactura,            /// Tipo de factura (si la factura ya existe)
        'idEstablecimiento'     => $page->get_current_establecimiento()->id_establecimiento,
        'ColorFondo'            => $color,
        'texto'                 => $mensaje
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