<?php

require_once(__DIR__."/../config.php");
require_once(__DIR__."/../lib/DateHelper.class.php");
require_once(__DIR__."/../lib/DbHelper.class.php");
require_once(__DIR__."/../lib/RowDbIterator.class.php");
require_once(__DIR__."/NSS.class.php");
require_once(__DIR__."/NIF.class.php");

define('EMPLEADOR_ACTIVO','A');
define('EMPLEADOR_INACTIVO','I');
define('EMPLEADOR_EXTERNO','S');
define('EMPLEADOR_INTERNO','N');

define('ID_EMPLEADOR_CCSS','A');
define('ID_EMPLEADOR_CIF','B');
define('ID_EMPLEADOR_NIF','C');
define('ID_EMPLEADOR_NIE','D');
define('ID_EMPLEADOR_PASAPORTE','E');

/** Tipos de carga de los cuestionarios de empleo */
if(!defined('TIPO_CARGA_XML'))
{
    @define("TIPO_CARGA_XML", "1");
    @define("TIPO_CARGA_WEB", "2");
    @define("TIPO_CARGA_FAX", "3");
}


/**
 * Clase que representa un empleador a la que asociar empleados.
 * Esto se hace para contabilizar, mediante cuestionarios, el número de empleados del establecimiento turístico.
 * 
 * @author SCC,SL
 *
 */
class Empleador
{
    /**
     * Identificador del establecimiento al que está asociado este empleador.
     * @var string $id_establecimiento
     */
    var $id_establecimiento;
    
    /**
     * Identificador del empleador.
     * Según el tipo de identificador, puede ser una cuenta de cotización/número de afiliación a la SS (objeto de tipo NSS) ó DNI/NIE/NIF (objeto NIF).
     * @var object $id_empleador
     */
    var $id_empleador;
    
    /**
     * Tipo de empleador.
     * Posibles valores:
     *   S=empresa externa (ETT)
     *   N=empresa propia
     *   
     * En caso de empresa propia, el identificador del empleador será un número de la Seguridad Social.
     * En caso de empresa externa, el identificador del empleador será un NIF.
     * @var string $externo
     */
    var $externo;
    
    /**
     * Descripción del empleador. Por ejemplo, en el caso de ETT es el nombre de la empresa.
     * @var string $descripcion
     */
    var $descripcion;
    
    /**
     * Nombre del establecimiento. Este campo proviene de la tabla TB_ESTABLECIMIENTOS_UNICO.
     * @var string $nombre_establecimiento
     */
    var $nombre_establecimiento;
    
    /**
     * Estado del registro. 'A'=registro activo; 'I'=registro inactivo (no se recogen trabajadores en los cuestionarios).
     * @var string $estado
     */
    var $estado;    
    
    /**
     * Constructor por defecto.
     */
    public function __construct()
    {
        $params=func_get_args();
        $num_params=func_num_args();
        /*
        $constructor='__construct'.$num_params;
        if(method_exists($this,$constructor))
        {
            call_user_func_array(array($this,$constructor), $params);
        }
        */
        $constructor='__construct'.$num_params;
        if($num_params==1)
        {
            //$constructor='__construct'.get_class($params[0]);
            if(is_subclass_of($params[0],'IPF'))
                $constructor='__construct'.get_class($params[0]);
        }
        if(method_exists($this,$constructor))
        {
            call_user_func_array(array($this,$constructor), $params);
        }
    }
    
    /**
     * Constructor sin parámetros
     */
    function __construct0()
    {
    }
    
    /**
     * Constructor de empleador con NIF
     */
    function __constructNIF($nif)
    {
        $this->id_empleador=$nif;
    }
    
    /**
     * Constructor de empleador con NSS
     */
    function __constructNSS($nss)
    {
        $this->id_empleador=$nss;
    }
    
    /**
     * Setter de la propiedad nombre_establecimiento.
     * @param string $nombreEstablecimiento
     */
    public function setNombreEstablecimiento($nombreEstablecimiento)
    {
        $this->nombre_establecimiento=$nombreEstablecimiento;
    }
    
    /**
     * Normaliza el identificador del empleador.
     */
    public function normalizar()
    {
        if(isset($this->id_empleador))
            $this->id_empleador->normalizar();
    }
    
    /**
     * Formatea el identificador del empleador a un formato específico.
     * @param string $formato Formato deseado. Los formatos admitidos dependen del tipo de identificador.
     * @return string representación del identificador del empleador en el formato especificado.
     */
    public function formatear($formato=null)
    {
        if(isset($this->id_empleador))
            return $this->id_empleador->formatear($formato);
        return null;
    }
    
    /**
     * Valida el identificador del empleador.
     *
     * @return array Errores detectados (array vacío si no hay errores).
     */
    public function validar()
    {
        if(!isset($this->id_empleador))
        {
            $errs=array();
            array_push($errs,"El identificador no está definido.");
            return $errs;
        }
        return $this->id_empleador->validar();
    }
}

/**
 * Clase que representa un cuestionario de empleo.
 * @author SCC,SL
 *
 */
class EmpleoCuestionario
{
    /**
     * <p>Identificador único del registro (formato numérico YYYYMMDDHHmmEEEE donde YYYY=año, MM=mes, DD=día, HH=hora, mm=minuto y EEEE=código de establecimiento).</p>
     * @var string $id
     */
    var $id;
    
    /**
     * Identificador del establecimiento asociado al cuestionario.
     * @var string $id_establecimiento
     */
    var $id_establecimiento;
    
    /**
     * Identificador del usuario que rellena el cuestionario.
     * @var string $id_usuario
     */
    var $id_usuario;
    
    /**
     * Tipo de carga en la que se ha rellenado la encuesta (XML=1, Web=2, Fax=3...).
     * @var string $tipo_carga
     */
    var $tipo_carga;
    
    /**
     * Número de mes del cuestionario.
     * @var integer $mes
     */
    var $mes;
    
    /**
     * Año del cuestionario
     * @var integer $ano
     */
    var $ano;
    
    /// TODO: Dejar sólo uno de los campos fecha ==> por ejemplo, dejar sólo el campo fecha_cierre con la fecha en que el usuario finalizó la cumplimentación del cuestionario.
    
    /**
     * Fecha en la que el usuario envia el cuestionario (cierra el cuestionario).
     * @var DateTime $fecha_cierre
     */
    var $fecha_cierre;
    
    /**
     * Fecha de recepción del cuestionario.
     * @var DateTime $fecha_recepcion
     */
    var $fecha_recepcion;
    
    /**
     * Array asociativo de NIFs y número de empleados correspondiente.
     * @var array string => EmpleoFilaFormulario $numero_empleados
     */
    var $numero_empleados;
    
    /**
     * Array asociativo de NIFs y número de empleados correspondiente del anterior cuestionario (si lo hay) o null.
     * @var array string => int $numero_empleados_anterior
     */
    var $numero_empleados_anterior;
    
    //     public function __construct($mes, $ano, $estab_id = null, $userid = null)
    //     {
    //         $this->id_usuario = $userid;
    //         $this->id_establecimiento = $estab_id;
    //         $this->mes = $mes;
    //         $this->ano = $ano;
    //     }
    
    /**
     * Indica si es nuevo (aún no se ha grabado en la tabla TB_EMPLEO_CUEST).
     * @return boolean
     */
    public function es_nuevo()
    {
        return $this->id == null;
    }
    
    /**
     * Indica si el cuestionario está cerrado (finalizado) o no.
     * @return boolean
     */
    public function esta_cerrado()
    {
        return ($this->fecha_cierre != null);
    }
    
    public function validar()
    {
        $errs = new EmpleoErrorCollection();
        
        // Regla #1: El número de empleados debe estar en el rango [0,9999]. Las entradas con cero empleados son eliminadas del cuestionario.
        $res=true;
        foreach($this->numero_empleados as $id_empleador => $empleador)
        {
            if($empleador->num_empleados<0)
            {
                $res=false;
                $errs->log_error(ERROR_GENERAL, sprintf("El empleador '%s' no puede haber empleado un número negativo de trabajadores.",isset($empleador->id_empleador_display) ? $empleador->id_empleador_display : $empleador->id_empleador));
            }
            elseif($empleador->num_empleados==0)
            {
                unset($this->numero_empleados[$id_empleador]);
            }
            elseif($empleador->num_empleados>9999)
            {
                $res=false;
                $errs->log_error(ERROR_GENERAL, sprintf("El empleador '%s' ha superado el límite de trabajadores empleados (9999).",isset($empleador->id_empleador_display) ? $empleador->id_empleador_display : $empleador->id_empleador));
            }
        }
        
        // Regla #2: Debe existir al menos un empleador no externo.
        $res=false;
        foreach($this->numero_empleados as $id_empleador => $empleados)
        {
            if($empleados->externo==EMPLEADOR_INTERNO)
            {
                $res=true;
                break;
            }
        }
        if($res==false)
        {
            $errs->log_error(ERROR_GENERAL, "Debe existir al menos un empleador no externo activo.");
        }
        
        
//         $a=new EmpleoFilaFormulario();
//         $a->
        
        return $errs;
    }
}

/**
 * Clase que representa una encuesta. O sea, una instancia de cuestionario de tipo empleo.
 * @author SCC,SL
 *
 */
class EmpleoCuestionarioEstado
{
    /**
     * Objeto EmpleoCuestionario asociado a la encuesta.
     * @var EmpleoCuestionario
     */
    var $encuesta;
    
    /**
     * Fecha límite para rellenar la encuesta asociada.
     * @var DateTime
     */
    var $plazo;
    
    /**
     * Indica si la encuesta es modificable. Bien porque sea una encuesta aún no cerrada o porque la ha cargado un administrador en modo RW.
     * @var boolean
     */
    var $es_rdonly;
    
    /**
     * Establecimiento con las propiedades ajustadas al momento de inicio de la encuesta.
     * @var Establecimiento
     */
    var $datos_estab;
}

class EmpleoFilaFormulario
{
    /**
     * Identificador del empleador ya normalizado.
     * @var string Cadena de 12 dígitos. Tal y como aparece en la tabla TB_EMPLEADORES.
     */
     var $id_empleador;
     
     /**
      * Identificador del empleador.
      * @var string Cadena de 12 dígitos. Se admiten caracteres separadores que serán ignorados.
      */
     var $id_empleador_display;
     
     /**
      * Descripción del empleador.
      * @var string $descripcion
      */
     var $descripcion;
     
     /**
      * Tipo de empleador.
      * Posibles valores:
      *   S=empresa externa (ETT)
      *   N=empresa propia
      *
      * En caso de empresa propia, el identificador del empleador será un número de la Seguridad Social.
      * En caso de empresa externa, el identificador del empleador será un NIF.
      * @var string $externo
      */
     var $externo;
     
     /**
      * Número de empleados asociados.
      * @var int
      */
     var $num_empleados;
     
     /**
      * Número de empleados asociados en la anterior encuesta.
      * @var int
      */
     var $num_empleados_anterior;
     
     static public function getFilasFromListaEmpleadores($empleadores)
     {
         $lista=array();
         foreach($empleadores as $empleador)
         {
             if($empleador->estado=='A')
             {
                 $empleo_fila_formulario=new EmpleoFilaFormulario();
                 $empleo_fila_formulario->id_empleador=$empleador->id_empleador->getId();
                 $empleo_fila_formulario->id_empleador_display=$empleador->id_empleador->toString();
                 $empleo_fila_formulario->descripcion=$empleador->descripcion;
                 $empleo_fila_formulario->externo=$empleador->externo;
                 $empleo_fila_formulario->num_empleados=-1;
                 $empleo_fila_formulario->num_empleados_anterior=-1;
                 $lista[$empleo_fila_formulario->id_empleador]=$empleo_fila_formulario;
             }
         }
         return $lista;
     }
}

/**
 * Clase que se encarga de la persistencia de los objetos Empleador en la BDD.
 *
 * @author SCC,SL
 *
 */
class EmpleadoresDao
{
    var $db;
    
    /**
     * Constructo básico. Inicializa el soporte de conexión a la BDD.
     * La conexión a BDD se establece cuando se hace la primera consulta.
     */
    public function __construct()
    {
        $this->db = new Istac_Sql();
    }
    
    /**
     * Obtiene un listado de empleadores para mostrar en la tabla de resultados.
     * @param string $estid
     * @param string $estado Estado (A ó I) de los empleadores a recuperar o null si no importa.
     * @param string $externos Externo (S ó N) de los empleadores a recuperar o null si no importa.
     * @return Empleador[] Array de empleadores obtenidos.
     */
    function listarEmpleadores($estid, $estado=null, $externos=null)
    {
        $params=array();
        $params[':idestab']=$estid;

        $sql="SELECT A.ID_EMPLEADOR, A.ID_ESTABLECIMIENTO, A.DESCRIPCION, A.EXTERNO, A.ESTADO, B.NOMBRE_ESTABLECIMIENTO FROM TB_EMPLEADORES A, TB_ESTABLECIMIENTOS_UNICO B WHERE A.ID_ESTABLECIMIENTO=B.ID_ESTABLECIMIENTO AND A.ID_ESTABLECIMIENTO=:idestab";
        
        if($estado!=null)
        {
            $sql.=" AND ESTADO=:estado";
            $params[':estado']=$estado;
        }
        
        if($externos!=null)
        {
            $sql.=" AND EXTERNO=:externo";
            $params[':externo']=$externos;
        }
        
        $sql.=" ORDER BY UPPER(A.DESCRIPCION) ASC";
        
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        
        $result = array();
        while ($this->db->next_record())
        {
            $empleador = new Empleador();
            $empleador->externo=$this->db->f('externo');
            $empleador->id_empleador=($empleador->externo=='S') ? new NIF($this->db->f('id_empleador')) : new NSS($this->db->f('id_empleador'));
            $empleador->id_establecimiento=$this->db->f('id_establecimiento');
            $empleador->descripcion=$this->db->f('descripcion');
            $empleador->estado=$this->db->f('estado');
            $empleador->nombre_establecimiento=$this->db->f('nombre_establecimiento');
            $result[] = $empleador;
        }
        return $result;
    }
    
    protected function insertar($empleador)
    {
        $errs=array();
        
        $sql = DbHelper::prepare_sql("INSERT INTO TB_EMPLEADORES(ID_EMPLEADOR,ID_ESTABLECIMIENTO,DESCRIPCION,EXTERNO,ESTADO,TIMESTAMP) VALUES(:id_empleador,:id_estab,:descripcion,:externo,:estado,current_timestamp)",
            array(":id_empleador" => $empleador->id_empleador->getId(),
                ":id_estab" => $empleador->id_establecimiento,
                ":descripcion" => $empleador->descripcion,
                ":externo" => ($empleador->id_empleador->getClase()=="NIF")?'S':'N',
                ":estado" => $empleador->estado
            ));

        @$this->db->query($sql);
        if($this->db->Error!=FALSE)
        {
            array_push($errs,sprintf("Error al insertar el nuevo registro. %s",$this->db->Error['message']));
            if($this->db->Error['code']==1)
            {
                array_push($errs,"Error. Posiblemente el empleador identificado por ".$empleador->id_empleador->getId()." ya ha sido registrado para este establecimiento (".$empleador->id_establecimiento.").");
            }
        }
        /*
        if($this->db->affected_rows()!=1)
        {
            array_push($errs,"Error. Posiblemente el empleador identificado por ".$empleador->id_empleador->getId()." ya ha sido registrado para este establecimiento (".$empleador->id_establecimiento.").");
        }
        */
        return $errs;
    }
    
    public function crear($empleador)
    {
        $empleador->normalizar();
        $res=$this->insertar($empleador);
        return $res;
    }
    
    /**
     * Borra el registro del empleador.
     * Sólo para administradores.
     * @param Empleador $empleador Empleador a eliminar.
     * @return array Array de strings vacío si el borrado se completó con éxito ó con los errores obtenidos en caso contrario.
     */
    protected function delete($empleador)
    {
        $errs=array();
        
        $sql = DbHelper::prepare_sql("DELETE FROM TB_EMPLEADORES WHERE ID_EMPLEADOR=:id_empleador AND ID_ESTABLECIMIENTO=:id_estab",
            array(":id_empleador" => $empleador->id_empleador->getId(),
                ":id_estab" => $empleador->id_establecimiento
            ));
        
        @$this->db->query($sql);
        if($this->db->Error!=FALSE)
        {
            if(isset($this->db->Error['code']))
            {
                switch($this->db->Error['code'])
                {
                    case 1:
                        array_push($errs,"Error al borrar el registro. Posiblemente el empleador identificado por ".$empleador->id_empleador->getId()." ya ha sido borrado para este establecimiento (".$empleador->id_establecimiento.").");
                        break;
                    case 2292:
                        array_push($errs,"Error al borrar el registro. Posiblemente el empleador identificado por ".$empleador->id_empleador->getId()." aparece en alguna encuesta de empleo de este establecimiento (".$empleador->id_establecimiento.").");
                        break;
                    default:
                        //array_push($errs,sprintf("Error al borrar el registro. %s",$this->db->Error['message'])); // --> ATENCIÓN: Posible fuga de información.
                        array_push($errs,"Error al borrar el registro.");
                        break;
                }
            }
            else
            {
                array_push($errs,sprintf("Error al borrar el registro. %s",$this->db->Error['message']));
            }
        }
        else
        {
            if($this->db->affected_rows()==0)
            {
                array_push($errs,"Error al borrar el registro. Posiblemente el empleador identificado por ".$empleador->id_empleador->getId()." ya ha sido borrado para este establecimiento (".$empleador->id_establecimiento.").");
            }
        }
        return $errs;
    }
    
    public function borrar($empleador)
    {
        $empleador->normalizar();
        $res=$this->delete($empleador);
        return $res;
    }
    
    /*
    public function borrarSeguro($empleador)
    {
        $empleador->normalizar();
        if($this->getNumeroEncuestas($empleado->id_establecimiento, $empleado->id_empleador)==0)
        {
            $res=$this->delete($empleador);
        }
        else
        {
            $res=array();
            array_push($res,"Error al borrar el registro. El empleador identificado por ".$empleador->id_empleador->getId()." aparece en alguna encuesta de empleo de este establecimiento (".$empleador->id_establecimiento.").");
        }
        return $res;
    }
    */
    
    protected function update($empleador, $full=false)
    {
        $errs=array();
        
        $params=array();
        $sql="UPDATE TB_EMPLEADORES SET TIMESTAMP=current_timestamp";
        if((isset($empleador->descripcion))||($full))
        {
            $params[':descripcion']=$empleador->descripcion;
            $sql.=',DESCRIPCION=:descripcion';
        }
        if((isset($empleador->externo))||($full))
        {
            $params[':externo']=$empleador->externo;
            $sql.=',EXTERNO=:externo';
        }
        if((isset($empleador->estado))||($full))
        {
            $params[':estado']=$empleador->estado;
            $sql.=',ESTADO=:estado';
        }
        
        if(count($params)>0)
        {
            $params[':id_empleador']=$empleador->id_empleador->getId();
            $params[':id_estab']=$empleador->id_establecimiento;
            $sql.=' WHERE ID_EMPLEADOR=:id_empleador AND ID_ESTABLECIMIENTO=:id_estab';
            
            $sql = DbHelper::prepare_sql($sql,$params);
            @$this->db->query($sql);
            if($this->db->Error!=FALSE)
            {
                array_push($errs,sprintf("Error al actualizar el registro. %s",$this->db->Error['message']));
                if($this->db->Error['code']==1)
                {
                    array_push($errs,"Error. Posiblemente el empleador identificado por ".$empleador->id_empleador->getId()." ya ha sido registrado para este establecimiento (".$empleador->id_establecimiento.").");
                }
            }
            if($this->db->affected_rows()!=1)
            {
                array_push($errs,"Error. Posiblemente el empleador identificado por ".$empleador->id_empleador->getId()." no ha sido registrado para este establecimiento (".$empleador->id_establecimiento.").");
            }
        }
        
        return $errs;
    }
    
    
    public function actualizar($empleador)
    {
        $empleador->normalizar();
        $res=$this->update($empleador);
        return $res;
    }
    
    /**
     * Obtiene el dia de plazo (dia del mes siguiente al mes preguntado) de la encuesta (coincide con el de la encuesta de alojamiento), o false si no existe plazo.
     * @param string $estid Código único del establecimiento
     * @param integer $grupo_est Código del grupo al que pertenece el establecimiento (de la tabla TB_GRUPO_ESTABLECIMIENTOS)
     * @param integer $mes Mes de la encuesta para la que se busca el plazo
     * @param integer $ano Año de la encuesta para la que se busca el plazo
     * @return number|boolean Número del día o FALSE si no hay plazo definido.
     */
    public function cargar_dia_plazo($estid, $grupo_est, $mes,$ano)
    {
        $sql = DbHelper::prepare_sql("select dia_mes_siguiente from TB_ALOJA_PLAZOS_ESTAB where ID_ESTABLECIMIENTO = :estid and MES=:mes and ANO=:ano",
            array(':estid' => (string)$estid,
                ':mes' => (int)$mes,
                ':ano' => (int)$ano));
            
            $this->db->query($sql);
            if ($this->db->next_record())
            {
                return (int)$this->db->f("dia_mes_siguiente");
            }
            
            $sql = DbHelper::prepare_sql("select * from tb_aloja_plazos_mes where (id_grupo = :grupo or id_grupo = 0) and mes = :mes order by id_grupo desc",
                array(':mes' => (int)$mes,
                    ':grupo' => (int)(($grupo_est != null)? $grupo_est : 0)));
                
                
                $this->db->query($sql);
                
                if ($this->db->next_record())
                {
                    return (int)$this->db->f("dia_mes_siguiente");
                    //return (int)((new DateTime('now'))->format('d'));
                }
                return false;
    }
    
    
    /********************************************************************/
    /*** OPERACIONES SOBRE LOS CUESTIONARIOS COMPLETOS (CON DETALLES) ***/
    /********************************************************************/
    /**
     * Busca y carga un cuestionario de empleo completo (con detalles si los hubiera).
     * @param string $est_id Código único del establecimiento
     * @param integer $mes Número del mes del cuestionario a cargar
     * @param integer $ano Número de año del cuestionario a cargar
     * @return EmpleoCuestionario|NULL Devuelve un objeto EmpleoCuestionario si ha cargado los datos (se ha encontrado el registro), nulo en caso contrario (no se ha encontrado).
     */
    public function cargar_cuestionario($est_id, $mes, $ano)
    {
        $sql="SELECT ID,ID_ESTABLECIMIENTO,ID_USUARIO,ID_TIPO_CARGA,MES,ANO,TO_CHAR(FECHA_RECEPCION,'DD/MM/YYYY') fr,TO_CHAR(FECHA_CIERRE,'DD/MM/YYYY') fc FROM TB_EMPLEO_CUEST WHERE id_establecimiento=:estid AND mes=:mes AND ano=:ano";
        $params=array();
        $params[':estid']=(string)$est_id;
        $params[':mes']=(int)$mes;
        $params[':ano']=(int)$ano;
        
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        
        if($this->db->next_record())
        {
            $cuestionario=new EmpleoCuestionario();
            $cuestionario->id = $this->db->f("id");
            $cuestionario->id_establecimiento = $this->db->f("id_establecimiento");
            $cuestionario->id_usuario = $this->db->f("id_usuario");
            $cuestionario->tipo_carga = $this->db->f("id_tipo_carga");
            $cuestionario->mes = $this->db->f("mes");
            $cuestionario->ano = $this->db->f("ano");
            $cuestionario->fecha_recepcion = DateTime::createFromFormat('d/m/Y', $this->db->f('fr'));
            if($this->db->f('fc') != null)
            {
                $cuestionario->fecha_cierre = DateTime::createFromFormat('d/m/Y', $this->db->f('fc'));
            }
            
            $cuestionario->numero_empleados=array();

            $sql="SELECT A.ID_EMPLEADOR,A.EXTERNO,A.NUMERO_EMPLEADOS, B.DESCRIPCION FROM TB_EMPLEO_CUEST_DET A, TB_EMPLEADORES B WHERE A.ID_EMPLEADOR=B.ID_EMPLEADOR AND A.ID=:id_cuest ORDER BY A.ID_EMPLEADOR";
            $params=array();
            $params[':id_cuest']=(string)$cuestionario->id;
            
            $sql = DbHelper::prepare_sql($sql,$params);
            $this->db->query($sql);
            
            while ($this->db->next_record())
            {
                $fila=new EmpleoFilaFormulario();
                $fila->externo=$this->db->f("externo");
                $fila->id_empleador=$this->db->f("id_empleador");
                $empleador=($fila->externo==EMPLEADOR_EXTERNO) ? new NIF($fila->id_empleador):new NSS($fila->id_empleador);
                $fila->id_empleador_display=$empleador->toString();
                $fila->descripcion=$this->db->f("descripcion");
                $fila->num_empleados=$this->db->f("numero_empleados");
                $fila->num_empleados_anterior=-1;
                $cuestionario->numero_empleados[$fila->id_empleador]=$fila;
                //$cuestionario->numero_empleados[$this->db->f("id_empleador")]=$this->db->f("numero_empleados");
            }
            
            return $cuestionario;
        }
        return null;
    }
    
    /**
     * Almacena un nuevo cuestionario completo usando una transacción.
     * @param EmpleoCuestionario $cuestionario
     * @return boolean Devuelve true si todo fue bien, false en caso contrario.
     */
    public function guardar_cuestionario(EmpleoCuestionario $cuestionario)
    {
        if ($cuestionario->es_nuevo())
        {
            //return $this->insertar_registro_cuestionario($cuestionario);
            return $this->insertar_cuestionario($cuestionario);
        }
        else
        {
            //return $this->actualizar_registro_cuestionario($cuestionario);
            return $this->actualizar_cuestionario($cuestionario);
        }
    }
    
    /**
     * Almacena un nuevo cuestionario completo, insertando un registro en la tabla maestra y los registros correspondientes en la tabla detalle, usando una transacción.
     * @param EmpleoCuestionario $cuestionario
     * @return boolean Devuelve true si todo fue bien, false en caso contrario.
     */
    public function insertar_cuestionario(EmpleoCuestionario $cuestionario)
    {
        if($this->db->beginTrans())
        {
            $nuevo_id=sprintf("%04d%02d%s%s",$cuestionario->ano,$cuestionario->mes,(new DateTime('now'))->format('dHi'),$cuestionario->id_establecimiento);
            
            // Insertamos el registro maestro.
            $sql="INSERT INTO TB_EMPLEO_CUEST(ID,ID_ESTABLECIMIENTO,ID_USUARIO,ID_TIPO_CARGA,MES,ANO,FECHA_RECEPCION,FECHA_CIERRE,TIMESTAMP) VALUES(:id_cuest,:estid,:userid,:tipo_carga,:mes,:ano,to_date(:fecha_recepcion,'yyyy-mm-dd'),to_date(:fecha_cierre,'yyyy-mm-dd'),current_timestamp)";
            $params=array();
            $params[':id_cuest']=(string)$nuevo_id;
            $params[':estid']=(string)$cuestionario->id_establecimiento;
            $params[':userid']=(string)$cuestionario->id_usuario;
            $params[':tipo_carga']=(string)$cuestionario->tipo_carga;
            $params[':mes']=(int)$cuestionario->mes;
            $params[':ano']=(int)$cuestionario->ano;
            $params[':fecha_recepcion'] = (string)$cuestionario->fecha_recepcion->format('Y-m-d');
            if($cuestionario->fecha_cierre!=null)
                $params[':fecha_cierre'] = (string)$cuestionario->fecha_cierre->format('Y-m-d');
                else
                    $params[':fecha_cierre'] = null;
                    
                    $sql = DbHelper::prepare_sql($sql,$params);
                    if($this->db->queryTrans($sql)!=false)
                    {
                        if($this->db->Error!=FALSE)
                        {
                            $mensaje_error=$this->db->Error['message'];
                        }
                        if($this->db->affected_rows()==1)
                        {
                            $succes=true;
                            /// Insertar los detalles
                            if(($cuestionario->numero_empleados!=null)&&(count($cuestionario->numero_empleados)>0))
                            {
                                foreach($cuestionario->numero_empleados as $id_empleador => $filaCuestionario)
                                {
                                    $sql="INSERT INTO TB_EMPLEO_CUEST_DET(ID,ID_ESTABLECIMIENTO,ID_EMPLEADOR,EXTERNO,NUMERO_EMPLEADOS) VALUES(:id_cuest,:estid,:id_empleador,:externo,:numero_empleados)";
                                    $params=array();
                                    $params[':id_cuest']=(string)$nuevo_id;
                                    $params[':estid']=(string)$cuestionario->id_establecimiento;
                                    $params[':id_empleador']=(string)$id_empleador;
                                    $params[':externo']=$filaCuestionario->externo;
                                    $params[':numero_empleados']=$filaCuestionario->num_empleados;
                                    $sql = DbHelper::prepare_sql($sql,$params);
                                    
                                    if(($this->db->queryTrans($sql)==false)||($this->db->affected_rows()!=1))
                                    {
                                        $succes=false;
                                        break;
                                    }
                                }
                            }
                            if ($succes)
                            {
                                // La operación finalizó con éxito.
                                $succes=$this->db->commit();
                                if($succes)
                                    $cuestionario->id=$nuevo_id;
                                    return $succes;
                            }
                        }
                    }
                    
                    // Falló la operación.
                    $this->db->rollback();
        }
        return false;
    }
    
    /**
     * Actualiza un cuestionario completo, borrando el cuestionario anterior (incluyendo los detalles) y volviendo a insertarlo.
     * @param EmpleoCuestionario $cuestionario
     * @return boolean Devuelve true si todo fue bien, false en caso contrario.
     */
    public function actualizar_cuestionario(EmpleoCuestionario $cuestionario)
    {
        if($this->db->beginTrans())
        {
            // NOTA: Si se borra un registro de la tabla maestra (TB_EMPLEO_CUEST), se borran en cascada todos los registros asociados en la tabla detalle (TB_EMPLEO_CUEST_DET).
            $sql="DELETE FROM TB_EMPLEO_CUEST WHERE ID=:id_cuest";
            $params=array();
            $params[':id_cuest']=(string)$cuestionario->id;
            
            $sql = DbHelper::prepare_sql($sql,$params);
            
            if(($this->db->queryTrans($sql)!=false)&&($this->db->affected_rows()!=0))
            {
                // Insertamos el registro maestro.
                $sql="INSERT INTO TB_EMPLEO_CUEST(ID,ID_ESTABLECIMIENTO,ID_USUARIO,ID_TIPO_CARGA,MES,ANO,FECHA_RECEPCION,FECHA_CIERRE,TIMESTAMP) VALUES(:id_cuest,:estid,:userid,:tipo_carga,:mes,:ano,to_date(:fecha_recepcion,'dd-mm-yyyy'),to_date(:fecha_cierre,'dd-mm-yyyy'),current_timestamp)";
                $params=array();
                $params[':id_cuest']=(string)$cuestionario->id;
                $params[':estid']=(string)$cuestionario->id_establecimiento;
                $params[':userid']=(string)$cuestionario->id_usuario;
                $params[':tipo_carga']=(string)$cuestionario->tipo_carga;
                $params[':mes']=(int)$cuestionario->mes;
                $params[':ano']=(int)$cuestionario->ano;
                $params[':fecha_recepcion'] = (string)$cuestionario->fecha_recepcion->format('d-m-Y');
                if($cuestionario->fecha_cierre!=null)
                    $params[':fecha_cierre'] = (string)$cuestionario->fecha_cierre->format('d-m-Y');
                    else
                        $params[':fecha_cierre'] = null;
                        
                        $sql = DbHelper::prepare_sql($sql,$params);
                        if($this->db->queryTrans($sql)!=false)
                        {
                            if($this->db->affected_rows()==1)
                            {
                                /// Insertar los detalles
                                if(($cuestionario->numero_empleados!=null)&&(count($cuestionario->numero_empleados)>0))
                                {
                                    $succes=true;
                                    foreach($cuestionario->numero_empleados as $id_empleador => $filaCuestionario)
                                    {
                                        $sql="INSERT INTO TB_EMPLEO_CUEST_DET(ID,ID_ESTABLECIMIENTO,ID_EMPLEADOR,EXTERNO,NUMERO_EMPLEADOS) VALUES(:id_cuest,:estid,:id_empleador,:externo,:numero_empleados)";
                                        $params=array();
                                        $params[':id_cuest']=(string)$cuestionario->id;
                                        $params[':estid']=(string)$cuestionario->id_establecimiento;
                                        $params[':id_empleador']=(string)$id_empleador;
                                        $params[':externo']=$filaCuestionario->externo;
                                        $params[':numero_empleados']=$filaCuestionario->num_empleados;
                                        $sql = DbHelper::prepare_sql($sql,$params);
                                        
                                        if(($this->db->queryTrans($sql)==false)||($this->db->affected_rows()!=1))
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
                                }
                            }
                        }
            }
            
            // Falló la operación.
            $this->db->rollback();
        }
        return false;
    }
    
    public function cargar_ultimos_detalles(EmpleoCuestionario $cuestionario)
    {
        list($mes_anterior, $ano_anterior) = DateHelper::mes_anterior($cuestionario->mes, $cuestionario->ano);
        $ult_cuestionario=$this->cargar_cuestionario($cuestionario->id_establecimiento, $mes_anterior, $ano_anterior);
        if($ult_cuestionario!=null)
        {
            $cuestionario->numero_empleados_anterior=$ult_cuestionario->numero_empleados;
        }
    }
    
    /**
     * Obtiene el listado de encuestas anteriores a la fecha indicada, hasta un año hacia atrás.
     * @param string $estid
     * @param integer $mes_actual
     * @param integer $ano_actual
     * @return mixed[]|array|NULL[]
     */
    public function obtener_encuestas_anteriores($estid, $mes_actual, $ano_actual)
    {
        $sql="SELECT ID,ID_ESTABLECIMIENTO,ID_USUARIO,ID_TIPO_CARGA,MES,ANO,TO_CHAR(FECHA_RECEPCION,'DD/MM/YYYY') fr,TO_CHAR(FECHA_CIERRE,'DD/MM/YYYY') fc FROM TB_EMPLEO_CUEST WHERE id_establecimiento=:estid AND fecha_cierre IS NOT NULL AND  (TO_DATE('01/'||:mes||'/'||(:ano-1), 'dd/mm/yyyy') <= TO_DATE('01/'||mes||'/'||ano, 'dd/mm/yyyy')) AND (TO_DATE('01/'||mes||'/'||ano, 'dd/mm/yyyy') <= TO_DATE('01/'||:mes||'/'||:ano, 'dd/mm/yyyy')) ORDER BY ano DESC, mes DESC";
        $params=array();
        $params[':estid']=(string)$estid;
        $params[':mes']=(int)$mes_actual;
        $params[':ano']=(int)$ano_actual;
        
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        
        $result = array();
        while ($this->db->next_record())
        {
            $cuestionario=new EmpleoCuestionario();
            $cuestionario->id = $this->db->f("id");
            $cuestionario->id_establecimiento = $this->db->f("id_establecimiento");
            $cuestionario->id_usuario = $this->db->f("id_usuario");
            $cuestionario->tipo_carga = $this->db->f("id_tipo_carga");
            $cuestionario->mes = $this->db->f("mes");
            $cuestionario->ano = $this->db->f("ano");
            $cuestionario->fecha_recepcion = DateTime::createFromFormat('d/m/Y', $this->db->f('fr'));
            if($this->db->f('fc') != null)
            {
                $cuestionario->fecha_cierre = DateTime::createFromFormat('d/m/Y', $this->db->f('fc'));
            }
            
            $result[] = $cuestionario;
        }
        
        return $result;
    }
    
    public function getNumeroEncuestas($estid, $id_empleador=null, $externos=null)
    {
        $params=array();
        $params[':idestab']=$estid;
        $sql="SELECT COUNT(*) AS NUMERO FROM TB_EMPLEO_CUEST A, TB_EMPLEO_CUEST_DET B WHERE A.ID=B.ID AND A.ID_ESTABLECIMIENTO=:idestab";
        
        if($id_empleador!=null)
        {
            $sql.=" AND B.ID_EMPLEADOR=:idempleador";
            $params[':idempleador']=$id_empleador;
        }
        if($externos!=null)
        {
            $sql.=" AND B.EXTERNO=:externo";
            $params[':externo']=$externos;
        }
        
        $sql = DbHelper::prepare_sql($sql,$params);
        $this->db->query($sql);
        
        $res=0;
        if($this->db->Error==FALSE)
        {
            if ($this->db->next_record())
            {
                $res=(int)$this->db->f('numero');
            }
        }
        
        return $res;
    }
}
