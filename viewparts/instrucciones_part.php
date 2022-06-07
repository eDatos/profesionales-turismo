<!-- COMIENZO MENU BORDES CUADRADOS -->
<div class="cuadro" >
    <h3 class="titulo_3" style="margin-bottom:4px;">Instrucciones sobre...</h3>
    <div class="subrayado"> </div>
    <ul class="lista_con_punto">
    	<li><a href="<?= $page->build_url("pdfshow.php", array("src"=>CONTENT_INSTRUCCIONES_USO_WEB)) ?>" class="enlace popup">Uso de la web</a></li>
    	<?php if ($page->user_can_do(OP_EXPECTATIVAS)) : ?>
    	<li><a href="<?= $page->build_url("pdfshow.php", array("src"=>CONTENT_INSTRUCCIONES_EXPECTATIVAS)) ?>" class="enlace popup">Encuesta de expectativas</a></li>
    	<?php endif; ?>
    	<?php if ($page->user_can_do(OP_ALOJAMIENTO)) : ?>
    	<li><a href="<?= $page->build_url("pdfshow.php", array("src"=>CONTENT_INSTRUCCIONES_ALOJAMIENTO)) ?>" class="enlace popup">Encuesta de alojamiento</a></li>
    	<?php endif; ?>
    </ul>
</div>
<!-- FIN MENU BORDES CUADRADOS -->