<?php
require_once(__DIR__."/../classes/SQLLog.class.php");

//---------------------------------------------------------------------------------------------------------------------------------------------------------------
// Clase de acceso a base de datos.
//---------------------------------------------------------------------------------------------------------------------------------------------------------------
class Istac_Sql extends DB_Sql {
  var $Host     =  DB_HOST;
  var $Database  = "";
  var $User     =  DB_USER;
  var $Password =  DB_PASSWORD;
  
  var $transaccion=false;
  
  static protected $initiated=false;
  static public function DBInit()
  {
  	if(Istac_Sql::$initiated)
  		return;
  	
  	$conn=0;
  	$DBerr=null;
  	try
  	{
  		$conn=oci_pconnect(DB_USER, DB_PASSWORD, DB_HOST, DB_CHARACTER_SET );
  		if(!$conn)
  		{
  			$DBerr=oci_error();
  			throw new Exception('DB initialization error: OCILogon failed. '.$DBerr['message']);
  		}
  
  		$statement=oci_parse($conn,"alter session set NLS_NUMERIC_CHARACTERS = '.,'");
  		if(!$statement)
  		{
  			$DBerr=oci_error();
  			oci_close($conn);
  			throw new Exception('DB initialization error: OCIParse failed. '.$DBerr['message']);
  		}
  		oci_execute($statement);
  		$DBerr=oci_error();
  		oci_close($conn);
  		if($DBerr!=false)
  			throw new Exception('DB initialization error: OCIExecute failed. '.$DBerr['message']);
  	}
  	catch(Exception $e)
  	{
  		throw $e;
  	}
  	
  	SQLLog::init();
  	Istac_Sql::$initiated=true;
  }

  /**
   * Devuelve la información del error generado por la última orden SQL. Esta información se borra cuando la orden SQL se ejecutó sin errores. False si no está disponible.
   * @return boolean|array
   */
  function getLastError()
  {
      return isset($this->Parse)?oci_error($this->Parse):false;
  }
  
  /**
   * Función que llama a la implementación base, registrando el comando SQL en el fichero de log correspondiente. Se evita generar nuevos errores/excepciones debidas al registro.
   * @param unknown $Query_String Comando SQL a ejecutar
   * @param unknown $Query2Log [optional] Comando SQL a registrar en el fichero de log (si no está presente, se registra el comando del parámetro $Query_String. Puede usarse cuando el comando a ejecutar contenga información sensible como contraseñas.
   * @return number|mixed
   * {@inheritDoc}
   * @see DB_Sql::query()
   */
  function query($Query_String,$Query2Log=null)
  {
      // Debemos ser transparente a las peticiones con órdenes SQL vacías (muy probablemente generadas durante la instanciación. Por ejemplo, '$db = new DB_Sql_Subclass;').
      if ($Query_String == "")
          return parent::query($Query_String);
      
      if($Query2Log==null)
          $Query2Log=$Query_String;
      
      set_error_handler('sql_error_handler');
      try
      {
          SQLLog::$lastQuery=$Query2Log;
          SQLLog::$conexion=$this;
          $res=parent::query($Query_String);
          SQLLog::$lastQuery=null;
          SQLLog::$conexion=null;
          @SQLLog::log($Query2Log, isset($this->Parse)?oci_error($this->Parse):false);
          restore_error_handler();
          return $res;
      }
      catch (Exception $e)
      {
          if(SQLLog::$lastQuery==null)
          {
              // La excepción se ha debido seguramente a la forma de obtener información del problema. Evitamos volver a solicitar esta información.
              @SQLLog::log($Query2Log, false,"Error DB N/A.");
          }
          else
          {
              // Se ha producido una excepción durante la ejecución de la orden SQL.
              @SQLLog::log($Query2Log, isset($this->Parse)?oci_error($this->Parse):false);
              restore_error_handler();
              throw $e;
          }
          //$DBerr=oci_error();
          //throw new Exception('DB error: Query failed. '.$DBerr['message']);
      }
      restore_error_handler();
  }
  
  /**
   * Función que llama a la implementación base directamente, sin almacenar información en el log. Se debería usar cuando existe peligro de relevar contraseñas.
   * @param unknown $Query_String
   * @return number|mixed
   */
  function queryRaw($Query_String)
  {
      return parent::query($Query_String);
  }
  
  function query_bind($Query_String, array $bindValues)
  {
      $res=parent::query_bind($Query_String, $bindValues);
      SQLLog::log($Query_String, isset($this->Parse)?oci_error($this->Parse):false);
      return $res;
  }
  
  function nextid($seqname)
  {
      $res=parent::nextid($seqname);
      $query="SELECT $seqname.NEXTVAL FROM DUAL";
      SQLLog::log($query, isset($this->Parse)?oci_error($this->Parse):false);
      return $res;
  }
  
  function lock($table, $mode = "write")
  {
      $res=parent::lock($table,$mode);
      SQLLog::log("Bloqueo de la tabla $table en modo $mode",isset($this->Parse)?oci_error($this->Parse):false);
      return $res;
  }
  
  /**
   * Inicia una transacción.
   * @return boolean Devuelve true si todo fue bien o false en caso contrario.
   */
  function beginTrans()
  {
      if($this->transaccion)
      {
          @SQLLog::log("Error: transacción ya abierta.", false);
          return false;
      }
      $this->transaccion=true;
      return true;
  }
  
  /**
   * Indica si se está dentro de una transacción.
   * @return boolean true si se está dentro de una transacción o false en caso contrario.
   */
  function isTrans()
  {
      return ($this->transaccion);
  }
  
  /**
   * Cierra una transacción confirmando todos los cambios a la BDD.
   * @return boolean Devuelve true si todo fue bien o false en caso contrario.
   */
  function commit()
  {
      if($this->transaccion==false)
      {
          @SQLLog::log("Error: Commit sin transacción abierta.", false);
          return false;
      }
      $this->transaccion=false;
      
      $ora_conn=$this->Link_ID;
      if ($ora_conn==false)
      {
          @SQLLog::log("Error: Commit sin conexión abierta.", false);
          return false;
      }
      return oci_commit($ora_conn);
  }
  
  /**
   * Cierra una transacción revertiendo todos los cambios a la BDD.
   * @return boolean Devuelve true si todo fue bien o false en caso contrario.
   */
  function rollback()
  {
      if($this->transaccion==false)
      {
          @SQLLog::log("Error: Rollback sin transacción abierta.", false);
          return false;
      }
      $this->transaccion=false;
      
      $ora_conn=$this->Link_ID;
      if ($ora_conn==false)
      {
          @SQLLog::log("Error: Rollback sin conexión abierta.", false);
          return false;
      }
      return oci_rollback($ora_conn);
  }
  
  /**
   * Ejecuta un query dentro de una transacción.
   * Función que llama a la implementación base, registrando el comando SQL en el fichero de log correspondiente. Se evita generar nuevos errores/excepciones debidas al registro.
   * @param unknown $Query_String Comando SQL a ejecutar
   * @param unknown $Query2Log [optional] Comando SQL a registrar en el fichero de log (si no está presente, se registra el comando del parámetro $Query_String). Puede usarse cuando el comando a ejecutar contenga información sensible como contraseñas.
   * @return number|mixed
   * {@inheritDoc}
   * @see DB_Sql::query()
   * @throws Exception
   * @return number|unknown
   */
  function queryTrans($Query_String,$Query2Log=null)
  {
      // Debemos ser transparente a las peticiones con órdenes SQL vacías (muy probablemente generadas durante la instanciación. Por ejemplo, '$db = new DB_Sql_Subclass;').
      if ($Query_String == "")
          return 0;
          
      if($Query2Log==null)
          $Query2Log=$Query_String;
          
      if($this->transaccion==false)
      {
          @SQLLog::log("Error: Query usando transacción cerrada o aún no abierta. ".$Query2Log, false);
          return false;
      }
      
      set_error_handler('sql_error_handler');
      try
      {
          SQLLog::$lastQuery=$Query2Log;
          SQLLog::$conexion=$this;
          
          // Código equivalente al DB_Sql::query
          $this->connect();
          $this->Parse=OCIParse($this->Link_ID,$Query_String);
          if(!$this->Parse)
          {
              $this->Error=OCIError($this->Parse);
          }
          else
          {
              OCIExecute($this->Parse,OCI_DEFAULT);
              $this->Error=OCIError($this->Parse);
          }
          $this->Row=0;
          if($this->Debug)
          {
              printf("Debug: query = %s<br>\n", $Query_String);
          }
          $res=$this->Parse;
          
          SQLLog::$lastQuery=null;
          SQLLog::$conexion=null;
          @SQLLog::log($Query2Log, isset($this->Parse)?oci_error($this->Parse):false);
          restore_error_handler();
          return $res;
      }
      catch (Exception $e)
      {
          if(SQLLog::$lastQuery==null)
          {
              // La excepción se ha debido seguramente a la forma de obtener información del problema. Evitamos volver a solicitar esta información.
              @SQLLog::log($Query2Log, false,"Error DB N/A.");
          }
          else
          {
              // Se ha producido una excepción durante la ejecución de la orden SQL.
              @SQLLog::log($Query2Log, isset($this->Parse)?oci_error($this->Parse):false);
              restore_error_handler();
              throw $e;
          }
          //$DBerr=oci_error();
          //throw new Exception('DB error: Query failed. '.$DBerr['message']);
      }
      restore_error_handler();
  }
  
}
                                
?>