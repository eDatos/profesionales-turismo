<?php

class registro_accesos
{
	function getRealIP()
	{
	   if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		  $HTTP_X_FORWARDED_FOR=$_SERVER['HTTP_X_FORWARDED_FOR'];
	   else
		  $HTTP_X_FORWARDED_FOR="";
	   if( $HTTP_X_FORWARDED_FOR != '' )
	   {
		  $client_ip =
			 ( !empty($_SERVER['REMOTE_ADDR']) ) ?
				$_SERVER['REMOTE_ADDR']
				:
				( ( !empty($_ENV['REMOTE_ADDR']) ) ?
				   $_ENV['REMOTE_ADDR']
				   :
				   "unknown" );
	 
		  // los proxys van añadiendo al final de esta cabecera
		  // las direcciones ip que van "ocultando". Para localizar la ip real
		  // del usuario se comienza a mirar por el principio hasta encontrar 
		  // una dirección ip que no sea del rango privado. En caso de no 
		  // encontrarse ninguna se toma como valor el REMOTE_ADDR
	 
		  $entries = preg_split('/[, ]/', $HTTP_X_FORWARDED_FOR);
	 
		  foreach($entries as $entry)
		  {
			 $entry = trim($entry);
			 if ( preg_match('/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/', $entry, $ip_list) )
			 {
				// http://www.faqs.org/rfcs/rfc1918.html
				$private_ip = array(
					  '/^0\./',
					  '/^127\.0\.0\.1/',
					  '/^192\.168\..*/',
					  '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
					  '/^10\..*/');
	 
				$found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
	 
				if ($client_ip != $found_ip)
				{
				   $client_ip = $found_ip;
				   break;
				}
			 }
		  }
	   }
	   else
	   {
		  $client_ip =
			 ( !empty($_SERVER['REMOTE_ADDR']) ) ?
				$_SERVER['REMOTE_ADDR']
				:
				( ( !empty($_ENV['REMOTE_ADDR']) ) ?
				   $_ENV['REMOTE_ADDR']
				   :
				   "unknown" );
	   }
	 
	   return $client_ip;
	}
	
	function getProxyIP()
	{
		  if(!isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			 return "";
		  $proxy_ip =
			 ( !empty($_SERVER['REMOTE_ADDR']) ) ?
				$_SERVER['REMOTE_ADDR']
				:
				( ( !empty($_ENV['REMOTE_ADDR']) ) ?
				   $_ENV['REMOTE_ADDR']
				   :
				   "unknown" );
	   return $proxy_ip;
	}
	
	
	function is_registro_enabled()
	{
		$db  = new Istac_Sql();
		$db->query("select registrar_ip from TB_CONFIGURATION");
		
		if($db->next_record()) {
			$db_registrar_ip = $db->f("registrar_ip");
		}
		if(!isset($db_registrar_ip))
			return false;
		
		if ($db_registrar_ip == 1)
			return true;
		return false;		
	}
	
	function registrar_fallo_acceso($user)
	{
		if (!$this->is_registro_enabled())
			return false;
		
		$ip = $this->getrealIP();
		$ip_proxy = $this->getproxyIP();
		$fecha = date("Y-m-d H:i:s");
		
		$db  = new Istac_Sql();      
		$db->query("insert into tb_registro_fallo_acceso(nombre_usuario,fecha,ip,ip_proxy) values('".$user."',to_date('".$fecha."','yyyy-mm-dd HH24:MI:SS'),'".substr($ip,0,16)."','".substr($ip_proxy,0,16)."')");
		return true;
	}

	function registrar_exito_acceso($usuario)
	{
		if (!$this->is_registro_enabled())
			return false;
		
		$db    = new Istac_Sql;
		$fecha = date("Y-m-d H:i:s");
                                              
        $sql="insert into tb_registro values('".$usuario."', to_date('$fecha','yyyy-mm-dd HH24:MI:SS'),'Acceso a la aplicación')";		        
        $db->query($sql);
		return true;
	} 
}
?>
