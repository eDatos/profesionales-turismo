<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/Trimestre.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/Establecimiento.class.php");
require_once(__DIR__."/classes/expectativas/ExpectativasController.class.php");
require_once(__DIR__."/classes/Aviso.class.php");
require_once(__DIR__."/classes/Noticias.class.php");
require_once(__DIR__."/classes/aloja/AlojaController.class.php");
require_once(__DIR__."/classes/RepositoriesDao.class.php");
require_once(__DIR__."/classes/EmpleoController.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY);

/// Administradores, grabador y usuarios tienen una vista de la pagina index diferente.
if ($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC)))
{
	/// Deseleccionar el establecimiento de trabajo si lo hubiera.
	$page->set_current_establecimiento(null);

	$vv = array(
			'abrirAlojaUrl' => $page->build_url(PAGE_ALOJA_CONTROL_MULTIPLE, array(ARG_OP => 'ac')),
			'borrarAlojaUrl' => $page->build_url(PAGE_ALOJA_CONTROL_MULTIPLE, array(ARG_OP => 'bc')),
			'cerrarAlojaUrl' => $page->build_url(PAGE_ALOJA_CONTROL, array(ARG_OP => 'cc')),
			'modificarAlojaUrl' => $page->build_url(PAGE_ALOJA_SELECT_PAISES)
			);
	
	$page->render( "admin_index_view.php" , $vv);
	
	if ($page->just_logged())
		@AuditLog::log($page->get_current_userid(), null, INICIA_SESION, SUCCESSFUL);
}
else if ($page->have_any_perm(PERM_GRABADOR))
{
	/// Deseleccionar el establecimiento de trabajo si lo hubiera.
	$page->set_current_establecimiento(null);
		
	$page->render( "grabador_index_view.php" );
	if ($page->just_logged()) 
		@AuditLog::log($page->get_current_userid(), null, INICIA_SESION, SUCCESSFUL);
}
else if ($page->have_any_perm(array(PERM_USER,PERM_RECEPCION,PERM_CONSUMOS)))
{
	/// 1. Comprobar si el usuario esta correctamente cargado para auditar fallo en caso contrario.
	$est_user = $page->load_current_user_data();
	if ($est_user === FALSE)
	{
		// No se ha podido inicializar el estado del usuario.
		@AuditLog::log($page->get_current_userid(), null, INICIA_SESION, FAILED);
		$page->logout();
		$page->client_redirect(PAGE_HOME, null, false);
		$page->end_session();
		exit();
	}
	
    /// 2. Obtener los datos del establecimiento del usuario
	$establecimiento = $page->get_current_establecimiento();
	
	/// Abortar la operacion si el establecimiento está dado de baja.
	$page->abort_si_estado_baja($establecimiento);
	
    /// 3. Preparar las variables que se utilizan en la vista.
    $viewvars = array();

    if ($page->user_can_do(OP_ALOJAMIENTO))
    {
        $viewvars['muestra_btn_xml'] = true;
    }
    if ($page->user_can_do(OP_EXPECTATIVAS))
    {
	    $exp_ctl = new ExpectativasController();
	    /// 3.1. Datos de la encuesta de expectativas
		$exp_estado = $exp_ctl->cargar_estado_encuesta_expectativas($establecimiento, new DateTime('now'));
	    $viewvars['exp_encuesta_abierta'] = $exp_estado['plazo_esta_abierto'];
	    $viewvars['exp_fecha_presentada'] = isset($exp_estado['fecha_presentacion'])?$exp_estado['fecha_presentacion']:null;
	    $viewvars['exp_trimestre'] = (isset($exp_estado['trimestre']))?$exp_estado['trimestre']->tostring() : null;
	    if($exp_estado['plazo_esta_abierto'])
	    	$viewvars['exp_establecimiento_cerrado']=!$establecimiento->abiertoParaExpectativas($exp_estado['trimestre']);
    }
    
    if($page->user_can_do(OP_EMPLEO))
    {
        $empleo_ctl = new EmpleoController();
        
        $empleo_estado = $empleo_ctl->cargar_estado_encuesta($establecimiento, $page->get_current_userid());
        
        $empleo_enc = $empleo_estado->encuesta;
        
        $viewvars['empleo_encuesta_abierta'] = true;
        
        // Comprobamos si el cuestionario en curso ya está cerrado (se ha cerrado prematuramente por cese de actividad, no ocupación, etc.).
        if($empleo_enc->esta_cerrado())
        {
            $viewvars['empleo_encuesta_abierta'] = false;
        }
        
        $viewvars['empleo_cuestionario'] = $empleo_enc;
    }
    
	/// Datos de la encuesta de alojamiento
	$aloja_ctl = new AlojaController();
    $encuesta_aloja_estado = $aloja_ctl->cargar_estado_encuesta_alojamiento($establecimiento, $page->get_current_userid());
	$viewvars['aloja_encuesta_abierta'] = !$encuesta_aloja_estado['encuesta']->esta_cerrado();
    $viewvars['aloja_dias_rellenos'] = $encuesta_aloja_estado['dias_rellenos'];
    $viewvars['aloja_cuestionario'] = $encuesta_aloja_estado['encuesta'];
    if($viewvars['aloja_encuesta_abierta'])
    {
    	// Calculamos si el establecimiento está cerrado actualmente o no
    	$viewvars['aloja_establecimiento_cerrado']=!$establecimiento->abiertoParaAlojamiento($encuesta_aloja_estado['encuesta']->mes, $encuesta_aloja_estado['encuesta']->ano);
    }
    
	/// AVISOS:
	$avisos = new AvisoDao();
	$viewvars['listAvisos'] = $avisos->filtrarByIdGrupo($establecimiento->get_grupo());
	
	/// Resultados comparativos anteriores
	$rep_dao = new RepositoriesDao();
	$viewvars['rc_urls'] = $rep_dao->getUrls($page);
	
	/// Noticias
	$ndao = new NoticiasDao();
	$viewvars['noticias'] = json_encode($ndao->getNoticiasFeeds());
	
	//Si es hotel o apartamento
	$viewvars['es_hotel'] = $establecimiento->es_hotel();
	
	$viewvars['alojaXmlUrl'] = $page->build_url(PAGE_ALOJA_XML);
	
    $page->render( "index_view.php" , $viewvars, true, true);
    
    if ($page->just_logged())
		@AuditLog::log($page->get_current_userid(), $establecimiento->id_establecimiento, INICIA_SESION, SUCCESSFUL);
} 
else 
{
	$page->logout();
	$page->client_redirect(PAGE_HOME, null, false);
}
$page->end_session();
?>
