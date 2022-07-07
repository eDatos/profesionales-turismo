<?php

//require_once(__DIR__."/../config.php");
require_once(__DIR__."/IPF.class.php");

define('NSS_CCC',0);
define('NSS_NAF',1);
define('NSS_ERR',9);


define("FORMATO_NSS_GUIONES",'%1$s-%2$s-%3$s');
define("FORMATO_NSS_ESPACIOS",'%1$s %2$s %3$s');
define("FORMATO_NSS_DOSPUNTOS",'%1$s:%2$s:%3$s');


/**
 * Clase que representa una cuenta de cotizaci�n a la Seguridad Social a la que asociar empleados.
 * Esto se hace para contabilizar, mediante cuestionarios, el n�mero de empleados del establecimiento tur�stico.
 *
 * @author SCC,SL
 *
 */
class NSS extends IPF
{
    /**
     * N�mero de la Seguridad Social, que puede ser de dos tipos:
     * 
     * [A] C�digo de Cuenta de Cotizaci�n (CCC) en la Seguridad Social que consta de 11 d�gitos. El formato es el siguiente:
     *  PPSSSSSSSCC
     * donde
     *  PP = dos d�gitos con c�digo de provincia
     *  SSSSSSS = n�mero secuencial de 7 d�gitos, asignado por la Seguridad Social
     *  CC = dos d�gitos de control obtenidos mediante el pseudo algoritmo siguiente:
     *      if (NumSS.substr(2,1) == 0)
     *          NumSS = NumSS.substr(0,2)+NumSS.substr(3, NumSS.length-3)
     *      dc = parseInt(NumSS) % 97
     *      if (dc <= 9)
     *          dc = "0" + dc
     * 
     * [N] N�mero de Afiliaci�n (NAF) a la Seguridad Social que consta de 12 d�gitos. El formato es el siguiente:
     *  PPSSSSSSSSCC
     * donde
     *  PP = dos d�gitos con c�digo de provincia
     *  SSSSSSSS = n�mero secuencial de 8 d�gitos, asignado por la Seguridad Social
     *  CC = dos d�gitos de control obtenidos mediante el pseudo algoritmo siguiente:
     *      if (NumSS.substr(2,1) == 0)
     *          NumSS = NumSS.substr(0,2)+NumSS.substr(3, NumSS.length-3)
     *      dc = parseInt(NumSS) % 97
     *      if (dc <= 9)
     *          dc = "0" + dc
     * Referencia: https://www.gabilos.com/textocalculadoranumss.htm
     *
     * @var string Cadena de 11 � 12 d�gitos. Se admiten caracteres separadores que ser�n ignorados.
     */
     protected $id;
     
     /**
      * Errores. Lista de errores encontrados en el NSS en la �ltima validaci�n.
      * @var array string $errs
      */
     public $errs;
     
     /**
      * Constructor por defecto.
      */
     public function __construct($nss=null)
     {
         $this->id=$nss;
         $this->errs=array();
     }
     
     /**
      * Devuelve el identificador NSS.
      */
     public function getId()
     {
         return $this->id;
     }
     
     /**
      * Normaliza el NSS del objeto, eliminando cualquier caracter que no sea un d�gito.
      */
     public function normalizar()
     {
         $this->id=preg_replace("/[^0-9]/", "", $this->id);
     }
     
     /**
      * Convierte el NSS a una cadena de texto en un formato espec�fico.
      * NOTA: El formato es el usado en las funciones sprintf y los argumentos son, por este orden, el c�digo de provincia, el n�mero secuencial y los dos d�gitos de control.
      * @param string $formato Formato deseado. Puede ser uno de los formatos predefinidos (FORMATO_NSS__GUIONES,FORMATO_NSS__ESPACIOS,FORMATO_NSS__DOSPUNTOS).
      * @return string representaci�n del NSS de este objeto en el formato especificado.
      */
     public function formatear($formato=null)
     {
         $codigo=NSS::clean($this->id);
         if($formato==null)
         {
             return $codigo;
         }
         else
         {
             switch($formato)
             {
                 case FORMATO_IPF_GUIONES:
                     $formato=FORMATO_NSS_GUIONES;
                     break;
                 case FORMATO_IPF_ESPACIOS:
                     $formato=FORMATO_NSS_ESPACIOS;
                     break;
                 case FORMATO_IPF_DOSPUNTOS:
                     $formato=FORMATO_NSS_DOSPUNTOS;
                     break;
             }
             $codprov=substr($codigo,0,2);
             $numero=substr($codigo,2,strlen($codigo)-4);
             $dc=substr($codigo,-2);
             return sprintf($formato,$codprov,$numero,$dc);
         }
     }
     
     /**
      * Valida el n�mero de la Seguridad Social.
      *
      * @return array Errores detectados (array vac�o si no hay errores).
      */
     public function validar()
     {
         $this->errs=array();
         if(!isset($this->id)||($this->id==""))
         {
             array_push($this->errs,"El NSS no est� definido.");
             return $this->errs;
         }
         $codigo=NSS::clean($this->id);
         if((strlen($codigo)!=11)&&(strlen($codigo)!=12))
         {
             array_push($this->errs,"El NSS '".$this->id."' es incorrecto. Debe ser un n�mero entero de 11 � 12 d�gitos. Los caracteres no permitidos se ignoran.");
             return $this->errs;
         }
         
         $codprov=substr($codigo,0,2);
         $numero=substr($codigo,0,strlen($codigo)-2);
         $dc=substr($codigo,-2);
         
         // Comprobamos el c�digo de provincia
         /*
          * Los c�digos de provincia admitidos son los siguientes:
          *
          +--------+------------------------+
          | C�digo | Provincia              |
          +========+========================+
          | 1      | Araba/�lava            |
          +--------+------------------------+
          | 2      | Albacete               |
          +--------+------------------------+
          | 3      | Alicante/Alacant       |
          +--------+------------------------+
          | 4      | Almer�a                |
          +--------+------------------------+
          | 5      | �vila                  |
          +--------+------------------------+
          | 6      | Badajoz                |
          +--------+------------------------+
          | 7      | Balears, Illes         |
          +--------+------------------------+
          | 8      | Barcelona              |
          +--------+------------------------+
          | 9      | Burgos                 |
          +--------+------------------------+
          | 10     | C�ceres                |
          +--------+------------------------+
          | 11     | C�diz                  |
          +--------+------------------------+
          | 12     | Castell�n/Castell�     |
          +--------+------------------------+
          | 13     | Ciudad Real            |
          +--------+------------------------+
          | 14     | C�rdoba                |
          +--------+------------------------+
          | 15     | Coru�a, A              |
          +--------+------------------------+
          | 16     | Cuenca                 |
          +--------+------------------------+
          | 17     | Girona                 |
          +--------+------------------------+
          | 18     | Granada                |
          +--------+------------------------+
          | 19     | Guadalajara            |
          +--------+------------------------+
          | 20     | Gipuzkoa               |
          +--------+------------------------+
          | 21     | Huelva                 |
          +--------+------------------------+
          | 22     | Huesca                 |
          +--------+------------------------+
          | 23     | Ja�n                   |
          +--------+------------------------+
          | 24     | Le�n                   |
          +--------+------------------------+
          | 26     | Rioja, La              |
          +--------+------------------------+
          | 27     | Lugo                   |
          +--------+------------------------+
          | 28     | Madrid                 |
          +--------+------------------------+
          | 29     | M�laga                 |
          +--------+------------------------+
          | 30     | Murcia                 |
          +--------+------------------------+
          | 31     | Navarra                |
          +--------+------------------------+
          | 32     | Ourense                |
          +--------+------------------------+
          | 33     | Asturias               |
          +--------+------------------------+
          | 34     | Palencia               |
          +--------+------------------------+
          | 35     | Palmas, Las            |
          +--------+------------------------+
          | 36     | Pontevedra             |
          +--------+------------------------+
          | 37     | Salamanca              |
          +--------+------------------------+
          | 38     | Santa Cruz de Tenerife |
          +--------+------------------------+
          | 40     | Segovia                |
          +--------+------------------------+
          | 41     | Sevilla                |
          +--------+------------------------+
          | 42     | Soria                  |
          +--------+------------------------+
          | 43     | Tarragona              |
          +--------+------------------------+
          | 44     | Teruel                 |
          +--------+------------------------+
          | 46     | Valencia/Val�ncia      |
          +--------+------------------------+
          | 47     | Valladolid             |
          +--------+------------------------+
          | 48     | Bizkaia                |
          +--------+------------------------+
          | 49     | Zamora                 |
          +--------+------------------------+
          | 25     | Lleida                 |
          +--------+------------------------+
          | 39     | Cantabria              |
          +--------+------------------------+
          | 45     | Toledo                 |
          +--------+------------------------+
          | 50     | Zaragoza               |
          +--------+------------------------+
          | 51     | Ceuta                  |
          +--------+------------------------+
          | 52     | Melilla                |
          +--------+------------------------+
          | 53     | Otros territorios      |
          +--------+------------------------+
          | 66     | Extranjero             |
          +--------+------------------------+
          */
         $prov=intval($codprov);
         switch(true)
         {
             case (($prov >= 1) && ($prov <= 53)):
                 break;
             case ($prov == 66):
                 break;
             default:
                 array_push($this->errs,"El c�digo de provincia '".$codprov."' es inv�lido. Debe ser uno los siguientes: del 01 al 53 � 66.");
                 break;
         }

         // Comprobamos el d�gito de control
         $digito_control=NSS::calcularDc($numero);
         if($dc!=$digito_control)
         {
             array_push($this->errs,"El d�gito de control '".$dc."' es inv�lido. Se esperaba '".$digito_control."'.");
         }
         return $this->errs;
     }
     
     /**
      * Indica si el NSS es v�lido o no.
      *
      * @return boolean TRUE si el NSS es v�lido. FALSE en caso contrario.
      */
     public function esValido()
     {
         $this->validar();
         return empty($this->errs);
     }
     
     /**
      * Obtiene el tipo del NSS mirando la longitud.
      *
      * @return string
      */
     public function getTipo()
     {
         if(!isset($this->id)||($this->id==""))
         {
             //return NSS_ERR;
             return 'ERR';
         }
         
         $codigo=NSS::clean($this->id);
         switch(strlen($codigo))
         {
             case 11:
                 //return NSS_CCC;
                 return 'CCC';
                 break;
             case 12:
                 //return NSS_NAF;
                 return 'NAF';
                 break;
         }
         //return NSS_ERR;
         return 'ERR';
     }
     
     /**
      * Obtiene el tipo largo del NSS mirando la longitud.
      *
      * @return string
      */
     public function getTipoLargo()
     {
         if(!isset($this->id)||($this->id==""))
         {
             //return NSS_ERR;
             return 'ERROR';
         }
         
         $codigo=NSS::clean($this->id);
         switch(strlen($codigo))
         {
             case 11:
                 //return NSS_CCC;
                 return 'C�digo de Cuenta de Cotizaci�n a la Seguridad Social (CCC)';
                 break;
             case 12:
                 //return NSS_NAF;
                 return 'N�mero de Afiliaci�n a la Seguridad Social (NAF)';
                 break;
         }
         //return NSS_ERR;
         return 'ERROR';
     }
     
     /**
      * Convierte el NSS a una cadena de texto adecuada para ser mostrada al usuario.
      * @return string representaci�n textual del NSS.
      */
     public function toString()
     {
         $codigo=NSS::clean($this->id);
         return substr($codigo,0,2).'-'.substr($codigo,2,strlen($codigo)-4).'-'.substr($codigo,-2);
     }
     
     /**
      * Calcula los d�gitos de control.
      */
     static private function calcularDc($numero)
     {
         // NOTA: El n�mero secuencial debe tener 7 � 8 d�gitos. Si el d�gito m�s significativo (izquierda) es 0, se considera que no est� presente a efectos del c�lculo.
         // Ejemplos (extra�dos de http://www.migoia.com/migoia/util/NSS/NSS.pdf)
         /*
          *          12345678
          * (NAF) 28/09999999/69    ==> 289999999 % 97 = 69
          * (NAF) 28/00123456/66    ==> 280123456 % 97 = 66
          * (NAF) 28/10000000/16    ==> 2810000000 % 97 = 16
          * (CCC) 01/0999999/53     ==> 1999999 % 97 = 53
          * (CCC) 01/1000000/06     ==> 11000000 % 97 = 6
          */
         // ATENCI�N: Falla cuando el n�mero a dividir entre 97 es mayor que PHP_INT_MAX (2.147.483.647).
         // NOTA: 1.000.000 mod 97 = 27
         // NOTA: 10.000.000 mod 97 = 76
         // NOTA: 100.000.000 mod 97 = 81
         
         // En caso de CCC el n�mero serial m�ximo ser�a 9.999.999 ==>  9.999.999 + 99*96 = 10.009.503 (<PHP_INT_MAX)
         // El caso m�s extremo es el de un NAF con n�mero serial 99.999.999 y provincia 99 ==> 99.999.999 + 99*96 = 100.009.503 (<PHP_INT_MAX)
         
         $codprov=substr($numero,0,2);
         $b=substr($numero,2,strlen($numero)-2);
         if(strlen($b)>7)
         {
             // NAF (d�gitos significativos) ==>          7                        8
             $resto = ($b < 10000000) ? ($b + ($codprov * 76)) : ($b + ($codprov * 81));
         }
         else
         {
             // CCC (d�gitos significativos) ==>          6                        7
             $resto = ($b < 1000000) ? ($b + ($codprov * 27)) : ($b + ($codprov * 76));
         }
         return sprintf('%02d', $resto % 97);
     }
     
     /**
      * Limpia el NSS de caracteres inv�lidos
      *
      * @param string $nss NSS a limpiar
      * @return mixed NSS limpio
      */
     static private function clean($nss)
     {
         return preg_replace("/[^0-9]/", "", $nss);
     }
}
