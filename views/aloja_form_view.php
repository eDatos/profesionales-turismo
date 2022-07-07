<link rel="stylesheet" href="css/pwet-theme/jquery-ui-1.9.1.custom.css"/>
<link rel="stylesheet" href="css/aloja_form_view.css"/>
<script type="text/javascript" src="js/jquery-ui-1.9.1.custom.js"></script>
<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/inputfields.js"></script>
<script type="text/javascript" src="js/aloja_form_view.js"></script>
<script type="text/javascript">
	diasMostrarFormulario = <?= $dias_mostrar_formulario ?>;
	numDiasMes = <?= $num_dias_mes ?>;
	numPlazas = <?= $num_plazas ?>;
	numPlazasSupletorias = <?= $num_plazas_supletorias ?>;
	numHabitaciones = <?= $num_habitaciones ?>;
	esHotel = <?= $tipo_establecimiento == 3 ? 'false' : 'true' ?>;
	
	numUTSeleccionados = <?= count($selected_UT) ?>;
	maxUTMostrarPagina = <?= MAX_UT_MOSTRAR ?>;
	numPaginas = Math.ceil(numUTSeleccionados / maxUTMostrarPagina);
	pagActual = 1;
	
	modoIntroduccion = '<?= (isset($cuestionario->modo_introduccion) && $cuestionario->modo_introduccion == '0') ? "EP" : "ES"?>';
	modoCumplimentado = '<?= $cuestionario->modo_cumplimentado ?>';
	modoIntroduccionADR = '<?= (isset($cuestionario->modo_porcentaje) && $cuestionario->modo_porcentaje == MODO_PORC_NUM) ? "N" : "P"?>';
	
	habitaciones = jQuery.parseJSON('<?= $json_habitaciones ?>');
	personalPrecios = jQuery.parseJSON('<?= $json_pers_prec ?>');

	soloLectura = <?= $solo_lectura ? "true" : "false" ?>;
	navPageURL="<?= $navpage_url; ?>";
	printFormURL="<?=$urlPrintForm;?>";

	mes_encuesta = <?= $mes_encuesta ?>;
	ano_encuesta = <?= $ano_encuesta ?>;
	mes_encuesta_finalizado = <?= $mes_encuesta_finalizado ? "true":"false" ?>;
	nombre_establecimiento = "<?= $nombre_establecimiento ?>";
	
	es_admin = <?= $es_admin ? "true":"false" ?>;

	tab_inicial = <?= $tab ?>;

	intervaloGrabacionesIntermedias= <?= ALOJA_BCK_INTERVAL ?>;
	fShowBackup= ((<?= ALOJA_BCK_FLAGS ?> & 1)!=0) ? true : false;		
	fShowOcupado= ((<?= ALOJA_BCK_FLAGS ?> & 2)!=0) ? true : false;

	var excesoPlazasPorciento = <?= (defined('ALOJA_EXCESO_PLAZAS') && ALOJA_EXCESO_PLAZAS) ? ALOJA_EXCESO_PLAZAS : 0?>;
	var excesoHabitacionesPorciento = <?= (defined('ALOJA_EXCESO_HABIT') && ALOJA_EXCESO_HABIT) ? ALOJA_EXCESO_HABIT : 0?>;
	
	excesoPlazas = <?= (defined('ALOJA_EXCESO_PLAZAS') && ALOJA_EXCESO_PLAZAS) ? "Math.ceil((numPlazas * ".ALOJA_EXCESO_PLAZAS.")/100.0)" : 0?>;
	excesoHabitaciones = <?= (defined('ALOJA_EXCESO_HABIT') && ALOJA_EXCESO_HABIT) ? "Math.ceil((numHabitaciones * ".ALOJA_EXCESO_HABIT.")/100.0)" : 0?>;

	motivosExcesoInfo=[];
	<?php
		if ($motivosExcesos->has_rows())
		{
			foreach ($motivosExcesos as $row)
			{
				echo "motivosExcesoInfo.push({'id':'".$row['idmot']."', 'desc':'".$row['descmot']."', 'oblig':'".$row['oblig']."', 'ayuda':'".$row['ayuda']."'});\n";
			}
		}
	?>

	<?php
	if(isset($codMotivoExcesoPlazas) && ($codMotivoExcesoPlazas!=99))
	{
		echo "codMotivoExcesoPlazas=\"".$codMotivoExcesoPlazas."\";\n";
		if(isset($detalleMotivoExcesoPlazas))
			echo "detalleMotivoExcesoPlazas=".json_encode(iconv("CP1252", "UTF-8", $detalleMotivoExcesoPlazas)).";\n";
	}
	if(isset($codMotivoExcesoHabitaciones) && ($codMotivoExcesoHabitaciones!=99))
	{
		echo "codMotivoExcesoHabitaciones=\"".$codMotivoExcesoHabitaciones."\";\n";
		if(isset($detalleMotivoExcesoHabitaciones))
			echo "detalleMotivoExcesoHabitaciones=".json_encode(iconv("CP1252", "UTF-8", $detalleMotivoExcesoHabitaciones)).";\n";
	}
	?>
	
	<?php
		if ($limiteInvitaciones->has_rows())
		{
			$separador="";
			echo "tipo_cliente_perct={";
			foreach ($limiteInvitaciones as $row)
			{
				echo $separador."'".$row['idcliente']."': ".$row['perct'];
				$separador=",";
			}
			echo "};\n";
		}
	?>

</script>  	
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<?php
		if($es_admin)
		{
			switch($cuestionario->tipo_carga)
			{
				case TIPO_CARGA_XML:
					echo '<div class="tipo_carga"><img class="tipo_carga_xml" title="Xml" src="images/xml.png"></div>';
					break;
				case TIPO_CARGA_WEB:
					echo '<div class="tipo_carga"><img class="tipo_carga_xml" title="Web" src="images/web.png"></div>';
					break;
				case TIPO_CARGA_FAX:
					echo '<div class="tipo_carga"><img class="tipo_carga_xml" title="Fax" src="images/fax.png"></div>';
					break;
			}			
		}
	?>
	<form id="ef" method="POST" action="<?= $site[PAGE_ALOJA_FORM_ENVIO]; ?>">
		<input type="hidden" name="<?= ARG_MES ?>" value="<?= $cuestionario->mes ?>">
		<input type="hidden" name="<?= ARG_ANO ?>" value="<?= $cuestionario->ano ?>">
	</form>
	<h2 class="titulo_2">Módulo de Alojamiento: <?= DateHelper::mes_tostring( $cuestionario->mes,'M') ?> de <?= $cuestionario->ano ?></h2>
    <div style="margin-bottom: 8px;<?=$solo_lectura?'display:none;':''?>">Pulse la tecla tabulador o intro<span id="ayuda_tc" onclick='MostrarAyuda("ayuda_tc","AYUDA11<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' class="ayudaicon2" title="Ayuda (tecla de acceso: k)" accesskey="k">&nbsp;</span>para moverse. Método de cumplimentado<span id="ayuda_mc" onclick='MostrarAyuda("ayuda_mc","AYUDA12<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' class="ayudaicon2" title="Ayuda (tecla de acceso: j)" accesskey="j">&nbsp;</span>: <span id="modoCumplimentado"></span></div>
    <div class="cuadro fondo_rojo_claro" style="font-size:95%;padding:2px 2px 2px 4px;">
    	<!-- <h3 class="titulo_3" style="display:inline;font-size:95%;">Importante:</h3> -->
		<b>ATENCIÓN: Grabe con frecuencia para evitar perder el trabajo.</b> Por motivos de seguridad los servidores cierran las sesiones que parecen inactivas.<br>
    	Para minimizar los errores durante la grabación, es conveniente que grabe el cuestionario comenzando por los datos de alojamiento, después <?= $tipo_establecimiento==3 ? "apartamentos" : "habitaciones" ?> y finalmente personal y precios.
	</div>
    <div></div>
    <form id="af" method="POST">
    <div id="tabs">
		<ul>
			<li onClick="if(!$('#pn_0')[0].disabled) $('#pn_0').focus().select();"><a href="#ent_sal_pern"><img src="images/libro_abierto.png" class="icono_tab"/> Entradas, salidas y pernoctaciones<span id="ayuda_t1" onclick='MostrarAyuda("ayuda_t1","AYUDA14<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' class="ayudaicon2" title="Ayuda (tecla de acceso: e)" accesskey="e">&nbsp;</span></a></li>
			<li onClick="if(!$('#hab_suplet_dia1')[0].disabled) $('#hab_suplet_1').focus().select();"><a href="#habitaciones"><img src="images/colgador_puerta.png" class="icono_tab"/> <?= $tipo_establecimiento==3 ? "Apartamentos ocupados" : "Habitaciones ocupadas" ?><span id="ayuda_t2" onclick='MostrarAyuda("ayuda_t2","AYUDA15<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' class="ayudaicon2" title="Ayuda (tecla de acceso: h)" accesskey="h">&nbsp;</span></a></li>
			<li onClick="if(!$('#pers_no_remunerado')[0].disabled) $('#pers_no_remunerado').focus().select();"><a href="#personal_precios"><img src="images/personas.png" class="icono_tab"/> Personal y precios<span id="ayuda_t3" onclick='MostrarAyuda("ayuda_t3","AYUDA16<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' class="ayudaicon2" title="Ayuda (tecla de acceso: p)" accesskey="p">&nbsp;</span></a></li>
		</ul>
        <div class="div_calendario">
			<span class="avisobackup-label avisobackup-initiated avisobackup-hide">Guardando...</span>
			<span class="avisobackup-label avisobackup-ok avisobackup-hide">Guardado</span>
			<span class="avisobackup-label avisobackup-error avisobackup-hide">Fallo</span>
			<span class="avisobackup-label avisobackup-info avisobackup-hide">Fallo</span>
			<span id="indicadorbusy" style="display:none" title="Grabación automática deshabilitada"></span>
            <img src="images/calendario.png" class="icono_tab"/>&nbsp;&nbsp;<strong>Días abierto en <?= DateHelper::mes_tostring( $cuestionario->mes,'m') ?> de <?= $cuestionario->ano ?>:</strong>&nbsp;
            <input type="text" class="numero sindecimales digits" style="width:30px;" name="dias_abierto" id="dias_abierto" maxlength="2" value="<?= @$cuestionario->dias_abierto ?>"/>
        </div>
		<div id="ent_sal_pern" style="padding:8px;">
            <div>
                <div class="texto_nacionalidades"><?=count($selected_UT); ?> nacionalidades y provincias seleccionadas</div>
                <div style="float: right;margin-bottom: 8px;<?=$solo_lectura?'display:none;':''?>">Modo de introducción<span id="ayuda_mi" onclick='MostrarAyuda("ayuda_mi","AYUDA13<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' class="ayudaicon2" title="Ayuda (tecla de acceso: m)" accesskey="m">&nbsp;</span>: <span id="modoIntroduccion"></span></div>
            </div>
            <div id="cab_datos" style="display: inline-flex;">
				<div id="cab_mes_dia" style="clear:both;float: left;">
					<table cellpadding="0" cellspacing="0" class="fondo_fixed">
						<tr class="filablank_cab_fixed_EPS">
							<td></td>
							<td class="celda_fixed_pagant_EPS"><img id="btn_ant" class="cursor_hand" src="images/pag_anterior.png"/></td>
						</tr>	
						<tr class="filadatos_cab_fixed_EPS">
						    <?php list($mes_anterior,$ano_anterior) = DateHelper::mes_anterior($cuestionario->mes, $cuestionario->ano); ?>
							<td class="celdames_cab_fixed_EPS" id="mes"><?= DateHelper::mes_tostring($mes_anterior,'M') ?></td>
							<td class="celdadia_cab_fixed_EPS" id="dia0"><?= $ult_mes_ant->format('d') ?></td>
						</tr>
					</table>
				</div>
				<?php for($i=0;$i<4;$i++): ?>		
				<div id="cab_ut<?= $i ?>" class="cab_ut">
					<table cellpadding="0" cellspacing="0">
						<tr class="fila_tit_cab_EPS">
							<td colspan=3><?php if(!$solo_lectura):?><img id="btn_clean_pais<?= $i ?>" src='images/cross.png' class="cursor_hand" style="float:left;margin-top:2px;display:none;"><?php endif?><div id="ut<?= $i ?>_tit" class="titulo_cab_EPS" style="float:left;">-</div><img id="imgerror_ut<?= $i ?>" src='images/error2.png' class="cursor_hand" style="float:left;margin-top:2px;margin-left:-18px;z-index:10;display:none;"><div style="clear: both;"> </div></td>
						</tr>				
						<tr class="fila_etq_cab_EPS">
							<td class="etq_cab_EPS" id="ut<?= $i ?>_col0_tit">-</td>
							<td class="etq_cab_EPS" id="ut<?= $i ?>_col1_tit">-</td>
							<td class="etq_cab_EPS deshabilitado no_borde_derecho" id="ut<?= $i ?>_col2_tit">-</td>
						</tr>
						<tr class="fila_pern_cab_EPS">
							<td class="celda_pern_EPS"></td>
							<td class="celda_pern_EPS" id="pn_col2_<?= $i ?>"></td>
							<td class="celda_pern_EPS deshabilitado" id="pn_col3_<?= $i ?>"><input type="text" class="numero sindecimales digits" style="width:35px;" name="pn_<?= $i ?>" id="pn_<?= $i ?>"/></td>
						</tr>
					</table>
				</div>
				<?php endfor ?>
				<div id="cab_col_totales" style="float: left;" cellpadding="0" cellspacing="0">
					<table cellpadding="0" cellspacing="0">
						<tr class="fila_tot_fixed_EPS">
							<td class="cab_tot_EPS"><img id="btn_sig" class="cursor_hand" src="images/pag_siguiente.png"/></td>
							<td colspan=3 style="vertical-align: top;">
								<table cellpadding="0" cellspacing="0">
									<tr class="fila_cab_tot_EPS">
										<td colspan=3 id="colm_total">Total</div></td>
									</tr>
									<tr class="fila_etq_cab_tot_EPS">
										<td class="cab_tot cab_tot_borde" style="width:55px;" id="total_col0_tit">-</td>
										<td class="cab_tot cab_tot_borde" style="width:55px;" id="total_col1_tit">-</td>
										<td class="cab_tot" style="width:54px;" id="total_col2_tit">-</td>
									</tr>
								</table>						
							</td>
						</tr>	
						<tr class="fila_cab_tot_pn">
							<td class="etq_tot_pn_EPS"></td>
						    <td class="etq_tot_pn_EPS" style="width:55px;"></td>
						    <td class="etq_tot_pn_EPS" style="width:55px;" id="total_pn_col1">-</td>
							<td class="etq_tot_pn_EPS" style="width:54px;" id="total_pn_col2"></td>
						</tr>
					</table>
				</div>
				<div style="clear: both;"> </div>
			</div>
			<div id="scroll_uts" class="contenido_scroll" style="width: 983px;">
				<div id="datos_mes_dia" style="clear:both;float: left;">
					<table cellpadding="0" cellspacing="0" class="fondo_fixed">			
						<tr class="fila_fixed_EPS">
							<td class="celda_mes_EPS" id="mes"><?= DateHelper::mes_tostring( $cuestionario->mes,'M') ?></td>
							<td class="celda_dia_EPS" id="dia1">01</td>
						</tr>
						<?php for($dia=2;$dia<=$dias_mostrar_formulario;$dia++):?>
						    <tr class="fila_fixed_EPS borde_fila_fixed">
							    <td class="celda_mes_EPS"></td>
							    <td class="celda_dia_EPS" id="dia<?= $dia ?>"><?= sprintf('%02d', $dia); ?></td>
						    </tr>					
						<?php endfor ?>
					</table>
				</div>
				<?php for($i=0;$i<4;$i++): ?>		
				<div id="datos_ut<?= $i ?>" class="div_UT">
					<table cellpadding="0" cellspacing="0">
						<?php for($dia=1;$dia<=$dias_mostrar_formulario;$dia++):?>				
						    <tr class="fila_EPS" id="fila_ut<?= $i ?>_dia<?= $dia ?>">
							    <td class="celda_EPS" style="width:54px;"><input type="text" class="numero sindecimales digits" style="width:35px;" maxlength="5" name="ut<?= $i ?>_col0_dia<?= $dia ?>" id="ut<?= $i ?>_col0_dia<?= $dia ?>"/></td>
							    <td class="celda_EPS" style="width:54px;"><input type="text" class="numero sindecimales digits" style="width:35px;" name="ut<?= $i ?>_col1_dia<?= $dia ?>" id="ut<?= $i ?>_col1_dia<?= $dia ?>"/></td>
							    <td class="celda_EPS deshabilitado" style="width:53px;"><div id="ut<?= $i ?>_col2_dia<?= $dia ?>" style="float:left;width:53px;">-</div><img id="imgerror_ut<?= $i ?>_dia<?= $dia ?>" src='images/error2.png' class="cursor_hand" style="float:left;margin-top:2px;margin-left:-14px;z-index:10;display:none;"><div style="clear: both;"> </div></td>
						    </tr>
						<?php endfor ?>	
	                </table>
				</div>
				<?php endfor ?>
				<div id="datos_col_totales" style="" cellpadding="0" cellspacing="0">
					<table cellpadding="0" cellspacing="0">				
						<?php for($dia=1;$dia<=$dias_mostrar_formulario;$dia++):?>
						<tr class="fila_tot_EPS">
							<td class="celda_tot_EPS" style="width:29px;"></td>
						    <td class="celda_tot_EPS" style="width:55px;" id="total_col0_dia<?= $dia ?>">-</td>
						    <td class="celda_tot_EPS" style="width:55px;" id="total_col1_dia<?= $dia ?>">-</td>
						    <td class="celda_tot_EPS" style="width:54px;"><div id="total_col2_dia<?= $dia ?>" style="float:left;width:54px;">-</div><img id="imgerror_uttot_dia<?= $dia ?>" src='images/error2.png' class="cursor_hand" style="float:left;margin-top:2px;margin-left:-14px;z-index:10;display:none;"><div style="clear: both;"> </div></td>
						</tr>
						<?php endfor ?>
					</table>
				</div>	
				<div style="clear: both;"> </div>
			</div>
			<div id="totales">
				<div id="totales_mes_dia" class="sombra_superior" style="clear:both;float: left;">
					<table cellpadding="0" cellspacing="0" class="fondo_fixed">			
						<tr class="fila_cab_tot_pie_EPS">
							<td colspan="2" class="cab_tot_pie_EPS" style="width:116px;" id="fila_totales">Total</div></td>
						</tr>                    
						<tr class="fila_cab_tot_pie_EPS">
							<td colspan="2" class="cab_tot_pie_EPS" style="width:116px;" id="fila_est_med">Estancia media</div></td>
						</tr>                    
					</table>
				</div>
				<?php for($i=0;$i<4;$i++): ?>		
				<div id="totales_ut<?= $i ?>" class="sombra_superior div_tot_pie">
					<table cellpadding="0" cellspacing="0">
						<tr class="fila_tot_pie_EPS">
						    <td class="celda_tot_pie_EPS" style="width:54px;" id="total<?= $i ?>_fila_col0">-</td>
						    <td class="celda_tot_pie_EPS" style="width:54px;" id="total<?= $i ?>_fila_col1">-</td>
							<td class="celda_tot_pie_EPS" style="width:53px;" id="total<?= $i ?>_fila_col2">-</td>
						</tr>                    
						<tr class="fila_tot_pie_EPS">
							<td class="celda_tot_pie_EPS" colspan=3 id="est_med_<?= $i ?>">-</td>
						</tr>
	                </table>
				</div>
				<?php endfor ?>
				<div id="totales_col_totales" class="sombra_superior" style="float: left;" cellpadding="0" cellspacing="0">
					<table cellpadding="0" cellspacing="0">				
						<tr class="fila_cab_tot_pie_EPS borde_negro">
	                        <td class="celda_tot_pie_EPS" style="width:29px;"></td>
						    <td class="celda_tot_pie_EPS" style="width:55px;" id="total_totales_col0">-</td>
						    <td class="celda_tot_pie_EPS" style="width:55px;" id="total_totales_col1">-</td>
							<td class="celda_tot_pie_EPS" style="width:54px;" id="total_totales_col2">-</td>
						</tr>
	                    <tr class="fila_cab_tot_pie_EPS borde_negro">
							<td class="celda_tot_pie_EPS" style="width:54px;" colspan=4 id="est_med_total">-</td>
						</tr>
					</table>
				</div>	
			</div>
			<table style="width:100%">
                <tr style="vertical-align: middle;">
                    <td style="width:30%;">
                        <div style="float:left;">Índice de ocupación por plazas<span id="ayuda_oc" onclick='MostrarAyuda("ayuda_oc","AYUDA17<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' class="ayudaicon2" title="Ayuda (tecla de acceso: l)" accesskey="l">&nbsp;</span>:&nbsp;</div><div style="float:left;" id="indice_ocupacion" name="indice_ocupacion"> </div>
                        <div style="clear: both;float:left;">Nº máximo de pernoctaciones<span id="ayuda_pc" onclick='MostrarAyuda("ayuda_pc","AYUDA18<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' class="ayudaicon2" title="Ayuda (tecla de acceso: b)" accesskey="b">&nbsp;</span>:&nbsp;</div><div style="float:left;" id="maximo_pernoctaciones" name="maximo_pernoctaciones"> </div>
                    </td>
                    <td>
                        <div id="paginas" style="text-align:center;"> </div>
                    </td>
                    <td  style="width:30%;text-align:right;">
                    	<?php if(!$solo_lectura):?>
                        <a href="<?=$urlSelectPaises?>" class="enlace"><img src="images/lista_check.png" border="0" style="vertical-align: middle;"/></a> <a href="<?=$urlSelectPaises?>" class="enlace" style="color:#30659A;">Seleccionar país / provincia / isla</a>
                        <?php endif?>
                    </td>
                </tr>
            </table>
			<div style="clear: both;"> </div>				
		</div>
		<div id="habitaciones" class="panel_tab">
			<div id="cab_habitac">
				<table cellpadding="0" cellspacing="0" style="background-color:#ffffff;">
					<tr class="fila_tit_HAB">
						<td colspan="2" style="background-color:#ffffff;"></td>
						<td colspan="5" style="padding-left:6px;">
							<?= $tipo_establecimiento==3 ? "Apartamentos ocupados" : "Habitaciones ocupadas" ?>
						</td>
					</tr>	
					<tr class="fila_cab_HAB">
							<td colspan="2" class="celda_blanco_cab_HAB"></td>
							<td class="cab_HAB"><?php if(!$solo_lectura):?><img id="btn_clean_hab_suplet_dia" src='images/cross.png' class="cursor_hand" style="float:left;margin-top:2px;"><?php endif?>Plazas supletorias u otras</td>
						    <td class="cab_HAB"><?php if(!$solo_lectura):?><img id="btn_clean_hab_dobles_dia" src='images/cross.png' class="cursor_hand" style="float:left;margin-top:2px;"><?php endif?><?= $tipo_establecimiento==3 ? "4/6 pax" : "Dobles (uso doble)" ?></td>
						    <td class="cab_HAB"><?php if(!$solo_lectura):?><img id="btn_clean_hab_indiv_dia" src='images/cross.png' class="cursor_hand" style="float:left;margin-top:2px;"><?php endif?><?= $tipo_establecimiento==3 ? "Estudio, 2/4 pax" : "Dobles (uso individual)" ?></td>
						    <td class="cab_HAB"><?php if(!$solo_lectura):?><img id="btn_clean_hab_otras_dia" src='images/cross.png' class="cursor_hand" style="float:left;margin-top:2px;"><?php endif?><?= $tipo_establecimiento==3 ? "Otros apartamentos" : "Otras habitaciones" ?></td>
					    	<td class="cab_tot_HAB">Total</td>
					</tr>									
				</table>
			</div>
			<div id="scroll_habitac" class="contenido_scroll" style="width:900px;">
				<table cellpadding="0" cellspacing="0" style="background-color:#ffffff;">		
					<?php for($dia=1;$dia<=$dias_mostrar_formulario;$dia++):?>						
					    <tr class="fila_dia_HAB">
						    <td class="celda_mes_HAB" style="<?= $dia==1 ? 'border-top: solid 1px #e0e0e0;' : '' ?>"><?= $dia==1 ? DateHelper::mes_tostring( $cuestionario->mes,'M') : '' ?></td>
						    <td class="celda_dia_HAB" style="<?= $dia==1 ? 'border-top: solid 1px #e0e0e0;' : '' ?>"><div id="dia<?= $dia ?>" style="text-align:center;"><?= sprintf('%02d', $dia); ?></div></td>
						    <td style="width:161px;" class="celda_HAB"><input type="text" class="numero sindecimales digits" style="width:35px;" maxlength="5" name="hab_suplet_dia<?= $dia ?>" id="hab_suplet_dia<?= $dia ?>"/></td>
						    <td style="width:161px;" class="celda_HAB"><input type="text" class="numero sindecimales digits" style="width:35px;" maxlength="5" name="hab_dobles_dia<?= $dia ?>" id="hab_dobles_dia<?= $dia ?>"/></td>
						    <td style="width:161px;" class="celda_HAB"><input type="text" class="numero sindecimales digits" style="width:35px;" maxlength="5" name="hab_indiv_dia<?= $dia ?>" id="hab_indiv_dia<?= $dia ?>"/></td>
					    	<td style="width:161px;" class="celda_HAB"><input type="text" class="numero sindecimales digits" style="width:35px;" maxlength="5" name="hab_otras_dia<?= $dia ?>" id="hab_otras_dia<?= $dia ?>"/></td>
					    	<td style="width:101px;" class="celda_HAB fondo_fixed inactivo">
					    	<div id="hab_coltotal_dia<?= $dia ?>" style="float:left;width:101px;">-</div>
					    	<img id="imgerror_hab_dia<?= $dia ?>" src='images/error2.png' class="cursor_hand" style="float:left;margin-top:2px;margin-left:-14px;z-index:10;display:none;"><div style="clear: both;"> </div></td>
					    	</div>
					    	</td>
	
						</tr>			
					<?php endfor ?>
				</table>
			</div>
			<div id="tot_habitac">
				<table cellpadding="0" cellspacing="0" style="background-color:#ffffff;">
					<tr class="fila_tot_HAB">
							<td colspan="2" style="width:133px;" class="cab_tot_pie_EPS">Totales</td>
							<td style="width:161px;" class="celda_tot_HAB" id="hab_tot_suplet">-</td>
						    <td style="width:161px;" class="celda_tot_HAB" id="hab_tot_dobles">-</td>
						    <td style="width:161px;" class="celda_tot_HAB" id="hab_tot_indiv">-</td>
						    <td style="width:161px;" class="celda_tot_HAB" id="hab_tot_otras">-</td>
					    	<td style="width:101px;" class="celda_tot_HAB" id="hab_tot_totales">-</td>
					</tr>									
				</table>
			</div>
			<table style="width:100%">
                <tr style="vertical-align: middle;">
                    <td style="width:25%;">
                        <div style="float:left;margin-top:3px;">Índice de <?= $tipo_establecimiento == 3 ? 'ocupación por apartamentos' : 'ocupación por habitaciones' ?><span id="ayuda_apoc" onclick='MostrarAyuda("ayuda_apoc","AYUDA24<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' class="ayudaicon2" title="Ayuda (tecla de acceso: g)" accesskey="g">&nbsp;</span>:&nbsp;</div><div style="float:left;margin-top:3px;" id="indice_apart_ocup" name="indice_apart_ocup"> </div>
                    </td>
                </tr>
            </table>						
		</div>
		<div id="personal_precios" class="panel_tab">      
			<div style="width:40%;float:left;">
                <span class="titulo_3">Personal</span>
                <div class="subrayado" style="width:100%"> </div>
                Indique el número total de personal ocupado durante el mes de referencia. No incluya personal de vacaciones ni de baja temporal.
                <table style="width:250px;margin-left:12px;margin-top:6px;border-spacing: 0 2px;" cellpadding="0">
                    <tr style="padding-bottom:2px;">
                        <td>No remunerado:</td>
                        <td><input type="text" id="pers_no_remunerado" name="pers_no_remunerado" class="numero sindecimales digits" style="width:55px;" tabindex="0" maxlength="10"></td>
                        <td style="width:15px;vertical-align:middle;"><img id="imgerror_pers_no_remunerado" src='images/error2.png' class="cursor_hand" style="display:none;"></td>
                    </tr>                    
                    <tr>
                        <td>Fijo:</td>
                        <td><input type="text" id="pers_fijo" name="pers_fijo" class="numero sindecimales digits" style="width:55px;" tabindex="1" maxlength="10"></td>
                        <td style="width:15px;vertical-align:middle;"><img id="imgerror_pers_fijo" src='images/error2.png' class="cursor_hand" style="display:none;"></td>
                    </tr>                    
                    <tr>
                        <td>Eventual:</td>
                        <td><input type="text" id="pers_eventual" name="pers_eventual" class="numero sindecimales digits" style="width:55px;" tabindex="2" maxlength="10"></td>
                        <td style="width:15px;vertical-align:middle;"><img id="imgerror_pers_eventual" src='images/error2.png' class="cursor_hand" style="display:none;"></td>
                    </tr>
                </table>
            </div>
            <div style="width:58%;float:left;padding-left:15px;">
                <span class="titulo_3">Precios</span>
                <div class="subrayado" style="width:100%"> </div>
                <table style="width:95%;margin-left:12px;margin-top:-5px;border-spacing: 0 2px;" cellpadding="0">
                    <tr>
                        <td style="width:50%">ADR<span onclick='MostrarAyuda("iconoADR", "AYUDA21<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' id='iconoADR' class='ayudaicon2' title='Ayuda (tecla de acceso: u)' accesskey='u'>&nbsp;</span> mensual por tipo de cliente:</td>
                        <td colspan="2"><div style="text-align: right;<?=$solo_lectura?'display:none;':''?>" id="modoIntroduccionADR"></div></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <table style="width:100%; margin-bottom: 10px;" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                    </td>
                                    <td class="cab_adr_PREC">
                                        ADR(€)
                                    </td>
                                    <td class="cab_numhab_PREC">
                                        <?= $tipo_establecimiento == 3 ? "Nº apartamentos ocupados" : "Nº habitaciones ocupadas"; ?>
                                    </td>
                                    <td class="cab_porchab_PREC" colspan="2">
                                        <?= $tipo_establecimiento == 3 ?  "% apartamentos ocupados" : "% habitaciones ocupadas"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="celda_tipocliente_PREC">
                                        Turoperador tradicional
                                    </td>
                                    <td class="celda_adr_PREC">
                                        <input type="text" class="numero condecimales digits"  maxlength="11" name="adr_to_tradic" id="adr_to_tradic" style="width:45px;" tabindex="5"/>
                                    </td>
                                    <td class="celda_numhab_PREC">
                                        <input type="text" class="numero sindecimales digits"  maxlength="6" name="num_habocup_to_tradic" id="num_habocup_to_tradic" style="width:35px;" tabindex="6"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 95px;">
                                    	<input type="text" class="numero condecimales digits"  maxlength="6" name="pct_habocup_to_tradic" id="pct_habocup_to_tradic" style="width:35px;position:relative;" tabindex="7"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 15px;vertical-align:middle;">
							    		<img id="imgerror_adr_to_tradic" src='images/error2.png' class="cursor_hand" style="display:none;">                          
                                    </td>
                                </tr>
                                <tr>
                                    <td class="celda_tipocliente_PREC">
                                        Empresas
                                    </td>
                                    <td class="celda_adr_PREC">
                                        <input type="text" class="numero condecimales digits"  maxlength="11" name="adr_emp" id="adr_emp" style="width:45px;" tabindex="8"/>
                                    </td>
                                    <td class="celda_numhab_PREC">
                                        <input type="text" class="numero sindecimales digits"  maxlength="6" name="num_habocup_emp" id="num_habocup_emp" style="width:35px;" tabindex="9"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 95px;">
                                        <input type="text" class="numero condecimales digits" maxlength="6" name="pct_habocup_emp" id="pct_habocup_emp" style="width:35px;" tabindex="10"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 15px;vertical-align:middle;">
                                   		<img id="imgerror_adr_emp" src='images/error2.png' class="cursor_hand" style="display:none;"> 
                                    </td>
                                </tr>                                
                                <tr>
                                    <td class="celda_tipocliente_PREC">
                                        Agencia de viaje tradicional
                                    </td>
                                    <td class="celda_adr_PREC">
                                        <input type="text" class="numero condecimales digits"  maxlength="11" name="adr_ag_tradic" id="adr_ag_tradic" style="width:45px;" tabindex="11"/>
                                    </td>
                                    <td class="celda_numhab_PREC">
                                        <input type="text" class="numero sindecimales digits"  maxlength="6" name="num_habocup_ag_tradic" id="num_habocup_ag_tradic" style="width:35px;" tabindex="12"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 95px;">
                                        <input type="text" class="numero condecimales digits"  maxlength="6" name="pct_habocup_ag_tradic" id="pct_habocup_ag_tradic" style="width:35px;" tabindex="13"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 15px;vertical-align:middle;">
                                        <img id="imgerror_adr_ag_tradic" src='images/error2.png' class="cursor_hand" style="display:none;">
                                    </td>
                                </tr>                                 
                                <tr>
                                    <td class="celda_tipocliente_PREC">
                                        Particulares
                                    </td>
                                    <td class="celda_adr_PREC">
                                        <input type="text" class="numero condecimales digits" maxlength="11" name="adr_partic" id="adr_partic" style="width:45px;" tabindex="14"/>
                                    </td>
                                    <td class="celda_numhab_PREC">
                                        <input type="text" class="numero sindecimales digits" maxlength="6" name="num_habocup_partic" id="num_habocup_partic" style="width:35px;" tabindex="15"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 95px;">
                                        <input type="text" class="numero condecimales digits" maxlength="6" name="pct_habocup_partic" id="pct_habocup_partic" style="width:35px;" tabindex="16"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 15px;vertical-align:middle;">
                                        <img id="imgerror_adr_partic" src='images/error2.png' class="cursor_hand" style="display:none;">
                                    </td>
                                </tr>                                 
                                <tr>
                                    <td class="celda_tipocliente_PREC">
                                        Grupos
                                    </td>
                                    <td class="celda_adr_PREC">
                                        <input type="text" class="numero condecimales digits" maxlength="11" name="adr_grupos" id="adr_grupos" style="width:45px;" tabindex="17"/>
                                    </td>
                                    <td class="celda_numhab_PREC">
                                        <input type="text" class="numero sindecimales digits" maxlength="6" name="num_habocup_grupos" id="num_habocup_grupos" style="width:35px;" tabindex="18"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 95px;">
                                        <input type="text" class="numero condecimales digits" maxlength="6" name="pct_habocup_grupos" id="pct_habocup_grupos" style="width:35px;" tabindex="19"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 15px;vertical-align:middle;">
                                        <img id="imgerror_adr_grupos" src='images/error2.png' class="cursor_hand" style="display:none;">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="celda_tipocliente_PREC">
                                        Contratación directa del hotel online
                                    </td>
                                    <td class="celda_adr_PREC">
                                        <input type="text" class="numero condecimales digits" maxlength="11" name="adr_ht" id="adr_ht" style="width:45px;" tabindex="20"/>
                                    </td>
                                    <td class="celda_numhab_PREC">
                                        <input type="text" class="numero sindecimales digits" maxlength="6" name="num_habocup_ht" id="num_habocup_ht" style="width:35px;" tabindex="21"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 95px;">
                                        <input type="text" class="numero condecimales digits" maxlength="6" name="pct_habocup_ht" id="pct_habocup_ht" style="width:35px;" tabindex="22"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 15px;vertical-align:middle;">
                                        <img id="imgerror_adr_ht" src='images/error2.png' class="cursor_hand" style="display:none;">
                                    </td>
                                </tr>  
                                <tr>
                                    <td class="celda_tipocliente_PREC">
                                        Agencias de viaje on-line
                                    </td>
                                    <td class="celda_adr_PREC">
                                        <input type="text" class="numero condecimales digits" maxlength="11" name="adr_ag_online" id="adr_ag_online" style="width:45px;" tabindex="23"/>
                                    </td>
                                    <td class="celda_numhab_PREC">
                                        <input type="text" class="numero sindecimales digits" maxlength="6" name="num_habocup_ag_online" id="num_habocup_ag_online" style="width:35px;" tabindex="24"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 95px;">
                                        <input type="text" class="numero condecimales digits" maxlength="6" name="pct_habocup_ag_online" id="pct_habocup_ag_online" style="width:35px;" tabindex="25"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 15px;vertical-align:middle;">
                                        <img id="imgerror_adr_ag_online" src='images/error2.png' class="cursor_hand" style="display:none;">
                                    </td>
                                </tr>                                 
                                <tr>
                                    <td class="celda_tipocliente_PREC">
                                        Turoperador on-line
                                    </td>
                                    <td class="celda_adr_PREC">
                                        <input type="text" class="numero condecimales digits" maxlength="11" name="adr_to_online" id="adr_to_online" style="width:45px;" tabindex="26"/>
                                    </td>
                                    <td class="celda_numhab_PREC">
                                        <input type="text" class="numero sindecimales digits" maxlength="6" name="num_habocup_to_online" id="num_habocup_to_online" style="width:35px;" tabindex="27"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 95px;">
                                        <input type="text" class="numero condecimales digits" maxlength="6" name="pct_habocup_to_online" id="pct_habocup_to_online" style="width:35px;" tabindex="28"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 15px;vertical-align:middle;">
                                        <img id="imgerror_adr_to_online" src='images/error2.png' class="cursor_hand" style="display:none;">
                                    </td>
                                </tr>                                 
                                <tr>
                                    <td class="celda_tipocliente_PREC">
                                        Otros
                                    </td>
                                    <td class="celda_adr_PREC">
                                        <input type="text" class="numero condecimales digits" maxlength="11" name="adr_otros" id="adr_otros" style="width:45px;" tabindex="29"/>
                                    </td>
                                    <td class="celda_numhab_PREC">
                                        <input type="text" class="numero sindecimales digits" maxlength="6" name="num_habocup_otros" id="num_habocup_otros" style="width:35px;" tabindex="30"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 95px;">
                                        <input type="text" class="numero condecimales digits" maxlength="6" name="pct_habocup_otros" id="pct_habocup_otros" style="width:35px;" tabindex="31"/>
                                    </td>
                                    <td class="celda_porchab_PREC" style="width: 15px;vertical-align:middle;">
                                        <img id="imgerror_adr_otros" src='images/error2.png' class="cursor_hand" style="display:none;">
                                    </td>
                                </tr>                                
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Tarifa media por <?= $tipo_establecimiento == 3 ? "apartamento" : "habitación"; ?> (ADR) <a href='javascript: MostrarAyuda("iconoTarMedHab", "AYUDA20<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' id='iconoTarMedHab' class='ayudaicon enlace' style='cursor: help;' title='Ayuda (tecla de acceso: t)' accesskey='t'><span></span></a>:</td>
                        <td><input type="text" id="tar_med_habitac"  maxlength="11" name="tar_med_habitac" class="numero condecimales digits" style="width:55px;" tabindex="4"> €</td>
                        <td style="width:15px;vertical-align:middle;"><img id="imgerror_prec_tar_med_habitac" src='images/error2.png' class="cursor_hand" style="display:none;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width:69%">Ingreso por <?= $tipo_establecimiento == 3 ? "apartamento" : "habitación"; ?> disponible mensual (RevPar) <a href='javascript: MostrarAyuda("iconoHabDispMens", "AYUDA19<?= $tipo_establecimiento==3 ? "_APT" : "_HOT" ?>");' id='iconoHabDispMens' class='ayudaicon enlace' style='cursor: help;' title='Ayuda (tecla de acceso: s)' accesskey="s"><span></span></a>:</td>
                        <td><input type="text" id="ing_hab_disp_mensual"  maxlength="11" name="ing_hab_disp_mensual" class="numero condecimales digits" style="width:55px;" tabindex="3"> €</td>
                        <td style="width:15px;vertical-align:middle;"><img id="imgerror_prec_ing_hab_disp_mensual" src='images/error2.png' class="cursor_hand" style="display:none;"></td>
                    </tr>                    
                </table>
            </div>
			<div style="clear: both;"> </div>				            
		</div>
	</div>
	<div style="float:left;<?php if($solo_lectura):?>padding-top: 15px;<?php endif; ?>">
		<a href="<?=$urlBack?>"><img src="images/volver.png" border="0"></a> <a href="<?=$urlBack?>" class="enlace" style=" position: relative; top: -2px;">Salir de la encuesta</a>
		<?php if(!$solo_lectura):?>
			<?php if(!$mes_encuesta_finalizado):?>
				<input class="" style="margin-left: 80px" id="ceseActividad" name="ceseActividad" type="checkbox" value="1">Permitir el envío del cuestionario antes de plazo por cese de actividad.</input><span id="ayuda_ca" onclick='MostrarAyuda("ayuda_ca","AYUDA30");' class="ayudaicon2" title="Ayuda (tecla de acceso: a)" accesskey="a">&nbsp;</span><br/>
			<?php endif ?>
		<input class="search btn_guardar" name="guardarBtn" type="button" value="Guardar"/>
		<input class="search btn_guardar_enviar" name="enviarBtn" type="button" value="Guardar y enviar encuesta"/>
		<?php endif ?>
		<?php if(isset($aloja_plazo)): ?>
		<span style="padding-left:10px;">
			Fecha límite: <font class="tx_marcado"><?= Datehelper::fecha_tostring($aloja_plazo,true) ?></font>
		</span>
		<?php endif ?>		
	</div>
	<div style="float: right;">
		<input class="search btn_imprimir" name="imprimirBtn" type="button" value="Imprimir"/>
	</div>
	</form>  
</div>
<!-- FIN BLOQUE INTERIOR -->

<div id="dialog-detail" title="Envío de encuesta" >
	<div id="msg_errores" style="text-align: left"></div>
</div>
<div id="dialog-exceso" title="Exceso de ocupación" >
	<div id="msg_exceso" style="text-align: left"></div>
</div>
<div id="dialog-modoADR" title="Cambiar modo de introducción" >
    <div id="msg_modoADR" class="cuadro fondo_amarillo" style="text-align: left;margin-top: 10px;"></div>
</div>
<div id="dialog-print" title="Imprimir encuesta" >
    <div id="msg_impresion" style="text-align: left;"></div>
</div>
<?php if(!$mes_encuesta_finalizado):?>
	<div id="dialog-aviso" title="Aviso" >
		<div id="msg_avisos">
		</div>
	</div>
<?php endif ?>
