<style>
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
   margin-left: 215px;
   height: inherit;
}
</style>
<script type="text/javascript">

// Hay que usar delegación de eventos para los nuevos elementos cargados dinámicamente.
// https://stackoverflow.com/questions/16598213/how-to-bind-events-on-ajax-loaded-content

function validarForm()
{
	return true;
}

$(document).on("click", '#botonenviar', function(event) { 
	if(!validarForm())
		event.preventDefault();
	$("#formdata").submit();
});

$(document).on("click", '#botonanterior', function(event) {
	window.location.href="<?= $urlPrev ?>";
});

function toFloat(cantidad)
{
	try
	{
		return parseFloat(cantidad.replace(',','.'));
	}catch(e)
	{
	}
	return 0.0;
}

function setupValidacion()
{
	jQuery.validator.setDefaults({
		  //debug: true,
		  //success: "valid",
		  ignoreTitle: true
		});
	jQuery.validator.addMethod(
        "total",
        function (value, element) {
            var total=toFloat(value);
            var importePotencia=toFloat($('#<?= VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL ?>').val());
            var importeEnergia=toFloat($('#<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL ?>').val());
            return (total>=(importePotencia+importeEnergia));
        },
        "La cantidad total debe ser igual o superior a la suma del importe de potencia y energía consumidas"
    );

	$("#formdata").validate({
		rules: {
			'<?= VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL ?>': {
				required: true
			},
			'<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL ?>': {
				required: true
			},
			'<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR ?>': {
				required: true,
				total: true
			}
		}
	});
}

$(document).ready( function() {
	$("input[type='button']").button();
	setupValidacion();
});
</script>
<script type="text/javascript" src="js/inputfields.js"></script>
<div id="formulario" class="formularioParcial">
	<h2 class="titulo_2">Factura Nº: <b><?= $NumeroFactura ?></b>
	<?php
      	if(isset($iconoSuministro))
      	{
      	    echo '<img class="iconoSuministro" src="images/consumos/'.$iconoSuministro->desc_corta.'" alt="'.$iconoSuministro->desc_larga.'" title="'.$iconoSuministro->desc_larga.'"/>';
      	}
    ?>
    </h2>

	<h3 class="titulo_2 titulo_seccion">RESUMEN DE FACTURACIÓN<div class="paso">Paso <?= $parte ?> de <?= $numpartes ?></div></h3>
	<div style="padding: 4px;">
    <form method="post" action="<?= $urlNext ?>" id="formdata">
    	<fieldset>
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL ?>">Importe Potencia (€): </label>
	        <input type="text" class="numero condecimales" name="<?= VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL ?>" id="<?= VAR_FACTURA_ELECTRICIDAD_POTENCIA_FACTURADA_IMPORTE_TOTAL ?>" value="<?= isset($TotalImportePotenciaFacturada)?$TotalImportePotenciaFacturada:'' ?>" _required="required">
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL ?>">Importe Energía (€): </label>
	        <input type="text" class="numero condecimales" name="<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL ?>" id="<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_IMPORTE_TOTAL ?>" value="<?= isset($TotalImporteEnergiaFacturada)?$TotalImporteEnergiaFacturada:'' ?>" _required="required">
	        <?php /*
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA ?>">Importe Energía: </label>
	        <input type="text" name="<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA ?>" id="<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_ENERGIA ?>" value="<?= isset($TotalImporteEnergia)?$TotalImporteEnergia:'' ?>" _required="required">
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS ?>">Servicios y otros conceptos: </label>
	        <input type="text" name="<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS ?>" id="<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_SERVICIOS_Y_OTROS ?>" value="<?= isset($TotalImporteServicios)?$TotalImporteServicios:'' ?>" _required="required">
	        */?>
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR ?>">Total a pagar (€): </label>
	        <input type="text" class="numero condecimales" name="<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR ?>" id="<?= VAR_FACTURA_ELECTRICIDAD_RESUMEN_TOTAL_PAGAR ?>" value="<?= isset($TotalPagar)?$TotalPagar:'' ?>" _required="required">
    	</fieldset>
    	<div class="botonera">
        <div class="botonAnterior"><input type="button" id="botonanterior" value="Anterior"></div>
        <div class="botonSiguiente"><input type="button" id="botonenviar" value="Enviar"></div>
        </div>
        <div style="clear: both;"/>
    </form>
    </div>
</div>
