<?php

/**
 * Cabecera de las paginas de administracion
 */
define("ADMIN_TITLE","Gesti�n del portal de estad�sticas de turismo para profesionales del sector");
define("USER_TITLE","Portal de estad�sticas de turismo para profesionales del sector");

/*
	Textos a mostrar para los permisos de usuarios
 */
define("PERMISOS_USUARIO_TEXT", "Alojamiento y expectativas");
define("PERMISOS_RECEPCION_TEXT", "Alojamiento");
define('PERMISOS_CONSUMOS_TEXT', 'Facturas');

/**
 *  Contenido est�tico de la aplicacion
 */
define("CONTENT_INF_LEGAL", "content/aviso_legal_portal_turismo.pdf");
define("CONTENT_EXPECTATIVAS_PDF", "content/cuestionario_expectativas_2012.pdf");
define("CONTENT_NOTA_CONFIDENCIAL", "content/confidencialidad.html");
define("CONTENT_INSTRUCCIONES_USO_WEB", "content/ayuda/instrucciones_uso.pdf");
define("CONTENT_INSTRUCCIONES_EXPECTATIVAS", "content/instrucciones_expectativas.pdf");
define("CONTENT_CORREO_RECUPERACION", "content/cuerpo_correo_recuperacion.html");
define("CONTENT_ALOJAMIENTO_XSD", "content/alojamiento.xsd");
define("CONTENT_DESCRIPCION_ETIQUETAS", "content/ANEXO_II_Descripcion_de_etiquetas.pdf");
define("CONTENT_VALIDACIONES", "content/ANEXO_III_Validaciones.pdf");

/**
 * Vinculos externos
 */
define("EXTERN_WEB_ISTAC", "http://www.gobiernodecanarias.org/istac/");
define("EXTERN_WEB_ESTADISTICAS", "http://www.gobiernodecanarias.org/istac/temas_estadisticos");
define("EXTERN_EL_ISTAC", "http://www.gobiernodecanarias.org/istac/istac/");
#define("EXTERN_WEB_ESCOLAR","http://www.gobiernodecanarias.org/istac/webescolar/");
define("EXTERN_INFORMACION_XML","codificaciones.php");
define("EXTERN_CONTACTO", "http://www.gobiernodecanarias.org/istac/servicios/atencion.html");
define("EXTERN_SUGREC", "http://www.gobiernodecanarias.org/principal/sugrec/");
define("EXTERN_AVISOLEGAL", "http://www.gobiernodecanarias.org/es/avisolegal.html");
define("EXTERN_ACCESIBILIDAD", "http://www.gobiernodecanarias.org/es/accesibilidad.html");

/**
 * Encuesta de expectativas
 */
define("FORM_EXPECTATIVAS", "/content/expectativas.xml");

/**
 *  Nombres de las paginas de la aplicacion
 */
define("PAGE_HOME", "index.php");
define("PAGE_LOGOUT", "logout.php");
define("PAGE_LOGIN", "login.php");
define("PAGE_LOGIN_DENIED", "login_denied.php");
define("PAGE_PASS_RECOVER", "estab_passwd_recover.php");
define("PAGE_PASS_RESET", "reset_login.php");
define("PAGE_WEB_CERRADA", "web_cerrada.php");


define("PAGE_ALOJA_INDEX", "aloja_index.php");
define("PAGE_ALOJA_SELECT_PAISES", "aloja_select_paises.php");
define("PAGE_ALOJA_PRINT", "aloja_form_print.php");
define("PAGE_ALOJA_FORM", "aloja_form.php");
define("PAGE_ALOJA_FORM_AJAX", "aloja_form_ajax.php");
define("PAGE_ALOJA_FORM_ENVIO", "aloja_form_envio.php");
define("PAGE_ALOJA_MES_SELECT", "admin_aloja_mes_select.php");
define("PAGE_ALOJA_XML", "aloja_xml.php");
define("PAGE_ALOJA_CONTROL", "admin_aloja_control.php");
define("PAGE_ALOJA_CONTROL_MULTIPLE", "admin_aloja_control_multiple.php");
define("PAGE_ALOJA_PLAZOS", "admin_aloja_plazos.php");
define("PAGE_ALOJA_PLAZOS_EXCEP", "admin_aloja_plazos_excep.php");
define("PAGE_ALOJA_PLAZOS_EXCEP_AJAX", "admin_aloja_plazos_excep_ajax.php");
define("PAGE_ALOJA_ACUSE", "aloja_acuse.php");
define("PAGE_ALOJA_RESULTADOS", "aloja_resultados.php");
define("PAGE_ALOJA_ANTERIORES", "admin_aloja_res_anteriores.php");
define("PAGE_ALOJA_NULA", "aloja_nula.php");

define("PAGE_EXP_INDEX", "exp_index.php");
define("PAGE_EXP_FORMULARIO", "exp_formulario.php");
define("PAGE_EXP_FORMULARIO_AJAX", "exp_formulario_ajax.php");
define("PAGE_EXP_PLAZOS", "admin_exp_plazos.php");
define("PAGE_EXP_ADMIN_CUESTIONARIOS", "admin_exp_cuestionarios.php");
define("PAGE_EXP_TRIMESTRE_SELECT", "admin_exp_trimestre_select.php");
define("PAGE_EXP_ANTERIORES", "admin_exp_anteriores.php");

define("PAGE_AVISOS", "admin_avisos.php");
define("PAGE_AVISOS_LIST", "avisos_list.php");

define("PAGE_NOTICIAS", "admin_noticias.php");

define("PAGE_EMPLEO", "admin_empleadores.php");
define("PAGE_EMPLEO_INDEX", "empleo_index.php");
define("PAGE_ADMIN_EMPLEADORES", "admin_empleadores.php");
define("PAGE_EMPLEO_FORM", "empleo_form.php");
define("PAGE_EMPLEO_PRINT", "empleo_form_print.php");
define("PAGE_EMPLEO_FORM_AJAX", "empleo_form_ajax.php");
define("PAGE_EMPLEO_FORM_ENVIO", "empleo_form_envio.php");
define("PAGE_EMPLEO_ACUSE", "empleo_acuse.php");

define("PAGE_CONSUMO_INDEX", "consumo_index.php");
define("PAGE_CONSUMO_FORM", "consumo_form.php");
define("PAGE_CONSUMO_LIST", "consumo_list.php");
define("PAGE_CONSUMO_PRINT", "consumo_print.php");

define("PAGE_LOG_CONFIG", "admin_log_config.php");
define("PAGE_LOG_SEARCH", "admin_log_search.php");
define("PAGE_LOG_LIST", "admin_log_list.php");
define("PAGE_LOG_DETAIL", "admin_log_detail.php");

define("PAGE_ESTAB_ADMIN", "admin_estab_edit.php");
define("PAGE_USER_ADMIN","admin_user_edit.php");
define("PAGE_ESTAB_PASSWD", "estab_passwd.php");
define("PAGE_ESTAB_CHANGE", "estab_change.php");

define("PAGE_BLOCK_USUARIOS","mantenimiento_web.php");

define("PAGE_ESTAB_SEARCH", "admin_estab_search.php");
define("PAGE_USER_SEARCH",  "admin_user_search.php");

define("PAGE_SELECT_ESTAB_MES_ANO","admin_select_estab_mes_ano.php");
define("PAGE_SELECT_ESTAB_MES_ANO_MULTIPLE","admin_select_estab_mes_ano_multiple.php");
define("PAGE_SELECT_ESTAB_TRIMESTRE", "admin_select_estab_trimestre.php");

define("PAGE_VIEW_SESSION", "admin_php_sess.php");

define("PAGE_COMENTARIOS", "comentarios.php");

define("PAGE_ENLACES_AYUDA","admin_enlaces_ayuda.php");
define("PAGE_AYUDA_AJAX", "ayuda_ajax.php");

define("PAGE_CODIFICACIONES", "codificaciones.php");

/**
 * Mapa del sitio (Mapa de la aplicacion)
 */
$page_titles = array(
		PAGE_HOME 			=> "Inicio",
		PAGE_LOGOUT			=> "Salir",
		PAGE_LOGIN			=> "Iniciar sesi�n",
		PAGE_PASS_RECOVER   => "Procedimiento de recuperaci�n de contrase�a",
		PAGE_PASS_RESET		=> "Procedimiento de recuperaci�n de contrase�a",
		
		PAGE_ALOJA_INDEX	=> "Encuesta de alojamiento tur�stico",
		PAGE_ALOJA_SELECT_PAISES => "Selecci�n de pa�ses y provincias",
		PAGE_ALOJA_FORM 	=> "Encuesta",
		PAGE_ALOJA_FORM_ENVIO => "Recogida de encuesta de alojamiento",
		PAGE_ALOJA_MES_SELECT => "Selecci�n de mes y a�o",
		PAGE_ALOJA_XML      => "Archivo de datos Xml",
		PAGE_ALOJA_CONTROL => "Operaciones sobre encuestas de alojamiento",
		PAGE_ALOJA_CONTROL_MULTIPLE => "Operaciones sobre m�ltiples encuestas de alojamiento",
		PAGE_ALOJA_PLAZOS   => "Configuraci�n de plazos de encuesta de alojamiento",
		PAGE_ALOJA_PLAZOS_EXCEP => "Configuraci�n de plazos excepcionales de encuesta de alojamiento",
		PAGE_ALOJA_ACUSE 	=> "Acuse de recibo de recepci�n encuesta de alojamiento",
		PAGE_ALOJA_RESULTADOS => "Resultados comparativos de encuesta de alojamiento",
		PAGE_ALOJA_ANTERIORES => "Encuestas presentadas",
		PAGE_ALOJA_NULA      => "Solicitud de env�o de cuestionario vac�o",
		
		PAGE_EXP_INDEX		=> "Encuesta de expectativas hoteleras",
		PAGE_EXP_FORMULARIO => "Encuesta",
        PAGE_EXP_PLAZOS     => "Configuraci�n de plazos de encuesta de expectativas",
        PAGE_EXP_ADMIN_CUESTIONARIOS => "Administraci�n de cuestionarios",
        PAGE_EXP_TRIMESTRE_SELECT => "Selecci�n de trimestre",
        PAGE_EXP_ANTERIORES => "Encuestas presentadas",
		PAGE_AVISOS			=> "Gesti�n de avisos",
		PAGE_AVISOS_LIST	=> "Informaci�n de inter�s",
		PAGE_NOTICIAS		=> "Gesti�n de canales de noticias",
    
		PAGE_EMPLEO_INDEX	=> "M�dulo de empleo",
		PAGE_EMPLEO		=> "Gesti�n m�dulo de empleo",
        PAGE_EMPLEO_FORM 	=> "M�dulo de empleo",
		PAGE_EMPLEO_FORM_ENVIO => "Recogida de datos de empleo",
        PAGE_EMPLEO_ACUSE 	=> "Acuse de recibo de recepci�n datos de empleo",
    
		PAGE_CONSUMO_INDEX	=> "Consumos",
        PAGE_CONSUMO_FORM   => 'Entrada de facturas',
        PAGE_CONSUMO_LIST   => 'Listado de facturas',
    
        PAGE_LOG_CONFIG		=> "Administraci�n de Log",
		PAGE_LOG_SEARCH		=> "Consulta del registro",
		PAGE_LOG_LIST		=> "Resultados de la b�squeda del registro",
		PAGE_ESTAB_ADMIN    => "Administraci�n de establecimientos",
		PAGE_BLOCK_USUARIOS => "Mantenimiento web",
		PAGE_USER_ADMIN     => "Administraci�n de usuarios",
		PAGE_ESTAB_PASSWD   => "Cambio de contrase�a",
		PAGE_ESTAB_SEARCH   => "Buscar establecimientos",
		PAGE_SELECT_ESTAB_MES_ANO   => "Seleccionar establecimiento, mes y a�o",
		PAGE_SELECT_ESTAB_MES_ANO_MULTIPLE   => "Seleccionar m�ltiples establecimientos, mes y a�o",
		PAGE_SELECT_ESTAB_TRIMESTRE   => "Seleccionar establecimiento, trimestre y a�o",
		PAGE_ESTAB_CHANGE	=> "Datos del establecimiento",
		PAGE_USER_SEARCH	=> "Buscar usuario",
		PAGE_COMENTARIOS    => "Comentarios",
		PAGE_VIEW_SESSION   => "Sesion",
		PAGE_ENLACES_AYUDA  => "Gesti�n de enlaces de ayuda",
		PAGE_CODIFICACIONES => "Informaci�n de env�o de fichero de datos");
			
?>