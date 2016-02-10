<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ShareModel extends CI_Model {

	private $_records_options=array();

	function __construct() {
		parent::__construct();
		$this->load->database();
	}
   	
    function getItems($params=array(),$sorting=array(),$page=-1) {          
        $return=array();        
        $this->db->select("*");
        $this->db->from("share_records");
        if (isset($params['name'])) {
            if (str_replace(" ","",$params['name'])!="") {
                $this->db->where("`name` LIKE '%".$this->db->escape_like_str($params['name'])."%'",NULL,false);
            }
        }
                            
        if (isset($sorting['sort-column']) && isset($sorting['sort-direction'])) {
            $this->db->order_by($sorting['sort-column'],$sorting['sort-direction']);
        } else {
            $this->db->order_by("name","asc");
        }
        
        $this->total_count=$this->db->get_total_count();
        if ($page!=-1) {
            $this->db->limit($this->pagination->count_per_page,$page*$this->pagination->count_per_page);
        }
        $query=$this->db->get();
        $return=$query->result();
        return $return;
    }
    
    function create($data){        
        $return=true;
        $this->db->select("id");
        $this->db->from("share_records");
        $this->db->where("name",$data['name']); 
        $query=$this->db->get();
        $result=$query->result();
        if (count($result)>0) {
            $return=false;
            $this->notifications->setError("\"".$data['name']."\" ".$this->lang->line("share_name_already_used"));
        }
        if ($return) {
            $insert=array(
                "name"=>$data['name'],
                "buy_price"=>$data['buy_price'],
                "sell_price"=>$data['sell_price'],
                "quantity"=>$data['quantity'],
                "commission"=>$data['commission'],
                "state" => 1
            );
            $this->db->insert("share_records",$insert);    
            $item_id=$this->db->insert_id();            
        }
        return $return;
    }
    
    function changeState($id, $state)
    {    
        $update=array(            
            "status"=>$state
        );
        $this->db->where("id",$id);
        $this->db->update("share_records",$update);
    }
   
   function getItem($item_id){
        $return=false;
        $this->db->select("*");
        $this->db->from("share_records");
        $this->db->where("id",$item_id);        
        $query=$this->db->get();
        $result=$query->result();
        if (count($result)>0) {
            $return=$result[0];
        }
        return $return;
    }

   function update($data,$item_id){
                                         
        $client=$this->getItem($item_id);    
        $return=true;
        $this->db->select("id");
        $this->db->from("share_records");        
        $this->db->where("id !=",$item_id);
        $query=$this->db->get();
        $result=$query->result();
        if (count($result)>0) {
            $return=false;
            $this->notifications->setError("\"".$data['name']."\" ".$this->lang->line("share_name_already_used"));
        }    
        if ($return) {
            $update=array(
                "name"=>$data['name'],
                "buy_price"=>$data['buy_price'],
                "sell_price"=>$data['sell_price'],
                "quantity"=>$data['quantity'],
                "commission"=>$data['commission']
            );
            $this->db->where("id",$item_id);
            $this->db->update("share_records",$update);                
        }
        return $return;
    } 
    
    function delete($item_id){
        $client=$this->getItem($item_id);    
        $this->db->where("id",$item_id);
        $this->db->delete("share_records");
        return true;
    } 
  
}
?>