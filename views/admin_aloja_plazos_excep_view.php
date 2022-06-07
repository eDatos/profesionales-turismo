<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/inputfields.js"></script>
<style>
.botoneraderecha {
	float: right;
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
.tablaresultado tr:nth-child(4n+1) {
	background-color: #FBFBFB;
}
.tablaresultado tr:nth-child(4n+2) {
	background-color: #F2F2F2;
}
.notas_plazo {
    display:block;
    margin-right: 2px;
    padding: 0 5px 0 5px;
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
$a = date('Y') + 1; /// +1 porque pueden haber cuestionarios referentes al proximo trimestre y puede ser el proximo año.
for ($i = 5; $i >= 0; $i--)
{
    $anios[$i] = $a;
    $a -= 1;
}

define("ESTILO_OK", "okicon");
define("ESTILO_ERROR", "erroricon");

?>
<script type="text/javascript">
var navPageURL="<?= PAGE_ALOJA_PLAZOS_EXCEP_AJAX ?>";
var detalles=false;
function toggleDetalles()
{
	document.getElementById('detalleErrores').style.display=(detalles)?'none':'block';
	detalles=!detalles;
}
$(document).ready( function() {
	$('#ampliar').click(function(event){
		toggleDetalles();
	});
	<?php if (count($data) > 0) : ?>
	$("input[type='submit'][value='Eliminar todos']").button().click( function(e) {
		//e.preventDefault();
		var form = $(this).parents('form:first');
		var mes_sel=$('[name="mes_sel"]',form).val();
		var ano_sel=$('[name="ano_sel"]',form).val();
		var estids="";
		var formularios=document.getElementById("tabla_resultados").getElementsByTagName("FORM");
		for(var i=0;i<formularios.length;i++)
		{
			if((formularios[i]["ano_sel"].value==ano_sel)&&(formularios[i]["mes_sel"].value==mes_sel))
				estids+=formularios[i]["estid"].value+",";
		}
		if(estids=="")
		{
			alert("No hay plazos a eliminar coincidentes con los criterios de búsqueda.");
			return false;
		}
		estids=estids.slice(0,-1);
		if(confirm("Atención\n\n Se dispone a eliminar todos los plazos listados del mes/año seleccionado.\n\t¿Desea continuar?")==false)
			return false;
		$('[name="<?= ARG_ESTIDS ?>"]',form).val(estids);
	});
	function mostrarAvisoNotasGuardadas()
	{
		$( "#dialog-notas-aviso" ).dialog(
				{
					resizable: false,
					modal: true,
					width: 300,
					title : 'Notas del plazo excepcional',
					position : { my: "center", at: "center", of: window },
					buttons: {
						"Aceptar": function() {
							$( this ).dialog( "close" );
						}
					},
					close: function(event, ui) {
						$( this ).dialog( "destroy" );
						$("#msg_notas-aviso").html("");
					}
				}
			);
		$("#msg_notas-aviso").html("Notas grabadas correctamente.");
		$( "#dialog-notas-aviso" ).dialog("open");
	}
	
	function entradaNotas(notas,funcionAceptar)
	{
		var codigo="<div class='cuadro fondo_amarillo'>"+
		"<p style='margin-top:15px;'>Introduzca los comentarios correspondientes al plazo extraordinario:</p>"+
		"<table style=\"margin-left:10px;width:99%\">"+
		"<tr><td style=\"width:11%; vertical-align: top\"><label for=\"notas_det\">Notas:</label></td><td><textarea id=\"notas_det\" name=\"notas_det\" style=\"width:90%; height:130px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em;\" placeholder=\"Introduzca aquí el texto deseado...\"></textarea></td></tr>"+
		"</table></div>";
		$("#msg_notas").html(codigo);
		$("textarea[id='notas_det']").val(notas);
		$( "#dialog-notas" ).dialog(
				{
					resizable: false,
					modal: true,
					width: 800,
					title : 'Notas del plazo excepcional',
					position : { my: "center", at: "center", of: window },
					buttons: {
						"Aceptar": function() {
							var texto_notas=$("#notas_det").val();
							funcionAceptar(texto_notas);
							$( this ).dialog( "close" );
						},
						"Cancelar": function() {
							$( this ).dialog( "close" );
						}
					},
					close: function(event, ui) {
						$( this ).dialog( "destroy" );
						$("#msg_notas").html("");
					}
				}
			);
		$( "#dialog-notas" ).dialog("open");
	}
	function guardarNotas(est,mes,ano,notas,funcion_exito)
	{
		try
		{
			var parametro={
					'op': 'save',
					'estid': est,
					'mes_sel': mes,
					'ano_sel': ano,
					'notas': notas
			};
			$.ajax({
				type: "POST",
				url: navPageURL,
				data: parametro,
				success: function(data)
				{
					try
					{
						var resultado = JSON.parse(data);
						if(resultado.error==false)
						{
    						if(funcion_exito!=null)
    							funcion_exito();
						}
					}
					catch(err)
					{
					}
					finally
					{
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
		}
		finally
		{
		}
	}
	$("input[type='submit'][value='Notas']").button().click( function(e) {
		var form = $(this).parents('form:first');
		var mes=$('[name="mes_sel"]',form).val();
		var ano=$('[name="ano_sel"]',form).val();
		var est=$('[name="estid"]',form).val();

		var nextFila=$(this).closest('tr').next('tr');		
		var campo_notas=$('[id="notas"]',nextFila);
		var notas_old=campo_notas.text();

		entradaNotas(notas_old,
				function(notas_new)
				{
					guardarNotas(est,mes,ano,notas_new,
            				function()
            				{
            					campo_notas.text(notas_new);
            					//alert("Ok!");
            					mostrarAvisoNotasGuardadas();
            				}
						);
				}
		);
		return false;
	});
	
	var fShowNotas=true;
	$("input[type='submit'][name='MostrarNotas']").button().click( function(e) {
		if(fShowNotas)
		{
			$(".fila_notas").hide();
			$(this).val("+");
			$(this).attr('title','Mostrar notas');
		}
		else
		{
			$(".fila_notas").show();
			$(this).val("-");
			$(this).attr('title','Ocultar notas');
		}
		fShowNotas=!fShowNotas;
		return false;
	});
	
	<?php endif; ?>
});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1"><?= USER_TITLE ?></h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
			<?php
			// Se comprueba si viene de alguna operación previa para mostrar el resultado 
	
			if($operacion!='')
			{		
				switch($operacion) 
				{
					case OP_INSERTAR:
					case OP_MODIFICAR:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al guardar el plazo";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Plazo guardado";
						}
						break;
					case OP_ELIMINAR:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al eliminar el plazo";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Plazo eliminado";
						}
						break;
					case OP_ELIMINAR_TODOS:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al eliminar el(los) plazo(s)";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Plazo(s) eliminado(s)";
						}
						break;
				}
				echo '<div class="'.(($error)?'pagemsg_error':'pagemsg_success').'"><span id="infomsg" class="titulo_3 ' . $estilo_msg . '">' . $mensaje . '</span>';
				if($error)
					echo '<img id="ampliar" class="botoneraderecha alineado" src="images/detalles.png"/>';
				if(!empty($listaErrores))
				{
					echo '<div id="detalleErrores" style="display:none"><ul>';
					foreach ($listaErrores as $linea)
						echo '<li>'.$linea.'</li>';
					echo '</ul></div>';
				}
				echo '</div>';
			}
		?>
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Nuevo plazo excepcional</h2>
	      <div class="subrayado"></div>
	        <form name="df" action="<?= $navToUrl ?>" method="post">
		    <table style="margin-left:10px;width:99%">
		    	<tr>
				<td style="width:20%;"><label for="estid">Códigos de establecimientos:</label></td>
				<td><input type="text" style="width:180px;margin-left:2px;" name="estid"/></td>
				</tr>
				<tr>
				<td style="width:20%;"><label for="mes_sel">Mes:</label></td>
				<td><select name="mes_sel">
               <?php $mes_enc_curso=(date("n")==1 ? 12 : date("n")-1) ?>				
                <?php for ($i = 1; $i <= 12; $i++): ?>
                     <?php $selected = ($i == $mes_enc_curso ? ' selected="selected"' : '') ?>
                     <option value="<?= $i ?>" <?= $selected?>><?= $trims[$i] ?></option>                     
                <?php endfor; ?>
                </select></td>
				</tr>
				<tr>
				<td style="width:20%;"><label for="ano_sel">Año:</label></td>
				<td><select name="ano_sel">
                <?php $ano_enc_curso=($mes_enc_curso==12 ? date("Y")-1 : date("Y")) ?>												
                <?php foreach($anios as $ano): ?>
                     <?php $selected = ($ano == $ano_enc_curso ? ' selected="selected"' : '') ?>                
                     <option value="<?= $ano ?>" <?= $selected?>><?= $ano ?></option>
                 <?php endforeach; ?>
                </select></td>
				</tr>
				<tr>
					<td style="width:20%;"><label for="dia_mes">Día mes siguiente:</label></td>
					<td><input type="text" class="numero sindecimales digits" maxlength="2" style="margin-left:2px;width:40px;text-align:right;" name="dia_mes"/></td>
				</tr>
				<tr>
					<td style="width:20%;"><label for="notas">Notas:</label></td>
					<td><input type="text" maxlength="200" style="margin-left:2px;width:600px;" name="notas"/></td>
				</tr>
            </table>
            <div>
            <input name="<?= ARG_ESTIDS ?>" type="hidden" value="">
			<input name="operationBtn" style="padding:0.2em 0.5em; margin-top:10px;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="Insertar nuevo plazo"/>
			<?php if (count($data) > 0) : ?>
			<input name="operationBtn" style="padding:0.2em 0.5em; margin-top:10px;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only botoneraderecha" aria-disabled="false" type="submit" value="Eliminar todos"/>
			<?php endif; ?>
            </div>
            </form>                   
		</div>
		<!-- FIN CAJA AMARILLA --> 
        <!-- COMIENZO BLOQUE RESULTADOS DE LA BUSQUEDA -->
		<div id="resultado">
			<div>
		    <h2 style="display: inline-block" class="titulo_2">Plazos excepcionales por establecimiento</h2>
		    <?php if (count($data) > 0) : ?>
		    <div class="botoneraderecha">
		    	<input name="MostrarNotas" style="padding:0.2em 0.5em;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" title="Ocultar notas" value="-"/>
		    </div>
		    <?php endif; ?>
		    </div>
        <div class="subrayado"></div>
		<?php if (count($data) > 0) : ?>
                  <!-- tabla de resultados -->
                  <div id="tabla_resultados">
                        <table class="tablaresultado" width="100%">
                          <tr>
                            <th scope="col">CODIGO</th>
                            <th scope="col">ESTABLECIMIENTO</th>
                            <th scope="col">MES</th>
                            <th scope="col">AÑO</th>
                            <th scope="col">DÍA MES SIGUIENTE</th>
                          </tr>
                          <?php foreach ($data as $row): ?>
                              <tr>
                              	<td><?= $row['id_est'] ?></td>
                                <td style="width:45%;"><a href="<?= $this->build_url( $navToUrl, array(ARG_ESTID => $row['id_est']) ); ?>"><?= $row['nombre_est'] ?></a></td>
                                <td><?= DateHelper::mes_tostring($row['mes'], 'M') ?></td>
                                <td><?= $row['ano'] ?></td>
                                <td style="width:400px;">
                                <form name="df" action="<?= $navToUrl ?>" method="post">
                                <input name="estid" type="hidden" value="<?= $row['id_est']; ?>">
                                <input name="mes_sel" type="hidden" value="<?= $row['mes']; ?>">
                                <input name="ano_sel" type="hidden" value="<?= $row['ano'] ?>">
                                <input name="dia_mes" type="text" class="numero sindecimales digits" maxlength="2" style="width:40px;text-align:right;" value="<?= $row['dia_mes_sig'] ?>"/>
                                <div class="botoneraderecha">
                                <input name="operationBtn" style="padding:0.2em 0.5em;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="Modificar"/>
                                <input name="operationBtn" style="padding:0.2em 0.5em;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="Eliminar"/>
                                <input name="NotasBtn" style="padding:0.2em 0.5em;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="Notas"/>
								</div>
								</form>
            				    </td>
            				   </tr>
            				   <tr class="fila_notas">
                                <td colspan="5">
                                <span id="notas" class="notas_plazo"><?php echo (isset($row['notas']) ? iconv("UTF-8", "CP1252", $row['notas']):''); ?></span>
                                </td>
                              </tr>
                          <?php endforeach; ?>                                         
                        </table>
                   </div>
        <?php else: ?>
              <div>No hay ningún plazo excepcional definido.</div>
        <?php endif; ?>
		</div>
        <!-- FIN BLOQUE RESULTADOS DE LA BUSQUEDA -->       
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->


</div>
<div id="dialog-notas" title="Notas del plazo excepcional" >
	<div id="msg_notas" style="text-align: left"></div>
</div>
<div id="dialog-notas-aviso" title="Notas del plazo excepcional" >
	<div id="msg_notas-aviso" style="text-align: left;margin-top:15px;"></div>
</div>
<!-- FIN BLOQUE INTERIOR -->