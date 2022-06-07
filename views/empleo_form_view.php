<link rel="stylesheet" href="css/pwet-theme/jquery-ui-1.9.1.custom.css"/>
<link rel="stylesheet" href="css/empleo_form_view.css"/>

<script type="text/javascript" src="js/jquery-ui-1.9.1.custom.js"></script>
<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/inputfields.js"></script>


<?php 
/*
 * Fases:
 *  0 = manejo de empleadores internos (cuentas de cotización)
 *  1 = cuestionario de empleados de empleadores internos
 *  2 = manejo de empleadores externos (ETTs, etc.)
 *  3 = cuestionario de empleados de empleadores externos
 */
?>
<?php if(($fase==0)||($fase==2)):
	/**************************/
	/* Manejo de empleadores  */
    /* ($fase==0 || $fase==2) */
	/**************************/
?>
<?php

define("ESTILO_OK", "okicon");
define("ESTILO_ERROR", "erroricon");

?>
<script type="text/javascript" src="js/empleadores_form_view.js"></script>
<script type="text/javascript">
arg_id_empleador='<?= ARG_ID_EMPLEADOR ?>';
arg_activa='<?= ARG_ACTIVA ?>';
etiqueta_tipo_establecimientos='<?=($es_hotel ? "Hoteleros" : "Extrahoteleros" )?>';
op_btnSiguiente='<?= ($fase==0) ? OP_GUARDAR_EMPLEADORES_INTERNOS : OP_GUARDAR_EMPLEADORES_EXTERNOS; ?>';
mes_encuesta = <?= isset($mes_encuesta) ? $mes_encuesta : date('m') ?>;
ano_encuesta = <?= isset($ano_encuesta) ? $ano_encuesta : date('Y') ?>;
nombre_establecimiento = "<?= $nombre_establecimiento ?>";
navPageURL="<?= $navpage_url; ?>";
navSgteURL="<?= $navSgteURL; ?>";
navPrevURL="<?= $navPrevURL; ?>";
numero_filas=<?= count($data) ?>;
soloLectura = <?= $solo_lectura ? "true" : "false" ?>;
detalles=false;
nEmpleadoresActivos=0;
externos= <?= (($fase==2)||($fase==3)) ? 'true':'false' ?>;
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1">Gestión módulo de empleo</h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">
			<?php
			if($operacion!='')
			{
			    switch($operacion)
			    {
			        case OP_INSERTAR:
			        case OP_MODIFICAR:
			            if($error)
			            {
			                $estilo_msg = ESTILO_ERROR;
			                $mensaje = "Error al guardar el empleador";
			            }
			            else
			            {
			                $estilo_msg = ESTILO_OK;
			                $mensaje = "Empleador guardado";
			            }
			            break;
			    }
			    echo '<div class="'.(($error)?'pagemsg_error':'pagemsg_success').'"><span id="infomsg" class="titulo_3 ' . $estilo_msg . '">' . $mensaje . '</span>';
			    echo '<img id="ocultar" class="botoneraderecha alineado" src="images/cross.png"/>';
			    if($error)
			    {
			        echo '<img id="ampliar" class="botoneraderecha alineado" src="images/detalles.png"/>';
			    }
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
		<div><h2 class="titulo_2" style="float:left;">Encuesta sobre el Empleo en Establecimientos <?=($es_hotel ? "Hoteleros" : "Extrahoteleros" )?>: <?= DateHelper::mes_tostring( $empleo_enc->mes,'M') ?> de <?= $empleo_enc->ano ?></h2>
		<div style="float:right;">Paso <?= ($fase+1) ?> de 4</div></div><div style="clear:both"></div>
    	<?php if(!$solo_lectura):?>
        <div class="cuadro fondo_rojo_claro" style="font-size:95%;padding:2px 2px 2px 4px;">
    		<b>ATENCIÓN: Grabe con frecuencia para evitar perder el trabajo.</b> Por motivos de seguridad los servidores cierran las sesiones que parecen inactivas.
    	</div>
    	<?php endif ?>
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2"><?= ($exts) ? 'Nueva empresa de trabajo temporal (ETT)':'Nueva Cuenta de Cotización'; ?></h2>
		  <span><?= ($exts) ? 'Introduzca los datos de las empresas de trabajo temporal de las que tiene empleados.':'Introduzca los datos de las cuentas de cotización a través de las que el establecimiento cotiza por sus empleados.'; ?></span>
	      <div class="subrayado"></div>
	        <form name="df" id="df" action="<?= $navToUrl ?>" method="post">
	        <input name="estid" type="hidden" value="<?= $query_id_est; ?>">
		    <table style="margin-left:10px;width:99%">
		        <?php  if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC))) : ?>
    		    	<tr>
        				<td style="width:20%;"><span>Establecimiento:</span></td>
        				<td><span style="font-size: 1.2em;font-weight: bold;width:180px;margin-left:2px;"><?= '('.$query_id_est.') '.$nombre_establecimiento;?></span></td>
    				</tr>
		        <?php endif; ?>
		    	<tr>
    				<td style="width:20%;"><label for="<?=ARG_ID_EMPLEADOR?>"><?= ($exts) ? 'Identificación (CIF,NIF,NIE)' : 'Cuenta de Cotización' ?>:</label></td>
    				<td><input type="text" style="width:180px;margin-left:2px;" name="<?=ARG_ID_EMPLEADOR?>" placeholder="<?= ($exts) ? '#########' : '##-#######-##' ?>"/></td>
				</tr>
				<tr>
					<td style="width:20%;"><label for="<?=ARG_NOMBRE_EMPRESA?>"><?= ($exts) ? 'Nombre de la Empresa' : 'Descripción' ?>:</label></td>
					<td><input type="text" maxlength="90" style="margin-left:2px;width:400px;" name="<?=ARG_NOMBRE_EMPRESA?>"/></td>
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
			<h2 style="display: inline-block" class="titulo_2"><?= ($exts)? 'Empleadores externos':'Cuentas de cotización'; ?></h2><br>
			<span><?= ($exts)? 'Seleccione las ETT de las que tiene personal en el mes de referencia para introducir, en el siguiente paso, la información del número de empleados. Si es necesario puede añadir nuevas ETT en el apartado superior "Nueva Empresa de Trabajo Temporal".':'Seleccione las cuentas de cotización activas para introducir, en el siguiente paso, la información de empleados por las que se cotiza en cada una. Si es necesario puede añadir nuevas cuentas de cotización en el apartado superior "Nueva Cuenta de Cotización".'; ?></span>
		    </div>
        <div class="subrayado"></div>
        <div><br></div>
		<?php if (count($data) > 0) : ?>
        <form id="af" action="<?= $navToUrl ?>" method="post">
            <div id="tabs">
                <div class="div_calendario">
        			<span class="avisobackup-label avisobackup-initiated avisobackup-hide">Guardando...</span>
        			<span class="avisobackup-label avisobackup-ok avisobackup-hide">Guardado</span>
        			<span class="avisobackup-label avisobackup-error avisobackup-hide">Fallo</span>
        			<span class="avisobackup-label avisobackup-info avisobackup-hide">Fallo</span>
        			<span id="indicadorbusy" style="display:none;" title="Grabación automática deshabilitada"></span>
                </div>
                  <!-- tabla de resultados -->
                  <div id="tabla_resultados">
                        <table class="tablaresultado" width="100%">
                          <tr>
                          <?php  if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC))) : ?>
                            <th scope="col">ESTABLECIMIENTO</th>
                          <?php endif; ?>
                          <?php if($exts) : ?>
                          	<th scope="col" class="fit">NIF</th>
                          	<th scope="col">EMPRESA</th>
                          <?php else : ?>
                          	<th scope="col" class="fit">CUENTA COTIZACIÓN</th>
                          	<th scope="col">DESCRIPCIÓN</th>
                          <?php endif; ?>
                          	<th scope="col">ACTIVA</th>
                          </tr>
                          <?php $nresultado=0; ?>
                          <?php foreach ($data as $row): ?>
                              <tr id="fila_<?= $nresultado ?>">
                              <?php  if($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC))) : ?>
                              	<td><?= '('.$row->id_establecimiento.') '.$row->nombre_establecimiento ?></td>
                              <?php endif; ?>
                              	<td class="fit2" title="<?= $row->id_empleador->getTipoLargo() ?>"><?= $row->id_empleador->toString() ?></td>
                                <input name="<?=ARG_ESTID?>_<?= $nresultado ?>" type="hidden" value="<?= $row->id_establecimiento; ?>">
                                <input name="<?=ARG_ID_EMPLEADOR?>_<?= $nresultado ?>" type="hidden" value="<?= $row->formatear(); ?>">
                              	<td><input name="desc_<?= $nresultado ?>" id="desc_<?=$nresultado?>" type="text" class="" maxlength="90" style="width:100%;" data-prev="<?= $row->descripcion; ?>" value="<?= $row->descripcion; ?>"></td>
                              	<td class="fit2"><input name="<?=ARG_ACTIVA?>_<?= $nresultado ?>" id="<?=ARG_ACTIVA?>_<?= $nresultado?>" type="checkbox" value="<?=EMPLEADOR_ACTIVO?>" data-prev="<?=($row->estado==EMPLEADOR_ACTIVO)?'true':'false' ?>" <?=($row->estado==EMPLEADOR_ACTIVO)?'checked':'' ?>/></td>
            				   </tr>
                              <?php $nresultado++; ?>
                          <?php endforeach; ?>                                         
                        </table>
                   </div>
               </form>
        <?php else: ?>
              <div>No hay ningún empleador definido.</div>
        <?php endif; ?>
		</div>
        <!-- FIN BLOQUE RESULTADOS DE LA BUSQUEDA -->       
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->
	
	<div style="float:left;<?php if($solo_lectura):?>padding-top: 15px;<?php endif; ?>">
		<a href="<?=$urlBack?>"><img src="images/volver.png" border="0"></a> <a href="<?=$urlBack?>" class="enlace" style=" position: relative; top: -2px;"><?= ($solo_lectura) ? 'Volver':'Salir de la encuesta' ?></a>
		<?php if(!$solo_lectura):?>
			<?php if($fase==0): ?>
				<input class="search btn_seguir" name="guardarBtn" type="button" value="Siguiente"/>
			<?php else: ?>
				<input class="search btn_seguir" name="anteriorBtn" type="button" value="Anterior"/>
				<input class="search btn_seguir" name="guardarBtn" type="button" value="Siguiente"/>
			<?php endif ?>
		<?php endif ?>
		<?php if(isset($empleo_plazo)): ?>
		<span style="padding-left:10px;">
			Fecha límite: <font class="tx_marcado"><?= Datehelper::fecha_tostring($empleo_plazo,true) ?></font>
		</span>
		<?php endif ?>		
	</div>

</div>
<!-- FIN BLOQUE INTERIOR -->			

<div id="dialog-detail" title="Envío de encuesta" >
	<div id="msg_errores" style="text-align: left"></div>
</div>
<div id="dialog-print" title="Imprimir encuesta" >
    <div id="msg_impresion" style="text-align: left;"></div>
</div>

<?php else:
	/*************************************/
	/* Formulario cuestionario de empleo */
    /*     ($fase==1 || $fase==3)        */
    /*************************************/
?>
<script type="text/javascript" src="js/empleo_form_view.js"></script>
<script type="text/javascript">
	soloLectura = <?= $solo_lectura ? "true" : "false" ?>;
	navPageURL="<?= $navpage_url; ?>";
	printFormURL="<?=$urlPrintForm;?>";
	navSgteURL="<?= $navSgteURL; ?>";
	navPrevURL="<?= $navPrevURL; ?>";

	numero_filas=<?= count($data) ?>;

	mes_encuesta = <?= $empleo_enc->mes ?>;
	ano_encuesta = <?= $empleo_enc->ano ?>;
	nombre_establecimiento = "<?= $nombre_establecimiento ?>";
	
	es_admin = <?= $es_admin ? "true":"false" ?>;
	externos = <?= (($fase==2)||($fase==3)) ? 'true':'false' ?>;

    sumaTotal=<?php
	   $nresultado=0;
	   foreach ($data as $id_empleador => $datos)
	   {
	       $valor=($solo_lectura)? $datos->num_empleados : $datos->num_empleados_anterior;
	       if($valor > 0)
	           $nresultado += $valor;
	   }
	   echo $nresultado;
    ?>;
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1">Gestión módulo de empleo</h1>
	<?php
	/*
		if($es_admin)
		{
		    switch($empleo_enc->tipo_carga)
			{
				case TIPO_CARGA_XML:
					echo '<div class="tipo_carga"><img class="tipo_carga_xml" title="Xml" src="images/xml.png"></div>';
					break;
				case TIPO_CARGA_WEB:
					echo '<div class="tipo_carga"><img class="tipo_carga_xml" title="Web" src="images/web.png"></div>';
					break;
				case TIPO_CARGA_FAX:
					echo '<div class="tipo_carga"><img class="tipo_carga_xml" title="Fax" src="images/fax.png"></div>';
					break;
			}			
		}
		*/
	?>
	<form id="ef" method="POST" action="<?= $site[PAGE_EMPLEO_FORM_ENVIO]; ?>">
		<input type="hidden" name="<?= ARG_MES ?>" value="<?= $empleo_enc->mes ?>">
		<input type="hidden" name="<?= ARG_ANO ?>" value="<?= $empleo_enc->ano ?>">
	</form>
	<div><h2 class="titulo_2" style="float:left;">Encuesta sobre el Empleo en Establecimientos <?=($es_hotel ? "Hoteleros" : "Extrahoteleros" )?>: <?= DateHelper::mes_tostring( $empleo_enc->mes,'M') ?> de <?= $empleo_enc->ano ?></h2>
	<div style="float:right;">Paso <?= ($fase+1) ?> de 4</div></div><div style="clear:both"></div>
    <?php if (count($data) > 0) : ?>
    	<?php if(!$solo_lectura):?>
        <div class="cuadro fondo_rojo_claro" style="font-size:95%;padding:2px 2px 2px 4px;">
    		<b>ATENCIÓN: Grabe con frecuencia para evitar perder el trabajo.</b> Por motivos de seguridad los servidores cierran las sesiones que parecen inactivas.
    	</div>
    	<?php endif ?>
		<div id="resultado">
			<div>
			<h2 style="display: inline-block" class="titulo_2"><?= ($exts)? 'Número de empleados externos':'Número de empleados propios'; ?></h2><br>
			<span><?= ($exts)? 'Introduzca los datos del número de empleados contratados a cada ETT.':'Introduzca los datos del número de empleados por los que se cotiza en cada cuenta en el mes de referencia.'; ?></span>
		    </div>
        <div class="subrayado"></div>
        <div><br></div>
        <form id="af" method="POST">
        <div id="tabs">
            <div class="div_calendario">
    			<span class="avisobackup-label avisobackup-initiated avisobackup-hide">Guardando...</span>
    			<span class="avisobackup-label avisobackup-ok avisobackup-hide">Guardado</span>
    			<span class="avisobackup-label avisobackup-error avisobackup-hide">Fallo</span>
    			<span class="avisobackup-label avisobackup-info avisobackup-hide">Fallo</span>
    			<span id="indicadorbusy" style="display:none;" title="Grabación automática deshabilitada"></span>
            </div>
            <div>
                <table class="tablaresultado" width="100%">
                  <tr>
                    <th scope="col" width="15%">ID</th>
                    <th scope="col">EMPRESA</th>
                    <?php if(!$solo_lectura):?><th scope="col" width="10%">MES ANTERIOR</th><?php endif ?>
                    <th scope="col" width="10%">Nº EMPLEADOS</th>
                  </tr>
                  <?php $nresultado=0; ?>
                      <?php foreach ($data as $empleador => $datos): ?>
                          <tr id="fila_<?= $nresultado ?>">
                          	<td id="cc_<?= $nresultado ?>" data-cc="<?= $datos->id_empleador ?>"><?= $datos->id_empleador_display ?></td>
                          	<td><?= $datos->descripcion ?></td>
                          	<?php if(!$solo_lectura):?>
                          		<td><?=($datos->num_empleados_anterior=='-1')?'N/A':$datos->num_empleados_anterior ?></td>
                          		<td><input name="ne_<?=$nresultado?>" id="ne_<?=$nresultado?>" type="text" class="" maxlength="5" style="width:40px;" value="<?=($datos->num_empleados=='-1')?'':$datos->num_empleados ?>"></td>
                          	<?php else: ?>
                          		<td><?=($datos->num_empleados=='-1')?'N/A':$datos->num_empleados ?></td>
                          	<?php endif ?>
        				   </tr>
                          <?php $nresultado++; ?>
                      <?php endforeach; ?>
                  <tr>
					<td colspan="<?= (!$solo_lectura)?2:2 ?>" style="text-align: right !important; padding-right: 20px; font-weight: bold;"><?= (!$solo_lectura)?'TOTALES: ':'TOTAL: ' ?></td>
					<?php if(!$solo_lectura):?>
						<th id="sumaTotalAnterior" scope="col" width="10%">ETT</th>
					<?php endif ?>
					<th id="sumaTotal" scope="col" width="10%">ETT</th>
                  </tr>
                </table>
            </div>
    	</div>
    	</form>  
    	<div style="float:left;<?php if($solo_lectura):?>padding-top: 15px;<?php endif; ?>">
    		<a href="<?=$urlBack?>"><img src="images/volver.png" border="0"></a> <a href="<?=$urlBack?>" class="enlace" style=" position: relative; top: -2px;"><?= ($solo_lectura) ? 'Volver':'Salir de la encuesta' ?></a>
    		<?php if(!$solo_lectura):?>
    			<input class="search btn_seguir" name="anteriorBtn" type="button" value="Anterior"/>
    			<?php if($fase==1): ?>
    				<input class="search btn_seguir" name="guardarBtn" type="button" value="Siguiente"/>
    			<?php else: ?>
    				<input class="search btn_guardar_enviar" name="enviarBtn" type="button" value="Guardar y enviar encuesta"/>
    			<?php endif ?>
    		<?php endif ?>
    		<?php if(isset($empleo_plazo)): ?>
    		<span style="padding-left:10px;">
    			Fecha límite: <font class="tx_marcado"><?= Datehelper::fecha_tostring($empleo_plazo,true) ?></font>
    		</span>
    		<?php endif ?>		
    	</div>
    	<div style="float: right;">
    		<input class="search btn_imprimir" name="imprimirBtn" type="button" value="Imprimir"/>
    	</div>
    <?php else: ?>
        <div></div>
        <div  style="font-size:125%;padding:2px 2px 2px 4px;">No hay trabajadores contratados a través de una ETT.</div>
        <!-- <div style="margin-top:20px;"><a href="<?= $site[PAGE_EMPLEO_INDEX] ?>" class="enlace volvericon">Volver</a></div>  -->
    	<div style="float:left;<?php if($solo_lectura):?>padding-top: 15px;<?php endif; ?>">
    		<a href="<?=$urlBack?>"><img src="images/volver.png" border="0"></a> <a href="<?=$urlBack?>" class="enlace" style=" position: relative; top: -2px;"><?= ($solo_lectura) ? 'Volver':'Salir de la encuesta' ?></a>
    		<?php if(!$solo_lectura):?>
    			<input class="search btn_seguir" name="anteriorBtn" type="button" value="Anterior"/>
    			<?php if($fase==1): ?>
    				<input class="search btn_seguir" name="guardarBtn" type="button" value="Siguiente"/>
    			<?php else: ?>
    				<input class="search btn_guardar_enviar" name="enviarBtn" type="button" value="Guardar y enviar encuesta"/>
    			<?php endif ?>
    		<?php endif ?>
    		<?php if(isset($empleo_plazo)): ?>
    		<span style="padding-left:10px;">
    			Fecha límite: <font class="tx_marcado"><?= Datehelper::fecha_tostring($empleo_plazo,true) ?></font>
    		</span>
    		<?php endif ?>		
    	</div>
    	<div style="float: right;">
    		<input class="search btn_imprimir" name="imprimirBtn" type="button" value="Imprimir"/>
    	</div>
    <?php endif; ?>
    
</div>
<!-- FIN BLOQUE INTERIOR -->

<div id="dialog-detail" title="Envío de encuesta" >
	<div id="msg_errores" style="text-align: left"></div>
</div>
<div id="dialog-print" title="Imprimir encuesta" >
    <div id="msg_impresion" style="text-align: left;"></div>
</div>
<?php endif ?>



