<?php

//require_once(__DIR__."/../config.php");
require_once(__DIR__."/IPF.class.php");

define('NIF_DNI',0);
define('NIF_NIE',1);
define('NIF_CIF',2);
define('NIF_ERR',9);


define("FORMATO_NIF_GUIONES",'%1$s-%2$s-%3$s');
define("FORMATO_NIF_ESPACIOS",'%1$s %2$s %3$s');
define("FORMATO_NIF_DOSPUNTOS",'%1$s:%2$s:%3$s');


//define("FORMATO_DISPLAY_DNI",'%1$s-%2$s-%3$s');

/**
 * Clase que representa un NIF.
 *
 * @author SCC,SL
 *
 */
class NIF extends IPF
{
    /**
     * N�mero de Identificaci�n Fiscal (NIF) que consta de 9 d�gitos/letras. El formato es el siguiente:
     *  TNNNNNNNC � TNNNNNNNNC
     * donde
     *  T = caracter que indica el tipo de NIF seg�n los siguientes casos:
     *      **** NIF para personas f�sicas ****
     *      Si es un n�mero, se trata de un DNI para nacionales.
     *      K, se trata de un NIF para nacionales menores de 14 a�os sin DNI. Antes del 1/7/2008 tambi�n se asignaba a extranjeros menores de 18 a�os sin NIE.
     *      L, se trata de un NIF para nacionales mayores de 14 a�os sin DNI residentes en el extranjero.
     *      M, se trata de un NIF para extranjeros sin NIE.
     *      X, se trata de un NIE para extranjeros. En este caso puede tener 7 � 8 d�gitos adem�s de la letra de control.
     *      Y, se trata de un NIE para extranjeros. Son 7 d�gitos adem�s de la letra de control.
     *      Z, se trata de un NIE para extranjeros. Son 7 d�gitos adem�s de la letra de control.
     *      **** NIF para personas jur�dicas y entidades en general ****
     *      A, se trata de un NIF para Sociedades an�nimas.
     *      B, se trata de un NIF para Sociedades de responsabilidad limitada.
     *      C, se trata de un NIF para Sociedades colectivas.
     *      D, se trata de un NIF para Sociedades comanditarias.
     *      E, se trata de un NIF para Comunidades de bienes.
     *      F, se trata de un NIF para Sociedades cooperativas.
     *      G, se trata de un NIF para Asociaciones y Fundaciones.
     *      H, se trata de un NIF para Comunidades de propietarios en r�gimen de propiedad horizontal.
     *      J, se trata de un NIF para Sociedades civiles.
     *      N, se trata de un NIF para Entidades extranjeras.
     *      P, se trata de un NIF para Corporaciones Locales.
     *      Q, se trata de un NIF para Organismos p�blicos.
     *      R, se trata de un NIF para Congregaciones e instituciones religiosas.
     *      S, se trata de un NIF para �rganos de la Administraci�n General del Estado y de las comunidades aut�nomas.
     *      U, se trata de un NIF para Uniones Temporales de Empresas (UTE).
     *      V, se trata de un NIF para otros tipos no definidos en el resto de claves.
     *      W, se trata de un NIF para Establecimientos permanentes de entidades no residentes en Espa�a.
     *  NNNNNNN � NNNNNNNN = 7 � 8 d�gitos con la numeraci�n.
     *  C = letra de control obtenido mediante el pseudo algoritmo siguiente:
     *      1.- De los d�gitos PPNNNNN, se suman los d�gitos en las posiciones pares (SUMA_A).
     *      2.- De los d�gitos PPNNNNN, se suman las cifras (1 � 2) resultantes de multiplicar cada d�gito en las posiciones impares (SUMA_B).
     *      3.- Tomar el complemento a 10 del d�gito de unidades obtenido de sumar SUMA_A y SUMA_B. Este es el d�gito de control.
     *      4.- Si la letra del tipo de organizaci�n (T) es N � P � Q � R � S � W, cambiar el d�gito de control por una letra seg�n las siguientes correspondencias:
     *          0 => J
     *          1 => A
     *          2 => B
     *          3 => C
     *          4 => D
     *          5 => E
     *          6 => F
     *          7 => G
     *          8 => H
     *          9 => I
     *          10 => J
     * Referencia: http://www.jagar.es/economia/ccif.htm
     *
     * @var string $id Cadena de 9 d�gitos/letras. Se admiten caracteres separadores que ser�n ignorados.
     */
     protected $id;
     
     /**
      * Errores. Lista de errores encontrados en el NIF en la �ltima validaci�n.
      * @var array string $errs
      */
     public $errs;
     
     /**
      * Constructor por defecto.
      */
     public function __construct($nif=null)
     {
         $this->id=$nif;
         $this->errs=array();
     }

     /**
      * Devuelve el identificador NIF.
      */
     public function getId()
     {
         return $this->id;
     }
     
     /**
      * Normaliza el NIF del objeto, pasando a may�sculas y eliminando cualquier caracter que no sea un d�gito � alguna de las letras permitidas.
      */
     public function normalizar()
     {
         $this->id=strtoupper($this->id);
         
         $this->id=preg_replace("/[^0123456789ABCDEFGHIJKLMNPQRSUVWXYZ]/", "", $this->id);
     }     
     
     /**
      * Convierte el NIF a una cadena de texto en un formato espec�fico.
      * NOTA: El formato es el usado en las funciones sprintf y los argumentos son, por este orden, el c�digo de tipo de NIF, el n�mero y el d�gito de control.
      * @param string $formato Formato deseado. Puede ser uno de los formatos predefinidos (FORMATO_NIF__GUIONES,FORMATO_NIF__ESPACIOS,FORMATO_NIF__DOSPUNTOS).
      * @return string representaci�n del NIF de este objeto en el formato especificado.
      */
     public function formatear($formato=null)
     {
         $codigo=NIF::clean($this->id);
         if($formato==null)
         {
             return $codigo;
         }
         else
         {
             switch($formato)
             {
                 case FORMATO_IPF_GUIONES:
                     $formato=FORMATO_NIF_GUIONES;
                     break;
                 case FORMATO_IPF_ESPACIOS:
                     $formato=FORMATO_NIF_ESPACIOS;
                     break;
                 case FORMATO_IPF_DOSPUNTOS:
                     $formato=FORMATO_NIF_DOSPUNTOS;
                     break;
             }
             $codOrg=substr($codigo,0,1);
             $numero=substr($codigo,1,strlen($codigo)-2);
             $dc=substr($codigo,-1);
             return sprintf($formato,$codOrg,$numero,$dc);
         }
     }
     
     /**
      * Valida el NIF.
      *
      * @return array Errores detectados (array vac�o si no hay errores).
      */
     public function validar()
     {
         $this->errs=array();
         if(!isset($this->id)||($this->id==""))
         {
             array_push($this->errs,"El NIF no est� definido.");
             return $this->errs;
         }
         
         $codigo=NIF::clean($this->id);
         
         $codOrg=substr($codigo,0,1);
         
         $NIF_invalido=false;
         $isCIF=false;
         $isNIE_X=false;
         $numero=substr($codigo,1,strlen($codigo)-2);
         switch($codOrg)
         {
             //case '0'-'9':
             case '0':
             case '1':
             case '2':
             case '3':
             case '4':
             case '5':
             case '6':
             case '7':
             case '8':
             case '9':
                 // DNI
                 $numero=substr($codigo,0,strlen($codigo)-1);
                 break;
             case 'X':
                 $isNIE_X=true;
                 break;
             case 'Y':
                 $numero='1'.$numero;
                 break;
             case 'Z':
                 $numero='2'.$numero;
                 break;
             case 'X':
             case 'K':
             case 'L':
             case 'M':
                 break;
                 
             // CIF
                 // ABCDEFGHJKLMNPQRSUVW
             case 'A':
             case 'B':
             case 'C':
             case 'D':
             case 'E':
             case 'F':
             case 'G':
             case 'H':
             case 'J':
             case 'N':
             case 'P':
             case 'Q':
             case 'R':
             case 'S':
             case 'U':
             case 'V':
             case 'W':
                 $isCIF=true;
                 break;
             default:
                 $NIF_invalido=true;
                 array_push($this->errs,"El NIF '".$codigo."' es incorrecto. La primera letra '".$codOrg."' no es v�lida.");
                 break;
         }
         
         if($NIF_invalido)
         {
             return $this->errs;
         }
         
         if($isCIF)
         {
             if(strlen($codigo)!=9)
             {
                 array_push($this->errs,"El NIF '".$this->id."' es incorrecto. Debe ser una combinaci�n de 8 � 9 d�gitos/letras. Los caracteres no permitidos se ignoran.");
                 return $this->errs;
             }
             
             $codprov=substr($codigo,1,2);
             $codsec=substr($codigo,3,5);
             $dc=substr($codigo,8,1);
             
             // Comprobamos el tipo de organizaci�n
             $temp=preg_replace("/[^ABCDEFGHJNPQRSUVW]/", "", $codOrg);
             if(strlen($temp)!=1)
             {
                 array_push($this->errs,"La letra que indica el tipo de organizaci�n '".$codOrg."' es incorrecta. Debe ser una de estas letras: A, B, C, D, E, F, G, H, J, K, L, M, N, P, Q, R, S, U, V � W.");
             }
             
             // Comprobamos el c�digo de provincia
             $temp=preg_replace("/[^0-9]/", "", $codprov);
             if(strlen($temp)!=2)
             {
                 array_push($this->errs,"El c�digo de provincia '".$codprov."' es incorrecto. Debe ser uno los siguientes: del 01 al 64, del 70 al 85 � del 91 al 99.");
             }
             else
             {
                 /*
                  * Los c�digos de provincia admitidos son los siguientes:
                  * 
                        +------------------------+------------------------------------------------+
                        | **Provincia **         | **Identificador **                             |
                        +========================+================================================+
                        | No Residente           | 0                                              |
                        +------------------------+------------------------------------------------+
                        | �lava                  | 1                                              |
                        +------------------------+------------------------------------------------+
                        | Albacete               | 2                                              |
                        +------------------------+------------------------------------------------+
                        | Alicante               | 03, 53, 54                                     |
                        +------------------------+------------------------------------------------+
                        | Almer�a                | 4                                              |
                        +------------------------+------------------------------------------------+
                        | �vila                  | 5                                              |
                        +------------------------+------------------------------------------------+
                        | Badajoz                | 6                                              |
                        +------------------------+------------------------------------------------+
                        | Islas Baleares         | 07, 57, 16                                     |
                        +------------------------+------------------------------------------------+
                        | Barcelona              | 08, 58, 59, 60, 61, 62, 63, 64, 65, 66, 68     |
                        +------------------------+------------------------------------------------+
                        | Burgos                 | 9                                              |
                        +------------------------+------------------------------------------------+
                        | C�ceres                | 10                                             |
                        +------------------------+------------------------------------------------+
                        | C�diz                  | 11,72                                          |
                        +------------------------+------------------------------------------------+
                        | Castell�n              | 12                                             |
                        +------------------------+------------------------------------------------+
                        | Ciudad Real            | 13                                             |
                        +------------------------+------------------------------------------------+
                        | C�rdoba                | 14,56                                          |
                        +------------------------+------------------------------------------------+
                        | La Coru�a              | 15,7                                           |
                        +------------------------+------------------------------------------------+
                        | Cuenca                 | 16                                             |
                        +------------------------+------------------------------------------------+
                        | Gerona                 | 17, 55, 67                                     |
                        +------------------------+------------------------------------------------+
                        | Granada                | 18, 19, 019                                    |
                        +------------------------+------------------------------------------------+
                        | Guadalajara            | 19                                             |
                        +------------------------+------------------------------------------------+
                        | Guip�zcoa              | 20,71                                          |
                        +------------------------+------------------------------------------------+
                        | Huelva                 | 21                                             |
                        +------------------------+------------------------------------------------+
                        | Huesca                 | 22                                             |
                        +------------------------+------------------------------------------------+
                        | Ja�n                   | 23                                             |
                        +------------------------+------------------------------------------------+
                        | Le�n                   | 24                                             |
                        +------------------------+------------------------------------------------+
                        | L�rida                 | 25                                             |
                        +------------------------+------------------------------------------------+
                        | La Rioja               | 26                                             |
                        +------------------------+------------------------------------------------+
                        | Lugo                   | 27                                             |
                        +------------------------+------------------------------------------------+
                        | Madrid                 | 28, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88 |
                        +------------------------+------------------------------------------------+
                        | M�laga                 | 29, 92, 93                                     |
                        +------------------------+------------------------------------------------+
                        | Murcia                 | 30,73                                          |
                        +------------------------+------------------------------------------------+
                        | Navarra                | 31,71                                          |
                        +------------------------+------------------------------------------------+
                        | Orense                 | 32                                             |
                        +------------------------+------------------------------------------------+
                        | Asturias               | 33,74                                          |
                        +------------------------+------------------------------------------------+
                        | Palencia               | 34                                             |
                        +------------------------+------------------------------------------------+
                        | Las Palmas             | 35,75                                          |
                        +------------------------+------------------------------------------------+
                        | Pontevedra             | 36, 37, 94                                     |
                        +------------------------+------------------------------------------------+
                        | Salamanca              | 37                                             |
                        +------------------------+------------------------------------------------+
                        | Santa Cruz de Tenerife | 38,76                                          |
                        +------------------------+------------------------------------------------+
                        | Cantabria              | 39                                             |
                        +------------------------+------------------------------------------------+
                        | Segovia                | 40                                             |
                        +------------------------+------------------------------------------------+
                        | Sevilla                | 41, 90, 91                                     |
                        +------------------------+------------------------------------------------+
                        | Soria                  | 42                                             |
                        +------------------------+------------------------------------------------+
                        | Tarragona              | 43,77                                          |
                        +------------------------+------------------------------------------------+
                        | Teruel                 | 44                                             |
                        +------------------------+------------------------------------------------+
                        | Toledo                 | 45                                             |
                        +------------------------+------------------------------------------------+
                        | Valencia               | 46, 96, 97, 98                                 |
                        +------------------------+------------------------------------------------+
                        | Valladolid             | 47                                             |
                        +------------------------+------------------------------------------------+
                        | Vizcaya                | 48,95                                          |
                        +------------------------+------------------------------------------------+
                        | Zamora                 | 49                                             |
                        +------------------------+------------------------------------------------+
                        | Zaragoza               | 50,99                                          |
                        +------------------------+------------------------------------------------+
                        | Ceuta                  | 51                                             |
                        +------------------------+------------------------------------------------+
                        | Melilla                | 52                                             |
                        +------------------------+------------------------------------------------+
                  */
                 $prov=intval($temp);
                 switch(true)
                 {
                     case (($prov >= 0) && ($prov <= 68)):
                         break;
                     case (($prov >= 70) && ($prov <= 88)):
                         break;
                     case (($prov >= 90) && ($prov <= 99)):
                         break;
                     default:
                         array_push($this->errs,"El c�digo de provincia '".$codprov."' es inv�lido. Debe ser uno los siguientes: del 01 al 64, del 70 al 85 � del 91 al 99.");
                         break;
                 }
             }
             
             // Comprobamos el n�mero secuencial
             $temp=preg_replace("/[^0-9]/", "", $codsec);
             if(strlen($temp)!=5)
             {
                 array_push($this->errs,"El c�digo secuencial '".$codsec."' es incorrecta. Debe ser 5 d�gitos.");
             }
             else
             {
                 // Comprobamos el d�gito de control
                 $digito_control=$this->calcularDcCIF($codigo);
                 $cif_codes = 'JABCDEFGHI';
                 if (in_array ($codOrg, array ('A', 'B', 'E', 'H')))
                 {
                     // N�mero
                     if (!in_array ($dc, array ('0','1','2','3','4','5','6','7','8','9')))
                     {
                         array_push($this->errs,"El d�gito de control '".$dc."' es incorrecto. Se esperaba un d�gito num�rico.");
                     }
                     else
                     {
                         if($dc!=$digito_control)
                         {
                             array_push($this->errs,"El d�gito de control '".$dc."' es inv�lido. Se esperaba '".$digito_control."'.");
                         }
                     }
                 }
                 elseif (in_array ($codOrg, array ('K', 'P', 'Q', 'S')))
                 {
                     // Letra
                     if (in_array ($codOrg, array ('0','1','2','3','4','5','6','7','8','9')))    //if (in_array ($dc, range('0','9'), false))                     //if(is_numeric($dc))
                     {
                         array_push($this->errs,"El d�gito de control '".$dc."' es incorrecto. Se esperaba una de las siguientes letras: A, B, C, D, E, F, G, H, I � J.");
                     }
                     else
                     {
                         if($dc!=$cif_codes[$digito_control])
                         {
                             array_push($this->errs,"El d�gito de control '".$dc."' es inv�lido. Se esperaba '".$cif_codes[$digito_control]."'.");
                         }
                     }
                 }
                 else
                 {
                     // Alfanum�rico
                     if (in_array ($codOrg, array ('0','1','2','3','4','5','6','7','8','9')))
                     {
                         if($dc!=$digito_control)
                         {
                             array_push($this->errs,"El d�gito de control '".$dc."' es inv�lido. Se esperaba '".$digito_control."' � '".$cif_codes[$digito_control]."'.");
                         }
                     }
                     else
                     {
                         if(($dc!=$digito_control) && ($dc!=$cif_codes[$digito_control]))
                         {
                             array_push($this->errs,"El d�gito de control '".$dc."' es inv�lido. Se esperaba '".$digito_control."' � '".$cif_codes[$digito_control]."'.");
                         }
                     }
                 }
             }
         }
         else
         {
             // NIF de persona f�sica
             if($isNIE_X)
             {
                 if((strlen($codigo)!=9)&&(strlen($codigo)!=10))
                 {
                     array_push($this->errs,"El NIF '".$this->id."' es incorrecto. Debe ser una combinaci�n de 8 � 9 d�gitos/letras. Los caracteres no permitidos se ignoran.");
                     return $this->errs;
                 }
             }
             else
             {
                 if(strlen($codigo)!=9)
                 {
                     array_push($this->errs,"El NIF '".$this->id."' es incorrecto. Debe ser una combinaci�n de 8 � 9 d�gitos/letras. Los caracteres no permitidos se ignoran.");
                     return $this->errs;
                 }
             }
             
             $digito_control=substr('TRWAGMYFPDXBNJZSQVHLCKE', $numero % 23, 1);
             $dc=substr($codigo,-1);
             if($digito_control!=$dc)
             {
                 array_push($this->errs,"El d�gito de control '".$dc."' es inv�lido. Se esperaba '".$digito_control."'.");
             }
         }
         return $this->errs;
     }

     /**
      * Indica si el NIF es v�lido o no.
      * 
      * @return boolean TRUE si el NIF es v�lido. FALSE en caso contrario.
      */
     public function esValido()
     {
         $this->validar();
         return empty($this->errs);
     }

     /**
      * Obtiene el tipo del NIF mirando el primer car�cter.
      * 
      * @return string
      */
     public function getTipo()
     {
         if(!isset($this->id)||($this->id==""))
         {
             //return NIF_ERR;
             return 'ERR';
         }
         
         $codigo=NIF::clean($this->id);
         $codOrg=substr($codigo,0,1);
         switch($codOrg)
         {
             //case '0'-'9':
             case '0':
             case '1':
             case '2':
             case '3':
             case '4':
             case '5':
             case '6':
             case '7':
             case '8':
             case '9':
                 //return NIF_DNI;
                 return 'DNI';
                 break;
             case 'X':
             case 'Y':
             case 'Z':
             case 'X':
             case 'K':
             case 'L':
             case 'M':
                 //return NIF_NIE;
                 return 'NIE';
                 break;
             case 'A':
             case 'B':
             case 'C':
             case 'D':
             case 'E':
             case 'F':
             case 'G':
             case 'H':
             case 'J':
             case 'N':
             case 'P':
             case 'Q':
             case 'R':
             case 'S':
             case 'U':
             case 'V':
             case 'W':
                 //return NIF_CIF;
                 return 'CIF';
                 break;
         }
         //return NIF_ERR;
         return 'ERR';
     }
     
     /**
      * Obtiene el tipo largo del NIF mirando el primer car�cter.
      *
      * @return string
      */
     public function getTipoLargo()
     {
         if(!isset($this->id)||($this->id==""))
         {
             //return NIF_ERR;
             return 'ERROR';
         }
         
         $codigo=NIF::clean($this->id);
         $codOrg=substr($codigo,0,1);
         switch($codOrg)
         {
             //case '0'-'9':
             case '0':
             case '1':
             case '2':
             case '3':
             case '4':
             case '5':
             case '6':
             case '7':
             case '8':
             case '9':
                 //return NIF_DNI;
                 return 'Documento Nacional de Identidad (DNI)';
                 break;
             case 'X':
             case 'Y':
             case 'Z':
             case 'X':
             case 'K':
             case 'L':
             case 'M':
                 //return NIF_NIE;
                 return 'N�mero de Identificaci�n de Extranjeros (NIE)';
                 break;
             case 'A':
             case 'B':
             case 'C':
             case 'D':
             case 'E':
             case 'F':
             case 'G':
             case 'H':
             case 'J':
             case 'N':
             case 'P':
             case 'Q':
             case 'R':
             case 'S':
             case 'U':
             case 'V':
             case 'W':
                 //return NIF_CIF;
                 return 'C�digo de Identificaci�n Fisal (CIF)';
                 break;
         }
         //return NIF_ERR;
         return 'ERROR';
     }
     
     /**
      * Convierte el NIF a una cadena de texto adecuada para ser mostrada al usuario.
      * @return string representaci�n textual del NIF.
      */
     public function toString()
     {
         $codigo=NIF::clean($this->id);
         switch($this->getTipo())
         {
             case 'DNI':
                 //return substr($codigo,0,2).'.'.substr($codigo,2,3).'.'.substr($codigo,5,3).'-'.substr($codigo,-1);
                 return number_format(substr($codigo,0,strlen($codigo)-1),0,'.','.').'-'.substr($codigo,-1);
                 break;
             case 'NIE':
                 return substr($codigo,0,1).'-'.number_format(substr($codigo,1,strlen($codigo)-2),0,'.','.').'-'.substr($codigo,-1);
                 break;
             case 'CIF':
                 return substr($codigo,0,1).'-'.substr($codigo,1,2).'-'.substr($codigo,3,strlen($codigo)-4).'-'.substr($codigo,-1);
                 break;
         }
         return 'NIF_ERR';
     }
     
     /**
      * Calcula el d�gito de control.
      * NOTA: Requiere que el CIF sea v�lido.
      */
     static private function calcularDcCIF($cif)
     {
         $suma=$cif[2]+$cif[4]+$cif[6];
         for ($i = 1; $i<8; $i += 2)
         {
             $tmp = 2 * $cif[$i];
             $suma += ($tmp > 9) ? ($tmp - 10 + 1) : $tmp;
         }
         return (10 - ($suma % 10)) % 10;
         //return (10 - substr ((string)$suma, -1)) % 10;
     }
     
     /**
      * Limpia el NIF de caracteres inv�lidos
      * NOTA: Requiere que el CIF sea v�lido.
      * 
      * @param string $nif NIF a limpiar
      * @return mixed NIF limpio
      */
     static private function clean($nif)
     {
         return preg_replace("/[^0-9A-Z]/", "", strtoupper($nif));
     }
}
