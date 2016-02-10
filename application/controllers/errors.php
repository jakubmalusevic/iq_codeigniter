<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Errors extends MX_Controller {
	
	var $check_permissions=false;

	public function access_denied() {
		if (!isset($this->view_data)) $this->view_data=array();
		if(!$this->input->is_ajax_request()){
			$this->load->view('header',$this->view_data);
		}
		$this->load->view('errors/accessdenied',$this->view_data);
		if(!$this->input->is_ajax_request()){
			$this->load->view('footer',$this->view_data);
		}		
	}
}