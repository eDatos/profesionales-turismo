<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");

// El ultimo parametro indica que esto es un logout.
$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_LOGOUT), true);

//@AuditLog::log($page->get_auth_data('uid'), null, FINALIZA_SESION, SUCCESSFUL);

//$page->logout();
//$page->client_redirect(PAGE_HOME, NULL, FALSE);
//$page->end_session(); 

?>
