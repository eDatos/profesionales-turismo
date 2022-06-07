<?php if (!isset($res)): ?>

<script type="text/javascript">
$(document).ready( function() 
{
	$("#cancelarBtn").button().click(function() {window.location.href='<?= $prevpage ?>'; });
	$("#guardarBtn").button();
	
	$("form[name='pwf']").submit(function()
	{
		// Validacion
		isvalid = validateForm() && validatePassConfirm($("#npass,#con_npass"));

		if (isvalid)
		{
			doChallengeResponse();
		}
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

function validatePassConfirm(passwords)
{
	if (passwords.eq(0).val() != passwords.eq(1).val())
	{
		passwords.eq(1).addClass('error').siblings(".validmsg").eq(1).show();
		return false;
	}
	return true;
}

function validateForm()
{
	var ui = $("#pass,#npass,#con_npass");

	$(".validmsg").hide();
	ui.removeClass('error');
	var validform = ui.filter(function(index) { return !isValidInput($(this));}).size() == 0;
	return validform;
}

function doChallengeResponse() 
{
	str = "<?= $username ?>" + ":" + MD5($("#pass").val()) + ":" + "<?= $challenge ?>";
	
	document.pwf.response.value = MD5(str);
	document.pwf.password.value = MD5($("#npass").val());
	$("#pass").val("");
	$("#npass").val("");
	$("#con_npass").val("");
}
</script>
<script type="text/javascript" src="js/md5.js"></script>
 <?php endif; ?>
<?php // Parte de la vista que muestra el mensaje informativo despues de cambiar la contrase�a ?>
<?php if (isset($res)): ?>
 <!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1 noprint">Portal de estad�sticas de turismo para profesionales del sector</h1>
	<div class="bloq_central">
		<?php if ($res === true) : ?>
		<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Cambio de contrase�a correcto</h3>
			<div class="subrayado"></div>
			<p>Su contrase�a ha sido cambiada.</p>
			<p>A partir de ahora se emplear� la nueva contrase�a para el acceso a la aplicaci�n.</p>
		</div>
        <?php else: ?>
		<div class="cuadro" style="text-align: justify; background-color:#FF9393;border-color:#F37575" class="noprint">
			<h3 class="titulo_3 erroricon" style="margin-bottom:4px;">Error en el cambio de contrase�a</h3>
			<p>No ha sido posible realizar el cambio de su contrase�a. Aseg�rese de que la contrase�a actual est� correcta y que la nueva contrase�a no est� vac�a.</p>
            <p><i>Puede volver al formulario,  corregir el error y volver a intentarlo.</i></p>
			<p><b>Muchas gracias por su colaboraci�n.</b></p>
		</div>		
        <?php endif; ?>
	</div>
</div>
<!-- FIN BLOQUE INTERIOR -->
<?php else: ?>	
<?php // Parte de la vista que muestra el formulario para cambiar la contrase�a ?>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
<h1 class="titulo_1">Portal de estad�sticas de turismo para profesionales del sector</h1>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">	
	<?php if (isset($es_pwd_original)) : ?>
		<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
			<h3 class="titulo_3 okicon" style="margin-bottom:4px;">Inicializaci�n de la contrase�a</h3>
			<div class="subrayado"></div>
			<p>Debe cambiar la contrase�a.</p>
			<p>A partir de ahora se emplear� la nueva contrase�a para el acceso a la aplicaci�n.</p>
		</div>	
	<?php endif; ?>
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Cambio de contrase�a</h2>
	      <div class="subrayado"></div>
	      	<form style="margin-left:1%;" name="pwf" action="#" method="post">
            	<input type="hidden" name="<?= ARG_OP ?>" value="<?= $op ?>"/> 
				<input type="hidden" name="response"/>
            	<input type="hidden" name="password"/> 	      	
	      		<table style="width:90%;">
                   <tr>
                      <td width="20%" >Introduzca su contrase�a actual:</td>
                      <td width="50%"><input tabindex="2" style="width:200px;" id="pass" type="password" value="" />
                      <span class="validmsg">Por favor, introduzca la contrase�a actual.</span></td>
                   </tr>
                   <tr>
                      <td width="20%" >Introduzca la nueva contrase�a:</td>
                      <td width="50%"><input tabindex="3" style="width:200px;" id="npass" type="password" value=""/>
                      <span class="validmsg">La nueva contrase�a no puede estar vac�a.</span></td>
                   </tr>
                   <tr>
                      <td width="20%" >Vuelva a introducir la nueva contrase�a:</td>
                      <td width="50%"><input tabindex="4" style="width:200px;" id="con_npass" type="password" value=""/>
                      <span class="validmsg">La nueva contrase�a no puede estar vac�a.</span>
                      <span class="validmsg">Las contrase�as no coinciden.</span>
                      </td>
                   </tr>                                         
                </table> 
                <input  tabindex="7" id="guardarBtn" type="submit" value="Guardar" style="margin: 10px 0px 10px 0px;">
                <input  tabindex="8" id="cancelarBtn" type="button" value="Cancelar" style="margin: 10px 0px 10px 2px;">            
            </form>           
		</div>
		<!-- FIN CAJA AMARILLA -->	       
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->
</div>
<!-- FIN BLOQUE INTERIOR -->
<?php endif; ?>