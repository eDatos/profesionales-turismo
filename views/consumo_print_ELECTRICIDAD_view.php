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
     border-style: solid;*/
}
.formularioParcial label {
    /*font-family: Georgia, "Times New Roman", Times, serif;
    font-size: 18px;*/
    font-weight: bold;
    color: #333;
    height: 20px;
    width: 200px;
    margin-top: 10px;
    margin-left: 10px;
    text-align: right;
    clear: both;
    float:left;
    margin-right:15px;
}
.titulo_seccion {
    background-color:lightblue;
    padding: 2px;
}
.botonSiguiente {
    float: right;
    margin-top: 10px;
    margin-right: 10px;
    width: 80px;
}
.botonAnterior {
    float: left;
    margin-top: 10px;
    margin-left: 10px;
    width: 80px;
}
.total {
    background-color:lightblue;
    text-align: right;
}
.importe {
    text-align: right;
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
function listaComercializadorasInit()
{
	$('#empresa_comercializadora').html('<b>Empresa comercializadora:</b> <?php
    	   foreach ($comercializadoras as $comercializadora)
    	   {
    	       if($comercializadora->seleccionado)
    	           echo addslashes($comercializadora->desc_corta);
    	   }
    	?>');
}

<?php /*
function listaDistribuidorasInit()
{
	$('#empresa_distribuidora').html('<b>Empresa distribuidora:</b> <?php
    	   foreach ($distribuidoras as $distribuidora)
    	   {
    	       if($distribuidora->seleccionado)
    	           echo addslashes($distribuidora->desc_corta);
    	   }
    	?>');
}
*/ ?>

function listaPeajesInit()
{
	$('#tipo_peaje').html('<b>Peaje de acceso a la red (ATR):</b> <?php
        foreach ($peajes as $peaje)
           {
               if($peaje->seleccionado)
                   echo addslashes($peaje->desc_corta);
           }
        ?>');
}

function crearInputPotenciaContratada(valor)
{
	return '<td style="text-align: center !important;">'+valor+' kW</td>';
}

function crearInputPotenciaNumeroDias(valor)
{
	return '<td>'+valor+' días</td>';
}

function crearInputPotenciaPrecio(valor)
{
	return '<td>'+valor+' €/kW día</td>';
}

function crearInputPotenciaImporte(valor)
{
	return '<td class="importe">'+valor+' €</td>';
}

function generarLineaP1TablaPotencia(cabecera)
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	<?php if($NumeroPeriodos>1): ?>
	salida+='        		<td><strong>P1</strong></td>';
	<?php endif; ?>
	salida+='        		'+crearInputPotenciaContratada('<?= $P1_potencia ?>');
	<?php /*
	salida+='        		'+crearInputPotenciaNumeroDias('<?= $P1_dias ?>');
	salida+='        		'+crearInputPotenciaPrecio('<?= $P1_precio_potencia ?>');
	salida+='        		'+crearInputPotenciaImporte('<?= $P1_importe_potencia ?>');
	*/ ?>
	salida+='        	</tr>';
	return salida;
}

<?php if($NumeroPeriodos>1): ?>
function generarLineaP2TablaPotencia()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P2</strong></td>';	
	salida+='        		'+crearInputPotenciaContratada('<?= $P2_potencia ?>');
	<?php /*
	salida+='        		'+crearInputPotenciaNumeroDias('<?= $P2_dias ?>');
	salida+='        		'+crearInputPotenciaPrecio('<?= $P2_precio_potencia ?>');
	salida+='        		'+crearInputPotenciaImporte('<?= $P2_importe_potencia ?>');
	*/ ?>
	salida+='        	</tr>';
	return salida;
}
<?php endif; ?>

<?php if($NumeroPeriodos>2): ?>
function generarLineaP3TablaPotencia()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P3</strong></td>';	
	salida+='        		'+crearInputPotenciaContratada('<?= $P3_potencia ?>');
	<?php /*
	salida+='        		'+crearInputPotenciaNumeroDias('<?= $P3_dias ?>');
	salida+='        		'+crearInputPotenciaPrecio('<?= $P3_precio_potencia ?>');
	salida+='        		'+crearInputPotenciaImporte('<?= $P3_importe_potencia ?>');
	*/ ?>
	salida+='        	</tr>';
	return salida;
}
<?php endif; ?>

<?php if($NumeroPeriodos>3): ?>
function generarLineaP4_6TablaPotencia()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P4</strong></td>';	
	salida+='        		'+crearInputPotenciaContratada('<?= $P4_potencia ?>');
	<?php /*
	salida+='        		'+crearInputPotenciaNumeroDias('<?= $P4_dias ?>');
	salida+='        		'+crearInputPotenciaPrecio('<?= $P4_precio_potencia ?>');
	salida+='        		'+crearInputPotenciaImporte('<?= $P4_importe_potencia ?>');
	*/ ?>
	salida+='        	</tr>';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P5</strong></td>';	
	salida+='        		'+crearInputPotenciaContratada('<?= $P5_potencia ?>');
	<?php /*
	salida+='        		'+crearInputPotenciaNumeroDias('<?= $P5_dias ?>');
	salida+='        		'+crearInputPotenciaPrecio('<?= $P5_precio_potencia ?>');
	salida+='        		'+crearInputPotenciaImporte('<?= $P5_importe_potencia ?>');
	*/ ?>
	salida+='        	</tr>';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P6</strong></td>';	
	salida+='        		'+crearInputPotenciaContratada('<?= $P6_potencia ?>');
	<?php /*
	salida+='        		'+crearInputPotenciaNumeroDias('<?= $P6_dias ?>');
	salida+='        		'+crearInputPotenciaPrecio('<?= $P6_precio_potencia ?>');
	salida+='        		'+crearInputPotenciaImporte('<?= $P6_importe_potencia ?>');
	*/ ?>
	salida+='        	</tr>';
	return salida;
}
<?php endif; ?>

function crearTablaPotenciaFacturada()
{
	var salida='';
	salida+='<table class="tablaresultado" width="100%">';
	salida+='	<tr>';
	salida+='	   <?= ($NumeroPeriodos>1) ? '<th scope="col" width="15%">Periodo</th>':''; ?>';
	salida+='		<th>Potencia (kW)</th>';
	<?php /*
	salida+='		<th>Días</th>';
	salida+='		<th>Precio (€/kW día)</th>';
	salida+='		<th>SUBTOTALES</th>';
	*/?>
	salida+='	</tr>';
	            <?php switch($Peaje):
	                 // Sin discriminación horaria.
	                 case '2.0A':
	                 case '2.1A': ?>
	salida+=generarLineaP1TablaPotencia();
	                 <?php break;
	                 // 2 periodos
	                 case '2.0DHA':
	                 case '2.1DHA': ?>
	salida+=generarLineaP1TablaPotencia();
	salida+=generarLineaP2TablaPotencia();
	                 <?php break;
	                 // 3 periodos
	                 case '2.0DHS':
	                 case '2.1DHS':
	                 case '3.0A':
	                 case '3.1A': ?>
	salida+=generarLineaP1TablaPotencia();
	salida+=generarLineaP2TablaPotencia();
	salida+=generarLineaP3TablaPotencia();
	                 <?php break;
	                 // 6 periodos
	                 case '6.1A':
	                 case '6.1B':
	                 case '6.2':
	                 case '6.3':
	                 case '6.4':
	                 case '6.5': ?>
	salida+=generarLineaP1TablaPotencia();
	salida+=generarLineaP2TablaPotencia();
	salida+=generarLineaP3TablaPotencia();
	salida+=generarLineaP4_6TablaPotencia();
                    <?php break; ?>
                <?php endswitch; ?>
    <?php /*
    salida+='<tr><td colspan="<?= ($NumeroPeriodos>1) ? 4:3; ?>" style="text-align: right !important; padding-right: 20px; font-weight: bold;">Total importe potencia hasta <?= $FechaFinalPeriodo ?></td><td class="total" style="font-weight: bold; font-size: 150%; padding-right: 4px; text-align: center !important;"><?= $P1_importe_potencia+$P2_importe_potencia+$P3_importe_potencia+$P4_importe_potencia+$P5_importe_potencia+$P6_importe_potencia ?></td></tr>';
    */ ?>
 	salida+='</table>';

 	return salida;
}

function crearInputEnergiaConsumida(valor)
{
	return '<td style="text-align: center !important;">'+valor+' kWh</td>';
}

function crearInputEnergiaPrecio(valor)
{
	return '<td>'+valor+' €/kWh</td>';
}

function crearInputEnergiaImporte(valor)
{
	return '<td class="importe">'+valor+' €</td>';
}

function generarLineaP1TablaEnergia()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	<?php if($NumeroPeriodos>1): ?>
	salida+='        		<td><strong>P1</strong></td>';
	<?php endif; ?>
	salida+='        		'+crearInputEnergiaConsumida('<?= $P1_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= $P1_precio_energia ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= $P1_importe_energia ?>');
	*/ ?>
	salida+='        	</tr>';
	return salida;
}

<?php if($NumeroPeriodos>1): ?>
function generarLineaP2TablaEnergia()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P2</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= $P2_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= $P2_precio_energia ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= $P2_importe_energia ?>');
	*/ ?>
	salida+='        	</tr>';
	return salida;
}
<?php endif; ?>

<?php if($NumeroPeriodos>2): ?>
function generarLineaP3TablaEnergia()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P3</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= $P3_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= $P3_precio_energia ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= $P3_importe_energia ?>');
	*/ ?>
	salida+='        	</tr>';
	return salida;
}
<?php endif; ?>

<?php if($NumeroPeriodos>3): ?>
function generarLineaP4_6TablaEnergia()
{
	var salida='';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P4</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= $P4_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= $P4_precio_energia ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= $P4_importe_energia ?>');
	*/ ?>
	salida+='        	</tr>';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P5</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= $P5_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= $P5_precio_energia ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= $P5_importe_energia ?>');
	*/ ?>
	salida+='        	</tr>';
	salida+='        	<tr class="filaDatos">';
	salida+='        		<td><strong>P6</strong></td>';	
	salida+='        		'+crearInputEnergiaConsumida('<?= $P6_energia ?>');
	<?php /*
	salida+='        		'+crearInputEnergiaPrecio('<?= $P6_precio_energia ?>');
	salida+='        		'+crearInputEnergiaImporte('<?= $P6_importe_energia ?>');
	*/ ?>
	salida+='        	</tr>';
	return salida;
}
<?php endif; ?>

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
	<?php $totalEnergia=0.0; ?>
	salida+='	</tr>';
	            <?php switch($Peaje):
	                 // Sin discriminación horaria.
	                 case '2.0A':
	                 case '2.1A':
	                      $totalEnergia+=str_replace(',','.',$P1_energia);
	            ?>
	salida+=generarLineaP1TablaEnergia();
	                 <?php break;
	                 // 2 periodos
	                 case '2.0DHA':
	                 case '2.1DHA':
	                     $totalEnergia+=str_replace(',','.',$P1_energia);
	                     $totalEnergia+=str_replace(',','.',$P2_energia);
	                     ?>
	salida+=generarLineaP1TablaEnergia();
	salida+=generarLineaP2TablaEnergia();
	                 <?php break;
	                 // 3 periodos
	                 case '2.0DHS':
	                 case '2.1DHS':
	                 case '3.0A':
	                 case '3.1A':
	                     $totalEnergia+=str_replace(',','.',$P1_energia);
	                     $totalEnergia+=str_replace(',','.',$P2_energia);
	                     $totalEnergia+=str_replace(',','.',$P3_energia);
	                     ?>
	salida+=generarLineaP1TablaEnergia();
	salida+=generarLineaP2TablaEnergia();
	salida+=generarLineaP3TablaEnergia();
	                 <?php break;
	                 // 6 periodos
	                 case '6.1A':
	                 case '6.1B':
	                 case '6.2':
	                 case '6.3':
	                 case '6.4':
	                 case '6.5':
	                     $totalEnergia+=str_replace(',','.',$P1_energia);
	                     $totalEnergia+=str_replace(',','.',$P2_energia);
	                     $totalEnergia+=str_replace(',','.',$P3_energia);
	                     $totalEnergia+=str_replace(',','.',$P4_energia);
	                     $totalEnergia+=str_replace(',','.',$P5_energia);
	                     $totalEnergia+=str_replace(',','.',$P6_energia);
	                     ?>
	salida+=generarLineaP1TablaEnergia();
	salida+=generarLineaP2TablaEnergia();
	salida+=generarLineaP3TablaEnergia();
	salida+=generarLineaP4_6TablaEnergia();
	                 <?php break; ?>
	                 <?php endswitch; ?>

    <?php
    $totalEnergia=number_format($totalEnergia,2,',','.');
    ?>
    <?php /*	                 
  	salida+='<tr><td colspan="<?= ($NumeroPeriodos>1) ? 3:2; ?>" style="text-align: right !important; padding-right: 20px; font-weight: bold;">Total <?= $totalEnergia ?> kWh hasta <?= $FechaFinalPeriodo ?></td><td class="total" style="font-weight: bold; font-size: 150%; padding-right: 4px; text-align: center !important;"><?= $totalEnergia ?></td></tr>';
    */ ?>
    salida+='<tr><td colspan="<?= ($NumeroPeriodos>1) ? 3:2; ?>" style="text-align: right !important; padding-right: 20px; font-weight: bold;">Total <?= $totalEnergia ?> kWh hasta <?= $FechaFinalPeriodo ?></td></tr>';  	
 	salida+='</table>';

	return salida;
}

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
	listaComercializadorasInit();
	//listaDistribuidorasInit();
	listaPeajesInit();
	$('#potenciafacturada').html(crearTablaPotenciaFacturada());
	$('#energiafacturada').html(crearTablaEnergiaFacturada());

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
<?php /*
		<p><b>Referencia Nº de contrato:</b> <?= $ReferenciaContrato ?></p>
*/?>		
<?php /*
    </div>
	
	<h3 class="titulo_2 titulo_seccion">DATOS RELACIONADOS CON EL SUMINISTRO</h3>
	<div style="padding: 4px;">
*/?>		
		<p id='empresa_comercializadora'></p>
<?php /*
		<p id='empresa_distribuidora'></p>
		<p><b>Número de contrato de acceso:</b> <?= $NumeroContrato ?></p>
*/ ?>
		<p><b>Identificación del punto de suministro (CUPS):</b> <?= $Cups ?></p>
		<p id='tipo_peaje'></p>
<?php /*
		<p><b>Fecha de vencimiento del contrato de acceso:</b> <?= $Vencimiento ?></p>
*/ ?>
    </div>
	
	<h3 class="titulo_2 titulo_seccion">DETALLE DE LOS CONSUMOS Y FACTURACIÓN</h3>
	<div style="padding: 4px;">
    	<h3>Potencia facturada</h3>
    	<div id="potenciafacturada"></div>
    </div>
	
	<h3 class="titulo_2 titulo_seccion">DETALLE DE LOS CONSUMOS Y FACTURACIÓN</h3>
	<div style="padding: 4px;">
    	<h3>Energía facturada</h3>
    	<div id="energiafacturada"></div>
    </div>
	
	<h3 class="titulo_2 titulo_seccion">RESUMEN DE FACTURACIÓN</h3>
	<div style="padding: 4px;">
		<?php /*
		<p><b>Importe energía:</b> <?= $TotalImporteEnergia ?> €</p>
		<p><b>Importe de Servicios y otros conceptos:</b> <?= $TotalImporteServicios ?> €</p>
		*/?>
		<p><b>Importe potencia:</b> <span class="money"><?= $TotalImportePotenciaFacturada ?></span></p>
		<p><b>Importe energía:</b> <span class="money"><?= $TotalImporteEnergiaFacturada ?></span></p>
		<p><b>Total a pagar:</b> <span class="money"><?= $TotalPagar ?></span></p>
    </div>
</div>
    
    <div style="margin-top:20px;"><a href="<?= $VolverUrl ?>" class="enlace volvericon">Volver</a></div>
    <!-- <div style="margin-top:20px;"><a href="javascript:history.back()" class="enlace volvericon">Volver</a></div> -->
    <div style="clear: both;"/>

</div>
<!-- FIN BLOQUE INTERIOR -->
