<div id="aviso-cookies">
	<h2>INFORMACI&Oacute;N IMPORTANTE SOBRE COOKIES</h2>
	<p>Este portal web utiliza cookies propias y de terceros para recopilar informaci&oacute;n que ayuda a optimizar su visita. Las cookies no se utilizan para recoger informaci&oacute;n de car&aacute;cter personal. Usted puede permitir su uso o rechazarlo, tambi&eacute;n puede cambiar su configuraci&oacute;n siempre que lo desee. Dispone de m&aacute;s informaci&oacute;n en nuestra <a href="<?= POLITICA_COOKIES_URL ?>" target="_self">Pol&iacute;tica de Cookies</a>.</p>
	<button type="button" class="btn" onclick="ocultarAviso()">Cerrar aviso</button>
</div>
<script>
	function readCookie(a){
		var d=[],
			e=document.cookie.split(";");
		a=RegExp("^\\s*"+a+"=\\s*(.*?)\\s*$");
		for(var b=0;b<e.length;b++){
			var f=e[b].match(a);
			f&&d.push(f[1])
		}
		return d
	}
	
	function ocultarAviso() {
		document.getElementById('aviso-cookies').style.display = 'none';
		document.cookie='cookies_allowed=true;expires=Tue, 01 Jan 2030 00:00:00 GMT;path=/';
	}
	
	if(readCookie('cookies_allowed') == 'true') {
		document.getElementById('aviso-cookies').style.display = 'none';
	}
</script>
