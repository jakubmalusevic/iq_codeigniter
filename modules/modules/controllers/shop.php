<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shop extends MX_Controller {
	
	var $check_permissions=true;

	public function index(){							
		$this->view_data['page_title']=$this->lang->line("shop");	
		$this->load->model('ShopApiModel');
		$params=array();
		$get=$this->input->get(NULL, TRUE, TRUE);
		if (isset($get['apply_filters']) && isset($get['filter'])) {
			$params=$get['filter'];
		}			
		$page=0;
		if (isset($get['page'])) {
			if (is_numeric($get['page']) && $get['page']>=0) {
				$page=$get['page'];
			}
		}	
		$this->pagination->count_per_page=12;
		$modules=$this->ShopApiModel->getModules($params,$page);
		if ($modules!==false) {		
			$this->pagination->setNumbers(count($modules->items),$modules->total_items);
			$this->view_data['modules']=$modules->items;
		}
		$this->view_data['categories']=$this->ShopApiModel->getCategories();	
		$this->load->view('general/header',$this->view_data);
		$this->load->view('shop/index',$this->view_data);
		$this->load->view('general/footer',$this->view_data);
	}
	
	public function view(){
		if ($this->uri->segment(4)!==FALSE) {
			$this->load->model('ShopApiModel');
			$this->load->model('ModulesModel');
			$this->view_data['module']=$this->ShopApiModel->getModule($this->uri->segment(4));
			if ($this->view_data['module']!==false) {
				$this->view_data['page_title']=$this->lang->line("shop").$this->theme->page_title_delimiter.$this->view_data['module']->title;	
				$this->view_data['categories']=$this->ShopApiModel->getCategories();	
				$this->load->view('general/header',$this->view_data);
				$this->load->view('shop/view',$this->view_data);
				$this->load->view('general/footer',$this->view_data);				
			} else {
				$this->notifications->setError($this->lang->line("module_not_found"));
				redirect($_SERVER['HTTP_REFERER']);			
			}
		} else {
			$this->notifications->setError($this->lang->line("module_not_found"));
			redirect($_SERVER['HTTP_REFERER']);
		}		
	}
	
	public function createreview(){
		$this->load->model('ShopApiModel');
		if ($data=$this->input->post(NULL, TRUE)) {
			if ($this->ShopApiModel->createReview($data)) {
				$this->notifications->setMessage($this->lang->line("review_submitted_successfully"));
			}
		}	
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function viewimage(){
		if ($this->uri->segment(4)!==FALSE) {
			$this->load->model('ShopApiModel');
			$this->view_data['image']=$this->ShopApiModel->getImage($this->uri->segment(4));
			if ($this->view_data['image']!==false) {
				$this->load->view('shop/images/view',$this->view_data);
			} else {
				$this->load->view('errors/notfound',$this->view_data);
			}
		} else {
			$this->load->view('errors/wrongparameters',$this->view_data);
		}		
	}	
	
	public function update(){
		if ($this->uri->segment(4)!==FALSE && $this->uri->segment(5)!==FALSE) {
			$this->load->model('ShopApiModel');
			if ($this->ShopApiModel->updateVersion($this->uri->segment(4),$this->uri->segment(5))) {
				$this->notifications->setMessage(str_replace(array("[module_name]","[version]"),array($this->ShopApiModel->updated_module,$this->ShopApiModel->updated_version),$this->lang->line("version_of_module_x_updated_to_x")));
			}			
		} else {
			$this->notifications->setError($this->lang->line("wrong_parameters"));
		}		
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function rollback(){
		if ($this->uri->segment(4)!==FALSE && $this->uri->segment(5)!==FALSE) {
			$this->load->model('ShopApiModel');
			if ($data=$this->input->post(NULL, TRUE)) {
				if (isset($data['version_id'])) {
					if ($this->ShopApiModel->rollbackVersion($this->uri->segment(4),$this->uri->segment(5),$data['version_id'])) {
						$this->notifications->setMessage(str_replace(array("[module_name]","[version]"),array($this->ShopApiModel->downgraded_module,$this->ShopApiModel->downgraded_version),$this->lang->line("version_of_module_x_downgraded_to_x")));
					}
				}
				redirect($_SERVER['HTTP_REFERER']);
			}				
			$this->view_data['versions']=$this->ShopApiModel->getPreviousVersions($this->uri->segment(4),$this->uri->segment(5));
			$this->view_data['system_module_id']=$this->uri->segment(4);
			$this->view_data['system_module_name']=$this->uri->segment(5);
			if ($this->view_data['versions']!==false) {
				$this->load->view('shop/rollback',$this->view_data);
			} else {
				$this->load->view('errors/notfound',$this->view_data);
			}
		} else {
			$this->load->view('errors/wrongparameters',$this->view_data);
		}		
	}	
	
	public function updateengine(){
		$this->load->model('ShopApiModel');
		$get=$this->input->get(NULL, TRUE, TRUE);
		if (isset($get['confirm'])) {
			if ($this->ShopApiModel->updateEngineVersion()) {
				$this->notifications->setMessage(str_replace("[version]",$this->ShopApiModel->updated_version,$this->lang->line("version_of_engine_updated_to_x")));
			}				
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->view_data['version_info']=$this->ShopApiModel->getEngineTextVersionInfo();
			$this->load->view('shop/updateengine',$this->view_data);
		}
	}
	
	public function rollbackengine(){
		$this->load->model('ShopApiModel');
		if ($data=$this->input->post(NULL, TRUE)) {
			if (isset($data['version_id'])) {
				if ($this->ShopApiModel->rollbackEngineVersion($data['version_id'])) {
					$this->notifications->setMessage(str_replace("[version]",$this->ShopApiModel->downgraded_version,$this->lang->line("version_of_engine_downgraded_to_x")));
				}
			}
			redirect($_SERVER['HTTP_REFERER']);
		}				
		$this->view_data['versions']=$this->ShopApiModel->getPreviousEngineVersions();
		if ($this->view_data['versions']!==false) {
			$this->load->view('shop/rollbackengine',$this->view_data);
		} else {
			$this->load->view('errors/notfound',$this->view_data);
		}	
	}		
	
	public function buy(){
		if ($this->uri->segment(4)!==FALSE && $this->uri->segment(5)!==FALSE) {
			$this->load->model('ShopApiModel');
			$this->load->model('ModulesModel');
			if ($data=$this->input->post(NULL, TRUE)) {
				$response=$this->ShopApiModel->prepareAdpativePayment($data,$this->uri->segment(4));
				if ($response->status=="error") {
					$this->notifications->setError($response->message);
					redirect($_SERVER['HTTP_REFERER']);				 	
				} else {
					redirect($response->redirect);
				}
			}				
			$this->view_data['item']=$this->ShopApiModel->getModule($this->uri->segment(4));
			if ($this->view_data['item']!==false) {
				$this->load->view('shop/buy',$this->view_data);
			} else {
				$this->load->view('errors/notfound',$this->view_data);
			}
		} else {
			$this->load->view('errors/wrongparameters',$this->view_data);
		}		
	}		
	
	public function confirmpayment(){
		if ($this->uri->segment(4)!==FALSE) {
			$this->load->model('ShopApiModel');
			if ($this->ShopApiModel->completeTransaction($this->uri->segment(4))) {
				$this->notifications->setMessage($this->lang->line("transaction_confirmed_modules_installed"));
			} else {
				$this->notifications->setError($this->lang->line("check_folders_permissions_and_reopen_link")."<br/><a href=\"".base_url()."modules/shop/confirmpayment/".$this->uri->segment(4)."\">".base_url()."modules/shop/confirmpayment/".$this->uri->segment(4)."</a>");
			}
		} else {
			$this->notifications->setError($this->lang->line("wrong_income_transaction"));
		}
		redirect("/modules/shop/index");
	}
	
	public function cancelpayment(){
		if ($this->uri->segment(4)!==FALSE) {
			$this->load->model('ShopApiModel');
			if ($this->ShopApiModel->cancelTransaction($this->uri->segment(4))) {
				$this->notifications->setMessage($this->lang->line("transaction_canceled"));
			}			
		}
		redirect("/modules/shop/index");
	}
	
}