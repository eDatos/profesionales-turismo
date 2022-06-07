<?php

require_once(__DIR__."/../lib/PageHelper.class.php");
require_once(__DIR__."/audit/AuditLog.class.php");
require_once(__DIR__."/Establecimiento.class.php");
require_once(__DIR__."/EstablishmentUser.class.php");
require_once(__DIR__."/EstablishmentUserDao.class.php");

/**
 * Variables de sesion
 */
define('SESS_ESTAB', 'estab_data');
define('SESS_ALOJA_FORM_DATA', 'aloja_form_data');


/**
 * Argumentos de las peticiones web
 */
define('ARG_OP','op');
define('ARG_MES', 'mes_sel');
define('ARG_ANO', 'ano_sel');
define('ARG_ESTID', 'estid');
define('ARG_ESTIDS','estids');
define('ARG_TRIM', 'trim_sel');

/**
 * Código de operación a enviar cuando se no se solicita hacer ninguna operación. 
 */
define("OP_DUMMY", "_nop_");


/**
 * Controlador global de la aplicacion.
 *
 */
class PWETPageHelper extends PageHelper
{
	protected $redirectUrlIfNotAllowed = PAGE_HOME;
	
	private $migas;
	
	/**
	 * Mantiene durante la vida de la pagina un objeto establecimiento para evitar recargarlo cada vez que se necesite.
	 * @var Establecimiento
	 */
	private $establecimiento;
		
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Comprueba los permisos para la operacion dada (contra la matriz de operaciones por perfil de usuario
	 * @param unknown_type $operation
	 * @return boolean
	 */
	public function user_can_do($operation)
	{
		global $_CONFIGPWET;
		
		if (!isset($_CONFIGPWET['conf_seguridad'][$operation]))
			return false;
		
		return $this->have_any_perm($_CONFIGPWET['conf_seguridad'][$operation]);
	}

	/**
	 * Reinicia el camino de hormigas.
	 * @param unknown_type $migas
	 */
	public function init_page_path($migas)
	{
		$this->migas = $migas;
	}
	
	/**
	 * 	Establece el camino de migas que se mostrará en la pagina.
	 * @param unknown_type $migas array con las pagina a añadir al camino.
	 */
	public function set_page_path($migas)
	{
		if (isset($this->migas))
		{
			$this->migas = array_merge($this->migas ,$migas);
		}
		else 
		{
			$this->migas = $migas;
		}
	}
	
	/**
	 * Sobrecarga que establece las variables PHP que estaran accesibles a todos los archivos tipo "_view" (que sean referenciados por $page->render).
	 * @see PageHelper::default_vars()
	 */
	protected function default_vars()
	{
		global $page_titles;
	
		/// Preparacion de los vinculos a paginas (añade el sessionid de phplib).
		$map = array();
		foreach ($page_titles as $site_page => $unused)
			$map[$site_page] = $this->build_url($site_page);
		
		if (!isset($this->isSessionOpen) || !$this->isSessionOpen)
		{
			/// Variable para menus y partes de la pagina generales.
			return array('site' => $map,
					'ruta_migas' => isset($this->migas)? $this->migas: array(),
					'page_titles' => $page_titles,
					'contacto_url' => CONTACTO_URL,
					'isLogged' => FALSE);
		}
			
		/// Nombre del establecimiento para el menu de cabecera
		$id_estab = "";
		$nombre_estab = "";
		$num_estrellas = 0;
		$star_o_llave = true;
		$hayEstab=false;
		
		$estdata = $this->get_sess_state(SESS_ESTAB);
		if ($estdata != null)
		{
			$hayEstab=true;
			$id_estab = $estdata['id'];
			$nombre_estab = $estdata['nombre'] . " - " . $estdata['tipo'];
			$num_estrellas = $estdata['estrellas'];
				
			if ($estdata['tipo'] == 'Apartamento')
			{
				$star_o_llave = false;
			}
		}
		/// Administrador muestra el nombre de usuario en vez del nombre del establecimiento, y le añade 
		/// el nombre del establecimiento si hay uno seleccionado. 
		if ($nombre_estab != null)
			$nombre_estab = "(" . $this->get_auth_data('uname') . ")&nbsp;&nbsp;&nbsp;" . $id_estab . " - " . $nombre_estab;
		else
			$nombre_estab = $this->get_auth_data('uname');
		
		/// Variable para menus y partes de la pagina generales.
		return array('site' => $map,
				'ruta_migas' => isset($this->migas)? $this->migas: array(),
				'page_titles' => $page_titles,
				'contacto_url' => CONTACTO_URL,
				'id_estab' => $id_estab,
				'hayEstab' => $hayEstab,
				'nombre_estab' => $nombre_estab,
				'num_estrellas' => $num_estrellas,
				'star_o_llave' => $star_o_llave,
				'isLogged' => TRUE);
	}
	
	
	/**
	 * Comprueba si $value es nulo, en cuyo caso aborta la ejecucion de la pagina redirigiendose a la pagina de error mostrando el mensaje indicado.
	 * @param mixed $value
	 * @param string $msgifnull
	 */
	public function abort_if_null($value, $msgifnull)
	{
		if ($value == null)
		{
			$this->abort_with_error(PAGE_USER_ADMIN, $msgifnull);
		}
	}
	
	/**
	 * Aborta la ejecucion de la pagina, mostrando un mensaje de error en vez de la pagina.
	 * @param unknown_type $page
	 * @param unknown_type $errorMsg
	 */
	public function abort_with_error($page, $errorMsg)
	{
		global $page_titles;
	
        @log::error($errorMsg);
        
		$this->render("error_view.php", array('errorMsg' => $errorMsg, 'title' => $page_titles[$page]));
		$this->end_session();
		exit();
	}
	
	/**
	 * Aborta la pagina redirigiendo a PAGE_LOGIN_DENIED si el establecimiento está de baja.
	 * @param unknown_type $establecimiento
	 */
	public function abort_si_estado_baja($establecimiento, $abortToUrl = PAGE_LOGIN_DENIED)
	{
		/// Si la carga del establecimiento no ha tenido exito o el establecimiento está dado de baja no dejar continuar.
		if ($establecimiento === FALSE || $establecimiento == null || !$establecimiento->esta_activo())
		{
			$estid = (($establecimiento === FALSE) || ($establecimiento == null)) ? "" : $establecimiento->id_establecimiento;
			//El establecimiento está dado de baja.
			@AuditLog::log($this->get_current_userid(), $estid, INICIA_SESION, FAILED, array("El establecimiento ".$estid." del usuario tiene el estado de BAJA."));
			$this->logout();
			$this->client_redirect($abortToUrl, NULL ,false);
			$this->end_session();
			exit();
		}
	}
	
	/**
	 * Realiza las operaciones de inicializacion que tienen en comun las paginas ajax:
	 * 		1. Comprueba que la sesión exista y que el usuario autenticado tiene permisos para esta página.
	 * 		3. Carga los datos de cabecera del establecimiento asignado al usuario si no estuvieran ya cargados.
	 * 
	 * En caso de error, se devuelve un código Forbidden (403).
	 * 
	 * @param unknown_type $arrayPermisos
	 * @return PWETPageHelper
	 */
	public static function start_page_ajax($arrayPermisos)
	{
		$page = new self();
		
		session_set_cookie_params(SESSION_TIMEOUT * 60,ini_get('session.cookie_path'),ini_get('session.cookie_domain'),ini_get('session.cookie_secure'),ini_get('session.cookie_httponly'));
		
		session_start();
		
		$sessid = session_id();
		
		if(empty($sessid))
		{
			http_response_code(403);	// 403 - Forbidden
			exit();
		}
		
		$page->isSessionOpen = TRUE;
		$page->isSessionInherited = FALSE;
		
		if (!isset($_SESSION["auth"]))
		{
			http_response_code(403);	// 403 - Forbidden
			exit();
		}
		
		// Evitamos el mecanismo interno de la aplicación para detectar sesiones caducadas.
		$auth = $_SESSION["auth"];
		$auth->last_activity = time();
		
		// No debería ocurrir pero ocurre...por ejemplo cuando se realizan las petiones AJAX del formulario web de encuestas de alojamiento tras cerrar la sesión en otra ventana del navegador.
		if (!isset($auth->perm))
		{
		    http_response_code(403);	// 403 - Forbidden
		    exit();
		}
		
		//Renueva la fecha de expiración de la cookie de sesion (esto sólo es necesario si el cookie ya venía en la petición)
		if(isset($_COOKIE[session_name()]) && ($_COOKIE[session_name()]==$sessid))
			setcookie(session_name(),$sessid,time() + SESSION_TIMEOUT * 60, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
		
		
		if (!$page->have_any_perm($arrayPermisos))
		{
			http_response_code(403);	// 403 - Forbidden
			$this->end_session();
			exit();
		}
		
		// La sesión existe y se tienen permisos. Podemos continuar con la petición AJAX.
		
		/// Establecer el establecimiento al que esta asignado el usuario
		$userEstabData = $page->get_sess_state(SESS_ESTAB);
		if ($userEstabData == null)
		{
			/// Lazy load de los datos del establecimiento.
			$user = $page->load_current_user_data();
			
			// El usuario administrador no tiene asignacion de establecimiento.
			if ($user->establishment_id != null)
			{
				// ATENCIÓN AQUÍ PORQUE PODEMOS ACABAR MOSTRANDO UNA PANTALLA DE ERROR (llamada a abort_with_error).
				$userEstabData = $page->load_estab_data($user->establishment_id);	
				if ($userEstabData != null) 
					$page->set_sess_state(SESS_ESTAB, $userEstabData);
			}			
		}
		
		return $page;
	}
	
	/**
	 * Realiza las operaciones de inicializacion que tienen en comun todas las paginas:
	 * 		1. Comprueba que el usuario autenticado tiene permisos para esta pagina.
	 * 		2. Establece el camino de migas de la pagina.
	 * 		3. Carga los datos de cabecera del establecimiento asignado al usuario si no estuvieran ya cargados.
	 * 
	 * @param unknown_type $arrayPermisos
	 * @param unknown_type $arrayPagePath
	 * @return PWETPageHelper
	 */
	public static function start_page($arrayPermisos, $arrayPagePath = null, $is_logout=false)
	{
		$page = new self();
		
		/// Comprobacion de permisos de acceso
		$page->start_session_check($arrayPermisos, $is_logout);
		
		/// Establecer la ruta de migas a la pagina
		if ($arrayPagePath != null)
			$page->set_page_path($arrayPagePath);
		
		/// Establecer el establecimiento al que esta asignado el usuario
		$userEstabData = $page->get_sess_state(SESS_ESTAB);
		if ($userEstabData == null)
		{
			/// Lazy load de los datos del establecimiento.
			$user = $page->load_current_user_data();
			
			// El usuario administrador no tiene asignacion de establecimiento.
			if ($user->establishment_id != null)
			{
				$userEstabData = $page->load_estab_data($user->establishment_id);	
				if ($userEstabData != null) 
					$page->set_sess_state(SESS_ESTAB, $userEstabData);
			}			
		}
		
		//Proceso de cambio de clave forzado por nuevo registro
		if ($page->get_auth_data('is_orig_pwd'))
		{
			$page->client_redirect(PAGE_ESTAB_PASSWD, NULL, true);
		}
		
		return $page;
	}
	
	/**
	 * Carga el estado del establecimiento que se guarda en SESS_ESTAB e inicializa ésta.
	 * @param unknown_type $est_id
	 */
	private function load_estab_data($est_id)
	{
		if ($est_id == null)
			return null;
		
		$estab = $this->load_establecimiento($est_id);
		if ($estab != null)
		{
			$userEstabData = array();
			$userEstabData['id'] = $estab->id_establecimiento;
			$userEstabData['nombre'] = $estab->nombre_establecimiento;
			$userEstabData['estrellas'] = $estab->id_categoria;
			$userEstabData['tipo'] = $estab->texto_tipo_establecimiento;
			
			return $userEstabData;
		}
		return null;
	}
	
	/**
	 * Carga los detalles del usuario que ha iniciado sesion. 
	 * Inicializa SESS_ESTAB_ID con el establecimiento al que está asignado el usuario.
	 */
	public function load_current_user_data()
	{
		$user_id = $this->get_current_userid();
		
		$eud = new EstablishmentUserDao();
		$est_user = $eud->loadUser($user_id);
		
		if ($est_user == null)
		{
			//ERROR: No se ha localizado el usuario en las tablas de la BBDD.
			@log::error("No se ha localizado el usuario en las tablas de la BBDD.");
			return null;
		}
		return $est_user;
	}

	
	/**
	 * Carga el establecimiento dado.
	 * @param unknown_type $est_id
	 * @return boolean|Establecimiento
	 */
	public function load_establecimiento($est_id)
	{
		$establecimiento = new Establecimiento();
		/// Carga los datos actuales del establecimiento.
		$res = $establecimiento->cargar_por_fecha($est_id, new DateTime('now'));
		if ($res === false)
		{
			if ($this->user_can_do(OP_VIEW_ADMIN_ERRORS))
			{
				$this->abort_with_error(PAGE_HOME, "Error al cargar el establecimiento: No se ha encontrado un registro actual para el establecimiento.");
			}
			else
			{
				/// Notificar si el usuario no es ADMIN.
				$mailSender = new Util();
				@$mailSender->enviar_mail_error($this->get_auth_data('uname'), $est_id);
				///ERROR: Incoherencia en los datos del establecimiento (la tabla historico no está correcta).
				@log::error("Un usuario ha intentado acceder pero no ha podido: Incoherencia en los datos del establecimiento (No se ha encontrado un registro actual para el establecimiento)");
				return null;
			}
		}
		
// 		if ($res != RESULT_OK)
// 		{
// 			$error = "desconocido";
// 			switch( $res)
// 			{
// 				case RESULT_ERROR_HISTORICO_FECHA_ALTA_NULO:
// 					$error = "La fecha de alta del establecimiento en la tabla de históricos es nula.";
// 					break;
// 				case RESULT_ERROR_HISTORICO_ESTADO_ALTA_CON_FECHA_BAJA:
// 					$error = "El establecimiento tiene el estado 'ALTA', pero tiene fecha de baja anterior a la fecha actual.";
// 					break;
// 			}
				
// 			if ($this->user_can_do(OP_VIEW_ADMIN_ERRORS))
// 			{
// 				$this->abort_with_error(PAGE_HOME, "El establecimiento tiene una incoherencia en los datos de histórico: ".$error);
// 			}
// 			else
// 			{
// 				/// Notificar si el usuario no es ADMIN.
// 				$mailSender = new Util();
// 				@$mailSender->enviar_mail_error($this->get_auth_data('uname'), $est_id);
// 				///ERROR: Incoherencia en los datos del establecimiento (la tabla historico no está correcta).
// 				@log::error("Un usuario ha intentado acceder pero no ha podido: Incoherencia en los datos del establecimiento (la tabla TB_ESTABLECIMIENTOS_HISTORICO no está correcta):".$error);
// 				return null;
// 			}
// 		}
			
		return $establecimiento;
	}
	
	/**
	 * Obtiene el id. de usuario que ha iniciado sesión.
	 * REDIRIGE a PAGE_HOME si la configuración del usuario autenticado es incorrecta o no existe.
	 * @return el id. de usuario. 
	 */
	public function get_current_userid()
	{
		$user_id = $this->get_auth_data('uid');
		if($user_id == null)
		{
			//ERROR: No se ha inicializado correctamente el uid (por el sistema de auth).
			@log::error("No se ha inicializado correctamente el uid (por el sistema de auth)");
			$this->logout();
			$this->client_redirect(PAGE_HOME, null, false);
			$this->end_session();
			exit();
		}	
		return $user_id;	
	}
	
	/**
	 * Obtiene el objeto establecimiento cargado actualmente (lo carga si no esta inicializado aun).
	 */
	public function get_current_establecimiento()
	{	
		if ($this->establecimiento == null)
		{
			$estData = $this->get_sess_state(SESS_ESTAB);
			if ($estData != null)
			{
				$this->establecimiento = $this->load_establecimiento($estData['id']);
			}
			/// else retorna null, que significa que no hay establecimiento en sesion.
		}
		return $this->establecimiento;
	}
	
	/**
	 * (Solo admin o grabador): Establece el establecimineto de trabajo a $newEstabId
	 * @param unknown_type $newEstabId
	 */
	public function set_current_establecimiento($newEstabId)
	{
		if (!$this->user_can_do(OP_CHANGE_ESTABLECIMIENTO))
		{
			/// Los usuarios no pueden elegir el establecimiento.
			return false;
		}	
		
		$this->unset_sess_state(SESS_ESTAB);
		$this->establecimiento = null;

		if ($newEstabId != null)
		{
			$estdata = $this->load_estab_data($newEstabId);
			if ($estdata != null)
				$this->set_sess_state(SESS_ESTAB, $estdata);
		}
		return true;
	}
	
	/**
	 * (Solo admin o grabador): Operacion de seleccion de establecimiento (usa PAGE_ESTAB_SEARCH). Devuelve el id. de establecimiento elegido.
	 * @param unknown_type $returnUrl
	 */
	public function select_establecimiento($returnUrl)
	{
		global $optit;
		
		if (!$this->user_can_do(OP_CHANGE_ESTABLECIMIENTO))
		{
			/// Los usuarios no pueden elegir el establecimiento.
			return false;
		}
				
		$this->set_current_establecimiento(null);
		
		$query_id_est = $this->request_post_or_get(ARG_ESTID, NULL);
		
		/// OPERACION 1: No se ha indicado ningun establecimiento, mostrar la pagina de busqueda.
		if ($query_id_est == null)
		{
			/// - Si no se ha llamado con el argumento est_id (el id. de establecimiento), convertir esta pagina en una pagina de busqueda.
			$navToUrl = $returnUrl;
			require_once(PAGE_ESTAB_SEARCH);
			$this->end_session();
			exit();
		}
		
		return $query_id_est;
	}
	
	public function select_establecimiento_mes_ano($returnUrl, $inNewWindow = false)
	{
		global $optit;
		
		if ((!$this->user_can_do(OP_CHANGE_ESTABLECIMIENTO))||(!$this->user_can_do(OP_SELECT_MES_ANO)))
		{
			/// Los usuarios no pueden elegir el establecimiento y/o mes y año.
			return false;
		}
				
		$this->set_current_establecimiento(null);
		$query_id_est = $this->request_post_or_get(ARG_ESTID, NULL);
    	$query_mes_sel = $this->request_post_or_get(ARG_MES, NULL);
    	$query_ano_sel = $this->request_post_or_get(ARG_ANO, NULL);
    	
    	// No está alguno de los parámetros requeridos. Mostramos la página para seleccionar establecimiento, mes y año.
    	if (($query_id_est == null) || ($query_mes_sel == null) || ($query_ano_sel == null))
    	{
    		$navToUrl = $returnUrl;
    		require_once(PAGE_SELECT_ESTAB_MES_ANO);
    		$this->end_session();
    		exit();    		
    	}

    	return array($query_id_est,$query_mes_sel, $query_ano_sel);
	}
	
	public function select_establecimiento_mes_ano_multiple($returnUrl, $inNewWindow = false)
	{
		global $optit;
		
		if ((!$this->user_can_do(OP_CHANGE_ESTABLECIMIENTO))||(!$this->user_can_do(OP_SELECT_MES_ANO)))
		{
			/// Los usuarios no pueden elegir el establecimiento y/o mes y año.
			return false;
		}
				
		$this->set_current_establecimiento(null);
		$query_id_est = $this->request_post_or_get(ARG_ESTIDS, NULL);  
    	$query_mes_sel = $this->request_post_or_get(ARG_MES, NULL);
    	$query_ano_sel = $this->request_post_or_get(ARG_ANO, NULL);
    	
    	// No está alguno de los parámetros requeridos. Mostramos la página para seleccionar establecimiento, mes y año.
    	if (($query_id_est == null) || ($query_mes_sel == null) || ($query_ano_sel == null))
    	{
    		$navToUrl = $returnUrl;
    		require_once(PAGE_SELECT_ESTAB_MES_ANO_MULTIPLE);
    		$this->end_session();
    		exit();    		
    	}

    	return array($query_id_est,$query_mes_sel, $query_ano_sel);
	}
	
	public function select_establecimiento_trimestre($returnUrl)
	{
		global $optit;
		
		if ((!$this->user_can_do(OP_CHANGE_ESTABLECIMIENTO))||(!$this->user_can_do(OP_SELECT_TRIMESTRE)))
		{
			/// Los usuarios no pueden elegir el establecimiento y/o trimestre.
			return false;
		}
				
		$this->set_current_establecimiento(null);
		$query_id_est = $this->request_post_or_get(ARG_ESTID, NULL);
        $query_trim_sel = $this->request_post_or_get(ARG_TRIM, NULL);
        $query_ano_sel = $this->request_post_or_get(ARG_ANO, NULL);
        
    	// No está alguno de los parámetros requeridos. Mostramos la página para seleccionar establecimiento, trimestre y año.
    	if (($query_id_est == null) || ($query_trim_sel == null) || ($query_ano_sel == null))
    	{
    		$navToUrl = $returnUrl;
    		require_once(PAGE_SELECT_ESTAB_TRIMESTRE);
    		$this->end_session();
    		exit();    		
    	}

    	return array($query_id_est,new Trimestre($query_trim_sel, $query_ano_sel));
	}

    /**
     * (Solo admin o grabador): Operacion de seleccion de trimestre (usa PAGE_EXP_TRIMESTRE_SELECT). Devuelve el trimestre elegido.
     * @param unknown_type $returnUrl
     */
    public function select_trimestre($returnUrl)
    {
		if (!$this->user_can_do(OP_SELECT_TRIMESTRE))
		{
			/// Los usuarios no pueden elegir el establecimiento.
			return false;
		}
		
        $query_trim_sel = $this->request_post_or_get(ARG_TRIM, NULL);
        $query_ano_sel = $this->request_post_or_get(ARG_ANO, NULL);
        
        /// No se ha indicado ningun trimestre, mostrar la pagina de busqueda.
        if ($query_trim_sel == null || $query_ano_sel == null)
        {
            $navToUrl = $returnUrl;
            require_once(PAGE_EXP_TRIMESTRE_SELECT);
            $this->end_session();
            exit();
        }
        return new Trimestre($query_trim_sel, $query_ano_sel);
    }
    
    /**
     * (Solo admin o grabador): Operacion de seleccion de mes y año de trabajo (usa PAGE_ALOJA_MES_SELECT). Devuelve el mes/año elegido.
     * @param unknown_type $returnUrl
     */
    public function select_mes_ano($returnUrl, $inNewWindow = false)
    {
    	if (!$this->user_can_do(OP_SELECT_MES_ANO))
    	{
    		/// Los usuarios no pueden elegir el establecimiento.
    		return false;
    	}
    
    	$query_mes_sel = $this->request_post_or_get(ARG_MES, NULL);
    	$query_ano_sel = $this->request_post_or_get(ARG_ANO, NULL);
    	/// No se ha indicado ningun trimestre, mostrar la pagina de busqueda.
    	if ($query_mes_sel == null || $query_ano_sel == null)
    	{
    		$navToUrl = $returnUrl;
    		require_once(PAGE_ALOJA_MES_SELECT);
    		$this->end_session();
    		exit();
    	}
    	return array($query_mes_sel, $query_ano_sel);
    }
}

?>