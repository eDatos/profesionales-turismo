<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="robots" content="all" />
<meta http-equiv="pragma" content="no-cache" />
<!-- TEXTO PARA SER INDEXADO EN BUSCADORES -->
<meta name="keywords" content="gobierno canarias, instituto canario de estadística, istac, estadísticas, inicio, noticias, novedades"/>
<!-- DESCRIPCION PARA SER INDEXADO EN BUSCADORES -->
<meta name="description" content="Página de inicio del portal de estadísticas de turismo para profesionales del sector del Instituto Canario de Estadística (ISTAC)" />
<meta http-equiv="imagetoolbar" content="no" />
<!-- NOMBRE DEL CENTRO DIRECTIVO -->
<title>ISTAC | Portal de estadisticas de turismo para profesionales del sector</title>
<link href="css/estilos.css" rel="stylesheet" type="text/css" media="screen"/>		
<link href="css/imprime.css" rel="stylesheet" type="text/css" media="print" />
<link href="css/voz.css" rel="stylesheet" type="text/css" media="aural" />
<link href="img/favicon.ico" rel="shortcut icon"/>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="https://www-pre.gobiernodecanarias.org/gcc/css/ie.css" />
<![endif]-->
<link rel="stylesheet" href="css/overwrite.css" type="text/css" />
<link href="css/pwet-theme/jquery-ui-1.9.1.custom.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
<script src="js/jquery-ui-1.9.1.custom.js"></script>
<script src="js/jquery-ui.custom/jquery.ui.datepicker-es.js"></script>
<script type="text/javascript">
	var ayuda_url = "<?= isset($page)? $page->build_url(PAGE_AYUDA_AJAX, NULL) : ""; ?>";
</script>
<script type="text/javascript" src="js/ayuda.js"></script>
<script type="text/javascript">
	function abrir_ventana(url)
	{
		window.open(url,"","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,width=600,height=480,left=100,top=100");
	}
	$(document).ready(function(){
		$("a.popup").click(function(){
			abrir_ventana($(this).attr('href'));
			return false;
		});
	});
</script>
