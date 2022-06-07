<?php

/**
 *	Control de permisos de acceso. 
 */
class Permisos
{
	
	var $permissions = array(
			PERM_USER        => 1,
			PERM_GRABADOR    => 4,
			PERM_RECEPCION   => 8,
			PERM_ADMIN_ISTAC => 16,
			PERM_ADMIN       => 32,
			PERM_CONSUMOS    => 64);
	
	/*
	 * Comprueba si perm_auth contiene los permisos p indicados.
	 * Devuelve verdadero si todos los permisos indicados en p están contenidos en perm_auth, falso en caso contrario.
	 */
	public function have_perm($p, $perm_auth)
	{
		if (! isset($perm_auth) )
		{
			$perm_auth = "";
		}
		//    $pageperm = split(",", $p);
		//    $userperm = split(",", $auth->auth["perm"]);
		$pageperm = explode(",", $p);
		$userperm = explode(",", $perm_auth);
	
		list ($ok0, $pagebits) = $this->permsum($pageperm);
		list ($ok1, $userbits) = $this->permsum($userperm);
	
		$has_all = (($userbits & $pagebits) == $pagebits);
		if (!($has_all && $ok0 && $ok1) ) {
			return false;
		} else {
			return true;
		}
	}
	
	##
	## Permission helpers.
	##
	private function permsum($p)
	{
		if (!is_array($p))
		{
			return array(false, 0);
		}
		$perms = $this->permissions;
	
		$r = 0;
		reset($p);
		while(list($key, $val) = each($p)) 
		{
			if (!isset($perms[$val])) 
			{
				return array(false, 0);
			}
			$r |= $perms[$val];
		}
		return array(true, $r);
	}
	
}


/**
 * Clase para control de autenticación del usuario.
 *
 */
class Autenticacion
{
	
	// Id. de usuario.
	var $uid;

	// Para indicar si es la prmiera visita de página después de la autenticación.
	var $just_logged;
	
	// Ultimo challenge generado.
	var $challenge;

	
	public function __construct()
	{
		$this->just_logged = false;
	}

	/**
	 * Comprueba si el usuario está autenticado o si la sessión no está caducada.
	 */
	public function is_authenticated()
	{
		if(isset($this->uid))
		{
			if (isset($this->last_activity) && (time() - $this->last_activity > (60 * SESSION_TIMEOUT)))
			{
				// last request was more than SESSION_TIMEOUT minutes ago
				return false;
			}
			return $this->uid;
		}
		return false;
	}
	
	/**
	 * Muestra el formulario de login.
	 */
	public function auth_loginform()
	{
		$challenge = md5(uniqid(SESSION_CHALLENGE_MAGIC)); 
		$this->challenge = $challenge;
		require_once(PAGE_LOGIN);
	}

	/**
	 * Valida las credenciales enviadas con el formulario de login.
	 */
	public function auth_validatelogin()
	{
		global $username, $password, $challenge, $response;

		$accesos = new registro_accesos();

		if (!isset($_POST["response"]) || !isset($_POST["username"]))
			return false;
		
		$response   = $_POST["response"];
		$username   = $_POST["username"];
		//$challenge  = $_POST["challenge"];
			
		$challenge = $this->challenge;

		$username = strtoupper($username);

		if (ENABLE_CAPTCHA)
		{
			$captcha 	 = $this->captcha;
			if(!isset($_POST["captcha_user"]))
			    return false;
			$captcha_user = strtoupper($_POST["captcha_user"]);
			if ($captcha_user != $captcha)
			{
				return false;
			}
		}
	  
		$this->uname = $username;        ## This provides access for "loginform.ihtml"
		 
		$sql = DbHelper::prepare_sql(sprintf("select U.USER_ID, U.USERNAME, U.password, U.PERMS, O.password as orig_password
				from %s U
				left join TB_ALOJA_PASSWORD_ORIG O
				on U.USER_ID = O.USER_ID
				where U.USERNAME = :username", USER_DATATABLE),
				array(':username' => $username));
	  
		 
		$db = new Istac_Sql();
	  
		$db->query($sql);
		 
		if($db->next_record())
		{
			$uid   = $db->f("user_id");
			$perm  = $db->f("perms");
			$pass  = $db->f("password");   ## Password is stored as a md5 hash
			$orig_pass  = $db->f("orig_password");   ## Password is stored as a md5 hash
		}
		else
		{
			///Fallo de acceso (username).
			$accesos->registrar_fallo_acceso($username);
			return false;
		}
	  
		$exspected_response = md5("$username:$pass:$challenge");


		## True when JS is disabled
		if ($response == "")
		{
			if (md5($password) != $pass)
			{       
				// md5 hash for non-JavaScript browsers
				//Fallo de acceso (clave incorrecta).
				$accesos->registrar_fallo_acceso($username);
				return false;
			}
			else
			{
				//Acceso permitido
				$this->perm = $perm;
				if (isset($orig_pass) && ($orig_pass == $pass))
					$this->is_orig_pwd = true;

				// FIXME: Comprobar debidamente los permisos (puede haber varios)
				if(CERRAR_WEB_MANTENIMIENTO && ($perm != "admin"))
				{
					$sesion_activa=new SesionActiva();
					$sesion_activa->borrar();
					//session_destroy();
					require_once(PAGE_WEB_CERRADA);
					exit();
				}
						
					
				/// Registrar accesos (admin caso especial) (NOTA: solo 4 bytes de espacio, de ahi que sea 'admi', pero $uid es de 32bytes!!! (error?)
				$accesos->registrar_exito_acceso(($perm == "admin")? "ADMI":$uid);
				return $uid;
			}
		}
	  
		// Response is set, JS is enabled
		if ($exspected_response != $response)
		{
			//Fallo de acceso (clave incorrecta).
			$accesos->registrar_fallo_acceso($username);
			return false;
		}
		else
		{
			//Acceso permitido
			$this->perm = $perm;
			if (isset($orig_pass) && ($orig_pass == $pass))
				$this->is_orig_pwd = true;
			
			// FIXME: Comprobar debidamente los permisos (puede haber varios)
			if(CERRAR_WEB_MANTENIMIENTO && ($perm != "admin"))
			{
				$sesion_activa=new SesionActiva();
				$sesion_activa->borrar();
				//session_destroy();
				require_once(PAGE_WEB_CERRADA);
				exit();
			}
				
			/// Registrar accesos (admin caso especial) (NOTA: solo 4 bytes de espacio, de ahi que sea 'admi', pero $uid es de 32bytes!!! (error?)
			$accesos->registrar_exito_acceso(($perm == "admin")? "ADMI":$uid);			
			return $uid;
		}
	}

	/**
	 * Realiza la comprobación inicial de autenticación en cada página. 
	 */
	public function start()
	{
		if ($this->is_authenticated())
		{
			$uid = $this->uid;
			switch( $uid)
			{
				case "form":
					$state = 3;
					break;
				default:
					$state = 2;
					break;
			}
		}
		else
		{
			$vieja_sid=session_id();
			session_destroy();
			session_start();
			SesionActiva::reemplazar($vieja_sid);
			$_SESSION["auth"] = $this;
			$state = 1;
		}

		$this->last_activity = time();
				
		
		switch( $state)
		{
			case 1:
				// No valid auth info or auth is expired
				$this->auth_loginform();
				$this->uid = "form";
				$this->just_logged=false;
				session_write_close();
				exit;
			case 2:
				// Valid auth info
				$this->just_logged=false;			
				break;
			case 3:
				$uid = $this->auth_validatelogin();
				if ($uid)
				{
					$this->uid = $uid;
					$this->just_logged=true;
					return true;
				}
				else
				{
					$this->auth_loginform();
					$this->uid = "form";
					$this->just_logged=false;
					session_write_close();
					exit;
				}
		}
		
	}

	/**
	 * Elimina las credenciales de sesión.
	 */
	public function logout()
	{
		if (isset($_SESSION['auth']))
		{
			unset($_SESSION['auth']);
			// Pone la cookie en el pasado para que expire.
			setcookie(session_name(),session_id(),time() - 525600, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), false, true);
		}
	}
}

?>