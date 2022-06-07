<script type="text/javascript">
$(document).ready( function() 
{
	$("#guardarBtn").button();
	
	$("form[name='pwf']").submit(function()
	{
		// Validacion
		isvalid = validateForm();
		return isvalid;
	});
	$("input").keydown(function()
	{
		$(".validmsg").hide();
		$(this).removeClass("error").next(".validmsg").hide();
	});
	$(".validmsg").hide();
	$("input").removeClass('error');
	$(":input:visible:enabled:first").focus();
});

function empty(v)
{
	return (v == null || v.length == 0);
}

function isValidInput(un)
{
	if (empty(un.val()))
	{
		un.addClass('error').next('.validmsg').show();
		return false;
	}
	return true;
}

function validateForm()
{
	var ui = $("[name='uname']");

	$(".validmsg").hide();
	ui.removeClass('error');
	var validform = ui.filter(function(index) { return !isValidInput($(this));}).size() == 0;
	return validform;
}
</script>

<?php // Parte de la vista que muestra el mensaje informativo despues de cambiar la contraseña ?>
<?php if (isset($res)): ?>
 <!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1 noprint">Portal de estadísticas de turismo para profesionales del sector</h1>
	    <!-- COMIENZO COLUMNA DERECHA -->
    <div class="columna_pequ_der">
		<div class="cuadro">
		    <h3 class="titulo_3">Informaci&oacute;n de contacto</h3>
		    <div class="subrayado"> </div>
			<ul class="lista_sin_punto">
				<li><a class="enlace" href="<?=$contacto_url?>" target="_blank">Formulario de contacto</a></li>
	            <li><strong><?=$contacto_telefono?></strong> (tel&eacute;fono)</li>
	            <li><strong><?=$contacto_fax?></strong> (fax)</li>
	            <li><a class="enlace" href="mailto:<?=$contacto_mail?>"><?=$contacto_mail?></a></li>
			</ul>
		</div>
    </div>
    <!-- FIN COLUMNA DERECHA -->
	<div class="bloq_central">
		<?php if ($res === true) : ?>
		<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;"><?= $page_title ?></h3>
			<div class="subrayado"></div>
			<p>Se le ha enviado un correo electrónico con las instrucciones para restablecer su contraseña.</p>
			<p><b>Muchas gracias por su colaboración.</b></p>
		</div>
        <?php else: ?>
		<div class="cuadro" style="text-align: justify; background-color:#FF9393;border-color:#F37575" class="noprint">
			<h3 class="titulo_3 erroricon" style="margin-bottom:4px;"><?= $page_title ?></h3>
			<p>No ha sido posible realizar el proceso de recuperación de su contraseña.</p>
            <p><b>Disculpe las molestias.</b></p>
		</div>		
        <?php endif; ?>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->
<?php else: ?>	
<?php // Parte de la vista que muestra el formulario para cambiar la contraseña ?>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
<h1 class="titulo_1">Portal de estadísticas de turismo para profesionales del sector</h1>
    <!-- COMIENZO COLUMNA DERECHA -->
    <div class="columna_pequ_der">
		<div class="cuadro">
		    <h3 class="titulo_3">Informaci&oacute;n de contacto</h3>
		    <div class="subrayado"> </div>
			<ul class="lista_sin_punto">
				<li><a class="enlace" href="<?=$contacto_url?>" target="_blank">Formulario de contacto</a></li>
	            <li><strong><?=$contacto_telefono?></strong> (tel&eacute;fono)</li>
	            <li><strong><?=$contacto_fax?></strong> (fax)</li>
	            <li><a class="enlace" href="mailto:<?=$contacto_mail?>"><?=$contacto_mail?></a></li>
			</ul>
		</div>
    </div>
    <!-- FIN COLUMNA DERECHA -->
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">	
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Recuperación de contraseña</h2>
	      <div class="subrayado"></div>
	      	<form style="margin-left:1%;" name="pwf" action="#" method="post">
            	<input type="hidden" name="<?= ARG_OP ?>" value="<?= $op ?>"/>  	      	
	      		<table style="width:100%;">
                   <tr>
                      <td width="30%" >Introduzca su nombre de usuario:</td>
                      <td width="60%"><input tabindex="2" style="width:180px;" id="userid" name="uname" type="text" value="" />
                      <span class="validmsg">El nombre de usuario no puede estar vacío.</span></td>
                   </tr>                                         
                </table> 
                <input  tabindex="7" id="guardarBtn" type="submit" value="Continuar" style="margin: 10px 0px 10px 0px;">
            </form>           
		</div>
		<!-- FIN CAJA AMARILLA -->	       
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->
</div>
<!-- FIN BLOQUE INTERIOR -->
<?php endif; ?>