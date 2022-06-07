<?php
/*
 * Funciones envolventes del control de PHPLIB de las paginas.
 */
require_once(__DIR__."/../config.php");
require_once(__DIR__."/Autenticacion.class.php");
require_once(__DIR__."/SesionActiva.class.php");
require_once(__DIR__."/../classes/audit/AuditLog.class.php");

define('ARG_REF','ref');

class PageHelper
{
	protected $isSessionOpen = FALSE; 
	private $isSessionInherited = FALSE;
	
	/**
	 * Si se establece mediante la funcion set_nav_handler, las llamadas a build_url inyectan la url que se indica 
	 * en esta variable para que se llame a la pagina nav_handler antes de llamar a la otra url.
	 * @var string
	 */
	private $nav_handler;
	
	private $nav_exceptionList;
	
	protected $redirectUrlIfNotAllowed;
	
	var $encoding;
	
	var $auto_init  = 1;       ## load this file on session start
	
	public function __construct()
	{
		$this->encoding = "ISO-8859-1";
	}
	
	/**
	 * 	Comienza o continua la sesion. Acepta un numero variable de argumentos, cada uno indicando 
	 *  un tipo de permiso de acceso.
	 *  
	 *  Ejemplo: start_session(PERM_ADMIN, PERM_USER): Continua/comienza la sesion si es admin O user.
	 *  
	 *  Si se llama sin parametros, cualquier llamada comenzara/continuara la sesion. 
	 *  
	 *  Si no supera el filtro de permisos, no se abre la sesion.
	 *  
	 * @return boolean Devuelve verdadero si es posible comenzar/continuar la sesion (se cumple al menos un permiso), Falso en caso contrario (no abriendo la sesion en este caso).
	 */ 
	private function start_session($permsToCheck, $is_logout = false)
	{
		//global $perm, $auth;
		
		session_set_cookie_params(SESSION_TIMEOUT * 60,ini_get('session.cookie_path'),ini_get('session.cookie_domain'),ini_get('session.cookie_secure'),ini_get('session.cookie_httponly'));
		
		$sessid = session_id();
		if (empty($sessid)) 
		{
			//1. Cargar sesion
			session_start();
			
			$sesion_activa=new SesionActiva();
						
			//2. Autenticar
			if (isset($_SESSION["auth"]))
			{
				$auth = $_SESSION["auth"];
			}
			else
			{
				$sesion_activa->crear();
				$auth = new Autenticacion();
				$_SESSION["auth"] = $auth;
			}
			
			if ($is_logout)
			{
				$estabid=null;
				$estData = $this->get_sess_state(SESS_ESTAB);
				if ($estData != null)
					$estabid=$estData['id'];
				
				//$test=SesionActiva::getSesionesActivas();
				$sesion_activa->borrar();
				SesionActiva::purgarSesiones();
				
				// Si la sesión caduca no realiza el registro de final de sesión.
				if (isset($auth->uid))
					@AuditLog::log($auth->uid, $estabid, FINALIZA_SESION, SUCCESSFUL);
				//else
				//	@AuditLog::log("  ", $estabid, FINALIZA_SESION, FAILED, array("Sesion caducada."));
				return false;
			}
			
			$ok = $auth->start();
			
			if ($auth->just_logged)
			{
				/// Recien logeados iniciar con un nuevo id de sesion para no cargar los datos de sesiones anteriores
				/// por si el cliente tuviera todavia una cookie de sesion de php.
				session_regenerate_id(true);
				$_SESSION["auth"] = $auth;
				
				$sesion_activa->regenerar($auth->uid);
				
				if ($this->auto_init != null)
				{
					// Load the auto_init-File, if one is specified.
					$this->log_session_start();		
				}
				
			}	
			else
				$sesion_activa->refrescar($auth->uid);
		}
		else
		{
			/// Ya hay sesion abierta (se ha llamado a este metodo desde una pagina que ya ha llamado a start_session.
			$this->isSessionInherited = TRUE;
		}
					
		$this->isSessionOpen = TRUE;
		
		//Renueva la fecha de expiración de la cookie de sesion (esto sólo es necesario si el cookie ya venía en la petición)
		if(isset($_COOKIE[session_name()]) && ($_COOKIE[session_name()]==session_id()))
			setcookie(session_name(),session_id(),time() + SESSION_TIMEOUT * 60, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
					
		if (!$this->have_any_perm($permsToCheck))
		{
			$this->end_session();
			return FALSE;
		}
		
		return TRUE;
	}

	
	public function log_session_start()
	{
		$var_http_referer=(isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : '');
		$var_http_remote_addr=$_SERVER['REMOTE_ADDR'];
		$var_http_http_user=$_SERVER['HTTP_USER_AGENT'];
		
		$db  = new Istac_Sql;
		$tab = "tb_session_stats";
		
		$sessid = session_id();
		$sessid = substr($sessid, 0, 32);
		
		$now = date("YmdHis", time());
		$query = sprintf("insert into %s ( name,  sid, start_time, referer, addr, user_agent ) values ( '%s', '%s',       '%s',    '%s', '%s',       '%s' )",
				$tab,
				(isset($_SESSION['auth'])? $_SESSION['auth']->uid : ''),
				$sessid,
				$now,
				$var_http_referer,
				$var_http_remote_addr,
				$var_http_http_user);
		
		@$db->query($query);		
	}
	
	public function end_session()
	{
		//global $auth;
		
		if ($this->isSessionOpen)
		{
			if (!$this->isSessionInherited) 
			{
				if (isset($_SESSION["auth"]))
				{
					$auth = $_SESSION["auth"];
				}
				else 
				{
					$auth = new Autenticacion();
				}
				
				if ($auth->is_authenticated())
					session_write_close();
				//else
				//	session_destroy();
				//page_close();
			}
			$this->isSessionOpen = FALSE;
		}
	}
		
	public function start_session_check($permsToCheck, $is_logout = false)
	{
		/// Comienzo de sesion si no está ya abierta y comprobacion de permisos
		if ($this->start_session($permsToCheck, $is_logout))
			return;
		
		/// Redireccion a la pagina dada si no se tiene permisos para acceder.
		$this->logout();
		$this->client_redirect($this->redirectUrlIfNotAllowed, NULL, FALSE);
		exit();
	}
	
	/**
	 *  Comprueba si se tiene permisos para alguno de los indicados.
	 */
	public function have_any_perm($permsToCheck)
	{
		if (isset($_SESSION["auth"]))
		{
			$auth = $_SESSION["auth"];
		}
		else
		{
			$auth = new Autenticacion();
		}
		
		
		$perm = new Permisos();
		
		//global $perm;
		
		/// Comprueba si la lista variable de parametros (que indican permisos)
		/// se cumple alguno de ellos.
		if (!is_array($permsToCheck))
			$permsToCheck = explode(",",$permsToCheck);
			
		$allowed = FALSE;
		foreach ($permsToCheck as $p)
		{
			if ($perm->have_perm($p, $auth->perm))
			{
				$allowed = TRUE;
				break;
			}
		}

		return $allowed;
	}
	
	public function get_auth_data($authvarname)
	{
		if (isset($_SESSION["auth"]))
		{
			$auth = $_SESSION["auth"];
		}
		else
		{
			$auth = new Autenticacion();
		}
		
		if (isset($auth->$authvarname))
			return $auth->$authvarname;
		return null;
	}
	
	public function set_auth_data($authvarname, $value)
	{
		if (isset($_SESSION["auth"]))
		{
			$auth = $_SESSION["auth"];
		}
		else
		{
			$auth = new Autenticacion();
		}
	
		$auth->$authvarname = $value;
	}
	
	/**
	 * Verdadero si acaba de iniciarse la sesion
	 */
	public function just_logged()
	{
		if (isset($_SESSION["auth"]))
		{
			$auth = $_SESSION["auth"];
		}
		else
		{
			$auth = new Autenticacion();
		}
		
		return $auth->just_logged;
	}
	
	public function logout()
	{
		if (isset($_SESSION["auth"]))
		{
			$auth = $_SESSION["auth"];
			$auth->logout();
		}
		$sessid = session_id();
		if (!empty($sessid))
		{
			session_destroy();
		}
	}

	
	public function get_request_params($argnames)
	{
		$req = array();
		if ($this->is_get())
		{	
			$req = $_GET;
		}
		else if ($this->is_post())
		{
			$req = $_POST;
		}
		else 
		{
			Log::error("Metodo de petición de página no soportado");	
		}
		
		$values = array();
		foreach($argnames as $arg)
		{
			$values[$arg] = isset($req[$arg])? $req[$arg] : NULL;
		}
		return $values;
	}
			
	public function request_get($name, $default = NULL)
	{
		return (isset($_GET[$name])) ? $_GET[$name]: $default;
	}
	
	public function request_post($name, $default = NULL)
	{
		return (isset($_POST[$name])) ? $_POST[$name]: $default;
	}
	
	public function request_post_or_get($name, $default = NULL)
	{
		///TODO: Comprobar que el argumento es valido y no incumple caracteres especiales....
		return (isset($_REQUEST[$name])) ? $_REQUEST[$name]: $default;
	}
	
	public function is_post()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
		
	public function is_get()
	{
		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}
	
	public function unset_sess_state($varname)
	{
		unset($_SESSION[$varname]);
	}
		
	public function set_sess_state($varname, $value)
	{
		$_SESSION[$varname] = $value;
	}
	
	
	public function get_sess_state($varname)
	{
		if (isset($_SESSION[$varname]))
			return $_SESSION[$varname];
		return null;
	}
	
	public function set_nav_handler($url, $params = array(), $navexceptionList = array())
	{
		$this->nav_handler = $this->build_url_internal($url, $params, TRUE);
		$this->nav_exceptionList = $navexceptionList;
	}
	
	public function self_url($params = array(), $preserveSession = TRUE) 
	{
		return $this->build_url_internal($_SERVER["PHP_SELF"], $params, $preserveSession);
	}
	
	private function decompose_url($url)
	{
		/// Comprobar si la cadena tiene parametros, separarla en la primera parte de la cadena hasta "?"
		/// y la segunda parte descompuesta en un array.
		$posInt = strpos( $url, "?");
		if ($posInt !== FALSE)
		{
			parse_str(ltrim(substr( $url , $posInt), "?"), $url_params);
			$url = strstr($url, "?", TRUE);
			return array($url, $url_params);
		}	
		return array($url, array());	
	}
	
	private function build_url_internal($url, $params = array(), $preserveSession = TRUE)
	{
		//global $sess;
		
		global $ifilter;
		
		$url = $ifilter->process($url);
		
		list($url, $url_params) = $this->decompose_url($url);
				
		/// Unir los parametros, dandole prioridad a $params.
		$params = array_merge((array)$url_params, (array)$params);
		
		/// Y unir las variables de sesion.
		//if ($preserveSession && $this->isSessionOpen && isset($sess))
		//	$params = array_merge( $params, array( urlencode($sess->name) => $sess->id ));
			
		/// Rehacer la cadena.
		if (empty($params))
			return $url;
		else
			return $url . "?" . http_build_query($params);
	}
	
	public function build_url($url, $params = array())
	{
		$retUrl = $this->build_url_internal( $url, $params, $this->isSessionOpen);
		if (isset($this->nav_handler))
		{
			/// Comprobar si es una redireccion a si misma y evitarlo
			$posInt = strpos($this->nav_handler, $url);
			if ($posInt !== FALSE && $posInt == 0)
			{
				/// Es una redireccion a si misma, evitarlo.
				return $retUrl;
			}
			/// Comprobar si es una direccion de la lista de excepciones y evitarlo
			if (isset($this->nav_exceptionList))
			{
				list($url, $url_params) = $this->decompose_url($url);
				if (in_array($url, $this->nav_exceptionList))
				{
					/// Es una direccion de la lista de excepciones, evitarlo.
					return $retUrl;
				}
			}
			
			return $this->build_url_internal($this->nav_handler, array(ARG_REF => htmlentities($retUrl)));
		}
		else
			return $retUrl;
	}
		
	public function client_redirect($url, $params = array(), $preserveSession = TRUE)
	{
		$built_url = $this->build_url_internal($url, $params, $preserveSession);
		
		$uri_sin_parametros = "";
		//Evitar redireccion a si mismo.
		if (!stristr($_SERVER['REQUEST_URI'], "?"))
			$uri_sin_parametros = $_SERVER['REQUEST_URI'];
		else
			$uri_sin_parametros = stristr($_SERVER['REQUEST_URI'], "?", true);
		
		$isSelfRedirect = substr($uri_sin_parametros, -strlen($built_url))===$built_url;
		if ($isSelfRedirect)
			return;

		header("location: ". $built_url);
	}
	

	protected function default_vars()
	{
	}
	
	public function renderCSV($contentFile, $vars = array(), $filename)
	{
		
		header("Content-type: text/csv; charset=".$this->encoding);
		header("Content-Disposition: attachment; filename=" . $filename);
		if (isset($this->isSessionOpen) && $this->isSessionOpen)
		{
			// NOTA: Prioridad a keys en vars si aparecen en ambos arrays.
			$variables = array('page' => $this) + $this->default_vars() + $vars;
		}
		else
		{
			$variables = $vars;
		}
			
		// Esta variable defeine la ruta completa para ser usada dentro de page_template.php.
		$contentFileFullPath = __DIR__."/../views/" . $contentFile;
	
		// making sure passed in variables are in scope of the template
		// each key in the $variables array will become a variable
		if (count($variables) > 0) {
			foreach ($variables as $key => $value) {
				if (strlen($key) > 0) {
					${
						$key} = $value;
				}
			}
		}
		require_once($contentFileFullPath);
	}
	
    public function render($contentFile, $vars = array(), $includeHeader = true, $includeBanner = false)  
    {  
    	header("Content-Type: text/html; charset=".$this->encoding);
		//if (isset($this->isSessionOpen) && $this->isSessionOpen)
		//{
			// NOTA: Prioridad a keys en vars si aparecen en ambos arrays.
			$variables = array('page' => $this) + $this->default_vars() + $vars;
		//}
		//else
		//{
		//	$variables = $vars;
		//}
			
		// Esta variable define la ruta completa para ser usada dentro de page_template.php.
        $contentFileFullPath = __DIR__."/../views/" . $contentFile;  
		
        // making sure passed in variables are in scope of the template  
        // each key in the $variables array will become a variable  
        if (count($variables) > 0) {  
            foreach ($variables as $key => $value) {  
                if (strlen($key) > 0) {  
                    ${$key} = $value;  
                }  
            }  
        }  
		require_once(__DIR__."/../templates/page_template.php");  
    }  	

    public function render_ajax($contentFile, $vars = array())
    {
    	///NOTA: AJAX en PHP solo soporta UTF-8, por lo que la salida de ajax siempre sera utf-8.
    	//header("Content-Type: text/html; charset=UTF-8");
    	if (isset($this->isSessionOpen) && $this->isSessionOpen)
    	{
    		// NOTA: Prioridad a keys en vars si aparecen en ambos arrays.
    		$variables = array('page' => $this) + $this->default_vars() + $vars;
    	}
    	else
    	{
    		$variables = $vars;
    	}
    		
    	// Esta variable define la ruta completa para ser usada dentro de page_template.php.
    	$contentFileFullPath = __DIR__."/../views/" . $contentFile;
    
    	// making sure passed in variables are in scope of the template
    	// each key in the $variables array will become a variable
    	if (count($variables) > 0) {
    		foreach ($variables as $key => $value) {
    			if (strlen($key) > 0) {
    				${$key} = $value;
    			}
    		}
    	}
    	require_once($contentFileFullPath);
    }
    
    public function renderErrorMsg($errors, $estilo_msg)
    {
    	if ($errors != null)
    	{
    		foreach ($errors as $errormsg)
    		{
    			echo '<span id="errormsg" class="' . $estilo_msg . '">' . $errormsg . '</span>';
    		}
    	}
    }
    
    
    public function set_encoding($encoding = "ISO-8859-1")
    {
    	$this->encoding = $encoding;
    }
    
    public function convert_encoding($str)
    {
    	return mb_convert_encoding($str, $this->encoding);
    }
}
?>