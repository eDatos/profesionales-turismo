<?php

/**
 * Realiza la representacion HTML de una tabla, definida mediante n_fils y n_columnas, 
 * permitiendo personalizar el contenido de cada celda mediante llamadas a callbacks.
 */
class TableRenderer
{
	var $cellContentRender;
	var $firstColContentRender;
	var $headContent;
	var $firstRowWidth;
	var $tableStyle = "";
	var $firstColumnStyle = "";
	var $headerStyle = "";
	var $combineColumns;
	
	var $output;
	
	public function render($numfilas, $numcols)
	{
		$this->output = "";
			
		$widthPerCol = 100/$numcols;

		if ($this->firstRowWidth != null)
			$widthPerCol = (100 - $this->firstRowWidth)/($numcols - 1);
		
		$this->startTable($this->tableStyle);
		
		/// RENDER DE CABECERA
		if ($this->headContent != null)
		{
			foreach($this->headContent as $header)
			{
				$this->startRow();
				for($hi = 0; $hi < count($header); $hi++)
				{
					$headercell = $header[$hi];
					/// Las posiciones impares del array determinan el colspan,
					if ($hi % 2 == 0)
					{
						$width = ($hi == 0)? $this->firstRowWidth : $widthPerCol;
						if ($headercell == 1)
						{
							$this->startHead($headercell, $this->headerStyle, $width . "%");
						}
						else 
						{
							/// Las columnas con colspan no establecen el width
							$this->startHead($headercell, $this->headerStyle);	
						}
					}
					else 
					{
						/// Y las pares el contenido.
						$this->output .= $headercell;
						$this->endHead();
					}
				}
				$this->endRow();
			}
		}
		
		/// RENDER DE CUERPO
		$r = $this->cellContentRender;
		$frr = $this->firstColContentRender;
		
		for ($i = 0; $i < $numfilas; $i++)
		{
			$this->startRow();
			
			if ($numcols > 0)
			{
				$this->startCell(1, $this->firstColumnStyle);
				$this->output .= $frr($i);
				$this->endCell();
			}	
				
			/// Verdadero indica que las columnas de datos deben combinarse en solo una.
			if ($this->combineColumns)
			{
				$this->startCell($numcols-1);
				$this->output .= $r($i,1);
				$this->endCell();				
			}
			else 
			{
				for($j = 1; $j < $numcols; $j++)
				{
					$this->startCell();
					$this->output .= $r($i,$j);
					$this->endCell();
				}
			}
			$this->endRow();
		}
		$this->endTable();
		
		return $this->output;
	}
	
	function startTable($tableStyle = '')
	{
		if ($tableStyle == NULL)
			$this->output.= "<table>";
		else 
			$this->output.= "<table class='".$tableStyle."'>";
	}
	
	function endTable() {
		$this->output.= "</table>";
	}
	
	function startRow($colSpan = 1, $rowStyle = '')
	{
		$hh = "<tr ";
		
		if ($rowStyle != NULL)
			$hh .= "class='".$rowStyle."' ";
		
		if ($colSpan != 1)
			$hh .= "colspan='".$colSpan."' ";
	
		$hh.= ">";
		$this->output.= $hh;
	}
	
	function endRow() {
		$this->output.= "</tr>";
	}

	function startCell($colSpan = 1, $cellStyle = '')
	{
		$hh = "<td ";
		if ($cellStyle != NULL)
			$hh .= "class='".$cellStyle."' ";			

		if ($colSpan != 1)
			$hh .= "colspan='".$colSpan."' ";
		
		$hh.= ">";
		$this->output.= $hh;		
	}
	
	function endCell() {
		$this->output.= "</td>";
	}

	function startHead($colSpan, $headStyle = '', $width = null)
	{
		$hh = "<th ";
		
		if ($headStyle != NULL)
			$hh .= "class='".$headStyle."' ";
		
		if ($colSpan != 1)
			$hh .= "colspan='".$colSpan."' ";
		
		if ($width != null)
			$hh .= "style='width:".$width."' ";
				
		
		$hh.= ">";
		$this->output.= $hh;
	}
	
	function endHead() {
		$this->output.= "</th>";
	}	
}
?>