<style>
#subirXmlBtn {
	display: inline-block;
	float: right;
	width:170px;
	padding-left: 24px;
	margin: 10px 0 10px 15px;
	background-image: url(images/subir.gif);
	background-repeat: no-repeat;
	background-position: 8px 6px;
}
#resubirXmlBtn {
	display: inline-block;
	float: right;
	width:200px;
	padding-left: 24px;
	margin: 10px 0 10px 15px;
	background-image: url(images/resubir.png);
	background-repeat: no-repeat;
	background-position: 0px -2px;
}
#descargarXmlBtn {
	display: inline-block;
	float: right;
	width:200px;
	padding-left: 24px;
	margin: 10px 0 10px 15px;
	background-image: url(images/descargar.png);
	background-repeat: no-repeat;
	background-position: 8px 8px;
}
</style>
<script type="text/javascript">
var hayExcesoPlazasMes=<?= $hayExcesoPlazasMes ? 'true':'false' ?>;
var hayExcesoPlazas=<?= $hayExcesoPlazas ? 'true':'false' ?>;
var hayExcesoHabitaciones=<?= $hayExcesoHabitaciones ? 'true':'false' ?>;
var codMotivoExcesoPlazas;
var detalleMotivoExcesoPlazas;
var codMotivoExcesoHabitaciones;
var detalleMotivoExcesoHabitaciones;

var excesoPlazasPorciento = <?= (defined('ALOJA_EXCESO_PLAZAS') && ALOJA_EXCESO_PLAZAS) ? ALOJA_EXCESO_PLAZAS : 0?>;
var excesoHabitacionesPorciento = <?= (defined('ALOJA_EXCESO_HABIT') && ALOJA_EXCESO_HABIT) ? ALOJA_EXCESO_HABIT : 0?>;

var motivosExcesoInfo=[];
<?php
	if(isset($motivosExcesos))
	{
		if ($motivosExcesos->has_rows())
		{
			foreach ($motivosExcesos as $row)
			{
				echo "motivosExcesoInfo.push({'id':'".$row['idmot']."', 'desc':'".$row['descmot']."', 'oblig':'".$row['oblig']."', 'ayuda':'".$row['ayuda']."'});\n";
			}
		}
	}
?>

<?php
	if(isset($limiteInvitaciones))
	{
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
	}
?>

function getMotivosExcesosOps()
{
	var opciones="";
	for(var i=0;i<motivosExcesoInfo.length;i++)
		opciones+='<option value="'+motivosExcesoInfo[i].id+'"'+(motivosExcesoInfo[i].oblig=='S' ? ' data-detalle="true"':'')+'>'+motivosExcesoInfo[i].id+' - '+motivosExcesoInfo[i].desc+'</option>';
	return opciones;
}

function ayudaMotivosExcesos(idmot)
{
	for(var i=0;i<motivosExcesoInfo.length;i++)
	{
		if(motivosExcesoInfo[i].id==idmot)
			return motivosExcesoInfo[i].ayuda;
	}
	return '';
}

function getAyudaMotivoExceso(id,cod)
{
	var ayuda="";
	if(id.endsWith('_plz'))
	{
		ayuda=ayudaMotivosExcesos(cod);
	}
	else
	{
		ayuda=ayudaMotivosExcesos(cod);
	}
	return ayuda;
}

function MostrarErroresExceso(excPl,excHb,funcionContinuar)
{
	codMotivoExcesoPlazas="";
	detalleMotivoExcesoPlazas="";
	codMotivoExcesoHabitaciones="";
	detalleMotivoExcesoHabitaciones="";
	
	var codigo="";
	
	var alto=130;
	if((excPl)&&(excHb))
		alto=90;
	if(excPl)
		codigo+="<div class='cuadro fondo_amarillo'>"+
		"<p style='margin-top:15px;'>Según los datos que tiene el ISTAC, las plazas ocupadas superan el número de plazas del establecimiento.</p>"+
		"<p>Indique el motivo para poder guardar el cuestionario:</p>"+
		"<table style=\"margin-left:10px;width:99%\">"+
		"<tr><td style=\"width:11%;\"><label for=\"motivo_exc_plz\">Motivo:</label></td><td><select id=\"motivo_exc_plz\" name=\"motivo_exc_plz\" style=\"width:90%;\">"+getMotivosExcesosOps()+"</select></td></tr>"+
		"<tr><td style=\"width:11%; vertical-align: top\"><label for=\"motivo_exc_plz_det\">Detalle:</label></td><td><textarea id=\"motivo_exc_plz_det\" name=\"motivo_exc_plz_det\" disabled style=\"width:90%; height:"+alto+"px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em;\" placeholder=\"Describa aquí el motivo del exceso de plazas ocupadas...\"></textarea></td></tr>"+
		"<tr><td style=\"width:11%;\"></td><td><div id=\"motivo_exc_plz_ayuda\" class=\"pulsante\" style=\"width:90%; background-color: #D3D3D3; padding: 0px 4px 0px 4px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em; font-weight: bold; font-style: italic\"></div></td></tr>"+
		"</table></div>";
	if(excHb)
		codigo+="<div class='cuadro fondo_amarillo'>"+
		"<p style='margin-top:15px;'>Según los datos que tiene el ISTAC, la ocupación de <?= $es_hotel ? "habitaciones" : "apartamentos" ?> supera el número de unidades del establecimiento.</p>"+
		"<p>Indique el motivo para poder guardar el cuestionario:</p>"+
		"<table style=\"margin-left:10px;width:99%\">"+
		"<tr><td style=\"width:11%;\"><label for=\"motivo_exc_hab\">Motivo:</label></td><td><select id=\"motivo_exc_hab\" name=\"motivo_exc_hab\" style=\"width:90%;\">"+getMotivosExcesosOps()+"</select></td></tr>"+
		"<tr><td style=\"width:11%; vertical-align: top\"><label for=\"motivo_exc_hab_det\">Detalle:</label></td><td><textarea id=\"motivo_exc_hab_det\" name=\"motivo_exc_hab_det\" disabled style=\"width:90%; height:"+alto+"px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em;\" placeholder=\"Describa aquí el motivo del exceso de habitaciones ocupadas...\"></textarea></td></tr>"+
		"<tr><td style=\"width:11%;\"></td><td><div id=\"motivo_exc_hab_ayuda\" class=\"pulsante\" style=\"width:90%; background-color: #D3D3D3; padding: 0px 4px 0px 4px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em; font-weight: bold; font-style: italic\"></div></td></tr>"+
		"</table></div>";
	$("#msg_exceso").html(codigo);
	
	// Limpiamos los motivos
	$("select[id^='motivo_exc_']").val([]);
	$("textarea[id^='motivo_exc_']").val("");
	$("div[id^='motivo_exc_'][id$='ayuda']").text("");
	
	$("select[id^='motivo_exc_']").change(
			function()
			{
				var elto=$(this).find(":selected");
				var id=$(this).attr('id');
				var cod=elto.val();
				if(elto.data("detalle"))
					$("#"+id+"_det").attr("disabled",false).focus();
				else
					$("#"+id+"_det").attr("disabled",true);
				var textoAyuda=$("#"+id+"_ayuda");
				textoAyuda.text(getAyudaMotivoExceso(id,cod));				
				var tmpClass=textoAyuda.attr('class');
				textoAyuda.removeClass();
				setTimeout(function(){textoAyuda.addClass(tmpClass).addClass('start-now');},10);
			}
	);
	
	$( "#dialog-exceso" ).dialog(
		{
			title : 'Exceso de ocupación',
			buttons: {
				"Aceptar": function() {
					var ok=true;
					var msg="";
					$("select[id^='motivo_exc_']").each(
						function()
						{
							var elto=$(this).find(":selected");
							if(elto.length==0)
							{
								ok=false;
								msg="Es obligatorio indicar un motivo del exceso.";
								return false;
							}
							var cod=elto.val();
							if(cod!=null)
							{
								var id=$(this).attr('id');
								var detalle=$("#"+id+"_det").val();
								if(elto.data("detalle") && (detalle==""))
								{
									ok=false;
									msg="Es obligatorio indicar el detalle requerido del motivo del exceso.";
									return false;
								}
							}
						}
					);
					if(ok==false)
					{
						alert(msg);
						return;
					}
					else
					{
						if(excPl)
						{
							codMotivoExcesoPlazas=$("#motivo_exc_plz").find(":selected").val();
							detalleMotivoExcesoPlazas=$("#motivo_exc_plz_det").val();
						}
						if(excHb)
						{
							codMotivoExcesoHabitaciones=$("#motivo_exc_hab").find(":selected").val();
							detalleMotivoExcesoHabitaciones=$("#motivo_exc_hab_det").val();
						}
						funcionContinuar();
						$( this ).dialog( "close" );
					}
				},
				"Cancelar": function() {
					$( this ).dialog( "close" );
				}
			}
		}
	);
	$( "#dialog-exceso" ).dialog("open");
}

$(document).ready( function() {
	<?php if (isset($url_acuse)) : ?>
	$("input[name='printAcuseBtn']").button().click(function()
			{
				window.open("<?=  $url_acuse ?>","_self");
			});
	<?php endif; ?>
	<?php if ($errors != null) : ?>
	$("input[name='operationBtn']").button().click(function()
			{
				window.print();
			});	
	<?php endif; ?>
	
	<?php if(($hayExcesoPlazasMes) || ($hayExcesoPlazas) || ($hayExcesoHabitaciones)) : ?>
	$("input[name='cerrarExcesoBtn']").button().click(function()
		{
			MostrarErroresExceso(<?=  ($hayExcesoPlazasMes || $hayExcesoPlazas) ? 'true':'false' ?>,<?=  $hayExcesoHabitaciones ? 'true':'false' ?>,
				function(){
					$("input[name='<?= ARG_TIPO_MOTIVO_EXCESO_PLAZAS ?>']").val(codMotivoExcesoPlazas);
					$("input[name='<?= ARG_TIPO_MOTIVO_EXCESO_HABITACIONES ?>']").val(codMotivoExcesoHabitaciones);
					$("input[name='<?= ARG_DETALLE_MOTIVO_EXCESO_PLAZAS ?>']").val(encodeURIComponent(detalleMotivoExcesoPlazas));
					$("input[name='<?= ARG_DETALLE_MOTIVO_EXCESO_HABITACIONES ?>']").val(encodeURIComponent(detalleMotivoExcesoHabitaciones));
					$('#<?= OP_SUBIR_XML_EXCESO ?>').submit();
				});
		});	
	<?php endif; ?>

	$( "#dialog-exceso" ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		height: "auto",
		width: "auto",
		position : { my: "center", at: "center", of: window },
		buttons: {
			"Aceptar": function() {
				$( this ).dialog( "close" );
			},
			"Cancelar": function() {
				$( this ).dialog( "close" );
			}
		}
	});

	<?php if (((!isset($es_encuesta_web)) || ($es_encuesta_web==false)) && (isset($botones)) && ($botones!=0)) :?>
	$("#subirXmlBtn").button().click(function(){
		$('#op').val("");
	});
	$("#resubirXmlBtn").button().click(function(){
		$('#op').val("<?= OP_RESUBIR_XML ?>");
	});
	$("#descargarXmlBtn").button().click(function(){
		$('#op').val("<?= OP_DESCARGAR_XML ?>");
	});
	<?php endif; ?>

  });
</script>

<div id="bloq_interior">
	<?php if (isset($titulo)) : ?>
	<h1 class="titulo_1 noprint" style="float:left;"><?= $titulo ?></h1>
	<?php endif; ?>
	<a id="ayuda_error" href="javascript:MostrarAyuda('ayuda_error','AYUDA08<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon enlace" style="float:right;margin-top:9px;background-position-y: 2px;" title="Ayuda (tecla de acceso: y)" accesskey="y"><strong>Ayuda</strong></a>	
	<div style="clear:both;"></div>
	<div class="bloq_central">
		<?php if ($errors == null) : ?>
			<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Recogida de encuestas</h3>
			<div class="subrayado"></div>
			<p>Su cuestionario de alojamiento ha sido subido y procesado con éxito.</p>
			<p><b>Muchas gracias por su colaboración.</b></p>
			<p>Si lo desea puede descargar un acuse de recibo.</p>
			<input class="search ui-button ui-widget ui-state-default ui-corner-all" name="printAcuseBtn" type="button" value="Descargar PDF" style="width:120px; margin: 10px 0px 10px 0px;background-image: url(images/descargar.png);background-repeat: no-repeat;background-position: 8px 4px;padding-left:27px;" role="button" aria-disabled="false">
        </div>
        <?php else :?>
			<div class="cuadro fondo_gris noprint" style="text-align: justify;">
				<h3 class="titulo_3" style="margin-bottom:4px;" >INFORME DE AVISOS</h3>
				<p><?= ($global_msg != null)? $global_msg:"No ha sido posible realizar el proceso debido a avisos en los datos. "?></p><p>Si lo desea puede imprimir un informe de los avisos encontrados.</p>
				<?php if ($cuestionario_parcial) :?>
					<div class="cuadro fondo_amarillo" style="text-align: justify;" class="noprint">
						<h3 class="titulo_3 avisoicon" style="margin-bottom:4px;">Aviso</h3>
						<div class="subrayado"></div>
						<p><strong>IMPORTANTE:</strong> Recuerde que debe enviar y cerrar el cuestionario completo a principios del próximo mes.</p>
					</div>				
				<?php endif; ?>
				<?php if ((isset($ir_formulario)) && ($ir_formulario)) : ?>
				<input class="search ui-but0px ui-widget ui-state-default ui-corner-all" name="operationBtn" type="button" value="Ir al formulario" style="margin: 10px 0px 10px 0px;background-image: url(img/formulario.gif);background-repeat: no-repeat;background-position: 8px 4px;padding-left:30px;" role="button" aria-disabled="false">
				<?php endif; ?>
				<?php if (isset($botones) && (($botones & 8)!=0)) :?>
					<div>
						<p>Si aparecen errores indicando que faltan datos de personal y/o precios, puede introducirlos manualmente en el formulario Web. Para ello pinche sobre el siguiente enlace:</p>
						<strong><a href="<?= $urlFormAloja; ?>" class="enlace noprint" _style=" position: relative; top: -2px;">Ir al formulario para rellenar personal y precios.</a></strong>
					</div>
				<?php endif; ?>
			</div>
			<div class="cuadro magnified_print" style="text-align: justify; background-color:#FFF6CA;border-color:#FFEB88" class="noprint">
				<h3 class="titulo_3" style="margin-bottom:4px;">LISTADO DE AVISOS</h3>
				<?php if ($errors->num_errores() == 1) : ?>
				<p>Se ha encontrado 1 aviso. Deberá corregir el aviso y repetir la operación.</p>
				<?php else : ?>
				<p>Se han encontrado <?= $errors->num_errores() ?> avisos:</p>
				<?php endif; ?>
				<?php 
				function print_tabla($tit, $errs, $filter = null)
				{
					if (!isset($filter) || $errs->hay_error_de_categorias($filter))
					{
						?>
						<table style="width:99%;border-collapse: collapse;">
						<tr>
						<th class="titulo_3" colspan="2" style="width:100%;background: #FFF3B6; border: solid 1px #FFEB88;padding:0px 8px;"><?= $tit ?></th>
						</tr>
						<?php foreach($errs->errores as $row) :?>
						<?php if (!isset($filter) || in_array($row->categoria, $filter)) :?>
						<tr>
						<?php if ($row->nivel == NIVEL_ERROR) :?>
							<td style="width:8%;background: #F8F8F8; color: #FF3C3C; border: solid 1px #FFEB88;text-align: center; vertical-align: middle; "><?= $row->nivel ?></td>
						<?php else: ?>
							<td style="width:8%;background: #F8F8F8; color: #FFC200; border: solid 1px #FFEB88;text-align: center; vertical-align: middle; "><?= $row->nivel ?></td>
						<?php endif;?>
						<td style="width:100%;background: #fdfdfd; border: solid 1px #FFEB88;padding:0px 8px;"><?= htmlentities(mb_convert_encoding($row->mensaje, "UTF-8")) ?></td>
						</tr>					
						<?php endif;?>
						<?php endforeach;?>
						</table>
						<?php 
					}
				}
				
				print_tabla("General", $errors, array(ERROR_GENERAL, ERROR_DATO_GLOBAL));
				print_tabla("Movimientos de viajeros", $errors, array(ERROR_MOVIMIENTOS));
				print_tabla("Habitaciones o apartamentos", $errors, array(ERROR_HABITACIONES));
				print_tabla("Personal y precios", $errors, array(ERROR_PERSONAL, ERROR_PRECIOS));
				print_tabla("Exceso mensual de plazas", $errors, array(ERROR_EXCESO_PLAZAS_MES));
				print_tabla("Exceso de plazas", $errors, array(ERROR_EXCESO_PLAZAS));
				print_tabla("Exceso de habitaciones", $errors, array(ERROR_EXCESO_HABITACIONES));

				?>
			</div> 
			<?php if (isset($es_encuesta_web) && $es_encuesta_web) :?>    
				<div style="padding-top: 6px;float: left;"><a href="<?= $site[PAGE_ALOJA_FORM];?>"><img src="images/volver.png" border="0" class="noprint"></a> <a href="<?= $site[PAGE_ALOJA_FORM];?>" class="enlace noprint" style=" position: relative; top: -2px;">Volver a la encuesta</a></div>
			<?php endif; ?>
			<?php if(($hayExcesoPlazasMes) || ($hayExcesoPlazas) || ($hayExcesoHabitaciones)) :?>
				<form name="<?= OP_SUBIR_XML_EXCESO ?>" id="<?= OP_SUBIR_XML_EXCESO ?>" action="#" method="post" accept-charset="UTF-8" > 
					<input type="hidden" name="<?= ARG_OP ?>" value="<?= OP_SUBIR_XML_EXCESO ?>"/>
					<input type="hidden" name="<?= ARG_MES ?>" value="<?= $mes_sel ?>">
					<input type="hidden" name="<?= ARG_ANO ?>" value="<?= $ano_sel ?>">
					<input type="hidden" name="<?= ARG_TIPO_MOTIVO_EXCESO_PLAZAS ?>" value="">
					<input type="hidden" name="<?= ARG_TIPO_MOTIVO_EXCESO_HABITACIONES ?>" value="">
					<input type="hidden" name="<?= ARG_DETALLE_MOTIVO_EXCESO_PLAZAS ?>" value="">
					<input type="hidden" name="<?= ARG_DETALLE_MOTIVO_EXCESO_HABITACIONES ?>" value="">
					<?php if ($cuestionario_parcial==false) :?>
						<input class="search ui-button ui-widget ui-state-default ui-corner-all noprint" name="cerrarExcesoBtn" type="button" value="Cerrar encuesta" role="button" aria-disabled="false">
					<?php endif; ?>
					<input class="search ui-button ui-widget ui-state-default ui-corner-all noprint" name="operationBtn" type="button" value="Imprimir" style="width:95px; background-image: url(images/imprimir.png);background-repeat: no-repeat;background-position: 8px 4px;padding-left:27px;float: right;" role="button" aria-disabled="false">
				</form>
			<?php else: ?>
				<form name="sfx" action="#" method="post" enctype="multipart/form-data" > 
					<input type="hidden" name="<?= ARG_OP ?>" value="<?= OP_SUBIR_XML_FORZADO ?>"/>
					<input type="hidden" name="<?= ARG_MES ?>" value="<?= $mes_sel ?>">
					<input type="hidden" name="<?= ARG_ANO ?>" value="<?= $ano_sel ?>">
					<?php if ($cuestionario_parcial==false) :?>
						<input class="search ui-button ui-widget ui-state-default ui-corner-all noprint" name="cerrarBtn" type="submit" value="Cerrar encuesta" role="button" aria-disabled="false">
					<?php endif; ?>
					<input class="search ui-button ui-widget ui-state-default ui-corner-all noprint" name="operationBtn" type="button" value="Imprimir" style="width:95px; background-image: url(images/imprimir.png);background-repeat: no-repeat;background-position: 8px 4px;padding-left:27px;float: right;" role="button" aria-disabled="false">
				</form>
			<?php endif; ?>
			<?php if ((!isset($es_encuesta_web)) || ($es_encuesta_web==false)) :?>
				<?php if (isset($botones) && (($botones & 8)!=0)) :?>
				<strong><a href="<?= $urlFormAloja; ?>" class="enlace noprint" _style=" position: relative; top: -2px;">Ir al formulario para rellenar personal y precios.</a></strong>
				<?php endif; ?>
				<br/><br/>   
				<form id="op_xml" method="POST" action="#">
					<input type="hidden" name="<?= ARG_OP ?>" id="op" value="">
					<input type="hidden" name="<?= ARG_MES ?>" value="<?= $mes_sel ?>">
					<input type="hidden" name="<?= ARG_ANO ?>" value="<?= $ano_sel ?>">
					<div>
						<?php if (($botones & 4)!=0) :?>
							<input id="descargarXmlBtn" class="search ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="Descargar fichero de datos">
						<?php endif; ?>
						<?php if (($botones & 2)!=0) :?>
							<input id="resubirXmlBtn" class="search ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="Resubir fichero de datos">
						<?php endif; ?>
						<?php if (($botones & 1)!=0) :?>
							<input id="subirXmlBtn" class="search ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="Subir fichero de datos">
						<?php endif; ?>
					</div>
				</form>
			<?php endif; ?> 
		<?php endif; ?>  
    </div>
</div>
<div id="dialog-exceso" title="Exceso de ocupación" >
	<div id="msg_exceso" style="text-align: left"></div>
</div>
<!-- FIN BLOQUE INTERIOR -->