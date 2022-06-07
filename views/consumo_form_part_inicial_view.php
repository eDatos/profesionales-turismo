<style>
.errmsg {
    font-weight: bold;
}
.botonera {
    margin-top: 10px;
}
.botonera input[type=button] {
    font-weight: bold;
}
.titulo_seccion {
    padding-left: 10px;
}
label.error {
   float: none;
   color: red;
   padding-left: .5em;
   vertical-align: middle;
   text-align: left important!;
   display: block !important;
   width: max-content !important;
   margin-left: 200px;
   height: inherit;
}
</style>
<script type="text/javascript">
// Hay que usar delegación de eventos para los nuevos elementos cargados dinámicamente.
// https://stackoverflow.com/questions/16598213/how-to-bind-events-on-ajax-loaded-content

var numeroFactura='<?= (isset($NumeroFactura)) ? $NumeroFactura:'' ?>';
var idEstablecimiento='<?= $idEstablecimiento ?>';
var ajaxURL='<?= $urlNext ?>';
var soloLectura=false;

function validarForm()
{
	//var suministradora=$('select[name="<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>"] option:selected').val();
	//var tipoSuministro=$('select[name="<?= VAR_FACTURA_TIPO_SUMINISTRO ?>"] option:selected').val();
	//var numeroFactura=$('#<?= VAR_FACTURA_NUMERO_FACTURA ?>').val().trim();
	if($('#formdata').valid()==false)
	{
		alert('Existen errores en los datos. Búsqueda cancelada.\nPor favor, corrija los errores antes de realizar la búsqueda.');
		return false;
	}
	
	return true;
}

var empresaSuministradora='<?= $empresaSuministradora ?>';
var tipoFactura='<?= $tipoFactura ?>';
var suministrosInfo=[
<?php
   foreach ($suministradoras as $suministradora)
   {
       $seleccionado=(($empresaSuministradora==$suministradora->nombre)&&($tipoFactura==$suministradora->tipo));
       echo '["'.$suministradora->nombre.'","'.$suministradora->tipo.'",'.(($seleccionado)?'true':'false').'],'.PHP_EOL;
   }
   //echo '["OTRA",null,false]'.PHP_EOL;
?>
];
var listaEmpresasSuministradoras=[];
var listaTiposFacturas=[];

function selectTipoFacturaChanged(tipo)
{
	<?php /*
	$('#<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>').empty();
	for(var i=0;i<suministrosInfo.length;i++)
	{
		if(tipo==suministrosInfo[i][1])
		{
			$('<option/>').val(suministrosInfo[i][0]).html(suministrosInfo[i][0]).appendTo('#<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>');
		}
	}
	*/?>
}

<?php /*
function selectSuministradorasChanged(empresa)
{
	$('#<?= VAR_FACTURA_TIPO_SUMINISTRO ?>').empty();
	if(empresa=='OTRA')
	{
		for(var i=0;i<listaTiposFacturas.length;i++)
		{
			$('<option/>').val(listaTiposFacturas[i]).html(listaTiposFacturas[i]).appendTo('#<?= VAR_FACTURA_TIPO_SUMINISTRO ?>');
		}
	}
	else
	{
		for(var i=0;i<suministrosInfo.length;i++)
		{
			if(empresa==suministrosInfo[i][0])
			{
				$('<option/>').val(suministrosInfo[i][1]).html(suministrosInfo[i][1]).appendTo('#<?= VAR_FACTURA_TIPO_SUMINISTRO ?>');
			}
		}
	}
}
*/?>

function cargarListas()
{
	// Generamos las listas de empresas y tipos de facturas.
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
}

function selectsInit()
{
	// Primero generamos las listas de empresas y tipos de facturas.
	cargarListas();

	// Incializamos el desplegable de tipos de factura
	$('#<?= VAR_FACTURA_TIPO_SUMINISTRO ?>').empty();
	for(var i=0;i<listaTiposFacturas.length;i++)
	{
		var opcion='<option value="'+listaTiposFacturas[i]+'"';
		if(tipoFactura==listaTiposFacturas[i])
			opcion+=' selected="selected"';
		opcion+='>'+listaTiposFacturas[i]+'</option>';
		$('#<?= VAR_FACTURA_TIPO_SUMINISTRO ?>').append(opcion);
	}
	$(document).on('change', '#<?= VAR_FACTURA_TIPO_SUMINISTRO ?>', function(event) {
		var tipo = $(this).val();
		selectTipoFacturaChanged(tipo);
	});

	<?php /*
	// Inicializamos el desplegable de empresas suministradoras
	$('#<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>').empty();
	if(tipoFactura!='')
	{
		// Sólo mostramos las suministradoras del tipo indicado
		for(var i=0;i<suministrosInfo.length;i++)
		{
			if(tipoFactura==suministrosInfo[i][1])
			{
				var opcion='<option value="'+suministrosInfo[i][0]+'"';
				if(empresaSuministradora==suministrosInfo[i][0])
					opcion+=' selected="selected"';
				opcion+='>'+suministrosInfo[i][0]+'</option>';
				$('#<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>').append(opcion);
			}
		}
	}
	*/?>

	selectTipoFacturaChanged($('#<?= VAR_FACTURA_TIPO_SUMINISTRO ?> option:selected').val());
}

<?php /*
function selectsInitVieja()
{
	// Primero generamos las listas de empresas y tipos de facturas.
	cargarListas();

	// Inicializamos el desplegable de empresas
	$('#<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>').empty();
	for(var i=0;i<listaEmpresasSuministradoras.length;i++)
	{
		var opcion='<option value="'+listaEmpresasSuministradoras[i]+'"';
		if(empresaSuministradora==listaEmpresasSuministradoras[i])
			opcion+=' selected="selected"';
		opcion+='>'+listaEmpresasSuministradoras[i]+'</option>';
		$('#<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>').append(opcion);
	}
	$(document).on('change', '#<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>', function(event) {
		var empresa = $(this).val();
		selectSuministradorasChanged(empresa);
	});

	// Inicializamos el desplegable de tipos de facturas
	$('#<?= VAR_FACTURA_TIPO_SUMINISTRO ?>').empty();
	if(empresaSuministradora!='')
	{
		if(empresaSuministradora=='OTRA')
		{
			for(var i=0;i<listaTiposFacturas.length;i++)
			{
				var opcion='<option value="'+listaTiposFacturas[i]+'"';
				if(tipoFactura==listaTiposFacturas[i])
					opcion+=' selected="selected"';
				opcion+='>'+listaTiposFacturas[i]+'</option>';
				$('#<?= VAR_FACTURA_TIPO_SUMINISTRO ?>').append(opcion);
			}
		}
		else
		{
			for(var i=0;i<suministrosInfo.length;i++)
			{
				if(empresaSuministradora==suministrosInfo[i][0])
				{
					var opcion='<option value="'+suministrosInfo[i][1]+'"';
					if(tipoFactura==suministrosInfo[i][1])
						opcion+=' selected="selected"';
					opcion+='>'+suministrosInfo[i][1]+'</option>';
					$('#<?= VAR_FACTURA_TIPO_SUMINISTRO ?>').append(opcion);
				}
			}
		}
	}

	selectSuministradorasChanged($('#<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?> option:selected').val());
}
*/?>

function VentanaConfirmEnvio(titulo, nf, oncontinuar)
{
	var mensaje='<div class="errmsg">';
	mensaje+='<h3>ATENCIÓN:<h3>';
	mensaje+='<p>La factura Nº <strong>'+nf+'</strong> ya existe. Si continúa se perderán los datos ya grabados.<br/><br/>Por favor, confirme la operación.</p>';
	mensaje+='</div>';
	$( "#dialogo_aviso" ).dialog( "destroy" );
	$( "#dialogo_aviso" ).remove();
	$('body').append('<div id="dialogo_aviso" style="text-align: left">'+mensaje+'</div>');
	
	//busy(true);
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
				//busy(false);
			}
    	}
    });
}

function EnviarForm()
{
	try
	{
		// Si es de sólo lectura, no hacemos nada.
		if(soloLectura)
    		return;

		var numfactura=$('#<?= VAR_FACTURA_NUMERO_FACTURA ?>').val().trim();
		
		$.ajax({
			type: "POST",
			url: ajaxURL, 
			dataType: 'JSON',
			data: {
				<?= ARG_DATA ?>: {
					'<?= ARG_ESTID ?>' : idEstablecimiento,
					'numfactura' : numfactura
				},
				<?= ARG_OP." : '".OP_CONFIRMAR_ENVIO_FACTURA."'" ?>
			},
			success: function(respuesta)
			{
				try
				{
					/*
					Como respuesta, se espera un objeto JSON como el siguiente:
					{
						error: true/false,
						mensaje: "ok"/"Mensaje de error específico",
						factura_existe: true/false
					}
					*/
					if(respuesta.error)
					{
						// La petición ha fallado. Informar al usuario.
						
						// Posibles mensajes:
						// ** La factura ya ha sido enviada y no es modificable. Para realizar cambios a una factura ya enviada, debe contactar con el Instituto Canario de Estadística. 
						// ** La petición ha fallado (error interno).
						alert(respuesta.mensaje);
					}
					else
					{
						if(respuesta.factura_existe==false)
						{
							$("#formdata").submit();
						}
						else
						{
							// La factura ya existe. Pedir confirmación al usuario antes de continuar con la grabación de la factura.
							VentanaConfirmEnvio("Guardar datos",numfactura,function(){
								//$("#<?= ARG_NUMERO_FACTURA ?>").val(numfactura);
								$("#<?= ARG_NUMERO_FACTURA ?>").val($('#<?= VAR_FACTURA_NUMERO_FACTURA ?>').val().trim());
								$("#formdata").submit();
							});
						}
					}
					
					//$("#msg_errores").html(data);
					//$( "#dialog-detail" ).dialog("open");					
				}
				catch(err)
				{
					//busy(false);
					alert(err.message);
				}
			},
			error: function(xhr) {
				var txt="Ocurrió un error al realizar la petición: (" + xhr.status + ") " + xhr.statusText;
				if(xhr.status==403)
					txt+="\n\nATENCIÓN: se ha perdido la sesión. Los datos que introduzca no serán guardados.\nPulse el enlace Salir para autentificarse nuevamente.";
				alert (txt);
				//busy(false);
			}
		}); 		    
	}
	catch(err)
	{
		alert(err.message);
	}
}


$(document).on("click", '#botonenviar', function(event) { 
	if(!validarForm())
		event.preventDefault();
	//$("#formdata").submit();
	EnviarForm();
});

function setupValidacion()
{
	jQuery.validator.setDefaults({
		  //debug: true,
		  //success: "valid",
		  ignoreTitle: true
		});

	$("#formdata").validate({
		rules: {
			<?php /*
			'<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>': {
				required: true
			},
			*/?>
			'<?= VAR_FACTURA_TIPO_SUMINISTRO ?>': {
				required: true
			},
			'<?= VAR_FACTURA_NUMERO_FACTURA ?>': {
				required: true
			}
		},
		errorPlacement: function(error, element) {
			error.insertAfter(element);
    	}
	});
}

$(document).ready( function() {
	// var elto=$("#<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>").find(":selected");
	//var suministradora=elto.val();

	$("input[type='button']").button();
	selectsInit();
	setupValidacion();
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
	<?php if (isset($NumeroFactura)) : ?>
		<h2 class="titulo_2">Factura Nº: <b><?= $NumeroFactura ?></b></h2>
	<?php endif; ?>
	<h3 class="titulo_2 titulo_seccion">Introducir/Modificar factura</h3>
	<div style="padding: 4px;">
    <form method="post" action="<?= $urlNext ?>" id="formdata">
    	<input type="hidden" id="<?= ARG_NUMERO_FACTURA ?>" name="<?= ARG_NUMERO_FACTURA ?>" value="">
    	<fieldset>
    	<label for="<?= VAR_FACTURA_TIPO_SUMINISTRO ?>">Tipo de suministro: </label>
    	<select id="<?= VAR_FACTURA_TIPO_SUMINISTRO ?>" name="<?= VAR_FACTURA_TIPO_SUMINISTRO ?>"></select>
    	<?php
    	/*
    	<label for="<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>">Suministradora: </label>
    	<select id="<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>" name="<?= VAR_FACTURA_EMPRESA_SUMINISTRADORA ?>"></select>
    	 */
    	?>
    	<?php if (isset($NumeroFactura)==false) : ?>
    	<label for="<?= VAR_FACTURA_NUMERO_FACTURA ?>"><b>Nº de factura:</b> </label>
    	<input type="text" name="<?= VAR_FACTURA_NUMERO_FACTURA ?>" id="<?= VAR_FACTURA_NUMERO_FACTURA ?>" value="<?= $NumeroFactura ?>" required="required">
    	<?php endif; ?>
    	</fieldset>
        <div class="botonera"><div style="float: right;"><input type="button" id="botonenviar" value="Empezar"></div></div>
        <div style="clear: both;"/>
    </form>
    </div>
</div>
