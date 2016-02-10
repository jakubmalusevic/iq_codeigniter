<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		IQDesk
 * @link		http://iqdesk.net
 */
class CI_Pagination {

	private $CI;
	var $page;
	private $page2display;
	var $displayed;
	var $total_count;
	var $count_per_page=20;
	var $count_of_pages;

	public function __construct() {
		$this->CI=& get_instance();
		$page=0;
		$get=$this->CI->input->get(NULL, TRUE, TRUE);
		if (isset($get['page'])) {
			if (is_numeric($get['page'])) {
				$page=$get['page'];
			}
		}		
		$this->page=$page;
		$this->page2display=$page+1;
		log_message("debug", "Pagination Class Initialized");
	}
	
	public function setNumbers($displayed,$total_count){
		$this->displayed=$displayed;
		$this->total_count=$total_count;
		$this->count_of_pages=ceil($this->total_count/$this->count_per_page);
		if ($this->count_of_pages==0) $this->count_of_pages=1;
		return false;
	}
	
	public function prevPageURL(){
		if ($this->page>0) $page=$this->page-1;
		else $page=$this->page;
		return $this->createURL($page);
	}
	
	public function nextPageURL(){
		if ($this->page<$this->count_of_pages-1) $page=$this->page+1;
		else $page=$this->page;
		return $this->createURL($page);
	}
	
	public function lastPageURL(){
		$page=$this->count_of_pages-1;
		return $this->createURL($page);
	}
	
	public function firstPageURL(){
		$page=0;
		return $this->createURL($page);
	}	
	
	public function getItemsNumbers(){
		if ($this->displayed>0) return (($this->page*$this->count_per_page) + 1)." - ".(($this->page*$this->count_per_page) + $this->displayed);
		else return "0 - 0";
	}		
	
	private function createURL($page) {
		$return=base_url().$this->CI->uri->uri_string();
		$params_parts=explode("?",$_SERVER['REQUEST_URI']);
		if (isset($params_parts[1])) $return.="?".$params_parts[1];
		if (preg_match("/(\?|\&)page=([0-9]+)/sui",$return)) {
			$return=preg_replace("/(\?|\&)page=([0-9]+)/sui","$1page=".$page,$return);
		} else {
			$return=$return.(substr_count($return,"?")>0?"&":"?")."page=".$page;
		}
		return $return;
	}
	
	public function drawPagination($return_html=false){
		$output=$this->CI->load->view('libraries/pagination',array("parent"=>$this,"page2display"=>$this->page2display),true);
		if ($return_html) return $output;
		else echo $output;
		return false;
	}
	
}
// END Pagination Class

/* End of file Pagination.php */
/* Location: ./application/libraries/Pagination.php */