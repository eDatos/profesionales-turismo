<?php
	function niceDate($fechahora)
	{
		return substr($fechahora,6,2).'/'.substr($fechahora,4,2).'/'.substr($fechahora,0,4).' '.substr($fechahora,8,2).':'.substr($fechahora,10,2).':'.substr($fechahora,12,2);
	}
	
	// Posibles estados:
	// 0 = sesión creada y no registrada (sin registro en tabla TB_SESIONES).
	// 1 = sesión creada y registrada pero no autenticada.
	// 2 = sesión creada, registrada y autenticada.
	// 3 = sesión huérfana (sin fichero asociado).
	$descEstados=array (
			0 => "creada",
			1 => "registrada",
			2 => "autenticada",
			3 => "huérfana"
	);
	
	$mi_sesion=session_id();
?>
<style>
#div_tabla_sesiones {
	overflow-x:auto;
}
table {
    border-collapse: collapse;
}
table caption {
	font-weight: bold;
	font-size: large;
}
th {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	background-color: #3399ff;
}
table, th, td {
    border: 1px solid black;
}
tr:hover {
	background-color: #33ccff;
}
th, td {
	text-align: center;
}
tr.misesion {
	color: #cc3300;
}
td.caducada {
	text-decoration: line-through;
}
.alignright {
	float: right;
}
.search {
	text-align: center;
	font-size: medium;
	margin: 10px 0px 10px 0px;
	padding: 2px;
}
div.error {
	background-color: #ffff99;
	padding: 5px 10px;
}
span.hora {
	font-size:small;
	background-color: #ccffff;
	float:right;
	padding: 0 5px;
}
</style>
<script type="text/javascript">
$(document).ready( function() {
	
	var d=new Date(<?= date("Y,m,d,H,i,s,0"); ?>);
    setInterval(function() {
        d.setSeconds(d.getSeconds() + 1);
        $('#timer').text((('0'+d.getDate()).slice(-2) + '/' + ('0'+d.getMonth()).slice(-2) + '/' + d.getFullYear() + ' '+ ('0'+d.getHours()).slice(-2) +':' + ('0'+d.getMinutes()).slice(-2) + ':' + ('0'+d.getSeconds()).slice(-2) ));
    }, 1000);

	$("input[name='btnOper']").button();
	$("input[name='btnOper']").click(function(event){
		if(this.value=='<?= OP_BLOCKAPP ?>')
		{
			if(confirm("Se dispone a poner la web en modo mantenimiento, lo que impedirá a los usuarios iniciar sesión.\n¿Está seguro?")==false)
			{
				event.preventDefault();
				return;
			}
		}
	});
  });
</script>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<form style="margin-left:1%;" name="formAviso" action="#" method="post">
	<h1 class="bloq_central titulo_1">Mantenimiento de la web<span id="timer" class="hora"><?= date('d/m/Y H:i:s'); ?></span></h1>
	<div class="bloq_central">
		<?php
			define("ESTILO_OK", "okicon pagemsg_success");
			define("ESTILO_ERROR", "erroricon pagemsg_error");

			// Se comprueba si viene de alguna operación previa para mostrar el resultado 
	
			if($operacion!='')
			{		
				switch($operacion) 
				{
					case OP_BLOCKAPP:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al bloquear el acceso de los usuarios a la aplicación";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Bloqueado con éxito el acceso de los usuarios a la aplicación";
						}
						break;
					case OP_UNBLOCKAPP:
						if($error)
						{
							$estilo_msg = ESTILO_ERROR;
							$mensaje = "Error al desbloquear el acceso de los usuarios a la aplicación";
						}
						else
						{
							$estilo_msg = ESTILO_OK;
							$mensaje = "Desbloqueado con éxito el acceso de los usuarios a la aplicación";
						}
						break;
				}
				echo '<p id="infomsg" class="titulo_3 ' . $estilo_msg . '">' . $mensaje . '</p>';
			}
		?>
		
		<input class="search alignright" name="btnOper" type="submit" value="<?= ($web_cerrada)?OP_UNBLOCKAPP:OP_BLOCKAPP ?>">
		<input class="search" name="btnOper" type="submit" value="<?= OP_REFRESH ?>">
		
		<?php if (count($sesiones)>0) : ?>
			<?php $indice=1; ?>
            <div id="div_tabla_sesiones" class="cuadro fondo_gris">
				<table width="100%" border="1px solid black" style="align: center">
					<caption>Sesiones activas</caption>
					<thead>
						<tr>
							<th>#</th>
							<th>ID sesión</th>
							<th>ID usuario</th>
							<th>Fecha creación</th>
							<th>Fecha último acceso</th>
							<th>Estado</th>
						</tr>
					</thead>
	            <?php foreach ($sesiones as $sesion): ?>
						<tr <?= ($sesion->sid == $mi_sesion)? 'class="misesion"':'' ?>>
		                     <td width="1%"><?= $indice++ ?></td>
		                     <td width="30%" <?= ($sesion->caducada)? 'class="caducada"':'' ?>><?= $sesion->sid ?></td>
		                     <td width="30%"><?= $sesion->uid ?></td>
		                     <td width="15%"><?= niceDate($sesion->tcreacion) ?></td>
		                     <td width="15%"><?= niceDate($sesion->tultacceso) ?></td>
		                     <td width="10%"><?= $descEstados[$sesion->estado] ?></td>
			            </tr> 
	            <?php endforeach; ?>                                         
	            </table>  			                
	        </div>
        <?php endif; ?>	
		<input class="search alignright" name="btnOper" type="submit" value="<?= ($web_cerrada) ? OP_UNBLOCKAPP:OP_BLOCKAPP ?>">
		<input class="search" name="btnOper" type="submit" value="<?= OP_REFRESH ?>">
		</div>
	</form>	
	</div>	
<!-- FIN BLOQUE INTERIOR -->
<?php
?>