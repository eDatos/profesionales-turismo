<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/EnlaceRecurso.class.php");

/// En esta pagina no se requieren credenciales.
$page = new PWETPageHelper();
$page->set_page_path(array(PAGE_CODIFICACIONES));


define('UUID','uuid');

$uuid = $page->request_get(UUID);
if(isset($uuid))
{
    // Se está pidiendo un documento por su uuid.
    $enlace=EnlaceRecurso::Load($uuid);
    if($enlace==false)
    {
        http_response_code(404);
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es"><head><meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" /></head>';
        echo '<body><div><div><h1>ERROR:</h1><h2>El documento solicitado no existe.</h2></div></div></body></html>';
        die;
    }
    else
    {
        $enlace->execute();
        exit;
    }
}
// Vista del listado de codificaciones disponibles.

$variables = array(
    "url_descarga" => $page->self_url(),
    "contacto_url" => CONTACTO_URL,
    "contacto_telefono" => CONTACTO_TELEFONO,
    "contacto_fax" => CONTACTO_FAX,
    "contacto_mail" => CONTACTO_MAIL);

$page->render("codificaciones_view.php", $variables);

?>