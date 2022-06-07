<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/dates.js"></script>
<script type="text/javascript">
$(document).ready( function() {

	$("#df").validate({
		rules: {
			fechaInicio: "required",
			fechaFin: {
				mayorque: "#fechaInicio"
			}
		}, 
		wrapper: "p",
    	errorPlacement: function(error, element) {
        	error.appendTo(element.parent());
        	error.addClass("validmsg");
    	}
	});
	$.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
	$(".datepicker").datepicker( { dateFormat: "<?= DateHelper::getDateFormat("datepicker") ?>" } );
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
    
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="bloq_central titulo_1">Gestión de avisos</h1>
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
							$mensaje = "Error al guardar el aviso";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Aviso guardado";
						}
						break;
					case OP_ELIMINAR:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al eliminar el aviso";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Aviso eliminado";
						}
						break;
				}
				echo '<span id="infomsg" class="titulo_3 ' . $estilo_msg . '">' . $mensaje . '</span>';
			}
		?>	
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Nuevo aviso</h2>
	      <div class="subrayado"></div>
			<form style="margin-left:1%;" id="df" name="formAviso" action="#" method="post">
				<table width="100%">
					<tr>
						<td>  
			                <table style="width:95%;">
			                   <tr>
			                     <td width="15%">Fecha de inicio:</td>
			                     <td width="35%"><input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" id="fechaInicio" name="fechaInicio" type="text"/></td>
			                     <td width="15%">Fecha de fin:</td>
			                     <td width="35%"><input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" id="fechaFin" name="fechaFin" type="text"/></td>
			                   </tr>                   
			                   <tr>
			                     <td width="15%">Título:</td>
			                     <td colspan=3><input id="titulo" name="titulo" type="text" maxlength=50 style="width:450px;"/></td>
			                   </tr>
			                   <tr>
			                     <td style="vertical-align: top; width=15%;">Aviso:</td>
			                     <td colspan=3><textarea name="texto" maxlength=4000 style="width:100%;height:200px;resize: none;"></textarea></td>
			                   </tr>                   
			                   <tr>
			                     <td width="15%">A:</td>
			                     <td colspan=3>
				                   <select name='grupo'>
									<?php
										foreach($grupos as $id_grupo => $descripcion)
										{
											echo "<option value='".$id_grupo."'>".$descripcion."</option>";							
										}  
									?>	
									</select>
			                     </td>
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
			<?php if ($avisos->has_rows()) : ?>
	            <?php foreach ($avisos as $row): ?>
		            <div class="cuadro fondo_gris">
						<form style="margin-left:1%;" name="formAviso" action="#" method="post">  
							<table width="100%">
								<tr>
									<td>  
						                <table style="width:95%;">
						                   <tr>
						                     <td width="15%">Fecha de inicio:</td>
						                     <td width="35%"><input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" name="fechaInicio" type="text" value="<?= str_replace('-', DATE_SEPARATOR, $row['fecha_ini_t']) ?>"/></td>
						                     <td width="15%">Fecha de fin:</td>
						                     <td width="35%"><input placeholder="<?= DateHelper::getDateFormat("show") ?>" class="datepicker" name="fechaFin" type="text" value="<?= str_replace('-', DATE_SEPARATOR, $row['fecha_fin']) ?>"/></td>
						                   </tr>                   
						                   <tr valign="top">
						                     <td width="15%">Título:</td>
						                     <td colspan=3><input name="titulo" type="text" maxlength=50 style="width:450px;"  value="<?= $row['titulo'] ?>"/></td>
						                   </tr>
						                   <tr valign="top">
						                     <td valign="top" width="15%">Aviso:</td>
						                     <td colspan=3><textarea name="texto" maxlength=4000 style="width:100%;height:200px;resize: none;"><?= $row['aviso'] ?></textarea></td>
						                   </tr>
						                   <tr>
						                     <td valign="top" width="15%">A:</td>
						                     <td colspan=3>
							                   <select name='grupo'>
												<?php
													foreach($grupos as $id_grupo => $descripcion)
													{
														echo "<option value='".$id_grupo."'";
														if ($id_grupo == $row['id_grupo'])
															echo "selected";
														echo ">".$descripcion."</option>";
													}									
												?>	
												</select>
						                     </td>
						                   </tr>                   			                                      
						                </table>
						            </td>
						            <td style="vertical-align: top;">
						            	<input class="search" name="operationBtn" type="submit" value="Modificar" style="width:90px; margin: 10px 0px 10px 0px;"><br/>          
						                <input class="search" name="operationBtn" type="submit" value="Eliminar" style="width:90px; margin: 10px 0px 10px 0px;">
						            </td>
					            </tr> 
				            </table>  			                
			                <input type="hidden" name="fechaCreacion" value="<?= $row['fecha_creacion'] ?>"/>
			            </form>
			        </div>
	            <?php endforeach; ?>                                         
	        <?php endif; ?>	
		</div>		
	</div>	
<!-- FIN BLOQUE INTERIOR -->