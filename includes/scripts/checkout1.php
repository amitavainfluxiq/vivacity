<?php


//set and submit landing page 2nd step

global $AI;
require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));
require_once(ai_cascadepath('includes/modules/mlmsignup/class.enrollment_lp.php'));

require_once(ai_cascadepath('includes/modules/genealogy/class.genealogy.php'));

$gene            = new C_genealogy( $AI->get_setting('structure_show_genealogy') ? C_genealogy::GENEALOGY_TREE : C_genealogy::ENROLLER_TREE );
$is_logged_in    = $AI->user->isLoggedIn();
$is_admin        = $AI->get_access_group_perm('Administrators');
$is_in_genealogy = $is_logged_in ? $gene->is_descendant(AI_STRUCTURE_NODE_ROOT, util_affiliate_id()) : false;


$landing_page = new C_landing_pages('prelauchtest');
$landing_page->next_step = 'prelaunchtest';
$landing_page->pp_create_campaign = true;

$landing_page->css_error_class = 'lp_error';


//add validation rule

$landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
$landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
$landing_page->add_validator('bill_address_line_1', 'is_length', 5,'Invalid Billing Address');
$landing_page->add_validator('bill_city', 'is_length', 5,'Invalid City');
$landing_page->add_validator('bill_region', 'is_length', 2,'Invalid State');
$landing_page->add_validator('bill_country', 'is_length', 2,'Invalid Country');
$landing_page->add_validator('bill_postal_code', 'is_length', 5,'Invalid Postal Code');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');
$landing_page->add_validator('phone', 'is_phone','','Invalid Phone Number');
$landing_page->add_validator('besttime', 'is_length', 3,'Invalid \'Best time to call\'');

$landing_page->add_validator('card_name', 'is_length', 3,'Invalid Name on Card');
$landing_page->add_validator('card_number', 'is_length', 14,'Invalid Card Number');
$landing_page->add_validator('card_type', 'is_length', 2,'Invalid Card Type');
$landing_page->add_validator('card_exp_mo', 'card_expire_check', '','Invalid Card Expiration');
$landing_page->add_validator('card_cvv', 'is_length', 3,'Invalid Card Security Code (CVV)');

$landing_page->card_type_options = array('visa'=>'Visa','mast'=>'Mastercard','amx'=>'American Express','disc'=>'Discover');
$landing_page->no_ship_addr = true;

if(util_is_POST()) {
    $landing_page->validate();
    if($landing_page->has_errors()) { $landing_page->display_errors(); }
    else {
        //save user as distributor
        $landing_page->save_user('Distributor');
        if($landing_page->has_errors()) { $landing_page->display_errors(); }
        else {
            //save oreder
            $landing_page->save_order();
            if($landing_page->has_errors()) { $landing_page->display_errors(); }
            else
            {
                // Subscribe them to the drip campaign
                $landing_page->pp_drip_opt_in();

                //$this->goto_next_step();


                $util_rep_id = 100;

                if(util_rep_id()){
                    $util_rep_id = util_rep_id();
                }

                $util_rep_id = 99;

                // add user at geneology tree

                $gene = new C_genealogy(C_genealogy::GENEALOGY_TREE);
                try
                {
                    $gene->insert_node($landing_page->session['created_user'], $util_rep_id, null, 'stop', true);
                }
                catch ( NodeAlreadyInTreeException $naite )
                {
                    $data = $naite->get_data();
                    if ( $data['parent'] != $util_rep_id )
                    {
                        $gene->move_sub_tree($landing_page->session['created_user'], $util_rep_id, 0);
                    }
                }

                // add user at enrollment tree

                $gene = new C_genealogy(C_genealogy::ENROLLER_TREE);
                try
                {
                    $gene->insert_node($landing_page->session['created_user'], $util_rep_id, null, 'stop', true);
                }
                catch ( NodeAlreadyInTreeException $naite )
                {
                    $data = $naite->get_data();
                    if ( $data['parent'] != $util_rep_id )
                    {
                        $gene->move_sub_tree($landing_page->session['created_user'], $util_rep_id, 0);
                    }
                }


                $orderid = $landing_page->session['created_order'];

                if(intval($orderid) > 0){
                    $email_name = 'Enroll Success';
                    $send_to = 'samsujj@gmail.com';
                    $send_from = 'iftekarkta@gmail.com';

                    $vars = array();
                    $vars['name'] = 'Samsuj Jaman';

                    $defaults = array();
                    $defaults['email_subject'] = 'Enroll Success';
                    $defaults['email_msg'] = getmailbody($orderid);

                    $se = new C_system_emails($email_name);
                    $se->set_from($send_from);
                    $se->set_defaults_array($defaults);
                    $se->set_vars_array($vars);
                    $se->send($send_to);
                }

                util_redirect('success');
            }
        }
    }
}

$landing_page->refill_form();


function getmailbody($orderid =0){

    $o_res = db_query("SELECT `o`.`userID`,`o`.`date_added`,`o`.`billing_addr`,`o`.`shipping_addr`,`o`.`shipping`,`o`.`tax`,`o`.`total`,`od`.* FROM `orders` `o` INNER JOIN `order_details` `od` ON `o`.`order_id` = `od`.`order_id` WHERE `o`.`order_id` = ".$orderid);

    $userdet = '';
    $date = '';
    $total_amnt = 0.00;
    $shipping = 0.00;
    $tax = 0.00;
    $subtotal = 0.00;

    $product_html='<table width="100%" border="0" cellspacing="0" cellpadding="0" style=" font-family:Arial, Helvetica, sans-serif;">
          <tr>
            <th width="2%" align="left" valign="middle" style="background:#51b517;">&nbsp;</th>
            <th width="47%" align="left" valign="middle" style="background:#51b517; padding:8px 10px;  font-size:16px; color:#fff; font-weight:normal; ">Item Description</th>
            <th width="5%" align="left" valign="middle" style="background:#51b517; padding:8px 10px;  font-size:16px; color:#fff; font-weight:normal; "><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/arrowimgupdate.png"  alt="#"/></th>
            <th width="9%" align="center" valign="middle" style="background:#51b517; padding:8px 10px;  font-size:16px; color:#fff; font-weight:normal; "> Price</th>
            <th width="5%" align="left" valign="middle" style="background:#51b517; padding:8px 10px;  font-size:16px; color:#fff; font-weight:normal; "><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/arrowimgupdate.png"  alt="#"/></th>
            <th width="8%" align="center" valign="middle" style="background:#51b517; padding:8px 10px;  font-size:16px; color:#fff; font-weight:normal; ">Qty. </th>
            <th width="5%" align="left" valign="middle" style="background:#51b517; padding:8px 10px;  font-size:16px; color:#fff; font-weight:normal; "><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/arrowimgupdate.png"  alt="#"/></th>
            <th width="16%" align="center" valign="middle" style="background:#51b517; padding:8px 10px;  font-size:16px; color:#fff; font-weight:normal; ">Total </th>
            <th width="2%" align="left" valign="middle" style="background:#51b517;">&nbsp;</th>
          </tr>
          
        ';

    while($o_res && $order = db_fetch_assoc($o_res)) {

        $billing_addr = $order['billing_addr'];
        $billing_addr = unserialize($billing_addr);

        $date = date('jS, M Y',strtotime($order['date_added']));

        $total_amnt = $order['total'];
        $shipping = $order['shipping'];
        $tax = $order['tax'];


        $userdet = '<h2 style="color: #555; font-size:18px; font-weight:normal; margin:0; padding:0px; display:inline-block;  ">Invoice to:</h2>
                <br />
                <h3 style="font-weight:normal; margin:5px 0 8px 0; padding:0px; font-size:22px;  color:#ec2e64; display:inline-block;  ">'.$billing_addr['first_name'].' '.$billing_addr['last_name'].'</h3>
                <br />
                <h4 style="font-weight:normal; margin:0; padding:0; font-size:14px; line-height:20px; color: #555555; display:inline-block; ">'.$billing_addr['address_line_1'].', '.$billing_addr['city'].', '.$billing_addr['region'].' '.$billing_addr['postal_code'].', '.$billing_addr['country'].'<br />
                  '.$billing_addr['email'].'</h4>';


        $p_total = ($order['price'] * $order['qty']);
        $p_total = number_format($p_total, 2, '.', '');

        $subtotal += $p_total;

        $product_html .= '<tr>
            <td align="left" valign="middle" style="border-bottom:solid 2px #51b517;">&nbsp;</td>
            <td  align="left" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">'.$order['title'].'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #9e9b9b;">&nbsp;</td>
            <td  align="center" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">$'.$order['price'].'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #9e9b9b;">&nbsp;</td>
            <td  align="center" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">'.$order['qty'].'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #9e9b9b;">&nbsp;</td>
            <td  align="center" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">$'.$p_total.'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #51b517;">&nbsp;</td>
          </tr>';


    }


    $product_html .= '</table>';


    return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mail Page</title>
</head>
<body style="background:#e9e9e9;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><div style="width:640px; margin:0 auto; font-family:Arial, Helvetica, sans-serif; background:#fff;">
    
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background:#fff; padding:20px; border-bottom:solid 2px #ec2e64;">
          <tr>
            <td align="left" valign="middle"><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/logo-enrollment.png"  alt="#" width="250"/></td>
            <td align="right" valign="middle" style="font-size:14px; color:#000; line-height:30px;"><span>455 Lorem Ipsum, AZ 85004, US <img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/mail_location.png"  style="margin-left:5px;"/></span><br />
              <span><a href="mailto:loremIpsum@mail.com" style="color:#000; text-decoration:none;">loremIpsum@mail.com </a><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/mail_mailinfo.png" style="margin-left:5px;"/></span><br />
              <span><a href="tel:(000) 000-0000" style="color:#000; text-decoration:none;">(000) 000-0000 </a><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/mail_phone.png" style="margin-left:5px;"/></span> </td>
          </tr>
        </table>
        
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:40px 0;  font-family:Arial, Helvetica, sans-serif;">
          <tr>
            <td align="left" valign="top">
            <div style="border-left:solid 6px #ec2e64; padding:6px 6px 6px 10px;">
                '.$userdet.'
              </div>
              </td>
            <td align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="right" valign="middle" style="padding:0 8px 0 0; color: #ec2e64; font-size:28px;  text-transform:uppercase;">Invoice No: '.$orderid.'</td>
                </tr>
                <tr>
                  <td align="right" valign="middle" style="padding:2px 8px 0 0; color: #0f0f0f; font-size:14px; ">Invoice date:'.$date.'</td>
                </tr>
                <tr>
                  <td align="right" valign="middle" style="padding:15px 8px 0 0; color: #111; font-size:20px;">Total amount:$'.$total_amnt.' </td>
                </tr>
              </table></td>
          </tr>
        </table>
        
        '.$product_html.'
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:20px; margin:0; font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#333; ">
          <tr>
            <td width="56%" align="center" valign="middle" style="font-size:18px; color:#aaa; font-style:italic; line-height:30px;">&nbsp;</td>
            <td width="44%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="64%" align="right" valign="middle" style="padding:5px; font-family:Arial, Helvetica, sans-serif;">Subtotal</td>
                  <td width="36%" align="right" valign="middle" style="padding:5px; font-family:Arial, Helvetica, sans-serif;">$'.$subtotal.'</td>
                </tr>
                <tr>
                  <td align="right" valign="middle" style="padding:5px; font-family:Arial, Helvetica, sans-serif;">Shipping</td>
                  <td align="right" valign="middle" style="padding:5px; font-family:Arial, Helvetica, sans-serif;">$'.$shipping.'</td>
                </tr>
                <tr>
                  <td align="right" valign="middle" style="padding:5px; font-family:Arial, Helvetica, sans-serif; ">Tax</td>
                  <td align="right" valign="middle" style="padding:5px; font-family:Arial, Helvetica, sans-serif;">$'.$tax.'</td>
                </tr>
                <tr>
                  <td align="right" valign="middle" style="padding:5px;  color:#fff; background:#51b517;">Grand Total</td>
                  <td align="right" valign="middle" style="padding:5px;  color:#fff; background:#51b517;">$'.$total_amnt.'</td>
                </tr>
              </table></td>
          </tr>
        </table>
        <div style="width:auto; padding:30px; text-align:center; background:#141414; color:#e9e9e9;text-align:center; margin-top:40px;">Thank you
          For Your Purchase Order</div>
      </div></td>
  </tr>
</table>
</body>
</html>';
}



?>

<form name="landing_page" id="landing_page" action="<?=$_SERVER['REQUEST_URI']?>" method="post">

<!--<form method="post" name="form" onSubmit="return validate(this)">-->
    <input name="pid" type="hidden" value="1">
    <input type="hidden" name="form_time" value="<?= date('Y-m-d H:i:s') ?>" />
    <p>
        <label for="first_name">user Name:</label>
        <input name="username" type="text" id="username" />
    </p>
    <p>
        <label for="last_name">password</label>
        <input name="password" type="password" id="password" />

    </p>
    <p>
        <label for="first_name">First Name:</label>
        <input name="first_name" type="text" id="first_name" />
    </p>
    <p>
        <label for="last_name">Last Name</label>
        <input name="last_name" type="text" id="last_name" />

    </p>
    <p>
        <label for="bill_address_line_1">Address</label>
        <input name="bill_address_line_1" type="text" id="bill_address_line_1" value="" />
    </p>
    <p>
        <label for="bill_city">City</label>
        <input name="bill_city" type="text" id="bill_city" value="" />
    </p>

    <p>

        <label for="bill_country">Country</label>
        <?php $landing_page->draw_country_select('bill_'); ?>
    </p>


    <p>
        <label for="bill_region">State</label>
        <?php $landing_page->draw_region_select('bill_'); ?>
    </p>
    <!--
        <p>
    <label for="">Province/Other</label>

    <input type="hidden" name="question[40]" value="Other state"  />
<input type="text" name="answer[40]" value="" onChange="this.form.state.selectedIndex=1"  />
        </p>
    -->
    <p>
        <label for="bill_postal_code">Zip / Postal Code</label>
        <input name="bill_postal_code" type="text" id="bill_postal_code" />
    </p>
    <p>
        <label for="phone">Phone</label>
        <input name="phone" type="text" id="phone" />
    </p>

    <p>
        <label for="email">Email</label>
        <input name="email" type="text" id="email"  />
    </p>
    <p>
        <label for="besttime">Best Time to Call</label>
        <select name="besttime" id="besttime">
            <?php $landing_page->draw_besttime_options(); ?>
        </select>
    </p>
    <h3 id="two">Enter your billing information - SECURE</h3>

    <p>
        <label for="card_name">Name on Credit Card</label>
        <input name="card_name" type="text" id="card_name" value="" />
    </p>
    <p>
        <label for="card_number">Credit Card Number</label>
        <input name="card_number" type="text" id="card_number" value="" />
        <!--<strong class="secure">Secure</strong>--></p>
    <p>
        <label for="card_type">Credit Card Type</label>
        <select name="card_type" class="col1 cc_type" id="card_type">
            <?php $landing_page->draw_card_type_options(); ?>
        </select>
    </p>
    <p>
        <label for="card_exp_mo">Expiration Date</label>
        <select name="card_exp_mo" id="card_exp_mo">
            <?php $landing_page->draw_card_month_options_short(); ?>
        </select>
        /
        <select name="card_exp_yr" id="card_exp_yr">
            <?php $landing_page->draw_card_year_options_short(); ?>
        </select>

    </p>
    <p>
        <label for="card_cvv">Card CVV#</label>

        <input name="card_cvv" type="text" id="card_cvv" value="" style='width:50px;' />
    </p>

    <div style="font-size:12px;color:black;text-align:center;">
        <input type="checkbox" name="iagreetoit" id="iagreetoit" value="Y" />
        I agree to your Terms &amp; Conditions.
        <br />
        &nbsp;
    </div>
<!--    <center><input type="submit" name="Submit" onclick="javascript:noPopup();" /></center>-->

    <input type="submit" value="Submit">

    </fieldset>
</form>