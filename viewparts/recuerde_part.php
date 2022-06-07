<?php if ($page->user_can_do(OP_VIEW_ESTAB_DATA)): ?>
<!-- COMIENZO MENU BORDES CUADRADOS -->
<div class="cuadro fondo_verde" >
    <h3 class="titulo_3" style="margin-bottom:4px;">Recuerde <span id="ayuda_rec" onclick="MostrarAyuda('ayuda_rec','AYUDA03<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon" title="Ayuda (tecla de acceso: r)" accesskey="r" >&nbsp;</span></h3>
    Mantenga actualizados los <a id="de" href="<?= $site[PAGE_ESTAB_CHANGE] ?>" class="enlace">datos de su establecimiento</a>
</div>
<!-- FIN MENU BORDES CUADRADOS -->
<?php endif; ?>