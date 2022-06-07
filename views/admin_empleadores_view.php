<link rel="stylesheet" href="css/pwet-theme/jquery-ui-1.9.1.custom.css"/>
<link rel="stylesheet" href="css/empleo_form_view.css"/>
<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/inputfields.js"></script>
<script type="text/javascript" src="js/dates.js"></script>
<style>
.botoneraderecha {
	float: right;
	margin-right: 17px;
}
.btnRefrescar span {
    line-height: normal !important;
}
.ui-btn-refrescar {
    background-image: url(images/refresh.png) !important;
    width: 16px;
    height: 16px;
    padding: 0px;
    background-repeat: no-repeat;
    background-position: center;
}
td.fit {
    width: 1%;
    white-space: nowrap;
}
td.fit2 {
    width: 1%;
    padding-left: 15px !important;
    padding-right: 15px !important;
    white-space: nowrap;
}
.alineado {
	margin: 2px 17px;
}
#detalleErrores {
	padding-left: 40px;
}
#detalleErrores ul {
	list-style-type: circle;
	list-style-position: inside;
}
.tablaresultado tr:nth-child(4n+1) {
	background-color: #FBFBFB;
}
.tablaresultado tr:nth-child(4n+2) {
	background-color: #F2F2F2;
}
.tablaresultado tbody tr td input {
    box-sizing: border-box;
}
.notas_plazo {
    display:block;
    margin-right: 2px;
    padding: 0 5px 0 5px;
}
</style>
<?php
define("ESTILO_OK", "okicon");
define("ESTILO_ERROR", "erroricon");

?>
<script type="text/javascript">
var navPageURL="<?= PAGE_ALOJA_PLAZOS_EXCEP_AJAX ?>";
var detalles=false;
var reglasValidacionEmpleadoresInternos;
var reglasValidacionEmpleadoresExternos;
function toggleDetalles()
{
	document.getElementById('detalleErrores').style.display=(detalles)?'none':'block';
	detalles=!detalles;
}
function limpiarCCC(ccc)
{
    var digitos="";
    var i;
    for(i=0;i<ccc.length;i++)
    {
        var letra=ccc.substr(i,1);
        if((letra[0]>='0')&&(letra[0]<='9'))
        	digitos+=letra;
    }
    return digitos;
}
function changeExternoInterno(esEmpleadorExterno)
{
	$('.empleadorExterno').toggle(esEmpleadorExterno);
	$('.empleadorInterno').toggle(esEmpleadorExterno==false);
	for(var regla in reglasValidacionEmpleadoresInternos)
	{
		if(esEmpleadorExterno)
		{
			//$('#'+regla).rules('remove');
			$("#df").rules('remove',regla);
		}
		else
		{
			//$('#'+regla).rules('add',reglasValidacionEmpleadoresInternos[regla]);
			$("#df").rules('add',regla);
		}
	}
	for(var regla in reglasValidacionEmpleadoresExternos)
	{
		if(esEmpleadorExterno)
		{
			//$('#'+regla).rules('add',reglasValidacionEmpleadoresExternos[regla]);
			$("#df").rules('add',regla);
		}
		else
		{
			//$('#'+regla).rules('remove');
			$("#df").rules('remove',regla);
		}
	}
}
$(document).ready( function() {
	jQuery.validator.addMethod(
            "nofutura",
            function (value, element) {
                var hoy=new Date();
                return parseDate(value) <= hoy;
            },
            "La fecha inicial no puede ser posterior a la actual"
        );

	jQuery.validator.addMethod(
            "CCC_CodProv",
            function (value, element) {
                var digitos=limpiarCCC(value);
                var codProv=parseInt(digitos.substr(0,2));
                return (((codProv>=1)&&(codProv<=53))||(codProv==66));
            },
            "El código de provincia de la cuenta de cotización no es válida."
        );

	jQuery.validator.addMethod(
            "CCC_DC",
            function (value, element) {
            	var digitos=limpiarCCC(value);
                if(digitos.substr(2,1)=='0')
                    digitos=digitos.substr(0,2)+digitos.substr(3, digitos.length-3);
                var dc = parseInt(digitos.substr(0,digitos.length-2)) % 97;
                var digitosControl="";
                if (dc <= 9)
                	digitosControl="0"+dc;
                else
                	digitosControl=""+dc;
            	return digitos.endsWith(digitosControl);
            },
            "La cuenta de cotización no es válida. Los dígitos de control no coinciden."
        );

	jQuery.validator.addMethod(
            "CCC_longitud",
            function (value, element) {
            	var digitos=limpiarCCC(value);
                return ((digitos.length==11)||(digitos.length==12));
            },
            "La cuenta de cotización debe contener 11(CCC) ó 12(NAF) dígitos. Se admiten separadores."
        );

    reglasValidacionEmpleadoresInternos={
		<?=ARG_CUENTA_COTIZACION?>: {
			required: true,
			CCC_CodProv: true,
			CCC_longitud: true,
			CCC_DC: true
		},
		<?=ARG_DESCRIPCION?>: "required"
    };

    reglasValidacionEmpleadoresExternos={
		<?=ARG_ID_EMPLEADOR?>: {
			required: true/*,
			CCC_CodProv: true,
			CCC_longitud: true,
			CCC_DC: true*/
		},
		<?=ARG_NOMBRE_EMPRESA?>: "required"
    };
	$("#df").validate({
		rules: {},
		wrapper: "p",
    	errorPlacement: function(error, element) {
        	error.appendTo(element.parent());
        	error.addClass("validmsg");
    	}
	});
	$.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
	$(".datepicker").datepicker( { dateFormat: "<?= DateHelper::getDateFormat("datepicker") ?>" } );
	
	<?php if (count($data) > 0) : ?>
	$("input[type='submit'][value='<?= OP_ELIMINAR ?>']").button().click( function(e) {
		var identificacion=$(this).closest('tr').children('td').eq(<?= /*$es_admin ? 1:0*/ 0 ?>).text();
		var externa=$(this).closest('tr').find('input[name="<?=ARG_ES_EXT?>"]').val();
		var mensaje;
		if(externa=='<?= EMPLEADOR_EXTERNO ?>')
		{
			var nombre_empresa=$(this).closest('tr').find('input[name="<?= ARG_NOMBRE_EMPRESA ?>"]').val();
			mensaje="Atención\n\n Se dispone a eliminar el empleador externo:\n\t'("+identificacion+") "+nombre_empresa+"'\n¿Desea continuar?";
		}
		else
		{
			var descripcion=$(this).closest('tr').find('input[name="<?= ARG_DESCRIPCION ?>"]').val();
			mensaje="Atención\n\n Se dispone a eliminar la cuenta de cotización:\n\t'("+identificacion+") "+descripcion+"'\n¿Desea continuar?";
		}
		if(confirm(mensaje)==false)
		{
			e.preventDefault();
			return false;
		}
	});
	<?php endif; ?>
	
	$('#ampliar').click(function(event){
		toggleDetalles();
	});

	//$('#<?=ARG_ES_EXT?>').val(false);
	$('#<?=ARG_ES_EXT?>').prop('checked', false);
	changeExternoInterno(false);
	$('#<?=ARG_ES_EXT?>').change(function(){
		// En función del estado anterior, mostramos unos elementos u otros.
		changeExternoInterno(this.checked);
	});

	$(".btnRefrescar").button({icons: {primary: 'ui-btn-refrescar'}});
});
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1">Gestión módulo de empleo</h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
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
							$mensaje = "Error al guardar los datos";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Cambios guardados";
						}
						break;
					case OP_ELIMINAR:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al eliminar el empleador";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Empleador eliminado";
						}
						break;
					case OP_ELIMINAR_TODOS:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al eliminar el(los) empleador(es)";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Empleador(es) eliminado(s)";
						}
						break;
					case OP_CONTAR:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al eliminar el(los) empleador(es)";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = $listaErrores[0];
						}
						break;
				}
				echo '<div class="'.(($error)?'pagemsg_error':'pagemsg_success').'"><span id="infomsg" class="titulo_3 ' . $estilo_msg . '">' . $mensaje . '</span>';
				if($error)
					echo '<img id="ampliar" class="botoneraderecha alineado" src="images/detalles.png"/>';
				if(!empty($listaErrores))
				{
					echo '<div id="detalleErrores" style="display:none"><ul>';
					foreach ($listaErrores as $linea)
						echo '<li>'.$linea.'</li>';
					echo '</ul></div>';
				}
				echo '</div>';
			}
		?>
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">
		  <!-- <span  class="empleadorExterno">Nueva empresa de trabajo temporal</span><span  class="empleadorInterno">Nueva cuenta de cotización</span> -->
		  <span>Nueva cuenta de cotización / empresa de trabajo temporal (ETT)</span>
		  </h2>
		  <span>Introduzca los datos de la nueva cuenta de cotización ó ETT según corresponda.</span>
		  <!-- 
		  <span  class="empleadorExterno">Seleccione las ETT de las que tiene personal en el mes de referencia para introducir, en el siguiente paso, la información del número de empleados. Si es necesario puede añadir nuevas ETT en el apartado superior "Nueva Empresa de Trabajo Temporal".</span>
		  <span  class="empleadorInterno">Introduzca los datos de las cuentas de cotización a través de las que el establecimiento cotiza por sus empleados.</span>
		  -->
	      <div class="subrayado"></div>
	        <form name="df" id="df" action="<?= $navToUrl ?>" method="post">
	        <input name="estid" type="hidden" value="<?= $query_id_est; ?>">
		    <table style="margin-left:10px;width:99%">
		    	<tr>
    				<td style="width:20%;"><span>Establecimiento:</span></td>
    				<td><span style="font-size: 1.2em;font-weight: bold;width:180px;margin-left:2px;"><?= '('.$query_id_est.') '.$nombre_establecimiento;?></span></td>
				</tr>
				<tr>
					<td style="width:20%;"><label for="<?=ARG_ES_EXT?>">¿Es una ETT?:</label></td>
					<td><input type="checkbox" style="margin-left:2px;" id="<?=ARG_ES_EXT?>" name="<?=ARG_ES_EXT?>" value="<?= EMPLEADOR_EXTERNO ?>"></td>
				</tr>
		    	<tr class="empleadorExterno">
    				<td style="width:20%;"><label for="<?=ARG_ID_EMPLEADOR?>">Identificación (CIF,NIF,NIE):</label></td>
    				<td><input type="text" style="width:180px;margin-left:2px;" name="<?=ARG_ID_EMPLEADOR?>" placeholder="#########"/></td>
				</tr>
				<tr class="empleadorExterno">
					<td style="width:20%;"><label for="<?=ARG_NOMBRE_EMPRESA?>">Nombre de la Empresa:</label></td>
					<td><input type="text" maxlength="90" style="margin-left:2px;width:400px;" name="<?=ARG_NOMBRE_EMPRESA?>"/></td>
				</tr>
		    	<tr class="empleadorInterno">
    				<td style="width:20%;"><label for="<?=ARG_CUENTA_COTIZACION?>">Cuenta de cotización:</label></td>
    				<td><input type="text" style="width:180px;margin-left:2px;" name="<?=ARG_CUENTA_COTIZACION?>" placeholder="##-#######-##"/></td>
				</tr>
				<tr class="empleadorInterno">
					<td style="width:20%;"><label for="<?=ARG_DESCRIPCION?>">Descripción:</label></td>
					<td><input type="text" maxlength="90" style="margin-left:2px;width:400px;" name="<?=ARG_DESCRIPCION?>"/></td>
				</tr>
            </table>
            <div>
			<input name="operationBtn" style="padding:0.2em 0.5em; margin-top:10px;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="<?= OP_INSERTAR ?>"/>
            </div>
            </form>                   
		</div>
		<!-- FIN CAJA AMARILLA --> 
        <!-- COMIENZO BLOQUE RESULTADOS DE LA BUSQUEDA -->
		<div id="resultado">
			<div>
		    <h2 style="display: inline-block" class="titulo_2">Empleadores registrados</h2>
		    <div style="float:right; margin-right:10px;">
		    	<span style="font-size: 1.0em;font-weight: bold;width:180px;margin-left:20px; align=left"><?= '('.$query_id_est.') '.$nombre_establecimiento;?></span>
		    	<a role="button" title="Refrescar" class="btnRefrescar"  href="<?= $navToUrl ?>" style="margin: 0 0 2px 10px">Refrescar</a>
		    </div>
		    </div>
        <div class="subrayado"></div>
		<?php if (count($data) > 0) : ?>
                  <!-- tabla de resultados -->
                  <div id="tabla_resultados">
                        <table class="tablaresultado" width="100%">
                          <tr>
                            <?php if($es_admin): ?>
                            <!-- <th scope="col">ESTABLECIMIENTO</th> -->
                            <?php endif; ?>
                            <th scope="col">IDENTIFICACIÓN</th>
                            <th scope="col">DESCRIPCIÓN</th>
                            <th scope="col">ETT</th>
                            <th scope="col">ACTIVA</th>
                            <th scope="col">OPERACIONES</th>
                          </tr>
                          <?php $nresultado=0; ?>
                          <?php foreach ($data as $row): ?>
                              <tr>
                                <?php if($es_admin): ?>
                              	<!-- <td><?= '('.$row->id_establecimiento.') '.$row->nombre_establecimiento ?></td> -->
                                <?php endif; ?>
                              	<td class="fit2" title="<?= $row->id_empleador->getTipoLargo() ?>"><?= $row->id_empleador->toString() ?></td>
                                <form name="dr" action="<?= $navToUrl ?>" method="post">
                                <input name="<?=ARG_ES_EXT?>" type="hidden" value="<?= $row->externo; ?>">
                                <?php  if($row->externo==EMPLEADOR_EXTERNO) : ?>
                                	<input name="<?=ARG_ID_EMPLEADOR?>" type="hidden" value="<?= $row->formatear(); ?>">
                                	<td><input name="<?=ARG_NOMBRE_EMPRESA?>" id="<?=ARG_NOMBRE_EMPRESA?>" type="text" class="" maxlength="90" style="width:100%;" value="<?= $row->descripcion; ?>"></td>
                                	<td class="fit">Sí</td>
                                <?php else: ?>
                                	<input name="<?=ARG_CUENTA_COTIZACION?>" type="hidden" value="<?= $row->formatear(); ?>">
                                	<td><input name="<?=ARG_DESCRIPCION?>" id="<?=ARG_DESCRIPCION?>" type="text" class="" maxlength="90" style="width:100%;" value="<?= $row->descripcion; ?>"></td>
                                	<td class="fit">No</td>
                                <?php endif; ?>
                              	<td class="fit2"><input name="<?=ARG_ACTIVA?>" id="<?=ARG_ACTIVA?>" type="checkbox" value="<?=EMPLEADOR_ACTIVO?>" data-prev="<?=($row->estado==EMPLEADOR_ACTIVO)?'true':'false' ?>" <?=($row->estado==EMPLEADOR_ACTIVO)?'checked':'' ?>/></td>
                              	<td class="fit">
                                <div class="botoneraderecha">
                                <input name="operationBtn" style="padding:0.2em 0.5em;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="<?= OP_CONTAR ?>" title="Contar el número de cuestionarios donde aparece este empleador" />
                                <input name="operationBtn" style="padding:0.2em 0.5em;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="<?= OP_MODIFICAR ?>" title="Guardar las modificaciones a este empleador" />
                                <input name="operationBtn" style="padding:0.2em 0.5em;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" aria-disabled="false" type="submit" value="<?= OP_ELIMINAR ?>" title="Eliminar este empleador" />
								</div>
            				    </td>
								</form>
            				   </tr>
                              <?php $nresultado++; ?>
                          <?php endforeach; ?>                                         
                        </table>
                   </div>
        <?php else: ?>
              <div>No hay ningún empleador definido.</div>
        <?php endif; ?>
		</div>
        <!-- FIN BLOQUE RESULTADOS DE LA BUSQUEDA -->       
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->


</div>
<div id="dialog-notas" title="Notas del plazo excepcional" >
	<div id="msg_notas" style="text-align: left"></div>
</div>
<div id="dialog-notas-aviso" title="Notas del plazo excepcional" >
	<div id="msg_notas-aviso" style="text-align: left;margin-top:15px;"></div>
</div>
<!-- FIN BLOQUE INTERIOR -->