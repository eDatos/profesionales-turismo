<div id="bloq_interior">
	<div class="bloq_central">
		<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Impresión de acuse de recepción</h3>
			<div class="subrayado"></div>
			<p>El acuse de recibo se ha emitido correctamente en otra ventana.</p>
        </div>
	</div>
	<a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a>	
</div>
<form id="acuse" action="<?= $page->build_url("pdfshow.php", array("src"=>"aloja_acuse_recibo.php")) ?>" method="POST" target="_blank">
<input type="hidden" name="<?=ARG_MES_ENCUESTA?>" value="<?=$mes_encuesta?>"/>
<input type="hidden" name="<?=ARG_ANO_ENCUESTA?>" value="<?=$ano_encuesta?>"/>
</form>
<script>document.forms[0].submit();</script>
