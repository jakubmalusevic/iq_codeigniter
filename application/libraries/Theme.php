<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Theme Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Theme
 * @author		IQDesk
 * @link		http://iqdesk.net
 */
class CI_Theme {

	private $CI;
	var $main_title="";
	var $direction="ltr";
	var $color_scheme="";
	var $page_title_delimiter="";
	var $logout_in_navigation=false;

	public function __construct() {
		$this->CI=& get_instance();
		$theme_config_loaded=false;
		if (file_exists(APPPATH."config/".ENVIRONMENT."/theme.php")) {
			require(APPPATH."config/".ENVIRONMENT."/theme.php");
			$theme_config_loaded=true;
		} else {
			if (file_exists(APPPATH."config/theme.php")) {
				require(APPPATH."config/theme.php");
				$theme_config_loaded=true;
			} else {
				echo "Theme config file is not found.";
			}			
		}		
		if ($theme_config_loaded) {
			$this->main_title=$theme['main_title'];
			if ($this->CI->lang->line("main_title")!="") $this->main_title=$this->CI->lang->line("main_title");
			$this->color_scheme=$theme['color_scheme'];
			$this->page_title_delimiter=$theme['page_title_delimiter'];
			$this->logout_in_navigation=$theme['logout_in_navigation'];
		}
		if ($this->CI->lang->line("_lang_direction")!="") $this->direction=$this->CI->lang->line("_lang_direction");
		log_message("debug", "Theme Class Initialized");
	}
	
	public function getModuleIcon($module="",$hover=false){
		$return="";
		$module=strtolower(trim($module));
		$filename="nav_icon".($hover?"_hover":"").".png";
		if ($module!="" && $return=="") {
			$assets_dir=BASEPATH."/../modules/".$module."/assets/";
			if (is_dir($assets_dir."themes/".$this->color_scheme."/")) {
				if (file_exists($assets_dir."themes/".$this->color_scheme."/".$filename)) {
					$return=base_url()."modules/".$module."/assets/themes/".$this->color_scheme."/".$filename;
				}				
			}
		}
		if ($module!="" && $return=="") {
			$icons_dir=BASEPATH."/../assets/themes/".$this->color_scheme."/images/modules-icons/".$module."/";
			if (is_dir($icons_dir)) {
				if (file_exists($icons_dir.$filename)) {
					$return=base_url()."assets/themes/".$this->color_scheme."/images/modules-icons/".$module."/".$filename;
				}				
			}
		}
		if ($module!="" && $return=="") {
			$assets_dir=BASEPATH."/../modules/".$module."/assets/";
			if (is_dir($assets_dir)) {
				if (file_exists($assets_dir.$filename)) {
					$return=base_url()."modules/".$module."/assets/".$filename;
				}
			}
		}
		if ($return=="") {
			$return=base_url()."assets/images/no-module-icon.png";
		}
		return $return;
	}
	
	public function getLogoutIcon($hover=false){
		$return="";
		$filename="logout".($hover?"_hover":"").".png";
		$assets_dir=BASEPATH."/../assets/images/";
		$theme_dir=BASEPATH."/../assets/themes/".$this->color_scheme."/images/";
		if (is_dir($assets_dir)) {
			if (file_exists($assets_dir.$filename)) {
				$return=base_url()."assets/images/".$filename;
			}
			if (is_dir($assets_dir)) {
				if (file_exists($assets_dir.$filename)) {
					$return=base_url()."assets/themes/".$this->color_scheme."/images/".$filename;
				}				
			}
		}
		if ($return=="") {
			$return=base_url()."assets/images/no-module-icon.png";
		}
		return $return;
	}	

	public function _includeThemeAdditionalFiles(){
		if ($this->direction=="rtl") {
			$rtl_stylesheet=BASEPATH."/../assets/themes/".$this->color_scheme."/theme.rtl.css";
			if (file_exists($rtl_stylesheet)) {
				echo "<link href=\"".base_url()."assets/themes/".$this->color_scheme."/theme.rtl.css\" rel=\"stylesheet\" type=\"text/css\" />";
			}
		}
		$header_file=$this->getThemePath()."header.php";
		if (file_exists($header_file)) {
			include_once($header_file);
			if (function_exists("includeStylesheet")) {
				$items=includeStylesheet($this->CI);
				if (is_array($items)) {
					foreach($items as $item){
						echo "<link href=\"".$item."\" rel=\"stylesheet\" type=\"text/css\" />";
					}
				}
			}
			if (function_exists("includeScript")) {
				$items=includeScript($this->CI);
				if (is_array($items)) {
					foreach($items as $item){
						echo "<script src=\"".$item."\" type=\"text/javascript\"></script>";
					}
				}
			}			
			if (function_exists("includeCustomScript")) {
				$items=includeCustomScript($this->CI);
				if (is_array($items)) {
					foreach($items as $item){
						echo "<script type=\"text/javascript\">".$item."</script>";
					}
				}
			}			
		}

	}

	public function getThemeUrl(){
		return base_url()."assets/themes/".$this->color_scheme."/";
	}

	public function getThemePath(){
		return BASEPATH."/../assets/themes/".$this->color_scheme."/";
	}
	
}
// END Theme Class

/* End of file Theme.php */
/* Location: ./application/libraries/Theme.php */