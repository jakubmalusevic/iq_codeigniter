<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ModulesModel extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->database();
	}
    
	function getItems($params=array(),$sorting=array(),$page=-1) {
		$return=array();
		$this->db->select("modules.*");
		$this->db->from("modules as modules");		
		if (isset($params['module_name'])) {
			if (str_replace(" ","",$params['module_name'])!="") {
				$this->db->where("modules.`title` LIKE '%".$this->db->escape_like_str($params['module_name'])."%'",NULL,false);	
			}
		}					
		if (isset($params['author_name'])) {
			if (str_replace(" ","",$params['author_name'])!="") {
				$this->db->where("modules.`author` LIKE '%".$this->db->escape_like_str($params['author_name'])."%'",NULL,false);	
			}
		}	
		if (isset($params['state'])) {
			if (str_replace(" ","",$params['state'])!="") {
				$this->db->where("modules.`state`",$params['state']);
			}
		}	
		if (isset($sorting['sort-column']) && isset($sorting['sort-direction'])) {
			$this->db->order_by($sorting['sort-column'],$sorting['sort-direction']);
		} else {
			$this->db->order_by("modules.order","asc");
		}
		$this->event->register("BuildModulesQuery");
		$this->total_count=$this->db->get_total_count();
		if ($page!=-1) {
			$this->db->limit($this->pagination->count_per_page,$page*$this->pagination->count_per_page);
		}
		$query=$this->db->get();
		$return=$query->result();
		return $return;
    }
    
    function getItem($item_id){
		$return=false;
		$this->db->select("modules.*");
		$this->db->from("modules as modules");
		$this->db->where("modules.id",$item_id);
		$this->event->register("BuildModuleQuery",$item_id);
		$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$return=$result[0];
			$return->depended_on=$this->getDependedOnModules($return->name);
		}
		return $return;    
    }
    
    function getDependedOnModules($module="") {
    	$return=array();
    	$module=trim(strtolower($module));
    	if ($module!="") {
			$this->db->select("modules.*,modules_dependencies.depended_on_module_name as required_module_name");
			$this->db->from("modules_dependencies as modules_dependencies");
			$this->db->join("modules as modules","modules.name=modules_dependencies.depended_on_module_name","left");
			$this->db->where("modules_dependencies.module_name",$module);
			$query=$this->db->get();
			$return=$query->result();
    	}
    	return $return;
    }
    
    function refreshModules() {
    	$this->event->register("BeforeRefreshModules");
    	$refreshed=array();
    	$this->refreshed=false;
    	$modules=array();
		$modules_dir=BASEPATH."../modules/";
		$dh=@opendir($modules_dir);
    	while ($f=@readdir($dh)) {
    		if (is_dir($modules_dir.$f) && $f!="." && $f!=".." && $f!="") {
    			if (file_exists($modules_dir.$f."/module.xml")) {
	    			$modules[]=strtolower($f);
	    		}
    		}
    	}    		
    	for($m=0;$m<count($modules);$m++){
			$this->db->select("*");
			$this->db->from("modules");
			$this->db->where("name",$modules[$m]);
			$query=$this->db->get();
			$results=$query->result();
			if (count($results)==0) {
				$insert=array();
				$xml_file=$modules_dir.$modules[$m]."/module.xml";
				$xml_file_handler=fopen($xml_file,"r");
				$xml_content=fread($xml_file_handler,filesize($xml_file));
				fclose($xml_file_handler);
				$xml=simplexml_load_string($xml_content);				
				$insert=array(
					"title"=>(string)$xml->title,
					"name"=>(string)$xml->name,
					"description"=>(string)$xml->description,
					"primary_navigation_item_section"=>(string)$xml->primaryNavigationItem->section,
					"primary_navigation_item_action"=>(string)$xml->primaryNavigationItem->action,
					"updated"=>date("Y-m-d H:i:s",time()),
					"undeletable"=>0,
					"version"=>(string)$xml->version,
					"author"=>(string)$xml->author,
					"state"=>1
				);
				$superadmin_persmissions=array();
				$superadmin_persmissions['show_in_navigation']=1;
				$in_sections=array();
				$i=0;
				foreach($xml->sections->section as $section) {
					$superadmin_persmissions[(string)$section->name]=array();
					$in_sections[$i]=new stdClass;
					$in_sections[$i]->title=(string)$section->title;
					$in_sections[$i]->name=(string)$section->name;					
					$in_sections[$i]->description=(string)$section->description;	
					$in_sections[$i]->actions=array();
					$a=0;
					foreach($section->actions->action as $action) {
						$superadmin_persmissions[(string)$section->name][]=(string)$action->name;
						$in_sections[$i]->actions[$a]=new stdClass;
						$in_sections[$i]->actions[$a]->title=(string)$action->title;
						$in_sections[$i]->actions[$a]->name=(string)$action->name;							
						$a++;
					}
					$i++;
				}
				$insert["sections"]=serialize($in_sections);			
				$this->db->select("order");
				$this->db->from("modules");
				$this->db->order_by("order","desc");
				$this->db->limit(1);
				$query=$this->db->get();
				$last_order=$query->result();	
				if (count($last_order)==0) {
					$insert["order"]=10;
				} else {
					$insert["order"]=(floor($last_order[0]->order/10)+1)*10;
				}
				$this->db->insert("modules",$insert);
				$this->db->select("permissions");
				$this->db->from("roles");
				$this->db->where("full_access",1);
				$query=$this->db->get();
				$superadmin_role=$query->result();					
				if (count($superadmin_role)>0){
					if (!$permissions=@unserialize($superadmin_role[0]->permissions)) $permissions=array();
					$permissions[$modules[$m]]=$superadmin_persmissions;
					$permissions=serialize($permissions);
					$this->db->where("id",1);
					$this->db->update("roles",array("permissions"=>$permissions));
				}
				$rc=count($refreshed);
				$refreshed[$rc]=$insert;
				$refreshed[$rc]['id']=$this->db->insert_id();
				$depended_on=array();
				if (!empty($xml->dependencies)) {
					if (!empty($xml->dependencies->dependedOnModule)) {
						foreach($xml->dependencies->dependedOnModule as $depended_on_module_name) {
							$depended_on[]=(string)$depended_on_module_name;
						}
					}
				}
				$table_prefix=$this->db->dbprefix;
				$sql_dir=$modules_dir.$modules[$m]."/sql/";
				if (is_dir($sql_dir)) {
					$install_script=$sql_dir."install.sql";
					if(file_exists($install_script)) {
						$sql_handler=fopen($install_script,"r");
						$sql_content=fread($sql_handler,filesize($install_script));
						fclose($sql_handler);
						$sql_content=preg_replace("/\r|\n/sui"," ",$sql_content);
						$sql_content=str_replace("[table_prefix]",$table_prefix,$sql_content);
						$queries=explode(";",$sql_content);
						for($q=0;$q<count($queries);$q++){
							if (trim($queries[$q])!="") {
								$this->db->query($queries[$q]);
							}
						}
					}
				}				
				$this->_updateDependencies($insert['name'],$depended_on);
				$this->SystemLog->write("modules","listing","refresh",1,"Module \"".((string)$xml->title)."\" has been installed in the system");
				$this->refreshed=true;								
			}
    	}
		$this->db->select("*");
		$this->db->from("modules");
		$query=$this->db->get();
		$results=$query->result();
		for($em=0;$em<count($results);$em++){
			if (in_array($results[$em]->name,$modules)) {
				$xml_file=$modules_dir.$results[$em]->name."/module.xml";
				$xml_file_handler=fopen($xml_file,"r");
				$xml_content=fread($xml_file_handler,filesize($xml_file));
				fclose($xml_file_handler);
				$xml=simplexml_load_string($xml_content);
				$update=array(
					"title"=>(string)$xml->title,
					"name"=>(string)$xml->name,
					"description"=>(string)$xml->description,
					"primary_navigation_item_section"=>(string)$xml->primaryNavigationItem->section,
					"primary_navigation_item_action"=>(string)$xml->primaryNavigationItem->action,
					"version"=>(string)$xml->version,
					"author"=>(string)$xml->author					
				);
				$in_sections=array();
				$i=0;
				foreach($xml->sections->section as $section) {
					$in_sections[$i]=new stdClass;
					$in_sections[$i]->title=(string)$section->title;
					$in_sections[$i]->name=(string)$section->name;					
					$in_sections[$i]->description=(string)$section->description;	
					$in_sections[$i]->actions=array();
					$a=0;
					foreach($section->actions->action as $action) {
						$in_sections[$i]->actions[$a]=new stdClass;
						$in_sections[$i]->actions[$a]->title=(string)$action->title;
						$in_sections[$i]->actions[$a]->name=(string)$action->name;							
						$a++;
					}
					$i++;
				}
				$update["sections"]=serialize($in_sections);
				$serialized_from_file=serialize($update);				
				$compare=array(
					"title"=>$results[$em]->title,
					"name"=>$results[$em]->name,
					"description"=>$results[$em]->description,
					"primary_navigation_item_section"=>$results[$em]->primary_navigation_item_section,
					"primary_navigation_item_action"=>$results[$em]->primary_navigation_item_action,
					"version"=>$results[$em]->version,
					"author"=>$results[$em]->author,					
					"sections"=>$results[$em]->sections
				);
				$serialized_from_db=serialize($compare);
				$depended_on=array();
				if (!empty($xml->dependencies)) {
					if (!empty($xml->dependencies->dependedOnModule)) {
						foreach($xml->dependencies->dependedOnModule as $depended_on_module_name) {
							$depended_on[]=(string)$depended_on_module_name;
						}
					}
				}
				$current_depended_on=$this->_getCurrentDependencies($results[$em]->name);				
				if ($serialized_from_db!=$serialized_from_file || !$this->_matchDependencies($depended_on,$current_depended_on)) {
					$this->_updateDependencies($results[$em]->name,$depended_on);
					$update['updated']=date("Y-m-d H:i:s",time());
					$this->db->where("id",$results[$em]->id);
					$this->db->update("modules",$update);
					$this->SystemLog->write("modules","listing","refresh",2,"Module \"".$results[$em]->title."\" has been updated in the system");
					$this->refreshed=true;		
					$rc=count($refreshed);
					$refreshed[$rc]=$update;		
					$refreshed[$rc]['id']=$results[$em]->id;						
				}			
			} else {
				$module_name=trim(strtolower($results[$em]->name));
				$this->db->where("module_name",$module_name);
				$this->db->delete("modules_dependencies");			
				$this->db->where("id",$results[$em]->id);	
    			$this->db->delete('modules'); 
    			$this->SystemLog->write("modules","listing","delete",3,"Module \"".$results[$em]->title."\" has been deleted from the system");
				$this->refreshed=true;    			
			}
		}
		$this->_checkModulesDependencies();
		$this->event->register("AfterRefreshModules",$refreshed);
    	return true;
    }
    
    public function checkInstalledModule($module=""){
    	$return=false;
    	$module=trim(strtolower($module));
    	if ($module!="") {
    		$this->db->select("id");
    		$this->db->from("modules");
    		$this->db->where("name",$module);
			$query=$this->db->get();
			$results=$query->result();
			if (count($results)>0) $return=true;
    	}
    	return $return;
    }
    
    private function _checkModulesDependencies(){
    	$all_modules=$this->getItems();
		for($i=0;$i<count($all_modules);$i++){
			$depended_on=$this->getDependedOnModules($all_modules[$i]->name);
			$missed_modules=array();
			for($m=0;$m<count($depended_on);$m++){
				if (!$this->checkInstalledModule($depended_on[$m]->required_module_name)) $missed_modules[]=$depended_on[$m]->required_module_name;
			}
			if (count($missed_modules)>0) {
				if ($this->changeState($all_modules[$i]->id,0)) {
					$this->notifications->setError(sprintf($this->lang->line("module_disabled_because_of_missed_modules"),$all_modules[$i]->title,implode(", ",$missed_modules)));
				}			
			}
		}
    }
    
    private function _checkModuleDependencies($item_id){
    	$return=true;
    	$module=$this->getItem($item_id);
    	if ($module!==false) {
			$depended_on=$this->getDependedOnModules($module->name);
			$missed_modules=array();
			for($m=0;$m<count($depended_on);$m++){
				if (!$this->checkInstalledModule($depended_on[$m]->required_module_name)) $missed_modules[]=$depended_on[$m]->required_module_name;
			}
			if (count($missed_modules)>0) {
				$this->db->where("id",$item_id);
				$this->db->update("modules",array("state"=>0));
				$this->notifications->setError(sprintf($this->lang->line("module_disabled_because_of_missed_modules"),$module->title,implode(", ",$missed_modules)));
				$return=false;		
			}
		}
		return $return;
    }    
    
    private function _updateDependencies($module,$depended_on){
    	$module=trim(strtolower($module));
    	if ($module!="") {
			$this->db->where("module_name",$module);
			$this->db->delete("modules_dependencies");
			for($i=0;$i<count($depended_on);$i++){
				$depended_on_module_name=trim(strtolower($depended_on[$i]));
				if ($depended_on_module_name!="") {
					$insert=array(
						"module_name"=>$module,
						"depended_on_module_name"=>$depended_on_module_name
					);
					$this->db->insert("modules_dependencies",$insert);
				}
			}
		}
    }
    
    private function _matchDependencies($depended_on_1,$depended_on_2){
    	$return=true;
    	$work_depended_data=$depended_on_1;
    	$compare_depended_data=$depended_on_2;
    	if (count($depended_on_2)>count($work_depended_data)) {
	    	$work_depended_data=$depended_on_2;
	    	$compare_depended_data=$depended_on_1;
	    }
    	if (count($work_depended_data)>0) {
    		$elements=0;
    		$matches=0;
    		foreach($work_depended_data as $match_module) {
    			$elements++;
    			if (in_array($match_module,$compare_depended_data)) $matches++;
    		}
    		if ($matches<$elements) $return=false;
    	}
    	return $return;
    }
    
    private function _getCurrentDependencies($module=""){
    	$return=array();
    	if ($module!="") {
    		$this->db->select("depended_on_module_name");
    		$this->db->from("modules_dependencies");
    		$this->db->where("module_name",$module);
			$query=$this->db->get();
			$results=$query->result();   		
			for($i=0;$i<count($results);$i++){
				$return[]=$results[$i]->depended_on_module_name;
			}
    	}
    	return $return;
    }
    
    public function installModule(){
    	$event_file=array();
    	if (isset($_FILES['archive'])) {
    		$event_file=$_FILES['archive'];
    	}
		$this->event->register("BeforeInstallModule",$event_file);
    	$return=false;
    	$this->updated=false;
    	if (isset($_FILES['archive'])) {
    		if ($_FILES['archive']['name']!="") {  
    			$ext=explode(".",$_FILES['archive']['name']);
    			$ext=strtolower($ext[count($ext)-1]);
    			if ($ext!="zip") {
    				$this->notifications->setError($this->lang->line("module_extension_not_allowed"));
    			} else {
    				$modules_dir=BASEPATH."../modules/";
    				if (!is_writeable($modules_dir)) {
    					$this->notifications->setError($this->lang->line("modules_dir_not_allowed_for_writing"));
    				} else {
						$table_prefix=$this->db->dbprefix;
						$this->load->library("unzip");
						$this->load->helper("file");
						$temp_dir=BASEPATH."../uploads/temp/";
						if (!is_dir($temp_dir)) @mkdir($temp_dir);
						if (is_dir($temp_dir) && is_writeable($temp_dir)) {
							$temp_dir=BASEPATH."../uploads/temp/".strtolower($_FILES['archive']['name'])."/";
							if (!is_dir($temp_dir)) @mkdir($temp_dir);	
							if (is_dir($temp_dir) && is_writeable($temp_dir)) {
								$this->unzip->extract($_FILES['archive']['tmp_name'],$temp_dir);
								$module_xml=$temp_dir."module.xml";
								if (file_exists($module_xml)) {	
									$xml_file_handler=fopen($module_xml,"r");
									$xml_content=fread($xml_file_handler,filesize($module_xml));
									fclose($xml_file_handler);
									$xml=simplexml_load_string($xml_content);		
									$module_name=strtolower(trim(@(string)$xml->name));
									if ($module_name!="") {
										$dest_dir=BASEPATH."../modules/".$module_name;
										if (is_dir($dest_dir)) {
											$this->updated=true;
											@delete_files($dest_dir,true);
											@rmdir($dest_dir);
										}
										if (@mkdir($dest_dir)) {
											$this->unzip->extract($_FILES['archive']['tmp_name'],$dest_dir);
											$sql_dir=$dest_dir."/sql/";
											if (is_dir($sql_dir)) {
												$install_script=$sql_dir."install.sql";
												if(file_exists($install_script)) {
													$sql_handler=fopen($install_script,"r");
													$sql_content=fread($sql_handler,filesize($install_script));
													fclose($sql_handler);
													$sql_content=preg_replace("/\r|\n/sui"," ",$sql_content);
													$sql_content=str_replace("[table_prefix]",$table_prefix,$sql_content);
													$queries=explode(";",$sql_content);
													for($q=0;$q<count($queries);$q++){
														if (trim($queries[$q])!="") {
															$this->db->query($queries[$q]);
														}
													}
												}
											}
											$return=true;
											$this->refreshModules();
										} else {
											$this->notifications->setError($this->lang->line("cannot_create_module_dir"));
										}
									} else {
										$this->notifications->setError($this->lang->line("name_node_not_found_in_module_xml_file"));
									}
								} else {
									$this->notifications->setError($this->lang->line("module_xml_file_not_found_in_installation_archive"));
								}
								@delete_files($temp_dir,true);
								@rmdir($temp_dir);								
							} else {
								$this->notifications->setError($this->lang->line("uploads_dir_not_allowed_for_writing"));
							}
						} else {
							$this->notifications->setError($this->lang->line("uploads_dir_not_allowed_for_writing"));
						}
					}
    			}    		
    		}
    	}
    	$this->event->register("AfterInstallModule",$event_file);
    	return $return;
    }    
    
    public function deleteModule($item_id){
    	$this->event->register("BeforeDeleteModule",$item_id);
    	$return=false;
		$this->db->select("*");
		$this->db->from("modules");
		$this->db->where("id",$item_id);   
		$query=$this->db->get();
		$results=$query->result();
		if (count($results)>0){
			$this->load->helper("file");
			$table_prefix=$this->db->dbprefix;
			$module_path=BASEPATH."../modules/".$results[0]->name;
			$sql_dir=$module_path."/sql/";
			if (is_dir($sql_dir)) {
				$uninstall_script=$sql_dir."uninstall.sql";
				if(file_exists($uninstall_script)) {
					$sql_handler=fopen($uninstall_script,"r");
					$sql_content=fread($sql_handler,filesize($uninstall_script));
					fclose($sql_handler);
					$sql_content=preg_replace("/\r|\n/sui"," ",$sql_content);
					$sql_content=str_replace("[table_prefix]",$table_prefix,$sql_content);
					$queries=explode(";",$sql_content);
					for($q=0;$q<count($queries);$q++){
						if (trim($queries[$q])!="") {
							$this->db->query($queries[$q]);
						}
					}
				}
			}			
			if (!is_writeable($module_path)) {
				$this->notifications->setError($this->lang->line("module_files_not_deleted_from_modules_dir"));
			} else {			
				@delete_files($module_path,true);
				@rmdir($module_path);
			}
			$module_name=trim(strtolower($results[0]->name));
			$this->db->where("module_name",$module_name);
			$this->db->delete("modules_dependencies");				
			$this->db->where("id",$results[0]->id);	
    		$this->db->delete('modules'); 
    		$this->_checkModulesDependencies();
    		$this->SystemLog->write("modules","listing","delete",3,"Module \"".$results[0]->title."\" has been deleted from the system"); 							
    		$return=true;
		}
		$this->event->register("AfterDeleteModule",$item_id);
		return $return;
    }
    
    public function saveOrder($data){
    	$this->event->register("BeforeSaveModulesOrder",$data);
    	for($i=0;$i<count($data);$i++){
    		$update=array(
				"order"=>$data[$i]['order']
			);	
			$this->db->where("id",$data[$i]['id']);
			$this->db->update("modules",$update);
    	}    
    	$this->event->register("AfterSaveModulesOrder",$data);
    	$this->SystemLog->write("modules","listing","saveorder",2,"Order of modules has been updated in the system"); 
    }
    
    public function changeState($item_id,$state){
		$this->event->register("BeforeChangeModuleState",$item_id,$state);
    	$return=false;
    	$module=$this->getItem($item_id);
    	if ($module!==false) {
    		if ($this->_checkModuleDependencies($item_id)) {
				if ($module->name=="users" && $state==0) {
					$this->notifications->setError($this->lang->line("you_cannot_disable_users_module"));
				} elseif ($module->name=="modules" && $state==0) {
					$this->notifications->setError($this->lang->line("you_cannot_disable_modules_module"));
				} else {
					$this->db->where("id",$item_id);
					$this->db->update("modules",array("state"=>$state));
					$return=true;
					if ($state==0) {
						$this->SystemLog->write("modules","listing","changestate",2,"Module \"".$module->title."\" has been disabled in the system");
					} else {
						$this->SystemLog->write("modules","listing","changestate",2,"Module \"".$module->title."\" has been enabled in the system");
					}
				}
			}
		}
		$this->event->register("AfterChangeModuleState",$item_id,$state);
    	return $return;
    }
    
}
?>