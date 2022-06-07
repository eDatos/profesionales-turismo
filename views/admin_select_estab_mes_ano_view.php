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

			$('#tipo_busqueda').val("nombre");
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

			$('#tipo_busqueda').val("codigo");
		}
	);
	$("input:text:visible:first").focus();
	$('[name="enlace"]').click(function(event){
		var m=$('select[name="<?= ARG_MES ?>"]').val();
		var a=$('select[name="<?= ARG_ANO ?>"]').val();
		
		var dest=$(this).attr("href");
		var argumentos=((dest.indexOf("?")!=-1) ? "&":"?") + $.param({<?= ARG_MES ?>: m, <?= ARG_ANO ?>: a});
		$(this).attr("href",dest+argumentos);
	});
	
  });
</script>
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

$mes_enc_curso=(date("n")==1 ? 12 : date("n")-1);
if(isset($mes))
	$mes_enc_curso=$mes;

$ano_enc_curso=($mes_enc_curso==12 ? date("Y")-1 : date("Y"));
if(isset($ano))
	$ano_enc_curso=$ano;
?>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1"><?= USER_TITLE ?><?php if(isset($optit)): ?> <small>(<?= $optit ?>)</small><?php endif; ?></h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
		<form name="select_estab_mes_ano" action="#" method="post">
		<input type="hidden" id="tipo_busqueda" name="tipo_busqueda" value="" />
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Seleccionar mes y año</h2>
	      <div class="subrayado"></div>
            <table style="margin-left:10px;width:99%">
				<tr>
				<td style="width:11%;"><label for="<?= ARG_MES ?>">Mes:</label></td>
				<td><select name="<?= ARG_MES ?>">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                     <?php $selected = ($i == $mes_enc_curso ? ' selected="selected"' : '') ?>
                     <option value="<?= $i ?>" <?= $selected?>><?= $trims[$i] ?></option>                     
                <?php endfor; ?>
                </select></td>
				</tr>
				<tr>
				<td style="width:11%;"><label for="<?= ARG_ANO ?>">Año:</label></td>
				<td><select name="<?= ARG_ANO ?>">
                <?php foreach($anios as $ano): ?>
                     <?php $selected = ($ano == $ano_enc_curso ? ' selected="selected"' : '') ?>                
                     <option value="<?= $ano ?>" <?= $selected?>><?= $ano ?></option>
                <?php endforeach; ?>
                </select></td>
				</tr>
				<tr>
            </table> 
		</div>
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Buscar establecimiento</h2>
	      <div class="subrayado"></div>
			<table width="auto" border="0">
              <tr>
                <td colspan="3" class="formhint">Introduzca el código del establecimiento si lo conoce, si no, búsquelo por su nombre en la casilla siguiente:</td>
              </tr>            
              <tr>
                <td style="padding:10px;width: 200px;"><input name="codigo" type="text" value="<?= @$estab_codigo ?>" size="32" maxlength="32"/></td>
                <td><input style="width:150px;" class="search" id="searchByCodigo" type="submit" value="Buscar por c&oacute;digo"></td>
              </tr>
            </table> 
			<table>
              <tr>
                <td colspan="3" class="formhint">Busque el establecimiento introduciendo el nombre del establecimiento o parte del mismo:</td>
              </tr>
              <tr>
                <td style="padding:10px;width: 200px;"><input name="nombre" type="text" value="<?= @$estab_nombre ?>" size="32" maxlength="32"/></td>
                <td><input class="search" id="searchByNombre" type="submit" value="Buscar por nombre"> </td>
              </tr>
            </table> 
		</div>
		</form>
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
                                <td><a name="enlace" href="<?= $this->build_url( $navToUrl, array(ARG_ESTID => $row['codigo']) ); ?>"><?= $row['nombre'] ?></a></td>
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
	<span style="margin-left:20px"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></span>


</div>
<!-- FIN BLOQUE INTERIOR -->