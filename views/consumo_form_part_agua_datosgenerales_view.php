<script type="text/javascript" src="js/dates.js"></script>
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
   display: block;
   width: max-content !important;
   margin-left: 215px;
}
</style>
<script type="text/javascript">
function setupValidacion()
{
	jQuery.validator.setDefaults({
		  //debug: true,
		  //success: "valid",
		  ignoreTitle: true
		});
	jQuery.validator.addMethod(
        "nofutura",
        function (value, element) {
            if(value=='')
                return true;
            var hoy=new Date();
            return parseDate(value) <= hoy;
        },
        "La fecha inicial no puede ser posterior a la actual"
    );
    
	jQuery.validator.addMethod(
        "rangofechas",
        function (value, element, params) {
            if((value=='')||($(params).val()==''))
                return true;
            
            return parseDate(value) >= parseDate($(params).val());
        },
        "La fecha final no puede ser posterior a la inicial"
    );

	$("#formdata").validate({
		rules: {
			'<?= VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>': {
				required: true,
				nofutura: true
			},
			'<?= VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL ?>': {
				required: true,
				rangofechas: "#<?= VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>"
			},
			'<?= VAR_FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO ?>': {
				required: true
			}
		}
	});
}

$(document).ready( function() {
	$.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
	$(".datepicker").datepicker( { dateFormat: "<?= DateHelper::getDateFormat("datepicker") ?>" } );

	$("input[type='button']").button();
	
	// Validación formulario
	setupValidacion();

	$('#botonenviar').click(function(event) {
	 	if($('#formdata').valid()==false)
	 	{
	 		alert('Existen errores en los datos.\nPor favor, corrija los errores antes de continuar.');
	 		event.preventDefault();
	 		return false;
	 	}
		$("#formdata").submit();
	});
});

</script>
<?php if (isset($errores)) : ?>
    <div class="pagemsg_error">
        <span id="infomsg" class="titulo_3 erroricon">Errores</span>
        <img id="ampliar" class="botoneraderecha alineado" src="images/detalles.png"/>
        <div id="detalleErrores" style="display:none"><ul>
        <?php foreach ($errores as $error): ?>
        	<li><?= $error ?></li>
    	<?php endforeach; ?>
        </ul></div>
    </div>
<?php endif; ?>

<div style="clear: both;"/>
<div id="formulario" class="formularioParcial">
	<h2 class="titulo_2">Factura Nº: <b><?= $NumeroFactura ?></b>
	<?php
      	if(isset($iconoSuministro))
      	{
      	    echo '<img class="iconoSuministro" src="images/consumos/'.$iconoSuministro->desc_corta.'" alt="'.$iconoSuministro->desc_larga.'" title="'.$iconoSuministro->desc_larga.'"/>';
      	}
    ?>
    </h2>
	
	<h3 class="titulo_2 titulo_seccion">DATOS GENERALES<div class="paso">Paso <?= $parte ?> de <?= $numpartes ?></div></h3>
	<div style="padding: 4px;">
    <form method="post" action="<?= $urlNext ?>" name="formdata" id="formdata">
    	<fieldset>
            <span><b>Periodo de facturación:</b> </span>
            <label for="<?= VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>">Desde </label>
            <input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" id="<?= VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>" name="<?= VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>" type="text" value="<?= ($FechaInicioPeriodo!=null) ? $FechaInicioPeriodo:'' ?>" style="width:90px" _required="required"/>
            <label for="<?= VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL ?>">Hasta </label>
            <input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" id="<?= VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL ?>" name="<?= VAR_FACTURA_AGUA_DATOS_GENERALES_PERIODO_FECHA_FINAL ?>" type="text" value="<?= ($FechaFinalPeriodo!=null) ? $FechaFinalPeriodo:'' ?>" style="width:90px" _required="required"/>
	        <label for="<?= VAR_FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO ?>">Referencia Nº de contrato: </label>
	        <input type="text" name="<?= VAR_FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO ?>" id="<?= FACTURA_AGUA_DATOS_GENERALES_REFERENCIA_CONTRATO ?>" value="<?= $ReferenciaContrato ?>" _required="required">
    	</fieldset>
        <div class="botonera"><div class="botonSiguiente"><input type="button" id="botonenviar" value="Siguiente" type="submit"></div></div>
        <div style="clear: both;"/>
    </form>
    </div>
</div>
<div id="dialogo_aviso" title="Modificar factura" >
	<div id="msg_errores" style="text-align: left"></div>
</div>
