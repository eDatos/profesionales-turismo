<?php

require_once(__DIR__."/../config.php");
require_once(__DIR__."/DateHelper.class.php");
require_once(__DIR__."/DbHelper.class.php");

define('SESSION_TIMEOUT_HISTERESIS', 2);

class Sesion
{
	var $sid;
	var $uid;
	var $tcreacion;
	var $tultacceso;
	
	// Posibles estados:
	// 0 = sesi�n creada y no registrada (sin registro en tabla TB_SESIONES).
	// 1 = sesi�n creada y registrada pero no autenticada.
	// 2 = sesi�n creada, registrada y autenticada.
	// 3 = sesi�n hu�rfana (sin fichero asociado).
	var $estado;
	
	var $caducada;
	
	public function __construct($sid,$uid,$tc,$tu)
	{
		$this->sid = $sid;
		$this->uid = $uid;
		$this->tcreacion = $tc;
		$this->tultacceso = $tu;
		$this->caducada=false;
	}
	
	public function check()
	{
		$limiteCaducidad=new DateTime();
		$limiteCaducidad->sub(new DateInterval("PT".(SESSION_TIMEOUT+SESSION_TIMEOUT_HISTERESIS)."M"));
		$tiempo = $limiteCaducidad->format("YmdHis");
		return ($this->tultacceso <= $tiempo);
	}
}

/*
 * Clase para el manejo de la tabla de sesiones activas (TB_SESIONES).
 * NOTA IMPORTANTE: Se requiere que se haya establecido correctamente la zona horaria del servidor mediante la funci�n date_default_timezone_set.
 */
class SesionActiva extends Sesion
{
	// FIXME: Problema => Registros repetidos (dos registros para una �nica sesi�n).
	// FIXME: Problema => Cuando un usuario y el administrador usan el mismo navegador, si el administrador ya ha iniciado sesi�n y el usuario intenta iniciar sesi�n con la web en mantenimiento, se pierde el regristo de la sesi�n del admininstrador.
	
	/*
	 * Constructor por defecto.
	 */
	public function __construct()
	{
		$this->sid = session_id();
	}
	
	/*
	 * Se crea una nueva sesi�n.
	 */
	public function crear()
	{
		$this->sid = session_id();
		$this->uid = null;
		$this->tcreacion = date("YmdHis");
		$this->tultacceso = $this->tcreacion;
		
		$this->borrar();
		$sql = sprintf("insert into tb_sesiones (sid,userid,tcreacion,tultacceso) values ('%s',%s,'%s','%s')",$this->sid,(isset($this->uid)?"'".$this->uid."'":'NULL'),$this->tcreacion,$this->tultacceso);
		$db = new Istac_Sql();
		$db->query($sql);
		return ($db->affected_rows() > 0);
	}
	
	/*
	 * La sesi�n ha sido recuperada, se refresca el tiempo del �ltimo acceso.
	 */
	public function refrescar($usuario)
	{
		$this->uid = $usuario;
		$this->tultacceso = date("YmdHis");
		$sql = sprintf("update tb_sesiones set userid=%s,tultacceso='%s' where sid='%s'",(isset($this->uid)?"'".$this->uid."'":'NULL'),$this->tultacceso,$this->sid);
		$db = new Istac_Sql();
		$db->query($sql);
		return ($db->affected_rows() > 0);
	}
	
	/*
	 * La sesi�n activa ha sido autenticada y se refresca toda la informaci�n.
	 */
	public function regenerar($usuario)
	{
		$nuevoSid = session_id();
		if(!empty($nuevoSid))
		{
			$this->uid = $usuario;
			$this->tcreacion = date("YmdHis");
			$this->tultacceso = $this->tcreacion;
			$sql = sprintf("update tb_sesiones set sid='%s',userid=%s,tcreacion='%s',tultacceso='%s' where sid='%s'",$nuevoSid,(isset($this->uid)?"'".$this->uid."'":'NULL'),$this->tcreacion,$this->tultacceso,$this->sid);
			$db = new Istac_Sql();
			$db->query($sql);
			if($db->affected_rows() > 0)
			{
				$this->sid = $nuevoSid;
				return true;
			}
			return false;
		}
		else
			return false;
	}
	
	/*
	 * Se elimina la sesi�n activa (porque se ha realizado una operaci�n de logout).
	 */
	public function borrar()
	{
		$sql = sprintf("delete from tb_sesiones where sid='%s'",$this->sid);
		$db = new Istac_Sql();
		$db->query($sql);
		return ($db->affected_rows() > 0);
	}
	
	/*
	 * Se reemplaza la sesi�n activa por una nueva. Puede ser porque la sesi�n sea inv�lida (no existente o no autenticada) o porque haya caducado.
	 * En este punto, la nueva sesi�n es an�nima (pendiente de autentificar).
	 */
	public static function reemplazar($sid)
	{
		if(!empty($sid))
		{
			$nuevoSid = session_id();
			if(!empty($nuevoSid))
			{
				$tiempo = date("YmdHis");
				$sql = sprintf("update tb_sesiones set sid='%s',userid=NULL,tcreacion='%s',tultacceso='%s' where sid='%s'",$nuevoSid,$tiempo,$tiempo,$sid);
				$db = new Istac_Sql();
				$db->query($sql);
				return ($db->affected_rows() > 0);
			}
		}
		return false;
	}
	
	/*
	 * Se eliminan todas aquellas sesiones activas que hayan caducado.
	 */
	public static function purgarSesiones()
	{
		$limiteCaducidad=new DateTime();
		$limiteCaducidad->sub(new DateInterval("PT".(SESSION_TIMEOUT+SESSION_TIMEOUT_HISTERESIS)."M"));
		$tiempo = $limiteCaducidad->format("YmdHis");
		$sql = sprintf("delete from tb_sesiones where tultacceso<='%s'",$tiempo);
		$db = new Istac_Sql();
		$db->query($sql);
		return $db->affected_rows();
	}
	
	/*
	 * Devuelve un array con todas las sesiones existentes.
	 */
	public static function getSesionesActivas()
	{
		$resultado=array();
		$dir=glob(ini_get('session.save_path').'/sess_*');
		foreach($dir as $fichero)
		{
			if(is_file($fichero))
			{
				$sid=substr(basename($fichero), 5);
				$uid=null;
				$tc=date("YmdHis",filectime($fichero));
				$tu=date("YmdHis",filemtime($fichero));
				$ses=new Sesion($sid, $uid, $tc, $tu);
				$ses->estado=0;
				$resultado[$sid]=$ses;
			}
		}
		
		$sql="select sid,userid,tcreacion,tultacceso from tb_sesiones";
		$db = new Istac_Sql();
		$db->query($sql);
		while($db->next_record())
		{
			$sid=$db->f('sid');
			$uid=$db->f('userid');
			$tc=$db->f('tcreacion');
			$tu=$db->f('tultacceso');
			$ses=new Sesion($sid, $uid, $tc, $tu);
			if(!isset($resultado[$sid]))
				$ses->estado=3;
			else
				$ses->estado=(isset($uid))?2:1;
			$resultado[$sid]=$ses;
		}
		
		foreach($resultado as $ses)
		{
			$ses->caducada=$ses->check();
		}

		return $resultado;
	}
}

?>