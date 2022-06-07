<?php

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../../lib/DbHelper.class.php");

/**
 * Metodos de accoes a datos a las tablas relacionadas con XML.
 *
 */
class AlojaXmlDao
{
	var $last_error;
	
	/**
	 * Carga una tabla lookup de correspondencia de Paises y provincias encontradas en el XML con las unidades territoriales.
	 */
	public function cargar_lookup_UT_XML()
	{
        $sql = "SELECT ID,COD_PAIS,COD_PROVISLA FROM TB_UNIDADES_TERRITORIALES";

        $db = new Istac_Sql();
        $db->query($sql);

        //* Key: cod_pais o cod_prov_isla
        //  Value: id_unidad_territorial que le corresponde
        $result = array();
        while ($db->next_record())
        {
        	$rec = $db->Record;
        	
        	$id_ut = $rec['id'];
        	
        	if (isset($rec['cod_pais']))
        	{
        		$cod = $rec['cod_pais'];
        		$d = "PAIS:" . $cod;
        	}
        	else if (isset($rec['cod_provisla']))
        	{
        		$cod = $rec['cod_provisla'];
        		$d = "PRIS:" . $cod;
        	}
        	else 
        	{
        		//Ignoramos entradas que no tienen correspondencia.
        		continue;
        	}
        	$result[$d] = $id_ut;
        }
        
        return $result;
	}
	
	/**
	 * Obtiene el contenido XML guardado en la base de datos para el id. de cuestionario dado.
	 * Devuelve nulo si no existen datos para ese id de cuestionario.
	 * @param unknown_type $id_cuestionario
	 */
	public function cargar_xml($id_cuestionario)
	{
	    if(defined('ENTORNO_DESARROLLO'))
	        return $this->leerXmlDB($id_cuestionario);
	    
		oci_internal_debug(false);
		$sql= DbHelper::prepare_sql("SELECT sys.xmltype.getclobval(contenido_xml) as xml_content FROM TB_ALOJA_REGISTRO_XML where id_cuestionario=:id_cuestionario",
				array(":id_cuestionario" => $id_cuestionario));
		$db  = new Istac_Sql;
		$db->query($sql);
		if ($db->next_record())
		{
		    return $db->f("xml_content");
		}
		return null;
	}
	
	/**
	 * Función para extraer un BLOB con un cuestionario XML de la BDD. En el entorno de desarrollo, la función OCI_Lob::Load falla??
	 * @param unknown $id_cuestionario
	 * @throws Exception
	 * @return String con el contenido del XMl ó NULL si hay error. 
	 */
	private function leerXmlDB($id_cuestionario)
	{
	    $xml=null;
	    $conn=0;
	    $DBerr=null;
	    $sql="SELECT sys.xmltype.getclobval(contenido_xml) as xml_content FROM TB_ALOJA_REGISTRO_XML where id_cuestionario='".$id_cuestionario."'";
	    try
	    {
	        $conn=oci_pconnect(DB_USER, DB_PASSWORD, DB_HOST, DB_CHARACTER_SET );
	        if(!$conn)
	        {
	            $DBerr=oci_error();
	            throw new Exception('DB initialization error: OCILogon failed. '.$DBerr['message']);
	        }
	        
	        $statement=oci_parse($conn,$sql);
	        if(!$statement)
	        {
	            $DBerr=oci_error();
	            oci_close($conn);
	            $conn=0;
	            throw new Exception('DB initialization error: OCIParse failed. '.$DBerr['message']);
	        }
	        
	        oci_execute($statement);
	        $DBerr=oci_error($statement);
	        if($DBerr!=false)
	        {
	            oci_close($conn);
	            $conn=0;
	            throw new Exception('DB initialization error: OCIExecute failed. '.$DBerr['message']);
	        }
	        
	        $resultado=oci_fetch_array($statement,OCI_ASSOC+OCI_RETURN_NULLS);
	        if($resultado==false)
	        {
	            // No hay disponible
	            // Vemos porqué
	            $errno=oci_error($statement);
	            if(1403 == $errno) { # 1043 means no more records found
	                // No hay más filas
	            } else {
	                // Error
	                oci_close($conn);
	                $conn=0;
	                throw new Exception('DB initialization error: OCIFetch failed. '.$DBerr['message']);
	            }
	        }
	        else
	        {
	            // Hemos leído la fila
	            $Record=array();
	            for($ix=1;$ix<=oci_num_fields($statement);$ix++) {
	                $col=strtoupper(oci_field_name($statement,$ix));
	                $colreturn=strtolower($col);
	                $Record[ "$colreturn" ] = $resultado["$col"];
	            }
	            
	            //print_r($Record['xml_content']);
	            //if(($Record['xml_content']) instanceof OCI_Lob)
	            if(is_object($Record['xml_content']))
	            {
	                try {
	                    //print $Record['xml_content']->load();
	                    $len=$Record['xml_content']->size();
	                    $xml="";
	                    while($len>0)
	                    {
	                        $xml=$xml.$Record['xml_content']->read(1000);
	                        $len-=1000;
	                    }
	                    //print $xml;
	                    //print $Record['xml_content']->size();
	                    //print $Record['xml_content']->read(1000);
	                } catch (Exception $e) {
	                    oci_close($conn);
	                    $conn=0;
	                    throw $e;
	                }
	                
	            }
	        }
	        
	        
	        oci_close($conn);
	        $conn=0;
	        if($DBerr!=false)
	            throw new Exception('DB initialization error: OCIExecute failed. '.$DBerr['message']);
	    }
	    catch(Exception $e)
	    {
	        $xml=null;
	        //throw $e;
	        $msg=$e->getMessage();
	        echo $msg;
	    }
	    return $xml;
	}
	
	public function guardar_xml($id_cuestionario, $fecha_grabacion, $validacion_xsd, & $xml_content)
	{
		if ($this->tiene_xml($id_cuestionario))
		{
			//return $this->actualizar_xml($id_cuestionario, $fecha_grabacion, $validacion_xsd, $xml_content);
			
			if($this->actualizar_xml($id_cuestionario, $fecha_grabacion, $validacion_xsd, $xml_content))
				return null;
		}
		else
		{
			//return $this->insertar_xml($id_cuestionario, $fecha_grabacion, $validacion_xsd, $xml_content);
			
			if($this->insertar_xml($id_cuestionario, $fecha_grabacion, $validacion_xsd, $xml_content))
				return null;
		}
		
		return "ERROR: Se ha producido un error interno al tratar el fichero xml.";
	}
	
	private function tiene_xml($id_cuestionario)
	{
		$sql = DbHelper::prepare_sql("select id_cuestionario from TB_ALOJA_REGISTRO_XML where id_cuestionario = :id_cuestionario",
				array(':id_cuestionario' => $id_cuestionario));
		
		$db = new Istac_Sql();
		$db->query($sql);
		if ($db->next_record())
		{
			return ($db->f('id_cuestionario') == $id_cuestionario);
		}
		return false;
	}
	
	public function actualizar_xml($id_cuestionario, $fecha_grabacion, $validacion_xsd, & $xml_content)
	{
		$sql= DbHelper::prepare_sql("UPDATE TB_ALOJA_REGISTRO_XML SET
				fecha_registro = to_date(:fecha_grabacion,'yyyy-mm-dd HH24:MI:SS'), 
				contenido_xml = SYS.XMLTYPE(:the_blob), 
				validacion = :validacion
				where id_cuestionario = :id_cuestionario",
				array(':id_cuestionario' => $id_cuestionario, 
						':fecha_grabacion' => $fecha_grabacion, 
						':validacion' => $validacion_xsd));
		
		$done = false;
		$this->last_error = null;
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
					$this->last_error = "ERROR: Se ha producido un error interno tratando el fichero";
				}
				else
				{
					OCIBindByName($stmt, ':the_blob', $lob, -1, OCI_B_CLOB);
					if ($lob->WriteTemporary($xml_content) != false)
					{
						$succes=OCIExecute($stmt, OCI_DEFAULT);
		
						if ($succes == false)
						{
							$aux1=OCIERROR($stmt);
							if (!empty($aux1['message']))
							{
								$this->last_error = "ERROR: Se ha producido un error interno al tratar el fichero xml.";
							}
						}
						else
							$done = true;
								
						$lob->close();
					}
					else
					{
						$this->last_error = "ERROR: Se ha producido un error interno tratando el fichero";
		
					}
					if($lob->free()==false)
					{
						$this->last_error = "ERROR: Se ha producido un error interno tratando el fichero";
		
					}
				}
				OCICommit($ora_conn);
		
				//$done = (OCIrowcount($stmt)!=FALSE)?true:false;
				
				if (OCIFreeStatement($stmt)== false)
				{
					$this->last_error = "ERROR: Se ha producido un error interno tratando el fichero";
				}
			}
			
			OCILogoff($ora_conn);
		}
		
		return $done;		
	}
	
	public function insertar_xml($id_cuestionario, $fecha_grabacion, $validacion_xsd, & $xml_content)
	{
		try {
			
		$sql= DbHelper::prepare_sql("INSERT INTO TB_ALOJA_REGISTRO_XML 
		(id_cuestionario, fecha_registro, contenido_xml, validacion)
 VALUES (:id_cuestionario, to_date(:fecha_grabacion,'yyyy-mm-dd HH24:MI:SS'), SYS.XMLTYPE(:the_blob), :validacion)",
		array(':id_cuestionario' => $id_cuestionario, ':fecha_grabacion' => $fecha_grabacion, ':validacion' => ($validacion_xsd)?1:0));
		
		$done = false;
		$this->last_error = null;
		$db = new Istac_Sql();
		
		$db->connect();
		$ora_conn=$db->Link_ID;
		
		if ($ora_conn!=false)
		{
			$lob = oci_new_descriptor($ora_conn, OCI_D_LOB);
			
			if ($lob!==false)
			{		
			    $stmt = oci_parse($ora_conn,$sql);
				
				if ($stmt==false)
				{
					$this->last_error = "ERROR: Se ha producido un error interno tratando el fichero";
				}
				else 
				{
					oci_bind_by_name($stmt, ':the_blob', $lob, -1, OCI_B_CLOB);
					if ($lob->WriteTemporary($xml_content) != false)
					{
					    // Insertamos el XML usando una transacción.
						$succes=oci_execute($stmt, OCI_DEFAULT);
				
						if ($succes == false)
						{
						    $aux1=oci_error($stmt);
							if (!empty($aux1['message']))
							{
								$this->last_error = "ERROR: Se ha producido un error interno al tratar el fichero xml.";
							}
						}
						else
							$done = true;
								
						$lob->close();
					}
					else
					{
						$this->last_error = "ERROR: Se ha producido un error interno tratando el fichero";
						
					}
					if($lob->free()==false)
					{
						$this->last_error = "ERROR: Se ha producido un error interno tratando el fichero";
						
					}
				}
				if($done)
				    oci_commit($ora_conn);
				else
				    oci_rollback($ora_conn);
				
				if (oci_free_statement($stmt)== false)
				{
					$this->last_error = "ERROR: Se ha producido un error interno tratando el fichero";
					
				}			
			}
			
			oci_close($ora_conn);
		}
		
		return $done;
		
		}
		catch(Exception $e)
		{
			log::error($e->getMessage());
		}
		return false;
	}
}

?>