<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SettingsModel extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	function getItems($params=array(),$sorting=array(),$page=-1) {  		
		$return=array();		
		$this->db->select("settings_records.*,settings_sections.module as section_module,settings_sections.name as section_name,settings_sections.title as section_title");
		$this->db->from("settings_records as settings_records");
		$this->db->join("settings_sections as settings_sections","settings_sections.id=settings_records.section_id","left");
		if (isset($params['value'])) {
			if (str_replace(" ","",$params['value'])!="") {
				$this->db->where("settings_records.`value` LIKE '%".$this->db->escape_like_str($params['value'])."%'",NULL,false);
			}
		}
		if (isset($params['section_id'])) {
			if (str_replace(" ","",$params['section_id'])!="") {
				$this->db->where("settings_records.section_id",$params['section_id']);
			}
		}
		$this->db->group_by("settings_records.id");
		$this->event->register("BuildSettingsQuery");
		$query=$this->db->get();
		$temp=$query->result();
		for($i=0;$i<count($temp);$i++){
			$temp_title="";
			if ($temp[$i]->section_module!="") {
				$temp_title=$this->language->getModuleLanguageLine($temp[$i]->section_module,"_settings_record_".$temp[$i]->name);
			}
			if ($temp_title!="") $temp[$i]->title=$temp_title;
			$temp_title="";
			if ($temp[$i]->section_module!="") {
				$temp_title=$this->language->getModuleLanguageLine($temp[$i]->section_module,"_settings_section_".$temp[$i]->section_name);
			}
			if ($temp_title!="") $temp[$i]->section_title=$temp_title;			
			$temp[$i]->value=$this->GlobalSettings->getOptionsValue($temp[$i]->section_name,$temp[$i]->name,$temp[$i]->value);
		}
		$search_name="";
		if (isset($params['name'])) {
			if (str_replace(" ","",$params['name'])!="") {
				$search_name=strtolower($params['name']);
			}
		}
		for($i=0;$i<count($temp);$i++) {
			if ($search_name=="" || ($search_name!="" && substr_count(strtolower($temp[$i]->title),$search_name)>0)) {
				$return[]=$temp[$i];
			}
		}
		if (isset($sorting['sort-column']) && isset($sorting['sort-direction'])) {
			if ($sorting['sort-column']=="title") {
				if ($sorting['sort-direction']=="asc") usort($return,array(&$this,"_sortSettingsByTitleASC"));
				if ($sorting['sort-direction']=="desc") usort($return,array(&$this,"_sortSettingsByTitleDESC"));
			}
			if ($sorting['sort-column']=="section_title") {
				if ($sorting['sort-direction']=="asc") usort($return,array(&$this,"_sortSettingsBySectionTitleASC"));
				if ($sorting['sort-direction']=="desc") usort($return,array(&$this,"_sortSettingsBySectionTitleDESC"));
			}			
			if ($sorting['sort-column']=="value") {
				if ($sorting['sort-direction']=="asc") usort($return,array(&$this,"_sortSettingsByValueASC"));
				if ($sorting['sort-direction']=="desc") usort($return,array(&$this,"_sortSettingsByValueDESC"));
			}			
		} else {
			usort($return,array(&$this,"_sortSettingsBySectionTitleASC"));
		}	
		$this->total_count=count($return);
		$temp=array();
		if ($page!=-1) {
			$temp=$return;
			$return=array();
			for($i=$page*$this->pagination->count_per_page;$i<($page+1)*$this->pagination->count_per_page;$i++) {
				if (isset($temp[$i])) {
					$return[]=$temp[$i];
				}
			}
		}
		return $return;
    }
    
    public function _sortSettingsByTitleASC($a,$b){
    	if (strcasecmp($a->title,$b->title)==0) return 0;    
    	if (strcasecmp($a->title,$b->title)<0) return -1;
		if (strcasecmp($a->title,$b->title)>0) return 1;
    }
    
    public function _sortSettingsByTitleDESC($a,$b){
    	if (strcasecmp($a->title,$b->title)==0) return 0;    
    	if (strcasecmp($a->title,$b->title)<0) return 1;
		if (strcasecmp($a->title,$b->title)>0) return -1;
    }    
    
    public function _sortSettingsBySectionTitleASC($a,$b){
    	if (strcasecmp($a->section_title,$b->section_title)==0) return 0;    
    	if (strcasecmp($a->section_title,$b->section_title)<0) return -1;
		if (strcasecmp($a->section_title,$b->section_title)>0) return 1;
    }
    
    public function _sortSettingsBySectionTitleDESC($a,$b){
    	if (strcasecmp($a->section_title,$b->section_title)==0) return 0;    
    	if (strcasecmp($a->section_title,$b->section_title)<0) return 1;
		if (strcasecmp($a->section_title,$b->section_title)>0) return -1;
    }  
    
    public function _sortSettingsByValueASC($a,$b){
    	if (strcasecmp($a->value,$b->value)==0) return 0;    
    	if (strcasecmp($a->value,$b->value)<0) return -1;
		if (strcasecmp($a->value,$b->value)>0) return 1;
    }
    
    public function _sortSettingsByValueDESC($a,$b){
    	if (strcasecmp($a->value,$b->value)==0) return 0;    
    	if (strcasecmp($a->value,$b->value)<0) return 1;
		if (strcasecmp($a->value,$b->value)>0) return -1;
    }  
    
    public function getItem($item_id){
    	$return=false;
		$this->db->select("settings_records.*,settings_sections.module as section_module,settings_sections.name as section_name,settings_sections.title as section_title");
		$this->db->from("settings_records as settings_records");
		$this->db->join("settings_sections as settings_sections","settings_sections.id=settings_records.section_id","left");
		$this->db->where("settings_records.id",$item_id);
		$this->event->register("BuildSettingQuery",$item_id);
		$this->db->group_by("settings_records.id");
		$query=$this->db->get();
		$result=$query->result();		
		if (count($result)>0){
			$return=$result[0];
			$temp_title="";
			if ($return->section_module!="") {
				$temp_title=$this->language->getModuleLanguageLine($return->section_module,"_settings_record_".$return->name);
			}
			if ($temp_title!="") $return->title=$temp_title;
			$temp_title="";
			if ($return->section_module!="") {
				$temp_title=$this->language->getModuleLanguageLine($return->section_module,"_settings_section_".$return->section_name);
			}
			if ($temp_title!="") $return->section_title=$temp_title;			
			$return->options=$this->GlobalSettings->getSettingOptions($return->section_name,$return->name);
		}
		return $return;
    }
    
    public function update($data,$item_id){
    	$return=false;
    	$this->event->register("BeforeUpdateSetting",$data,$item_id);
    	$setting=$this->getItem($item_id);	
		if ($setting!==false) {
			if ($this->GlobalSettings->setValue($setting->section_name,$setting->name,$data['setting_value'])) {
				$return=true;
				$new_setting=$this->getItem($item_id);	
				$this->event->register("AfterUpdateSetting",$data,$item_id);	
				$this->SystemLog->write("settings","listing","update",2,"Setting \"".$setting->title."\" has been updated in the system from \"".$this->GlobalSettings->getOptionsValue($setting->section_name,$setting->name,$setting->value)."\" to \"".$this->GlobalSettings->getOptionsValue($new_setting->section_name,$new_setting->name,$new_setting->value)."\"");				
			}
		}
		return $return;
    }
    
    public function resetGlobalToDefault(){
    	$return=false;
    	$this->event->register("BeforeResetGlobalSettings");
    	$this->db->select("id");
    	$this->db->from("settings_sections");
    	$this->db->where("name","global");
		$query=$this->db->get();
		$section=$query->result();		
		if (count($section)>0) {
			$section_id=$section[0]->id;
			if ($section_id>0) {
				$this->db->select("*");
				$this->db->from("settings_records");
				$this->db->where("section_id",$section_id);
				$query=$this->db->get();
				$settings=$query->result();					
				if (count($settings)>0) {
					foreach($settings as $setting) {
						$old_setting=$this->getItem($setting->id);
						if ($this->GlobalSettings->setValue("global",$setting->name,$setting->default_value)) {
							$return=true;
							$new_setting=$this->getItem($setting->id);	
							$this->SystemLog->write("settings","listing","update",2,"Setting \"".$old_setting->title."\" has been updated in the system from \"".$this->GlobalSettings->getOptionsValue($old_setting->section_name,$old_setting->name,$old_setting->value)."\" to \"".$this->GlobalSettings->getOptionsValue($new_setting->section_name,$new_setting->name,$new_setting->value)."\"");				
						}						
					}
				}
			}
		}
		if ($return) {
			$this->event->register("AfterResetGlobalSettings");
		}
		return $return;    	
    }
    
}
?>