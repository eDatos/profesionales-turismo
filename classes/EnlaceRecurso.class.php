<?php
require_once(__DIR__."/../lib/DateHelper.class.php");
require_once(__DIR__."/../lib/DbHelper.class.php");
require_once(__DIR__."/../lib/RowDbIterator.class.php");
require_once(__DIR__."/../lib/DocumentoAlVuelo.class.php");

/**
 *	Tipos de enlaces
 */
define("ENLACE_FICHERO_LOCAL", 0);
define("ENLACE_ALVUELO_LOCAL", 1);
define("ENLACE_URL_LOCAL", 2);
define("ENLACE_URL_REMOTO", 3);

class EnlaceRecursoUtils
{
    public static function getPermisos()
    {
        // En caso de que el usuario no esté autenticado, no tiene ningún permiso especial...
        $permisos="";
        if (isset($_SESSION["auth"]))
        {
            $auth = $_SESSION["auth"];
            $permisos=$auth->perm;
        }
        return $permisos;
    }
    
    public static function checkPermisos($permisos_requeridos,$permisos_usuario)
    {
        //PageHelper::have_any_perm($permisos_usuario);
        $allowed = FALSE;
        $perm = new Permisos();
        if (!is_array($permisos_requeridos))
            $permisos_requeridos = explode(",",$permisos_requeridos);
            foreach ($permisos_requeridos as $p)
            {
                if ($perm->have_perm($p, $permisos_usuario))
                {
                    $allowed = TRUE;
                    break;
                }
            }
            
            return $allowed;
    }
    
    public static function getMymeType($nombre)
    {
        $salida=mime_content_type($nombre);
        if($salida==FALSE)
            $salida=null;
        /*
        $ext = pathinfo($nombre, PATHINFO_EXTENSION);
        if(isset($ext))
        {
            $ext=strtolower($ext);
            switch($ext)
            {
                case '.txt':
            }
        }
        */
        return $salida;
    }
}

/**
 * Esta clase representa un enlace a un recurso.
 * @author 
 *
 */
class EnlaceRecurso 
{
    var $inicializado;
    
	var $id;
	var $uuid;
	var $pagina;
	var $orden;
	var $numEnlace;
	var $permisos;
	var $get_enabled;
	var $post_enabled;
	var $siempre_visible;
	var $nombre;
	var $ubicacion;
	var $tipo;
	var $target;
	var $content_type;
	var $inline;
	var $icono;
	var $desc_larga;
	var $desc_corta;
	var $tooltip;

	var $habilitado;
	
	public function __construct()
	{
	    $this->inicializado = false;
	}	
	
	public static function Load($uuid_solicitado)
	{
	    if(!isset($uuid_solicitado))
	        return false;
	    
        $enlace=false;
        
	    $permisosObtenidos=EnlaceRecursoUtils::getPermisos();
	    $db=new Istac_Sql();
	    $sql = DbHelper::prepare_sql("select id_recurso,rawtohex(uuid) as uuid_hex,id_pagina,orden,id_num_enlace,permisos,metodos_permitidos,siempre_visible,nombre,ubicacion,tipo_recurso,target,mty_enc,inline,icono,desc_larga,desc_corta,tooltip from tb_enlaces_recursos where activado='S' and (hextoraw(:uuid_solicitado)=uuid)",array(":uuid_solicitado" => $uuid_solicitado));
	    $db->query($sql);
	    if($db->next_record())
	    {
	        $enlace=new EnlaceRecurso();
	        $enlace->id=$db->f('id_recurso');
	        $enlace->uuid=$db->f('uuid_hex');
	        $enlace->pagina=$db->f('id_pagina');
	        $enlace->orden=$db->f('orden');
	        $enlace->numEnlace=$db->f('id_num_enlace');
	        $enlace->permisos=$db->f('permisos');
	        $metodos=$db->f('metodos_permitidos');
	        $enlace->get_enabled=(($metodos==0)||($metodos==2));
	        $enlace->post_enabled=(($metodos==1)||($metodos==2));
	        $enlace->siempre_visible=($db->f('siempre_visible')=='S');
	        $enlace->nombre=$db->f('nombre');
	        $enlace->ubicacion=$db->f('ubicacion');
	        $enlace->tipo=$db->f('tipo_recurso');
	        $enlace->target=$db->f('target');
	        $enlace->content_type=$db->f('mty_enc');
	        $enlace->inline=($db->f('inline')=='S');
	        $enlace->icono=$db->f('icono');
	        $enlace->desc_larga=$db->f('desc_larga');
	        $enlace->desc_corta=$db->f('desc_corta');
	        $enlace->tooltip=$db->f('tooltip');
	        
	        $enlace->habilitado=true;
	        if(!is_null($enlace->permisos))
	        {
	            if($enlace->permisos=="")
	            {
	                // Nadie tiene los permisos requeridos.
	                return false;
	            }
	            if(!EnlaceRecursoUtils::checkPermisos($enlace->permisos,$permisosObtenidos))
	            {
	                // El usuario no tiene los permisos necesarios.
	                return false;
	            }
	        }
	        if(isset($enlace->content_type)==false)
	            $enlace->content_type=getMymeType($enlace->nombre);
            $enlace->inicializado = true;
	    }
	    return $enlace;
	}
	
	private function selfCheck()
	{
	    if($this->inicializado==false)
	        throw new Exception("Objeto no inicializado.");
	}
	
	private function metodoCheck($metodo)
	{
	    if(($metodo=='GET')&&($this->get_enabled))
	        return;
        if(($metodo=='POST')&&($this->post_enabled))
            return;
        throw new Exception("Método de acceso no permitido.");
	}
	
	private function documentoAlVueloCheck($tipoDocumento)
	{
	    if(is_callable($tipoDocumento)==true)
	        return;
        throw new Exception("Tipo de documento desconocido.");
	}
	
	public function getID()
	{
	    $this->selfCheck();
	    return $this->id;
	}
	
	public function getOrden()
	{
	    $this->selfCheck();
	    return $this->orden;
	}
	
	public function getPagina()
	{
	    $this->selfCheck();
	    return $this->pagina;
	}
	
	public function getDescLarga()
	{
	    $this->selfCheck();
		return $this->desc_larga;
	}
	
	public function getDescCorta()
	{
	    $this->selfCheck();
	    return $this->desc_corta;
	}
	
	public function getUbicacion()
	{
	    $this->selfCheck();
	    return $this->ubicacion;
	}
	
	private function getUrlEfectiva()
	{
	    if($this->habilitado==false)
	        return '';
	    
	    switch($this->tipo)
	    {
	        case ENLACE_FICHERO_LOCAL:
	        case ENLACE_ALVUELO_LOCAL:
	            // TODO: Estudiar alternativas seguras a la hora de calcular la url que procesará estas peticiones.
	            $urlObjetivo=$_SERVER["PHP_SELF"];
	            $urlObjetivo.=((strpos($urlObjetivo,'?')==FALSE) ? "?":"&");
	            $urlObjetivo.="uuid=".$this->uuid;
	            return $urlObjetivo;
	        case ENLACE_URL_LOCAL:
	        case ENLACE_URL_REMOTO:
	            return $this->ubicacion;
	    }
	    return '';
	}
	
	public function render()
	{
	    $this->selfCheck();
	    $urlObjetivo=$this->getUrlEfectiva();
	    $salida=($this->habilitado)?'':'<span class="disable-links">';
	    $salida.='<A class="enlace" HREF="'.$urlObjetivo.'"';
	    $salida.=(isset($this->target))?' TARGET="'.$this->target.'">':'>';
	    if(isset($this->icono))
	    {
	        $salida.='<IMG TITLE="'.$this->tooltip.'" SRC="'.$this->icono.'">';
	    }
	    else
	    {
	        $salida.=$this->desc_larga;
	    }
	    $salida.='</A>';
	    $salida.=($this->habilitado)?'':'</span>';
		return $salida;
	}
	
	public function execute()
	{
	    $this->selfCheck();
	    $metodo=$_SERVER['REQUEST_METHOD'];
	    if(isset($metodo))
	        $metodo=strtoupper($metodo);
	    switch($this->tipo)
	    {
	        case ENLACE_FICHERO_LOCAL:
	            $this->metodoCheck($metodo);
	            if(isset($this->content_type))
	                header("Content-type: ".$this->content_type);
                header("Pragma: public");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Disposition: ".(($this->inline) ? "inline":"attachment")."; filename=".$this->nombre);
                readfile($this->ubicacion);
                break;
	        case ENLACE_ALVUELO_LOCAL:
	            $this->metodoCheck($metodo);
	            $this->documentoAlVueloCheck($this->ubicacion);
	            $contenido=forward_static_call($this->ubicacion);
	            if(!isset($contenido))
	                throw new Exception("Contenido nulo.");
                if(isset($this->content_type))
                    header("Content-type: ".$this->content_type);
                header("Pragma: public");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Disposition: ".(($this->inline) ? "inline":"attachment")."; filename=".$this->nombre);
                echo $contenido;
                break;
	    }
	}
}

/**
 * Esta clase representa una colección de enlaces al mismo recurso.
 * @author 
 *
 */
class GrupoRecurso
{
    var $enlaces;
        
    /**
     * Función que carga los enlaces para una página determinada
     * TODO: montar el tema de el array de enlaces por cada recurso...
     */
    public static function loadGruposEnlaces($pagina)
    {
        $salida=array();
        
        $db=new Istac_Sql();
        if(!isset($pagina))
            $sql = "select id_recurso,rawtohex(uuid) as uuid_hex,id_pagina,orden,id_num_enlace,permisos,metodos_permitidos,siempre_visible,nombre,ubicacion,tipo_recurso,target,mty_enc,inline,icono,desc_larga,desc_corta,tooltip from tb_enlaces_recursos where activado='S' order by orden,id_num_enlace";
            else
                $sql = DbHelper::prepare_sql("select id_recurso,rawtohex(uuid) as uuid_hex,id_pagina,orden,id_num_enlace,permisos,metodos_permitidos,siempre_visible,nombre,ubicacion,tipo_recurso,target,mty_enc,inline,icono,desc_larga,desc_corta,tooltip from tb_enlaces_recursos where activado='S' and (id_pagina='-' or id_pagina=:pagina) order by orden,id_num_enlace",array(":pagina" => (string)$pagina));
        $db->query($sql);
        
        $permisosObtenidos=null;
        $ordenActual=999;
        $grupo=null;
        while($db->next_record())
        {
            $enlace=new EnlaceRecurso();
            $enlace->id=$db->f('id_recurso');
            $enlace->uuid=$db->f('uuid_hex');
            $enlace->pagina=$db->f('id_pagina');
            $enlace->orden=$db->f('orden');
            $enlace->numEnlace=$db->f('id_num_enlace');
            $enlace->permisos=$db->f('permisos');
            $metodos=$db->f('metodos_permitidos');
            $enlace->get_enabled=(($metodos==0)||($metodos==2));
            $enlace->post_enabled=(($metodos==1)||($metodos==2));
            $enlace->siempre_visible=($db->f('siempre_visible')=='S');
            $enlace->nombre=$db->f('nombre');
            $enlace->ubicacion=$db->f('ubicacion');
            $enlace->tipo=$db->f('tipo_recurso');
            $enlace->target=$db->f('target');
            $enlace->content_type=$db->f('mty_enc');
            $enlace->inline=($db->f('inline')=='S');
            $enlace->icono=$db->f('icono');
            $enlace->desc_larga=$db->f('desc_larga');
            $enlace->desc_corta=$db->f('desc_corta');
            $enlace->tooltip=$db->f('tooltip');
            
            $enlace->habilitado=true;
            if(!is_null($enlace->permisos))
            {
                if($enlace->permisos=="")
                {
                    // Nadie tiene los permisos requeridos.
                    $enlace->habilitado=false;
                    if($enlace->siempre_visible==false)
                        continue;
                }
                if(!isset($permisosObtenidos))
                {
                    $permisosObtenidos=EnlaceRecursoUtils::getPermisos();
                }
                if(!EnlaceRecursoUtils::checkPermisos($enlace->permisos,$permisosObtenidos))
                {
                    // El usuario no tiene los permisos necesarios.
                    $enlace->habilitado=false;
                    if($enlace->siempre_visible==false)
                        continue;
                }
            }
                
            if($enlace->numEnlace<=$ordenActual)
            {
                $grupo=new GrupoRecurso();
                $grupo->enlaces=array();
                $salida[]=$grupo;
                $ordenActual=$enlace->numEnlace;
            }
            $enlace->inicializado = true;
            $grupo->enlaces[]=$enlace;
        }
        return $salida;
    }
}
?>