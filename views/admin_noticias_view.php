<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/inputfields.js"></script>
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
    
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="bloq_central titulo_1">Gestión de canales de noticias</h1>
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
							$mensaje = "Error al guardar el cambio del canal";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Canal guardado";
						}
						break;
					case OP_ELIMINAR:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al eliminar el canal";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Canal eliminado";
						}
						break;
				}
				echo '<span id="infomsg" class="titulo_3 ' . $estilo_msg . '">' . $mensaje . '</span>';
			}
		?>	
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Nuevo canal de noticias</h2>
	      <div class="subrayado"></div>
			<form style="margin-left:1%;" id="df" name="formAviso" action="#" method="post">
				<table width="100%">
					<tr>
						<td>  
			                <table style="width:95%;">
			                	<tr>
			                     <td width="15%">Título del canal:</td>
			                     <td width="20%"><input required placeholder="texto descriptivo del canal" id="titulo" name="titulo" type="text" maxlength="300"  style="width:500px;" /></td>
			                   </tr>                  
			                   <tr>
			                     <td width="15%">Dirección del canal de noticias:</td>
			                     <td width="20%"><input required placeholder="url del canal" id="url" name="url" type="text" maxlength="300"  style="width:500px;" /></td>
			                   </tr>                  
			                   <tr>
			                     <td width="15%">Número de entradas a mostrar:</td>
			                     <td colspan=3><input class="numero sindecimales digits" required min="0" id="num_entradas" style="width:40px;" name="num_entradas" type="number" maxlength="2"/></td>
			                   </tr>
			                   <tr>
			                     <td width="15%">Activado:</td>
			                     <td colspan=3><input id="activado" name="activado" type="checkbox"/></td>
			                   </tr>
			                   <tr>
			                     <td width="15%">¿Es un canal del ISTAC?</td>
			                     <td colspan=3><input id="esistac" name="esistac" type="checkbox"/></td>
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
			<?php if (isset($noticias) && count($noticias) > 0) : ?>
	            <?php foreach ($noticias as $row): ?>
		            <div class="cuadro fondo_gris">
						<form style="margin-left:1%;" name="formAviso" action="#" method="post">
							<input type="hidden" name="id" value="<?= $row->id ?>" /> 
							<table style="width:100%;">
								<tr>
									<td>  
						                <table style="width:95%;">
						                	<tr>
			                     				<td width="15%">Título del canal:</td>
			                     				<td width="20%"><input required placeholder="texto descriptivo del canal" id="titulo" name="titulo" type="text" maxlength="300"  style="width:500px;" value="<?= @$row->titulo ?>" /></td>
			                   				</tr>                  
						                   <tr>
						                     <td width="15%">Dirección del canal de noticias:</td>
						                     <td width="20%"><input required placeholder="url del canal" id="url" name="url" type="text" maxlength="300"  style="width:500px;" value="<?= $row->url ?>" /></td>
						                   </tr>                   
						                   <tr>
						                     <td width="15%">Número de entradas a mostrar:</td>
						                     <td colspan=3><input class="numero sindecimales digits" required min="0" id="num_entradas" name="num_entradas" type="number" maxlength="2" style="width:40px;" value="<?= $row->max ?>" /></td>
						                   </tr>
						                   <tr>
						                     <td width="15%">Activado:</td>
						                     <td colspan=3><input id="activado" name="activado" type="checkbox" <?= $row->activado ? "checked='checked'" : ""; ?>" /></td>
						                   </tr>
						                   <tr>
						                     <td width="15%">¿Es un canal del ISTAC?</td>
						                     <td colspan=3><input id="esistac" name="esistac" type="checkbox" <?= $row->hasPriority ? "checked='checked'" : ""; ?>" /></td>
						                   </tr>			                   
						                 </table>
						            </td>
						            <td style="vertical-align: top;">
						            	<input class="search" name="operationBtn" type="submit" value="Modificar" style="width:90px; margin: 10px 0px 10px 0px;"><br/>          
						                <input class="search" name="operationBtn" type="submit" value="Eliminar" style="width:90px; margin: 10px 0px 10px 0px;">
						            </td>
					            </tr> 
				            </table>  			                
			            </form>
			        </div>
	            <?php endforeach; ?>                                         
	        <?php endif; ?>	
		</div>		
	</div>	
<!-- FIN BLOQUE INTERIOR -->