<script src="js/jquery-ui-1.10.3.js"></script>
<link rel="stylesheet" href="css/aloja_select_paises.css"/>
<link rel="stylesheet" href="css/pwet-theme/jquery-ui-1.10.3.css" />
<script type="text/javascript">
    var paises_no_habituales = new Array();
    var paises_agregados = new Array();
	$(document).ready( function() {		
		$("input[type='button']").button();
		$("#button").button();
		$("#subirXmlBtn").button();
		$("input[type='submit']").button();
 		$("#encNulaBtn").button();
       $("#checkEspana").click(function() {

			var tp = $("table.paises");
			var tpe = tp.find("tr.div_espana");
			tpe.toggle();
		
            if (!$("#checkEspana").is(':checked')) 
            {
            	tpe.find("input[type='checkbox']").removeAttr('checked');
            }
            else 
            {
            	tp.find(".div_espana.tiene-pres").find("input[type='checkbox']").attr("checked", true).each(function(index, obj) {
                	prefijo_hijos = obj.id.substring(0,obj.id.indexOf('ut'));
             		nombre_grupo = prefijo_hijos.replace('grp','grupo');
             		nombre_grupo = nombre_grupo.substring(0,nombre_grupo.length-1);
                    $("#" + nombre_grupo).attr("checked", $("input[id^='" + prefijo_hijos + "']").length == $("input[id^='" + prefijo_hijos + "']:checked").length);                	
            	});
            }
        });

        var newFila=function(id,etiqueta)
        {
        	return $("<tr class='' >"+
        	"<td class='pais_cab ' >"+
        	"<input type='checkbox' name='ut[]' class='ut' id='grp_nh_ut_3' checked value='" + id + "' >" + etiqueta+ "</td>"+
        	"<td class='col-um ' ></td>"+
        	"<td class=' '  >&nbsp;</td>"+
        	"<td class='' >&nbsp;</td>"+
        	"<td class='' >&nbsp;</td>"+
        	"<td class='' >&nbsp;</td>"+
        	"<td class='' >&nbsp;</td></tr>");
        };
        
        var insertarFila=function(id,etiqueta) {
            var insertado=false;
        	$(".paises").find('tr#fila_no_habituales').nextAll('tr').each(
        		function() {
        			//contador++;
        			if($(this).find('td.pais_cab').text().trim()>=etiqueta)
        			{
        				$(this).before(newFila(id,etiqueta));
        				insertado=true;
        				return false;
        			}
        		}
        	);
        	if(insertado==false)
        		$(".paises tr:last").after(newFila(id,etiqueta));
        };

		$("#agregar").click(function(e) {
			e.preventDefault();
			var selected = $("#no_habituales").val();
			var encontrado = -1;
			for(var i=0;i<paises_no_habituales.length;i++)
			{
				if(selected==paises_no_habituales[i].label)
				{
					encontrado=i;
					break;
				}
			}
			if(encontrado==-1)
			{
				alert("La unidad territorial introducida no se encuentra en la lista de selección.");
			}
			else
			{
				agregar_ut=true;
				for(i=0;i<paises_agregados.length;i++)
				{
					if(paises_agregados[i]==paises_no_habituales[encontrado].id) 
					{
						agregar_ut=false;
						break;
					}
				}
				if(agregar_ut) 
				{
				paises_agregados.push(paises_no_habituales[encontrado].id);
				insertarFila(paises_no_habituales[encontrado].id,paises_no_habituales[encontrado].label);
				$("#no_habituales").val("");
				}
				else
				{
					alert("La unidad territorial introducida ya existe en la selección de paises no habituales.");					
				}
			}
		});
        //De/seleccionar todas las unidades territoriales de un grupo
        $(".grupo").change(function() {
            $("input[id^='" + this.id.replace('grupo', 'grp') + "_']").attr("checked",this.checked);
        });

        // si todos las unidades territoriales de un grupo están seleccionadas
        // se marca el grupo y viceversa
        $(".ut").change(function(){
     		prefijo_hijos = this.id.substring(0,this.id.indexOf('ut'));
     		nombre_grupo = prefijo_hijos.replace('grp','grupo');
     		nombre_grupo = nombre_grupo.substring(0,nombre_grupo.length-1);
            $("#" + nombre_grupo).attr("checked", $("input[id^='" + prefijo_hijos + "']").length == $("input[id^='" + prefijo_hijos + "']:checked").length);    
        }); 

        //Se marcan las cabeceras de grupo si tienen todos sus hijos seleccionados
        var grupos = $(".grupo");
        for(var i=0;i<grupos.length;i++)
        {
	 		nombre_grupo = grupos[i].id;
	 		prefijo_hijos = nombre_grupo.replace('grupo', 'grp') + "_";
	        $("#" + nombre_grupo).attr("checked", $("input[id^='" + prefijo_hijos + "']").length == $("input[id^='" + prefijo_hijos + "']:checked").length);
        }    

        var tp = $("table.paises");
        //Se comprueba si alguna unidad territorial de las comunidades autónomas está marcada para dejar abierto el div de las comunidades autónomas
        if (tp.find("tr.div_espana .ut:checked").length!=0)
        { 
            $("#checkEspana").attr('checked', true);
            tp.find("tr.div_espana").show();
        }   
        else
        {
        	$("#checkEspana").attr('checked', false);
        	tp.find("tr.div_espana").hide();
        }        
	});
</script>

<!-- COMIENZO BLOQUE INTERIOR -->
<div id="bloq_interior">
	<h2 class="titulo_2">Encuesta de Alojamiento Turístico en Establecimientos <?=($es_hotel ? "Hoteleros" : "Extrahoteleros" )?>: <?= DateHelper::mes_tostring( $mes_encuesta,'M') ?> de <?= $ano_encuesta ?></h2>
	<div class="subrayado"></div>
	<?php if ($esAdmin) : ?>
		<div style="margin-top:15px; margin-bottom:15px;">Seleccione las nacionalidades y provincias a las que pertenecen los huéspedes de su establecimiento para introducir los datos de entradas, salidas y pernoctaciones de <?= DateHelper::mes_tostring( $mes_encuesta,'M') ?> de <?= $ano_encuesta ?>.</div>
	<?php else: ?>
		Para responder a la encuesta puede subir un fichero de datos o bien introducir los datos manualmente.<br/><br/>
		<ul>
		<li class="opcion" >
			<h2 class="cabecera_1er_nivel">Opción <strong>A. Subir fichero</strong> <span id="ayuda_xml" onclick="MostrarAyuda('ayuda_xml','AYUDA09<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon" title="Ayuda (tecla de acceso: l)" accesskey="l" >&nbsp;</span></h2>
			<div class="tabbed">
				La extensión del fichero debe ser xml. Este proceso puede tardar varios minutos dependiendo del tamaño del fichero y el tipo de conexión a Internet utilizada.<br/>
				<a id="subirXmlBtn" href="<?= @$site[PAGE_ALOJA_XML] ?>" style="width:190px;">Subir fichero de datos</a>
				<!--  <a href="comofunciona" class="enlace" style="margin-left:10px;">¿Cómo funciona?</a> -->
			</div>
		</li>
		<li class="opcion">
			<h2 class="cabecera_1er_nivel">Opción <strong>B. Introducir los datos manualmente</strong></h2>
			<div class="tabbed">Seleccione las nacionalidades y provincias a las que pertenecen los huéspedes de su establecimiento para introducir los datos de entradas, salidas y pernoctaciones de <?= DateHelper::mes_tostring( $mes_encuesta,'M') ?> de <?= $ano_encuesta ?>.</div>
		</li>
		</ul>
	<?php endif; ?>
	<?php $div_espana_definido=FALSE; $div_no_habituales_definido=FALSE; $id_grupo=0; $puede_marcarse=FALSE; $no_habituales = array();?>
        <div class="tabbed titulo_3">Países <span id="ayuda_paises" onclick="MostrarAyuda('ayuda_paises','AYUDA10<?= $es_hotel ? "_HOT" : "_APT" ?>');" class="ayudaicon" title="Ayuda (tecla de acceso: p)" accesskey="p" >&nbsp;</span></div>
		<form action="<?= $alojaformUrl; ?>" name="seleccion_paises" method="post">
		<div style="margin-left: 20px;">
			<input class="search" type="submit" value="Rellenar cuestionario" style="margin: 10px 0px 10px 0px;"/>
		</div>			
                <table class="paises">
			       	<tr>
		        		<th></th>
		        		<th>Último movimiento</th>
		        		<th>Presentes fin mes anterior</th>
		        		<th>Presentes comienzo mes</th>
		        		<th>Entradas</th>
		        		<th>Salidas</th>
		        		<th>Presentes fin mes</th>
		        	</tr>                
	        	<?php foreach ($grupo_lista_paises as $grupo => $lista_paises) : ?>
                <?php   $es_nacional = substr($grupo,0,1);
                        $nombre_grupo = substr($grupo,1);
                        $num_pais = 0;

                        if(($es_nacional=='1' || $es_nacional=='3') && (!$div_espana_definido)) 
                        {
                        	$div_espana_definido=TRUE;
                ?>
		        	<tr><td colspan="7" class="fila_vacia"/></tr>                         	
		        	<tr>
			        	<td class="grupo_pais">
			        		<input type="checkbox" id='checkEspana' checked>España</td>
			        	<td class="grupo_pais" colspan="6" ></td>
		        	</tr>                       	
				<?php 
                        }
                        if(($es_nacional=='2') && (!$div_no_habituales_definido))
                        {
                        	$div_no_habituales_definido=TRUE;
                        	?>
                        <tr><td colspan="7" class="fila_vacia"/></tr>
                        <tr id="fila_no_habituales">
                        <td class="grupo_pais">Resto de países</td>
                        <td class="grupo_pais" colspan="6" style="text-align:left;"><input id="no_habituales" style="width: 300px;margin-top:5px;"/><input class="search" id="agregar" type="submit" value="Agregar" style="margin: -3px 0px 0px 10px;"/></td>
                        </tr>
				<?php 
                        }
                        if($es_nacional=='0')
                        {
                       		?>
		        	<tr>
          	                <?php
                        	                        	
                        }
                        if($es_nacional=='1' || $es_nacional=='3')
                        {
                        	?>                        	
                    <tr class='div_espana'>
          	                <?php                        	                        
                        }
                        if($es_nacional!='2')                       
                        {
		        ?>	        	
		        	<td class="grupo_pais">
		        		<input type="checkbox" name="grupo[]" class='grupo' id='grupo_<?= ++$id_grupo ?>'><?= $nombre_grupo; ?>
		        	</td>
		        	<td class="grupo_pais" colspan="6" ></td>
	        	</tr>
		        <?php
                        } 
		        foreach($lista_paises as $id_UT => $reg_pais) {
		        	//En caso de que no hayan ya unidades territoriales marcadas, se escogen las que tengan último movimiento o presentes a fin de mes anterior
		        	$puede_marcarse = $reg_pais->ultimo_movimiento!='' || (!$soloLectura && ($reg_pais->presentes_fin_mes_anterior!='' && $reg_pais->presentes_fin_mes_anterior!=0));
		        	$marcar_rojo = (empty($reg_pais->entradas) && empty($reg_pais->salidas) && ($reg_pais->presentes_fin_mes_anterior!='' && $reg_pais->presentes_fin_mes_anterior!=0));
			        if(!empty($reg_pais->presentes_fin_mes_anterior))
			        {
			        	$reg_pais->presentes_fin_mes_anterior = $reg_pais->presentes_fin_mes_anterior==0 ? "&nbsp;" : $reg_pais->presentes_fin_mes_anterior;
			        }
			        else
			        {
			        	$reg_pais->presentes_fin_mes_anterior = "&nbsp;";
			        }
			        if($reg_pais->ultimo_movimiento == -1)
			        {
			        	$reg_pais->ultimo_movimiento = "&nbsp;";
			        	$reg_pais->presentes_comienzo_mes = $reg_pais->presentes_comienzo_mes == 0 ? "" : $reg_pais->presentes_comienzo_mes;
			        	$reg_pais->presentes_fin_mes = "&nbsp;";
			        	$reg_pais->entradas = "&nbsp;";
			        	$reg_pais->salidas = "&nbsp;";
			        }
			        $num_pais++;
			        if($es_nacional=='0')
			        {
			        	?>
			        	<tr class='<?= $marcar_rojo ? "tiene-pres": ""?>' >
			        	<?php
		        	}
		        	if($es_nacional=='1' || $es_nacional=='3')
		        	{
		        		?>
		        			<tr class='div_espana <?= $marcar_rojo ? "tiene-pres": ""?>' >
		        		<?php
		        	}
		        	if($es_nacional!='2' || $reg_pais->mov_meses_anteriores || ((count($selected_UT)!=0) && in_array($id_UT, $selected_UT)) || ((count($selected_UT)==0) && $puede_marcarse))
		        	{
		        		?>
	        		<td class='pais_cab <?= ($num_pais == 1) ? "primer_pais" : ""; ?>' >
	        			<input type="checkbox" name="ut[]" class='ut' id='grp_<?= $id_grupo . '_ut_' . $id_UT ?>' value="<?= $id_UT ?>" <?= ((count($selected_UT)!=0) && in_array($id_UT, $selected_UT)) || ((count($selected_UT)==0) && $puede_marcarse) ? "checked" :"" ?>><?= mb_convert_case(mb_strtolower($reg_pais->nombre, 'ISO-8859-1'),MB_CASE_TITLE) ?>
	        		</td>
	        		<td class='col-um <?= ($num_pais == 1) ? "primer_pais" : ""; ?>' >
	        			<?= $reg_pais->ultimo_movimiento ?>
	        		</td>
	        		<td class='<?= ($num_pais == 1) ? "primer_pais" : ""; ?> <?= $marcar_rojo ? "marca-rojo" : ""; ?>' <?= $marcar_rojo ? "title='Debería marcar " . mb_convert_case(mb_strtolower($reg_pais->nombre, 'ISO-8859-1'),MB_CASE_TITLE) . " por tener presentes a fin de mes anterior.'" : ""; ?> >
	        			<?= $reg_pais->presentes_fin_mes_anterior ?>
	        		</td>
	        		<td class='<?= ($num_pais == 1) ? "primer_pais" : ""; ?>' >
	        			<?= $reg_pais->presentes_comienzo_mes ?>
	        		</td>
	        		<td class='<?= ($num_pais == 1) ? "primer_pais" : ""; ?>' >
	        			<?= $reg_pais->entradas ?>
	        		</td>
	        		<td class='<?= ($num_pais == 1) ? "primer_pais" : ""; ?>' >
	        			<?= $reg_pais->salidas ?>
	        		</td>
	        		<td class='<?= ($num_pais == 1) ? "primer_pais" : ""; ?>' >
	        			<?= $reg_pais->presentes_fin_mes ?>
	        		</td>
	        	</tr>
	        	<?php
		        	}
		        	if($es_nacional=='2')
		        	{
		        		$no_habituales[$id_UT]=array('pais'=>mb_convert_case(mb_strtolower($reg_pais->nombre, 'ISO-8859-1'),MB_CASE_TITLE),'grupo'=>$nombre_grupo);
		        	}
		        }?> 
	        	<?php endforeach;?>
    <style>
  .ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
  }
  .ui-autocomplete {
  	width: 300px;
    max-height: 130px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
    height: 130px;
  }
  </style>
  <script>
  var accentMap = { "á": "a", "é": "e", "í": "i", "ó": "o", "ú": "u" };
  var normalize = function( term ) 
    {
      var ret = "";
      term = term.toLowerCase();
      for ( var i = 0; i < term.length; i++ ) 
      {
        ret += accentMap[ term.charAt(i) ] || term.charAt(i);
      }
      return ret;
    };  
  $.widget( "custom.catcomplete", $.ui.autocomplete, { 
    _renderMenu: function( ul, items ) 
	    {
	      var that = this, currentCategory = "";
	      $.each( items, function( index, item ) 
	    	{
	        	if ( item.category != currentCategory ) 
		        {
	          		ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
	          		currentCategory = item.category;
	        	}
	        	that._renderItemData( ul, item );
	    	});
    	}
  });
  </script>	        
	        <script>  
	        $(function() {
<?php
    foreach($no_habituales as $id_ut => $pais)
    {
		print "paises_no_habituales.push({label:'" .$pais["pais"] . "',category:'" . $pais["grupo"] ."',id:" . $id_ut . "});";
    }
?>		                 	          
	          $( "#no_habituales" ).catcomplete({
	              delay: 0,
	              source: function( request, response ) 
	        		{
	  				var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
	            		response( $.grep( paises_no_habituales, function( value ) {
	              		value = value.label || value.value || value;
	              		return matcher.test( value ) || matcher.test( normalize( value ) );
	              	})); 
	       		  }
	            });
	        });		                 	          
	        </script>	        
	        </table>
            <input type="hidden" name="mes_encuesta" value="<?= $mes_encuesta ?>"/>
	        <input type="hidden" name="ano_encuesta" value="<?= $ano_encuesta ?>"/>
	        <input type="hidden" name="desde_sel_ut" value="1"/>
            <div style="margin-left: 20px;">
	        	<input class="search" type="submit" value="Rellenar cuestionario" style="margin: 10px 0px 10px 0px;"/>
	        </div>
	        <br/>
        </form>
        <form action="<?= @$site[PAGE_ALOJA_NULA]; ?>" name="cuestionario_nulo" method="post">
            <input type="hidden" name="<?= ARG_OP ?>" value="<?= OP_DUMMY ?>"/>
            <input type="hidden" name="<?= ARG_MES_ENCUESTA ?>" value="<?= $mes_encuesta ?>"/>
	        <input type="hidden" name="<?= ARG_ANO_ENCUESTA ?>" value="<?= $ano_encuesta ?>"/>
            <div style="margin-left: 20px;">
	        	<input class="search" type="submit" value="Solicitar envío de cuestionario vacío" style="margin: 10px 0px 10px 0px;"/>
	        </div>
        </form>
	        <br/><br/>
	        <div style="position:relative;top:-8px;"><?php if ($esAdmin) : ?><a href="index.php" class="enlace volvericon">Volver</a><?php else: ?><a href="javascript:history.back()" class="enlace volvericon">Volver</a><?php endif; ?></div>
</div>
<!-- FIN BLOQUE INTERIOR -->