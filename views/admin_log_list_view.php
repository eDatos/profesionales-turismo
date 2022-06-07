<script type="text/javascript">
	$(document).ready(function(){
		$(".mas").click(function(){
			$.ajax({
					type: "POST",
					url: "<?= $detailUrl; ?>", 
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					data: { id: $(this).attr('id') }, 
					success: function(data) 
					{ 
						$( "#dialog-detail .msg" ).html(data);
						$( "#dialog-detail" ).dialog("open"); 
					}
					});
			return false;
		});
		$( "#dialog-detail" ).dialog({
	    	autoOpen: false,
	        resizable: false,
	        modal: true,
	    	buttons: {
	            "Ok": function() {
	            	$( this ).dialog( "close" );
	            }
	        }
	    });
	});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<div class="bloq_central">
		<h1 class="titulo_1">Resultados de la búsqueda del registro</h1>
		<?php $page->renderErrorMsg($errors, "titulo_3 erroricon pagemsg_error"); ?>
		<div id="dialog-detail" title="Detalles de la entrada">
    		<p style="text-align:left;"><span class="msg"></span></p>
		</div>		
		<?php if ($logdata != NULL && $logdata->has_rows()) : ?>
        	<div style="position:relative;top:-8px;"><a href="javascript:history.back()" class="enlace volvericon">Volver</a></div>
        	<div class="cuadro fondo_verde" style="margin-top:0px;">
			    <h3 class="titulo_3" style="margin-bottom:4px;">Nota</h3>
	        	Sólo se muestran los <?=MAX_LOG_LIST_ITEMS ?> primeros registros.
	        </div>
	        <!-- tabla de resultados -->
			<div id="tabla_resultados">
	        	<table class="tablaresultado">
					<tr>
						<th>USUARIO</th><th>ESTABLECIMIENTO</th><th style="text-align: center">MES/TRIM</th><th style="text-align: center">AÑO</th><th>DESCRIPCION</th><th>FECHA</th><th>HORA</th>
	                </tr>
	                <?php foreach ($logdata as $rowlog): ?>
	                <?php
	                	// Prepara los atributos de la fila
	                	$trow = array('id' => "id='".$rowlog['id']."'");
	                	$trowstyle = "";
	                	$trowclass = "";
	                	if ($rowlog['estado_ejecucion'] != SUCCESSFUL)
	                	{
	                		$trowstyle .= "color:red;";
	                	} 
	                	if ($rowlog['nummsgs'] > 0)
	                	{
	                		$trowstyle .= "cursor:pointer;cursor:hand;";
	                		$trowclass = "class='mas'";
	                	}
	                	if (strlen($trowstyle) > 0)
	                	{
	                		$trowstyle = "style='".$trowstyle."'";
	                	}
	                	// Prepara los atributos de la columna 'descripcion'
	                	$tddesc = $rowlog['descripcion'];
	                	$tdstyle = "";
	                	if ($rowlog['estado_ejecucion'] != SUCCESSFUL)
	                	{
	                		$tddesc .=" (Fallido)";
	                		$tdstyle .="color:red;";
	                	}
	                	if ($rowlog['nummsgs'] > 0)
	                	{
	                		$tddesc .="<span style='float:right;'>(+)</span>";
	                	}
	                ?>
	                <tr <?= @$trow['id']?> <?= $trowstyle?> <?= $trowclass?>>
	                    <td><?= $rowlog['username'] ?></td>
	                	<td><?= $rowlog['nombre_est'] ?></td>
	                	<td style="text-align: center"><?= ((is_null($rowlog['trim_mes']))?'-':$rowlog['trim_mes']) ?></td>
	                	<td style="text-align: center"><?= ((is_null($rowlog['anio']))?'-':$rowlog['anio']) ?></td>
	                	<td><?= $tddesc ?></td>
	                	<td><?= $rowlog['fecha'] ?></td>
	                	<td><?= $rowlog['hora'] ?></td>
	                </tr> 
	                <?php endforeach; ?>                                         
	            </table>
	        </div>
        <?php else: ?>
            <div style="font-style: italic; margin:10px;">No hay ningún registro que coincida con los criterios de búsqueda indicados.</div>
        <?php endif; ?>
	        <div><a href="javascript:history.back()" class="enlace volvericon">Volver</a></div>
        
    </div>
</div>
<!-- FIN BLOQUE INTERIOR -->