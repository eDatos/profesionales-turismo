//Inicialización de variables
var soloLectura=false;
var navPageURL="";
var printFormURL="";
var navSgteURL="";
var navPrevURL="";
var numero_filas=0;
var mes_encuesta;
var ano_encuesta;
var nombre_establecimiento;
var es_admin;
var sumaTotal=0;
var formModificado=false;
var externos=false;

function getDatos(esEnvio)
{
	var datos = new Array();
	for(var nd=0; nd<numero_filas; nd++)
	{
		datos[nd]={
				//'cc': $("#cc_" + nd).text(),
				'cc': $("#cc_" + nd).data("cc"),
				'ne': $("#ne_" + nd).val().trim()
		};
	}
	var parametro = {
			'var_json': {
				'datos' : datos
			},
			'externos': externos,
			'op' : (esEnvio ? "send":"save")
	};
	
	return parametro;
}

function validarDatos(parametro)
{
	var datos=parametro.var_json.datos;
	for(var nd=0; nd<numero_filas; nd++)
	{
		var numero=parseInt(datos[nd].ne);
		if(numero==NaN)
		{
			alert("El número de empleados asociados al empleador '"+$("#cc_" + nd).text()+"' no es correcto.\nDebe indicar un número entero mayor o igual a cero.");
			return false;
		}
		var snumero=""+numero;
		if(snumero!=datos[nd].ne)
		{
			alert("El número de empleados asociados al empleador '"+$("#cc_" + nd).text()+"' no es correcto.\nDebe indicar un número entero mayor o igual a cero.");
			return false;
		}
		if(numero<0)
		{
			alert("El número de empleados asociados al empleador '"+$("#cc_" + nd).text()+"' no es correcto.\nDebe indicar un número entero mayor o igual a cero.");
			return false;
		}
	}
	return true;
}

function lineaAviso(grupo)
{
	this.grupo=grupo;
	this.errores="";
	this.avisos="";
	this.otros="";
}

function categoria2grupo(categ)
{
	var grupo=0;
	switch(categ)
	{
	case 2:
		grupo = 1;
		break;
	case 3:
		grupo = 2;
		break;
	case 4:
	case 5:
		grupo = 3;
		break;
	}
	return grupo;
}

function VentanaErroresEnvio(titulo, errores, guard_ok)
{
	try
	{
		//busy(true);
		
		$( "#dialogo_error" ).dialog( "destroy" );
		$( "#dialogo_error" ).remove();
		$('body').append("<div id='dialogo_error' style='text-align: left'></div>");
		
		var avisos=new Array();
		for (var i = 0; i < errores.length; i++)
		{
			var err = errores[i];
			var grupo=categoria2grupo(err.categoria);
			if(avisos[grupo]==undefined)
				avisos[grupo]=new lineaAviso(grupo);
			
			if(err.nivel=="ERROR")
				avisos[grupo].errores+="<li> <strong class='linea_error'>[ERROR]</strong> " + err.mensaje +  "</li>";
			else if(err.nivel=="AVISO")
				avisos[grupo].avisos+="<li> <strong class='linea_aviso'>[AVISO]</strong> " + err.mensaje +  "</li>";
			else
				avisos[grupo].otros+='<li>' + err.mensaje +  '</li>';
		}
		
		$( "#dialogo_error" ).empty();
		var c = $( "#dialogo_error" ).append("<div></div>");
		
		if(avisos[0]!=undefined)
		{
			var contenidoErrorGlobal = $('<ul class="lista_ventana"></ul>');
			contenidoErrorGlobal.append(avisos[0].errores+avisos[0].avisos+avisos[0].otros);
			c.append('<strong>Errores Globales</strong>').append(contenidoErrorGlobal);
		}
		if(avisos[1]!=undefined)
		{
			var contenidoErrorMovs = $('<ul class="lista_ventana"></ul>');
			contenidoErrorMovs.append(avisos[1].errores+avisos[1].avisos+avisos[1].otros);
			c.append('<strong>Errores Movimientos</strong>').append(contenidoErrorMovs);
		}
		if(avisos[2]!=undefined)
		{
			var contenidoErrorHabs = $('<ul class="lista_ventana"></ul>');
			contenidoErrorHabs.append(avisos[2].errores+avisos[2].avisos+avisos[2].otros);
			if (esHotel)
				c.append('<strong>Errores Habitaciones</strong>').append(contenidoErrorHabs);
			else
				c.append('<strong>Errores Apartamentos</strong>').append(contenidoErrorHabs);
		}
		if(avisos[3]!=undefined)
		{
			var contenidoErrorPrec = $('<ul class="lista_ventana"></ul>');
			contenidoErrorPrec.append(avisos[3].errores+avisos[3].avisos+avisos[3].otros);
			c.append('<strong>Errores Personal y precios</strong>').append(contenidoErrorPrec);
		}
		
		if (guard_ok)
			$( "#dialogo_error" ).prepend("<p>Se ha guardado el cuestionario. Deberá corregir los errores que aparecen a continuación para poder enviar el cuestionario.</p>");
		else
			$( "#dialogo_error" ).prepend("<p>No se ha podido guardar el cuestionario. Corrija los errores que aparecen a continuación.</p>");

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
					var titulo='Encuesta sobre el Empleo: '+nombreMesEncuesta+' de '+ano_encuesta;
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

function VentanaConfirmEnvio(titulo, avisos, oncontinuar)
{
	$( "#dialogo_aviso" ).dialog( "destroy" );
	$( "#dialogo_aviso" ).remove();
	$('body').append("<div id='dialogo_aviso' style='text-align: left'></div>");
	
	
	
	$( "#dialogo_aviso" ).empty();	
	var c = $( "#dialogo_aviso" ).append('<div></div>');
	
	if(avisos)
	{
		var contenidoAvisos = $('<ul class="lista_ventana"></ul>');
		for (var i = 0; i < avisos.length; i++)
		{
			var err = avisos[i];
			contenidoAvisos.append('<li>' + err.mensaje +  '</li>');
		}
		c.append('<strong>Avisos</strong>').append(contenidoAvisos);
	}
	else 
	{
		c.append("<p>El cuestionario ha sido guardado y validado. Pulse el botón 'Enviar encuesta' para confirmar su envío.</p>");
	}
	
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
            "Enviar encuesta": function() {
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
					var titulo='Encuesta sobre el Empleo: '+nombreMesEncuesta+' de '+ano_encuesta;
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

function VentanaErrores_2(titulo, errores, mostrarcat, guard_ok)
{
	try
	{
		//busy(true);
		
		$( "#dialogo_error" ).dialog( "destroy" );
		$( "#dialogo_error" ).remove();
		$('body').append("<div id='dialogo_error' style='text-align: left'></div>");
		
		if (mostrarcat)
		{
			var contenidoErrorGlobal = $('<ul class="lista_ventana"></ul>');
			var contenidoErrorMovs = $('<ul class="lista_ventana"></ul>');
			var contenidoErrorHabs = $('<ul class="lista_ventana"></ul>');
			var contenidoErrorPrec = $('<ul class="lista_ventana"></ul>');
				
			for (var i = 0; i < errores.length; i++)
			{
				var err = errores[i];
				var cnt = contenidoErrorGlobal;
				switch(err.categoria)
				{
				case 2:
					cnt = contenidoErrorMovs;
					break;
				case 3:
					cnt = contenidoErrorHabs;
					break;
				case 4:
				case 5:
					cnt = contenidoErrorPrec;
					break;
				}
				cnt.append('<li>' + err.mensaje +  '</li>');
			}
			
			$( "#dialogo_error" ).empty();
			var c = $( "#dialogo_error" ).append("<div></div>");
			if (contenidoErrorGlobal.children().length)
				c.append('<strong>Errores Globales</strong>').append(contenidoErrorGlobal);
			if (contenidoErrorMovs.children().length)
				c.append('<strong>Errores Movimientos</strong>').append(contenidoErrorMovs);
			if (contenidoErrorHabs.children().length)
			{
				if (esHotel)
					c.append('<strong>Errores Habitaciones</strong>').append(contenidoErrorHabs);
				else
					c.append('<strong>Errores Apartamentos</strong>').append(contenidoErrorHabs);
			}
			if (contenidoErrorPrec.children().length)
				c.append('<strong>Errores Personal y precios</strong>').append(contenidoErrorPrec);
			
			if (guard_ok)
				$( "#dialogo_error" ).prepend("<p>Se ha guardado el cuestionario. Deberá corregir los errores que aparecen a continuación para poder enviar el cuestionario.</p>");
			else
				$( "#dialogo_error" ).prepend("<p>No se ha podido guardar el cuestionario. Corrija los errores que aparecen a continuación.</p>");
		}
		else 
		{
			var contenidoErrorGlobal = $('<ul class="lista_ventana"></ul>');
			for (var i = 0; i < errores.length; i++)
			{
				var err = errores[i];
				contenidoErrorGlobal.append('<li>' + err.mensaje +  '</li>');
			}
			$( "#dialogo_error" ).empty();
			var c = $( "#dialogo_error" ).append('<div></div>');
			c.append(contenidoErrorGlobal);		
		}

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
					var titulo='Encuesta sobre el Empleo: '+nombreMesEncuesta+' de '+ano_encuesta;
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

function EnviarForm()
{
	try
	{
		// Si es de sólo lectura, no hacemos nada.
		if(soloLectura)
    		return;

		/// ...
		var dd=getDatos(true);

		if(validarDatos(dd)==false)
		{
    		return;
		}
		
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
						var errs = errores[0].errores;
						var guar_ok = errores[1];
						if(errs)
						{
							//Si existe algún error mostrará una ventana con los errores en lugar de la ventana de confirmación de envío
							for(var i=0; i< errs.length;i++)
							{
								if(errs[i].nivel=="ERROR") 
								{
									if (guar_ok)
										formModificado=false;
									VentanaErroresEnvio("Guardar y enviar encuesta", errs, guar_ok);
									return;
								}
							}
						}
					}
					
					formModificado=false;
					VentanaConfirmEnvio("Guardar y enviar encuesta" , errs, function(){
						$("#ef").submit();
					});
					
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
		
		var dd=getDatos(false);

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
					funcionGuardarExito();					
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

function ImprimirForm()
{
	if(formModificado)
	{
		//busy(true);
		$("#msg_impresion").html("Existen modificaciones sin guardar. Para guardar los cambios pulse 'Guardar',<br/>de lo contrario la encuesta se imprimirá sin los últimos cambios.");
		$("#dialog-print").on( "dialogclose", function( event, ui ) {/*busy(false);*/} );
		$("#dialog-print").dialog("open");
	}
	else
	{
		window.open(printFormURL);
	}    	
}

function actualizarTotales()
{
	var err=false;
	var total=0;
	$("input[id^='ne_']").each(function() {
		var n=$(this).val();
		if(n)
		{
			if(!isNaN(n))
			{
				total+=parseInt(n);
			}
			else
			{
				err=true;
				return false;
			}
		}
		return true;
	});
	$('#sumaTotal').text((err)?'N/A':total);
}

$(document).ready( function() 
{
	$("input[name='enviarBtn']").button().click(EnviarForm);
	
	$("input[name='guardarBtn']").button().click(function() {
		if(soloLectura)
		{
			window.location.href=navSgteURL;
			return;
		}
		GuardarForm(function() {
			//busy(true);
			
			window.location.href=navSgteURL;
			
			/*
			$("#dialog-detail").dialog('option', 'title', 'Datos almacenados');
			$("#dialog-detail").on( "dialogclose", function( event, ui ) {
					//busy(false);
					window.location.href=navSgteURL;
				});
			$("#msg_errores").html("<div class='cuadro fondo_verde'>"+
					"<p class='okicon' style='margin-top:15px;'>Su cuestionario de empleo ha sido guardado.</p>" +
				"</div>");
			
			$( "#dialog-detail" ).dialog("open");
			*/
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
	
	$("input[name='imprimirBtn']").button().click(ImprimirForm);
	
	$( "#dialog-print" ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		height: "auto",
		width: "auto",
		position : { my: "center", at: "center", of: window },
		buttons: {
			"Guardar": function() {
				GuardarForm(function() {
					window.open(printFormURL);
					/*busy(false);*/
				});
				$( this ).dialog( "close" );
			},
			"Cancelar": function() {
				$( this ).dialog( "close" );
				window.open(printFormURL);
			}
		}
	});
	
	$("input").change(function()
	{
		formModificado=true;
	});


	//$('#indicadorbusy').hide();
	
	if(soloLectura==false)
	{
		$("input[id^='ne_']").change(actualizarTotales);
	}
	
	if(soloLectura)
	{
		$("#sumaTotal").text(sumaTotal);
	}
	else
	{
		$("#sumaTotalAnterior").text(sumaTotal);
		$("input[id^='ne_']").change(actualizarTotales);
		actualizarTotales();
	}
});	
