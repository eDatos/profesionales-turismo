<?php

require_once(__DIR__."/../lib/RowDbIterator.class.php");
require_once(__DIR__."/../lib/Util.class.php");

define("ALTA", "0");
define("BAJA", "1");

define("RESULT_OK", "1");
define("RESULT_ERROR_HISTORICO_FECHA_ALTA_NULO", "-1");
define("RESULT_ERROR_HISTORICO_ESTADO_ALTA_CON_FECHA_BAJA", "-2");

class Establecimiento
{
	/* NOTA: Este codigo ha sido heredado de la versión anterior (datos_resultados.class.php) 
			 Solo se ha ajustado para desacoplarlo de var_get y de phplib.
	*/
	var $id_establecimiento;
	var	$nombre_establecimiento;
	
	var $fecha_baja;
	var $fecha_alta;
	var $estado;
	
	var $id_categoria;
	var $grupo_categoria;
    var $id_grupo_categoria;

	var $id_tipo_establecimiento;
	var $texto_id_tipo_establecimiento;
	var $texto_tipo_establecimiento;
	
	var $id_municipio;
	var $municipio;
	var $id_isla;
	var $nombre_isla;
	var $id_zona_turistica;
	
	//var $anyo_resultados;
	//var $trimestre_resultados;
	//var $ultimo_mes_resultados;
	
	var $texto_num_estrellas;
	var $num_plazas;
	var $num_habitaciones;
	var $num_plazas_supletorias;

	var $director;
	var $direccion;
	var $localidad;
	var $codigo_postal;
	var $telefono;
	var $telefono2;
	var $fax;
	var $fax2;
	var $email;
    var $email2;
    var $url;

    var $provincia;
    var $razon_social;
    var $cif_nif;
    
    var $nombre_contacto;
    var $nombre_explotacion;
    var $num_registro;
    
	
	function __construct()
	{
	}
	
	
	private function search($where)
	{
		$sql="	select id_establecimiento codigo, nombre_establecimiento nombre, tb_islas.nombre_isla isla, tb_municipios.nombre_municipio 
				from tb_establecimientos_unico, tb_islas, tb_municipios
				where " . (isset($where) ? ( $where . " AND ") : "") . "tb_islas.id_isla = tb_establecimientos_unico.id_isla 
				AND tb_municipios.id_isla      = tb_islas.id_isla
				AND tb_municipios.id_municipio = tb_establecimientos_unico.id_municipio
				ORDER BY id_establecimiento";
		
		$db = new Istac_Sql();
		$db->query($sql);
		return new RowDbIterator( $db, array( 'codigo','nombre','isla','nombre_municipio'));		
	}
	
	/**
	 * Obtiene el numero de plazas máximas en un dia.
	 */
	public function max_plazas_por_dia()
	{
		return $this->num_plazas + $this->num_plazas_supletorias;
	}
	
	/**
	 * Comprueba si el establecimiento se considera un hotel o no.
	 */
	public function es_hotel()
	{
		return $this->id_tipo_establecimiento !=3;
	}
	
	function searchByNombre($estab_nombre)
	{
		$letrasEspeciales=array();
		$letrasEspeciales["\\"]="\\\\";
		$letrasEspeciales["%"]="\\%";
		$letrasEspeciales["_"]="\\_";
		$letrasEspeciales["'"]="''";
		foreach ($letrasEspeciales as $varname => $varvalue)
		{
			$estab_nombre=str_replace($varname,$varvalue,$estab_nombre);
		}
		return $this->search("upper(nombre_establecimiento) LIKE '%".mb_strtoupper($estab_nombre,'ISO-8859-1')."%' ESCAPE '\'");
	}

	function searchByCodigo($estab_codigo)
	{
		if(!ctype_digit($estab_codigo))
			return RowDbIterator::zero();
		return $this->search("id_establecimiento = '".$estab_codigo."' ");		
	}
	
	function searchByCodigoMultiple($estids)
	{
		if(empty($estids))
			return RowDbIterator::zero();
		
		$ids=explode(",",$estids);
		foreach ($ids as $estid)
		{
			if(!ctype_digit($estid))
				return RowDbIterator::zero();
		}
		
		$where="'".implode("','", $ids)."'";
		$sql="	select id_establecimiento codigo, nombre_establecimiento nombre, tb_islas.nombre_isla isla, tb_municipios.nombre_municipio 
				from tb_establecimientos_unico, tb_islas, tb_municipios
				where id_establecimiento in (" . $where . ") AND tb_islas.id_isla = tb_establecimientos_unico.id_isla 
				AND tb_municipios.id_isla      = tb_islas.id_isla
				AND tb_municipios.id_municipio = tb_establecimientos_unico.id_municipio
				ORDER BY id_establecimiento";
		
		$db = new Istac_Sql();
		$db->query($sql);
		return new RowDbIterator($db, array( 'codigo','nombre','isla','nombre_municipio'));		
	}

	public function cargar_por_fecha($id_estab, $para_fecha)
	{
		$sql = DbHelper::prepare_sql("SELECT e.ID_ESTABLECIMIENTO,
				  e.NOMBRE_ESTABLECIMIENTO,
				  TO_CHAR(h.FECHA_ALTA, 'dd-mm-yyyy') FECHA_ALTA,
				  TO_CHAR(h.FECHA_BAJA, 'dd-mm-yyyy') FECHA_BAJA,
				  h.BAJA_DEFINITIVA,
				  TB_CATEGORIAS.ID_CATEGORIA, TB_CATEGORIAS.DESCRIPCION,
				  tb_tipo_establecimientos.id_tipo_establecimiento,
				  TB_TIPO_ESTABLECIMIENTOS.DESCRIPCION  AS TIPO, ge.descripcion AS GRUPODESC, ge.id_grupo AS GRUPO,
				  TB_MUNICIPIOS.ID_MUNICIPIO, TB_MUNICIPIOS.NOMBRE_MUNICIPIO,
				  TB_ISLAS.ID_ISLA, TB_ISLAS.NOMBRE_ISLA, TB_MUNICIPIOS.ID_ZONA_TURISTICA,
				  e.NOMBRE_DIRECTOR,
				  e.DIRECCION, e.LOCALIDAD, e.CODIGO_POSTAL,
				  e.TELEFONO1, e.TELEFONO2,
				  e.FAX, e.FAX2,
				  e.EMAIL, e.EMAIL2, e.URL,
				  e.PROVINCIA, e.RAZON_SOCIAL, e.CIF_NIF,
				  e.NOMBRE_CONTACTO, e.NOMBRE_EXPLOTACION, e.NUMERO_REGISTRO,
				  h.num_habitaciones, h.num_plazas, h.num_plazas_supletorias
				FROM TB_GRUPO_ESTABLECIMIENTOS ge
				INNER JOIN TB_GRUPO_TIPO_EST
				ON TB_GRUPO_TIPO_EST.ID_GRUPO = ge.ID_GRUPO
				RIGHT JOIN TB_TIPO_ESTABLECIMIENTOS
				ON TB_GRUPO_TIPO_EST.ID_TIPO_ESTABLECIMIENTO = TB_TIPO_ESTABLECIMIENTOS.ID_TIPO_ESTABLECIMIENTO
				INNER JOIN TB_CATEGORIAS
				ON TB_TIPO_ESTABLECIMIENTOS.ID_TIPO_ESTABLECIMIENTO = TB_CATEGORIAS.ID_TIPO_ESTABLECIMIENTO
				INNER JOIN TB_ESTABLECIMIENTOS_HISTORICO h
				ON TB_CATEGORIAS.ID_TIPO_ESTABLECIMIENTO = h.ID_TIPO_ESTABLECIMIENTO
				AND TB_CATEGORIAS.ID_CATEGORIA           = h.ID_CATEGORIA
				INNER JOIN TB_ESTABLECIMIENTOS_UNICO e
				ON e.ID_ESTABLECIMIENTO = h.ID_ESTABLECIMIENTO
				LEFT JOIN TB_MUNICIPIOS
				ON (TB_MUNICIPIOS.ID_MUNICIPIO = e.ID_MUNICIPIO AND TB_MUNICIPIOS.ID_ISLA = e.ID_ISLA)
				INNER JOIN TB_ISLAS
				ON e.ID_ISLA = TB_ISLAS.ID_ISLA
				WHERE (e.ID_ESTABLECIMIENTO = :estid) AND h.FECHA_ALTA <= TO_DATE(:t, 'dd-mm-yyyy') AND (TO_DATE(:t, 'dd-mm-yyyy') < h.FECHA_BAJA OR h.FECHA_BAJA IS NULL)", 
				array(':estid' => (string)$id_estab, ':t' => (string)$para_fecha->format('d-m-Y')));
		
		$db = new Istac_Sql();
		$db->query($sql);
		
		if ($db->next_record())
		{
			$this->id_establecimiento = $db->f('id_establecimiento');
			$this->nombre_establecimiento = $db->f('nombre_establecimiento');
			$fb = Datehelper::parseDate($db->f('fecha_baja'));
			$this->fecha_baja = ($fb === false)? null : $fb;
			$this->fecha_alta = Datehelper::parseDate($db->f('fecha_alta'));
			$this->estado = ($db->f("baja_definitiva") == 1)? BAJA : ALTA;
			$this->id_categoria = $db->f('id_categoria');
			$this->grupo_categoria = $db->f('descripcion');
			$this->id_tipo_establecimiento = $db->f('id_tipo_establecimiento');
			$this->texto_id_tipo_establecimiento = $db->f('tipo');
			$this->texto_tipo_establecimiento = $db->f('tipo');
			$this->id_grupo_categoria = $db->f('grupo');
			$this->id_municipio = $db->f('id_municipio');
			$this->municipio = $db->f('nombre_municipio');
			$this->id_isla = $db->f('id_isla');
			$this->nombre_isla = $db->f('nombre_isla');
			$this->id_zona_turistica = $db->f('id_zona_turistica');
			$this->director = $db->f('nombre_director');
			$this->direccion = $db->f('direccion');
			$this->localidad = $db->f('localidad');
			$this->codigo_postal = $db->f('codigo_postal');
			$this->telefono = $db->f('telefono1');
			$this->telefono2 = $db->f('telefono2');
			$this->fax = $db->f('fax');
			$this->fax2 = $db->f('fax2');
			$this->email = $db->f('email');
			$this->email2 = $db->f('email2');
			$this->url = $db->f('url');	
			$this->provincia = $db->f('provincia');
			$this->razon_social = $db->f('razon_social');
			$this->cif_nif = $db->f('cif_nif');
			$this->num_habitaciones = $db->f('num_habitaciones');
			$this->num_plazas = $db->f('num_plazas');
			$this->num_plazas_supletorias = $db->f('num_plazas_supletorias');
			
			$this->nombre_contacto = $db->f('nombre_contacto');
			$this->nombre_explotacion = $db->f('nombre_explotacion');
			$this->num_registro = $db->f('numero_registro');
			
			//CABECERA: NÚMERO DE ESTRELLAS DEL HOTEL
			if ($this->id_isla==5 || $this->id_isla==6 || $this->id_isla==7 )
			{
				$this->texto_num_estrellas='1-5';
			}
			else
			{
				$this->texto_num_estrellas=($this->id_grupo_categoria=='1')?'1, 2 y 3':'4 y 5';
			}
			
			return true;
		}
		
		return false;
	}

	function get_grupo()
	{
		return ($this->id_grupo_categoria == null)? 0 : $this->id_grupo_categoria;
	}
	
// 	function cargar_old($id_estab)
// 	{
// 		$fecha_now = date('Y-m-d');	
           
// 		$this->id_establecimiento = $id_estab;
		             
// 		/// La logica para comprobar permisos se realiza en la llamada.
					                       
// 		/*if($perm->have_perm("admin")||$perm->have_perm("admin_istac")|| $perm->have_perm("grabador")) 
// 		{
//             $this->user_id = $auth->auth["uid"];            
//              if ($id == 0)
// 			    $this->id_establecimiento = $util->var_get("id_hotel");
//              else 
//             	$this->id_establecimiento = $id;
// 		}
// 		else
// 		{
// 			$this->user_id = $auth->auth["uid"];
// 			$sql="SELECT * from tb_usuario_hotel where user_id='".$this->user_id."'"; 
                        
// 			$this->db->query($sql);                                    
// 			$this->db->next_record();
// 			$this->id_establecimiento = $this->db->f("id_hotel");                       
// 		}*/
		
// 		if($this->id_establecimiento != "")
//         {
// 		//ISLA DEL ESTABLECIMIENTO        
        
// 		$sql="SELECT  est.id_isla, i.nombre_isla, est.nombre_director, est.direccion, est.localidad  FROM tb_establecimientos_unico est, tb_islas i WHERE id_establecimiento='".$this->id_establecimiento."'  and i.id_isla = est.id_isla"; 	                    
        
//         $this->db->query($sql);
// 		$this->db->next_record();
// 		$this->id_isla      = $this->db->f('id_isla');
// 		$this->nombre_isla  = $this->db->f('nombre_isla');
// 		$this->director     = $this->db->f('nombre_director');
//         $this->direccion    = $this->db->f('direccion');
//         $this->localidad    = $this->db->f('localidad');
                                             
// 		//DATOS DE TRIMESTRE Y anyo
// 		$sql="SELECT anyo anyo,TRIMESTRE FROM (";
// 		$sql.=" select * from tb_enexho_variables order by to_number(anyo) desc, to_number(TRIMESTRE) desc )";
// 		$sql.=" WHERE rownum<2";
                      
                      
// 		$this->db->query($sql);
		                
// 		if($this->db->next_record($sql))
// 		{		
// 			//****TRIMESTRE******
// 			$this->trimestre_resultados=$this->db->f('trimestre');
// 			//****ANYO******
// 			$this->anyo_resultados=$this->db->f("anyo");
// 		}
// 		else
// 		{
// 			$this->trimestre_resultados="";
// 			$this->anyo_resultados="";
// 		}
// 		//*****ULTIMO MES DEL TRIMESTRE  *******
// 		$this->ultimo_mes_resultados=($this->trimestre_resultados-1)*3+3;

                		
// 		//CATEGORIA DEL HOTEL
//         //falta añadir en el sql los campos de: num_plazas_sc y num_plazas_cc
                       
// // 		$sql= "select c.id_categoria                      
// //                FROM   tb_establecimientos_historico e ,tb_categorias c, tb_tipo_establecimientos t
// //                WHERE  id_establecimiento='".$this->id_establecimiento."'  AND 
// //                       t.id_tipo_establecimiento = e.id_tipo_establecimiento AND
// //                       c.id_tipo_establecimiento = e.id_tipo_establecimiento   AND
// //                       e.fecha_baja is null AND
// //                       c.id_categoria = e.id_categoria order by fecha_alta desc";
    
// //         $this->db->query($sql);

// //         if ($this->db->next_record())
// //         {
			
// // 	        $this->id_grupo_categoria = ($this->db->f("id_categoria") < 4) ? '1' : '2';
	        
// // 	        //CABECERA: NÚMERO DE ESTRELLAS DEL HOTEL
// // 	        if ($this->id_isla==5 || $this->id_isla==6 || $this->id_isla==7 )
// // 	        {
// // 	             $this->texto_num_estrellas='1-5';
// // 	        }
// // 	        else
// // 	        {
// // 	            $this->texto_num_estrellas=($this->id_grupo_categoria=='1')?'1, 2 y 3':'4 y 5';
// // 	        }                                    
// //         }
        		
//         /// Obtener el registro de detalles del hotel MAS ACTUAL.
//         $sql= "select to_char(fecha_alta,'yyyy-mm-dd')fecha_alta, to_char(fecha_baja,'yyyy-mm-dd')fecha_baja, baja_definitiva, c.id_categoria, 
//                       num_plazas, num_habitaciones, num_plazas_supletorias, t.id_tipo_establecimiento, t.descripcion tipo_establecimiento, c.descripcion categoria
//                FROM   tb_establecimientos_historico e ,tb_categorias c, tb_tipo_establecimientos t
//                WHERE  id_establecimiento='".$this->id_establecimiento."'  AND 
//                       t.id_tipo_establecimiento = e.id_tipo_establecimiento AND
//                       c.id_tipo_establecimiento = e.id_tipo_establecimiento   AND
//                       e.fecha_baja is null AND
//                       c.id_categoria = e.id_categoria order by fecha_alta desc";
    
//         $this->db->query($sql);
//         $salir=false;     
                                            
// 		while (!$salir && $this->db->next_record())
// 		{			
// 			$fecha_baja_null=false;
// 			if ($this->db->f("fecha_baja")==NULL)
// 			{
// 				$fecha_baja_null = true;
// 			}
			
// 			if ($this->db->f("fecha_alta") == NULL)
// 			{	
// 				//$mailSender = new Util();
// 				//$mailSender->enviar_mail_error($auth_username, $this->id_establecimiento);
// 				//header("location:".$sess->url("exp_comentario.php?num_comentario=9"));
// 				return RESULT_ERROR_HISTORICO_FECHA_ALTA_NULO;
// 			}
			
// 			$this->fecha_alta = $this->db->f("fecha_alta");
// 			$this->fecha_baja = $this->db->f("fecha_baja");
// 			$baja_definitiva  = $this->db->f("baja_definitiva");
            
//             if ($baja_definitiva==1)            
//                $this->estado = BAJA;
//             else
//                $this->estado = ALTA; 
                                                      
// 			if ($this->fecha_alta <= $fecha_now)
// 			{   
// 				///NOTA(antigua web): Aqui se pretende detectar el caso en el que el establecimiento esta de alta, pero 
// 				/// tiene fecha de baja ANTERIOR a la fecha pedida, por lo que hay incoherencia en los datos.
// 				/// (de todas formas este caso nunca se dara, ya que en la quey se exige fecha_baja = null.              
//                 //if (( $baja_definitiva == 1 || (!$fecha_baja_null && $fecha_now >= $this->fecha_baja)) && (!$is_admin) )
// 				if ($this->estado == ALTA && (!$fecha_baja_null && $this->fecha_baja <= $fecha_now))
// 				{			
// 					//$mailSender = new Util();
// 					//$mailSender->enviar_mail_error($auth_username, $this->id_establecimiento);
// 					//header("location:".$sess->url("exp_comentario.php?num_comentario=9"));
// 					return RESULT_ERROR_HISTORICO_ESTADO_ALTA_CON_FECHA_BAJA;
// 				}
// 				else  //fecha_baja=null o fecha_sistema < fecha_baja
// 				{					                    
//                     $this->id_categoria              = $this->db->f("id_categoria");
//                     $this->grupo_categoria           = $this->db->f("categoria");                                    
// 					$this->num_plazas                = $this->db->f("num_plazas");
// 					$this->num_habitaciones          = $this->db->f("num_habitaciones");
// 					$this->num_plazas_supletorias    = $this->db->f("num_plazas_supletorias");                                        					
//                     $this->id_tipo_establecimiento   = $this->db->f("id_tipo_establecimiento");                    
//                     $this->texto_tipo_establecimiento = $this->db->f("tipo_establecimiento"); 					

//                     $this->texto_id_tipo_establecimiento = $this->db->f("tipo_establecimiento");
                    
//                     $this->id_grupo_categoria = ($this->db->f("id_categoria") < 4) ? '1' : '2';
//                     //CABECERA: NÚMERO DE ESTRELLAS DEL HOTEL
//                     if ($this->id_isla==5 || $this->id_isla==6 || $this->id_isla==7 )
//                     {
//                     	$this->texto_num_estrellas='1-5';
//                     }
//                     else
//                     {
//                     	$this->texto_num_estrellas=($this->id_grupo_categoria=='1')?'1, 2 y 3':'4 y 5';
//                     }
                    
// 					$salir = true;
// 				}	
// 			}
// 		}                						        
                		
// 		$sql="SELECT  est.nombre_establecimiento, est.email, est.email2, est.telefono1, est.telefono2, m.id_municipio , m.nombre_municipio , m.id_zona_turistica 
//               FROM tb_establecimientos_unico est, tb_municipios m 
//               WHERE id_establecimiento='".$this->id_establecimiento."'  and                         
//                     m.id_municipio = est.id_municipio AND  
//                     m.id_isla = est.id_isla";   
                    
// 		$this->db->query($sql);
// 		$this->db->next_record($sql);
		
// 		$this->nombre_establecimiento = $this->db->f("nombre_establecimiento");
// 		$this->email                  = $this->db->f("email");
//         $this->email2                 = $this->db->f("email2");
//         $this->telefono                  = $this->db->f("telefono1");
//         $this->telefono2                 = $this->db->f("telefono2");        
// 		$this->municipio              = $this->db->f("nombre_municipio");
// 		$this->id_municipio           = $this->db->f("id_municipio");
// 		$this->id_zona_turistica      = $this->db->f("id_zona_turistica");
        
                
// //         $sql = "SELECT t.id_tipo_establecimiento, t.descripcion, t.id_tipo_establecimiento 
// //             FROM tb_establecimientos_historico e, tb_tipo_establecimientos t        
// //             WHERE e.id_establecimiento ='".$this->id_establecimiento."' and
// //                   e.id_tipo_establecimiento = t.id_tipo_establecimiento and
// //                   rownum < 2 order by fecha_alta desc"; 
                                                                      
// //         $this->db->query($sql);
// //         if ($this->db->next_record($sql)) {	                
// //            $this->id_tipo_establecimiento       = $this->db->f("id_tipo_establecimiento");                    
// //            $this->texto_id_tipo_establecimiento = $this->db->f("tipo_establecimiento");
// //         }
//       }
	  
// 	  return RESULT_OK;
// 	}
	
//     function get_grupo($id_establecimiento)
//     {
//     	///SQL_CHANGED: Se modifica consulta porque subquery daba más de un registro       
//         $sql = "SELECT id_grupo FROM tb_grupo_tipo_est WHERE id_tipo_establecimiento =
//         (SELECT id_tipo_establecimiento FROM (SELECT h.id_tipo_establecimiento FROM tb_establecimientos_historico h 
//         									  WHERE h.id_establecimiento= " . $id_establecimiento . " AND
//         									  h.fecha_baja IS NULL ORDER BY h.fecha_alta DESC) WHERE ROWNUM < 2)";
//         $this->db->query($sql);

//         if ($this->db->next_record())
//         {
//             return $this->db->f("id_grupo");
//         }
        
//         return 0;
//     }
	
    function esta_activo() {
    	return ($this->estado == ALTA);
    }

    public function load_all_grupos()
    {  
    	$sql = "SELECT id_grupo, descripcion FROM tb_grupo_establecimientos ORDER BY id_grupo";
    
    	$db = new Istac_Sql();
    	$db->query($sql);
    	$grupos = array();
    	while($db->next_record())
    	{
    		$grupos[$db->f('id_grupo')] = $db->f('descripcion'); 
    	}
    	return $grupos;
    }

    function dar_de_alta() 
	{
       $fecha_actual = date("Y-m-d");
       if ($this->fecha_baja == $fecha_actual) 
	   {
            $this->estado = BAJA;               
            return false;
       }
           
       if ($this->cierra_operacion_anterior($fecha_actual)) 
	   {
       		if ($this->registra_nueva_operacion($fecha_actual, ALTA)) 
			{
               $this->estado = ALTA;                              
               return true;
       		}   
       }
       
       return false;              
    }
            
    function dar_de_baja() 
	{
       $fecha_actual = date("Y-m-d");
       if ($this->fecha_alta == $fecha_actual) 
	   {
           $this->estado = ALTA;                  
           return false;
       }

       if ($this->cierra_operacion_anterior($fecha_actual)) 
	   {
           if ($this->registra_nueva_operacion($fecha_actual, BAJA)) 
		   {
               $this->estado = BAJA;
               return true;
           }     
       }      
       return false;              
    }
    
    function cierra_operacion_anterior($fecha) 
	{
   
        $result =  false;
        $sql = "update tb_establecimientos_historico set fecha_baja = to_date('$fecha','yyyy-mm-dd') 
                where id_establecimiento = '".$this->id_establecimiento."' and fecha_baja is null"; 
        
        $db = new Istac_Sql();
        $db->query($sql);
                       
        $this->fecha_baja = $fecha;
                       
        if ($db->affected_rows() > 0) 
			$result = true;
        else 
			$result = false;
           
        return $result;
    }
    
    function registra_nueva_operacion($fecha, $tipo_operacion) 
	{ 
          
        $result = false;
        
        $id                     = $this->id_establecimiento;
        $id_tipo                = $this->id_tipo_establecimiento;
        $id_categoria           = $this->id_categoria;
        $num_plazas             = $this->num_plazas;
        $muestra_alojamiento    = 0;
        $muestra_expectativas   = 0;
        $fecha_alta             = $this->fecha_alta;
        $fecha_baja             = null;
        $baja_definitiva        = $tipo_operacion;
        $num_habitaciones       = $this->num_habitaciones;
        $num_plazas_supletorias = $this->num_plazas_supletorias;                

        $sql = " insert into tb_establecimientos_historico(id_establecimiento, id_tipo_establecimiento, id_categoria, num_plazas, muestra_alojamiento, muestra_expectativas, fecha_alta, baja_definitiva, num_habitaciones, num_plazas_supletorias) ".
               " values('". $id ."', '". $id_tipo ."', '". $id_categoria ."', '". $num_plazas ."', '". $muestra_alojamiento ."', '". $muestra_expectativas ."', to_date('$fecha', 'yyyy-mm-dd'), '". $baja_definitiva ."', '". $num_habitaciones ."', '". $num_plazas_supletorias ."')"; 

        $db = new Istac_Sql();
        $db->query($sql);
    
        if ($db->affected_rows() > 0) $result = true;
        else $result = false;
    
        return $result;
    }

    /**
     * Registra las modificaciones de los datos del establecimiento.
     * @param unknown_type $modificaciones
     */
    public function registrar_modificacion($estid, $userid, $modificaciones)
    {
    	if ($modificaciones != null && count($modificaciones) > 0)
    	{
	    	$db = new Istac_Sql();
	    	
	    	$sql= "insert into TB_MODIFICACIONES_HOTELES(FECHA_GRABACION,ID_ESTABLECIMIENTO,USUARIO,";
	    	$i=0;
	    	
	    	$fecha_grabacion= date("Y-m-d H:i:s");
	    	
	    	foreach ($modificaciones as $clave=>$valor)
	    	{
	    		if  ($i!=0)
	    		{
	    			$sql.=",".$clave;
	    		}
	    		else
	    		{
	    			$sql.=$clave;
	    		}
	    	
	    		$i++;
	    	}
	    	
	    	$sql.=") values (to_date('".$fecha_grabacion."','yyyy-mm-dd HH24:MI:SS'),'".$estid."','".$userid."'";
	    	
	    	foreach ($modificaciones as $clave=>$valor)
	    	{
	    		$sql.=",".DbHelper::prepare_string($valor);
	    	}
	    	
	    	$sql.=")";
	    	
	    	$db->query($sql); 

	    	if ($db->affected_rows() > 0)
	    		return $fecha_grabacion;	
    	} 	
	    return false;
    }
    
    public function nombre_largo() {
    	return $this->texto_tipo_establecimiento . " " . $this->nombre_establecimiento;
    }
    
    public function abiertoParaAlojamiento($mes,$ano)
    {
    	$sql = DbHelper::prepare_sql("SELECT ID_ESTABLECIMIENTO FROM TB_ESTABLECIMIENTOS_HISTORICO WHERE (ID_ESTABLECIMIENTO = :estid) AND (MUESTRA_ALOJAMIENTO=1) AND (FECHA_ALTA <= TO_DATE(:t, 'dd-mm-yyyy') AND (TO_DATE(:t, 'dd-mm-yyyy') < FECHA_BAJA OR FECHA_BAJA IS NULL))", 
				array(':estid' => $this->id_establecimiento, ':t' => sprintf("01-%02d-%04d", $mes, $ano)));
		$db = new Istac_Sql();
		$db->query($sql);
		
		if ($db->next_record())
		{
			return true;
		}
		return false;
    }
    
    public function abiertoParaExpectativas($trimestre)
    {
    	$ntrim=$trimestre->trimestre;
    	$trimestre_fecha_inicial=sprintf("01-%02d-%04d", (3*($ntrim-1))+1, $trimestre->anyo);
    	$trimestre_fecha_final=sprintf("%02d-%02d-%04d", (($ntrim==2)||($ntrim==3))?30:31, (3 * $ntrim), $trimestre->anyo);
    	$sql = DbHelper::prepare_sql("SELECT ID_ESTABLECIMIENTO FROM TB_ESTABLECIMIENTOS_HISTORICO
       		WHERE (ID_ESTABLECIMIENTO=:estid) AND (MUESTRA_EXPECTATIVAS=1) AND (FECHA_ALTA<=TO_DATE(:ff, 'dd-mm-yyyy')) AND
       ((FECHA_BAJA>TO_DATE(:fi, 'dd-mm-yyyy')) OR (FECHA_BAJA IS NULL))",
       		array(':estid' => $this->id_establecimiento, ":fi" => $trimestre_fecha_inicial, ":ff" => $trimestre_fecha_final));
		$db = new Istac_Sql();
		$db->query($sql);
		
		if ($db->next_record())
		{
			return true;
		}
		return false;
    }
}

?>
