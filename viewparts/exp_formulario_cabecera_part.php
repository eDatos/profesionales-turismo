<div class="center">
<table class="cabtabla">
	<tr>
		<td>		
			<table width="100%" height="100%" cellspacing="1" cellpadding="1" align="center">
				<tr align="center"><td colspan='4' align='right'><img src="images/secreto_estadistico.png"></td></tr>
				<tr align="center"><td colspan='4' align='left'>
						<hr style="background-color:black;"/><b><font face='Century Gothic' size="6">ENCUESTA DE EXPECTATIVAS DE LA ACTIVIDAD HOTELERA</font></b>
				</td></tr>
				<tr align="center"><td colspan='4' align='left'>
						<hr style="background-color:black;"/><b><font face='Century Gothic'>DATOS DE IDENTIFICACIÓN</font></b>
				</td></tr>
				<tr align="center">
					<td colspan='4' align='left'>
						<hr style="background-color:black;"/>
						<b>Datos del establecimiento</b>
					</td>
				</tr>		
				<tr align="center">
					<td colspan='2' align='left'>
						<input type='text' name='pregunta_nombre_establecimiento' class='istac' size='65' maxlength='65' value='<?= $establecimiento->nombre_establecimiento ?>' readonly="readonly">
					</td>
					<td colspan='2' align='left'>
						<input type='text' name='pregunta_signatura' class='istac' size='5' maxlength='5' value='<?= $establecimiento->id_establecimiento ?>' readonly="readonly">
					</td>
				</tr>	
				<tr align="center">
					<td colspan='2' align='left'>Nombre del establecimiento</td><td colspan='2' align='left'>Signatura</td>
				</tr>						
				<tr align="center"><td colspan='4' align='left'>&nbsp;</td></tr>				
				<tr align="center">
					<td colspan='1' align='left'><input type='text' name='pregunta_municipio' class='istac' size='50' maxlength='50' value='<?= $establecimiento->municipio ?>' readonly="readonly"></td>
					<td colspan='1' align='left'><input type='text' name='pregunta_num_habitaciones' class='istac' size='5' maxlength='5' value='<?= $establecimiento->num_habitaciones ?>' readonly="readonly"></td>
					<td colspan='1' align='left'><input type='text' name='pregunta_plazas' class='istac' size='5' maxlength='5' value='<?= $establecimiento->num_plazas ?>' readonly="readonly"></td>
					<td colspan='1' align='left'><input type='text' name='pregunta_categoria' class='istac' size='20' maxlength='50' value='<?= $establecimiento->grupo_categoria ?>' readonly="readonly"></td>
				</tr>	
				<tr align="center">
					<td colspan='1' align='left'>Municipio</td>
					<td colspan='1' align='left'>Nº habitaciones</td>
					<td colspan='1' align='left'>Plazas</td>
					<td colspan='1' align='left'>Categoría (*)</td>
				</tr>
				<tr align="center">
					<td colspan='4' align='left'><hr style="background-color:black;"/><b>Persona que contesta a esta encuesta:</b></td>
				</tr>		
				<tr align="center">
					<td colspan='4' align='left'>
					<?php
                        if (!$encuesta->es_nuevo)
						{
							if ($encuesta->es_director)
							{
								$value_director = ES_DIRECTOR;
								$value_cargo = "";
								$checked_director = "checked='checked'";
								$checked_otra = "";
							}
							else 
							{
								$value_director = NO_ES_DIRECTOR;
								$value_cargo = $encuesta->otra_persona_cargo;
								$checked_director = "";
								$checked_otra = "checked='checked'";
							}
						}
						else 
						{
							$value_director = "";
							$value_cargo = "";
							$checked_director = "";
							$checked_otra = "";
						}
					?>
						<font id='preg0'>[0.-]</font>	
						<?php if ($page->have_any_perm(PERM_GRABADOR)): ?>
						 <font id='preg0director'>Director</font>
						 <input type="text" name="pregunta00"  size='1' maxlength='1' tabindex='1'  value="<?= $value_director; ?>">
						 <font id='preg0otro'>Otro cargo</font>
						 <input type='text' name='pregunta00_texto' size='20' maxlength='20' tabindex='1' value='<?= $value_cargo; ?>'>		
						<?php else: ?>						
						 <input type="radio" name="pregunta00" value="Director" <?= $checked_director; ?>>
						 <font id='preg0director'>Director</font>
						 <input type="radio" name="pregunta00" value="Otra persona" <?= $checked_otra; ?>>
						 <font id='preg0otro'>Otro cargo</font>
						 <input type='text' name='pregunta00_texto' class='istac' size='20' maxlength='20' tabindex='0' value='<?= $value_cargo; ?>'>
						<?php endif; ?>
					</td>
				</tr>				
				<tr align="center">
					<td colspan='4' align='left'>&nbsp;</td>
				</tr>				
				<tr align="center">
					<td colspan='1' align='left'>
						<?php
							$emails = array();
							if ($establecimiento->email != null)
								$emails[] = $establecimiento->email;
							if ($establecimiento->email2 != null)
								$emails[] = $establecimiento->email2;
							
							$tlfs = array();
							if ($establecimiento->telefono != null)
								$tlfs[] = $establecimiento->telefono;
							if ($establecimiento->telefono2 != null)
								$tlfs[] = $establecimiento->telefono2;							
						?>
						<input type='text' name='pregunta_email' class='istac' size='40' maxlength='40' value='<?= implode(" - ", $emails); ?>' readonly="readonly">
					</td>
					<td colspan='3' align='left'>
						<input type='text' name='pregunta_telefono' class='istac' size='40' maxlength='40' value='<?= implode(" - ", $tlfs); ?>' readonly="readonly">
					</td>
				</tr>	
				<tr align="center">
					<td colspan='1' align='left'>Correo electrónico</td>
					<td colspan='3' align='left'>Teléfono</td>
				</tr>				
			</table>
			
			<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="1" align="center">
				<tr align="center">
					<td colspan='2' align='left' width='50%'>
						<hr style="background-color:black;"/><b>Periodo de referencia:</b>
					</td>
					<td colspan='1' align='left' width='50%'>
						<hr style="background-color:black;"/><b>Fecha de cumplimentación:</b>
					</td>
				</tr>		
				<tr align="center">
					<td colspan='1' align='left'><input type='text' name='pregunta_trimestre' class='istac' size='1' maxlength='1' value='<?= $encuesta->trimestre ?>' readonly="readonly"></td>
					<td colspan='1' align='left'><input type='text' name='pregunta_anyo' class='istac' size='5' maxlength='4' value='<?= $encuesta->anyo ?>' readonly="readonly"></td>
					<td colspan='1' rowspan='2' align='left'>
						<table>
							<tr>
							<?php if ($page->have_any_perm(PERM_GRABADOR)) : ?>
								<td align='left'>
								<input type='text' name='pregunta_fecha_dia' class='istac' size='5' maxlength='5' tabindex='1' value='<?= $encuesta->fecha_grabacion->format('d') ?>'> -
								</td>
								<td align='left'>
								<input type='text' name='pregunta_fecha_mes' class='istac' size='5' maxlength='5' tabindex='1' value='<?= $encuesta->fecha_grabacion->format('m') ?>'> -
								</td>
								<td align='left'>
								<input type='text' name='pregunta_fecha_agno' class='istac' size='5' maxlength='5' tabindex='1' value='<?= $encuesta->fecha_grabacion->format('Y') ?>'>
								</td>
							<?php else :?>
								<td align='left'>
								<input type='text' name='pregunta_fecha_dia' class='istac' size='5' maxlength='5' tabindex='0' value='<?= $encuesta->fecha_grabacion->format('d') ?>' readonly="readonly"> -
								</td>
								<td align='left'>
								<input type='text' name='pregunta_fecha_mes' class='istac' size='5' maxlength='5' tabindex='0' value='<?= $encuesta->fecha_grabacion->format('m') ?>' readonly="readonly"> -
								</td>
								<td align='left'>
								<input type='text' name='pregunta_fecha_agno' class='istac' size='5' maxlength='5' tabindex='0' value='<?= $encuesta->fecha_grabacion->format('Y') ?>' readonly="readonly">
								</td>
							<?php endif; ?>
							</tr>
							<tr>
								<td align='left' id='pregdia'>Día</td><td align='left' id='pregmes'>Mes</td><td align='left' id='preganyo'>Año</td>
							</tr>
						</table>					
					</td>
				</tr>	
				<tr align="center">
					<td colspan='1' align='left'>Trimestre</td>
					<td colspan='1' align='left'>Año</td>
				</tr>			
				<tr align="center">
					<td colspan='4' align='left'><hr style="background-color:black;"/></td>
				</tr>					
			</table>
			
			<table class="noprint" width="100%" height="100%" cellspacing="1" cellpadding="1" align="center">
				<tr>
				<?php if ($page->have_any_perm(PERM_USER)): ?>
					<td colspan='2'  class="color_fondo_claro istac" align="center" valign="middle" height='30'><a href='<?= $modDatosUrl ?>' title='Haciendo click en este enlace irá a modicar los datos de su establecimiento'>IR A MODIFICAR LOS DATOS DE SU ESTABLECIMIENTO</a></td>
				<?php else: ?>
					<td colspan='2'  class="color_fondo_claro istac" align="center" valign="middle" height='30'></td>
				<?php endif; ?>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<table width="100%" class="noprint">
	<tr>
		<td align="right">
		<!-- onclick="JavaScript:window.open('content/confidencialidad.html','popup','width=600,height=400,resizable=yes,scrollbars=yes'); return false"  -->
		<a href="<?= CONTENT_NOTA_CONFIDENCIAL ?>" target="_blank" class="istacnotas" title="Nota sobre confidencialidad de los datos.">NOTA SOBRE CONFIDENCIALIDAD</a>
		</td>
	</tr>
</table>
<hr style="background-color:black;align:center;"/>
