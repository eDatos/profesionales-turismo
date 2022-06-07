<!-- COMIENZO CAJA AMARILLA -->
<script>
$(document).ready(function() {
			$("#subirXmlBtn").button();
		});
</script>
<div class="cuadro fondo_amarillo">
	<h2 class="titulo_2">Encuestas en curso</h2>
	<div class="subrayado"></div>
	<ul class="lista_sin_punto indentado" style="margin-top:10px;font-size:1.1em;">
    <?php if (isset($exp_encuesta_abierta) && $exp_encuesta_abierta): ?>
		<li><a class="titulo_3 enlace" href="<?= $site[PAGE_EXP_FORMULARIO]; ?>">Expectativas hoteleras <span style="font-weight:normal;">para los meses <?= @$exp_trimestre; ?> próximos</span></a><br/>
		<span class="tx_gris"><?php if ($exp_fecha_presentada != null): ?>(Enviada el <?= DateHelper::fecha_tostring($exp_fecha_presentada); ?>)<?php else: ?>(Pendiente)<?php endif;?></span>
		<?php if(isset($exp_establecimiento_cerrado) && $exp_establecimiento_cerrado): ?>
			<span class="tx_gris" style="font-size: 1.0em;color:red;padding: 3px;border-radius: 5px;cursor: default;" title="Según nuestra información, este establecimiento se encuentra de baja para el periodo de referencia de esta encuesta.
Si no es correcto, por favor, contacte con nosotros.">Cerrado en el periodo de referencia</span>
		<?php endif ?>
		</li>
	<?php endif; ?>
    <?php if (isset($aloja_encuesta_abierta) && $aloja_encuesta_abierta): ?>
        <li><a class="titulo_3 enlace" href="<?= $site[PAGE_ALOJA_INDEX]; ?>">Alojamiento Turístico en Establecimientos <?=($es_hotel ? "Hoteleros" : "Extrahoteleros" )?>:  <span style="font-weight:normal;"><?= DateHelper::mes_tostring( $aloja_cuestionario->mes,'M')?> de <?=$aloja_cuestionario->ano?></span></a><br/>
		<?php if($aloja_dias_rellenos[0] != ''): ?>
		<span class="tx_gris">(Rellenado del <?=$aloja_dias_rellenos[0]?> al <?=$aloja_dias_rellenos[1]?> de <?= DateHelper::mes_tostring( $aloja_cuestionario->mes, 'm') ?>)
		</span>
		<?php else: ?>
		<span class="tx_gris">(Pendiente)</span>
		<?php endif ?>
		<?php if(isset($aloja_establecimiento_cerrado) && $aloja_establecimiento_cerrado): ?>
			<span class="tx_gris" style="font-size: 1.0em;color:red;padding: 3px;border-radius: 5px;cursor: default;" title="Según nuestra información, este establecimiento se encuentra de baja para el periodo de referencia de esta encuesta.
Si no es correcto, por favor, contacte con nosotros.">Cerrado en el periodo de referencia</span>
		<?php endif ?>
		</li>
		<div><a id="subirXmlBtn" href="<?= $alojaXmlUrl ?>" style="width:170px; margin: 10px 0px 10px 0px;background-image: url(images/subir.gif);background-repeat: no-repeat;background-position: 8px 4px;">Subir fichero de datos</a></div>
    <?php endif ?>
    <?php if ((!isset($exp_encuesta_abierta) || !$exp_encuesta_abierta) && (!isset($aloja_encuesta_abierta) || !$aloja_encuesta_abierta)) : ?>
        <li>Actualmente no existe ninguna encuesta abierta.</li>
    <?php endif ?>
	</ul>
</div>
<!-- FIN CAJA AMARILLA -->