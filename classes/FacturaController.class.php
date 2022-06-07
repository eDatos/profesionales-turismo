<?php

require_once(__DIR__."/../config.php");
require_once(__DIR__."/../lib/DateHelper.class.php");
require_once(__DIR__."/../lib/DbHelper.class.php");
require_once(__DIR__."/FacturaDao.class.php");

class FacturaController
{
    /**
     * Objeto DAO a usar en las operaciones.
     * @var FacturaDao $dao
     */
    var $dao;
    
    /**
     * Constructo básico. Inicializa el objeto DAO requerido.
     */
    public function __construct()
    {
        $this->dao = new FacturaDao();
    }
    
    public function crear_nuevo_cuestionario($est_id, $user_id, $suministradora, $numero_factura, $tipo_factura, $guardar)
    {
        $factura = new Factura();

        $factura->id_consumo=null;
        $factura->id_establecimiento=$est_id;
        $factura->id_usuario=$user_id;
        $factura->suministradora=new Suministradora($suministradora,$tipo_factura);
        $factura->fecha=new DateTime('now');
        $factura->fecha_cierre=null;
        $factura->num_factura=$numero_factura;
        $factura->tipo=$tipo_factura;
        $factura->fecha_inicio=new DateTime('now');
        $factura->fecha_final=null;
        $factura->validada='I';
        $factura->detalles=array();
        
        if ($guardar)
        {
            return $this->dao->insertar_maestro_factura($factura);
        }
        return true;
    }
    
    public function actualizar_cuestionario(Factura $factura, array $valores)
    {
        return $this->dao->update_maestro_factura($factura, $valores);
    }
    
    public function actualizar_periodo_factura(Factura $factura)
    {
        return $this->dao->update_periodo_factura($factura);
    }
    
    public function agregar_detalles(Factura $factura, array $atributosFactura)
    {
        return $this->dao->agregarNuevosAtributos($factura,$atributosFactura);
    }
    
    public function generarAtributos(array $atributos, $fase=0)
    {
        return $this->dao->crearListaAtributos($atributos,$fase);
    }
    
    public function cargar_factura($num_factura,$id_establecimiento=null)
    {
        return $this->dao->cargar_factura($num_factura,$id_establecimiento);
    }
    
    public function cargar_ultima_factura($id_estab,$tipo,$num_factura=null)
    {
        return $this->dao->cargar_ultima_factura($id_estab,$tipo,$num_factura);
    }
    
    public function guardarFactura(Factura $factura)
    {
        return $this->dao->guardarFactura($factura);
    }
    
    public function listarSuministradoras()
    {
        return $this->dao->listarSuministradoras();
    }
    
    public function leerListaOpciones($grupo)
    {
        return $this->dao->leerListaOpciones($grupo);
    }
    
    public function leerOpcion($grupo,$codigo)
    {
        return $this->dao->leerOpcion($grupo,$codigo);
    }
    
    public function abrir_modificar_factura(Factura $factura)
    {
        return $this->dao->abrir_modificar_factura($factura);
    }
    
    public function update_atributos(Factura $factura, array $atributosFactura)
    {
        return $this->dao->update_atributos($factura,$atributosFactura);
    }
    
    public function cargarAtributosPrevios(Factura $factura, array $atributosPrevios=null)
    {
        return $this->dao->cargarAtributosPrevios($factura, $atributosPrevios);
    }
    
    public function actualizarFase(Factura $factura)
    {
        return $this->dao->actualizarFase($factura);
    }
    
    public function buscarFacturas($estid, $tipo, $cerrada=null)
    {
        return $this->dao->buscarFacturas($estid,$tipo,$cerrada);
    }
    
    public function cerrarFactura(Factura $factura)
    {
        return $this->dao->cerrarFactura($factura);
    }
    
    public function filtrarFacturas(Array $argumentos)
    {
        return $this->dao->filtrarFacturas($argumentos);
    }
    
    public function buscarFacturasRecientes($estid)
    {
        return $this->dao->buscarFacturasRecientes($estid);
    }
    
    public function borrarFactura(Factura $factura)
    {
        return $this->dao->borrarFactura($factura);
    }
    
    public function limpiarAtributos(Factura $factura, Array $listaOids)
    {
        return $this->dao->limpiarAtributos($factura, $listaOids);
    }
    
    /**
     * 
     * @param Factura $factura Factura a ser modificada (se actualizan sus atributos y la fase de recogida).
     * @param array $datos Array asociativo con valores de atributos recogidos del usuario. Aquellos valores null, se consideran eliminados de la entrada.
     * @param integer $fase Fase de recogida de la factura.
     * @return string|boolean True si todo fue bien, mensaje de error si falló.
     */
    public function cargarDatosEntradaEnFactura(Factura $factura, Array $datos, $fase)
    {
        $fase_anterior=$factura->fase;
        $factura->fase=$fase;
        
        $listaOids=array();
        $listaOidsDesaparecidos=array();
        foreach($datos as $clave => $valor)
        {
            if($datos[$clave]!=null)
            {
                $listaOids[]=$clave;
            }
            else
            {
                unset($factura->detalles[$clave]);
                $listaOidsDesaparecidos[]=$clave;
            }
        }
        
        $nuevosAtributosFactura=array();
        
        $OidDao=new OIDDao($this->dao->db);
        $atributos=$OidDao->leerListaOids($listaOids);
        foreach($atributos as $atributo)
        {
            $nuevosAtributosFactura[$atributo->oid]=AtributoFactura::crear($atributo->oid,$atributo->tipo,$datos[$atributo->oid],$factura->fase);
        }
        
        $listaOidsDesaparecidos=array_keys($datos);
        if($this->dao->cargarDatosEntradaEnFactura($factura, $nuevosAtributosFactura, $listaOidsDesaparecidos)==false)
        {
            $factura->fase=$fase_anterior;
            return "Error durante la grabación. Operación interrumpida.";
        }
        
        return true;
    }
    
    public function iniciarCapturaFactura(Factura &$factura)
    {
        global $page;
        
        if($page->is_post()==false)
        {
            return true;
        }
        
        // oids (atributos comunes a todas las facturas)
        define ('FACTURA_EMPRESA_SUMINISTRADORA','0000.1000');
        define ('FACTURA_TIPO_SUMINISTRO','0000.1001');
        define ('FACTURA_NUMERO_FACTURA','0000.1002');
        
        // variables de entrada del formulario
        define ('VAR_FACTURA_EMPRESA_SUMINISTRADORA','0000_1000');
        define ('VAR_FACTURA_TIPO_SUMINISTRO','0000_1001');
        define ('VAR_FACTURA_NUMERO_FACTURA','0000_1002');
        
        $valores=array();
        $valores[FACTURA_EMPRESA_SUMINISTRADORA]=$page->request_post_or_get(VAR_FACTURA_EMPRESA_SUMINISTRADORA, /*$factura->suministradora->nombre*/ NULL);
        $valores[FACTURA_TIPO_SUMINISTRO]=$page->request_post_or_get(VAR_FACTURA_TIPO_SUMINISTRO, /*$factura->suministradora->tipo*/ NULL);
        $valores[FACTURA_NUMERO_FACTURA]=$page->request_post_or_get(VAR_FACTURA_NUMERO_FACTURA, /*$factura->num_factura*/ NULL);
        
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
                //$factura->suministradora=new Suministradora($valores[FACTURA_EMPRESA_SUMINISTRADORA],$valores[FACTURA_TIPO_SUMINISTRO]);
                //$factura->num_factura=$valores[FACTURA_NUMERO_FACTURA];
                //$factura->tipo=$valores[FACTURA_TIPO_SUMINISTRO];
                
                // Lo único que puede haber cambiado es el nombre de la suministradora. El tipo es el mismo...
                if($valores[FACTURA_EMPRESA_SUMINISTRADORA]!=null)
                {
                    $factura->suministradora->nombre=$valores[FACTURA_EMPRESA_SUMINISTRADORA];
                }
                
                if($this->abrir_modificar_factura($factura)==false)
                {
                    return "Error al reabrir la factura para modificarla.";
                }
                
                // En este punto, la factura es una nueva copia de la existente y no tiene atributos. Generamos los básicos.
                $atributos=array(
                    FACTURA_EMPRESA_SUMINISTRADORA => $valores[FACTURA_EMPRESA_SUMINISTRADORA],		// Empresa suministradora
                    FACTURA_TIPO_SUMINISTRO => $valores[FACTURA_TIPO_SUMINISTRO],			        // Tipo de suministro
                    FACTURA_NUMERO_FACTURA => $valores[FACTURA_NUMERO_FACTURA]					    // Número de factura
                );
                
                $atts_factura=$this->generarAtributos($atributos,FASE_RECOGIDA_FACTURA);
                if($this->agregar_detalles($factura,$atts_factura)==false)
                {
                    return "Error al reabrir la factura para modificarla.";
                }
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
            
            $factura->detalles=$this->generarAtributos($atributos,FASE_RECOGIDA_FACTURA);
            
            if($this->guardarFactura($factura)==false)
            {
                /// TODO: Controlar de manera adecuada el error.
                return "Fallo al crear la factura.";
            }
        }
        
        return true;
    }
    
    public function crearNuevaFactura()
    {
        global $page;
        
        if($page->is_post()==false)
        {
            return "Fallo al crear la factura. Petición incorrecta.";
        }
        
        // oids (atributos comunes a todas las facturas)
        define ('FACTURA_EMPRESA_SUMINISTRADORA','0000.1000');
        define ('FACTURA_TIPO_SUMINISTRO','0000.1001');
        define ('FACTURA_NUMERO_FACTURA','0000.1002');
        
        // variables de entrada del formulario
        define ('VAR_FACTURA_EMPRESA_SUMINISTRADORA','0000_1000');
        define ('VAR_FACTURA_TIPO_SUMINISTRO','0000_1001');
        define ('VAR_FACTURA_NUMERO_FACTURA','0000_1002');
        
        $valores=array();
        $valores[FACTURA_EMPRESA_SUMINISTRADORA]=$page->request_post_or_get(VAR_FACTURA_EMPRESA_SUMINISTRADORA, NULL);
        $valores[FACTURA_TIPO_SUMINISTRO]=$page->request_post_or_get(VAR_FACTURA_TIPO_SUMINISTRO, NULL);
        $valores[FACTURA_NUMERO_FACTURA]=$page->request_post_or_get(VAR_FACTURA_NUMERO_FACTURA, NULL);
        
        $establecimiento = $page->get_current_establecimiento();
        
        $factura=new Factura();
        $factura->id_consumo=null;
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
        
        $factura->detalles=$this->generarAtributos($atributos,FASE_RECOGIDA_FACTURA);
        
        if($this->guardarFactura($factura)==false)
        {
            /// TODO: Controlar de manera adecuada el error.
            return "Fallo al crear la factura.";
        }
        
        return $factura;
    }
}