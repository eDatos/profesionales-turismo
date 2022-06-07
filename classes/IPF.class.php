<?php

//require_once(__DIR__."/../config.php");


define("FORMATO_IPF_GUIONES",'-');
define("FORMATO_IPF_ESPACIOS",' ');
define("FORMATO_IPF_DOSPUNTOS",':');

/**
 * Clase que representa un identificador de un empleador.
 * Esto se hace para contabilizar, mediante cuestionarios, el n�mero de empleados del establecimiento tur�stico.
 *
 * @author SCC,SL
 *
 */
abstract class IPF
{
    /**
     * Devuelve el identificador.
      * @return string identificador.
     */
    abstract public function getId();
    
    /**
     * Normaliza el identificador. Esto suele consistir en pasar a may�sculas y eliminar cualquier caracter no permitidos.
     */
    abstract public function normalizar();
     
     /**
      * Convierte el identificador a una cadena de texto en un formato espec�fico.
      * @param string $formato Formato deseado.
      * @return string representaci�n del identificador en el formato especificado.
      */
     abstract public function formatear($formato=null);
     
     /**
      * Valida el identificador.
      *
      * @return array Errores detectados (array vac�o si no hay errores).
      */
     abstract public function validar();
     
     /**
      * Indica si el identificador es v�lido o no.
      *
      * @return boolean TRUE si el identificador es v�lido. FALSE en caso contrario.
      */
     abstract public function esValido();
     
     /**
      * Obtiene el tipo del identificador.
      *
      * @return string
      */
     abstract public function getTipo();
     
     /**
      * Obtiene el tipo del identificador en formato largo.
      *
      * @return string
      */
     abstract public function getTipoLargo();
     
     /**
      * Obtiene la clase del identificador.
      *
      * @return string
      */
     public function getClase()
     {
         return get_class($this);
     }
     
     /**
      * Convierte el identificador a una cadena de texto adecuada para ser mostrada al usuario.
      * @return string representaci�n textual del identificador.
      */
     abstract public function toString();
}
