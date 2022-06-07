<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/DateHelper.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/FacturaController.class.php");

//$page = PWETPageHelper::start_page(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION), array(PAGE_ALOJA_INDEX, PAGE_ALOJA_FORM));
$page = PWETPageHelper::start_page(PERMS_ANY);
$page->set_page_path(array(PAGE_CONSUMO_FORM));

define('ARG_NUMERO_FACTURA', 'numero_factura');
$numero_factura = $page->request_post_or_get(ARG_NUMERO_FACTURA, NULL);

define('ARG_PARTE', 'part');
$parte = $page->request_post_or_get(ARG_PARTE, NULL);

$accion=$page->request_post(ARG_OP);

//// TODO: En el futuro, puede ser necesario usar esta página en modo sólo lectura.
define('ARG_RDONLY','rdonly');
$rd_only = $page->request_post_or_get(ARG_RDONLY, false);

define('OP_CONFIRMAR_ENVIO_FACTURA', 'cef');
define('OP_ENVIO_FACTURA', 'cierre');
define('ARG_DATA', 'var_json');
define('LOCAL_VAR_FACTURA_TIPO_SUMINISTRO','0000_1001');

//// TODO: Gestionar correctamente el error ==> hay que identificar si el error se ha producido en un POST o en el GET inicial.

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));

$establecimiento = $page->get_current_establecimiento();

if((isset($accion))&&($accion==OP_CONFIRMAR_ENVIO_FACTURA))
{
    respuestaAjax();
}

/// 1. Obtener el establecimiento y mes/año con el que se va a trabajar
if($es_admin)
{
	if($establecimiento == null)
	{
		/// Mostrar las pagina de busqueda de establecimiento.
		$selected_estid = $page->select_establecimiento($page->self_url(NULL, TRUE));
		$page->set_current_establecimiento($selected_estid);
	}
}

$establecimiento = $page->get_current_establecimiento();

if ($establecimiento == null)
{
    //// TODO: Cambiar esta dirección de error.
    gestionarError("No se ha definido el establecimiento para el que mostrar el cuestionario.");
}

$factura_ctl = new FacturaController();

$factura=null;
if($numero_factura!=null)
{
    $factura=$factura_ctl->cargar_factura($numero_factura,$establecimiento->id_establecimiento);
    if($factura==null)
    {
        // Factura no encontrada.
        gestionarError("Factura Nº ".$numero_factura." no encontrada.");
    }
}

$res=false;

if($factura!=null)
{
    if($factura->esta_cerrado())
    {
        if($es_admin)
        {
            // Sólo el administrador puede editar una factura ya cerrada.
            $factura->fecha_cierre=null;
            if($factura_ctl->guardarFactura($factura)==false)
            {
                $page->abort_with_error(PAGE_HOME, 'Fallo al reabrir la factura '.$factura->num_factura.' del establecimiento con código '.$factura->id_establecimiento.'.');
            }
        }
        else
        {
            $page->abort_with_error(PAGE_HOME, 'No es posible modificar la facturar '.$factura->num_factura.'. Por favor, contacte con el Instituto Canario de Estadística para notificar cualquier cambio en esta factura.');
        }
    }
}


// $factura!=null sólo si se ha pedido una factura explícitamente ($numero_factura!=null).


// Control paranóico ==> sólo admitimos GET cuando no viene informado el número de factura.
if($parte==null)
{
    if($page->is_get()==false)
    {
        gestionarError("Error en la entrada de datos. Petición incorrecta.");
    }
}
else
{
    /*
    if($page->is_post()==false)
    {
        gestionarError("Error en la entrada de datos. Petición incorrecta.");
    }
    */
}


if(($factura==null)&&($parte==null))
{
    // Iniciamos la creación de una nueva factura.
    require_once(__DIR__."/consumo_form_part_inicial.php");
    $res=procesarParteFactura();
}
else
{
    // Si pedimos una factura explícitamente
    if($parte==null)
    {
        // Se ha solicitado una factura en concreto...(se intenta modificar una factura existente)
        // La petición es un GET para iniciar la modificación de una factura existente.
    }    
    
    if($factura==null)
    {
        if($parte!=1)
        {
            gestionarError("Error en la entrada de datos. La factura no se encuentra.");
        }
        else
        {
            // En este punto ya podemos decidir qué tipo de factura se va a cumplimentar.
            $tipo_factura=$page->request_post_or_get(LOCAL_VAR_FACTURA_TIPO_SUMINISTRO, NULL);
        }
    }
    else
    {
        //$tipo_factura=$factura->tipo;
        $tipo_factura=$page->request_post_or_get(LOCAL_VAR_FACTURA_TIPO_SUMINISTRO, $factura->tipo);
    }
    
    if($parte==OP_ENVIO_FACTURA)
    {
        $res=cerrar_factura($tipo_factura);
    }
    else
    {
        $form_parts=array();
        switch ($tipo_factura)
        {
            case 'ELECTRICIDAD':
                $form_parts=array(
                '1' => __DIR__."/consumo_form_part_electricidad_datosgenerales.php",
                //'2' => __DIR__."/consumo_form_part_electricidad_datossuministro.php",
                '2' => __DIR__."/consumo_form_part_electricidad_datosconsumo.php",
                '3' => __DIR__."/consumo_form_part_electricidad_datosconsumo2.php",
                '4' => __DIR__."/consumo_form_part_electricidad_resumen.php"
                    );
                break;
            case 'AGUA':
                $form_parts=array(
                '1' => __DIR__."/consumo_form_part_agua_datosgenerales.php",
                '2' => __DIR__."/consumo_form_part_agua_datossuministro.php",
                '3' => __DIR__."/consumo_form_part_agua_resumen.php"
                //'3' => __DIR__."/consumo_form_part_agua_datosconsumo.php",
                //'4' => __DIR__."/consumo_form_part_agua_resumen.php"
                    );
                break;
            case 'PROPANO':
                $form_parts=array();
                break;
            case 'BUTANO':
                $form_parts=array();
                break;
            case 'GASOIL':
                $form_parts=array();
                break;
            default:
                gestionarError("Error en la entrada de datos. Tipo de factura no reconocida.");
                break;
        }
        
        if(array_key_exists($parte,$form_parts))
        {
            require_once($form_parts[$parte]);
            $res=procesarParteFactura();
            $variablesPart['parte']=$parte;
            $variablesPart['numpartes']=sizeof($form_parts);
            $iconoSuministro=$factura_ctl->leerOpcion('ICONOS_SUMINISTRO',$factura->tipo);
            $variablesPart['iconoSuministro']=(array_key_exists($factura->tipo,$iconoSuministro))?$iconoSuministro[$factura->tipo]:null;       /// Icono del tipo de suministro
        }
        else
        {
            // Código de pruebas
            $subview=__DIR__ ."/views/consumo_form_part_no_implementado_view.php";
            $res=true;
            $color='red';
            $variablesPart=array();
            $variablesPart['subview']=$subview;
            // Código de pruebas
        }
        
        /*
         switch($parte)
         {
         case '1':
         require_once(__DIR__."/consumo_form_part_electricidad_datosgenerales.php");
         $res=procesarParteFactura();
         break;
         case '2':
         require_once(__DIR__."/consumo_form_part_electricidad_datossuministro.php");
         $res=procesarParteFactura();
         break;
         case '3':
         $url_next=$page->self_url(array(ARG_PARTE=>'4', ARG_NUMERO_FACTURA=>$numero_factura));
         $mensaje="Está ud. viendo la parte #3";
         $color='blue';
         break;
         case '4':
         $url_next=$page->self_url(array(ARG_PARTE=>'5', ARG_NUMERO_FACTURA=>$numero_factura));
         $mensaje="Está ud. viendo la parte #4";
         $color='cyan';
         break;
         default:
         // Código de pruebas
         $subview=__DIR__ ."/views/consumo_form_part_no_implementado_view.php";
         $res=true;
         $color='red';
         $variablesPart=array();
         // Código de pruebas
         break;
         }
         */
    }
    
}



if($res!==true)
{
    gestionarError($res);
}

/// Preparamos las variables que necesita la vista.
/// Empezando por las variables usadas en la parte general.
$colorBordeFormParcial='green';
$variables = array(
    'ColorBordeFormParcial'             => $colorBordeFormParcial,
);

/// Y agregamos las variables necesarias para la vista del formulario parcial actual.
if(isset($variablesPart))
{
    $variables+=$variablesPart;
}


/// Render de la pagina
$page->render( "consumo_form_view.php", $variables);

$page->end_session();
exit(0);


function respuestaAjax()
{
    global $page,$rd_only,$es_admin,$establecimiento;
    
    //header("Content-Type: text/html; charset=UTF-8");
    header('Content-Type: application/json');
    
    $data = $page->request_post(ARG_DATA);
    
    $dato=new \stdClass;
    
    if(($page->is_post()==false)||($data==null)||($establecimiento==null)||($data['numfactura']==null)||($data[ARG_ESTID]!=$establecimiento->id_establecimiento))
    {
        $dato->error=true;
        $dato->mensaje=utf8_encode('Error en la entrada de datos. Petición incorrecta.');
    }
    else
    {
        $factura_ctl = new FacturaController();
        $factura=$factura_ctl->cargar_factura($data['numfactura'],$establecimiento->id_establecimiento);
        if($factura==null)
        {
            $dato->error=false;
            $dato->mensaje=utf8_encode('ok');
            $dato->factura_existe=false;
        }
        else
        {
            // Estas comprobaciones se hacen sólo para informar correctamente al usuario del motivo del eventual error.
            // Son redundantes porque se volverán a realizar posteriormente en el envío real de la factura.
            if($rd_only)
            {
                // Error.
                $dato->error=true;
                $dato->mensaje=utf8_encode('Se está intentando modificar datos de sólo lectura.');
            }
            else
            {
                if($factura->esta_cerrado())
                {
                    if($es_admin)
                    {
                        // La Fuerza es poderosa en él...
                        $dato->error=false;
                        $dato->mensaje=utf8_encode('ok');
                    }
                    else
                    {
                        // Error. El usuario no está autorizado a modificar las facturas ya enviadas.
                        $dato->error=true;
                        $dato->mensaje=utf8_encode('Operación no autorizada. La factura ya ha sido enviada y no es modificable. Para realizar cambios a una factura ya enviada, debe contactar con el Instituto Canario de Estadística.');
                    }
                }
            }
            $dato->factura_existe=true;
        }
    }

    echo json_encode($dato);
    $page->end_session();
    exit(0);
}

function cerrar_factura($tipo_factura)
{
    global $page,$es_admin,$factura_ctl,$factura,$numero_factura,$variablesPart;
    
    switch ($tipo_factura)
    {
        case 'ELECTRICIDAD':
            define('FASE_RECOGIDA_FACTURA',5);
            define('FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA','1111.3000.1001');
            define('FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS','1111.3000.1002');
            define('FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR','1111.3000.1100');
            define('FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL','1111.6000.1001.1001.1001.1002');
            define('FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL','1111.6000.1001.1002.1001.1003');
            define('VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA','1111_3000_1001');
            define('VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS','1111_3000_1002');
            define('VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR','1111_3000_1100');
            define('VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL','1111_6000_1001_1001_1001_1002');
            define('VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL','1111_6000_1001_1002_1001_1003');
            
            $valores=array();
            //$valores[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA, NULL);
            //$valores[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS, NULL);
            $valores[FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL, NULL);
            $valores[FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL, NULL);
            $valores[FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR]=$page->request_post_or_get(VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR, NULL);
            
            $listaOids=array();
            foreach($valores as $clave => $valor)
            {
                if($valores[$clave]!=null)
                    $listaOids[]=$clave;
            }
            
            $OidDao=new OIDDao($factura_ctl->dao->db);
            $atributos=$OidDao->leerListaOids($listaOids);
            foreach($atributos as $atributo)
            {
                $factura->detalles[$atributo->oid]=AtributoFactura::crear($atributo->oid,$atributo->tipo,$valores[$atributo->oid],FASE_RECOGIDA_FACTURA);
            }
            
            if($factura_ctl->agregar_detalles($factura,$factura->detalles)==false)
            {
                return "Error durante la grabación. Operación interrumpida.";
            }
            if($factura_ctl->cerrarFactura($factura)==false)
            {
                return "Error durante la grabación. Operación interrumpida.";
            }
            
            break;
        case 'AGUA':
            define('FASE_RECOGIDA_FACTURA',5);
            define('FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE','2222.7000.1000.1001');
            define('FACTURA_AGUA_RESUMEN_TOTAL_PAGAR','2222.6000.1000.1002');
            define('VAR_FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE','2222_7000_1000_1001');
            define('VAR_FACTURA_AGUA_RESUMEN_TOTAL_PAGAR','2222_6000_1000_1002');
            
            $valores=array();
            $valores[FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE]=$page->request_post_or_get(VAR_FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE, NULL);
            $valores[FACTURA_AGUA_RESUMEN_TOTAL_PAGAR]=$page->request_post_or_get(VAR_FACTURA_AGUA_RESUMEN_TOTAL_PAGAR, NULL);
            
            $listaOids=array();
            foreach($valores as $clave => $valor)
            {
                if($valores[$clave]!=null)
                    $listaOids[]=$clave;
            }
            
            $OidDao=new OIDDao($factura_ctl->dao->db);
            $atributos=$OidDao->leerListaOids($listaOids);
            foreach($atributos as $atributo)
            {
                $factura->detalles[$atributo->oid]=AtributoFactura::crear($atributo->oid,$atributo->tipo,$valores[$atributo->oid],FASE_RECOGIDA_FACTURA);
            }
            
            if($factura_ctl->agregar_detalles($factura,$factura->detalles)==false)
            {
                return "Error durante la grabación. Operación interrumpida.";
            }
            if($factura_ctl->cerrarFactura($factura)==false)
            {
                return "Error durante la grabación. Operación interrumpida.";
            }
            
            break;
        case 'PROPANO':
            return "Error en la entrada de datos. Tipo de factura no reconocida.";
            break;
        case 'BUTANO':
            return "Error en la entrada de datos. Tipo de factura no reconocida.";
            break;
        case 'GASOIL':
            return "Error en la entrada de datos. Tipo de factura no reconocida.";
            break;
        default:
            gestionarError("Error en la entrada de datos. Tipo de factura no reconocida.");
            break;
    }
    
    $variablesPart=array();
    $variablesPart['esAdmin']=$es_admin;
    $variablesPart['subview']=__DIR__ ."/views/consumo_form_factura_completada_view.php";
    $variablesPart['urlNext']=($es_admin)?PAGE_HOME:PAGE_CONSUMO_INDEX;
    $variablesPart['NumeroFactura']=$numero_factura;
    $variablesPart['estid']=$factura->id_establecimiento;
    $iconoSuministro=$factura_ctl->leerOpcion('ICONOS_SUMINISTRO',$factura->tipo);
    $variablesPart['iconoSuministro']=(array_key_exists($factura->tipo,$iconoSuministro))?$iconoSuministro[$factura->tipo]:null;       /// Icono del tipo de suministro
    return true;
}

function gestionarError($mensaje)
{
    global $page;
    
    $page->abort_with_error(PAGE_HOME, $mensaje);
}

?>