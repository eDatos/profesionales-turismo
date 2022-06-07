<script type="text/javascript">
$(document).ready( function() {
	$("#darBaja, #darAlta, #nuevoUsuarioBtn").button();
  });
</script>
    
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1">Administración de Establecimientos</h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Datos del establecimiento</h2>
	      <div class="subrayado"></div>
			<form name="cambiar_estado" action="#" method="post">  
				    <table style="width:100%">
	                  <tr>
	                    <td style="width:20%" class="formlabel">Nombre:</td>
	                    <td><?= $establecimiento->id_establecimiento ?> - <?= $establecimiento->nombre_establecimiento ?></td>
		                <td style="width:10%" class="formlabel">Isla:</td>
	                    <td><?= $establecimiento->nombre_isla ?></td>                
	                  </tr>
	                  <tr>
	                    <td class="formlabel">Tipo establecimiento:</td>
	                    <td><?= $establecimiento->texto_id_tipo_establecimiento ?></td>
	      	            <td class="formlabel">Dirección:</td>
	                    <td><?= $establecimiento->direccion ?></td>                
	                  </tr>
	                  <tr>
	                    <td class="formlabel">Categoría:</td>
	                    <td><?= $establecimiento->grupo_categoria ?></td>
	                    <td class="formlabel">Localidad:</td>
	                    <td><?= $establecimiento->localidad ?></td>                
	                    </tr>              
	                  <tr>
	                    <td class="formlabel">Estado:</td>
	                    <td colspan="2"><?= $establecimiento->esta_activo() ? 'En activo': 'Baja definitiva'  ?></td>
	                  </tr>                            
	                </table>         
                </form>
                <div style="clear:both;">
                <?php if ($establecimiento->esta_activo()) : ?>
                <form name="dbf" action="#" method="post" style="display:inline-block">
                	<input type="hidden" value="baja" name="<?= ARG_OP ?>"/>
                    <input type="hidden" value="<?= $establecimiento->id_establecimiento ?>" name="<?= ARG_ESTID ?>"/>
                </form>
                <form name="daf" action="#" method="post" style="display:inline-block">
                	<input type="hidden" value="alta" name="<?= ARG_OP ?>"/>
                    <input type="hidden" value="<?= $establecimiento->id_establecimiento ?>" name="<?= ARG_ESTID ?>"/>
                </form>                    
				<?php else: ?> 
				<form name="dbf" action="#" method="post" style="display:inline-block">
                	<input type="hidden" value="baja" name="<?= ARG_OP ?>"/>
                	<input type="hidden" value="<?= $establecimiento->id_establecimiento ?>" name="<?= ARG_ESTID ?>"/>
                </form>
                <form name="daf" action="#" method="post" style="display:inline-block">
                	<input type="hidden" value="alta" name="<?= ARG_OP ?>"/>
                    <input type="hidden" value="<?= $establecimiento->id_establecimiento ?>" name="<?= ARG_ESTID ?>"/>
                </form>
				<?php endif; ?> 
				</div>          
		</div>
		<!-- FIN CAJA AMARILLA -->
		
<?php if($usersData): ?>
        <!-- COMIENZO BLOQUE RESULTADOS DE LA BUSQUEDA -->
		<div id="resultado">
		    <h2 class="titulo_2">Usuarios</h2>
        <div class="subrayado"></div>
		<?php if (count($usersData) > 0) : ?>
                  <!-- tabla de resultados -->
                  <div id="tabla_resultados">
                        <table class="tablaresultado" width="100%">
                          <tr>
                            <th>CODIGO</th>
                            <th>NOMBRE</th>
                            <th>PERMISOS</th>
                          </tr>
                          <?php foreach ($usersData as $row): ?>
                              <tr>
                                <td><?= $row->id ?></td>
                                <td><a href="<?= $page->build_url( $actionEditUserUrl, array( ARG_USER_ID => $row->id)); ?>"><?= $row->username ?></a></td>
                                <td><?= @$userProfiles[ $row->profile ] ?></td>
                              </tr> 
                          <?php endforeach; ?>                                         
                        </table>
                   </div>
        <?php else: ?>
              <div>El establecimiento seleccionado no tiene usuarios dados de alta.</div>
        <?php endif; ?>
        <a href="<?= $actionNewUserUrl ?>" class="ui-button ui-widget ui-state-default ui-corner-all" id="nuevoUsuarioBtn" style="margin: 10px 0px 10px 2px;">Nuevo usuario</a>
        </div>
        <!-- FIN BLOQUE RESULTADOS DE LA BUSQUEDA -->
<?php endif; ?>
       
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->


</div>
<!-- FIN BLOQUE INTERIOR -->