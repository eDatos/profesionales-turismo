<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/EstablishmentUserDao.class.php");
require_once(__DIR__."/lib/email.class.php");

/// En esta pagina no se requieren credenciales.
$page = new PWETPageHelper();

$variables = array(ARG_OP => 'rec', 
		'isLogged' => false, 
		'page_title' => $page_titles[PAGE_PASS_RECOVER],
		    "contacto_url" => CONTACTO_URL,
    "contacto_telefono" => CONTACTO_TELEFONO,
    "contacto_fax" => CONTACTO_FAX,
    "contacto_mail" => CONTACTO_MAIL );

if ($page->request_post(ARG_OP) == 'rec')
{
	///FASE 1B: El usuario ha indicado que quiere recuperar la contraseña, mandando su nombre de usuario.
	
	$uname_to_recover = $page->request_post('uname');
	
	$dao = new EstablishmentUserDao();
	
	$user_data = $dao->get_user_contact_info( $uname_to_recover );
	
	// SOLO usuarios tipo user y recepcion pueden restablecer contraseña.
	if ($user_data == null || ($user_data['perms'] != PERM_USER && $user_data['perms'] != PERM_RECEPCION))
	{
		$variables[ 'res' ] = false; //Ha se han encontrado los datos del usuario.
	}
	else 
	{
		// Generacion de token de recuperacion aleatoriamente.
		$nueva_clave = generar_token_aleatorio();
		
		$fecha_peticion = new DateTime();
		$validez_hasta = clone $fecha_peticion;
		$fecha_peticion->sub(new DateInterval(RESET_PASSWORD_PERIOD));
		$validez_hasta->add(new DateInterval(RESET_PASSWORD_UNTIL));
		
		//$ok = $dao->insert_recovery_token($user_data['userid'], $nueva_clave, $validez_desde, $validez_hasta);
		$ok = $dao->insert_recovery_token($user_data['userid'], $nueva_clave, $fecha_peticion, $validez_hasta);
		
		if ($ok)
		{
			$to_mail_address = ($user_data['perms'] == PERM_USER)
								? $user_data['email2']
								: $user_data['email'];
			
			if ($to_mail_address == null)
			{
				$ok = false; /// No se tiene dato del correo.
			}
			else 
			{
				$restore_url = SERVER_APP_ROOT .  $page->build_url(PAGE_PASS_RESET, array('token' => $nueva_clave), false);
				
				$asunto = "Modificación de la contraseña del Portal de Turismo del ISTAC.";
				$cuerpo = file_get_contents(__DIR__."/".CONTENT_CORREO_RECUPERACION);
				
				$cuerpo = str_replace("%%RESTORE_URL%%", $restore_url, $cuerpo);
				
				//TODO: Formato correo al usuario.
				$email = new Email();
				$ok = $email->send($asunto, $cuerpo, $to_mail_address);
				
			}
		}
		
		$variables[ 'res' ] = $ok;
	}
}

///FASE 1A: El usuario ha pulsado sobre "Ha olvidado su contraseña". (GET sin parametros).
$page->render("estab_passwd_recover_view.php", $variables);

function generar_token_aleatorio()
{
	$base64_alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ'
	.'abcdefghijklmnopqrstuvwxyz0123456789+/';
	$salt='$1$';
	for($i=0; $i<9; $i++)
	{
		$salt.=$base64_alphabet[rand(0,63)];
	}
	
	/// Fecha.
	$password = uniqid(); //date('l jS \of F Y h:i:s A');
	for($i=0; $i<10; $i++)
	{
		$password.=$base64_alphabet[rand(0,63)];
	}
	
	// return the crypt md5 password (longitud 44)
	return base64_encode(substr(crypt($password,$salt.'$'), 3));
}

?>