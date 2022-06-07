<style>
.total {
    background-color:lightblue;
    text-align: right;
}
.importe {
    text-align: right;
}
#ImporteTotalEnergiaFacturada {
    font-weight: bold;
    font-size: 150%;
    padding-right: 4px;
    text-align: center !important;
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
   vertical-align: middle;
   text-align: left important!;
   display: block !important;
   width: max-content !important;
   height: inherit;
}
</style>
<script type="text/javascript" src="js/jquery.inputmask.min.js"></script>
<script type="text/javascript">
function crearInputEnergiaConsumida(id,valor)
{
	//return '<td style="text-align: center !important;"><input name="'+id+'" id="'+id+'" type="text" class="energia" maxlength="6" size="6" _style="width:40px;" value="'+valor+'"> kWh</td>';
	return '<td style="text-align: center !important;"><input name="'+id+'" id="'+id+'" type="text" class="energia numero condecimales" data-numdecimales="2" maxlength="8" size="8" _style="width:40px;" value="'+valor+'"> kWh</td>';
}

<?php /*
function crearInputEnergiaPrecio(id,valor)
{
	return '<td><input name="'+id+'" id="'+id+'" type="text" class="precio decimal" maxlength="9" size="9" _style="width:40px;" value="'+valor+'"> €/kWh</td>';
}

function crearInputEnergiaImporte(id,valor)
{
	return '<td><input name="'+id+'" id="'+id+'" type="text" class="importe" maxlength="9" size="9" _style="width:40px;" value="'+valor+'" readonly="readonly"> €</td>';
}
*/?>

<?php switch($Peaje):
     // 6 periodos
     case '6.1A':
     case '6.1B':
     case '6.2':
     case '6.3':
     case '6.4':
     case '6.5': ?>
function generarLineaP4_6Tabla()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P4</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_ENERGIA ?>','<?= $P4_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_PRECIO ?>','<?= $P4_precio ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P4_IMPORTE ?>','<?= $P4_importe ?>');
	*/?>
	salida+='        	</tr>';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P5</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_ENERGIA ?>','<?= $P5_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_PRECIO ?>','<?= $P5_precio ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P5_IMPORTE ?>','<?= $P5_importe ?>');
	*/?>
	salida+='        	</tr>';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P6</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_ENERGIA ?>','<?= $P6_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_PRECIO ?>','<?= $P6_precio ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P6_IMPORTE ?>','<?= $P6_importe ?>');
	*/?>
	salida+='        	</tr>';
	return salida;
}
 <?php
 // 3 periodos
 case '2.0DHS':
 case '2.1DHS':
 case '3.0A':
 case '3.1A': ?>
function generarLineaP3Tabla()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P3</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_ENERGIA ?>','<?= $P3_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_PRECIO ?>','<?= $P3_precio ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P3_IMPORTE ?>','<?= $P3_importe ?>');
	*/?>
	salida+='        	</tr>';
	return salida;
}
<?php
	     // 2 periodos
	     case '2.0DHA':
	     case '2.1DHA': ?>
function generarLineaP2Tabla()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P2</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_ENERGIA ?>','<?= $P2_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_PRECIO ?>','<?= $P2_precio ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P2_IMPORTE ?>','<?= $P2_importe ?>');
	*/?>
	salida+='        	</tr>';
	return salida;
}
<?php
	     // Sin discriminación horaria.
	     case '2.0A':
	     case '2.1A': ?>
function generarLineaP1Tabla()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	<?php if($NumeroPeriodos>1): ?>
	salida+='        		<td><strong>P1</strong></td>';
	<?php endif; ?>
	salida+='        		'+crearInputEnergiaConsumida('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_ENERGIA ?>','<?= $P1_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_PRECIO ?>','<?= $P1_precio ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_IMPORTE ?>','<?= $P1_importe ?>');
	*/?>
	salida+='        	</tr>';
	return salida;
}
<?php break;
endswitch; ?>

function crearTablaEnergiaFacturada()
{
	var salida='';
	salida+='<table class="tablaresultado" width="100%">';
	salida+='	<tr>';
	salida+='	   <?= ($NumeroPeriodos>1) ? '<th>Periodo</th>':''; ?>';
	salida+='		<th>Energía (kWh)</th>';
	<?php /*
	salida+='		<th>Precio (€/kWh)</th>';
	salida+='		<th>SUBTOTALES</th>';
	*/?>
	salida+='	</tr>';
	            <?php switch($Peaje):
	                 // Sin discriminación horaria.
	                 case '2.0A':
	                 case '2.1A': ?>
	salida+=generarLineaP1Tabla();
	                 <?php break;
	                 // 2 periodos
	                 case '2.0DHA':
	                 case '2.1DHA': ?>
	salida+=generarLineaP1Tabla();
	salida+=generarLineaP2Tabla();
	                 <?php break;
	                 // 3 periodos
	                 case '2.0DHS':
	                 case '2.1DHS':
	                 case '3.0A':
	                 case '3.1A': ?>
	salida+=generarLineaP1Tabla();
	salida+=generarLineaP2Tabla();
	salida+=generarLineaP3Tabla();
                    <?php break;
                    // 6 periodos
                    case '6.1A':
                    case '6.1B':
                    case '6.2':
                    case '6.3':
                    case '6.4':
                    case '6.5': ?>
	salida+=generarLineaP1Tabla();
	salida+=generarLineaP2Tabla();
	salida+=generarLineaP3Tabla();
	salida+=generarLineaP4_6Tabla();
	                 <?php break; ?>
	                 <?php endswitch; ?>
    <?php /*
    salida+='<tr><td id="totalkWh" colspan="<?= ($NumeroPeriodos>1) ? 3:2; ?>" style="text-align: right !important; padding-right: 20px; font-weight: bold;"></td><td id="ImporteTotalEnergiaFacturada" class="total"></td></tr>';
    */?>
 	salida+='<tr><td id="totalkWh" colspan="<?= ($NumeroPeriodos>1) ? 2:1; ?>" style="text-align: right !important; padding-right: 20px; font-weight: bold;"></td></tr>';
 	salida+='</table>';
 	salida+='<div id="errorTablaEnergiaFacturada"></div>';

	return salida;
}

<?php /*
function redondearEuros(cantidad)
{
	return Math.round((cantidad + Number.EPSILON) * 100)/100;
}

function sumarFila(fila)
{
	var importe=fila.find('.importe').eq(0);
	var energia=fila.find('.energia').eq(0).val();
	if((!isNaN(energia))&&(energia.length!==0))
	{
		energia=parseFloat(energia);
	}
	else
	{
		importe.val('');
		return 0;
	}
	var precio=fila.find('.precio').eq(0).getFloat();
	if(isNaN(precio))
	{
		importe.val('');
		return 0;
	}
	return redondearEuros(energia*precio);
}

function sumarTabla()
{
	var total=0;
	$('.filaDatos').each(function() {
		var subtotal=sumarFila($(this));
		total+=subtotal;
		$(this).find('.importe').eq(0).val(subtotal);
	});
	total=redondearEuros(total);
	$('#ImporteTotalEnergiaFacturada').text(total+' €');
	sumar_kWh();
}

function sumarSubtotales()
{
	var total=0;
	$('.tablaresultado .importe').each(function() {
		var subtotal=$(this).val();
		if((!isNaN(subtotal))&&(subtotal.length!==0))
			total+=parseFloat(subtotal);
	});
	total=redondearEuros(total);
	$('#ImporteTotalEnergiaFacturada').text(total+' €');
}
*/?>

function sumar_kWh()
{
	var total_kWh=0;
	$('.tablaresultado .energia').each(function() {
		var subtotal=$(this).val();
		try
		{
			var cantidad=subtotal.replace(',','.');
			var consumo=parseFloat(cantidad);
			if(isNaN(consumo)==false)
				total_kWh+=parseFloat(cantidad);
		}catch(e)
		{
		}
		//if((!isNaN(subtotal))&&(subtotal.length!==0))
		//	total_kWh+=parseFloat(subtotal);
	});
	$('#totalkWh').text('Total '+total_kWh.toFixed(2)+' kWh hasta <?= $FechaFinalPeriodo ?>');
}

<?php /*
function calcularImporteEnergiaPeriodo()
{
	$(this).data("valorprevio",$(this).val());	// Parche autofill: para evitar dobles llamadas.
	var fila=$(this).closest('tr');
	var subtotal=sumarFila(fila);
	fila.find('.importe').eq(0).val(subtotal);
	sumarSubtotales();
	sumar_kWh();
}
*/?>


// Hay que usar delegación de eventos para los nuevos elementos cargados dinámicamente.
// https://stackoverflow.com/questions/16598213/how-to-bind-events-on-ajax-loaded-content

function setupValidacion()
{
	jQuery.validator.setDefaults({
		  //debug: true,
		  //success: "valid",
		  ignoreTitle: true,
		});
	
	jQuery.validator.addClassRules({
		  "energia-requerida": {
			  required: true
		  }
		});

	$("#formdata").validate({
		errorPlacement: function(error, element) {
			return true;
	    },
	    showErrors: function(errorMap, errorList) {
		    this.defaultShowErrors();
		},
	  invalidHandler: function(event, validator) {
		  var nerrores = validator.numberOfInvalids();
		  if(nerrores)
		  {
			  var msg='<label class="error">Debe rellenar todos los campos de entrada.';
			  //msg+=(errores==1)?' El campo a cumplimentar ha sido resaltado.':' Los '+nerrores+' campos a cumplimentar han sido resaltados.';
			  msg+=(nerrores==1)?' Por favor, cumplimente el campo resaltado.':' Por favor, cumplimente los '+nerrores+' campos resaltados.';
			  msg+='</label>';
			  $("#errorTablaEnergiaFacturada").html(msg);
			  $("#errorTablaEnergiaFacturada").show();
		  }
		  else
		  {
			  $("#errorTablaEnergiaFacturada").hide();
		  }
	  }
	});
}

$(document).ready( function() {
	$("input[type='button']").button();
	$('#energiafacturada').html(crearTablaEnergiaFacturada());
	$("input[name='<?= VAR_FACTURA_ELECTRICIDAD_ENERGIA_FACTURADA_P1_ENERGIA ?>']").addClass("energia-requerida");
	
	// Validación formulario
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
	 	var vacios=$("#formdata").find(':input').filter(function(){
		 	return $(this).val()==='';
		});
		vacios.prop('disabled',true);
		$("#formdata").submit();
	});

	$('#botonanterior').click(function(event) {
		window.location='<?= $urlPrev ?>';
	});

	$.fn.getFloat = function(){
        if (false == $(this).hasClass("decimal")) {
            return;
        }
        var valor=$(this).val();
        valor=valor.replaceAll(',','.');
        if((!isNaN(valor))&&(valor.length!==0))
        {
	        return parseFloat(valor);
        }

        return NaN;
	};
	
	<?php /*
	$(".energia").inputmask({regex: "^[0-9]{1,6}$"});
	$(".precio").inputmask({regex: "^[0-9]{1,2}(,\\d{1,6})?$"});

	sumarTabla();

	$('.tablaresultado td input').change(calcularImporteEnergiaPeriodo);
	*/?>

	sumar_kWh();
	$('.tablaresultado td input').change(sumar_kWh);

	$('.tablaresultado td input').focus(function(){
		$(this).data("valorprevio", $(this).val());		
	}).blur(function(){
		var valorprevio=$(this).data("valorprevio");
		if(valorprevio!=null)
		{
			if(valorprevio!=$(this).val())
			{
				$(this).trigger("change");
			}
		}
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

	<h3 class="titulo_2 titulo_seccion">DETALLE DE LOS CONSUMOS Y FACTURACIÓN<div class="paso">Paso <?= $parte ?> de <?= $numpartes ?></div></h3>
	<div style="padding: 4px;">
    <form method="post" action="<?= $urlNext ?>" id="formdata">
    	<h3>Energía facturada</h3>
    	<p>Introduzca los periodos tal y como aparecen en su factura. Si en su factura hay menos de los que se muestran en el formulario, deje en blanco los sobrantes.</p>
    	<div id="energiafacturada"></div>
    	<div class="botonera">
        <div class="botonAnterior"><input type="button" id="botonanterior" value="Anterior"></div>
        <div class="botonSiguiente"><input type="button" id="botonenviar" value="Siguiente"></div>
        </div>
        <div style="clear: both;"/>
    </form>
    </div>
</div>
