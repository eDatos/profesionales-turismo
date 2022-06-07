<?php

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../../lib/DateHelper.class.php");
require_once(__DIR__."/../Establecimiento.class.php");
require_once(__DIR__."/ExpectativasFormData.class.php");
require_once(__DIR__."/ExpectativasPlazos.class.php");
require_once(__DIR__."/../../lib/email.class.php");

/**
 * Operaciones relativas a las encuestas en curso realizadas en multiples paginas.
 */
class ExpectativasController
{
    
    /**
     * Obtiene los datos necesarios para poder mostrar el estado de la encuesta de expectativas.
     */
    public function cargar_estado_encuesta_expectativas($establecimiento, $fecha)
    {
        $exp_plazo = ExpectativasPlazos::cargar_plazo_abierto($fecha);
        
        $exp_estado = array();
        
        $exp_estado['plazo_esta_abierto'] = ($exp_plazo != null);
        if ($exp_plazo != null)
        {
            /// Obtener la encuesta si existe, para mostrar en la pagina si se ha entregado ya o está pendiente.
            $exp_dao = new ExpectativasFormDao();
            $trimestre = $exp_plazo->trimestre;
            $exp = $exp_dao->cargar_cabecera($establecimiento->id_establecimiento,$trimestre->trimestre, $trimestre->anyo);
            if (!$exp->es_nuevo)
            {
                $exp_estado['fecha_presentacion'] = $exp->fecha_grabacion;
            }
            else 
            {
                $exp_estado['fecha_presentacion'] = null;
            }
            
            $exp_estado['trimestre'] = $exp_plazo->trimestre;
            $exp_estado['fecha_limite'] = $exp_plazo->fecha_fin;
        } 
        
        return $exp_estado;
    }
    
    public function enviar_correo_confirmacion_cierre(Establecimiento $estab, Trimestre $trimestre = null)
    {
    	try {
    		$destinatario = $estab->email2;
    		if (!isset($destinatario))
    			$destinatario = $estab->email;
    		if (!isset($destinatario))
    			return;
    
    		$nombre_estab = $estab->nombre_largo();
    		$asunto = "Acuse de recibo de la encuesta de expectativas hoteleras: trimestre ". $trimestre->trimestre ." de " . $trimestre->anyo . " - " . $nombre_estab;
    
    		$exp_dao = new ExpectativasFormDao();
    		$cuerpo = $exp_dao->obtener_cuerpo_correo_desde_configuracion();
    
    		$email = new Email();
    		$email->send($asunto, $cuerpo, $destinatario);
    	}
    	catch (Exception $e)
    	{
    		log::error($e->getMessage());
    	}
    }
}

?>