<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Language Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Language
 * @author		IQDesk
 * @link		http://iqdesk.net
 */
class CI_Language {

	private $CI;
	private $_languages=array();
	private $_loaded_modules_languages=array();

	public function __construct() {
		$this->CI=& get_instance();
		$this->_checkLanguages();
		$this->_changeLanguage();
		$this->_setCurrentLanguage();
		log_message("debug", "Language Class Initialized");
	}
	
	public function getModuleTitle($module){
		$return="";
		if (!isset($this->_loaded_modules_languages[$module])) {
			$config=get_config();
			$default_lang=(!isset($config['language']))?'english':$config['language'];		
			$lang_file=BASEPATH."/../modules/".$module."/language/".$default_lang."/".$module."_lang.php";	
			if (file_exists($lang_file)) {
				include($lang_file);
				$this->_loaded_modules_languages[$module]=$lang;
			} else {
				$lang_file=BASEPATH."/../modules/".$module."/language/english/".$module."_lang.php";	
				if (file_exists($lang_file)) {
					include($lang_file);
					$this->_loaded_modules_languages[$module]=$lang;
					foreach($this->_loaded_modules_languages[$module] as &$line) {
						$line.='*';
					}	
					$this->CI->lang->setUntraslatedWordsStatus(true);			
				}			
			}
		}
		if (isset($this->_loaded_modules_languages[$module]['_module_name'])) $return=$this->_loaded_modules_languages[$module]['_module_name'];
		return $return;
	}
	
	public function getSectionTitle($module,$section){
		$return="";
		if (!isset($this->_loaded_modules_languages[$module])) {
			$config=get_config();
			$default_lang=(!isset($config['language']))?'english':$config['language'];		
			$lang_file=BASEPATH."/../modules/".$module."/language/".$default_lang."/".$module."_lang.php";	
			if (file_exists($lang_file)) {
				include($lang_file);
				$this->_loaded_modules_languages[$module]=$lang;
			} else {
				$lang_file=BASEPATH."/../modules/".$module."/language/english/".$module."_lang.php";	
				if (file_exists($lang_file)) {
					include($lang_file);
					$this->_loaded_modules_languages[$module]=$lang;
					foreach($this->_loaded_modules_languages[$module] as &$line) {
						$line.='*';
					}
					$this->CI->lang->setUntraslatedWordsStatus(true);					
				}			
			}
		}
		$line_name="_section_".$section;
		if (isset($this->_loaded_modules_languages[$module][$line_name])) $return=$this->_loaded_modules_languages[$module][$line_name];
		return $return;
	}
	
	public function getActionTitle($module,$section,$action){
		$return="";
		if (!isset($this->_loaded_modules_languages[$module])) {
			$config=get_config();
			$default_lang=(!isset($config['language']))?'english':$config['language'];		
			$lang_file=BASEPATH."/../modules/".$module."/language/".$default_lang."/".$module."_lang.php";	
			if (file_exists($lang_file)) {
				include($lang_file);
				$this->_loaded_modules_languages[$module]=$lang;
			} else {
				$lang_file=BASEPATH."/../modules/".$module."/language/english/".$module."_lang.php";	
				if (file_exists($lang_file)) {
					include($lang_file);
					$this->_loaded_modules_languages[$module]=$lang;
					foreach($this->_loaded_modules_languages[$module] as &$line) {
						$line.='*';
					}	
					$this->CI->lang->setUntraslatedWordsStatus(true);				
				}			
			}
		}
		$line_name="_section_".$section."_action_".$action;
		if (isset($this->_loaded_modules_languages[$module][$line_name])) $return=$this->_loaded_modules_languages[$module][$line_name];
		return $return;
	}	
	
	public function getSectionDescription($module,$section){
		$return="";
		$module=strtolower($module);
		$section=strtolower($section);		
		if ($module!="" && $section!="") {
			$return=$this->getModuleLanguageLine($module,"_".$section."_section_description");
		}
		return $return;
	}	
	
	public function getModuleLanguageLine($module,$line_name){
		$return="";
		if (!isset($this->_loaded_modules_languages[$module])) {
			$config=get_config();
			$default_lang=(!isset($config['language']))?'english':$config['language'];		
			$lang_file=BASEPATH."/../modules/".$module."/language/".$default_lang."/".$module."_lang.php";	
			if (file_exists($lang_file)) {
				include($lang_file);
				$this->_loaded_modules_languages[$module]=$lang;
			} else {
				$lang_file=BASEPATH."/../modules/".$module."/language/english/".$module."_lang.php";	
				if (file_exists($lang_file)) {
					include($lang_file);
					$this->_loaded_modules_languages[$module]=$lang;
					foreach($this->_loaded_modules_languages[$module] as &$line) {
						$line.='*';
					}	
					$this->CI->lang->setUntraslatedWordsStatus(true);				
				}			
			}
		}
		if (isset($this->_loaded_modules_languages[$module][$line_name])) $return=$this->_loaded_modules_languages[$module][$line_name];
		return $return;
	}
	
	private function _changeLanguage(){
		if ($data=$this->CI->input->post(NULL, TRUE)) {
			if (isset($data['task']) && isset($data['language'])) {
				if ($data['task']=="change-language" && $data['language']!="") {
					if (isset($this->_languages[$data['language']])) {
						$this->CI->session->set_userdata("language",$data['language']);
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
			}
		}
	}
	
	private function _setCurrentLanguage(){
		$language=$this->CI->session->userdata("language");
		if ($language!="") {
			$this->CI->config->set_item('language', $language);
		}
		$this->CI->lang->load("main");
	}
	
	private function _checkLanguages(){
		$this->CI->load->helper('file');
		$dir=dirname(__FILE__)."/../language/";
		if (is_dir($dir)) {
			$dir_info=get_dir_file_info($dir);
			if (is_array($dir_info)) {
				foreach($dir_info as $lang_dir=>$info) {
					if (file_exists($info['server_path']."/main_lang.php")) {
						include($info['server_path']."/main_lang.php");
						if (isset($lang['_lang_name'])) {
							$this->_languages[$lang_dir]=$lang['_lang_name'];
						}
					}
				}
			}
		}
	}
	
	public function drawLanguageNavigator($return_html=false){
		$output="";
		if (count($this->_languages)>1) {
			$config=get_config();
			$output=$this->CI->load->view('libraries/language',array("items"=>$this->_languages,"current_language"=>$config['language']),true);
		}
		if ($return_html) return $output;
		else echo $output;		
	}
	
}
// END Language Class

/* End of file Language.php */
/* Location: ./application/libraries/Language.php */