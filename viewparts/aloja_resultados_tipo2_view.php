		  <?php switch ($tabla_num) {
		    case VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA: ?>
<br><font class="titulo_3"><b>VIAJEROS ENTRADOS POR LUGAR DE RESIDENCIA</b></font>
<br>
<font class="istac_bold">Comparativa del número de viajeros entrados en establecimientos <?= $tipo_est ?> en el mes de <?= $mes  ?> de <?= $ano  ?> por lugar de residencia.</font>
<br>
<br>		    
		    <?php break; 
		    case VIAJEROS_ALOJADOS_POR_PAIS: ?>
<br><font class="titulo_3"><b>VIAJEROS ALOJADOS POR LUGAR DE RESIDENCIA</b></font><br>
<font class="istac_bold">Comparativa del número de viajeros alojados en establecimientos <?= $tipo_est ?> en el mes de <?= $mes  ?> de <?= $ano  ?> por lugar de residencia.</font><br><br>
		    
		    <?php break; 
		    case PERNOCTACIONES_POR_PAIS: ?>
<br><font class="titulo_3"><b>PERNOCTACIONES POR LUGAR DE RESIDENCIA</b></font><br>
<font class="istac_bold">Comparativa del número de pernoctaciones en establecimientos <?= $tipo_est ?> en el mes de <?= $mes  ?> de <?= $ano  ?> por lugar de residencia.</font><br><br>
		    
		    <?php break; 
		    case ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA: ?>
<br><font class="titulo_3"><b>ESTANCIA MEDIA POR LUGAR DE RESIDENCIA</b></font><br>
<font class="istac_bold">Comparativa de la estancia media <?= @$tipo ?> en el mes de <?= $mes  ?> de <?= $ano  ?> por lugar de residencia.</font><br><br>
		    
		  <?php break; } ?>


<table class="tabr">
	<tr>
		<td class="c01"></td>
		<td class="cab0" colspan="4"><?= @$tabla_tit ?></td>
	</tr>
	<tr>
		<td class="cab01" style="text-align:center; padding: 0px;">Lugar de residencia</td>
		<td class="cab1" style="text-align:center">Su establecimiento</td>
		<td class="cab1"><?= $mun_cabecera ?><br>(<?= $cab_grupo ?>)</td>
		<td class="cab1">Isla<br>(<?= $cab_grupo ?>)</td>
		<td class="cab1">Canarias<br>(<?= $cab_grupo ?>)</td>
	</tr>
	<?php foreach ($result as $row) : ?>
	<tr class="rowi">
		<td class="row02"><?= $row['lugar_residencia'] ?></td>
		<td><?= $row['estab'] ?></td>
		<td><?= $row['munic'] ?></td>
		<td><?= $row['isla'] ?></td>
		<td><?= $row['canarias'] ?></td>
	</tr>
	<?php endforeach; ?>	
</table>
<table border='0' width='90%' cellspacing='0' cellpadding='5' align='center'>       
      <tr bgcolor='ffffff'>
        <td class='istac_bold'>* Dato no disponible debido a una de las siguientes causas: Dato ilegible en la recepción del fax, Cuestionario recibido fuera de plazo, No contestó a la pregunta correspondiente, No envió el cuestionario
        </td>
      </tr>
	  <tr bgcolor='ffffff'>
        <td class='istac_bold'><?= @$pie_grupo ?></td>
      </tr>	  
</table>