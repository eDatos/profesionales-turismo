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
		  function imprimir_cabecera_pagina($cuestionario, $datos_estab, $ent_sal_pern, $num_pagina)
		  {
		  		if($num_pagina==1) 
		  		{
		  			echo "<div id='pagina' style='margin-top:8px;'>";
		  		}
		  		else
		  		{
		  			echo "</div>";
		  			echo "<div id='pagina' style='page-break-before: always;margin-top:8px;'>";
		  		}
		  	?> 
		  		
		  		<img src="images/logo_istac.jpg"/>
		  		<br/><br/>
				<h2 class='titulo_2'>Encuesta de Alojamiento Turístico en Establecimientos <?= $datos_estab->id_tipo_establecimiento==3 ? "Extrahoteleros" : "Hoteleros" ?>: <?= DateHelper::mes_tostring( $cuestionario->mes,'M') ?> de <?= $cuestionario->ano ?></h2>
			        <img src="images/calendario.png" style="margin-top:-3px;vertical-align:middle;"/>&nbsp;&nbsp;<strong>Días abierto en <?= DateHelper::mes_tostring( $cuestionario->mes,'m') ?> de <?= $cuestionario->ano ?>:</strong>&nbsp;<?= @$cuestionario->dias_abierto ?><br/>
			        <strong><?=count($ent_sal_pern); ?> nacionalidades y provincias seleccionadas</strong>
			        <br/><br/>
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
	        
		list($mes_anterior,$ano_anterior) = DateHelper::mes_anterior($cuestionario->mes, $cuestionario->ano);
		$modo_introduccion = (isset($cuestionario->modo_introduccion) && $cuestionario->modo_introduccion == '0') ? 0 : 1;
		
		//Se imprimirán tres grupos
		//1. Países habituales + Países no habituales. En el total se incluyen los datos de España
		//2. Provicias de España sin especificar Canarias. En el total se incluyen todas las provincias más las de Canarias.
		//3. Provincias e islas de Canarias. En el total se incluyen las provincias e islas de Canarias.
		
		//Inicialización de los grupos y de los totales
		$tipos_grupos = array('paises','provincias','canarias');
		$grupos = array();
		$totales = array();
		foreach($tipos_grupos as $tipo_grupo)
		{
			$grupos[$tipo_grupo] = array();
			$totales[$tipo_grupo] = array();
			$totales[$tipo_grupo]['presentes_comienzo_mes']=0;
			for($dia=1;$dia<=$dias_rellenados;$dia++)
			{
				$totales[$tipo_grupo][$dia]=new AlojaESP();
				$totales[$tipo_grupo][$dia]->entradas=0;
				$totales[$tipo_grupo][$dia]->salidas=0;
				$totales[$tipo_grupo][$dia]->pernoctaciones=0;
			}
		}
		
		foreach($ent_sal_pern as $datos_ut)
		{
			switch($datos_ut['es_nacional'])
			{
				case '0':	//Países habituales
				case '2':	//Países no habituales
					$tipo_grupo = 'paises';					
					break;		
				case '1':	//Provincias (sin Canarias)
					$tipo_grupo = 'provincias';
					break;
				case '3':	//Canarias
					$tipo_grupo = 'canarias';
					break;
			}
			array_push($grupos[$tipo_grupo],$datos_ut);
			$totales[$tipo_grupo]['presentes_comienzo_mes']+=$datos_ut['presentes_comienzo_mes'];
			for($dia=1;$dia<=$dias_mostrar_formulario;$dia++)
			{
				if (array_key_exists($dia, $datos_ut['filas']->movimientos))
				{
					$totales[$tipo_grupo][$dia]->entradas+=$datos_ut['filas']->movimientos[$dia]->entradas;
					$totales[$tipo_grupo][$dia]->salidas+=$datos_ut['filas']->movimientos[$dia]->salidas;
					$totales[$tipo_grupo][$dia]->pernoctaciones+=$datos_ut['filas']->movimientos[$dia]->pernoctaciones;
				}				
			}	
		}
		
		//Se suman a PROVINCIAS los totales de CANARIAS y de PROVINCIAS a PAISES
		$totales['provincias']['presentes_comienzo_mes']+=$totales['canarias']['presentes_comienzo_mes'];
		$totales['paises']['presentes_comienzo_mes']+=$totales['provincias']['presentes_comienzo_mes'];
		for($dia=1;$dia<=$dias_rellenados;$dia++)
		{
			$totales['provincias'][$dia]->entradas+=$totales['canarias'][$dia]->entradas;
			$totales['provincias'][$dia]->salidas+=$totales['canarias'][$dia]->salidas;
			$totales['provincias'][$dia]->pernoctaciones+=$totales['canarias'][$dia]->pernoctaciones;

			$totales['paises'][$dia]->entradas+=$totales['provincias'][$dia]->entradas;
			$totales['paises'][$dia]->salidas+=$totales['provincias'][$dia]->salidas;
			$totales['paises'][$dia]->pernoctaciones+=$totales['provincias'][$dia]->pernoctaciones;
		}		
		
		foreach($tipos_grupos as $tipo_grupo)
		{
			if(count($grupos[$tipo_grupo])!=0)
			{
				while((count($grupos[$tipo_grupo])+1) % (MAX_UT_MOSTRAR+1)!=0)
				{
					array_push($grupos[$tipo_grupo],null);
				}
			}				
		}
	?>
        <?php $items_fila=0; $index=0; $num_pagina=1; ?>
		<?php foreach ($tipos_grupos as $tipo_grupo): ?>
			<?php for($i=0;$i<count($grupos[$tipo_grupo]);$i++): ?>
					<?php $tot_col1 = 0; $tot_col2 = 0; $tot_col3 = 0; ?>
					<?php if($items_fila==0): ?>
					<?php imprimir_cabecera_pagina($cuestionario, $datos_estab, $ent_sal_pern, $num_pagina); $num_pagina++; ?>
					<div id="cab_mes_dia" style="clear:both;float: left;border-right:solid 1px #b1cdf1;margin-bottom:20px;">
						<table class="tabla_esp_mes_dia">
							<tr style="height:60px;">
								<td colspan="2" style="background-color:#ffffff;"></td>
							</tr>	
							<tr style="height:26px;vertical-align:middle;">
								<td class="tabla_esp_celda_mes_ant"><div id="mes" style="padding-left:4px;"><?= DateHelper::mes_tostring($mes_anterior,'M') ?></div></td>
								<td class="tabla_esp_celda_dia_ant"><div id="dia0" style="text-align:center;"><?= $ult_mes_ant->format('d') ?></div></td>
							</tr>
							<tr style="height:26px;vertical-align:middle;">
								<td class="tabla_esp_celda_mes_enc"><div id="mes" style="padding-left:4px;"><?= DateHelper::mes_tostring( $cuestionario->mes,'M') ?></div></td>
								<td class="tabla_esp_celda_dia_enc"><div id="dia1" style="text-align:center;">01</div></td>
							</tr>
							<?php for($dia=2;$dia<=$dias_mostrar_formulario;$dia++):?>
							    <tr style="height:25px;vertical-align:middle;border-bottom: solid 1px #e0e0e0;">
								    <td style="border-bottom: solid 1px #e0e0e0;"></td>
								    <td style="width:30px; text-align:center;border-bottom: solid 1px #e0e0e0;"><div id="dia<?= $dia ?>" style="text-align:center;"><?= sprintf('%02d', $dia); ?></div></td>
							    </tr>					
							<?php endfor ?>
							<tr style="height:26px;vertical-align:middle;">
								<td colspan="2" class="tabla_esp_total"><div id="fila_totales" style="padding-left:4px;width:70px;">Total</div></td>
							</tr>                    
							<tr style="height:26px;vertical-align:middle;">
								<td colspan="2" class="tabla_esp_est_media"><div id="fila_est_med" style="padding-left:4px;">Estancia media</div></td>
							</tr>                    
						</table>
					</div>
					<?php endif ?>
					<div style="float:left;border-right:solid 1px #b1cdf1;">
						<table class="tabla_esp">
							<tr style="height:27px;background-color:#caddf7;vertical-align:middle;">
								<td colspan=3 style="height:38px"><div id="ut<?= $index ?>_tit" style="margin-left:4px;white-space: nowrap;width: 155px;text-overflow: ellipsis;overflow: hidden;vertical-align:middle;"><?=$grupos[$tipo_grupo][$i]['nombre']?></div></td>
							</tr>				
							<tr style="height:23px;vertical-align:middle;background-color:#f2f2f2;">
								<td style="width:53px; text-align:center;border-bottom: solid 1px #e0e0e0;border-right: solid 1px #caddf7;"><div id="ut<?= $index ?>_col0_tit" style="text-align:center;">Entradas</div></td>
								<td style="width:53px; text-align:center;border-bottom: solid 1px #e0e0e0;border-right: solid 1px #caddf7;"><div id="ut<?= $index ?>_col1_tit" style="text-align:center;"><?=$modo_introduccion==MODO_INTRO_ES ? 'Salidas' : 'Pernoc.'?></div></td>
								<td style="width:53px; text-align:center;border-bottom: solid 1px #e0e0e0;"><div id="ut<?= $index ?>_col2_tit" style="text-align:center;"><?=$modo_introduccion==MODO_INTRO_ES ? 'Pernoc.' : 'Salidas'?></div></td>
							</tr>
							<tr style="height:26px;vertical-align:middle;border-bottom: solid 1px #f0f0f0;background-color:#fbfbfb;">
								<td style="width:53px; text-align:center;border-bottom: solid 1px #f0f0f0;"></td>
								<td style="width:53px; text-align:center;border-bottom: solid 1px #f0f0f0;"><?php if($modo_introduccion==MODO_INTRO_EP) {echo $grupos[$tipo_grupo][$i]['presentes_comienzo_mes']; } else {echo '';} ?></td>
								<td style="width:53px; text-align:center;border-bottom: solid 1px #f0f0f0;"><?php if($modo_introduccion==MODO_INTRO_ES) {echo $grupos[$tipo_grupo][$i]['presentes_comienzo_mes']; } else {echo '';} ?></td>
							</tr>
							
							<?php for($dia=1;$dia<=$dias_mostrar_formulario;$dia++): ?>				
						    <tr class="tabla_esp_fila">
							    <td class="tabla_esp_celda_54"><?php if(isset($grupos[$tipo_grupo][$i]['filas']->movimientos[$dia])) { echo $grupos[$tipo_grupo][$i]['filas']->movimientos[$dia]->entradas; $tot_col1+=$grupos[$tipo_grupo][$i]['filas']->movimientos[$dia]->entradas; } else {echo "-";}?></td>
							    <td class="tabla_esp_celda_54"><?php if(isset($grupos[$tipo_grupo][$i]['filas']->movimientos[$dia])) {$valor=($modo_introduccion==MODO_INTRO_ES ? $grupos[$tipo_grupo][$i]['filas']->movimientos[$dia]->salidas : $grupos[$tipo_grupo][$i]['filas']->movimientos[$dia]->pernoctaciones); echo $valor; $tot_col2+=$valor; /*No importa que sean salidas o pernoctaciones. En el total se asumen las salidas como la 2a columna*/} else {echo "-";}?></td>
							    <td class="tabla_esp_celda_53"><?php if(isset($grupos[$tipo_grupo][$i]['filas']->movimientos[$dia])) {$valor=($modo_introduccion==MODO_INTRO_ES ? $grupos[$tipo_grupo][$i]['filas']->movimientos[$dia]->pernoctaciones : $grupos[$tipo_grupo][$i]['filas']->movimientos[$dia]->salidas); echo $valor; $tot_col3+=$valor; /*No importa que sean salidas o pernoctaciones. En el total se asumen las pernoctaciones como la 3a columna*/} else {echo "-";}?></td>
						    </tr>
							<?php endfor ?>
							<tr class="tabla_esp_fila_tot">
							    <td class="tabla_esp_celda_tot_54"><?=($grupos[$tipo_grupo][$i]==NULL ? "-" : $tot_col1) ?></td>
							    <td class="tabla_esp_celda_tot_54"><?=($grupos[$tipo_grupo][$i]==NULL ? "-" : $tot_col2) ?></td>
								<td class="tabla_esp_celda_tot_53"><?=($grupos[$tipo_grupo][$i]==NULL ? "-" : $tot_col3) ?></td>
							</tr>                    
							<tr class="tabla_esp_fila_tot">
								<td class="tabla_esp_celda_tot" colspan=3>
										<?php
											//Se calcula la estancia media
											if($grupos[$tipo_grupo][$i]==NULL)
											{
												echo "-";
											}
											else
											{
												$est_med=0;
												if($tot_col1+$grupos[$tipo_grupo][$i]['presentes_comienzo_mes']!=0)
													$est_med = ($modo_introduccion==MODO_INTRO_ES ? $tot_col3:$tot_col2) / ($tot_col1+$grupos[$tipo_grupo][$i]['presentes_comienzo_mes']);
												echo str_replace('.',',',round($est_med*100)/100);
											}				
										?>
								</td>
							</tr>					
						</table>										
					</div>
					<?php	
						$items_fila++; $index++;
						if($items_fila==5)
						{
							$items_fila=0;
						}  
					?>
			<?php endfor ?>
			
			<?php if(count($grupos[$tipo_grupo])!=0): ?>
			<div id="cab_col_totales" style="float: left;margin-bottom:20px;border-right:solid 1px #b1cdf1;" cellpadding="0" cellspacing="0">
				<table class="tabla_esp_tot">
					<tr style="height:49px;">
						<td colspan=4 style="vertical-align: top;">
							<table cellpadding="0" cellspacing="0">
								<tr style="height:27px;vertical-align:middle;">
									<td colspan=3 style="background-color:#d4d4d4;height:38px"><div id="colm_total" style="text-align:center;vertical-align:middle;">Total <?=($tipo_grupo=='paises' ? 'de países<br/>(incluye España)' : ($tipo_grupo=='provincias' ? 'de España<br/>(incluye Canarias)' : 'de Canarias')) ?></div></td>
								</tr>
								<tr style="height:23px;">
									<td style="width:53px;border-right:solid 1px #d4d4d4;background-color:#f2f2f2;border-bottom: solid 1px #e0e0e0;"><div id="total_col0_tit" style="text-align:center;">Entradas</div></td>
									<td style="width:53px;border-right:solid 1px #d4d4d4;background-color:#f2f2f2;border-bottom: solid 1px #e0e0e0;"><div id="total_col1_tit" style="text-align:center;"><?=$modo_introduccion==MODO_INTRO_ES ? 'Salidas' : 'Pernoc.'?></div></td>
									<td style="width:53px;background-color:#f2f2f2;border-bottom: solid 1px #e0e0e0;"><div id="total_col2_tit" style="text-align:center;"><?=$modo_introduccion==1 ? 'Pernoc.' : 'Salidas'?></div></td>
								</tr>
							</table>						
						</td>
					</tr>	
					<tr style="height:26px;vertical-align:middle;border: solid 1px black;">
					    <td style="width:53px;border-bottom: solid 1px #e0e0e0;background-color:#f2f2f2;"></td>
					    <td style="width:53px;border-bottom: solid 1px #e0e0e0;background-color:#f2f2f2;text-align:center;"><?=($modo_introduccion==MODO_INTRO_EP ? $totales[$tipo_grupo]['presentes_comienzo_mes'] : "") ?></td>
						<td style="width:53px;border-bottom: solid 1px #e0e0e0;background-color:#f2f2f2;text-align:center;"><?=($modo_introduccion==MODO_INTRO_ES ? $totales[$tipo_grupo]['presentes_comienzo_mes'] : "") ?></td>
					</tr>
					<?php $tot_col1 = 0; $tot_col2 = 0; $tot_col3 = 0; ?>
					<?php for($dia=1;$dia<=$dias_rellenados;$dia++):?>
					<tr class="tabla_esp_tot_fila">
					    <td class="tabla_esp_tot_celda_53"><?php echo $totales[$tipo_grupo][$dia]->entradas; $tot_col1+=$totales[$tipo_grupo][$dia]->entradas;?></td>
					    <td class="tabla_esp_tot_celda_53"><?php $valor=($modo_introduccion==MODO_INTRO_ES ? $totales[$tipo_grupo][$dia]->salidas : $totales[$tipo_grupo][$dia]->pernoctaciones); echo $valor; $tot_col2+=$valor;/*Se asume que en salidas viene el total de la 2a columna*/?></td>
						<td class="tabla_esp_tot_celda_53"><?php $valor=($modo_introduccion==MODO_INTRO_ES ? $totales[$tipo_grupo][$dia]->pernoctaciones : $totales[$tipo_grupo][$dia]->salidas); echo $valor; $tot_col3+=$valor;/*Se asume que en pernoctaciones viene el total de la 3a columna*/?></td>
					</tr>
					<?php endfor ?>
					<?php for($dia=$dias_rellenados+1;$dia<=$dias_mostrar_formulario;$dia++):?>
					<tr class="tabla_esp_tot_fila">
					    <td class="tabla_esp_tot_celda_53">-</td>
					    <td class="tabla_esp_tot_celda_53">-</td>
						<td class="tabla_esp_tot_celda_53">-</td>
					</tr>
					<?php endfor ?>					
					<tr class="tabla_esp_tot_fila_tot">
					    <td class="tabla_esp_tot_celda_53"><?=$tot_col1?></td>
					    <td class="tabla_esp_tot_celda_53"><?=$tot_col2?></td>
						<td class="tabla_esp_tot_celda_53"><?=$tot_col3?></td>
					</tr>
                    <tr class="tabla_esp_tot_fila_tot">
						<td style="width:53px;border-bottom: solid 1px #e0e0e0;text-align:center;background-color:#f2f2f2;" colspan=4>
							<?php
								//Se calcula la estancia media total
								$est_med=0;
								if($tot_col1+$totales[$tipo_grupo]['presentes_comienzo_mes']!=0)
									$est_med = ($modo_introduccion==MODO_INTRO_ES ? $tot_col3:$tot_col2) / ($tot_col1+$totales[$tipo_grupo]['presentes_comienzo_mes']);
								echo str_replace('.',',',round($est_med*100)/100);
							?>							
						</td>
					</tr>
				</table>
			</div>
			<?php endif ?>
			<p>			
			<?php $items_fila=0; ?>
			
		<?php endforeach ?>
		</div>

		
		
		<div id="habitaciones" style="clear:both; padding-left: 8px;padding-right: 8px;">
		<?php imprimir_cabecera_pagina($cuestionario, $datos_estab, $ent_sal_pern, $num_pagina); $num_pagina++; ?>
			<div id="cab_habitac">
				<table class="tabla_hab">
					<tr style="height:28px;background-color:#caddf7;vertical-align:middle;">
						<td colspan="2" style="background-color:#ffffff;"></td>
						<td colspan="5" style="padding-left:6px;">
							<?= $datos_estab->id_tipo_establecimiento==3 ? "Apartamentos ocupados" : "Habitaciones ocupadas" ?>
						</td>
					</tr>	
					<tr style="height:25px;vertical-align:middle;border-bottom: solid 1px #e0e0e0;background-color:#f2f2f2;">
							<td colspan="2" style="background-color:#ffffff;width:137px;"></td>
							<td style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-left: solid 1px #caddf7;">Plazas supletorias u otras</td>
						    <td style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-left: solid 1px #caddf7;border-right: solid 1px #caddf7;"><?= $datos_estab->id_tipo_establecimiento==3 ? "4/6 pax" : "Dobles (uso doble)" ?></td>
						    <td style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-right: solid 1px #caddf7;"><?= $datos_estab->id_tipo_establecimiento==3 ? "Estudio, 2/4 pax" : "Dobles (uso individual)" ?></td>
						    <td style="width:161px; text-align: center; border-bottom: solid 1px #f0f0f0;border-right: solid 1px #caddf7;"><?= $datos_estab->id_tipo_establecimiento==3 ? "Otros apartamentos" : "Otras habitaciones" ?></td>
					    	<td style="width:166px; text-align: center; border-bottom: solid 1px #f0f0f0;background-color:#d4d4d4;">Total</td>
					</tr>									
					<?php
						$tot_suplet=0; $tot_doble=0; $tot_indiv=0; $tot_otras=0; 
						for($dia=1;$dia<=$num_dias_mes;$dia++):
					?>						
					    <tr class="tabla_hab_fila">
						    <td class="tabla_hab_celda_mes"<?= $dia==1 ? ' style="border-top: solid 1px #e0e0e0;"' : '' ?>><?= $dia==1 ? DateHelper::mes_tostring( $cuestionario->mes,'M') : '' ?></td>
						    <td class="tabla_hab_celda_dia"<?= $dia==1 ? ' style="border-top: solid 1px #e0e0e0;"' : '' ?>><div id="dia<?= $dia ?>" style="text-align:center;"><?= sprintf('%02d', $dia); ?></div></td>
						    <td class="tabla_hab_celda_162"><?=(isset($habitac[$dia]) ? $habitac[$dia]->supletorias : "-")?></td>
						    <td class="tabla_hab_celda_162"><?=(isset($habitac[$dia]) ? $habitac[$dia]->uso_doble : "-")?></td>
						    <td class="tabla_hab_celda_162"><?=(isset($habitac[$dia]) ? $habitac[$dia]->uso_individual : "-")?></td>
					    	<td class="tabla_hab_celda_162"><?=(isset($habitac[$dia]) ? $habitac[$dia]->otras : "-")?></td>
					    	<td class="tabla_hab_celda_101"><?=(isset($habitac[$dia]) ? $habitac[$dia]->uso_doble+$habitac[$dia]->uso_individual+$habitac[$dia]->otras : "-")?></td>
							<?php 
								if(isset($habitac[$dia]))
								{
									$tot_suplet+=$habitac[$dia]->supletorias;
									$tot_doble+=$habitac[$dia]->uso_doble;
									$tot_indiv+=$habitac[$dia]->uso_individual;
									$tot_otras+=$habitac[$dia]->otras;
								}
							?>
						</tr>			
					<?php endfor ?>
					<tr class="tabla_hab_fila_tot">
							<td colspan="2" class="tabla_hab_fila_tot_celda_137">Totales</td>
							<td class="tabla_hab_fila_tot_celda_162"><?=$tot_suplet ?></td>
						    <td class="tabla_hab_fila_tot_celda_162"><?=$tot_doble ?></td>
						    <td class="tabla_hab_fila_tot_celda_162"><?=$tot_indiv ?></td>
						    <td class="tabla_hab_fila_tot_celda_162"><?=$tot_otras ?></td>
					    	<td class="tabla_hab_fila_tot_celda_101"><?=$tot_doble+$tot_indiv+$tot_otras ?></td>
					</tr>									
				</table>
			</div>			
		</div>
		<div id="pers_precios" style="padding-left: 8px;padding-right: 8px;">  
		<?php imprimir_cabecera_pagina($cuestionario, $datos_estab, $ent_sal_pern, $num_pagina); $num_pagina++; ?>    
			<div style="width:45%;float:left;">
                <span class="titulo_3">Personal</span>
                <div class="subrayado" style="width:100%"> </div>
                Indique el número total de personal ocupado durante el mes de referencia. No incluya personal de vacaciones ni de baja temporal.
                <table class="tabla_personal">
                    <tr>
                        <td>No remunerado:</td>
                        <td><div class="tabla_personal_celda">&nbsp;<?=$pers_prec[0]->no_remunerado?></div></td>
                    </tr>                    
                    <tr>
                        <td>Fijo:</td>
                        <td><div class="tabla_personal_celda">&nbsp;<?=$pers_prec[0]->remunerado_fijo?></div></td>
                    </tr>                    
                    <tr>
                        <td>Eventual:</td>
                        <td><div class="tabla_personal_celda">&nbsp;<?=$pers_prec[0]->remunerado_eventual?></div></td>
                    </tr>
                </table>
            </div>
            <div style="width:53%;float:left;padding-left:15px;">
                <span class="titulo_3">Precios</span>
                <div class="subrayado" style="width:100%"> </div>
                <table style="width:95%;margin-left:12px;margin-top:-5px;">
                    <tr>
                        <td>Ingreso por <?= $datos_estab->id_tipo_establecimiento==3 ? "apartamento" : "habitación" ?> disponible mensual:</td>
                        <td class="precio_ingreso_mensual"><div style="border:1px solid #E3ECF7;text-align:right;width:55px;display:inline-block;margin-right:3px;padding-right:3px;">&nbsp;<?=str_replace('.',',',$pers_prec[1]->revpar_mensual)?></div>€</td>
                    </tr>                    
                    <tr>
                        <td>Indicar la tarifa media por <?= $datos_estab->id_tipo_establecimiento==3 ? "apartamento" : "habitación" ?>:</td>
                        <td class="precio_tarifa_media"><div style="border:1px solid #E3ECF7;text-align:right;width:55px;display:inline-block;margin-right:3px;padding-right:3px;">&nbsp;<?=str_replace('.',',',$pers_prec[1]->adr_mensual)?></div>€</td>
                    </tr>
                    <tr>
                        <td>ADR mensual por tipo de cliente:</td><td class="precio_ADR_mensual"><div style="text-align: right;" id="modoIntroduccionADR"></div></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="tabla_precios">
                                <tr>
                                    <td>
                                    </td>
                                    <td style="width:60px;text-align:center;background-color:#f2f2f2;border-right: solid 1px #caddf7;">
                                        ADR(€)
                                    </td>
                                    <td style="width:90px;text-align:right;background-color:#f2f2f2;border-right: solid 1px #caddf7;padding-right:8px;">
                                    	<?= $datos_estab->id_tipo_establecimiento==3 ? "Nº apartamentos ocupados" : "Nº habitaciones ocupadas"; ?>
                                    </td>
                                    <td style="width:90px;text-align:right;background-color:#f2f2f2;padding-right:8px;">
                                    	<?= $datos_estab->id_tipo_establecimiento==3 ? "% apartamentos ocupados" : "% habitaciones ocupadas"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color:#f2f2f2;padding-left:8px;border-top:solid 1px #e0e0e0;">
                                        Turoperador tradicional
                                    </td>
                                    <td class="tabla_precios_celda_ADR">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:45px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->adr[TO_TRADICIONAL]) ? str_replace('.',',',$pers_prec[1]->adr[TO_TRADICIONAL]) : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_num_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->num[TO_TRADICIONAL]) ? $pers_prec[1]->num[TO_TRADICIONAL] : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_perc_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->pct[TO_TRADICIONAL]) ? str_replace('.',',',$pers_prec[1]->pct[TO_TRADICIONAL]) : '' ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color:#f2f2f2;padding-left:8px;border-top:solid 1px #e0e0e0;">
                                        Empresas
                                    </td>
                                    <td class="tabla_precios_celda_ADR">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:45px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->adr[EMPRESAS]) ? str_replace('.',',',$pers_prec[1]->adr[EMPRESAS]) : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_num_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->num[EMPRESAS]) ? $pers_prec[1]->num[EMPRESAS] : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_perc_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->pct[EMPRESAS]) ? str_replace('.',',',$pers_prec[1]->pct[EMPRESAS]) : '' ?></div>
                                    </td>
                                </tr>                                
                                <tr>
                                    <td style="background-color:#f2f2f2;padding-left:8px;border-top:solid 1px #e0e0e0;">
                                        Agencia de viaje tradicional
                                    </td>
                                    <td class="tabla_precios_celda_ADR">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:45px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->adr[AGENCIA_TRADICIONAL]) ? str_replace('.',',',$pers_prec[1]->adr[AGENCIA_TRADICIONAL]) : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_num_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->num[AGENCIA_TRADICIONAL]) ? $pers_prec[1]->num[AGENCIA_TRADICIONAL] : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_perc_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->pct[AGENCIA_TRADICIONAL]) ? str_replace('.',',',$pers_prec[1]->pct[AGENCIA_TRADICIONAL]) : '' ?></div>
                                    </td>
                                </tr>                                 
                                <tr>
                                    <td style="background-color:#f2f2f2;padding-left:8px;border-top:solid 1px #e0e0e0;">
                                        Particulares
                                    </td>
                                    <td class="tabla_precios_celda_ADR">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:45px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->adr[PARTICULARES]) ? str_replace('.',',',$pers_prec[1]->adr[PARTICULARES]) : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_num_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->num[PARTICULARES]) ? $pers_prec[1]->num[PARTICULARES] : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_perc_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->pct[PARTICULARES]) ? str_replace('.',',',$pers_prec[1]->pct[PARTICULARES]) : '' ?></div>
                                    </td>
                                </tr>                                 
                                <tr>
                                    <td style="background-color:#f2f2f2;padding-left:8px;border-top:solid 1px #e0e0e0;">
                                        Grupos
                                    </td>
                                    <td class="tabla_precios_celda_ADR">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:45px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->adr[GRUPOS]) ? str_replace('.',',',$pers_prec[1]->adr[GRUPOS]) : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_num_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->num[GRUPOS]) ? $pers_prec[1]->num[GRUPOS] : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_perc_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->pct[GRUPOS]) ? str_replace('.',',',$pers_prec[1]->pct[GRUPOS]) : '' ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color:#f2f2f2;padding-left:8px;border-top:solid 1px #e0e0e0;">
                                        Contratación directa del hotel online
                                    </td>
                                    <td class="tabla_precios_celda_ADR">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:45px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->adr[INTERNET]) ? str_replace('.',',',$pers_prec[1]->adr[INTERNET]) : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_num_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->num[INTERNET]) ? $pers_prec[1]->num[INTERNET] : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_perc_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->pct[INTERNET]) ? str_replace('.',',',$pers_prec[1]->pct[INTERNET]) : '' ?></div>
                                    </td>
                                </tr>  
                                <tr>
                                    <td style="background-color:#f2f2f2;padding-left:8px;border-top:solid 1px #e0e0e0;">
                                        Agencias de viaje on-line
                                    </td>
                                    <td class="tabla_precios_celda_ADR">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:45px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->adr[AGENCIA_ONLINE]) ? str_replace('.',',',$pers_prec[1]->adr[AGENCIA_ONLINE]) : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_num_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->num[AGENCIA_ONLINE]) ? $pers_prec[1]->num[AGENCIA_ONLINE] : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_perc_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->pct[AGENCIA_ONLINE]) ? str_replace('.',',',$pers_prec[1]->pct[AGENCIA_ONLINE]) : '' ?></div>
                                    </td>
                                </tr>                                 
                                <tr>
                                    <td style="background-color:#f2f2f2;padding-left:8px;border-top:solid 1px #e0e0e0;">
                                        Turoperador on-line
                                    </td>
                                    <td class="tabla_precios_celda_ADR">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:45px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->adr[TO_ONLINE]) ? str_replace('.',',',$pers_prec[1]->adr[TO_ONLINE]) : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_num_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->num[TO_ONLINE]) ? $pers_prec[1]->num[TO_ONLINE] : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_perc_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->pct[TO_ONLINE]) ? str_replace('.',',',$pers_prec[1]->pct[TO_ONLINE]) : '' ?></div>
                                    </td>
                                </tr>                                 
                                <tr>
                                    <td style="background-color:#f2f2f2;padding-left:8px;border-top:solid 1px #e0e0e0;border-bottom:solid 1px #e0e0e0;">
                                        Otros
                                    </td>
                                    <td class="tabla_precios_celda_ADR">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:45px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->adr[OTROS]) ? str_replace('.',',',$pers_prec[1]->adr[OTROS]) : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_num_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->num[OTROS]) ? $pers_prec[1]->num[OTROS] : '' ?></div>
                                    </td>
                                    <td class="tabla_precios_celda_perc_hab">
                                        <div style="border:1px solid #E3ECF7;text-align:right;width:35px;display:inline-block;padding-right:3px;">&nbsp;<?=isset($pers_prec[1]->pct[OTROS]) ? str_replace('.',',',$pers_prec[1]->pct[OTROS]) : '' ?></div>
                                    </td>
                                </tr>                                
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
			<div style="clear: both;"> </div>				            
		</div>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->