<script>
	$(document).ready( function() {
		print();
	});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<div id="tabs">
	<div id="ent_sal_pern" style="padding-left: 8px;padding-right: 8px;">
	<?php 
		  function imprimir_cabecera_pagina($cuestionario, $datos_estab)
		  {
		  	?> 

		  		<div id='pagina' style='margin-top:8px;'>
		  		<img src="images/logo_istac.jpg"/>
		  		<br/><br/>
				<h2 class='titulo_2'>Encuesta de Empleo Turístico en Establecimientos <?= $datos_estab->id_tipo_establecimiento==3 ? "Extrahoteleros" : "Hoteleros" ?>: <?= DateHelper::mes_tostring( $cuestionario->mes,'M') ?> de <?= $cuestionario->ano ?></h2>
			        <br/>
				    <table cellpadding="0" cellspacing="0" style="width:957px;">
				    	<tr>
				    		<td style="width:50%;">Nombre: <?= $datos_estab->nombre_establecimiento ?></td>
				    		<td style="width:25%;">Código: <?= $datos_estab->id_establecimiento ?></td>
				    		<td style="width:25%;">Categoría: <?= $datos_estab->grupo_categoria ?></td>
				    	</tr>
				    	<tr>
				    		<td style="width:50%;">Nº de  <?= $datos_estab->id_tipo_establecimiento==3 ? "apartamentos" : "habitaciones" ?>: <?= $datos_estab->num_habitaciones ?></td>
				    		<td style="width:25%;">Nº de plazas: <?= $datos_estab->num_plazas ?></td>
				    		<td style="width:25%;">Nº de plazas supletorias: <?= $datos_estab->num_plazas_supletorias ?></td>
				    	</tr>
				    	<tr>
				    		<td style="width:50%;">Municipio: <?= $datos_estab->municipio ?></td>
				    		<td style="width:25%;" colspan="2">Isla: <?= $datos_estab->nombre_isla ?></td>
				    	</tr>	    	
				    	<tr>
				    		<td style="width:50%;">Director: <?= $datos_estab->director ?></td>
				    		<td style="width:25%;" colspan="2">E-mail: <?= $datos_estab->email ?></td>
				    	</tr>
				    	<tr>
				    		<td style="width:50%;">&nbsp;</td>
				    		<td style="width:25%;" colspan="2">E-mail 2: <?= $datos_estab->email2 ?></td>
				    	</tr>
			    	</table>
			    	<br/><br/>
	<?php 
		  }
	?>
		</div>

		
		
		<div id="habitaciones" style="clear:both; padding-left: 8px;padding-right: 8px;">
		<?php imprimir_cabecera_pagina($cuestionario, $datos_estab); ?>

			<div id="cab_habitac">
				<table class="tabla_hab">
					<tr style="height:28px;background-color:#caddf7;vertical-align:middle;">
						<td colspan="2" style="background-color:#ffffff;"></td>
						<td colspan="5" style="padding-left:6px;">Detalle del número de contrataciones por cada cuenta de cotización</td>
					</tr>	
					<tr style="height:25px;vertical-align:middle;border-bottom: solid 1px #e0e0e0;background-color:#f2f2f2;">
							<td colspan="2" style="background-color:#ffffff;width:137px;"></td>
                            <th scope="col" style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-left: solid 1px #caddf7;">CUENTA COTIZACIÓN</th>
                            <th scope="col" style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-left: solid 1px #caddf7;">DESCRIPCION</th>
                            <th scope="col" style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-left: solid 1px #caddf7;">Nº EMPLEADOS</th>
					</tr>
					<?php
					$total_resto=0; 
						foreach ($data as $datos)
						{
						    if($datos->externo!=EMPLEADOR_EXTERNO)
						    {
					?>						
        					    <tr class="tabla_hab_fila">
        					    	<td colspan="2" />
        					    	<td class="tabla_hab_celda_162"><?= $datos->id_empleador_display ?></td>
        					    	<td class="tabla_hab_celda_162"><?= $datos->descripcion ?></td>
        					    	<td class="tabla_hab_celda_162"><?=($datos->num_empleados=='-1')?'N/A':$datos->num_empleados ?></td>
        							<?php 
        							if($datos->num_empleados!='-1')
        							    $total_resto += $datos->num_empleados;
        							?>
        						</tr>			
					<?php
						    }
						}
					?>						
					<tr class="tabla_hab_fila_tot">
							<td colspan="2" class="tabla_hab_fila_tot_celda_137">Totales</td>
							<td colspan="4" _class="tabla_hab_fila_tot_celda_162"></td>
					    	<td class="tabla_hab_fila_tot_celda_101"><?= $total_resto ?></td>
					</tr>									
				</table>
			</div>

<br/>
<br/>

			<div id="cab_habitac">
				<table class="tabla_hab">
					<tr style="height:28px;background-color:#caddf7;vertical-align:middle;">
						<td colspan="2" style="background-color:#ffffff;"></td>
						<td colspan="5" style="padding-left:6px;">Detalle del número de contrataciones a través de ETT</td>
					</tr>	
					<tr style="height:25px;vertical-align:middle;border-bottom: solid 1px #e0e0e0;background-color:#f2f2f2;">
							<td colspan="2" style="background-color:#ffffff;width:137px;"></td>
                            <th scope="col" style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-left: solid 1px #caddf7;">CIF</th>
                            <th scope="col" style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-left: solid 1px #caddf7;">EMPRESA</th>
                            <th scope="col" style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-left: solid 1px #caddf7;">Nº EMPLEADOS</th>
					</tr>
					<?php
						$total_ett=0; 
						foreach ($data as $datos)
						{
						    if($datos->externo==EMPLEADOR_EXTERNO)
						    {
					?>						
        					    <tr class="tabla_hab_fila">
        					    	<td colspan="2" />
        					    	<td class="tabla_hab_celda_162"><?= $datos->id_empleador_display ?></td>
        					    	<td class="tabla_hab_celda_162"><?= $datos->descripcion ?></td>
        					    	<td class="tabla_hab_celda_162"><?=($datos->num_empleados=='-1')?'N/A':$datos->num_empleados ?></td>
        							<?php 
        							if($datos->num_empleados!='-1')
        							    $total_ett += $datos->num_empleados;
        							?>
        						</tr>			
					<?php
						    }
						}
					?>						
					<tr class="tabla_hab_fila_tot">
							<td colspan="2" class="tabla_hab_fila_tot_celda_137">Totales</td>
							<td colspan="4" _class="tabla_hab_fila_tot_celda_162"></td>
					    	<td class="tabla_hab_fila_tot_celda_101"><?= $total_ett ?></td>
					</tr>									
				</table>
			</div>			
						
		</div>
		
		<br/>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->