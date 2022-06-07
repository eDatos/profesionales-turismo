<style>
.total {
    background-color:lightblue !important;
    text-align: right;
}
.importe {
    text-align: right;
}
.tablaaguafacturada td {
    background-color: lightYellow;
}
.cabeceraTabla th {
    background-color: lavender;
}
.tablafactura td {
    background-color: lightYellow;
}
.tablafactura th {
    background-color: lavender;
}
.etiquetaTotal {
    text-align: right !important;
    padding-right: 20px;
    font-weight: bold;
    background-color: lightGray !important;
}
#ImporteTotalAguaFacturada,#ImporteTotalSaneamientoFacturada,#ImporteTotalDepuracionFacturada {
    font-weight: bold;
    font-size: 150%;
    padding-right: 4px;
    text-align: center !important;
}
/*.celdaTramo,.celdaLimite,.celdaConsumo,.celdaPrecio,.celdaImporte {
    /+*text-align: center !important;*+/
    text-align: left !important;
}*/
th {
    text-align: center;
}
/*
.celdaTramo {
    text-align: left !important;
}
*/
input:read-only {
    background-color: #C7C7C7;
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
sup.nota {
    color: red;
    font-weight: bold;
}
p.nota {
    font-style: italic;
    font-weight: bold;
    witdh: 100%;
}
label.authError {
    color: red;
    width: max-content !important;
}
</style>
<script type="text/javascript" src="js/jquery.inputmask.min.js"></script>
<script type="text/javascript">
var datosModificados=false;

var infoTramosAgua=[];
<?php
    for($tramo=0;$tramo<=$nTramosConsumo;$tramo++)
    {
        echo 'infoTramosAgua['.$tramo.']={';
        echo '\'limite\': "'.(isset(${'T'.$tramo.'_consumo_limite'})?${'T'.$tramo.'_consumo_limite'}:'').'",';
        echo '\'precio\': "'.(isset(${'T'.$tramo.'_consumo_precio'})?${'T'.$tramo.'_consumo_precio'}:'').'",';
        echo '\'realizado\': "'.(isset(${'T'.$tramo.'_consumo_realizado'})?${'T'.$tramo.'_consumo_realizado'}:'').'",';
        echo '\'importe\': "'.(isset(${'T'.$tramo.'_consumo_importe'})?${'T'.$tramo.'_consumo_importe'}:'').'"';
        echo '};'.PHP_EOL;
    }
?>
<?php /*
var infoTramosSaneamiento=[];
<?php
    for($tramo=0;$tramo<=$nTramosSaneamiento;$tramo++)
    {
        echo 'infoTramosSaneamiento['.$tramo.']={';
        echo '\'limite\': "'.(isset(${'T'.$tramo.'_saneamiento_limite'})?${'T'.$tramo.'_saneamiento_limite'}:'').'",';
        echo '\'precio\': "'.(isset(${'T'.$tramo.'_saneamiento_precio'})?${'T'.$tramo.'_saneamiento_precio'}:'').'",';
        echo '\'realizado\': "'.(isset(${'T'.$tramo.'_saneamiento_realizado'})?${'T'.$tramo.'_saneamiento_realizado'}:'').'",';
        echo '\'importe\': "'.(isset(${'T'.$tramo.'_saneamiento_importe'})?${'T'.$tramo.'_saneamiento_importe'}:'').'"';
        echo '};'.PHP_EOL;
    }
?>
var infoTramosDepuracion=[];
<?php
    for($tramo=0;$tramo<=$nTramosDepuracion;$tramo++)
    {
        echo 'infoTramosDepuracion['.$tramo.']={';
        echo '\'limite\': "'.(isset(${'T'.$tramo.'_depuracion_limite'})?${'T'.$tramo.'_depuracion_limite'}:'').'",';
        echo '\'precio\': "'.(isset(${'T'.$tramo.'_depuracion_precio'})?${'T'.$tramo.'_depuracion_precio'}:'').'",';
        echo '\'realizado\': "'.(isset(${'T'.$tramo.'_depuracion_realizado'})?${'T'.$tramo.'_depuracion_realizado'}:'').'",';
        echo '\'importe\': "'.(isset(${'T'.$tramo.'_depuracion_importe'})?${'T'.$tramo.'_depuracion_importe'}:'').'"';
        echo '};'.PHP_EOL;
    }
?>
*/?>

function crearLineaTabla(prefijo,ntramo,limiteTramo)
{
	var idPrimerBloque=parseInt(<?= ID_INDICE_PRIMER_BLOQUE ?>,10);
	var secuencia=('0000'+(idPrimerBloque + ntramo)).substr(-4);
	var ndigitos=((' '+limiteTramo).length)-1;
	var ndigitosSize=6;
	var salida='';
	salida+='        	<tr class="filaDatos" data-tramo="'+ntramo+'">';
	//salida+='        		<td class="celdaLimite"><div><strong>T'+(ntramo+1)+'</strong> <!--<label for='"+prefijo+secuencia+'<?= VAR_ID_BLOQUE_LIMITE ?>">-->Límite tramo (en M<sup>3</sup>): <input name='"+prefijo+secuencia+'<?= VAR_ID_BLOQUE_LIMITE ?>" id='"+prefijo+secuencia+'<?= VAR_ID_BLOQUE_LIMITE ?>" type="text"  class="limite entero" maxlength="3" size="3" value="'+limiteTramo+'"/><!--</label>--></div></td>';
	salida+='        		<td class="celdaTramo" title="Tramo Nº '+(ntramo+1)+'"><strong>T'+(ntramo+1)+'</strong></td>';
	salida+='        		<td class="celdaLimite"><input name="'+prefijo+secuencia+'<?= VAR_ID_BLOQUE_LIMITE ?>" id="'+prefijo+secuencia+'<?= VAR_ID_BLOQUE_LIMITE ?>" type="text"  class="limite entero parche_autofill" maxlength="'+ndigitos+'" size="'+ndigitosSize+'" value="'+limiteTramo+'" title="Límite de M3 facturables en este tramo'+((ntramo==0)?'':' (poner a 0 para borrar el tramo)')+'"/></td>';
    salida+='        		<td class="celdaConsumo"><input name="'+prefijo+secuencia+'<?= VAR_ID_BLOQUE_CONSUMO_REALIZADO ?>" id="'+prefijo+secuencia+'<?= VAR_ID_BLOQUE_CONSUMO_REALIZADO ?>" type="text" class="consumo entero parche_autofill" maxlength="'+ndigitos+'" size="'+ndigitosSize+'" value="'+limiteTramo+'" readonly title="M3 facturados en este tramo"> M<sup>3</sup></td>';
    <?php /*
    salida+='        		<td class="celdaPrecio"><input name="'+prefijo+secuencia+'<?= VAR_ID_BLOQUE_PRECIO ?>" id="'+prefijo+secuencia+'<?= VAR_ID_BLOQUE_PRECIO ?>" type="text" class="precio decimal" maxlength="6" size="6" title="precio del M3 en este tramo"> €/M<sup>3</sup></td>';
    salida+='        		<td class="celdaImporte"><input name="'+prefijo+secuencia+'<?= VAR_ID_BLOQUE_IMPORTE ?>" id="'+prefijo+secuencia+'<?= VAR_ID_BLOQUE_IMPORTE ?>" type="text" class="importe" maxlength="6" size="6" readonly title="Importe de los M3 facturados en este tramo"> €</td>';
    */?>
    salida+='        	</tr>';
    return salida;
}

function crearLineasTabla(prefijo,datos)
{
	if(datos.length==0)
	{
		return crearLineaTabla(prefijo,0,<?= $M3Consumidos ?>);
	}
	else
	{
		var salida='';
		var idPrimerBloque=parseInt(<?= ID_INDICE_PRIMER_BLOQUE ?>,10);
		var ndigitosSize=6;
		for(var i=0;i<datos.length;i++)
		{
			var secuencia=('0000'+(idPrimerBloque + i)).substr(-4);
			var readonly=(i==(datos.length-1))?'':' readonly';
			var pre=prefijo+secuencia;
			salida+='        	<tr class="filaDatos" data-tramo="'+i+'">';
			//salida+='        		<td class="celdaLimite"><div><strong>T'+(i+1)+'</strong> <!--<label for='"+pre+'<?= VAR_ID_BLOQUE_LIMITE ?>">-->Límite tramo (en M<sup>3</sup>): <input name='"+pre+'<?= VAR_ID_BLOQUE_LIMITE ?>" id='"+pre+'<?= VAR_ID_BLOQUE_LIMITE ?>" type="text"  class="limite entero" maxlength="3" size="3" value="'+datos[i].limite+'"'+readonly+'/><!--</label>--></div></td>';
			salida+='        		<td class="celdaTramo" title="Tramo Nº '+(i+1)+'"><strong>T'+(i+1)+'</strong></td>';
			salida+='        		<td class="celdaLimite"><input name="'+pre+'<?= VAR_ID_BLOQUE_LIMITE ?>" id="'+pre+'<?= VAR_ID_BLOQUE_LIMITE ?>" type="text"  class="limite entero" maxlength="6" size="'+ndigitosSize+'" value="'+datos[i].limite+'" title="Límite de M3 facturables en este tramo'+((i==0)?'':' (poner a 0 para borrar el tramo)')+'"'+readonly+'/></td>';
		    salida+='        		<td class="celdaConsumo"><input name="'+pre+'<?= VAR_ID_BLOQUE_CONSUMO_REALIZADO ?>" id="'+pre+'<?= VAR_ID_BLOQUE_CONSUMO_REALIZADO ?>" type="text" class="consumo entero" maxlength="6" size="'+ndigitosSize+'" value="'+datos[i].realizado+'" readonly title="M3 facturados en este tramo"> M<sup>3</sup></td>';
		    <?php /*
		    salida+='        		<td class="celdaPrecio"><input name="'+pre+'<?= VAR_ID_BLOQUE_PRECIO ?>" id="'+pre+'<?= VAR_ID_BLOQUE_PRECIO ?>" type="text" class="precio decimal" maxlength="6" size="6" value="'+datos[i].precio+'" title="precio del M3 en este tramo"> €/M<sup>3</sup></td>';
		    salida+='        		<td class="celdaImporte"><input name="'+pre+'<?= VAR_ID_BLOQUE_IMPORTE ?>" id="'+pre+'<?= VAR_ID_BLOQUE_IMPORTE ?>" type="text" class="importe" maxlength="6" size="6" value="'+datos[i].importe+'" readonly title="Importe de los M3 facturados en este tramo"> €</td>';
		    */?>
		}
		return salida;
	}
}

function crearTablaAguaFacturada()
{
	var salida='';
	salida+='<h4 class="titulo_seccion">Consumo de agua</h4>';
	salida+='<h3>Se han consumido <?= $M3Consumidos ?><?= ((isset($Overflow)&&($Overflow=='S'))?'<sup class="nota" title="contador rebasado">*</sup>':'')?> metros cúbicos durante <?= $nDiasPeriodo; ?> días (de <?= $FechaPeriodoInicial ?> a <?= $FechaPeriodoFinal ?>)</h3>';
	salida+='<table class="tablaaguafacturada tablafactura" data-prefijo="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES ?>" width="100%">';
	salida+='	<tr class="cabeceraTabla">';
	salida+='	    <th scope="col" width="5%">Tramo</th>';
	salida+='		<th>Límite del tramo (M<sup>3</sup>)</th>';
	salida+='		<th>Consumo (M<sup>3</sup>)</th>';
	<?php /*
	salida+='		<th>Precio (€/M<sup>3</sup>)</th>';
	salida+='		<th>SUBTOTALES</th>';	
	*/?>
	salida+='	</tr>';

	//salida+=crearLineaTabla('<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES ?>',0,<?= $M3Consumidos ?>);
	salida+=crearLineasTabla('<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES ?>',infoTramosAgua);

	<?php /*
 	salida+='<tr><td colspan="4" class="etiquetaTotal">Total importe consumos</td><td id="ImporteTotalAguaFacturada" class="total" title="Importe total del consumo de agua"></td></tr>';
	*/?>
 	salida+='</table>';
 	<?php if((isset($Overflow))&&($Overflow=='S')): ?>
 	salida+='<p class="nota"><sup class="nota" title="contador rebasado">*</sup>NOTA: El contador ha rebasado su límite.</p>';
 	<?php endif; ?>
 	
 	salida+='<input type="type" style="display: none" name="validTablaAguaFacturada"></input>';
 	salida+='<div id="errorTablaAguaFacturada"></div>';

	return salida;
}

<?php /*
function crearTablaSaneamientoFacturada()
{
	var salida='';
	salida+='<h4 class="titulo_seccion">Saneamiento</h4>';
	salida+='<table class="tablasaneamientofacturada tablafactura" data-prefijo="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES ?>" width="100%">';
	salida+='	<tr class="cabeceraTabla">';
	salida+='	    <th scope="col" width="5%">Tramo</th>';
	salida+='		<th>Límite del tramo (M<sup>3</sup>)</th>';
	salida+='		<th>Volumen (M<sup>3</sup>)</th>';
	salida+='		<th>Precio (€/M<sup>3</sup>)</th>';
	salida+='		<th>SUBTOTALES</th>';
	salida+='	</tr>';

	//salida+=crearLineaTabla('<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES ?>',0,<?= $M3Consumidos ?>);
	salida+=crearLineasTabla('<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES ?>',infoTramosSaneamiento);

 	salida+='<tr><td colspan="4" class="etiquetaTotal">Total importe consumos</td><td id="ImporteTotalSaneamientoFacturada" class="total" title="Importe total del saneamiento"></td></tr>';
 	salida+='</table>';

	return salida;
}

function crearTablaDepuracionFacturada()
{
	var salida='';
	salida+='<h4 class="titulo_seccion">Depuración</h4>';
	salida+='<table class="tabladepuracionfacturada tablafactura" data-prefijo="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES ?>" width="100%">';
	salida+='	<tr class="cabeceraTabla">';
	salida+='	    <th scope="col" width="5%">Tramo</th>';
	salida+='		<th>Límite del tramo (M<sup>3</sup>)</th>';
	salida+='		<th>Volumen (M<sup>3</sup>)</th>';
	salida+='		<th>Precio (€/M<sup>3</sup>)</th>';
	salida+='		<th>SUBTOTALES</th>';
	salida+='	</tr>';

	//salida+=crearLineaTabla('<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES ?>',0,<?= $M3Consumidos ?>);
	salida+=crearLineasTabla('<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES ?>',infoTramosDepuracion);

 	salida+='<tr><td colspan="4" class="etiquetaTotal">Total importe consumos</td><td id="ImporteTotalDepuracionFacturada" class="total" title="Importe total de la depuración"></td></tr>';
 	salida+='</table>';

	return salida;
}

function redondearEuros(cantidad)
{
	return Math.round((cantidad + Number.EPSILON) * 100)/100;
}

function sumarFila(fila)
{
	var importe=fila.find('.importe').eq(0);
	var consumo=fila.find('.consumo').eq(0).val();
	if((!isNaN(consumo))&&(consumo.length!==0))
	{
		consumo=parseFloat(consumo);
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

	return redondearEuros(consumo*precio);
}

function sumarTablas()
{
	var total=0.0;
	$('.tablaaguafacturada .filaDatos').each(function() {
		var subtotal=sumarFila($(this));
		total+=subtotal;
		$(this).find('.importe').eq(0).val(subtotal);
	});
	//total=total.toFixed(2);
	total=redondearEuros(total);
	$('.tablaaguafacturada .total').text(total+' €');
	
	var total=0.0;
	$('.tablasaneamientofacturada .filaDatos').each(function() {
		var subtotal=sumarFila($(this));
		total+=subtotal;
		$(this).find('.importe').eq(0).val(subtotal);
	});
	//total=total.toFixed(2);
	total=redondearEuros(total);
	$('.tablasaneamientofacturada .total').text(total+' €');
	
	var total=0.0;
	$('.tabladepuracionfacturada .filaDatos').each(function() {
		var subtotal=sumarFila($(this));
		total+=subtotal;
		$(this).find('.importe').eq(0).val(subtotal);
	});
	//total=total.toFixed(2);
	total=redondearEuros(total);
	$('.tabladepuracionfacturada .total').text(total+' €');
}

function sumarSubtotales(tabla)
{
	var total=0.0;
	tabla.find('.importe').each(function() {
		var subtotal=$(this).val();
		if((!isNaN(subtotal))&&(subtotal.length!==0))
			total+=parseFloat(subtotal);
	});
	//total=total.toFixed(2);
	total=redondearEuros(total);
	tabla.find('.total').text(total+' €');

	datosModificados=true;
}

function calcularImporteTabla()
{
	$(this).data("valorprevio",$(this).val());	// Parche autofill: para evitar dobles llamadas.
	var fila=$(this).closest('tr');
	var subtotal=sumarFila(fila);
	fila.find('.importe').eq(0).val(subtotal);
	sumarSubtotales($(this).closest('table'));
}

function recalcularImportesTabla(fila)
{
	var subtotal=sumarFila(fila);
	fila.find('.importe').eq(0).val(subtotal);
	sumarSubtotales(fila.closest('table'));
}
*/?>

function sumarLimites()
{
	var total=0.0;
	$('.tablaaguafacturada .filaDatos').each(function() {
		var limite=$(this).find('.limite').eq(0).val();
		if((!isNaN(limite))&&(limite.length!==0))
		{
			limite=parseFloat(limite);
		}
		else
		{
			return 0;
		}
		total+=limite;
	});
	return total;
}

function cambiarLimiteTramo()
{
	var eltoLimite=$(this);
	var nuevoLimite=eltoLimite.val();
	$(this).data("valorprevio",nuevoLimite);	// Parche autofill: para evitar dobles llamadas.
	if(isNaN(nuevoLimite))
		return;
	if(nuevoLimite.length!==0)
	{
		nuevoLimite=parseInt(nuevoLimite,10);
	}
	else
		nuevoLimite=0;

	var consumo=null;
	var fila=$(this).closest('tr');
	var ntramo=fila.data('tramo');
	fila.find('.consumo').each(function() {
		var valor=$(this).val();
		if((!isNaN(valor))&&(valor.length!==0))
		{
			consumo=parseInt(valor,10);

			if(nuevoLimite==0)
			{
				// Se quiere borrar la fila actual.
				if(ntramo>0)
				{
					var filaPrev=fila.prev();
					fila.remove();
					nuevoLimite=consumo+parseInt(filaPrev.find('.consumo').val(),10);
				    var ndigitos=((' '+nuevoLimite).length)-1;
				    filaPrev.find('.consumo,.limite').inputmask({regex: "^[0-9]{1,"+ndigitos+"}$"});
					filaPrev.find('.limite').val(nuevoLimite);
					filaPrev.find('.consumo').val(nuevoLimite);
					//filaPrev.find('.limite').val(<?= $M3Consumidos ?>);
					//filaPrev.find('.consumo').val(<?= $M3Consumidos ?>);
					
					<?php /*
					filaPrev.find('.limite,.consumo').removeAttr('readonly');
					*/ ?>
					filaPrev.find('.limite').removeAttr('readonly');
					
					<?php /*
					recalcularImportesTabla(filaPrev);
					*/ ?>
					filaPrev.find('.limite').focus();
				}
				else
				{
					//alert('No puede eliminarse este tramo porque es el único.');
					fila.find('.limite').val(consumo);
					return;
				}
			}
			else
			{
				if(consumo>nuevoLimite)
				{
					if(ntramo>=2)
					{
						alert('Superado el número de tramos.');
						fila.find('.limite').val(consumo);
						return;
					}
					
					// Hay que crear una línea.
					var diferencia=consumo - nuevoLimite;
					ntramo++;
					var tabla=fila.closest('table');
					var prefijo=tabla.data('prefijo');
				    fila.find('.consumo').attr('readonly', true);
				    $(this).val(nuevoLimite);
				    eltoLimite.attr('readonly', true);

				    fila.after(crearLineaTabla(prefijo,ntramo,diferencia));
				    var newFila=fila.next();

				    // TODO: Modificar esto para afectar sólo a los controles de la nueva línea.
				    var ndigitos=((' '+diferencia).length)-1;
				    newFila.find('.consumo,.limite').inputmask({regex: "^[0-9]{1,"+ndigitos+"}$"});
				    <?php /*
				    tabla.find('.precio').inputmask({regex: "^[0-9]{1,2}(,\\d{1,6})?$"});
				    tabla.find('.consumo,.precio').change(calcularImporteTabla);
				    */?>
				    tabla.find('.limite').change(cambiarLimiteTramo);

				    <?php /*
				    recalcularImportesTabla(fila);
				    */?>

				    fila.find('.limite,.consumo').rules('add', {
						required: true/*,
						totales: true*/
					});

				    // No funciona.
				    newFila=tabla.find('tr').eq(newFila.index());
				    <?php /*
				    newFila.find('.precio').focus();
				    */?>
				    newFila.find('.limite').focus();
				    // No funciona.

				    //tabla.find('.limite').change(cambiarLimiteTramo);	// Funciona.
				    //alert(newFila.index());
				    //newFila=tabla.find('tr').eq(newFila.index());
				    //newFila.find('.limite').change(cambiarLimiteTramo);	// No funciona.
				    
				    //alert(newFila);
				    /*
				    var row_index = fila.index();
				    tabla.find('tr').eq(index);
				    tabla.append(salida);
				    */

				    // TODO: Modificar esto para afectar sólo a los controles de la nueva línea.
				    /*
					$(".consumo .limite").inputmask({regex: "^[0-9]{1,3}$"});
					$(".precio").inputmask({regex: "^[0-9]{1,2}(,\\d{1,6})?$"});
					$('.consumo').change(calcularImporteTabla);
					$('.precio').change(calcularImporteTabla);
				    $('.limite').change(cambiarLimiteTramo);
				    */
				}
				else
				{
					// Cancelamos el cambio. No hace falta recalcular totales.
					fila.find('.limite').val(consumo);
				}
			}
		}
	});
}


// Hay que usar delegación de eventos para los nuevos elementos cargados dinámicamente.
// https://stackoverflow.com/questions/16598213/how-to-bind-events-on-ajax-loaded-content

<?php /*
function limpiarComasFinales()
{
	$('.decimal').each(function() {
		var valor=$(this).val();
		if(valor.endsWith(','))
			$(this).val(valor.slice(0,-1));
	});
}
*/?>

function validarForm()
{
	<?php /*
	limpiarComasFinales();
	*/?>
	return true;
}

function setupValidacion()
{
	jQuery.validator.setDefaults({
		  //debug: true,
		  //success: "valid",
		  ignoreTitle: true,
		  errorClass: "authError",
		});
	jQuery.validator.addMethod(
        "totales",
        function (value, element, params) {
            return (sumarLimites()==<?= $M3Consumidos ?>);
        },
        "ERROR: El total no coincide con los M3 consumidos"
    );

	$("#formdata").validate({
		rules: {
			'validTablaAguaFacturada': {
				'totales': true
			}
		},
		errorPlacement: function(error, element) {
			error.insertBefore($("#errorTablaAguaFacturada"));
			/*
	        if (element.attr("name") == 'validTablaAguaFacturada') {
	             error.insertBefore($("#errorTablaAguaFacturada"));
	        } else {
	            error.insertAfter(element);
	        }*/
	    }
	});
}

$(document).ready( function() {
	$('#aguafacturada').html(crearTablaAguaFacturada());
	<?php /*
	$('#saneamientofacturada').html(crearTablaSaneamientoFacturada());
	$('#depuracionfacturada').html(crearTablaDepuracionFacturada());

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
	
	$.fn.getEntero = function(){
        if (false == $(this).hasClass("entero")) {
            return;
        }
        var valor=$(this).val();
        if((!isNaN(valor))&&(valor.length!==0))
        {
	        return parseFloat(valor);
        }

        return NaN;
	};
	
	$(".consumo,.limite").inputmask({regex: "^[0-9]{1,3}$"});
	$(".precio").inputmask({regex: "^[0-9]{1,2}(,\\d{1,6})?$"});

	sumarTablas();

	$('.consumo').change(calcularImporteTabla);
	$('.precio').change(calcularImporteTabla);
	*/?>

	setupValidacion();
	$('.limite,.consumo').rules('add', {
		required: true,
		totales: true
	});
	
	$('.limite').change(cambiarLimiteTramo);

	$('.parche_autofill').focus(function(){
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

	$("input[type='button']").button();
	
	$('#botonanterior').click(function() {
		window.location='<?= $urlPrev ?>';
	});
	
	$("#botonenviar").click(function(event) {
	 	if($('#formdata').valid()==false)
	 	{
	 		alert('Existen errores en los datos.\nPor favor, corrija los errores antes de continuar.');
	 		event.preventDefault();
	 		return false;
	 	}
		$("#formdata").submit();
	});
	
	$("#formdata").submit(function(event) {
		if(!validarForm())
		{
			event.preventDefault();
			return false;
		}
		datosModificados=false;
	});

	$(window).on("beforeunload", function() {
		return ((datosModificados)? "Se dispone a abandonar la página.\nSi continúa pueden perderse los cambios no guardados." : null);
	});
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
	<h3 class="titulo_2 titulo_seccion">DETALLE DE LOS CONSUMOS Y FACTURACIÓN<div class="paso">Paso <?= $parte ?> de <?= $numpartes ?></div></h3>
	<div style="padding: 4px;">
    <form method="post" action="<?= $urlNext ?>" id="formdata">
    	<div id="aguafacturada"></div><br>
    	<div id="saneamientofacturada"></div><br>
    	<div id="depuracionfacturada"></div>
    	<div class="botonera">
        <div class="botonAnterior"><input type="button" id="botonanterior" value="Anterior"></div>
        <div class="botonSiguiente"><input type="button" id="botonenviar" value="Siguiente"></div>
        </div>
        <div style="clear: both;"/>
    </form>
    </div>
</div>
