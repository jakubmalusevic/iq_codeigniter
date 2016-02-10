<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Listing extends MX_Controller {
	
	var $check_permissions=true;

	public function index(){							
		$this->view_data['page_title']=$this->lang->line("modules");		
		$this->load->model('ModulesModel');
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
		$this->view_data['items']=$this->ModulesModel->getItems($params,$sorting,$page);
		$total_items=$this->ModulesModel->total_count;
		$this->pagination->setNumbers(count($this->view_data['items']),$total_items);
		$enabled_options=array(
			array("value"=>"","label"=>$this->lang->line("module_state")),
			array("value"=>"1","label"=>$this->lang->line("enabled")),
			array("value"=>"0","label"=>$this->lang->line("disabled"))
		);
		$sidebar_params=array(
			"name"=>"left-sidebar",
			"title"=>$this->lang->line("filters"),
			"position"=>"left",
			"is_filter"=>true,
			"filter_action"=>base_url()."modules/listing/index",
			"submit_button"=>$this->lang->line("apply_filters"),
			"reset_button"=>$this->lang->line("reset_filters"),
			"filter_event"=>"ModulesFilterFormRow",
			"elements"=>array(
				array(
					"type"=>"text",
					"name"=>"module_name",
					"placeholder"=>$this->lang->line("enter_name")
				),
				array(
					"type"=>"text",
					"name"=>"author_name",
					"placeholder"=>$this->lang->line("enter_author")
				),
				array(
					"type"=>"select",
					"name"=>"state",
					"options"=>$enabled_options
				)
			)
		);
		$this->sidebar->register($sidebar_params);	
		$this->load->model('ShopApiModel');
		$this->view_data['versions_info']=$this->ShopApiModel->getVersionsInfo();
		$this->load->view('general/header',$this->view_data);
		$this->load->view('listing/index',$this->view_data);
		$this->load->view('general/footer',$this->view_data);
	}
	
	public function refresh(){
		$this->load->model('ModulesModel');
		if ($this->ModulesModel->refreshModules()) {
			if ($this->ModulesModel->refreshed) {
				$this->notifications->setMessage($this->lang->line("modules_refreshed_successfully"));
				$this->notifications->setMessage($this->lang->line("dont_forget_check_roles"));
			} else {
				$this->notifications->setMessage($this->lang->line("no_modules_updated"));
			}
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function view(){
		if ($this->uri->segment(4)!==FALSE) {
			$this->load->model('ModulesModel');
			$this->view_data['item']=$this->ModulesModel->getItem($this->uri->segment(4));
			if ($this->view_data['item']!==false) {
				$this->load->view('listing/view',$this->view_data);
			} else {
				$this->load->view('errors/notfound',$this->view_data);
			}
		} else {
			$this->load->view('errors/wrongparameters',$this->view_data);
		}		
	}
	
	public function install(){
		if ($data=$this->input->post(NULL, TRUE)) {
			$this->load->model('ModulesModel');
			if ($this->ModulesModel->installModule()) {
				if (!$this->ModulesModel->updated) $this->notifications->setMessage($this->lang->line("module_installed_successfully"));
				else $this->notifications->setMessage($this->lang->line("module_updated_successfully"));
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
		$this->load->view('listing/install',$this->view_data);
	}	
	
	public function delete(){
		if ($this->uri->segment(4)!==FALSE) {
			$this->load->model('ModulesModel');
			if ($this->ModulesModel->deleteModule($this->uri->segment(4))) {		
				$this->notifications->setMessage($this->lang->line("module_deleted_successfully"));
			}
		} else {
			$this->notifications->setError($this->lang->line("record_not_found"));
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function saveorder(){
		if ($data=$this->input->post(NULL, TRUE)) {
			if (isset($data['data'])) {
				if (count($data['data'])>0) {
					$this->load->model('ModulesModel');
					$this->ModulesModel->saveOrder($data['data']);
				}
			}
		}		
	}
	
	public function changestate(){
		if ($this->uri->segment(4)!==FALSE) {
			$get=$this->input->get(NULL, TRUE, TRUE);
			if (isset($get['state'])) {
				if ($get['state']==0 || $get['state']==1) {
					$this->load->model('ModulesModel');
					if ($this->ModulesModel->changeState($this->uri->segment(4),$get['state'])) {	
						if ($get['state']==1) {
							$this->notifications->setMessage($this->lang->line("module_enabled_successfully"));
						}
						if ($get['state']==0) {
							$this->notifications->setMessage($this->lang->line("module_disabled_successfully"));
						}						
					}					
				} else {
					$this->notifications->setError($this->lang->line("wrong_parameters"));
				}
			} else {
				$this->notifications->setError($this->lang->line("wrong_parameters"));
			}
		} else {
			$this->notifications->setError($this->lang->line("record_not_found"));
		}
		redirect($_SERVER['HTTP_REFERER']);
	}	
	
}