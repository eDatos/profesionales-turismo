<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY);

/// Parametros de la pagina
// ARG_MES_ENCUESTA: Mes de encuesta para la que hay que cargar los datos de la selección de países
define('ARG_MES_ENCUESTA', 'mes_encuesta');
// ARG_ANYO_ENCUESTA: Año de encuesta para la que hay que cargar los datos de la selección de países
define('ARG_ANO_ENCUESTA', 'ano_encuesta');

$src=$page->request_post_or_get('src',NULL);
if($src=="aloja_acuse_recibo.php")
{
    $src=$page->build_url($src,array(ARG_MES_ENCUESTA=>$src=$page->request_post_or_get(ARG_MES_ENCUESTA,NULL),ARG_ANO_ENCUESTA=>$src=$page->request_post_or_get(ARG_ANO_ENCUESTA,NULL)));
}
elseif($src=="empleo_acuse_recibo.php")
{
    $src=$page->build_url($src,array(ARG_MES_ENCUESTA=>$src=$page->request_post_or_get(ARG_MES_ENCUESTA,NULL),ARG_ANO_ENCUESTA=>$src=$page->request_post_or_get(ARG_ANO_ENCUESTA,NULL)));
}

header("Content-Type: text/html; charset=".$page->encoding);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="robots" content="all" />
<meta http-equiv="pragma" content="no-cache" />
<!-- TEXTO PARA SER INDEXADO EN BUSCADORES -->
<meta name="keywords" content="gobierno canarias, instituto canario de estadística, istac, estadísticas, inicio, noticias, novedades"/>
<!-- DESCRIPCION PARA SER INDEXADO EN BUSCADORES -->
<meta name="description" content="Página de inicio del portal de estadísticas de turismo para profesionales del sector del Instituto Canario de Estadística (ISTAC)" />
<meta http-equiv="imagetoolbar" content="no" />

<title><?= USER_TITLE ?></title>
<link href="css/estilos.css" rel="stylesheet" type="text/css" media="screen"/>		
<link href="css/imprime.css" rel="stylesheet" type="text/css" media="print" />
<link href="css/voz.css" rel="stylesheet" type="text/css" media="aural" />
<link href="img/favicon.ico" rel="shortcut icon"/>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="https://www-pre.gobiernodecanarias.org/gcc/css/ie.css" />
<![endif]-->
<link rel="stylesheet" href="css/overwrite.css" type="text/css" />
<style>
.botoneraderecha {
	float: right;
	margin: 10px 17px;
	width: 32px;
	height: auto;
}
</style>
</head>
<body>
<div  id="contenido" style="width:100%;">
	<img id="ocultar" class="botoneraderecha" src="images/cross.png" onclick="javascript: this.parentNode.style.display='none';"/>
	<p align="justify" style="padding: 5px;">Si no puede ver correctamente el siguiente documento PDF, descargue la última versión de Reader.<br><a target="_BLANK" href="http://get.adobe.com/es/reader/"><img border=0 src="images/get_adobe_reader.png" style="vertical-align: middle;" alt="Descargar Adobe Reader"/></a><a class="enlace" target="_BLANK" href="http://get.adobe.com/es/reader/" style="margin-left:8px;">Descargar</a></p>
</div>
<IFRAME src="<?= $src ?>" scrolling="no" style="width:100%;height:100%;" frameborder="0"></IFRAME>
</body>
</html>
<?php
$page->end_session();
?>
