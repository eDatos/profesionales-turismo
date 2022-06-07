<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");

$page = PWETPageHelper::start_page_ajax(PERMS_ANY);

define('OP_KEEPALIVE', 'ka');

header("Content-Type: text/html; charset=UTF-8");

$accion=$page->request_post(ARG_OP);

if ($accion == OP_KEEPALIVE)
	echo json_encode(array( 'resultado' => true,'op' => $accion));
else
	echo json_encode(array( 'resultado' => false));
$page->end_session();

?>