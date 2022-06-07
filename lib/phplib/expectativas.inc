<?php
function quita_comentarios($str)//Quita los comentarios del fichero de entrada 
{
			$pos=strpos($str,"#");
			if($pos>0){	$str=substr($str,0,$pos);}
			return $str;
}
function cambia($str)//Elimina caracteres que sobran y posibles comentarios
{
	$n=0;
/*	$txt=ereg_replace("(\n)*", "",$str);
	$txt=ereg_replace("(\r)*", "",$txt);
	$txt=ereg_replace("( )*", "",$txt);*/
	$txt=preg_replace("/(\n)*/", "",$str);
	$txt=preg_replace("/(\r)*/", "",$txt);
	$txt=preg_replace("/( )*/", "",$txt);
	$n=strpos($txt,"#");
	if($n>0)$txt=substr($txt,0,$n);
	return $txt;
}

function solo_texto($str)
{
	$l=strlen($str);
	$str2="";
	for($j=0;$j<$l;$j++)
	{
		if((($str[$j]>='A')&&($str[$j]<='Z'))||($str[$j]=='_'))
		{
			$str2.=$str[$j];			
		}
	}
	return $str2;
}

function captura_valores($str,$fd,$bloque,$v_pregunta,$i,$v_bloque,$ib)
{
	$lectura="";
	if($bloque){
		$txt_ini = "BLOQUE";
		$txt_fin = "FIN_BLOQUE";
	}
	else{
		$txt_ini = "PREGUNTA";
		$txt_fin = "FIN_PREGUNTA";
	}
	
	$n=strpos($str,"]")+1;
	$indice=substr($str,0,$n);  //Hemos encontrado el principio de un campo [CAMPO], ahora tenemos que buscar [FIN_CAMPO]
	$str=substr($str,$n);
	if((!strstr($indice,$txt_fin))&&(!strstr($indice,$txt_ini)))
	{
		$indice=solo_texto($indice);
		while((!strstr($str,"FIN_".$indice))&&(!feof($fd)))
		{
			$lectura=fgets($fd,1000);
			$lectura=quita_comentarios($lectura);
			$str.=$lectura;//vamos almacenando todo lo que se lee
		}
		if(feof($fd))
		{
			echo "ERROR: El fichero de entrada de datos está mal formateado. Por favor póngase en contacto con el responsable.";
			exit();
		}
		$nfin=strrpos ($str,"[");
		$long_val=$nfin;
		$valor=substr($str,0,$long_val);
		if($bloque)
			$v_bloque[$ib][$indice]=$valor;
		else
			$v_pregunta[$i][$indice]=$valor;
	}
	if($bloque)
		return $v_bloque;
	else
		return $v_pregunta;
}

function muestra_formulario($tabla_color,$tabla_color_claro,$tabla_border,$form,$tpl,$fechas,$fichero_formulario,$v_pregunta,$v_bloque,$modificar,$db,$id_hotel,$trimestre_referencia,$anyo_referencia,$grabador){
//CREAR EL FORMULARIO
	$preguntas="";
		// Las siguientes variables se mantienen constantes para todo el formulario
	$textos_valores_estilo="istac2";
	$textos_opciones_estilo="istac2";
	$width_opciones="30%"; // si se deja en blanco la celda se acomoda al ancho del texto.
	$width_valores="100";  // si se deja en blanco la celda se acomoda al ancho del texto.
	$formulario=new formulario();
	$preseleccionados=array();
	
// Estas variables cambian para cada pregunta.
/*	$tipo
	$titulo
	$comentario
	$n_valores
	$valores
	$textos_valores
	$n_opciones
	$textos_opciones
	$obligatorias
*/
	$cont_radios=0;	//Variable que cuenta los radio_buttons que se han creado
	$fd=fopen($fichero_formulario, "r");
	if (!is_resource($fd))
	{
		$form=NO_FORMULARIO."'expectativas.form'";//ERROR, no se encuentra el fichero de parametros 
	}
	else 
	{
	$i=0;
	$ib=0;
	$n_sub_preg=0;
	$num_preg=0;
		while (!feof($fd)) 
		{
			$linea="";
			$pregunta="";
			$linea=fgets($fd,1000); 
			$linea=quita_comentarios($linea);//Quita los comentarios de la linea recogida
			$linea=cambia($linea);//Elimina caracteres como /n /r

			//En este bucle se trata una pregunta en cada pasada
			while(((strcmp($linea,"[PREGUNTA]")!=0)&&(strcmp($linea,"[BLOQUE]")!=0))&&(!feof($fd)))//Buscamos el inicio de una pregunta
			{
				$linea="";
				$linea=fgets($fd,1000);
				$linea=quita_comentarios($linea);
				$linea=cambia($linea);				
			}		

			if(strcmp($linea,"[BLOQUE]")==0)$bloque=1;
			else $bloque=0;
			$pregunta.=$linea;
			if(!feof($fd))
			{
				while(((!strstr($linea,"FIN_PREGUNTA"))&&(!strstr($linea,"FIN_BLOQUE")))&&(!feof($fd)))//Buscamos el final de esta pregunta y almacenamos todo en $pregunta
				{
					$linea="";
					$linea=fgets($fd,1000);
					$linea=quita_comentarios($linea);//Quita los comentarios de la linea recogida
					$ret=captura_valores($linea, $fd,$bloque,$v_pregunta,$i,$v_bloque,$ib);//Esta función va rellenando el vector global v_pregunta[] con todos los campos de cada pregunta
					if($bloque)
						$v_bloque=$ret;
					else
						$v_pregunta=$ret;
					$linea=cambia($linea);//Elimina caracteres como /n /r
					$pregunta.=$linea;
				}
				$linea="";
				if($bloque){
					if(isset($v_bloque[$ib]["TITULO"])){$tpl->set_var("TITULO",$v_bloque[$ib]["TITULO"]);}
					else{$tpl->set_var("TITULO","");}
					if(isset($v_bloque[$ib]["IDENTIFICADOR"])){$tpl->set_var("IDENTIFICADOR",$v_bloque[$ib]["IDENTIFICADOR"]);}
					else{$tpl->set_var("IDENTIFICADOR","");}
					if(isset($v_bloque[$ib]["COMENTARIO"])){$tpl->set_var("COMENTARIO",$v_bloque[$ib]["COMENTARIO"]);}
					else{$tpl->set_var("COMENTARIO","");}
				
					$tpl->parse("preg","pregunta_bloque",false);
					$pregunta_act="<tr><td>".$tpl->get("preg")."</td></tr>";
					$preguntas.=$pregunta_act;
					$ib++;
					continue;
				}
				
				if(isset($v_pregunta[$i]["N_CABECERAS"])){
					$n_cabeceras=$v_pregunta[$i]["N_CABECERAS"];//Nº de valores posibles a responder para esta pregunta
				}else{
					$n_cabeceras=1;
				}

				if(isset($v_pregunta[$i]["TEXTOS_CABECERAS"])){
					$textos_cabeceras=explode(',',$v_pregunta[$i]["TEXTOS_CABECERAS"]);//Textos				
				}else{
					$textos_cabeceras="";
				}
				
				$n_valores=$v_pregunta[$i]["N_VALORES"];//Nº de valores posibles a responder para esta pregunta
				$valores=explode(',',$v_pregunta[$i]["VALORES"]);//Valores que se almacenarán en la base de datos
				$textos_valores=explode(',',$v_pregunta[$i]["TEXTOS_VALORES"]);//Textos
				$nombre_oracle=explode(',',$v_pregunta[$i]["NOMBRE_ORACLE"]);//Nombres de campos de Oracle
				if(isset($v_pregunta[$i]["POR_DEFECTO_VALOR"]))
					$valor_por_defecto=$v_pregunta[$i]["POR_DEFECTO_VALOR"];//Nº de valores posibles a responder para esta pregunta
				else
					$valor_por_defecto="";//Nº de valores posibles a responder para esta pregunta
				if(isset($v_pregunta[$i]["PRESELECCIONADOS"]))//Marca los preseleccionados. Esta función no está muy afinada, así que mejor no marcar preseleccionados
					$prec_actual=explode(',',$v_pregunta[$i]["PRESELECCIONADOS"]);
				$max=sizeof($prec_actual);
				$pregunta_preselec=0;
				for($m=0;$m<$max;$m++)
				{
					$preseleccionados[$pregunta_preselec]=$prec_actual[$m];    //aquí se ponen los valores a los que queremos inicializar los radio butons, si lo dejamos en blanco aparecen sin inicializar.				
					$pregunta_preselec++;
				}

				$n_opciones=$v_pregunta[$i]["N_OPCIONES"];//Nº de subpreguntas de la pregunta actual

				if($modificar>0)
				{
					for($y=0;$y<$n_cabeceras;$y++){					
						for($z=$y*$n_opciones;$z<$n_opciones*($y+1);$z++){					
							$nombre_campo=strtolower(trim($nombre_oracle[$z]));
							$sql="select $nombre_campo FROM tb_expectativas";
							$sql.=" WHERE id_establecimiento='".$id_hotel."' AND TRIMESTRE='".$trimestre_referencia."' AND anyo='".$anyo_referencia."'";
							$db->query($sql);
							$db->next_record($sql);							
							$preseleccionados[$z]=$db->f("$nombre_campo");
						}
					}
				}

				$temp_obligatorias[$i]=$v_pregunta[$i]["OBLIGATORIAS"];//Subpreguntas obligatorias
				
				$max=sizeof($temp_obligatorias[$i]);  //Voy a calcular si una sub-pregunta es obligatoria o no
				$v_preg_act=explode(',',$temp_obligatorias[$i]);
				$k=0;
				$sub_obligatorias=array();
				for($h=0;$h<$n_opciones;$h++)//inicializo todo el vector a 0 == no obligatorias
				{
					$sub_obligatorias[$h]=0;
				}
				
				$max=sizeof($v_preg_act);
				//El vector $v_preg_act contiene el vector de sub_preguntas obligatorias ta cual se lee del fichero (1,3,4,5,7)
				//mientras que sub_obligatorias tiene en cada elemento un uno o un cero dependiendo de si la sub_pregunta que ocupa esa posición en la pregunta actual es obligatoria o no
				//$sub_obligatorias[0]=0
				//$sub_obligatorias[1]=1
				//$sub_obligatorias[2]=0
				//$sub_obligatorias[3]=1
				//$sub_obligatorias[4]=1
				//$sub_obligatorias[5]=1
				//$sub_obligatorias[6]=0
				//$sub_obligatorias[7]=1
				//$sub_obligatorias[8]=0
				for($h=0;$h<$max;$h++)
				{
					$sub_obligatorias[($v_preg_act[$h]-1)]=1;
				}
			//Marco las obligatorias. El vector obligatorias tiene tantos elementos
			// como sub_preguntas el formulario completo, mientras	que sub_obligatorias
			// solo contiene el vector de obligatorias de la pregunta actual.

				if($n_cabeceras>0)$num_preg+=$n_opciones*$n_cabeceras;
				else $num_preg+=$n_opciones;
				for($h=0;$h<$n_opciones;$h++) //vamos a ver que sub_preguntas, según el indice del formulario son obligatorios
				{
						$obligatorias[$n_sub_preg]=$sub_obligatorias[$h];
						$n_sub_preg++;
				}
				$max=sizeof($obligatorias);
				$textos_opciones=explode(',',$v_pregunta[$i]["TEXTOS_OPCIONES"]);
				//Dependiendo del tipo se llama a uno de los metodos de formulario
				
				if($modificar==2)$disable=1;
				else $disable=0;

				if(!$grabador){
					switch($v_pregunta[$i]["TIPO"])
					{
					case 1:
					case 3:					
						$res=$formulario->crea_radio_button($valor_por_defecto,$nombre_oracle,$n_valores,$valores,$textos_valores,$preseleccionados,$n_opciones,$textos_opciones,$textos_valores_estilo,$textos_opciones_estilo,$tabla_border,$tabla_color,$tabla_color_claro,$width_opciones,$width_valores,$obligatorias,$disable);
						break;	
					case 2:				
						$res=$formulario->crea_input_text($valor_por_defecto,$nombre_oracle,$textos_valores,$n_opciones,$textos_opciones,$textos_opciones_estilo,$tabla_border,$tabla_color,$tabla_color_claro,$width_opciones,$obligatorias,$disable);
						break;
					case 4:
						$res=$formulario->crea_radio_button_sin_texto($valor_por_defecto,$nombre_oracle,$n_valores,$valores,$textos_valores,$preseleccionados,$n_opciones,$textos_opciones,$textos_valores_estilo,$textos_opciones_estilo,$tabla_border,$tabla_color,$tabla_color_claro,$width_opciones,$width_valores,$obligatorias,$disable);
						break;	
					case 5:
						$res=$formulario->crea_radio_button_n_cabeceras($valor_por_defecto,$nombre_oracle,$n_cabeceras,$textos_cabeceras,$n_valores,$valores,$textos_valores,$preseleccionados,$n_opciones,$textos_opciones,$textos_valores_estilo,$textos_opciones_estilo,$tabla_border,$tabla_color,$tabla_color_claro,$width_opciones,$width_valores,$obligatorias,$disable);
						break;	
					case 6:
						$res=$formulario->crea_checkbox($valor_por_defecto,$nombre_oracle,$n_valores,$valores,$textos_valores,$preseleccionados,$n_opciones,$textos_opciones,$textos_valores_estilo,$textos_opciones_estilo,$tabla_border,$tabla_color,$tabla_color_claro,$width_opciones,$width_valores,$obligatorias,$disable);
						break;	
					default://caso de error, tipo no definido
						$volcado="";
						$k=$i;
						{
							foreach($v_pregunta[$k] as $v=>$c)
							{
								$volcado.="<br>[$v]=$c";
							}
						}
						$res="<tr><td class='istac_error'>Error: Pregunta ".($i+1).". Tipo de pregunta no definido o el fichero de entrada de datos está mal formateado<br>".$volcado."</td></tr>";
						break;
					}
				}
				else{
						$res=$formulario->crea_input_text_grabador($i,$valor_por_defecto,$nombre_oracle,$n_cabeceras,$textos_cabeceras,$n_valores,$valores,$textos_valores,$preseleccionados,$n_opciones,$textos_opciones,$textos_valores_estilo,$textos_opciones_estilo,$tabla_border,$tabla_color,$tabla_color_claro,$width_opciones,$width_valores,$obligatorias,$disable,$modificar);
				}
				//Damos el valor del ttulo en el template
				if(isset($v_pregunta[$i]["TITULO"])){$tpl->set_var("TITULO",$v_pregunta[$i]["TITULO"]);}
				else{$tpl->set_var("TITULO","");}
				//Damos el valor del comentario en el template
				if(isset($v_pregunta[$i]["COMENTARIO"])){$tpl->set_var("COMENTARIO",$v_pregunta[$i]["COMENTARIO"]);}
				else{$tpl->set_var("COMENTARIO","");}
				if($grabador){
					if(isset($v_pregunta[$i]["COMENTARIO_GRABADOR"])){$tpl->set_var("COMENTARIO",$v_pregunta[$i]["COMENTARIO_GRABADOR"]);}
					else{$tpl->set_var("COMENTARIO","");}
					if($v_pregunta[$i]["TIPO"]>2)$v_pregunta[$i]["TIPO"]=1;
				}
				$tpl->set_var("TABLA",$res);
				if($v_pregunta[$i]["TIPO"]==1)
				{
					$tpl->parse("preg","pregunta",false);
				}
				if($v_pregunta[$i]["TIPO"]==2)
				{
					//Este tipo de pregunta es sin título 
					//(En expectativas hoteleras se usa en la pregunta 9
					$tpl->parse("preg","pregunta_tipo2",false);
				}
				if($v_pregunta[$i]["TIPO"]==3)
				{
					$tpl->parse("preg","pregunta_sin_comentario",false);
				}
				if($v_pregunta[$i]["TIPO"]==4)
				{
					$tpl->parse("preg","pregunta_sin_comentario",false);
				}
				if($v_pregunta[$i]["TIPO"]==5)
				{
					$tpl->parse("preg","pregunta_sin_comentario",false);
				}
				if($v_pregunta[$i]["TIPO"]==6)
				{
					$tpl->parse("preg","pregunta_sin_comentario",false);
				}
				$i++;
				$pregunta_act="<tr><td>".$tpl->get("preg")."</td></tr>";
				$preguntas.=$pregunta_act;
			}
		}

		$tpl->set_var("CORREO",DIRECCION_MAIL);
		$tpl->parse("preg","pregunta_ayuda",false);
		$pregunta_act="<tr><td>".$tpl->get("preg")."</td></tr>";
		$preguntas.=$pregunta_act;
		
		$form_html="";//monto la pagina central
		$form_html.="<input type='hidden' name='id_hotel' value='".$id_hotel."'>";
		$form_html.="<input type='hidden' name='num_preguntas' value='".$num_preg."'>";
		
		$form_js="
			<script language='JavaScript'>
			function Validate (TheForm){";
			
		if(!$grabador){
			$form_js.="
					// Comprobación de persona que graba los datos
					if((!document.form_datos.pregunta00[0].checked)&&(!document.form_datos.pregunta00[1].checked))
					{
						msg = \"Debe indicar quien contesta esta encuesta. En la pregunta 0\";
						document.form_datos.pregunta00[0].focus();
						alert( msg ) ;
						document.getElementById('preg0').style.color=\"#FF0000\";
						document.getElementById('preg0director').style.color=\"#FF0000\";
						return false;
					}
					if((document.form_datos.pregunta00[1].checked)&&(document.form_datos.pregunta00_texto.value==''))
					{
						msg = \"Si el formulario no lo rellena el director, debe especificar quien lo hace en la pregunta 0\";
						document.form_datos.pregunta00[0].focus();
						alert( msg ) ;
						document.getElementById('preg0').style.color=\"#FF0000\";
						document.getElementById('preg0otro').style.color=\"#FF0000\";
						return false;
					}
	document.getElementById('preg0').style.color=\"#000000\";
	document.getElementById('preg0director').style.color=\"#000000\";
	document.getElementById('preg0otro').style.color=\"#000000\";
	document.getElementById('preg0otro').style.color=\"#000000\";
					";
		}
		else{
			$form_js.="if((document.form_datos.pregunta00.value!='1')&&(document.form_datos.pregunta00.value!='6'))
					{
						msg = \"Debe indicar quien contesta esta encuesta. En la pregunta 0\";
						alert( msg ) ;
						document.form_datos.pregunta00.focus();
						document.getElementById('preg0').style.color=\"#FF0000\";
						document.getElementById('preg0director').style.color=\"#FF0000\";
						return false;
					}
					if((document.form_datos.pregunta00_texto.value=='')&&(document.form_datos.pregunta00.value=='6'))
					{
						msg = \"Si no es el director, debe especificar quien contesta esta encuesta. En la pregunta 0\";
						alert( msg ) ;
						document.form_datos.pregunta00_texto.focus();
						document.getElementById('preg0').style.color=\"#FF0000\";
						document.getElementById('preg0otro').style.color=\"#FF0000\";
						return false;
					}
	document.getElementById('preg0').style.color=\"#000000\";
	document.getElementById('preg0director').style.color=\"#000000\";
	document.getElementById('preg0otro').style.color=\"#000000\";
	if(";
	for($j=0;$j<32;$j++)
	{
		$dia=$j;
		if($j<10)$dia="0".$j;
		if($j>0)
			$form_js.="&&";
		$form_js.="(document.form_datos.pregunta_fecha_dia.value!='".$dia."')";			
	}
	$form_js.="){
		if(document.form_datos.pregunta_fecha_mes.value!='')
			alert('El valor ' + document.form_datos.pregunta_fecha_dia.value + ' no es válido, debe estar entre el siguiente rango: 01 y 31');	
		else
			alert('El valor debe estar entre el siguiente rango: 01 y 31');	
		document.form_datos.pregunta_fecha_dia.focus();
		document.getElementById('pregdia').style.color=\"#FF0000\";
		return false;
	}					
	document.getElementById('pregdia').style.color=\"#000000\";
	if(";
	for($j=0;$j<13;$j++)
	{
		$mes=$j;
		if($j<10)$mes="0".$j;
		if($j>0)
			$form_js.="&&";
		$form_js.="(document.form_datos.pregunta_fecha_mes.value!='".$mes."')";
	}
	$form_js.="){
		if(document.form_datos.pregunta_fecha_mes.value!='')
			alert('El valor ' + document.form_datos.pregunta_fecha_mes.value + ' no es válido, debe estar entre el siguiente rango: 01 y 12');
		else
			alert('El valor debe estar entre el siguiente rango: 01 y 12');
		document.form_datos.pregunta_fecha_mes.focus();
		document.getElementById('pregmes').style.color=\"#FF0000\";
		return false;
	}
	document.getElementById('pregmes').style.color=\"#000000\";
	if(document.form_datos.pregunta_fecha_agno.value.length!=4){
		if(document.form_datos.pregunta_fecha_mes.value!='')
			alert('El valor ' + document.form_datos.pregunta_fecha_agno.value + ' no es un año correcto, ejemplo: 2012');	
		else
			alert('No ha introducido un año, ejemplo: ".$fechas->anyo_referencia."');	
		document.form_datos.pregunta_fecha_agno.focus();
		document.getElementById('preganyo').style.color=\"#FF0000\";
		return false;
	}
	document.getElementById('preganyo').style.color=\"#000000\";
";
		}
		
			$num_pregunta=1;
			for($h=0;$h<$i;$h++)
			{
				$temp_act[$h]=$v_pregunta[$h]["TEXTOS_OPCIONES"];//Subpreguntas obligatorias				
				$v_preg_act=explode(',',$temp_act[$h]);	

				if(isset($v_pregunta[$h]["N_CABECERAS"]))
					$n_cabeceras=$v_pregunta[$h]["N_CABECERAS"];//Nº de valores posibles a responder para esta pregunta
				else
					$n_cabeceras=1;

				$temp_obl[$h]=$v_pregunta[$h]["OBLIGATORIAS"];//Subpreguntas obligatorias				
				$v_preg_obl=explode(',',$temp_obl[$h]);	

				for($z=0;$z<$n_cabeceras;$z++)
				{
					for($x=0;$x<sizeof($v_preg_act);$x++)
					{
						if(($num_pregunta+$x)<10){	
							$pregunta="pregunta0".($num_pregunta+$x);
						}
						else{
							$pregunta="pregunta".($num_pregunta+$x);
						}	
						//NULOS obligatorias
						$obligatoria=0;
						for($n=0;$n<sizeof($v_preg_obl);$n++){
							if(($x+1)==$v_preg_obl[$n]){
								$obligatoria=1;
								break;
							}
						}
						if($obligatoria==1)
						{
							for($y=0;$y<$v_pregunta[$h]["N_VALORES"];$y++)
							{
								if($y==0)$form_js.="if(";
								$check=$pregunta."[".$y."]";
								if(!$grabador){
									$form_js.="(!document.form_datos.$check.checked)";
								}else{
									$form_js.="(document.form_datos.$pregunta.value=='')";
									break;
								}
								if($y<$v_pregunta[$h]["N_VALORES"]-1)$form_js.="&&";
							}
							$numero=$num_pregunta+$x;
							$form_js.="){
									msg = 'No ha respondido una de las preguntas obligatorias. Pregunta $numero';";
									$form_js.="alert(msg); ";
									if(!$grabador){
										$form_js.="document.form_datos.$check.focus();";
									}else{
										$form_js.="document.form_datos.$pregunta.focus();";
									}
									if($grabador)
										$numero=$num_pregunta+floor($x/$n_cabeceras)*$n_cabeceras;
									$form_js.="document.getElementById('preg$numero').style.color=\"#FF0000\";";
									$form_js.="return false;
								}
								document.getElementById('preg$numero').style.color=\"#000000\";
								";
						}

						//VALORES
						$temp_obligatorias[$h]=$v_pregunta[$h]["N_OPCIONES"];//Subpreguntas obligatorias				
						$v_preg_val=explode(',',$temp_obligatorias[$h]);	
						for($y=0;$y<sizeof($v_preg_val);$y++)
						{
							if($grabador){
								$valores=explode(',',$v_pregunta[$h]["VALORES"]);//VALORES
								$valores_alerta="";
								for($y=0;$y<$v_pregunta[$h]["N_VALORES"];$y++)
								{
									if($y==0){
										$form_js.="if(";
										$valores_alerta.=trim($valores[$y]);
										if($obligatoria==0)$form_js.="(document.form_datos.$pregunta.value!='')&&";
									}
									else{
										$valores_alerta.=", ".trim($valores[$y]);
										$form_js.="&&";
									}
									$form_js.="(document.form_datos.$pregunta.value!=".$valores[$y].")";
								}
								if($v_pregunta[$h]["POR_DEFECTO_VALOR"].length!=0)
								{
									$form_js.="&&";
									$form_js.="(document.form_datos.$pregunta.value!=".$v_pregunta[$h]["POR_DEFECTO_VALOR"].")";
									if($v_pregunta[$h]["POR_DEFECTO_VALOR"]!=0)
										$valores_alerta.=", ".trim($v_pregunta[$h]["POR_DEFECTO_VALOR"]);
								}			
								if(sizeof($v_preg_val)>0){
									$numero=$num_pregunta+$x;
									$form_js.="){
											msg = 'El valor ' + document.form_datos.$pregunta.value + ' no es válido, debe estar entre los siguientes: ".$valores_alerta.". Pregunta $numero';";
											$form_js.="alert(msg); ";
											$form_js.="document.form_datos.$pregunta.focus();";
											$form_js.="return false;
										}
										";
								}
							}
						}
						
					}
		
					
					$num_pregunta+=$v_pregunta[$h]["N_OPCIONES"];			
				}
			}

		$form_js.="document.form_datos.submit();
					return true;}
		
function CheckValorCeldaDirector(valorCelda)
{
	if((valorCelda!='1')&&(valorCelda!='6')){
		if(valorCelda!='')
			alert('El valor ' + valorCelda + ' no es válido, debe estar entre los siguientes: 1, 6');	
		else
			alert('El valor debe estar entre los siguientes: 1, 6');	
		
		return false;
	}
	return true;
}
function CheckValorCeldaOtroDirector(valorCeldaOtro,valorCeldaDirector)
{
	if((valorCeldaDirector=='6')&&(valorCeldaOtro=='')){
		alert('Si no es el director, debe indicar quien rellena');	
		return false;
	}
	return true;
}

function CheckValorCeldaDia(valorCelda)
{
	if(";
	for($j=0;$j<32;$j++)
	{
		$dia=$j;
		if($j<10)$dia="0".$j;
		if($j>0)
			$form_js.="&&";
		$form_js.="(valorCelda!='".$dia."')";			
	}
	$form_js.="){
		if(valorCelda!='')
			alert('El valor ' + valorCelda + ' no es válido, debe estar entre el siguiente rango: 01 y 31');	
		else
			alert('El valor debe estar entre el siguiente rango: 01 y 31');	
		return false;
	}
	return true;
}

function CheckValorCeldaMes(valorCelda)
{
	if(";
	for($j=0;$j<13;$j++)
	{
		$mes=$j;
		if($j<10)$mes="0".$j;
		if($j>0)
			$form_js.="&&";
		$form_js.="(valorCelda!='".$mes."')";
	}
	$form_js.="){
		if(valorCelda!='')
			alert('El valor ' + valorCelda + ' no es válido, debe estar entre el siguiente rango: 01 y 12');	
		else
			alert('El valor debe estar entre el siguiente rango: 01 y 12');	
		return false;
	}
	return true;
}

function CheckValorCeldaAnyo(valorCelda)
{
	if(valorCelda.length!=4){
";
	//$fechas = new fechas;

$form_js.="		if(valorCelda!='')
			alert('El valor ' + valorCelda + ' no es un año correcto, ejemplo: ".$fechas->anyo_referencia."');	
		else
			alert('No ha introducido un año, ejemplo: 2012');	
		return false;
	}
	return true;
}
";

		
		
		for($h=0;$h<$i;$h++)
		{		
$form_js.="		
function CheckValorCelda".$h."(valorCelda)
{
	if(
";
			if(isset($v_pregunta[$h]["POR_DEFECTO_VALOR"]))
				$valor_defecto=$v_pregunta[$h]["POR_DEFECTO_VALOR"];//Nº de valores posibles a responder para esta pregunta
			else
				$valor_defecto="";

			if(strlen($valor_defecto)>0)
			{
$form_js.="(true)&&";
			}else{
$form_js.="(false)&&";
			}
$form_js.="(valorCelda==\"\")){
	return true;
}
";
			
		
			$valores=explode(',',$v_pregunta[$h]["VALORES"]);//VALORES
			$valores_alerta="";
			for($j=0;$j<$v_pregunta[$h]["N_VALORES"];$j++)
			{
				if($j==0){
					$form_js.="if(";
					$valores_alerta.=trim($valores[$j]);
				}
				else{
					$valores_alerta.=", ".trim($valores[$j]);
					$form_js.="&&";
				}
					
				$form_js.="(valorCelda!=".$valores[$j].")";
			}
			if(isset($v_pregunta[$h]["POR_DEFECTO_VALOR"]))
				$valor_defecto=$v_pregunta[$h]["POR_DEFECTO_VALOR"];//Nº de valores posibles a responder para esta pregunta
			else
				$valor_defecto="";

			if(strlen($valor_defecto)>0){
				$form_js.="&&";
				$form_js.="(valorCelda!=".$v_pregunta[$h]["POR_DEFECTO_VALOR"].")";
				if($v_pregunta[$h]["POR_DEFECTO_VALOR"]>0)
					$valores_alerta.=", ".trim($v_pregunta[$h]["POR_DEFECTO_VALOR"]);
			}			
$form_js.="	){
		if(valorCelda!='')
			alert('El valor ' + valorCelda + ' no es válido, debe estar entre los siguientes: ".$valores_alerta."');
		else
			alert('El valor debe estar entre los siguientes: ".$valores_alerta."');
		return false;
	}
	return true;
}";
		}
$form_js.="</script>";
		
		$form.="<table border='0'>".$form_html.$preguntas."<tr><td align='center'><br>";
		$texto_enviar_comentarios="";
		if($modificar<2){
			$texto_enviar_comentarios="<br><font  class='istac'>Nota:&nbsp; Una vez enviado el cuestionario puede utilizar la opción <b>'Enviar comentarios'</b> del menú izquierdo para enviar cualquier comentario.</font><br>&nbsp;";
			if(!$modificar)$form.="<input type='button' title='Pulse el botón para enviar los datos' name='guardar' value='Enviar cuestionario' onclick='return Validate(form_datos);'>";
			else $form.="<input type='button' title='Pulse el botón para modificar los datos' name='guardar' value='Enviar modificaciones' onclick='return Validate(form_datos);'>";
			$form.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			if(!$modificar) $form.="<input type='reset' title='Pulse el botón para comenzar de nuevo en un formulario vacío' name='limpiar' value='Limpiar formulario'>";
			else $form.="<input type='reset' title='Deshacer los cambios' name='limpiar' value='Cancelar'>";
		}
		$form.="</td></tr></table>";
		$form.="</form>.$form_js";
		$form.=$texto_enviar_comentarios;
		fclose($fd);
		
		//RESULTADO
		/*
		for($k=0;$k<$i;$k++)
		{
		echo "<br><br>Pregunta ".($k+1);
			foreach($v_pregunta[$k] as $v=>$c)
			{
				echo"<br>pregunta[$k][$v]=#$c#";
			}
		}
		*/

	}
	
	
	/* SUSTITUCION de textos de fechas (trimestres, meses, año...)*/
	$form=str_replace("[RECOGIDA_TRES_MESES_ANTERIOR]",$fechas->recogida_tres_meses_anterior,$form);
	$form=str_replace("[RECOGIDA_TRES_MESES_ANTERIOR_AÑO]",$fechas->recogida_tres_meses_anterior." de ".$fechas->recogida_tres_meses_anterior_anyo,$form);
	$form=str_replace("[RECOGIDA_TRES_MESES_ANTERIOR_AÑO_ANTERIOR]",$fechas->recogida_tres_meses_anterior." de ".($fechas->recogida_tres_meses_anterior_anyo-1),$form);
	$form=str_replace("[RECOGIDA_TRES_MESES_ANTERIOR_ANTERIOR]",$fechas->recogida_tres_meses_anterior_anterior,$form);
	$form=str_replace("[RECOGIDA_TRES_MESES_ANTERIOR_ANTERIOR_AÑO]",$fechas->recogida_tres_meses_anterior_anterior." de ".$fechas->recogida_tres_meses_anterior_anterior_anyo,$form);
	$form=str_replace("[RECOGIDA_TRES_MESES_AÑO_ANTERIOR]",$fechas->recogida_tres_meses." de ".($fechas->anyo_recogida-1),$form);
	
	
	$form=str_replace("[RECOGIDA_TRES_MESES]",$fechas->recogida_tres_meses,$form);
	$form=str_replace("[RECOGIDA_TRES_MESES_AÑO]",$fechas->recogida_tres_meses." de ".$fechas->anyo_recogida,$form);
	$form=str_replace("[PROXIMOS_TRES_MESES]",$fechas->proximos_tres_meses,$form);
	$form=str_replace("[PROXIMOS_TRES_MESES_AÑO]",$fechas->proximos_tres_meses." de ".$fechas->anyo_referencia,$form);
	$form=str_replace("[PROXIMOS_TRES_MESES_AÑO_ANTERIOR]",$fechas->proximos_tres_meses." de ".($fechas->anyo_referencia-1),$form);
	

	$form=str_replace("[PRIMER_MES]",$fechas->primer_mes,$form);
	$form=str_replace("[SEGUNDO_MES]",$fechas->segundo_mes,$form);
	$form=str_replace("[TERCER_MES]",$fechas->tercer_mes,$form);

	/*   TEXTO INICIAL  */
	$texto1="Estimado usuario:<br><br>
	Contestar el cuestionario que le presentamos a continuación le ocupará muy pocos minutos. 
	Le rogamos que lea detenidamente las preguntas y las conteste basándose en su experiencia profesional y su conocimiento del sector.
	Las respuestas deben reflejar sus expectativas para los meses de ";
	
	$texto2="<b>próximos</b> y deben referirse <b>exclusivamente</b> a su establecimiento hotelero.";
	
	$tpl->set_var("INTRODUCCION","<table align='center' width='95%' border=0><tr><td align='justify'>".$texto1."<b>".$fechas->proximos_tres_meses_mayus." </b>".$texto2."</td></tr></table>");

	
	
	/*MENU*/

	$menu= new menu;
	$menuhtml=$menu->crea_menu();
	$tpl->set_var("MENU",$menuhtml);
	$tpl->set_var("VARIABLE",$form);
	$tpl->set_var("NOMBRE_HOTEL",$fechas->nombre_establecimiento());  
	$tpl->parse("salida","formulario");
	$tpl->p("salida");
}

function rellena_cabecera($db,$tpl_cabecera,$id_hotel,$trimestre_referencia,$anyo_referencia,$disable,$grabador){
	$sql="select director,otra_persona_cargo FROM tb_expectativas";
	$sql.=" WHERE id_establecimiento='".$id_hotel."' AND TRIMESTRE='".$trimestre_referencia."' AND anyo='".$anyo_referencia."'";
	$db->query($sql);
	$db->next_record($sql);
	//DIRECTOR
	if($grabador)
	{
		 if ($db->f("director")!=NULL)
		 {
			if ($db->f("director")==1)
			{
				$tpl_cabecera->set_var("VALUE_DIRECTOR",$db->f("director"));
				$tpl_cabecera->set_var("OTRA_PERSONA_CARGO","");
			}
			else{
				$tpl_cabecera->set_var("VALUE_DIRECTOR","6");
				$tpl_cabecera->set_var("OTRA_PERSONA_CARGO",$db->f("otra_persona_cargo"));
			}
		 }
	}
	else{
		 if ($db->f("director")==NULL)
		 {
			$tpl_cabecera->set_var("CHECKED_DIRECTOR","");
			$tpl_cabecera->set_var("CHECKED_OTRA","");
			$tpl_cabecera->set_var("OTRA_PERSONA_CARGO","");
		 }
		 else
		 {
			if ($db->f("director")==1)
			{
				$tpl_cabecera->set_var("CHECKED_DIRECTOR","checked");
				$tpl_cabecera->set_var("CHECKED_OTRA",$disable);
				$tpl_cabecera->set_var("OTRA_PERSONA_CARGO","");
			}
			else
			{
				$tpl_cabecera->set_var("CHECKED_DIRECTOR",$disable);
				$tpl_cabecera->set_var("CHECKED_OTRA","checked");
				$tpl_cabecera->set_var("OTRA_PERSONA_CARGO",$db->f("otra_persona_cargo"));
			}
		 }
	}
}

?>