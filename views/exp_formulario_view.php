<style>
@import url(css/exp_estilos_impresion.css) print;
@import url(css/exp_estilos.css) screen;

.error_color
{
	color: red;
}
input.error {
	border: 1px solid red; 
}
input[readonly] {
	border: none;
}

.avisobackup-label {
	width:10em;
	text-align:center;
	margin-left:1em;
	
	/*
	position:relative;
	display:inline-block;
	*/
	position: fixed;
	z-index: -1;
	bottom: 0;
	right: 0;

	padding:2px 4px;
	font-size:11.844px;
	font-weight:bold;
	line-height:14px;
	color:#fff;
	vertical-align:baseline;
	white-space:nowrap;
	text-shadow:0 -1px 0 rgba(0,0,0,0.25);
	background-color:#999;
}

.avisobackup-initiated {
	background-color:#f89406;
}

.avisobackup-ok {
	background-color:#468847;
}

.avisobackup-error {
	background-color:#cd0a0a;
}

.avisobackup-info {
    background-color: #3a87ad;
    color: red;
}

.avisobackup-hide {
	display:none;
}

<?php  
// El grabador tiene un height de celda mayor ya que la textbox ocupa mas, y se compensa poniendo menos padding a las celdas.
if ($page->have_any_perm(PERM_GRABADOR)): ?>

.istactable td:not(.istacfirstcol) {
	padding : 0px;
	border-spacing:1;
}
<?php endif; ?>
#cuerpo_preguntas {
 display:none;
 visibility:hidden;
}

<?php if ($modificable) : ?>
@media print {
	#cuerpo_preguntas.modificado {
	 display:inline;
	 visibility:visible;
	 position:fixed;
	 top:5px;
	 left:5px;
	 opacity:0.2;
	 z-index:99;
	 color:white;
	 width: 100%;
	 height: 100%;
	}
}
<?php endif; ?>
</style>
<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/dates.js"></script>
<script type="text/javascript">
//Prevent the backspace key from navigating back.
$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
var dirty;
var avisobackup_fadeoutTimeMs=2500;
var intervaloKeepAliveSession=30;
var timerKeepAliveSession=0;
$(document).ready( function() {
	$("#df").submit(whenSubmit);
	$("input[type='button']").button();
	$("input[type='submit']").button();
	$("input[type='reset']").button();

	dirty=false;
   <?php if (!$modificable) : ?>
   $("input[type='text']").attr('readonly','readonly');
   $("input[type='radio']:unchecked").attr('disabled','disabled');
   $("input[type='checkbox']").attr('onclick','return false');
   <?php else: ?>
   $("input").change(
		   function()
		   {
			   dirty=true;
			   $("#cuerpo_preguntas").addClass("modificado");
			}
	);
   <?php endif; ?>
   $("input[name='printBtn']").button().click(
	   function()
		{
			if(dirty)
			{
				if(confirm("ATENCIÓN\n\nEl formulario ha sido modificado y aún no ha sido enviado.\nLos datos impresos pueden NO COINCIDIR con los registrados por el ISTAC.\n¿Desea continuar?")==false)
					return;
			}
			window.print();
		}
	);

   <?php if (isset($do_print) && $do_print) : ?>
   window.print();
   <?php elseif (($modificable==true) && ($es_admin==false)): ?>
   if(intervaloKeepAliveSession!=0)
   {
	   $('body').append('<span class="avisobackup-label avisobackup-info avisobackup-hide">Sesión perdida</span>');
	   var intervaloMS=intervaloKeepAliveSession * 1000;
	   timerKeepAliveSession=setInterval(function(){
			$.ajax({
				type: "POST",
				url: "<?= $navpage_url; ?>", 
				data: {op : "ka"}, 
				success: function(data) 
				{
					try
					{
						var datos=JSON.parse(data);
						if((datos.resultado==true)&&(datos.op=="ka"))
							$(".avisobackup-label").hide();
					}
					catch(e)
					{
					}
				},
				error: function(xhr)
				{
					try
					{
						if(xhr.status==403)
						{
							$(".avisobackup-label").hide();
							var aviso=$(".avisobackup-label.avisobackup-info");
							aviso.show();
							//aviso.fadeOut(avisobackup_fadeoutTimeMs);
						}
					}
					catch(e)
					{
					}
				}
			});
		   },intervaloMS);
   }
   <?php endif; ?>
});

var reglas = jQuery.parseJSON('<?php echo $template->get_js_validation_code(); ?>');

<?php if ($page->have_any_perm(PERM_GRABADOR)): ?>

function getRespuestas()
{
	var respuestas = [];
	$("#df input[name^='respuestas']").each( function(index, elto ) {
		if (elto.value)
		{
			eval(elto.name  + "= '"+ elto.value + "'");
		}
	});	
	return respuestas;
}

function tienevalor(value, msg)
{
	if (!value.val())
	{
		alert(msg);
		value.focus();
		value.addClass("error");
		return false;
	}
	return true;
}

function validateCabecera()
{
	sel = $("input[name='pregunta00']");
	if (sel.val() != 1 && sel.val() != 6)
	{
		alert("Debe indicar quien contesta esta encuesta. En la pregunta 0");
		$("input[name='pregunta00']").first().focus();
		$("#preg0").addClass("error_color");
		return false;
	}
	if (sel.val() == 6 && !($("input[name='pregunta00_texto']").val().trim()))
	{
			alert("Si el formulario no lo rellena el director, debe especificar quien lo hace en la pregunta 0");
			$("input[name='pregunta00_texto']").first().focus();
			$("input[name='pregunta00_texto']").addClass("error");
			return false;
	}
	
	$("#preg0").removeClass("error_color");
	$("input[name='pregunta00_texto']").removeClass("error");

	var a = $("input[name='pregunta_fecha_agno']");
	var m = $("input[name='pregunta_fecha_mes']");
	var d = $("input[name='pregunta_fecha_dia']");

	d.removeClass("error");
	m.removeClass("error");
	a.removeClass("error");
	if (!tienevalor(d,"Debe indicar el día de esta encuesta."))
		return false;	
	if (!tienevalor(m,"Debe indicar el mes de esta encuesta."))
		return false;
	if (!tienevalor(a,"Debe indicar el año de esta encuesta."))
		return false;	
	if (!validDate(d.val(),m.val(),a.val()))
	{
		alert("La fecha indicada no es correcta.");
		d.focus();
		d.addClass("error");
		m.addClass("error");
		a.addClass("error");
		return false;
	}	
	return true;
}

<?php else: ?>

function validateCabecera()
{
	sel = $("input[name='pregunta00']:checked");
	if (sel.length == 0)
	{
		alert("Debe indicar quien contesta esta encuesta. En la pregunta 0");
		$("input[name='pregunta00']").first().focus();
		$("#preg0").addClass("error_color");
		return false;
	}
	if (sel.val() == "Otra persona" && !($("input[name='pregunta00_texto']").val().trim()))
	{
			alert("Si el formulario no lo rellena el director, debe especificar quien lo hace en la pregunta 0");
			$("input[name='pregunta00_texto']").first().focus();
			$("input[name='pregunta00_texto']").addClass("error");
			return false;
	}
	
	$("#preg0").removeClass("error_color");
	$("input[name='pregunta00_texto']").removeClass("error");
	return true;
}

function getRespuestas()
{
	var respuestas = [];
	$("input[name^='respuestas']:checked").each( function(index, elto ){
		eval(elto.name  + "= '"+ elto.value + "'");
	});
	return respuestas;
}

<?php endif; ?>

function whenSubmit()
{
    if (!validateCabecera())
		return false;
	
	var respuestas = getRespuestas();
	$("input[name^='respuestas']").closest("tr").children(":first").removeClass("error_color");
	for(var i in reglas)
	{
		if (reglas[i] != null)
		{
			error = "";
			vi = respuestas[i];
            if (!vi)
            {
                //Error se requiere un valor.
                if (reglas[i].req)
                    error = "No ha respondido una de las preguntas obligatorias. Pregunta " + i;
            }
			else if ( jQuery.inArray( vi.toString(), reglas[i].vals ) == -1)
			{
				//Error el valor introducido no es correcto.
				error = "El valor '"+ vi.toString() +"' no es válido, debe estar entre los siguientes: " + reglas[i].vals + ". Pregunta " + i;				
			}
			if (error)
			{
				$("input[name='respuestas["+i+"]']").closest("tr").children(":first").addClass("error_color");
				$("input[name='respuestas["+i+"]']").focus();
				alert(error);
				return false;				
			}
		}
	}

	dirty=false;
	$("#cuerpo_preguntas").removeClass("modificado");
	return true;
}

</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_2 noprint" style="font-size:20px;">Encuesta de Expectativas Hoteleras</h1>
	<div class="bloq_central">
        <?php if ($modificable) : ?>
		<div style="text-align: justify;margin-top:20px;" class="noprint">
		Estimado usuario:<br><br>
		Contestar el cuestionario que le presentamos a continuación le ocupará muy pocos minutos. 
		Le rogamos que lea detenidamente las preguntas y las conteste basándose en su experiencia profesional y su conocimiento del sector.
		Las respuestas deben reflejar sus expectativas para los meses de <b><?= @$trimestre_encuesta ?> próximos</b> y deben referirse <b>exclusivamente</b> a su establecimiento hotelero.
		</div>
		<?php endif; ?>
        <form name="df" id="df" action="#" method="post">
			<?php include(__DIR__ . "/../viewparts/exp_formulario_cabecera_part.php");?>
			<?php if ($modificable) : ?><div id="cuerpo_preguntas"><img alt="" src="images/borrador.png" style="width: 100%; height: 100%;"></div><?php endif; ?>
			<?php include(__DIR__ . "/../viewparts/exp_formulario_cuerpo_part.php");?>
			<?php include(__DIR__ . "/../viewparts/exp_formulario_pie_part.php");?>
			
            <div class="noprint" style="width: 95%; margin-top:30px;margin-bottom: 30px; margin-left: auto; margin-right: auto; text-align: right;">
				<?php if ($modificable) : ?>
				<input type="submit" value="Enviar cuestionario" title="Pulse el botón para enviar los datos" name="guardar"/>
				<input type="reset" value="Deshacer cambios" title="Pulse el botón para comenzar de nuevo con el formulario cargado inicialmente" style="margin-left:10px;"/>
				<input type="hidden" name="<?= ARG_OP ?>" value="save"/>
				<input type="hidden" name="<?= ARG_ESTID ?>" value="<?= $establecimiento->id_establecimiento ?>"/>
				<?php endif; ?>
				<input class="search ui-button ui-widget ui-state-default ui-corner-all" name="printBtn" type="button" value="Imprimir" style="width:95px; background-image: url(images/imprimir.png);background-repeat: no-repeat;background-position: 8px 4px;margin-left:10px;padding-left:27px;margin-top:15px;" role="button" aria-disabled="false">        
            </div>
		</form>
        <?php if ($modificable) : ?>
		<div class="noprint" style="font-size:14px;"><i>Nota:&nbsp; Una vez enviado el cuestionario puede utilizar la opción <b>'Ayúdenos a mejorar'</b> de la página de inicio para enviar cualquier comentario.</i></div>
        <?php endif; ?>
		</div>
</div>
<!-- FIN BLOQUE INTERIOR -->

