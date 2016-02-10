<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DashboardCatcher {

	private $CI;

	function __construct() {
		$this->CI=& get_instance();
	}

	public function onBeforeControllerInitiated($module,$controller,$action){
		$this->CI->load->model("dashboard/Dashboard");
	}
    
    public function onInitWidgets(){
    	$params=array(
    		"name"=>"clock",
			"title"=>$this->CI->lang->line("current_server_time"),
			"html"=>$this->CI->load->view("dashboard/widget/clock",array(),true),
			"size"=>"1-column"
    	);
    	$this->CI->Dashboard->registerWidget($params);
    	$params=array(
    		"name"=>"developer_contacts",
			"title"=>$this->CI->lang->line("developer_contacts"),
			"html"=>$this->CI->load->view("dashboard/widget/developer_contacts",array(),true),
			"size"=>"1-column"
    	);
    	$this->CI->Dashboard->registerWidget($params);   
    	
    	$widget_welcome_text=$this->CI->GlobalSettings->getValue("dashboard","widget_welcome_text",""); 	
    	if ($widget_welcome_text!="") {
			$params=array(
				"name"=>"dashboard_welcome_text",
				"title"=>$this->_replaceShortcodes($this->CI->lang->line("widget_welcome_header")),
				"html"=>$this->CI->load->view("dashboard/widget/welcome_text",array("text"=>$this->_replaceShortcodes($widget_welcome_text)),true),
				"size"=>"1-column"
			);
			$this->CI->Dashboard->registerWidget($params);    
		}
    }
    
    protected function _replaceShortcodes($input){
    	$output=$input;
    	if ($this->CI->session->userdata("user_id")>0) {
			$this->CI->db->select("*");
			$this->CI->db->from("users");
			$this->CI->db->where("id",$this->CI->session->userdata("user_id"));
			$query=$this->CI->db->get();
			$results=$query->result();  
			if (count($results)>0) {
				$shortcodes=array(
					"[username]"=>$results[0]->username,
					"[full name]"=>$results[0]->full_name,
					"[email]"=>$results[0]->email
				);
				foreach($shortcodes as $replace_what=>$replace_for) {
					$output=str_replace($replace_what,$replace_for,$output);
				}
			}
		}
		return $output;
    }
    
    public function onBeforeDeleteUser($user_id){
		$this->CI->db->where("user_id",$user_id);
		$this->CI->db->delete("dashboard_widgets");		
	}
	
	public function onRegisterSettings(){
		$this->CI->GlobalSettings->registerSettingsSection("dashboard","Dashboard");
		$this->CI->GlobalSettings->registerSetting("dashboard","widget_welcome_text","Welcome Widget Text<br/><small style='color:#999;'>Allowed shortcodes (related to current user): [username], [full name] and [email]</small>","Dear user, you have logged in with following accont details:\nUsername: [username]\nFull name: [full name]\nEmail: [email]");
	}	
    
}
?>