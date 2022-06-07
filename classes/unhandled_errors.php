<?php
$PWETlogFile=getMilogFile();

function getMilogFile()
{
	$resultado=null;
	try
	{
		$resultado=dirname(ini_get('error_log')).'/PWET_Unhandled.log';
	}
	catch(Exception $e)
	{
		$resultado=null;
	}
	return $resultado;
}

function Milog($msg)
{
	global $PWETlogFile;

	if($PWETlogFile==null)
		return;

	file_put_contents($PWETlogFile, $msg, FILE_APPEND | LOCK_SH);
}

function logHttp($headers)
{
    $hds=logUrl();
    $hds.="[HTTP Headers]\n";
	if(isset($headers))
	{
		foreach ($headers as $nombre=>$valor)
		{
			$hds .= $nombre.': '.$valor."\n";
		}
	}
	$hds .= "------\n";

	return $hds;
}

function logUrl()
{
    $peticion="URL: (".$_SERVER['REQUEST_METHOD'].") ";
    $peticion.=(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on')) ? "https://" : "http://";
    $peticion.=$_SERVER['HTTP_HOST'];
    $peticion.=$_SERVER['PHP_SELF'];
    $peticion.="\n";
    
    return $peticion;
}

function errorHandler($errno, $errstr, $errfile, $errline)
{
	try
	{
		$fechaHora=date("[d-m-Y H:i:s e]");
			
		try
		{
			$msg = "%s PHP error:  Uncaught error %s with message '%s' in %s:%s\n";
			$msg = sprintf($msg,$fechaHora,$errno,$errstr,$errfile,$errline);
		  
		}
		catch(Exception $e)
		{
			$msg=$fechaHora." PHP error:  Uncaught error. Error al procesar la excepción.";
		}
		
		Milog($msg);
		Milog(logHttp(getallheaders()));
	}
	catch(Exception $e)
	{
	}
	
	// Se permite que el error se mande al gestor interno de errores del PHP. O sea, el error puede acabar en otro fichero log de errores.
	return FALSE;
}

function exceptionHandler($exception)
{
	try
	{
		$fechaHora=date("[d-m-Y H:i:s e]");
			
		if($exception==NULL)
		{
			Milog($fechaHora." PHP Fatal error:  Uncaught EXCEPTION. No hay información adicional.");
			Milog(logHttp(getallheaders()));
			return;
		}

		$traceline = "#%s %s(%s): %s(%s)";
		$msg = "%s PHP Fatal error:  Uncaught EXCEPTION '%s' with message '%s' in %s:%s\nStack trace:\n%s\n";

		try
		{
		    $traza=" N/A";
			$trace = $exception->getTrace();
			if(count($trace)>0)
			{
			    foreach ($trace as $key => $stackPoint) {
			        // I'm converting arguments to their type
			        // (prevents passwords from ever getting logged as anything other than 'string')
			        $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
			    }
			    
			    $result = array();
			    foreach ($trace as $key => $stackPoint) {
			        $result[] = sprintf(
			            $traceline,
			            $key,
			            $stackPoint['file'],
			            $stackPoint['line'],
			            $stackPoint['function'],
			            implode(', ', $stackPoint['args'])
			            );
			    }
			    // trace always ends with {main}
			    if(isset($key))
			        $result[] = '#' . ++$key . ' {main}';
			    
			    $traza=implode("\n", $result);
			}
				
			// write tracelines into main template
			$msg = sprintf(
					$msg,
					$fechaHora,
					get_class($exception),
					$exception->getMessage(),
					$exception->getFile(),
					$exception->getLine(),
					$traza
					);
		}
		catch(Exception $e)
		{
			$msg=$fechaHora." PHP Fatal error:  Uncaught EXCEPTION. Error al procesar la excepción.";
		}

		Milog($msg);
		Milog(logHttp(getallheaders()));
	}
	catch(Exception $e)
	{
	}
	
	// Algo grave ha sucedido. Debemos abortar la ejecución y devolver error.
	header('HTTP/1.1 500 Internal Server Error');
	readfile( __DIR__."/../error.html");
	exit(0);
}

@set_exception_handler('exceptionHandler');
@set_error_handler('errorHandler');
?>
