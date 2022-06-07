<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/RowDbIterator.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");

//NOTA: Esta pagina se llama mediante jQuery.ajax.

$page = PWETPageHelper::start_page(array(PERM_ADMIN, PERM_ADMIN_ISTAC));

define('ARG_ACTION_ID', 'id');

$actid = $page->request_post(ARG_ACTION_ID);
if ($actid != null)
{
	$logdata = AuditLog::listEntriesForAction($actid);
	if ($logdata != null && $logdata->has_rows())
	{
		foreach ($logdata as $entry)
		{
			echo "<p>".utf8_encode($entry['mensaje'])."</p>";
		}
	}
	else {
		echo "<p>No hay detalles.</p>";
	}
}

$page->end_session();

?>
