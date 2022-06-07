<?php
require_once (__DIR__.'/AlojaCuestionario.class.php');
require_once (__DIR__.'/AlojaHabitaciones.class.php');
require_once (__DIR__.'/AlojaUTMovimientos.class.php');
require_once (__DIR__.'/AlojaESP.class.php');
require_once (__DIR__.'/AlojaPersonal.class.php');
require_once (__DIR__.'/AlojaPrecios.class.php');
require_once (__DIR__.'/AlojaErrorCollection.class.php');
require_once (__DIR__.'/AlojaXmlHeader.class.php');
require_once (__DIR__.'/AlojaXml.class.php');

class AlojaXmlReader
{
	/**
	 * todo el contenido del archivo XML.
	 */
	var $archivoXml;
    
	var $errors;
	var $dom;
	
	public function __construct()
	{
		$this->errors = new AlojaErrorCollection();
	}
	
	/**
	 * Carga el archivo xml desde una cadena de texto.
	 * @param unknown_type $xml_content
	 */
    public function read_xml(& $xml_content)
    {
    	libxml_use_internal_errors(true);
    	$dom = new DOMDocument();
    	$ok = $dom->loadXML($xml_content);
    	$libxml_errors = libxml_get_errors();
    	if (!$ok || isset($libxml_errors) && !empty($libxml_errors) )
    	{
    		$dets = array();
    		foreach($libxml_errors as $liberr)
    		{
    			$dets[] = "Error: " . $liberr->message . " en la linea " . $liberr->line;
    		}
    		$this->errors->log_error(ERROR_GENERAL, "Ocurrió un error sintáctico al analizar el documento. Probablemente está intentando cargar un fichero que no es XML", $dets);
    		libxml_clear_errors();
    		libxml_use_internal_errors(false);
    		return false;
    	} 
    	libxml_clear_errors();
    	libxml_use_internal_errors(false);
    	
    	$this->archivoXml = $xml_content;
    	$this->dom = $dom;
    	
    	return true;
    }
    
    /**
     * Carga el archivo xml desde un archivo subido (por $_FILES).
     * @return boolean
     */
    public function read_xml_desde_request($userfile)
    {
    	global $establecimiento;
    	
		if(isset($userfile['name']))
			$nombre_archivo = $userfile['name'];
		else
			$nombre_archivo = "";
		if(isset($userfile['type']))
			$tipo_archivo = $userfile['type'];
		else
			$tipo_archivo = "";
		if(isset($userfile['size']))
			$tamano_archivo = $userfile['size'];
		else
			$tamano_archivo = "";
        
		$pos=strrpos($nombre_archivo,'.');
		$extension=substr($nombre_archivo,$pos+1);
		
        if (strcasecmp($extension, "xml"))
        {
            $this->errors->log_error(ERROR_GENERAL, "La extensión del archivo ha de ser xml");
            return false;
        }
        
        $xml_content = null;
        $tmp_filename = $userfile['tmp_name'];
        if($fd = fopen($tmp_filename,'r'))
        {
            $xml_content=fread($fd,filesize($tmp_filename));
            fclose($fd);    
        }  
        else 
        {
            $this->errors->log_error(ERROR_GENERAL, "Ocurrió algún error al subir el archivo. No se pudo leer el archivo XML.");
            return false;
        }
        
        // Eliminamos los caracteres BOM si existen.
        $xml_content=ltrim($xml_content,"\xef\xbb\xbf");
        
        if($this->read_xml($xml_content)==true)
        {
        	// Si el elemento raíz del XML es APARTAMENTOS, asumimos que se trata de una encuesta de alojamiento del INE.
        	// Una alternativa consiste en validar el XML contra el esquema del INE (https://arce.ine.es/ARCE/ficheros/schemaTurismoApartamentos.xsd).
        	if(strtoupper($this->dom->documentElement->tagName)=="APARTAMENTOS")
        	{
        		$this->errors->log_error(ERROR_GENERAL,"Por favor, cargue el fichero de la Encuesta de Alojamiento Turístico del ISTAC. Probablemente está intentando cargar un fichero con datos de la Encuesta de Ocupación en Alojamientos Turísticos del INE (Apartamentos Turísticos).","El documento contiene un elemento raíz no reconocido. Probablemente está intentando cargar un fichero con datos de la Encuesta de Ocupación en Alojamientos Turísticos (ARCE) del INE. Más información en \"https://arce.ine.es/\".");
        		return false;
        	}
        	return true;
        }
        return false;
    }
    
    /**
     * Valida el xml contra un XSD para saber si cumple el esquema obligado 
     */
    public function validarEsquemaXml($file_xsd)
    {
    	if ($this->dom == null)
    	{
    		/// No se ha cargado aun el xml
    		return false;
    	}
    	
    	libxml_use_internal_errors(true);
    	$ok = $this->dom->schemaValidate($file_xsd);
    	$libxml_errors = libxml_get_errors();
    	if (!$ok || isset($libxml_errors) && !empty($libxml_errors) )
    	{
    		$err_dets = array();
    		foreach($libxml_errors as $liberr)
    		{
    			$err_dets[] = $liberr->message . " en la linea " . $liberr->line;
    		}
    		
    		$this->errors->log_error(ERROR_GENERAL,"Ocurrió un error de esquema al analizar el documento. Por favor, comuníqueselo a la empresa que ha desarrollado su programa o bien remita por correo electrónico el fichero al ISTAC y le indicaremos las correcciones a realizar:", $err_dets);
    		 
    		
    		libxml_clear_errors();
    		libxml_use_internal_errors(false);
    		return false;
    	}
    	libxml_clear_errors();
    	libxml_use_internal_errors(false);
    	    	
    	return $ok;
    }
    
    /**
     * Obtiene el mes y el año que aparece en el archivo
     */
    public function obtenerFechaReferencia()
    {
    	$fecha = array('mes' => null, 'ano' => null);
    	
    	if ($this->dom == null)
    	{
    		/// No se ha cargado aun el xml
    		return $fecha;
    	}
    	
    	$xp = new DOMXPath($this->dom);
    	$m = $xp->query("//CABECERA/FECHA_REFERENCIA/MES");
    	foreach($m as $mes)
    	{
    		$fecha['mes'] = (int)$mes->nodeValue;
    	}
    	$a = $xp->query("//CABECERA/FECHA_REFERENCIA/ANYO");
    	foreach($a as $ano)
    	{
    		$fecha['ano'] = (int)$ano->nodeValue;
    	}  
    	return $fecha;  	
    }
    
    /**
     * Obtener el listado de datos que aparecen en la cabecera.
     */
    public function obtenerDatosCabecera()
    {
    	if ($this->dom == null)
    	{
    		return null;
    	}
    	
    	// Obtener los datos que aparecen en la cabecera
    	$m = $this->getChildrenMap("//CABECERA", $this->dom->encoding);
	
    	$aloja_cabecera = new AlojaXmlHeader();
    	$aloja_cabecera->razon_social = $this->getValue($m, "RAZON_SOCIAL", null);
    	$aloja_cabecera->nombre_establecimiento = $this->getValue($m, "NOMBRE_ESTABLECIMIENTO");
    	$aloja_cabecera->cif_nif = $this->getValue($m, "CIF_NIF");
    	$aloja_cabecera->numero_registro = $this->getValue($m, "NUMERO_REGISTRO");
    	$aloja_cabecera->direccion = $this->getValue($m, "DIRECCION");
    	$aloja_cabecera->codigo_postal = $this->getValue($m,"CODIGO_POSTAL");
    	$aloja_cabecera->localidad = $this->getValue($m,"LOCALIDAD");
    	$aloja_cabecera->municipio = $this->getValue($m,"MUNICIPIO");
    	$aloja_cabecera->provincia = $this->getValue($m,"PROVINCIA");
    	$aloja_cabecera->telefono_1 = $this->getValue($m,"TELEFONO_1");
    	$aloja_cabecera->telefono_2 = $this->getValue($m,"TELEFONO_2");
    	$aloja_cabecera->fax_1 = $this->getValue($m,"FAX_1");
    	$aloja_cabecera->fax_2 = $this->getValue($m,"FAX_2");
    	$aloja_cabecera->tipo = $this->getValue($m,"TIPO");
    	$aloja_cabecera->categoria = $this->getValue($m,"CATEGORIA");
    	$aloja_cabecera->habitaciones = $this->getValue($m,"HABITACIONES");
    	$aloja_cabecera->plazas_disponibles_sin_supletorias = $this->getValue($m,"PLAZAS_DISPONIBLES_SIN_SUPLETORIAS");
    	$aloja_cabecera->url = $this->getValue($m,"URL");
    	 
    	return $aloja_cabecera;
    }
    
    
    /**
     * Genera el objeto cuestionario, haciendo corresponder los paises y provincias a las UT segun la tabla especificada.
     * @param unknown_type $lookup_ut: Tabla de correspondencia entre paises que aparecen en el xml y unidades territoriales.
     */
    public function generarCuestionario($enc, $lookup_ut)
    {
    	if ($this->dom == null)
    	{
    		return null;
    	}    	   	

    	$enc->tipo_carga = TIPO_CARGA_XML;
    	$enc->dias_abierto = (int)$this->getNodeValue("//CABECERA/DIAS_ABIERTO_MES_REFERENCIA");
    	
    	$enc->mov_por_ut = $this->generarMovimientos($enc, $lookup_ut);
    	$enc->habitaciones = $this->generarHabitaciones();
    	$enc->precios = $this->generarPrecios();
    	$enc->personal = $this->generarPersonal();
    	
    	return $enc;
    }
    
    /** 
     * Genera los movimientos por pais o prov o isla a partir de los datos del xml (/ENCUESTA/ALOJAMIENTO)
     * @param unknown_type $lookup_ut
     * @return multitype:Ambigous <NULL, AlojaUTMovimientos>
     */
    private function generarMovimientos($enc, $lookup_ut)
    {
    	$mov_xml = $this->obtenerMovimientosXml();
    	
    	$result = array();
    	foreach($mov_xml as $mov)
    	{
    		///Comprobar que ESPAÑA está desglosado en provincias.
    		if (!strcasecmp($mov['cod_ppi'], "ESP"))
    		{
    			$enc->val_errors->log_error(ERROR_MOVIMIENTOS,"Los datos referentes a España deben ser desglosados en provincias.");
    			continue;
    		}
    		
    		/// Comprobar que el pais o la provincia o isla indicadas son aceptadas
    		if ($mov['es_pais'])
    		{
    			$pais_aceptado = array_key_exists("PAIS:" . $mov['cod_ppi'], $lookup_ut);
    			if(!$pais_aceptado)
    			{
    				// REGLA 4 del BOC
    				$enc->val_errors->log_error(ERROR_MOVIMIENTOS,"Según la norma ISO 3166-1 alfa 3, no existe ningún país con la codificación " . $mov['cod_ppi'] . ", o el valor no es aceptado actualmente.");
    				continue;
    			}
    			
    			$id_ut = $lookup_ut[ "PAIS:" . $mov['cod_ppi'] ];
    		}
    		else 
    		{
    			$provisla_aceptada = array_key_exists("PRIS:" . $mov['cod_ppi'], $lookup_ut);
    			if(!$provisla_aceptada)
    			{
    				// REGLA 5 del BOC
    				$enc->val_errors->log_error(ERROR_MOVIMIENTOS,"Según la norma NUTS_III, no existe ninguna provincia con la codificación " . $mov['cod_ppi'] . ", o el valor no es aceptado actualmente.");
    				continue;
    			}
    			
    			$id_ut = $lookup_ut[ "PRIS:" . $mov['cod_ppi'] ];
    		}

    		//ASSERT: id_ut contiene el codigo corespondiente al pais del movimiento.
    		
    		$aloja_mov = null;
    		if (!isset($result[$id_ut]))
    		{
    			/// No existe aun ningun movimiento para la UT indicada, crear uno nuevo.
    			$aloja_mov = new AlojaUTMovimientos();
    			/// TODO: Calculo de presentes comienzo mes cuando se sube el xml.
    			$aloja_mov->presentes_comienzo_mes = 0;
    			$result[ $id_ut ] = $aloja_mov;
    		}
    		else 
    		{
    			$aloja_mov = $result[ $id_ut ];
    		}
    		
    		/// Ya existen datos para la ut dada, añadir los nuevos.
    		foreach($mov['movimientos'] as $dia => $esp)
    		{
    			if (!isset($aloja_mov->movimientos[(int)$dia]))
    			{ 
    				$aloja_mov->movimientos[(int)$dia] = $esp;
    			}
    			else 
    			{
    				$e_esp = $aloja_mov->movimientos[(int)$dia];
    				$e_esp->sumar($esp);
    			}
    		}
    		
    		/// Calculo de presentes comienzo mes cuando se sube el xml a partir de los datos del dia 1
    		if (isset($aloja_mov->movimientos[1]))
    		{
    			$esp = $aloja_mov->movimientos[1];
    			$aloja_mov->presentes_comienzo_mes = $esp->pernoctaciones - $esp->entradas + $esp->salidas;
    		}
    	}
    	return $result;
    }
    
    /** 
     * Devuelve los movimientos que aparecen en el xml
     * @return Array de objetos AlojaXmlPaisProvIsla. 
     */
    private function obtenerMovimientosXml()
    {
    	$xp = new DOMXPath($this->dom);
    	$m = $xp->query("/ENCUESTA/ALOJAMIENTO/*");
    	if ($m == null)
    	{
    		return null;
    	}
    	 
    	$movs = array();
    	foreach($m as $residencia)
    	{
    		/// movimientos por (pais o prov o isla).
    		$aloja_ppi = array();

    		//-- Inicializar el id de pais o provincia o isla, segun corresponda.
    		$res_pais = $residencia->getElementsByTagName("ID_PAIS");
    		if ($res_pais != null && $res_pais->length > 0)
    		{
    			///Es un id de pais
    			$aloja_ppi['es_pais'] = true;
    			$aloja_ppi['cod_ppi'] = trim($res_pais->item(0)->nodeValue);
    		}
    		else
    		{
    			///Es un id de provincia o isla
    			$res_pais = $residencia->getElementsByTagName("ID_PROVINCIA_ISLA");
    			$aloja_ppi['es_pais'] = false;
    			$id_pais_prov = trim($res_pais->item(0)->nodeValue);
    			///Caso especial en el que se indica el id de provincia o isla sin el "ES" inicial.
    			if (strlen($id_pais_prov) == 3)
    				$id_pais_prov = "ES" . $id_pais_prov;
    			$aloja_ppi['cod_ppi'] = $id_pais_prov;
    		}

    		//-- Inicializar los movimientos para la ut.
    		$ppi_movs = array();
    		$res_movs = $residencia->getElementsByTagName("MOVIMIENTO");
    		foreach($res_movs as $res_mov)
    		{
    			$s = simplexml_import_dom($res_mov);
    			$esp = new AlojaESP();
    			$esp->entradas = (int)trim($s->ENTRADAS);
    			$esp->salidas = (int)trim($s->SALIDAS);
    			$esp->pernoctaciones = (int)trim($s->PERNOCTACIONES);
    			
    			if(($esp->entradas!=0)||($esp->salidas!=0)||($esp->pernoctaciones!=0))
    			{
    			    $n_dia = (string)trim($s->N_DIA);
    			    
    			    $ppi_movs[ $n_dia ] = $esp;
    			}
    		}
    		$aloja_ppi['movimientos'] = $ppi_movs;
    		
    		if(count($ppi_movs)>0)
    		    $movs[] = $aloja_ppi;
    	}
    	return $movs;
    }
    
    private function generarHabitaciones()
    {
    	$xp = new DOMXPath($this->dom);
    	$m = $xp->query("/ENCUESTA/HABITACIONES/*");
    	if ($m == null)
    	{
    		return null;
    	}
    	
    	$habs = array();
    	foreach($m as $hab_mov)
    	{
    		$s = simplexml_import_dom($hab_mov);
    		$hab = new AlojaHabitaciones();
    		$hab->supletorias = (int)trim($s->PLAZAS_SUPLETORIAS);
    		$hab->uso_individual = (int)trim($s->HABITACIONES_DOBLES_USO_INDIVIDUAL);
    		$hab->uso_doble = (int)trim($s->HABITACIONES_DOBLES_USO_DOBLE);
    		$hab->otras = (int)trim($s->HABITACIONES_OTRAS);
    		
    		$hab_dia = (string)trim($s->HABITACIONES_N_DIA);
    		$habs [ (int)$hab_dia ] = $hab;    		
    	}
    	
    	// SI TODOS LOS DATOS ESTAN A 0, SE CONSIDERA QUE ESTA VACIO.
    	foreach($habs as $entrada_hab)
    	{
    		if ($entrada_hab->supletorias != 0 || $entrada_hab->uso_individual != 0 || $entrada_hab->uso_doble != 0 || $entrada_hab->otras != 0)
    			return $habs;
    	}
    	
    	// Se llega aqui si todos los valores estan a 0.
    	return null;
    }
    
    private function generarPrecios()
    {
    	$m = $this->getChildrenMap("/ENCUESTA/PRECIOS", $this->dom->encoding);
    	if ($m == null)
    		return null;
    	
    	$pts = new AlojaPrecios();
    	$pts->revpar_mensual = (float)$this->getValue($m, "REVPAR_MENSUAL");
    	$pts->adr_mensual = (float)$this->getValue($m, "ADR_MENSUAL"); 

    	/**TOUROPERADOR_TRADICIONAL*/
    	$pts->adr[TO_TRADICIONAL] = (float)$this->getValue($m, "ADR_TOUROPERADOR_TRADICIONAL"); 
    	$pts->pct[TO_TRADICIONAL] = (float)$this->getValue($m, "PCTN_HABITACIONES_OCUPADAS_TOUROPERADOR_TRADICIONAL");
    	/**EMPRESAS*/
    	$pts->adr[EMPRESAS] = (float)$this->getValue($m, "ADR_EMPRESAS"); 
    	$pts->pct[EMPRESAS] = (float)$this->getValue($m, "PCTN_HABITACIONES_OCUPADAS_EMPRESAS");
    	/**AGENCIA_DE_VIAJE_TRADICIONAL*/
    	$pts->adr[AGENCIA_TRADICIONAL] = (float)$this->getValue($m, "ADR_AGENCIA_DE_VIAJE_TRADICIONAL");
    	$pts->pct[AGENCIA_TRADICIONAL] = (float)$this->getValue($m, "PCTN_HABITACIONES_OCUPADAS_AGENCIA_TRADICIONAL");  
    	/**PARTICULARES*/
    	$pts->adr[PARTICULARES] = (float)$this->getValue($m, "ADR_PARTICULARES");
    	$pts->pct[PARTICULARES] = (float)$this->getValue($m, "PCTN_HABITACIONES_OCUPADAS_PARTICULARES"); 
    	/**GRUPOS*/
    	$pts->adr[GRUPOS] = (float)$this->getValue($m, "ADR_GRUPOS");
    	$pts->pct[GRUPOS] = (float)$this->getValue($m, "PCTN_HABITACIONES_OCUPADAS_GRUPOS"); 
    	/**INTERNET*/
    	$pts->adr[INTERNET] = (float)$this->getValue($m, "ADR_INTERNET");
    	$pts->pct[INTERNET] = (float)$this->getValue($m, "PCTN_HABITACIONES_OCUPADAS_INTERNET"); 
    	/**AGENCIA_DE_VIAJE_ONLINE*/
    	$pts->adr[AGENCIA_ONLINE] = (float)$this->getValue($m, "ADR_AGENCIA_DE_VIAJE_ONLINE");
    	$pts->pct[AGENCIA_ONLINE] = (float)$this->getValue($m, "PCTN_HABITACIONES_OCUPADAS_AGENCIA_ONLINE"); 
    	/**TOUROPERADOR_ONLINE*/
    	$pts->adr[TO_ONLINE] = (float)$this->getValue($m, "ADR_TOUROPERADOR_ONLINE");
    	$pts->pct[TO_ONLINE] = (float)$this->getValue($m, "PCTN_HABITACIONES_OCUPADAS_TOUROPERADOR_ONLINE");    	
    	/**OTROS*/
    	$pts->adr[OTROS] = (float)$this->getValue($m, "ADR_OTROS");
    	$pts->pct[OTROS] = (float)$this->getValue($m, "PCTN_HABITACIONES_OCUPADAS_OTROS");

    	
    	// SI TODOS LOS DATOS ESTAN A 0, SE CONSIDERA QUE ESTA VACIO.
    	if ($pts->adr_mensual != 0 || $pts->revpar_mensual != 0)
    		return $pts;
    	
    	if (isset($pts->adr))
    	{
    		foreach ($pts->adr as $entrada_adr)
    		{
    			if ($entrada_adr != 0)
    				return $pts;
    		}
    	}
    	 
    	if (isset($pts->num))
    	{
    		foreach ($pts->num as $entrada_adr)
    		{
    			if ($entrada_adr != 0)
    				return $pts;
    		}
    	}
    	if (isset($pts->pct))
    	{
    		foreach ($pts->pct as $entrada_adr)
    		{
    			if ($entrada_adr != 0)
    				return $pts;
    		}
    	}
    	
    	// Se llega aqui si todos los datos estan a 0.
    	return null;
    }
    
    /**
     * Genera DTO con los datos de personal.
     */
    private function generarPersonal()
    {
    	$m = $this->getChildrenMap("/ENCUESTA/PERSONAL_OCUPADO", $this->dom->encoding);
    	if ($m == null)
    		return null;
    	    	
    	$pers = new AlojaPersonal();
    	$pers->no_remunerado = (int)$this->getValue($m, "PERSONAL_NO_REMUNERADO");
    	$pers->remunerado_fijo = (int)$this->getValue($m, "PERSONAL_REMUNERADO_FIJO");
        $pers->remunerado_eventual = (int)$this->getValue($m, "PERSONAL_REMUNERADO_EVENTUAL");
        return $pers;
    }
    
    /**
     * Obtiene un diccionario de pares (nombre nodo, valor nodo) de los hijos del nodo cuya ruta se especifica, 
     * convirtiendo los textos a la codificacion especificada.
     * @param unknown_type $parentXPath
     * @return multitype:NULL |NULL
     */
    private function getChildrenMap($parentXPath, $encoding = 'ISO_8859-1')
    {
    	$xp = new DOMXPath($this->dom);
    	$m = $xp->query($parentXPath);
    	if (isset($m))
    	{
    		$par_node = $m->item(0);
    		if (isset($par_node))
    		{
	    		$ret = array();
	    		
	    		foreach ($par_node->childNodes as $child_node)
	    		{
	    			$ret[$child_node->nodeName] = mb_convert_encoding(trim($child_node->nodeValue), $encoding);
	    		}
	    		return $ret;
    		}
    	}
    	return null;
    }
    
    /**
     * Obtiene el valor del nodo cuyo path se indica, en la codificacion especificada.
     * @param unknown_type $nodePath
     * @param unknown_type $default
     */
    private function getNodeValue($nodePath, $encoding = 'ISO_8859-1', $default = null)
    {
    	$xp = new DOMXPath($this->dom);
    	$m = $xp->query($nodePath);
    	if (isset($m))
    	{
    		$child_node = $m->item(0);
    		if (isset($child_node))
    		{
    			return mb_convert_encoding(trim($child_node->nodeValue), $encoding);
    		}
    	}
    	return $default;    	
    }
    
    /**
     * Obtener el valor de la clave name mirando en el mapa, devolviendo default en caso de que no exista.
     * @param unknown_type $map
     * @param unknown_type $name
     * @param unknown_type $default
     * @return unknown
     */
    private function getValue($map, $name, $default = null)
    {
		if (isset($map[$name]))
    	{    	
    		return $map[$name];
    	}
    	return $default;
    }

}
?>