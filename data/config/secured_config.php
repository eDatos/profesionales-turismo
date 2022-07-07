<?php
/** 
 * Archivo de configuracion con valores sensibles dede el punto de vista de la seguridad.
 */

/**
 * Configuracion de la base de datos 
 */

define ( 'DB_HOST' , '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=nombre_host)(PORT=numero_puerto))(CONNECT_DATA=(SID=sid)))');
define ( 'DB_USER' , 'user_name');
define ( 'DB_PASSWORD' , 'pwd');
define ( 'DB_CHARACTER_SET' , 'WE8ISO8859P15');

/**
 *	Permisos de usuario 
 */
define("PERM_USER", "user");
define("PERM_GRABADOR", "grabador");
define("PERM_RECEPCION", "recepcion");
define("PERM_ADMIN_ISTAC", "admin_istac");
define("PERM_ADMIN", "admin");
define('PERM_CONSUMOS', 'consumos');

define("PERMS_ANY", PERM_USER.",".PERM_GRABADOR.",".PERM_RECEPCION.",".PERM_ADMIN_ISTAC.",".PERM_ADMIN.",".PERM_CONSUMOS );

/**
 * Operaciones que estan controladas por permisos
 */
define("OP_CHANGE_ESTABLECIMIENTO", 1);
define("OP_VIEW_ADMIN_ERRORS", 2);
define("OP_ALOJAMIENTO", 3);
define("OP_EXPECTATIVAS", 4);
define("OP_ADMIN_ESTAB", 5);
define("OP_VIEW_ESTAB_DATA", 6);
define("OP_CHANGE_PASSWORD", 7);
define("OP_SELECT_TRIMESTRE", 8);
define("OP_SELECT_MES_ANO", 9);
//define("OP_EMPLEO_Y_SUMINISTROS", 10);
define("OP_EMPLEO", 10);
define("OP_SUMINISTROS", 11);

/**
 *	Operaciones que pueden realizarse segun los perfiles de usuario.
 */
$_CONFIGPWET['conf_seguridad'] = array(
OP_CHANGE_ESTABLECIMIENTO     => array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR ),
        OP_SELECT_TRIMESTRE           => array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR ),
        OP_SELECT_MES_ANO          => array(PERM_ADMIN, PERM_ADMIN_ISTAC, ),
OP_VIEW_ADMIN_ERRORS      => array(PERM_ADMIN, PERM_ADMIN_ISTAC ),
OP_ALOJAMIENTO      => array(PERM_ADMIN, PERM_ADMIN_ISTAC,    PERM_USER, PERM_RECEPCION),
OP_EXPECTATIVAS      => array(PERM_ADMIN, PERM_ADMIN_ISTAC, PERM_GRABADOR, PERM_USER,   ),
OP_ADMIN_ESTAB      => array(PERM_ADMIN, PERM_ADMIN_ISTAC ),
OP_VIEW_ESTAB_DATA      => array(PERM_ADMIN, PERM_ADMIN_ISTAC,    PERM_USER, PERM_RECEPCION, PERM_CONSUMOS),
OP_CHANGE_PASSWORD        => array(PERM_ADMIN, PERM_ADMIN_ISTAC,    PERM_USER, PERM_RECEPCION, PERM_CONSUMOS),
//        OP_EMPLEO_Y_SUMINISTROS  => array(PERM_ADMIN, PERM_ADMIN_ISTAC,    PERM_USER                ),
        OP_EMPLEO                  => array(PERM_ADMIN, PERM_ADMIN_ISTAC,    PERM_USER                ),
        OP_SUMINISTROS                => array(PERM_ADMIN, PERM_ADMIN_ISTAC,    PERM_USER, PERM_CONSUMOS )
);
/**
 * Operaciones que estan controladas por permisos
 */
define("LOG_SQL_ENABLED", false);           // Activa o desactiva el registro de operaciones con la BDD.

?>