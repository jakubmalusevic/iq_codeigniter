<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Listing extends MX_Controller {
    
    var $check_permissions=true;

    public function index(){                            
        $this->view_data['page_title']=$this->lang->line("clients");
        $params=array();
        $get=$this->input->get(NULL, TRUE, TRUE);
        if (isset($get['apply_filters']) && isset($get['filter'])) {
            $params=$get['filter'];
        }            
        $sorting=array();
        if (isset($get['sort-column']) && @$get['sort-column']!="") {
            $sorting['sort-column']=$get['sort-column'];
            $sorting['sort-direction']="asc";
            if (isset($get['sort-direction'])) {
                if (strtolower($get['sort-direction'])=="asc" || strtolower($get['sort-direction'])=="desc") {
                    $sorting['sort-direction']=$get['sort-direction'];
                }
            }
        }    
        $page=0;
        if (isset($get['page'])) {
            if (is_numeric($get['page']) && $get['page']>=0) {
                $page=$get['page'];
            }
        }                
        $this->load->model('ShareModel');
        $this->view_data['items']=$this->ShareModel->getItems($params,$sorting,$page);
        $total_items=$this->ShareModel->total_count;
        $this->pagination->setNumbers(count($this->view_data['items']),$total_items);
            
        $this->load->view('general/header',$this->view_data);
        $this->load->view('listing/index',$this->view_data);
        $this->load->view('general/footer',$this->view_data);
    }
    
    public function create(){
        $this->load->model('ShareModel');
        if ($data=$this->input->post(NULL, TRUE)) {   
            if ($this->ShareModel->create($data)) {
                $this->notifications->setMessage($this->lang->line("share_created_successfully"));
            }
            redirect($_SERVER['HTTP_REFERER']);
        }    
        $this->load->view('listing/create',$this->view_data);
    }  
    
    public function changestate(){        
        if ($this->uri->segment(4)!==FALSE) {
            $get=$this->input->get(NULL, TRUE, TRUE);
            if (isset($get['state'])) {
                if ($get['state']==0 || $get['state']==1) {
                    $this->load->model('ShareModel');
                    $this->ShareModel->changeState($this->uri->segment(4),$get['state']);                    
                } else {
                    $this->notifications->setError($this->lang->line("wrong_parameters"));
                }
            } else {
                $this->notifications->setError($this->lang->line("wrong_parameters"));
            }
        } else {
            $this->notifications->setError($this->lang->line("record_not_found"));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }      
    
    public function update(){
        $this->load->model('ShareModel');
        if ($this->uri->segment(4)!==FALSE) {
            if ($data=$this->input->post(NULL, TRUE)) {
                if ($this->ShareModel->update($data,$this->uri->segment(4))) {
                    $this->notifications->setMessage($this->lang->line("client_updated_successfully"));
                }
                redirect($_SERVER['HTTP_REFERER']);
            }        
            $this->view_data['item']=$this->ShareModel->getItem($this->uri->segment(4));    
            if ($this->view_data['item']===false) {
                $this->load->view('errors/notfound',$this->view_data);
            } else {
                $this->load->view('listing/update',$this->view_data);
            }
        } else {
            $this->load->view('errors/wrongparameters',$this->view_data);
        }
    }        
    
    public function delete(){
        if ($this->uri->segment(4)!==FALSE) {
            $this->load->model('ShareModel');
            if ($this->ShareModel->delete($this->uri->segment(4))) {
                $this->notifications->setMessage($this->lang->line("share_deleted_successfully"));
            }            
        } else {
            $this->notifications->setError($this->lang->line("wrong_parameters"));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }    
    
}