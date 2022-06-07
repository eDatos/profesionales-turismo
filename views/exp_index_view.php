<?php 
require_once(__DIR__."/../classes/EnlaceRecurso.class.php");
?>
   
<style>
span.disable-links {
    cursor: not-allowed;
}
span.disable-links a {
    pointer-events: none;
}
</style>

<script type="text/javascript">
$(document).ready( function() {
	//alert('Hola');
	$("input[type=submit]").button();
	//$("input").button();
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
		<?php if (isset($exp_encuesta_abierta) && $exp_encuesta_abierta): ?>
		<div class="cuadro fondo_amarillo">
			<h2 class="titulo_2" style="float: left;">Encuesta en curso: <?= @ucfirst($exp_trimestre); ?> próximos</h2>
			<?php if(isset($exp_plazo)): ?>
			<div style="float: right;margin-top: 5px;">
				Fecha límite: <font class="tx_marcado"><?= Datehelper::fecha_tostring($exp_plazo,true) ?></font>
			</div>
			<?php endif ?>
			<div class="subrayado inferior_15"></div>
			<div class="formicon">
				<a class="titulo_3 enlace" href="<?= $url_encuesta; ?>">Rellenar encuesta</a>
				<?php if(isset($exp_fecha_presentada)): ?>
				<span class="tx_gris"><?php if ($exp_fecha_presentada != null): ?>(Enviada el <?= DateHelper::fecha_tostring($exp_fecha_presentada); ?>)<?php else: ?>(Pendiente)<?php endif;?></span>
				<?php else: ?>
				<span class="tx_gris"> (Pendiente)</span>
				<?php endif ?><br/>
				<!--  <a class="enlace" href="#">Consultar las estadísticas recogidas hasta ahora de su establecimiento</a> -->
			</div>
		</div>	
		<?php else: ?>
		<div class="cuadro fondo_amarillo">
			<h2 class="titulo_2">Ninguna encuesta en curso</h2>
			<div class="subrayado"></div>
			<ul class="lista_sin_punto indentado" style="margin-top:10px;font-size:1.1em;">
		    	<li>Actualmente no existe ninguna encuesta abierta.</li>
		    </ul>
		</div>
		<?php endif ?>		
		<!-- COMIENZO BLOQUE LINKS A ENCUESTAS ANTERIORES-->
		<div>
		    <h2 class="titulo_2">Encuestas anteriores</h2>
		    <div class="subrayado"></div>
		    <?php /*<div style="width:45%; float:left;">
		        <h2 class="titulo_3">Resultados comparativos</h2>
		        <ul class="lista_sin_punto indentado">
			        <li><a href="#" class="enlace">Previsión del grado de ocupación</a></li>
			        <li><a href="#" class="enlace">Expectativas del trimestre</a></li>
			        <li><a href="#" class="enlace">Evolución del turismo según nacionalidades</a></li>
			        <li><a href="#" class="enlace">Tendencia de los precios de las plazas hoteleras</a></li>
			        <li><a href="#" class="enlace">Inversión privada</a></li>
			        <li><a href="#" class="enlace">Empleo</a></li>
			   </ul>
		    </div>
		    */ ?>
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
		    <div style="clear:both"></div>
		</div>
		<div style="clear:both;margin-top:30px;">		    
		    <h2 class="titulo_2">Información adicional <span id="ayuda_adic" onclick='MostrarAyuda("ayuda_adic","AYUDA52<?= $es_hotel ? "_HOT" : "_APT" ?>");' class="ayudaicon enlace" title="Ayuda (tecla de acceso: s)" accesskey="s">&nbsp;</span></h2>
		    <div class="subrayado"></div>
		    <div style="width:100%; float:left; margin-top:10px;">
		        <h2 class="titulo_3">Publicaciones adicionales</h2>
		        <ul class="lista_sin_punto indentado">
        			<?php
            			// NOTA: Se esperan filas de un único enlace sin iconos.
            			$filas=GrupoRecurso::loadGruposEnlaces('INDICE_EXP');
            			foreach($filas as $fila)
            			{
            			    echo "<li>";
            			    foreach($fila->enlaces as $enlace)
            			    {
            			        echo $enlace->render();
            			    }
            			    echo "</li>\n";
            			}
        			?>
				</ul>		    
		    </div>
		</div>
		<!-- FIN BLOQUE LINKS A ENCUESTAS ANTERIORES -->
	</div>
	<div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
</div>
<!-- FIN BLOQUE INTERIOR -->
