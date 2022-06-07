<div id="bloq_interior">
	<div class="bloq_central">
		<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Impresi�n de acuse de recepci�n</h3>
			<div class="subrayado"></div>
			<p>El acuse de recibo se ha emitido correctamente en otra ventana.</p>
        </div>
	</div>
	<a href="<?= $urlVolver ?>" class="enlace volvericon">Volver</a>	
</div>
<?php
    /// Parametros de la pagina
    // ARG_MES_ENCUESTA: Mes de encuesta para la que hay que cargar los datos de la selecci�n de pa�ses
    define('ARG_MES_ENCUESTA', 'mes_encuesta');
    // ARG_ANYO_ENCUESTA: A�o de encuesta para la que hay que cargar los datos de la selecci�n de pa�ses
    define('ARG_ANO_ENCUESTA', 'ano_encuesta');
?>
<form id="acuse" action="pdfshow.php" method="POST" target="_blank">
<input type="hidden" name="<?=ARG_MES_ENCUESTA?>" value="<?=$mes_encuesta?>"/>
<input type="hidden" name="<?=ARG_ANO_ENCUESTA?>" value="<?=$ano_encuesta?>"/>
<input type="hidden" name="src" value="empleo_acuse_recibo.php"/>
</form>
<script>
window.addEventListener('load', function () {
	document.forms[0].submit();
	})
</script>