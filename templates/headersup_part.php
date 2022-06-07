<div id="cab_superior">
    <!-- MENU DE AYUDA -->
    <ul>
		<?php if ($isLogged): ?>	
			<?php if ($page->user_can_do(OP_VIEW_ESTAB_DATA)) :?>
		        <?php
		        	$llaves = "";
		        	for ($j = 0; $j < $num_estrellas; $j++)
		        	{
		        		if ($star_o_llave)
		        			$llaves.= "<span class='staricon'></span>";
		        		else
		        			$llaves.= "<span class='llaveicon'></span>";
		        	} 
		        ?>				
        		<li>
        			<?php if ($hayEstab) : ?>
        				<a class="enlace" style="margin-right:3px;" href="<?= $site[PAGE_ESTAB_CHANGE] ?>" accesskey="d" title="Datos de su establecimiento (tecla de acceso: d)"><span class="usericon"><?= $nombre_estab; ?></span></a>
        			<?php else: ?>
        				<span class="usericon"><?= $nombre_estab; ?></span>
       				<?php endif;?>
        		<?= @$llaves; ?>
        	<?php else: ?>
        		<li><span class="color_enlace usericon"><?= $page->convert_encoding($nombre_estab); ?></span>
        	<?php endif;?>
        </li>
        <?php if ($page->user_can_do(OP_CHANGE_PASSWORD)) : ?>
        <li>|</li>
        <li><a class="enlace" style="margin-right:3px;" href="<?= $site[PAGE_ESTAB_PASSWD] ?>" accesskey="c" title="Cambiar contraseña (tecla de acceso: c)"><?= $page->convert_encoding("Cambiar contraseña"); ?></a></li>
        <?php endif; ?>
        <li>|</li>
        <li><a href="<?= $site[PAGE_LOGOUT] ?>" accesskey="x" title="Salir (tecla de acceso: x)" style="margin-right:80px">Salir</a></li>
        <?php endif; ?>
        <li><a target="_blank" href="<?= $contacto_url ?>" accesskey="o" title="Contacte con nosotros (tecla de acceso: o)">Contacto</a></li>
    </ul>
    <img src="images/Profe_tur.jpg" style="width:100px; margin-right:10px; margin-top:5px;"/>
</div>