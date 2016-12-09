<?php

//set and submit landing page


global $AI;
require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));

$landing_page = new C_landing_pages('prelauchtest');
$landing_page->next_step = 'prelaunch-checkout1';
$landing_page->next_step_send_ses_key = true;
$landing_page->css_error_class = 'lp_error';

$landing_page->pp_create_campaign = true;

//add validation rule

$landing_page->add_validator('username', 'is_length', 3,'Invalid User Name');
$landing_page->add_validator('password', 'is_length', 3,'Invalid Password');
$landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
$landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');
$landing_page->add_validator('phone', 'is_phone','','Invalid Phone Number');

if(util_is_POST()) {
    $landing_page->validate();
    if($landing_page->has_errors()) { $landing_page->display_errors(); }
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


<form name="landing_page" id="landing_page" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
    <table style="width: 100%; " cellspacing="1">
                                <tr>
                                    <td style="width: 45px; height: 8px;"></td>
                                    <td align="left" style="width: 257px; height: 8px;" class="style3"></td>
                                </tr>
        <tr>
            <td style="width: 45px">&nbsp;</td>
            <td align="left" style="width: 257px" class="style4"><label for="username" class="label notice">User Name</label></td>
        </tr>
        <tr>
            <td style="width: 45px">&nbsp;</td>
            <td align="left" style="width: 257px" class="style3"><strong>
                    <input name="username" maxlength="40" size="20" value="" type="text" style="width: 242px"></strong></td>
        </tr>
        <tr>
            <td style="width: 45px">&nbsp;</td>
            <td align="left" style="width: 257px" class="style4"><label for="password" class="label notice">Password</label></td>
        </tr>
        <tr>
            <td style="width: 45px">&nbsp;</td>
            <td align="left" style="width: 257px" class="style3"><strong>
                    <input name="password" type="password" style="width: 242px"></strong></td>
        </tr>
        <tr>
            <td style="width: 45px">&nbsp;</td>
            <td align="left" style="width: 257px" class="style4"><label for="first_name" class="label notice">First
                    Name</label></td>
        </tr>
        <tr>
            <td style="width: 45px">&nbsp;</td>
            <td align="left" style="width: 257px" class="style3"><strong>
                    <input name="first_name" maxlength="40" size="20" value="" type="text" style="width: 242px"></strong></td>
        </tr>
                                <tr>
                                    <td style="width: 45px">&nbsp;</td>
                                    <td align="left" style="width: 257px" class="style4"><label for="last_name" class="label notice">Last
                                            Name </label></td>
                                </tr>
                                <tr>
                                    <td style="width: 45px">&nbsp;</td>
                                    <td align="left" style="width: 257px" class="style3"><strong>
                                            <input name="last_name" maxlength="40" size="20" value="" type="text" style="width: 242px"></strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 45px">&nbsp;</td>
                                    <td align="left" style="width: 257px" class="style4"><label for="email" class="label notice">Your
                                            Email </label></td>
                                </tr>
                                <tr>
                                    <td style="width: 45px">&nbsp;</td>
                                    <td align="left" style="width: 257px" class="style3"><strong>
                                            <input name="email" maxlength="40" size="20" value="" type="text" style="width: 242px"></strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 45px">&nbsp;</td>
                                    <td align="left" style="width: 257px" class="style4"><label for="phone" class="label notice">Your
                                            Phone</label></td>
                                </tr>
        <tr>
            <td style="width: 45px"></td>
            <td align="left" style="width: 257px">
                <input name="phone" maxlength="40" size="20" value="" type="text" style="width: 242px"></td>
        </tr>
        <tr>
            <td style="width: 45px"></td>
            <td align="left" style="width: 257px">
                <input type="submit" value="submit"></td>
        </tr>


    </table>

</form>
