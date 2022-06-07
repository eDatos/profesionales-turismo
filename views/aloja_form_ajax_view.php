<?php if ($todo_ok) : ?>
<div class="cuadro fondo_verde">
<p class="okicon" style="margin-top:15px;">Su cuestionario de alojamiento ha sido guardado.</p>
</div>
<?php else:?>
<div class="cuadro" style="text-align: justify; background-color:#FCE8E8;border-color:#F5C2C2">
<p class="erroricon" style="margin-top:15px;">No ha sido posible guardar el cuestionario debido a errores internos.</p>
<p>Disculpe las molestias.</p>
</div>
<?php endif;?>
<?php exit; ?>