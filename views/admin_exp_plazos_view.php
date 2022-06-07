<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/dates.js"></script>
<script type="text/javascript">
$(document).ready( function() 
{
	initDatepicker($(".datepicker"), "<?= DateHelper::getDateFormat("datepicker"); ?>");

	$("#df").validate({
		rules: {
			trim1_ini: { 
				rangomeses: { param: [0,11], depends : function(element) { return $("input[name='trim1_check']:checked").val() == 1; }  } 
			},
			trim2_ini: { 
				rangomeses: { param: [2,3], depends : function(element) { return $("input[name='trim2_check']:checked").val() == 1; }  } 
			},	
			trim3_ini: { 
				rangomeses: { param: [5,6], depends : function(element) { return $("input[name='trim3_check']:checked").val() == 1; }  } 
			},	
			trim4_ini: { 
				rangomeses: { param: [8,9], depends : function(element) { return $("input[name='trim4_check']:checked").val() == 1; }  } 
			},			
			trim1_fin: { 
				mayorque: { param: "#trim1_ini", depends : function(element) { return $("input[name='trim1_check']:checked").val() == 1; } },
				rangomeses: { param: [0,11], depends : function(element) { return $("input[name='trim1_check']:checked").val() == 1; }  } },
			trim2_fin: { 
				mayorque: { param: "#trim2_ini", depends : function(element) { return $("input[name='trim2_check']:checked").val() == 1; }},
				rangomeses: { param: [2,3], depends : function(element) { return $("input[name='trim2_check']:checked").val() == 1; }  }  },
			trim3_fin: { 
				mayorque: { param: "#trim3_ini", depends : function(element) { return $("input[name='trim3_check']:checked").val() == 1; }},
				rangomeses: { param: [5,6], depends : function(element) { return $("input[name='trim3_check']:checked").val() == 1; }  }  },
			trim4_fin: { 
				mayorque: { param: "#trim4_ini", depends : function(element) { return $("input[name='trim4_check']:checked").val() == 1; }  } ,
				rangomeses: { param: [8,9], depends : function(element) { return $("input[name='trim4_check']:checked").val() == 1; }  }  }
		},
		messages : {
			trim1_ini: { rangomeses: "El mes debe ser diciembre o enero." },
			trim1_fin: { rangomeses: "El mes debe ser diciembre o enero." },
			trim2_ini: { rangomeses: "El mes debe ser marzo o abril." },
			trim2_fin: { rangomeses: "El mes debe ser marzo o abril." },
			trim3_ini: { rangomeses: "El mes debe ser junio o julio." },
			trim3_fin: { rangomeses: "El mes debe ser junio o julio." },
			trim4_ini: { rangomeses: "El mes debe ser septiembre u octubre." },
			trim4_fin: { rangomeses: "El mes debe ser septiembre u octubre." },			
		},
    	errorPlacement: function(error, element) {
        	error.appendTo(element.parent());
        	error.addClass("validmsg");
    	}
	});
	
	$(".datepicker").datepicker("option", "maxDate", new Date(<?= date('Y') + 1?>,11,31));
	$(".datepicker").datepicker("option", "minDate", new Date(<?= date('Y') - 1?>,0,1));
	
	$("input[type='submit']").button();
	
    $("input[name='trim1_check']").click( function() {
        changeTrimestre("trim1");   
    });
    $("input[name='trim2_check']").click( function() {
        changeTrimestre("trim2");   
    });    
    $("input[name='trim3_check']").click( function() {
        changeTrimestre("trim3");   
    });    
    $("input[name='trim4_check']").click( function() {
        changeTrimestre("trim4");   
    });
    
    changeTrimestre("trim1"); 
    changeTrimestre("trim2"); 
    changeTrimestre("trim3"); 
    changeTrimestre("trim4");   

	<?php if ($show_ok) :?>
	$("#divinfomsg").fadeOut(2500);
	<?php endif; ?>
});

function changeTrimestre(trimprefix)
{
    var name_check = trimprefix + "_check";
    var ini = trimprefix + "_ini";
    var fin = trimprefix + "_fin";
    
	if ($("input[name='" + name_check + "']:checked").val() == 1)
	{
		$("#"+ini).removeAttr('disabled');
		$("#"+fin).removeAttr('disabled');        
	}
	else 
	{
        $("#"+ini).attr('disabled','disabled');
		$("#"+fin).attr('disabled','disabled');        
	}  
}
  
</script>
<?php
$trim1_ischecked = (isset($plazos[1]) && $plazos[1] != null);
if ($trim1_ischecked)
{
    $trim1_fi = $plazos[1]->fecha_inicio->format('d/m/Y');
    $trim1_ff = $plazos[1]->fecha_fin->format('d/m/Y');
}

$trim2_ischecked = (isset($plazos[2]) && $plazos[2] != null);
if ($trim2_ischecked)
{
    $trim2_fi = $plazos[2]->fecha_inicio->format('d/m/Y');
    $trim2_ff = $plazos[2]->fecha_fin->format('d/m/Y');    
}

$trim3_ischecked = (isset($plazos[3]) && $plazos[3] != null);
if ($trim3_ischecked)
{
    $trim3_fi = $plazos[3]->fecha_inicio->format('d/m/Y');
    $trim3_ff = $plazos[3]->fecha_fin->format('d/m/Y');    
}
$trim4_ischecked = (isset($plazos[4]) && $plazos[4] != null);
if ($trim4_ischecked)
{
    $trim4_fi = $plazos[4]->fecha_inicio->format('d/m/Y');
    $trim4_ff = $plazos[4]->fecha_fin->format('d/m/Y');    
}
?>  
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<div class="bloq_central">
		<h1 class="titulo_1">Configuración de plazos de encuesta de expectativas</h1>
        <?php if ($show_ok) :?>
        <div id="divinfomsg">
        <div class="cuadro fondo_verde" style="padding:0px;">
			<span id="infomsg" class="titulo_3 okicon" style="color:#059905;">Cambios guardados</span>
        </div>
        </div>
        <?php elseif (isset($errs)) :?>
        <div class="cuadro fondo_rojo" style="padding:0px;color:#B60000;">
        	<?php foreach($errs as $err_msg): ?>
			<div class="titulo_3 erroricon"><?= $err_msg ?></div>
			<?php endforeach; ?>
        </div>        
		<?php endif; ?>		
		<form id="df" action="#" method="post">
        <input type="hidden" name="<?= ARG_OP ?>" value="save"/>
		<div class="cuadro fondo_gris">
			<h2 class="titulo_2" style="float:left;"><input id="plazoEnabled" name="trim1_check" type="checkbox" value="1" <?=($trim1_ischecked)?"checked='checked'":""?>/> Trimestre 1: Enero, febrero y marzo</h2>
			<div class="subrayado"> </div>
            <table style="margin-left:10px;width:99%">
				<tr>
				<td style="width:21%;"><label for="trim1_ini">Desde el día:</label></td>
				<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" 
                            id="trim1_ini" class="datepicker dateUS" maxlength="10" size="15" name="trim1_ini" type="text" value="<?= @$trim1_fi ?>"/></td>
				</tr>
				<tr>
				<td><label for="trim1_fin">Hasta el día:</label></td>
				<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" 
                            id="trim1_fin" class="datepicker dateUS" maxlength="10" size="15" name="trim1_fin" type="text" value="<?= @$trim1_ff ?>"/></td>
				</tr>
				<tr>
            </table>
			<h2 class="titulo_2" style="float:left;"><input id="plazoEnabled" name="trim2_check" type="checkbox" value="1" <?=($trim2_ischecked)?"checked='checked'":""?>/> Trimestre 2: Abril, mayo y junio</h2>
			<div class="subrayado"> </div>
            <table style="margin-left:10px;width:99%">
				<tr>
				<td style="width:21%;"><label for="trim2_ini">Desde el día:</label></td>
				<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" 
                            id="trim2_ini" class="datepicker dateUS" maxlength="10" size="15" name="trim2_ini" type="text" value="<?= @$trim2_fi ?>"/></td>
				</tr>
				<tr>
				<td><label for="trim2_fin">Hasta el día:</label></td>
				<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" 
                            id="trim2_fin" class="datepicker dateUS" maxlength="10" size="15" name="trim2_fin" type="text" value="<?= @$trim2_ff ?>"/></td>
				</tr>
				<tr>
            </table> 
			<h2 class="titulo_2" style="float:left;"><input id="plazoEnabled" name="trim3_check" type="checkbox" value="1" <?=($trim3_ischecked)?"checked='checked'":""?>/> Trimestre 3: Julio, agosto y septiembre</h2>
			<div class="subrayado"> </div>
            <table style="margin-left:10px;width:99%">
				<tr>
				<td style="width:21%;"><label for="trim3_ini">Desde el día:</label></td>
				<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" 
                            id="trim3_ini" class="datepicker dateUS" maxlength="10" size="15" name="trim3_ini" type="text" value="<?= @$trim3_fi ?>"/></td>
				</tr>
				<tr>
				<td><label for="trim3_fin">Hasta el día:</label></td>
				<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" 
                            id="trim3_fin" class="datepicker dateUS" maxlength="10" size="15" name="trim3_fin" type="text" value="<?= @$trim3_ff ?>"/></td>
				</tr>
				<tr>
            </table>   
			<h2 class="titulo_2" style="float:left;"><input id="plazoEnabled" name="trim4_check" type="checkbox" value="1" <?=($trim4_ischecked)?"checked='checked'":""?>/> Trimestre 4: Octubre, noviembre y diciembre</h2>
			<div class="subrayado"> </div>
            <table style="margin-left:10px;width:99%">
				<tr>
				<td style="width:21%;"><label for="trim4_ini">Desde el día:</label></td>
				<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" 
                            id="trim4_ini" class="datepicker dateUS" maxlength="10" size="15" name="trim4_ini" type="text" value="<?= @$trim4_fi ?>"/></td>
				</tr>
				<tr>
				<td><label for="trim4_fin">Hasta el día:</label></td>
				<td><input autocomplete="off" placeholder="<?= DateHelper::getDateFormat("show") ?>" 
                            id="trim4_fin" class="datepicker dateUS" maxlength="10" size="15" name="trim4_fin" type="text" value="<?= @$trim4_ff ?>"/></td>
				</tr>
				<tr>
            </table>            
			<input style="padding:0.2em 0.5em; margin-top:10px;" 
				role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="Guardar"/>
		</div>  
        </form>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->