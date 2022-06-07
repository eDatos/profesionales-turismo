<script type="text/javascript">
$(document).ready( function() {
	//alert('Hola');
	$("input[type=submit]").button();
	//$("input").button();
  });
</script>
<style>
td {
    padding : 10px 10px 0px 10px;
}
</style>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h1 class="bloq_central titulo_1"><?= ADMIN_TITLE ?></h1>
	<div class="bloq_central">
		<div class="cuadro fondo_verde">
		    <h2 class="titulo_2">Gestión de la aplicación de expectativas hoteleras y alojamiento turístico.</h2>
		    <div class="subrayado"></div>
		    <ul class="lista_con_punto">
			    <li>Desde esta aplicación y como administrador puede crear y modificar usuarios.</li>
	            <li>También se le ofrece la opción de introducir, borrar o modificar los cuestionarios, 
			    así como visualizar los resultados de las distintas comparativas que se realizan entre los datos 
			    de un establecimiento y los resultados de su isla y Canarias.</li>
		    </ul>	
	    </div>
        <table style="width:100%">
        <tr>
        <td style="width:33%">
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Encuesta de alojamiento</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
                <li><a class="enlace" href="<?= $site[PAGE_ALOJA_PLAZOS]; ?>">Configurar plazos</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_ALOJA_PLAZOS_EXCEP]; ?>">Configurar plazos excepcionales</a></li>
                <li><a class="enlace" href="<?= $abrirAlojaUrl ?>">Abrir cuestionario</a></li>
                <li><a class="enlace" href="<?= $cerrarAlojaUrl ?>">Cerrar cuestionario</a></li>
                <li><a class="enlace" href="<?= $modificarAlojaUrl ?>">Crear o modificar cuestionario</a></li>
                <li><a class="enlace" href="<?= $borrarAlojaUrl ?>">Borrar cuestionario</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_ALOJA_ACUSE]; ?>">PDF recepción encuesta</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_ALOJA_RESULTADOS]; ?>">Consultar resultados</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_ALOJA_ANTERIORES]; ?>">Ver encuestas presentadas</a></li>
                <li style="background-image:none;"><br/></li>
            </ul>
        </div>
        </td>
        <td style="width:33%">
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Encuesta de alojamiento XML</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
                <li><a class="enlace" href="<?= $site[PAGE_ALOJA_XML]; ?>">Subir archivo XML</a></li>
                <li><a class="enlace" href="<?= $page->build_url($site[PAGE_ALOJA_XML], array(ARG_OP=>'rsx')); ?>">Resubir archivo XML</a></li>
                <li><a class="enlace" href="<?= $page->build_url($site[PAGE_ALOJA_XML], array(ARG_OP=>'dwl')); ?>">Descargar archivo XML</a></li>
            </ul>
        </div>
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Gestión módulo de empleo</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
                <li><a class="enlace" href="<?= PAGE_ADMIN_EMPLEADORES ?>">Gestionar Empleadores</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_EMPLEO_FORM]; ?>">Crear o modificar cuestionario</a></li>
                <li style="background-image:none;"><br/></li>
            </ul>
        </div>
        </td>        
        <td  style="width:33%">
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Encuesta de expectativas</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
                <li><a class="enlace" href="<?= $site[PAGE_EXP_PLAZOS]; ?>">Configurar plazos</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_EXP_FORMULARIO]; ?>">Crear o modificar cuestionario</a></li>
                <li><a class="enlace" href="<?= $page->build_url($site[PAGE_EXP_ADMIN_CUESTIONARIOS], array(ARG_OP=>'delete')); ?>">Borrar cuestionario</a></li>
                <?php // <!-- <li><a class="enlace" href="#">Consultar resultados</a></li>-->; ?>
                <li><a class="enlace" href="<?= $site[PAGE_EXP_ANTERIORES]; ?>">Ver encuestas presentadas</a></li>
            </ul>
        </div> 
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Gestión módulo de suministros</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
                <li><a class="enlace" href="<?= $site[PAGE_CONSUMO_FORM]; ?>">Introducir nueva factura</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_CONSUMO_LIST]; ?>">Ver o modificar factura</a></li>
            </ul>
        </div>
        </td>
        </tr>
        </table>
        <table style="width:100%">
        <tr>
        <td style="width:18%;">
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Avisos y noticias</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
                <li><a class="enlace" href="<?= $site[PAGE_AVISOS]; ?>">Configurar avisos</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_NOTICIAS]; ?>">Configurar noticias</a></li>
            </ul>
        </div>
        </td>
        <td style="width:17%;">
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Enlaces de ayuda</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
                <li><a class="enlace" href="<?= $site[PAGE_ENLACES_AYUDA]; ?>">Configurar</a></li>
                <li style="background-image:none;"><br/></li>
            </ul>
        </div>
        </td>        
        <td style="width:25%;">
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Administrar establecimientos</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
                <li><a class="enlace" href="<?= $site[PAGE_ESTAB_ADMIN] ?>">Modificar establecimiento</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_BLOCK_USUARIOS] ?>">Mantenimiento web</a></li>
            </ul>
        </div> 
        </td>        
        <td style="width:20%;">
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Log de operaciones</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
                <li><a class="enlace" href="<?= $site[PAGE_LOG_CONFIG]; ?>">Configuración</a></li>
                <li><a class="enlace" href="<?= $site[PAGE_LOG_SEARCH]; ?>">Consultar</a></li>
            </ul>
        </div> 
        </td>
        <td style="width:20%;">
        <div class="cuadro fondo_gris">
            <h2 class="titulo_3">Informática</h2>
            <div class="subrayado"></div>
            <ul class="lista_con_punto">
            	<li>Versión: <?= APPLICATION_VERSION ?></li>
            </ul>
        </div> 
        </td>        
        </tr>
        </table>
    </div>

</div>
<!-- FIN BLOQUE INTERIOR -->