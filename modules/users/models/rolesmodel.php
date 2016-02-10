<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RolesModel extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->database();
	}
    
	function getItems($params=array(),$sorting=array(),$page=-1) {
		$return=array();	
		$this->db->select("roles.*,COUNT(users.id) as count_of_users");
		$this->db->from("roles as roles");
		$this->db->join("users as users","roles.id=users.role_id","left");		
		if (isset($params['role_name'])) {
			if (str_replace(" ","",$params['role_name'])!="") {
				$this->db->where("roles.`name` LIKE '%".$this->db->escape_like_str($params['role_name'])."%'",NULL,false);	
			}
		}				
		$this->db->group_by("roles.id");
		if (isset($sorting['sort-column']) && isset($sorting['sort-direction'])) {
			$this->db->order_by($sorting['sort-column'],$sorting['sort-direction']);
		} else {
			$this->db->order_by("roles.name","asc");
		}
		$this->event->register("BuildRolesQuery");
		$this->total_count=$this->db->get_total_count();
		if ($page!=-1) {
			$this->db->limit($this->pagination->count_per_page,$page*$this->pagination->count_per_page);
		}
		$query=$this->db->get();
		$return=$query->result();
		return $return;
    }
	
	function create($data){
		$return=true;
		$this->event->register("BeforeCreateRole",$data);
		$permissions=array();
		if (!isset($data['full_access'])) {
			if (isset($data['permissions'])) {
				$permissions=$data['permissions'];
			}
		} else {
			$this->load->model("modules/ModulesModel");
			$all_modules=$this->ModulesModel->getItems();
			foreach($all_modules as $module) {
				$permissions[$module->name]=array();
				if (!$sections=@unserialize($module->sections)) $sections=array();
				foreach($sections as $section) {
					$permissions[$module->name][$section->name]=array();
					foreach($section->actions as $action) {
						$permissions[$module->name][$section->name][]=$action->name;
					}
				}
			}
			if (isset($data['permissions'])) {
				if (is_array($data['permissions'])) {
					foreach($data['permissions'] as $module=>$module_permission) {
						if (isset($module_permission['show_in_navigation'])) {
							if (!isset($permissions[$module])) $permissions[$module]=array();
							$permissions[$module]['show_in_navigation']=1;
						}
					}
				}
			}			
		}
		$permissions=serialize($permissions);
		$insert=array(
			"name"=>$data['name'],
			"permissions"=>$permissions,
			"full_access"=>isset($data['full_access'])?1:0
		);
		$this->db->insert("roles",$insert);	
		$item_id=$this->db->insert_id();
		$this->event->register("AfterCreateRole",$data,$item_id);
		$this->SystemLog->write("users","roles","create",1,"Role \"".$data['name']."\" has been created in the system");
		return $return;		
	}
	
	function getItem($item_id){
		$return=false;
		$this->db->select("roles.*");
		$this->db->from("roles as roles");
		$this->db->where("roles.id",$item_id);		
		$this->event->register("BuildRoleQuery",$item_id);
		$query=$this->db->get();
		$results=$query->result();
		if (count($results)>0) {
			$return=$results[0];
		}
		return $return;
	}
	
	function update($data,$item_id){
		$this->event->register("BeforeUpdateRole",$data,$item_id);
		$return=true;
		$permissions=array();
		if (!isset($data['full_access'])) {
			if (isset($data['permissions'])) {
				$permissions=$data['permissions'];
			}
		} else {
			$this->load->model("modules/ModulesModel");
			$all_modules=$this->ModulesModel->getItems();
			foreach($all_modules as $module) {
				$permissions[$module->name]=array();
				if (!$sections=@unserialize($module->sections)) $sections=array();
				foreach($sections as $section) {
					$permissions[$module->name][$section->name]=array();
					foreach($section->actions as $action) {
						$permissions[$module->name][$section->name][]=$action->name;
					}
				}
			}
			if (isset($data['permissions'])) {
				if (is_array($data['permissions'])) {
					foreach($data['permissions'] as $module=>$module_permission) {
						if (isset($module_permission['show_in_navigation'])) {
							if (!isset($permissions[$module])) $permissions[$module]=array();
							$permissions[$module]['show_in_navigation']=1;
						}
					}
				}
			}
		}		
		$permissions=serialize($permissions);	
		$update=array(
			"name"=>$data['name'],
			"permissions"=>$permissions,
			"full_access"=>isset($data['full_access'])?1:0		
		);
		$this->db->where("id",$item_id);
		$this->db->update("roles",$update);
		$this->event->register("AfterUpdateRole",$data,$item_id);
		$this->SystemLog->write("users","roles","update",2,"Role \"".$data['name']."\" has been updated in the system");
		return $return;
	}
	
	function delete($item_id){
		$this->event->register("BeforeDeleteRole",$item_id);
    	$item=$this->getItem($item_id);		
		$this->db->where("id",$item_id);
		$this->db->delete("roles");
		$this->event->register("AfterDeleteRole",$item_id);
		$this->SystemLog->write("users","roles","delete",3,"Role \"".$item->name."\" has been deleted from the system");
		return true;	
	}

}
?>