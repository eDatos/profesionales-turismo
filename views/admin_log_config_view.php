<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/dates.js"></script>
<script type="text/javascript">
$(document).ready( function() {
   
	$("#df").validate({
    	errorPlacement: function(error, element) {
        	error.appendTo(element.parent());
        	error.addClass("validmsg");
    	}
	});

	initDatepicker($(".datepicker"), "<?= DateHelper::getDateFormat("datepicker"); ?>");

	$("input[type='button'], #button, input[type='submit']").button();
	$("input[type='checkbox']").click(function() {
		$("#infomsg").hide();
	});
	$( "#dialog-confirm" ).dialog({
    	autoOpen: false,
        resizable: false,
        modal: true,
    	buttons: {
            "Borrar entradas": function() {
            	$( this ).dialog( "close" );
            	$("#df").submit();
            },
            Cancel: function() {
            	$( this ).dialog( "close" );
            }
        }
    });   
    $("#delButton").click(function() {
    	if ($("#df").valid())
    	{
    		$( "#dialog-confirm" ).dialog("open");
    	}
    });
  });
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<div class="bloq_central">
	<h1 class="titulo_1">Configuración del registro de operaciones</h1>
		<?php
				if ($deletedOk && isset($showOkDelMsg) && $showOkDelMsg !== FALSE)
					$page->renderErrorMsg(array($showOkDelMsg." registros eliminados"),"okicon pagemsg_success");
				elseif (!$deletedOk)
					$page->renderErrorMsg(array("La fecha introducida no es correcta"), "erroricon pagemsg_error");
		?>
		<div class="cuadro fondo_gris">
			<h2 class="titulo_2" style="float:left;">Limpiar registro</h2>

			<div class="subrayado"> </div>
			<div id="dialog-confirm" title="Eliminar entradas del registro">
    			<p style="text-align:left;"><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Las entradas serán eliminadas permanentemente.¿Está seguro?</span></p>
			</div>
			<form id="df" action="#" method="post">
				<label for="idFechaBorrar">Eliminar entradas anteriores a la fecha:</label>
				<input placeholder="<?= DateHelper::getDateFormat(1); ?>" autocomplete="off" id="idFechaBorrar" class="datepicker required dateUS" name="beforeDate" type="text"/>
				<input type="hidden" name="<?= ARG_OP ?>" value="del"/>
				<input id="delButton" style="padding:0.2em 0.5em; margin-top:10px;" 
					role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="button" value="Borrar entradas"/>			
			</form>
		</div>
		<form action="#" method="post">
		<div class="cuadro tabla_opciones">
				<h2 class="titulo_2" style="float:left;">Registro de operaciones</h2>
				<?php if ($showOkOptsMsg) :?>
				<span id="infomsg" class="titulo_3 okicon" style="color:#059905; float:right;position:relative;top:5px;">Cambios guardados</span>
				<?php endif; ?>
				<div class="subrayado"> </div>
				<div>
					<h3 class="titulo_3">Cuestionario de Alojamiento</h3>
					<?php foreach($opcionesAloja as $optid => $optne): ?>
					<span style="margin:7px;display:block;">
	                    <input tabindex="6" style="position:relative;top:2px;" id="id_<?= $optid ?>" name="opt[<?= $optid ?>]" type="checkbox" value="1" <?= ($optne[1] == 1)?'checked':''; ?>>
	                    <label style="margin-left:2px;" for="id_<?= $optid ?>"><?= $optne[0]; ?></label>
	                </span>
					<?php endforeach;?>
				</div>
				<div>
				<h3 class="titulo_3">Cuestionario de Expectativas</h3>
					<?php foreach($opcionesExp as $optid => $optne): ?>
					<span style="margin:7px;display:block;">
	                    <input tabindex="6" style="position:relative;top:2px;" id="id_<?= $optid ?>" name="opt[<?= $optid ?>]" type="checkbox" value="1" <?= ($optne[1] == 1)?'checked':''; ?>>
	                    <label style="margin-left:2px;" for="id_<?= $optid ?>"><?= $optne[0]; ?></label>
	                </span>
					<?php endforeach;?>				
				</div>
				<div>
				<h3 class="titulo_3">Otras opciones</h3>
					<?php foreach($opcionesOtras as $optid => $optne): ?>
					<span style="margin:7px;display:block;">
	                    <input tabindex="6" style="position:relative;top:2px;" id="id_<?= $optid ?>" name="opt[<?= $optid ?>]" type="checkbox" value="1" <?= ($optne[1] == 1)?'checked':''; ?>>
	                    <label style="margin-left:2px;" for="id_<?= $optid ?>"><?= $optne[0]; ?></label>
	                </span>
					<?php endforeach;?>				
				</div>
		</div>	
		<input type="hidden" name="<?= ARG_OP ?>" value="save"/>
		<input type="submit" value="Guardar configuración" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false"/>
		</form>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->