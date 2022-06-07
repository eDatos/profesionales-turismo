<!-- COMIENZO MENU BORDES CUADRADOS -->
<style>
.anim {
	background: url('images/wait.gif') no-repeat left center;
	background-size:20px 20px;
	padding: 0px 0px 0px 30px;
	overflow: auto;
	height: 30px;
	vertical-align: middle;
}
</style>
<div class="cuadro" >
    <div id="dialog-detail" title="Proceso de envío" >
    	<p style="text-align:left;padding-top: 15px;margin: 0px;margin-left:20px;" class ="anim"><span class="msg">Enviando comentario...</span></p>
	</div>
    <h3 class="titulo_3 commenticon" style="margin-bottom:4px;">Ayúdenos a mejorar</h3>
    <form method="post" action="#">
    <textarea id="coment" name="coment" style="width:196px; height:130px; resize:none; font-family: verdana, arial, helvetica, sans-serif; font-size:0.9em;" placeholder="Escriba su sugerencia aquí..."></textarea>
    <input style="position: relative;top: 8px;left: -2px;height: 28px;margin-bottom:6px;font-size: 12px;" id="cmt" type="button" value="Enviar">
    </form>
</div>
<script type="text/javascript">
$(document).ready(function(){
		$("#cmt").button().click(function(){
			$( "#dialog-detail" ).dialog("open");

			$.ajax({
					type: "POST",
					url: "<?= $site[PAGE_COMENTARIOS]; ?>", 
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					data: { coment: $("#coment").val() }, 
					success: function(data) 
					{ 
						$(".anim").removeClass("anim");
						$( "#dialog-detail .msg" ).html(data);
						$( "#dialog-detail" ).dialog("open"); 
					}
					});
			return false;
		});
		$( "#dialog-detail" ).dialog({
	    	autoOpen: false,
	        resizable: false,
	        modal: true,
	    	buttons: {
	            "Aceptar": function() {
	            	$( this ).dialog( "close" );
	            	$("#coment").val("");
	            }
	        }
	    });
	});	
</script>
<!-- FIN MENU BORDES CUADRADOS -->