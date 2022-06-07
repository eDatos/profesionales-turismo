<?php

class SQLLog {
    protected static $inicializada=false;
    protected static $SQLlogFile;
    
    /**
     * Última conexión usada a efectos de registro en el fichero de log correspondiente.
     */
    static public $conexion=null;
    
    /**
     * Última query lanzada a efectos de registro en el fichero de log correspondiente.
     */
    static public $lastQuery=null;
    
    
    static function init()
    {
        if(SQLLog::$inicializada==false)
        {
            SQLLog::$inicializada=true;
            if(LOG_SQL_ENABLED)
            {
                SQLLog::$SQLlogFile=null;
                try
                {
                    SQLLog::$SQLlogFile=dirname(ini_get('error_log')).'/PWET_SQL.log';
                }
                catch(Exception $e)
                {
                    SQLLog::$SQLlogFile=null;
                }
            }
        }
    }
    
	public static function log($query,$res,$comentario=null)
	{
	    if(SQLLog::$SQLlogFile!=null)
	    {
	        //$msg='['.$_SERVER['REQUEST_TIME'].'] '.$_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI'].' '.$_SERVER['SERVER_PROTOCOL'].PHP_EOL;
	        $msg=date("[d-m-Y H:i:s e]");
            $user_id='N/A';
            $esta_id='N/A';
            if (isset($_SESSION['auth']))
            {
                $auth = $_SESSION['auth'];
                if (isset($auth->uname))
                {
                    $user_id=$auth->uname;
                }
            }
            if (isset($_SESSION['estab_data']))
            {
                $estData = $_SESSION['estab_data'];
                if (isset($estData->id))
                {
                    $esta_id=$estData->id;
                }
            }
            $msg.=sprintf(" [%s,%s]",$user_id,$esta_id);
	        if($res==FALSE)
	            $msg.=' [OK]';
            else
                $msg.=sprintf(" [%d,%s]",$res['code'],$res['message']);
            $query = str_replace(array("\t", "\n", "\r"), '', $query);
	        $msg.=' '.$query;
            if(!empty($comentario))
                $msg.=' # '.$comentario;
            $msg.=PHP_EOL;
            file_put_contents(SQLLog::$SQLlogFile, $msg, FILE_APPEND | LOCK_SH);
	    }
	}
}

//SQLLog::init();

/**
 * Error handler nulo que se debemos usar para los potenciales errores generados por la propia actividad de logging.
 * @param unknown $errno
 * @param unknown $errstr
 */
function sql_error_handler($errno, $errstr)
{
    // Silenciamos los errores por defecto.
    if((!(error_reporting() & $errno)) || (SQLLog::$lastQuery==null))
       return;
    
    try
    {
        if(isset(SQLLog::$conexion))
        {
            // Hay conexión disponible intentamos extraer la información del error.
            @SQLLog::log(SQLLog::$lastQuery, isset(SQLLog::$conexion->Parse)?oci_error(SQLLog::$conexion->Parse):false);
        }
        else
        {
            // No hay más información disponible.
            @SQLLog::log(SQLLog::$lastQuery, false,"Error DB N/A.");
        }
    }
    catch (\Exception $e)
    {
    }
    return;
}


?>