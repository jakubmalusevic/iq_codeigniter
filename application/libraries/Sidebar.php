<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sidebar Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sidebar
 * @author		IQDesk
 * @link		http://iqdesk.net
 */
class CI_Sidebar {

	private $CI;
	var $sidebars=array();

	public function __construct() {
		$this->CI=& get_instance();
		log_message("debug", "Sidebar Class Initialized");
	}
	
	public function register($params=array()){
		if (isset($params['name'])) {
			if (trim($params['name'])!="") {
				$sidebar=array(
					"title"=>isset($params['title'])?$params['title']:"",
					"position"=>isset($params['position'])?$params['position']:"",
					"is_filter"=>isset($params['is_filter'])?$params['is_filter']:false,
					"filter_action"=>isset($params['filter_action'])?$params['filter_action']:"",
					"filter_method"=>isset($params['filter_method'])?$params['filter_method']:"get",
					"submit_button"=>isset($params['submit_button'])?$params['submit_button']:"",
					"reset_button"=>isset($params['reset_button'])?$params['reset_button']:"",
					"filter_event"=>isset($params['filter_event'])?$params['filter_event']:"",
					"elements"=>isset($params['elements'])?$params['elements']:""
				);
				if (count($sidebar['elements'])>0) {
					foreach($sidebar['elements'] as $i=>&$element) {
						if (!isset($element['type'])) $element['type']="text";
						if (!isset($element['name'])) $element['name']="field_".$i;
						if (!isset($element['placeholder'])) $element['placeholder']="";
						if (!isset($element['options'])) $element['options']=array();
						if (!isset($element['default_value'])) $element['default_value']="";
					}
				}
				$this->sidebars[$params['name']]=$sidebar;
			}
		}
	}
	
	public function renderSidebar($name,$return_html=false){
		$output="";
		$name=trim($name);
		if ($name!="") {
			if (isset($this->sidebars[$name])) {
				$output=$this->CI->load->view('libraries/sidebar',array("sidebar"=>$this->sidebars[$name],"name"=>$name),true);
			}
		}
		if ($return_html) return $output;
		else echo $output;
		return false;
	}
	
}
// END Sidebar Class

/* End of file Sidebar.php */
/* Location: ./application/libraries/Sidebar.php */