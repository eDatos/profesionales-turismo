<?php
require_once(__DIR__."/../lib/DbHelper.class.php");
require_once(__DIR__."/../lib/RowDbIterator.class.php");

class EnlaceAyuda 
{
	var $id;
	var $cod_enlace;
	var $titulo;
	var $desc_corta;
	var $desc_larga;
	var $tipo; //0-mensaje central jquery; 1-ventana flotante con posición relativa al enlace; 2-enlace externo
	var $contenido_ayuda;
	var $url_enlace_externo;
	var $posX_popup;
	var $posY_popup;
	var $ancho_popup;
	var $alto_popup;
}

class EnlaceAyudaDao
{
	var $db;
	
	public function __construct()
	{
	 	$this->db = new Istac_Sql();
	}
	
	public function cargar($cod_enlace, $encoding)
	{
		$sql = DbHelper::prepare_sql("SELECT id, cod_enlace, titulo, desc_corta, desc_larga, tipo, contenido_ayuda,
				url_enlace_externo, posx_popup, posy_popup, ancho_popup, alto_popup
				FROM tb_enlaces_ayuda WHERE cod_enlace=:cod_enlace",
				array(":cod_enlace"   => (string)$cod_enlace));		
		
		$this->db->query($sql);

		$enlace = NULL;
		if($this->db->next_record())
		{
			$enlace = new EnlaceAyuda();
			$enlace->id = $this->db->f("id");
			$enlace->cod_enlace = $this->db->f("cod_enlace");
			$enlace->titulo = mb_convert_encoding($this->db->f("titulo"), $encoding , 'ISO-8859-1');
			$enlace->desc_corta = mb_convert_encoding($this->db->f("desc_corta"), $encoding , 'ISO-8859-1');
			$enlace->desc_larga = mb_convert_encoding($this->db->f("desc_larga"), $encoding , 'ISO-8859-1');
			$enlace->tipo = $this->db->f("tipo");
			if($enlace->tipo!=3)
				$enlace->contenido_ayuda = htmlspecialchars(mb_convert_encoding($this->db->f("contenido_ayuda"), $encoding , 'ISO-8859-1'),ENT_COMPAT | ENT_HTML401, $encoding);
			else
				$enlace->contenido_ayuda = mb_convert_encoding($this->db->f("contenido_ayuda"), $encoding , 'ISO-8859-1');
			$enlace->url_enlace_externo = $this->db->f("url_enlace_externo");
			$enlace->posX_popup = $this->db->f("posx_popup");
			$enlace->posY_popup = $this->db->f("posy_popup");
			$enlace->ancho_popup = $this->db->f("ancho_popup");
			$enlace->alto_popup = $this->db->f("alto_popup");
		}
		
		//Valores por defecto.
		if ($enlace->posX_popup == null) $enlace->posX_popup = 15;
		if ($enlace->posY_popup == null) $enlace->posY_popup = 15;
		if ($enlace->ancho_popup == null) $enlace->ancho_popup = 60;
		if ($enlace->alto_popup == null) $enlace->alto_popup = 40;
		
		return $enlace;
	}
	
	public function guardar($enlaceGuardar)
	{
		if($enlaceGuardar->id != NULL)
		{
		    $sql = DbHelper::prepare_sql("UPDATE tb_enlaces_ayuda SET
		    		cod_enlace=:cod_enlace,
		    		titulo=:titulo,
		    		desc_corta=:desc_corta,
		    		desc_larga=:desc_larga,
		    		tipo=:tipo,
		    		contenido_ayuda=:contenido_ayuda,
		    		url_enlace_externo=:url_enlace_externo,
		    		posX_popup=:posX_popup,
		    		posY_popup=:posY_popup,
		    		ancho_popup=:ancho_popup,
		    		alto_popup=:alto_popup
		            where id=:id",
		    		array(":id"					=>	(int)$enlaceGuardar->id,
		    			  ":cod_enlace"			=>	(string)$enlaceGuardar->cod_enlace,
		    			  ":titulo"				=>	(string)$enlaceGuardar->titulo,
		    			  ":desc_corta"			=>	(string)$enlaceGuardar->desc_corta,
		    			  ":desc_larga"			=>	(string)$enlaceGuardar->desc_larga,
		    			  ":tipo"				=>	(string)$enlaceGuardar->tipo,
		    			  //":contenido_ayuda"	=>	(string)$enlaceGuardar->contenido_ayuda,
		    			  ":url_enlace_externo"	=>	(string)$enlaceGuardar->url_enlace_externo,
		    			  ":posX_popup"			=>	(string)$enlaceGuardar->posX_popup,
		    			  ":posY_popup"			=>	(string)$enlaceGuardar->posY_popup,
		    			  ":ancho_popup"		=>	(string)$enlaceGuardar->ancho_popup,
		    			  ":alto_popup"			=>	(string)$enlaceGuardar->alto_popup));
		    
		    
		    
		    return $this->actualizar_ayuda($sql, $enlaceGuardar->contenido_ayuda, ($enlaceGuardar->tipo!=3)) != 0;
		    
		    //$this->db->query($sql);
		    
		    //$long= strlen((string)$enlaceGuardar->contenido_ayuda);
		   // return ($this->db->affected_rows()!=0);
		}
		else {
			$sql = DbHelper::prepare_sql("INSERT INTO tb_enlaces_ayuda (cod_enlace, titulo, desc_corta, desc_larga,
					tipo, contenido_ayuda, url_enlace_externo, posX_popup, posY_popup, ancho_popup, alto_popup) VALUES (
					:cod_enlace, :titulo, :desc_corta, :desc_larga, :tipo, :contenido_ayuda, :url_enlace_externo,
					:posX_popup, :posY_popup, :ancho_popup, :alto_popup)",
		    		array(":cod_enlace"			=>	(string)$enlaceGuardar->cod_enlace,
		    			  ":titulo"				=>	(string)$enlaceGuardar->titulo,
		    			  ":desc_corta"			=>	(string)$enlaceGuardar->desc_corta,
		    			  ":desc_larga"			=>	(string)$enlaceGuardar->desc_larga,
		    			  ":tipo"				=>	(string)$enlaceGuardar->tipo,
		    			  //":contenido_ayuda"	=>	(string)$enlaceGuardar->contenido_ayuda,
		    			  ":url_enlace_externo"	=>	(string)$enlaceGuardar->url_enlace_externo,
		    			  ":posX_popup"			=>	(string)$enlaceGuardar->posX_popup,
		    			  ":posY_popup"			=>	(string)$enlaceGuardar->posY_popup,
		    			  ":ancho_popup"		=>	(string)$enlaceGuardar->ancho_popup,
		    			  ":alto_popup"			=>	(string)$enlaceGuardar->alto_popup));
			
			$af_rows = $this->actualizar_ayuda($sql, $enlaceGuardar->contenido_ayuda, ($enlaceGuardar->tipo!=3));
		    //$this->db->query($sql);
			if($af_rows!=0)
			{
				$sql = "SELECT TB_ENLACES_AYUDA_ID_SEQ.CURRVAL id FROM DUAL";
				$this->db->query($sql);
				if($this->db->next_record())
				{
					$enlaceGuardar->id=$this->db->f("id");
				}
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
	}
	
	public function actualizar_ayuda($sql, & $ayuda_content, $decodeHtmlEntities=true)
	{
		$done = false;
		$error = null;
		$db = new Istac_Sql();
		
		$db->connect();
		
		$ora_conn=$db->Link_ID;
	
		if ($ora_conn!=false)
		{
			$lob = OCINewDescriptor($ora_conn, OCI_D_LOB);
	
			if ($lob!==false)
			{
				$stmt = OCIParse($ora_conn,$sql);
				
				if ($stmt==false)
				{
					$error = "ERROR: Se ha producido un error interno tratando el fichero";
				}
				else
				{
					$data = ($decodeHtmlEntities) ? htmlspecialchars_decode($ayuda_content) : $ayuda_content; //Se supone codificación ISO-8859-1 
					OCIBindByName($stmt, ':contenido_ayuda', $lob, -1, OCI_B_CLOB);
					if ($lob->WriteTemporary($data) != false)
					{
						$succes=OCIExecute($stmt, OCI_DEFAULT);
	
						if ($succes == false)
						{
							$aux1=OCIERROR($stmt);
							if (!empty($aux1['message']))
							{
								$error = "ERROR: Se ha producido un error interno al tratar el fichero xml.";
							}
						}
						$lob->close();
					}
					else
					{
						$error = "ERROR: Se ha producido un error interno tratando el fichero";
	
					}
					if($lob->free()==false)
					{
						$error = "ERROR: Se ha producido un error interno tratando el fichero";
	
					}
				}
				OCICommit($ora_conn);
	
				$done = OCIrowcount($stmt);
	
				if (OCIFreeStatement($stmt)== false)
				{
					$error = "ERROR: Se ha producido un error interno tratando el fichero";
				}
			}
				
			OCILogoff($ora_conn);
		}
	
		return $done;
	}
	
	public function eliminar($enlaceEliminar)
	{
		$sql = DbHelper::prepare_sql("DELETE FROM tb_enlaces_ayuda WHERE id=:id",
				array(":id"		=>		(int)$enlaceEliminar->id));
		
		$this->db->query($sql);
		return ($this->db->affected_rows()!=0);
	}	
	
	public function obtenerTodos()
	{
		$sql = "SELECT id, cod_enlace, titulo, desc_corta, desc_larga, tipo, contenido_ayuda, url_enlace_externo, posx_popup, posy_popup, ancho_popup, alto_popup
				FROM tb_enlaces_ayuda ORDER BY cod_enlace";

		$this->db->query($sql);
		return new RowDbIterator($this->db, array('id', 'cod_enlace', 'titulo', 'desc_corta', 'desc_larga', 'tipo', 'contenido_ayuda', 'url_enlace_externo', 'posx_popup', 'posy_popup', 'ancho_popup', 'alto_popup'));		
	}
}

?>