<script type="text/javascript">
$(document).ready( function() {
	$("#searchByNombre").button().click(
		function() {
			//e.preventDefault();
			var nv = $("input[name='nombre']")[0];
			
			if (nv.value == "")
			{
				alert("Ha de introducir parte del nombre del establecimiento para realizar la búsqueda");                
            	return false;
			}
		}
	);
	$("#searchByCodigo").button().click(
		function() {
			//e.preventDefault();
			var cv = $("input[name='codigo']")[0];
			
			if (cv.value == "")
			{
            	alert("Ha de introducir el código de un establecimiento para realizar la búsqueda");        
            	return false;
			}
		}
	);
	$("input:text:visible:first").focus();
  });
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1"><?= USER_TITLE ?><?php if(isset($optit)): ?> <small>(<?= $optit ?>)</small><?php endif; ?></h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Buscar establecimiento</h2>
	      <div class="subrayado"></div>
			<form name="buscarform_codigo" action="#" method="post">            
			<table width="auto" border="0">
              <tr>
                <td colspan="3" class="formhint">Introduzca el código del establecimiento si lo conoce, si no, búsquelo por su nombre en la casilla siguiente:</td>
              </tr>            
              <tr>
                <td style="padding:10px;width: 200px;"><input name="codigo" type="text" value="<?= @$estab_codigo ?>" size="32" maxlength="32"/></td>
                <td><input style="width:150px;" class="search" id="searchByCodigo" type="submit" value="Buscar por c&oacute;digo"></td>
              </tr>
            </table> 
            <input type="hidden" name="tipo_busqueda" value="codigo" />
          	</form>           
			<form name="buscarform_nombre" action="#" method="post">            
			<table>
              <tr>
                <td colspan="3" class="formhint">Busque el establecimiento introduciendo el nombre del establecimiento o parte del mismo:</td>
              </tr>
              <tr>
                <td style="padding:10px;width: 200px;"><input name="nombre" type="text" value="<?= @$estab_nombre ?>" size="32" maxlength="32"/></td>
                <td><input class="search" id="searchByNombre" type="submit" value="Buscar por nombre"> </td>
              </tr>
            </table> 
            <input type="hidden" name="tipo_busqueda" value="nombre" />
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
                            <th scope="col">ISLA</th>
                            <th scope="col">MUNICIPIO</th>
                          </tr>
                          <?php foreach ($data as $row): ?>
                              <tr>
                                <td><?= $row['codigo'] ?></td>
                                <td><a href="<?= $this->build_url( $navToUrl, array(ARG_ESTID => $row['codigo']) ); ?>"><?= $row['nombre'] ?></a></td>
                                <td><?= $row['isla'] ?></td>
                                <td><?= $row['nombre_municipio'] ?></td>
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
        
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->


</div>
<!-- FIN BLOQUE INTERIOR -->