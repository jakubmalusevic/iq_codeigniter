<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SettingsCatcher {

	private $CI;
	private $_default_settings_titles=array(
		"main_title"=>"Browser Tab Title",
		"color_scheme"=>"Color Scheme",
		"page_title_delimiter"=>"Page Title Delimiter",
		"logout_in_navigation"=>"Display Logout in Navigation",
		"language"=>"Default Language",
		"timezone"=>"Timezone"
	);
	private $_timezones=array(
		"Pacific/Midway"=>"(GMT-11:00) Midway Island, Samoa",
		"America/Adak"=>"(GMT-10:00) Hawaii-Aleutian",
		"Etc/GMT+10"=>"(GMT-10:00) Hawaii",
		"Pacific/Marquesas"=>"(GMT-09:30) Marquesas Islands",
		"Pacific/Gambier"=>"(GMT-09:00) Gambier Islands",
		"America/Anchorage"=>"(GMT-09:00) Alaska",
		"America/Ensenada"=>"(GMT-08:00) Tijuana, Baja California",
		"Etc/GMT+8"=>"(GMT-08:00) Pitcairn Islands",
		"America/Los_Angeles"=>"(GMT-08:00) Pacific Time (US & Canada)",
		"America/Denver"=>"(GMT-07:00) Mountain Time (US & Canada)",
		"America/Chihuahua"=>"(GMT-07:00) Chihuahua, La Paz, Mazatlan",
		"America/Dawson_Creek"=>"(GMT-07:00) Arizona",
		"America/Belize"=>"(GMT-06:00) Saskatchewan, Central America",
		"America/Cancun"=>"(GMT-06:00) Guadalajara, Mexico City, Monterrey",
		"Chile/EasterIsland"=>"(GMT-06:00) Easter Island",
		"America/Chicago"=>"(GMT-06:00) Central Time (US & Canada)",
		"America/New_York"=>"(GMT-05:00) Eastern Time (US & Canada)",
		"America/Havana"=>"(GMT-05:00) Cuba",
		"America/Bogota"=>"(GMT-05:00) Bogota, Lima, Quito, Rio Branco",
		"America/Caracas"=>"(GMT-04:30) Caracas",
		"America/Santiago"=>"(GMT-04:00) Santiago",
		"America/La_Paz"=>"(GMT-04:00) La Paz",
		"Atlantic/Stanley"=>"(GMT-04:00) Faukland Islands",
		"America/Campo_Grande"=>"(GMT-04:00) Brazil",
		"America/Goose_Bay"=>"(GMT-04:00) Atlantic Time (Goose Bay)",
		"America/Glace_Bay"=>"(GMT-04:00) Atlantic Time (Canada)",
		"America/St_Johns"=>"(GMT-03:30) Newfoundland",
		"America/Araguaina"=>"(GMT-03:00) UTC-3",
		"America/Montevideo"=>"(GMT-03:00) Montevideo",
		"America/Miquelon"=>"(GMT-03:00) Miquelon, St. Pierre",
		"America/Godthab"=>"(GMT-03:00) Greenland",
		"America/Argentina/Buenos_Aires"=>"(GMT-03:00) Buenos Aires",
		"America/Sao_Paulo"=>"(GMT-03:00) Brasilia",
		"America/Noronha"=>"(GMT-02:00) Mid-Atlantic",
		"Atlantic/Cape_Verde"=>"(GMT-01:00) Cape Verde Is.",
		"Atlantic/Azores"=>"(GMT-01:00) Azores",
		"Europe/Belfast"=>"(GMT) Greenwich Mean Time : Belfast",
		"Europe/Dublin"=>"(GMT) Greenwich Mean Time : Dublin",
		"Europe/Lisbon"=>"(GMT) Greenwich Mean Time : Lisbon",
		"Europe/London"=>"(GMT) Greenwich Mean Time : London",
		"Africa/Abidjan"=>"(GMT) Monrovia, Reykjavik",
		"Europe/Amsterdam"=>"(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna",
		"Europe/Belgrade"=>"(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague",
		"Europe/Brussels"=>"(GMT+01:00) Brussels, Copenhagen, Madrid, Paris",
		"Africa/Algiers"=>"(GMT+01:00) West Central Africa",
		"Africa/Windhoek"=>"(GMT+01:00) Windhoek",
		"Asia/Beirut"=>"(GMT+02:00) Beirut",
		"Africa/Cairo"=>"(GMT+02:00) Cairo",
		"Asia/Gaza"=>"(GMT+02:00) Gaza",
		"Africa/Blantyre"=>"(GMT+02:00) Harare, Pretoria",
		"Asia/Jerusalem"=>"(GMT+02:00) Jerusalem",
		"Europe/Minsk"=>"(GMT+02:00) Minsk",
		"Asia/Damascus"=>"(GMT+02:00) Syria",
		"Europe/Moscow"=>"(GMT+03:00) Moscow, St. Petersburg, Volgograd",
		"Africa/Addis_Ababa"=>"(GMT+03:00) Nairobi",
		"Asia/Tehran"=>"(GMT+03:30) Tehran",
		"Asia/Dubai"=>"(GMT+04:00) Abu Dhabi, Muscat",
		"Asia/Yerevan"=>"(GMT+04:00) Yerevan",
		"Asia/Kabul"=>"(GMT+04:30) Kabul",
		"Asia/Yekaterinburg"=>"(GMT+05:00) Ekaterinburg",
		"Asia/Tashkent"=>"(GMT+05:00) Tashkent",
		"Asia/Kolkata"=>"(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi",
		"Asia/Katmandu"=>"(GMT+05:45) Kathmandu",
		"Asia/Dhaka"=>"(GMT+06:00) Astana, Dhaka",
		"Asia/Novosibirsk"=>"(GMT+06:00) Novosibirsk",
		"Asia/Rangoon"=>"(GMT+06:30) Yangon (Rangoon)",
		"Asia/Bangkok"=>"(GMT+07:00) Bangkok, Hanoi, Jakarta",
		"Asia/Krasnoyarsk"=>"(GMT+07:00) Krasnoyarsk",
		"Asia/Hong_Kong"=>"(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi",
		"Asia/Irkutsk"=>"(GMT+08:00) Irkutsk, Ulaan Bataar",
		"Australia/Perth"=>"(GMT+08:00) Perth",
		"Australia/Eucla"=>"(GMT+08:45) Eucla",
		"Asia/Tokyo"=>"(GMT+09:00) Osaka, Sapporo, Tokyo",
		"Asia/Seoul"=>"(GMT+09:00) Seoul",
		"Asia/Yakutsk"=>"(GMT+09:00) Yakutsk",
		"Australia/Adelaide"=>"(GMT+09:30) Adelaide",
		"Australia/Darwin"=>"(GMT+09:30) Darwin",
		"Australia/Brisbane"=>"(GMT+10:00) Brisbane",
		"Australia/Hobart"=>"(GMT+10:00) Hobart",
		"Asia/Vladivostok"=>"(GMT+10:00) Vladivostok",
		"Australia/Lord_Howe"=>"(GMT+10:30) Lord Howe Island",
		"Etc/GMT-11"=>"(GMT+11:00) Solomon Is., New Caledonia",
		"Asia/Magadan"=>"(GMT+11:00) Magadan",
		"Pacific/Norfolk"=>"(GMT+11:30) Norfolk Island",
		"Asia/Anadyr"=>"(GMT+12:00) Anadyr, Kamchatka",
		"Pacific/Auckland"=>"(GMT+12:00) Auckland, Wellington",
		"Etc/GMT-12"=>"(GMT+12:00) Fiji, Kamchatka, Marshall Is.",
		"Pacific/Chatham"=>"(GMT+12:45) Chatham Islands",
		"Pacific/Tongatapu"=>"(GMT+13:00) Nuku'alofa",
		"Pacific/Kiritimati"=>"(GMT+14:00) Kiritimati"	
	);

	function __construct() {
		$this->CI=& get_instance();
	}
	
	public function onBeforeDeleteModule($module_id){
		$this->CI->db->select("name");
		$this->CI->db->from("modules");
		$this->CI->db->where("id",$module_id);
		$query=$this->CI->db->get();
		$module=$query->result();	
		if (count($module)>0) {
			$module_name=$module[0]->name;
			$this->CI->db->select("id");
			$this->CI->db->from("settings_sections");
			$this->CI->db->where("module",$module_name);
			$query=$this->CI->db->get();
			$section=$query->result();		
			for($i=0;$i<count($section);$i++){
				$section_id=$section[$i]->id;
				$this->CI->db->where("section_id",$section_id);
				$this->CI->db->delete("settings_records");		
			}
			$this->CI->db->where("module",$module_name);
			$this->CI->db->delete("settings_sections");						
		}
	}

	public function onBeforeControllerInitiated($module,$controller,$action){
		$this->CI->load->model("settings/GlobalSettings");
		$this->CI->GlobalSettings->registerSettingsSection("global","Global");
		$theme_settings=array();
		$theme_config_loaded=false;
		if (file_exists(BASEPATH."../".APPPATH."config/".ENVIRONMENT."/theme.php")) {
			require(BASEPATH."../".APPPATH."config/".ENVIRONMENT."/theme.php");
			$theme_config_loaded=true;
		} else {
			if (file_exists(BASEPATH."../".APPPATH."config/theme.php")) {
				require(BASEPATH."../".APPPATH."config/theme.php");
				$theme_config_loaded=true;
			}			
		}	
		if ($theme_config_loaded) {
			$theme_settings=$theme;
			if (is_array($theme_settings)) {
				foreach($theme_settings as $name=>$value) {
					$this->CI->GlobalSettings->registerSetting("global",$name,(isset($this->_default_settings_titles[$name])?$this->_default_settings_titles[$name]:$name),$value);
					$this->CI->GlobalSettings->forceUpdateSetting("global",$name,$value);
				}
			}
		}
		
		$this->CI->load->helper('file');
		
		$themes=array();
		$dir=BASEPATH."../assets/themes";
		if (is_dir($dir)) {
			$dir_info=get_dir_file_info($dir);
			if (is_array($dir_info)) {
				foreach($dir_info as $theme_dir=>$info) {
					if (file_exists($info['server_path']."/theme.css")) {
						$theme_name=basename($info['server_path']);
						$themes[$theme_name]=ucfirst($theme_name);
					}
				}
			}
		}		
		$this->CI->GlobalSettings->setSettingOptions("global","color_scheme",$themes);
		$this->CI->GlobalSettings->setSettingOptions("global","logout_in_navigation",array("0"=>"No","1"=>"Yes"));
		
		$config_settings=array();
		$config_loaded=false;
		if (file_exists(BASEPATH."../".APPPATH."config/".ENVIRONMENT."/config.php")) {
			require(BASEPATH."../".APPPATH."config/".ENVIRONMENT."/config.php");
			$config_loaded=true;
		} else {
			if (file_exists(BASEPATH."../".APPPATH."config/config.php")) {
				require(BASEPATH."../".APPPATH."config/config.php");
				$config_loaded=true;
			}			
		}			
		if ($config_loaded) {
			$config_settings=$config;
			$name="language";
			if (isset($config_settings[$name])) {
				$this->CI->GlobalSettings->registerSetting("global",$name,(isset($this->_default_settings_titles[$name])?$this->_default_settings_titles[$name]:$name),$config_settings[$name]);
				$this->CI->GlobalSettings->forceUpdateSetting("global",$name,$config_settings[$name]);	
				$languages=array();
				$dir=BASEPATH."../".APPPATH."language/";
				if (is_dir($dir)) {
					$dir_info=get_dir_file_info($dir);
					if (is_array($dir_info)) {
						foreach($dir_info as $lang_dir=>$info) {
							if (file_exists($info['server_path']."/main_lang.php")) {
								$lang_name=basename($info['server_path']);
								include($info['server_path']."/main_lang.php");
								if (isset($lang['_lang_name'])) {
									$languages[$lang_name]=$lang['_lang_name'];
								}
							}
						}
					}
				}		
				$this->CI->GlobalSettings->setSettingOptions("global","language",$languages);
			}
		}
		
		$name="timezone";
		$this->CI->GlobalSettings->registerSetting("global",$name,(isset($this->_default_settings_titles[$name])?$this->_default_settings_titles[$name]:$name),"Europe/London");
		@date_default_timezone_set($this->CI->GlobalSettings->getValue("global","timezone","Europe/London"));
		$this->CI->GlobalSettings->setSettingOptions("global","timezone",$this->_timezones);	
		
		$this->CI->GlobalSettings->registerSetting("global","header_html","Header HTML<br/><small style='color:#999;'>Allowed using &lt;?php ... ?&gt; and &lt;?= ... ?&gt; constructions</small>","");
		$this->CI->GlobalSettings->registerSetting("global","footer_html","Footer HTML<br/><small style='color:#999;'>Allowed using &lt;?php ... ?&gt; and &lt;?= ... ?&gt; constructions</small>","");
		
		$this->CI->event->register("RegisterSettings");
	}
	
	public function onBeforeNavigationHTML(){
		$html=$this->CI->GlobalSettings->getValue("global","header_html","");
		if (substr_count(mb_strtolower($html),"<?php")==0 && substr_count(mb_strtolower($html),"<?=")==0) {
			$file=dirname(__FILE__)."/../../../uploads/settings/header_html.php";
			if (file_exists($file)) @unlink($file);
			if (trim($html)!="") {
				echo $html;
			}		
		} else {
			$dir_failed=true;
			$dir=dirname(__FILE__)."/../../../uploads/settings/";
			if (!is_dir($dir)) @mkdir($dir);
			if (is_dir($dir)) {
				$file=dirname(__FILE__)."/../../../uploads/settings/header_html.php";
				if (!file_exists($file)) @touch($file);
				if (file_exists($file)) {
					$dir_failed=false;
					file_put_contents($file,$html);
					error_reporting(0);
					include($file);
					if (defined('ENVIRONMENT')) {
						switch (ENVIRONMENT) {
							case 'development':
								error_reporting(E_ALL);
							break;
	
							case 'testing':
							case 'production':
								error_reporting(0);
							break;

							default:
								exit('The application environment is not set correctly.');
						}
					}
				}
			}
			if ($dir_failed) {
				$this->CI->notifications->setError($this->CI->language->getModuleLanguageLine("settings","cannot_embed_header_html"));
			}
		}
	}
	
	public function onAfterModuleHTML(){
		$html=$this->CI->GlobalSettings->getValue("global","footer_html","");
		if (substr_count(mb_strtolower($html),"<?php")==0 && substr_count(mb_strtolower($html),"<?=")==0) {
			$file=dirname(__FILE__)."/../../../uploads/settings/footer_html.php";
			if (file_exists($file)) @unlink($file);
			if (trim($html)!="") {
				echo $html;
			}
		} else {
			$dir_failed=true;
			$dir=dirname(__FILE__)."/../../../uploads/settings/";
			if (!is_dir($dir)) @mkdir($dir);
			if (is_dir($dir)) {
				$file=dirname(__FILE__)."/../../../uploads/settings/footer_html.php";
				if (!file_exists($file)) @touch($file);
				if (file_exists($file)) {
					$dir_failed=false;
					file_put_contents($file,$html);
					error_reporting(0);
					include($file);
					if (defined('ENVIRONMENT')) {
						switch (ENVIRONMENT) {
							case 'development':
								error_reporting(E_ALL);
							break;
	
							case 'testing':
							case 'production':
								error_reporting(0);
							break;

							default:
								exit('The application environment is not set correctly.');
						}
					}
				}
			}
			if ($dir_failed) {
				$this->CI->notifications->setError($this->CI->language->getModuleLanguageLine("settings","cannot_embed_footer_html"));
			}
		}		
	}
	
	public function onSettingUpdateFormRow($setting){
		if ($setting->section_name=="global" && ($setting->name=="header_html" || $setting->name=="footer_html")) {
			echo '
			<script>
			var _form=$("#setting_value").closest("form");
			if (_form.length>0) {
				$("#setting_value").css("height",306);
				_form.removeClass("column-4").addClass("column-12");
				$(".modal-content").find(".inline-form-row").each(function(){
					var _label=$(this).find(".column-6:eq(0)");
					var _field=$(this).find(".column-6:eq(1)");
					_label.removeClass("column-6").addClass("column-2");
					_field.removeClass("column-6").addClass("column-10");
				});
			}
			</script>
			';
		}
	}
    
}
?>