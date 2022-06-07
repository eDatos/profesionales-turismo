<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es">
<head>
<?php include("htmlhead_part.php"); ?>
</head>
<body>
<?php include("aviso_cookies.php"); ?>
<!-- BLOQUE de CONTENIDO -->
<div id="contenido">
<?php 

	if ($includeHeader)
	{
		include("header_part.php");
		if ($includeBanner && ENABLE_BANNER)  
			include("banner_part.php");
		
		include("migas_part.php");
	}
	
	if (file_exists($contentFileFullPath)) {  
		include($contentFileFullPath);  
	} else {  
		/* 
			If the file isn't found the error can be handled in lots of ways. 
			In this case we will just include an error template. 
		*/  
		include("error.php");  
	}  
	
	include("footer_part.php"); 
?>
</div>
<!-- FIN BLOQUE de CONTENIDO -->
</body>
</html>