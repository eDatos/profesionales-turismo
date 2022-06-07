<?php

require_once(__DIR__."/../config.php");
require_once(__DIR__."/../lib/DateHelper.class.php");
require_once(__DIR__."/../lib/DbHelper.class.php");
require_once(__DIR__."/EmpleadoresDao.class.php");

/**
 * Clase que engloba las operaciones relacionadas con las encuestas de empleo.
 * @author SCC,SL
 *
 */
class EmpleoController
{
    /**
     * Objeto DAO a usar en las operaciones.
     * @var EmpleadoresDao
     */
    var $dao;
    
    /**
     * Constructo básico. Inicializa el objeto DAO requerido.
     */
    public function __construct()
    {
        $this->dao = new EmpleadoresDao();
    }

    public function crear_nuevo_cuestionario($mes, $ano, $est_id, $user_id, $tipo_carga, $guardar)
    {
        //$cuestionario = new EmpleoCuestionario($mes, $ano, $est_id, $user_id);
        $cuestionario = new EmpleoCuestionario();
        $cuestionario->mes=$mes;
        $cuestionario->ano=$ano;
        $cuestionario->id_establecimiento=$est_id;
        $cuestionario->id_usuario=$user_id;
        
        $cuestionario->tipo_carga = $tipo_carga;
        $cuestionario->fecha_recepcion = new DateTime('now');
        $cuestionario->numero_empleados=array();
        
        if ($guardar)
        {
            $this->dao->guardar_cuestionario($cuestionario);
        }
        return $cuestionario;
    }
    
    /**
     * Carga la encuesta correspondiente al establecimiento, usuario, mes y año indicados (o la actual si ambos son NULL). Con los datos 
     * @param Establecimiento $establecimiento
     * @param string $user_id
     * @param int $mes_encuesta
     * @param int $ano_encuesta
     * @param boolean $rdonly
     * @return EmpleoCuestionarioEstado
     */
    public function cargar_encuesta($establecimiento, $user_id, $mes_encuesta = NULL, $ano_encuesta = NULL, $rdonly = FALSE)
    {
        // Cargamos la encuesta actual (en curso).
        $empleo_estado=$this->cargar_estado_encuesta($establecimiento, $user_id);
        $empleo_enc = $empleo_estado->encuesta;
        
        /// Si no estan definidos por parametros, se definen automaticamente segun el calculo hecho en cargar_estado_encuesta.
        if ($mes_encuesta==NULL && $ano_encuesta==NULL)
        {
            $mes_encuesta = $empleo_enc->mes;
            $ano_encuesta = $empleo_enc->ano;
        }
        
        // Marcamos la encuesta como sólo lectura si:
        // [1] Se ha solicitado con el parámetro $rdonly igual a TRUE (probablemente un usuario consultando un cuestionario anterior).
        // [2] Si el mes o año de la encuesta actual no coinciden con el solicitado (o sea, es un cuestionario histórico).
        // [3] Si el cuestionario ha sido marcado como cerrado.
        $empleo_estado->es_rdonly = ($rdonly || $empleo_enc->mes != $mes_encuesta || $empleo_enc->ano != $ano_encuesta || $empleo_enc->esta_cerrado());
        
        /// Cargar los datos del cuestionario para el mes y año
        if ($empleo_estado->es_rdonly)
        {
            /// Recargamos el cuestionario sólo si no es el actual.
            if($empleo_enc->mes != $mes_encuesta || $empleo_enc->ano != $ano_encuesta)
            {
                $empleo_enc = $this->dao->cargar_cuestionario($establecimiento->id_establecimiento, $mes_encuesta, $ano_encuesta);
                $empleo_estado->encuesta = $empleo_enc;
            }
            
            if ($empleo_enc == null)
            {
                /// No existe cuestionario para esa fecha, abortar la operacion ($empleo_estado->encuesta sera nulo)
                return $empleo_estado;
            }
        }
        
        /// Carga los datos del establecimiento valido en el mes y ano de la encuesta.
        /// el criterio seguido es que los datos a dia 1 del mes de la encuesta son los correctos.
        $fecha_efectiva_datos = DateHelper::parseDate(sprintf("%02d-%02d-%04d", 1, $empleo_enc->mes, $empleo_enc->ano));
        if ($fecha_efectiva_datos < $establecimiento->fecha_alta)
        {
            // Carga los datos del establecimiento valido en el mes y ano de la encuesta.
            $est_id = $establecimiento->id_establecimiento;
            $est2 = new Establecimiento();
            $est2->cargar_por_fecha($est_id, $fecha_efectiva_datos);
            $empleo_estado->datos_estab = $est2;
        }
        else
        {
            /// Los datos actuales son valido para el mes/año de la encuesta.
            $empleo_estado->datos_estab = $establecimiento;
        }
        
        return $empleo_estado;
    }
    
    /**
     * Obtiene un objeto EmpleoCuestionarioEstado asociado a la encuesta en curso.
     * Si existe ya un cuestionario en BDD, lo carga. En caso contrario, lo crea.
     * @param Establecimiento $estab Establecimiento asociado a la encuesta.
     * @param string $user_id Código único del usuario que rellena la encuesta.
     * @return EmpleoCuestionarioEstado Objeto que contiene los datos de la encuesta.
     */
    public function cargar_estado_encuesta($estab, $user_id)
    {
        /// 0: Calcula que encuesta está en curso según la fecha de plazo y el estado de la misma.
        
        /// Calcula el mes de la encuesta y despues carga los datos de la encuesta.
        // 1. Solo pueden ser dos meses, el mes actual o el mes anterior.
        // 2. Puede ser el mes anterior si:
        //		- El establecimiento no ha cerrado ya la encuesta.
        //		- Con la encuesta abierta, si se está dentro del plazo.
        
        $dt = new DateHelper();
        $dia_actual = $dt->dia_sistema;
        $mes_actual = $dt->mes_sistema;
        $ano_actual = $dt->anyo_sistema;
        
        list($mes_anterior, $ano_anterior) = DateHelper::mes_anterior($mes_actual, $ano_actual);
        
        $empleo_cuest = $this->dao->cargar_cuestionario($estab->id_establecimiento, $mes_anterior, $ano_anterior);
        
        /// Indica si el cuestionario está en captura aún (true) o no (false).
        $es_encuesta_abierta = false;
        
        if ($empleo_cuest == null || !$empleo_cuest->esta_cerrado())
        {
            /// Comprobar si se esta en plazo del mes anterior todavia, pero con el tipo de establecimiento efectivo el mes anterior.
            //Cargar el tipo de establecimiento para el mes_anterior (evitar hacerlo si los datos disponibles son iguales).
            /// Se considera que la fecha efectiva para el mes anterior son los datos del día 1.
            $fecha_efectiva_datos = DateHelper::parseDate(sprintf("%02d-%02d-%04d", 1, $mes_anterior, $ano_anterior));
            if ($estab->fecha_alta <= $fecha_efectiva_datos)
            {
                $est2 = $estab;
            }
            else
            {
                $est2 = new Establecimiento();
                $est2->cargar_por_fecha($estab->id_establecimiento, $fecha_efectiva_datos);
            }
            
            //2. Comprobar si dentro de plazo.
            
            $dia_plazo = $this->dao->cargar_dia_plazo($estab->id_establecimiento,$est2->get_grupo(),$mes_anterior,$ano_anterior);
            if ($dia_actual <= $dia_plazo)
            {
                $es_encuesta_abierta = true;
            }
        }
        
        $empleo_estado = new EmpleoCuestionarioEstado();
        //$empleo_estado = array();
        
        /// A: Cargar o inicializar la encuesta en curso
        if ($es_encuesta_abierta)
        {
            /// Inicializar un nuevo cuestionario si no existe.
            if ($empleo_cuest == null)
            {
                $empleo_cuest = $this->crear_nuevo_cuestionario($mes_anterior, $ano_anterior, $estab->id_establecimiento, $user_id, TIPO_CARGA_WEB, false);
            }
            // Ajustamos la fecha límite para rellenar este cuestionario.
            // Se puede rellenar todavia la encuesta anterior
            /*
            $empleo_estado['plazo'] = new DateTime();
            $empleo_estado['plazo']->setDate($ano_actual, $mes_actual, $dia_plazo);
            $empleo_estado['encuesta'] =  $empleo_cuest;
            */
            
            $empleo_estado->plazo = (new DateTime())->setDate($ano_actual, $mes_actual, $dia_plazo);
            $empleo_estado->encuesta =  $empleo_cuest;
        }
        else
        {
            // Cuestionario del mes anterior ya cerrado o no existente.
            // Cargamos la encuesta del mes actual completa por si hubiera sido grabado ya con algún dato provisional.
            $empleo_cuest = $this->dao->cargar_cuestionario($estab->id_establecimiento, $mes_actual, $ano_actual);
            if ($empleo_cuest == null)
            {
                $empleo_cuest = $this->crear_nuevo_cuestionario($mes_actual, $ano_actual, $estab->id_establecimiento, $user_id, TIPO_CARGA_WEB, false);
            }
            
            /// El mes anterior esta ya cerrado, solo puede ser el mes actual.
            /// o El mes anterior esta ya fuera de plazo, solo puede ser el mes actual.
            $dia_plazo = $this->dao->cargar_dia_plazo($estab->id_establecimiento,$estab->get_grupo(),$mes_actual,$ano_actual);
            list($mes_siguiente, $ano_siguiente) = DateHelper::mes_siguiente($mes_actual, $ano_actual);
            /*
            $empleo_estado['plazo'] = new DateTime();
            $empleo_estado['plazo']->setDate($ano_siguiente, $mes_siguiente, $dia_plazo);
            
            $empleo_estado['encuesta']  = $empleo_cuest;
            */
            
            $empleo_estado->plazo = (new DateTime())->setDate($ano_siguiente, $mes_siguiente, $dia_plazo);
            $empleo_estado->encuesta =  $empleo_cuest;
        }
        
        return $empleo_estado;
    }
    
    public function inicializar_cuestionario_ult_detalles(EmpleoCuestionario $cuestionario)
    {
        $this->dao->cargar_ultimos_detalles($cuestionario);
    }
    
    public function guardar_cuestionario(EmpleoCuestionario $cuestionario)
    {
        return $this->dao->guardar_cuestionario($cuestionario);
    }
    
    public function cerrar_cuestionario(EmpleoCuestionario $cuestionario)
    {
        if ($cuestionario->esta_cerrado())
        {
            return "El cuestionario ya está cerrado.";
        }
        $cuestionario->fecha_cierre=new DateTime();
        if($this->guardar_cuestionario($cuestionario))
        {
            return true;
        }
        return "Fallo al grabar el cuestionario. Reintente la operación y si el fallo persiste, avise al administrador.";
    }
    
    public function listarEmpleadores($estid, $estado=null, $externos=null)
    {
        return $this->dao->listarEmpleadores($estid, $estado, $externos);
    }
}