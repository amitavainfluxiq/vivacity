<?php

//set and submit landing page


global $AI;
require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));

$landing_page = new C_landing_pages('prelauch');
$landing_page->next_step = 'prelaunch-checkout';
$landing_page->next_step_send_ses_key = true;
$landing_page->css_error_class = 'lp_error';

$landing_page->pp_create_campaign = true;

//add validation rule

$landing_page->add_validator('username', 'is_length', 3,'Invalid User Name');
//$landing_page->add_validator('password', 'is_length', 3,'Invalid Password');
$landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
$landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
//$landing_page->add_validator('company', 'is_length', 3,'Invalid Company Name');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');
//$landing_page->add_validator('phone', 'is_phone','','Invalid Phone Number');
$landing_page->add_validator('check_terms','is_checked','','You must accept the Terms &amp; Conditions');

if(util_is_POST()) {
    $landing_page->validate();

    $err = $AI->user->validate_password($_POST['password']);


    if (!empty($_POST['username'])){
        $err_arr = array();
        if(strlen($_POST['username'])<3) {
            $err_arr[] ='Username must be at least 3 characters.';
        }
        if(preg_match('/[^0-9A-Za-z-]/',$_POST['username'])) {
            $err_arr[] ='Username must only contain letters, numbers, and dashes.';
        }
        if(substr($_POST['username'],0,1)=='-' || substr($_POST['username'],-1)=='-') {
            $err_arr[] ='Username must not start or end with dash.';
        }

        if(count($err_arr) == 0){
            $lookup_userID = db_lookup_scalar("SELECT userID FROM users WHERE username = '" . db_in( $_POST['username'] ) . "';");
            if( is_numeric($lookup_userID) && $lookup_userID != $this->te_key )
            {
                $err_arr[] = 'Sorry, that username has already been taken, please choose another.';
            }
        }
    }

    if($landing_page->has_errors()) { $landing_page->display_errors(); }
    elseif (count($err_arr) > 0){
        $js[]="jonbox_alert('".implode('<br>',$err_arr)."');";
        if(count($js)>0) $AI->skin->js_onload("//DRAW LP ERRORS:\n\n".implode("\n\n",$js));
    }
    elseif($err !== true){
        $js[]="jonbox_alert('".$err."');";
        if(count($js)>0) $AI->skin->js_onload("//DRAW LP ERRORS:\n\n".implode("\n\n",$js));
    }
    elseif( isset($_POST['retype_password']) && $_POST['password'] != trim(@$_POST['retype_password']) )
    {
        $err = 'Your passwords do not match. Please re-type them.';
        $js[]="jonbox_alert('".$err."');";
        if(count($js)>0) $AI->skin->js_onload("//DRAW LP ERRORS:\n\n".implode("\n\n",$js));
    }
    else {
        if($landing_page->save_lead($AI->get_setting('owner_id')))
        {
            // Subscribe them to the drip campaign
            $landing_page->pp_drip_opt_in();

            $landing_page->goto_next_step();
        }
    }
}
$landing_page->refill_form();


?>
