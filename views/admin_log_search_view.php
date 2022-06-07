<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/dates.js"></script>
<script type="text/javascript">
$(document).ready( function() {
	//alert('Hola');

	$("#df").validate({
		rules: {
			to: {
				mayorque: "#from"
			}
		},
    	errorPlacement: function(error, element) {
        	error.appendTo(element.parent());
        	error.addClass("validmsg");
    	}
	});
	//$.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
	initDatepicker($(".datepicker"), "<?= DateHelper::getDateFormat("datepicker"); ?>");
	
	$("input[type='button'],#btnuser,#button, input[type='submit']").button();
	$("input[name='usertype']").click(uus);
	uus();
	$("#btnuser").click( function() {
		$("#df").attr('action','#').submit();
	});

  });

<?php /* Deshabilita el boton para seleccionar usuario y la caja que muestra el nombre si se seleccion "Para todos" */ ?>
function uus()
{
	if ($("input[name='usertype']:checked").val() == 'TODOS')
	{
		$("#btnuser").button('disable');
		$("#idusuario").attr('disabled','disabled');
	}
	else 
	{
		$("#btnuser").button('enable');
		$("#idusuario").removeAttr('disabled');
	}
}
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<div class="bloq_central">
		<h1 class="titulo_1">Consulta del registro</h1>
		<div class="cuadro fondo_gris">
			<h2 class="titulo_2" style="float:left;">Criterios de búsqueda</h2>
			<div class="subrayado"> </div>
			<form id="df" action="<?=$actionUrl ?>" method="post">
				<input type="hidden" name="<?= ARG_OP ?>" value="searchuser"/>
				<table style="margin-left:10px;width:99%">
					<tr>
					<td style="width:21%;"><label for="idFechaDesde">Desde el día:</label></td>
					<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" id="from" class="datepicker required dateUS" maxlength="10" size="15" name="from" type="text" value="<?= @$defbegindate ?>"/></td>
					</tr>
					<tr>
					<td><label for="idFechaHasta">Hasta el día:</label></td>
					<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" id="to" class="datepicker required dateUS" maxlength="10" size="15" name="to" type="text" value="<?= @$defenddate ?>"/></td>
					</tr>
				    <tr>
					<td>Para todos los usuarios:</td>
					<td><input style="position:relative;top:1px;" type="radio" name="usertype" value='TODOS' <?= ($defusertype == 'TODOS')?'checked':''; ?> ></td>
					</tr>
				    <tr>
					<td>Sólo para el usuario:</td>
					<td><input style="position:relative;top:1px;" type="radio" name="usertype" value='SEL' <?= ($defusertype == 'SEL')?'checked':''; ?> >
					<input type="text" id="idusuario" name="foruser" readonly value="<?= @$defuser ?>" size="32" placeholder="ningún usuario seleccionado" />
					<input type="button" id="btnuser" value="Seleccionar usuario" style="padding: 0.1em 0.8em;font-size: 13px;margin-top: 0px;" role="button" aria-disabled="false"/>
						
					</tr>					
				    <tr>
					<td><label for="idtipoaccion">Sólo para el tipo de acción:</label></td>
					<td>
					<select name='foraction'>
					<option value=''>TODAS LAS ACCIONES</option>
					<?php
						foreach($actiontypes as $acttype)
						{
							echo "<option value='".$acttype['id']."' ";
							if ($acttype['id'] == $defacttype)
								echo "selected";
							echo ">".$acttype['descripcion']."</option>";							
						}  
					?>	
					</select>
					</td>
					</tr>
				</table>
					<input style="padding:0.2em 0.5em; margin-top:10px;" 
						role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="Realizar consulta"/>
			</form>
		</div>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->