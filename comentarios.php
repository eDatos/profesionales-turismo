<?php
require_once(__DIR__."/config.php");
require_once(__DIR__."/lib/RowDbIterator.class.php");
require_once(__DIR__."/classes/PWETPageHelper.class.php");
require_once(__DIR__."/classes/audit/AuditLog.class.php");
require_once(__DIR__."/lib/email.class.php");

$page = PWETPageHelper::start_page(PERMS_ANY, array(PAGE_COMENTARIOS));

define('ARG_COMMENT', 'coment');

if (isset($_POST[ARG_COMMENT]))
{
	$comment = $_POST[ARG_COMMENT];
	
	$estUser = $page->load_current_user_data();
	
	$email = new Email();
	
	$asunto = "PWET:"." (".$estUser->establishment_id.")";
	$comment=iconv("UTF-8", "CP1252", $comment);
	$texto  = "Comentario enviado por el establecimiento ".$estUser->username." (".$estUser->establishment_id."):\r\n".$comment;
	
	$enviado = $email->send($asunto, $texto);
	
	if ($enviado)
	{
?>
	<p class="okicon" style="margin-top:0px;background-position: top left;" >Su comentario ha sido enviado.</p>
	<p style="margin-top:10px;"><em>Muchas gracias por su colaboración.</em></p>
<?php 	
	}
	else
	{
?>
	<p class="erroricon" style="background-position: top left;">Debido a un error interno no ha sido posible envíar su comentario.</p><p>Inténtelo más tarde y si persiste el error por favor comuníquelo al ISTAC.</p>
	<p style="margin-top:10px;"><em>Muchas gracias por su colaboración.</em></p>
<?php 		
	}
}

$page->end_session();

?>