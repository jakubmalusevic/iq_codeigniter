<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Listing extends MX_Controller {
	
	var $check_permissions=true;

	public function index(){							
		$this->view_data['page_title']=$this->lang->line("log");
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
		$this->view_data['items']=$this->SystemLog->getItems($params,$sorting,$page);
		$total_items=$this->SystemLog->total_count;
		$this->pagination->setNumbers(count($this->view_data['items']),$total_items);
		$type_options=array(
			array(
				"value"=>"",
				"label"=>$this->lang->line("all_types_of_records")
			),
			array(
				"value"=>1,
				"label"=>$this->lang->line("create_type")
			),
			array(
				"value"=>2,
				"label"=>$this->lang->line("update_type")
			),
			array(
				"value"=>3,
				"label"=>$this->lang->line("delete_type")
			)
		);
		$made_by_options=array(
			array(
				"value"=>"",
				"label"=>$this->lang->line("all_users")
			)
		);
		$this->load->model('users/UsersModel');
		$all_users=$this->UsersModel->getItems();
		foreach($all_users as $user){
			$made_by_options[]=array("value"=>$user->id,"label"=>$user->full_name);
		}
		$sidebar_params=array(
			"name"=>"left-sidebar",
			"title"=>$this->lang->line("filters"),
			"position"=>"left",
			"is_filter"=>true,
			"filter_action"=>base_url()."log/listing/index",
			"submit_button"=>$this->lang->line("apply_filters"),
			"reset_button"=>$this->lang->line("reset_filters"),
			"filter_event"=>"LogFilterFormRow",
			"elements"=>array(
				array(
					"type"=>"text",
					"name"=>"description",
					"placeholder"=>$this->lang->line("search_record")
				),
				array(
					"type"=>"select",
					"name"=>"log_type",
					"options"=>$type_options
				),
				array(
					"type"=>"select",
					"name"=>"made_by",
					"options"=>$made_by_options
				),
				array(
					"type"=>"datepicker",
					"name"=>"period_from",
					"placeholder"=>$this->lang->line("enter_period_from")
				),
				array(
					"type"=>"datepicker",
					"name"=>"period_to",
					"placeholder"=>$this->lang->line("enter_period_to")
				)
			)
		);
		$this->sidebar->register($sidebar_params);	
		$this->load->view('general/header',$this->view_data);
		$this->load->view('listing/index',$this->view_data);
		$this->load->view('general/footer',$this->view_data);
	}
	
	public function delete(){
		if ($this->uri->segment(4)!==FALSE) {
			if ($this->SystemLog->delete($this->uri->segment(4))) {
				$this->notifications->setMessage($this->lang->line("log_record_deleted_successfully"));
			}			
		} else {
			$this->notifications->setError($this->lang->line("wrong_parameters"));
		}
		redirect($_SERVER['HTTP_REFERER']);
	}	
	
	public function clear(){
		$this->SystemLog->clear();
		$this->notifications->setMessage($this->lang->line("system_log_cleared_successfully"));
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function batchdelete(){
		if ($data=$this->input->post(NULL, TRUE)) {
			if (isset($data['ids'])) {
				if (count($data['ids'])>0) {
					if ($this->SystemLog->batchDelete($data['ids'])) {
						$this->notifications->setMessage($this->SystemLog->deleted_records." ".$this->lang->line("log_records_deleted_successfully"));
					}
					redirect($_SERVER['HTTP_REFERER']);
				} else {
					$this->load->view('errors/wrongparameters',$this->view_data);
				}
			} else {
				$this->load->view('errors/wrongparameters',$this->view_data);
			}
		} else {
			$this->load->view('errors/wrongparameters',$this->view_data);
		}
	}
	
}