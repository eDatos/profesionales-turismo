<style>
.enlaces {
    /*margin-left: 40px;*/
    padding-left: 10px;
    padding-right: 10px;
}
.iconoSuministroFinal {
    display: inline-block;
    width: 48px;
    height: 48px;
    float: right;
    margin-right: 10px;
}
</style>
<div style="clear: both;"/>
<div id="formulario" class="formularioParcial">
	<div class="cuadro fondo_verde" style="text-align: justify;" class="noprint">
	<h2 class="titulo_2 okicon">Factura Nº: <b><?= $NumeroFactura ?></b> completada.
	<?php
      	if(isset($iconoSuministro))
      	{
      	    echo '<img class="iconoSuministroFinal" src="images/consumos/'.$iconoSuministro->desc_corta.'" alt="'.$iconoSuministro->desc_larga.'" title="'.$iconoSuministro->desc_larga.'"/>';
      	}
    ?>
    </h2>
    <div class="subrayado"></div>

	<h3 style="margin-bottom: 0px;">Ahora puede:</h3>
	<div class="enlaces">
	<ul>
	<li><p>Introducir una nueva factura<?= ($esAdmin) ? ', del mismo establecimiento':'' ?>, pulsando <a href="<?= PAGE_CONSUMO_FORM ?>">aquí</a>.</p></li>
	<li><p>Revisar su factura pulsando <a title="Ver factura" href="<?= ($esAdmin) ? $this->build_url( PAGE_CONSUMO_PRINT, array(ARG_ESTID => $estid, ARG_NUMERO_FACTURA=>$NumeroFactura)) : $this->build_url( PAGE_CONSUMO_PRINT, array(ARG_NUMERO_FACTURA=>$NumeroFactura)) ?>">aquí</a>.</p></li>
	<li><p>Consultar sus facturas registradas <a href="<?= PAGE_CONSUMO_LIST ?>">aquí</a>.</p></li>
	</ul>
	</div>
	</div>
	<div style="margin-top:20px;"><a href="<?= $urlNext ?>" class="enlace volvericon">Volver</a></div>
</div>
