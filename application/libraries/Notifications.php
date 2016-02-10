<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Notifications Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Notifications
 * @author		IQDesk
 * @link		http://iqdesk.net
 */
class CI_Notifications {

	private $CI;
	var $errors_template;
	var $messages_template;
	var $notifications;

	public function __construct() {
		$this->CI=& get_instance();
		$this->_loadNotificationsConfig();
		$this->notifications=new stdClass;
		$this->notifications->errors=array();
		$this->notifications->messages=array();
		log_message("debug", "Notifications Class Initialized");
	}

	private function _loadNotificationsConfig(){
		$acl_file_loaded=false;
		if (file_exists(APPPATH."config/".ENVIRONMENT."/notifications.php")) {
			require(APPPATH."config/".ENVIRONMENT."/notifications.php");
			$notifications_file_loaded=true;
		} else {
			if (file_exists(APPPATH."config/notifications.php")) {
				require(APPPATH."config/notifications.php");
				$notifications_file_loaded=true;
			} else {
				echo "Notifications configuration file is not found.";
			}			
		}
		if ($notifications_file_loaded) {
			$this->errors_template=$acl["errors_template"];
			$this->messages_template=$acl["messages_template"];
		}
	}
	
	public function setError($error=""){
		if ($error!="") {
			$notifications=$this->CI->session->userdata('notifications');
			if (isset($notifications->messages)) {
				$this->notifications->messages=$notifications->messages;
			}
			if (isset($notifications->errors)) {
				$this->notifications->errors=$notifications->errors;
			}			
			if (!is_array($this->notifications->messages)) $this->notifications->messages=array();
			if (!is_array($this->notifications->errors)) $this->notifications->errors=array();
			if (!in_array($error,$this->notifications->errors)) {
				$this->notifications->errors[]=$error;
				$this->CI->session->set_userdata('notifications', $this->notifications);
			}
		}
		return false;
	}
	
	public function clearError($error="") {
		if ($error!="") {
			$notifications=$this->CI->session->userdata('notifications');
			if (isset($notifications->messages)) {
				$this->notifications->messages=$notifications->messages;
			}
			if (isset($notifications->errors)) {
				$this->notifications->errors=$notifications->errors;
			}			
			if (!is_array($this->notifications->messages)) $this->notifications->messages=array();
			if (!is_array($this->notifications->errors)) $this->notifications->errors=array();
			$errors=array();
			for($i=0;$i<count($this->notifications->errors);$i++){
				if ($this->notifications->errors[$i]!=$error) $errors[]=$this->notifications->errors[$i];
			}
			$this->notifications->errors=$errors;
			$this->CI->session->set_userdata('notifications', $this->notifications);
		}
		return false;
	}
	
	public function setMessage($message=""){
		if ($message!="") {
			$notifications=$this->CI->session->userdata('notifications');
			if (isset($notifications->messages)) {
				$this->notifications->messages=$notifications->messages;
			}
			if (isset($notifications->errors)) {
				$this->notifications->errors=$notifications->errors;
			}			
			if (!is_array($this->notifications->messages)) $this->notifications->messages=array();
			if (!is_array($this->notifications->errors)) $this->notifications->errors=array();
			if (!in_array($message,$this->notifications->messages)) {
				$this->notifications->messages[]=$message;
				$this->CI->session->set_userdata('notifications', $this->notifications);			
			}
		}
		return false;
	}
	
	public function clearMessage($message="") {
		if ($error!="") {
			$notifications=$this->CI->session->userdata('notifications');
			if (isset($notifications->messages)) {
				$this->notifications->messages=$notifications->messages;
			}
			if (isset($notifications->errors)) {
				$this->notifications->errors=$notifications->errors;
			}			
			if (!is_array($this->notifications->messages)) $this->notifications->messages=array();
			if (!is_array($this->notifications->errors)) $this->notifications->errors=array();
			$messages=array();
			for($i=0;$i<count($this->notifications->messages);$i++){
				if ($this->notifications->messages[$i]!=$message) $messages[]=$this->notifications->messages[$i];
			}
			$this->notifications->messages=$messages;
			$this->CI->session->set_userdata('notifications', $this->notifications);
		}	
		return false;
	}		
	
	public function getErrors(){
		$notifications->errors=$this->CI->session->userdata('notifications');
		return $notifications->errors;
	}
	
	public function getMessages(){
		$notifications->messages=$this->CI->session->userdata('notifications');
		return $notifications->messages;		
	}
	
	public function checkNotifications(){
		$notifications=$this->CI->session->userdata('notifications');
		$total_count=0;
		if (isset($notifications->messages)) $total_count+=count($notifications->messages);
		if (isset($notifications->errors)) $total_count+=count($notifications->errors);
		if ($total_count>0) return true;
		else return false;
	}
	
	public function getNotifications(){
		$notifications=$this->CI->session->userdata('notifications');
		return $notifications;		
	}	
	
	public function drawErrors(){
		$notifications=$this->CI->session->userdata('notifications');
		if (isset($notifications->errors)) {
			if (count($notifications->errors)>0) {
				$errors=implode("<br/>",$notifications->errors);
				echo str_replace("{errors}",$errors,$this->errors_template);
			}
		}
		$this->CI->session->unset_userdata('notifications');
		return false;
	}
	
	public function drawMessages(){
		$notifications=$this->CI->session->userdata('notifications');
		if (isset($notifications->messages)) {
			if (count($notifications->messages)>0) {
				$messages=implode("<br/>",$notifications->messages);
				echo str_replace("{messages}",$messages,$this->messages_template);
			}
		}
		$this->CI->session->unset_userdata('notifications');
		return false;
	}
	
	public function drawNotifications(){
		$notifications=$this->CI->session->userdata('notifications');
		if (isset($notifications->messages)) {
			if (count($notifications->messages)>0) {
				$messages=implode("<br/>",$notifications->messages);
				echo str_replace("{messages}",$messages,$this->messages_template);
			}
		}
		if (isset($notifications->errors)) {
			if (count($notifications->errors)>0) {
				$errors=implode("<br/>",$notifications->errors);
				echo str_replace("{errors}",$errors,$this->errors_template);
			}
		}
		$this->CI->session->unset_userdata('notifications');		
		return false;	
	}	
	
	public function clearNotifications(){
		$this->notifications=new stdClass;
		$this->notifications->errors=array();
		$this->notifications->messages=array();
		$this->CI->session->unset_userdata('notifications');
		return false;
	}	
	
}
// END Notifications Class

/* End of file Notifications.php */
/* Location: ./application/libraries/Notifications.php */