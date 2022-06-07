<!-- COMIENZO MIGAS DE PAN -->
<div id="migas" style="font: 95% Arial, Helvetica, sans-serif;">
	<p class="txt">Est&aacute; en:</p>
	<ul>
	<?php if (!isset($site)): ?>
		<li><a href="index.php">Inicio</a></li>
	<?php else: ?>
		<li><a href="<?= $site[PAGE_HOME]?>"><?= $page_titles[PAGE_HOME] ?></a></li>
		<?php 
		if (!empty($ruta_migas))
		{
			$last_pag = array_pop($ruta_migas);
			foreach($ruta_migas as $pagina) 
			{
				$hr = $site[$pagina];
				$at = $page_titles[$pagina];
				echo "&gt;<li><a href='".$hr."'>".$at."</a></li>";
			} 
			echo "&gt;<li><strong>".$page_titles[$last_pag]."</strong></li>";
		} ?>
	<?php endif; ?>
	</ul>
</div>
<!-- FIN MIGAS DE PAN -->
