<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/FacturaController.class.php");

//$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR));
$page = PWETPageHelper::start_page(PERMS_ANY);
$page->set_page_path(array(PAGE_CONSUMO_LIST));

$es_admin=$page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC));
if($es_admin)
{
    /// Deseleccionar el establecimiento de trabajo si lo hubiera.
    $page->set_current_establecimiento(null);
}
else
{
    $establecimiento = $page->get_current_establecimiento();
}

//// TODO: En el futuro, puede ser necesario usar esta página en modo sólo lectura.
define('ARG_RDONLY','rdonly');
$rd_only = $page->request_post_or_get(ARG_RDONLY, false);

define('OP_CONFIRMAR_ENVIO_FACTURA', 'cef');
define('OP_ENVIO_FACTURA', 'cierre');
define('ARG_DATA', 'var_json');
define('LOCAL_VAR_FACTURA_TIPO_SUMINISTRO','0000_1001');

define('BTN_OPERATION', 'operationBtn');
define('OP_MODIFICAR_FACTURA', 'Modificar');
define('OP_BORRAR_FACTURA', 'Eliminar');

define('ARG_PARTE', 'part');
define("ARG_TIPO_BUSQUEDA", "tipo_busqueda");
define("ARG_NOMBRE_EST", "nombre");
define("ARG_CODIGO_EST", "codigo");

define('ARG_SELEC_EST', 'selecest');

define('OP_SEARCH', $page->is_post());
define('ARG_SUMINISTRADORA','suministradora');
define('ARG_TIPO_SUMINISTRO','tipoSuministro');
define('ARG_NUMERO_FACTURA', 'numero_factura');
define('ARG_MES_FACTURA','mesFactura');
define('ARG_ANO_FACTURA','anoFactura');
define('ARG_FECHA_FACTURACION_DESDE','fecha_facturacion_desde');
define('ARG_FECHA_FACTURACION_HASTA','fecha_facturacion_hasta');

$factura_ctl = new FacturaController();

/****************************************************
********* ALTERNATIVA CON AJAX **********************
****************************************************/
define('OP_BUSCAR_FACTURAS', 'obf');
$accion=$page->request_post(ARG_OP);
if((isset($accion))&&($accion==OP_BUSCAR_FACTURAS))
{
    busquedaAjax();
}

/*
 * Esperamos una petición con unos datos JSON como los siguientes:
        {
			'<?= ARG_SUMINISTRADORA ?>' : xxx,           // Opcional
			'<?= ARG_TIPO_SUMINISTRO ?>' : xxx,          // Opcional
			'<?= ARG_NUMERO_FACTURA ?>' : xxx,           // Opcional
			'<?= ARG_MES_FACTURA ?>' : xxx,              // Opcional
			'<?= ARG_ANO_FACTURA ?>' : xxx,              // Opcional
			
			/// Sólo administradores /// 
			'<?= ARG_SELEC_EST ?>' : xxx,              // Obligatorio
			'<?= ARG_CODIGO_EST ?>' : xxx              // Obligatorio si ARG_SELEC_EST=='codigo'
			'<?= ARG_NOMBRE_EST ?>' : xxx              // Obligatorio si ARG_SELEC_EST=='nombre'
			/// Sólo administradores /// 
        }
 * Se generará una respuesta JSON como la siguiente:
        {
			'error' : xxx,           // xxx={true,false}
			'mensaje' : xxx,         // xxx={'ok','error mensaje #1',...,'error mensaje #N'}
			'resultados' : [         // Sólo si error==false
			     {
			         'id_consumo' : xxx,
			         'id_establecimiento' : xxx,
			         'nombre_establecimiento' : xxx,
			         'id_usuario' : xxx,
			         'num_factura' : xxx,
			         'fecha' : xxx,                      // Fecha en formato d/m/Y
			         'tipo' : xxx,
			         'cerrada' : xxx                     // xxx={true,false}
			     },
			     ...
			]
        }
 */
function busquedaAjax()
{
    global $page,$factura_ctl,$es_admin,$establecimiento;
    
    /// NOTA: La función AJAX del JQuery parece que siempre usa UTF-8, independientemente de lo que se ponga en la propiedad contentType.
    /// Además la función PHP json_encode, sólo acepta UTF-8 (es posible que en versiones recientes ya lo haga)
    /// Más información ==> https://www.theerrormessage.com/2013/10/weird-characters-transmitted-to-and-from-server-through-jquery-ajax-call/
    /// La entrada con potenciales caracteres especiales debe ser pasada por la función PHP utf8_decode
    /// La salida con potenciales caracteres especiales debe ser pasada por la función PHP utf8_encode
    header("Content-Type: text/html; charset=UTF-8");
    
    $data = $page->request_post(ARG_DATA);
    
    $dato=new \stdClass;
    
    if(($page->is_post()==false)||($data==null))
    {
        $dato->error=true;
        $dato->mensaje=utf8_encode('Error en la entrada de datos. Petición incorrecta.');
        echo json_encode($dato);
        $page->end_session();
        exit(0);
    }
    
    $argBusqueda=array();
    
    if($es_admin)
    {
        // A los administradores se les permite el acceso a facturas de cualquier establecimiento.
        switch($data[ARG_SELEC_EST])
        {
            case 'todos':
                break;
            case 'nombre':
                $argBusqueda['est_nombre']=utf8_decode($data[ARG_NOMBRE_EST]);
                break;
            case 'codigo':
                $argBusqueda['est_codigo']=$data[ARG_CODIGO_EST];
                break;
            default:
                $dato->error=true;
                $dato->mensaje=utf8_encode('Error en la entrada de datos. Petición incorrecta.');
                echo json_encode($dato);
                $page->end_session();
                exit(0);
        }
    }
    else
    {
        if($establecimiento == null)
        {
            $dato->error=true;
            $dato->mensaje=utf8_encode('Error en la entrada de datos. Petición incorrecta.');
            echo json_encode($dato);
            $page->end_session();
            exit(0);
        }
        
        $argBusqueda['est_id']=$establecimiento->id_establecimiento;
    }
    
    $argBusqueda[ARG_SUMINISTRADORA]=utf8_decode($data[ARG_SUMINISTRADORA]);
    $argBusqueda[ARG_TIPO_SUMINISTRO]=$data[ARG_TIPO_SUMINISTRO];
    $argBusqueda[ARG_NUMERO_FACTURA]=$data[ARG_NUMERO_FACTURA];
    $argBusqueda[ARG_MES_FACTURA]=$data[ARG_MES_FACTURA];
    $argBusqueda[ARG_ANO_FACTURA]=$data[ARG_ANO_FACTURA];
    $argBusqueda[ARG_FECHA_FACTURACION_DESDE]=$data[ARG_FECHA_FACTURACION_DESDE];
    $argBusqueda[ARG_FECHA_FACTURACION_HASTA]=$data[ARG_FECHA_FACTURACION_HASTA];
    
    $dato->error=false;
    $dato->mensaje=utf8_encode('ok');
    $dato->resultados=$factura_ctl->filtrarFacturas($argBusqueda);
    $dato->mierda=utf8_encode("CANAL GESTIÓN LANZAROTE");
    echo json_encode($dato);
    $page->end_session();
    exit(0);
}


/****************************************************
 ********* ALTERNATIVA CON AJAX **********************
 ****************************************************/









$resultados=array();


if($page->is_post())
{
    $accion=$page->request_post(ARG_OP);
    if($accion==OP_BORRAR_FACTURA)
    {
        $dato=borrarFacturaAjax();
        echo json_encode($dato);
        $page->end_session();
        exit(0);
    }
    
    $argBusqueda=array();
    if($es_admin)
    {
        // A los administradores se les permite el acceso a facturas de cualquier establecimiento.
        $query_tipo_busqueda = $page->request_post(ARG_SELEC_EST, NULL);
        switch($query_tipo_busqueda)
        {
            case 'todos':
                break;
            case 'nombre':
                $argBusqueda['est_nombre']=$page->request_post(ARG_NOMBRE_EST, '');
                break;
            case 'codigo':
                $argBusqueda['est_codigo']=$page->request_post(ARG_CODIGO_EST, '');
                break;
            /*
             case 'sesion':
            // Si usamos el establecimiento seleccionado en el objeto de sesión.
            if($establecimiento == null)
            {
                gestionarError("No se ha definido el establecimiento correctamente.");
            }
            break;
            */
            default:
                gestionarError("Petición incorrecta.");
                break;
        }
    }
    else
    {
        if($establecimiento == null)
        {
            //// TODO: Cambiar esta dirección de error.
            gestionarError("No se ha definido el establecimiento correctamente.");
        }
        
        $argBusqueda['est_id']=$establecimiento->id_establecimiento;
    }
    
    /*
    $query_tipo_busqueda = $page->request_post(ARG_TIPO_BUSQUEDA, '');
    
    if($query_tipo_busqueda=='nombre')
    {
        $argBusqueda['est_nombre']=$page->request_post(ARG_NOMBRE_EST, '');
    }
    if($query_tipo_busqueda=='codigo')
    {
        $argBusqueda['est_codigo']=$page->request_post(ARG_CODIGO_EST, '');
    }
    if($query_tipo_busqueda=='factura')
    {
        if(($es_admin==false)&&($establecimiento == null))
        {
            //// TODO: Cambiar esta dirección de error.
            gestionarError("No se ha definido el establecimiento correctamente.");
        }
        
        $argBusqueda['est_id']=$establecimiento->id_establecimiento;
    }
    */
    
    $argBusqueda[ARG_SUMINISTRADORA]=$page->request_post(ARG_SUMINISTRADORA, NULL);
    $argBusqueda[ARG_TIPO_SUMINISTRO]=$page->request_post(ARG_TIPO_SUMINISTRO, NULL);
    $argBusqueda[ARG_NUMERO_FACTURA]=$page->request_post(ARG_NUMERO_FACTURA, NULL);
    $argBusqueda[ARG_MES_FACTURA]=$page->request_post(ARG_MES_FACTURA, NULL);
    $argBusqueda[ARG_ANO_FACTURA]=$page->request_post(ARG_ANO_FACTURA, NULL);
    $argBusqueda[ARG_FECHA_FACTURACION_DESDE]=$page->request_post(ARG_FECHA_FACTURACION_DESDE, NULL);
    $argBusqueda[ARG_FECHA_FACTURACION_HASTA]=$page->request_post(ARG_FECHA_FACTURACION_HASTA, NULL);
    
    $resultados=$factura_ctl->filtrarFacturas($argBusqueda);
}








/*
define("ARG_DIA","dia_mes");
define("ARG_NOTAS","notas");

// BTN_OPERATION: Nombre del botón que define la operación.
define("BTN_OPERATION", "operationBtn");
// OP_INSERTAR: Valor de botón que indica que se trata de una inserción.
define("OP_INSERTAR" , "Insertar nuevo plazo");
// OP_Modificar: Valor de botón que indica que se trata de una modificación.
define("OP_MODIFICAR" , "Modificar");
// OP_Eliminar: Valor de botón que indica que se trata de una eliminación.
define("OP_ELIMINAR" , "Eliminar");
// OP_Eliminar_Todos: Valor de botón que indica que se trata de una eliminación múltiple.
define("OP_ELIMINAR_TODOS" , "Eliminar todos");

$error=$page->request_post_or_get("error", NULL);
$operacion=$page->request_post_or_get(ARG_OP, NULL);
*/

$establecimiento = $page->get_current_establecimiento();

if(($es_admin==false)&&($establecimiento == null))
{
    //// TODO: Cambiar esta dirección de error.
    gestionarError("No se ha definido el establecimiento correctamente.");
}

/*
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
    gestionarError("No se ha definido el establecimiento correctamente.");
}
*/
			
$factura_ctl = new FacturaController();

$suministradoras=$factura_ctl->listarSuministradoras();
$iconosSuministros=$factura_ctl->leerListaOpciones('ICONOS_SUMINISTRO');


/// Preparacion de la vista de la pagina
$variables = array(
    'esAdmin'                               => $es_admin,
    'estab_codigo'                          => ($establecimiento!=null)?$establecimiento->id_establecimiento:'',
    'estab_nombre'                          => ($establecimiento!=null)?$establecimiento->nombre_establecimiento:'',
    'mierda'                                => htmlspecialchars("x<&$%'ç>ñÁ",ENT_QUOTES),
    'data'                                  => $resultados,
    'navToUrl'                              => PAGE_CONSUMO_PRINT,
    'urlModificacion'                       => PAGE_CONSUMO_FORM,
    'urlBorrado'                            => PAGE_CONSUMO_LIST,
    'rdOnly'                                => $rd_only,
    'suministradoras'                       => $suministradoras,        /// Lista de compañías suministradoras y los tipos de suministro que proveen
    'iconosSuministros'                     => $iconosSuministros       /// Lista de iconos de los distintos tipos de suministros
);

/// Render de la pagina
$page->render( "consumo_list_view.php", $variables );


$page->end_session();


function borrarFacturaAjax()
{
    global $page,$rd_only,$es_admin,$establecimiento;
    
    //header("Content-Type: text/html; charset=UTF-8");
    header('Content-Type: application/json');
    
    $data = $page->request_post(ARG_DATA);
    
    $dato=new \stdClass;
    
    /// Comprobaciones de seguridad.
    // Sólo admitimos POST.
    // Los usuarios no administradores deben tener establecido el establecimiento.
    if(($page->is_post()==false)||(($establecimiento==null)&&(!$es_admin)))
    {
        $dato->error=true;
        $dato->mensaje=utf8_encode('Error en la entrada de datos. Petición incorrecta.');
        return $dato;
    }
    
    /// Datos de entrada necesarios:
    // [1] Número de factura
    // [2] código de establecimiento de la factura a buscar (si es administrador, se permite que difiera del valor almacenado en sesión)
    if(($data==null)||($data['numfactura']==null)||($data[ARG_ESTID]==null))
    {
        $dato->error=true;
        $dato->mensaje=utf8_encode('Error en la entrada de datos. Petición incorrecta.');
        return $dato;
    }
    
    if((!$es_admin)&&($data[ARG_ESTID]!=$establecimiento->id_establecimiento))
    {
        $dato->error=true;
        $dato->mensaje=utf8_encode('Operación no autorizada. Petición incorrecta.');
        return $dato;
    }

    if($rd_only)
    {
        $dato->error=true;
        $dato->mensaje=utf8_encode('Se está intentando modificar datos de sólo lectura. Petición incorrecta.');
        return $dato;
    }
    
    $factura_ctl = new FacturaController();
    $factura=$factura_ctl->cargar_factura($data['numfactura'],$data[ARG_ESTID]);
    if($factura==null)
    {
        // Si la factura no existe, la operación finaliza con éxito.
        $dato->error=false;
        $dato->mensaje=utf8_encode('ok');
    }
    
    if((!$es_admin)&&($factura->esta_cerrado()))
    {
        $dato->error=true;
        //$dato->mensaje=utf8_encode('La factura está cerrada y no es modificable. Petición incorrecta.');
        $dato->mensaje=utf8_encode('Operación no autorizada. La factura ya ha sido enviada y no es modificable. Para realizar cambios a una factura ya enviada, debe contactar con el Instituto Canario de Estadística.');
        return $dato;
    }
    
    // Procedemos al borrado de la factura.
    if($factura_ctl->borrarFactura($factura))
    {
        $dato->error=false;
        $dato->mensaje=utf8_encode('ok');
    }
    else
    {
        $dato->error=true;
        $dato->mensaje=utf8_encode('Error interno durante la operación.');
    }
    return $dato;
}

function gestionarError($mensaje)
{
    global $page;
    
    $page->abort_with_error(PAGE_HOME, $mensaje);
}

?>