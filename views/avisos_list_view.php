<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="bloq_central titulo_1"><?= $page_titles[PAGE_AVISOS_LIST] ?></h1>
	<div class="bloq_central">
		<?php if($aviso_unico_mostrar != NULL): ?>
			<div class="cuadro">
				<h3 class="titulo3 subrayado"><?= $aviso_unico_mostrar->titulo ?></h3>
				<?= nl2br($aviso_unico_mostrar->texto) ?>
			</div>
		<?php else: ?>
			<?php if ($avisos_mostrar!=NULL && $avisos_mostrar->has_rows()) : ?>
				<?php foreach ($avisos_mostrar as $row): ?>
					<div class="cuadro">
						<h3 class="titulo3 subrayado"><?= $row['titulo'] ?></h3>
						<?= nl2br($row['aviso']) ?>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				No hay avisos para mostrar.
			<?php endif ?>
		<?php endif ?>
		<div style="position:relative;top:5px;margin-bottom:5px;"><a href="<?= $index_url ?>" class="enlace volvericon">Volver</a></div>
	</div>	
</div>	
<!-- FIN BLOQUE INTERIOR -->