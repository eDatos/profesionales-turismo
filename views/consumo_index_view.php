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
	<h1 class="titulo_1" style="float:left;">Recogida de Consumos</h1>
	<a id="ayuda_exp" href="javascript:MostrarAyuda('ayuda_exp','AYUDA70<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon enlace" style="float:right;margin-top:9px;background-position-y: 2px;" title="Ayuda (tecla de acceso: y)" accesskey="y"><strong>Ayuda</strong></a>	
	<div style="clear:both;"></div>
	
	<div class="columna_pequ_der">
		<?php include(__DIR__."/../viewparts/recuerde_part.php"); ?>	
		<?php include(__DIR__."/../viewparts/ayudenos_part.php"); ?>
	</div>	
	<div class="bloq_central">
		<div class="cuadro fondo_amarillo">
			<h2 class="titulo_2" style="float: left;">Recogida de facturas:</h2>
			<div class="subrayado inferior_15"></div>
			<div class="formicon">
				<a class="titulo_3 enlace" href="<?= $site[PAGE_CONSUMO_FORM]; ?>">Cumplimentar nueva factura</a>
			</div>
		</div>	
		<!-- COMIENZO BLOQUE LINKS A ENCUESTAS ANTERIORES-->
		<div>
		    <h2 class="titulo_2">Gestionar Facturas</h2>
		    <div class="subrayado"></div>
		    <div style="width:50%; float:left;">
		        <ul class="lista_sin_punto indentado">
			        <li><a class="titulo_3 enlace" href="<?= $site[PAGE_CONSUMO_LIST]; ?>">Buscar facturas anteriores</a></li>
		        </ul>
		    </div>
		    <div style="clear:both"></div>
		</div>
		<div style="clear:both;margin-top:30px;">
		    <h2 class="titulo_2">Facturas anteriores</h2>
		    <div class="subrayado"></div>
		    <div style="width:50%; float:left;">
		        <h2 class="titulo_3">Sus facturas más recientes (menos de un año)</h2>
		        <?php if (isset($facturasRecientes) && count($facturasRecientes) > 0): ?>
		        <ul class="lista_sin_punto indentado">
		        	<?php foreach($facturasRecientes as $facturaReciente): ?>
		        		<!-- <li><?= $facturaReciente->fecha->format('d/m/Y') ?> Factura Nº <a href="<?= $this->build_url( PAGE_CONSUMO_FORM, array(ARG_PARTE=>'1', ARG_NUMERO_FACTURA=>$facturaReciente->num_factura)) ?>"><b><?= $facturaReciente->num_factura ?></b></a> (<?= $facturaReciente->tipo ?>)</li> -->
		        		<li><?= $facturaReciente->fecha->format('d/m/Y') ?> Factura Nº <a href="<?= $this->build_url( PAGE_CONSUMO_PRINT, array(ARG_NUMERO_FACTURA=>$facturaReciente->num_factura)) ?>"><b><?= $facturaReciente->num_factura ?></b></a> (<?= $facturaReciente->tipo ?>)</li>
			        <?php endforeach; ?>
		        </ul>
		        <?php else : ?>	
		        <ul class="lista_sin_punto indentado">
			        <li>No existe ninguna factura en el último año</li>
		        </ul>
		        <?php endif; ?>
		    </div>
		    <div style="clear:both"></div>
		</div>
		<div style="clear:both;margin-top:30px;">		    
		    <h2 class="titulo_2">Información adicional <span id="ayuda_adic" onclick='MostrarAyuda("ayuda_adic","AYUDA71<?= $es_hotel ? "_HOT" : "_APT" ?>");' class="ayudaicon enlace" title="Ayuda (tecla de acceso: s)" accesskey="s">&nbsp;</span></h2>
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
	<div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
</div>
<!-- FIN BLOQUE INTERIOR -->
