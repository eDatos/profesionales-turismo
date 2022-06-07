<?php

require_once(__DIR__."/EncuestaTemplate.class.php");

///Tags que definen el formato del archivo.
define("TOKEN_COMMENT", "#");
define("TOKEN_BLOQUE", "BLOQUE");
define("TOKEN_FIN_BLOQUE","FIN_BLOQUE");
define("TOKEN_PREGUNTA", "PREGUNTA");
define("TOKEN_FIN_PREGUNTA", "FIN_PREGUNTA");
define("TOKEN_IDENTIFICADOR", "IDENTIFICADOR");
define("TOKEN_FIN_IDENTIFICADOR", "FIN_IDENTIFICADOR");
define("TOKEN_TITULO", "TITULO");
define("TOKEN_FIN_TITULO", "FIN_TITULO");
define("TOKEN_COMENTARIO", "COMENTARIO");
define("TOKEN_FIN_COMENTARIO", "FIN_COMENTARIO");
define("TOKEN_TIPO", "TIPO");
define("TOKEN_FIN_TIPO", "FIN_TIPO");
define("TOKEN_COMENTARIO_GRABADOR", "COMENTARIO_GRABADOR");
define("TOKEN_FIN_COMENTARIO_GRABADOR", "FIN_COMENTARIO_GRABADOR");
define("TOKEN_N_VALORES", "N_VALORES");
define("TOKEN_FIN_N_VALORES", "FIN_N_VALORES");
define("TOKEN_VALORES", "VALORES");
define("TOKEN_FIN_VALORES", "FIN_VALORES");
define("TOKEN_TEXTOS_VALORES", "TEXTOS_VALORES");
define("TOKEN_FIN_TEXTOS_VALORES", "FIN_TEXTOS_VALORES");
define("TOKEN_PRESELECCIONADOS", "PRESELECCIONADOS");
define("TOKEN_FIN_PRESELECCIONADOS", "FIN_PRESELECCIONADOS");
define("TOKEN_N_OPCIONES", "N_OPCIONES");
define("TOKEN_FIN_N_OPCIONES", "FIN_N_OPCIONES");
define("TOKEN_OBLIGATORIAS", "OBLIGATORIAS");
define("TOKEN_FIN_OBLIGATORIAS", "FIN_OBLIGATORIAS");
define("TOKEN_TEXTOS_OPCIONES", "TEXTOS_OPCIONES");
define("TOKEN_FIN_TEXTOS_OPCIONES", "FIN_TEXTOS_OPCIONES");
define("TOKEN_NOMBRE_ORACLE", "NOMBRE_ORACLE");
define("TOKEN_FIN_NOMBRE_ORACLE", "FIN_NOMBRE_ORACLE");
define("TOKEN_TEXTOS_CABECERAS", "TEXTOS_CABECERAS");
define("TOKEN_FIN_TEXTOS_CABECERAS", "FIN_TEXTOS_CABECERAS");
define("TOKEN_POR_DEFECTO_VALOR", "POR_DEFECTO_VALOR");
define("TOKEN_FIN_POR_DEFECTO_VALOR", "FIN_POR_DEFECTO_VALOR");

/**
 * Lector de una encuesta (de expectativas) a partir de un archivo ".form".
 * Devuelve un objeto EncuestaTemplate (ver EncuestaTemplate.class.php) que contiene los datos
 * del archivo.
 */
class EncuestaTemplateFormReader 
{
	/// Mensaje de error si no se ha podido leer (fallo sintactico, ..)
	var $error;
	
	/// Todas las opciones tendran un indice unico (autoincremental en el orden de lectura).
	private $current_opcion_index;
	/**
	 * Carga el archvio .form dado obteniendo un objeto EncuestaTemplate con el contenido del archivo.
	 * @param unknown_type $filename
	 * @return boolean|EncuestaTemplate False si no se ha podido procesar el archivo correctamente.
	 */
	public function load($filename)
	{
		$this->current_opcion_index = 1;
		
		///Resultado
		$bloques = array();
		
		// Array asociativo temporal para almacenar los datos leidos antes de convertirlo a objeto Bloque.
		$lastbloque = null;
		// Array asociativo temporal para almacenar los datos leidos antes de convertirlo a objeto Pregunta.
		$lastpregunta = null;
		// Flags de control para saber si un tag interno está dentro de un bloque o de una pregunta.
		$dentro_bloque = false;
		$dentro_pregunta = false;
		
		$p = new Tokenizer($filename);	
		if ($p->eof())
			return false;
		
		$t = true;
		while (!$p->eof())
		{
			// Avanzar al proximo token.
			$t = $p->getnexttoken();
			if ($t === false)
			{
				$this->error = $p->error;
				return false;
			}
			
			if (isset($t))
			{
				switch($t)
				{
					case TOKEN_COMMENT:
						//Ignorar comentarios
						break;
					case TOKEN_BLOQUE:
						//Comienzo nuevo bloque.
						$dentro_bloque = true;
						$lastbloque = array();
						break;
					case TOKEN_FIN_BLOQUE:
						//fin bloque, convertir lo leido a objeto bloque.
						$bloques[] = $this->crear_bloque($lastbloque);
						$dentro_bloque = false;
						break;
					case TOKEN_PREGUNTA:
						//Comienzo nueva pregunta.
						$dentro_pregunta = true;
						$lastpregunta = array();
						break;
					case TOKEN_FIN_PREGUNTA:
						// fin pregunta, crear objeto con lo leido y añadirla al ultimo bloque.
						$preg = $this->crear_pregunta($lastpregunta);
						$bloques[count($bloques)-1]->addPregunta($preg);
						$dentro_pregunta = false;
						break;
					default:
						// Para los tags que no son BLOQUE o PREGUNTA, se lee su contenido como texto.
						$texto = $p->getinnertext($t);
						if ($dentro_bloque)
						{
							$lastbloque[$t] = $texto;
						}
						elseif ($dentro_pregunta)
						{
							$lastpregunta[$t] = $texto;
						}
						else
						{
							$this->error = "Error: Linea " . $p->num_line ." : El tag [$t] debe estar dentro de PREGUNTA o BLOQUE";
							return false;
						}
						break;
				}
			}
		} // fin while	
		$p->close();
		
		$encuesta = new EncuestaTemplate();
		$encuesta->bloques = $bloques;
		return $encuesta;
	}
	
	/**
	 * Convierte los datos del bloque almacenados en un array a un objeto Bloque, eliminando espacios
	 * iniciales y finales.
	 * @param unknown_type $barray
	 * @return Bloque
	 */
	private function crear_bloque($barray)
	{
		$b = new Bloque();
		if(isset($barray[TOKEN_IDENTIFICADOR])) $b->identificador = trim($barray[TOKEN_IDENTIFICADOR]);
		if(isset($barray[TOKEN_TITULO])) $b->titulo = trim($barray[TOKEN_TITULO]);
		if(isset($barray[TOKEN_COMENTARIO])) $b->comentario = trim($barray[TOKEN_COMENTARIO]);
		return $b;
	}
	
	/**
	 * Convierte los datos de la pregunta almacenados en un array a un objeto Pregunta, eliminando
	 * espacios iniciales y finales de los textos.
	 */
	private function crear_pregunta($parray)
	{
		$p = new Pregunta();
		$p->apartado = ""; // No hay tag de apartado.
		if(isset($parray[TOKEN_TITULO])) $p->titulo = trim($parray[TOKEN_TITULO]);
		if(isset($parray[TOKEN_TIPO])) $p->tipo = trim($parray[TOKEN_TIPO]);
		if(isset($parray[TOKEN_COMENTARIO])) $p->comentario = trim($parray[TOKEN_COMENTARIO]);
		if(isset($parray[TOKEN_COMENTARIO_GRABADOR])) $p->comentario_grabador = trim($parray[TOKEN_COMENTARIO_GRABADOR]);
		if(isset($parray[TOKEN_TEXTOS_CABECERAS])) $p->cabecera = trim($parray[TOKEN_TEXTOS_CABECERAS]);
	
		/// Valores
		$valores = explode(",",trim($parray[TOKEN_VALORES]));
		$t_valores = explode(",",trim($parray[TOKEN_TEXTOS_VALORES]));
		$num_valores = trim($parray[TOKEN_N_VALORES]);
		for ($i = 0;$i < $num_valores; $i++)
		{
			$vlr = new Valor();
			$vlr->num = trim($valores[$i]);
			$vlr->texto = trim($t_valores[$i]);
			$p->addValor($vlr);
		}
	
		///Valor por defecto
		$sinvalor = null;
		if (isset($parray[TOKEN_POR_DEFECTO_VALOR]))
		{
			$sinvalor = trim($parray[TOKEN_POR_DEFECTO_VALOR]);
		}
	
		/// Opciones
		$t_opcs = explode(",", trim($parray[TOKEN_TEXTOS_OPCIONES]));
		$ora_opcs = explode(",", trim($parray[TOKEN_NOMBRE_ORACLE]));
		$num_opcs = trim($parray[TOKEN_N_OPCIONES]);
		for ($i = 0;$i < $num_opcs; $i++)
		{
			$opc = new Opcion();
			$opc->index = $this->current_opcion_index++;
			
			$opc->texto = trim($t_opcs[$i]);
			$opc->nombre_oracle = trim($ora_opcs[$i]);
			$opc->sinvalor = $sinvalor;
			$p->addOpcion($opc);
		}
	
		/// Flag para opciones obligatorias
		if(isset($parray[TOKEN_OBLIGATORIAS]))
		{
			$t_req = trim($parray[TOKEN_OBLIGATORIAS]);
			if (strlen($t_req) > 0)
			{
				$reqs = explode(",", $t_req);
				foreach ($reqs as $req_index)
				{
					$p->opciones[$req_index-1]->requerida = true;
				}
			}
		}
	
		return $p;
	}
}

/**
 * Lector del archivo que reconoce los tokens del formato de archivo ".form" para realizar al proceso de parsing
 * sobre el mismo. 
 *
 */
class Tokenizer
{
	// Handler de archivo
	private $fh;
	// Buffer de lectura (una linea cada vez).
	private $buffer;
	// Longitud de la linea en el buffer.
	private $buffer_len;
	// Posicion actual de lectura en el buffer
	private $ic;
	// Numero de linea cargada en el buffer.
	private $num_line;

	// Correspondencia de tags iniciales y finales 
	public $tokens = array(
			TOKEN_BLOQUE 				=>  TOKEN_FIN_BLOQUE,
			TOKEN_PREGUNTA 				=>  TOKEN_FIN_PREGUNTA,
			TOKEN_IDENTIFICADOR 		=>  TOKEN_FIN_IDENTIFICADOR,
			TOKEN_TITULO 				=>  TOKEN_FIN_TITULO,
			TOKEN_COMENTARIO 			=>  TOKEN_FIN_COMENTARIO,
			TOKEN_TIPO 					=>  TOKEN_FIN_TIPO,
			TOKEN_COMENTARIO_GRABADOR 	=>  TOKEN_FIN_COMENTARIO_GRABADOR,
			TOKEN_N_VALORES 			=>  TOKEN_FIN_N_VALORES,
			TOKEN_VALORES 				=>  TOKEN_FIN_VALORES,
			TOKEN_TEXTOS_VALORES 		=>  TOKEN_FIN_TEXTOS_VALORES,
			TOKEN_PRESELECCIONADOS 		=>  TOKEN_FIN_PRESELECCIONADOS,
			TOKEN_N_OPCIONES 			=>  TOKEN_FIN_N_OPCIONES,
			TOKEN_OBLIGATORIAS 			=>  TOKEN_FIN_OBLIGATORIAS,
			TOKEN_TEXTOS_OPCIONES 		=>  TOKEN_FIN_TEXTOS_OPCIONES,
			TOKEN_NOMBRE_ORACLE 		=>  TOKEN_FIN_NOMBRE_ORACLE,
			TOKEN_TEXTOS_CABECERAS 		=>  TOKEN_FIN_TEXTOS_CABECERAS,
			TOKEN_POR_DEFECTO_VALOR 	=>  TOKEN_FIN_POR_DEFECTO_VALOR);

	// Contiene un mensaje con el ultimo error
	public $error;

	public function __construct($filename)
	{
		$this->fh = fopen($filename, "r");
		if ($this->fh != null)
		{
			$this->buffer = fgets($this->fh, 4096);
			$this->buffer_len = strlen($this->buffer);
			$this->ic = 0;
			$this->num_line = 1;
		}
	}

	/**
	 * Comprueba si se ha llegado al final del archivo.
	 * @return boolean
	 */
	public function eof()
	{
		if ($this->fh == null)
			return true;
		
		return feof($this->fh);
	}

	/**
	 * Cierra el handle de archivo.
	 * @return boolean false si no habia archivo abierto.
	 */
	public function close()
	{
		if ($this->fh == null)
			return false;
				
		fclose($this->fh);
	}

	/**
	 * Obtiene el siguiente caracter, si es necesario carga la siguiente linea en el buffer.
	 * Devuelve false si ya no hay mas caracteres que leer.
	 * @return boolean|unknown
	 */
	public function nextchar()
	{
		$c = $this->buffer[$this->ic];
		$this->ic++;
		if ($this->ic == $this->buffer_len)
		{
			if (!$this->nextline())
				return false;
		}
		return $c;
	}

	/**
	 * Carga la siguiente linea en el buffer. Devuelve false si ya se ha alcanzado el final del archivo.
	 * @return boolean Verdadero si se ha leido con exito la linea.
	 */
	public function nextline()
	{
		if (feof($this->fh))
			return false;

		$this->buffer = fgets($this->fh, 4096);
		if ($this->buffer !== false)
		{
			$this->buffer_len = strlen($this->buffer);
			$this->ic = 0;
			$this->num_line++;
			return true;
		}
		return false;
	}

	/**
	 * Obtiene el tag de final que corresponde al tag de inicio pasado.
	 * @param unknown_type $tipo
	 * @return NULL|multitype:
	 */
	private function endtoken($tipo)
	{
		return $this->tokens[$tipo];
	}

	/**
	 * Lee todo el contenido del tag pasado como texto.
	 * @param Token $t
	 * @return boolean|string
	 */
	function getinnertext($t)
	{
		$endtoken = "[".$this->endtoken($t)."]";
		return $this->read_until($endtoken);
	}

	/**
	 * Lee el nombre dle tag que acaba de encontrar.
	 * @param unknown_type $t
	 * @return Ambigous <boolean, string>
	 */
	private function read_tagname()
	{
		$endtoken = "]";
		return $this->read_until($endtoken);
	}
	
	/**
	 * Lee desde la posicion actual hasta que encuentra el string dado, devolviendo
	 * el texto leido, sin incluir el caracter $endtoken.
	 * @param string $endtoken
	 */
	private function read_until($endtoken)
	{
		/// Si se esta en el final del archivo, no se puede hacer la operacion.
		if (feof($this->fh))
			return false;
				
		$linea = "";
		/// Si no se encuentra en la linea actual, leer la siguiente.
		while (($pos = strpos($this->buffer, $endtoken, $this->ic)) === false)
		{
			/// Lo que queda de buffer va en la salida.
			if ($this->ic < $this->buffer_len)
				$linea .= substr($this->buffer, -($this->buffer_len - $this->ic));
			if (!$this->nextline())
				break;
		}
		/// Resto de buffer
		if ($this->ic < $this->buffer_len)
			$buf_rest = substr($this->buffer, -($this->buffer_len - $this->ic));
		/// Adjuntamos HASTA el endtoken (sin incluirlo).
		$linea .= substr($buf_rest, 0, strpos($buf_rest, $endtoken));
		
		///Se avanza el ic hasta justo despues del token (saltando $endtoken).
		$this->ic = min(array($pos + strlen($endtoken), $this->buffer_len));

		return $linea;
	}
	
	/**
	 * Lee el siguiente token del archivo, ignorando ' ','\t,'\r' y '\n'.
	 * @return boolean|Token False si no se h apodido obtener un token (p.ej. si se llega al final del archivo.
	 */
	public function getnexttoken()
	{
		// delete whitespace
		while (($c = $this->nextchar()) == ' ' || $c == "\t" || $c == "\n" || $c == "\r");

		if (feof($this->fh))
			return false;

		///  Una vez eliminados BLANCOS, una linea solo puede empezar por # (comentario)
		///  o por "[" (inicio de tag).
		if ($c == '#')
		{
			/// "Absorbe" el comentario.
			$this->nextline();
			return TOKEN_COMMENT;
		}
		else if ($c == "[")
		{
			/// Lee el texto (nombre del tag) hasta que encuentra un ].
			$tag = $this->read_tagname();
			return $tag;
		}
		/// Linea invalida.
		$this->error = "linea no válida: " . $this->num_line ." '$this->buffer'";
		return false;
	}


}

?>