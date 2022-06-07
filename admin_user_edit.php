<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/RowDbIterator.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/Establecimiento.class.php");
require_once(__DIR__."/classes/EstablishmentUser.class.php");
require_once(__DIR__."/classes/EstablishmentUserDao.class.php");

/// Parametros de la pagina
define("ARG_USER_ID"	, "userid");
define("ARG_USERNAME"	, "username");
define("ARG_PASSWORD"	, "password");
define("ARG_RESET_PASSWORD", "orig_pw");
define("ARG_PROFILE"	, "profile");

/// Mensajes de error
define("ERROR_ESTID_INVALID", "El establecimiento indicado no es válido.");
define("ERROR_USERID_INVALID", "El identificado de usuario indicado no es válido.");
define("ERROR_USERNAME_INVALID", "El nombre de suario indicado no es válido.");
define("ERROR_LOADING_USER","No se han podido obtener los datos del usuario.");
define("ERROR_PASS_INVALID","La contraseña indicada no es válida.");
define("ERROR_PROF_INVALID","Los permisos indicados no son válidos.");
define("ERROR_RESET_PASSWORD","No se ha podido realizar la operación de reinicio de la contraseña del usuario.");
define("ERROR_SAVE","No se ha podido realizar la operación de escritura de los nuevos datos del usuario.");
define("ERROR_USERID_ALREADY_EXISTS","No se puede introducir el nuevo usuario: Ya existe un usuario con el identificador especificado.");
define("ERROR_USERNAME_ALREADY_EXISTS","No se puede introducir el nuevo usuario:  Ya existe un usuario con el nombre especificado.");


$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR), array(PAGE_ESTAB_ADMIN, PAGE_USER_ADMIN));

/// Seleccion de accion de la pagina segun el tipo de request y el valor del parametro OP.
define("OP_EDIT_START", $page->is_get() && isset($_GET[ARG_OP]) && $_GET[ARG_OP] == 'edit');
define("OP_EDIT_END", $page->is_post() && isset($_POST[ARG_OP]) && $_POST[ARG_OP] == 'sedit');
define("OP_NEW_START", $page->is_get() && isset($_GET[ARG_OP]) && $_GET[ARG_OP] == 'new');
define("OP_NEW_END", $page->is_post() && isset($_POST[ARG_OP]) && $_POST[ARG_OP] == 'snew');

$query_op = $page->request_post_or_get(ARG_OP, '');

/// Si la operacion no es ninguna de estas, aborta.
if (!(OP_EDIT_START || OP_EDIT_END || OP_NEW_START || OP_NEW_END))
{
	$page->logout();
	$page->client_redirect(PAGE_HOME, NULL, FALSE);
}

if (OP_EDIT_START)
{
	/// Se comienza la edicion de un usuario: Cargar sus datos de la BBDD y mostrarlos en un formaulario de edicion.
	$params = $page->get_request_params(array(ARG_ESTID, ARG_USER_ID));
	
	// Validar parametros de la llamada (no siguen si hay error)
	$page->abort_if_null($params[ARG_ESTID], ERROR_ESTID_INVALID);
	$page->abort_if_null($params[ARG_USER_ID],  ERROR_USERID_INVALID);
		
	/// Cargar el usuario para los argumentos especificados y mostrar la pagina para editar sus atributos.
	$userDao = new EstablishmentUserDao();
	$est_user = $userDao->load( $params[ARG_ESTID], $params[ARG_USER_ID] );
	
	$page->abort_if_null($est_user, ERROR_LOADING_USER);
}
else if (OP_EDIT_END)
{
	$doresetpassword = false;
	
	$params = $page->get_request_params(array(ARG_ESTID, ARG_USER_ID, ARG_PASSWORD, ARG_PROFILE, ARG_RESET_PASSWORD));
	
	// Validar parametros de la llamada
	$page->abort_if_null($params[ARG_ESTID], ERROR_ESTID_INVALID);
	$page->abort_if_null($params[ARG_USER_ID],  ERROR_USERID_INVALID);
		
	$doresetpassword = ($params[ARG_RESET_PASSWORD] == '1');
	
	$page->abort_if_null($params[ARG_PROFILE], ERROR_PROF_INVALID);
	
	/// Cargar los datos del usuario o abortar si no existe
	$userDao = new EstablishmentUserDao();
	$edited_user = $userDao->load( $params[ARG_ESTID], $params[ARG_USER_ID] );
	$page->abort_if_null($edited_user, ERROR_LOADING_USER);
		
	if ($doresetpassword)
	{	
		/// Cargar la clave original del usuario.
		$orig_pwd = $userDao->load_original_password_for($params[ARG_USER_ID]);
		if ($orig_pwd === false)
		{
			$page->abort_with_error(PAGE_USER_ADMIN, ERROR_RESET_PASSWORD);
		}
		
		$edited_user->password = $orig_pwd;
	}
	else 
	{
		/// Cambiar la clave si se ha indicado una
		if ($params[ARG_PASSWORD] != '')
			$edited_user->password = md5($params[ARG_PASSWORD]);
	}
	
	$edited_user->profile = $params[ARG_PROFILE];
	
	/// Guardar los cambios o abortar si ocurre error al guardar.
	if (!$userDao->save($edited_user))
	{
		$page->abort_with_error(PAGE_USER_ADMIN, ERROR_SAVE);
	}

}
else if (OP_NEW_START)
{
	$params = $page->get_request_params(array(ARG_ESTID));
	
	// Validar parametros de la llamada
	$page->abort_if_null($params[ARG_ESTID], ERROR_ESTID_INVALID);

	// Crear una estructura vacia para la vista (no se guarda en BBDD aun)
	$est_user = new EstablishmentUser( NULL, $params[ARG_ESTID]);
	
}
else if (OP_NEW_END)
{
	$params = $page->get_request_params(array(ARG_ESTID, ARG_USER_ID, ARG_USERNAME, ARG_PASSWORD, ARG_PROFILE));
	
	// Validar parametros de la llamada
	$page->abort_if_null($params[ARG_ESTID], ERROR_ESTID_INVALID);
	$page->abort_if_null($params[ARG_USER_ID],  ERROR_USERID_INVALID);
	$page->abort_if_null($params[ARG_USERNAME], ERROR_USERNAME_INVALID);
	$page->abort_if_null($params[ARG_PASSWORD], ERROR_PASS_INVALID);
	$page->abort_if_null($params[ARG_PROFILE],  ERROR_PROF_INVALID);

	
	//Siempre poner en mayusculas los nombres de usuario
	$params[ARG_USER_ID] = strtoupper($params[ARG_USER_ID]);
	$params[ARG_USERNAME] = strtoupper($params[ARG_USERNAME]);
	
	$userDao = new EstablishmentUserDao();
	
	/// Comprobar si ya existe un usuario con ese userid
	$ue = $userDao->loadUser($params[ARG_USER_ID]);
	if ($ue != '')
	{
		$page->abort_with_error(PAGE_USER_ADMIN, ERROR_USERID_ALREADY_EXISTS);
	}
	/// Comprobar si ya existe un usuario con ese nombre.
	$ue = $userDao->load_by_username($params[ARG_USERNAME]);
	if ($ue != '')
	{
		$page->abort_with_error(PAGE_USER_ADMIN, ERROR_USERNAME_ALREADY_EXISTS);
	}
	
	/// Crear el nuevo usuario, abortar si ocurre un error.
	$res = $userDao->create($params[ARG_USER_ID],
					 $params[ARG_USERNAME],
				 md5($params[ARG_PASSWORD]),
					 $params[ARG_PROFILE],
					 $params[ARG_ESTID]);
	
	if ($res === false)
	{
		$page->abort_with_error(PAGE_USER_ADMIN, ERROR_SAVE);
	}
}

/// Parte del script que prepara la vista.
if (OP_EDIT_START || OP_NEW_START)
{
	/// Mostrar un formulario
	$opciones = array(PERM_USER			=>	PERMISOS_USUARIO_TEXT,
	    PERM_RECEPCION	=>	PERMISOS_RECEPCION_TEXT,
	    PERM_CONSUMOS	=>	PERMISOS_CONSUMOS_TEXT
	);
	$variables = array(
			'op_type'		=>	$query_op,
			'op_out'		=>	$query_op == 'new' ? 'snew' : 'sedit',
			'est_user'	=>	$est_user,
			'selopcion'	=>  @$opciones[$est_user->profile],
			'opciones'	=>  $opciones
	);
	
	/// Render de la pagina
	$page->render( "admin_user_edit_view.php", $variables );
	$page->end_session();
}
else if (OP_EDIT_END | OP_NEW_END)
{
	/// Volver a la pagina de edicion de establecimientos
	$page->client_redirect(PAGE_ESTAB_ADMIN, array( ARG_ESTID => $params[ARG_ESTID]));
	$page->end_session();
}


?>