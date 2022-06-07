<?php

require_once(__DIR__."/../classes/expectativas/EncuestaRenderer.class.php");

/**
 * REPRESENTA UN FORMULARIO SEGUN UNA PLANTILLA (variable $template)
 */

/// Aplanar jerarquia
$filas = array();
foreach($template->bloques as $bloque)
{
	$filas[] = $bloque;
	foreach ($bloque->preguntas as $ppp)
	{
		$filas[] = $ppp;
	}
}

$er = new EncuestaRenderer(); 
/// VISUALIZACION
foreach($filas as $bloque)
{
	if (is_a($bloque, "Bloque"))
	{
		echo $er->render_bloque($bloque);
	}
	else if (is_a($bloque, "Pregunta"))
	{
		echo $er->render_pregunta($bloque, $page->have_any_perm(PERM_GRABADOR));
	}
}

?>