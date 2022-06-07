<?php
require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../../lib/DbHelper.class.php");
require_once(__DIR__."/../../lib/DateHelper.class.php");
require_once(__DIR__."/../../lib/Trimestre.class.php");


/**
 * Representa un plazo de recogida de encuesta de expectativas, 
 * especificado por una fecha de inicio, una fecha de final y el Trimestre (ver Trimestre.class.php) de la encuesta que está abierta.
 */
class ExpectativasPlazos
{
	/** Comienzo del plazo de recogida **/
    var $fecha_inicio;
    /** Finalizacion del plazo de recogida, incluido **/
    var $fecha_fin;
    /** objeto Trimestre, indicando el trimestre */
    var $trimestre;
        
    /**
     * Obtiene el plazo abierto para el instante especificado. Devuelve nulo si no hay ningun plazo abierto en ese instante.
     * @param $fecha
     * @return
     */
    public static function cargar_plazo_abierto(DateTime $fecha)
    {
        /// Obtener el registro de plazo en el que la fecha indicada cae dentro del rango fecha_inicio-fecha_fin.
        $sql = DbHelper::prepare_sql("select to_char(fecha_inicio,'DD/MM/YYYY') fi, to_char(fecha_fin,'DD/MM/YYYY') ff, trimestre from tb_expectativas_plazos
                                      where fecha_inicio <= to_date(:t,'DD/MM/YYYY') and to_date(:t,'DD/MM/YYYY') <= fecha_fin",
                                      array(":t" => (string)($fecha->format('d/m/Y'))));
        
        $db = new Istac_Sql();
        $db->query($sql);
        if ($db->next_record())
        {
            $ep = new ExpectativasPlazos();
            $ep->fecha_inicio = DateTime::createFromFormat('d/m/Y', $db->f('fi'));
            $ep->fecha_fin = DateTime::createFromFormat('d/m/Y', $db->f('ff'));
            
            $trimestre_encuesta = new Trimestre($db->f('trimestre'), $fecha->format('Y') );
            /// CASO ESPECIAL: El plazo se refiere al primer trimestre del año que viene, en este caso se le añade un año.
            if ($trimestre_encuesta->trimestre < Trimestre::create_from_date($fecha)->trimestre )
            {
                // El trimestre para la encuesta es del año que viene
                $trimestre_encuesta = $trimestre_encuesta->anyosiguiente();
            }
            $ep->trimestre = $trimestre_encuesta;
            return $ep;
        }
        
        return null;
    }
        
    /**
     * Obtiene la lista de plazos almacenados en la BBDD.
     * @return
     */
    public static function cargar_plazos()
    {
        $sql = "select to_char(fecha_inicio,'DD/MM/YYYY') fi, to_char(fecha_fin,'DD/MM/YYYY') ff, trimestre from tb_expectativas_plazos order by trimestre";
        
        $db = new Istac_Sql();
        $db->query($sql);
        
        // La pos. 0 no se usa.
        $plazos = array_fill(0, 5, null);
        
        while ($db->next_record())
        {
            $pl = new ExpectativasPlazos();
            $pl->fecha_inicio = DateTime::createFromFormat('d/m/Y', $db->f('fi'));
            $pl->fecha_fin = DateTime::createFromFormat('d/m/Y', $db->f('ff'));
            //pl->trimestre no se usa
            $trim_index = $db->f('trimestre');
            $plazos[ (int) $trim_index ] = $pl;
        }
        
        return $plazos;
    }
    
    /**
     * Guarda los plazos indicados en la tabla de plazos de la encuesta de expectativas.
     * el array pasado contiene en cada posicion un objeto ExpectativasPlazos. El indice en el array indica el trimestre (no se usa la posicion 0).
     * Si una posicion del array contiene nulo, el plazo para dicho trimestre se borra de la bbdd (indicando así que no hay encuesta abierta para dicho trimestre).
     * @param array $plazos
     */
    public static function guardar_plazos(array $plazos)
    {
        $db = new Istac_Sql();
        for ($i = 1; $i <= 4; $i++)
        {
            $sql = DbHelper::prepare_sql("delete from tb_expectativas_plazos where trimestre = :trim", array(':trim' => (int)$i));
            $db->query($sql);
            
            if (isset($plazos[$i]) || $plazos[$i] != null)
            {
                $sql = DbHelper::prepare_sql("insert into tb_expectativas_plazos (fecha_inicio, fecha_fin, trimestre) 
                                              values (to_date(:fi, 'DD/MM/YYYY'), to_date(:ff, 'DD/MM/YYYY'), :trim)", 
                                          array(':fi'   => (string)$plazos[$i]->fecha_inicio->format('d/m/Y'),
                                                ':ff'   => (string)$plazos[$i]->fecha_fin->format('d/m/Y'),
                                                ':trim' => (int)$i));
                $db->query($sql);
                if ($db->Error != null)
                {
                    @log::error("No se pudo actualizar el plazo del trimestre $i: " + $db->Error);
                }
            }
        }
        
        
    }
}

?>