<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC));

echo "Sesión de PHP:<br/>";
var_dump($_SESSION);
echo "<br/>Sesión de PHPLIB:<br/>";
global $sess;
var_dump($sess);
  
$page->end_session();
?>