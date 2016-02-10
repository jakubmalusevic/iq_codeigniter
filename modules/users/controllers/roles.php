<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Roles extends MX_Controller {
	
	var $check_permissions=true;

	public function index(){			
		$this->view_data['page_title']=$this->lang->line("users_and_roles").$this->theme->page_title_delimiter.$this->lang->line("roles");
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
		$this->view_data['items']=$this->RolesModel->getItems($params,$sorting,$page);
		$total_items=$this->RolesModel->total_count;
		$this->pagination->setNumbers(count($this->view_data['items']),$total_items);
		$sidebar_params=array(
			"name"=>"left-sidebar",
			"title"=>$this->lang->line("filters"),
			"position"=>"left",
			"is_filter"=>true,
			"filter_action"=>base_url()."users/roles/index",
			"submit_button"=>$this->lang->line("apply_filters"),
			"reset_button"=>$this->lang->line("reset_filters"),
			"filter_event"=>"RolesFilterFormRow",
			"elements"=>array(
				array(
					"type"=>"text",
					"name"=>"role_name",
					"placeholder"=>$this->lang->line("enter_role_name")
				)
			)
		);		
		$this->sidebar->register($sidebar_params);		
		$this->load->view('general/header',$this->view_data);
		$this->load->view('rolesview/index',$this->view_data);
		$this->load->view('general/footer',$this->view_data);
	}	
	
	public function create(){
		$this->load->model('RolesModel');	
		$this->load->model('modules/ModulesModel');
		if ($data=$this->input->post(NULL, TRUE)) {
			if ($this->RolesModel->create($data)) {
				$this->notifications->setMessage($this->lang->line("role_created_successfully"));
			}
			redirect($_SERVER['HTTP_REFERER']);
		}	
		$this->view_data['modules']=$this->ModulesModel->getItems();
		$this->view_data['all_roles']=$this->RolesModel->getItems();
		$this->view_data['copy']=false;		
		if ($this->uri->segment(4)!==FALSE) {
			$this->view_data['item']=$this->RolesModel->getItem($this->uri->segment(4));
			if ($this->view_data['item']!==false) {
				$this->view_data['copy']=true;
			}
		}
		$this->load->view('rolesview/create',$this->view_data);
	}	
	
	public function update(){
		$this->load->model('RolesModel');
		$this->load->model('modules/ModulesModel');		
		if ($this->uri->segment(4)!==FALSE) {
			if ($data=$this->input->post(NULL, TRUE)) {
				if ($this->RolesModel->update($data,$this->uri->segment(4))) {
					$this->notifications->setMessage($this->lang->line("role_updated_successfully"));
				}
				redirect($_SERVER['HTTP_REFERER']);
			}		
			$this->view_data['item']=$this->RolesModel->getItem($this->uri->segment(4));
			$this->view_data['modules']=$this->ModulesModel->getItems();
			if ($this->view_data['item']===false) {
				$this->load->view('errors/notfound',$this->view_data);
			} else {
				$this->load->view('rolesview/update',$this->view_data);
			}
		} else {
			$this->load->view('errors/wrongparameters',$this->view_data);
		}
	}	
	
	public function delete(){
		if ($this->uri->segment(4)!==FALSE) {
			$this->load->model('RolesModel');
			if ($this->RolesModel->delete($this->uri->segment(4))) {
				$this->notifications->setMessage($this->lang->line("role_deleted_successfully"));
			}			
		} else {
			$this->notifications->setError($this->lang->line("record_not_found"));
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
}