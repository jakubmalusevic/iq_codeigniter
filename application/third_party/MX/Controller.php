<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/** load the CI class for Modular Extensions **/
require dirname(__FILE__).'/Base.php';

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library replaces the CodeIgniter Controller class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Controller.php
 *
 * @copyright	Copyright (c) 2011 Wiredesignz
 * @version 	5.4
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
class MX_Controller 
{
	public $autoload = array();
	public $check_permissions=false;
	public $redirect_on_failed_permissions="";
	public $view_data=array();
	
	public function __construct() 
	{
		$module=CI::$APP->router->fetch_module();
		$controller=$this->router->class;
		$action=$this->router->method;
		$this->event->register("BeforeControllerInitiated",$module,$controller,$action);
		$class = str_replace(CI::$APP->config->item('controller_suffix'), '', get_class($this));
		log_message('debug', $class." MX_Controller Initialized");
		Modules::$registry[strtolower($class)] = $this;	
		
		/* copy a loader instance and initialize */
		$this->load = clone load_class('Loader');
		$this->load->initialize($this);	
		
		/* autoload module items */
		$this->load->_autoloader($this->autoload);
		$this->lang->load("main");
		$module=CI::$APP->router->fetch_module();
		$this->lang->appendModuleLanguage($module);		
		$this->SystemUser->verify();
		$user_role=$this->session->userdata($this->acl->session_variable_name);
		if ($user_role=="") $user_role=$this->acl->default_role;
		$this->view_data['page_title']="";
		$this->event->register("BeforeCheckPermissions",$module,$controller,$action);
		if ($this->check_permissions) {
			$this->checkPermissions();
		}
		$this->event->register("AfterCheckPermissions",$module,$controller,$action);
		$this->checkModuleEnabled($module);
		$this->event->register("AfterControllerInitiated",$module,$controller,$action);
	}
	
	public function __get($class) {
		return CI::$APP->$class;
	}
	
	public function checkPermissions($module="",$controller="",$action=""){
		$module=$module!=""?$module:(CI::$APP->router->fetch_module());
		$controller=$controller!=""?$controller:($this->router->class);
		$action=$action!=""?$action:($this->router->method);	
    	if (!$this->acl->checkPermissions($module,$controller,$action)){
    		$this->notifications->setError($this->lang->line("error_access_denied"));
    		$user_role=$this->session->userdata($this->acl->session_variable_name);
    		if ($user_role=="") $user_role=$this->acl->default_role;
    		if(!$this->input->is_ajax_request()) {
    			if ($user_role!=$this->acl->default_role) {
    				redirect($this->SystemUser->getPrimaryRedirection());
    			} else {
    				redirect($this->acl->redirect_on_access_denied);
    			}
    		} else redirect($this->acl->redirect_on_ajax_access_denied);    		
    	}		
	}
	
	public function checkModuleEnabled($module=""){
		$module=$module!=""?$module:(CI::$APP->router->fetch_module());
		$module=strtolower($module);
		if ($module!="") {
			$this->db->select("id");
			$this->db->from("modules");
			$this->db->where("name",$module);
			$this->db->where("state",1);
			$query=$this->db->get();
			$results=$query->result();
			if (count($results)==0) {
				$this->notifications->setError($this->lang->line("module_disabled"));
				redirect($this->SystemUser->getPrimaryRedirection());
			}
		}
	}

}