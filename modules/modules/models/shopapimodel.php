<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ShopApiModel extends CI_Model {

	private $_shop_url="https://iqdesk.net/engine/";

	function __construct() {
		parent::__construct();
		$this->load->database();
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$this->_shop_url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_USERAGENT,'iQDesk Engine v.2.0 Shop Connector');				
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_exec($ch);
		if (curl_getinfo($ch,CURLINFO_HTTP_CODE)!=200) {
			echo "cannot connect shop server! Please contact support@iqdesk.net.";
			die();			
		}
		curl_close($ch);		
	}
	
	function _makeRequest($function="",$params=array(),$method="GET"){
		$return=false;
		if ($function!="") {
			$action_url=$this->_shop_url."modulesshop/api/".$function;
			if ($method=="GET") {
				if (count($params)>0) $action_url.="?".http_build_str($params);
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$action_url);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch,CURLOPT_HEADER,0);
				curl_setopt($ch,CURLOPT_USERAGENT,'iQDesk Engine v.2.0 Shop Connector');				
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				$return=curl_exec($ch);
				curl_close($ch);
				if (!$return=@json_decode($return)) $return=false;
			}
			if ($method=="POST") {
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$action_url);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch,CURLOPT_HEADER,0);
				curl_setopt($ch,CURLOPT_POST,true);
			    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_str($params));
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);			    
				curl_setopt($ch,CURLOPT_USERAGENT,'iQDesk Engine v.2.0 Shop Connector');
				$return=curl_exec($ch);
				curl_close($ch);
				if (!$return=@json_decode($return)) $return=false;
			}			
		}
		return $return;
	}
	
	function _requestFile($function="",$filename,$params=array(),$method="GET"){
		$return=false;
		if ($function!="") {
			$action_url=$this->_shop_url."modulesshop/api/".$function;
			$fh=fopen($filename,"w");
			if ($method=="GET") {
				if (count($params)>0) $action_url.="?".http_build_str($params);
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$action_url);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch,CURLOPT_HEADER,0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);				
				curl_setopt($ch,CURLOPT_USERAGENT,'iQDesk Engine v.2.0 Shop Connector');
				curl_setopt($ch,CURLOPT_FILE,$fh);
				curl_exec($ch);
			}
			if ($method=="POST") {
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$action_url);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch,CURLOPT_HEADER,0);
				curl_setopt($ch,CURLOPT_POST,true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);				
			    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_str($params));
				curl_setopt($ch,CURLOPT_USERAGENT,'iQDesk Engine v.2.0 Shop Connector');
				curl_setopt($ch,CURLOPT_FILE,$fh);
				curl_exec($ch);
			}
			fclose($fh);			
		}
		return $return;
	}	
    
	function getModules($params=array(),$page=-1) {
		$modules=false;
		$temp=$this->_makeRequest("getModules",array("apply_filters"=>1,"filter"=>$params,"page"=>-1));
		if ($temp!==false) {
			if (isset($temp->items)) {
				$modules=new stdClass;
				$modules->items=array();
				$modules->total_items=0;
				for($i=0;$i<count($temp->items);$i++) {
					$rc=count($modules->items);
					$modules->items[$rc]=$temp->items[$i];				
					$this->db->select("version");
					$this->db->from("modules");
					$this->db->where("name",$temp->items[$i]->name);
					$query=$this->db->get();
					$result=$query->result();
					if (count($result)==0) {
						$modules->items[$rc]->installed_version=false;
					} else {
						$modules->items[$rc]->installed_version=$result[0]->version;
					}
				}
				$modules->total_items=count($modules->items);
				if ($page!=-1) {
					$temp=$modules->items;
					$modules->items=array();
					for($i=$page*$this->pagination->count_per_page;$i<($page+1)*$this->pagination->count_per_page;$i++){
						if (isset($temp[$i])) {
							$modules->items[]=$temp[$i];
						}
					}
				}
			}
		}
		return $modules;
    }

	function getCategories(){
		$categories=$this->_makeRequest("getCategories");
		if ($categories===false) $categories=array();
		return $categories;
	}
	
	function getModule($module_id=0) {
		$return=false;
		if ($module_id>0) {
			$return=$this->_makeRequest("getModule",array("id"=>$module_id));
			if ($return!==false) {
				$return->installed_version=false;
				$return->update=false;
				$return->buy=false;
				$return->rollback=false;
				$this->db->select("version");
				$this->db->from("modules");
				$this->db->where("name",$return->name);
				$query=$this->db->get();
				$result=$query->result();
				if (count($result)>0) {
					$return->installed_version=$result[0]->version;
					if ($return->installed_version!=$return->latest_version) $return->update=true;
					if ($return->installed_version==$return->latest_version && count($return->versions)>1) $return->rollback=true;
				} else {
					$return->buy=true;
				}
				$key=$this->config->config['encryption_key'];
				$return->allow_comment=false;
				if ($return->installed_version!==false) {
					$found_comment=false;
					for($i=0;$i<count($return->reviews);$i++) {
						if ($return->reviews[$i]->sender_unique_id==$key) $found_comment=true;
					}
					if (!$found_comment) {
						$return->allow_comment=true;
					}
				}		
			}
		}
		return $return;
	}
	
	function createReview($data){
		$data['sender_unique_id']=$this->config->config['encryption_key'];
		$return=$this->_makeRequest("createReview",$data,"POST");
		return $return;
	}
	
	function getImage($image_id){
		$return=$this->_makeRequest("getImage",array("image_id"=>$image_id));
		return $return;		
	}
	
	function updateVersion($system_module_id,$system_module_name){
		$return=false;
		$this->db->select("modules.*");
		$this->db->from("modules as modules");
		$this->db->where("name",$system_module_name);
		$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$current_module=$result[0];
			$version_info=$this->_makeRequest("getVersionInfo",array("module_id"=>$system_module_id,"current_version"=>$current_module->version));
			if ($version_info->latest_version!=$current_module->version) {
				$temp_dir=dirname(__FILE__)."/../../../uploads/modules/";
				if (!is_dir($temp_dir)) {
					if (!@mkdir($temp_dir)) {
						$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
						return false;
					}
				}
				$temp_dir=dirname(__FILE__)."/../../../uploads/modules/temp/";
				if (!is_dir($temp_dir)) {
					if (!@mkdir($temp_dir)) {
						$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
						return false;
					}
				}		
				$filename=$temp_dir.$system_module_name."_".preg_replace("/[^0-9]/ui","_",$version_info->latest_version).".zip";
				if (!@touch($filename)){
					$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
					return false;				
				}
				$this->_requestFile("getUpdateDistr",$filename,array("module_id"=>$system_module_id,"current_version"=>$current_module->version));
				$this->load->library("unzip");
				$dest_dir=dirname(__FILE__)."/../../../uploads/modules/temp/".$system_module_name."_".preg_replace("/[^0-9]/ui","_",$version_info->latest_version)."/";
				if (!is_dir($dest_dir)) {
					if (!@mkdir($dest_dir)) {
						$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
						return false;
					}
				}
				$this->unzip->extract($filename,$dest_dir);
				$module_dir=dirname(__FILE__)."/../../../modules/".$system_module_name."/";
				for($i=0;$i<count($version_info->changed_files);$i++) {
					$cur_dir=$module_dir.$version_info->changed_files[$i];
					$cur_dir=explode("/",$cur_dir);
					$temp_cur_dir="";
					for($dp=0;$dp<count($cur_dir)-1;$dp++) {
						$temp_cur_dir.=($dp==0?"":"/").$cur_dir[$dp];
					}
					if (!is_dir($temp_cur_dir)) mkdir($temp_cur_dir,0777,true);
					if (file_exists($module_dir.$version_info->changed_files[$i])) {
						if (!is_writable($module_dir.$version_info->changed_files[$i])) {
							$this->notifications->setError($this->lang->line("module_files_not_allowed_for_writing"));
							return false;							
						}
					} else {
						if (!@touch($module_dir.$version_info->changed_files[$i])) {
							$this->notifications->setError($this->lang->line("module_files_not_allowed_for_writing"));
							return false;													
						}
					}
				}
				for($i=0;$i<count($version_info->changed_files);$i++) {
					copy($dest_dir.$version_info->changed_files[$i],$module_dir.$version_info->changed_files[$i]);
				}
				$this->load->helper("file");
				@delete_files(dirname(__FILE__)."/../../../uploads/modules/",true);
				@rmdir(dirname(__FILE__)."/../../../uploads/modules/");				
				$this->load->model("ModulesModel");
				$this->ModulesModel->refreshModules();
				$this->updated_module=$current_module->title;
				$this->updated_version=$version_info->latest_version;
				$return=true;
				$this->SystemLog->write("modules","shop","update",2,"Module \"".$current_module->title."\" has been updated to the latest actual version v.".$version_info->latest_version);
			}
		}
		return $return;
	}
	
	function getPreviousVersions($system_module_id,$system_module_name){
		$return=false;
		$this->db->select("modules.*");
		$this->db->from("modules as modules");
		$this->db->where("name",$system_module_name);
		$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$current_module=$result[0];
			$return=$this->_makeRequest("getPreviousVersions",array("module_id"=>$system_module_id,"current_version"=>$current_module->version));
		}
		return $return;
	}
	
	function rollbackVersion($system_module_id,$system_module_name,$version){
		$return=false;
		$this->db->select("modules.*");
		$this->db->from("modules as modules");
		$this->db->where("name",$system_module_name);
		$query=$this->db->get();
		$result=$query->result();
		if (count($result)>0) {
			$current_module=$result[0];
			$version_files=$this->_makeRequest("getVersionFiles",array("module_id"=>$system_module_id,"version"=>$version));
			if ($version!=$current_module->version) {
				$temp_dir=dirname(__FILE__)."/../../../uploads/modules/";
				if (!is_dir($temp_dir)) {
					if (!@mkdir($temp_dir)) {
						$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
						return false;
					}
				}
				$temp_dir=dirname(__FILE__)."/../../../uploads/modules/temp/";
				if (!is_dir($temp_dir)) {
					if (!@mkdir($temp_dir)) {
						$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
						return false;
					}
				}		
				$filename=$temp_dir.$system_module_name."_".preg_replace("/[^0-9]/ui","_",$version).".zip";
				if (!@touch($filename)){
					$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
					return false;				
				}
				$this->_requestFile("getDowngradeDistr",$filename,array("module_id"=>$system_module_id,"version"=>$version));
				$this->load->library("unzip");
				$dest_dir=dirname(__FILE__)."/../../../uploads/modules/temp/".$system_module_name."_".preg_replace("/[^0-9]/ui","_",$version)."/";
				if (!is_dir($dest_dir)) {
					if (!@mkdir($dest_dir)) {
						$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
						return false;
					}
				}
				$this->unzip->extract($filename,$dest_dir);
				$module_dir=dirname(__FILE__)."/../../../modules/".$system_module_name."/";
				for($i=0;$i<count($version_files);$i++) {
					if (file_exists($module_dir.$version_files[$i]->file)) {
						if (!is_writable($module_dir.$version_files[$i]->file)) {
							$this->notifications->setError($this->lang->line("module_files_not_allowed_for_writing"));
							return false;							
						}
					} else {
						if (!@touch($module_dir.$version_files[$i]->file)) {
							$this->notifications->setError($this->lang->line("module_files_not_allowed_for_writing"));
							return false;													
						}
					}
				}
				for($i=0;$i<count($version_files);$i++) {
					copy($dest_dir.$version_files[$i]->file,$module_dir.$version_files[$i]->file);
				}
				$this->load->helper("file");
				@delete_files(dirname(__FILE__)."/../../../uploads/modules/",true);
				@rmdir(dirname(__FILE__)."/../../../uploads/modules/");				
				$this->load->model("ModulesModel");
				$this->ModulesModel->refreshModules();
				$this->downgraded_module=$current_module->title;
				$this->downgraded_version=$version;
				$return=true;
				$this->SystemLog->write("modules","shop","rollback",2,"Module \"".$current_module->title."\" has been downgraded to version v.".$version);
			}
		}
		return $return;	
	}
	
	function getVersionsInfo(){
		$return=new stdClass;
		$return->modules=array();
		$return->engine=new stdClass;
		$return->engine->latest_version=$this->config->config['version'];
		$return->engine->all_versions=array($this->config->config['version']);
		$this->db->select("*");
		$this->db->from("modules");
		$query=$this->db->get();
		$result=$query->result();
		$modules=array();
		for($i=0;$i<count($result);$i++) {
			$return->modules[$result[$i]->name]=$result[$i]->version;
			$modules[]=$result[$i]->name;
		}
		$all_versions_info=$this->_makeRequest("getAllVersionsInfo",array("modules"=>$modules));
		if (count($all_versions_info->modules)) {
			foreach($all_versions_info->modules as $module=>$version){
				if (isset($return->modules[$module])) $return->modules[$module]=$version;
			}
		}
		if ($all_versions_info->engine->latest_version!="") {
			$return->engine->latest_version=$all_versions_info->engine->latest_version;
		}
		if (count($all_versions_info->engine->all_versions)>0) {
			$return->engine->all_versions=$all_versions_info->engine->all_versions;
		}
		return $return;
	}
	
	function getEngineTextVersionInfo(){
		return $this->_makeRequest("getEngineTextVersionInfo");
	}
	
	function updateEngineVersion(){
		$return=false;
		$version_info=$this->_makeRequest("getEngineVersionInfo",array("current_version"=>$this->config->config['version']));
		if ($version_info->latest_version!=$this->config->config['version']) {
			$temp_dir=dirname(__FILE__)."/../../../uploads/modules/";
			if (!is_dir($temp_dir)) {
				if (!@mkdir($temp_dir)) {
					$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
					return false;
				}
			}
			$temp_dir=dirname(__FILE__)."/../../../uploads/modules/temp/";
			if (!is_dir($temp_dir)) {
				if (!@mkdir($temp_dir)) {
					$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
					return false;
				}
			}		
			$filename=$temp_dir."engine_".preg_replace("/[^0-9]/ui","_",$version_info->latest_version).".zip";
			if (!@touch($filename)){
				$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
				return false;				
			}
			$this->_requestFile("getEngineUpdateDistr",$filename,array("current_version"=>$this->config->config['version']));
			$this->load->library("unzip");
			$dest_dir=dirname(__FILE__)."/../../../uploads/modules/temp/engine_".preg_replace("/[^0-9]/ui","_",$version_info->latest_version)."/";
			if (!is_dir($dest_dir)) {
				if (!@mkdir($dest_dir)) {
					$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
					return false;
				}
			}
			$this->unzip->extract($filename,$dest_dir);
			$home_dir=dirname(__FILE__)."/../../../";
			for($i=0;$i<count($version_info->changed_files);$i++) {
				if (file_exists($home_dir.$version_info->changed_files[$i])) {
					if (!is_writable($home_dir.$version_info->changed_files[$i])) {
						$this->notifications->setError($this->lang->line("engine_files_not_allowed_for_writing"));
						return false;							
					}
				} else {
					if (!is_dir(dirname($home_dir.$version_info->changed_files[$i]))) @mkdir(dirname($home_dir.$version_info->changed_files[$i]));
					if (!@touch($home_dir.$version_info->changed_files[$i])) {
						$this->notifications->setError($this->lang->line("engine_files_not_allowed_for_writing"));
						return false;													
					}
				}
			}
			for($i=0;$i<count($version_info->changed_files);$i++) {
				copy($dest_dir.$version_info->changed_files[$i],$home_dir.$version_info->changed_files[$i]);
			}
			$fh=fopen($home_dir."application/config/config.php","r");
			$config_content=fread($fh,filesize($home_dir."application/config/config.php"));
			fclose($fh);
			$config_content=preg_replace("/\['version'\] = '([^']+)';/sui","['version'] = '".$version_info->latest_version."';",$config_content);
			$fh=fopen($home_dir."application/config/config.php","w");
			fwrite($fh,$config_content);
			fclose($fh);
			$this->load->helper("file");
			@delete_files(dirname(__FILE__)."/../../../uploads/modules/",true);
			@rmdir(dirname(__FILE__)."/../../../uploads/modules/");				
			$this->updated_version=$version_info->latest_version;
			$return=true;
			$this->SystemLog->write("modules","shop","updateengine",2,"The engine has been updated to the latest actual version v.".$version_info->latest_version);
		}
		return $return;
	}	
	
	function getPreviousEngineVersions(){
		$return=false;
		$return=$this->_makeRequest("getEnginePreviousVersions",array("current_version"=>$this->config->config['version']));
		return $return;
	}
	
	function rollbackEngineVersion($version){
		$return=false;
		$version_files=$this->_makeRequest("getEngineVersionFiles",array("version"=>$version));
		if ($version!=$this->config->config['version']) {
			$temp_dir=dirname(__FILE__)."/../../../uploads/modules/";
			if (!is_dir($temp_dir)) {
				if (!@mkdir($temp_dir)) {
					$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
					return false;
				}
			}
			$temp_dir=dirname(__FILE__)."/../../../uploads/modules/temp/";
			if (!is_dir($temp_dir)) {
				if (!@mkdir($temp_dir)) {
					$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
					return false;
				}
			}		
			$filename=$temp_dir."engine_".preg_replace("/[^0-9]/ui","_",$version).".zip";
			if (!@touch($filename)){
				$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
				return false;				
			}
			$this->_requestFile("getEngineDowngradeDistr",$filename,array("version"=>$version));
			$this->load->library("unzip");
			$dest_dir=dirname(__FILE__)."/../../../uploads/modules/temp/engine_".preg_replace("/[^0-9]/ui","_",$version)."/";
			if (!is_dir($dest_dir)) {
				if (!@mkdir($dest_dir)) {
					$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
					return false;
				}
			}
			$this->unzip->extract($filename,$dest_dir);
			$home_dir=dirname(__FILE__)."/../../../";
			for($i=0;$i<count($version_files);$i++) {
				if (file_exists($home_dir.$version_files[$i]->file)) {
					if (!is_writable($home_dir.$version_files[$i]->file)) {
						$this->notifications->setError(str_replace("[file]",$version_files[$i]->file,$this->lang->line("file_x_not_allowed_for_writing")));
						return false;							
					}
				} else {
					if (!@touch($home_dir.$version_files[$i]->file)) {
						$folder=explode("/",$version_files[$i]->file);
						$folder=str_replace($version_files[$i]->file,"",$folder[count($folder)-1]);
						$this->notifications->setError(str_replace("[folder]",$folder,$this->lang->line("folder_x_not_allowed_for_writing")));
						return false;													
					}
				}
			}
			for($i=0;$i<count($version_files);$i++) {
				copy($dest_dir.$version_files[$i]->file,$home_dir.$version_files[$i]->file);
			}
			$fh=fopen($home_dir."application/config/config.php","r");
			$config_content=fread($fh,filesize($home_dir."application/config/config.php"));
			fclose($fh);
			$config_content=preg_replace("/\['version'\] = '([^']+)';/sui","['version'] = '".$version."';",$config_content);
			$fh=fopen($home_dir."application/config/config.php","w");
			fwrite($fh,$config_content);
			fclose($fh);			
			$this->load->helper("file");
			@delete_files(dirname(__FILE__)."/../../../uploads/modules/",true);
			@rmdir(dirname(__FILE__)."/../../../uploads/modules/");				
			$this->downgraded_version=$version;
			$return=true;
			$this->SystemLog->write("modules","shop","rollbackengine",2,"The engine has been downgraded to version v.".$version);
		}
		return $return;	
	}	
	
	function prepareAdpativePayment($data,$module_id=0) {
		if (isset($data['_free_install']) && $module_id>0) {
			$this->load->model('ModulesModel');
			$item=$this->getModule($module_id);
			$total=0;
			for($i=0;$i<count($item->dependencies);$i++) {
				if (!$this->ModulesModel->checkInstalledModule($item->dependencies[$i]->name)) {
					$total+=$item->dependencies[$i]->price;
				}
			}		
			$total+=$item->price;
			if ($total==0) {
				$return=new stdClass;
				$return->redirect=$_SERVER['HTTP_REFERER'];
				$return->status="ok";
				$temp_dir=dirname(__FILE__)."/../../../uploads/modules/";
				if (!is_dir($temp_dir)) {
					if (!@mkdir($temp_dir)) {
						$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
						return $return;
					}
				}
				$temp_dir=dirname(__FILE__)."/../../../uploads/modules/temp/";
				if (!is_dir($temp_dir)) {
					if (!@mkdir($temp_dir)) {
						$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
						return $return;
					}
				}	
				$this->load->library("unzip");						
				$installed_modules=array();
				if (isset($data['related_modules'])) {
					foreach($data['related_modules'] as $module_id) {
						if (!in_array($module_id,$installed_modules)) {
							$installed_modules[]=$module_id;
							$version_info=$this->_makeRequest("getVersionInfo",array("module_id"=>$module_id,"current_version"=>"0"));
							$filename=$temp_dir.$version_info->module."_".preg_replace("/[^0-9]/ui","_",$version_info->latest_version).".zip";
							if (!@touch($filename)){
								$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
								return $return;			
							}
							$this->_requestFile("getUpdateDistr",$filename,array("module_id"=>$module_id,"current_version"=>"0"));
							$this->load->library("unzip");
							$module_dir=dirname(__FILE__)."/../../../modules/".$version_info->module."/";
							if (!is_dir($module_dir)) {
								if (!@mkdir($module_dir)) {
									$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
									return $return;	
								}
							}					
							$this->unzip->extract($filename,$module_dir);		
							$module=$this->getModule($module_id);
							$this->SystemLog->write("modules","shop","update",1,"Module \"".$module->title."\" has been installed in the system");						
						}
					}
				}
				if (isset($data['modules'])) {
					foreach($data['modules'] as $module_id) {
						if (!in_array($module_id,$installed_modules)) {
							$installed_modules[]=$module_id;
							$version_info=$this->_makeRequest("getVersionInfo",array("module_id"=>$module_id,"current_version"=>"0"));
							$filename=$temp_dir.$version_info->module."_".preg_replace("/[^0-9]/ui","_",$version_info->latest_version).".zip";
							if (!@touch($filename)){
								$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
								return $return;				
							}
							$this->_requestFile("getUpdateDistr",$filename,array("module_id"=>$module_id,"current_version"=>"0"));
							$this->load->library("unzip");
							$module_dir=dirname(__FILE__)."/../../../modules/".$version_info->module."/";
							if (!is_dir($module_dir)) {
								if (!@mkdir($module_dir)) {
									$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
									return $return;
								}
							}					
							$this->unzip->extract($filename,$module_dir);		
							$module=$this->getModule($module_id);
							$this->SystemLog->write("modules","shop","update",1,"Module \"".$module->title."\" has been installed in the system");						
						}
					}						
				}
				$this->load->helper("file");
				@delete_files(dirname(__FILE__)."/../../../uploads/modules/",true);
				@rmdir(dirname(__FILE__)."/../../../uploads/modules/");				
				$this->ModulesModel->refreshModules();		
				$this->notifications->setMessage($this->lang->line("new_modules_installed"));		
				return $return;	
			}
		}		
		$token=$this->_storeTransaction($data);
		$data['cancelUrl']=base_url()."modules/shop/cancelpayment/".$token;
		$data['returnUrl']=base_url()."modules/shop/confirmpayment/".$token;
		$response=$this->_makeRequest("getAdaptivePayment",array("data"=>$data),"POST");
		return $response;
	}
	
	private function _storeTransaction($data){
		$token=md5(microtime());
		$uploads_dir=dirname(__FILE__)."/../../../uploads/";
		$data=json_encode($data);
		$fh=fopen($uploads_dir.$token,"w");
		fwrite($fh,$data);
		fclose($fh);
		return $token;
	}
	
	public function completeTransaction($token){
		$return=false;
		if ($token!="") {
			$uploads_dir=dirname(__FILE__)."/../../../uploads/";
			if (file_exists($uploads_dir.$token)) {
				$file_content=file_get_contents($uploads_dir.$token);
				if ($data=@json_decode($file_content,true)) {
					$return=true;
					$temp_dir=dirname(__FILE__)."/../../../uploads/modules/";
					if (!is_dir($temp_dir)) {
						if (!@mkdir($temp_dir)) {
							$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
							return false;
						}
					}
					$temp_dir=dirname(__FILE__)."/../../../uploads/modules/temp/";
					if (!is_dir($temp_dir)) {
						if (!@mkdir($temp_dir)) {
							$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
							return false;
						}
					}	
					$this->load->library("unzip");						
					$installed_modules=array();
					if (isset($data['related_modules'])) {
						foreach($data['related_modules'] as $module_id) {
							if (!in_array($module_id,$installed_modules)) {
								$installed_modules[]=$module_id;
								$version_info=$this->_makeRequest("getVersionInfo",array("module_id"=>$module_id,"current_version"=>"0"));
								$filename=$temp_dir.$version_info->module."_".preg_replace("/[^0-9]/ui","_",$version_info->latest_version).".zip";
								if (!@touch($filename)){
									$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
									return false;				
								}
								$this->_requestFile("getUpdateDistr",$filename,array("module_id"=>$module_id,"current_version"=>"0"));
								$this->load->library("unzip");
								$module_dir=dirname(__FILE__)."/../../../modules/".$version_info->module."/";
								if (!is_dir($module_dir)) {
									if (!@mkdir($module_dir)) {
										$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
										return false;
									}
								}					
								$this->unzip->extract($filename,$module_dir);		
								$module=$this->getModule($module_id);
								$this->SystemLog->write("modules","shop","update",1,"Module \"".$module->title."\" has been installed in the system");						
							}
						}
					}
					if (isset($data['modules'])) {
						foreach($data['modules'] as $module_id) {
							if (!in_array($module_id,$installed_modules)) {
								$installed_modules[]=$module_id;
								$version_info=$this->_makeRequest("getVersionInfo",array("module_id"=>$module_id,"current_version"=>"0"));
								$filename=$temp_dir.$version_info->module."_".preg_replace("/[^0-9]/ui","_",$version_info->latest_version).".zip";
								if (!@touch($filename)){
									$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
									return false;				
								}
								$this->_requestFile("getUpdateDistr",$filename,array("module_id"=>$module_id,"current_version"=>"0"));
								$this->load->library("unzip");
								$module_dir=dirname(__FILE__)."/../../../modules/".$version_info->module."/";
								if (!is_dir($module_dir)) {
									if (!@mkdir($module_dir)) {
										$this->notifications->setError($this->lang->line("cannot_store_update_archive"));
										return false;
									}
								}					
								$this->unzip->extract($filename,$module_dir);		
								$module=$this->getModule($module_id);
								$this->SystemLog->write("modules","shop","update",1,"Module \"".$module->title."\" has been installed in the system");						
							}
						}						
					}
					$this->load->helper("file");
					@delete_files(dirname(__FILE__)."/../../../uploads/modules/",true);
					@rmdir(dirname(__FILE__)."/../../../uploads/modules/");				
					$this->load->model("ModulesModel");
					$this->ModulesModel->refreshModules();
					$this->cancelTransaction($token);
				}
			}
		}
		return $return;
	}
	
	public function cancelTransaction($token){
		$return=false;
		$uploads_dir=dirname(__FILE__)."/../../../uploads/";
		if (file_exists($uploads_dir.$token)) {		
			$return=true;
			@unlink($uploads_dir.$token);
		}
		return $return;
	}
    
}

if (!function_exists('http_build_str')) {
    function http_build_str($query, $prefix='', $arg_separator='', $in_depth=false) {
        if (!is_array($query)) {
            return null;
        }
        if ($arg_separator == '') {
            $arg_separator = ini_get('arg_separator.output');
        }
        $args = array();
        foreach ($query as $key => $val) {
			if (!$in_depth) $name = $prefix.$key;
			else $name = $prefix."[".$key."]";        
        	if (!is_array($val)) {
				if (!is_numeric($name)) {
					$args[] = rawurlencode($name).'='.urlencode($val);
				}
			} else {
				$args[] = http_build_str($val,$name,'',true);
			}
        }
        return implode($arg_separator, $args);
    }
}
?>
