<style>
hr {
	width: 100%;
	height: 1px;
	background-color: #FFEB99;
	color: #FFEB99;
	border: 0px;
}
  .tabr {
  	margin-left: 46px;
  	border-collapse: collapse;
  	color: #333333;
  }
  .c00 {
  	background-color : #F7F7F7;
  	border-bottom: 1px solid #E0E0E0;
  }
  .cab0 {
  	background-color : #CADDF7;
  	border-right: 1px solid #B1CDF1;
  	padding-left: 10px;
  }
  .c01 {
  	background-color : #F7F7F7;
  }
  .cab01 {
  	background-color : #F7F7F7;
  	padding: 0px;
  	border-bottom: 1px solid #E0E0E0;
  }  
  .cab1 {
  	background-color : #F2F2F2;
  	border-right: 1px solid #CADDF7;
  	border-bottom: 1px solid #E0E0E0;
  	text-align:right;
  	padding-right: 10px;
  	
  } 
  .rowi {
  	background-color : #FBFBFB;
  	border-top: 1px solid #F0F0F0;
  	border-right: 1px solid #CADDF7;
  	text-align:right;
  }
   .rowi td {
  	padding-right: 10px;
  	width:170px;
  }
  .rowi .row0 {
  	width: 10px;
  	padding-left: 10px;
  	padding-right: 0px;
  	background-color : #F2F2F2;
  	border-top: 1px solid #E0E0E0;
  	text-align:left;
  }
  .rowi .row01 {
  	width: 100px;
  	padding-left: 10px;
  	padding-right: 10px;
  	background-color : #F2F2F2;
  	border-top: 1px solid #E0E0E0;
  	text-align:left;
  }
  .rowi .row02 {
  	width: 140px;
  	padding-left: 10px;
  	padding-right: 10px;
  	background-color : #F2F2F2;
  	border-top: 1px solid #E0E0E0;
  	text-align:left;
  }
</style>
<script type="text/javascript"> 
	$(document).ready(function() {
		$("select[name='tabla']").change(function()
		{
			$("#tablas_resultados_alojamientos").submit();
		});

		$("option[value='<?= $tabla_num ?>']").attr("selected", "selected");
		$("input[name='printBtn']").button().click(function()
				{
					window.print();
				});
		$("input[name='descargarBtn']").button().click(function()
				{
			window.location = "<?= $descarga_url ?>";
		});
		
	});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1 noprint">Explotaciones personalizadas de la encuesta de Alojamiento</h1>
	<div class="bloq_central">
		  <table width="100%" border=0 class="noprint">
		    <tr>    
		      <td width="25%" valign="top">Seleccione la tabla que desee visualizar: &nbsp;&nbsp;</td>
		      <td class="istac" align="left" valign="top">
		        <form name="tablas_resultados_alojamientos" id="tablas_resultados_alojamientos" method="post" action="#">
		          <select name="tabla">
		            <option value="<?= VIAJEROS_ENTRADOS?>"> VIAJEROS ENTRADOS </option>
		            <option value="<?=VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA?>"> VIAJEROS ENTRADOS POR LUGAR DE RESIDENCIA</option>
		            <option value="<?=VIAJEROS_ALOJADOS?>"> VIAJEROS ALOJADOS </option>
		            <option value="<?=VIAJEROS_ALOJADOS_POR_PAIS?>"> VIAJEROS ALOJADOS POR LUGAR DE RESIDENCIA </option>
		            <option value="<?=PERNOCTACIONES?>"> PERNOCTACIONES </option>
		            <option value="<?=PERNOCTACIONES_POR_PAIS?>"> PERNOCTACIONES POR LUGAR DE RESIDENCIA </option>
		            <option value="<?=ESTANCIA_MEDIA?>"> ESTANCIA MEDIA </option>
		            <option value="<?=ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA?>"> ESTANCIA MEDIA POR LUGAR DE RESIDENCIA</option>
		            <option value="<?=INDICE_OCUPACION?>"> ÍNDICE CENSAL DE OCUPACIÓN POR PLAZAS</option>
		            <option value="<?=INDICE_OCUPACION_POR_HABITACIONES?>"> ÍNDICE CENSAL DE OCUPACIÓN POR <?= ($establecimiento->id_tipo_establecimiento == 3 ? "APARTAMENTOS" : "HABITACIONES")?> </option>
		            <option value="<?=TARIFA_MEDIA_POR_HABITACION_MENSUAL?>"> TARIFA MEDIA POR <?= ($establecimiento->id_tipo_establecimiento == 3 ? "APARTAMENTO MENSUAL" : "HABITACIÓN MENSUAL")?> </option>
		          </select>
		        </form>
		      </td>
		    </tr>
		    <tr>
		      <td colspan='2'><hr></td>
		    </tr>
		  </table>
		  
		  <?php if (count($result) == 0) : ?>
		  <p align="center">La consulta no ha devuelto ningún resultado</p>
		  <?php else :?>
		  <?php if ($tabla_tipo == 1)
		    include(__DIR__ . "/../viewparts/aloja_resultados_tipo1_view.php");
		  else
		  	include(__DIR__ . "/../viewparts/aloja_resultados_tipo2_view.php");
		  ?>
		  <?php endif;?>
		<div style="margin-top:20px;float:left;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
		<div class="noprint" style="float:right;">
		<?php if (count($result) != 0) : ?>
		<input class="search ui-button ui-widget ui-state-default ui-corner-all" name="printBtn" type="button" value="Imprimir" style="width:95px; background-image: url(images/imprimir.png);background-repeat: no-repeat;background-position: 8px 4px;margin-left:10px;padding-left:27px;margin-top:15px;" role="button" aria-disabled="false">
		<input class="search ui-button ui-widget ui-state-default ui-corner-all" name="descargarBtn" type="button" value="Descargar" style="width:95px; background-image: url(images/descargar.png);background-repeat: no-repeat;background-position: 8px 6px;margin-left:10px;padding-left:27px;margin-top:15px;" role="button" aria-disabled="false">
		<?php endif;?>
		</div>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->