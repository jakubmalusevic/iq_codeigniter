<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Js extends MX_Controller {
	
	var $check_permissions=false;

	public function language() {
		$get=$this->input->get(NULL, TRUE, TRUE);
		if (isset($get['module'])) {
			$this->lang->appendModuleLanguage($get['module']);	
		}
		$this->output->set_content_type("application/javascript");
		$this->load->view('js/language',$this->view_data);		
	}
}