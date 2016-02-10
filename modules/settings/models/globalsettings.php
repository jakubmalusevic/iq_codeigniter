<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class GlobalSettings extends CI_Model {

	private $_records_options=array();

	function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	function registerSettingsSection($section_name="",$section_title="") {
		$section_name=strtolower(trim($section_name));
		$section_title=trim($section_title);
		if ($section_name!="" && $section_title!="") {
			$trace=debug_backtrace();
			$module_parts=explode(".",strtolower(basename($trace[0]['file'])));
			$module="";
			if (count($module_parts)>1) {
				for($i=0;$i<count($module_parts)-1;$i++) {
					$module.=($module==""?"":".").$module_parts[$i];
				}
			} else {
				$module=$module_parts[0];
			}
			if ($module!="") {
				if ($this->getSettingsSectionByName($section_name)===false) {
					$insert=array(
						"module"=>$module,
						"name"=>$section_name,
						"title"=>$section_title
					);
					$this->db->insert("settings_sections",$insert);
				}
			}
		}
	}
	
	function getSettingsSectionByName($name){
		$return=false;
		$this->db->select("*");
		$this->db->from("settings_sections");
		$this->db->where("name",$name);
    	$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$return=$result[0];
		}
		return $return;
	}
	
	function getRecord($section_name="",$name=""){
		$return=false;
		$section_name=strtolower(trim($section_name));
		$name=strtolower(trim($name));
		if ($section_name!="" && $name!="") {
			$section=$this->getSettingsSectionByName($section_name);
			if ($section!==false) {
				$this->db->select("*");
				$this->db->from("settings_records");
				$this->db->where("section_id",$section->id);
				$this->db->where("name",$name);
				$query=$this->db->get();
				$result=$query->result();
				if (count($result)>0) {
					$return=$result[0];
				}				
			}
		}
		return $return;
	}	
	
	function registerSetting($section_name="",$name="",$title="",$default_value=""){
		$section_name=strtolower(trim($section_name));
		$name=strtolower(trim($name));
		if ($section_name!="" && $name!="") {
			if ($this->getRecord($section_name,$name)===false) {
				$section=$this->getSettingsSectionByName($section_name);
				if ($section!==false) {
					$insert=array(
						"section_id"=>$section->id,
						"name"=>$name,
						"title"=>$title,
						"value"=>$default_value,
						"default_value"=>$section_name=="global"?$default_value:""
					);
					$this->db->insert("settings_records",$insert);				
				}
			}
		}
	}
	
	function forceUpdateSetting($section_name="",$name="",$value=""){
		$section_name=strtolower(trim($section_name));
		$name=strtolower(trim($name));
		if ($section_name!="" && $name!="") {
			$record=$this->getRecord($section_name,$name);
			if ($record!==false) {
				$update=array("value"=>$value);
				$this->db->where("id",$record->id);
				$this->db->update("settings_records",$update);					
			}
		}		
	}
	
	function getValue($section_name="",$name="",$default_value=""){
		$return=$default_value;
		$section_name=strtolower(trim($section_name));
		$name=strtolower(trim($name));
		if ($section_name!="" && $name!="") {
			$record=$this->getRecord($section_name,$name);
			if ($record!==false) {
				$return=$record->value;
			}
		}		
		return $return;
	}
	
	function getOptionsValue($section_name="",$name="",$value=""){
		$return=$value;
		$section_name=strtolower(trim($section_name));
		$name=strtolower(trim($name));
		if ($section_name!="" && $name!="") {
			$context=$section_name.":".$name;
			if (isset($this->_records_options[$context])) {
				if (isset($this->_records_options[$context][$value])) $return=$this->_records_options[$context][$value];
			}
		}		
		return $return;
	}
	
	function setSettingOptions($section_name="",$name="",$options=array()){
		$section_name=strtolower(trim($section_name));
		$name=strtolower(trim($name));
		if ($section_name!="" && $name!="" && count($options)>0) {
			$context=$section_name.":".$name;
			if (!isset($this->_records_options[$context])) $this->_records_options[$context]=array();
			$this->_records_options[$context]=array_merge($this->_records_options[$context],$options);
		}
	}
	
	function getSettingOptions($section_name="",$name=""){
		$return=array();
		$section_name=strtolower(trim($section_name));
		$name=strtolower(trim($name));
		if ($section_name!="" && $name!="") {
			$context=$section_name.":".$name;
			if (isset($this->_records_options[$context])) {
				$return=$this->_records_options[$context];
			}
		}
		return $return;
	}	
	
	function getSettingsSections(){
		$return=array();
		$this->db->select("*");
		$this->db->from("settings_sections");
		$this->db->order_by("id","ASC");
    	$query=$this->db->get();
		$return=$query->result();
		return $return;		
	}
	
	function setValue($section_name="",$name="",$value=""){
		$return=false;
		$section_name=strtolower(trim($section_name));
		$name=strtolower(trim($name));
		if ($section_name!="" && $name!="") {
			$record=$this->getRecord($section_name,$name);
			if ($record!==false) {
				$this->db->where("id",$record->id);
				$this->db->update("settings_records",array("value"=>$value));
				$return=true;
				if ($section_name=="global") {
					if ($name=="language") {
						$file_found=false;
						$found_file="";
						if (file_exists(BASEPATH."../".APPPATH."config/".ENVIRONMENT."/config.php")) {
							$file_found=true;
							$found_file=BASEPATH."../".APPPATH."config/".ENVIRONMENT."/config.php";
						} else {
							if (file_exists(BASEPATH."../".APPPATH."config/config.php")) {
								$file_found=true;
								$found_file=BASEPATH."../".APPPATH."config/config.php";
							}			
						}	
						if (!$file_found) {
							$this->notifications->setError($this->lang->line("config_file_not_found"));
							$return=false;
						} else {
							if (!is_writeable($found_file)) {
								$this->notifications->setError($this->lang->line("config_file_is_not_allowed_for_writing"));
								$return=false;							
							} else {
								$file_content=file_get_contents($found_file);
								$file_content=preg_replace(
									"/config\[\'language\'\](.*?)=(.*?)\'(.*?)\';/sui",
									"config['language']\$1=\$2'".$value."';",
									$file_content
								);
								$fh=fopen($found_file,"w");
								fwrite($fh,$file_content);
								fclose($fh);
							}
						}
					}
					if ($name=="main_title" || $name=="color_scheme" || $name=="page_title_delimiter" || $name=="logout_in_navigation") {
						$file_found=false;
						$found_file="";
						if (file_exists(BASEPATH."../".APPPATH."config/".ENVIRONMENT."/theme.php")) {
							$file_found=true;
							$found_file=BASEPATH."../".APPPATH."config/".ENVIRONMENT."/theme.php";
						} else {
							if (file_exists(BASEPATH."../".APPPATH."config/theme.php")) {
								$file_found=true;
								$found_file=BASEPATH."../".APPPATH."config/theme.php";
							}			
						}	
						if (!$file_found) {
							$this->notifications->setError($this->lang->line("theme_config_file_not_found"));
							$return=false;
						} else {
							if (!is_writeable($found_file)) {
								$this->notifications->setError($this->lang->line("theme_config_file_is_not_allowed_for_writing"));
								$return=false;							
							} else {
								if ($name=="logout_in_navigation") {
									$file_content=file_get_contents($found_file);
									$file_content=preg_replace(
										"/theme\[\'logout_in_navigation\'\](.*?)=(.*?);/sui",
										"theme['logout_in_navigation']\$1=".($value==1?"true":"false").";",
										$file_content
									);
									$fh=fopen($found_file,"w");
									fwrite($fh,$file_content);
									fclose($fh);
								} else {
									$file_content=file_get_contents($found_file);
									$file_content=preg_replace(
										"/theme\[\'".$name."\'\](.*?)=(.*?)\"(.*?)\";/sui",
										"theme['".$name."']\$1=\$2\"".$value."\";",
										$file_content
									);
									$fh=fopen($found_file,"w");
									fwrite($fh,$file_content);
									fclose($fh);								
								}
							}
						}						
					}					
				}
			}
		}	
		return $return;
	}
    
}
?>