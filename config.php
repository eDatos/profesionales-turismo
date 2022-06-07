<?php

/**
 * Versión de la aplicación
 */
require_once(__DIR__."/config/version.php");

/**
 * Configuracion de la zona horaria
 */
date_default_timezone_set('Atlantic/Canary');
/*
 *
 *	Configuracion global de los errores de la aplicacion.
 *  NOTAS: En estos primeros instantes, la configuración que prevalece es la puesta en el fichero php.ini.
 */
define( 'LOG_FILENAME', '/data/gobiernodecanarias.org/profesionalesdelturismo/logs/log_'. date("Ymd") . '.txt');  /// Ruta a un archivo especifico en la que realizar el registro de logs (dejar vacio para utilizar el valor indicado en php.ini)
//define( 'LOG_FILENAME', './log_'. date("Ymd") . '.txt');  /// Ruta a un archivo especifico en la que realizar el registro de logs (dejar vacio para utilizar el valor indicado en php.ini)
define( 'PHP_ERROR_REPORTING', E_ALL|E_STRICT);	/// Que errores de PHP seran registrados.
error_reporting(PHP_ERROR_REPORTING);
if (defined('LOG_FILENAME'))
	ini_set('error_log',LOG_FILENAME);

require_once(__DIR__."/classes/unhandled_errors.php");


/* Valores de configuracion sensibles (almacenados en otra ruta por seguridad) */

//================================================================================================================
//  PRODUCCION: Mover el archivo config/secured_config.php al dir. externo indicado en la sig. linea comentada.
//================================================================================================================
//require("/nublo/gobiernodecanarias.org/data/istac/man/turismo/secured_config.php");
//require_once(__DIR__."/config/secured_config.php");
require("/data/gobiernodecanarias.org/profesionalesdelturismo/config/secured_config.php");


// Configuracion de PHPLIB (antes prepend.php)
$_PHPLIB = array();
$_PHPLIB["libdir"] = __DIR__."/lib/phplib/";

require($_PHPLIB["libdir"] . "db_oci8.inc");  //Gobierno de Canarias

// Local Istac ya no contiene informacion sensible.
require(__DIR__."/config/local_istac_xml.php");

Istac_Sql::DBInit();

$db = new Istac_Sql();

// Lectura de los parámetros de configuración desde la base de datos
$sql = "SELECT * FROM TB_CONFIGURATION";
$db->query($sql);
	
if (!$db->next_record())
{
//TODO: Ver qué se hace en caso de que no esté configurado el registro de configuración	
}

/*	
 * 
 *	Configuracion global de la aplicacion.
 */
define( 'PHP_DISPLAY_ERRORS', $db->f('php_display_errors')=='0' ? 'Off' : 'On');			/// Deshabilitar que los errores de php se muestren en la pagina.

/**
 * Num. de version de la aplicacion.
 */
define( 'APPLICATION_VERSION', $db->f('version'));


/**
 * Url publica del servidor (para generar url absolutas aun estando detras de un proxy).
 * Se utiliza como URL para los correos de cambio de contraseña.
 * @var unknown_type
 */
define( 'SERVER_APP_ROOT', $db->f('server_app_root'));

/**
 * Numero de filas maximas que devolverá la consulta del log. 
 * @var unknown_type
 */
define( 'MAX_LOG_LIST_ITEMS', $db->f('max_log_list_items'));

/**
 * Número máximo de avisos a motrar en el cuadro de avisos
 */
define('NUM_AVISOS_MOSTRAR', $db->f('num_avisos_mostrar'));

/**
 * Formato para los campos de fecha
 */
define('DATE_SEPARATOR', $db->f('date_separator'));

/**
 * Número máximo de unidades territoriales a mostrar por página en el formulario de alojamiento
 */
define('MAX_UT_MOSTRAR', $db->f('max_ut_mostrar'));

/**
 * Número máximo de meses anteriores a consultar para comprobar movimientos para la selección de países.
 */
define('MESES_SEL_PAISES', $db->f('meses_sel_paises'));

/**
 * Configuracion del CAPTCHA de la ventana de login
 */
define('ENABLE_CAPTCHA', $db->f('enable_captcha')=='1');

/**
 * Url del servicio web generador de captchas. 
 */
/*define('CAPTCHA_SERVICE', 'http://iisweb-pre.gobiernodecanarias.net:5003/ws/WSCaptcha/Service.asmx?wsdl');*/
define('CAPTCHA_SERVICE', 'https://www-pre.gobiernodecanarias.org/ws/WSCaptcha/Service.asmx?wsdl');
/*define('CAPTCHA_SERVICE', 'http://iis75pre-tfe.gobiernodecanarias.org:5012/ws/WSCaptcha/Service.asmx?wsdl');*/

/**
 * Periodo de tiempo durante el cual no se puede repetir el proceso de recuperacion de contraseña.
 */
define('RESET_PASSWORD_PERIOD', $db->f('reset_password_period')); // 60 segundos. Ver http://php.net/manual/es/dateinterval.construct.php

/**
 * Periodo de tiempo durante el cual es valido una petición de recuperación de contraseña.
 */
define('RESET_PASSWORD_UNTIL', $db->f('reset_password_until')); // 1 dia. Ver http://php.net/manual/es/dateinterval.construct.php

/**
 * Intervalo, en segundos, entre grabaciones intermedias (mecanismo de grabación transparente al usuario en el formulario de Encuestas de Alojamiento Turístico).
 * Poner a cero para desactivar las grabaciones intermedias.
 * NOTA: Compromiso entre reducir pérdidas de datos y rendimiento del servidor. Valor recomendado: 30 segundos.
 */
define('ALOJA_BCK_INTERVAL', $db->f('aloja_bck_interval'));

/**
 * Máscara de bits (byte) con los flags que controlan la grabación automática transparente al usuario en el formulario de Encuestas de Alojamiento Turístico. 
 * Sólo tienen significado los siguientes bits:
 * Bit #0 => Mostrar(1)/ocultar(0) el indicador de grabación intermedia.
 * Bit #1 => Mostrar(1)/ocultar(0) el indicador de página ocupada (grabaciones intermedias desactivadas temporalmente).
 */
define('ALOJA_BCK_FLAGS', $db->f('aloja_bck_flags'));

/**
 * Porcentaje de exceso admitido en el número de plazas en el formulario del cuestionario de alojamiento.
 */
define('ALOJA_EXCESO_PLAZAS', $db->f('aloja_exceso_plazas'));

/**
 * Porcentaje de exceso admitido en el número de plazas en el formulario del cuestionario de alojamiento.
 */
define('ALOJA_EXCESO_HABIT', $db->f('aloja_exceso_habit'));

/**
 * Tamaño máximo admitido del fichero XML del cuestionario de alojamiento.
 */
define('ALOJA_MAX_FILE_SIZE', $db->f('aloja_max_file_size'));

/**
 * Url al documento con la política de cookies usada por el sitio de la aplicación.
 */
define('POLITICA_COOKIES_URL', $db->f('politica_cookies_url'));

/**
 * Url al documento con la política de Datos Abiertos seguida por el ISTAC.
 */
define('POLITICA_DATOS_ABIERTOS_URL', $db->f('politica_datos_abiertos_url'));

/**
 * Url al documento con la política de privacidad seguida por el ISTAC.
 */
define('POLITICA_PRIVACIDAD_URL', $db->f('politica_privacidad_url'));

/**
 * Ruta al fichero que contiene el esquema usado para validar los ficheros XML subidos por los clientes con sus cuestionarios de alojamiento. 
 * @var unknown
 */
define('ALOJA_XML_ESQUEMA',__DIR__.'/content/alojamiento.xsd');

/**
 * Directorio donde se almacenan los ficheros con la información de las peticiones HTTP recibidas por la aplicación.
 * NOTA: Poner este valor a NULL para desactivar el mecanismo de registro de las peticiones HTTP.
 */
define('HTTP_LOG_PATH',$db->f('http_log_path'));

require_once(__DIR__."/classes/HttpRecord.class.php");
require_once(__DIR__."/classes/CookieRecord.class.php");



/**
 * Gestión de sesion
 */
// Seed para aleatorio en la generación del challenge para login.
define('SESSION_CHALLENGE_MAGIC', 'Istacmatanto');
// Tabla con los usuarios de la aplicación.
define('USER_DATATABLE', 'tb_auth_user_md5');
// Caducidad de la sesion en minutos.
define('SESSION_TIMEOUT', 30);


/*
 * Cierre de la web para mantenimiento
 */
define ('CERRAR_WEB_MANTENIMIENTO',$db->f('cerrar_web_mantenimiento')=='1' ? true : false);


/**
 * Información de contacto del ISTAC
 */
define('CONTACTO_URL', $db->f('contacto_url'));
define('CONTACTO_TELEFONO', $db->f('contacto_telefono'));
define('CONTACTO_FAX', $db->f('contacto_fax'));
define('CONTACTO_MAIL', $db->f('contacto_mail'));
define('AVISO_LEGAL_URL', $db->f('avisolegal_url'));
define('SUGERENCIAS_RECLAMACIONES_URL', $db->f('sugerencias_url'));

/**
 * Configuracion de envio de correo.
 */
define('SMTP_SERVER', $db->f('smtp_server'));
define('USUARIO_MAIL', $db->f('usuario_mail'));
define('PASS_MAIL', $db->f('pass_mail'));
define('DIRECCION_MAIL', $db->f('direccion_mail'));
define('NOMBRE_USUARIO_MAIL', $db->f('nombre_usuario_mail'));
define("MAIL_SMTP_PORT",$db->f('mail_smtp_port'));
define("MAIL_USE_SSL",$db->f('mail_use_ssl')=='1');
define("RECEPTOR_MAILS", $db->f('receptor_mails'));
define("MAIL_SEPARADOS",$db->f('mail_separados')!='N');
define('ENABLE_MAIL',$db->f('enable_mail')!='N');

/**
 * Configuracion de los consumos.
 */
define('USAR_HISTORIAL_SUGERIR_CONSUMO', $db->f('usar_historial_sugerir_consumo'));

/**
 * Configuracion del banner
 */
define('ENABLE_BANNER', $db->f('enable_banner')=='1');
//define('BANNER_LOCATION', 'images/banner/');
//define('BANNER_LOCATION', 'http://www.gobiernodecanarias.org/istac/galerias/imagenes/banners/profesionalesdelturismo/');
define('BANNER_LOCATION', '');

define('BANNER_SPEED', 5000); // milisegundos que se mantiene cada imagen del banner.

/**
 * Lista de imágenes y url que se utilizarán en el banner. 
 */
$banners_cfg = array();

//Se añade cada uno de los elementos del array de banners
for($i=1;$i<=10;$i++)
{
	if($db->f('banner' . $i . '_imagen'))
	{
			$banners_cfg[$db->f('banner' . $i . '_imagen')]=$db->f('banner' . $i . '_url');
	}
}


/**
 * Configuracion de librerias especificas
 */
// Helpers para filtros para evitar la inyeccion de codigo y cross-site-script
require_once(__DIR__."/lib/ext/class.inputfilter.php5"); 
require_once(__DIR__."/lib/ext/xss.inc");

// Helpers para el registro (auditoria) de accesos a la aplicación
require_once(__DIR__."/classes/registro_accesos.class.php");

/* 
    Error reporting. 
*/  
ini_set('display_errors', (PHP_DISPLAY_ERRORS == 1) ? 'On' : 'Off');

/**
 *  Error logging
 */ 
require_once(__DIR__."/lib/Log.class.php");

/** 
 * Constantes globales de la aplicacion 
 **/
require_once(__DIR__."/config/constantes.php");

?>