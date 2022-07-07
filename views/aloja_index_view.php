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

<script>
$(document).ready(function() {
			$("input[type='button']").button();
			$("#button").button();
			$("#subirXmlBtn").button();
		});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1" style="float:left;">Encuesta de Alojamiento Turístico</h1>	
	<a id="ayuda_aloja" href="javascript:MostrarAyuda('ayuda_aloja','AYUDA05<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon enlace" style="float:right;margin-top:9px;background-position-y: 2px;" title="Ayuda (tecla de acceso: y)" accesskey="y"><strong>Ayuda</strong></a>	
	<div style="clear:both;"></div>
	<!-- COMIENZO COLUMNA DERECHA -->
	<div class="columna_pequ_der">
		<?php include(__DIR__."/../viewparts/recuerde_part.php"); ?>
		<?php include(__DIR__."/../viewparts/ayudenos_part.php"); ?>
	</div>		
	<div class="bloq_central">
		<?php if(isset($aloja_encuesta_abierta) && ($aloja_encuesta_abierta==false)): ?>
			<div class="cuadro fondo_amarillo">
				<h2 class="titulo_2">Encuestas en curso</h2>
				<div class="subrayado"></div>
				<ul class="lista_sin_punto indentado" style="margin-top:10px;font-size:1.1em;">
			    <li>Actualmente no existe ninguna encuesta abierta.</li>
				</ul>
			</div>
		<?php else: ?>
			<div class="cuadro fondo_amarillo">
				<h2 class="titulo_2" style="float: left;">Encuesta en curso: <?= DateHelper::mes_tostring( $aloja_cuestionario->mes,'M')?> de <?=$aloja_cuestionario->ano?></h2>
				<?php if(isset($aloja_plazo)): ?>
				<div style="float: right;margin-top: 5px;">
					Fecha límite: <font class="tx_marcado"><?= Datehelper::fecha_tostring($aloja_plazo,true) ?></font>
				</div>
				<?php endif ?>
				<div class="subrayado inferior_15"></div>
				<?php if ($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION))): ?>
				<fieldset style="margin-bottom: 10px;">
				<legend style="font-weight: bold;">Módulo de alojamiento</legend>
				<div>
					<div><span style="font-weight:bold;">Opción A.</span> Tiene la posibilidad de rellenar el módulo de alojamiento mediante un fichero XML. La extensión del fichero debe ser xml. Este proceso puede tardar varios minutos dependiendo del tamaño del fichero y el tipo de conexión a Internet utilizada. <span id="ayuda_index" onclick="MostrarAyuda('ayuda_index','AYUDA06<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon" title="Ayuda (tecla de acceso: l)" accesskey="l" >&nbsp;</span></div>
					<a id="subirXmlBtn" href="<?= $alojaXmlUrl ?>" style="width:170px; margin: 10px 0px 10px 0px;background-image: url(images/subir.gif);background-repeat: no-repeat;background-position: 8px 4px;">Subir fichero de datos</a>
					<!--  <a class="enlace" href="comofunciona" style="margin-left:10px;">¿Cómo funciona?</a> -->			
				</div>
				<div style="margin:10px 0px 10px 0px;"><span style="font-weight:bold;">Opción B.</span> También tiene la posibilidad de rellenar el módulo manualmente a través del formulario web.</div>
				<div class="formicon">
					<a class="titulo_3 enlace" href="<?= $encuestaUrl ?>">Formulario web</a>
					<?php if(isset($aloja_dias_rellenos[0])): ?>
					<span class="tx_gris"> (Rellenado del <?=$aloja_dias_rellenos[0]?> al <?=$aloja_dias_rellenos[1]?> de <?= DateHelper::mes_tostring( $aloja_cuestionario->mes,'m') ?>)
					</span>
					<?php else: ?>
					<span class="tx_gris"> (Pendiente)</span>
					<?php endif ?><br/>
				</div>
				</fieldset>
				<?php endif;?>
				<?php if ($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER))): ?>
				<div class="formicon">
					<a class="titulo_3 enlace" href="<?= $site[PAGE_EMPLEO_INDEX] ?>">Módulo de empleo</a>
					
<?php if (isset($empleo_encuesta_abierta) && $empleo_encuesta_abierta): ?>
	<?php if(isset($empleo_fecha_recepcion)): ?>
		<span class="tx_gris"><?php if ($empleo_fecha_recepcion != null): ?>(En curso, grabada el <?= DateHelper::fecha_tostring($empleo_fecha_recepcion); ?>)<?php else: ?>(Pendiente)<?php endif;?></span>
	<?php else: ?>
		<span class="tx_gris"> (Pendiente)</span>
	<?php endif;?>
<?php else: ?>
<span class="tx_gris"> (Ninguna encuesta en curso)</span>
<?php endif;?>
					
					
				</div>
				<?php endif;?>
				<?php if ($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_CONSUMOS))): ?>
				<div class="formicon">
					<a class="titulo_3 enlace" href="<?= $site[PAGE_CONSUMO_INDEX] ?>">Módulo de suministros</a>
				</div>			
				<?php endif;?>
			</div>
		<?php endif ?>
		<!-- COMIENZO BLOQUE LINKS A ENCUESTAS ANTERIORES-->
		<div>
		    <h2 class="titulo_2">Encuestas anteriores</h2>
		    <div class="subrayado"></div>
		    <div style="width:45%; float:left;">
		        <h2 class="titulo_3">Resultados comparativos</h2>
		        <ul class="lista_sin_punto indentado">
		        	<?php foreach($rc_urls as $res_comp): ?>
			        <li><a href="<?= $res_comp['url'] ?>" class="enlace"><?= $res_comp['tit'] ?></a></li>
			        <?php endforeach; ?>
			   </ul>
		    </div>
		    <?php if ($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC,PERM_USER,PERM_RECEPCION))): ?>
		    <div style="width:50%; float:left;">
		        <h2 class="titulo_3">Sus encuestas</h2>
		        <?php if (isset($enc_anteriores) && count($enc_anteriores) > 0): ?>
		        <ul class="lista_sin_punto indentado">
		        	<?php foreach($enc_anteriores as $enc_ant): ?>
			        <li><a href="<?=  $enc_ant['url'] ?>" class="enlace">Encuesta <?= DateHelper::mes_tostring( $enc_ant['mes'],'M')?> <?=$enc_ant['ano']?></a>&nbsp;&nbsp;<a href="<?=  $enc_ant['url_acuse'] ?>" class="enlace" style="font-style: italic;font-size:0.9em;">(pdf recepción)</a></li>
			        <?php endforeach; ?>
		        </ul>
		        <?php else : ?>	
		        <ul class="lista_sin_punto indentado">
			        <li>No existe ninguna encuesta anterior</li>
		        </ul>
		        <?php endif; ?>
		    </div>
		    <?php endif; ?>
		    <div style="clear:both"></div>
		</div>
		<div style="clear:both;margin-top:30px;">		    
		    <h2 class="titulo_2">Información adicional <span id="ayuda_adic" onclick='MostrarAyuda("ayuda_adic","AYUDA51<?= $es_hotel ? "_HOT" : "_APT" ?>");' class="ayudaicon enlace" title="Ayuda (tecla de acceso: s)" accesskey="s">&nbsp;</span></h2>
		    <div class="subrayado"></div>
		    <div style="width:100%; float:left; margin-top:10px;">
		        <h2 class="titulo_3">Publicaciones adicionales</h2>
		        <ul class="lista_sin_punto indentado">
        			<?php
            			// NOTA: Se esperan filas de un único enlace sin iconos.
            			$filas=GrupoRecurso::loadGruposEnlaces('INDICE_ALOJA');
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