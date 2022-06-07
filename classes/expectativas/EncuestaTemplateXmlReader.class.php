<?php

require_once(__DIR__."/EncuestaTemplate.class.php");

/**
 * Lector de una encuesta (de expectativas) a partir de un archivo ".xml".
 * Devuelve un objeto EncuestaTemplate (ver EncuestaTemplate.class.php) que contiene los datos
 * del archivo.
 */
class EncuestaTemplateXmlReader 
{
	/// Errores encontrados durante la lectura.
	var $error;
		
	private $current_opcion_index; 
	
	/**
	 * Carga un archivo XML y lo procesa obteniendo un objeto EncuestaTemplate con su contenido inicializado
	 * desde el archivo.
	 * @param unknown_type $filename
	 * @return boolean|EncuestaTemplate False si ha ocurrido algún error durante la lectura del archivo.
	 */
	public function load($filename)
	{
		/// Leer el archivo XML, controlando los errores.
		$this->error = "";
		libxml_use_internal_errors(true);
		$doc = simplexml_load_file($filename);
		if (!$doc) 
		{
			$errors = libxml_get_errors();
			foreach ($errors as $error) 
			{
				$this->error .= $this->display_xml_error($error);
			}
			libxml_clear_errors();
			return false;
		}
	
		/// Explotar el contenido del archivo XML (usando SimpleXml (http://es2.php.net/manual/en/book.simplexml.php)).
		$this->current_opcion_index = 1;
		
		$exp_form = new EncuestaTemplate();
		$exp_form->bloques = array();
	
		foreach ($doc->bloque as $bloque)
		{
			$b = $this->read_bloque($bloque);
			$exp_form->bloques[] = $b;
			foreach($bloque->preguntas->pregunta as $pregunta)
			{
				$pr = $this->read_pregunta($pregunta);
				$b->addPregunta($pr);
				foreach($pregunta->valores->valor as $valor)
				{
					$vr = $this->read_valor($valor);
					$pr->addValor($vr);
				}
				foreach($pregunta->opciones->opcion as $opcion)
				{
					$opc = $this->read_opcion($opcion);
					$pr->addOpcion($opc);
				}
			}
		}
		return $exp_form;
	}
	
	/**
	 * Convierte un tag <bloque> a su correspondiente objeto Bloque.
	 * Realiza la conversion de los textos desde la codificacion origen UTF-8 a ISO_8859-1
	 * @param SimpleXMLElement $bloque
	 * @return Bloque
	 */
	private function read_bloque(SimpleXMLElement $bloque)
	{
		$b = new Bloque();
		$b->identificador = mb_convert_encoding((string)$bloque['identificador'], 'ISO_8859-1', 'UTF-8');
		$b->titulo = mb_convert_encoding((string)$bloque->titulo, 'ISO_8859-1', 'UTF-8');
		$b->comentario = mb_convert_encoding((string)$bloque->comentario, 'ISO_8859-1', 'UTF-8');
		return $b;
	}
	
	/**
	 * Convierte un tag <pregunta> a su correspondiente objeto Pregunta.
	 * Realiza la conversion de los textos desde la codificacion origen UTF-8 a ISO_8859-1
	 * @param SimpleXMLElement $preg
	 * @return Pregunta
	 */
	private function read_pregunta(SimpleXMLElement $preg)
	{
		$p = new Pregunta();
		$p->tipo = (string)$preg['tipo'];
		$p->apartado = mb_convert_encoding((string)$preg['apartado'], 'ISO_8859-1', 'UTF-8');
		$p->titulo =  mb_convert_encoding((string)$preg->titulo, 'ISO_8859-1', 'UTF-8');
		$p->cabecera =  mb_convert_encoding((string)$preg->cabecera, 'ISO_8859-1', 'UTF-8');
		$p->comentario = mb_convert_encoding((string)$preg->comentario, 'ISO_8859-1', 'UTF-8');
		$p->comentario_grabador = mb_convert_encoding((string)$preg->comentario_grabador, 'ISO_8859-1', 'UTF-8');
		return $p;
	}
	
	/**
	 * Convierte un tag <valor> a su correspondiente objeto Valor.
	 * Realiza la conversion de los textos desde la codificacion origen UTF-8 a ISO_8859-1
	 * @param SimpleXMLElement $valor
	 * @return Valor
	 */
	private function read_valor(SimpleXMLElement $valor)
	{
		$v = new Valor();
		$v->num =  mb_convert_encoding((string)$valor['num'], 'ISO_8859-1', 'UTF-8');
		$v->texto =  mb_convert_encoding((string)$valor['texto'], 'ISO_8859-1', 'UTF-8');
		return $v;
	}
	
	/**
	 * Convierte un tag <opcion> a su correspondiente objeto Opcion.
	 * Realiza la conversion de los textos desde la codificacion origen UTF-8 a ISO_8859-1
	 * @param SimpleXMLElement $opc
	 * @return Opcion
	 */
	private function read_opcion(SimpleXMLElement $opc)
	{
		$o = new Opcion();
		/// Le asignamos el indice de la opcion y lo incrementamos.
		$o->index = $this->current_opcion_index++;
		
		$o->nombre_oracle = (string)$opc['nombre'];
		$o->texto = mb_convert_encoding((string)$opc->texto, 'ISO_8859-1', 'UTF-8');
		$o->requerida = false;
		$req = (string)$opc['requerida'];
		if ($req != null && $req == 1)
			$o->requerida = true;
		$sinvalor = (string)$opc['sinvalor'];
		if ($sinvalor != null)
			$o->sinvalor = $sinvalor;
		return $o;
	}

	/**
	 * Formatea un mensaje de error de SimpleXml. 
	 * @param unknown_type $error
	 * @return string
	 */
	private function display_xml_error($error)
	{
		$return = "";
	
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "Warning $error->code: ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "Error $error->code: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "Fatal Error $error->code: ";
				break;
		}
	
		$return .= trim($error->message) .
		"<br/>  Line: $error->line" .
		"<br/>  Column: $error->column";
	
		if ($error->file) {
			$return .= "<br/>  File: $error->file";
		}
	
		return "$return<br/>--------------------------------------------<br/>";
	}	
}

?>