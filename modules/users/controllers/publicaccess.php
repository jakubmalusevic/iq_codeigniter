<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PublicAccess extends MX_Controller {
	
	var $check_permissions=false;

	public function login(){
		$this->view_data['page_title']=$this->lang->line("login");
		if ($data=$this->input->post(NULL, TRUE)) {
			if ($this->SystemUser->processLogin($data)) {
				$this->notifications->setMessage($this->lang->line("successful_login"));
			} else {
				$this->notifications->setError($this->lang->line("username_or_password_wrong"));
				redirect('');
			}
		}
		if ($this->session->userdata("user_id")>0) {
			redirect($this->SystemUser->getPrimaryRedirection());
		}
		$this->load->view('general/header',$this->view_data);
		$this->load->view('publicaccess/login',$this->view_data);
		$this->load->view('general/footer',$this->view_data);
	}
	
	public function logout(){
		$this->SystemUser->processLogout();
		redirect('');		
	}
	
	public function reset(){
		$output=new stdClass;
		$output->result="error";
		$output->message=$this->lang->line("email_not_found");
		if ($data=$this->input->post(NULL, TRUE)) {
			if (@$data['email']!="") {
				$this->db->select("users.*");
				$this->db->from("users");
				$this->db->where("users.email",$data['email']);
				$query=$this->db->get();
				$result=$query->result();
				if (count($result)>0) {
					$user=$result[0];
					$reset_token=md5(microtime());
					$this->db->where("id",$user->id);
					$this->db->update("users",array("reset_token"=>$reset_token));
					$output->result="ok";
					$output->message=$this->lang->line("email_with_instructions_has_been_sent");	
					$email_subject=$this->lang->line("resetting_password_mail_subject");	
					$email_body=$this->lang->line("resestting_password_mail_body");	
					$replace_what=array(
						"[user_full_name]",
						"[reset_password_link]"
					);
				
					$replace_for=array(
						$user->full_name,
						"<a href=\"".base_url()."users/publicaccess/resetstep2/".$reset_token."\">".base_url()."users/publicaccess/resetstep2/".$reset_token."</a>"
					);
					$email_subject=str_replace($replace_what,$replace_for,$email_subject);
					$email_body=str_replace($replace_what,$replace_for,$email_body);
					$this->load->library('email');
					$email_config['mailtype']="html";
					$this->email->initialize($email_config);
					$this->email->from('no-reply@'.$_SERVER['SERVER_NAME']);
					$this->email->to($user->email);
					$this->email->subject($email_subject);					
					$this->email->message($email_body);
					$this->email->send();										
				}
			}
		}
		echo json_encode($output);
		die();
	}
	
	public function resetstep2($token=""){
		$this->view_data['page_title']=$this->lang->line("reset_password");
		$this->view_data['user_found']=false;
		$this->view_data['user_name']="";
		$this->view_data['reset_token']="";
		$token=trim($token);
		if ($token!="") {
			$this->db->select("users.*");
			$this->db->from("users");
			$this->db->where("users.reset_token",$token);
			$query=$this->db->get();
			$result=$query->result();
			if (count($result)>0) {
				$user=$result[0];
				$this->view_data['user_found']=true;
				$this->view_data['user_name']=$user->username;
				$this->view_data['reset_token']=$token;			
			}
		}
		$this->load->view('general/header',$this->view_data);
		$this->load->view('publicaccess/resetstep2',$this->view_data);
		$this->load->view('general/footer',$this->view_data);	
	}
	
	public function newpassword(){
		$output=new stdClass;
		$output->result="error";
		$output->message=$this->lang->line("security_token_is_incorrect");
		if ($data=$this->input->post(NULL, TRUE)) {
			if (@$data['reset_token']!="") {
				$this->db->select("users.*");
				$this->db->from("users");
				$this->db->where("users.reset_token",$data['reset_token']);
				$query=$this->db->get();
				$result=$query->result();
				if (count($result)>0) {
					$user=$result[0];
					$update=array(
						"password"=>md5($data['password']),
						"reset_token"=>""
					);
					$this->db->where("id",$user->id);
					$this->db->update("users",$update);
					$output->result="ok";
					$output->message=$this->lang->line("password_changed_successfully");											
				}
			}
		}
		echo json_encode($output);
		die();	
	}
	
}