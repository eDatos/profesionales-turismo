//Inicialización de variables
var utColumns = new Array();
var totales = new UT();
var subtotales = new UT();
var diasMostrarFormulario;
var numDiasMes;
var numPlazas;
var numPlazasSupletorias;
var numHabitaciones;
var esHotel;
var nombre_establecimiento;
var mes_encuesta;
var ano_encuesta;
var mes_encuesta_finalizado;

var numUTSeleccionados;
var maxUTMostrarPagina;
var numPaginas;
var pagActual;

var modoIntroduccion;
var modoCumplimentado;
var modoIntroduccionADR;

var habitaciones;
var personalPrecios;

var checkChangesEnabled = true;
var formModificado = false;

var soloLectura = true;
var navPageURL="";
var printFormURL="";

var es_admin=false;
var tab_inicial=0;

var intervaloGrabacionesIntermedias=30;
var timerGrabacionesIntermedias=0;
var avisobackup_fadeoutTimeMs=2500;

var fOcupado=true;
var fShowBackup=true;
var fShowOcupado=true;

var excesoPlazasPorciento=0;
var excesoPlazas=0;
var excesoHabitacionesPorciento=0;
var excesoHabitaciones=0;
var motivosExcesoInfo=[];

var codMotivoExcesoPlazas;
var detalleMotivoExcesoPlazas;
var codMotivoExcesoHabitaciones;
var detalleMotivoExcesoHabitaciones;

var tipo_cliente_perct=[];

function getMotivosExcesosOps()
{
	var opciones="";
	for(var i=0;i<motivosExcesoInfo.length;i++)
		opciones+='<option value="'+motivosExcesoInfo[i].id+'"'+(motivosExcesoInfo[i].oblig=='S' ? ' data-detalle="true"':'')+'>'+motivosExcesoInfo[i].id+' - '+motivosExcesoInfo[i].desc+'</option>';
	return opciones;
}

function ayudaMotivosExcesos(idmot)
{
	for(var i=0;i<motivosExcesoInfo.length;i++)
	{
		if(motivosExcesoInfo[i].id==idmot)
			return motivosExcesoInfo[i].ayuda;
	}
	return '';
}

//Definición de clases
function UT(pos, id, title, pcm)
{
	this.Pos = pos;
	this.Id = id;
	this.Title = title;
	this.PresComMes = pcm;
	this.EPSLines;
	this.Err;
}

function EPS_Line(d, e, s, p)
{
	this.Dia = d;
	this.E = e;
	this.S = s;
	this.P = p;
} 

function Linea_Habitacion(dia, sup, dob, ind, otr)
{
	this.Dia = dia;
	this.Sup = sup;
	this.Dob = dob;
	this.Ind = ind;
	this.Otr = otr;
	this.Err;
}

function ADR_Line(tipo,valor)
{
	this.Tipo = tipo;
	this.Valor = valor;
}

//Tipos de clientes para la pestaña de personal y precios
var tipo_cliente_adr = {
		'TOUROPERADOR_TRADICIONAL': 'to_tradic',
		'EMPRESAS': 'emp',
		'AGENCIA_DE_VIAJE_TRADICIONAL': 'ag_tradic',
		'PARTICULARES': 'partic',
		'GRUPOS': 'grupos',
		'INTERNET': 'ht',
		'AGENCIA_DE_VIAJE_ONLINE': 'ag_online',
		'TOUROPERADOR_ONLINE': 'to_online',
		'OTROS': 'otros'
};

var tipo_cliente_label = {
		'TOUROPERADOR_TRADICIONAL': 'Turoperador tradicional',
		'EMPRESAS': 'Empresas',
		'AGENCIA_DE_VIAJE_TRADICIONAL': 'Agencia de viaje tradicional',
		'PARTICULARES': 'Particulares',
		'GRUPOS': 'Grupos',
		'INTERNET': 'Contratación directa del hotel online',
		'AGENCIA_DE_VIAJE_ONLINE': 'Agencias de viaje on-line',
		'TOUROPERADOR_ONLINE': 'Turoperador on-line',
		'OTROS': 'Otros'
}; 


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
		busy(true);
		
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
					var titulo='Encuesta de Alojamiento Turístico: '+nombreMesEncuesta+' de '+ano_encuesta;
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
					busy(false);
				}
			}
		});	
	}
	catch(err)
	{
		busy(false);
	}
}

function VentanaErrores(titulo, errores, mostrarcat, guard_ok)
{
	try
	{
		busy(true);
		
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
					var titulo='Encuesta de Alojamiento Turístico: '+nombreMesEncuesta+' de '+ano_encuesta;
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
					busy(false);
				}
			}
		});	
	}
	catch(err)
	{
		busy(false);
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
	
	busy(true);
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
				busy(false);
			}
    	}
    });
}

//Mostrar ventana flotante con errores
function MostrarErrores_EPS(elementoReferencia)
{
	var errores=[];
	if(elementoReferencia.indexOf("dia")<0)
	{
		errores.push({categoria:3,mensaje:"La unidad territorial tiene alguna línea con errores"});
	}
	else
	{
		var dia=parseInt(elementoReferencia.substring(elementoReferencia.indexOf("dia")+3));
		//Se comprueba si es error en la columna de totales o en la columna de unidad territorial 
		if(elementoReferencia.indexOf("tot")<0)
		{
			//Se busca en la columna de la unidad territorial la línea que contiene el día para el que se muestran los errores
			var ut=parseInt(elementoReferencia.substring(elementoReferencia.indexOf("ut")+2));
			for(var fl=0;fl<utColumns[ut].Err.length;fl++)
			{
				if(utColumns[ut].Err[fl].Dia==dia)
				{
					errores.push({categoria:2,mensaje:utColumns[ut].Err[fl].Mensaje});
				}
			}
		}
		else
		{
			//Se busca en la columna de totales la línea que contiene el día para el que se muestran los errores
			for(var fl=0;fl<totales.Err.length;fl++)
			{
				if(totales.Err[fl].Dia==dia)
				{
					errores.push({categoria:2,mensaje:totales.Err[fl].Mensaje});
				}
			}		
		}
	}
	VentanaErrores("Error en datos",errores);
}

function MostrarErrores_Hab(elementoReferencia)
{
	var errores=[];
	var dia=parseInt(elementoReferencia.substring(elementoReferencia.indexOf("dia")+3));
	//Se busca la línea que contiene el día para el que se muestran los errores
	for(var fl=0;fl<habitaciones.length;fl++)
	{
		if(habitaciones[fl].Dia==dia)
		{
			for(var i=0;i<habitaciones[fl].Err.length;i++)
			{
				errores.push({categoria:3,mensaje:habitaciones[fl].Err[i].msg});
			}
			break;
		}
	}
	
	VentanaErrores("Error en datos",errores);
}

function MostrarErrores_PP(elementoReferencia)
{
	var errores=[];
	var tipo;
	switch(elementoReferencia.substring(9,12))
	{
		case "adr":
			tipo=elementoReferencia.substring(elementoReferencia.indexOf("adr_")+4);
			break;
		case "per":
			tipo=elementoReferencia.substring(elementoReferencia.indexOf("pers_")+5);
			break;			
		case "pre":
			tipo=elementoReferencia.substring(elementoReferencia.indexOf("prec_")+5);
			break;
	}
			
	//Se busca la línea que contiene el día para el que se muestran los errores
	for(var fl=0;fl<personalPrecios.Err.length;fl++)
	{
		switch(personalPrecios.Err[fl].Tipo)
		{
			case 0: //Personal
			case 1: //Precios				
				if(personalPrecios.Err[fl].Key==tipo)
				{
					errores.push({categoria:5,mensaje:personalPrecios.Err[fl].Mensaje});
				}
				break;
			case 2: //ADR
				if(tipo_cliente_adr[personalPrecios.Err[fl].Key]==tipo)
				{
					errores.push({categoria:5,mensaje:personalPrecios.Err[fl].Mensaje});
				}
				break;
		}
	}
	
	VentanaErrores("Error en datos",errores);
}

//Se limpian los elementos de la vista para la pestaña de Entradas, salidas y pernoctaciones
function CleanViewEPS()
{
	//Recorrer campos para limpiar valores
	for(var cl=0; cl<maxUTMostrarPagina; cl++)
	{
		$("#ut" + cl + "_tit").html("");
		$("#pn_" + cl).val("");
		for(var fl=1; fl<=diasMostrarFormulario; fl++)
		{
			$("#ut" + cl + "_col0_dia" + fl).val("");
			$("#ut" + cl + "_col1_dia" + fl).val("");
			$("#ut" + cl + "_col2_dia" + fl).html("-");	
		}
		$("#total" + cl + "_fila_col0").html("-");
		$("#total" + cl + "_fila_col1").html("-");
		$("#total" + cl + "_fila_col2").html("-");
		$("#est_med_" + cl).html("-");
	}

	//Recorrer los totales para limpiar valores
	for(var fl=1; fl<=diasMostrarFormulario; fl++)
	{
		$("#total_col0_dia" + fl).html("-");
		$("#total_col1_dia" + fl).html("-");
		$("#total_col2_dia" + fl).html("-");
	}            
	
	$("img[id^='imgerror_ut']").hide();
	$("img[id^='imgerror_ut']").closest("tr").removeClass("error_fila");
	
	$("img[id^='imgerror_uttot']").hide();
}

//Cálculo automático de pernoctaciones. Dependerá del modo de introducción (ES/EP)
function CalcularPernoctaciones(col_ut,dia,tipo_celda)
{
	if (modoIntroduccion=='ES')
	{
		// tipo_celda = 0 = entrada, 1 = salida.
		pant = (col_ut.PresComMes != null)? col_ut.PresComMes : 0;
		for(var fl=0; fl<diasMostrarFormulario; fl++)
		{
			var fila = col_ut.EPSLines[fl];		
			fila.P = (fila.E != null? fila.E:0)- (fila.S!=null?fila.S:0) + pant;
			pant = fila.P;
		}
	}
	else
	{
		// tipo_celda = 0 = entrada, 1 = pernoctacion.
	
		var pernoc_anterior=0; 
		var entrada = 0; 
		var salida = 0; 
		var pernocta = 0; 
		var pernoc_anterior_nula=false;
		
		dia--; //Se resta el valor de dia para recorrer array con base 0
        if (dia != -1)  // Si dia es -1 es porque la celda que se modifica es presentes a comienzo de mes
        {
            //Se guarda el valor de pernoctaciones anteriores para futuros cálculos
            pernoc_anterior = (dia==0)?col_ut.PresComMes:col_ut.EPSLines[dia-1].P;
            if(pernoc_anterior==null) 
            	pernoc_anterior=0;
          
            if (col_ut.EPSLines[dia].S == null)
            	col_ut.EPSLines[dia].S = 0;
            
        	//Se guarda el valor de entrada y salida para futuros cálculos
            salida = col_ut.EPSLines[dia].S!=null?col_ut.EPSLines[dia].S:0;
            
            entrada = col_ut.EPSLines[dia].E!=null?col_ut.EPSLines[dia].E:0;
            
        	//Se calculan las pernoc. de la línea si se está introduciendo una entrada y la salida es 0
            if (tipo_celda==0 && salida==0)
            {
            	col_ut.EPSLines[dia].P=pernoc_anterior+entrada;
            }
             
        	//Se guarda el valor de pernoctación para futuros cálculos
            pernocta = col_ut.EPSLines[dia].P!=null?col_ut.EPSLines[dia].P:0;
           
            if (tipo_celda==1 || (tipo_celda==0 && salida!=0 ))
            {
            	col_ut.EPSLines[dia].S = entrada+pernoc_anterior-pernocta; 
            }
            
            salida = col_ut.EPSLines[dia].S!=null?col_ut.EPSLines[dia].S:0;
        }
        else
        {
            salida = 0;
            pernocta = (col_ut.PresComMes != null)? col_ut.PresComMes : 0;
            pernoc_anterior_nula = true;
        }
        
        var diaSig = dia + 1;
        var salida_siguiente=0;
        
        if (diaSig<diasMostrarFormulario)
        {
        	salida_siguiente = col_ut.EPSLines[diaSig].S!=null?col_ut.EPSLines[diaSig].S:0;
        	pernoc_anterior = (diaSig==0)?((col_ut.PresComMes != null)? col_ut.PresComMes : 0):col_ut.EPSLines[diaSig-1].P;
        }
                     
        if (salida==0 || tipo_celda==1)
        {
            while (diaSig<diasMostrarFormulario && salida_siguiente==0)
            {
                entrada = col_ut.EPSLines[diaSig].E!=null?col_ut.EPSLines[diaSig].E:0;
                salida = col_ut.EPSLines[diaSig].S!=null?col_ut.EPSLines[diaSig].S:0;
                pernoc_anterior = (diaSig==0)?col_ut.PresComMes:col_ut.EPSLines[diaSig-1].P;
                
                pernoc_anterior_nula = false;
                
                if(pernoc_anterior==null) 
                {
                	pernoc_anterior=0;
                	pernoc_anterior_nula = true;
                }

                var calculo_pernocta = pernoc_anterior+entrada;
                if (calculo_pernocta==0 && pernoc_anterior_nula)
                {
                	col_ut.EPSLines[diaSig].P=null;
                }
                else
                {
                	col_ut.EPSLines[diaSig].P=calculo_pernocta;
                }
		        
                pernoc_anterior = col_ut.EPSLines[diaSig].P;
                
                diaSig++;        
                if (diaSig<diasMostrarFormulario)
                {
                	salida_siguiente = col_ut.EPSLines[diaSig].S!=null?col_ut.EPSLines[diaSig].S:0;
                }
            }
        }
            
        if (salida_siguiente !=0)
        {
            entrada = col_ut.EPSLines[diaSig].E!=null?col_ut.EPSLines[diaSig].E:0;
            pernocta = col_ut.EPSLines[diaSig].P!=null?col_ut.EPSLines[diaSig].P:0;
            col_ut.EPSLines[diaSig].S=pernoc_anterior+entrada-pernocta;
        }
               
	}
}

//Se actualizan los elementos del modelo con los elementos de la vista para la pestaña Entradas, salidas y pernoctaciones
function UpdateModelFromViewEPS()
{
	//Recorrer campos para actualizar valores
	for(var cl=0; cl<maxUTMostrarPagina; cl++)
	{
		if (utColumns[cl])
		{
			var pcm = $("#pn_" + cl).val();
			pcm = (!pcm)? null : parseInt(pcm);
			utColumns[cl].PresComMes = pcm;
			utColumns[cl].EPSLines = new Array();
			for(var fl=1; fl<=diasMostrarFormulario; fl++)
			{
				e = $("#ut" + cl + "_col0_dia" + fl).val();
				if (modoIntroduccion=='ES')
				{
					s = $("#ut" + cl + "_col1_dia" + fl).val();
					p = $("#ut" + cl + "_col2_dia" + fl).html();
					e = (!e)? null : parseInt(e);
					s = (!s)? null : parseInt(s);
					p = (!p || p=='-')? null : parseInt(p);
					utColumns[cl].EPSLines.push(new EPS_Line(fl, e,s,p));
				}
				else 
				{
					p = $("#ut" + cl + "_col1_dia" + fl).val();	
					s = $("#ut" + cl + "_col2_dia" + fl).html();
					e = (!e)? null : parseInt(e);
					p = (!p)? null : parseInt(p);
					s = (!s || s == '-')? null : parseInt(s);
					utColumns[cl].EPSLines.push(new EPS_Line(fl, e,s,p));
				}
			}
		}
	}
}

function CalcularTotalesEPS()
{	
	//Se calculan los totales
	if(totales.EPSLines!=undefined)
	{
		totales.PresComMes=subtotales.PresComMes;
		if(subtotales.EPSLines!=null)
		{
			for(var i=0;i<subtotales.EPSLines.length;i++)
			{
				totales.EPSLines[i]=new EPS_Line(subtotales.EPSLines[i].Dia,subtotales.EPSLines[i].E,subtotales.EPSLines[i].S,subtotales.EPSLines[i].P);
			}
		}        	
		for(var cl=0; cl<utColumns.length; cl++)
		{
			totales.PresComMes += (utColumns[cl].PresComMes != null)? parseInt(utColumns[cl].PresComMes) : 0;
			if (utColumns[cl].EPSLines != null)
			{
				for(var fl=0; fl<utColumns[cl].EPSLines.length; fl++)
				{
					var fila = utColumns[cl].EPSLines[fl];
					totales.EPSLines[fila.Dia-1].E+= (fila.E != null)? parseInt(fila.E) : 0;
					totales.EPSLines[fila.Dia-1].S+= (fila.S != null)? parseInt(fila.S) : 0;
					totales.EPSLines[fila.Dia-1].P+= (fila.P != null)? parseInt(fila.P) : 0; 
				}
			}
		}
	}
}

function CalcularSubtotalesEPS() 
{
	//Se inicializa la lista de subtotales con los totales
	subtotales.PresComMes = parseInt(totales.PresComMes);
	
	//Se crean los subtotales para cada una de las líneas
	subtotales.EPSLines = new Array();
	for(var i=0;i<diasMostrarFormulario;i++)
	{
		subtotales.EPSLines[i]=new EPS_Line(i+1,0,0,0);
	}

	if(totales.EPSLines!=undefined)
	{
		for(i=0;i<totales.EPSLines.length;i++)
		{
			subtotales.EPSLines[totales.EPSLines[i].Dia-1].E=parseInt(totales.EPSLines[i].E);
			subtotales.EPSLines[totales.EPSLines[i].Dia-1].S=parseInt(totales.EPSLines[i].S);
			subtotales.EPSLines[totales.EPSLines[i].Dia-1].P=parseInt(totales.EPSLines[i].P);
		}
	}

	//Para cada una de las unidades territoriales se resta el valor para cada día
	for(var cl=0; cl<utColumns.length; cl++)
	{
		subtotales.PresComMes -= (utColumns[cl].PresComMes != null)? parseInt(utColumns[cl].PresComMes) : 0;
		if (utColumns[cl].EPSLines != null)
		{
			for(var fl=0; fl<utColumns[cl].EPSLines.length; fl++)
			{
				var fila = utColumns[cl].EPSLines[fl];
				subtotales.EPSLines[fila.Dia-1].E-= (fila.E != null)? parseInt(fila.E) : 0;
				subtotales.EPSLines[fila.Dia-1].S-= (fila.S != null)? parseInt(fila.S) : 0;
				subtotales.EPSLines[fila.Dia-1].P-= (fila.P != null)? parseInt(fila.P) : 0;
			}
		}
	}	
}

function UpdateViewFromModelEPS(show_ceros)
{
	//Recorrer valores para rellenar campos
	for(var cl=0; cl<utColumns.length; cl++)
	{
		$("#btn_clean_pais"+cl).show();
		$("#ut" + utColumns[cl].Pos + "_tit").html(utColumns[cl].Title);
		$("#ut" + utColumns[cl].Pos + "_tit").attr('title',utColumns[cl].Title);
		$("#pn_" + utColumns[cl].Pos).val(utColumns[cl].PresComMes).attr('disabled',false);
		$("#ut" + utColumns[cl].Pos + "_col0_tit").html("Entradas");
		$("#ut" + utColumns[cl].Pos + "_col1_tit").html(modoIntroduccion=='ES' ? "Salidas" : "Pernoc.");
		$("#ut" + utColumns[cl].Pos + "_col2_tit").html(modoIntroduccion=='ES' ? "Pernoc." : "Salidas");

		//Se habilitan los inputs para cada uno de los días
		for(var fl=1; fl<=diasMostrarFormulario; fl++)
		{
			$("#ut" + utColumns[cl].Pos + "_col0_dia" + fl).attr('disabled',false);
			$("#ut" + utColumns[cl].Pos + "_col1_dia" + fl).attr('disabled',false);
		}

		//Se rellenan los datos si la columna contiene valores
		tot_col_ent=0; tot_col_sal=0; tot_col_per=0;				
		if (utColumns[cl].EPSLines != null)
		{
			for(var fl=0; fl<utColumns[cl].EPSLines.length; fl++)
			{
				var fila = utColumns[cl].EPSLines[fl];
				
				var valcol2 = modoIntroduccion=='ES' ? fila.P : fila.S;
				if(valcol2==null) valcol2=0;
				$("#ut" + utColumns[cl].Pos + "_col2_dia" + fila.Dia).html(valcol2);
				if (show_ceros)
				{
					$("#ut" + utColumns[cl].Pos + "_col0_dia" + fila.Dia).val(fila.E);
					$("#ut" + utColumns[cl].Pos + "_col1_dia" + fila.Dia).val(modoIntroduccion=='ES' ? fila.S : fila.P);
				}
				else 
				{
					if (fila.E != 0)
						$("#ut" + utColumns[cl].Pos + "_col0_dia" + fila.Dia).val(fila.E);
					if (modoIntroduccion=='ES')
					{
						if (fila.S != 0)
							$("#ut" + utColumns[cl].Pos + "_col1_dia" + fila.Dia).val(fila.S);
					}
					else 
					{
						if (fila.P != 0)
							$("#ut" + utColumns[cl].Pos + "_col1_dia" + fila.Dia).val(fila.P);
					}
				}
				tot_col_ent+= (fila.E != null)?parseInt(fila.E) : 0;
				tot_col_sal+= (fila.S != null)?parseInt(fila.S) : 0;
				tot_col_per+= (fila.P != null)?parseInt(fila.P) : 0;						
			}
		}

		//Se refrescan los totales calculados
		$("#total" + utColumns[cl].Pos + "_fila_col0").html(tot_col_ent);
		$("#total" + utColumns[cl].Pos + "_fila_col1").html(modoIntroduccion=='ES' ? tot_col_sal : tot_col_per);
		$("#total" + utColumns[cl].Pos + "_fila_col2").html(modoIntroduccion=='ES' ? tot_col_per : tot_col_sal);

		//Se calcula la estancia media
		var est_med=0;
		var pcm = (utColumns[cl].PresComMes != null) ? parseInt(utColumns[cl].PresComMes) : 0;
		if(tot_col_ent+pcm!=0)
			est_med = tot_col_per / (tot_col_ent+pcm); 
		$("#est_med_" + utColumns[cl].Pos).html(String(Math.round(est_med*100)/100).replace('.',','));	
	}
	
	//Deshabilita el resto de los textbox
	for(var cl=utColumns.length;cl<maxUTMostrarPagina;cl++)
	{
		$("#btn_clean_pais"+cl).hide();
		$("#ut" + cl + "_tit").html("");
		$("#ut" + cl + "_tit").attr('title',"");
		$("#pn_" + cl).attr('disabled',true);
		for(var fl=1; fl<=diasMostrarFormulario; fl++)
		{
			$("#ut" + cl + "_col0_dia" + fl).attr('disabled',true);
			$("#ut" + cl + "_col1_dia" + fl).attr('disabled',true);
		}
	}

	//Recorrer los totales y actualizar su valor
	$("#total_col0_tit").html("Entradas");
	$("#total_col1_tit").html(modoIntroduccion=='ES' ? "Salidas" : "Pernoc.");
	$("#total_col2_tit").html(modoIntroduccion=='ES' ? "Pernoc." : "Salidas");
	//Refresca el total de pernoctaciones en la columna correspondiente
	if(modoIntroduccion=='ES')
	{
		$("#total_pn_col1").html('');
		$("#total_pn_col2").html(totales.PresComMes);
	}
	else
	{
		$("#total_pn_col2").html('');
		$("#total_pn_col1").html(totales.PresComMes);				
	}

	//Refresca cada una de las líneas de los totales
	tot_ent=0; tot_sal=0; tot_per=0;
	if (totales.EPSLines != null)
	{
		for(var fl=0; fl<totales.EPSLines.length; fl++)
		{
			$("#total_col0_dia" + totales.EPSLines[fl].Dia).html(totales.EPSLines[fl].E);
			$("#total_col1_dia" + totales.EPSLines[fl].Dia).html(modoIntroduccion=='ES' ? totales.EPSLines[fl].S : totales.EPSLines[fl].P);
			$("#total_col2_dia" + totales.EPSLines[fl].Dia).html(modoIntroduccion=='ES' ? totales.EPSLines[fl].P : totales.EPSLines[fl].S);

			tot_ent+=parseInt(totales.EPSLines[fl].E);
			tot_sal+=parseInt(totales.EPSLines[fl].S);
			tot_per+=parseInt(totales.EPSLines[fl].P);						
		}  
	} 

	//Se refresca el total de totales
	$("#total_totales_col0").html(tot_ent);
	$("#total_totales_col1").html(modoIntroduccion=='ES' ? tot_sal : tot_per);
	$("#total_totales_col2").html(modoIntroduccion=='ES' ? tot_per : tot_sal);

	//Se calcula la estancia media
	est_med=0;
	if(totales.PresComMes!=undefined)
	{
		if(tot_ent+totales.PresComMes!=0)
			est_med = tot_per / (tot_ent+totales.PresComMes); 
		$("#est_med_total").html(String(Math.round(est_med*100)/100).replace('.',','));
	}
	else
	{
		$("#est_med_total").html("0");
	}

	//Se calcula el índice de ocupación
	var numDiasAbierto = $("#dias_abierto").val();
	ind_ocup = 0;
	if (numDiasAbierto && !isNaN(numDiasAbierto) && numDiasAbierto != 0)
	if(numDiasAbierto*numPlazas!=0)
		ind_ocup = (tot_per*100)/(numDiasAbierto*numPlazas);
	$("#indice_ocupacion").html(String(Math.round(ind_ocup*100)/100).replace('.',',')+ "%");

	//Deshabilita todos los input al estar en sólo lectura
	if(soloLectura)
	{	
		$(':text').attr('disabled',true);
		$(":text").css("background-color","#FBFBFB").css("border", "1px solid #fbfbfb").css("text-align", "center");		
	}
	
	UpdateViewValidationEPS();
}

function ValidateEPS() 
{
	var hayExceso=false;
	
	//Recorrer las filas de las unidades territoriales para comprobar los valores
	for(var cl=0; cl<maxUTMostrarPagina; cl++)
	{
		if (utColumns[cl])
		{
			var clines = utColumns[cl].EPSLines;
			utColumns[cl].Err = new Array();
			for(var fl=0; fl<clines.length; fl++)
			{
				//Regla 106 (parcial)
				if(parseInt(clines[fl].E)<0 || parseInt(clines[fl].S)<0 || parseInt(clines[fl].P)<0)
					utColumns[cl].Err.push({Dia : clines[fl].Dia , Mensaje: "Los valores de Entradas, Salidas y Pernoctaciones deben ser enteros no negativos.", fatal: true});
				//Regla 107
				var p = clines[fl].P != null ? parseInt(clines[fl].P) : 0;
				var e = clines[fl].E != null ? parseInt(clines[fl].E) : 0;
				if(p<e) 
					utColumns[cl].Err.push({Dia : clines[fl].Dia , Mensaje: "Las pernoctaciones de un día deben ser mayores o iguales al número de entradas de dicho día.", fatal: true});
			}
			
			for (var d=1; d <= diasMostrarFormulario; d++)
			{
				//Regla 108
				var epsDiaAct = BuscarEPSLine(utColumns[cl].EPSLines, d);
				var e = 0;
				var s = 0;
				var p = 0;
				if (epsDiaAct)
				{
					e = (epsDiaAct.E != null) ? parseInt(epsDiaAct.E) : 0;
					s = (epsDiaAct.S != null) ? parseInt(epsDiaAct.S) : 0;
					p = (epsDiaAct.P != null) ? parseInt(epsDiaAct.P) : 0;
				}
				
				var pant = 0;
				if(d==1)
				{
					pant= (utColumns[cl].PresComMes != null)? parseInt(utColumns[cl].PresComMes) : 0;	
				}
				else
				{
					var epsDiaAnt = BuscarEPSLine(utColumns[cl].EPSLines, d-1);
					
					if (epsDiaAnt)
					{
						pant = (epsDiaAnt.P != null)? parseInt(epsDiaAnt.P) : 0;
					}					
				}
					
				if (p != (pant + e -s))
				{
					utColumns[cl].Err.push({Dia : d , Mensaje: "Las pernoctaciones para cada día deben ser iguales a las del día anterior + Entradas - Salidas.", fatal: true});
				}
				
			}
		}
	}
	
	//Regla 112
	if(totales && totales.EPSLines)
	{
		totales.Err = new Array();
		for(var fl=0;fl<totales.EPSLines.length;fl++)
		{
			if(parseInt(totales.EPSLines[fl].P) > ((numPlazas + numPlazasSupletorias)+excesoPlazas))
			{
				totales.Err.push({Dia : totales.EPSLines[fl].Dia , Mensaje: "El nº de pernoctaciones en un día no puede superar al nº de plazas disponibles más las supletorias (" + (numPlazas + numPlazasSupletorias) + ").", fatal: true});
			}
			else if(parseInt(totales.EPSLines[fl].P) > (numPlazas + numPlazasSupletorias))
			{				
				totales.Err.push({Dia : totales.EPSLines[fl].Dia , Mensaje: "El nº de pernoctaciones en un día no puede superar al nº de plazas disponibles más las supletorias (" + (numPlazas + numPlazasSupletorias) + ").", fatal: false, exceso: true});
				hayExceso=true;
			}
		}
	}
	
	if(hayExceso==false)
	{
		// Ya no hay exceso de plazas.
		// Limpiamos el motivo para que la próxima vez que aparezca un exceso lo pidamos nuevamente.
		codMotivoExcesoPlazas=null;
		detalleMotivoExcesoPlazas=null;
	}
}

function BuscarEPSLine(lines, dia)
{
	//Buscar eps dia anterior
	for(var fl=0; fl<lines.length; fl++)
	{
		if (lines[fl].Dia == dia)
		{
			return lines[fl];
		}
	}
	return null;	
}

function UpdateViewValidationEPS()
{
	//Recorrer las filas de las unidades territoriales para comprobar si tienen errores
	for(var cl=0; cl<maxUTMostrarPagina; cl++)
	{
		if (utColumns[cl] && utColumns[cl].EPSLines)
		{
			for(var fl=0; fl<utColumns[cl].Err.length; fl++)
			{
				$("img[id=imgerror_ut" + cl + "_dia" + utColumns[cl].Err[fl].Dia + "]").show();
				$("tr[id=fila_ut" + cl + "_dia" + utColumns[cl].Err[fl].Dia + "]").addClass("error_fila");
			}
			if(utColumns[cl].Err.length != 0)
			{
				$("img[id=imgerror_ut" + cl + "]").show();
			}
			else
			{
				$("img[id=imgerror_ut" + cl + "]").hide();
			}
		}
	}
	
	//Recorrer las fila de la columna de totales para comprobar si tienen errores
	if(totales && totales.Err)
	{
		for(var fl=0; fl<totales.Err.length; fl++)
		{
			$("img[id=imgerror_uttot_dia" + totales.Err[fl].Dia + "]").show();
		}
	}
}

function UpdateOcupPlazas()
{
	//Se calcula el número máximo de pernoctaciones
	var numDiasAbierto = $("#dias_abierto").val();
	
	if (numDiasAbierto && !isNaN(numDiasAbierto))
	{
		max_pern = numDiasAbierto * (numPlazas + numPlazasSupletorias); 			       
		$("#maximo_pernoctaciones").html(Math.floor(max_pern));
	}
	else
	{
		$("#maximo_pernoctaciones").html(" -");
	}
}

function CleanViewHab()
{
	//Recorrer campos para limpiar valores
	for(var fl=1; fl<=numDiasMes; fl++)
	{
		$("#hab_suplet_dia" + fl).val("");
		$("#hab_dobles_dia" + fl).val("");
		$("#hab_indiv_dia" + fl).val("");
		$("#hab_otras_dia" + fl).val("");
	}
	
	//Recorrer campos para limpiar valores
	for(var fl=1; fl<=numDiasMes; fl++)
	{
		$("#hab_coltotal_dia" + fl).html("-");
	}

	//Se limpian los totales generales
	$("#hab_tot_suplet").html("-");
	$("#hab_tot_dobles").html("-");
	$("#hab_tot_indiv").html("-");
	$("#hab_tot_otras").html("-");
	$("#hab_tot_totales").html("-");
	
	$("img[id^='imgerror_hab']").hide();
	$("img[id^='imgerror_hab']").closest("td").removeClass("error_fila");
}

function UpdateModelFromViewHab()
{
	//Recorrer campos para actualizar valores
	habitaciones = new Array();
	for(var fl=1; fl<=numDiasMes; fl++)
	{
		sup = $("#hab_suplet_dia" + fl).val();
		sup = !sup ? null : parseInt(sup);
		dob = $("#hab_dobles_dia" + fl).val();
		dob = !dob ? null : parseInt(dob);
		ind = $("#hab_indiv_dia" + fl).val();
		ind = !ind ? null : parseInt(ind);
		otr = $("#hab_otras_dia" + fl).val();
		otr = !otr ? null : parseInt(otr);

		if(sup!=null || dob!=null || ind!=null || otr!=null)
			habitaciones.push(new Linea_Habitacion(fl, sup, dob, ind, otr));
	}
	
	ValidateHab();
}

function UpdateViewFromModelHab()
{
	//Recorrer valores para rellenar campos

	tot_sup=0; tot_dob=0; tot_ind=0; tot_otr=0;				
	if (habitaciones != null)
	{
		for(var fl=0; fl<habitaciones.length; fl++)
		{
			$("#hab_suplet_dia" + habitaciones[fl].Dia).val(habitaciones[fl].Sup);
			$("#hab_dobles_dia" + habitaciones[fl].Dia).val(habitaciones[fl].Dob);
			$("#hab_indiv_dia" + habitaciones[fl].Dia).val(habitaciones[fl].Ind);
			$("#hab_otras_dia" + habitaciones[fl].Dia).val(habitaciones[fl].Otr);
			$("#hab_coltotal_dia" + habitaciones[fl].Dia).html(habitaciones[fl].Dob+habitaciones[fl].Ind+habitaciones[fl].Otr);

			tot_sup+=(habitaciones[fl].Sup != null)? parseInt(habitaciones[fl].Sup) : 0;
			tot_dob+=(habitaciones[fl].Dob != null)? parseInt(habitaciones[fl].Dob) : 0; 
			tot_ind+=(habitaciones[fl].Ind != null)? parseInt(habitaciones[fl].Ind) : 0;
			tot_otr+=(habitaciones[fl].Otr != null)? parseInt(habitaciones[fl].Otr) : 0;
		}
	}

	//Se refrescan los totales calculados
	$("#hab_tot_suplet").html(tot_sup);
	$("#hab_tot_dobles").html(tot_dob);
	$("#hab_tot_indiv").html(tot_ind);
	$("#hab_tot_otras").html(tot_otr);
	$("#hab_tot_totales").html(tot_dob+tot_ind+tot_otr);
	
	UpdateViewValidationHab();
}	    

function ValidateHab() 
{
	var hayExceso=false;
	
	//Recorrer las filas de las unidades territoriales para comprobar los valores
	for(var d=0; d<habitaciones.length; d++)
	{
		habitaciones[d].Err = new Array();
		//Regla 106 (parcial)
		if(habitaciones[d].Sup<0 || habitaciones[d].Dob<0 || habitaciones[d].Ind<0 || habitaciones[d].Otr<0)
		{
			if (esHotel)
				habitaciones[d].Err.push({msg: "Los valores de plazas supletorias y habitaciones deben ser enteros no negativos.", fatal: true});
			else
				habitaciones[d].Err.push({msg: "Los valores de plazas supletorias y apartamentos deben ser enteros no negativos.", fatal: true});
		}
		
		//Regla 113
		var suma = habitaciones[d].Dob + habitaciones[d].Ind + habitaciones[d].Otr;
		if(suma > (numHabitaciones+excesoHabitaciones))
		{
			if (esHotel)
				habitaciones[d].Err.push({msg: "El nº de habitaciones ocupadas cada día ha de ser menor o igual al nº de habitaciones del establecimiento (" + (numHabitaciones) + ").", fatal: true});
			else
				habitaciones[d].Err.push({msg: "El nº de apartamentos ocupados cada día ha de ser menor o igual al nº de apartamentos del establecimiento (" + (numHabitaciones) + ").", fatal: true});
		}
		else if(suma > numHabitaciones)
		{
			if (esHotel)
				habitaciones[d].Err.push({msg: "El nº de habitaciones ocupadas cada día ha de ser menor o igual al nº de habitaciones del establecimiento (" + (numHabitaciones) + ").", fatal: false, exceso: true});
			else
				habitaciones[d].Err.push({msg: "El nº de apartamentos ocupados cada día ha de ser menor o igual al nº de apartamentos del establecimiento (" + (numHabitaciones) + ").", fatal: false, exceso: true});
			hayExceso=true;
		}
		
		//Regla 203
		if((habitaciones[d].Sup!=0 && habitaciones[d].Sup != null)
				&& (habitaciones[d].Dob==0 || habitaciones[d].Dob == null) 
				&& (habitaciones[d].Ind==0 || habitaciones[d].Ind == null) 
				&& (habitaciones[d].Otr==0 || habitaciones[d].Otr == null))
		{
			if (esHotel)
				habitaciones[d].Err.push({msg: "Si hay plazas supletorias ocupadas, tiene que haber habitaciones ocupadas.", fatal: true});
			else
				habitaciones[d].Err.push({msg: "Si hay plazas supletorias ocupadas, tiene que haber apartamentos ocupados.", fatal: true});
		}
	}
	
	if(hayExceso==false)
	{
		// Ya no hay exceso de habitaciones.
		// Limpiamos el motivo para que la próxima vez que aparezca un exceso lo pidamos nuevamente.
		codMotivoExcesoHabitaciones=null;
		detalleMotivoExcesoHabitaciones=null;
	}
}

function UpdateViewValidationHab()
{
	if (habitaciones)
	{
		for(var d=0; d<habitaciones.length; d++)
		{
			if(habitaciones[d].Err && habitaciones[d].Err.length>0)
			{
				$("img[id=imgerror_hab_dia" + habitaciones[d].Dia + "]").show();
				$("img[id=imgerror_hab_dia" + habitaciones[d].Dia + "]").closest("td").addClass("error_fila");
			}
		}
	}
}

function UpdateApartOcup()
{
	var numDiasAbierto = $("#dias_abierto").val();
	var total = parseInt($("#hab_tot_totales").html());
	
	//Calcular total de pernoctaciones
	tot_per=0;
	if (totales.EPSLines != null)
	{
		for(var fl=0; fl<totales.EPSLines.length; fl++)
		{
			tot_per+=parseInt(totales.EPSLines[fl].P);						
		}  
	} 
	
	if (numDiasAbierto && !isNaN(numDiasAbierto) && numDiasAbierto != 0)
	{
		$("#indice_apart_ocup").html(String(Math.round((total*100 / (numHabitaciones*numDiasAbierto))*100)/100).replace('.',',') + '%');
		
		ind_ocup = 0;
		if(numDiasAbierto*numPlazas!=0)
			ind_ocup = (tot_per*100)/(numDiasAbierto*numPlazas);
		$("#indice_ocupacion").html(String(Math.round(ind_ocup*100)/100).replace('.',',')+ "%");
	}
	else
	{
		$("#indice_apart_ocup").html(' -');
	}
}

function UpdateModelFromViewPersPrec()
{
	//Recorrer campos para actualizar valores
	personalPrecios.NoRem = $("#pers_no_remunerado").val();
	personalPrecios.Fijo = $("#pers_fijo").val();
	personalPrecios.Event = $("#pers_eventual").val();

	valor = $("#ing_hab_disp_mensual").val();
	personalPrecios.IngDispMen = parseFloat(valor.replace(',','.'));
	valor =  $("#tar_med_habitac").val();
	personalPrecios.TarMedHab = parseFloat(valor.replace(',','.'));

	personalPrecios.ADR = new Array();
	for(key in tipo_cliente_adr)
	{
		valor = $("#adr_" + tipo_cliente_adr[key]).val();
		if(valor) 
		{
			valor = parseFloat(valor.replace(',','.'));
			personalPrecios.ADR.push(new ADR_Line(key,valor));
		}
	}

	personalPrecios.NumHabOcup = new Array();
	for(key in tipo_cliente_adr)
	{
		valor = $("#num_habocup_" + tipo_cliente_adr[key]).val();
		if(valor) 
		{
			valor = parseInt(valor);
			personalPrecios.NumHabOcup.push(new ADR_Line(key,valor));
		}
	}			

	personalPrecios.PctHabOcup = new Array();
	for(key in tipo_cliente_adr)
	{
		valor = $("#pct_habocup_" + tipo_cliente_adr[key]).val();
		if(valor) 
		{
			valor = parseFloat(valor.replace(',','.'));
			personalPrecios.PctHabOcup.push(new ADR_Line(key,valor));
		}
	}	
	
	ValidatePersPrec();
}

function ValidatePersPrec()
{
	//Reglas 115 y 116. Recorrer las filas de ADR por tipo de cliente para ver si cumplen las condiciones
	personalPrecios.Err = new Array();
	
	//Regla 201
	if(parseInt($("#pers_no_remunerado").val())<0) personalPrecios.Err.push({Tipo: 0, Key: "no_remunerado", Mensaje: "Los datos de personal deben ser enteros no negativos."});
	if(parseInt($("#pers_fijo").val())<0) personalPrecios.Err.push({Tipo: 0, Key: "fijo", Mensaje: "Los datos de personal deben ser enteros no negativos."});
	if(parseInt($("#pers_eventual").val())<0) personalPrecios.Err.push({Tipo: 0, Key: "eventual", Mensaje: "Los datos de personal deben ser enteros no negativos."});
	
	//Regla 202
	if(parseInt($("#ing_hab_disp_mensual").val())<0) personalPrecios.Err.push({Tipo: 1, Key: "ing_hab_disp_mensual", Mensaje: "Los datos de precios deben ser enteros no negativos."});
	if(parseInt($("#tar_med_habitac").val())<0) personalPrecios.Err.push({Tipo: 1, Key: "tar_med_habitac", Mensaje: "Los datos de precios deben ser enteros no negativos."});
	
	for(key in tipo_cliente_adr)
	{
		var lineADR = BuscarLineaADR(key);
		var lineCantidad;
		if (modoIntroduccionADR == 'N')
			lineCantidad = BuscarLineaNumHabOcup(key);
		else 
			lineCantidad = BuscarLineaPctHabOcup(key);
		var valorADR=0; var valorCantidad=0;
		if(lineADR) valorADR=lineADR.Valor;
		if(lineCantidad) valorCantidad=lineCantidad.Valor;
		//Regla 202
		if(valorADR<0 || valorCantidad<0)
		{
			personalPrecios.Err.push({Tipo: 2, Key: key, Mensaje: "Los datos de precios deben ser enteros no negativos."});		
		}
		//Regla 115
		if (valorADR!=0 && valorCantidad==0)
		{
			if (esHotel)
				personalPrecios.Err.push({Tipo: 2, Key: key, Mensaje: "Si el ADR por tipo de cliente es >0, entonces el % de ocupación o el número de habitaciones ocupadas por ese tipo de cliente ha de ser >0."});
			else
				personalPrecios.Err.push({Tipo: 2, Key: key, Mensaje: "Si el ADR por tipo de cliente es >0, entonces el % de ocupación o el número de apartamentos ocupados por ese tipo de cliente ha de ser >0."});
		}
		//Regla 116
		if (valorADR==0 && valorCantidad!=0)
		{
			var limite=0;
			if((tipo_cliente_perct[key]!=null)&&(tipo_cliente_perct[key]>0.0))
			{
				// Hay limite extendido para las invitaciones de este tipo de clientes.
				limite=tipo_cliente_perct[key];
				if(modoIntroduccionADR == 'N')
					limite=Math.ceil(((numHabitaciones * numDiasMes) * limite) / 100.0);
			}
			if(valorCantidad > limite)
			{
				if (esHotel)
					personalPrecios.Err.push({Tipo: 2, Key: key, Mensaje: "Si el ADR por tipo de cliente es cero, % de ocupación o número de habitaciones para ese tipo ha de ser 0."});
				else
					personalPrecios.Err.push({Tipo: 2, Key: key, Mensaje: "Si el ADR por tipo de cliente es cero, % de ocupación o número de apartamentos para ese tipo ha de ser 0."});
			}
		}
	}
}

function BuscarLineaADR(tipo)
{
	for(var i = 0; i < personalPrecios.ADR.length; i++)
	{
		if (personalPrecios.ADR[i].Tipo == tipo)
			return personalPrecios.ADR[i];
	}
	return null;
}

function BuscarLineaPctHabOcup(tipo)
{
	for(var i = 0; i < personalPrecios.PctHabOcup.length; i++)
	{
		if (personalPrecios.PctHabOcup[i].Tipo == tipo)
			return personalPrecios.PctHabOcup[i];
	}
	return null;
}

function BuscarLineaNumHabOcup(tipo)
{
	for(var i = 0; i < personalPrecios.NumHabOcup.length; i++)
	{
		if (personalPrecios.NumHabOcup[i].Tipo == tipo)
			return personalPrecios.NumHabOcup[i];
	}
	return null;
}

function UpdateViewFromModelPersPrec()
{
	//Recorrer valores para rellenar campos
	if(personalPrecios!=null)
	{
		$("#pers_no_remunerado").val(personalPrecios.NoRem);
		$("#pers_fijo").val(personalPrecios.Fijo);
		$("#pers_eventual").val(personalPrecios.Event);
		if(personalPrecios.IngDispMen)
			$("#ing_hab_disp_mensual").val(String(personalPrecios.IngDispMen).replace('.',','));
		if(personalPrecios.TarMedHab)
			$("#tar_med_habitac").val(String(personalPrecios.TarMedHab).replace('.',','));

		if(personalPrecios.ADR!=null)
		{
			for(var fl=0;fl<personalPrecios.ADR.length;fl++)
			{
				$("#adr_" + tipo_cliente_adr[personalPrecios.ADR[fl].Tipo]).val(String(personalPrecios.ADR[fl].Valor).replace('.',','));
			}
		}

		if(personalPrecios.NumHabOcup!=null)
		{
			for(var fl=0;fl<personalPrecios.NumHabOcup.length;fl++)
			{
				$("#num_habocup_" + tipo_cliente_adr[personalPrecios.NumHabOcup[fl].Tipo]).val(personalPrecios.NumHabOcup[fl].Valor);
			}
		}

		if(personalPrecios.PctHabOcup!=null)
		{
			for(var fl=0;fl<personalPrecios.PctHabOcup.length;fl++)
			{
				$("#pct_habocup_" + tipo_cliente_adr[personalPrecios.PctHabOcup[fl].Tipo]).val(String(personalPrecios.PctHabOcup[fl].Valor).replace('.',','));
			}
		}	  
		
		UpdateValidationViewPersPrec();
	}	
}	    

function CleanViewPersPrec()
{	
	$("img[id^='imgerror_adr']").hide();
	$("img[id^='imgerror_adr']").closest("tr").find('.celda_adr_PREC,.celda_numhab_PREC,.celda_porchab_PREC').removeClass("error_fila");
	$("img[id^='imgerror_pers']").hide();
	$("img[id^='imgerror_pers']").closest("tr").removeClass("error_fila");
	$("img[id^='imgerror_prec']").hide();
	$("img[id^='imgerror_prec']").closest("tr").removeClass("error_fila");	
}

function UpdateValidationViewPersPrec()
{
	if (personalPrecios.Err)
	{
		for (var i = 0; i < personalPrecios.Err.length; i++)
		{
			switch(personalPrecios.Err[i].Tipo)
			{
				case 0:  //Errores de personal
					$("img[id=imgerror_pers_" + personalPrecios.Err[i].Key + "]").show();
					$("img[id=imgerror_pers_" + personalPrecios.Err[i].Key + "]").closest("tr").addClass("error_fila");					
					break;
				case 1:  //Errores de precios
					$("img[id=imgerror_prec_" + personalPrecios.Err[i].Key + "]").show();
					$("img[id=imgerror_prec_" + personalPrecios.Err[i].Key + "]").closest("tr").addClass("error_fila");					
					break;

				case 2:  //Errores de ADR
					$("img[id=imgerror_adr_" + tipo_cliente_adr[personalPrecios.Err[i].Key] + "]").show();
					$("img[id=imgerror_adr_" + tipo_cliente_adr[personalPrecios.Err[i].Key] + "]").closest("tr").find('.celda_adr_PREC,.celda_numhab_PREC,.celda_porchab_PREC').addClass("error_fila");
					break;
			}
		}
	}
}

function getAyudaMotivoExceso(id,cod)
{
	var ayuda="";
	if(id.endsWith('_plz'))
	{
		ayuda=ayudaMotivosExcesos(cod);
	}
	else
	{
		ayuda=ayudaMotivosExcesos(cod);
	}
	return ayuda;
}

function MostrarErroresExceso(excPl,excHb,funcionContinuar)
{
	busy(true);

	var codigo="";
	
	var alto=130;
	if((excPl!=0)&&(excHb!=0))
		alto=90;
	if(excPl!=0)
		codigo+="<div class='cuadro fondo_amarillo'>"+
		"<p style='margin-top:15px;'>Según los datos que tiene el ISTAC, las plazas ocupadas superan el número de plazas del establecimiento.</p>"+
		"<p>Indique el motivo para poder guardar el cuestionario:</p>"+
		"<table style=\"margin-left:10px;width:99%\">"+
		"<tr><td style=\"width:11%;\"><label for=\"motivo_exc_plz\">Motivo:</label></td><td><select id=\"motivo_exc_plz\" name=\"motivo_exc_plz\" style=\"width:90%;\">"+getMotivosExcesosOps()+"</select></td></tr>"+
		"<tr><td style=\"width:11%; vertical-align: top\"><label for=\"motivo_exc_plz_det\">Detalle:</label></td><td><textarea id=\"motivo_exc_plz_det\" name=\"motivo_exc_plz_det\" disabled style=\"width:90%; height:"+alto+"px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em;\" placeholder=\"Describa aquí el motivo del exceso de plazas ocupadas...\"></textarea></td></tr>"+
		"<tr><td style=\"width:11%;\"></td><td><div id=\"motivo_exc_plz_ayuda\" class=\"pulsante\" style=\"width:90%; background-color: #D3D3D3; padding: 0px 4px 0px 4px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em; font-weight: bold; font-style: italic\"></div></td></tr>"+
		"</table></div>";
	if(excHb!=0)
		codigo+="<div class='cuadro fondo_amarillo'>"+
		"<p style='margin-top:15px;'>Según los datos que tiene el ISTAC, la ocupación de "+(esHotel ? "habitaciones" : "apartamentos")+" supera el número de unidades del establecimiento.</p>"+
		"<p>Indique el motivo para poder guardar el cuestionario:</p>"+
		"<table style=\"margin-left:10px;width:99%\">"+
		"<tr><td style=\"width:11%;\"><label for=\"motivo_exc_hab\">Motivo:</label></td><td><select id=\"motivo_exc_hab\" name=\"motivo_exc_hab\" style=\"width:90%;\">"+getMotivosExcesosOps()+"</select></td></tr>"+
		"<tr><td style=\"width:11%; vertical-align: top\"><label for=\"motivo_exc_hab_det\">Detalle:</label></td><td><textarea id=\"motivo_exc_hab_det\" name=\"motivo_exc_hab_det\" disabled style=\"width:90%; height:"+alto+"px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em;\" placeholder=\"Describa aquí el motivo del exceso de habitaciones ocupadas...\"></textarea></td></tr>"+
		"<tr><td style=\"width:11%;\"></td><td><div id=\"motivo_exc_hab_ayuda\" class=\"pulsante\" style=\"width:90%; background-color: #D3D3D3; padding: 0px 4px 0px 4px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em; font-weight: bold; font-style: italic\"></div></td></tr>"+
		"</table></div>";
	$("#msg_exceso").html(codigo);
	
	// Limpiamos los motivos
	$("select[id^='motivo_exc_']").val([]);
	$("textarea[id^='motivo_exc_']").val("");
	$("div[id^='motivo_exc_'][id$='ayuda']").text("");
	
	// ...cargamos valores previos de los motivos y sus detalles.
	// ...
	if(codMotivoExcesoPlazas!=null)
	{
		$("#motivo_exc_plz").val(codMotivoExcesoPlazas);
		if($("#motivo_exc_plz").find(":selected").data("detalle"))
			$("#motivo_exc_plz_det").attr("disabled",false);
		else
			$("#motivo_exc_plz_det").attr("disabled",true);
	}
	if(detalleMotivoExcesoPlazas!=null)
		$("#motivo_exc_plz_det").val(detalleMotivoExcesoPlazas);
	if(codMotivoExcesoHabitaciones!=null)
	{
		$("#motivo_exc_hab").val(codMotivoExcesoHabitaciones);
		if($("#motivo_exc_hab").find(":selected").data("detalle"))
			$("#motivo_exc_hab_det").attr("disabled",false);
		else
			$("#motivo_exc_hab_det").attr("disabled",true);
	}
	if(detalleMotivoExcesoHabitaciones!=null)
		$("#motivo_exc_hab_det").val(detalleMotivoExcesoHabitaciones);
	
	$("select[id^='motivo_exc_']").change(
			function()
			{
				var elto=$(this).find(":selected");
				var id=$(this).attr('id');
				var cod=elto.val();
				if(elto.data("detalle"))
					$("#"+id+"_det").attr("disabled",false).focus();
				else
					$("#"+id+"_det").attr("disabled",true);
				var textoAyuda=$("#"+id+"_ayuda");
				textoAyuda.text(getAyudaMotivoExceso(id,cod));				
				var tmpClass=textoAyuda.attr('class');
				textoAyuda.removeClass();
				setTimeout(function(){textoAyuda.addClass(tmpClass).addClass('start-now');},10);
			}
	);
	
	$( "#dialog-exceso" ).dialog(
		{
			title : 'Exceso de ocupación',
			//close: function(event, ui){busy(false);},
			buttons: {
				"Aceptar": function() {
					var ok=true;
					var msg="";
					$("select[id^='motivo_exc_']").each(
						function()
						{
							var elto=$(this).find(":selected");
							if(elto.length==0)
							{
								ok=false;
								msg="Es obligatorio indicar un motivo del exceso.";
								return false;
							}
							var cod=elto.val();
							if(cod!=null)
							{
								var id=$(this).attr('id');
								var detalle=$("#"+id+"_det").val();
								if(elto.data("detalle") && (detalle==""))
								{
									ok=false;
									msg="Es obligatorio indicar el detalle requerido del motivo del exceso.";
									return false;
								}
							}
						}
					);
					if(ok==false)
					{
						alert(msg);
						return;
					}
					else
					{
						if(excPl!=0)
						{
							codMotivoExcesoPlazas=$("#motivo_exc_plz").find(":selected").val();
							detalleMotivoExcesoPlazas=$("#motivo_exc_plz_det").val();
						}
						if(excHb!=0)
						{
							codMotivoExcesoHabitaciones=$("#motivo_exc_hab").find(":selected").val();
							detalleMotivoExcesoHabitaciones=$("#motivo_exc_hab_det").val();
						}
						funcionContinuar();
						$( this ).dialog( "close" );
					}
				},
				"Cancelar": function() {
					busy(false);
					$( this ).dialog( "close" );
				}
			}
		}
	);
	$( "#dialog-exceso" ).dialog("open");
}

// Se recorren todos los errores detectados durante la entrada de datos...
// Validaciones que se realizan de los datos del formulario:
// 1.- Se cuentan los errores en las filas de las UTs.
// 2.- Se cuentan los errores de exceso en las filas de totales
// 3.- Se cuentan los errores en las filas de habitaciones.
// 4.- Se cuentan los errores en las filas de personal y precios.
// NOTA: Los errores de exceso de habitaciones o plazas se cuentan separadamente.
// Si existen errores, se informa al usuario y se aborta la operación.
// Si no existen errores pero existen excesos, se solicita al usuario el detalle justificativo y se continúa con la operación.
function HayErrorValidacion(funcionGuardarExito)
{
	
	var con_error = 0;
	var con_errores_exceso_hab = 0;
	var con_errores_exceso_plz = 0;
	var listaerrores = '<ul class="lista_ventana">';
	
	//Recorrer las filas de las unidades territoriales para comprobar los valores
	for(var cl=0; cl<maxUTMostrarPagina; cl++)
	{
		if (utColumns[cl])
		{
			if (utColumns[cl].Err)
			{
				//con_error += utColumns[cl].Err.length;
				if(utColumns[cl].Err.length!=0)
				{
					var dias_error=',';
					for(var i=0;i<utColumns[cl].Err.length;i++)
					{
						if(utColumns[cl].Err[i].fatal)
						{
							con_error++;
							var dia = utColumns[cl].Err[i].Dia;
							if(dias_error.indexOf(','+dia+',')<0) dias_error=dias_error + dia + ',';
						}
						else
						{
							if(utColumns[cl].Err[i].exceso)
								con_errores_exceso_plz++;
						}
					}
					listaerrores = listaerrores + '<li>La unidad territorial ' + utColumns[cl].Title + ' contiene errores para el/los día/s ' + dias_error.substring(1,dias_error.length-1) + '.</li>';
				}
			}
		}
		
	}
	
	if(totales && totales.Err)
	{
		for(var i=0;i<totales.Err.length;i++)
		{
			if((totales.Err[i].fatal==false)&&(totales.Err[i].exceso==true))
				con_errores_exceso_plz++;
		}
	}
	
	if (habitaciones)
	{
		var dias_error=',';
		for(var d=0; d<habitaciones.length; d++)
		{
			if (habitaciones[d].Err)
			{
				var ne=0;
				for(var e=0;e<habitaciones[d].Err.length;e++)
				{
					if(habitaciones[d].Err[e].fatal)
						ne++;
					else
					{
						if(habitaciones[d].Err[e].exceso)
							con_errores_exceso_hab++;
					}
				}
				if(ne>0)
				{
					con_error += ne;
					var dia = habitaciones[d].Dia;
					if(dias_error.indexOf(','+dia+',')<0) dias_error=dias_error + dia + ',';
				}
			}
		}
		if(dias_error!=',')
			listaerrores = listaerrores + '<li>' + (esHotel ? "Habitaciones Ocupadas" : "Apartamentos Ocupados") + ' contiene errores para el/los día/s ' + dias_error.substring(1,dias_error.length-1) + '.</li>';
	}
	
	if (personalPrecios && personalPrecios.Err)
	{
		con_error += personalPrecios.Err.length;
		var error_personal_neg=false;
		var error_precios_neg=false;
		for(var i=0;i<personalPrecios.Err.length;i++)
		{
			switch(personalPrecios.Err[i].Tipo)
			{
				case 0:
					if(!error_personal_neg)
					{
						listaerrores = listaerrores + '<li>' + personalPrecios.Err[i].Mensaje + '</li>';
						error_personal_neg=true;
					}
					break;
				case 1:
					if(!error_precios_neg)
					{
						listaerrores = listaerrores + '<li>' + personalPrecios.Err[i].Mensaje + '</li>';
						error_precios_neg=true;
					}					
					break;
				case 2:
					listaerrores = listaerrores + '<li>' +  tipo_cliente_label[personalPrecios.Err[i].Key] + ": " + personalPrecios.Err[i].Mensaje + '</li>';
					break;						
			}
		}
	}
	
	if (con_error != 0)
	{
		busy(true);
		
		$("#msg_errores").html("<div class='cuadro fondo_amarillo'>"+
				"<p style='margin-top:15px;'>Existen errores en el cuestionario. Debe corregirlos para poder continuar con la operación:</p>" + listaerrores +
			"</ul></div>");
		
		$( "#dialog-detail" ).dialog({ title : 'Errores en el cuestionario', close: function(event, ui){busy(false);} });
		$( "#dialog-detail" ).dialog("open");
		return true;
	}
	else
	{
		if(((con_errores_exceso_plz!=0)&&(codMotivoExcesoPlazas==null)) || ((con_errores_exceso_hab!=0)&&(codMotivoExcesoHabitaciones==null)))
		{
			MostrarErroresExceso(con_errores_exceso_plz,con_errores_exceso_hab,funcionGuardarExito);			
			return true;
		}
		else
			funcionGuardarExito();
	}
	return false;
}

function CambiarModoCumplimentado(modo)
{
	modoCumplimentado = modo;
	if(modo=='V')
		$("#modoCumplimentado").html("<strong>vertical</strong> &#124; <a class='enlace' href=\"javascript:CambiarModoCumplimentado('H')\" id=\"modoHorizontal\">horizontal</a>");
	else
		$("#modoCumplimentado").html("<a class='enlace' href=\"javascript:CambiarModoCumplimentado('V')\" id=\"modoHorizontal\">vertical</a> &#124; <strong>horizontal</strong>");

	RefrescarTabIndex();
	if(!$('#pn_0')[0].disabled) $('#pn_0').focus().select();
}

function CambiarModoIntroduccionADR(modo)
{
	modoIntroduccionADR = modo;
	comienzoFraseIntroduccion = "Cumplimentar: ";
	label = (esHotel ? "habitac." : "apart."); 
	if(modo=='N')
		$("#modoIntroduccionADR").html(comienzoFraseIntroduccion+"<strong>Nº "+label+"</strong> &#124; <a class='enlace' href=\"javascript:PreguntarCambiarModoADR('P')\" id=\"modoPorcHab\">% "+label+"</a>");
	else
		$("#modoIntroduccionADR").html(comienzoFraseIntroduccion+"<a class='enlace' href=\"javascript:PreguntarCambiarModoADR('N')\" id=\"modoNumHab\">Nº "+label+"</a> &#124; <strong>% "+label+"</strong>");

	for(key in tipo_cliente_adr)
	{
		$('#num_habocup_' + tipo_cliente_adr[key]).attr('disabled', modo!='N');
		$('#pct_habocup_' + tipo_cliente_adr[key]).attr('disabled', modo=='N');

		if (modo=='N')
		{
			$('#pct_habocup_' + tipo_cliente_adr[key]).val('');
		}
		else 
		{
			$('#num_habocup_' + tipo_cliente_adr[key]).val('');
		}
	}			
	RefrescarTabIndex();
	UpdateModelFromViewPersPrec();
	CleanViewPersPrec();
	UpdateViewFromModelPersPrec();
	if(!$('#adr_to_tradic')[0].disabled) $("#adr_to_tradic").focus();
}

function RefrescarTabIndex()
{
	tabindex = 0;

	//Días abierto
	$('#dias_abierto').attr('tabindex', tabindex++);

	//Pestaña entradas, salidas y pernoctaciones
	//Tanto para el modo de cumplimentado horizonatal como vertical, las pernoctaciones se introducen siempre en horizontal
	for(var cl=0; cl<maxUTMostrarPagina; cl++)
	{
		elem = $('#pn_' + cl)[0];
		if(elem.disabled)
			$('#pn_' + cl).attr('tabindex', -1);
		else
			$('#pn_' + cl).attr('tabindex', tabindex++);
	}

	var tipoCeldaHab = ['suplet', 'dobles', 'indiv', 'otras']; 
	//Dependiendo del modo de cumplimentado se ordenan los tabindex
	if(modoCumplimentado=='V')
	{
		//Pestaña E,S,P
		for(var cl=0; cl<maxUTMostrarPagina; cl++)
		{
			for(var fl=1; fl<=diasMostrarFormulario; fl++)
			{
				elem = $('#ut' + cl + '_col0_dia' + fl)[0];
				if(elem.disabled)
				{						
					$('#ut' + cl + '_col0_dia' + fl).attr('tabindex', -1);
					$('#ut' + cl + '_col1_dia' + fl).attr('tabindex', -1);
				}				
				else
				{						
					$('#ut' + cl + '_col0_dia' + fl).attr('tabindex', tabindex++);
					$('#ut' + cl + '_col1_dia' + fl).attr('tabindex', tabindex++);
				}				
			}
		}
		
		//Pestaña Habitaciones
		for(var tipo=0; tipo<4; tipo++)
		{
			for(var fl=1; fl<=diasMostrarFormulario; fl++)
			{
				
				elem = $('#hab_' + tipoCeldaHab[tipo] + '_dia' + fl);
				if(elem[0].disabled)
				{						
					elem.attr('tabindex', -1);
				}				
				else
				{						
					elem.attr('tabindex', tabindex++);
				}				
			}	
		}
	}
	else
	{
		//Pestaña E,S,P
		for(var fl=1; fl<=diasMostrarFormulario; fl++)			
		{
			for(var cl=0; cl<maxUTMostrarPagina; cl++)
			{
				elem = $('#ut' + cl + '_col0_dia' + fl)[0];
				if(elem.disabled)
				{						
					$('#ut' + cl + '_col0_dia' + fl).attr('tabindex', -1);
					$('#ut' + cl + '_col1_dia' + fl).attr('tabindex', -1);
				}
				else
				{						
					$('#ut' + cl + '_col0_dia' + fl).attr('tabindex', tabindex++);
					$('#ut' + cl + '_col1_dia' + fl).attr('tabindex', tabindex++);
				}
			}
		}
		
		//Pestaña Habitaciones
		for(var fl=1; fl<=diasMostrarFormulario; fl++)
		{
			for(var tipo=0; tipo<4; tipo++)
			{
				elem = $('#hab_' + tipoCeldaHab[tipo] + '_dia' + fl);
				if(elem[0].disabled)
				{						
					elem.attr('tabindex', -1);
				}				
				else
				{						
					elem.attr('tabindex', tabindex++);
				}				
			}	
		}
		
	}

	//Personal y precios
	$('#pers_no_remunerado').attr('tabindex', tabindex++);
	$('#pers_fijo').attr('tabindex', tabindex++);
	$('#pers_eventual').attr('tabindex', tabindex++);

	for(key in tipo_cliente_adr)
	{
		$('#adr_' + tipo_cliente_adr[key]).attr('tabindex', tabindex++);
		elem = $('#num_habocup_' + tipo_cliente_adr[key])[0];
		if(elem.disabled)
			$('#num_habocup_' + tipo_cliente_adr[key]).attr('tabindex', -1);
		else
			$('#num_habocup_' + tipo_cliente_adr[key]).attr('tabindex', tabindex++);
		elem = $('#pct_habocup_' + tipo_cliente_adr[key])[0];
		if(elem.disabled)
			$('#pct_habocup_' + tipo_cliente_adr[key]).attr('tabindex', -1);
		else
			$('#pct_habocup_' + tipo_cliente_adr[key]).attr('tabindex', tabindex++);
	}

	$('#tar_med_habitac').attr('tabindex', tabindex++);
	$('#ing_hab_disp_mensual').attr('tabindex', tabindex++);
}

function CambiarModoIntroduccion(modo)
{
	modoIntroduccion = modo;
	comienzoFraseIntroduccion = "";
	if(modo=='ES')
	{
		$("#modoIntroduccion").html(comienzoFraseIntroduccion + "<strong>Entradas y salidas</strong> &#124; <a class='enlace' style='color:#30659A;' href=\"javascript:CambiarModoIntroduccion('EP')\" id=\"modoEP\">Entradas y pernoctaciones</a>");
		//Se comprueba que el input esté en la otra columna para cambiarlo
		if($('#pn_1').closest('td').attr('id')=='pn_col2_1')
		{
			for(var cl=0; cl<maxUTMostrarPagina; cl++)
			{
				$('#pn_col2_'+cl+'>input').appendTo( $('#pn_col3_'+cl) );
			}
		}
	}
	else
	{
		$("#modoIntroduccion").html(comienzoFraseIntroduccion + "<a class='enlace' style='color:#30659A;' href=\"javascript:CambiarModoIntroduccion('ES')\" id=\"modoES\">Entradas y salidas</a> &#124; <strong>Entradas y pernoctaciones</strong>");
		if($('#pn_1').closest('td').attr('id')=='pn_col3_1')
		{
			for(var cl=0; cl<maxUTMostrarPagina; cl++)
			{
				$('#pn_col3_'+cl+'>input').appendTo( $('#pn_col2_'+cl) );
			}                
		}
	}

	//Se actualizan los maxlength de entradas/salidas y pernoctaciones
	var maxlength=(modo=='ES' ? 5 : 6);
	for(var cl=0;cl<maxUTMostrarPagina;cl++)
	{
		for(var fl=1;fl<=diasMostrarFormulario;fl++)
		{
			$("#ut" + cl + "_col1_dia" + fl).attr("maxlength",maxlength);					
		}
	}
	CleanViewEPS();
	UpdateViewFromModelEPS(true);
}

// Prepara el formulario para su envío (grabar ó enviar)
function PrepararForm(esEnvio)
{
	// Preparación de parámetros
	if(totales.EPSLines !== undefined)
	{
		UpdateModelFromViewEPS();
		CalcularTotalesEPS();
		ValidateEPS();
	}
	
	UpdateModelFromViewHab();
	UpdateModelFromViewPersPrec();

	totales_POST = null;
	if(totales.EPSLines !== undefined)
	{
		//Se eliminan las líneas de totales antes de realizar el POST
		totales_POST = new UT();
		totales_POST.PresComMes = totales.PresComMes;
		totales_POST.EPSLines=new Array();
		for(var fl=0;fl<totales.EPSLines.length;fl++)
		{
			if(totales.EPSLines[fl].E!=0 || totales.EPSLines[fl].S!=0 || totales.EPSLines[fl].P!=0)
			{
				totales_POST.EPSLines.push(totales.EPSLines[fl]);	
			}
		}
	}

	var numDiasAbierto=-1;
	var dias_ab = $("#dias_abierto").val();
	if(dias_ab && !isNaN(dias_ab))
		numDiasAbierto=parseInt(dias_ab);
	
	// En caso de que se envíe el cuestionario a mitad de mes (cese de actividad), se debe verificar que el establecimiento quede vacío.
	if((esEnvio)&&(!mes_encuesta_finalizado))
	{
		// Comprobación inicial: No deben existir movimientos ni habitaciones ocupadas más allá del presente día.
		var d=new Date();
		var diaMes=d.getDate();
		
		var fErrores=false;
		var listaerrores='<ul class="lista_ventana">';
		
		// Primera comprobación: comprobamos que el número de dias abierto no sea superior al día actual.
		if((numDiasAbierto <= 0) || (numDiasAbierto > diaMes))
		{
			listaerrores = listaerrores + '<li style="text-align:left;">El número de días de actividad del establecimiento es incorrecto.</li>';
			fErrores=true;
		}
		
		// Segunda comprobación: el último movimiento debe generar 0 pernoctaciones.
		for(var fl=0; fl<totales_POST.EPSLines.length; fl++)
		{
			if((totales_POST.EPSLines[fl].Dia>=diaMes) && (totales_POST.EPSLines[fl].P!=0))
			{
				// Se ha violado la regla:
				listaerrores = listaerrores + '<li style="text-align:left;">Existen huéspedes pernoctando en el establecimiento en el momento del cese de actividad.</li>';
				fErrores=true;
				break;
			}		
		}
		
		// Tercera comprobación: el último día, el número de habitaciones ocupadas debe ser cero.
		for(var d=0; d<habitaciones.length; d++)
		{
			if((habitaciones[d].Dia>=diaMes) && (habitaciones[d].Sup>0 || habitaciones[d].Dob>0 || habitaciones[d].Ind>0 || habitaciones[d].Otr>0))
			{
				// Se ha violado la regla:
				listaerrores = listaerrores + '<li style="text-align:left;">Existen habitaciones ocupadas en el establecimiento en el momento del cese de actividad.</li>';
				fErrores=true;
				break;
			}
		}
		
		if(fErrores)
		{
			$("#msg_errores").html("<div class='cuadro fondo_amarillo'>"+
					"<p style='margin-top:15px;'>Existen errores en el cuestionario. Debe corregirlos para poder continuar con la operación:</p>" + listaerrores +
				"</ul></div>");
			
			$( "#dialog-detail" ).dialog({ title : 'Errores en el cuestionario', close: function(event,ui){busy(false);} });
			$( "#dialog-detail" ).dialog("open");
			return;
		}
	}
	
	var json_uts = JSON.stringify(utColumns);
	var json_totales_uts = JSON.stringify(totales_POST);
	var json_hab = JSON.stringify(habitaciones);
	var json_pers_precios = JSON.stringify(personalPrecios);

	var parametro = {
			var_json: {
				'mi' : modoIntroduccion,
				'mc' : modoCumplimentado,
				'mp' : modoIntroduccionADR,
				'uts': json_uts,
				'totales': json_totales_uts,
				'hab': json_hab,
				'pp' : json_pers_precios
			},
			op : (esEnvio ? "send":"save")
	};
	if(codMotivoExcesoPlazas!=null)
	{
		parametro.var_json.excesoPlazas=codMotivoExcesoPlazas;
		if(detalleMotivoExcesoPlazas!=null)
			parametro.var_json.excesoPlazasDetalle=encodeURIComponent(detalleMotivoExcesoPlazas);
	}
	if(codMotivoExcesoHabitaciones!=null)
	{
		parametro.var_json.excesoHabitaciones=codMotivoExcesoHabitaciones;
		if(detalleMotivoExcesoHabitaciones!=null)
			parametro.var_json.excesoHabitacionesDetalle=encodeURIComponent(detalleMotivoExcesoHabitaciones);
	}
	
	if (numDiasAbierto!=-1)
		parametro.var_json.da = numDiasAbierto;
	
	return parametro;
}

function GuardarForm_validado(funcionGuardarExito)
{
	try
	{
		busy(true);
		
		if(soloLectura)
		{
			funcionGuardarExito();
			return;			
		}
		
		// Preparación de parámetros
		var parametro=PrepararForm(false);

		// Evitamos posibles interferencias...
		watchdogGrabacionesIntermedias();
		
		// Petición guardar
		$.ajax({
			type: "POST",
			url: navPageURL, 
			data: parametro, 
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
							if (guar_ok)
								formModificado=false;
							VentanaErrores("Guardar", errs, true, guar_ok);
							return;
						}
					}
					formModificado=false;
					funcionGuardarExito();					
				}
				catch(err)
				{
					busy(false);
				}
			},
			error: function(xhr) {
				var txt="Ocurrió un error al realizar la petición: (" + xhr.status + ") " + xhr.statusText;
				if(xhr.status==403)
					txt+="\n\nATENCIÓN: se ha perdido la sesión. Los datos que introduzca no serán guardados.\nPulse el enlace Salir para autentificarse nuevamente.";
				alert (txt);
				busy(false);
			}
		}); 		    		
	}
	catch(err)
	{
		busy(false);
	}
	finally
	{
	}
}

function GuardarForm(funcionGuardarExito)
{
	try
	{
		busy(true);
		
		if (HayErrorValidacion(function(){GuardarForm_validado(funcionGuardarExito);}))
		{
			busy(false);
			return;
		}
	}
	catch(err)
	{
		busy(false);
	}
	finally
	{
	}
}

function enviarForm()
{
	try
	{
		busy(true);
		
		if(soloLectura)
		{
			return;			
		}
		
		// Preparación de parámetros
		var parametro=PrepararForm(true);

		// Evitamos posibles interferencias
		watchdogGrabacionesIntermedias();
		
		// Petición guardar
		$.ajax({
			type: "POST",
			url: navPageURL, 
			data: parametro,
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
						$("#ef").append('<input type="hidden" name="ceseActividad" value="1">');
						$("#ef").submit();
					});
					
					//$("#msg_errores").html(data);
					//$( "#dialog-detail" ).dialog("open");					
				}
				catch(err)
				{
					busy(false);
				}
			},
			error: function(xhr) {
				var txt="Ocurrió un error al realizar la petición: (" + xhr.status + ") " + xhr.statusText;
				if(xhr.status==403)
					txt+="\n\nATENCIÓN: se ha perdido la sesión. Los datos que introduzca no serán guardados.\nPulse el enlace Salir para autentificarse nuevamente.";
				alert (txt);
				busy(false);
			}
		}); 		    
	}
	catch(err)
	{
		busy(false);
	}
}

function ValidarForm()
{
	try
	{
		busy(true);
		
		if (HayErrorValidacion(enviarForm))
		{
			busy(false);
			return;
		}
	}
	catch(err)
	{
		busy(false);
	}
}

function ImprimirForm()
{
	if(formModificado)
	{
		busy(true);
		$("#msg_impresion").html("Existen modificaciones sin guardar. Para guardar los cambios pulse 'Guardar',<br/>de lo contrario la encuesta se imprimirá sin los últimos cambios.");
		$("#dialog-print").on( "dialogclose", function( event, ui ) {busy(false);} );
		$("#dialog-print").dialog("open");
	}
	else
	{
		window.open(printFormURL);
	}    	
}

function PreguntarCambiarModoADR(modo)
{
	var hayDatos = false;
	var msg = "";
	if (modoIntroduccionADR == 'P')
	{
		hayDatos = ($("input[id^='pct_habocup_']").filter(function( index) { return $(this).val(); }).length != 0);
		if (esHotel)
			msg = "Existen datos en porcentajes de habitaciones ocupadas.<br/><br/>Pulse 'Continuar' para cambiar el modo aunque se borren los datos actuales.<br><br>Pulse 'Cancelar' si desea conservar los datos y el modo actual.";
		else
			msg = "Existen datos en porcentajes de apartamentos ocupados.<br/><br/>Pulse 'Continuar' para cambiar el modo aunque se borren los datos actuales.<br><br>Pulse 'Cancelar' si desea conservar los datos y el modo actual.";
	}
	else 
	{
		hayDatos = ($("input[id^='num_habocup_']").filter(function( index) { return $(this).val(); }).length != 0);
		if (esHotel)
			msg = "Existen datos en número de habitaciones ocupadas.<br/><br/>Pulse 'Continuar' para cambiar el modo aunque se borren los datos actuales.<br><br>Pulse 'Cancelar' si desea conservar los datos y el modo actual.";
		else
			msg = "Existen datos en número de apartamentos ocupados.<br/><br/>Pulse 'Continuar' para cambiar el modo aunque se borren los datos actuales.<br><br>Pulse 'Cancelar' si desea conservar los datos y el modo actual.";
	}
	if(hayDatos)
	{
		busy(true);
		$("#msg_modoADR").html(msg);
		$("#dialog-modoADR").on( "dialogclose", function( event, ui ) {busy(false);} );
		$("#dialog-modoADR").dialog("open");
	}
	else
	{
		CambiarModoIntroduccionADR(modo);
	}    	
}

function loadUTs(num_pagina)
{
	try
	{
		if (num_pagina <= 0 || numPaginas < num_pagina)
		{
			return;
		} 

		// Realizar la petición
		var parametro = {
				pagina: num_pagina,
				op: "gp"
		};
		$.ajax({
			type: "POST",
			url: navPageURL, 
			data: parametro,
			success: function(data) 
			{
				try
				{
					var result = JSON.parse(data);
					utColumns = result.columnas;
					totales = result.totales;
					var ultimo_dia_cargado = new Array();
					
					for(var ut=0;ut<utColumns.length;ut++)
					{
						utColumns[ut].PresComMes = parseInt(utColumns[ut].PresComMes);
						var epslines = utColumns[ut].EPSLines;
						utColumns[ut].EPSLines = new Array();
						ultimo_dia_cargado[ut] = 1;
						
						for(var dia = 1; dia <= diasMostrarFormulario;dia++)
						{
							epsline = BuscarEPSLine(epslines, dia);
							if (epsline)
							{
								epsline.E = parseInt(epsline.E);
								epsline.P = parseInt(epsline.P);
								epsline.S = parseInt(epsline.S);
								utColumns[ut].EPSLines.push(epsline);
								ultimo_dia_cargado[ut] = dia;
							}
							else 
							{
								utColumns[ut].EPSLines.push(new EPS_Line(dia, null, null, null));
							}
						}
					}
					
					// Rehacer pernoctaciones en totales a partir de presentes comienzos mes.
					var totalesTemp = totales.EPSLines;
					totales.EPSLines=new Array();
					for(var dia = 1; dia <= diasMostrarFormulario;dia++)
					{
						epsline = BuscarEPSLine(totalesTemp, dia);
						if (epsline)
						{
							totales.EPSLines.push(epsline);						
						}
						else 
						{
							totales.EPSLines.push({ 
								Dia: "" + (totales.EPSLines.length+1) ,
								E : "0", 
								P : "0", 
								S : "0" 
								});
						}
					}
					
					var p = parseInt(totales.PresComMes);
					for(var i=0;i<totales.EPSLines.length;i++)
					{
						p = (p + parseInt(totales.EPSLines[i].E) - parseInt(totales.EPSLines[i].S));
						totales.EPSLines[i].P = "" + p;
					}
					
					for(var ut=0;ut<utColumns.length;ut++)
					{
						CalcularPernoctaciones(utColumns[ut], ultimo_dia_cargado[ut], 1);
					}
					
					CalcularSubtotalesEPS();
					
					ValidateEPS();
					pagActual = num_pagina;
					CleanViewEPS();
					UpdateViewFromModelEPS(false);
					RefrescarTabIndex();
					RefrescarPaginacion(num_pagina);
					if(!$('#pn_0')[0].disabled) $('#pn_0').focus().select();
					
					// Reiniciamos el timer de las copias intermedias
					watchdogGrabacionesIntermedias();
					
					busy(false);
				}
				catch(err)
				{
					busy(false);
				}
			},
			error: function(xhr) {
				var txt="Ocurrió un error al realizar la petición: (" + xhr.status + ") " + xhr.statusText;
				if(xhr.status==403)
					txt+="\n\nATENCIÓN: se ha perdido la sesión. Los datos que introduzca no serán guardados.\nPulse el enlace Salir para autentificarse nuevamente.";
				alert (txt);
				busy(false);
			}
		});			
	}
	catch(err)
	{
		busy(false);
	}
}

function CargarUTs(num_pagina)
{
	if (num_pagina <= 0 || numPaginas < num_pagina)
	{
		return;
	} 

	GuardarForm(function(){loadUTs(num_pagina);});
}

function RefrescarPaginacion(num_pagina)
{
	if(num_pagina==undefined) return;

	var pag_anterior = num_pagina-1;
	var pagActual = num_pagina;
	var pag_siguiente = num_pagina+1;

	//Se introduce en una lista de páginas cada una de las páginas
	var paginas = ",1,";
	if(pag_anterior>1) paginas = paginas + pag_anterior + ",";
	if(paginas.indexOf("," + pagActual + ",")==-1) paginas = paginas + pagActual + ",";
	if(pag_siguiente<numPaginas && paginas.indexOf("," + pag_siguiente + ",")==-1) paginas = paginas + pag_siguiente + ",";
	if(paginas.indexOf("," + numPaginas + ",")==-1) paginas = paginas + numPaginas + ",";
	arrPaginas = paginas.split(",");
	paginasHTML = "";
	//Añade enlace a página anterior (si no es la primera página)
	if(num_pagina!=1)
	{
		paginasHTML = paginasHTML + "<a class='enlace previous_page' style='color: #30659A;' href='javascript:CargarUTs(" + (num_pagina-1) + ")'>Anterior</a>";
	}
	for(i=1;i<arrPaginas.length-1;i++)
	{
		if((i>1) && arrPaginas[i]-1!=arrPaginas[i-1]) paginasHTML = paginasHTML + "<span class='pagination'>...</span>";
		if(arrPaginas[i]==num_pagina)
		{
			paginasHTML = paginasHTML + "<span class='pagination pag-current'>"+arrPaginas[i]+"</span>";
		}
		else
		{
			paginasHTML = paginasHTML + "<a class='pagination pag-page' href='javascript:CargarUTs(" + arrPaginas[i] + ")'>"+arrPaginas[i]+"</a>";
		}            		
	}
	//Añade enlace a página siguiente (si no es la última página)
	if(num_pagina!=numPaginas)
	{
		paginasHTML = paginasHTML + "<a class='enlace next_page' style='color: #30659A;' href='javascript:CargarUTs(" + (num_pagina+1) + ")'>Siguiente</a>";
	}
	$("#paginas").html(paginasHTML);
}

function PosicionarScrollByDia(nombreScroll,idCelda,altoCelda)
{
	pos_dia = (parseInt(idCelda.substring(idCelda.indexOf('dia')+3))-1)*altoCelda;
	topScroll = $("#"+nombreScroll).scrollTop();
	altoScroll = $("#"+nombreScroll).height();
	//Comprueba si se tiene que hacer scroll arriba o abajo
	if(pos_dia<topScroll)
	{
		$("#"+nombreScroll).scrollTop(pos_dia - altoCelda*3);
	}
	else
	{
		if(pos_dia+altoCelda>topScroll+altoScroll)
			$("#"+nombreScroll).scrollTop(topScroll + altoCelda*3);
	}														
}

var seguroSalida=true;

function chkSalida()
{
	if(confirm("Al abandonar esta página se perderán datos.\n¿Deseas permitir salir de esta página?"))
    	seguroSalida=false;
}

function ConfirmarSalida(event)
{
	if(checkChangesEnabled && formModificado)
	{
		if(seguroSalida)
		{
			window.setTimeout(function() {window.stop();chkSalida();}, 1);
		}
		/*
		checkChangesEnabled = false;
		setTimeout(function(){checkChangesEnabled = true;}, "100");
		return "Se perderán las modificaciones si abandona la página.";
		*/
	}
}

function LimpiarPais(pais)
{
	$("input[id^='ut"+pais+"_col0_']").val("");
	$("input[id^='ut"+pais+"_col1_']").val("");
	$("div[d^='ut" + pais + "_col2_']").html("-");
	
	UpdateModelFromViewEPS();
	if (utColumns[pais])
	{
		for(var fl=0; fl<diasMostrarFormulario; fl++)
			CalcularPernoctaciones(utColumns[pais],fl, 0);
	}
	CalcularTotalesEPS();
	ValidateEPS();
	
	CleanViewEPS();
	UpdateViewFromModelEPS(true);
}

function LimpiarHabitaciones(hab)
{
	$("input[id^='"+hab+"']").val("");
	
	UpdateModelFromViewHab();			
	CleanViewHab();
	UpdateViewFromModelHab();
	UpdateApartOcup();
}

function BackupForm()
{
	// Hay una operación en curso. No debemos interrumpir.
	if(fOcupado)
	{
		watchdogGrabacionesIntermedias();
		return;
	}
	
	// Preparamos los datos
	if(numPaginas>0)
	{
		UpdateModelFromViewEPS();
		CalcularTotalesEPS();
		ValidateEPS();		
	}
	
	UpdateModelFromViewHab();
	UpdateModelFromViewPersPrec();

	//Se eliminan las líneas de totales antes de realizar el POST
	totales_POST = new UT();
	totales_POST.PresComMes = totales.PresComMes;
	totales_POST.EPSLines=new Array();
	if(numPaginas>0)
	{
		for(var fl=0;fl<totales.EPSLines.length;fl++)
		{
			if(totales.EPSLines[fl].E!=0 || totales.EPSLines[fl].S!=0 || totales.EPSLines[fl].P!=0)
			{
				totales_POST.EPSLines.push(totales.EPSLines[fl]);	
			}
		}		
	}
	var json_uts = JSON.stringify(utColumns);
	var json_totales_uts = JSON.stringify(totales_POST);
	var json_hab = JSON.stringify(habitaciones);
	var json_pers_precios = JSON.stringify(personalPrecios);

	var dias_ab = $("#dias_abierto").val();

	var parametro = {
			var_json: {
				'mi' : modoIntroduccion,
				'mc' : modoCumplimentado,
				'mp' : modoIntroduccionADR,
				'uts': json_uts,
				'totales': json_totales_uts,
				'hab': json_hab,
				'pp' : json_pers_precios
			},
			op : "backup"
	};
	if (dias_ab && !isNaN(dias_ab))
		parametro.var_json.da = parseInt(dias_ab);

	// Petición guardar
	mostrarAvisoBackup(0);
	$.ajax({
		type: "POST",
		url: navPageURL, 
		data: parametro, 
		success: function(data) 
		{
			try {
				var resultado=JSON.parse(data);
				mostrarAvisoBackup((resultado==true) ? 1:2);
			} catch(e)
			{
			}
		},
		error: function(xhr) {
			var txt="Ocurrió un error al realizar la petición de guardado temporal: (" + xhr.status + ") " + xhr.statusText;
			if(xhr.status==403)
			{
				txt+="\n\nATENCIÓN: se ha perdido la sesión. Los datos que introduzca no serán guardados.\nPulse el enlace Salir para autentificarse nuevamente.";
				mostrarAvisoBackup(3,"Sesión perdida");
			}
			else
				mostrarAvisoBackup(3);
			alert (txt);
		}
	});
}

function mostrarAvisoBackup(tipo,msg)
{
	if(fShowBackup)
	{
		try{
			$(".avisobackup-label").hide();
			var aviso;
			if(tipo==0)
			{
				aviso=$(".avisobackup-label.avisobackup-initiated");
				if(msg===undefined)
					msg="Guardando...";
			}
			else if(tipo==1)
			{
				aviso=$(".avisobackup-label.avisobackup-ok");
				if(msg===undefined)
					msg="Guardado";
			}
			else if(tipo==2)
			{
				aviso=$(".avisobackup-label.avisobackup-error");
				if(msg===undefined)
					msg="Fallo";
			}
			else if(tipo==3)
			{
				aviso=$(".avisobackup-label.avisobackup-info");
				if(msg===undefined)
					msg="Fallo";
			}
			else return;
			
			aviso.text(msg);
			aviso.show();
			if(tipo!=3)
				aviso.fadeOut(avisobackup_fadeoutTimeMs);
		}
		catch(err)
		{
		}
	}
}

function watchdogGrabacionesIntermedias()
{
	if(timerGrabacionesIntermedias!=0)
	{
		clearInterval(timerGrabacionesIntermedias);
		timerGrabacionesIntermedias=0;
	}
	
	// Las grabaciones intermedias no están disponibles para el administrador ni cuando estamos en modo sólo lectura.
	if((es_admin) || (soloLectura))
		return;
	
	if(intervaloGrabacionesIntermedias==0)
		return;
	
	var intervaloMS=intervaloGrabacionesIntermedias * 1000;
	timerGrabacionesIntermedias=setInterval(BackupForm,intervaloMS);
}

function busy(flag)
{
	fOcupado=flag;
	if(fShowOcupado)
	{
		if(flag)
			$('#indicadorbusy').show();
		else
			$('#indicadorbusy').hide();
	}
}

$(document).ready( function() 
{
	fOcupado=false;
	
	//Se asocian las funciones a los diferentes eventos
	$("img[id^='btn_clean_pais']").click(function(){
		var pais = $(this).attr('id').slice('btn_clean_pais'.length);
		if(confirm("Se van a borrar todos los datos de Entradas y Salidas correspondientes al país '"+utColumns[pais].Title+"'.\n¿Desea continuar?"))
			LimpiarPais(pais);
	});

	$("img[id^='btn_clean_hab']").click(function(){
		var hab = $(this).attr('id').slice('btn_clean_'.length);
		if(confirm("Se van a borrar todos los datos de la columna '"+$(this).parent()[0].innerText+"'.\n¿Desea continuar?"))
			LimpiarHabitaciones(hab);
	});
	
	$("#btn_ant").click(function(){
		CargarUTs(pagActual-1);
	});
	
	$("#btn_sig").click(function(){
		CargarUTs(pagActual+1);
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
	
	$( "#dialog-exceso" ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		height: "auto",
		width: "auto",
		position : { my: "center", at: "center", of: window },
		buttons: {
			"Aceptar": function() {
				$( this ).dialog( "close" );
			},
			"Cancelar": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#dialog-print" ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		height: "auto",
		width: "auto",
		position : { my: "center", at: "center", of: window },
		buttons: {
			"Guardar": function() {
				GuardarForm(function() {window.open(printFormURL);busy(false);});
				$( this ).dialog( "close" );
			},
			"Cancelar": function() {
				$( this ).dialog( "close" );
				window.open(printFormURL);
			}
		}
	});
	
	$( "#dialog-modoADR" ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		height: "auto",
		width: "auto",
		position : { my: "center", at: "center", of: window },
		buttons: {
			"Continuar": function() {
				nuevoModo = (modoIntroduccionADR == 'P')? 'N': 'P';
				CambiarModoIntroduccionADR(nuevoModo);
				$( this ).dialog( "close" );
			},
			"Cancelar": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$("input[id^='ut'],input[id^='pn_']").change(function()
	{
		UpdateModelFromViewEPS();
		var idcelda = $(this).attr('id');
		var col_ut = 0;
		if(idcelda[0] == 'u')
		{
			col_ut = parseInt(idcelda.substring(2,idcelda.indexOf('_')));
			dia = parseInt(idcelda.substring(idcelda.indexOf("dia")+3));
			tipo_celda = parseInt(idcelda.substring(idcelda.indexOf("col")+3, idcelda.indexOf('_dia')));
		}
		else
		{
			col_ut = parseInt(idcelda.substring(3));
			dia = 0;
			tipo_celda = 1;
		}
		
		if (utColumns[col_ut])
		{
			CalcularPernoctaciones(utColumns[col_ut],dia, tipo_celda);
		}
		CalcularTotalesEPS();
		ValidateEPS();
		
		CleanViewEPS();
		UpdateViewFromModelEPS(true);
	});
	
	$("input[id^='pn_']").focus(function()
	{
		$(this).select();
	});
	
	$("input[id^='ut']").focus(function()
	{
		PosicionarScrollByDia("scroll_uts",this.id,25);
		$(this).select();
	});
	
	$("input[id^='hab_']").blur(function()
	{
		UpdateModelFromViewHab();			
		CleanViewHab();
		UpdateViewFromModelHab();
		UpdateApartOcup();
	});
	
	$("input[id^='hab_']").focus(function()
	{
		PosicionarScrollByDia("scroll_habitac",this.id,25);
		$(this).select();
	});
	
	$("input[id^='pers_'],input[id^='ing_hab_'],input[id^='tar_med_']").blur(function()
	{
		UpdateModelFromViewPersPrec();
	});
	
	$("input[id^='pers_'],input[id^='ing_hab_'],input[id^='tar_med_'],input[id^='adr_'],input[id^='num_habocup_'],input[id^='pct_habocup_']").focus(function()
	{
		$(this).select();
	});
	
	$("input[id^='adr_'],input[id^='num_habocup_'],input[id^='pct_habocup_'],input[id^='pers_'],#ing_hab_disp_mensual,#tar_med_habitac").blur(function()
	{
		UpdateModelFromViewPersPrec();
		CleanViewPersPrec();
		UpdateViewFromModelPersPrec();
	});
	
	$("#dias_abierto").blur(function()
	{
		UpdateOcupPlazas();
		UpdateApartOcup();
	});
	
	$("input").change(function()
	{
		formModificado=true;
	});
	
	$(window).resize(function() 
	{
		$("#scroll_uts").height(Math.min(1 + diasMostrarFormulario * 25 , $(window).height()-544));
		$("#scroll_habitac").height(Math.min(1 + diasMostrarFormulario * 25, $(window).height()-422));
		$("#pers_precios").height($(window).height()-344);
	});
	
	$("img[id^='imgerror_ut']").click(function (e) {
		e.preventDefault();
		MostrarErrores_EPS(this.id);
	});
	$("img[id^='imgerror_hab']").click(function (e) {
		e.preventDefault();
		MostrarErrores_Hab(this.id);
	});
	$("img[id^='imgerror_adr'],img[id^='imgerror_pers'],img[id^='imgerror_prec']").click(function (e) {
		e.preventDefault();
		MostrarErrores_PP(this.id);
	});
	
	//Se realiza la inicialización de la página
	$("input[name='enviarBtn']").button().click(ValidarForm);
	
	$("input[name='guardarBtn']").button().click(function() {
		GuardarForm(function() {
			busy(true);
			$("#dialog-detail").dialog('option', 'title', 'Datos almacenados');
			$("#dialog-detail").on( "dialogclose", function( event, ui ) {busy(false);} );
			$("#msg_errores").html("<div class='cuadro fondo_verde'>"+
					"<p class='okicon' style='margin-top:15px;'>Su cuestionario de alojamiento ha sido guardado.</p>" +
				"</div>");
			
			$( "#dialog-detail" ).dialog("open");
		});
	});
	
	$("input[name='imprimirBtn']").button().click(ImprimirForm);
	$( "#tabs" ).tabs({ active: tab_inicial });

	if(!mes_encuesta_finalizado)
	{
		$( "#dialog-aviso" ).dialog({
			autoOpen: false,
			resizable: false,
			title : 'Aviso',
			modal: true,
			height: "auto",
			width: "auto",
			position : { my: "center", at: "center", of: window },
			buttons: {
				"Confirmar": function() {
					$('input[name="enviarBtn"]').button("enable");
					$( this ).dialog( "close" );
				},
				"Cancelar": function() {
					document.getElementById('ceseActividad').checked=false;
					$( this ).dialog( "close" );
				}
			}
		});
		
		$('#ceseActividad').change(function(){
			if($(this).is(':checked'))
			{
				$("#msg_avisos").html('<div class="cuadro fondo_amarillo">'+
									'<h3 class="titulo_3 avisoicon">Atención</h3>'+
									'<p>Se ha habilitado el envío del cuestionario antes de finalizar el mes de la encuesta por cierre de actividad del establecimiento.<br>'+
									'Por favor, no active esta casilla en ningún otro caso.</p>'+
									'<p><strong>Recuerde</strong> ajustar el número de días que ha estado abierto el establecimiento</p>'+
									'<img src="images/diasAbierto.png" class="icono_tab"/>'+
									'</div>');
	
				$( "#dialog-aviso" ).dialog("open");
			}
			else
				$('input[name="enviarBtn"]').button("disable");
		});
		
		$('#ceseActividad').attr('checked',false);
		$('input[name="enviarBtn"]').button("disable");
	}
	
	// Con esto nos aseguramos de que si los datos son cargados con errores, se muestren los avisos de validación sin necesidad de pasar el foco por ellos.
	try
	{
		// Cuando no hay datos iniciales, las validaciones pueden fallar de forma inesperada.
		if(habitaciones!=null)
			ValidateHab();
		ValidatePersPrec();
	}
	catch(err)
	{
	}
	
	UpdateOcupPlazas();
	UpdateViewFromModelHab();
	UpdateApartOcup();
	UpdateViewFromModelPersPrec();
	
	loadUTs(1);
	CambiarModoCumplimentado(modoCumplimentado);
	CambiarModoIntroduccion(modoIntroduccion);
	CambiarModoIntroduccionADR(modoIntroduccionADR);
	//Se inicializa el paginador
	RefrescarPaginacion();
	$("#scroll_uts").height(Math.min(1+ diasMostrarFormulario * 25 , $(window).height()-544));
	$("#scroll_habitac").height(Math.min(1 + diasMostrarFormulario * 25, $(window).height()-422));
	$("#pers_precios").height($(window).height()-344);
	//Deshabilita todos los input al estar en sólo lectura
	if(soloLectura)
	{
		$(':text').attr('disabled',true);	
	}
	
	window.onbeforeunload = ConfirmarSalida;
	
	// Lanzamos timer de grabaciones intermedias.
	$(".avisobackup-label").hide();
	$('#indicadorbusy').hide();
	watchdogGrabacionesIntermedias();
	if((es_admin) || (soloLectura) || (intervaloGrabacionesIntermedias==0))
		fShowOcupado=false;
	
});