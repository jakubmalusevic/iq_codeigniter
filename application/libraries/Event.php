<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Event Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Event
 * @author		IQDesk
 * @link		http://iqdesk.net
 */
class CI_Event {

	private $CI;
	public $defined_catchers=array();
	public $defined_events=array();

	public function __construct() {
		$this->CI=& get_instance();
		$this->_defineCatchers();
		log_message("debug", "Sidebar Class Initialized");
	}
	
	private function _defineCatchers(){
		$this->CI->db->select("name");
		$this->CI->db->from("modules");
		$this->CI->db->where("state",1);
		$query=$this->CI->db->get();
		$modules=$query->result();
		$modules_dir=dirname(__FILE__)."/../../modules/";
		for($i=0;$i<count($modules);$i++) {
			$modules[$i]->name=strtolower($modules[$i]->name);
			if (is_dir($modules_dir.$modules[$i]->name)) {
				$events_dir=$modules_dir.$modules[$i]->name."/events/";
				if (is_dir($events_dir)) {
					$events_file=$events_dir.$modules[$i]->name.".php";
					if (file_exists($events_file)) {
						include_once($events_file);
						$class_name=ucfirst($modules[$i]->name)."Catcher";
						if (class_exists($class_name)) {
							$methods=get_class_methods($class_name);
							$vars=get_class_vars($class_name);
							if (count($methods)>0) {
								foreach($methods as $method) {
									if (mb_strtolower(mb_substr($method,0,2))=="on") {
										$event_name=mb_strtolower(mb_substr($method,2,mb_strlen($method)-1));
										if (!isset($this->defined_catchers[$event_name])) {
											$this->defined_catchers[$event_name]=array();
										}
										if (!in_array($modules[$i]->name,$this->defined_catchers[$event_name])) {
											$define_value=new stdClass;
											$define_value->module=$modules[$i]->name;
											$define_value->priority=isset($vars['priority'][$method])?$vars['priority'][$method]:100;
											$this->defined_catchers[$event_name][]=$define_value;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		foreach($this->defined_catchers as $catcher_key=>$catcher) {
			usort($this->defined_catchers[$catcher_key],array($this,'_sort_catchers'));
		}
		return false;
	}
	
	private function _sort_catchers($a,$b){
		if ($a->priority>$b->priority) return 1;
		if ($a->priority==$b->priority) return 0;
		if ($a->priority<$b->priority) return -1;
	}
	
	public function register($arg1=null,&$arg2=null,&$arg3=null,&$arg4=null,&$arg5=null,&$arg6=null,&$arg7=null,&$arg8=null,&$arg9=null,&$arg10=null,&$arg11=null){
		if ($arg1=="AfterHeadHTML") {
			$this->CI->theme->_includeThemeAdditionalFiles();
		}
		$args=array();
		$params=array();
		for($i=1;$i<=11;$i++){
			$var_name="arg".$i;
			if (!is_null(${$var_name})) {
				$args[]=&${$var_name};
				if ($i>1) $params[]=&${$var_name};
			}
		}
		$trace=debug_backtrace();
		foreach($trace[0]['args'] as &$arg) $args[]=&$arg;
		$modules_dir=dirname(__FILE__)."/../../modules/";
		if (count($args)>0) {
			if (trim($args[0])!="") {
				$event_name=trim(mb_strtolower($args[0]));
				if (!is_array($this->defined_events)) $this->defined_events=array();
				if (!in_array($event_name,$this->defined_events)) {
					$this->defined_events[]=$event_name;
				}
				if (isset($this->defined_catchers[$event_name])) {
					if (count($this->defined_catchers[$event_name])>0) {						
						foreach($this->defined_catchers[$event_name] as $catcher) {
							if (file_exists($modules_dir.$catcher->module."/events/".$catcher->module.".php")) {
								include_once($modules_dir.$catcher->module."/events/".$catcher->module.".php");
								$class_name=ucfirst($catcher->module)."Catcher";
								$temp_catcher=new $class_name;
								call_user_func_array(array($temp_catcher,"on".$event_name),$params);						
								unset($temp_catcher);
							}
						}
					}
				}				
			}
		}
		return false;
	}
	
}
// END Event Class

/* End of file Event.php */
/* Location: ./application/libraries/Event.php */