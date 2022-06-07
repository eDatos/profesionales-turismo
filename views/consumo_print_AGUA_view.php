<link rel="stylesheet" href="css/pwet-theme/jquery-ui-1.9.1.custom.css"/>
<link rel="stylesheet" href="css/aloja_form_view.css"/>
<script type="text/javascript" src="js/jquery-ui-1.9.1.custom.js"></script>
<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/inputfields.js"></script>
<script type="text/javascript">
</script>
<style>
.tablaresultado th,td {
    text-align: center !important;
}
.tablaresultado td:nth-child(2) {
    text-align: left !important;
}
/*.tablaresultado td:nth-child(2) th:nth-child(2) {
    text-align: left;
}*/

.formularioParcial {
     clear: both;
     padding: 5px;
     border-style: solid;
}
.titulo_seccion {
    background-color:lightblue;
    padding: 2px;
}
.total {
    background-color:lightblue;
    text-align: right;
}
.importe {
    text-align: right;
}

.total {
    background-color:lightblue !important;
    /*text-align: right;*/
    font-weight: bold;
    font-size: 150%;
    padding-right: 4px;
    text-align: center !important;
}
.importe {
    text-align: right;
}
.tablafactura td {
    background-color: lightYellow;
}
.tablafactura th {
    background-color: lavender;
}
td span {
    display: contents;
}
.cabeceraTabla th {
    background-color: lavender;
}
.etiquetaTotal {
    text-align: right !important;
    padding-right: 20px;
    font-weight: bold;
    background-color: lightGray !important;
}
#ImporteTotalAguaFacturada,#ImporteTotalSaneamientoFacturada,#ImporteTotalDepuracionFacturada {
}
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
.iconoSuministroFinal {
    display: inline-block;
    width: 48px;
    height: 48px;
    float: right;
    margin-right: 10px;
}
.money {
    /*font-weight: bold;*/
    margin-left: 10px;
    /*text-align: right;
    width: 70px;
    display: inline-block;*/
}
</style>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">

<script type="text/javascript">
<?php /*
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
*/?>
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

function crearLineasTabla(prefijo,datos)
{
	var salida='';
	var idPrimerBloque=parseInt(<?= ID_INDICE_PRIMER_BLOQUE ?>,10);
	for(var i=0;i<datos.length;i++)
	{
		var secuencia=('0000'+(idPrimerBloque + i)).substr(-4);
		var readonly=(i==(datos.length-1))?'':' readonly';
		var pre=prefijo+secuencia;
		salida+='        	<tr class="filaDatos" data-tramo="'+i+'">';
		//salida+='        		<td class="celdaLimite"><div><strong>T'+(i+1)+'</strong> <!--<label for='"+pre+'<?= VAR_ID_BLOQUE_LIMITE ?>">-->Límite tramo (en M3): <input name='"+pre+'<?= VAR_ID_BLOQUE_LIMITE ?>" id='"+pre+'<?= VAR_ID_BLOQUE_LIMITE ?>" type="text"  class="limite entero" maxlength="3" size="3" value="'+datos[i].limite+'"'+readonly+'/><!--</label>--></div></td>';
		salida+='        		<td class="celdaTramo" title="Tramo Nº '+(i+1)+'"><strong>T'+(i+1)+'</strong></td>';
		salida+='        		<td class="celdaLimite"><span class="limite entero">'+datos[i].limite+'</span></td>';
	    salida+='        		<td class="celdaConsumo"><span class="consumo entero">'+datos[i].realizado+'</span> M3</td>';
	    <?php /*
	    salida+='        		<td class="celdaPrecio"><span class="precio decimal">'+datos[i].precio+'</span> €/M3</td>';
	    salida+='        		<td class="celdaImporte"><span class="importe">'+datos[i].importe+'</span> €</td>';
	    */?>
	}
	return salida;
}

function crearTablaAguaFacturada()
{
	var salida='';
	salida+='<h4 class="titulo_seccion">Consumo de agua</h4>';
	salida+='<h3>Se han consumido <?= number_format($M3Consumidos,2,',','.') ?> metros cúbicos durante <?= $nDiasPeriodo; ?> días (de <?= $FechaInicioPeriodo; ?> a <?= $FechaFinalPeriodo; ?>)</h3>';

	return salida;
}

<?php /*
function crearTablaAguaFacturada()
{
	var salida='';
	salida+='<h4 class="titulo_seccion">Consumo de agua</h4>';
	salida+='<h3>Se han consumido <?= $M3Consumidos ?> metros cúbicos durante <?= $nDiasPeriodo; ?> días (de <?= $FechaInicioPeriodo; ?> a <?= $FechaFinalPeriodo; ?>)</h3>';
	salida+='<table class="tablaaguafacturada tablafactura" data-prefijo="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES ?>" width="100%">';
	salida+='	<tr class="cabeceraTabla">';
	salida+='	    <th scope="col" width="5%">Tramo</th>';
	salida+='		<th>Límite del tramo (M3)</th>';
	salida+='		<th>Consumo (M3)</th>';
	<?php /*
	salida+='		<th>Precio (€/M3)</th>';
	salida+='		<th>SUBTOTALES</th>';
	* /?*>
	salida+='	</tr>';

	salida+=crearLineasTabla('<*?= VAR_FACTURA_AGUA_DATOS_CONSUMO_AGUA_BLOQUES ?*>',infoTramosAgua);

	<*?php /*
 	salida+='<tr><td colspan="4" class="etiquetaTotal">Total importe consumos</td><td id="ImporteTotalAguaFacturada" class="total" title="Importe total del consumo de agua"></td></tr>';
 	* /?*>
 	salida+='</table>';

	return salida;
}

function crearTablaSaneamientoFacturada()
{
	var salida='';
	salida+='<h4 class="titulo_seccion">Saneamiento</h4>';
	salida+='<table class="tablasaneamientofacturada tablafactura" data-prefijo="<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_SANEMAMIENTO_BLOQUES ?>" width="100%">';
	salida+='	<tr>';
	salida+='	    <th scope="col" width="5%">Tramo</th>';
	salida+='		<th>Límite del tramo (M3)</th>';
	salida+='		<th>Volumen (M3)</th>';
	salida+='		<th>Precio (€/M3)</th>';
	salida+='		<th>SUBTOTALES</th>';
	salida+='	</tr>';

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
	salida+='	<tr>';
	salida+='	    <th scope="col" width="5%">Tramo</th>';
	salida+='		<th>Límite del tramo (M3)</th>';
	salida+='		<th>Volumen (M3)</th>';
	salida+='		<th>Precio (€/M3)</th>';
	salida+='		<th>SUBTOTALES</th>';
	salida+='	</tr>';

	salida+=crearLineasTabla('<?= VAR_FACTURA_AGUA_DATOS_CONSUMO_DEPURACION_BLOQUES ?>',infoTramosDepuracion);

 	salida+='<tr><td colspan="4" class="etiquetaTotal">Total importe consumos</td><td id="ImporteTotalDepuracionFacturada" class="total" title="Importe total de la depuración"></td></tr>';
 	salida+='</table>';

	return salida;
}

function redondearEuros(cantidad)
{
	return Math.round((cantidad + Number.EPSILON) * 100)/100;
}

/+*
function sumarFila(fila)
{
	var importe=fila.find('.importe').eq(0);
	var consumo=fila.find('.consumo').eq(0).text();
	if((!isNaN(consumo))&&(consumo.length!==0))
	{
		consumo=parseFloat(consumo);
	}
	else
	{
		importe.text('');
		return 0;
	}
	var precio=fila.find('.precio').eq(0).getFloat();
	if(isNaN(precio))
	{
		importe.text('');
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
		$(this).find('.importe').eq(0).text(subtotal);
	});
	//total=total.toFixed(2);
	total=redondearEuros(total);
	$('.tablaaguafacturada .total').text(total+' €');
	
	var total=0.0;
	$('.tablasaneamientofacturada .filaDatos').each(function() {
		var subtotal=sumarFila($(this));
		total+=subtotal;
		$(this).find('.importe').eq(0).text(subtotal);
	});
	//total=total.toFixed(2);
	total=redondearEuros(total);
	$('.tablasaneamientofacturada .total').text(total+' €');
	
	var total=0.0;
	$('.tabladepuracionfacturada .filaDatos').each(function() {
		var subtotal=sumarFila($(this));
		total+=subtotal;
		$(this).find('.importe').eq(0).text(subtotal);
	});
	//total=total.toFixed(2);
	total=redondearEuros(total);
	$('.tabladepuracionfacturada .total').text(total+' €');
}
*+/

function totalizarTabla(tabla)
{
	var total=0.0;
	tabla.find('.importe').each(function() {
		var importe=$(this).text();
		if((!isNaN(importe))&&(importe.length!==0))
			total+=parseFloat(importe);
	});
	//total=total.toFixed(2);
	total=redondearEuros(total);
	tabla.find('.total').eq(0).text(total+' €');
}
*/?>

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

$(document).ready( function() {
	$('#aguafacturada').html(crearTablaAguaFacturada());
	<?php /*
	$('#saneamientofacturada').html(crearTablaSaneamientoFacturada());
	$('#depuracionfacturada').html(crearTablaDepuracionFacturada());

	totalizarTabla($('.tablaaguafacturada'));
	totalizarTabla($('.tablasaneamientofacturada'));
	totalizarTabla($('.tabladepuracionfacturada'));

	/+*
	$.fn.getFloat = function(){
        if (false == $(this).hasClass("decimal")) {
            return;
        }
        var valor=$(this).text();
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
        var valor=$(this).text();
        if((!isNaN(valor))&&(valor.length!==0))
        {
	        return parseFloat(valor);
        }

        return NaN;
	};

	sumarTablas();
	*+/
	*/?>

	$('.money').each(function(){
		$(this).text(new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(toFloat($(this).text())));
	});
});

</script>
<div style="clear: both;"/>
<div id="formulario" class="formularioParcial">
	<h1>Factura Nº: <b><?= $NumeroFactura ?></b>
			<?php
      	if(isset($iconoSuministro))
      	{
      	    echo '<img class="iconoSuministroFinal" src="images/consumos/'.$iconoSuministro->desc_corta.'" alt="'.$iconoSuministro->desc_larga.'" title="'.$iconoSuministro->desc_larga.'"/>';
      	}
    ?>
	</h1>
	<h3 class="titulo_2 titulo_seccion">DATOS GENERALES</h3>
	<div style="padding: 4px;">
	    <?php /*
		<p><b>Suministradora:</b> <?= addslashes($suministradora) ?></p>
	    */?>
		<?php if(isset($FechaFinalPeriodo)): ?>
		<p><b>Periodo de facturación:</b> De <?= $FechaInicioPeriodo ?> Hasta <?= $FechaFinalPeriodo ?></p>
		<?php else: ?>
		<p><b>Fecha de facturación:</b> <?= $FechaInicioPeriodo ?></p>
		<?php endif; ?>
		<p><b>Referencia Nº de contrato:</b> <?= $ReferenciaContrato ?></p>
    </div>
	
	<h3 class="titulo_2 titulo_seccion">DATOS RELACIONADOS CON EL SUMINISTRO</h3>
	<div style="padding: 4px;">
		<?php /*
		<p><b>Empresa distribuidora:</b> <?= addslashes($distribuidoras_sel) ?></p>
		<p><b>Número de póliza:</b> <?= $NumeroContrato ?></p>
		*/?>
		<p><b>Número de contador:</b> <?= $NumeroContador ?></p>
		<?php /*
		<p><b>Calibre:</b> <?= addslashes($calibres_sel) ?></p>
		<p><b>Tipo de contador:</b> <?= addslashes($tiposContador_sel) ?></p>
		*/?>
		<p><b>Lectura anterior (<?= $FechaInicioPeriodo; ?>):</b> <?= $LecturaAnterior ?></p>
		<p><b>Lectura actual (<?= $FechaFinalPeriodo; ?>):</b> <?= $LecturaActual ?></p>
		<p><b>Tipo de lectura:</b> <?= ($esLecturaReal)?'REAL':'ESTIMADA' ?></p>
		<?php /*
		<p><b>Tarifa:</b> <?= addslashes($tarifas_sel) ?></p>
		<p><b>Categoría:</b> <?= addslashes($tiposSuministros_sel) ?></p>
		<p><b>Actividad principal:</b> <?= addslashes($actividades_sel) ?></p>
		*/?>
    </div>
	
	<!-- <h3 class="titulo_2 titulo_seccion">DETALLE DE LOS CONSUMOS Y FACTURACIÓN</h3>  -->
	<h3 class="titulo_2 titulo_seccion">DETALLE DEL CONSUMO</h3>
	<div style="padding: 4px;">
    	<!-- <h3>Agua consumida</h3> -->
    	<div id="aguafacturada"></div><!-- <br> -->
    	<?php /*
    	<h3>Saneamiento</h3>
    	<div id="saneamientofacturada"></div><br>
    	<h3>Depuración</h3>
    	<div id="depuracionfacturada"></div>
    	 */?>
    </div><br>
	
	<h3 class="titulo_2 titulo_seccion">RESUMEN DE FACTURACIÓN</h3>
	<div style="padding: 4px;">
		<p><b>Importe del consumo:</b> <span class="money"><?= $TotalImporteFactura ?></span></p>
		<p><b>Total a pagar:</b>  <span class="money"><?= $TotalPagar ?></span></p>
    </div>
</div>
    
    <div style="margin-top:20px;"><a href="<?= $VolverUrl ?>" class="enlace volvericon">Volver</a></div>
    <!-- <div style="margin-top:20px;"><a href="javascript:history.back()" class="enlace volvericon">Volver</a></div> -->
    <div style="clear: both;"/>

</div>
<!-- FIN BLOQUE INTERIOR -->
