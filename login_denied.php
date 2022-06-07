<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");

$page = new PWETPageHelper();

$viewvars = array('isLogged' => false,
		"contacto_url" => CONTACTO_URL,
		"contacto_telefono" => CONTACTO_TELEFONO,
		"contacto_fax" => CONTACTO_FAX,
		"contacto_mail" => CONTACTO_MAIL
);

$page->render("login_denied_view.php", $viewvars);

?>

