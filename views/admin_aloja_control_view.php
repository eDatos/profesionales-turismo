<style>
.botoneraderecha {
	float: right;
	margin-right: 17px;
}
.alineado {
	margin: 2px 17px;
}
#detalleErrores {
	padding-left: 40px;
}
#detalleErrores ul {
	list-style-type: circle;
	list-style-position: inside;
}
</style>
<script type="text/javascript">
var detalles=false;
function toggleDetalles()
{
	document.getElementById('detalleErrores').style.display=(detalles)?'none':'block';
	detalles=!detalles;
}
$(document).ready( function() {
	$('#ampliar').click(function(event){
		toggleDetalles();
	});
});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1 noprint">Operaciones sobre la Encuesta de Alojamiento</h1>
	<div class="bloq_central">
		<?php if ($res === true) : ?>
		<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;"><?= $op_titulo ?></h3>
			<div class="subrayado"></div>
			<p>La operación se ha completado con éxito.</p>
		</div>
        <?php else: ?>
		<div class="cuadro fondo_rojo" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 erroricon" style="margin-bottom:4px;">Error en la operación: <?= $op_titulo ?>
			<?php if(!empty($errores)) : ?>
				<img id="ampliar" class="botoneraderecha alineado" src="images/detalles.png"/></h3>
				<div id="detalleErrores" style="display:none"><ul>
				<?php foreach($errores as $linea): ?>
				<li><?= $linea ?></li>
				<?php endforeach; ?>
				</ul></div>
			<?php else: ?>
				</h3>
				<p><?= $res ?></p>
			<?php endif; ?>
		</div>		
        <?php endif; ?>
		<div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
		
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->