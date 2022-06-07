<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/EstablishmentUserDao.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_USER, PERM_RECEPCION, PERM_CONSUMOS), array(PAGE_ESTAB_PASSWD));

if ($page->request_post(ARG_OP) == "cp")
{
	/// OPERACION POST: Cambio de la contraseña como respuesta al post del usuario en el formulario.
	$response = $page->request_post('response');
	$nueva_password = $page->request_post('password');
	
	$username = get_username();
	$challenge = $page->get_sess_state('challenge');
	
	$eu_dao = new EstablishmentUserDao();
	
	$old_password = $eu_dao->get_old_password($username);
	
	$exp_response = md5("$username:$old_password:$challenge");
	
	$res = null;
	if ($exp_response == $response)
	{
		if ($eu_dao->update_user_password($username, $nueva_password))
			$res = true;
		else
			$res = "No se ha podido actualizar la contraseña del usuario.";
	}
	else 
	{
		$res = "La contraseña antigua introducida no coincide con la contraseña del usuario.";
	}
	
	$establecimiento = $page->get_current_establecimiento();
	
	if ($res === true)
	{
		$page->set_auth_data('is_orig_pwd', null);
		@AuditLog::log($page->get_current_userid(), ($establecimiento!=null) ? $establecimiento->id_establecimiento : null, CAMBIA_CLAVE, SUCCESSFUL);
	}
	else
	{
		@AuditLog::log($page->get_current_userid(), ($establecimiento!=null) ? $establecimiento->id_establecimiento : null, CAMBIA_CLAVE, FAILED, array($res));
	}
	
	$viewvars = array('res' => $res);

	/// Render de la pagina
	$page->render( "estab_passwd_view.php", $viewvars );	
}
else 
{
	/// OPERACION GET: Mostrar el formulario de cambio de contraseña.
	$auth = new Autenticacion();
	$challenge = md5(uniqid());
	$page->set_sess_state('challenge', $challenge);
	$username = get_username();
	
	$viewvars = array(
			ARG_OP => 'cp',
			'challenge' => $challenge,
			'username' => $username,
			'prevpage' => PAGE_HOME
			);
	
	if ($page->get_auth_data('is_orig_pwd') != null)
	{
		$viewvars['es_pwd_original'] = true;
	}
	
	/// Render de la pagina
	$page->render( "estab_passwd_view.php", $viewvars );
}

$page->end_session();


function get_username()
{
	global $page;
	$est_user = $page->load_current_user_data();
	if ($est_user != null)
		return $est_user->username;
	return null;
}

?>