<?php
$_GET['page']='env-selector-settings';$_POST['env_selector_save']='dashboard';
$before=get_site_option('wp_env_selector');
$die=function(){return function($message){throw new RuntimeException(is_string($message)?$message:'Denied');};};add_filter('wp_die_handler',$die);add_filter('wp_die_ajax_handler',$die);
wp_set_current_user(0);
try{env_selector_handle_save();throw new LogicException('Anonymous save was accepted');}catch(RuntimeException $e){echo "PASS: anonymous environment save rejected\n";}
wp_set_current_user(1);$_POST['env_selector_nonce']='invalid';$_REQUEST['env_selector_nonce']='invalid';
try{env_selector_handle_save();throw new LogicException('Invalid nonce was accepted');}catch(RuntimeException $e){echo "PASS: invalid environment nonce rejected\n";}
if(get_site_option('wp_env_selector')!==$before){throw new RuntimeException('Rejected save changed environment');}
echo "PASS: rejected saves leave environment unchanged\n";
