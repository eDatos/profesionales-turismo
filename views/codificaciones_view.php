<style>
table.t_cod
{
	
	margin-left: 15px;
}
table.t_cod td
{
	width : 40px;
}
table.t_cod td:nth-child(1)
{
	width : 500px;
}
img {
	vertical-align:middle;
}
span.disable-links {
    cursor: not-allowed;
}
span.disable-links a {
    pointer-events: none;
}
</style>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="titulo_1"><?= USER_TITLE ?></h1>
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
	            <li><strong><?=$contacto_fax?></strong> (fax)</li>
	            <li><a class="enlace" href="mailto:<?=$contacto_mail?>"><?=$contacto_mail?></a></li>
			</ul>
		</div>
    </div>
	<div class="bloq_central">
		    <h2 class="titulo_2">Envío de fichero de datos en formato XML</h2>
		    <p>		    
		    Es posible enviar, a través de la opción correspondiente de la aplicación, un <strong>fichero de datos en formato XML con la información de alojamiento</strong>. El formato de dicho fichero está definido en la resolución publicada en el 	
		    <a href="http://www.gobiernodecanarias.org/boc/2007/220/001.html" target="_blank">BOC 2007/220 página 24931 del viernes 2 de noviembre de 2007.</a>
			</p>
			<p>
			Aquí puede descargar, tanto la información sobre la estructura del fichero y validaciones que ha de cumplir, como el contenido de las tablas de codificación utilizadas:</p>
			<table class="t_cod">
			<?php
    			// NOTA: Se esperan filas con uno o varios enlaces con iconos.
    			$filas=GrupoRecurso::loadGruposEnlaces('CODIFICACIONES');
    			foreach($filas as $fila)
    			{
        			echo '<tr><td><li>'.$fila->enlaces[0]->getDescLarga()."</li></td>";
    			    foreach($fila->enlaces as $enlace)
    			    {
    			        echo "<td>".$enlace->render()."</td>";
    			    }
    			    echo "</tr>\n";
    			}
			?>
			</table>			
    </div>
</div>
<!-- FIN BLOQUE INTERIOR -->