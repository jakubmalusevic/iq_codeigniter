<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MX_Controller {
	
	var $check_permissions=true;

	public function index(){			
		$this->event->register("InitWidgets");				
		$this->view_data['page_title']=$this->lang->line("dashboard");
		$this->view_data['items']=$this->Dashboard->getWidgets();
		$this->load->view('general/header',$this->view_data);
		$this->load->view('main/index',$this->view_data);
		$this->load->view('general/footer',$this->view_data);
	}
	
	public function rearrange(){
		if ($data=$this->input->post(NULL, TRUE)) {
			if (isset($data['data'])) {
				if (count($data['data'])>0) {
					$this->Dashboard->saveWidgetsState($data['data']);
				}
			}
		}		
	}
	
	public function changestate(){
		if ($data=$this->input->post(NULL, TRUE)) {
			$this->Dashboard->saveWidgetState($data);
		} else {
			$this->event->register("InitWidgets");		
			$this->view_data['items']=$this->Dashboard->getWidgets();	
			$this->load->view('main/changestate',$this->view_data);
		}	
	}
	
}