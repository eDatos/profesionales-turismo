<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript">
$(document).ready( function() {
	$("input[type='button']").button();
	$("#button").button();
	$("input[type='submit']").button();
	$("input[type='checkbox']").click(function() {
		$("#infomsg").hide();
	});
  });
</script>
<?php
define("ESTILO_OK", "okicon pagemsg_success");
define("ESTILO_ERROR", "erroricon pagemsg_error");
?>
<script type="text/javascript" src="js/inputfields.js"></script>    
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="bloq_central titulo_1">Gestión de enlaces de ayuda</h1>
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
							$mensaje = "Error al guardar el enlace de ayuda";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Enlace de ayuda guardado";
						}
						break;
					case OP_ELIMINAR:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al eliminar el enlace de ayuda";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Enlace de ayuda eliminado";
						}
						break;
				}
				echo '<span id="infomsg" class="titulo_3 ' . $estilo_msg . '">' . $mensaje . '</span>';
			}
		?>	
		<?php if(isset($enlace_mostrar)): ?>
            <div class="cuadro fondo_gris">
				<form style="margin-left:1%;" name="formEnlaceAyuda" action="#" method="post">  
					<table width="100%">
						<tr>
							<td>  
				                <table style="width:95%;">
				                   <tr>
				                     <td width="20%">Código:</td>
				                     <td><input id="cod_enlace" name="cod_enlace" type="text" maxlength=15 value="<?= $enlace_mostrar->cod_enlace ?>" style="width:450px;" required/></td>
				                   </tr>                   
				                   <tr>
				                     <td width="20%">Título:</td>
				                     <td><input id="titulo" name="titulo" type="text" maxlength=100 value="<?= $enlace_mostrar->titulo ?>" style="width:450px;" required/></td>
				                   </tr>
				                   <tr>
				                     <td width="20%">Desc. corta:</td>
				                     <td><input id="desc_corta" name="desc_corta" type="text" maxlength=50 value="<?= $enlace_mostrar->desc_corta ?>" style="width:450px;"/></td>
				                   </tr>
				                   <tr>
				                     <td style="vertical-align: top; width=20%;">Desc. larga:</td>
				                     <td colspan=3><textarea name="desc_larga" maxlength=200 style="width:100%;resize: none;"><?= $enlace_mostrar->desc_larga ?></textarea></td>
				                   </tr>
				                   <tr>
				                     <td width="20%">Tipo:</td>
				                     <td>
	   				                   <select name='tipo'>
											<option value='0' <?php if ($enlace_mostrar->tipo=='0') echo "selected";?>>Mensaje central</option>
											<option value='1' <?php if ($enlace_mostrar->tipo=='1') echo "selected";?>>Ventana flotante</option>
											<option value='2' <?php if ($enlace_mostrar->tipo=='2') echo "selected";?>>Enlace externo</option>							
											<option value='3' <?php if ($enlace_mostrar->tipo=='3') echo "selected";?>>Aviso embebido</option>							
										</select>
				                     </td>
				                   </tr>
				                   <tr>
				                     <td style="vertical-align: top; width=20%;">Contenido ayuda:</td>
				                     <td colspan=3><textarea name="contenido_ayuda" style="width:100%;height:200px;resize: none;"><?= $enlace_mostrar->contenido_ayuda ?></textarea></td>
				                   </tr>
				                   <tr>
				                     <td width="20%">URL enlace externo:</td>
				                     <td><input id="url_enlace_externo" name="url_enlace_externo" type="text" maxlength=200 value="<?= $enlace_mostrar->url_enlace_externo ?>" style="width:450px;"/></td>
				                   </tr>                 
				                   <tr>
				                     <td width="20%">Ancho ventana:</td>
				                     <td width="30%"><input class="numero sindecimales digits" id="ancho_popup" name="ancho_popup" type="text" maxlength=4 value="<?= $enlace_mostrar->ancho_popup ?>" style="width:50px;"/></td>
				                     <td width="20%">Alto ventana:</td>
				                     <td width="30%"><input class="numero sindecimales digits" id="alto_popup" name="alto_popup" type="text" maxlength=4 value="<?= $enlace_mostrar->alto_popup ?>" style="width:50px;"/></td>
	   			                   </tr>
								   <tr>
				                   	<td width="20%" colspan="4"><br>Posición relativa al elemento de muestra la ayuda (sólo aplicable a ventana flotante):</td>
				                   </tr>
				                   <tr>
				                     <td width="20%">Posición X:</td>
				                     <td width="30%"><input class="numero negativo digits" id="posx_popup" name="posx_popup" type="text" maxlength=4 style="width:50px;" value="<?= $enlace_mostrar->posX_popup ?>"/></td>
				                     <td width="20%">Posición Y:</td>
				                     <td width="30%"><input class="numero negativo digits" id="posy_popup" name="posy_popup" type="text" maxlength=4 style="width:50px;" value="<?= $enlace_mostrar->posY_popup ?>"/></td>
	   			                   </tr>                                      
	   			                                      
	   			            	</table>
				            </td>
				            <td style="vertical-align: top;">
				            	<input class="search" name="operationBtn" type="submit" value="Modificar" style="width:90px; margin: 10px 0px 10px 0px;"><br/>          
				                <input class="search" name="operationBtn" type="submit" value="Eliminar" style="width:90px; margin: 10px 0px 10px 0px;">
				            </td>
			            </tr> 
		            </table>  			                
	                <input type="hidden" name="id" value="<?= $enlace_mostrar->id ?>"/>
	            </form>
	        </div>		
	        <div style="position:relative;"><a href="javascript:history.back()" class="enlace volvericon">Volver</a></div>
		<?php else: ?>
			<div class="cuadro fondo_gris">
			  <h2 class="titulo_2">Nuevo enlace de ayuda</h2>
		      <div class="subrayado"></div>
				<form style="margin-left:1%;" id="df" name="formEnlaceAyuda" action="#" method="post">
					<table width="100%">
						<tr>
							<td>  
				                <table style="width:95%;">
				                   <tr>
				                     <td width="20%">Código:</td>
				                     <td><input id="cod_enlace" name="cod_enlace" type="text" maxlength=15 style="width:150px;" required/></td>
				                   </tr>                   
				                   <tr>
				                     <td width="20%">Título:</td>
				                     <td><input id="titulo" name="titulo" type="text" maxlength=100 style="width:450px;" required/></td>
				                   </tr>
				                   <tr>
				                     <td width="20%">Desc. corta:</td>
				                     <td colspan=3><input id="desc_corta" name="desc_corta" type="text" maxlength=50 style="width:80%;"/></td>
				                   </tr>
				                   <tr>
				                     <td style="vertical-align: top; width=20%;">Desc. larga:</td>
				                     <td colspan=3><textarea name="desc_larga" maxlength=200 style="width:100%;resize: none;"></textarea></td>
				                   </tr>
				                   <tr>
				                     <td width="20%">Tipo:</td>
				                     <td>
	   				                   <select name='tipo'>
											<option value='0'>Mensaje central</option>
											<option value='1'>Ventana flotante</option>
											<option value='2'>Enlace externo</option>							
											<option value='3'>Aviso embebido</option>							
										</select>
				                     </td>
				                   </tr>
				                   <tr>
				                     <td style="vertical-align: top; width=20%;">Contenido ayuda:</td>
				                     <td colspan=3><textarea name="contenido_ayuda" style="width:100%;height:200px;resize: none;"></textarea></td>
				                   </tr>
				                   <tr>
				                     <td width="20%">URL enlace externo:</td>
				                     <td><input id="url_enlace_externo" name="url_enlace_externo" type="text" maxlength=200 style="width:450px;"/></td>
				                   </tr>
				                   <tr>
				                     <td width="20%">Ancho ventana:</td>
				                     <td width="30%"><input class="numero sindecimales digits" id="ancho_popup" name="ancho_popup" type="text" maxlength=4 style="width:50px;"/></td>
				                     <td width="20%">Alto ventana:</td>
				                     <td width="30%"><input class="numero sindecimales digits" id="alto_popup" name="alto_popup" type="text" maxlength=4 style="width:50px;"/></td>
	   			                   </tr>				                   	
				                   <tr>
				                   	<td width="20%" colspan="4"><br>Posición relativa al elemento de muestra la ayuda (sólo aplicable a ventana flotante):</td>
				                   </tr>
				                   <tr>
				                     <td width="20%">Posición X:</td>
				                     <td width="30%"><input class="numero negativo digits" id="posx_popup" name="posx_popup" type="text" maxlength=4 style="width:50px;"/></td>
				                     <td width="20%">Posición Y:</td>
				                     <td width="30%"><input class="numero negativo digits" id="posy_popup" name="posy_popup" type="text" maxlength=4 style="width:50px;"/></td>
	   			                   </tr>                                      
	   			            	</table>
				            </td>
				            <td style="vertical-align: top;">
				            	<input class="search" name="operationBtn" type="submit" value="Insertar" style="width:90px; margin: 10px 0px 10px 0px;">
				            </td>
			            </tr> 
		            </table>                          
	            </form>           
			</div>            
			<?php if ($enlaces->has_rows()) : ?>
	        <!-- COMIENZO LISTADO ENLACES -->
				<div id="listado_enlaces">
                  <!-- tabla de resultados -->
                  <div id="tabla_resultados">
                        <table class="tablaresultado" width="100%">
                          <tr>
                            <th scope="col">CODIGO</th>
                            <th scope="col">TITULO</th>
                            <th scope="col">DESCRIPCIÓN CORTA</th>
                          </tr>
                          <?php foreach ($enlaces as $row): ?>
 						  <tr>
                                <td><?= $row["cod_enlace"]?></td>
                                <td><a href="<?= $this->build_url( $actionEditEnlace, array(ARG_COD_ENLACE => $row['cod_enlace']) ); ?>"><?= $row["titulo"]?></a></td>
                                <td><?= $row["desc_corta"]?></td>
                          </tr> 
						  <?php endforeach; ?>                                                                   
                        </table>
                   </div>
        		</div>
        	<!-- FIN BLOQUE LISTADO ENLACES -->
        	<?php endif ?>			
        <?php endif ?>
	</div>	
<!-- FIN BLOQUE INTERIOR -->