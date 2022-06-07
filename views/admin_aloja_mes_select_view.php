<script type="text/javascript">
$(document).ready( function() {
	$("input[type='submit']").button();
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

?>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1"><?= USER_TITLE ?></h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Seleccionar mes y año</h2>
	      <div class="subrayado"></div>
	      <?php if ($inNewWindow) : ?>
			<form name="df" action="<?= $navToUrl ?>" method="post" target="_blank">  
		  <?php else: ?>
		  	<form name="df" action="<?= $navToUrl ?>" method="post">
		  <?php endif; ?>          
            <table style="margin-left:10px;width:99%">
				<tr>
				<td style="width:11%;"><label for="mes_sel">Mes:</label></td>
				<td><select name="mes_sel">
                <?php $mes_enc_curso=(date("n")==1 ? 12 : date("n")-1) ?>				
                <?php for ($i = 1; $i <= 12; $i++): ?>
                     <?php $selected = ($i == $mes_enc_curso ? ' selected="selected"' : '') ?>
                     <option value="<?= $i ?>" <?= $selected?>><?= $trims[$i] ?></option>                     
                <?php endfor; ?>
                </select></td>
				</tr>
				<tr>
				<td style="width:11%;"><label for="ano_sel">Año:</label></td>
				<td><select name="ano_sel">
                <?php $ano_enc_curso=($mes_enc_curso==12 ? date("Y")-1 : date("Y")) ?>								
                <?php foreach($anios as $ano): ?>
                     <?php $selected = ($ano == $ano_enc_curso ? ' selected="selected"' : '') ?>                
                     <option value="<?= $ano ?>" <?= $selected?>><?= $ano ?></option>
                <?php endforeach; ?>
                </select></td>
				</tr>
				<tr>
            </table> 
            <input type="hidden" name="estid" value="<?= $estid ?>"/>
			<input style="padding:0.2em 0.5em; margin-top:10px;" 
				role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="Continuar"/>
            </form>                   
		</div>
		<!-- FIN CAJA AMARILLA -->   
		     
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->
	<span style="margin-left:20px"><a href="<?= $site[PAGE_HOME] ?>" class="enlace volvericon">Volver</a></span>

</div>
<!-- FIN BLOQUE INTERIOR -->