<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1 noprint">Encuesta de Expectativas Hoteleras</h1>
	<div class="bloq_central">
		<?php if ($ok_o_errormsg === true) : ?>
		<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Administración de encuestas</h3>
			<div class="subrayado"></div>
			<p>El cuestionario de expectativas hoteleras ha sido eliminado.</p>
		</div>
		<?php else: ?>
		<div class="cuadro" style="text-align: justify; background-color:#FF9393;border-color:#F37575" class="noprint">
			<h3 class="titulo_3 erroricon" style="margin-bottom:4px;">Administración de encuestas</h3>
			<p>No ha sido posible eliminar la encuesta debido al siguiente error:</p>
            <ul><li style="margin-left: 30px;"><b><?= $ok_o_errormsg ?></b></li></ul>
		</div>		
		<?php endif; ?>
        <div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->