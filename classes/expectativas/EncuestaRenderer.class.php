<?php

require_once(__DIR__."/../../lib/TableRenderer.class.php");

/**
 * Ayuda a realizar la representacion de un cuestionario de expectativas.
 * 
 *
 */
class EncuestaRenderer
{
	var $data;
	
	function __construct($data = null)
	{
		$this->data = $data;
	}
	
	function render_bloque($bloque)
	{
		$br = <<<EOB
			<div class="encuesta_bloque">
				<div class="encuesta_bloque_id">{$bloque->identificador}</div>
				<div class="encuesta_bloque_titulo">{$bloque->titulo}</div>
				<div class="encuesta_bloque_comentario">{$bloque->comentario}</div>
			</div>
			<hr style="background-color:black;align:center;color: FFEB99;border: 0px;height: 1px;"/>
EOB;
		return $br;
	}

	function render_pregunta($preg, $forceTextMode = false)
	{
		$opcs = $this->render_opciones($preg, $forceTextMode);

		if ($forceTextMode)
		{
			$br = <<<EOB
				<div class="encuesta_pregunta_titulo"><b>{$preg->apartado}&nbsp;</b>{$preg->titulo}<p class="encuesta_pregunta_comentario">{$preg->comentario_grabador}</p></div>
				<div class="encuesta_pregunta_opciones">{$opcs}</div>
				<hr style="background-color:black;align:center;"/>
EOB;
			return $br;
		}
		else 
		{
			$br = <<<EOB
				<div class="encuesta_pregunta_titulo"><b>{$preg->apartado}&nbsp;</b>{$preg->titulo}</div>
				<div class="encuesta_pregunta_opciones">{$opcs}</div>
				<hr style="background-color:black;align:center;"/>
EOB;
			return $br;			
		}
	}

	function render_input_tag($inputType, $name_suffix, $value, $is_selected = false)
	{
		$ip = "<input ";
		$ip .= "type='$inputType' ";
		$ip .= "name='". sprintf("respuestas[%d]", $name_suffix) . "' ";
		$ip .= "value='$value' ";
		$ip .= $is_selected?"checked='checked'":"";
		$ip .= ">";
		return $ip;
	}
	
	function render_text_tag($name_suffix, $value)
	{
		$ip = "<input ";
		$ip .= "type='text' ";
		$ip .= "size='1' maxlength='1' ";
		$ip .= "name='". sprintf("respuestas[%d]", $name_suffix) . "' ";
		$ip .= "value='$value' ";
		$ip .= ">";
		return $ip;
	}	

	function render_opciones($preg, $forceTextMode = false)
	{
		$t = new TableRenderer();
		$t->tableStyle = "istactable";
		$t->firstColumnStyle = "istacfirstcol";
		$t->firstRowWidth = 30; // En porcentaje 
		
		$t->combineColumns = $forceTextMode;
		
		$tipo_input = "radio";
		
		switch($preg->tipo)
		{
			case 5:
				//Cabecera de titulo
				$t->headContent[] = array(1, "", count($preg->valores), $preg->cabecera);
				// Continua;	
			case 1:
			case 3:
				//Cabecera con los valores
				$header = array(1, "");
				foreach($preg->valores as $valor)
				{
					$header[] = 1;
					$header[] = $valor->texto;
				}
				$t->headContent[] = $header;
				break;	
			case 6:
				/// No tiene cabecera ni valores.
				$tipo_input="checkbox";
				break;
		}
		
		//Contenido de la primera columna
		$t->firstColContentRender = function ($i) use ($preg)
		{
			return "[{$preg->opciones[$i]->index}.-] ". $preg->opciones[$i]->texto;
		};
		//Contenido de cada celda
		$t->cellContentRender = function ($i,$j) use ($preg, $tipo_input, $forceTextMode)
		{
			if ($forceTextMode)
			{
				return $this->render_text_tag($preg->opciones[$i]->index, $preg->opciones[$i]->valor);
			}
			else 
			{
				/// Si el valor actual es igual al valor del num, se establece como marcado.
				$is_selected = $preg->opciones[$i]->valor == $preg->valores[$j-1]->num;
				return $this->render_input_tag($tipo_input, $preg->opciones[$i]->index, $preg->valores[$j-1]->num, $is_selected);
			}
		};	
		return $t->render(count($preg->opciones), count($preg->valores)+1);
	}
}

?>