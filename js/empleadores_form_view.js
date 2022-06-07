// Constantes
var arg_id_empleador="";
var arg_activa="";
var etiqueta_tipo_establecimientos="";
var op_btnSiguiente="";

var mes_encuesta="";
var ano_encuesta="";
var nombre_establecimiento="";
var navPageURL="";
var navSgteURL="";
var navPrevURL="";
var numero_filas=0;
var soloLectura=false;
var detalles=false;
var nEmpleadoresActivos=0;
var externos=false;

function toggleDetalles()
{
	document.getElementById('detalleErrores').style.display=(detalles)?'none':'block';
	detalles=!detalles;
}

function limpiarCCC(ccc)
{
    var digitos="";
    var i;
    for(i=0;i<ccc.length;i++)
    {
        var letra=ccc.substr(i,1);
        if((letra[0]>='0')&&(letra[0]<='9'))
        	digitos+=letra;
    }
    return digitos;
}

function getDatos(operacion)
{
	var n=0;
	var datos = new Array();
	for(var nd=0; nd<numero_filas; nd++)
	{
		var id_empleador=$("input[name='"+arg_id_empleador+'_'+nd+"']").val();
		var descripcion=$("input[name='desc_"+nd+"']").val();
		var estado=$("input[name='"+arg_activa+'_'+nd+"']").is(':checked');
		if(($("input[name='desc_"+nd+"']").data("prev")!=descripcion) || ($("input[name='"+arg_activa+'_'+nd+"']").data("prev")!=estado))
		{
			datos[n++]={
					'id_empleador': id_empleador,
					'descripcion': encodeURIComponent(descripcion),
					'estado': estado ? 'A':'I'
			};
		}
	}
	var parametro = {
			'var_json': {
				'datos' : datos
			},
			'externos': externos,
			'op' : operacion
	};
	
	return parametro;
}


function validarDatos(parametro)
{
	nEmpleadoresActivos=0;
	var datos=parametro.var_json.datos;
	for(var nd=0; nd<numero_filas; nd++)
	{
		if($("input[name='"+arg_activa+'_'+nd+"']").is(':checked'))
			nEmpleadoresActivos++;
	}
	if((externos==false)&&(nEmpleadoresActivos==0))
	{
		alert("Debe haber al menos un registro activo para poder continuar con la encuesta.");
		return false;
	}
	return true;
}

function VentanaErrores(titulo,errores,guard_ok)
{
	try
	{
		//busy(true);
		
		$( "#dialogo_error" ).dialog( "destroy" );
		$( "#dialogo_error" ).remove();
		$('body').append("<div id='dialogo_error' style='text-align: left'></div>");
		
		var contenidoErrorGlobal = $('<ul class="lista_ventana"></ul>');
		for (var i = 0; i < errores.length; i++)
		{
			contenidoErrorGlobal.append('<li>' + errores[i] +  '</li>');
		}
		$( "#dialogo_error" ).empty();
		var c = $( "#dialogo_error" ).append('<div></div>');
		c.append(contenidoErrorGlobal);		

		var d=new Date();
		var timestamp=('0'+d.getDate()).slice(-2) + '/' + ('0'+d.getMonth()).slice(-2) + '/' + d.getFullYear() + ' ' + ('0'+d.getHours()).slice(-2) + ':' + ('0'+d.getMinutes()).slice(-2) + ':' + ('0'+d.getSeconds()).slice(-2);
		var meses=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
		var nombreMesEncuesta=meses[mes_encuesta-1];
		$( "#dialogo_error" ).css('max-height', '400px');
		$( "#dialogo_error" ).dialog({
			autoOpen: true,
			resizable: false,
			modal: true,
			width: 800,
			title: titulo,
			position : { my: "center", at: "center", of: window },
			buttons: {
				"Imprimir": function() {
					var nuevaventana=window.open();
					var base=window.location.href;
					base=base.slice(0,base.lastIndexOf('/'));
					var titulo='Encuesta sobre el Empleo en Establecimientos '+etiqueta_tipo_establecimientos+': '+nombreMesEncuesta+' de '+ano_encuesta;
					var cabecera='<a href="http://www.gobiernodecanarias.org/istac/" target="_blank" title="Página principal del Instituto Canario de Estadística (ISTAC) - Opciones de accesibilidad (tecla de acceso: i)" accesskey="i"><img src="images/logo_istac.jpg" style="width:300px; margin-right:10px; margin-top:5px;"></a><h1>'+titulo+'</h1><h1>Informe de errores</h1><h3>Establecimiento: '+nombre_establecimiento+'</h3><h3>Fecha y hora de envío: '+timestamp+'</h3><br/>';
					var html='<html><head><base href="'+base+'/" target="_blank"></head><body>'+cabecera+$(this).html()+'</body></html>';
					$(nuevaventana.document.body).html(html);
					nuevaventana.print();
				},
				"Aceptar": function() {
					$( this ).dialog( "close" ).remove();						
				}
			},
			close: function(event, ui) {
				try
				{
					$( "#dialogo_error" ).dialog( "destroy" );
					$( "#dialogo_error" ).remove();					
				}
				finally
				{
					//busy(false);
				}
			}
		});	
		
	}
	catch(err)
	{
		//busy(false);
		alert(err.message);
	}
}

function GuardarForm(funcionGuardarExito)
{
	try
	{
		//busy(true);
		
		if(soloLectura)
			return;
		/*{
			funcionGuardarExito();
			return;			
		}*/
		
		var dd=getDatos(op_btnSiguiente);

		if(dd.var_json.datos.length==0)
		{
			formModificado=false;
			funcionGuardarExito(false);
			return;					
		}

		if(validarDatos(dd)==false)
		{
    		return;
		}
		
		// Petición guardar
		$.ajax({
			type: "POST",
			url: navPageURL, 
			data: dd, 
			success: function(data) 
			{
				try
				{
					var errores = JSON.parse(data);
					
					if (errores)
					{
						//var errs = errores[0].errores;
						var errs = errores[0];
						var guar_ok = errores[1];
						
						if(errs)
						{
							if (guar_ok)
								formModificado=false;
							//VentanaErrores("Guardar", errs, true, guar_ok);
							VentanaErrores("Guardar", errs, guar_ok);
							return;
						}
					}
					formModificado=false;
					funcionGuardarExito(true);					
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
		//busy(false);
		alert(err.message);
	}
	finally
	{
	}
}

$(document).ready( function() {
	jQuery.validator.addMethod(
            "nofutura",
            function (value, element) {
                var hoy=new Date();
                return parseDate(value) <= hoy;
            },
            "La fecha inicial no puede ser posterior a la actual"
        );

	jQuery.validator.addMethod(
            "CCC_CodProv",
            function (value, element) {
                var digitos=limpiarCCC(value);
                var codProv=parseInt(digitos.substr(0,2));
                return (((codProv>=1)&&(codProv<=53))||(codProv==66));
            },
            "El código de provincia de la cuenta de cotización no es válida."
        );

	jQuery.validator.addMethod(
            "CCC_DC",
            function (value, element) {
            	var digitos=limpiarCCC(value);
                if(digitos.substr(2,1)=='0')
                    digitos=digitos.substr(0,2)+digitos.substr(3, digitos.length-3);
                var dc = parseInt(digitos.substr(0,digitos.length-2)) % 97;
                var digitosControl="";
                if (dc <= 9)
                	digitosControl="0"+dc;
                else
                	digitosControl=""+dc;
            	return digitos.endsWith(digitosControl);
            },
            "La cuenta de cotización no es válida. Los dígitos de control no coinciden."
        );

	jQuery.validator.addMethod(
            "CCC_longitud",
            function (value, element) {
            	var digitos=limpiarCCC(value);
                return ((digitos.length==11)||(digitos.length==12));
            },
            "La cuenta de cotización debe contener 11(CCC) ó 12(NAF) dígitos. Se admiten separadores."
        );

	if(externos==false)
	{
		$("#df").validate({
			rules: {
				fechaAlta: {
					required: true,
					nofutura: true
				},
				cuentaCotizacion: {
					required: true,
					CCC_CodProv: true,
					CCC_longitud: true,
					CCC_DC: true
				},
				nombreEmpresa: "required"
			}, 
			wrapper: "p",
	    	errorPlacement: function(error, element) {
	        	error.appendTo(element.parent());
	        	error.addClass("validmsg");
	    	}
		});
	}
	
	$('#ampliar').click(function(event){
		toggleDetalles();
	});
	
	$('#ocultar').click(function(event){
		$(this).parent().toggle();
	});

	$("input[name='guardarBtn']").button().click(function() {
		GuardarForm(function(hayCambios) {
			if(hayCambios)
			{
				//busy(true);
				
				window.location.href=navSgteURL;
				
				/*
				$("#dialog-detail").dialog('option', 'title', 'Datos almacenados');
				$("#dialog-detail").on( "dialogclose", function( event, ui ) {
						//busy(false);
						window.location.href=navSgteURL;
					});
				$("#msg_errores").html("<div class='cuadro fondo_verde'>"+
						"<p class='okicon' style='margin-top:15px;'>Sus cambios han sido guardados.</p>" +
					"</div>");
				
				$( "#dialog-detail" ).dialog("open");
				*/
			}
			else
				window.location.href=navSgteURL;
		});
	});
	
	$("input[name='anteriorBtn']").button().click(function() {
		window.location.href=navPrevURL;
	});

	$( "#dialog-detail" ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		height: "auto",
		width: "auto",
		position : { my: "center", at: "center", of: window },
		buttons: {
			"Aceptar": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
});
