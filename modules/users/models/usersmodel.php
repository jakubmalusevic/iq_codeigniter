<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UsersModel extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->database();
	}
    
	function getItems($params=array(),$sorting=array(),$page=-1) {  		
		$return=array();		
		$this->db->select("users.*,roles.name as role_name");
		$this->db->from("users as users");
		$this->db->join("roles as roles","users.role_id=roles.id","left");		
		if (isset($params['user_name'])) {
			if (str_replace(" ","",$params['user_name'])!="") {
				$this->db->where("users.`username` LIKE '%".$this->db->escape_like_str($params['user_name'])."%'",NULL,false);
			}
		}
		if (isset($params['user_full_name'])) {
			if (str_replace(" ","",$params['user_full_name'])!="") {
				$this->db->where("users.`full_name` LIKE '%".$this->db->escape_like_str($params['user_full_name'])."%'",NULL,false);
			}
		}			
		if (isset($params['user_role'])) {
			if (str_replace(" ","",$params['user_role'])!="") {
				$this->db->where("users.role_id",$params['user_role']);
			}
		}		
		if (isset($params['user_activated'])) {
			if (str_replace(" ","",$params['user_activated'])!="") {
				$this->db->where("users.activated",$params['user_activated']);
			}
		}						
		if (isset($sorting['sort-column']) && isset($sorting['sort-direction'])) {
			$this->db->order_by($sorting['sort-column'],$sorting['sort-direction']);
		} else {
			$this->db->order_by("users.username","asc");
		}
		$this->event->register("BuildUsersQuery");
		$this->total_count=$this->db->get_total_count();
		if ($page!=-1) {
			$this->db->limit($this->pagination->count_per_page,$page*$this->pagination->count_per_page);
		}
		$query=$this->db->get();
		$return=$query->result();
		return $return;
    }
	
	function create($data){
		$this->event->register("BeforeCreateUser",$data);
    	$return=true;
    	$this->db->select("id");
    	$this->db->from("users");
    	$this->db->where("username",$data['username']); 
    	$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$return=false;
			$this->notifications->setError("\"".$data['username']."\" ".$this->lang->line("username_already_used"));
		}
    	$this->db->select("id");
    	$this->db->from("users");
    	$this->db->where("email",$data['email']); 
    	$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$return=false;
			$this->notifications->setError("\"".$data['email']."\" ".$this->lang->line("email_already_used"));
		}		
		if ($return) {
			$insert=array(
				"username"=>$data['username'],
				"password"=>md5($data['password']),
				"full_name"=>$data['full_name'],
				"role_id"=>$data['role_id'],
				"email"=>$data['email'],
				"seskey"=>"",
				"activated"=>(isset($data['activated'])?$data['activated']:0),
				"last_activity"=>"0000-00-00 00:00:00"
			);
			$this->db->insert("users",$insert);	
			$item_id=$this->db->insert_id();
			$this->event->register("AfterCreateUser",$data,$item_id);
			$this->SystemLog->write("users","users","create",1,"User \"".$data['username']."\" has been created in the system");	
		}
		return $return;
	}
	
	function getItem($item_id){
		$return=false;
		$this->db->select("users.*");
		$this->db->from("users");
		$this->db->where("users.id",$item_id);
		$this->event->register("BuildUserQuery",$item_id);
    	$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$return=$result[0];
		}
		return $return;
	}
	
	function update($data,$item_id){
		$this->event->register("BeforeUpdateUser",$data,$item_id);
		$user=$this->getItem($item_id);	
    	$return=true;
    	$this->db->select("id");
    	$this->db->from("users");
    	$this->db->where("username",$data['username']); 
    	$this->db->where("id !=",$item_id);
    	$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$return=false;
			$this->notifications->setError("\"".$data['username']."\" ".$this->lang->line("username_already_used"));
		}
    	$this->db->select("id");
    	$this->db->from("users");
    	$this->db->where("email",$data['email']); 
    	$this->db->where("id !=",$item_id);
    	$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$return=false;
			$this->notifications->setError("\"".$data['email']."\" ".$this->lang->line("email_already_used"));
		}	
		if ($return) {
			if (isset($data['change_password'])) {
				$update=array(
					"username"=>$data['username'],
					"password"=>md5($data['new_password']),
					"full_name"=>$data['full_name'],					
					"role_id"=>$data['role_id'],
					"email"=>$data['email']
				);
			} else {
				$update=array(
					"username"=>$data['username'],
					"full_name"=>$data['full_name'],					
					"role_id"=>$data['role_id'],
					"email"=>$data['email']
				);			
			}
			if (isset($data['activated'])) {
				$update['activated']=1;
			} else {
				if ($item_id!=$this->session->userdata("user_id")) {
					$update['activated']=0;
				}
			}
			$this->db->where("id",$item_id);
			$this->db->update("users",$update);
			if ($item_id==$this->session->userdata("user_id")) {
				$this->session->set_userdata("user_role",$data['role_id']);
				$this->session->set_userdata("user_name",$data['username']);
				$this->session->set_userdata("user_full_name",$data['full_name']);			
			}			
			$this->event->register("AfterUpdateUser",$data,$item_id);	
			$this->SystemLog->write("users","users","update",2,"User \"".$user->full_name."\" has been updated in the system");
		}
		return $return;
	}

	function delete($item_id){
		$this->event->register("BeforeDeleteUser",$item_id);
		$user=$this->getItem($item_id);	
		$this->db->where("id",$item_id);
		$this->db->delete("users");
		$this->event->register("AfterDeleteUser",$item_id);
		$this->SystemLog->write("users","users","delete",3,"User \"".$user->full_name."\" has been deleted from the system");
		return true;
	}

}
?>