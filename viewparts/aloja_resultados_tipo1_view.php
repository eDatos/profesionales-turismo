		  <?php switch ($tabla_num) {
		    case VIAJEROS_ENTRADOS: ?>
		    <br><font class="titulo_3"><b>VIAJEROS ENTRADOS</b></font><br>
<font class="istac_bold">Comparativa de los datos sobre el número de viajeros entrados <?= @$tipo ?>.</font><br><br>
		    
		    <?php break; 
		    case VIAJEROS_ALOJADOS: ?>
<br><font class="titulo_3"><b>VIAJEROS ALOJADOS</b></font><br>
<font class="istac_bold">Comparativa de los datos sobre el número de viajeros alojados <?= @$tipo ?>.</font><br><br>
		    
		    <?php break; 
		    case PERNOCTACIONES: ?>
<br><font class="titulo_3"><b>PERNOCTACIONES</b></font><br>
<font class="istac_bold">Comparativa de los datos sobre el número de pernoctaciones <?= @$tipo ?>.</font><br><br>
		    
		    <?php break; 
		    case ESTANCIA_MEDIA: ?>
<br><font class="titulo_3"><b>ESTANCIA MEDIA</b></font><br>
<font class="istac_bold">Comparativa de los datos sobre la estancia media <?= @$tipo ?>.</font><br><br>
		    <?php break; 
		    case INDICE_OCUPACION: ?>
<br><font class="titulo_3"><b>ÍNDICE CENSAL DE OCUPACIÓN POR PLAZAS</b></font><br>
<font class="istac_bold">Comparativa de los datos sobre el índice censal de ocupación <?= @$tipo ?>.</font><br><br>
		    
		    <?php break; 
		    case INDICE_OCUPACION_POR_HABITACIONES: ?>
<br><font class="titulo_3"><b>ÍNDICE CENSAL DE OCUPACIÓN POR <?= ($establecimiento->id_tipo_establecimiento == 3 ? "APARTAMENTOS" : "HABITACIONES")?></b></font><br>
<font class="istac_bold">Comparativa de los datos sobre el índice censal de ocupación <?= @$tipo ?>.</font><br><br>
		    
		  <?php break; 
		    case TARIFA_MEDIA_POR_HABITACION_MENSUAL: ?>
<br><font class="titulo_3"><b>TARIFA MEDIA POR <?= ($establecimiento->id_tipo_establecimiento == 3 ? "APARTAMENTO MENSUAL" : "HABITACIÓN MENSUAL")?></b></font><br>
<font class="istac_bold">Comparativa de los datos sobre la tarifa media por <?= ($establecimiento->id_tipo_establecimiento == 3 ? "apartamento mensual" : "habitación mensual")?> <?= @$tipo ?>.</font><br><br>
		    
		  <?php break; } ?>

<table class="tabr" >
	<tr>
		<td class="c00" rowspan="2" colspan="2"></td>
		<?php if ($establecimiento->id_tipo_establecimiento == 3) : ?>
			<td class="cab0" colspan="4"><?= @str_replace("habitaciones", "apartamentos", str_replace("habitación", "apartamento", $tabla_tit)); ?></td>
		<?php else :?>
			<td class="cab0" colspan="4"><?= @$tabla_tit ?></td>
		<?php endif?>
	</tr>
	<tr>
		<td class="cab1" style="text-align:center">Su establecimiento</td>
		<td class="cab1"><?= $mun_cabecera ?><br>(<?= $cab_grupo ?>)</td>
		<td class="cab1">Isla<br>(<?= $cab_grupo ?>)</td>
		<td class="cab1">Canarias<br>(<?= $cab_grupo ?>)</td>
	</tr>
	<?php foreach ($result as $row) : ?>
	<tr class="rowi">
		<td class="row0"><?= $row['ano']?></td>
		<td class="row01"><?= $row['mes']?></td>
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

