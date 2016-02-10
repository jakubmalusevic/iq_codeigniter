<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Model {

	private $_widgets=array();

	function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	private function _get_widget_state($widget_name){
		$return=new stdClass;
		$last_order=0;
		foreach($this->_widgets as $widget){
			if ($widget->state->order>$last_order) $last_order=$widget->state->order;
		}
		$return->order=(floor($last_order/10))*10+10;
		$return->state=1;
		$user_id=$this->session->userdata("user_id");
		if ($user_id>0) {
			$this->db->select("*");
			$this->db->from("dashboard_widgets");
			$this->db->where("user_id",$user_id);
			$this->db->where("widget_name",$widget_name);
			$query=$this->db->get();
			$result=$query->result();
			if (count($result)>0) {
				$return->order=$result[0]->order;
				$return->state=$result[0]->state;
			}
		}
		return $return;
	}
	
	public function registerWidget($params){
		if (isset($params['name'])) {
			if (trim($params['name'])!="") {
				if (!isset($this->_widgets[$params['name']])) {
					if (!isset($params['title'])) $params['title']="";
					if (!isset($params['html'])) $params['html']="";
					if (!isset($params['size'])) $params['size']="1-column";
					$item=new stdClass;
					$item->name=$params['name'];
					$item->title=$params['title'];
					$item->html=$params['html'];
					$item->size=$params['size'];
					$item->state=$this->_get_widget_state($params['name']);
					$this->_widgets[$params['name']]=$item;
				}
			}
		}
	}
	
	public function getWidgets(){
		usort($this->_widgets,array(&$this,"_sortWidgets"));
		return $this->_widgets;
	}
	
	protected function _sortWidgets($a,$b){
		if ($a->state->order<$b->state->order) return -1;
		if ($a->state->order==$b->state->order) return 0;
		if ($a->state->order>$b->state->order) return 1;
	}
	
	public function saveWidgetsState($data){
		$user_id=$this->session->userdata("user_id");
		if ($user_id>0) {	
			foreach($data as $widget){
				$this->db->select("id");
				$this->db->from("dashboard_widgets");
				$this->db->where("user_id",$user_id);
				$this->db->where("widget_name",$widget['name']);
				$query=$this->db->get();
				$result=$query->result();
				if (count($result)>0) {
					$update=array(
						"order"=>$widget['order'],
						"state"=>$widget['state']
					);
					$this->db->where("id",$result[0]->id);
					$this->db->update("dashboard_widgets",$update);
				} else {
					$insert=array(
						"user_id"=>$user_id,
						"widget_name"=>$widget['name'],
						"order"=>$widget['order'],
						"state"=>$widget['state']
					);
					$this->db->insert("dashboard_widgets",$insert);
				}	
			}
		}
	}
	
	public function saveWidgetState($data){
		$user_id=$this->session->userdata("user_id");
		if (isset($data['widget']) && isset($data['state']) && $user_id>0) {
			$this->db->select("id");
			$this->db->from("dashboard_widgets");
			$this->db->where("user_id",$user_id);
			$this->db->where("widget_name",$data['widget']);
			$query=$this->db->get();
			$result=$query->result();
			if (count($result)>0) {
				$update=array("state"=>$data['state']);
				$this->db->where("id",$result[0]->id);
				$this->db->update("dashboard_widgets",$update);
			} else {
				$insert=array(
					"user_id"=>$user_id,
					"widget_name"=>$data['widget'],
					"order"=>$data['order'],
					"state"=>$data['state']
				);
				$this->db->insert("dashboard_widgets",$insert);
			}			
		}
	}
    
}
?>