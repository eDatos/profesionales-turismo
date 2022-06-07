<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/EnlaceAyuda.class.php");
require_once(__DIR__."/lib/captcha.php");


$page = new PWETPageHelper();

/// Se incluye desde una funcion en auth.inc ($this es el objeto auth).
if (!isset($this))
{
	$page->logout();
	$page->client_redirect(PAGE_HOME, NULL, FALSE);
	exit();
}

$habilita_captcha = ENABLE_CAPTCHA;
$imdata = false;

if ($habilita_captcha)
{
	/// Generar el captcha aleatorio y obtenerlo como array en base64 (embebido en formulario).
	$keywords = captcha_getkeys();
	//$this->auth['captcha'] = $keywords;
	$this->captcha = $keywords;
	
	/// Obtener la imagen del captcha desde el servicio de imagenes.
	$imdata = captcha_getimage($keywords);

	if ($imdata === false)
	{
		/// Si no se ha podido generar la imagen correctamente se deshabilita el captcha.
		$habilita_captcha = false;
	}
}

@$dao = new EnlaceAyudaDao();
@$enlace_ayuda = $dao->cargar("AYUDA01",'UTF-8');
@$texto_aviso = $dao->cargar("AVISOLOGIN",'UTF-8');

/// NOTA: this se refiere al objeto auth (auth.inc) en el que se incluye este codigo.
$variables = array(
	"texto_ayuda" => (isset($enlace_ayuda)? json_encode($enlace_ayuda) : ""),
	"texto_aviso" => (isset($texto_aviso)? json_encode($texto_aviso) : ""),
	"challenge" => $challenge,
	"captcha_img" => $imdata,
	"is_captcha_enabled" => $habilita_captcha,
	//"loginUrl" => $this->url(),
	"loginUrl" => ($page->self_url($_GET) == PAGE_LOGOUT) ? PAGE_HOME : $page->self_url($_GET),
	//"loginUrl" => "test_sesion_login.php",
	"pwdRecoverUrl" => PAGE_PASS_RECOVER,
	"uname" => (isset($this->uname) ? $this->uname : '' ),
	"isLogged" => FALSE,
    "contacto_url" => CONTACTO_URL,
    "contacto_telefono" => CONTACTO_TELEFONO,
    "contacto_fax" => CONTACTO_FAX,
    "contacto_mail" => CONTACTO_MAIL
);

$page->render("login_view.php", $variables, true, true);

?>

