<?php

require_once(__DIR__."/../config.php");
require_once(__DIR__."/../lib/DateHelper.class.php");
require_once(__DIR__."/../lib/DbHelper.class.php");
require_once(__DIR__."/../lib/RowDbIterator.class.php");

// Tipos de factura conocidos
define('FACTURA_ELECTRICIDAD','ELECTRICIDAD');
define('FACTURA_AGUA','AGUA');
define('FACTURA_GASOIL','GASOIL');
define('FACTURA_PROPANO','PROPANO');
define('FACTURA_BUTANO','BUTANO');

define('TIPO_FACTURA_DETALLE_NULL',null);               // El atributo no contiene valor.
define('TIPO_FACTURA_DETALLE_DATE','DATE');             // El atributo contiene una fecha.
define('TIPO_FACTURA_DETALLE_TEXT','TEXT');             // El atributo contiene un texto (máx 150 caracteres).
define('TIPO_FACTURA_DETALLE_NUMBER','NUMBER');         // El atributo contiene un número en formato texto (ejemplos: 10 20.5 -2 -0.0005).
define('TIPO_FACTURA_DETALLE_MONEY','MONEY');           // El atributo contiene una cantidad en euros en formato texto (debería ir con dos decimales).

// NOTA:
//  Las fechas se leen en formato DD/MM/YYYY usando la forma: TO_CHAR(VALOR,'DD/MM/YYYY')
//  Las fechas se graban en formato DD/MM/YYYY usando la forma: TO_DATE(:valor,'DD/MM/YYYY')


/**
 * Clase que representa un OID.
 * @author SCC,SL
 *
 */
class OID
{
    /**
     * Identificador único del atributo.
     * @var string $oid
     */
    var $oid;
    
    /**
     * Tipo del valor del atributo. NULL indica que el atributo no tiene ningún valor asociado más allá de su mera presencia.
     * @var string $tipo
     */
    var $tipo;
    
    /**
     * Descripción corta del atributo.
     * @var string $descripcion_corta
     */
    var $descripcion_corta;
    
    /**
     * Descripción larga del atributo.
     * @var string $descripcion_larga
     */
    var $descripcion_larga;
    
    /**
     * Indica si este atributo es de cumplimentación obligatoria(true) o no(false).
     * @var boolean $obligatorio
     */
    var $obligatorio;
    
    /**
     * Crea un nuevo objeto OID a partir de los argumentos.
     * @param string $oid Identificador único del OID
     * @param string $tipo Tipo del OID
     * @param string $desc_corta Descripción corta del OID.
     * @param string $desc_larga Descripción larga del OID.
     * @param string $obligatorio Indica si el OID es obligatorio(S) o no (N).
     * @return OID Nuevo objeto OID creado.
     */
    public static function crear($oid,$tipo,$desc_corta,$desc_larga,$obligatorio)
    {
        $newOid=new OID();
        $newOid->oid=$oid;
        $newOid->tipo=$tipo;
        $newOid->descripcion_corta=$desc_corta;
        $newOid->descripcion_larga=$desc_larga;
        $newOid->obligatorio=($obligatorio=='S');
        return $newOid;
    }
}

/**
 * Esta clase es innecesaria...
 * @author SCC,SL
 *
 */
class OIDDao
{
    /**
     * Referencia al objeto que permite el acceso a BDD.
     * @var Istac_Sql $db
     */
    var $db;
    
    /**
     * Constructor con soporte para sobrecarga.
     */
    public function __construct()
    {
        $params = func_get_args();
        $num_params = func_num_args();
        $funcion_constructor ='__construct'.$num_params;
        if (method_exists($this,$funcion_constructor))
        {
            call_user_func_array(array($this,$funcion_constructor),$params);
        }
    }
    
    /**
     * Constructor básico. Inicializa el soporte de conexión a la BDD.
     * La conexión a BDD se establece cuando se hace la primera consulta.
     */
    public function __construct0()
    {
        $this->db = new Istac_Sql();
    }
    
    /**
     * Constructor donde se le pasa el objeto de acceso a la BDD.
     */
    public function __construct1($db)
    {
        $this->db = $db;
    }
    
    /**
     * Carga un array asociativo con todos los OIDs que comiencen por el prefijo indicado.
     * @param string $prefijo Prefijo de los OIDs buscados. Por defecto, null que devuelve todos los OIDs.
     * @return OID[] Array asociativo de todos los OIDs encontrados.
     */
    public function cargarOIDs($prefijo=null)
    {
        $sql=(empty($prefijo)) ?
            "SELECT OID,TIPO,DESC_CORTA,DESC_LARGA,OBLIGATORIO FROM TB_OIDS ORDER BY OID":
            "SELECT OID,TIPO,DESC_CORTA,DESC_LARGA,OBLIGATORIO FROM TB_OIDS WHERE OID LIKE '".$prefijo."%' ORDER BY OID";
        $this->db->query($sql);
        
        $resultado=array();
        while ($this->db->next_record())
        {
            $oid=$this->db->f("oid");
            $resultado[$oid]=OID::crear($oid,$this->db->f("tipo"),$this->db->f("desc_corta"),$this->db->f("desc_larga"),$this->db->f("obligatorio"));
        }
        
        return $resultado;
    }
    
    /**
     * Carga un array asociativo con todos los OIDs solicitados.
     * @param array $listaOids Lista de códigos de los OIDs solicitados.
     * @return OID[] Array asociativo de todos los OIDs encontrados o null si hay error.
     */
    public function leerListaOids(array $listaOids)
    {
        $resultado=array();
        $codigosOids="";
        foreach($listaOids as $oid)
        {
            $codigosOids.=",'".$oid."'";
        }
        if(empty($codigosOids)==false)
        {
            $codigosOids=substr($codigosOids,1);
            $sql="SELECT OID,TIPO,DESC_CORTA,DESC_LARGA,OBLIGATORIO FROM TB_OIDS WHERE OID IN(".$codigosOids.") ORDER BY OID";
            $this->db->query($sql);
            while ($this->db->next_record())
            {
                $oid=$this->db->f("oid");
                $resultado[$oid]=OID::crear($oid,$this->db->f("tipo"),$this->db->f("desc_corta"),$this->db->f("desc_larga"),$this->db->f("obligatorio"));
            }
            
            if(count($resultado)!=count($listaOids))
            {
                // TODO: Mandar mensaje al log de errores.
                return null;
            }
        }
        return $resultado;
    }
}

/**
 * Clase que representa las opciones a usar en los campos de selección de los formularios que sirven para cumplimentar las facturas.
 * @author SCC,SL
 *
 */
class SelectOption
{
    //GRUPO,CODIGO,ORDEN,DESC_CORTA,DESC_LARGA,DEFECTO
    
    /**
     * Identificador del grupo que aglutina los valores relacionados.
     * @var string $grupo
     */
    var $grupo;
    
    /**
     * Valor a usar en las facturas
     * @var string $codigo
     */
    var $codigo;
    
    /**
     * Orden de presentación del valor (cuando proceda).
     * @var integer $orden
     */
    var $orden;
    
    /**
     * Descripción corta del valor (cuando proceda).
     * @var string $desc_corta
     */
    var $desc_corta;
    
    /**
     * Descripción larga del valor (cuando proceda).
     * @var string $desc_larga
     */
    var $desc_larga;
    
    /**
     * Indica (cuando proceda), si el se trata de un valor por defecto(S) o no (N).
     * @var boolean $defecto
     */
    var $defecto;
    
    /**
     * Indica si este es el valor en uso en la factura actual.
     * @var boolean $seleccionado
     */
    var $seleccionado;
    
    public function __construct($Grupo, $Codigo, $Orden, $Desc_corta, $Desc_larga, $Defecto, $Seleccionado)
    {
        $this->grupo=$Grupo;
        $this->codigo=$Codigo;
        $this->orden=$Orden;
        $this->desc_corta=$Desc_corta;
        $this->desc_larga=$Desc_larga;
        $this->defecto=$Defecto;
        $this->seleccionado=$Seleccionado;
    }
}

/**
 * Clase que describe una empresa suministradora y su tipo de suministro.
 * @author SCC,SL
 *
 */
class Suministradora
{
    /**
     * Nombre de la empresa suministradora
     * @var string $nombre
     */
    var $nombre;
    
    /**
     * Tipo de suministro
     * @var string $tipo
     */
    var $tipo;
    
    public function __construct($Nombre, $Tipo)
    {
        $this->nombre=$Nombre;
        $this->tipo=$Tipo;
    }
}

/**
 * Clase que representa un detalle de una factura.
 * @author SCC,SL
 *
 */
class AtributoFactura
{
    /**
     * Identificador único del atributo.
     * @var string $oid
     */
    var $oid;
    
    /**
     * Tipo del valor del atributo. NULL indica que el atributo no tiene ningún valor asociado más allá de su mera presencia.
     * @var string $tipo
     */
    var $tipo;
    
    /**
     * Indica si este atributo es de cumplimentación obligatoria(S) o no(N).
     * @var string $obligatorio
     */
    //var $obligatorio;     // NO CREO QUE SIRVA DE NADA TENER ESTE ELEMENTO AQUÍ.
    
    /**
     * Valor del atributo de la factura.
     * @var mixed $valor
     */
    var $valor;
    
    /**
     * Número de paso en el que se recogió este atributo de la factura.
     * @var integer $fase
     */
    var $fase;
    
    public function load()
    {
        switch($this->tipo)
        {
            case TIPO_FACTURA_DETALLE_NULL:
                return 'NULL valor';
            case TIPO_FACTURA_DETALLE_DATE:
                return "TO_CHAR(VALOR,'DD/MM/YYYY') valor";
            case TIPO_FACTURA_DETALLE_MONEY:
            case TIPO_FACTURA_DETALLE_NUMBER:
                return 'VALOR';
            default:
                return 'VALOR';
        }
    }
    
    public function save($nombre)
    {
        switch($this->tipo)
        {
            case TIPO_FACTURA_DETALLE_NULL:
                return 'NULL';
            case TIPO_FACTURA_DETALLE_DATE:
                //return 'TO_DATE(:'.$nombre.",'DD/MM/YYYY')";ç
                return ':'.$nombre;
            case TIPO_FACTURA_DETALLE_MONEY:
            case TIPO_FACTURA_DETALLE_NUMBER:
                return ':'.$nombre;
            default:
                return ':'.$nombre;
        }
    }
    
    public function getValor()
    {
        switch($this->tipo)
        {
            case TIPO_FACTURA_DETALLE_NULL:
                return 'NULL';
            case TIPO_FACTURA_DETALLE_DATE:
                return $this->valor->format('d/m/Y');
            case TIPO_FACTURA_DETALLE_MONEY:
                //return floatval($this->valor);
                return (string)$this->valor;
            case TIPO_FACTURA_DETALLE_NUMBER:
                //return intval($this->valor);
                return (string)$this->valor;
            default:
                return (string)$this->valor;
        }
    }
    
    /**
     * Crea un nuevo objeto AtributoFactura a partir de los argumentos.
     * @param string $oid
     * @param string $tipo
     * @param string $valor
     * @param integer $fase
     * @return AtributoFactura Nuevo objeto AtributoFactura creado.
     */
    public static function crear($oid,$tipo,$valor,$fase)
    {
        $atributo=new AtributoFactura();
        $atributo->oid=$oid;
        $atributo->tipo=$tipo;
        $atributo->fase=$fase;
        if($valor==null)
            $atributo->valor=null;
        else
        {
            switch($atributo->tipo)
            {
                case TIPO_FACTURA_DETALLE_NULL:
                    $atributo->valor=null;
                    break;
                case TIPO_FACTURA_DETALLE_DATE:
                    $atributo->valor=DateTime::createFromFormat('d/m/Y', $valor);
                    break;
                case TIPO_FACTURA_DETALLE_MONEY:
                case TIPO_FACTURA_DETALLE_NUMBER:
                    $atributo->valor=$valor;
                    break;
                default:
                    $atributo->valor=$valor;
                    break;
            }
        }
        return $atributo;
    }
    
    public function prepareInsertDetalle(Factura $factura, $nombre_tabla=null)
    {
        return ($nombre_tabla==null) ? "INSERT INTO TB_CONSUMOS_DETALLE".$this->prepareInsertDetalleParcial($factura) : "INSERT INTO ".$nombre_tabla.$this->prepareInsertDetalleParcial($factura);
    }
    
    public function prepareInsertDetalleParcial(Factura $factura)
    {
        $sql="(ID_CONSUMO,OID,VALOR,FASE) VALUES(:id_consumo,:oid,".$this->save('valor').",:fase)";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $params[':oid']=(string)$this->oid;
        $params[':valor']=$this->getValor();
        $params[':fase']=$this->fase;
        $sql = DbHelper::prepare_sql($sql,$params);
        
        return $sql;
    }
    
    public function prepareInsertDetalleHist(Factura $factura)
    {
        $sql="INSERT INTO TB_CONSUMOS_DETALLE_HIST(ID_CONSUMO,OID,VALOR) VALUES(:id_consumo,:oid,".$this->save('valor').")";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $params[':oid']=(string)$this->oid;
        $params[':valor']=$this->getValor();
        $sql = DbHelper::prepare_sql($sql,$params);
        
        return $sql;
    }
    
    public function prepareUpdateDetalle(Factura $factura)
    {
        $sql="UPDATE TB_CONSUMOS_DETALLE SET VALOR=:valor, FASE=:fase WHERE ID_CONSUMO=:id_consumo AND OID=:oid";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $params[':oid']=(string)$this->oid;
        $params[':valor']=(string)$this->getValor();
        $params[':fase']=$this->fase;
        return DbHelper::prepare_sql($sql,$params);
    }
    
    public function prepareUpdateDetalleHist(Factura $factura)
    {
        $sql="UPDATE TB_CONSUMOS_DETALLE_HIST SET VALOR=:valor WHERE ID_CONSUMO=:id_consumo AND OID=:oid";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $params[':oid']=(string)$this->oid;
        $params[':valor']=$this->getValor();
        $sql = DbHelper::prepare_sql($sql,$params);
        
        return $sql;
    }
}

/**
 * Clase que representa una factura (consumo).
 * @author SCC,SL
 *
 */
class Factura
{
    /**
     * <p>Identificador único interno del registro (obtenido a partir de la secuencia TB_CONSUMOS_ID_SEQ).
     * Si es NULL, el consumo aún no ha sido grabado a BDD.</p>
     * @var string $id_consumo
     */
    var $id_consumo;
    
    /**
     * Identificador del establecimiento asociado a la factura.
     * @var string $id_establecimiento
     */
    var $id_establecimiento;
    
    /**
     * Identificador del usuario que crea el registro
     * @var string $id_usuario
     */
    var $id_usuario;
    
    /**
     * Fecha de recepción del cuestionario/consumo.
     * @var DateTime $fecha
     */
    var $fecha;
    
    /**
     * Fecha en la que el usuario envia el cuestionario/consumo (cierra el cuestionario) o NULL si el cuestionario aún no ha sido enviado/cerrado.
     * @var DateTime $fecha_cierre
     */
    var $fecha_cierre;
    
    /**
     * Número de factura.
     * @var string $num_factura
     */
    var $num_factura;
    
    /**
     * Tipo de factura (electricidad, agua, combustible, etc.).
     * @var string $tipo
     */
    var $tipo;
    
    /**
     * Empresa suministradora y tipo.
     * @var Suministradora $suministradora
     */
    var $suministradora;
    
    /*
     * Fecha de alta de la cuenta de cotización.
     * @var DateTime $fecha_inicio
     */
    var $fecha_inicio;
    
    /*
     * Fecha de finalización del periodo de facturación o NULL si la factura no tiene periodo asignado.
     * @var DateTime $fecha_final
     */
    var $fecha_final;
    
    /**
     * Estado de la factura: A=factura validada ó I=no validada
     * @var string $validada
     */
    var $validada;
    
    /**
     * Número de paso en el que se encuentra la introducción de la factura.
     * @var integer $fase
     */
    var $fase;
    
    /**
     * Indica si este objeto ha sido creado (true) o leído desde la BDD (false).
     * @var boolean $es_creada
     */
    var $es_creada;
    
    /**
     * Detalles de la factura.
     * @var array $detalles
     */
    var $detalles;
    
    public function __construct()
    {
        $this->es_creada=true;
    }
    
    /**
     * Indica si es nuevo (aún no se ha grabado en la tabla TB_CONSUMOS).
     * @return boolean True si la factura es nueva y aún no ha sido grabada en BDD. False en caso contrario.
     */
    public function es_nuevo()
    {
        return ($this->id_consumo == null);
    }
    
    /**
     * Indica si el cuestionario está cerrado (finalizado) o no.
     * @return boolean True si la factura está cerrada (fecha de cierre no nula). False en caso contrario.
     */
    public function esta_cerrado()
    {
        return ($this->fecha_cierre != null);
    }
    
    /**
     * Agrega un nuevo atributo a la lista de atributos de la factura.º
     * @param AtributoFactura $atributo
     */
    public function addAtributo(AtributoFactura $atributo)
    {
        if($this->detalles==null)
        {
            $this->detalles=array();
        }
        $this->detalles[$atributo->oid]=$atributo;
    }
    
//     public static function crearFactura($id_estab)
//     {
//         $factura=new Factura();
//         $factura->id_consumo=null;
//         $factura->id_establecimiento=$id_estab;
//         //$factura->id_usuario=$page->get_current_userid();
//         //$factura->suministradora=new Suministradora($valores[FACTURA_EMPRESA_SUMINISTRADORA],$valores[FACTURA_TIPO_SUMINISTRO]);
//         $factura->fecha = new DateTime('now');
//         $factura->fecha_cierre=null;
//         //$factura->num_factura=$valores[FACTURA_NUMERO_FACTURA];
//         //$factura->tipo=$valores[FACTURA_TIPO_SUMINISTRO];
//         $factura->fecha_inicio=new DateTime('now');
//         $factura->fecha_final=null;
//         $factura->validada='I';
//        
//         return $factura;
//     }
}

class ResultadoBusquedaFacturas
{
    var $id_consumo;
    var $id_establecimiento;
    var $nombre_establecimiento;
    var $id_usuario;
    var $fecha;
    var $num_factura;
    var $tipo;
    var $cerrada;
}

/**
 * Clase para la persistencia de los objetos de tipo Factura.
 * @author SCC,SL
 *
 */
class FacturaDao
{
    /**
     * Referencia al objeto que permite el acceso a BDD.
     * @var Istac_Sql $db
     */
    var $db;
    
    /**
     * Constructo básico. Inicializa el soporte de conexión a la BDD.
     * La conexión a BDD se establece cuando se hace la primera consulta.
     */
    public function __construct()
    {
        $this->db = new Istac_Sql();
    }
    
    public function insertar_maestro_factura(Factura $factura)
    {
        $sql="INSERT INTO TB_CONSUMOS(ID_ESTABLECIMIENTO,ID_USUARIO,SUMINISTRADORA,FECHA,FECHA_CIERRE,NUM_FACTURA,TIPO,FECHA_INICIO,FECHA_FINAL,VALIDADA) VALUES(:id_estab,:id_usuario,:suministradora,to_date(:fecha,'dd/mm/yyyy'),to_date(:fecha_cierre,'dd/mm/yyyy'),:num_factura,:tipo,to_date(:fecha_inicio,'dd/mm/yyyy'),to_date(:fecha_final,'dd/mm/yyyy'),:validada)";
        $params=array();
        //$params[':id_consumo']=(string)$factura->id_consumo;          // Este identificador se genera automáticamente durante la inserción.
        $params[':id_estab']=(string)$factura->id_establecimiento;
        if($factura->id_usuario!=null)
        {
            $params[':id_usuario']=(string)$factura->id_usuario;
        }
        $params[':suministradora']=(string)$factura->suministradora->nombre;
        if($factura->fecha!=null)
        {
            $params[':fecha']=(string)$factura->fecha->format('d/m/Y');
        }
        if($factura->fecha_cierre!=null)
        {
            $params[':fecha_cierre']=(string)$factura->fecha_cierre->format('d/m/Y');
        }
        $params[':num_factura']=(string)$factura->num_factura;
        $params[':tipo']=(string)$factura->tipo;
        if($factura->fecha_inicio!=null)
        {
            $params[':fecha_inicio']=(string)$factura->fecha_inicio->format('d/m/Y');
        }
        if($factura->fecha_final!=null)
        {
            $params[':fecha_final']=(string)$factura->fecha_final->format('d/m/Y');
        }
        $params[':validada']=(string)$factura->validada;
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        return ($this->db->affected_rows()==1);
    }
    
    public function update_maestro_factura(Factura $factura, array $valores)
    {
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $params[':fase']=(string)$factura->fase;
        $campos=array();
        foreach($valores as $columna => $valor)
        {
            $clave=":v_".strtolower($columna);
            $params[$clave]=$valor;
            $campos[]=strtoupper($columna)."=".$clave;
        }
        if(count($campos)>0)
        {
            $sql="UPDATE TB_CONSUMOS SET FASE=:fase, ".implode(", ",$campos)." WHERE ID_CONSUMO=:id_consumo";
            krsort($params);
            $sql = DbHelper::prepare_sql($sql,$params);
            $this->db->query($sql);
            return ($this->db->affected_rows()==1);
        }
        return true;
    }
    
    public function update_periodo_factura(Factura $factura)
    {
        $campos=array();
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $params[':fase']=(string)$factura->fase;
        if($factura->fecha_inicio!=null)
        {
            $params[':fecha_inicio']=(string)$factura->fecha_inicio->format('d/m/Y');
            $campos[]="FECHA_INICIO=TO_DATE(:fecha_inicio,'DD/MM/YYYY')";
        }
        if($factura->fecha_final!=null)
        {
            $params[':fecha_final']=(string)$factura->fecha_final->format('d/m/Y');
            $campos[]="FECHA_FINAL=TO_DATE(:fecha_final,'DD/MM/YYYY')";
        }
        if(count($campos)>0)
        {
            $sql="UPDATE TB_CONSUMOS SET FASE=:fase, ".implode(", ",$campos)." WHERE ID_CONSUMO=:id_consumo";
            krsort($params);
            $sql = DbHelper::prepare_sql($sql,$params);
            $this->db->query($sql);
            return ($this->db->affected_rows()==1);
        }
        return true;
    }
    
    public function update_atributos(Factura $factura, array $atributosFactura)
    {
        if($this->db->beginTrans())
        {
            $succes=true;
            foreach($atributosFactura as $att)
            {
                $sqlpart1="SET VALOR=:valor, FASE=:fase WHERE ID_CONSUMO=:id_consumo AND OID=:oid";
                $sqlpart2="SET VALOR=:valor WHERE ID_CONSUMO=:id_consumo AND OID=:oid";
                $params=array();
                $params[':id_consumo']=(string)$factura->id_consumo;
                $params[':oid']=(string)$att->oid;
                $params[':valor']=(string)$att->getValor();
                $params[':fase']=$att->fase;
                $sqlpart1=DbHelper::prepare_sql($sqlpart1,$params);
                $sqlpart2=DbHelper::prepare_sql($sqlpart2,$params);
                $sql = "UPDATE TB_CONSUMOS_DETALLE ".$sqlpart1;
                if(($this->db->queryTrans($sql))&&($this->db->affected_rows()==1))
                {
                    // El atributo ya existe en la tabla TB_CONSUMOS_DETALLE_HIST, debemos actualizarlo también.
                    $sql = "UPDATE TB_CONSUMOS_DETALLE_HIST ".$sqlpart2;
                    if(($this->db->queryTrans($sql)==false)||($this->db->affected_rows()!=1))
                    {
                        $succes=false;
                        break;
                    }
                }
                else
                {
                    $succes=false;
                    break;
                }
            }
            if($succes)
            {
                // La operación finalizó con éxito.
                foreach($atributosFactura as $att)
                {
                    $factura->detalles[$att->oid]=$att;
                }
                return $this->db->commit();
            }
            else
            {
                // Falló la operación.
                $this->db->rollback();
            }
        }
        return false;
    }
    
    /**
     * Carga la factura completa a partir del número de factura.
     * @param string $num_factura Número de factura.
     * @param string $id_estab Código de establecimiento
     * @return NULL|Factura El objeto Factura cargado con la información (incluídos los atributos) o null si no se encuentra o hay errores.
     */
    public function cargar_factura($num_factura,$id_estab=null)
    {
        $factura=null;
        
        $sql="SELECT ID_CONSUMO,ID_ESTABLECIMIENTO,ID_USUARIO,SUMINISTRADORA,TO_CHAR(FECHA,'DD/MM/YYYY') fr,TO_CHAR(FECHA_CIERRE,'DD/MM/YYYY') fc,TIPO,TO_CHAR(FECHA_INICIO,'DD/MM/YYYY') fi,TO_CHAR(FECHA_FINAL,'DD/MM/YYYY') ff,FASE,VALIDADA FROM TB_CONSUMOS WHERE NUM_FACTURA=:num_factura";
        $params=array();
        $params[':num_factura']=(string)$num_factura;
        if(!empty($id_estab))
        {
            $sql.=" AND ID_ESTABLECIMIENTO=:id_estab";
            $params[':id_estab']=(string)$id_estab;
        }
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        if($this->db->next_record())
        {
            $factura=new Factura();
            $factura->id_consumo=$this->db->f("id_consumo");
            $factura->id_establecimiento=$this->db->f("id_establecimiento");
            $factura->id_usuario=$this->db->f("id_usuario");
            $factura->fecha = DateTime::createFromFormat('d/m/Y', $this->db->f('fr'));
            if($this->db->f('fc') != null)
            {
                $factura->fecha_cierre = DateTime::createFromFormat('d/m/Y', $this->db->f('fc'));
            }
            
            $factura->num_factura=$num_factura;
            $factura->tipo=$this->db->f("tipo");
            $factura->suministradora=new Suministradora($this->db->f("suministradora"),$factura->tipo);
            if($this->db->f('fi') != null)
            {
                $factura->fecha_inicio = DateTime::createFromFormat('d/m/Y', $this->db->f('fi'));
            }
            if($this->db->f('ff') != null)
            {
                $factura->fecha_final = DateTime::createFromFormat('d/m/Y', $this->db->f('ff'));
            }
            
            $factura->fase=($this->db->f('fase') != null)? $this->db->f("fase") : 0;
            $factura->validada=$this->db->f("validada");
            $factura->es_creada=false;
            
            $this->cargarAtributos($factura);
        }
        
        return $factura;
    }

    /**
     * Carga la factura cerrada más reciente del establecimiento y tipo indicado.
     * @param string $id_estab Identificación del establecimiento
     * @param string $tipo Tipo de factura buscada
     * @param string $num_factura Si no es null, indica que se excluye la factura con ese número de la búsqueda.
     * @return NULL|Factura
     */
    public function cargar_ultima_factura($id_estab,$tipo,$num_factura=null)
    {
        $factura=null;
        
        $sql="SELECT ID_CONSUMO,ID_ESTABLECIMIENTO,ID_USUARIO,SUMINISTRADORA,TO_CHAR(FECHA,'DD/MM/YYYY') fr,TO_CHAR(FECHA_CIERRE,'DD/MM/YYYY') fc,NUM_FACTURA,TIPO,TO_CHAR(FECHA_INICIO,'DD/MM/YYYY') fi,TO_CHAR(FECHA_FINAL,'DD/MM/YYYY') ff,FASE,VALIDADA FROM TB_CONSUMOS WHERE FECHA_CIERRE IS NOT NULL AND ID_ESTABLECIMIENTO=:id_estab AND TIPO=:tipo";
        $params=array();
        $params[':id_estab']=(string)$id_estab;
        $params[':tipo']=(string)$tipo;
        if(!empty($num_factura))
        {
            $sql.=" AND NUM_FACTURA<>:num_factura";
            $params[':num_factura']=(string)$num_factura;
        }
        $sql.=" ORDER BY FECHA DESC";
        
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        if($this->db->next_record())
        {
            $factura=new Factura();
            $factura->id_consumo=$this->db->f("id_consumo");
            $factura->id_establecimiento=$this->db->f("id_establecimiento");
            $factura->id_usuario=$this->db->f("id_usuario");
            $factura->suministradora=new Suministradora($this->db->f("suministradora"),$factura->tipo);
            $factura->fecha = DateTime::createFromFormat('d/m/Y', $this->db->f('fr'));
            if($this->db->f('fc') != null)
            {
                $factura->fecha_cierre = DateTime::createFromFormat('d/m/Y', $this->db->f('fc'));
            }
            
            $factura->num_factura=$this->db->f("num_factura");
            $factura->tipo=$this->db->f("tipo");
            if($this->db->f('fi') != null)
            {
                $factura->fecha_inicio = DateTime::createFromFormat('d/m/Y', $this->db->f('fi'));
            }
            if($this->db->f('ff') != null)
            {
                $factura->fecha_final = DateTime::createFromFormat('d/m/Y', $this->db->f('ff'));
            }
            
            $factura->fase=($this->db->f('fase') != null)? $this->db->f("fase") : 0;
            $factura->validada=$this->db->f("validada");
            $factura->es_creada=false;
            
            $this->cargarAtributos($factura);
        }
        
        return $factura;
    }    
    
    /**
     * Guarda la factura completa en BDD.
     * @param Factura $factura Objeto Factura con la información a almacenar en BDD.
     * @return boolean True si se guardó la factura correctamente. False en caso contratio.
     */
    public function guardarFactura(Factura $factura)
    {
        if($this->db->beginTrans())
        {
            $sqlInsert="INSERT INTO TB_CONSUMOS(ID_ESTABLECIMIENTO,ID_USUARIO,SUMINISTRADORA,FECHA,FECHA_CIERRE,NUM_FACTURA,TIPO,FECHA_INICIO,FECHA_FINAL,FASE,VALIDADA) VALUES(:id_estab,:id_usuario,:suministradora,to_date(:fecha,'dd/mm/yyyy'),to_date(:fecha_cierre,'dd/mm/yyyy'),:num_factura,:tipo,to_date(:fecha_inicio,'dd/mm/yyyy'),to_date(:fecha_final,'dd/mm/yyyy'),:fase,:validada)";
            $params=array();
            //$params[':id_consumo']=(string)$factura->id_consumo;          // Este identificador se genera automáticamente durante la inserción.
            $params[':id_estab']=(string)$factura->id_establecimiento;
            if($factura->id_usuario!=null)
            {
                $params[':id_usuario']=(string)$factura->id_usuario;
            }
            $params[':suministradora']=(string)$factura->suministradora->nombre;
            if($factura->fecha!=null)
            {
                $params[':fecha']=(string)$factura->fecha->format('d/m/Y');
            }
            else
            {
                $params[':fecha']=NULL;
            }
            if($factura->fecha_cierre!=null)
            {
                $params[':fecha_cierre']=(string)$factura->fecha_cierre->format('d/m/Y');
            }
            else
            {
                $params[':fecha_cierre']=NULL;
            }
            $params[':num_factura']=(string)$factura->num_factura;
            $params[':tipo']=(string)$factura->tipo;
            if($factura->fecha_inicio!=null)
            {
                $params[':fecha_inicio']=(string)$factura->fecha_inicio->format('d/m/Y');
            }
            else
            {
                $params[':fecha_inicio']=NULL;
            }
            if($factura->fecha_final!=null)
            {
                $params[':fecha_final']=(string)$factura->fecha_final->format('d/m/Y');
            }
            else
            {
                $params[':fecha_final']=NULL;
            }
            $params[':fase']=(string)$factura->fase;
            $params[':validada']=(string)$factura->validada;
            
            // Superimportante porque la función DbHelper::prepare_sql está mal hecha y reemplaza mal cuando una de las claves es prefijo de otra.
            krsort($params);
            
            $sqlInsert = DbHelper::prepare_sql($sqlInsert,$params);
            
            if($this->db->queryTrans($sqlInsert))
            {
                if($this->db->affected_rows()==0)
                {
                    // No se insertó porque el registro ya existía.
                    $sqlBorrado="DELETE FROM TB_CONSUMOS WHERE ID_ESTABLECIMIENTO=:id_estab AND NUM_FACTURA=:num_factura";
                    
                    $sqlBorrado = DbHelper::prepare_sql($sqlBorrado,$params);
                    if(($this->db->queryTrans($sqlBorrado)==false)||($this->db->affected_rows()==0))
                    {
                        // Falló el borrado. Abortamos.
                        $this->db->rollback();
                        return false;
                    }
                    
                    if(($this->db->queryTrans($sqlInsert)==false)||($this->db->affected_rows()!=1))
                    {
                        // Falló de nuevo. Abortamos.
                        $this->db->rollback();
                        return false;
                    }
                    
                    //// TODO: El borrado en cascada de los atributos de la anterior factura no funciona!!!
                    $sql="DELETE FROM TB_CONSUMOS_DETALLE WHERE ID_CONSUMO=:id_consumo";
                    if($factura->id_consumo!=null)
                    {
                        $params=array();
                        $params[':id_consumo']=(string)$factura->id_consumo;
                        $sql = DbHelper::prepare_sql($sql,$params);
                        if($this->db->queryTrans($sql)==false)
                        {
                            // Falló de nuevo. Abortamos.
                            $this->db->rollback();
                            return false;
                        }
                    }
                }
                
                // Obtenemos la clave primaria.
                if($this->leerCodigoFactura($factura))
                {
                    if($this->insertAllDetalles($factura))
                    {
                        // La operación finalizó con éxito.
                        return $this->db->commit();
                    }
                }
                
                // Falló la operación.
                $this->db->rollback();
            }
        }
        return false;
    }
    
    /**
     * Elimina un consumo borrando el registro de la tabla maestra lo que desencadena un borrado en cascada de registros en la tabla detalle.
     * @param Factura $factura
     * @return boolean Devuelve true si todo fue bien y false en caso contrario.
     */
    private function borrar_registro_factura(Factura $factura)
    {
        // NOTA: Si se borra un registro de la tabla maestra (TB_CONSUMOS), se borran en cascada todos los registros asociados en la tabla detalle (TB_CONSUMOS_DETALLE).
        $sql="DELETE FROM TB_CONSUMOS WHERE ID_CONSUMO=:id_consumo";
        $params=array();
        $params[':id_consumo']=$factura->id_consumo;
        
        $sql = DbHelper::prepare_sql($sql,$params);
        
        @$this->db->query($sql);
        
        return ($this->db->affected_rows()!=0);
    }
    
    /**
     * Lee, desde la BDD, todos los atributos de la factura.
     * @param Factura $factura
     */
    public function cargarAtributos(Factura $factura)
    {
        $sql="SELECT C.OID,C.VALOR,C.FASE,O.TIPO,O.OBLIGATORIO FROM TB_CONSUMOS_DETALLE C, TB_OIDS O WHERE C.ID_CONSUMO=:id_consumo AND C.OID=O.OID ORDER BY C.OID";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        
        $factura->detalles=array();
        while ($this->db->next_record())
        {
            $factura->detalles[$this->db->f("oid")]=AtributoFactura::crear($this->db->f("oid"),$this->db->f("tipo"),$this->db->f("valor"),$this->db->f("fase"));
        }
    }
    
    public function cargarAtributosPrevios(Factura $factura, array $atributosPrevios=null)
    {
        $sql="SELECT C.OID,C.VALOR,O.TIPO,O.OBLIGATORIO FROM TB_CONSUMOS_DETALLE_HIST C, TB_OIDS O WHERE C.ID_CONSUMO=:id_consumo AND C.OID=O.OID ORDER BY C.OID";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        
        $resultado=($atributosPrevios!=null) ? $atributosPrevios:array();
        while ($this->db->next_record())
        {
            $resultado[$this->db->f("oid")]=AtributoFactura::crear($this->db->f("oid"),$this->db->f("tipo"),$this->db->f("valor"),0);
        }
        
        return $resultado;
    }
    
    /**
     * Guarda todos los atributos de la factura en la tabla de detalles (TB_CONSUMOS_DETALLE).
     * @param Factura $factura Objeto de tipo Factura del que se quiere guardar los detalles en BDD.
     * @return boolean True si todo fue bien. False en caso contrario.
     */
    public function guardarAtributos(Factura $factura)
    {
        // NOTA: Se supone que el registro maestro correspondiente debe:
        // [1] Existir en la tabla TB_CONSUMOS
        // [2] No tener entradas asociadas en la tabla TB_CONSUMOS_DETALLE porque aún no se han insertado.

        //$this->borrarAtributos($factura);
        
        if($factura->detalles!=null)
        {
            if(($factura->detalles!=null)&&(count($factura->detalles)>0))
            {
                if($this->db->beginTrans())
                {
                    $succes=true;
                    foreach($factura->detalles as $detalle)
                    {
                        if(($this->db->queryTrans($detalle->prepareInsertDetalle($factura))==false)||($this->db->affected_rows()!=1))
                        {
                            $succes=false;
                            break;
                        }
                    }
                    if ($succes)
                    {
                        // La operación finalizó con éxito.
                        return $this->db->commit();
                    }
                    
                    // Falló la operación.
                    $this->db->rollback();
                }
                return false;
            }
        }
        return true;
    }
    
    private function borrarAtributos(Factura $factura, $listaOids)
    {
        $sqlPart="WHERE ID_CONSUMO=:id_consumo AND OID IN (".$listaOids.")";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $sqlPart = DbHelper::prepare_sql($sqlPart,$params);
        if($this->db->queryTrans("DELETE FROM TB_CONSUMOS_DETALLE ".$sqlPart))
        {
            if($this->db->queryTrans("DELETE FROM TB_CONSUMOS_DETALLE_HIST ".$sqlPart))
                return true;
        }
        return false;
    }
    
    public function limpiarAtributos(Factura $factura, $listaOids)
    {
        if(($listaOids!=null)&&(count($listaOids)>0))
        {
            $lista="'".implode("','",$listaOids)."'";
            if($this->db->beginTrans())
            {
                if($this->borrarAtributos($factura, $lista))
                {
                    // La operación finalizó con éxito.
                    return $this->db->commit();
                }
                
                // Falló la operación.
                $this->db->rollback();
            }
            return false;
        }
        return true;
    }
    
    /**
     * Agrega a la factura nuevos atributos y los guarda en en la tabla de detalles (TB_CONSUMOS_DETALLE).
     * @param Factura $factura
     * @param AtributoFactura[] $atributosFactura
     * @return boolean True si todo fue bien. False en caso contrario.
     */
    public function agregarNuevosAtributos(Factura $factura, array $atributosFactura)
    {
        // NOTA: Se supone que el registro maestro correspondiente debe:
        // [1] Existir en la tabla TB_CONSUMOS
        // [2] Puede contener entradas asociadas en la tabla TB_CONSUMOS_DETALLE. Se borrarán selectivamente.
        
        $listaOids="";
        foreach($atributosFactura as $detalle)
        {
            $listaOids.=",'".$detalle->oid."'";
        }
        if(empty($listaOids))
            return true;
        
        $listaOids=substr($listaOids,1);
        if($this->db->beginTrans())
        {
            if($this->borrarAtributos($factura, $listaOids))
            {
                $succes=true;
                foreach($atributosFactura as $detalle)
                {
                    $sql1=$detalle->prepareInsertDetalle($factura);
                    $sql2=$detalle->prepareInsertDetalleHist($factura);
                    
                    if(($this->db->queryTrans($sql1)==false)||($this->db->affected_rows()!=1))
                    {
                        $succes=false;
                        break;
                    }
                    if(($this->db->queryTrans($sql2)==false)||($this->db->affected_rows()!=1))
                    {
                        $succes=false;
                        break;
                    }
                }
                if ($succes)
                {
                    // La operación finalizó con éxito.
                    if($factura->detalles==null)
                    {
                        $factura->detalles=array();
                    }
                    foreach($atributosFactura as $detalle)
                    {
                        $factura->detalles[$detalle->oid]=$detalle;
                    }
                    
                    return $this->db->commit();
                }
            }
            
            // Falló la operación.
            $this->db->rollback();
        }
        return false;
    }
    
    /**
     * Crea un array asociativo de atributos a partir de un array.
     * @param array $atributos Array asociativo de valores
     * @return AtributoFactura[] array de atributos
     */
    public function crearListaAtributos(array $atributos, $fase=0)
    {
        $Oids=$this->leerListaOids(array_keys($atributos));
        
        $resultado=array();
        
        foreach($Oids as $oid)
        {
            $resultado[$oid->oid]=AtributoFactura::crear($oid->oid, $oid->tipo, $atributos[$oid->oid], $fase);
        }
        
        return $resultado;
    }
    
    /**
     * Devuelve las suministradoras a partir del contenido de la tabla TB_SUMINISTRADORAS de la BDD.
     * @return Suministradora[] array de objetos de tipo Suministradora.
     */
    public function listarSuministradoras()
    {
        $sql="SELECT NOMBRE,TIPO FROM TB_SUMINISTRADORAS ORDER BY NOMBRE,TIPO";
        $this->db->query($sql);
        
        $resultado=array();
        while ($this->db->next_record())
        {
            $resultado[]=new Suministradora($this->db->f("nombre"),$this->db->f("tipo"));
        }
        
        return $resultado;
    }
    
    
    /**
     * Carga un array asociativo con todos los OIDs solicitados.
     * @param array $listaOids Lista de códigos de los OIDs solicitados.
     * @return OID[] Array asociativo de todos los OIDs encontrados o null si hay error.
     */
    private function leerListaOids(array $listaOids)
    {
        $resultado=array();
        $codigosOids=implode("','",$listaOids);
        if(empty($codigosOids)==false)
        {
            $codigosOids="'".$codigosOids."'";
            $sql="SELECT OID,TIPO,DESC_CORTA,DESC_LARGA,OBLIGATORIO FROM TB_OIDS WHERE OID IN(".$codigosOids.") ORDER BY OID";
            $this->db->query($sql);
            while ($this->db->next_record())
            {
                $oid=$this->db->f("oid");
                $resultado[$oid]=OID::crear($oid,$this->db->f("tipo"),$this->db->f("desc_corta"),$this->db->f("desc_larga"),$this->db->f("obligatorio"));
            }
            
            if(count($resultado)!=count($listaOids))
            {
                // TODO: Mandar mensaje al log de errores.
                //return null;
            }
        }
        return $resultado;
    }
    
    
    /**
     * Inserta todos los detalles de la factura usando la transacción actual. Se asume que ninguno de los detalles existe ya en la tabla TB_CONSUMOS_DETALLE.
     * @param Factura $factura Factura de la que se quiere insertar los detalles.
     * @return boolean true si todo fue bien, false en caso contrario.
     */
    private function insertAllDetalles(Factura $factura)
    {
        $succes=true;
        if($factura->detalles!=null)
        {
            if(($factura->detalles!=null)&&(count($factura->detalles)>0))
            {
                foreach($factura->detalles as $detalle)
                {
                    if(($this->db->queryTrans($detalle->prepareInsertDetalle($factura))==false)||($this->db->affected_rows()!=1))
                    {
                        $succes=false;
                        break;
                    }
                }
                if($this->addDetallesHist($factura)==false)
                {
                    $succes=false;
                }
            }
        }
        return $succes;
    }
    
    
    /**
     * Devuelve la lista de opciones del grupo indicado.
     * @param string $grupo
     * @return SelectOption[] array de objetos con las opciones encontradas en la tabla TB_CONSUMOS_MISC.
     */
    public function leerListaOpciones($grupo)
    {
        $resultado=array();
        $sql="SELECT GRUPO,CODIGO,ORDEN,DESC_CORTA,DESC_LARGA,DEFECTO FROM TB_CONSUMOS_MISC WHERE GRUPO=:grupo ORDER BY ORDEN";
        $params=array();
        $params[':grupo']=(string)$grupo;
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        while ($this->db->next_record())
        {
            $resultado[$this->db->f("codigo")]=new SelectOption($this->db->f("grupo"),$this->db->f("codigo"),$this->db->f("orden"),$this->db->f("desc_corta"),$this->db->f("desc_larga"),($this->db->f("defecto")=='S'),false);
        }
        
        return $resultado;
    }
    
    /**
     * Devuelve la opción coincidente con el código y grupo indicados.
     * @param string $grupo
     * @param string $codigo
     * @return SelectOption[] array asociativo con el código y objeto de tipo SelectOption encontrado en la tabla TB_CONSUMOS_MISC.
     */
    public function leerOpcion($grupo,$codigo)
    {
        $resultado=array();
        $sql="SELECT GRUPO,CODIGO,ORDEN,DESC_CORTA,DESC_LARGA,DEFECTO FROM TB_CONSUMOS_MISC WHERE GRUPO=:grupo AND CODIGO=:codigo ORDER BY ORDEN";
        $params=array();
        $params[':grupo']=(string)$grupo;
        $params[':codigo']=(string)$codigo;
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        while ($this->db->next_record())
        {
            $resultado[$this->db->f("codigo")]=new SelectOption($this->db->f("grupo"),$this->db->f("codigo"),$this->db->f("orden"),$this->db->f("desc_corta"),$this->db->f("desc_larga"),($this->db->f("defecto")=='S'),false);
        }
        
        return $resultado;
    }

    private function addDetallesHist(Factura $factura)
    {
        if($factura->detalles!=null)
        {
            foreach ($factura->detalles as $att)
            {
                if($this->db->queryTrans($att->prepareUpdateDetalleHist($factura)))
                {
                    if($this->db->affected_rows()==0)
                    {
                        if($this->db->queryTrans($att->prepareInsertDetalleHist($factura)))
                        {
                            if($this->db->affected_rows()!=1)
                            {
                                return false;
                            }
                        }
                        else
                        {
                            return false;
                        }
                    }
                }
                else
                {
                    return false;
                }
            }
        }
        return true;
    }
    
    private function mergeDetallesHist(Factura $factura)
    {
        // Alternativa #1 (NO FUNCIONA EN ORACLE).
        /*
        $sql="INSERT INTO TB_CONSUMOS_DETALLE_HIST(ID_CONSUMO,OID,VALOR) SELECT ID_CONSUMO,OID,VALOR FROM TB_CONSUMOS_DETALLE WHERE ID_CONSUMO=:id_consumo ON DUPLICATE KEY UPDATE ID_CONSUMO=:id_consumo";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $sql = DbHelper::prepare_sql($sql,$params);
        return ($this->db->queryTrans($sql));
        */
        
        // Alternativa #2 (comando MERGE).
        
        // Alternativa #3 (comando INSERT+UPDATE).
        $sql="INSERT INTO TB_CONSUMOS_DETALLE_HIST(ID_CONSUMO,OID,VALOR) SELECT ID_CONSUMO,OID,VALOR FROM TB_CONSUMOS_DETALLE tto WHERE tto.ID_CONSUMO=:id_consumo AND NOT EXISTS (SELECT * FROM TB_CONSUMOS_DETALLE_HIST ttd WHERE ttd.ID_CONSUMO=tto.ID_CONSUMO AND ttd.OID=tto.OID)";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $sql = DbHelper::prepare_sql($sql,$params);
        if($this->db->queryTrans($sql))
        {
            //return true;
            //$sql="UPDATE TB_CONSUMOS_DETALLE_HIST ttd SET (VALOR)=(SELECT VALOR FROM TB_CONSUMOS_DETALLE tto WHERE tto.ID_CONSUMO=ttd.ID_CONSUMO AND tto.OID=ttd.OID) WHERE ttd.ID_CONSUMO=:id_consumo";
            $sql="UPDATE TB_CONSUMOS_DETALLE_HIST ttd SET (VALOR)=(SELECT VALOR FROM TB_CONSUMOS_DETALLE tto WHERE tto.ID_CONSUMO=ttd.ID_CONSUMO AND tto.OID=ttd.OID) WHERE ttd.ID_CONSUMO=:id_consumo AND EXISTS(SELECT * FROM TB_CONSUMOS_DETALLE tto WHERE tto.ID_CONSUMO=ttd.ID_CONSUMO AND tto.OID=ttd.OID)";
            $sql = DbHelper::prepare_sql($sql,$params);
            return ($this->db->queryTrans($sql));
        }
        return false;
    }    
    
    private function duplicarFactura(Factura $factura)
    {
        global $page;
        
        $factura->id_usuario=$page->get_current_userid();
        $factura->fecha = new DateTime('now');
        $factura->fecha_cierre=null;
        $factura->validada='I';
        
        $sql="UPDATE TB_CONSUMOS SET ID_CONSUMO=TB_CONSUMOS_ID_SEQ.nextval, ID_USUARIO=:id_usuario, SUMINISTRADORA=:suministradora, NUM_FACTURA=:num_factura, TIPO=:tipo, FECHA=TO_DATE(:fecha,'DD/MM/YYYY'), FECHA_CIERRE=NULL, FASE=:fase, VALIDADA='I' WHERE ID_CONSUMO=:id_consumo";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $params[':id_usuario']=(string)$page->get_current_userid();
        $params[':fecha']=(string)$factura->fecha->format('d/m/Y');
        $params[':fase']=$factura->fase;
        $params[':num_factura']=(string)$factura->num_factura;
        $params[':suministradora']=(string)$factura->suministradora->nombre;
        $params[':tipo']=(string)$factura->tipo;
        
        $sql = DbHelper::prepare_sql($sql,$params);
        return (($this->db->queryTrans($sql))&&($this->db->affected_rows()==1));
    }    
    
    private function leerCodigoFactura(Factura $factura)
    {
        $sql="SELECT ID_CONSUMO FROM TB_CONSUMOS WHERE ID_ESTABLECIMIENTO=:id_estab AND NUM_FACTURA=:num_factura";
        $params=array();
        $params[':id_estab']=(string)$factura->id_establecimiento;
        $params[':num_factura']=(string)$factura->num_factura;
        $sql = DbHelper::prepare_sql($sql,$params);
        if($this->db->queryTrans($sql))
        {
            if($this->db->next_record())
            {
                $factura->id_consumo=$this->db->f("id_consumo");
                return true;
            }
        }
        return false;
    }
    
    /**
     * Abre una factura para modificación. Esto hace que se genere una nueva copia de la factura limpia, sin atributos.
     * Los atributos de la factura original, se conservan en la tabla TB_CONSUMOS_DETALLE_HIST para facilitar el rellanado de campos durante la modificación de la nueva factura.
     * @param Factura $factura Factura original a modificar, si todo va bien, se le asigna un nuevo código interno (ID_CONSUMO).
     * @return boolean true si todo fue bien, false en caso contrario.
     */
    public function abrir_modificar_factura(Factura $factura)
    {
        if(($factura==null)||($factura->id_consumo==null))
        {
            return false;
        }
        $old_id_consumo=$factura->id_consumo;
        $old_id_usuario=$factura->id_usuario;
        $old_fecha_cierre=$factura->fecha_cierre;
        $old_fecha=$factura->fecha;
        $old_validada=$factura->validada;
        if($this->db->beginTrans())
        {
            // [1] Conservamos una copia de los atributos de la factura en la tabla TB_CONSUMOS_DETALLE_HIST.
            if($this->mergeDetallesHist($factura))
            {
                // [2] Generamos un nuevo registro maestro de la factura como copia exacta de la factura original (excepto algunos campos).
                // [3] Borramos el registro maestro de la factura original.
                if($this->duplicarFactura($factura))
                {
                    // [4] Eliminamos todos los detalles de la factura antigua (la nueva aún no tiene ninguno) de la tabla TB_CONSUMOS_DETALLE.
                    // Esto es necesario porque no funciona el borrado en cascada.
                    $sql="DELETE FROM TB_CONSUMOS_DETALLE WHERE ID_CONSUMO=:id_consumo";
                    $params=array();
                    $params[':id_consumo']=(string)$factura->id_consumo;
                    $sql=DbHelper::prepare_sql($sql,$params);
                    if($this->db->queryTrans($sql))
                    {
                        // Obtenemos la clave primaria de la nueva factura.
                        if($this->leerCodigoFactura($factura))
                        {
                            // [5] Se actualizan los ID_CONSUMO de estos registros en la tabla TB_CONSUMOS_DETALLE_HIST.
                            $sql="UPDATE TB_CONSUMOS_DETALLE_HIST SET ID_CONSUMO=:id_consumo WHERE ID_CONSUMO=:old_id_consumo";
                            $params=array();
                            $params[':id_consumo']=(string)$factura->id_consumo;
                            $params[':old_id_consumo']=(string)$old_id_consumo;
                            $sql = DbHelper::prepare_sql($sql,$params);
                            if($this->db->queryTrans($sql))
                            {
                                // La operación finalizó con éxito.
                                if($this->db->commit())
                                {
                                    $factura->detalles=array();
                                    return true;
                                }
                                else
                                {
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
            
            // Falló algo. Abortamos.
            $factura->id_consumo=$old_id_consumo;
            $factura->id_usuario=$old_id_usuario;
            $factura->fecha_cierre=$old_fecha_cierre;
            $factura->fecha=$old_fecha;
            $factura->validada=$old_validada;
            $this->db->rollback();
        }
        return false;
    }
    
    public function actualizarFase(Factura $factura)
    {
        $nueva_fase=1+($factura->fase);
        $sql="UPDATE TB_CONSUMOS SET FASE=:fase WHERE ID_CONSUMO=:id_consumo";
        $params=array();
        $params[':id_consumo']=(string)$factura->id_consumo;
        $params[':fase']=$factura->fase;
        $sql = DbHelper::prepare_sql($sql,$params);
        return (($this->db->query($sql))&&($this->db->affected_rows()==1));
    }
    
    public function buscarFacturas($estid, $tipo, $cerrada=null)
    {
        $resultados=array();
        
        $campos=array();
        $params=array();
        if($estid!=null)
        {
            $params[':estid']=(string)$estid;
            $campos[]="ID_ESTABLECIMIENTO=:estid";
        }
        if($tipo!=null)
        {
            $params[':tipo']=(string)$tipo;
            $campos[]="TIPO=:tipo";
        }
        if($cerrada!=null)
        {
            $campos[] = ($cerrada!==true) ? "(FECHA_CIERRE IS NOT NULL)" : "(FECHA_CIERRE IS NULL)";
        }
        if(count($campos)>0)
        {
            $sql="SELECT ID_CONSUMO,ID_ESTABLECIMIENTO,ID_USUARIO,SUMINISTRADORA,TO_CHAR(FECHA,'DD/MM/YYYY') fr,TIPO,TO_CHAR(FECHA_CIERRE,'DD/MM/YYYY') fc FROM TB_CONSUMOS WHERE ".implode(" AND ",$campos);
            //krsort($params);
            $sql = DbHelper::prepare_sql($sql,$params);
            $this->db->query($sql);
            while ($this->db->next_record())
            {
                $res=new ResultadoBusquedaFacturas();
                $res->id_consumo=$this->db->f("id_consumo");
                $res->id_establecimiento=$this->db->f("id_establecimiento");
                $res->id_usuario=$this->db->f("id_usuario");
                $res->fecha= DateTime::createFromFormat('d/m/Y', $this->db->f('fr'));
                $res->tipo=$this->db->f("tipo");
                $res->cerrada=($this->db->f("fc")!=null);
                $resultados[]=$res;
            }
        }
        
        return $resultados;
    }
    
    public function cerrarFactura(Factura $factura)
    {
        global $page;
        
        if(($factura==null)||($factura->id_consumo==null))
        {
            return false;
        }
        if($this->db->beginTrans())
        {
            $factura->fase=0;
            $factura->fecha_cierre=new DateTime('now');
            
            $sql="UPDATE TB_CONSUMOS SET ID_USUARIO=:id_usuario, FECHA_CIERRE=TO_DATE(:fecha_cierre,'DD/MM/YYYY'), FASE=:fase WHERE ID_CONSUMO=:id_consumo";
            $params=array();
            $params[':id_consumo']=(string)$factura->id_consumo;
            $params[':id_usuario']=(string)$page->get_current_userid();
            $params[':fecha_cierre']=(string)$factura->fecha_cierre->format('d/m/Y');
            $params[':fase']=$factura->fase;
            
            $sql = DbHelper::prepare_sql($sql,$params);
            if(($this->db->queryTrans($sql))&&($this->db->affected_rows()==1))
            {
                // Ahora debemos borrar todo el historial de atributos.
                $sql="DELETE FROM TB_CONSUMOS_DETALLE_HIST WHERE ID_CONSUMO=:id_consumo";
                $sql = DbHelper::prepare_sql($sql,$params);
                if($this->db->queryTrans($sql))
                {
                    return $this->db->commit();
                }
            }
            $this->db->rollback();
        }
        return false;
    }
    
    public function filtrarFacturas(Array $argumentos)
    {
        $sql="SELECT ttc.ID_CONSUMO,ttc.ID_ESTABLECIMIENTO,tte.NOMBRE_ESTABLECIMIENTO,ttc.ID_USUARIO,ttc.NUM_FACTURA,TO_CHAR(ttc.FECHA,'DD/MM/YYYY') fr,ttc.TIPO,TO_CHAR(ttc.FECHA_CIERRE,'DD/MM/YYYY') fc FROM TB_CONSUMOS ttc, TB_ESTABLECIMIENTOS_UNICO tte";
        $sql.=" WHERE ttc.ID_ESTABLECIMIENTO=tte.ID_ESTABLECIMIENTO";
        $params=array();
        $campos=array();
        
        // Como máximo, sólo una de las claves siguientes puede aparecer: est_id, est_codigo, est_nombre.
        if(key_exists('est_id', $argumentos)&&($argumentos['est_id']!=null))
        {
            $params[':est_id']=$argumentos['est_id'];
            $campos[]="ttc.ID_ESTABLECIMIENTO=:est_id";
        }
        if(key_exists('est_codigo', $argumentos)&&($argumentos['est_codigo']!=null))
        {
            $params[':codigos_establecimientos']=$argumentos['est_codigo'];
            $campos[]='ttc.ID_ESTABLECIMIENTO IN (:codigos_establecimientos)';
        }
        if(key_exists('est_nombre', $argumentos)&&($argumentos['est_nombre']!=null))
        {
            //$params[':nombre_establecimientos']=$argumentos['est_nombre'];
            $campos[]="ttc.ID_ESTABLECIMIENTO IN (SELECT ID_ESTABLECIMIENTO FROM TB_ESTABLECIMIENTOS_UNICO tte2 WHERE UPPER(tte2.NOMBRE_ESTABLECIMIENTO) LIKE '%".strtoupper($argumentos['est_nombre'])."%')";
        }
        
        if(key_exists('suministradora', $argumentos)&&($argumentos['suministradora']!=null))
        {
            $params[':suministradora']=$argumentos['suministradora'];
            //1111.5000.1002
            $campos[]='ttc.SUMINISTRADORA=:suministradora';
        }
        if(key_exists('tipoSuministro', $argumentos)&&($argumentos['tipoSuministro']!=null))
        {
            $params[':tipo']=$argumentos['tipoSuministro'];
            $campos[]='ttc.TIPO=:tipo';
        }
        if(key_exists('numero_factura', $argumentos)&&($argumentos['numero_factura']!=null))
        {
            //$params[':numerofactura']=$argumentos['numero_factura'];
            $campos[]="ttc.NUM_FACTURA LIKE '%".$argumentos['numero_factura']."%'";
        }
        if(key_exists('mesFactura', $argumentos)&&($argumentos['mesFactura']!=null)&&($argumentos['mesFactura']!=0))
        {
            $params[':mesfactura']=$argumentos['mesFactura'];
            $campos[]="TO_CHAR(ttc.FECHA,'MM')=:mesfactura";
        }
        if(key_exists('anoFactura', $argumentos)&&($argumentos['anoFactura']!=null)&&($argumentos['anoFactura']!=0))
        {
            $params[':anofactura']=$argumentos['anoFactura'];
            $campos[]="TO_CHAR(ttc.FECHA,'YYYY')=:anofactura";
        }
        if(key_exists('fecha_facturacion_desde', $argumentos)&&($argumentos['fecha_facturacion_desde']!=null))
        {
            $params[':fecha_facturacion_desde']=$argumentos['fecha_facturacion_desde'];
            //$campos[]="(TO_DATE(:fecha_facturacion_desde,'DD/MM/YYYY')>=ttc.FECHA_INICIO) AND (TO_DATE(:fecha_facturacion_desde,'DD/MM/YYYY')<=ttc.FECHA_FINAL)";
            $campos[]="TO_DATE(:fecha_facturacion_desde,'DD/MM/YYYY')<ttc.FECHA_FINAL";
        }
        if(key_exists('fecha_facturacion_hasta', $argumentos)&&($argumentos['fecha_facturacion_hasta']!=null))
        {
            $params[':fecha_facturacion_hasta']=$argumentos['fecha_facturacion_hasta'];
            //$campos[]="(TO_DATE(:fecha_facturacion_hasta,'DD/MM/YYYY')>=ttc.FECHA_INICIO) AND (TO_DATE(:fecha_facturacion_hasta,'DD/MM/YYYY')<=ttc.FECHA_FINAL)";
            $campos[]="TO_DATE(:fecha_facturacion_hasta,'DD/MM/YYYY')>ttc.FECHA_INICIO";
        }
        if(count($campos)>0)
        {
            $sql.=" AND ".implode(" AND ",$campos);
            krsort($params);
            $sql = DbHelper::prepare_sql($sql,$params);
        }
        $sql.=' ORDER BY ttc.ID_ESTABLECIMIENTO,ttc.TIPO,ttc.FECHA';
        $this->db->query($sql);
        
        $resultados=array();
        while ($this->db->next_record())
        {
            $res=new ResultadoBusquedaFacturas();
            $res->id_consumo=$this->db->f("id_consumo");
            $res->id_establecimiento=$this->db->f("id_establecimiento");
            $res->nombre_establecimiento=$this->db->f("nombre_establecimiento");
            $res->id_usuario=$this->db->f("id_usuario");
            $res->num_factura=$this->db->f("num_factura");
            $res->fecha= DateTime::createFromFormat('d/m/Y', $this->db->f('fr'));
            $res->tipo=$this->db->f("tipo");
            $res->cerrada=($this->db->f("fc")!=null);
            $resultados[]=$res;
        }
        return $resultados;
    }
    
    public function buscarFacturasRecientes($estid, $cerrada=true)
    {
        $sql="SELECT ttc.ID_CONSUMO,ttc.ID_ESTABLECIMIENTO,tte.NOMBRE_ESTABLECIMIENTO,ttc.ID_USUARIO,ttc.NUM_FACTURA,TO_CHAR(ttc.FECHA,'DD/MM/YYYY') fr,ttc.TIPO,TO_CHAR(ttc.FECHA_CIERRE,'DD/MM/YYYY') fc FROM TB_CONSUMOS ttc, TB_ESTABLECIMIENTOS_UNICO tte";
        $sql.=" WHERE ttc.ID_ESTABLECIMIENTO=tte.ID_ESTABLECIMIENTO AND (ttc.FECHA BETWEEN ADD_MONTHS(TRUNC(SYSDATE),-12) AND TRUNC(SYSDATE))";
        
        $resultados=array();
        
        $campos=array();
        $params=array();
        if($estid!=null)
        {
            $params[':estid']=(string)$estid;
            $sql.=" AND ttc.ID_ESTABLECIMIENTO=:estid";
        }
        if($cerrada==true)
        {
            $sql.= " AND (ttc.FECHA_CIERRE IS NOT NULL)";
        }
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        while ($this->db->next_record())
        {
            $res=new ResultadoBusquedaFacturas();
            $res->id_consumo=$this->db->f("id_consumo");
            $res->id_establecimiento=$this->db->f("id_establecimiento");
            $res->nombre_establecimiento=$this->db->f("nombre_establecimiento");
            $res->id_usuario=$this->db->f("id_usuario");
            $res->num_factura=$this->db->f("num_factura");
            $res->fecha= DateTime::createFromFormat('d/m/Y', $this->db->f('fr'));
            $res->tipo=$this->db->f("tipo");
            $res->cerrada=($this->db->f("fc")!=null);
            $resultados[]=$res;
        }
        
        return $resultados;
    }
    
    public function borrarFactura(Factura $factura)
    {
        // Borrar una factura nula o inexistente, se considera un éxito.
        if($factura==null)
        {
            return true;
        }
        if($this->db->beginTrans())
        {
            $params=array();
            $params[':id_consumo']=$factura->id_consumo;
            
            $sql="DELETE FROM TB_CONSUMOS_DETALLE_HIST WHERE ID_CONSUMO=:id_consumo";
            $sql = DbHelper::prepare_sql($sql,$params);
            if($this->db->queryTrans($sql))
            {
                $sql="DELETE FROM TB_CONSUMOS_DETALLE WHERE ID_CONSUMO=:id_consumo";
                $sql = DbHelper::prepare_sql($sql,$params);
                if($this->db->queryTrans($sql))
                {
                    $sql="DELETE FROM TB_CONSUMOS WHERE ID_CONSUMO=:id_consumo";
                    $sql = DbHelper::prepare_sql($sql,$params);
                    if($this->db->queryTrans($sql))
                    {
                        // La operación finalizó con éxito.
                        return $this->db->commit();
                    }
                }
            }
            
            // Falló algo. Deshacemos los cambios.
            $this->db->rollback();
        }
        return false;
    }
    
    /**
     * Actualiza la fase de recogida de la factura y rellena los detalles de la factura con nuevos atributos generados a partir de la entrada del usuario.
     * @param Factura $factura Factura a modificar.
     * @param array $atributosFactura Array de atributos a agregar a la factura.
     * @param array $listaOidsEsperables Array de nombres (OID) de atributos que se esperaban de la entrada del usuario. Los atributos de esta lista que no aparezcan en la lista de atributos a agregar, serán eliminados (si existían previamente) de la factura.
     * @return boolean True si todo fue bien, false en caso contrario.
     */
    public function cargarDatosEntradaEnFactura(Factura $factura, Array $atributosFactura, Array $listaOidsEsperables)
    {
        $listaOids="";
        if(($listaOidsEsperables!=null)&&(count($listaOidsEsperables)>0))
        {
            $listaOids="'".implode("','",$listaOidsEsperables)."'";
        }
        if(empty($listaOids))
        {
            return true;            // No hay nada que hacer.
        }
        
        if($this->db->beginTrans())
        {
            // PASO #1: Borramos los atributos existentes.
            if($this->borrarAtributos($factura, $listaOids)==false)
            {
                $this->db->rollback();
                //return "Error durante la grabación. Operación interrumpida.";     // Por si es necesario dar detalles del error.
                return false;
            }
            
            // PASO #2: Insertamos los nuevos atributos.
            $succes=true;
            foreach($atributosFactura as $detalle)
            {
                $sql1=$detalle->prepareInsertDetalle($factura);
                $sql2=$detalle->prepareInsertDetalleHist($factura);
                
                if(($this->db->queryTrans($sql1)==false)||($this->db->affected_rows()!=1))
                {
                    $succes=false;
                    break;
                }
                if(($this->db->queryTrans($sql2)==false)||($this->db->affected_rows()!=1))
                {
                    $succes=false;
                    break;
                }
            }
            if ($succes)
            {
                // La operación finalizó con éxito.
                if($factura->detalles==null)
                {
                    $factura->detalles=array();
                }
                foreach($atributosFactura as $detalle)
                {
                    $factura->detalles[$detalle->oid]=$detalle;
                }
                
                // PASO #3: Actualizamos la fase de la factura.
                $sql="UPDATE TB_CONSUMOS SET FASE=:fase WHERE ID_CONSUMO=:id_consumo";
                $params=array();
                $params[':id_consumo']=(string)$factura->id_consumo;
                $params[':fase']=$factura->fase;
                $sql = DbHelper::prepare_sql($sql,$params);
                if(($this->db->queryTrans($sql))&&($this->db->affected_rows()==1))
                {
                    return $this->db->commit();
                }
            }
            
            // Falló la operación.
            $this->db->rollback();
        }
        return false;
    }
}