<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1 noprint">Encuesta de Expectativas Hoteleras</h1>
	<div class="bloq_central">
		<?php if ($res === true) : ?>
		<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Recogida de encuestas</h3>
			<div class="subrayado"></div>
			<p>Su cuestionario de expectativas hoteleras ha sido almacenado.</p>
			<p><b>Muchas gracias por su colaboración.</b></p>
		</div>
        <div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
		<?php else: ?>
		<div class="cuadro" style="text-align: justify; background-color:#FF9393;border-color:#F37575" class="noprint">
			<h3 class="titulo_3 erroricon" style="margin-bottom:4px;">Recogida de encuestas</h3>
			<p>No ha sido posible recoger la encuesta debido a los siguientes errores:</p>
            <?php foreach($res as $error) : ?>
			<ul><li style="margin-left: 30px;"><b><?= $error ?></b></li></ul>
            <?php endforeach; ?>
			<p><i>Puede volver al formulario,  corregir el error y volver a intentarlo.</i></p>
			<p><b>Muchas gracias por su colaboración.</b></p>
		</div>		
        <div style="margin-top:20px;"><a href="javascript:history.back()" class="enlace volvericon">Volver al formulario</a></div>
		<?php endif; ?>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->