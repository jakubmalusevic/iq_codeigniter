<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UsersCatcher {

	private $CI;

	function __construct() {
		$this->CI=& get_instance();
	}

	public function onAfterControllerInitiated($module,$controller,$action){
		$user_seskey=$this->CI->session->userdata("user_seskey");
		if ($user_seskey!="") {
			$update=array("last_activity"=>date("Y-m-d H:i:s",time()));
			$this->CI->db->where("seskey",$user_seskey);
			$this->CI->db->update("users",$update);
		}		
	}
	
	public function onBeforeControllerInitiated($module,$controller,$action){
		$this->_checkUpdates05032014();
	}	
	
	protected function _checkUpdates05032014(){
		$reset_token_field=false;
		$query=$this->CI->db->query("DESCRIBE `".$this->CI->db->dbprefix."users`");
		$fields=$query->result();
		foreach($fields as $field){
			if ($field->Field=="reset_token") $reset_token_field=true;
		}
		if (!$reset_token_field) {
			$this->CI->db->query("ALTER TABLE `".$this->CI->db->dbprefix."users` ADD `reset_token` varchar(255) NOT NULL AFTER `last_activity`");
		}		
	}
    
}
?>