<style>
#formdata label {
    width: 300px;
}
<?php /*
#<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA ?>,#<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE ?> {
    width: 450px;
}
#<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA ?>,#<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR ?> {
    width: 180px;
}
*/?>
<?php /*
#<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA ?> {
    width: 450px;
}
*/?>
#<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR ?> {
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
label.chkbox {
   vertical-align: middle;
   width: max-content !important;
   height: inherit;
   clear: unset;
   margin-left: unset;
}
input.chkbox {
   width: max-content !important;
   margin-left: 10px;
}
#consumoCalculado {
    height: 20px;
    width: 180px;
    /*border: 1px solid #000;*/
    margin-top: 10px;
    float: left;
}
</style>
<script type="text/javascript">
<?php /*
function listaDistribuidorasInit()
{
	var select=document.getElementById('<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA ?>');
	//$('#<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA ?>').empty();
	var listaOpciones="";
	listaOpciones+='<option disabled selected value> -- seleccione una distribuidora -- </option>';
	<?php
	   foreach ($distribuidoras as $distribuidora)
	   {
	       echo 'listaOpciones+=\'<option value="'.addslashes($distribuidora->codigo).'"'.($distribuidora->seleccionado?' selected="selected"':'').'>'.addslashes($distribuidora->desc_corta).'</option>\';'.PHP_EOL;
	   }
	?>
	select.innerHTML=listaOpciones;
}

function listaCalibresInit()
{
	var select=document.getElementById('<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE ?>');
	//$('#<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE ?>').empty();
	var listaOpciones="";
	<?php
	foreach ($calibres as $calibre)
	   {
	       echo 'listaOpciones+=\'<option value="'.addslashes($calibre->codigo).'"'.($calibre->seleccionado?' selected="selected"':'').'>'.addslashes($calibre->desc_corta).'</option>\';'.PHP_EOL;
	   }
	?>
	select.innerHTML=listaOpciones;
}

function listaTiposSuministroInit()
{
	var select=document.getElementById('<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA ?>');
	var listaOpciones="";
	<?php
	foreach ($tiposSuministros as $tSuministro)
	   {
	       echo 'listaOpciones+=\'<option value="'.addslashes($tSuministro->codigo).'"'.($tSuministro->seleccionado?' selected="selected"':'').'>'.addslashes($tSuministro->desc_corta).'</option>\';'.PHP_EOL;
	   }
	?>
	select.innerHTML=listaOpciones;
}

function listaActividadesInit()
{
	var select=document.getElementById('<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD ?>');
	var listaOpciones="";
	<?php
	foreach ($actividades as $actividad)
	   {
	       echo 'listaOpciones+=\'<option value="'.addslashes($actividad->codigo).'"'.($actividad->seleccionado?' selected="selected"':'').'>'.addslashes($actividad->desc_corta).'</option>\';'.PHP_EOL;
	   }
	?>
	select.innerHTML=listaOpciones;
}

function listaTarifasInit()
{
	var select=document.getElementById('<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA ?>');
	var listaOpciones="";
	<?php
	foreach ($tarifas as $tarifa)
	   {
	       echo 'listaOpciones+=\'<option value="'.addslashes($tarifa->codigo).'"'.($tarifa->seleccionado?' selected="selected"':'').'>'.addslashes($tarifa->desc_corta).'</option>\';'.PHP_EOL;
	   }
	?>
	select.innerHTML=listaOpciones;
}

function listaTiposContadorInit()
{
	var select=document.getElementById('<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR ?>');
	var listaOpciones="";
	<?php
	foreach ($tiposContador as $tipo)
	   {
	       echo 'listaOpciones+=\'<option value="'.addslashes($tipo->codigo).'"'.($tipo->seleccionado?' selected="selected"':'').'>'.addslashes($tipo->desc_corta).'</option>\';'.PHP_EOL;
	   }
	?>
	select.innerHTML=listaOpciones;
}
*/?>

function initListas()
{
	<?php /*
	listaDistribuidorasInit();
	listaCalibresInit();
	listaTiposSuministroInit();
	listaActividadesInit();
	listaTarifasInit();
	listaTiposContadorInit();
	*/?>
}

function setupValidacion()
{
	jQuery.validator.setDefaults({
		  //debug: true,
		  //success: "valid",
		  ignoreTitle: true
		});
	jQuery.validator.addMethod(
        "rangolecturas",
        function (value, element, params) {
        	var hasta=parseFloat(value.replace(',','.'));
        	var desde=$(params).val().replace(',','.');
        	if((!isNaN(desde))&&(desde.length!==0))
        	{
        		desde=parseFloat(desde);
        		if(hasta>=desde)
        		{
        			$('#overflow').prop('checked', false);
        			$('.chkbox').hide();
        			return true;
        		}
        		$('.chkbox').show();
        		if($('#overflow').is(':checked'))
        		{
            		return true;
        		}
        	}
        	return false;
        },
        "La lectura actual es menor que la anterior (marque la casilla si es necesario)"
    );

	$("#formdata").validate({
		rules: {
			<?php /*
			'<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA ?>': {
				required: true
			},
			*/ ?>
			'<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR ?>': {
				required: true
			},
			'<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR ?>': {
				required: true//,
				//number: true
			},
			'<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL ?>': {
				required: true,
				//number: true,
				rangolecturas: "#<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR ?>"
			},
			'<?= VAR_TIPO_LECTURA_CONTADOR ?>': {
				required: true
			}
		},
		messages: {
			<?php /*
			'<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA ?>': {
				required: 'Debe indicar la empresa distribuidora'
			},
			*/ ?>
			'<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR ?>': {
				required: 'Debe indicar el número de contador'
			},
			'<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR ?>': {
				required: 'Debe indicar el valor de la lectura anterior'
			},
			'<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL ?>': {
				required: 'Debe indicar el valor de la lectura actual'
			},
			'<?= VAR_TIPO_LECTURA_CONTADOR ?>': {
				required: 'Debe indicar si las lectura es real o estimada'
			}
		},
		errorPlacement: function(error, element) {
			if(element.attr('name')=='<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL ?>')
				error.appendTo(element.parent());
			else
				error.insertAfter(element);
    	}
	});
}

function calcularComplemento(valor)
{
	var max=''+Math.trunc(valor);
	var capacidadContador=parseFloat('1'+('0'.repeat(max.length)));
	return (capacidadContador-valor);
}

function actualizarConsumoCalculado()
{
	var lectAnterior=parseFloat($('#<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR ?>').val().replaceAll(',','.'));
	if(!isNaN(lectAnterior))
	{
		var lectActual=parseFloat($('#<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL ?>').val().replaceAll(',','.'));
		if(!isNaN(lectActual))
		{
			if(lectAnterior>lectActual)
			{
				var max=''+Math.trunc(lectAnterior);
				var capacidadContador=parseFloat('1'+('0'.repeat(max.length)));
				$('#consumoCalculado').html((calcularComplemento(lectAnterior)+lectActual).toFixed(2).replaceAll('.',',')+" M<sup>3</sup>");
			}
			else
				$('#consumoCalculado').html((lectActual - lectAnterior).toFixed(2).replaceAll('.',',')+" M<sup>3</sup>");
			return;
		}
	}
	$('#consumoCalculado').html("N/A");
}

$(document).ready( function() {
	$("input[type='button']").button();
	initListas();

	setupValidacion();
	$('.chkbox').hide();

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
		window.location.href="<?= $urlPrev ?>";
	});

	$('#overflow').change(function(){$('#<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL ?>').valid()});

	$('#<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR ?>, #<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL ?>').change(function(){
		actualizarConsumoCalculado();
	});

	actualizarConsumoCalculado();
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

	<h3 class="titulo_2 titulo_seccion">DATOS RELACIONADOS CON EL SUMINISTRO<div class="paso">Paso <?= $parte ?> de <?= $numpartes ?></div></h3>
	<div style="padding: 4px;">
    <form method="post" action="<?= $urlNext ?>" id="formdata">
    	<fieldset>
			<?php /*
            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA ?>">Empresa distribuidora: </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA ?>" name="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_EMPRESA ?>" title="Empresa distribuidora de agua de abastecimiento"></select>
			*/?>

			<?php /*
            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA ?>">Póliza: </label>
	        <input type="text" name="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA ?>" id="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_POLIZA ?>" value="<?= isset($NumeroContrato)?$NumeroContrato:'' ?>" title="Número de póliza" _required="required">
			*/?>

            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR ?>">Número de contador: </label>
	        <input type="text" name="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR ?>" id="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_NUMERO_CONTADOR ?>" value="<?= isset($NumeroContador)?$NumeroContador:'' ?>" title="Número identificador del contador" _required="required">

			<?php /*
            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE ?>">Calibre: </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE ?>" name="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CALIBRE ?>" title="Diámetro de la tubería de suministro (en mm.)"></select>

            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR ?>">Tipo de contador: </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR ?>" name="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TIPO_CONTADOR ?>" title="Tipo de contador de suministro"></select>
			*/?>

			<!-- <fieldset>
			<legend>Consumo</legend>-->
            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR ?>">Lectura anterior (<?= $FechaPeriodoInicial; ?>): </label>
	        <input type="text" class="numero condecimales" data-numdecimales="2" maxlength="9" name="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR ?>" id="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ANTERIOR ?>" value="<?= isset($LecturaAnterior)?$LecturaAnterior:'' ?>" title="Lectura del contador al final del periodo facturado anterior" _required="required">

            <div>
            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL ?>">Lectura actual (<?= $FechaPeriodoFinal; ?>): </label>
	        <input type="text" class="numero condecimales" data-numdecimales="2" maxlength="9" name="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL ?>" id="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_LECTURA_ACTUAL ?>" value="<?= isset($LecturaActual)?$LecturaActual:'' ?>" title="Lectura del contador al final del periodo facturado actual" _required="required">
	        <input name="overflow" id="overflow" type="checkbox" class="chkbox" title="Marque esta casilla si el contador ha superado su límite" /><label for="overflow" class="chkbox" title="Marque esta casilla si el contador ha superado su límite">contador rebasado</label>
	        </div>

            <label _style="width: 300px;" for="tipoLectura">Tipo de lectura: </label>
            <select _style="width: 450px;" id="tipoLectura" name="<?= VAR_TIPO_LECTURA_CONTADOR ?>" title="Indique si la lectura del contador es real o estimada">
            	<option disabled selected value> -- seleccione lectura real ó estimada -- </option>
            	<option value="<?= FACTURA_AGUA_DATOS_GENERALES_LECTURA_REAL ?>"<?= ($esLecturaReal)?' selected="selected"':'' ?>>Real</option>
            	<option value="<?= FACTURA_AGUA_DATOS_GENERALES_LECTURA_ESTIMADA ?>"<?= ($esLecturaEstimada)?' selected="selected"':'' ?>>Estimada</option>
            </select>
            <label>Consumo calculado: </label>
            <div id="consumoCalculado"></div>
            <!-- </fieldset> -->

			<?php /*
            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA ?>">Tarifa: </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA ?>" name="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_TARIFA ?>" title="Lista de las tarifas aplicables (costes de los servicios)"></select>

            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA ?>">Categoría: </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA ?>" name="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_CATEGORIA ?>" title="Lista de las categorías (tipos de suministros)"></select>

            <label _style="width: 300px;" for="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD ?>">Actividad principal: </label>
            <select _style="width: 450px;" id="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD ?>" name="<?= VAR_FACTURA_AGUA_DATOS_SUMINISTRO_ACTIVIDAD ?>" title="Lista de las actividades (tipos de usuarios según su actividad)"></select>
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
