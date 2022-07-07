<script type="text/javascript">
<!--
$(document).ready( function() {
	$("input[type=submit]").button();
	$("form[name='login']").submit(function()
	{
		doChallengeResponse();
	});
	// Activate the appropriate input form field.
	if (document.login.username.value == '') {
		document.login.username.focus();
	} else {
		document.login.password.focus();
	}
	$("input").keypress(function(e) { $("#1_2").hide(); });
	$("input[name='password']").keypress(function(e) { 
	    var s = String.fromCharCode( e.which );
	    if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
	    	$("#1_1").show();
	    }
	    else {
	    	$("#1_1").hide();
	    }
	});	

	$("#ayuda_btn").click(function(e) {
		e.preventDefault();
		AbrirAyuda(jQuery.parseJSON('<?= $texto_ayuda ?>'), "ayuda_btn");
		return false;
	});

	AbrirAyuda(jQuery.parseJSON('<?= $texto_aviso ?>'),"aviso_login");
});
// -->
</script>
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript">
<!--
  function doChallengeResponse() 
  {
	document.login.username.value = document.login.username.value.toUpperCase();
    str = document.login.username.value + ":" +
          MD5(document.login.password.value) + ":" +
          document.login.challenge.value;
    document.login.response.value = MD5(str);
    document.login.password.value = "";
  }
  
// -->
</script>

<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
    <h1 class="titulo_1">Portal de estadísticas de turismo para profesionales del sector</h1>
    <!-- COMIENZO COLUMNA DERECHA -->
    <div class="columna_pequ_der">
		<div class="cuadro">
	        <h3 class="titulo_3">En este sitio web puede:</h3>
		    <div class="subrayado"> </div>
			<ul class="lista_con_punto">
				<li>Rellenar encuestas sobre ocupaci&oacute;n y expectativas</li>
	            <li>Consultar los datos estad&iacute;sticos propios</li>
	            <li>Consultar informaci&oacute;n tur&iacute;stica procedente de otras fuentes</li>
			</ul>
		</div>
		<div class="cuadro">
		    <h3 class="titulo_3">Informaci&oacute;n de contacto</h3>
		    <div class="subrayado"> </div>
			<ul class="lista_sin_punto">
				<li><a class="enlace" href="<?=$contacto_url?>" target="_blank">Formulario de contacto</a></li>
	            <li><strong><?=$contacto_telefono?></strong> (tel&eacute;fono)</li>
	            <li><a class="enlace" href="mailto:<?=$contacto_mail?>"><?=$contacto_mail?></a></li>
			</ul>
		</div>
    </div>
    <!-- FIN COLUMNA DERECHA -->
    <div class="bloq_central"> 
        <!-- COMIENZO CAJA CELESTE -->
        <div id="aviso_login"></div>
        <?php if (CERRAR_WEB_MANTENIMIENTO) : ?>
        	<div id="aviso_web_mantenimiento" style="font-size: x-large; color:red; background-color: #ffff99; text-align: center; margin:0; padding: 0">¡ Web en mantenimiento !</div>
        <?php endif ?>
	    <!-- FIN CAJA CELESTE -->
    	<div class="cuadro" style="width: 50%; margin-left:25%; margin-top:5%; border: 1px solid #dddddd;background-color:#f8f8f8;">
            <h2 class="titulo_2" style="float:left;">Acceda con sus datos</h2>
            <a href="" id="ayuda_btn" class="ayudaicon enlace" style="float:right;margin-top:4px;background-position-y: 2px;" title="Ayuda (tecla de acceso: y)" accesskey="y"><strong>Ayuda</strong></a>
            <div class="subrayado"></div>
            <form name="login" action="<?= $loginUrl ?>" method="post">
            <div style="width: 80%; margin: 10px auto;" >          
            <table style="margin:20px 0px 5px 0px">
              <tr>
                <td>Usuario:</td>
                <td><input type="text" name="username" value="<?= $uname ?>" size="32" maxlength="32" /></td>
              </tr>
              <tr>
                <td>Contraseña:</td>
                <td><input type="password" name="password" size="32" maxlength="32"/></td>
              </tr>
              <?php if (@$is_captcha_enabled) : ?>
              <tr>
                <td colspan="2"><img src="data:image/png;base64,<?= $captcha_img ?>" style="width:300px;height:100px;margin-top:5px;"/>
                </td>
              </tr>              
              <tr>
                <td colspan=2>Escriba las cinco letras que aparecen en la imagen:</td>
              </tr>
              <tr>
                <td></td>
                <td style="text-align: right;"><input type="text" name="captcha_user" size="12" maxlength="5"/></td>
              </tr>
             <?php endif ?>
              <tr>
                <td colspan="2">
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input type="submit" value="Acceder" style="margin: 10px 0px 10px 2px; font-weight:bold;"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><a class="enlace" href="<?= $pwdRecoverUrl ?>">¿Ha olvidado su contraseña?</a></td>
              </tr>  
            </table>
             <!-- Set up the form with the challenge value and an empty reply value -->
            <input type="hidden" name="challenge" value="<?= $challenge ?>">
            <input type="hidden" name="response"  value="">
            </div>
             <p id="1_1" style="display:none;color: #DA750A; font-size:90%; font-weight: bold; font-style: italic;">¡ATENCION! La tecla de MAYUSCULAS está activada. Esto podría provocar errores al introducir la clave.</p>
             <?php global $username; if ( isset($username) ): ?>
             <?php if (@$is_captcha_enabled) : ?>
             <!-- failed login code -->
             <p id="1_2" style="color: red; font-size:90%; font-weight: bold; font-style: italic;">No ha logrado validar su acceso, probablemente haya tecleado mal su usuario o su clave o el resultado de la operación de la imagen no es correcta.
             <?php else: ?>
             <!-- failed login code -->
             <p id="1_2" style="color: red; font-size:90%; font-weight: bold; font-style: italic;">No ha logrado validar su acceso, probablemente haya tecleado mal su usuario o su clave.</p>             
             <?php endif ?>
             <?php endif ?>               
            </form>           
        </div>    	
    </div>
    <!-- FIN BLOQUE IZQUIERDO GRANDE -->

</div>
<!-- FIN BLOQUE INTERIOR -->

