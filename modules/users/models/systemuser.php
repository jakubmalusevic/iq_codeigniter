<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SystemUser extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->database();	
	}
    
	function processLogin($data) {
		$this->event->register("BeforeLogin",$data);	
		$return=false;
		$this->db->select("*");
		$this->db->from("users");
		$this->db->where("username",$data['username']);
		$this->db->where("password",md5($data['password']));
		$this->db->where("activated",1);
		$query=$this->db->get();
		$results=$query->result();
		$user_id=0;
		if (count($results)>0) {
			$user_id=$results[0]->id;
			$this->session->set_userdata("user_role",$results[0]->role_id);
			$this->session->set_userdata("user_id",$results[0]->id);
			$this->session->set_userdata("user_name",$results[0]->username);
			$this->session->set_userdata("user_full_name",$results[0]->full_name);
			$seskey=md5(microtime());
			$this->db->where("id",$results[0]->id);
			$this->db->update("users",array("seskey"=>$seskey)); 
			$this->session->set_userdata("user_seskey",$seskey);
			if (isset($data['remember'])) {
				if ($data['remember']==1) {
					$this->input->set_cookie(array("name"=>"remember","value"=>$results[0]->id,"expire"=>30*24*3600));
				}
			}	
			$return=true;		
		}
		$this->event->register("AfterLogin",$data,$user_id,$return);		
		return $return;
    }
    
    function verify(){
    	$return=false;
    	$user_role=$this->session->userdata("user_role");
    	$user_id=$this->session->userdata("user_id");
    	$user_name=$this->session->userdata("user_name");
    	$user_full_name=$this->session->userdata("user_full_name");
    	if ($user_role!="" && $user_id!="" && $user_name!="" && $user_full_name!="") {
    		$user_seskey=$this->session->userdata("user_seskey");
    		if ($user_seskey=="") {
    			$return=false;
    		} else {
				$this->db->select("*");
				$this->db->from("users");
				$this->db->where("id",$user_id);
				$this->db->where("seskey",$user_seskey);
				$this->db->where("activated",1);
				$query=$this->db->get();
				$results=$query->result();  
				if (count($results)>0) {
					$return=true;
				} else {
					$return=false;
				}
			}
    	} else {
    		$remember=$this->input->cookie("remember");
    		if ($remember!="") {
				$this->db->select("*");
				$this->db->from("users");
				$this->db->where("id",$remember);
				$query=$this->db->get();
				$results=$query->result();  	
				if (count($results)>0) {
					$this->session->set_userdata("user_role",$results[0]->role_id);
					$this->session->set_userdata("user_id",$results[0]->id);
					$this->session->set_userdata("user_name",$results[0]->username);
					$this->session->set_userdata("user_full_name",$results[0]->full_name);
					$seskey=md5(microtime());
					$this->db->where("id",$results[0]->id);
					$this->db->update("users",array("seskey"=>$seskey)); 
					$this->session->set_userdata("user_seskey",$seskey);													
					$return=true;					
				}		
    		}
    	}
    	if (!$return && $this->session->userdata("user_id")>0) {
    		$this->processLogout();
    	}
    	return $return;
    }
    
    function processLogout(){
    	$user_id=$this->session->userdata("user_id");
    	$this->event->register("BeforeLogout",$user_id);
    	if ($user_id!="") {
			$this->db->where("id",$user_id);
			$this->db->update("users",array("seskey"=>"")); 
    	}
		delete_cookie("remember");
		$this->session->unset_userdata('user_role');
		$this->session->unset_userdata('user_id');
		$this->session->unset_userdata('user_name');
		$this->session->unset_userdata('user_full_name');
		$this->session->unset_userdata("user_seskey");
		$this->event->register("AfterLogout",$user_id);    	   	    	
    }
    
    public function getPrimaryRedirection(){
    	$return="";
		$role_id=$this->session->userdata("user_role");
		if ($role_id>0) {
			$this->db->select("permissions");
			$this->db->from("roles");
			$this->db->where("id",$role_id);
			$query=$this->db->get();
			$results=$query->result();
			if (count($results)>0) {
				if (!$permissions=@unserialize($results[0]->permissions)) $permissions=array();
				foreach($permissions as $module=>$values) {
					if ($return=="" && $this->checkModuleEnabled($module)) {
						foreach($values as $section=>$actions) {
							if ($section!="show_in_navigation" && $return=="" && isset($actions[0])) {
								$return=$module."/".$section."/".$actions[0];
							}
						}
					}
				}
			}
		}
		if ($return=="") {
			$return="users/publicaccess/logout";
		}
		return $return;
    }
    
    public function checkModuleEnabled($module=""){
    	$return=false;
    	if ($module!="") {
    		$module=strtolower($module);
			$this->db->select("id");
			$this->db->from("modules");
			$this->db->where("name",$module);
			$this->db->where("state",1);
			$query=$this->db->get();
			$results=$query->result();
			if (count($results)>0) {
				$return=true;
			}    		
    	}
    	return $return;
    }
    
}
?>