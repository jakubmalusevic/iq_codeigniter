<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$acl["default_role"]="guest";
$acl["map_source"]="db_table";
$acl["map_source_table"]="roles";
$acl["session_variable_name"]="user_role";
$acl["redirect_on_access_denied"]="users/publicaccess/login";
$acl["redirect_on_ajax_access_denied"]="errors/access_denied";
?>