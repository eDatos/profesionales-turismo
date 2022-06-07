<?php
	require_once(__DIR__."/config.php");
	require_once(__DIR__."/classes/PWETPageHelper.class.php");
	require_once(__DIR__."/classes/EnlaceAyuda.class.php");
	
	$page = PWETPageHelper::start_page(PERMS_ANY);
	
	define('ARG_COD_ENLACE', 'cod_enlace');
	
	/// Recoger el valor del código de enlace
	$v = $page->request_post(ARG_COD_ENLACE);
	
	$dao = new EnlaceAyudaDao();
	$enlace_ayuda = $dao->cargar($v,'UTF-8');
	
	if ($page->have_any_perm(array(PERM_ADMIN, PERM_ADMIN_ISTAC)) && $enlace_ayuda == null)
	{
		$enlace_ayuda = new EnlaceAyuda();
		$enlace_ayuda->tipo = "0"; /// Mensaje central.
		$enlace_ayuda->titulo = "Ayuda no encontrada";
		$enlace_ayuda->contenido_ayuda = "No se ha encontrado el elemento de ayuda (".$v.")";
		$enlace_ayuda->ancho_popup = 300;
		$enlace_ayuda->alto_popup = 120;
	}
	
	echo json_encode($enlace_ayuda);

	$page->end_session();

?>
