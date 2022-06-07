<style>
#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>,#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?> {
    width: 450px;
}
#formdata label {
    width: 300px;
}
#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO ?>,#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS ?> {
    width: 180px;
}
.botonera {
    /*margin-top: 10px;*/
}
.botonera input[type=button] {
    font-weight: bold;
}
.titulo_seccion {
    padding-left: 10px;
}
.paso {
    float: right;
    margin-right: 20px;
}
label.error {
   float: none;
   color: red;
   padding-left: .5em;
   vertical-align: middle;
   text-align: left important!;
   display: block !important;
   width: max-content !important;
   margin-left: 315px;
   height: inherit;
}
</style>
<script type="text/javascript">
function listaDistribuidorasInit()
{
	var select=document.getElementById('<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>');
	//$('#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>').empty();
	var listaOpciones="";
	listaOpciones+='<option disabled selected value> -- seleccione una distribuidora -- </option>';
	<?php
	   foreach ($distribuidoras as $distribuidora)
	   {
	       echo 'listaOpciones+=\'<option value="'.$distribuidora->codigo.'"'.($distribuidora->seleccionado?' selected="selected"':'').'>'.addslashes($distribuidora->desc_corta).'</option>\';'.PHP_EOL;
	   }
	?>
	select.innerHTML=listaOpciones;
}

function listaPeajesInit()
{
	var select=document.getElementById('<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>');
	//$('#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>').empty();
	var listaOpciones="";
	listaOpciones+='<option disabled selected value> -- seleccione un peaje de acceso -- </option>';
	<?php
	   foreach ($peajes as $peaje)
	   {
	       echo 'listaOpciones+=\'<option value="'.$peaje->codigo.'"'.($peaje->seleccionado?' selected="selected"':'').'>'.addslashes($peaje->desc_corta).'</option>\';'.PHP_EOL;
	   }
	?>
	select.innerHTML=listaOpciones;
}

// Hay que usar delegación de eventos para los nuevos elementos cargados dinámicamente.
// https://stackoverflow.com/questions/16598213/how-to-bind-events-on-ajax-loaded-content

function validarForm()
{
	return true;
}

function setupValidacion()
{
	jQuery.validator.setDefaults({
		  //debug: true,
		  //success: "valid",
		  ignoreTitle: true
		});

	$("#formdata").validate({
		rules: {
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>': {
				required: true
			},
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>': {
				required: true
			}
		},
		messages: {
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>': {
				required: 'Debe indicar la empresa distribuidora'
			},
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>': {
				required: 'Debe indicar el peaje de acceso'
			}
		}
	});
}

$(document).on("click", '#botonenviar', function(event) { 
	if(!validarForm())
		event.preventDefault();
	$("#formdata").submit();
});

$(document).on("click", '#botonanterior', function(event) {
	window.location.href="<?= $urlPrev ?>";
});

$(document).ready( function() {
	listaDistribuidorasInit();
	listaPeajesInit();
	setupValidacion();
	$.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
	$(".datepicker").datepicker( { dateFormat: "<?= DateHelper::getDateFormat("datepicker") ?>" } );
	$("input[type='button']").button();
});
</script>
<div id="formulario" class="formularioParcial">
	<h2 class="titulo_2">Factura Nº: <b><?= $NumeroFactura ?></b>
	<?php
      	if(isset($iconoSuministro))
      	{
      	    echo '<img class="iconoSuministro" src="images/consumos/'.$iconoSuministro->desc_corta.'" alt="'.$iconoSuministro->desc_larga.'" title="'.$iconoSuministro->desc_larga.'"/>';
      	}
    ?>
    </h2>

	<h3 class="titulo_2 titulo_seccion">DATOS RELACIONADOS CON EL SUMINISTRO<div class="paso">Paso <?= $parte ?> de <?= $numpartes ?></div></h3>
	<div style="padding: 4px;">
    <form method="post" action="<?= $urlNext ?>" id="formdata">
    	<fieldset>
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>">Empresa distribuidora: </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>"></select>
            <?php /*
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO ?>">Número de contrato de acceso: </label>
	        <input type="text" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO ?>" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO ?>" value="<?= isset($NumeroContrato)?$NumeroContrato:'' ?>" _required="required">	        
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS ?>">Identificación del punto de suministro (CUPS): </label>
	        <input type="text" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS ?>" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS ?>" value="<?= isset($Cups)?$Cups:'' ?>" _required="required">
	        */?>
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>">Peaje de acceso a la red (ATR): </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>"></select>
            <?php /*
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO ?>">Fecha de vencimiento del contrato de acceso: </label>
            <input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO ?>" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_VENCIMIENTO_CONTRATO ?>" value="<?= isset($Vencimiento) ? $Vencimiento->format('d/m/Y'):'' ?>" type="text" style="width:90px" _required="required"/>
            */?>
    	</fieldset>
    	<div class="botonera">
        <div class="botonAnterior"><input type="button" id="botonanterior" value="Anterior"></div>
        <div class="botonSiguiente"><input type="button" id="botonenviar" value="Siguiente"></div>
        </div>
        <div style="clear: both;"/>
    </form>
    </div>
</div>
