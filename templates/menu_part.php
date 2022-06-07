<?php if ($isLogged): ?>
	<div id="menu_contextual">
		<ul class="menu">
			  <li class="inactive"><a href="<?= $site[PAGE_HOME] ?>" accesskey="1" title="Inicio (tecla de acceso: 1)">Inicio</a></li>
			  <?php if ($page->have_any_perm(array(PERM_ADMIN,PERM_ADMIN_ISTAC))): ?>
				  <?php if ($page->user_can_do(OP_ALOJAMIENTO)): ?>
				  		<li class="inactive"><a href="<?= $site[PAGE_ALOJA_SELECT_PAISES] ?>" accesskey="2" title="Encuestas de alojamiento (tecla de acceso: 2)">Alojamiento</a></li>
				  <?php endif;?>
				  <?php if ($page->user_can_do(OP_EXPECTATIVAS)): ?>
					  	<li class="inactive"><a href="<?= $site[PAGE_EXP_FORMULARIO] ?>" accesskey="3" title="Encuestas de expectativas (tecla de acceso: 3)">Expectativas</a></li>
				  <?php endif;?>			  
				  <?php if ($page->user_can_do(OP_EMPLEO)): ?>
					  	<li class="inactive"><a href="<?= $site[PAGE_EMPLEO_FORM] ?>" accesskey="4" title="Módulo de empleo (tecla de acceso: 4)">Empleo</a></li>
				  <?php endif;?>			  
				  <?php if ($page->user_can_do(OP_SUMINISTROS)): ?>
					  	<li class="inactive"><a href="<?= $site[PAGE_CONSUMO_FORM] ?>" accesskey="5" title="Consumos (tecla de acceso: 5)">Consumos</a></li>
				  <?php endif;?>			  
			  <?php else:?>
				  <?php if ($page->user_can_do(OP_ALOJAMIENTO)): ?>
				  		<li class="inactive"><a href="<?= $site[PAGE_ALOJA_INDEX] ?>" accesskey="2" title="Encuestas de alojamiento (tecla de acceso: 2)">Alojamiento</a></li>
				  <?php endif;?>
				  <?php if ($page->user_can_do(OP_EXPECTATIVAS)): ?>
					  	<li class="inactive"><a href="<?= $site[PAGE_EXP_INDEX] ?>" accesskey="3" title="Encuestas de expectativas (tecla de acceso: 3)">Expectativas</a></li>
				  <?php endif;?>
			  <?php endif;?>
			  <li class="inactive"><a class="popup" target="_blank" href="<?= $page->build_url("pdfshow.php", array("src"=>CONTENT_INF_LEGAL)) ?>" accesskey="6" title="Información legal (tecla de acceso: 6)"><?= $page->convert_encoding("Información legal");?></a></li>
			  <li class="inactive"><a target="_blank" href="<?= EXTERN_INFORMACION_XML ?>" accesskey="7" title="Información xml (tecla de acceso: 7)">Información XML</a></li>
		</ul>
		<p id="version">v<?= VERSION_APP ?></p>
	</div>	
<?php else: ?>
		<div id="menu_contextual">
			<ul class="menu">
				  <li class="inactive"><a href="<?= EXTERN_WEB_ESTADISTICAS ?>" accesskey="1" title="Estadísticas (tecla de acceso: 1)">Estadísticas</a></li>
				  <li class="inactive"><a href="<?= EXTERN_EL_ISTAC ?>" accesskey="2" title="El ISTAC (tecla de acceso: 2)">El ISTAC</a></li>
				  <li class="inactive"><a href="<?= POLITICA_DATOS_ABIERTOS_URL ?>" accesskey="3" title="Datos abiertos (tecla de acceso: 3)">Datos Abiertos</a></li>
				  <li class="inactive"><a target="_blank" href="<?= EXTERN_INFORMACION_XML ?>" accesskey="4" title="Información XML (tecla de acceso: 4)">Información XML</a></li>
			</ul>
			<p id="version">v<?= VERSION_APP ?></p>
		</div>	
<?php endif; ?>
	

