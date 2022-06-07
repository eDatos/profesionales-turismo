<style>
#msg_avisos p {
	text-align: justify;
	line-height: 1.6;
	padding: 0 20px;
}

#msg_avisos h3 {
	margin-bottom: 4px;
}
</style>
<script type="text/javascript">
function formatBytes(a,b){if(0==a)return"0 Bytes";var c=1024,d=b||2,e=["Bytes","KB","MB","GB","TB","PB","EB","ZB","YB"],f=Math.floor(Math.log(a)/Math.log(c));return parseFloat((a/Math.pow(c,f)).toFixed(d))+" "+e[f];}

var mes_encuesta_finalizado = <?= $mes_encuesta_finalizado ? "true":"false" ?>;

var fAnularFichero=false;

function checkExtension(fichero)
{
	if(fichero.toLowerCase().endsWith('.xml')==false)
	{
		fAnularFichero=true;
		$("#msg_errores").html('<div class="cuadro fondo_amarillo">'+
				'<h3 class="titulo_3 avisoicon">Atención</h3>'+
				'<p>La extensión del fichero debe ser ".XML".</p></div>');

		$("#dialog-error").dialog("open");
		return false;
	}
	return true;
}

$(document).ready( function() {
	$("input[type='submit']").button();

	$( "#dialog-error" ).dialog({
		autoOpen: false,
		resizable: false,
		title : 'Error',
		modal: true,
		height: "auto",
		width: "auto",
		position : { my: "center", at: "center", of: window },
		buttons: {
    		"Aceptar": function() {
        		if(fAnularFichero)
        			$("input[name='userfile']")[0].value="";
    			$( this ).dialog( "close" );
    		}
    	}
	});
	
<?php if((defined('ALOJA_MAX_FILE_SIZE')) && (ALOJA_MAX_FILE_SIZE!=0)): ?>
	$( "#userfile-label").text("La extensión del fichero debe ser xml. Este proceso puede tardar varios minutos dependiendo del tamaño del fichero y el tipo de conexión a Internet utilizada.\nNOTA: El tamaño máximo del fichero es de "+formatBytes(<?= ALOJA_MAX_FILE_SIZE ?>)+" aprox.");
    $( "#dialog-aviso-filesize" ).dialog({
    	autoOpen: false,
    	resizable: false,
    	title : 'Aviso',
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
	$('input[name="userfile"]').bind('change', function() {
    	if(checkExtension(this.value)==false)
        	return;
		var limite=<?= ALOJA_MAX_FILE_SIZE ?>;
		if(this.files[0].size>=limite)
		{
			var slimite=formatBytes(limite);
			$("#msg_avisos_filesize").html('<div class="cuadro fondo_amarillo">'+
					'<h3 class="titulo_3 avisoicon">Atención</h3>'+
					'<p>El tamaño del fichero seleccionado sobrepasa el máximo admitido ('+slimite+' aprox.).<br>'+
					'Es posible que la operación falle.</p></div>');

			$("#dialog-aviso-filesize").dialog("open");
		}
	});
<?php else: ?>
    $('input[name="userfile"]').bind('change', function() {
    	checkExtension(this.value);
    });
<?php endif; ?>

	hay_datos = <?= (isset($hay_datos) && ($hay_datos)) ? "true":"false"?>;
	if (hay_datos)
	{
		$("input[type='submit']").click(function()
		{
			var entrar = confirm("Ya existen datos para el cuestionario de la encuesta actual.\n Si continúa dichos datos se sobreescribirán. ¿Desea continuar?");
			return entrar;	
		});
	}


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
									'Por favor, no active esta casilla en ningún otro caso.</p></div>');
	
				$( "#dialog-aviso" ).dialog("open");
			}
			else
				$('input[name="enviarBtn"]').button("disable");
		});
		
		$('#ceseActividad').attr('checked',false);
		/* CODVID-19: 
		$('input[name="enviarBtn"]').button("disable");
		*/
	}

	$('input[name="enviarBtn"]').button().click(
			function() {
				//e.preventDefault();
				var nv = $("input[name='userfile']")[0];
				
				if (nv.files.length == 0)
				{
        			$("#msg_errores").html('<div class="cuadro fondo_amarillo">'+
        					'<h3 class="titulo_3 avisoicon">Atención</h3>'+
        					'<p>Debe seleccionar un fichero XML para ser enviado.</p></div>');
        
        			$("#dialog-error").dialog("open");
					                
	            	return false;
				}
		    	if(checkExtension(nv.value)==false)
		        	return false;
			}
		);
  });
</script>
<?php if(isset($res)): ?>
<div id="bloq_interior">
	<h1 class="titulo_1 noprint" >Envío de archivo de datos</h1>
	<div class="bloq_central">
		<?php if ($res === true) : ?>
		<div class="cuadro fondo_verde noprint" style="text-align: justify;">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Envío de archivo de datos</h3>
			<div class="subrayado"></div>
			<p>El archivo de datos ha sido subido.</p>
			<p><b>Muchas gracias por su colaboración.</b></p>
		</div>
        <div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
		<?php else: ?>
		<div class="cuadro" style="text-align: justify; background-color:#FF9393;border-color:#F37575" class="noprint">
			<h3 class="titulo_3 erroricon" style="margin-bottom:4px;">Envío de archivo de datos</h3>
			<p>No ha sido posible subir el archivo de datos debido a un error.</p>
        </div>		
        <?php endif; ?>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->
<?php else : ?>
	<div id="bloq_interior">
		<h1 class="titulo_1" style="float:left;">Envío de archivo de datos</h1>
		<a id="ayuda_xml" href="javascript:MostrarAyuda('ayuda_xml','AYUDA07<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon enlace" style="float:right;margin-top:9px;background-position-y: 2px;" title="Ayuda (tecla de acceso: y)" accesskey="y"><strong>Ayuda</strong></a>	
		<div style="clear:both;"></div>
		<div class="bloq_central">
			<?php if (isset($hay_datos) && $hay_datos) :?>
			<div class="cuadro fondo_amarillo" style="text-align: justify;" class="noprint">
				<h3 class="titulo_3 avisoicon" style="margin-bottom:4px;">Aviso</h3>
				<div class="subrayado"></div>
				<p>Ya existen datos para el cuestionario de la encuesta actual. <b>Si continúa, dichos datos se sobrescribirán</b>.</p>
			</div>
			<?php elseif (!isset($hay_datos)) :?>
			<div class="cuadro fondo_amarillo" style="text-align: justify;" class="noprint">
				<h3 class="titulo_3 avisoicon" style="margin-bottom:4px;">Aviso</h3>
				<div class="subrayado"></div>
				<p>Podría haber datos para el cuestionario. <b>Si continúa, dichos datos se sobrescribirán</b>.</p>
			</div>		
	        <?php endif; ?>
			<div class="cuadro fondo_gris">
			  <h2 class="titulo_2">Archivo de datos para la encuesta de alojamiento actual: <?= DateHelper::mes_tostring( $mes_trabajo,'M')?> de <?=$ano_trabajo?></h2>
		      <div class="subrayado"></div>
		      <p id="userfile-label">La extensión del fichero debe ser xml. Este proceso puede tardar varios minutos dependiendo del tamaño del fichero y el tipo de conexión a Internet utilizada.</p>
				<form name="sfx" action="#" method="post" enctype="multipart/form-data" >    
				<div class="fileinputs"><input name="userfile" type="file" title="Introduzca la ruta al fichero que contiene los datos de la Encuesta de Alojamiento.">        
	            </div>
	            <input type="hidden" name="<?= ARG_OP ?>" value="<?= OP_SUBIR_XML ?>"/>
	            <input type="hidden" name="mes_sel" value="<?= $mes_trabajo ?>"/>
	            <input type="hidden" name="ano_sel" value="<?= $ano_trabajo ?>"/>
	            <input style="margin-top:20px;" name="enviarBtn"
					role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="Enviar archivo"/>
				<?php if(!$mes_encuesta_finalizado):?>
					<input class="" style="margin-left: 40px" id="ceseActividad" name="ceseActividad" type="checkbox" value="1">Permitir el envío del cuestionario antes de plazo por cese de actividad.</input><span id="ayuda_ca" onclick='MostrarAyuda("ayuda_ca","AYUDA30");' class="ayudaicon2" title="Ayuda (tecla de acceso: a)" accesskey="a">&nbsp;</span><br/>
				<?php endif ?>
	            </form>                   
			</div>        
		</div>
	</div>
<?php endif; ?>
<?php if(!$mes_encuesta_finalizado):?>
	<div id="dialog-aviso" title="Aviso" >
		<div id="msg_avisos">
		</div>
	</div>
<?php endif ?>
<?php if((defined('ALOJA_MAX_FILE_SIZE')) && (ALOJA_MAX_FILE_SIZE!=0)): ?>
	<div id="dialog-aviso-filesize" title="Aviso" >
		<div id="msg_avisos_filesize">
		</div>
	</div>
<?php endif; ?>
	<div id="dialog-error" title="Error" >
		<div id="msg_errores">
		</div>
	</div>
