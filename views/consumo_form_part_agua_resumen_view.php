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
        function (value, element, param) {
            var totalPagar=toFloat(value);
            var importeTotal=toFloat($(param).val());
            return (totalPagar>=importeTotal);
        },
        "La cantidad total a pagar debe ser igual o superior al importe total"
    );

	$("#formdata").validate({
		rules: {
			'<?= VAR_FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE ?>': {
				required: true
			},
			'<?= VAR_FACTURA_AGUA_RESUMEN_TOTAL_PAGAR ?>': {
				required: true,
				total: '#<?= VAR_FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE ?>'
			}
		}
	});
}

$(document).ready( function() {
	$("input[type='button']").button();
	setupValidacion();

	$('#botonenviar').click(function(event) {
    	$('.condecimales').each(function() {
    		var valor=$(this).val();
    		if(valor.endsWith(','))
    			$(this).val(valor.slice(0,-1));
    		else
    		{
        		if(valor.startsWith(','))
        		{
        			valor='0'+valor;
        			if(this.maxLength>=0)
        			{
            			valor=valor.slice(0,this.maxLength);
        			}
        			$(this).val(valor);
        		}
        	}
    	});
	 	if($('#formdata').valid()==false)
	 	{
	 		alert('Existen errores en los datos.\nPor favor, corrija los errores antes de continuar.');
	 		event.preventDefault();
	 		return false;
	 	}
		$("#formdata").submit();
	});

	$('#botonanterior').click(function(event) {
		window.location='<?= $urlPrev ?>';
	});
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
            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE ?>">Importe del consumo: </label>
	        <input type="text" class="numero condecimales" data-numdecimales="2" maxlength="9" title="Importe total de la factura antes de impuestos" name="<?= VAR_FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE ?>" id="<?= VAR_FACTURA_AGUA_RESUMEN_TOTAL_IMPORTE ?>" value="<?= isset($TotalImporteFactura)?$TotalImporteFactura:'' ?>" _required="required">
            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_RESUMEN_TOTAL_PAGAR ?>">Total a pagar: </label>
	        <input type="text" class="numero condecimales" data-numdecimales="2" maxlength="9" title="Importe total de la factura a pagar" name="<?= VAR_FACTURA_AGUA_RESUMEN_TOTAL_PAGAR ?>" id="<?= VAR_FACTURA_AGUA_RESUMEN_TOTAL_PAGAR ?>" value="<?= isset($TotalPagar)?$TotalPagar:'' ?>" _required="required">
    	</fieldset>
        <div class="botonera"><div class="botonAnterior"><input type="button" id="botonanterior" value="Anterior"></div>
        <div class="botonSiguiente"><input type="button" id="botonenviar" value="Enviar"></div>
        </div>
        <div style="clear: both;"/>
    </form>
    </div>
</div>
