<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SystemLog extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->database();
		$this->_checkLogTable(500000);
	}
	
	private function _checkLogTable($count){
		$this->db->select("COUNT(id) as log_count");
		$this->db->from("log");
		$query=$this->db->get();
		$result=$query->result();
		if ($result[0]->log_count>$count && ($this->acl->checkPermissions("log","listing","clear") || $this->acl->checkPermissions("log","listing","delete") || $this->acl->checkPermissions("log","listing","batchdelete"))) {
			$module=CI::$APP->router->fetch_module();
			$controller=$this->router->class;
			$action=$this->router->method;
			if ($module!="log" || ($module=="log" && $controller!="listing") || ($module=="log" && $controller=="listing" && $action!="clear" && $action!="delete" && $action!="batchdelete")) {
				$this->notifications->setError($this->language->getModuleLanguageLine("log","too_many_log_records"));
			} else {
				$this->notifications->clearError($this->language->getModuleLanguageLine("log","too_many_log_records"));
			}
		}
	}
    
	function getItems($params=array(),$sorting=array(),$page=-1) {
		$return=array();
		$this->db->select("log.*,users.full_name as made_by_name");
		$this->db->from("log as log");
		$this->db->join("users as users","users.id=log.made_by","left");		
		if (isset($params['description'])) {
			if (str_replace(" ","",$params['description'])!="") {
				$this->db->where("log.`description` LIKE '%".$this->db->escape_like_str($params['description'])."%'",NULL,false);
			}
		}					
		if (isset($params['log_type'])) {
			if ($params['log_type']>0) {
				$this->db->where("log.log_type",$params['log_type']);
			}
		}
		if (isset($params['made_by'])) {
			if ($params['made_by']>0) {
				$this->db->where("log.made_by",$params['made_by']);
			}
		}
		if (isset($params['period_from'])) {
			$temp=explode("/",$params['period_from']);
			if (count($temp)==3) {
				$date=$temp[2]."-".$temp[1]."-".$temp[0]." 00:00:00";
				$this->db->where("log.`date_time`>='".$date."'",NULL,false);
			}
		}
		if (isset($params['period_to'])) {
			$temp=explode("/",$params['period_to']);
			if (count($temp)==3) {
				$date=$temp[2]."-".$temp[1]."-".$temp[0]." 23:59:59";
				$this->db->where("log.`date_time`<='".$date."'",NULL,false);
			}
		}				
		if (isset($sorting['sort-column']) && isset($sorting['sort-direction'])) {
			$this->db->order_by($sorting['sort-column'],$sorting['sort-direction']);
		} else {
			$this->db->order_by("log.date_time","desc");
		}
		$this->event->register("BuildLogQuery");
		$this->total_count=$this->db->get_total_count();
		if ($page!=-1) {
			$this->db->limit($this->pagination->count_per_page,$page*$this->pagination->count_per_page);
		}
		$query=$this->db->get();
		$return=$query->result();
		return $return;
    }
    
    function delete($item_id){
    	$this->event->register("BeforeDeleteLogRecord",$item_id);
		$this->db->where("id",$item_id);
		$this->db->delete("log");
		$this->event->register("AfterDeleteLogRecord",$item_id);
		return true;    	
    }
    
    function clear(){
    	$this->event->register("BeforeClearLog");
    	$table_prefix=$this->db->dbprefix;
    	$query="TRUNCATE ".$table_prefix."log";
    	$this->db->query($query);
    	$this->event->register("AfterClearLog");
    	return true;
    }
    
    function batchDelete($ids){
    	$this->event->register("BeforeDeleteLogRecords",$ids);
    	$this->deleted_records=0;
    	for($i=0;$i<count($ids);$i++){
    		$this->db->where("id",$ids[$i]);
    		$this->db->delete("log");
    		$this->deleted_records++;
    	}
    	$this->event->register("AfterDeleteLogRecords",$ids);
    	return true;
    }
    
    function write($module,$controller,$action,$type,$description){
    	$this->event->register("BeforeWriteLogRecord",$module,$controller,$action,$type,$description);
		if ($module!="" && $controller!="" && $action!="" && $type!="" && $description!="") {
			$insert=array(
				"log_type"=>$type,
				"description"=>$description,
				"date_time"=>date("Y-m-d H:i:s",time()),
				"made_by"=>$this->session->userdata("user_id")
			);
			$this->db->insert("log",$insert);
			$record_id=$this->db->insert_id();
			$this->event->register("AfterWriteLogRecord",$module,$controller,$action,$type,$description,$record_id);
		}
	}
    
}
?>