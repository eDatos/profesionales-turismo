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
			<?php if (isset($email)) : ?>
			<p>Se le ha enviado un correo de confirmación de entrega a <?= $email ?>.</p>
			<?php else : ?>
			<p>Si nos facilita un correo electrónico, podrá recibir la confirmación de la entrega en próximas ocasiones.</p>
			<?php endif; ?>
			<p><b>Muchas gracias por su colaboración.</b></p>
			<p>Si lo desea puede descargar un acuse de recibo.</p>
			<input class="search ui-button ui-widget ui-state-default ui-corner-all" name="printAcuseBtn" type="button" value="Descargar PDF" style="width:120px; margin: 10px 0px 10px 0px;background-image: url(images/descargar.png);background-repeat: no-repeat;background-position: 8px 4px;padding-left:27px;" role="button" aria-disabled="false">
        </div>
        <?php else :?>
		<div class="cuadro fondo_gris noprint" style="text-align: justify;">
			<h3 class="titulo_3" style="margin-bottom:4px;" >INFORME DE ERRORES</h3>
			<p><?= ($global_msg != null)? $global_msg:"No ha sido posible realizar el proceso debido a errores en los datos. "?></p><p>Si lo desea puede imprimir un informe de los errores encontrados.</p>
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
			<h3 class="titulo_3" style="margin-bottom:4px;">LISTADO DE ERRORES</h3>
			<?php if ($errors->num_errores() == 1) : ?>
			<p>Se ha encontrado 1 error. Deberá corregir el error y repetir la operación.</p>
			<?php else : ?>
			<p>Se han encontrado <?= $errors->num_errores() ?> errores:</p>
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
					<?php if ($row->detalles != null) :?>
						<td style="width:100%;background: #fdfdfd; border: solid 1px #FFEB88;padding:0px 8px;"><?= htmlentities(mb_convert_encoding($row->mensaje,"UTF-8")) ?>
						<?php foreach($row->detalles as $d => $msg_detalle) : ?>
							<br>&nbsp;&nbsp;&nbsp;&nbsp;- <?= htmlentities(mb_convert_encoding($msg_detalle,"UTF-8")) ?>
						<?php endforeach;?>
						</td>
					<?php else: ?>
						<td style="width:100%;background: #fdfdfd; border: solid 1px #FFEB88;padding:0px 8px;"><?= htmlentities(mb_convert_encoding($row->mensaje,"UTF-8")) ?></td>
					<?php endif;?>
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

			?>
        </div> 
        <?php if (isset($es_encuesta_web) && $es_encuesta_web) :?>    
		<div style="padding-top: 6px;float: left;"><a href="<?= $site[PAGE_ALOJA_FORM];?>"><img src="images/volver.png" border="0" class="noprint"></a> <a href="<?= $site[PAGE_ALOJA_FORM];?>" class="enlace noprint" style=" position: relative; top: -2px;">Volver a la encuesta</a></div>
    	<?php else: ?>
    	<?php if (isset($botones) && (($botones & 8)!=0)) :?>
    	<strong><a href="<?= $urlFormAloja; ?>" class="enlace noprint" _style=" position: relative; top: -2px;">Ir al formulario para rellenar personal y precios.</a></strong>
    	<?php endif; ?>
    	<?php endif; ?>
        <input class="search ui-button ui-widget ui-state-default ui-corner-all" name="operationBtn" type="button" value="Imprimir" style="width:95px; background-image: url(images/imprimir.png);background-repeat: no-repeat;background-position: 8px 4px;padding-left:27px;float: right;" role="button" aria-disabled="false">
        <?php if (((!isset($es_encuesta_web)) || ($es_encuesta_web==false)) && (isset($botones)) && ($botones!=0)) :?>
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
<!-- FIN BLOQUE INTERIOR -->