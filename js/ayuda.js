		function VentanaFlotante(elementoReferencia, json_ayuda)
		{
			var posIcono = $("#" + elementoReferencia).offset();
			
			var div="<div id='ayuda' class='cuadro' style='z-index:3000;position:absolute;text-align:left;width:" + json_ayuda.ancho_popup + "px;height:" + json_ayuda.alto_popup + "px;border: 1px solid #ACACAC;background-color:white;border-radius: 5px;padding-bottom:0px;'>";
			div+="<div style='float:left;width:90%;'><strong>" + json_ayuda.titulo + "</strong></div><div class='CerrarAyuda' style='float:right;margin-top:5px;margin-right:7px;cursor:pointer;'><img class='CerrarAyuda' src='img/cerrar.gif' style='height:8px;'></div>";
			div+="<div style='clear:both;padding-bottom:2px;' class='subrayado'> </div>";
			div+="<div id='contenido_ayuda'></div>";
			div+="</div>";

			$("#ayuda").remove();
			$('body').append(div);
			$('#contenido_ayuda').html($("<div/>").html(json_ayuda.contenido_ayuda).text());
			$("#ayuda").offset({left: posIcono.left + parseInt(json_ayuda.posX_popup), top: posIcono.top + parseInt(json_ayuda.posY_popup)});
			$('body').click(function(e) {
				if((e.target.id!== undefined 
						&& e.target.className!="CerrarAyuda") 
						&& (e.target.id=='ayuda' || (e.target.parentElement!== undefined 
						&& ($(e.target.parentElement).closest("#ayuda" )[0]!==undefined 
						&& $(e.target.parentElement).closest("#ayuda")[0].id=='ayuda')))) return;
				$(this).unbind('click');
				$("#ayuda").remove();
			});
		}

		function VentanaDialogo(json_ayuda)
		{
			$( "#dialogo_ayuda" ).dialog( "destroy" );
        	$( "#dialogo_ayuda" ).remove();
			$('body').append("<div id='dialogo_ayuda'></div>");
    		$( "#dialogo_ayuda" ).html($("<div/>").html(json_ayuda.contenido_ayuda).text()).dialog({
    	    	autoOpen: true,
    	        resizable: false,
    	        modal: true,
    	        height: parseInt(json_ayuda.alto_popup),
    	        width: parseInt(json_ayuda.ancho_popup),
    	        title: json_ayuda.titulo,
    	        position : { my: "center", at: "center", of: window },
    	    	/*buttons: {
    	            "Aceptar": function() {
    	            	$( this ).dialog( "close" ).remove();
    	            }
    	        },*/
    	        close: function(event, ui) {
    	        	$( "#dialogo_ayuda" ).dialog( "destroy" );
    	        	$( "#dialogo_ayuda" ).remove();
            	}
            });			
		}
		
		function ContenidoHtmlEstatico(json_ayuda)
		{
			var htmlCod="";
			htmlCod+="<div class='cuadro fondo_celeste'>\n";
			htmlCod+="<h2 class='titulo_2' style='float: left;'>"+json_ayuda.titulo+"</h2>\n";
			htmlCod+="<div class='subrayado'></div>\n";
			htmlCod+=json_ayuda.contenido_ayuda;
			htmlCod+="\n</div>";
			return htmlCod;
		}

		
		function AbrirAyuda(data, eltoreferencia)
		{
			switch(data.tipo)
			{
				case "0": //Ventana de diálogo
					VentanaDialogo(data);
					break;
				case "1": //Ventana flotante
					VentanaFlotante(eltoreferencia, data);
					break;
				case "2": //Enlace externo
					if(data.url_enlace_externo.substring(data.url_enlace_externo.length-4).toLowerCase()=='.pdf')
						window.open("pdfshow.php?src=" + data.url_enlace_externo);
					else
						window.open(data.url_enlace_externo);
					break;
				case "3": // Contenido html estático
					if(eltoreferencia!=null)
						$('#'+eltoreferencia).html(ContenidoHtmlEstatico(data));
					else
						document.write(ContenidoHtmlEstatico(data));
					break;
			} 
		}
		
		function MostrarAyuda(elementoReferencia, codigo)
		{
        	var parametro = {
		            cod_enlace: codigo
		    };
		
			$.ajax({
				type: "POST",
				url: ayuda_url,  // Global.
				data: parametro, 
				success: function(data) 
				{
					var json_ayuda = jQuery.parseJSON(data);
					if (json_ayuda)
					{
						AbrirAyuda(json_ayuda, elementoReferencia); 
					}
					
				},
			    fail: function(xhr) {
			        alert ("Ocurrió un error al mostrar el elemento de ayuda: " + xhr.statusText);
			    }
				});
		}