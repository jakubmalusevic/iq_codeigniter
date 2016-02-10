<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends MX_Controller {
	
	var $check_permissions=true;

	public function index(){			
		$this->view_data['page_title']=$this->lang->line("users_and_roles").$this->theme->page_title_delimiter.$this->lang->line("users");;
		$this->load->model('UsersModel');
		$this->load->model('RolesModel');
		$params=array();
		$get=$this->input->get(NULL, TRUE, TRUE);
		if (isset($get['apply_filters']) && isset($get['filter'])) {
			$params=$get['filter'];
		}			
		$sorting=array();
		if (isset($get['sort-column']) && @$get['sort-column']!="") {
			$sorting['sort-column']=$get['sort-column'];
			$sorting['sort-direction']="asc";
			if (isset($get['sort-direction'])) {
				if (strtolower($get['sort-direction'])=="asc" || strtolower($get['sort-direction'])=="desc") {
					$sorting['sort-direction']=$get['sort-direction'];
				}
			}
		}		
		$page=0;
		if (isset($get['page'])) {
			if (is_numeric($get['page']) && $get['page']>=0) {
				$page=$get['page'];
			}
		}			
		$this->view_data['items']=$this->UsersModel->getItems($params,$sorting,$page);
		$total_items=$this->UsersModel->total_count;
		$this->pagination->setNumbers(count($this->view_data['items']),$total_items);
		$roles_options=array(
			array("value"=>"","label"=>$this->lang->line("all_roles"))
		);
		$all_roles=$this->RolesModel->getItems();
		foreach($all_roles as $role){
			$roles_options[]=array("value"=>$role->id,"label"=>$role->name);
		}
		$sidebar_params=array(
			"name"=>"left-sidebar",
			"title"=>$this->lang->line("filters"),
			"position"=>"left",
			"is_filter"=>true,
			"filter_action"=>base_url()."users/users/index",
			"submit_button"=>$this->lang->line("apply_filters"),
			"reset_button"=>$this->lang->line("reset_filters"),
			"filter_event"=>"UsersFilterFormRow",
			"elements"=>array(
				array(
					"type"=>"text",
					"name"=>"user_name",
					"placeholder"=>$this->lang->line("enter_user_name")
				),
				array(
					"type"=>"text",
					"name"=>"user_full_name",
					"placeholder"=>$this->lang->line("enter_full_name")
				),
				array(
					"type"=>"select",
					"name"=>"user_role",
					"options"=>$roles_options
				),
				array(
					"type"=>"checkbox",
					"name"=>"user_activated",
					"placeholder"=>$this->lang->line("activated_users")
				)
			)
		);		
		$this->sidebar->register($sidebar_params);
		$this->load->view('general/header',$this->view_data);
		$this->load->view('usersview/index',$this->view_data);
		$this->load->view('general/footer',$this->view_data);
	}	
	
	public function create(){
		$this->load->model('UsersModel');
		$this->load->model('RolesModel');
		if ($data=$this->input->post(NULL, TRUE)) {
			if ($this->UsersModel->create($data)) {
				$this->notifications->setMessage($this->lang->line("user_created_successfully"));
			}
			redirect($_SERVER['HTTP_REFERER']);
		}	
		$this->view_data['all_roles']=$this->RolesModel->getItems();
		$this->load->view('usersview/create',$this->view_data);
	}	
	
	public function update(){
		$this->load->model('UsersModel');
		$this->load->model('RolesModel');		
		if ($this->uri->segment(4)!==FALSE) {
			if ($data=$this->input->post(NULL, TRUE)) {
				if ($this->UsersModel->update($data,$this->uri->segment(4))) {
					$this->notifications->setMessage($this->lang->line("user_updated_successfully"));
				}
				redirect($_SERVER['HTTP_REFERER']);
			}		
			$this->view_data['item']=$this->UsersModel->getItem($this->uri->segment(4));
			$this->view_data['all_roles']=$this->RolesModel->getItems();	
			if ($this->view_data['item']===false) {
				$this->load->view('errors/notfound',$this->view_data);
			} else {
				$this->view_data['updateown']=false;
				$this->load->view('usersview/update',$this->view_data);
			}
		} else {
			$this->load->view('errors/wrongparameters',$this->view_data);
		}
	}	
	
	public function updateown(){
		$this->load->model('UsersModel');
		$this->load->model('RolesModel');		
		if ($this->uri->segment(4)!==FALSE) {
			if ($data=$this->input->post(NULL, TRUE)) {
				if ($this->UsersModel->update($data,$this->uri->segment(4))) {
					$this->notifications->setMessage($this->lang->line("user_updated_successfully"));
				}
				redirect($_SERVER['HTTP_REFERER']);
			}		
			$this->view_data['item']=$this->UsersModel->getItem($this->uri->segment(4));
			$this->view_data['all_roles']=$this->RolesModel->getItems();	
			if ($this->view_data['item']===false) {
				$this->load->view('errors/notfound',$this->view_data);
			} else {
				$this->view_data['updateown']=true;
				$this->load->view('usersview/update',$this->view_data);
			}
		} else {
			$this->load->view('errors/wrongparameters',$this->view_data);
		}
	}
	
	public function delete(){
		if ($this->uri->segment(4)!==FALSE) {
			$this->load->model('UsersModel');
			if ($this->UsersModel->delete($this->uri->segment(4))) {
				$this->notifications->setMessage($this->lang->line("user_deleted_successfully"));
			}			
		} else {
			$this->notifications->setError($this->lang->line("wrong_parameters"));
		}
		redirect($_SERVER['HTTP_REFERER']);
	}	
	
}