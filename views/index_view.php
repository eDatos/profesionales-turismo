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
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1" style="float:left;">Portal de estadísticas de turismo para profesionales del sector</h1>
 	<a id="ayuda_portal" href="javascript:MostrarAyuda('ayuda_portal','AYUDA04<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon enlace" style="float:right;margin-top:9px;background-position-y: 2px;" title="Ayuda (tecla de acceso: y)" accesskey="y"><strong>Ayuda</strong></a>	
	<div style="clear:both;"></div>
	<!-- COMIENZO COLUMNA DERECHA -->
	<div class="columna_pequ_der">
		<?php include(__DIR__."/../viewparts/recuerde_part.php"); ?>
		<?php include(__DIR__."/../viewparts/infointeres_part.php"); ?>
		<?php include(__DIR__."/../viewparts/noticias_part.php"); ?>
		<?php include(__DIR__."/../viewparts/ayudenos_part.php"); ?>
	</div>
	<!-- FIN COLUMNA DERECHA -->
	<!-- BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
		<?php include(__DIR__."/../viewparts/encuestas_en_curso_part.php"); ?>
		<!-- COMIENZO BLOQUE LINKS A ENCUESTAS ANTERIORES-->
		<div style="clear:both;margin-top:30px;">
		    <h2 class="titulo_2">Resultados comparativos de encuestas anteriores <span id="ayuda_comp" onclick='MostrarAyuda("ayuda_comp","AYUDA02<?= $es_hotel ? "_HOT" : "_APT" ?>");' class="ayudaicon enlace" title="Ayuda (tecla de acceso: r)" accesskey="r">&nbsp;</span></h2>
		    <div class="subrayado"></div>
		    <div style="width:45%; float:left; margin-top:10px;">
		        <h2 class="titulo_3">Alojamiento</h2>
		        <ul class="lista_sin_punto indentado">
		        	<?php foreach($rc_urls as $res_comp): ?>
		        	<?php if (!$es_hotel) : ?>
			        	<li><a href="<?= $res_comp['url'] ?>" class="enlace"><?= @str_replace("habitaciones", "apartamentos", str_replace("habitación", "apartamento", $res_comp['tit'])); ?></a></li>
			        <?php else : ?>
			        	<li><a href="<?= $res_comp['url'] ?>" class="enlace"><?= $res_comp['tit'] ?></a></li>
			        
			        <?php endif; ?>
			        <?php endforeach; ?>
			   </ul>		    
		    </div>
		    <div style="clear:both"></div>
		</div>
		<div style="clear:both;margin-top:30px;">		    
		    <h2 class="titulo_2">Información adicional <span id="ayuda_adic" onclick='MostrarAyuda("ayuda_adic","AYUDA50<?= $es_hotel ? "_HOT" : "_APT" ?>");' class="ayudaicon enlace" title="Ayuda (tecla de acceso: s)" accesskey="s">&nbsp;</span></h2>
		    <div class="subrayado"></div>
		    <div style="width:100%; float:left; margin-top:10px;">
		        <h2 class="titulo_3">Publicaciones adicionales</h2>
		        <ul class="lista_sin_punto indentado">
        			<?php
            			// NOTA: Se esperan filas de un único enlace sin iconos.
            			$filas=GrupoRecurso::loadGruposEnlaces('INDICE_PUBLICACIONES');
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
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->
</div>
<!-- FIN BLOQUE INTERIOR -->