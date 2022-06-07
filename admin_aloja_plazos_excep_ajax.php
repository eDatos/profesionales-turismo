<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/aloja/AlojaPlazosExcepcionalDao.class.php");


$page = PWETPageHelper::start_page_ajax(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR));

define("ARG_NOTAS","notas");

define('OP_GUARDAR', 'save');

//header("Content-Type: text/html; charset=UTF-8");

$accion=$page->request_post(ARG_OP);
if ($accion == OP_GUARDAR)
{
    $est = $page->request_post_or_get(ARG_ESTID, NULL);
    $mes = $page->request_post_or_get(ARG_MES, NULL);
    $ano = $page->request_post_or_get(ARG_ANO, NULL);
    
    if ($est != NULL && $mes != null && $ano != null)
    {
        $dao = new AlojaPlazosExcepcionalDao();
        $plazoGuardar = new AlojaPlazoExcepcional();
        $plazoGuardar->id_estab = $est;
        $plazoGuardar->mes = $mes;
        $plazoGuardar->ano = $ano;
        $plazoGuardar->notas = $page->request_post_or_get(ARG_NOTAS, NULL);
        if(!$dao->guardarNotas($plazoGuardar))
            echo json_encode(array(
                'error' => TRUE,
                'desc'  => 'Error al grabar el campo notas del plazo excepcional en la BDD.'
            ));
        else
            echo json_encode(array('error' => FALSE));
        
        $page->end_session();
        exit;       
    }
}

echo json_encode(array(
    'error' => TRUE,
    'desc'  => 'Operación incorrecta o no permitida.'
));
$page->end_session();
exit;

?>