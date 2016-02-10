<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Listing extends MX_Controller {
	
	var $check_permissions=true;

	public function index(){							
		$this->view_data['page_title']=$this->lang->line("settings");
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
		$this->load->model('SettingsModel');
		$this->view_data['items']=$this->SettingsModel->getItems($params,$sorting,$page);
		$total_items=$this->SettingsModel->total_count;
		$this->pagination->setNumbers(count($this->view_data['items']),$total_items);
		$settings_sections_options=array(array("value"=>"","label"=>$this->lang->line("all_sections")));
		$sections=$this->GlobalSettings->getSettingsSections();
		for($i=0;$i<count($sections);$i++){
			$lang_title="";
			if ($sections[$i]->module!="") {
				$lang_title=$this->language->getModuleLanguageLine($sections[$i]->module,"_settings_section_".$sections[$i]->name);
			}
			$settings_sections_options[]=array(
				"value"=>$sections[$i]->id,
				"label"=>$lang_title!=""?$lang_title:$sections[$i]->title
			);
		}
		$sidebar_params=array(
			"name"=>"left-sidebar",
			"title"=>$this->lang->line("filters"),
			"position"=>"left",
			"is_filter"=>true,
			"filter_action"=>base_url()."settings/listing/index",
			"submit_button"=>$this->lang->line("apply_filters"),
			"reset_button"=>$this->lang->line("reset_filters"),
			"filter_event"=>"SettingsFilterFormRow",
			"elements"=>array(
				array(
					"type"=>"text",
					"name"=>"name",
					"placeholder"=>$this->lang->line("enter_setting_name")
				),
				array(
					"type"=>"text",
					"name"=>"value",
					"placeholder"=>$this->lang->line("enter_setting_value")
				),
				array(
					"type"=>"select",
					"name"=>"section_id",
					"options"=>$settings_sections_options
				)
			)
		);
		$this->sidebar->register($sidebar_params);	
		$this->load->view('general/header',$this->view_data);
		$this->load->view('listing/index',$this->view_data);
		$this->load->view('general/footer',$this->view_data);
	}
	
	public function update(){
		$this->load->model('SettingsModel');
		if ($this->uri->segment(4)!==FALSE) {
			if ($data=$this->input->post(NULL, TRUE)) {
				if ($this->SettingsModel->update($_POST,$this->uri->segment(4))) {
					$this->notifications->setMessage($this->lang->line("setting_updated_successfully"));
				}
				redirect($_SERVER['HTTP_REFERER']);
			}		
			$this->view_data['item']=$this->SettingsModel->getItem($this->uri->segment(4));	
			if ($this->view_data['item']===false) {
				$this->load->view('errors/notfound',$this->view_data);
			} else {
				$this->load->view('listing/update',$this->view_data);
			}
		} else {
			$this->load->view('errors/wrongparameters',$this->view_data);
		}
	}		


	public function reset(){
		$this->load->model('SettingsModel');
		if ($this->SettingsModel->resetGlobalToDefault()) {
			$this->notifications->setMessage($this->lang->line("global_settings_resetted_to_default_successfully"));
		}		
		redirect($_SERVER['HTTP_REFERER']);
	}
	
}