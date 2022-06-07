<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#selgrupo").change(function() {
		$("#opi").val("chggrp");
		$("#df").submit();
	});

	$("#df").validate({ errorPlacement: function(error, element) {
					    	error.appendTo(element.parent().next());
					    	error.addClass("validmsg");
						}
	});

	$("#pl1").rules("add", { number: true, range: [1,29] });
	$("#pl2").rules("add", { number: true, range: [1,31] });
	$("#pl3").rules("add", { number: true, range: [1,30] });
	$("#pl4").rules("add", { number: true, range: [1,31] });
	$("#pl5").rules("add", { number: true, range: [1,30] });
	$("#pl6").rules("add", { number: true, range: [1,31] });
	
	$("#pl7").rules("add", { number: true, range: [1,31] });
	$("#pl8").rules("add", { number: true, range: [1,30] });
	$("#pl9").rules("add", { number: true, range: [1,31] });
	$("#pl10").rules("add", { number: true, range: [1,30] });
	$("#pl11").rules("add", { number: true, range: [1,31] });
	$("#pl12").rules("add", { number: true, range: [1,31] });

	<?php if ($show_ok) :?>
	$("#divinfomsg").fadeOut(2500);
	<?php endif; ?>
});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1">Configuración de plazos de encuesta de alojamiento</h1>
	<div class="bloq_central">
		<?php if ($show_ok) :?>
		<div id="divinfomsg">
        <div class="cuadro fondo_verde" style="padding:0px;">
			<span id="infomsg" class="titulo_3 okicon" style="color:#059905;">Cambios guardados</span>
        </div>
        </div>
        <?php elseif (isset($errs)) :?>
        <div class="cuadro fondo_rojo" style="padding:0px;color:#B60000;">
        	<?php foreach($errs as $err_msg): ?>
			<div class="titulo_3 erroricon"><?= $err_msg ?></div>
			<?php endforeach; ?>
        </div>        
		<?php endif; ?>		
        <div class="cuadro fondo_gris">
		<form id="df" action="#" method="post">
	  		<input id="opi" type="hidden" name="<?= ARG_OP ?>" value="save"/>
			<h2 class="titulo_2" style="float:left;">Plazos por grupo de establecimiento y mes</h2>
			<div class="subrayado"> </div>
            <table style="margin-left:10px;width:99%">
				<tr>
				<td style="width:21%;"><label for="selgrupo">Grupo de establecimiento:</label></td>
				<td><select name="grupo" id="selgrupo">
				<?php
					foreach ($grupos as $grp)
					{
						echo "<option value='{$grp['id']}' ". (isset($grp['sel'])?"selected=selected":"")  .">{$grp['nombre']}</option>";
					} 
				?>
				</select></td>
				</tr>
				<tr>
				<td><label for="trim1_fin">Plazos por mes:</label></td>
				<td>
					<table>
						<tr>
						<th style="width:10%;text-align:left;">Mes de la encuesta</th>
						<th style="width:15%;text-align:left;">Cierre de plazo</th>
						<th style="width:30%"></th>
						</tr>
						<?php for($i=1;$i<=12;$i++): ?>
						<tr>
							<td><?= DateHelper::mes_tostring($i,'M')?></td>
							<td><input type="hidden" name="orig_pl[<?= $i ?>]" <?= isset($plazos[$i])?"value='{$plazos[$i]}'":"" ?>>
							<input type="text" style="text-align:right;"  maxlength="2" size="2" id="pl<?= $i ?>" name="pl[<?= $i ?>]" <?= isset($plazos[$i])?"value='{$plazos[$i]}'":"" ?>>de <?= DateHelper::mes_tostring(($i)%12 +1, 'm') ?></td>
							<td></td>
						</tr>
						<?php endfor; ?>
					</table>
				</td>
				</tr>
				<tr>
            </table>
			<input style="padding:0.2em 0.5em; margin-top:10px;" 
				class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" value="Guardar"/>
			<input style="padding:0.2em 0.5em; margin-top:10px;" 
				class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="reset" value="Reiniciar"/>
		</form>
		</div>  
		<div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->