<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/EstablishmentUserDao.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");

$page = new PWETPageHelper();

if ($page->request_post(ARG_OP) == "reset")
{
	session_start();
	
	/// OPERACION POST: Cambio de la contraseña como respuesta al post del usuario en el formulario.
	$response = $page->request_post('response');
	$nueva_password = $page->request_post('password');
	
	$sent_token = $page->get_sess_state('reset_token');
	
	$dao = new EstablishmentUserDao();
	$recover_data = $dao->get_recovery_data($sent_token, new DateTime());
	
	if ($recover_data != null)
	{
		$user_id = $recover_data['user_id'];
		
		$username = $recover_data['username'];
		$challenge = $page->get_sess_state('challenge');
		
		$exp_response = md5("$username:". md5($sent_token) . ":$challenge");
		
		$res = null;
		if ($exp_response == $response)
		{
			$res = $dao->update_user_password($username, $nueva_password);
			if ($res)
			{
				@$dao->delete_recovery_token($user_id, $sent_token);
				$page->unset_sess_state('reset_token');
				$page->unset_sess_state('challenge');
			}
			else
				$res = "No se ha podido actualizar la contraseña del usuario.";
		}
		else 
		{
			$res = "La contraseña antigua introducida no coincide con la contraseña del usuario.";
		}
		
		if ($res === true)
		{
			@AuditLog::log($user_id, null, CAMBIA_CLAVE, SUCCESSFUL);
		}
		else
		{
			@AuditLog::log($user_id, null, CAMBIA_CLAVE, FAILED, array($res));
		}
		
		$viewvars = array('res' => $res, 'isLogged' => false);
	
		/// Render de la pagina
		$page->render( "reset_login_view.php", $viewvars );	
		
		exit;
	}
}
else if ($page->request_get('token') != null)
{
	$sent_token = $page->request_get('token');
	if (strlen($sent_token) == 44)
	{
		session_start();
		
		$dao = new EstablishmentUserDao();
		$recover_data = $dao->get_recovery_data($sent_token, new DateTime());
		
		if ($recover_data != null)
		{
			/// OPERACION GET: Mostrar el formulario de cambio de contraseña.
			$challenge = md5(uniqid(substr($recover_data['token'], 10)));
			$page->set_sess_state('challenge', $challenge);
			$page->set_sess_state('reset_token', $recover_data['token']);
			$username = $recover_data['username'];
			
			$viewvars = array(
					'isLogged' => false,
					ARG_OP => 'reset',
					'challenge' => $challenge,
					'username' => $username,
					'sec_token' => $recover_data['token']
					);
			
			
			/// Render de la pagina
			$page->render( "reset_login_view.php", $viewvars );
			
			exit;
		}
	}
}

$page->logout();	
$page->client_redirect(PAGE_HOME, NULL, false);

?>