<!-- COMIENZO BLOQUE INTERIOR -->
<?php if(isset($res)): ?>
<div id="bloq_interior">
	<h1 class="titulo_1 noprint" >Envío de archivo de datos</h1>
	<div class="bloq_central">
		<?php if ($res === true) : ?>
		<div class="cuadro fondo_verde noprint" style="text-align: justify;">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Solicitud enviada</h3>
			<div class="subrayado"></div>
			<p>Su solicitud ha sido enviada correctamente y será procesada a la mayor brevedad por el personal del ISTAC.</p>
			<p><b>Muchas gracias por su colaboración.</b></p>
		</div>
        <div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
		<?php else: ?>
		<div class="cuadro" style="text-align: justify; background-color:#FF9393;border-color:#F37575" class="noprint">
			<h3 class="titulo_3 erroricon" style="margin-bottom:4px;">Su solicitud ha fallado</h3>
			<p>Su solicitud no ha sido recibida correctamente. Por favor, vuelva a intentarlo más tarde.</p>
			<p>Si el fallo persiste, contacte con nuestro personal.</p>
			<p><b>Muchas gracias por su colaboración.</b></p>
			<?php if(isset($errorop)): ?><p style="resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em;"><b>ERROR:</b> <?= $errorop ?></p><?php endif; ?>
        </div>		
        <?php endif; ?>
	</div>
</div>
<?php else : ?>
<script type="text/javascript">
var motivosInfo=[];
function selectInit(sel)
{
	var opciones="";
<?php
	if ($motivos->has_rows())
	{
		$ops="";
		foreach ($motivos as $row)
		{
			echo "\tmotivosInfo.push({'id':'".$row['idmot']."', 'desc':'".$row['descmot']."', 'oblig':'".$row['oblig']."', 'ayuda':'".$row['ayuda']."'});\n";
			echo "\topciones+='<option value=\"".$row['idmot']."\"".($row['oblig']=='S' ? " data-detalle=\"true\"":"").">".$row['idmot']." - ".$row['descmot']."</option>';\n";
		}
	}
?>
	sel.innerHTML=opciones;
}
function ayudaMotivo(idmot)
{
	for(var i=0;i<motivosInfo.length;i++)
	{
		if(motivosInfo[i].id==idmot)
			return motivosInfo[i].ayuda;
	}
	return '';
}
$(document).ready( function() {
	selectInit(document.getElementById('motivo_sel'));
	$("#motivo_sel").val([]);
	$("#motivo_sel").change(
			function()
			{
				var elto=$(this).find(":selected");
				var cod=elto.val();
				if(elto.data("detalle"))
					$("#motivo_detalle").attr("disabled",false).focus();
				else
					$("#motivo_detalle").attr("disabled",true);
				var textoAyuda=$("#ayudamot");
				textoAyuda.text(ayudaMotivo(cod));				
				var tmpClass=textoAyuda.attr('class');
				textoAyuda.removeClass();
				setTimeout(function(){textoAyuda.addClass(tmpClass).addClass('start-now');},10);
			}
	);
	$("#btnEnviar").button().click(
		function(event)
		{
			var elto=$("#motivo_sel").find(":selected");
			if(elto.length==0)
			{
				alert("Es obligatorio indicar un motivo para la solicitud de envío de cuestionario vacío.");
				event.preventDefault();
				return;
			}
			var cod=elto.val();
			if(cod!=null)
			{
				var detalle=$("#motivo_detalle").val();
				if(elto.data("detalle") && (detalle==""))
				{
					alert("Es obligatorio indicar el detalle requerido del motivo de la solicitud de envío de cuestionario vacío.");
					event.preventDefault();
				}
			}
		}
	);
  });
</script>
<div id="bloq_interior">
	<h1 class="titulo_1"><?= USER_TITLE ?></h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
		<form name="aloja_nulo" action="#" method="post">
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		<?php if($es_admin) :?>
		  <h2 class="titulo_2">Formulario de solicitud de envío de cuestionario nulo (<?= DateHelper::mes_tostring( $mes_encuesta,'M') ?> de <?= $ano_encuesta ?>)</h2>
		<?php else: ?>
		  <h2 class="titulo_2">Formulario de solicitud de envío de cuestionario nulo</h2>
		<?php endif; ?>
	      <div class="subrayado"></div>
	      <br/>
            <table style="margin-left:10px;width:99%">
				<tr>
				<td style="width:11%;"><label for="motivo_sel">Motivo:</label></td>
				<td><select id="motivo_sel" name="motivo_sel" style="width:90%;"></select></td>
				</tr>
				<tr>
				<td style="width:11%; vertical-align: top"><label for="det_sel">Detalle:</label></td>
				<td>
				<textarea id="motivo_detalle" name="motivo_detalle" disabled style="width:90%; height:130px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em;" placeholder="Describa aquí el motivo por el que no se rellena el cuestionario de alojamiento..."></textarea>
				</td>
				</tr>
				<tr>
				<td style="width:11%;"></td>
				<td><div id="ayudamot" class="pulsante" style="width:90%; background-color: #D3D3D3; padding: 0px 4px 0px 4px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em; font-weight: bold; font-style: italic"></div></td>
				</tr>
				<tr>
				<td style="width:11%;">
				<br/><input style="width:150px;" class="search" id="btnEnviar" type="submit" value="Enviar solicitud">
				</td>
				</tr>
            </table> 
		</div>
		<input type="hidden" name="<?= ARG_OP ?>" value="es"/>
		<?php if($es_admin) :?>
            <input type="hidden" name="<?= ARG_MES_ENCUESTA ?>" value="<?= $mes_encuesta ?>"/>
            <input type="hidden" name="<?= ARG_ANO_ENCUESTA ?>" value="<?= $ano_encuesta ?>"/>
        <?php endif; ?>
		</form>
		<!-- FIN CAJA AMARILLA -->
		
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->
	<span style="margin-left:20px"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></span>


</div>
<?php endif; ?>
<!-- FIN BLOQUE INTERIOR -->