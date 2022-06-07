<!DOCTYPE html>
<html>
<head>
<meta http-equiv="refresh" content="30"/>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="robots" content="none|noindex|nofollow" />
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
<link href="css/banner.css" rel="stylesheet" type="text/css"/>
<link href="img/favicon.ico" rel="shortcut icon"/>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="https://www-pre.gobiernodecanarias.org/gcc/css/ie.css" />
<![endif]-->
<link rel="stylesheet" href="css/overwrite.css" type="text/css" />
<link href="css/pwet-theme/jquery-ui-1.9.1.custom.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
<script src="js/jquery-ui-1.9.1.custom.js"></script>
</head>
<body>
<!-- BLOQUE de CONTENIDO -->
<div id="contenido">
<!-- CABECERA -->
<div id="cabecera">
    <!-- IMAGEN GOBIERNO DE CANARIAS -->
    <h1><a href="http://www.gobiernodecanarias.org/istac/" target="_blank" title="Página principal del Instituto Canario de Estadística (ISTAC) - Opciones de accesibilidad (tecla de acceso: i)" accesskey="i">Instituto Canario de Estadística</a></h1>
<div id="cab_superior">
    <!-- MENU DE AYUDA -->
    <ul>
		        <li><a target="_blank" href="http://www.gobiernodecanarias.org/istac/servicios/atencion.html" accesskey="o" title="Contacte con nosotros (tecla de acceso: o)">Contacto</a></li>
    </ul>
    <img src="images/Profe_tur.jpg" style="width:100px; margin-right:10px; margin-top:5px;"/>
</div>		<div id="menu_contextual">
			<ul class="menu">
				  <li class="inactive"><a href="http://www.gobiernodecanarias.org/istac/temas_estadisticos" accesskey="1" title="Estadísticas (tecla de acceso: 1)">Estadísticas</a></li>
				  <li class="inactive"><a href="http://www.gobiernodecanarias.org/istac/istac/" accesskey="2" title="El ISTAC (tecla de acceso: 2)">El ISTAC</a></li>
				  <li class="inactive"><a href="http://www.gobiernodecanarias.org/istac/webescolar/" accesskey="3" title="WEB Escolar (tecla de acceso: 3)">WEB Escolar</a></li>
				  <li class="inactive"><a href="http://www.gobiernodecanarias.org/istac/profesionalesdelturismo/codificaciones.php" accesskey="4" title="Información XML (tecla de acceso: 4)">Información XML</a></li>
			</ul>
		</div>	
	

</div>
<!-- FIN CABECERA --> 
<img src="./images/Turismo_web.jpg" alt="Pofesionales del Turismo" style="width:1000px;height:100px;clear:both;">
<!-- COMIENZO MIGAS DE PAN -->
<div id="migas" style="font: 95% Arial, Helvetica, sans-serif;">
	<p class="txt">Est&aacute; en:</p>
	<ul>
			<li><a href="index.php">Inicio</a></li>
				</ul>
</div>
<!-- FIN MIGAS DE PAN -->
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
    <h1 class="titulo_1">Portal de estadísticas de turismo para profesionales del sector</h1>
    <!-- COMIENZO COLUMNA DERECHA -->
    <div class="columna_pequ_der">
		<div class="cuadro">
	        <h3 class="titulo_3">En este sitio web puede:</h3>
		    <div class="subrayado"> </div>
			<ul class="lista_con_punto">
				<li>Rellenar encuestas sobre ocupaci&oacute;n y expectativas</li>
	            <li>Consultar los datos estad&iacute;sticos propios</li>
	            <li>Consultar informaci&oacute;n tur&iacute;stica procedente de otras fuentes</li>
			</ul>
		</div>
		<div class="cuadro">
		    <h3 class="titulo_3">Informaci&oacute;n de contacto</h3>
		    <div class="subrayado"> </div>
			<ul class="lista_sin_punto">
				<li><a class="enlace" href="http://www.gobiernodecanarias.org/istac/servicios/atencion.html" target="_blank">Formulario de contacto</a></li>
	            <li><strong>902 103 301</strong> (tel&eacute;fono)</li>
	            <li><strong>900 102 519</strong> (fax)</li>
	            <li><a class="enlace" href="mailto:turismo.istac@gobiernodecanarias.org">turismo.istac@gobiernodecanarias.org</a></li>
			</ul>
		</div>
    </div>
    <!-- FIN COLUMNA DERECHA -->
    <div class="bloq_central"> 
        <!-- COMIENZO CAJA CELESTE -->
	    <div class="cuadro fondo_celeste">
		    <h2 class="titulo_2" style="float: left;">Aviso</h2>
	        <div class="subrayado"></div>
		    <p>Por problemas técnicos, este servicio está temporalmente desactivado.</p>
		    <p>Por favor, espere unos momentos y la página intentará completar la operación en curso. </p>
		    <p>En cualquier caso, puede reintentarlo inmediatamente pulsando <a id="yomismo" href="#">aquí</a>.</p>
		    <p>Disculpe las molestias.</p>
		    <br>
		    <p id="msgerr">
				<?php
				if(isset($_SERVER["REDIRECT_STATUS"]))
				{
					$err=$_SERVER["REDIRECT_STATUS"];
					$msgerr[400]='400 Bad Request.';
					$msgerr[401]='401 Unauthorized (RFC 7235).';
					$msgerr[402]='402 Payment Required.';
					$msgerr[403]='403 Forbidden.';
					$msgerr[404]='404 Not Found.';
					$msgerr[405]='405 Method Not Allowed.';
					$msgerr[406]='406 Not Acceptable.';
					$msgerr[407]='407 Proxy Authentication Required (RFC 7235).';
					$msgerr[408]='408 Request Time-out.';
					$msgerr[409]='409 Conflict.';
					$msgerr[410]='410 Gone.';
					$msgerr[411]='411 Length Required.';
					$msgerr[412]='412 Precondition Failed (RFC 7232).';
					$msgerr[413]='413 Payload Too Large (RFC 7231).';
					$msgerr[414]='414 URI Too Long (RFC 7231).';
					$msgerr[415]='415 Unsupported Media Type.';
					$msgerr[416]='416 Range Not Satisfiable (RFC 7233).';
					$msgerr[417]='417 Expectation Failed.';
					$msgerr[418]="418 I'm a teapot (RFC 2324).";
					$msgerr[421]='421 Misdirected Request (RFC 7540).';
					$msgerr[422]='422 Unprocessable Entity (WebDAV; RFC 4918).';
					$msgerr[423]='423 Locked (WebDAV; RFC 4918).';
					$msgerr[424]='424 Failed Dependency (WebDAV; RFC 4918).';
					$msgerr[426]='426 Upgrade Required.';
					$msgerr[428]='428 Precondition Required (RFC 6585).';
					$msgerr[429]='429 Too Many Requests (RFC 6585).';
					$msgerr[431]='431 Request Header Fields Too Large (RFC 6585).';
					$msgerr[451]='451 Unavailable For Legal Reasons.';
					$msgerr[500]='500 Internal Server Error.';
					$msgerr[501]='501 Not Implemented.';
					$msgerr[502]='502 Bad Gateway.';
					$msgerr[503]='503 Service Unavailable.';
					$msgerr[504]='504 Gateway Time-out.';
					$msgerr[505]='505 HTTP Version Not Supported.';
					$msgerr[506]='506 Variant Also Negotiates (RFC 2295).';
					$msgerr[507]='507 Insufficient Storage (WebDAV; RFC 4918).';
					$msgerr[508]='508 Loop Detected (WebDAV; RFC 5842).';
					$msgerr[510]='510 Not Extended (RFC 2774).';
					$msgerr[511]='511 Network Authentication Required (RFC 6585).';
					if(isset($msgerr[$err]))
						echo '<strong>'.$msgerr[$err].'</strong>';
				}
				?>
		    </p>
		</div>
	    <!-- FIN CAJA CELESTE -->	
    </div>
    <!-- FIN BLOQUE IZQUIERDO GRANDE -->

</div>
<!-- FIN BLOQUE INTERIOR -->

<!-- PIE DE PAGINA -->
<div id="pie">
    <p class="izda" style="position:relative;top:-2px;">&copy; Gobierno de Canarias</p>
    <div class="dcha" style="position:relative;top:-5px;">
        <ul>
            <li><a href="http://www.gobiernodecanarias.org/es/avisolegal.html" target="_blank">Aviso Legal</a></li>
            <li>|</li>
            <li><a href="http://www.gobiernodecanarias.org/principal/sugrec/" target="_blank">Sugerencias y Reclamaciones</a></li>
        </ul>
    </div>
</div>
<!-- FIN PIE DE PAGINA -->
<script type="text/javascript">
	/// DEV: colorea de rojo los vinculos que no tienen enlaces (por hacer)
	$("a[href='#']").css('color','red');
	document.getElementById('yomismo').href=document.location.href;
</script>
   
</div>
<!-- FIN BLOQUE de CONTENIDO -->

</body>
</html> 