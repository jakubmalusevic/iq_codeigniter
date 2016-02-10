<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helpers extends MX_Controller {
	
	var $check_permissions=false;

	public function drawNavigation() {
		$this->view_data['active_module']=$_POST['active_module'];
		$this->load->view('helpers/navigation',$this->view_data);		
	}
}