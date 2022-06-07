<?php

require_once(__DIR__."/../config.php");

class DocumentoAlVuelo
{
    public static function CodificacionPaisResidenciaTXT()
    {
        return DocumentoAlVuelo::_codificacionPaisResidencia('txt');
    }
    
    public static function CodificacionPaisResidenciaXLS()
    {
        return DocumentoAlVuelo::_codificacionPaisResidencia('xls');
    }
    
    public static function CodificacionPaisResidenciaXML()
    {
        return DocumentoAlVuelo::_codificacionPaisResidencia('xml');
    }
    
    public static function CodificacionProvinciaResidenciaTXT()
    {
        return DocumentoAlVuelo::_codificacionProvinciaResidencia('txt');
    }
    
    public static function CodificacionProvinciaResidenciaXLS()
    {
        return DocumentoAlVuelo::_codificacionProvinciaResidencia('xls');
    }
    
    public static function CodificacionProvinciaResidenciaXML()
    {
        return DocumentoAlVuelo::_codificacionProvinciaResidencia('xml');
    }
    
    public static function CodificacionTipoCategoriaEstablecimientoTXT()
    {
        return DocumentoAlVuelo::_codificacionTipoCategoriaEstablecimiento('txt');
    }
    
    public static function CodificacionTipoCategoriaEstablecimientoXLS()
    {
        return DocumentoAlVuelo::_codificacionTipoCategoriaEstablecimiento('xls');
    }
    
    public static function CodificacionTipoCategoriaEstablecimientoXML()
    {
        return DocumentoAlVuelo::_codificacionTipoCategoriaEstablecimiento('xml');
    }
    
    private static function _codificacionPaisResidencia($formato)
    {
        $salida="";
        $db=new Istac_Sql();
        $sql="select cod_pais as id_pais_alpha3, literal from tb_unidades_territoriales where not cod_pais is null order by cod_pais";
        $db->query($sql);
        if (!$db->affected_rows())
        {
            if (!strcmp($formato,"xml"))
            {
                $salida.="<?xml version=".'"'."1.0".'"'." encoding=".'"'."iso-8859-1".'"'." ?>\r\n<ISO_3166_1_ALPHA3>\r\n";
                $aux="</ID_PAIS>\r\n        <NOM_PAIS>";
            }
            elseif (!strcmp($formato,"xls"))
                $aux="\t";
                elseif (!strcmp($formato,"txt"))
                $aux="   ";
            
            while($db->next_record())
            {
                $principio=(!strcmp($formato,"xml"))? "    <PAIS>\r\n        <ID_PAIS>":"";
                $fin=(!strcmp($formato,"xml"))? "</NOM_PAIS>\r\n    </PAIS>":"";
                if (!strcmp($formato,"txt"))
                    $salida.=$principio.str_pad($db->f("id_pais_alpha3"),6," ").$aux.$db->f("literal").$fin."\r\n";
                else
                    $salida.=$principio.$db->f("id_pais_alpha3").$aux.$db->f("literal").$fin."\r\n";
            }
            if (!strcmp($formato,"xml"))
                $salida.="</ISO_3166_1_ALPHA3>\r\n";
        }
        return $salida;
    }
    
    private static function _codificacionProvinciaResidencia($formato)
    {
        $salida="";
        $db=new Istac_Sql();
        $sql="select literal, cod_provisla as id_nuts_iii from tb_unidades_territoriales where not cod_provisla is null order by cod_provisla";
        $db->query($sql);
        if (!$db->affected_rows())
        {
            if (!strcmp($formato,"xml"))
            {
                $salida.='<?xml version='.'"'."1.0".'"'." encoding=".'"'."iso-8859-1".'"'." ?>\r\n<NUTS_III>\r\n";
                $aux="</COD_PROVINCIA_ISLA>\r\n        <NOM_PROVINCIA_ISLA>";
            }
            elseif (!strcmp($formato,"xls"))
                $aux="\t";
                elseif (!strcmp($formato,"txt"))
                $aux="   ";
            
            while($db->next_record())
            {
                $principio=(!strcmp($formato,"xml"))? "    <PROVINCIA_ISLA>\r\n        <COD_PROVINCIA_ISLA>":"";
                $fin=(!strcmp($formato,"xml"))? "</NOM_PROVINCIA_ISLA>\r\n    </PROVINCIA_ISLA>":"";
                $salida.=$principio.$db->f("id_nuts_iii").$aux.$db->f("literal").$fin."\r\n";
                
            }
            if (!strcmp($formato,"xml"))
                $salida.="</NUTS_III>\r\n";
        }
        return $salida;
    }
    
    private static function _codificacionTipoCategoriaEstablecimiento($formato)
    {
        $salida="";
        $db=new Istac_Sql();
        $sql="select t.descripcion, c.categoria from tb_tipo_establecimientos_xml t, tb_categorias_xml c where t.ID_TIPO_ESTABLECIMIENTO = c.ID_TIPO_ESTABLECIMIENTO order by t.ID_TIPO_ESTABLECIMIENTO, c.id_categoria";
        $db->query($sql);
        if (!$db->affected_rows())
        {
            if (!strcmp($formato,"xml"))
                $salida.="<?xml version=".'"'."1.0".'"'." encoding=".'"'."iso-8859-1".'"'." ?>\r\n<Tipos_Categorias>\r\n";
                
                $tencontrado = array();
                while($db->next_record())
                {
                    if (!strcmp($formato,"xml"))
                    {
                        if 	(!isset($tencontrado[$db->f("descripcion")]) || $tencontrado[$db->f("descripcion")]!=1)
                        {
                            if (count($tencontrado)!=0)
                                $salida.="    </TIPO>\r\n";
                                $salida.="    <TIPO valor=".'"'.$db->f("descripcion").'"'.">\r\n";
                                
                                if ($db->f("descripcion")=="Otros")
                                    $tencontrado[$db->f("descripcion")."1"]=1;
                                else
                                    $tencontrado[$db->f("descripcion")]=1;
                                        
                        }
                        $salida.="        <CATEGORIA>".$db->f("categoria")."</CATEGORIA>\r\n";
                    }
                    elseif (!strcmp($formato,"txt"))
                        $salida.=str_pad($db->f("descripcion"),35," ").$db->f("categoria")."\r\n";
                        elseif (!strcmp($formato,"xls"))
                        $salida.=$db->f("descripcion")."\t".$db->f("categoria")."\r\n";
                }
                $salida.=(!strcmp($formato,"xml"))?"    </TIPO>\r\n</Tipos_Categorias>":"";
        }
        return $salida;
    }
}

?>