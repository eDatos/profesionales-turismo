<script type="text/javascript">
$(document).ready( function() {
	$("input[type=submit]").button();
  });
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1" style="float:left;">Encuesta de Expectativas Hoteleras</h1>
	<a id="ayuda_exp" href="javascript:MostrarAyuda('ayuda_exp','AYUDA23<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon enlace" style="float:right;margin-top:9px;background-position-y: 2px;" title="Ayuda (tecla de acceso: y)" accesskey="y"><strong>Ayuda</strong></a>	
	<div style="clear:both;"></div>
	
	<div class="columna_pequ_der">
		<?php include(__DIR__."/../viewparts/recuerde_part.php"); ?>	
		<?php include(__DIR__."/../viewparts/ayudenos_part.php"); ?>
	</div>	
	<div class="bloq_central">		
		<!-- COMIENZO BLOQUE LINKS A ENCUESTAS ANTERIORES-->
		<div>
		    <h2 class="titulo_2">Encuestas anteriores</h2>
		    <div class="subrayado"></div>
		    <div style="width:50%; float:left;">
		        <h2 class="titulo_3">Sus encuestas</h2>
		        <?php if (isset($enc_anteriores) && count($enc_anteriores) > 0): ?>
		        <ul class="lista_sin_punto indentado">
		        	<?php foreach($enc_anteriores as $enc_ant): ?>
			        <li><a href="<?=  $enc_ant['url'] ?>" class="enlace">Encuesta <?= ucfirst($enc_ant['nombre']) ?></a></li>
			        <?php endforeach; ?>
		        </ul>
		        <?php else : ?>	
		        <ul class="lista_sin_punto indentado">
			        <li>No existe ninguna encuesta anterior</li>
		        </ul>
		        <?php endif; ?>
		    </div>
		</div>
		<!-- FIN BLOQUE LINKS A ENCUESTAS ANTERIORES -->
	</div>
	<div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
</div>
<!-- FIN BLOQUE INTERIOR -->
