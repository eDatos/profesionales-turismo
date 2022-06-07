<?PHP
$a=array("onmouseover","alert","src","script");
$_POST = str_ireplace($a,"",$_POST);
$_GET = str_ireplace($a,"",$_GET);

$ifilter = new InputFilter();
$_POST = $ifilter->process($_POST);
$_GET = $ifilter->process($_GET);
?>