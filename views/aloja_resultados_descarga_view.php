<?php if (count($result) != 0) : ?>
<?php if ($tabla_tipo == 1) : ?>
<?php switch ($tabla_num) {
    case VIAJEROS_ENTRADOS: ?>
VIAJEROS ENTRADOS
Comparativa de los datos sobre el n�mero de viajeros entrados <?= @$tipo ?>.
<?php break; 
    case VIAJEROS_ALOJADOS: ?>
VIAJEROS ALOJADOS
Comparativa de los datos sobre el n�mero de viajeros alojados <?= @$tipo ?>.
<?php break; 
    case PERNOCTACIONES: ?>
PERNOCTACIONES
Comparativa de los datos sobre el n�mero de pernoctaciones <?= @$tipo ?>.
<?php break; 
    case ESTANCIA_MEDIA: ?>
ESTANCIA MEDIA
Comparativa de los datos sobre la estancia media <?= @$tipo ?>.
<?php break; 
    case INDICE_OCUPACION: ?>
�NDICE CENSAL DE OCUPACI�N POR PLAZAS
Comparativa de los datos sobre el �ndice censal de ocupaci�n <?= @$tipo ?>.
<?php break; 
    case INDICE_OCUPACION_POR_HABITACIONES: ?>
    <?php if ($establecimiento->id_tipo_establecimiento == 3) : ?>
�NDICE CENSAL DE OCUPACI�N POR APARTAMENTOS
    <?php else: ?>
�NDICE CENSAL DE OCUPACI�N POR HABITACIONES    
    <?php endif; ?>
Comparativa de los datos sobre el �ndice censal de ocupaci�n <?= @$tipo ?>.    
<?php break; 
    case TARIFA_MEDIA_POR_HABITACION_MENSUAL: ?>
    <?php if ($establecimiento->id_tipo_establecimiento == 3) : ?>
TARIFA MEDIA POR APARTAMENTO MENSUAL
Comparativa de los datos sobre la tarifa media por apartamento mensual <?= @$tipo ?>.
<?php else: ?>
TARIFA MEDIA POR HABITACI�N MENSUAL
Comparativa de los datos sobre la tarifa media por habitaci�n mensual <?= @$tipo ?>.
<?php endif; ?>
<?php break; } ?>
<?php if ($establecimiento->id_tipo_establecimiento == 3) : ?>
;<?= @str_replace("habitaciones", "apartamentos", str_replace("habitaci�n", "apartamento", $tabla_tit)); ?>
<?php else: ?>
;<?= @$tabla_tit ?>
<?php endif; ?>

;Su establecimiento;<?= $mun_cabecera ?> (<?= $cab_grupo ?>);Isla (<?= $cab_grupo ?>);Canarias (<?= $cab_grupo ?>)
<?php foreach ($result as $row) : ?>

<?= $row['ano']?> <?= $row['mes']?>;<?= $row['estab'] ?>;<?= $row['munic'] ?>;<?= $row['isla'] ?>;<?= $row['canarias'] ?>
<?php endforeach; ?>

* Dato no disponible debido a una de las siguientes causas: Dato ilegible en la recepci�n del fax, Cuestionario recibido fuera de plazo, No contest� a la pregunta correspondiente, No envi� el cuestionario
<?php else: ?>
<?php switch ($tabla_num) {
	case VIAJEROS_ENTRADOS_POR_LUGAR_RESIDENCIA: ?>
VIAJEROS ENTRADOS POR LUGAR DE RESIDENCIA
Comparativa del n�mero de viajeros entrados en establecimientos <?= $tipo_est ?> en el mes de <?= $mes  ?> de <?= $ano  ?> por lugar de residencia.
<?php break; 
		    case VIAJEROS_ALOJADOS_POR_PAIS: ?>
VIAJEROS ALOJADOS POR LUGAR DE RESIDENCIA
Comparativa del n�mero de viajeros alojados en establecimientos <?= $tipo_est ?> en el mes de <?= $mes  ?> de <?= $ano  ?> por lugar de residencia.
<?php break; 
		    case PERNOCTACIONES_POR_PAIS: ?>
PERNOCTACIONES POR LUGAR DE RESIDENCIA
Comparativa del n�mero de pernoctaciones en establecimientos <?= $tipo_est ?> en el mes de <?= $mes  ?> de <?= $ano  ?> por lugar de residencia.
<?php break; 
		    case ESTANCIA_MEDIA_POR_LUGAR_RESIDENCIA: ?>
ESTANCIA MEDIA POR LUGAR DE RESIDENCIA
Comparativa de la estancia media <?= @$tipo ?> en el mes de <?= $mes  ?> de <?= $ano  ?> por lugar de residencia.
<?php break; } ?>

;<?= @$tabla_tit ?>

Lugar de residencia;Su establecimiento;<?= $mun_cabecera ?> (<?= $cab_grupo ?>);Isla (<?= $cab_grupo ?>);Canarias (<?= $cab_grupo ?>)
<?php foreach ($result as $row) : ?>
<?= $row['lugar_residencia'] ?>;<?= $row['estab'] ?>;<?= $row['munic'] ?>;<?= $row['isla'] ?>;<?= $row['canarias'] ?>

<?php endforeach; ?>	

* Dato no disponible debido a una de las siguientes causas: Dato ilegible en la recepci�n del fax, Cuestionario recibido fuera de plazo, No contest� a la pregunta correspondiente, No envi� el cuestionario
<?php endif;?>
<?php endif;?>