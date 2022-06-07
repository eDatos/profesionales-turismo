<?php 
function config_banner()
{
	global $banners_cfg;
	$c = array();	
	foreach($banners_cfg as $img => $link)
	{
		$c[] = array('img' => BANNER_LOCATION . $img, 'link' => $link);
	}
	return $c;
}
?>
<link href="css/banner.css" rel="stylesheet" type="text/css"> 
<script type="text/javascript" src="js/banner.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		var banners = JSON.parse(<?= "'" . @json_encode(config_banner()) . "'"; ?>);
		ba.init(banners, $("#banner_ph"), "slow", <?= defined("BANNER_SPEED") ? BANNER_SPEED : 5000; ?>);
	});
</script>
<div id="banner">
	<div id="banner_ph"></div>
	<div id="banner_sels"></div>
</div>