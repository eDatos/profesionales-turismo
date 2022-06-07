<?php

class EstablishmentUser 
{
	var $id;
	var $username = "";       
	var $password = ""; 
	var $profile  = "";
	var $establishment_id;
	var $establishment;
       
	function EstablishmentUser($id, $establishment_id) 
	{           
	   $this->id = $id;
	   $this->establishment_id  = $establishment_id;
	}        
}

?>