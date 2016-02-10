<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Acl Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Acl
 * @author		IQDesk
 * @link		http://iqdesk.net
 */
class CI_Acl {

	private $CI;
	var $roles=array();
	var $permissions=array();
	var $default_role="";
	var $session_variable_name="";
	var $redirect_on_access_denied="";
	var $redirect_on_ajax_access_denied="";

	public function __construct() {
		$this->CI=& get_instance();
		$this->_loadAclConfig();
		log_message("debug", "Acl Class Initialized");
	}
	
	private function _loadAclConfig(){
		$acl_file_loaded=false;
		if (file_exists(APPPATH."config/".ENVIRONMENT."/acl.php")) {
			require(APPPATH."config/".ENVIRONMENT."/acl.php");
			$acl_file_loaded=true;
		} else {
			if (file_exists(APPPATH."config/acl.php")) {
				require(APPPATH."config/acl.php");
				$acl_file_loaded=true;
			} else {
				echo "Acl file is not found.";
			}			
		}
		$map_source="file";
		if (isset($acl["map_source"])) {
			if ($acl["map_source"]=="db_table" && isset($acl["map_source_table"])) $map_source="table";
		}
		if ($acl_file_loaded && $map_source=="file") {
			$this->roles=$acl["roles"];
			$this->permissions=$acl["permissions"];
		}
		if ($acl_file_loaded && $map_source=="table") {
			$this->CI->db->select("*");
			$this->CI->db->from($acl["map_source_table"]);
			$query=$this->CI->db->get();
			$acl_table=$query->result();
			$this->roles=array();
			$this->permissions=array();
			for($i=0;$i<count($acl_table);$i++){
				$this->roles[]=$acl_table[$i]->id;
				$this->permissions[$acl_table[$i]->id]=array();
				$this->permissions[$acl_table[$i]->id]=@unserialize($acl_table[$i]->permissions);
				$this->full_access[$acl_table[$i]->id]=$acl_table[$i]->full_access;
				
			}
		}
		if ($acl_file_loaded){
			$this->default_role=$acl["default_role"];
			$this->session_variable_name=$acl["session_variable_name"];
			$this->redirect_on_access_denied=$acl["redirect_on_access_denied"];
			$this->redirect_on_ajax_access_denied=$acl["redirect_on_ajax_access_denied"];	
		}
	}
	
	public function checkPermissions($module,$controller,$action,$role=""){
		if ($role=="") {
			if ($this->session_variable_name!="") {
				$this->CI->load->library('session');
				$session_user_role=$this->CI->session->userdata($this->session_variable_name);
				if ($session_user_role!="") {
					$role=$session_user_role;
				} else {
					if ($this->default_role!="") {
						$role=$this->default_role;
					} else {
						echo "Default role is not found.";
					}
				}
			} else {
				if ($this->default_role!="") {
					$role=$this->default_role;
				} else {
					echo "Default role is not found.";
				}			
			}
		}
		$access=false;
		if ($role!="") {
			$full_access=0;
			if (isset($this->full_access[$role])) {
				$full_access=$this->full_access[$role];
			}
			if (!$full_access){
				if (isset($this->permissions[$role][$module])) {
					if (isset($this->permissions[$role][$module][$controller])) {
						if (in_array($action,$this->permissions[$role][$module][$controller])) {
							$access=true;	
						}
					}
				}
			} else {
				$access=true;
			}
		}
		return $access;
	}
	
	public function isGuest(){
		if ($this->CI->session->userdata("user_role")==$this->default_role || $this->CI->session->userdata("user_role")=="") return true;
		else return false;
	}
	
	public function checkNavigationItem($module,$controller,$action){
		$return=false;
		$this->CI->db->select("permissions");
		$this->CI->db->from("roles");
		$this->CI->db->where("id",$this->CI->session->userdata("user_role"));
		$query=$this->CI->db->get();
		$permissions_row=$query->result();
		if (count($permissions_row)>0){
			$permissions=array();
			$permissions=@unserialize($permissions_row[0]->permissions);
			if (isset($permissions[$module]['show_in_navigation'])) {
				if ($permissions[$module]['show_in_navigation']==1 && isset($permissions[$module][$controller])) {
					if (in_array($action,$permissions[$module][$controller])) $return=true;
				}
			}
		}
		return $return;
	}

}
// END Acl Class

/* End of file Acl.php */
/* Location: ./application/libraries/Acl.php */