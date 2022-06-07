<script>
var valoresIniciales=[];
function validarSubmit(form)
{
	var res=false;
	$("#ef table input").each(function(index,element){var a=new String(valoresIniciales[index]); var b=new String(element.value); if(a.toUpperCase()!==b.toUpperCase()) {res=true; return false;}});
	if(res==false)
		alert("Solicitud de modificación no enviada. No hay cambios que enviar.");
	return res;
}
$(document).ready(function() {
			$("#enviarBtn").button();
			$("#resetBtn").button();
			$("input:text:visible:first").focus();

			$("#ef table input").each(function(index,element){valoresIniciales[index]=element.value;});
			$("#ef").submit(
					function(event)
					{
						if(validarSubmit(this)==false)
						{
							event.preventDefault();
							return false;
						}
					});
		});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<?php if(isset($res)): ?>
<div id="bloq_interior">
	<h1 class="titulo_1 noprint">Datos de su establecimiento</h1>
	<div class="bloq_central">
		<?php if ($res === true) : ?>
		<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Modificación de los datos de su establecimiento</h3>
			<div class="subrayado"></div>
			<p>Su solicitud de modificación de datos ha sido enviada y será tramitada lo antes posible. <b>Recuerde que no verá reflejados los cambios hasta que finalice todo el proceso.</b></p>
			<p>Como medida de seguridad adicional, es posible que nos pongamos en contacto con usted para confirmar los nuevos datos antes de incorporarlos definitivamente a nuestros ficheros.</p>
			<p><b>Muchas gracias por su colaboración.</b></p>
		</div>
        <div style="margin-top:20px;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
		<?php else: ?>
		<div class="cuadro" style="text-align: justify; background-color:#FF9393;border-color:#F37575" class="noprint">
			<h3 class="titulo_3 erroricon" style="margin-bottom:4px;">Modificación de los datos de su establecimiento</h3>
			<p>No ha sido posible recoger su solicitud de modificación de datos debido a un error.</p>
            <p><b>Muchas gracias por su colaboración.</b></p>
		</div>		
        <?php endif; ?>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->
<?php else : ?>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
		<div class="cuadro fondo_amarillo">
		<h2 class="titulo_2" style="float:left;">Datos de su establecimiento</h2>
		<a id="ayuda_estab" href="javascript:MostrarAyuda('ayuda_estab','AYUDA22<?= $establecimiento->es_hotel() ? "_HOT" : "_APT" ?>');" class="ayudaicon enlace" style="float:right;margin-top:3px;background-position-y: 2px;" title="Ayuda (tecla de acceso: y)" accesskey="y"><strong>Ayuda</strong></a>	
		<div style="clear:both;"></div>
	
		<div class="subrayado"></div>
		<p>Aquí puede solicitar cambios en sus datos, si observa algún error en los mismos o si se produce alguna modificación. Sus datos aparecerán actualizados tan pronto su solicitud sea procesada por el personal del ISTAC.</p><p>
Los datos referentes a su establecimiento son recogidos mediante protocolo seguro de comunicaciones. El ISTAC se compromete a utilizarlos exclusivamente para la elaboración de estadísticas y a no cederlos en ningún caso, ni usarlos para ningún otro fin.</p><p>

Todo ello al amparo del <b>secreto estadístico establecido en la Ley 1/1991 de Estadística de Canarias.</b></p>
		</div>
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Datos actuales de su establecimiento</h2>
	      <div class="subrayado"></div>
			<form id="ef" name="ef" action="#" method="post">  
				   <table style="margin-top:8px;width:100%">
	                  <tr style="line-height:8px;">
	                    <td style="width:57%" class="formlabel">Razón social</td>
	                    <td class="formlabel">CIF / NIF</td>
	                    <td class="formlabel">Nº registro</td>
	                  </tr>
	                  <tr>
	                    <td><input name="rs" style="margin-left:0px;" type="text" maxlength="100" size="80" value="<?= @$establecimiento->razon_social ?>"></td>
		                <td><input name="cif" style="margin-left:0px;" type="text" maxlength="9" size="15" value="<?= @$establecimiento->cif_nif ?>"></td>
		                <td><input name="reg" style="margin-left:0px;" type="text" maxlength="50" size="15" value="<?= @$establecimiento->num_registro ?>"></td>
		              </tr>	
	                  <tr style="font-size:11px;line-height:3px;">
	                    <td><?= @$establecimiento->razon_social ?></td>
		                <td><?= @$establecimiento->cif_nif ?></td>
		                <td><?= @$establecimiento->num_registro ?></td>		                
		              </tr>
		          </table>
				   <table style="margin-top:8px;width:100%">
	                  <tr style="line-height:8px;">
	                    <td style="width:57%" class="formlabel">Denominación</td>
	                    <td class="formlabel">Tipo de establecimiento</td>
	                    <td class="formlabel">Categoría</td>
	                  </tr>
	                  <tr>
	                    <td><input name="den" style="margin-left:0px;" type="text" maxlength="40" size="80" value="<?= @$establecimiento->nombre_establecimiento ?>"></td>
		                <td><input name="tipo" style="margin-left:0px;" type="text" maxlength="20" size="25" value="<?= @$establecimiento->texto_tipo_establecimiento ?>"></td>
		                <td><input name="cat" style="margin-left:0px;" type="text" maxlength="1" size="25" value="<?= @$establecimiento->id_categoria ?>"></td>
		              </tr>	
	                  <tr style="font-size:11px;line-height:3px;">
	                    <td><?= @$establecimiento->nombre_establecimiento ?></td>
		                <td><?= @$establecimiento->texto_tipo_establecimiento ?></td>
		                <td><?= @$establecimiento->grupo_categoria ?></td>
		              </tr>
		          </table>	
		          	<table style="margin-top:8px;width:62%">
	                  <tr style="line-height:8px;">
	                    <td  class="formlabel">Nº de plazas</td>
	                    <td  class="formlabel"><?= ($establecimiento->id_tipo_establecimiento == 3)?'Nº de apartamentos':'Nº de habitaciones' ?></td>
	                    <td  class="formlabel">Nº de camas supletorias</td>
	                  </tr>
	                  <tr>
	                    <td><input name="nplazas" style="margin-left:0px;" type="text" maxlength="5" size="10" value="<?= @$establecimiento->num_plazas ?>"></td>
		                <td><input name="nhab" style="margin-left:0px;" type="text" maxlength="5" size="20" value="<?= @$establecimiento->num_habitaciones ?>"></td>
		                <td><input name="nsup" style="margin-left:0px;" type="text" maxlength="5" size="20" value="<?= @$establecimiento->num_plazas_supletorias ?>"></td>
		              </tr>	
	                  <tr style="font-size:11px;line-height:3px;">
	                    <td><?= @$establecimiento->num_plazas ?></td>
	                    <td><?= @$establecimiento->num_habitaciones ?></td>
	                    <td><?= @$establecimiento->num_plazas_supletorias ?></td>
		              </tr>
		          </table>
		          <hr style="margin-top:15px;border:0px solid black;border-top:1px solid #999;height:0px;"/>
				   <table style="margin-top:8px;width:100%">
	                  <tr style="line-height:8px;">
	                    <td style="width:58%" class="formlabel">Dirección</td>
	                    <td class="formlabel">Localidad</td>
	                    <td class="formlabel">Código postal</td>
	                  </tr>
	                  <tr>
	                    <td><input name="dir" style="margin-left:0px;" type="text" maxlength="150" size="80" value="<?= @$establecimiento->direccion ?>"></td>
		                <td><input name="loc" style="margin-left:0px;" type="text" maxlength="50" size="40" value="<?= @$establecimiento->localidad ?>"></td>
		                <td><input name="cp" style="margin-left:0px;" type="text" maxlength="5" size="10" value="<?= @$establecimiento->codigo_postal ?>"></td>
		              </tr>	
	                  <tr style="font-size:11px;line-height:3px;">
	                    <td><?= @$establecimiento->direccion ?></td>
	                    <td><?= @$establecimiento->localidad ?></td>
	                    <td><?= @$establecimiento->codigo_postal ?></td>
		              </tr>
		          </table>		          	
		          	<table style="margin-top:8px;width:100%;">
	                  <tr style="line-height:8px;">
	                    <td  class="formlabel">Provincia</td>
	                    <td  class="formlabel">Municipio</td>
	                    <td  class="formlabel">Isla</td>
	                  </tr>
	                  <tr>
	                    <td><input name="prov" style="margin-left:0px;" type="text" maxlength="25" size="40" value="<?= @$establecimiento->provincia ?>"></td>
		                <td><input name="mun" style="margin-left:0px;" type="text" maxlength="50" size="40" value="<?= @$establecimiento->municipio ?>"></td>
		                <td><input name="isla" style="margin-left:0px;" type="text" maxlength="20" size="30" value="<?= @$establecimiento->nombre_isla ?>"></td>
		              </tr>	
	                  <tr style="font-size:11px;line-height:3px;">
	                    <td><?= @$establecimiento->provincia ?></td>
	                    <td><?= @$establecimiento->municipio ?></td>
	                    <td><?= @$establecimiento->nombre_isla ?></td>
		              </tr>
		          </table>		  
		          <hr style="margin-top:15px;border:0px solid black;border-top:1px solid #999;height:0px;"/>
		           <table style="margin-top:8px;width:100%;">
	                  <tr style="line-height:8px;">
	                    <td  class="formlabel">Director</td>
	                    <td  class="formlabel">Persona de contacto</td>
	                    <td  class="formlabel">Explotación</td>
	                  </tr>
	                  <tr>
	                    <td><input name="director" style="margin-left:0px;" type="text" maxlength="30" size="40" value="<?= @$establecimiento->director ?>"></td>
		                <td><input name="contacto" style="margin-left:0px;" type="text" maxlength="30" size="40" value="<?= @$establecimiento->nombre_contacto ?>"></td>
		                <td><input name="expl" style="margin-left:0px;" type="text" maxlength="30" size="30" value="<?= @$establecimiento->nombre_explotacion ?>"></td>
		              </tr>	
	                  <tr style="font-size:11px;line-height:3px;">
	                    <td><?= @$establecimiento->director ?></td>
	                    <td><?= @$establecimiento->nombre_contacto ?></td>
	                    <td><?= @$establecimiento->nombre_explotacion ?></td>
		              </tr>
		          </table>
		           <table style="margin-top:8px;width:100%;">
	                  <tr style="line-height:8px;">
	                    <td  class="formlabel">Teléfono</td>
	                    <td  class="formlabel">Fax</td>
	                    <td  class="formlabel">Email</td>
	                  </tr>
	                  <tr>
	                    <td><input name="tel1" style="margin-left:0px;" type="text" maxlength="15" size="20" value="<?= @$establecimiento->telefono ?>"></td>
		                <td><input name="fax1" style="margin-left:0px;" type="text" maxlength="13" size="20" value="<?= @$establecimiento->fax ?>"></td>
		                <td><input name="email1" style="margin-left:0px;" type="text" maxlength="100" size="40" value="<?= @$establecimiento->email ?>"></td>
		              </tr>	
	                  <tr style="font-size:11px;line-height:3px;">
	                    <td><?= @$establecimiento->telefono ?></td>
	                    <td><?= @$establecimiento->fax ?></td>
	                    <td><?= @$establecimiento->email ?></td>
		              </tr>
		          </table>	
		          <table style="margin-top:8px;width:100%;">
	                  <tr style="line-height:8px;">
	                    <td  class="formlabel">Teléfono 2</td>
	                    <td  class="formlabel">Fax 2</td>
	                    <td  class="formlabel">Email 2</td>
	                  </tr>
	                  <tr>
	                    <td><input name="tel2" style="margin-left:0px;" type="text" maxlength="15" size="20" value="<?= @$establecimiento->telefono2 ?>"></td>
		                <td><input name="fax2" style="margin-left:0px;" type="text" maxlength="13" size="20" value="<?= @$establecimiento->fax2 ?>"></td>
		                <td><input name="email2" style="margin-left:0px;" type="text" maxlength="100" size="40" value="<?= @$establecimiento->email2 ?>"></td>
		              </tr>	
	                  <tr style="font-size:11px;line-height:3px;">
	                    <td><?= @$establecimiento->telefono2 ?></td>
	                    <td><?= @$establecimiento->fax2 ?></td>
	                    <td><?= @$establecimiento->email2 ?></td>
		              </tr>
		          </table>	
		          <table style="margin-top:8px;width:100%;">
	                  <tr style="line-height:8px;">
	                    <td  class="formlabel">Web</td>
	                  </tr>
	                  <tr>
	                    <td><input name="web" style="margin-left:0px;" type="text" maxlength="100" size="80" value="<?= @$establecimiento->url ?>"></td>
		              </tr>	
	                  <tr style="font-size:11px;line-height:3px;">
	                  <td><?= @$establecimiento->url ?></td>
	                  </tr>
		          </table>
	             <input id="enviarBtn" type="submit" value="Enviar modificaciones" style="margin: 10px 0px 10px 0px;"/>        
	             <input id="resetBtn" type="reset" value="Dejar como estaba" style="margin: 10px 0px 10px 0px;"/>
	             <input type="hidden" name="<?= ARG_OP ?>" value="ce"/>        
			</form>          
		</div>
	</div>
	<div style="clear:both;"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></div>
</div>
<!-- FIN CAJA AMARILLA -->
<?php endif;?>
