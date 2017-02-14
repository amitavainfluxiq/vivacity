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


$landing_page = new C_landing_pages('xcellerate-affiliate');

$landing_page->pp_create_campaign = true;

$landing_page->css_error_class = 'lp_error';


//add validation rule
if(!isset($landing_page->session['created_user']) && !$AI->user->is_logged_in()){
    $landing_page->add_validator('username', 'is_length', 3,'Invalid User Name');
    $landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
    $landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
}

$landing_page->add_validator('pid', 'is_length', 1,'Please select one product');
$landing_page->add_validator('bill_first_name', 'is_length', 3,'Invalid Billing First Name');
$landing_page->add_validator('bill_last_name', 'is_length', 3,'Invalid Billing Last Name');
$landing_page->add_validator('bill_address_line_1', 'is_length', 5,'Invalid Billing Address');
$landing_page->add_validator('bill_city', 'is_length', 5,'Invalid Billing City');
$landing_page->add_validator('bill_region', 'is_length', 2,'Invalid Billing State');
$landing_page->add_validator('bill_country', 'is_length', 2,'Invalid Country');
$landing_page->add_validator('bill_postal_code', 'is_length', 5,'Invalid Billing Postal Code');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');
$landing_page->add_validator('phone', 'is_phone','','Invalid Phone Number');
//$landing_page->add_validator('besttime', 'is_length', 3,'Invalid \'Best time to call\'');

if(!isset($_POST['bill_same_as_ship'])){
    $landing_page->add_validator('ship_first_name', 'is_length', 3,'Invalid Shipping First Name');
    $landing_page->add_validator('ship_last_name', 'is_length', 3,'Invalid Shipping Last Name');
    $landing_page->add_validator('ship_address_line_1', 'is_length', 3,'Invalid Shipping Address');
    $landing_page->add_validator('ship_city', 'is_length',3,'Invalid Shipping City');
    $landing_page->add_validator('ship_region', 'is_length', 2,'Invalid Shipping State');
    $landing_page->add_validator('ship_country', 'is_length', 2,'Invalid Shipping Country');
    $landing_page->add_validator('ship_postal_code', 'is_length', 5,'Invalid Shipping Postal Code');
}


$landing_page->add_validator('card_number', 'is_length', 14,'Invalid Card Number');
$landing_page->add_validator('card_type', 'is_length', 2,'Invalid Card Type');
$landing_page->add_validator('card_exp_mo', 'card_expire_check', '','Invalid Card Expiration');
$landing_page->add_validator('card_cvv', 'is_length', 3,'Invalid Card Security Code (CVV)');

$landing_page->card_type_options = array('visa'=>'Visa','mast'=>'Mastercard','amx'=>'American Express','disc'=>'Discover');
$landing_page->no_ship_addr = true;

$is_chk = 0;
$pid = '';

if(isset($landing_page->session['form_data']['bill_same_as_ship'])){
    $is_chk = 1;
}
if(isset($landing_page->session['form_data']['pid'])){
    $pid = $landing_page->session['form_data']['pid'];
}

if(util_is_POST()) {
    $landing_page->set_shipping_amt($_POST['shipping']);

    $util_rep_id = 100;

    if(util_rep_id()){
        $util_rep_id = util_rep_id();
    }

    $util_rep_id = 99;

    $landing_page->validate();

    if($AI->user->is_logged_in()){
        $userID = $AI->user->userID;
        $landing_page->load_userID($userID);
        $landing_page->save_order();
        if ($landing_page->has_errors()) {
            $landing_page->display_errors();
        } else {
            $created_order = $landing_page->session['created_order'];

            $landing_page->clear_session();
            unset($landing_page->session['lead_id']);
            unset($landing_page->session['created_user']);
            unset($landing_page->session['created_order']);

            after_saveorder($userID,$util_rep_id,$created_order);
        }
    }elseif(isset($landing_page->session['created_user'])){
        $landing_page->save_order();
        if ($landing_page->has_errors()) {
            $landing_page->display_errors();
        } else {
            $created_user = $landing_page->session['created_user'];
            $created_order = $landing_page->session['created_order'];

            $landing_page->clear_session();
            unset($landing_page->session['lead_id']);
            unset($landing_page->session['created_user']);
            unset($landing_page->session['created_order']);
            after_saveorder($created_user,$util_rep_id,$created_order);
        }
    }else{
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
        elseif( isset($_POST['retype_password']) && $_POST['password'] != trim(@$_POST['retype_password']) ) {
            $err = 'Your passwords do not match. Please re-type them.';
            $js[] = "jonbox_alert('" . $err . "');";
            if (count($js) > 0) $AI->skin->js_onload("//DRAW LP ERRORS:\n\n" . implode("\n\n", $js));
        }else {
            //save user as distributor
            $landing_page->save_user('Customer');
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





                    $created_user = $landing_page->session['created_user'];
                    $created_order = $landing_page->session['created_order'];

                    $landing_page->clear_session();
                    unset($landing_page->session['lead_id']);
                    unset($landing_page->session['created_user']);
                    unset($landing_page->session['created_order']);

                    after_saveorder($created_user,$util_rep_id,$created_order);

                }
            }
        }
    }

}


if(!isset($landing_page->session['form_data']['bill_first_name'])){
    $landing_page->set_data('bill_first_name',$landing_page->get_data('first_name'));
}

if(!isset($landing_page->session['form_data']['bill_last_name'])){
    $landing_page->set_data('bill_last_name',$landing_page->get_data('last_name'));
}

if(!isset($landing_page->session['form_data']['bill_address_line_1'])){
    $landing_page->set_data('bill_address_line_1',$landing_page->get_data('address'));
}

if(!isset($landing_page->session['form_data']['bill_city'])){
    $landing_page->set_data('bill_city',$landing_page->get_data('city'));
}

if(!isset($landing_page->session['form_data']['bill_region'])){
    $landing_page->set_data('bill_region',$landing_page->get_data('state'));
}

if(!isset($landing_page->session['form_data']['bill_postal_code'])){
    $landing_page->set_data('bill_postal_code',$landing_page->get_data('zip'));
}

$landing_page->refill_form();



function after_saveorder($userid,$util_rep_id,$orderid){
    $gene = new C_genealogy(C_genealogy::GENEALOGY_TREE);
    try
    {
        $gene->insert_node($userid, $util_rep_id, null, 'stop', true);
    }
    catch ( NodeAlreadyInTreeException $naite )
    {
        $data = $naite->get_data();
        if ( $data['parent'] != $util_rep_id )
        {
            $gene->move_sub_tree($userid, $util_rep_id, 0);
        }
    }

    // add user at enrollment tree

    $gene = new C_genealogy(C_genealogy::ENROLLER_TREE);
    try
    {
        $gene->insert_node($userid, $util_rep_id, null, 'stop', true);
    }
    catch ( NodeAlreadyInTreeException $naite )
    {
        $data = $naite->get_data();
        if ( $data['parent'] != $util_rep_id )
        {
            $gene->move_sub_tree($userid, $util_rep_id, 0);
        }
    }


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




    util_redirect('xcellerate-success?order='.$orderid);

}










$tax_str = '';
$tax_arr = array();

$res = db_query("SELECT * FROM `taxes`");

while($res && $row = db_fetch_assoc($res)) {
    $tax_arr[$row['region']] = $row['amount'];
}

$tax_str = json_encode($tax_arr);

$price_arr = array();

$product1=array();
$product2=array();
$product3=array();
$product4=array();
$p_res = db_query("SELECT `ps`.`product_id`,`ps`.`stock_item_id`,`ps`.`price`,`p`.`title`,`p`.`img_url` FROM `product_stock_items` `ps` INNER JOIN `products` `p` ON `p`.`product_id` =`ps`.`product_id` ");
while($p_res && $product = db_fetch_assoc($p_res)) {
    $price_arr[] = $product['price'];
    if ($product['stock_item_id'] == 55) {
        $product1=$product;
    }
    if ($product['stock_item_id'] == 56) {
        $product2=$product;
    }
    if ($product['stock_item_id'] == 57) {
        $product3=$product;
    }if ($product['stock_item_id'] == 58) {
        $product4=$product;
    }
}


$ship_str = '';
$ship_arr = array();

foreach ($price_arr as $val){
    $res = db_query("SELECT * FROM `shipping_by_price` WHERE `min` <".$val." && `max` >=".$val);
    $ship_arr[$val] = 6;
    while($res && $row = db_fetch_assoc($res)) {
        $ship_arr[$val] = $row['cost'];
    }
    $ship_str = json_encode($ship_arr);
}


?>

<script>
    $(function(){
        var is_chk = '<?php echo $is_chk; ?>';
        var pid = '<?php echo $pid; ?>';
        bill_ship_same(is_chk);

        if(pid != ''){
            $('#pbtn'+pid).click();
        }

    });

</script>


<input type="hidden" id="taxarr" value='<?php echo $tax_str;?>'>
<input type="hidden" id="shiparr" value='<?php echo $ship_str;?>'>



<div class="container-fluid toplogoblock text-center">
    <img class="img-responsive" src="system/themes/awaken_cbd/images/landing1_logo.png">
</div>


<div class="container vcproduct_wraper vcproduct_wrapernowdiv" style="margin-top: 0px;">



    <div class=" col-lg-8 col-md-8 col-sm-12 col-xs-12 vcproduct_block">

        <div class="vcproduct_block_top">
            <div class="vcproduct_block_top_wrapper">
                <img class="new_pro_mcafee" src="system/themes/awaken_cbd/images/xcellerate_top_img1.png">
                <img class="new_pro_top_bannertext" src="system/themes/awaken_cbd/images/xcellerate_top_newimg1.png">
                <img class="new_pro_top_bannertext3" src="system/themes/awaken_cbd/images/xcellerate_top_newimg2.png">
                <img class="new_pro_top_bannertext2" src="system/themes/awaken_cbd/images/xcellerate_top_img3.png">


            </div>
        </div>


        <div class="vcproduct_block_newwrapper">


            <?php if(count($product1)>0){?>
                <div class="vcproduct_block_table">
                    <table width="100%" border="0">
                        <tr>
                            <th colspan="2"><?php echo $product1['title']?></th>
                        </tr>

                        <tr>
                            <td align="center" valign="middle" class="balance_pro_img"> <img class="img-responsive" src="<?php echo $product1['img_url']?>"></td>
                            <td align="center" valign="middle" class="balance_pro_text">
                                <h2>Buy 1</h2>
                                <h3> $<?php echo number_format($product1['price'], 2, '.', ''); ?></h3>

                                <input  class="pselbtn" id="pbtn<?php echo $product1['stock_item_id'] ?>" type="button" value="Select" ptitle="<?php echo $product1['title'] ?>" pprice="<?php echo $product1['price'] ?>" stock_item_id="<?php echo $product1['stock_item_id'] ?>">

                            </td>
                        </tr>
                    </table>

                </div>
            <?php }?>
            <?php if(count($product2)>0){?>
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
                                <h6>Per bottle!</h6>
                                <input   class="pselbtn" id="pbtn<?php echo $product2['stock_item_id'] ?>" type="button" value="Select" ptitle="<?php echo $product2['title'] ?>" pprice="<?php echo $product2['price'] ?>" stock_item_id="<?php echo $product2['stock_item_id'] ?>">
                            </td>
                        </tr>
                    </table>

                </div>
            <?php }?>
            <?php if(count($product3)>0){?>

                <div class="vcproduct_block_table">
                    <table width="100%" border="0">
                        <tr>
                            <th colspan="2"><?php echo $product3['title'] ?></th>
                        </tr>

                        <tr>
                            <td align="center" valign="middle" class="balance_pro_img"> <img class="img-responsive" src="<?php echo $product3['img_url'] ?>"></td>
                            <td align="center" valign="middle" class="balance_pro_text">

                                <h3> $<?php echo number_format($product3['price'], 2, '.', ''); ?></h3>
                                <h4>Only pay </h4>
                                <h5>$<?php echo number_format($product3['price']/5, 2, '.', ''); ?> </h5>
                                <h6>Per bottle!</h6>
                                <input  class="pselbtn" id="pbtn<?php echo $product3['stock_item_id'] ?>" type="button" value="Select" ptitle="<?php echo $product3['title'] ?>" pprice="<?php echo $product3['price'] ?>" stock_item_id="<?php echo $product3['stock_item_id'] ?>">
                            </td>
                        </tr>
                    </table>

                </div>
            <?php }?>
            <?php if(count($product4)>0){?>

                <div class="vcproduct_block_table">
                    <table width="100%" border="0">
                        <tr>
                            <th colspan="2"><?php echo $product4['title'] ?></th>
                        </tr>

                        <tr>
                            <td align="center" valign="middle" class="balance_pro_img "> <img class="img-responsive" src="<?php echo $product4['img_url'] ?>">
                                <label>BEST VALUE</label>
                            </td>
                            <td align="center" valign="middle" class="balance_pro_text">

                                <h3> $<?php echo number_format($product4['price'], 2, '.', ''); ?></h3>
                                <h4>Only pay </h4>
                                <h5>$<?php echo number_format($product4['price']/7, 2, '.', ''); ?></h5>
                                <h6>Per bottle! </h6>
                                <input class="pselbtn" id="pbtn<?php echo $product4['stock_item_id'] ?>" type="button" value="Select" ptitle="<?php echo $product4['title'] ?>" pprice="<?php echo $product4['price'] ?>" stock_item_id="<?php echo $product4['stock_item_id'] ?>">
                            </td>
                        </tr>
                    </table>

                </div>


            <?php }?>



        </div>




    </div>

    <div class=" col-lg-4 col-md-4 col-sm-12 col-xs-12 vcform_block2">

        <div class="productformnew">
            <div class="formheadingnew">

                <img class="img-responsive" src="system/themes/awaken_cbd/images/affiliate-checkout_lockicon.png" style="display: block; margin: 0 auto;">
                <h1>FINAL STEP</h1>
                <h6>Billing Information</h6>
            </div>


            <h5>Payment information</h5>
            <h4>
                <span>Secure 128-bit SSL Connection</span>
            </h4>


            <form name="landing_page" id="landing_page" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                <input type="hidden" name="bill_country" value="US">
                <input type="hidden" name="ship_country" value="US">
                <input name="pid" type="hidden" value="" id="pid">
                <input name="shipping" type="hidden" value="0" id="shipping">
                <?php
                if(!isset($landing_page->session['created_user'])  && !$AI->user->is_logged_in()) {
                    ?>
                    <div class="new_form_divwrapper">
                        <h2>User Information</h2>
                        <div class="new_form_blockcon">
                            <div class="form-group">
                                <label >First Name</label>
                                <input type="text"  class="form-control" name="first_name" id="first_name" />
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                                <label >Last Name</label>
                                <input type="text"  class="form-control" name="last_name" id="last_name" />
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <label >User Name</label>
                                <input type="text"  class="form-control" name="username" id="username" />
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                                <label >Password</label>
                                <input type="password"  class="form-control" name="password" id="password" />
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <!--<div class="new_form_divwrapper">
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
                            <?php /*$landing_page->draw_region_select('bill_'); */?>
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

                </div>-->
                <div class="new_form_divwrapper">
                    <h2>BILLING Address</h2>
                    <div class="new_form_blockcon">
                        <div class="form-group">
                            <label >First Name</label>
                            <input type="text"  class="form-control fieldcommon"  name="bill_first_name" id="bill_first_name" />
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >Last Name</label>
                            <input type="text"  class="form-control fieldcommon"  name="bill_last_name" id="bill_last_name" />
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >Address</label>
                            <textarea class="form-control fieldcommon" name="bill_address_line_1" id="bill_address_line_1"></textarea>
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >City</label>
                            <input type="text"  class="form-control fieldcommon"  name="bill_city" id="bill_city" />
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >State</label>
                            <?php $landing_page->draw_region_select('bill_'); ?>
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >Zip</label>
                            <input type="text"  class="form-control fieldcommon" name="bill_postal_code" id="bill_postal_code" />
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >Phone</label>
                            <input type="text"  class="form-control fieldcommon" name="phone" id="phone" />
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >Email</label>
                            <input type="email"  class="form-control fieldcommon" name="email" id="email" />
                            <div class="clearfix"></div>
                        </div>

                        <div class="formchakebox">
                            <input id="iagreetoitnew1" class="css-checkbox" name="bill_same_as_ship" value="1" type="checkbox">
                            <label class="css-label" for="iagreetoitnew1"></label>
                            <span style="padding-top: 0px; color: #424242;">Use my billing address as my ship-to address</span>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="new_form_divwrapper shipbillcls">
                    <h2>Shipping Address</h2>
                    <div class="new_form_blockcon">
                        <div class="form-group">
                            <label >First Name</label>
                            <input type="text"  class="form-control fieldcommon"  name="ship_first_name" id="ship_first_name" />
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >Last Name</label>
                            <input type="text"  class="form-control fieldcommon" name="ship_last_name" id="ship_last_name" />
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >Address</label>
                            <textarea class="form-control fieldcommon" name="ship_address_line_1" id="ship_address_line_1"></textarea>
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >City</label>
                            <input type="text"  class="form-control fieldcommon" name="ship_city" id="ship_city" />
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >State</label>
                            <?php $landing_page->draw_region_select('ship_'); ?>
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label >Zip</label>
                            <input type="text"  class="form-control fieldcommon" name="ship_postal_code" id="ship_postal_code" />
                            <div class="clearfix"></div>
                        </div>


                    </div>
                </div>

             <!--   <div class="new_form_divwrapper">
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
                            <?php /*$landing_page->draw_region_select('bill_'); */?>
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
                </div>-->

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
                            <input type="text"  class="form-control" name="card_number" id="card_number" />
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
                            <input type="text"  class="form-control2" name="card_cvv" id="card_cvv" />
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
                <td valign="middle" align="left"><span id="package_name">No Package</span></td>
            </tr>
            <tr>
                <td valign="middle" align="left">Sub-total: </td>
                <td valign="middle" align="left"><span id="package_subtotal">$0.00</span></td>
            </tr>
            <tr>
                <td valign="middle" align="left">Shipping:</td>
                <td valign="middle" align="left"><span id="package_shipping">$0.00</span></td>
            </tr>
            <tr>
                <td valign="middle" align="left">Sales Tax: </td>
                <td valign="middle" align="left"><span id="package_tax">$0.00</span></td>
            </tr>
            <tr>
                <td valign="middle" align="left">Total: </td>
                <td valign="middle" align="left"><span id="package_total">$0.00</span> </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>