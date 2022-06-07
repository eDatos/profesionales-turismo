<?php
class HttpRecord {
	public static function record()
	{
		if(defined('HTTP_LOG_PATH') && HTTP_LOG_PATH)
		{
			$file=HTTP_LOG_PATH.'/http_'.date("Ymd_His").'_'.$_SERVER['REQUEST_TIME'];
			
			$req=$_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI'].' '.$_SERVER['SERVER_PROTOCOL'].PHP_EOL;
			$headers=getallheaders();
			if(isset($headers))
			{
				foreach ($headers as $nombre=>$valor)
				{
					$req .= $nombre.':'.$valor.PHP_EOL;
				}
			}
			$req .= PHP_EOL;
			$req .= file_get_contents('php://input');
			
			file_put_contents($file, $req, FILE_APPEND | LOCK_SH);
		}
	}
}

HttpRecord::record();

?>