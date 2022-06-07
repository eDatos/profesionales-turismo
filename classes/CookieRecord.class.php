<?php

class CookieRecord {
	protected static $file=null;
	
	protected static function cookies()
	{
		$msg='';
		
		$headers=getallheaders();
		if(isset($headers))
		{
			foreach ($headers as $nombre=>$valor)
			{
				if($nombre=='Cookie')
					$msg.='Cookie: '.$valor.PHP_EOL;
			}
		}
		
		$hds=headers_list();
		if(isset($hds))
		{
			foreach ($hds as $hdr)
			{
				if(stripos($hdr, 'Set-Cookie:')!==false)
					$msg.=$hdr.PHP_EOL;
			}
		}
		
		return $msg;
	}
	
	public static function record()
	{
		if(defined('HTTP_LOG_PATH') && HTTP_LOG_PATH)
		{
			$file=HTTP_LOG_PATH.'/cookie_'.date("Ymd");
			
			$msg='['.$_SERVER['REQUEST_TIME'].'] '.$_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI'].' '.$_SERVER['SERVER_PROTOCOL'].PHP_EOL;
			switch(connection_status())
			{
				case CONNECTION_NORMAL:
					$msg.=CookieRecord::cookies();
					$msg.='Code response: '.http_response_code().'.';
					break;
				case CONNECTION_ABORTED:
					$msg.='Conexión abortada por el usuario.';
					break;
				case CONNECTION_TIMEOUT:
					$msg.='Conexión timeout.';
					break;
				case CONNECTION_ABORTED|CONNECTION_TIMEOUT:
					$msg.='Conexión timeout y abortada por el usuario.';
					break;
			}
			$msg.=PHP_EOL;
							
			file_put_contents($file, $msg, FILE_APPEND | LOCK_SH);
		}
	}
}

function shutdown_handler()
{
	CookieRecord::record();
}

register_shutdown_function('shutdown_handler');

?>