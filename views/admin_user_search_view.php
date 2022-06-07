<script type="text/javascript">
$(document).ready( function() {
	//alert('Hola');
	$("#searchByNombre").button().click(
		function() {
			//e.preventDefault();
			var nv = $("input[name='nombre']")[0];
			
			if (nv.value == "")
			{
				$( "#dialog-modal" ).dialog("open");
				return false;
			}
		}
	);
    $( "#dialog-modal" ).dialog({
    	autoOpen: false,
        resizable: false,
        position: { my: "center", at: "center", of: "#searchByNombre" },
        modal: true,
        buttons: { Ok: function() { $( this ).dialog( "close" ); } }
    });	
	$("input:text:visible:first").focus();
  });
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<div id="dialog-modal" title="Buscar usuario">
	    <p style="text-align: left;">
	    <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
	    Ha de introducir el nombre del usuario o parte de él para poder realizar la búsqueda.</p>
	</div>	
	<h1 class="bloq_central titulo_1"><?= ADMIN_TITLE ?></h1>
	<div class="bloq_central">
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Buscar usuario</h2>
	      <div class="subrayado"></div>
			<form name="buscarform_nombre" action="#" method="post">            
			<table>
              <tr>
                <td colspan="3" class="formhint">Busque el usuario introduciendo su nombre o parte del mismo:</td>
              </tr>
              <tr>
                <td style="padding:10px;width: 200px;"><input name="nombre" type="text" value="<?= @$user_nombre ?>" size="32" maxlength="32"/></td>
                <td><input style="padding: 0.1em 0.8em;font-size: 13px;margin-top: 0px" class="search" id="searchByNombre" type="submit" value="Buscar"> </td>
              </tr>
            </table> 
            <input type="hidden" name="<?= ARG_OP ?>" value="suser" />
            </form>                    
		</div>
		<!-- FIN CAJA AMARILLA -->
		
<?php if($data): ?>
        <!-- COMIENZO BLOQUE RESULTADOS DE LA BUSQUEDA -->
		<div id="resultado">
		    <h2 class="titulo_2">Resultados de la b&uacute;squeda</h2>
        <div class="subrayado"></div>
		<?php if ($data->has_rows()) : ?>
                  <!-- tabla de resultados -->
                  <div id="tabla_resultados">
                        <table class="tablaresultado" width="100%">
                          <tr>
                            <th scope="col">CODIGO</th>
                            <th scope="col">NOMBRE</th>
                            <th scope="col">ESTABLECIMIENTO</th>
                          </tr>
                          <?php foreach ($data as $row): ?>
                              <tr>
                                <td><?= $row['user_id'] ?></td>
                                <td><a href="<?= $page->build_url($navToUrl, array(ARG_USER =>  $row['username'])); ?>"><?= $row['username'] ?></a></td>
                                <td><?= $row['nombre_establecimiento'] ?></td>
                              </tr>
                          <?php endforeach; ?>                                         
                        </table>
                   </div>
        <?php else: ?>
              <div>La búsqueda no produjo ningún resultado.</div>
        <?php endif; ?>
		</div>
        <!-- FIN BLOQUE RESULTADOS DE LA BUSQUEDA -->
<?php endif; ?>
        
	<div style="clear:both;"><a href="javascript:history.back()" class="enlace volvericon">Volver</a></div>
	</div>

</div>
<!-- FIN BLOQUE INTERIOR -->