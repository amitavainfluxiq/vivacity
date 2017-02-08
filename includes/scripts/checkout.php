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


$landing_page = new C_landing_pages('prelauch');
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
//$landing_page->add_validator('besttime', 'is_length', 3,'Invalid \'Best time to call\'');

$landing_page->add_validator('card_name', 'is_length', 3,'Invalid Name on Card');
$landing_page->add_validator('card_number', 'is_length', 14,'Invalid Card Number');
$landing_page->add_validator('card_type', 'is_length', 2,'Invalid Card Type');
$landing_page->add_validator('card_exp_mo', 'card_expire_check', '','Invalid Card Expiration');
$landing_page->add_validator('card_cvv', 'is_length', 3,'Invalid Card Security Code (CVV)');

$landing_page->card_type_options = array('visa'=>'Visa','mast'=>'Mastercard','amx'=>'American Express','disc'=>'Discover');
$landing_page->no_ship_addr = true;

if(util_is_POST()) {
    $pcost = $_POST['pcost'];

    $lookup_userID = db_lookup_scalar("SELECT cost FROM `shipping_by_price` WHERE `min` < ".intval($pcost)." && `max` >= ".intval($pcost));

    $shiping_cost = intval($lookup_userID);

    if($shiping_cost > 0){
        $shiping_cost = number_format($shiping_cost,2);
    }else{
        $shiping_cost = number_format(6,2);
    }


    $landing_page->set_shipping_amt($shiping_cost);

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
                    $email_name = 'Order Success';
                    $send_to = $_POST['email'];
                    $send_from = 'iftekarkta@gmail.com';

                    $vars = array();
                    //$vars['name'] = 'Samsuj Jaman';
                    $vars = getmailbody($orderid);

                    $defaults = array();
                    $defaults['email_subject'] = 'Order Success';
                    //$defaults['email_msg'] = ' This is [[email_msg]]';
                    //print_r($defaults);
                    //exit;

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
            <th width="2%" align="left" valign="middle" style="background:#069d1f;">&nbsp;</th>
            <th width="47%" align="left" valign="middle" style="background:#069d1f; padding:8px 10px;  font-size:16px; font-weight: bold !important; color:#fff; font-weight:normal; ">Item Description</th>
            <th width="5%" align="left" valign="middle" style="background:#069d1f; padding:8px 10px;  font-size:16px; font-weight: bold !important; color:#fff; font-weight:normal; "><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/arrowimgupdate.png"  alt="#"/></th>
            <th width="9%" align="center" valign="middle" style="background:#069d1f; padding:8px 10px;  font-size:16px; font-weight: bold !important; color:#fff; font-weight:normal; "> Price</th>
            <th width="5%" align="left" valign="middle" style="background:#069d1f; padding:8px 10px;  font-size:16px; font-weight: bold !important; color:#fff; font-weight:normal; "><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/arrowimgupdate.png"  alt="#"/></th>
            <th width="8%" align="center" valign="middle" style="background:#069d1f; padding:8px 10px;  font-size:16px; font-weight: bold !important; color:#fff; font-weight:normal; ">Qty. </th>
            <th width="5%" align="left" valign="middle" style="background:#069d1f; padding:8px 10px;  font-size:16px; font-weight: bold !important; color:#fff; font-weight:normal; "><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/arrowimgupdate.png"  alt="#"/></th>
            <th width="16%" align="center" valign="middle" style="background:#069d1f; padding:8px 10px;  font-size:16px; font-weight: bold !important; color:#fff; font-weight:normal; ">Total </th>
            <th width="2%" align="left" valign="middle" style="background:#069d1f;">&nbsp;</th>
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
            <td align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
            <td  align="left" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">'.$order['title'].'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #9e9b9b;">&nbsp;</td>
            <td  align="center" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">$'.$order['price'].'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #9e9b9b;">&nbsp;</td>
            <td  align="center" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">'.$order['qty'].'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #9e9b9b;">&nbsp;</td>
            <td  align="center" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">$'.$p_total.'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
          </tr>';
        $var['pname']=$order['title'];
        $var['pprice']=$order['price'];
        $var['pqty']=$order['qty'];


    }


    $product_html .= '</table>';

    $var['orderid']=$orderid;
    $var['user']=$billing_addr['first_name'].' '.$billing_addr['last_name'];
    $var['useraddr']=$billing_addr['address_line_1'].', '.$billing_addr['city'].', '.$billing_addr['region'].' '.$billing_addr['postal_code'].', '.$billing_addr['country'];
    $var['useremail']=$billing_addr['email'];
    $var['total']=$total_amnt;
    $var['orderdate']=$date;

    $var['ptotal']=$p_total;
    $var['subtotal']=$subtotal;
    $var['shipping']=$shipping;
    $var['$tax']=$tax;
    return $var;


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
                <h2 style="color: #555; font-size:18px; font-weight:normal; margin:0; padding:0px; display:inline-block;  ">Invoice to:</h2>
                <br />
                <h3 style="font-weight:normal; margin:5px 0 8px 0; padding:0px; font-size:22px;  color:#ec2e64; display:inline-block;  ">'.$billing_addr['first_name'].' '.$billing_addr['last_name'].'</h3>
                <br />
                <h4 style="font-weight:normal; margin:0; padding:0; font-size:14px; line-height:20px; color: #555555; display:inline-block; ">'.$billing_addr['address_line_1'].', '.$billing_addr['city'].', '.$billing_addr['region'].' '.$billing_addr['postal_code'].', '.$billing_addr['country'].'<br />
                  '.$billing_addr['email'].'</h4>
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
        
        <tr>
            <td align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
            <td  align="left" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">'.$order['title'].'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #9e9b9b;">&nbsp;</td>
            <td  align="center" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">$'.$order['price'].'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #9e9b9b;">&nbsp;</td>
            <td  align="center" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">'.$order['qty'].'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #9e9b9b;">&nbsp;</td>
            <td  align="center" valign="middle" style="padding:8px 10px;  font-size:16px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b">$'.$p_total.'</td>
            <td align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
          </tr>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:20px 0px; margin:0; font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#333; ">
          <tr>
            <!---<td width="56%" align="center" valign="middle" style="font-size:18px; color:#aaa; font-style:italic; line-height:30px;">&nbsp;</td>-->
            <td width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
                  <td width="67%" align="right" valign="middle" style="padding:5px; border-bottom:solid 2px #9e9b9b; font-family:Arial, Helvetica, sans-serif;font-size: 18px !important;">Subtotal</td>
                  <td width="23%" align="right" valign="middle" style="padding:5px; border-bottom:solid 2px #9e9b9b; font-family:Arial, Helvetica, sans-serif;font-size: 18px !important;">$'.$subtotal.'</td>
                  <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
                </tr>
                <tr>
                  <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
                  <td width="67%" align="right" valign="middle" style="padding:5px;  border-bottom:solid 2px #9e9b9b; font-family:Arial, Helvetica, sans-serif;font-size: 18px !important;">Shipping</td>
                  <td width="23%" align="right" valign="middle" style="padding:5px; border-bottom:solid 2px #9e9b9b; font-family:Arial, Helvetica, sans-serif;font-size: 18px !important;">$'.$shipping.'</td>
                  <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
                </tr>
                <tr>
                  <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
                  <td width="67%" align="right" valign="middle" style="padding:5px; border-bottom:solid 2px #9e9b9b; font-family:Arial, Helvetica, sans-serif;font-size: 18px !important; ">Tax</td>
                  <td width="23%" align="right" valign="middle" style="padding:5px; border-bottom:solid 2px #9e9b9b; font-family:Arial, Helvetica, sans-serif;font-size: 18px !important;">$'.$tax.'</td>
                  <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f;">&nbsp;</td>
                </tr>
                <tr>
                  <td width="5%" align="left" valign="middle" style="background:#069d1f;">&nbsp;</td>
                  <td width="67%" align="right" valign="middle" style="padding:5px; color:#fff; background:#069d1f; font-size: 22px !important;">Grand Total</td>
                  <td width="23%" align="right" valign="middle" style="padding:5px; color:#fff; background:#069d1f;">$'.$total_amnt.'</td>
                  <td width="5%" align="left" valign="middle" style="background:#069d1f;">&nbsp;</td>
                </tr>
              </table>
              </td>
          </tr>
        </table>
        <div class="pull-right text-right">
            <a style="display:block; margin: 0px auto 10px auto; width:120px; height:31px; background:#069d1f; font-size:16px; color:#fff; text-align:center; text-transform:uppercase; font-weight:bold; line-height:33px; text-decoration:none;" href="http://www.vivacitygo.com/login?ai_bypass=true" target="_blank">login</a>
        </div>        
        <div style="width:auto; padding:30px; text-align:center; background:#141414; color:#e9e9e9;text-align:center; margin-top:20px;">Thank you
          For Your Purchase Order</div>
      </div></td>
  </tr>
</table>
</body>
</html>';
}


$tax_str = '';
$tax_arr = array();

$res = db_query("SELECT * FROM `taxes`");

while($res && $row = db_fetch_assoc($res)) {
    $tax_arr[$row['region']] = $row['amount'];
}

$tax_str = json_encode($tax_arr);

$ship_str = '';
$ship_arr = array();

$price_arr = array(99,199);

$price_arr = array();

$p_res = db_query("SELECT `ps`.`price` FROM `product_stock_items` `ps`");

while($p_res && $product = db_fetch_assoc($p_res)) {
    $price_arr[] = $product['price'];
}

$price_arr = array_unique($price_arr);


foreach ($price_arr as $val){

    $res = db_query("SELECT * FROM `shipping_by_price` WHERE `min` <".$val." && `max` >=".$val);

    $ship_arr[$val] = 6;

    while($res && $row = db_fetch_assoc($res)) {
        $ship_arr[$val] = $row['cost'];
    }

    $ship_str = json_encode($ship_arr);
}



?>
<div class="productform" id="productformcon">

    <input type="hidden" id="taxarr" value='<?php echo $tax_str;?>'>
    <input type="hidden" id="shiparr" value='<?php echo $ship_str;?>'>

    <div class="formheading">

        <h1>  FINAL STEP</h1>
        <h6>Billing Information</h6>
    </div>

    <h2>Payment information</h2>

    <h4><span>Secure 128-bit SSL Connection</span></h4>
    <div class="formmainbg">
<form name="landing_page" id="landing_page_chk" action="<?=$_SERVER['REQUEST_URI']?>" method="post">

<!--<form method="post" name="form" onSubmit="return validate(this)">-->





<h3>USER INFORMATION</h3>


    <input type="hidden" id="testpid" value="0">

    <input name="pid" type="hidden" value="0" id="pid">
    <input name="shippingcost" type="hidden" value="0" id="shippingcost">
    <input name="taxcost" type="hidden" value="0" id="taxcost">
    <input name="pcost" type="hidden" value="0" id="pcost">
    <input type="hidden" name="form_time" value="<?= date('Y-m-d H:i:s') ?>" />
    <input name="username" type="hidden" />
    <input name="password" type="hidden" />
    <input name="company" type="hidden" />

    <!--<div class="form-group">
        <label for="first_name">user Name:</label>
        <input name="username" type="text" id="username" />
        <div class="clearfix"></div>
    </div>
    <div class="form-group">
        <label for="last_name">password</label>
        <input name="password" type="password" id="password" />
    <div class="clearfix"></div>
    </div>-->
    <div class="form-group">
        <label for="first_name">First Name:</label>
        <input name="first_name" type="text" id="first_name" />
    <div class="clearfix"></div>
    </div>
    <div class="form-group">
        <label for="last_name">Last Name</label>
        <input name="last_name" type="text" id="last_name" />
    <div class="clearfix"></div>
  </div>
    <div class="form-group">
        <label for="bill_address_line_1">Address</label>
        <input name="bill_address_line_1" type="text" id="bill_address_line_1" value="" />
    <div class="clearfix"></div>
    </div>
    <div class="form-group">
        <label for="bill_city">City</label>
        <input name="bill_city" type="text" id="bill_city" value="" />
    <div class="clearfix"></div>
    </div>

    <div class="form-group">

        <label for="bill_country">Country</label>
        <?php $landing_page->draw_country_select('bill_',array('United States')); ?>
    <div class="clearfix"></div>
   </div>


    <div class="form-group">
        <label for="bill_region">State</label>
        <?php $landing_page->draw_region_select('bill_'); ?>
    <div class="clearfix"></div>
   </div>
    <!--
        <div class="form-group">
    <label for="">Province/Other</label>

    <input type="hidden" name="question[40]" value="Other state"  />
<input type="text" name="answer[40]" value="" onChange="this.form.state.selectedIndex=1"  />
 <div class="clearfix"></div>
        </div>
    -->
    <div class="form-group">
        <label for="bill_postal_code">Zip / Postal Code</label>
        <input name="bill_postal_code" type="text" id="bill_postal_code" />
    <div class="clearfix"></div>
    </div>
    <div class="form-group">
        <label for="phone">Phone</label>
        <input name="phone" type="text" id="phone" />
    <div class="clearfix"></div>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input name="email" type="text" id="email"  />
    <div class="clearfix"></div>
   </div>
   <!-- <div class="form-group">
        <label for="besttime">Best Time to Call</label>
        <select name="besttime" id="besttime">
            <?php //$landing_page->draw_besttime_options(); ?>
        </select>
    <div class="clearfix"></div>
   </div>-->
    <h3 id="two" style="margin-top: 15px;">Pay with Credit or Debit Card</h3>

    <div class="form-group">
        <label for="card_name">Name on Credit Card</label>
        <input name="card_name" type="text" id="card_name" value="" />
    <div class="clearfix"></div>
   </div>


    <div class="form-group">
        <label for="card_type">Credit Card Type</label>
        <select name="card_type" class="col1 cc_type" id="card_type">
            <?php $landing_page->draw_card_type_options(); ?>
        </select>
    <div class="clearfix"></div>
   </div>
    <div class="form-group">
        <label for="card_number">Credit Card Number</label>
        <input name="card_number" type="text" id="card_number" value="" />
        <!--<strong class="secure">Secure</strong>-->

        <div class="clearfix"></div>
    </div>
    <div class="form-group2">
        <label for="card_exp_mo">Expiration Date</label>
        <select name="card_exp_mo" id="card_exp_mo">
            <?php $landing_page->draw_card_month_options_short(); ?>
        </select>
        /
        <select name="card_exp_yr" id="card_exp_yr">
            <?php $landing_page->draw_card_year_options_short(); ?>
        </select>
    <div class="clearfix"></div>
   </div>
    <div class="form-group">
        <label for="card_cvv">Card CVV#</label>

        <input name="card_cvv" type="text" id="card_cvv" value="" style='width:80px;' />
    <div class="clearfix"></div>
   </div>

    <div class="formchakebox">
        <input type="checkbox" name="iagreetoit" id="iagreetoit" value="Y"  class="css-checkbox"/>


       <label for="iagreetoit" class="css-label"></label>

        <span data-toggle="modal" data-target="#myModalterms">Accept All Terms</span>




        <div class="clearfix"></div>
    </div>
<!--    <center><input type="submit" name="Submit" onclick="javascript:noPopup();" /></center>-->

    <input type="button" value="RUSH MY ORDER" class="formsub_btn" onclick="formchkvalidate()">

    <!--</fieldset>-->
</form>


    </div>
</div>