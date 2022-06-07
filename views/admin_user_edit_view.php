<script type="text/javascript">
$(document).ready( function() {
	$("#cancelarBtn").button().click(function() { history.back(); });
	$("#guardarBtn").button();
	
	$("#orig_pw_id").click( function()
	{
		if ($("#orig_pw_id").is(":checked"))
		{
			$("input[type='password']").attr('disabled', 'disabled');
		}
		else 
		{
			$("input[type='password']").removeAttr('disabled');
		}
	});
	$("form[name='edit_usuario']").submit(function()
	{
		if ($("#orig_pw_id").is(":checked"))
		{
			$("input[type='password']").val('');
		}

		// Validacion
		var isEdit = <?= ($op_type == 'edit')?'true':'false'?>;
		if (!isEdit)
		{
			return validateForm() && validatePassConfirm($("[name='password'],[name='confirm_password']"));
		}
		return ($("#orig_pw_id").is(":checked")) || validatePassConfirm($("[name='password'],[name='confirm_password']"));
	});
	$("input").keydown(function(){
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
	var ui = $("[name='userid'],[name='username'],[name='password'],[name='confirm_password']");

	$(".validmsg").hide();
	ui.removeClass('error');
	var validform = ui.filter(function(index) { return !isValidInput($(this));}).size() == 0;
	return validform;
}
</script>
    
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
<h1 class="titulo_1">Administración de usuarios</h1>
	<div class="columna_pequ_der">
		<!-- COMIENZO MENU BORDES CUADRADOS -->
		<div class="cuadro">
	        <h3 class="titulo_3">Recuerde</h3>
		    <div class="subrayado"> </div>
			<ul class="lista_con_punto">
				<li>La <em>clave original</em> es la clave que se introdujo cuando se creó el usuario.</li>
			</ul>
		</div>
		<!-- FIN MENU BORDES CUADRADOS -->
	</div>
	<!-- COMIENZO BLOQUE IZQUIERDO GRANDE -->
	<div class="bloq_central">	
		<!-- COMIENZO CAJA AMARILLA -->
		<div class="cuadro fondo_gris">
		  <h2 class="titulo_2">Datos del usuario</h2>
	      <div class="subrayado"></div>
			<form style="margin-left:1%;" name="edit_usuario" action="#" method="post">  
            	<input type="hidden" value="<?= $op_out ?>" name="<?= ARG_OP ?>"/> 
                <input type="hidden" value="<?= $est_user->establishment_id ?>" name="estid"/>         
                <table style="width:90%;">
                   <?php if ($op_type == 'edit'): ?>
	               <tr>
                     <td width="10%">Nombre:<input type="hidden" value="<?= $est_user->id ?>" name="userid"/></td>
                     <td width="50%"><input style="width:200px;" name="username" type="text" value="<?= @$est_user->username ?>" disabled="disabled"/>
                     </td>
				   </tr>                   
                   <tr>
                    <td width="10%">Nueva clave:</td>
                    <td width="50%"><input tabindex="1" style="width:200px;" name="password" type="password" value=""/>
                    <span style="margin-top:3px;">
                    <label style="margin-left:20px;" for="orig_pw_id">Recuperar clave original</label><input tabindex="6" style="position:relative;top:2px;" id="orig_pw_id" name="orig_pw" type="checkbox" value="1">
                    </span>
                    </td>
                  </tr>
                  <?php else: ?>              
                   <tr>
                    <td width="10%" >Identificador:</td>
					<td width="50%"><input tabindex="1" style="width:200px;" name="userid" type="text" value="" /><span class="validmsg">Se requiere un valor para el identificador.</span></td>
                   </tr>  
                   <tr>
                    <td width="10%" >Nombre:</td>
                    <td width="50%"><input tabindex="2" style="width:200px;" name="username" type="text" value="" /><span class="validmsg">Se requiere un valor para el nombre.</span></td>
                   </tr>
                 <tr>
                    <td width="10%" >Nueva clave:</td>
                    <td width="50%"><input tabindex="3" style="width:200px;" name="password" type="password" value=""/><span class="validmsg">Se requiere un valor para la clave.</span></td>
                  </tr>
                  <?php endif; ?>
                  <tr>
                    <td width="10%" >Confirmar clave:</td>
                    <td width="50%"><input tabindex="4" style="width:200px;" name="confirm_password" type="password" value=""/><span class="validmsg">Se requiere un valor para la clave.</span>
                    <span class="validmsg">Las claves no coinciden.</span></td>
                  </tr>              
                  <tr>
                    <td width="10%">Permisos:</td>
                    <td width="50%"><select tabindex="5" style="width:200px;" name="profile">
                    <?php foreach($opciones as $k => $opcion) 
					{
						echo "<option value='$k' ";
						if ($opcion == $selopcion)
							echo "selected='true' ";
						echo ">".$opcion."</option>";
					}
	                ?>
                    </select></td>
                  </tr>                            
                </table> 
                <input class="search" tabindex="7" id="guardarBtn" type="submit" value="Guardar" style="margin: 10px 0px 10px 0px;">
                <input class="search" tabindex="8" id="cancelarBtn" type="button" value="Cancelar" style="margin: 10px 0px 10px 2px;">            
            </form>           
		</div>
		<!-- FIN CAJA AMARILLA -->
		       
	</div>
	<!-- FIN BLOQUE IZQUIERDO GRANDE -->

</div>
<!-- FIN BLOQUE INTERIOR -->