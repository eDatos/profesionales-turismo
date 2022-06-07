<?php  if($listAvisos != NULL && $listAvisos->has_rows()): ?>
<!-- COMIENZO MENU BORDES CUADRADOS -->
	<div class="cuadro">
        <h3 class="titulo_3">Información de interés</h3>
	    <div class="subrayado"> </div>
	    <?php 
    	if($listAvisos != NULL && $listAvisos->has_rows())
    	{ 
			echo '<ul class="lista_con_punto">';
			$i=0;
			$enlace_mas = FALSE;
			foreach ($listAvisos as $av)
			{
				if($i==NUM_AVISOS_MOSTRAR) 
				{
					$enlace_mas = TRUE;
					break;
				}
				echo '<li><a class="enlace" href="' . $page->build_url(PAGE_AVISOS_LIST, array("fecha_creacion" => $av['fecha_creacion'])) . '">' . $av['titulo'] . '</a></li>';
				$i++;
			}
			echo '</ul>';
			if($enlace_mas) echo '<div style="text-align:right;"><a href="' . $page->build_url(PAGE_AVISOS_LIST, array("id_grupo" => $cuestionario->id_grupo)) . '" class="enlace ver_mas">más</a></div>';
		}
		else {
			echo '<ul class="lista_sin_punto">';
			echo '<li>No hay información disponible.</li>';
			echo '</ul>';
		}
	?>
</div>
<!-- FIN MENU BORDES CUADRADOS -->	
<?php  endif; ?>