<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Navigation Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Navigation
 * @author		IQDesk
 * @link		http://iqdesk.net
 */
class CI_Navigation {

	private $CI;

	public function __construct() {
		$this->CI=& get_instance();
		log_message("debug", "Navigation Class Initialized");
	}
	
	public function drawNavigation($return_html=false,$exclude_wrapper=false,$active_module=""){
		$output="";
		$items=array();
		$modules=$this->getModules();
		for($i=0;$i<count($modules);$i++){
			if ($modules[$i]->state==1) {
				if ($this->CI->acl->checkNavigationItem($modules[$i]->name,$modules[$i]->primary_navigation_item_section,$modules[$i]->primary_navigation_item_action)) {
					$item=new stdClass;
					$module_title=$this->CI->language->getModuleTitle($modules[$i]->name);
					$item->title=$module_title==""?$modules[$i]->title:$module_title;
					$item->icon=$this->CI->theme->getModuleIcon($modules[$i]->name);
					$item->icon_hovered=$this->CI->theme->getModuleIcon($modules[$i]->name,true);
					$item->module=$modules[$i]->name;
					$item->controller=$modules[$i]->primary_navigation_item_section;
					$item->action=$modules[$i]->primary_navigation_item_action;
					$item->is_active=false;
					if ($active_module!="" && $active_module==strtolower($modules[$i]->name)) {
						$item->is_active=true;
					}
					if (strtolower($this->CI->router->uri->segments[1])==strtolower($modules[$i]->name)) $item->is_active=true;
					$items[]=$item;
				} else {
					$sections=unserialize($modules[$i]->sections);
					$primary_actions=$this->getModulePrimaryActions($sections);			
					for($pa=0;$pa<count($primary_actions);$pa++){
						if ($this->CI->acl->checkNavigationItem($modules[$i]->name,$primary_actions[$pa]->section,$primary_actions[$pa]->action)) {
							$item=new stdClass;
							$module_title=$this->CI->language->getModuleTitle($modules[$i]->name);
							$item->title=$module_title==""?$modules[$i]->title:$module_title;
							$item->icon=$this->CI->theme->getModuleIcon($modules[$i]->name);
							$item->icon_hovered=$this->CI->theme->getModuleIcon($modules[$i]->name,true);						
							$item->module=$modules[$i]->name;
							$item->controller=$primary_actions[$pa]->section;
							$item->action=$primary_actions[$pa]->action;
							$item->is_active=false;
							if ($active_module!="" && $active_module==strtolower($modules[$i]->name)) {
								$item->is_active=true;
							}						
							if (strtolower($this->CI->router->uri->segments[1])==strtolower($modules[$i]->name)) $item->is_active=true;						
							$items[]=$item;
							break;
						}
					}
				}
			}
		}
		if ($this->CI->theme->logout_in_navigation) {
			$item=new stdClass;
			$item->icon=$this->CI->theme->getLogoutIcon();
			$item->icon_hovered=$this->CI->theme->getLogoutIcon(true);			
			$item->module="users";
			$item->controller="publicaccess";
			$item->action="logout";
			$item->is_active=false;	
			$item->title=$this->CI->lang->line("logout");
			$items[]=$item;		
		}
		$output=$this->CI->load->view('libraries/navigation',array("items"=>$items,"exclude_wrapper"=>$exclude_wrapper),true);
		if ($return_html) return $output;
		else echo $output;
		return false;
	}
	
	public function getModulePrimaryActions($sections){
		$return=array();
		for($s=0;$s<count($sections);$s++){
			if (count($sections[$s]->actions)>0) {
				$item=new stdClass;
				$item->section=$sections[$s]->name;
				$item->action=$sections[$s]->actions[0]->name;
				$return[]=$item;
			}
		}
		return $return;
	}
	
	public function getModules(){
		$this->CI->db->select("*");
		$this->CI->db->from("modules");
		$this->CI->db->where("state",1);
		$this->CI->db->order_by("order","asc");
		$query=$this->CI->db->get();
		$modules=$query->result();
		return $modules;	
	}
	
}
// END Navigation Class

/* End of file Navigation.php */
/* Location: ./application/libraries/Navigation.php */