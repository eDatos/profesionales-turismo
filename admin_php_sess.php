<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC));

echo "Sesi�n de PHP:<br/>";
var_dump($_SESSION);
echo "<br/>Sesi�n de PHPLIB:<br/>";
global $sess;
var_dump($sess);
  
$page->end_session();
?>