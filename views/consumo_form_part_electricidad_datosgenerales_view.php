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
#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>,#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE ?>,#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?> {
    width: 450px;
}
#formdata label {
    width: 300px;
}
#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_NUMERO_CONTRATO ?>,#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS ?> {
    width: 180px;
}
</style>
<script type="text/javascript">
function listaComercializadorasInit()
{
	var select=document.getElementById('<?= VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE ?>');
	//$('#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE ?>').empty();
	var listaOpciones="";
	listaOpciones+='<option disabled selected value> -- seleccione una comercializadora -- </option>';
	<?php
	   foreach ($comercializadoras as $comercializadora)
	   {
	       echo 'listaOpciones+=\'<option value="'.$comercializadora->codigo.'"'.($comercializadora->seleccionado?' selected="selected"':'').'>'.addslashes($comercializadora->desc_corta).'</option>\';'.PHP_EOL;
	   }
	?>
	select.innerHTML=listaOpciones;
}

<?php /*
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
*/?>

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
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>': {
				required: true,
				nofutura: true
			},
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL ?>': {
				required: true,
				rangofechas: "#<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>"
			},
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS ?>': {
				required: true
			},
			/*
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO ?>': {
				required: true
			},*/
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE ?>': {
				required: true
			},
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>': {
				required: true
			}
		},
		messages: {
			'<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>': {
				required: 'Debe indicar el peaje de acceso'
			}
		}
	});
}

$(document).ready( function() {
	$.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
	$(".datepicker").datepicker( { dateFormat: "<?= DateHelper::getDateFormat("datepicker") ?>" } );
	$("input[type='button']").button();
	
	listaComercializadorasInit();
	//listaDistribuidorasInit();
	listaPeajesInit();

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
            <label for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>">Desde </label>
            <input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_INICIO ?>" type="text" value="<?= ($FechaInicioPeriodo!=null) ? $FechaInicioPeriodo:'' ?>" style="width:90px" _required="required"/>
            <label for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL ?>">Hasta </label>
            <input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL ?>" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_PERIODO_FECHA_FINAL ?>" type="text" value="<?= ($FechaFinalPeriodo!=null) ? $FechaFinalPeriodo:'' ?>" style="width:90px" _required="required"/>
<?php /*
	        <label for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO ?>">Referencia Nº de contrato: </label>
	        <input type="text" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO ?>" id="<?= FACTURA_ELECTRICIDAD_DATOS_GENERALES_REFERENCIA_CONTRATO ?>" value="<?= $ReferenciaContrato ?>" _required="required">
*/?>
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS ?>">Identificación del punto de suministro (CUPS): </label>
	        <input type="text" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS ?>" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_CUPS ?>" value="<?= isset($Cups)?$Cups:'' ?>" _required="required">
<?php /*
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>">Empresa distribuidora: </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_DISTRIBUIDORA ?>"></select>
*/?>
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE ?>">Empresa comercializadora: </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE ?>" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_COMERCIALIZADORA_NOMBRE ?>"></select>
            <label _style="width: 300px;" for="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>">Peaje de acceso a la red (ATR): </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>" name="<?= VAR_FACTURA_ELECTRICIDAD_DATOS_SUMINISTRO_PEAJE_ACCESO ?>"></select>
    	</fieldset>
        <div class="botonera">
        <div class="botonSiguiente"><input type="button" id="botonenviar" value="Siguiente"></div>
        </div>
        <div style="clear: both;"/>
    </form>
    </div>
</div>
<div id="dialogo_aviso" title="Modificar factura" >
	<div id="msg_errores" style="text-align: left"></div>
</div>
