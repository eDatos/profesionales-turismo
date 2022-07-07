<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/inputfields.js"></script>
<script type="text/javascript" src="js/dates.js"></script>
<style>
.botoneraderecha {
	/*float: right;*/
	margin-right: 17px;
}
.alineado {
	margin: 2px 17px;
}
#detalleErrores {
	padding-left: 40px;
}
#detalleErrores ul {
	list-style-type: circle;
	list-style-position: inside;
}
/*
.tablaresultado tr:nth-child(4n+1) {
	background-color: #FBFBFB;
}
.tablaresultado tr:nth-child(4n+2) {
	background-color: #F2F2F2;
}
*/
.notas_plazo {
    display:block;
    margin-right: 2px;
    padding: 0 5px 0 5px;
}
label.campo {
    display:inline-block;
    width: 180px;
}
label.filtrado {
}
label.desde-fecha {
    display:inline-block;
    width: 40px;
}
label.hasta-fecha {
    display:inline-block;
    width: 40px;
    margin-left: 20px;
}
label.tablita {
    display:inline-block;
    width: 180px;
}
label.opcion-filtrado-estab {
}
label.campo-busqueda-estab {
    display:inline-block;
    width: 100%;
}

/*fieldset input,select {
    width: 250px;
}*/
.campo {
    width: 250px;
}
#tablita {
    padding-left: 40px;
}
.btnEliminar {
    float: right;
}
.btnEliminar span {
    line-height: normal !important;
}
.search span {
    line-height: normal !important;
}
.operationBtn {
    float: right;
    margin-left: 4px;
    margin-right: 4px;
}
.operationBtn span {
    line-height: normal !important;
}
.iconoSuministro {
    display: inline-block;
    width: 24px;
    height: 24px;
}
.ui-icon-clearForm { 
  width: 28px;
  height: 28px;
  padding: 2px;
  background-image: url(images/clean.png) !important;
  background-repeat: no-repeat;
  background-position: center;
}
</style>
<?php
$trims = array();
for ($i = 1; $i <= 12; $i++)
{
    $t = DateHelper::mes_tostring($i, "M");
    $trims[$i] = $i . " - " . $t;
}

$anios = array();
$a = date('Y');
for ($i = 5; $i >= 0; $i--)
{
    $anios[$i] = $a;
    $a -= 1;
}

define("ESTILO_OK", "okicon");
define("ESTILO_ERROR", "erroricon");

?>
<script type="text/javascript">

var suministrosInfo=[
<?php
   echo '["Cualquiera",null,false]'.PHP_EOL;
   foreach ($suministradoras as $suministradora)
   {
       echo ',["'.$suministradora->nombre.'","'.$suministradora->tipo.'",false]'.PHP_EOL;
   }
?>
];
var iconosSuministros=Array();
<?php foreach($iconosSuministros as $icono): ?>
iconosSuministros["<?= $icono->codigo ?>"]={
		'desc_corta': '<?= $icono->desc_corta ?>',
		'defecto': '<?= $icono->defecto ?>',
		'desc_larga': '<?= $icono->desc_larga ?>',
		'grupo': '<?= $icono->grupo ?>',
		'orden': '<?= $icono->orden ?>',
		'seleccionado': '<?= $icono->seleccionado ?>'
};
<?php endforeach; ?>

var listaEmpresasSuministradoras=[];
var listaTiposFacturas=[];

function selectSuministradorasChanged(empresa)
{
	$('#<?= ARG_TIPO_SUMINISTRO ?>').empty();
	//$('<option/>').val('Cualquiera').html('Cualquiera').appendTo('#<?= ARG_TIPO_SUMINISTRO ?>');
	$('<option/>').val('').html('Cualquiera').appendTo('#<?= ARG_TIPO_SUMINISTRO ?>');
	if(empresa=='Cualquiera')
	{
		for(var i=0;i<listaTiposFacturas.length;i++)
		{
			$('<option/>').val(listaTiposFacturas[i]).html(listaTiposFacturas[i]).appendTo('#<?= ARG_TIPO_SUMINISTRO ?>');
		}
	}
	else
	{
		for(var i=0;i<suministrosInfo.length;i++)
		{
			if(empresa==suministrosInfo[i][0])
			{
				$('<option/>').val(suministrosInfo[i][1]).html(suministrosInfo[i][1]).appendTo('#<?= ARG_TIPO_SUMINISTRO ?>');
			}
		}
	}
}

function selectsInit()
{
	// Primero generamos las listas de empresas y tipos de facturas.
	var salida=[];
	for(var i=0;i<suministrosInfo.length;i++)
	{
		var nombre=suministrosInfo[i][0];
		if(listaEmpresasSuministradoras.indexOf(nombre)==-1)
			listaEmpresasSuministradoras.push(nombre);
		var tipo=suministrosInfo[i][1];
		if(tipo!=null)
		{
    		if(listaTiposFacturas.indexOf(tipo)==-1)
    			listaTiposFacturas.push(tipo);
		}
	}

	<?php /*
	// Inicializamos el desplegable de empresas
	$('#<?= ARG_SUMINISTRADORA ?>').empty();
	for(var i=0;i<listaEmpresasSuministradoras.length;i++)
	{
		var opcion='<option value="'+((i==0)?'':listaEmpresasSuministradoras[i])+'"';
		opcion+='>'+listaEmpresasSuministradoras[i]+'</option>';
		$('#<?= ARG_SUMINISTRADORA ?>').append(opcion);
	}
	$(document).on('change', '#<?= ARG_SUMINISTRADORA ?>', function(event) {
		var empresa = $(this).val();
		selectSuministradorasChanged(empresa);
	});
	*/?>

	// Inicializamos el desplegable de tipos de facturas
	selectSuministradorasChanged('Cualquiera');
}

function mostrarAvisoFacturaBorrada(mensaje)
{
	$( "#dialog-borrado-aviso" ).dialog(
			{
				resizable: false,
				modal: true,
				width: 300,
				title : 'BORRAR FACTURA',
				position : { my: "center", at: "center", of: window },
				buttons: {
					"Aceptar": function() {
						$( this ).dialog( "close" );
					}
				},
				close: function(event, ui) {
					$( this ).dialog( "destroy" );
					$("#msg_borrado-aviso").html("");
				}
			}
		);
	$("#msg_borrado-aviso").html(mensaje);
	$( "#dialog-borrado-aviso" ).dialog("open");
}

function VentanaConfirmBorrado(titulo, aviso, oncontinuar)
{
	var mensaje='<div class="errmsg">';
	mensaje+='<h3>ATENCIÓN:<h3>';
	mensaje+=aviso;
	mensaje+='</div>';
	$( "#dialogo_aviso" ).dialog( "destroy" );
	$( "#dialogo_aviso" ).remove();
	$('body').append('<div id="dialogo_aviso" style="text-align: left">'+mensaje+'</div>');
	
	$( "#dialogo_aviso" ).css('max-height', '400px');
	$( "#dialogo_aviso" ).dialog({
    	autoOpen: true,
        resizable: false,
        modal: true,
        width: 800,
        title: titulo,
        position : { my: "center", at: "center", of: window },
    	buttons: {
            "Continuar": function() {
            	$( this ).dialog( "close" ).remove();
            	oncontinuar();
            },
            "Cancelar" : function() {
            	$(this).dialog("close").remove();
            }
        },
        close: function(event, ui) {
			try
			{
				$( "#dialogo_aviso" ).dialog( "destroy" );
				$( "#dialogo_aviso" ).remove();							
			}
			finally
			{
			}
    	}
    });
}

function borrarFactura(estid,numfactura,onexito)
{
	try
	{
		$.ajax({
			type: "POST",
			//url: '#', 
			dataType: 'JSON',
			data: {
				<?= ARG_DATA ?>: {
					'<?= ARG_ESTID ?>' : estid,
					'numfactura' : numfactura
				},
				<?= ARG_OP." : '".OP_BORRAR_FACTURA."'" ?>
			},
			success: function(respuesta)
			{
				try
				{
					<?php /*
					Como respuesta, se espera un objeto JSON como el siguiente:
					{
						error: true/false,
						mensaje: "ok"/"Mensaje de error específico",
					}
					*/ ?>
					if(respuesta.error)
					{
						<?php /*
						// La petición ha fallado. Informar al usuario.
						
						// Posibles mensajes:
						// ** La factura ya ha sido enviada y no es modificable. Para realizar cambios a una factura ya enviada, debe contactar con el Instituto Canario de Estadística. 
						// ** La petición ha fallado (error interno).
						*/ ?>
						alert(respuesta.mensaje);
					}
					else
					{
						onexito();
						mostrarAvisoFacturaBorrada('Factura Nº <strong>'+numfactura+'</strong> borrada correctamente.');
					}
				}
				catch(err)
				{
					alert(err.message);
				}
			},
			error: function(xhr) {
				var txt="Ocurrió un error al realizar la petición: (" + xhr.status + ") " + xhr.statusText;
				if(xhr.status==403)
					txt+="\n\nATENCIÓN: se ha perdido la sesión. Los datos que introduzca no serán guardados.\nPulse el enlace Salir para autentificarse nuevamente.";
				alert (txt);
			}
		}); 		    
	}
	catch(err)
	{
		alert(err.message);
	}
}

function radioButtonEstablecimientosUIupdate()
{
	if($('#est_todos').is(':checked'))
	{
		$('#est_codigos').attr('disabled','disabled');
		$('#est_nombres').attr('disabled','disabled');
		$('#div_est_codigos').hide();
		$('#div_est_nombres').hide();
		return;
	}
	if($('#est_codigo').is(':checked'))
	{
		$('#est_codigos').removeAttr('disabled');
		$('#est_nombres').attr('disabled','disabled');
		$('#div_est_codigos').show();
		$('#div_est_nombres').hide();
		return;
	}
	if($('#est_nombre').is(':checked'))
	{
		$('#est_nombres').removeAttr('disabled');
		$('#est_codigos').attr('disabled','disabled');
		$('#div_est_codigos').hide();
		$('#div_est_nombres').show();
		return;
	}
}

function OperationBtnFn(e)
{
	var numfactura=$(this).data('numfactura');
	var estid=$(this).data('estid');
	var estnombre=$(this).data('estnombre');
	var cerrada=$(this).data('cerrada');
	var titulo="MODIFICAR FACTURA";
	<?php if($esAdmin): ?>
	var aviso='<p>La factura Nº <strong>'+numfactura+'</strong>, del establecimiento "('+estid+') '+estnombre+'" va a ser '+((cerrada=='S')?'reabierta y ':'')+'modificada.<br/><br/>Por favor, confirme la operación.</p>';
	<?php else: ?>
	var aviso='<p>La factura Nº <strong>'+numfactura+'</strong> va a ser '+((cerrada=='S')?'reabierta y ':'')+'borrada.<br/><br/>Por favor, confirme la operación.</p>';
	<?php endif; ?>

	var formulario=$(this).closest('form');
	VentanaConfirmBorrado(titulo, aviso, function(){
		formulario.submit();
	});
	return false;
}

function BtnEliminarFn(e)
{
	var numfactura=$(this).data('numfactura');
	var estid=$(this).data('estid');
	var estnombre=$(this).data('estnombre');
	
	var titulo="BORRAR FACTURA";
	<?php if($esAdmin): ?>
	var aviso='<p>La factura Nº <strong>'+numfactura+'</strong>, del establecimiento "('+estid+') '+estnombre+'" va a ser borrada.<br/><br/>Por favor, confirme la operación.</p>';
	<?php else: ?>
	var aviso='<p>La factura Nº <strong>'+numfactura+'</strong> va a ser borrada.<br/><br/>Por favor, confirme la operación.</p>';
	<?php endif; ?>

	var fila=$(this).closest ('tr');
	var tabla=fila.parent();
	VentanaConfirmBorrado(titulo, aviso, function(){
		borrarFactura(estid,numfactura,function(){
			fila.remove();
			if(tabla.prop('rows').length==1)
			{
				// La tabla ha quedado vacía.
				$('#tabla_resultados').html('<div>No se encuentra ninguna factura con esos criterios.</div>');
			}
		});
	});
	//e.preventDefault();
	return false;
}

function ClearFormBtnFn(e)
{
	<?php /* $('select[name="<?= ARG_SUMINISTRADORA ?>"]').val(''); */?>
	$('select[name="<?= ARG_TIPO_SUMINISTRO ?>"]').val('');
	$('#<?= ARG_NUMERO_FACTURA ?>').val('');
	$('select[name="<?= ARG_MES_FACTURA ?>"]').val('0');
	$('select[name="<?= ARG_ANO_FACTURA ?>"]').val('0');
	$('#<?= ARG_FECHA_FACTURACION_DESDE ?>').val('');
	$('#<?= ARG_FECHA_FACTURACION_HASTA ?>').val('');
	<?php if($esAdmin): ?>
	$("#est_todos").click();
	$('input[name="<?= ARG_NOMBRE_EST ?>"]').val('');
	$('input[name="<?= ARG_CODIGO_EST ?>"]').val('');
	<?php endif; ?>
	$("#formulario").validate().resetForm();
	e.preventDefault();
	return false;
}

function escapeHtml(text)
{
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function cargarResultados(resultados)
{
	var salida='';
	if(resultados.length==0)
	{
		salida='No se encuentra ninguna factura con esos criterios.';
	}
	else
	{
		salida+='<table class="tablaresultado" width="100%">';
		salida+='  <tr>';
		<?php if($esAdmin): ?>
		salida+='	<th scope="col">CODIGO</th>';
		salida+='	<th scope="col">ESTABLECIMIENTO</th>';
		<?php endif; ?>
		salida+='	<th style="text-align: center" scope="col">Nº FACTURA</th>';
		salida+='	<th style="text-align: center" scope="col">FECHA REGISTRO</th>';
		salida+='	<th style="text-align: center" scope="col">TIPO</th>';
		salida+='	<th style="text-align: center" scope="col">CERRADA</th>';
		salida+='	<th style="text-align: center" scope="col">OPERACIONES</th>';
		salida+='  </tr>';

		for(var i=0;i<resultados.length;i++)
		{
			salida+='	  <tr nombre="'+resultados[i].num_factura+'">';
			if(resultados[i].cerrada)
			{
    			<?php if($esAdmin): ?>
    			salida+='		<td style="width:4%; text-align: center">'+resultados[i].id_consumo+'</td>';
    			salida+='		<td style="width:35%;">('+ resultados[i].id_establecimiento + ') ' + resultados[i].nombre_establecimiento +'</td>';
    			var link="<?= $navToUrl.'?'.ARG_PARTE.'=1&'.ARG_ESTID ?>="+resultados[i].id_establecimiento+"&<?= ARG_NUMERO_FACTURA?>="+resultados[i].num_factura;
    			<?php else: ?>
    			var link="<?= $navToUrl.'?'.ARG_NUMERO_FACTURA?>="+resultados[i].num_factura;
    			<?php endif; ?>
    			salida+='		<td style="width:10%; text-align: center"><a title="Ver factura" href="'+link+'">'+resultados[i].num_factura+'</a></td>';
			}
			else
			{
    			salida+='		<td style="width:10%; text-align: center">'+resultados[i].num_factura+'</td>';
			}
			var d=new Date(resultados[i].fecha.date);
			salida+='		<td style="text-align: center">'+('0'+d.getDate()).slice(-2) + '/' + ('0'+(d.getMonth()+1)).slice(-2) + '/' + d.getFullYear()+'</td>';
			salida+='		<td style="text-align: center">';
			if(iconosSuministros[resultados[i].tipo]==null)
			{
				salida+=resultados[i].tipo;
			}
			else
			{
				salida+='		<img class="iconoSuministro" src="images/consumos/'+iconosSuministros[resultados[i].tipo].desc_corta+'" alt="'+iconosSuministros[resultados[i].tipo].desc_larga+'" title="'+iconosSuministros[resultados[i].tipo].desc_larga+'"/>';
			}
			salida+='		</td>';
			salida+='		<td style="text-align: center">'+ (resultados[i].cerrada ? 'Sí':'No') +'</td>';
			salida+='		<td style="width:25%;">';
			salida+='		<div class="botoneraderecha">';
			if(<?= ($esAdmin) ? 'true':'resultados[i].cerrada==false' ?>)
			{
				salida+='		<!-- Botón modificar -->';
				salida+='		<form name="df" action="<?= $urlModificacion ?>" method="post">';
				salida+='		<input name="<?= ARG_PARTE ?>" type="hidden" value="1">';
				salida+='		<input name="<?= ARG_ESTID ?>" type="hidden" value="'+resultados[i].id_establecimiento+'">';
				salida+='		<input name="<?= ARG_NUMERO_FACTURA ?>" type="hidden" value="'+resultados[i].num_factura+'">';
				salida+='		<a href="22#" name="operationBtn" class="operationBtn" role="button" data-numfactura="'+resultados[i].num_factura+'" data-estid="'+resultados[i].id_establecimiento+'" data-estnombre="'+ escapeHtml(resultados[i].nombre_establecimiento) +'" data-cerrada="'+(resultados[i].cerrada ? 'S':'N')+'" title="Reabrir y/o Modificar factura">Modificar</a>';
				salida+='		</form>';
    			salida+='		<!-- Botón eliminar -->';
    			salida+='		<a role="button" title="Borrar factura" class="btnEliminar" href="22#" data-numfactura="'+resultados[i].num_factura+'" data-estid="'+resultados[i].id_establecimiento+'" data-estnombre="'+ escapeHtml(resultados[i].nombre_establecimiento) +'">Eliminar</a>';
    			salida+='		</div>';
    			salida+='		</td>';
    			salida+='	   </tr>';
			}
		}
		salida+='</table>';
	}
	$('#tabla_resultados').html(salida);
	$('#tabla_resultados').find('.operationBtn').each(initBotonModificarItem);
	$('#tabla_resultados').find('.btnEliminar').each(initBotonEliminarItem);
	location.href = "#";
	location.href = "#tabla_resultados";
}

function EnviarForm()
{
	try
	{
		<?php /* //$('#formulario').validate(); */ ?>
		if($('#formulario').valid()==false)
		{
			alert('Existen errores en los datos. Búsqueda cancelada.\nPor favor, corrija los errores antes de realizar la búsqueda.');
			return false;
		}
		// Recogemos los parámetros de la búsqueda
		<?php /*
		var suministradora=$('select[name="<?= ARG_SUMINISTRADORA ?>"] option:selected').val();
		*/?>
		var suministradora='';
		var tipoSuministro=$('select[name="<?= ARG_TIPO_SUMINISTRO ?>"] option:selected').val();
		var numeroFactura=$('#<?= ARG_NUMERO_FACTURA ?>').val().trim();
		var mes=$('select[name="<?= ARG_MES_FACTURA ?>"] option:selected').val();
		var anyo=$('select[name="<?= ARG_ANO_FACTURA ?>"] option:selected').val();
		var fechaFactDesde=$('#<?= ARG_FECHA_FACTURACION_DESDE ?>').val();
		var fechaFactHasta=$('#<?= ARG_FECHA_FACTURACION_HASTA ?>').val();
		<?php if($esAdmin): ?>
		var estSelec=$('input[name="<?= ARG_SELEC_EST ?>"]:checked').val().trim();
		var estNombre=$('input[name="<?= ARG_NOMBRE_EST ?>"]').val();
		var estCodigo=$('input[name="<?= ARG_CODIGO_EST ?>"]').val();
		<?php endif; ?>
		$.ajax({
			type: "POST",
			//url: '#', 
			dataType: 'JSON',
			data: {
				<?= ARG_DATA ?>: {
					'<?= ARG_SUMINISTRADORA ?>' : suministradora,
					'<?= ARG_TIPO_SUMINISTRO ?>' : tipoSuministro,
					'<?= ARG_NUMERO_FACTURA ?>' : numeroFactura,
					'<?= ARG_MES_FACTURA ?>' : mes,
					'<?= ARG_ANO_FACTURA ?>' : anyo,
					'<?= ARG_FECHA_FACTURACION_DESDE ?>' : fechaFactDesde,
					'<?= ARG_FECHA_FACTURACION_HASTA ?>' : fechaFactHasta<?php if($esAdmin): ?>,
					'<?= ARG_SELEC_EST ?>' : estSelec,
					'<?= ARG_CODIGO_EST ?>' : estCodigo,
					'<?= ARG_NOMBRE_EST ?>' : estNombre
					<?php endif; ?>
				},
				<?= ARG_OP." : '".OP_BUSCAR_FACTURAS."'" ?>
			},
			success: function(respuesta)
			{
				try
				{
					<?php /*
					Como respuesta, se espera un objeto JSON como el siguiente:
					{
						error: true/false,
						mensaje: "ok"/"Mensaje de error específico",
						resultados : [         // Sólo si error==false
						     {
						         'id_consumo' : xxx,
						         'id_establecimiento' : xxx,
						         'nombre_establecimiento' : xxx,
						         'id_usuario' : xxx,
						         'num_factura' : xxx,
						         'fecha' : xxx,                      // Fecha en formato d/m/Y
						         'tipo' : xxx,
						         'cerrada' : xxx                     // xxx={true,false}
						     },
						     ...
						]
					}
					*/ ?>
					if(respuesta.error)
					{
						<?php /*
						// La petición ha fallado. Informar al usuario.
						
						// Posibles mensajes:
						// ** La factura ya ha sido enviada y no es modificable. Para realizar cambios a una factura ya enviada, debe contactar con el Instituto Canario de Estadística. 
						// ** La petición ha fallado (error interno).
						*/ ?>
						alert(respuesta.mensaje);
					}
					else
					{
						cargarResultados(respuesta.resultados);
					}
				}
				catch(err)
				{
					alert(err.message);
				}
			},
			error: function(xhr) {
				var txt="Ocurrió un error al realizar la petición: (" + xhr.status + ") " + xhr.statusText;
				if(xhr.status==403)
					txt+="\n\nATENCIÓN: se ha perdido la sesión. Los datos que introduzca no serán guardados.\nPulse el enlace Salir para autentificarse nuevamente.";
				alert (txt);
			}
		}); 		    
	}
	catch(err)
	{
		alert(err.message);
	}
}

function initBotonEliminarItem()
{
	$(this).button({icons: {primary: 'ui-icon-trash'}}).click(BtnEliminarFn);
	//$(this).button().click(BtnEliminarFn);
}

function initBotonModificarItem()
{
	$(this).button({icons: {primary: 'ui-icon-pencil'}}).click(OperationBtnFn);
	//$(this).button().click(OperationBtnFn);
}

function initBotonBuscar()
{
	$(this).button({icons: {primary: 'ui-icon-search'}}).click(function(ev){
		EnviarForm();
		return false;
	});
	/*
	$(this).button().click(function(ev){
    	EnviarForm();
    	return false;
	});
	*/
}

function setupValidacion()
{
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

	$("#formulario").validate({
		rules: {
			<?= ARG_FECHA_FACTURACION_DESDE ?>: {
				/*required: false,*/
				nofutura: true
			},
			<?= ARG_CODIGO_EST ?>: {
				required: {
					depends: function(elem) {
						return $('#est_codigo').is(':checked');
					}/*,
					message: 'Ha de introducir el código de un establecimiento para realizar la búsqueda'*/
				}
			},
			<?= ARG_NOMBRE_EST ?>: {
				required: {
					depends: function(elem) {
						return $('#est_nombre').is(':checked');
					}/*,
					message: 'Ha de introducir parte del nombre del establecimiento para realizar la búsqueda'*/
				}
			},
			<?= ARG_FECHA_FACTURACION_HASTA ?>: {
				rangofechas: "#<?= ARG_FECHA_FACTURACION_DESDE ?>"
			}
		}, 
		wrapper: "p",
    	errorPlacement: function(error, element) {
        	error.appendTo(element.parent());
        	error.addClass("validmsg");
    	}
	});
}

$(document).ready( function() {
	$.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
	$(".datepicker").datepicker( { dateFormat: "<?= DateHelper::getDateFormat("datepicker") ?>" } );
	selectsInit();
	<?php if($esAdmin): ?>
    	$('input[name="<?= ARG_SELEC_EST ?>"]').change(function(){
    		radioButtonEstablecimientosUIupdate();
    	});
    	radioButtonEstablecimientosUIupdate();
	<?php endif; ?>

	<?php /* $('#searchFactura').button(); */ ?>
	$('#searchFactura2').each(initBotonBuscar);
	
	//$('#clearForm').button({icons: {primary: 'ui-icon-clearForm'}, text: false});
	$('#clearForm').button().click(ClearFormBtnFn);

	$('.operationBtn').each(initBotonModificarItem);
	$('.btnEliminar').each(initBotonEliminarItem);
	
	setupValidacion();
});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1"><?= USER_TITLE ?></h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
		<form name="formulario" id="formulario" action="#" method="post">
		<input type="hidden" id="tipo_busqueda" name="tipo_busqueda" value="" />
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Seleccionar parámetros de la búsqueda</h2>
	      <div class="subrayado"></div>
	      <fieldset>
            <table style="margin-left:10px;width:99%">
            	<?php /*
				<tr>
				<td>
				<label class="campo" for="<?= ARG_SUMINISTRADORA ?>"><b>Empresa suministradora:</b> </label>
				<select class="campo" id="<?= ARG_SUMINISTRADORA ?>" name="<?= ARG_SUMINISTRADORA ?>"></select>
				</td>
				</tr>
            	*/?>
				<tr>
				<td>
            	<label class="campo" for="<?= ARG_TIPO_SUMINISTRO ?>"><b>Tipo de suministro:</b> </label>
            	<select class="campo" id="<?= ARG_TIPO_SUMINISTRO ?>" name="<?= ARG_TIPO_SUMINISTRO ?>"></select>
				</td>
				</tr>
				<tr>
				<td>
            	<label class="campo" for="<?= ARG_NUMERO_FACTURA ?>"><b>Nº de factura:</b> </label>
            	<input class="campo" type="text" name="<?= ARG_NUMERO_FACTURA ?>" id="<?= ARG_NUMERO_FACTURA ?>" value=""/>
				</td>
				</tr>
				<tr>
				<td>
				<label class="campo" for="<?= ARG_MES_FACTURA ?>"><b>Mes de registro de la factura:</b> </label>
				<select class="campo" name="<?= ARG_MES_FACTURA ?>">
				<option value="0">Cualquier mes</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                     <option value="<?= sprintf('%02d', $i); ?>"><?= $trims[$i] ?></option>                     
                <?php endfor; ?>
                </select>
                </td>
				</tr>
				<tr>
				<td>
				<label class="campo" for="<?= ARG_ANO_FACTURA ?>"><b>Año de registro de la factura:</b> </label>
				<select class="campo" name="<?= ARG_ANO_FACTURA ?>">
				<option value="0">Cualquier año</option>
                <?php foreach($anios as $ano): ?>
                     <option value="<?= $ano ?>"><?= $ano ?></option>
                <?php endforeach; ?>
                </select>
                </td>
				</tr>
				<tr><td>
                <span class="campo" style="display: inline-block"><b>Periodo de facturación:</b> </span>
                <label class="desde-fecha" for="<?= ARG_FECHA_FACTURACION_DESDE ?>">Desde: </label>
                <input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" id="<?= ARG_FECHA_FACTURACION_DESDE ?>" name="<?= ARG_FECHA_FACTURACION_DESDE ?>" type="text" value="" style="width:90px"/>
                <label class="hasta-fecha" for="<?= ARG_FECHA_FACTURACION_HASTA ?>">Hasta: </label>
                <input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" id="<?= ARG_FECHA_FACTURACION_HASTA ?>" name="<?= ARG_FECHA_FACTURACION_HASTA ?>" type="text" value="" style="width:90px"/><br/>
				</td></tr>
				<?php if($esAdmin): ?>
				<tr>
				<td>
    				<label class="tablita" for="tablita"><b>Filtrado por establecimiento:</b> </label>
        			<table id="tablita" width="auto" border="0">
                      <tr>
                      <td>
                      	<div><input title="No importa el establecimiento" type="radio" id="est_todos" name="<?= ARG_SELEC_EST ?>" value="todos" checked><label class="opcion-filtrado-estab" for="<?= ARG_SELEC_EST ?>">Cualquier establecimiento</label></div>
        			  </td>
                      </tr>
                      <tr>
                        <td>
                          	<div>
                          	<input title="Filtrar por código de establecimiento" type="radio" id="est_codigo" name="<?= ARG_SELEC_EST ?>" value="codigo"><label class="opcion-filtrado-estab" for="<?= ARG_SELEC_EST ?>">Buscar por código</label><br>
                          	<div id="div_est_codigos" style="padding-left: 40px;">
                              	<label class="campo-busqueda-estab" for="<?= ARG_CODIGO_EST ?>">Introduzca los códigos de los establecimientos buscados, separados por comas, en la casilla siguiente:</label>
                            	<input name="<?= ARG_CODIGO_EST ?>" id="est_codigos" type="text" value="<?= @$estab_codigo ?>" size="92"/>
                          	</div>
                        	</div>
                    	</td>
                      </tr>
                      <tr>
                        <td>
                          	<div>
                          	<input title="Filtrar por nombre de establecimiento" type="radio" id="est_nombre" name="<?= ARG_SELEC_EST ?>" value="nombre"><label class="opcion-filtrado-estab" for="<?= ARG_SELEC_EST ?>">Buscar por nombre</label><br>
                          	<div id="div_est_nombres" style="padding-left: 40px;">
                              	<label class="campo-busqueda-estab" for="<?= ARG_NOMBRE_EST ?>">Busque el establecimiento introduciendo el nombre del establecimiento o parte del mismo:</label>
                            	<input name="<?= ARG_NOMBRE_EST ?>" id="est_nombres" type="text" value="<?= @$estab_nombre ?>" size="92" maxlength="92"/>
                          	</div>
                        	</div>
                    	</td>
                      </tr>
                    </table>
				</td>
				</tr>
				<?php endif; ?>
				<tr>
				<td style="text-align: right;">
					<button title="Limpiar formulario" class="ui-icon-clearForm search" id="clearForm"></button>
					<?php /* <button title="Buscar facturas" class="search" id="searchFactura" type="submit"><b>Buscar facturas</b></button> */ ?>
					<button title="Buscar facturas" class="search" id="searchFactura2" type="submit"><b>Buscar facturas</b></button>
				</td>
				</tr>
            </table>
            </fieldset>
		</div>
		</form>
		<!-- FIN CAJA AMARILLA --> 
        <!-- COMIENZO BLOQUE RESULTADOS DE LA BUSQUEDA -->
		<div id="resultado">
			<div>
		    <h2 style="display: inline-block" class="titulo_2">Facturas registradas</h2>
		    </div>
        <div class="subrayado"></div>
		<?php if (count($data) > 0) : ?>
                  <!-- tabla de resultados -->
                  <div id="tabla_resultados">
                        <table class="tablaresultado" width="100%">
                          <tr>
                            <?php if($esAdmin): ?>
                            <th scope="col">CODIGO</th>
                            <th scope="col">ESTABLECIMIENTO</th>
                            <?php endif; ?>
                            <th style="text-align: center" scope="col">Nº FACTURA</th>
                            <th style="text-align: center" scope="col">FECHA REGISTRO</th>
                            <th style="text-align: center" scope="col">TIPO</th>
                            <th style="text-align: center" scope="col">CERRADA</th>
                            <th style="text-align: center" style="text-align: center" scope="col">OPERACIONES</th>
                          </tr>
                          <?php foreach ($data as $row): ?>
                              <tr nombre="<?= $row->num_factura ?>">
                              	<?php if($esAdmin): ?>
                              	<td style="width:4%; text-align: center"><?= $row->id_consumo ?></td>
                              	<td style="width:35%;">(<?= $row->id_establecimiento ?>) <?= $row->nombre_establecimiento ?></td>
                              	<?php endif; ?>
                              	<td style="width:10%; text-align: center"><a title="Ver factura" href="<?= ($esAdmin) ? $this->build_url( $navToUrl, array(ARG_PARTE=>'1', ARG_ESTID => $row->id_establecimiento, ARG_NUMERO_FACTURA=>$row->num_factura)) : $this->build_url( $navToUrl, array(ARG_NUMERO_FACTURA=>$row->num_factura)) ?>"><?= $row->num_factura ?></a></td>
                              	<td style="text-align: center"><?= $row->fecha->format('d/m/Y') ?></td>
                              	<td style="text-align: center">
                              	<?php
                              	if(isset($iconosSuministros[$row->tipo]))
                              	{
                              	    echo '<img class="iconoSuministro" src="images/consumos/'.$iconosSuministros[$row->tipo]->desc_corta.'" alt="'.$iconosSuministros[$row->tipo]->desc_larga.'" title="'.$iconosSuministros[$row->tipo]->desc_larga.'"/>';
                              	}
                              	else
                              	    echo $row->tipo;
                              	?>
                              	</td>
                              	<td style="text-align: center"><?= $row->cerrada ? 'Sí':'No' ?></td>
                              	
                              	<td style="width:20%;">
                              	<div class="botoneraderecha">
                              	<?php if(($esAdmin) || ($row->cerrada==false)): ?>
                              	<!-- Botón modificar -->
                              	<form name="df" action="<?= $urlModificacion ?>" method="post">
                              	<input name="<?= ARG_PARTE ?>" type="hidden" value="1">
                              	<input name="<?= ARG_ESTID ?>" type="hidden" value="<?= $row->id_establecimiento ?>">
                              	<input name="<?= ARG_NUMERO_FACTURA ?>" type="hidden" value="<?= $row->num_factura ?>">
                              	<input name="operationBtn" class="operationBtn" role="button" data-numfactura="<?= $row->num_factura ?>" data-estid="<?= $row->id_establecimiento ?>" data-estnombre="<?= htmlspecialchars($row->nombre_establecimiento) ?>" data-cerrada="<?= ($row->cerrada)?'S':'N' ?>" type="submit" title="Reabrir y/o Modificar factura" value="Modificar"/>
                              	</form>
                              	<!-- Botón eliminar -->
                              	<?php endif; ?>
                              	<a role="button" title="Borrar factura" class="btnEliminar" href="22#" data-numfactura="<?= $row->num_factura ?>" data-estid="<?= $row->id_establecimiento ?>" data-estnombre="<?= htmlspecialchars($row->nombre_establecimiento) ?>">Eliminar</a>
                              	</div>
                              	</td>
            				   </tr>
                          <?php endforeach; ?>                                         
                        </table>
                   </div>
        <?php else: ?>
              <div id="tabla_resultados">Para localizar facturas, seleccione los criterios de búsqueda y pulse el botón <i>Buscar facturas</i>.</div>
        <?php endif; ?>
		</div>
        <!-- FIN BLOQUE RESULTADOS DE LA BUSQUEDA -->       
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->


</div>
<div id="dialog-borrado-aviso" title="Resultado de la operación de borrado" >
	<div id="msg_borrado-aviso" style="text-align: left;margin-top:15px;"></div>
</div>
<!-- FIN BLOQUE INTERIOR -->