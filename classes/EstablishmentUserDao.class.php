<?php
  
require_once(__DIR__."/EstablishmentUser.class.php");
require_once(__DIR__."/../lib/DbHelper.class.php");
require_once(__DIR__."/../lib/RowDbIterator.class.php");

class EstablishmentUserDao 
{
	var $db;   
		
	function __construct()
	{
		$this->db = new Istac_Sql();
	}
	
	/*
	*   create user. return user or null
	*/
	function create($user_id, $username, $md5_password, $perm, $establishment_id) 
	{           
		if ($user_id == '' || $username == '' || $md5_password == '' || $perm == '' || $establishment_id =='')
			return false;
		
		$sql1 = DbHelper::prepare_sql("insert into tb_auth_user_md5(user_id, username, password, perms) 
				values(:userid ,:username,:pass,:prof)",
				array(":userid"   => (string)$user_id,
					  ":username" => (string)$username,
					  ":pass"     => (string)$md5_password,
					  ":prof"     => (string)$perm));
		                                                                                                                                                        
		@$this->db->query($sql1,sprintf("insert into tb_auth_user_md5(user_id, username, password, perms) values('%s','%s','****','%s')",$user_id,$username,$perm)); 
		                            
		if ($this->db->affected_rows() == 0) 
		{
			return false; 
		}
																		
		$sql2 = DbHelper::prepare_sql("insert into tb_usuario_hotel(user_id, id_hotel) values(:userid, :estid)",
				array(":userid"   => (string)$user_id,
					  ":estid" => (string)$establishment_id));
		
		@$this->db->query($sql2);
		if ($this->db->affected_rows() == 0)
		{
			Log::warning("Error en la creacion de usuario: Se ha podido introducir el usuario en tb_auth_user_md5, pero no en tb_usuario_hotel.");
			return false;
		}
		
		$sql3 = DbHelper::prepare_sql("insert into tb_aloja_password_orig(user_id, username, password) 
				values(:userid ,:username,:pass)",
				array(":userid"   => (string)$user_id,
					  ":username" => (string)$username,
					  ":pass"     => (string)$md5_password));
		@$this->db->query($sql3,sprintf("insert into tb_aloja_password_orig(user_id, username, password) values('%s' ,'%s','****')",$user_id,$username));
		if ($this->db->affected_rows() == 0) 
		{
			Log::warning("Error en la creacion de usuario: Se ha podido introducir el usuario en tb_auth_user_md5 y tb_usuario_hotel, pero no en tb_aloja_password_orig.");
			return false;
		}
		
		$user = new EstablishmentUser($user_id, $establishment_id);
		$user->username = $username;
		$user->password = $md5_password;
		$user->profile  = $perm;
																			  
		return $user;      
	}

	/*
	*   load user. return user or null
	*/
	function load($establishment_id, $id) 
	{          
		$user = null;          
		if (($establishment_id == '') || ($id == '')) 
			return null;
		
		$sql  = DbHelper::prepare_sql(" select u.user_id, u.username, u.password, u.perms, e.id_hotel from tb_auth_user_md5 u, tb_usuario_hotel e".
			  " where u.user_id = e.user_id and ".
			  " u.user_id=:userid and e.id_hotel=:estid",
				array(":userid" => (string)$id, 
					   ":estid" => (string)$establishment_id));
																								  
		@$this->db->query($sql);
											
		if ($this->db->next_record()) 
		{                                    
			$user = new EstablishmentUser($this->db->f('user_id'), $this->db->f('id_hotel'));
			$user->username         = $this->db->f('username');
			$user->password         = $this->db->f('password');
			$user->profile          = $this->db->f('perms');            
		}
		return $user;
	}
      
	/*
	*   load user. return user or null
	*/
	function loadUser($id) 
	{
		$user = null;
		
		if ($id == null) return null;
		 
		$sql  = DbHelper::prepare_sql("select u.user_id, u.username, u.password, u.perms, e.id_hotel from tb_auth_user_md5 u, tb_usuario_hotel e".
				" where u.user_id = e.user_id(+) and ".
				" u.user_id = :userid", array(":userid" => $id));
																	 
		$this->db->query($sql);
		if ($this->db->next_record()) 
		{
			$userId          = $this->db->f("user_id");
			$username        = $this->db->f("username");
			$perm            = $this->db->f("perms");
			$establishmentId = $this->db->f("id_hotel");
		
			$user = new EstablishmentUser($userId, $establishmentId);
			$user->username = $username;
			$user->profile  = $perm;
		}
		return $user;
	}

	public function load_by_username($query_username)
	{
		$user = null;
	
		if ($query_username == '') return null;
			
		$sql  = DbHelper::prepare_sql("select u.user_id, u.username, u.password, u.perms, e.id_hotel from tb_auth_user_md5 u, tb_usuario_hotel e".
				" where u.user_id = e.user_id(+) and upper(u.username) = :username", 
				array(":username" => (string)strtoupper($query_username)));
	
		$this->db->query($sql);
		if ($this->db->next_record())
		{
			$userId          = $this->db->f("user_id");
			$username        = $this->db->f("username");
			$perm            = $this->db->f("perms");
			$establishmentId = $this->db->f("id_hotel");
	
			$user = new EstablishmentUser($userId, $establishmentId);
			$user->username = $username;
			$user->profile  = $perm;
		}
		return $user;
	}
	

	/*
	*   save user. return true or false
	*/                        
	function save($user) 
	{
		if ($user->password == '' && $user->profile == '')
			return false;
		
		$sql = "update tb_auth_user_md5 set ";  

		$ss = array();
		if ($user->password != '')
			$ss[] = "password = :pass ";
		
		if ($user->profile != '')
			$ss[] = "perms = :prof ";
			
		$sql .= implode(",", $ss);
		
		$sql .= " where user_id=:userid";
		
		$psql = DbHelper::prepare_sql($sql, array(':pass' => (string)($user->password), 
												  ':prof' => (string)($user->profile),
				 								  ':userid' => (string)($user->id)));
		
		$this->db->query($psql,str_replace(":pass","'****'",str_replace(":userid","'".(string)($user->id)."'",str_replace(":perms","'".(string)($user->profile)."'",$sql))));                                        
		if ($this->db->affected_rows() == 0) 
			return false;
		
		return true;          
	}

	/*
	*   search return array of users
	*/
	function search($id_hotel, $username = NULL) 
	{
		$users = array();
						  
		$sql = $this->createQueryForSearchUsers($id_hotel, $username);                
		$this->db->query($sql); 
		
		while ($this->db->next_record()) 
		{
			 $user = new EstablishmentUser($this->db->f('user_id'), $this->db->f('id_hotel'));
			 $user->username      = $this->db->f('username');
			 $user->password      = $this->db->f('password');
			 $user->profile       = $this->db->f('perms');
			 //$user->establishment = new datos_resultados($this->db->f('id_hotel'));
			 
			 $users[] = $user;             
		}                   
		return $users;                  
	}
      
	/*
	*   delete return true or false
	*/         
	function delete($user_id) 
	{
		if ($user_id == '')
			return false;
		
		$sql1 = DbHelper::prepare_sql("delete from tb_auth_user_md5 where user_id = :userid", array(":userid"=>(string)$user_id));
		$sql2 = DbHelper::prepare_sql("delete from tb_usuario_hotel where user_id = :userid", array(":userid"=>(string)$user_id));
		
		$this->db->query($sql1);          
		if ($this->db->affected_rows() == 0) 
			return false;
		
		$this->db->query($sql2);                    
		if ($this->db->affected_rows() == 0) 
			return false;
		
		return true;
	}              
      
	function load_original_password_for($user_id) 
	{
		if ($user_id == '')
			return false;
		
		$sql = DbHelper::prepare_sql("select password from tb_aloja_password_orig where user_id = :userid", array(":userid" => (string)$user_id));        
					   
		$this->db->query($sql);                
		$this->db->next_record(); 

		$pw = $this->db->f('password');
		if (!isset($pw))
			return false;
		
		return $pw;
	}
      
	private function createQueryForSearchUsers($id_hotel, $username) 
	{          
		$sql = "select u.user_id, u.username, u.password, u.perms, e.id_hotel from tb_auth_user_md5 u, tb_usuario_hotel e".
			 " where u.user_id = e.user_id ";
		
		if ($id_hotel != '') 
			$sql .= " and e.id_hotel = :hotelid";
		if ($username != '')       
			$sql .= " and upper(u.username) like :username";
		
		$sql .= " order by username"; 
						  
		return DbHelper::prepare_sql($sql, array(":hotelid" => (string)$id_hotel, ":username"=>(string)"%".strtoupper($username)."%"));
	}
      
	
	public function search_users_by_name($username)
	{
		$sql = DbHelper::prepare_sql("SELECT u.user_id, u.username, e.nombre_establecimiento
				FROM tb_auth_user_md5 u
				INNER JOIN tb_usuario_hotel uh ON u.user_id = uh.user_id
				INNER JOIN tb_establecimientos_unico e ON uh.id_hotel = e.id_establecimiento
				WHERE upper(u.username) LIKE :usercrit", 
				array(':usercrit' => "%".strtoupper($username)."%"));
		
		$this->db->query($sql);
	
		return new RowDbIterator($this->db, array('user_id','username','nombre_establecimiento'));
	}
	
	public function get_old_password($username)
	{
		$sql = DbHelper::prepare_sql("select password from tb_auth_user_md5 where username = :uname",
				array(':uname' => (string)$username));
	
		$db = new Istac_Sql();
		$db->query($sql);
		if ($db->next_record())
		{
			return $db->f('password');
		}
		return null;
	}
	
	public function update_user_password($username, $nueva_password_md5)
	{	
		$sql = DbHelper::prepare_sql("update tb_auth_user_md5 set password = :pwd where username = :uname",
				array(':uname' => (string)$username, ":pwd" => (string)$nueva_password_md5));
	
		$db = new Istac_Sql();
		$db->query($sql,sprintf("update tb_auth_user_md5 set password = '****' where username = '%s'",$username));
		if ($db->affected_rows() > 0)
		{
			return true;
		}
		return false;
	}
	
	/**********************************************/
	/*** METODOS DE RECUPERACION DE CONTRASEÑAS ***/
	/**********************************************/
	
	public function get_recovery_data($token, $fecha_comprobacion)
	{
		$sql = DbHelper::prepare_sql("SELECT R.USER_ID, U.USERNAME, R.RECOVERY_TOKEN, R.VALIDEZ_HASTA
				FROM TB_AUTH_USER_MD5 U
				LEFT JOIN TB_AUTH_RECOVERY R
				on U.USER_ID     = R.USER_ID
				WHERE R.RECOVERY_TOKEN = :token
				 and R.validez_hasta >= to_date(:fecha_validez, 'yyyy-mm-dd HH24:MI:SS')",
				array(':token' => (string)$token, 
					  ":fecha_validez" => (string)$fecha_comprobacion->format('Y-m-d H:i:s')));
	
		$db = new Istac_Sql();
		$db->query($sql);
		if ($db->next_record())
		{
			$result['user_id'] = $db->f('user_id');
			$result['username'] = $db->f('username');
			$result['token'] = $db->f('recovery_token');
			$result['validez'] = $db->f('validez_hasta');
			return $result;
		}
		return null;
	}
	
	public function get_user_contact_info($username)
	{
		if (!isset($username) || $username == null)
			return null;
		
		$sql = DbHelper::prepare_sql("SELECT U.USER_ID, U.PERMS, E.EMAIL, E.EMAIL2
										FROM TB_AUTH_USER_MD5 U
										INNER JOIN TB_USUARIO_HOTEL UE
										ON U.USER_ID = UE.USER_ID
										INNER JOIN TB_ESTABLECIMIENTOS_UNICO E
										ON UE.ID_HOTEL   = E.ID_ESTABLECIMIENTO
										WHERE U.USERNAME = :username",
				array(':username' => (string)strtoupper($username)));
		
		$db = new Istac_Sql();
		$db->query($sql);
		if ($db->next_record())
		{
			$result = array();
			
			$result['userid'] = $db->f('user_id');
			$result['perms'] = $db->f('perms');
			$result['email'] = $db->f('email');
			$result['email2'] = $db->f('email2');
			return $result;
		}
		return null;
	}
	
	/**
	 * Inserta un registro en la tabla tb_auth_recovery para la recuperacion de contraseña.  
	 * @param unknown_type $userid			Id. de usuario para el que se quiere recuperar la contraseña
	 * @param unknown_type $recovery_token  Token de seguridad para la recuperacion.
	 * @param unknown_type $valida_desde	Fecha para comprobar que no existe ya un proceso de recuperacion en marcha.
	 * @param unknown_type $valida_hasta	Fecha hasta la que sera valido el proceso de recuperacion.
	 */
	public function insert_recovery_token($userid, $recovery_token, $fecha_peticion, $valida_hasta)
	{
		$db = new Istac_Sql();
		
		/// Borra intentos anteriores que no están aún en fechas de uso.
		if ($fecha_peticion != null)
		{			
			$sql = DbHelper::prepare_sql("delete from tb_auth_recovery 
					where user_id = :userid 
					and fecha_peticion < to_date(:fecha_peticion, 'yyyy-mm-dd HH24:MI:SS')",
					array(":userid"        => (string)$userid,
						  ":fecha_peticion" => (string)$fecha_peticion->format('Y-m-d H:i:s')));
		}
		else
		{
			// no hay cadencia entre intentos de recuperacion.
			$sql = DbHelper::prepare_sql("delete from tb_auth_recovery where user_id = :userid",
					array(":userid"        => (string)$userid));
		}
		
		$db->query($sql);
		/// No borra nada si no hay recuperacion anterior para el usuario OR hay una activa.
		
		$fecha_pet = new DateTime();
		
		//Es correcto que falle si hay un proceso anterior en marcha (por unique key).
		$sql = DbHelper::prepare_sql("insert into tb_auth_recovery(user_id, recovery_token, fecha_peticion, validez_hasta) 
				values(:userid, :recpwd, to_date(:fecha_peticion, 'yyyy-mm-dd HH24:MI:SS'), to_date(:fecha_validez, 'yyyy-mm-dd HH24:MI:SS'))",
				array(":userid"   => (string)$userid,
					  ":recpwd" => (string)$recovery_token,
					  ":fecha_peticion" => (string)$fecha_pet->format('Y-m-d H:i:s'),
					  ":fecha_validez" => (string)$valida_hasta->format('Y-m-d H:i:s')));
	
		@$db->query($sql);
		
		if ($db->affected_rows() > 0)
		{
			return true;
		}
		return false;
	}
	
	public function delete_recovery_token($userid, $recovery_token)
	{
		$db = new Istac_Sql();
	
		/// Borra intentos anteriores que no están aún en fechas de uso.
		// no hay cadencia entre intentos de recuperacion.
		$sql = DbHelper::prepare_sql("delete from tb_auth_recovery where user_id = :userid and recovery_token = :recpwd",
				array(":userid"        => (string)$userid,
						":recpwd" => (string)$recovery_token));
	
		$db->query($sql);
	
		if ($db->affected_rows() > 0)
		{
			return true;
		}
		return false;
	}
}
      
?>
