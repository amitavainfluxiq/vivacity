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


$landing_page = new C_landing_pages('awaken-and-revive-affiliate');
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
$product1=array();
$product2=array();
$product3=array();
$product4=array();
$p_res = db_query("SELECT `ps`.`product_id`,`ps`.`stock_item_id`,`ps`.`price`,`p`.`title`,`p`.`img_url` FROM `product_stock_items` `ps` INNER JOIN `products` `p` ON `p`.`product_id` =`ps`.`product_id` ");
while($p_res && $product = db_fetch_assoc($p_res)) {
    if ($product['stock_item_id'] == 51) {
        $product1=$product;
    }
    if ($product['stock_item_id'] == 52) {
        $product2=$product;
    }
    if ($product['stock_item_id'] == 53) {
        $product3=$product;
    }if ($product['stock_item_id'] == 54) {
        $product4=$product;
    }
}


?>

<input type="hidden" id="taxarr" value='<?php echo $tax_str;?>'>
<input type="hidden" id="shiparr" value='<?php echo $ship_str;?>'>
<input type="hidden" id="pname" value='Awaken'>
<input type="hidden" id="pprice" value='49.95'>






<div class="container-fluid toplogoblock text-center">
    <img class="img-responsive" src="system/themes/awaken_and_revive/images/landing1_logo.png">
</div>


<div class="container vcproduct_wraper vcproduct_wrapernowdiv" style="margin-top: 0px;">



<div class=" col-lg-8 col-md-8 col-sm-12 col-xs-12 vcproduct_block">

    <div class="vcproduct_block_top">
        <div class="vcproduct_block_top_wrapper">
            <img class="new_pro_mcafee" src="system/themes/awaken_and_revive/images/new_pro_mcafee.png">
            <img class="new_pro_top_bannertext" src="system/themes/awaken_and_revive/images/awaken_newtext1.png">
            <img class="new_pro_top_bannertext3" src="system/themes/awaken_and_revive/images/awaken_newtext2.png">
            <img class="new_pro_top_bannertext2" src="system/themes/awaken_and_revive/images/new_pro_top_bannertext2.png">


        </div>
    </div>


<div class="vcproduct_block_newwrapper">

<?php if(count($product1)>0){
    ?>

    <div class="vcproduct_block_table vcproductactiveblock">
        <table width="100%" border="0">
            <tr>
                <th colspan="2"><?php echo $product1['title'] ?></th>
            </tr>

            <tr>
                <td align="center" valign="middle" class="balance_pro_img"> <img class="img-responsive" src="<?php echo $product1['img_url'] ?>">
                   <!-- <img class="img-responsive" src="system/themes/awaken_and_revive/images/balance_pro1.png">-->
                </td>
                <td align="center" valign="middle" class="balance_pro_text">
                    <h2>Buy 1</h2>
                    <h3> $<?php echo $product1['price'] ?></h3>

                    <input type="button" value="Selected" class="selected_btn">

                </td>
            </tr>
        </table>

  </div>
    <?php }?>
    <?php if(count($product2)>0){
    ?>
    <div class="vcproduct_block_table">
        <table width="100%" border="0">
            <tr>
                <th colspan="2"><?php echo $product2['title'] ?></th>
            </tr>

            <tr>
                <td align="center" valign="middle" class="balance_pro_img"> <img class="img-responsive" src="<?php echo $product2['img_url'] ?>"></td>
                <td align="center" valign="middle" class="balance_pro_text">

                    <h3> $<?php echo number_format($product2['price'], 2, '.', ''); ?></h3>
                    <h4>Only pay </h4>
                    <h5>$<?php echo number_format($product2['price']/3, 2, '.', '') ?>  </h5>
                    <h6>Per Program! </h6>
                    <input type="button" value="Select">
                </td>
            </tr>
        </table>

    </div>
    <?php }?>
    <?php if(count($product3)>0){
    ?>
    <div class="vcproduct_block_table">
        <table width="100%" border="0">
            <tr>
                <th colspan="2"><?php echo $product3['title'] ?> </th>
            </tr>

            <tr>
                <td align="center" valign="middle" class="balance_pro_img"> <img class="img-responsive" src="<?php echo $product3['img_url'] ?>"></td>
                <td align="center" valign="middle" class="balance_pro_text">

                    <h3> $<?php echo number_format($product3['price'], 2, '.', ''); ?></h3>
                    <h4>Only pay </h4>
                    <h5>$<?php echo number_format($product3['price']/5, 2, '.', '') ?>  </h5>
                    <h6>Per Program! </h6>
                    <input type="button" value="Select">
                </td>
            </tr>
        </table>

  </div>
    <?php }?>

    <?php if(count($product3)>0){
        ?>
        <div class="vcproduct_block_table">
            <table width="100%" border="0">
                <tr>
                    <th colspan="2"><?php echo $product4['title'] ?></th>
                </tr>

                <tr>
                    <td align="center" valign="middle" class="balance_pro_img"> <img class="img-responsive" src="<?php echo $product4['img_url'] ?>">
                        <label>BEST VALUE</label>
                    </td>
                    <td align="center" valign="middle" class="balance_pro_text">

                        <h3> $<?php echo number_format($product4['price'], 2, '.', ''); ?></h3>
                        <h4>Only pay </h4>
                        <h5>$<?php echo number_format($product4['price']/7, 2, '.', '') ?>  </h5>
                        <h6>Per Program! </h6>
                        <input type="button" value="Select">
                    </td>
                </tr>
            </table>

        </div>
    <?php }?>
  <!--  <div class="vcproduct_block_table">
        <table width="100%" border="0">
            <tr>
                <th colspan="2">Buy 4 Get 3 Free </th>
            </tr>

            <tr>
                <td align="center" valign="middle" class="balance_pro_img "> <img class="img-responsive" src="system/themes/awaken_and_revive/images/balance_pro4.png">
                <label>BEST VALUE</label>
                </td>
                <td align="center" valign="middle" class="balance_pro_text">

                    <h3> $249.80</h3>
                    <h4>Only pay </h4>
                    <h5>$35.68</h5>
                    <h6>Per Program! </h6>
                    <input type="button" value="Select">
                </td>
            </tr>
        </table>

    </div>-->






</div>




</div>

<div class=" col-lg-4 col-md-4 col-sm-12 col-xs-12 vcform_block2">

    <div class="productformnew">
        <div class="formheadingnew">

            <img class="img-responsive" src="system/themes/awaken_and_revive/images/affiliate-checkout_lockicon.png" style="display: block; margin: 0 auto;">
            <h1>FINAL STEP</h1>
            <h6>Billing Information</h6>
        </div>


        <h5>Payment information</h5>
        <h4>
            <span>Secure 128-bit SSL Connection</span>
        </h4>


       <form name="landing_page" id="landing_page" action="<?=$_SERVER['REQUEST_URI']?>" method="post">


            <div class="new_form_divwrapper">
                <h2>BILLING Address</h2>
                <div class="new_form_blockcon">
            <div class="form-group">
                <label >First Name</label>
                <input type="text"  class="form-control" />
                <div class="clearfix"></div>
            </div>

                <div class="form-group">
                    <label >Last Name</label>
                    <input type="text"  class="form-control" />
                    <div class="clearfix"></div>
                </div>

                <div class="form-group">
                    <label >Address</label>
                    <textarea class="form-control"></textarea>
                    <div class="clearfix"></div>
                </div>

                <div class="form-group">
                    <label >City</label>
                    <input type="text"  class="form-control" />
                    <div class="clearfix"></div>
                </div>

                <div class="form-group">
                    <label >State</label>
                    <?php $landing_page->draw_region_select('bill_'); ?>
                    <div class="clearfix"></div>
                </div>

                <div class="form-group">
                    <label >Zip</label>
                    <input type="text"  class="form-control" />
                    <div class="clearfix"></div>
                </div>

                <div class="form-group">
                    <label >Phone</label>
                    <input type="text"  class="form-control" />
                    <div class="clearfix"></div>
                </div>

                <div class="form-group">
                    <label >Email</label>
                    <input type="email"  class="form-control" />
                    <div class="clearfix"></div>
                </div>


                <div class="formchakebox">
                    <input id="iagreetoitnew1" class="css-checkbox" name="iagreetoitnew1" value="Y" type="checkbox">
                    <label class="css-label" for="iagreetoitnew1"></label>
                    <span style="padding-top: 0px; color: #424242;">Use my billing address as my ship-to address</span>
                    <div class="clearfix"></div>
                </div>





                </div>

        </div>

           <div class="new_form_divwrapper">
               <h2>Shipping Address</h2>
               <div class="new_form_blockcon">
               <div class="form-group">
                   <label >First Name</label>
                   <input type="text"  class="form-control" />
                   <div class="clearfix"></div>
               </div>

               <div class="form-group">
                   <label >Last Name</label>
                   <input type="text"  class="form-control" />
                   <div class="clearfix"></div>
               </div>

               <div class="form-group">
                   <label >Address</label>
                   <textarea class="form-control"></textarea>
                   <div class="clearfix"></div>
               </div>

               <div class="form-group">
                   <label >City</label>
                   <input type="text"  class="form-control" />
                   <div class="clearfix"></div>
               </div>

               <div class="form-group">
                   <label >State</label>
                   <?php $landing_page->draw_region_select('bill_'); ?>
                   <div class="clearfix"></div>
               </div>

               <div class="form-group">
                   <label >Zip</label>
                   <input type="text"  class="form-control" />
                   <div class="clearfix"></div>
               </div>

               <div class="form-group">
                   <label >Phone</label>
                   <input type="text"  class="form-control" />
                   <div class="clearfix"></div>
               </div>

               <div class="form-group">
                   <label >Email</label>
                   <input type="email"  class="form-control" />
                   <div class="clearfix"></div>
               </div>

</div>
               </div>

           <div class="new_form_divwrapper">

               <div class="new_form_blockcon2">

               <h6>Pay with Credit or Debit Card</h6>
               <div class="form-group">
                   <label >We Accep</label>
                   <img class="img-responsive" src="system/themes/awaken_and_revive/images/affiliate-checkout_cardicon.jpg">
                   <div class="clearfix"></div>
               </div>


               <div class="form-group">
                   <label >Card Type</label>
                   <select name="card_type" class="col1 cc_type" id="card_type">
                       <?php $landing_page->draw_card_type_options(); ?>
                   </select>
                   <div class="clearfix"></div>
               </div>


               <div class="form-group">
                   <label >CC #</label>
                   <input type="text"  class="form-control" />
                   <div class="clearfix"></div>
               </div>

               <div class="form-group form-groupdate">
                   <label >Exp Date</label>
                   <select name="card_exp_mo" id="card_exp_mo">
                       <?php $landing_page->draw_card_month_options_short(); ?>
                   </select>

                   <select name="card_exp_yr" id="card_exp_yr">
                       <?php $landing_page->draw_card_year_options_short(); ?>
                   </select>
                   <div class="clearfix"></div>
               </div>


               <div class="form-group">
                   <label >CVV</label>
                   <input type="text"  class="form-control2" />
                   <div class="clearfix"></div>
               </div>




        <!--   <div class="formchakebox">
               <input id="iagreetoit" class="css-checkbox" name="iagreetoit" value="Y" type="checkbox">
               <label class="css-label" for="iagreetoit"></label>
               <span data-toggle="modal" data-target="#myModalterms">Accept All Terms</span>
               <div class="clearfix"></div>
           </div>
-->
           <input type="submit" value="Rush my order"  class="formsub_btnnew1">
               </div>

           </div>
      </form>

    </div>


</div>


<div class="clearfix"></div>


<div class="vcproduct_orderbox awakentavlevlock" id="revive-promoter-checkout">
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


