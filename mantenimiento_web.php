<?php 

require_once(__DIR__."/config.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/lib/RowDbIterator.class.php");
require_once(__DIR__."/classes/Establecimiento.class.php");
require_once(__DIR__."/classes/EstablishmentUser.class.php");
require_once(__DIR__."/classes/EstablishmentUserDao.class.php");

// FIXME: Ver si dejamos acceder a esta página a los grabadores.
$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR), array(PAGE_BLOCK_USUARIOS));

/// Parametros de la pagina
// ARG_ACTION: operación solicitada
define('ARG_ACTION', 'btnOper');

// Tipos de operaciones admitidas
define('OP_REFRESH', 'Refrescar');
define('OP_BLOCKAPP', 'Bloquear');
define('OP_UNBLOCKAPP', 'Desbloquear');

$operacion=null;
$error=null;

$web_cerrada=CERRAR_WEB_MANTENIMIENTO;


if(isset($_POST[ARG_ACTION]))
{
	switch($_POST[ARG_ACTION])
	{
		case OP_BLOCKAPP:
		case OP_UNBLOCKAPP:
			$operacion=$_POST[ARG_ACTION];
			$sql = DbHelper::prepare_sql(sprintf("update tb_configuration set cerrar_web_mantenimiento=%s",($operacion==OP_BLOCKAPP)?"1":"0"));
			$db = new Istac_Sql();
			$db->query($sql);
			if($db->affected_rows() > 0)
			{
				$web_cerrada=($operacion==OP_BLOCKAPP);
				$error=false;
			}
			else
				$error=true;
			break;
	}
}

// Refrescamos la información de las sesiones.
SesionActiva::purgarSesiones();			// Buen momento para purgar todas las sesiones caducadas de la BDD.
$sesiones=SesionActiva::getSesionesActivas();
$vv = array(
		'sesiones' 		=> 	$sesiones,
		'operacion'		=>	$operacion, 
		'web_cerrada'	=> 	$web_cerrada,
		'error'			=> 	$error
);

$page->render( "mantenimiento_web_view.php" , $vv);


$page->end_session();
?>