<?php

class Log
{
	public static function error($msg)
	{
		error_log('[PWET ERROR] '.$msg);
	}
	public static function warning($msg)
	{
		error_log('[PWET WARNING] '.$msg);
	}
	public static function notice($msg)
	{
		error_log('[PWET NOTICE] '.$msg);
	}
	public static function trace($msg, $display=FALSE)
	{
		error_log('[PWET TRACE] '.$msg);
	}
	
	public static function display_error($msg)
	{
		trigger_error('[PWET ERROR] '.$msg, E_USER_ERROR);
	}
	public static function display_warning($msg)
	{
		trigger_error('[PWET WARNING] '.$msg, E_USER_WARNING);
	}
	public static function display_notice($msg)
	{
		trigger_error('[PWET NOTICE] '.$msg, E_USER_NOTICE);
	}
	public static function display_trace($msg)
	{
		trigger_error('[PWET TRACE] '.$msg, E_USER_NOTICE);
	}	
}

?>