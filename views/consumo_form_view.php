<link rel="stylesheet" href="css/pwet-theme/jquery-ui-1.9.1.custom.css"/>
<link rel="stylesheet" href="css/aloja_form_view.css"/>
<script type="text/javascript" src="js/jquery-ui-1.9.1.custom.js"></script>
<script type="text/javascript" src="js/lib/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/lib/messages_es.js"></script>
<script type="text/javascript" src="js/inputfields.js"></script>
<script type="text/javascript">
</script>
<style>
.tablaresultado th,td {
    text-align: center !important;
}
.tablaresultado td:nth-child(2) {
    text-align: left !important;
}
/*.tablaresultado td:nth-child(2) th:nth-child(2) {
    text-align: left;
}*/

.formularioParcial {
     clear: both;
     padding: 5px;
     /*border-color:<?= $ColorBordeFormParcial ?>;
     border-style: solid;*/
}
.formularioParcial label {
    /*font-family: Georgia, "Times New Roman", Times, serif;
    font-size: 18px;*/
    font-weight: bold;
    color: #333;
    height: 20px;
    width: 200px;
    margin-top: 10px;
    margin-left: 10px;
    text-align: right;
    clear: both;
    float:left;
    margin-right:15px;
}
.formularioParcial span {
    /*font-family: Georgia, "Times New Roman", Times, serif;
    font-size: 18px;*/
    font-weight: bold;
    color: #333;
    height: 20px;
    width: 200px;
    margin-top: 10px;
    margin-left: 10px;
    text-align: right;
    clear: both;
    float:left;
    margin-right:15px;
}
.formularioParcial fieldset input {
    height: 20px;
    width: 180px;
    /*border: 1px solid #000;*/
    margin-top: 10px;
    float: left;
}
.formularioParcial select {
    height: 20px;
    width: 300px;
    /*border: 1px solid #000;*/
    margin-top: 10px;
    margin-right: 10px;
    float: left;
}
.titulo_seccion {
    background-color:lightblue;
    padding: 2px;
}
.botonSiguiente {
    float: right;
    margin-top: 10px;
    margin-right: 10px;
    width: 80px;
}
.botonAnterior {
    float: left;
    margin-top: 10px;
    margin-left: 10px;
    width: 80px;
}
.iconoSuministro {
    display: inline-block;
    width: 24px;
    height: 24px;
    float: right;
    margin-right: 10px;
}
</style>
<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<?php include $subview; ?>
</div>
<!-- FIN BLOQUE INTERIOR -->
