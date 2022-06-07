<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/lib/Trimestre.class.php");

isset($page) OR $page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR));
$page->set_page_path(array(PAGE_EXP_TRIMESTRE_SELECT));

$estid = $page->request_post_or_get(ARG_ESTID, NULL);

$viewvars = array(
	'navToUrl' => (isset($navToUrl))? $page->build_url($navToUrl) : $page->self_url(),
	'estid' => $estid
);
/// Render de la pagina
$page->render( "admin_exp_trimestre_select_view.php", $viewvars);


$page->end_session();
?>