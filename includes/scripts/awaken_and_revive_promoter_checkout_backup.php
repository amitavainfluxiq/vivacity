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


$landing_page = new C_landing_pages('awaken-and-revive-promoter');
//$landing_page->next_step = 'prelaunchtest';
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
    $landing_page->validate();
    if($landing_page->has_errors()) { $landing_page->display_errors(); }
    else {
        //save user as distributor
        $landing_page->save_user();
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
                    $o_res = db_query("SELECT `o`.`userID`,`o`.`date_added`,`o`.`billing_addr`,`o`.`shipping_addr`,`o`.`shipping`,`o`.`tax`,`o`.`total`,`od`.* FROM `orders` `o` INNER JOIN `order_details` `od` ON `o`.`order_id` = `od`.`order_id` WHERE `o`.`order_id` = ".$orderid);

                    $order = db_fetch_assoc($o_res);

                    $billing_addr = $order['billing_addr'];
                    $billing_addr = unserialize($billing_addr);

                    $date = date('jS, M Y',strtotime($order['date_added']));

                    $total_amnt = $order['total'];
                    $shipping = $order['shipping'];
                    $tax = $order['tax'];

                    $p_total = ($order['price'] * $order['qty']);
                    $p_total = number_format($p_total, 2, '.', '');

                    $email_name = 'Order Success';
                    $send_to = $billing_addr['email'];
                    $send_from = 'iftekarkta@gmail.com';

                    $vars = array();
                    $vars['user'] = $billing_addr['first_name'].' '.$billing_addr['last_name'];
                    $vars['orderid'] = $orderid;
                    $vars['total'] = $total_amnt;
                    $vars['useremail'] = $billing_addr['email'];
                    $vars['useraddr'] = $billing_addr['address_line_1'].', '.$billing_addr['city'].', '.$billing_addr['region'].' '.$billing_addr['postal_code'].', '.$billing_addr['country'];
                    $vars['orderdate'] = date('jS, M Y');;
                    $vars['pname'] = $order['title'];
                    $vars['pprice'] = $order['price'];
                    $vars['pqty'] = $order['qty'];
                    $vars['ptotal'] = $p_total;
                    $vars['subtotal'] = $p_total;
                    $vars['shipping'] = $shipping;
                    $vars['$tax'] = $tax;

                    $defaults = array();

                    $se = new C_system_emails($email_name);
                    $se->set_from($send_from);
                    $se->set_defaults_array($defaults);
                    $se->set_vars_array($vars);
                    $se->send($send_to);
                }

                util_redirect('order-success');
            }
        }
    }
}

$landing_page->refill_form();


$tax_str = '';
$tax_arr = array();

$res = db_query("SELECT * FROM `taxes`");

while($res && $row = db_fetch_assoc($res)) {
    $tax_arr[$row['region']] = $row['amount'];
}

$tax_str = json_encode($tax_arr);

$ship_str = '';
$ship_arr = array();

$price_arr = array('49.95');

/*$price_arr = array();

$p_res = db_query("SELECT `ps`.`price` FROM `product_stock_items` `ps`");

while($p_res && $product = db_fetch_assoc($p_res)) {
    $price_arr[] = $product['price'];
}

$price_arr = array_unique($price_arr);
*/

foreach ($price_arr as $val){

    $res = db_query("SELECT * FROM `shipping_by_price` WHERE `min` <".$val." && `max` >=".$val);

    $ship_arr[$val] = 6;

    while($res && $row = db_fetch_assoc($res)) {
        $ship_arr[$val] = $row['cost'];
    }

    $ship_str = json_encode($ship_arr);
}




?>

<input type="hidden" id="taxarr" value='<?php echo $tax_str;?>'>
<input type="hidden" id="shiparr" value='<?php echo $ship_str;?>'>
<input type="hidden" id="pname" value='Awaken'>
<input type="hidden" id="pprice" value='49.95'>

<div class="container-fluid toplogoblock text-center">
    <img class="img-responsive" src="system/themes/awaken_and_revive/images/landing1_logo.png">
</div>


<div class="container vcproduct_wraper ">



<div class=" col-lg-8 col-md-8 col-sm-12 col-xs-12 vcproduct_block">




    <div class="vcproduct_conbox">
        <h2><?php /*echo $product['title'];*/?>Balance</h2>
        <div class="clearfix"></div>

        <div class="vcpro_textcon">
            <div class="vcpro_textcon_img">
                <img src="system/themes/awaken_and_revive/images/ad_product1.png" alt="#" class="ad_product1">
            </div>

            <div class="vcpro_textcon_text">


                <span>Balance</span> is the ultimate supplement formulated for an amplified body-equilibrium. <span>Balance</span> works as an essential element to experiencing <span>The Shift</span> by assisting the necessary preparation of body and mind for experiencing lasting success in our <span>VioPhaze</span> and <span>VioFit</span> programs. That is why we established it as the foundational first-half of our 4-step total wellness philosophy!<br/><br/>

                Now you can benefit more from your body’s natural 24-hour cycle.<br/><br/>

                <span>Balance</span> tips the scale in your favor, from dawn-to-dusk and back again to dawn. The internal systems of your body never cease, but that doesn’t mean we should expect them to work overtime. Optimal health requires regular maintenance. <span>Balance</span> actively maintains your internal systems in such a way as to encourage maximum vitality, all day and throughout the night. Our expert team of health-professionals have guided the development of our formulas and verified their inherent benefits.
                <br/><br/>

                Start every day with <span>Awaken S-7</span> to magnify your morning routine. <span>Awaken S-7</span> increases alertness without experiencing the inevitable crash that accompanies conventional morning wake-up stimulants (coffee, sugar-sweetened beverages, energy drinks). When you wake up to <span>Awaken S-7</span>, you may feel energetic and inspired enough to knock out your daily <span>VioFit</span> activity the moment you jump out of bed. That is something other wellness systems haven’t figured out at all.
                <br/><br/>
                Activate your mornings immediately and stay alert throughout the entire day with <span>Awaken S-7</span>.

                <br/><br/>

                <span>Recover</span> promotes a gentle detoxification and increases serotonin while your body repairs and prepares for another day. Formulated with zeolite and melatonin, <span>Recover</span> absorbs free radicals and prolongs the REM sleep cycle. Better sleep makes for a healthier mind and body. Remember the 24-Hour Core Dynamics System we mentioned earlier? <span>Recover</span> maintains this essential process throughout the night. Make the most of your body’s naturally designated revitalization period and work towards a healthier body while you sleep!
                <br/><br/>

                <span>Balance</span> combines  <span>Awaken S-7</span> and  <span>Recover</span> for the ultimate wake-up call. These supplements have been bundled together into one amazing program, with your vitality in mind, to prepare your body for the life-changing feats you are about to accomplish through the application of our total wellness philosophy.
                <br/><br/>


                Feel refreshed and keep that good feeling flowing throughout the day!
                <br/><br/>
                Overall balance in health is the key to your daily transformation. Once balance is established, your body’s equilibrium becomes easier to regulate and stabilize. You may discover that you have more energy and require less sleep! Think of what you could do with all that extra time and energy.




                <h4>$49.95<?php /*echo $product['price'];*/?></h4>

                <!--<input id="selbtn11" class="select_btn" value="select this package">-->
            </div>
            <div class="clearfix"></div>
        </div>
    </div>







    <div class="vcproduct_orderbox" id="revive-promoter-checkout">
        <table width="100%" border="0">
            <tbody>
            <tr>
                <th colspan="2">Order Summary</th>
            </tr>
            <tr>
                <td valign="middle" align="left">Package:</td>
                <td valign="middle" align="left"><span id="package_name">Balance - 5 boxes</span></td>
            </tr>
            <tr>
                <td valign="middle" align="left">Sub-total: </td>
                <td valign="middle" align="left"><span id="package_subtotal">$000.00</span></td>
            </tr>
            <tr>
                <td valign="middle" align="left">Shipping:</td>
                <td valign="middle" align="left"><span id="package_shipping"> FREE</span></td>
            </tr>
            <tr>
                <td valign="middle" align="left">Sales Tax: </td>
                <td valign="middle" align="left"><span id="package_tax">-$0</span></td>
            </tr>
            <tr>
                <td valign="middle" align="left">Total: </td>
                <td valign="middle" align="left"><span id="package_total"> $000.00</span> </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>

    <div class=" col-lg-4 col-md-4 col-sm-12 col-xs-12 vcform_block">

<div class="productform">
    <div class="formheading">
    <h1> FINAL STEP</h1>
    <h6>Billing Information</h6>
</div>
<h2>Payment information</h2>
<h4>
    <span>Secure 128-bit SSL Connection</span>
</h4>



<form name="landing_page" id="landing_page" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
    <div class="formmainbg">
        <!--<form method="post" name="form" onSubmit="return validate(this)">-->
        <input name="pid" type="hidden" value="7">
        <input type="hidden" name="form_time" value="<?= date('Y-m-d H:i:s') ?>" />
        <div class="form-group">
            <label for="first_name">user Name:</label>
            <input name="username" type="text" id="username" />
        </div>
        <div class="form-group">
            <label for="last_name">password</label>
            <input name="password" type="password" id="password" />

        </div>
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input name="first_name" type="text" id="first_name" />
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input name="last_name" type="text" id="last_name" />

        </div>
        <div class="form-group">
            <label for="bill_address_line_1">Address</label>
            <input name="bill_address_line_1" type="text" id="bill_address_line_1" value="" />
        </div>
        <div class="form-group">
            <label for="bill_city">City</label>
            <input name="bill_city" type="text" id="bill_city" value="" />
        </div>

        <div class="form-group">

            <label for="bill_country">Country</label>
            <?php $landing_page->draw_country_select('bill_'); ?>
        </div>


        <div class="form-group">
            <label for="bill_region">State</label>
            <?php $landing_page->draw_region_select('bill_'); ?>
        </div>
        <!--
            <p>
        <label for="">Province/Other</label>

        <input type="hidden" name="question[40]" value="Other state"  />
    <input type="text" name="answer[40]" value="" onChange="this.form.state.selectedIndex=1"  />
            </p>
        -->
        <div class="form-group">
            <label for="bill_postal_code">Zip / Postal Code</label>
            <input name="bill_postal_code" type="text" id="bill_postal_code" />
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input name="phone" type="text" id="phone" />
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input name="email" type="text" id="email"  />
        </div>

        <h3 id="two" style="margin-top: 15px;">Enter your billing information - SECURE</h3>

        <div class="form-group">
            <label for="card_name">Name on Credit Card</label>
            <input name="card_name" type="text" id="card_name" value="" />
        </div>
        <div class="form-group">
            <label for="card_number">Credit Card Number</label>
            <input name="card_number" type="text" id="card_number" value="" />
            <!--<strong class="secure">Secure</strong>--></div>
        <div class="form-group">
            <label for="card_type">Credit Card Type</label>
            <select name="card_type" class="col1 cc_type" id="card_type">
                <?php $landing_page->draw_card_type_options(); ?>
            </select>
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
        </div>

        <div class="formchakebox">
            <input id="iagreetoit" class="css-checkbox" name="iagreetoit" value="Y" type="checkbox">
            <label class="css-label" for="iagreetoit"></label>
            <span data-toggle="modal" data-target="#myModalterms">Accept All Terms</span>
            <div class="clearfix"></div>
        </div>
        <!--    <center><input type="submit" name="Submit" onclick="javascript:noPopup();" /></center>-->

        <input type="submit" value="Submit"  class="formsub_btn">

        </fieldset>

    </div>
</form>

    </div>


    </div>


    <div class="clearfix"></div>

</div>