<?php

$p_res = db_query("SELECT `p`.`product_id`,`p`.`title`,`p`.`description`,`p`.`features`,`p`.`benefits`,`p`.`img_url`,`ps`.`stock_item_id`,`ps`.`price` FROM `products` `p` INNER JOIN `products2folders` `pf` ON `p`.`product_id`=`pf`.`product_id` INNER JOIN `product_stock_items` `ps` ON `p`.`product_id`=`ps`.`product_id` WHERE `pf`.`folderID`=11 GROUP BY `ps`.`product_id` ORDER BY `p`.`title`");





global $AI;
require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));

$landing_page = new C_landing_pages('opportunity-enrollment');
$landing_page->pp_create_campaign = true;
$landing_page->css_error_class = 'lp_error';

//add validation rule
if(!isset($landing_page->session['created_user'])){
    $landing_page->add_validator('username', 'is_length', 3,'Invalid User Name');
    $landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
    $landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
    $landing_page->add_validator('company', 'is_length', 3,'Invalid company Name');
    $landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');
}

$landing_page->add_validator('bill_first_name', 'is_length', 3,'Invalid Billing First Name');
$landing_page->add_validator('bill_last_name', 'is_length', 3,'Invalid Billing Last Name');
$landing_page->add_validator('bill_address_line_1', 'is_length', 3,'Invalid Billing Address');
$landing_page->add_validator('bill_city', 'is_length', 3,'Invalid Billing City');
$landing_page->add_validator('bill_region', 'is_length', 2,'Invalid Billing State');
$landing_page->add_validator('bill_country', 'is_length', 2,'Invalid Billing Country');
$landing_page->add_validator('bill_postal_code', 'is_length', 5,'Invalid Billing Postal Code');
$landing_page->add_validator('bill_email', 'util_is_email','','Invalid Billing Email Address');
$landing_page->add_validator('bill_phone', 'is_phone','','Invalid Billing Phone Number');

if(!isset($_POST['bill_same_as_ship'])){
    $landing_page->add_validator('ship_first_name', 'is_length', 3,'Invalid Shipping First Name');
    $landing_page->add_validator('ship_last_name', 'is_length', 3,'Invalid Shipping Last Name');
    $landing_page->add_validator('ship_address_line_1', 'is_length', 3,'Invalid Shipping Address');
    $landing_page->add_validator('ship_city', 'is_length',3,'Invalid Shipping City');
    $landing_page->add_validator('ship_region', 'is_length', 2,'Invalid Shipping State');
    $landing_page->add_validator('ship_country', 'is_length', 2,'Invalid Shipping Country');
    $landing_page->add_validator('ship_postal_code', 'is_length', 5,'Invalid Shipping Postal Code');
    $landing_page->add_validator('ship_email', 'util_is_email','','Invalid Shipping Email Address');
    $landing_page->add_validator('ship_phone', 'is_phone','','Invalid Shipping Phone Number');
}

$landing_page->add_validator('card_name', 'is_length', 3,'Invalid Name on Card');
$landing_page->add_validator('card_number', 'is_length', 14,'Invalid Card Number');
$landing_page->add_validator('card_type', 'is_length', 2,'Invalid Card Type');
$landing_page->add_validator('card_exp_mo', 'card_expire_check', '','Invalid Card Expiration');
$landing_page->add_validator('card_cvv', 'is_length', 3,'Invalid Card Security Code (CVV)');

$landing_page->add_validator('check_terms','is_checked','','You must accept the Terms &amp; Conditions');


$landing_page->card_type_options = array('visa'=>'Visa','mast'=>'Mastercard','amx'=>'American Express','disc'=>'Discover');
$landing_page->no_ship_addr = true;

$is_chk = 0;
$pid = 0;

if(isset($landing_page->session['form_data']['bill_same_as_ship'])){
    $is_chk = 1;
}
if(isset($landing_page->session['form_data']['pid'])){
    $pid = $landing_page->session['form_data']['pid'];
}


if(util_is_POST()) {

    $landing_page->validate();

    if(isset($landing_page->session['created_user'])){
        $landing_page->save_order();
        if ($landing_page->has_errors()) {
            $landing_page->display_errors();
        } else {
            $landing_page->clear_session();
            unset($landing_page->session['lead_id']);
            unset($landing_page->session['created_user']);
            util_redirect('/');
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
        elseif( isset($_POST['retype_password']) && $_POST['password'] != trim(@$_POST['retype_password']) )
        {
            $err = 'Your passwords do not match. Please re-type them.';
            $js[]="jonbox_alert('".$err."');";
            if(count($js)>0) $AI->skin->js_onload("//DRAW LP ERRORS:\n\n".implode("\n\n",$js));
        }else{
            if($landing_page->save_lead($AI->get_setting('owner_id')))
            {
                $landing_page->save_user('Distributor');
                if($landing_page->has_errors()) { $landing_page->display_errors(); }
                else {
                    //save oreder
                    $landing_page->save_order();
                    if ($landing_page->has_errors()) {
                        $landing_page->display_errors();
                    } else {
                        $landing_page->clear_session();
                        unset($landing_page->session['lead_id']);
                        unset($landing_page->session['created_user']);
                        util_redirect('/');
                    }
                }
            }else{
                $landing_page->display_errors();
            }

        }

    }


}

$landing_page->refill_form();

?>

<script>
    $(function(){
        var is_chk = '<?php echo $is_chk; ?>';
        var pid = '<?php echo $pid; ?>';
        bill_ship_same(is_chk);

        if(pid > 0){
            productsel($('#bynowoppo'+pid));
        }

        $('#checkout_billing_shipping_same').change(function(){
            if($(this).is(':checked')){
                is_chk = 1;
            }else{
                is_chk = 0;
            }
            bill_ship_same(is_chk);
        });


        $('.bynowoppo').click(function(){
            productsel($(this));
        });

    });


    function productsel(obj){
        var shipping = 0;
        var tax = 0;
        shipping = parseFloat(shipping);
        tax = parseFloat(tax);
        var stock_item_id = $(obj).attr('stock_item_id');
        var ptitle = $(obj).attr('ptitle');
        var price = $(obj).attr('price');
        price = parseFloat(price);
        $('#pid').val(stock_item_id);
        $('.bynowoppo').text('buy now');
        $(obj).text('selected');

        var totalamnt = price+shipping+tax;

        $('#ptitle').text(ptitle);
        $('#pprice').text(price.toFixed(2));
        $('#pamnt').text(price.toFixed(2));
        $('#psubtotal').text(price.toFixed(2));
        $('#pship').text(shipping.toFixed(2));
        $('#ptax').text(tax.toFixed(2));
        $('#ptotal').text(totalamnt.toFixed(2));
        $('#pquan').text(1);
    }
    
    
    function bill_ship_same(is_chk) {

        if(is_chk == 1){
            $('.shipbillcls').hide();
        }else {

            $('#ship_first_name').val($('#bill_first_name').val());
            $('#ship_last_name').val($('#bill_last_name').val());
            $('#ship_address_line_1').val($('#bill_address_line_1').val());
            $('#ship_city').val($('#bill_city').val());
            $('#ship_region').val($('#bill_region').val());
            $('#ship_email').val($('#bill_email').val());
            $('#ship_phone').val($('#bill_phone').val());
            $('#ship_postal_code').val($('#bill_postal_code').val());


            $('.shipbillcls').show();
        }
    }
    
</script>

<div class="container-fluid innerpagetitleblock text-center">
<div class="innerpagetitleblockwrapper">
    <h1>Opportunity</h1>
</div>
</div>

<div class="container-fluid">
    <div class="container containerwrapper">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 aboutusblock1 aboutusblock1newtext">
                <p>
                    <img src="system/themes/vivacity_frontend/images/aboutusimg1.jpg" class="img-responsive aboutimg1">
                    <span class="titleinfo">Vivacity’s Mission Statement</span>
                    <span class="titleheadlinenew2">Get Pre-Qualified For Promotership!</span>
                    Vivacity is dedicated to bringing all points together for
                    experiencing the vital essence of an inspired life.<br><br>Come alive with our industry-changing, all natural wellness products, generous compensation plan, and overall whole-self approach to living your life on purpose. Take charge, be a leader, and let’s MakeWay to a new life for each of us, our loved ones, and generations that follow.<br><br>
                    <img src="system/themes/vivacity_frontend/images/aboutusimg2.jpg" class="img-responsive aboutimg2new">
                    Life should be about more than earning money, paying bills, and all that falls in between. New homes, clothes, and material possessions are nice, but at the end of the day they mean nothing if we do not feel good. Quality of life means as much, if not more, than quantity. At MakeWay Wellness we believe in the whole self approach and aim to nurture mind, body, soul, and spirit. We want to help you make every second count!<br><br>We provide the industry’s BEST compensation plan and, above all, offer top notch wellness products. We focus on personal growth, building relationships, friends, family, and giving back to a world in need. Our
                    mission is simple: to live life on purpose.
                </p>
            </div>


            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 aboutusblock1">
                <span class="titleinfo2">Choose From Any Of our Programs and you’re pre-qualified to become
a promoter</span>



        </div>
    </div>
</div>

</div>



<div class="container containerwrapper containerwrapper_opportunity ">


    <?php

    while($p_res && $product = db_fetch_assoc($p_res)) {

        $img_path = $product['img_url'];

        if (!file_exists($img_path)) {
            $img_path = "system/themes/vivacity_frontend/images/defaultproduct.png";
        }

        $id = $product['product_id'];
        $title = $product['title'];
        $url_title = strtolower($title);
        $url_title = preg_replace("/[^a-z0-9_\s-]/", "", $url_title);
        $url_title = preg_replace("/[\s-]+/", " ", $url_title);
        $url_title = preg_replace("/[\s_]/", "-", $url_title);



        ?>
        <div class="opportunity_block_con">

            <div class="opp_div1"> <img src="<?php echo $img_path; ?>" > </div>
            <div class="opp_div2">
                <h2><?php echo $title;?></h2>
                <h3><?php echo $AI->get_defaulted_dynamic_area($product['description'],'');?></h3>


            </div>

            <div class="opp_div3">
                <h4>Benefits</h4>
                <h5>Decrease pain from*</h5>


                <?php  echo $AI->get_defaulted_dynamic_area($product['benefits'],'');?>


            </div>

            <div class="opp_div4">
                <img src="system/themes/vivacity_frontend/images/ad_opportunity_img1.jpg" class="opportunity_proimg">
                <a href="javascript:void(0)" class="opplink_pro1" onclick="js:$('#programopModal<?php echo $product['stock_item_id'];?>').modal('show');">More Info</a>
                <a href="javascript:void(0)" class="opplink_pro2 bynowoppo" id="bynowoppo<?php echo $product['stock_item_id'];?>" stock_item_id="<?php echo $product['stock_item_id'];?>" ptitle="<?php echo $title;?>" price="<?php echo $product['price'];?>">buy now</a>
                <div class="clearfix"></div>
            </div>


            <div class="clearfix"></div>

        </div>


        <div class="modal fade " id="programopModal<?php echo $product['stock_item_id'];?>" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo $title;?></h4>
                    </div>
                    <div class="modal-body">
                        <h3><?php echo $AI->get_defaulted_dynamic_area($product['description']);?></h3>
                    </div>
                </div>

            </div>
        </div>



        <?php
    }
    ?>






    </div>




<div class="checkoutblockwrapper checkoutblockwrappernew">

    <div class="container-fluid checkoutblock1">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 checkoutfromblock">
            <form name="landing_page" id="landing_page_chk" action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="form-inline">

                <input type="hidden" name="bill_country" value="US">
                <input type="hidden" name="ship_country" value="US">

                <input name="pid" type="hidden" value="" id="pid">



<?php
if(!isset($landing_page->session['created_user'])) {
    ?>

    <div class="hrlinenew"></div>
    <h2>User Information</h2>


    <div class="form-group">
        <label for="first_name">First Name<span>*</span></label>
        <input type="text"  class="form-control" name="first_name" id="first_name" />
        <!---<span class="help-block errormsg">firstname is not valid</span>--->
    </div>


    <div class="form-group">
        <label for="last_name"> Last Name<span>*</span></label>
        <input type="text"  class="form-control" name="last_name" id="last_name"  />
    </div>

    <div class="form-group">
        <label for="company">Company  <span>*</span></label>
        <input type="text"  class="form-control" name="company" id="company"  />
    </div>
    <div class="form-group">
        <label for="email"> Email  <span>*</span></label>
        <input type="email"  class="form-control" name="email" id="email"  />
    </div>
    <div class="form-group">
        <label for="username"> Username  <span>*</span></label>
        <input type="text"  class="form-control" name="username" id="username"  />
    </div>

    <div class="form-group">
        <label for="password"> Password  <span>*</span></label>
        <input type="password"  class="form-control" name="password" id="password"  />
    </div>


    <?php
}
?>

                <div class="clearfix"></div>

                <div class=" singlecolumn">
                    <h5 style="float: none; margin: 0; padding:0px; font-weight: normal;">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="checkout_billing_shipping_same" class="" name="bill_same_as_ship" value="1" />
                                My billing and shipping address are the same
                            </label>
                        </div>
                    </h5>

                </div>




                <div class="hrlinenew"></div>
                <div class="clearfix"></div> <h2>BILLING INFORMATION</h2>


                <div class="form-group">
                    <label for="bill_first_name">First Name<span>*</span></label>
                    <input type="text"  class="form-control"  name="bill_first_name" id="bill_first_name" />
                </div>
                <div class="form-group">
                    <label for="bill_last_name">Last Name<span>*</span></label>
                    <input type="text"  class="form-control"  name="bill_last_name" id="bill_last_name" />
                </div>
                <div class="form-group">
                    <label for="bill_address_line_1">Address<span>*</span></label>
                    <textarea class="form-control" name="bill_address_line_1" id="bill_address_line_1"></textarea>
                </div>

                <div class="form-group">
                    <label for="bill_city">City <span>*</span></label>
                    <input type="text"  class="form-control"  name="bill_city" id="bill_city" />
                </div>


                <div class="form-group">
                    <label for="bill_state">State<span>*</span></label>
                    <?php $landing_page->draw_region_select('bill_'); ?>
                </div>

                <div class="form-group">
                    <label for="bill_postal_code">Zip Code<span>*</span></label>
                    <input type="text"  class="form-control" name="bill_postal_code" id="bill_postal_code"  />
                </div>

                <div class="form-group">
                    <label for="bill_phone">Phone <span>*</span></label>
                    <input type="text"  class="form-control" name="bill_phone" id="bill_phone"  />
                </div>


                <div class="form-group">
                    <label for="bill_email">Billing Email <span>*</span></label>
                    <input type="email"  class="form-control" name="bill_email" id="bill_email"  />
                </div>


                <div class="clearfix"></div>

                <div class="hrlinenew shipbillcls"></div>


                <h2 class="shipbillcls">SHIPPING INFORMATION</h2>


                <div class="form-group shipbillcls">
                    <label for="ship_first_name">First Name<span>*</span></label>
                    <input type="text"  class="form-control"  name="ship_first_name" id="ship_first_name" />
                </div>
                <div class="form-group shipbillcls">
                    <label for="ship_last_name">Last Name<span>*</span></label>
                    <input type="text"  class="form-control"  name="ship_last_name" id="ship_last_name" />
                </div>
                <div class="form-group shipbillcls">
                    <label for="ship_address_line_1">Address<span>*</span></label>
                    <textarea class="form-control" name="ship_address_line_1" id="ship_address_line_1"></textarea>
                </div>

                <div class="form-group shipbillcls">
                    <label for="ship_city">City <span>*</span></label>
                    <input type="text"  class="form-control"  name="ship_city" id="ship_city" />
                </div>


                <div class="form-group shipbillcls">
                    <label for="ship_state">State<span>*</span></label>
                    <?php $landing_page->draw_region_select('ship_'); ?>
                </div>

                <div class="form-group shipbillcls">
                    <label for="ship_postal_code">Zip Code<span>*</span></label>
                    <input type="text"  class="form-control" name="ship_postal_code" id="ship_postal_code"  />
                </div>

                <div class="form-group shipbillcls">
                    <label for="ship_phone">Phone <span>*</span></label>
                    <input type="text"  class="form-control" name="ship_phone" id="ship_phone"  />
                </div>


                <div class="form-group shipbillcls">
                    <label for="ship_email">Shipping Email <span>*</span></label>
                    <input type="email"  class="form-control" name="ship_email" id="ship_email"  />
                </div>

                <div class="clearfix shipbillcls"></div>





                <div class="hrlinenew"></div>


                <h2>PAYMENT INFORMATION</h2>

                <div class="form-group singlecolumn">
                    <div class="paymentmode">
                        <?php echo get_cc_radio('card_type');?>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 tripplecolumn">
                        <label for="city">Card Name</label>
                        <input type="text" id="card_name" class="span8" name="card_name" />
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 tripplecolumnnew1">
                        <label for="city">Card Number</label>
                        <input type="text" id="card_number" class="span8" name="card_number" />
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 cardexpiredblock">
                        <label for="city">Expiry Date</label>
                        <?php echo get_cc_expire_input('card_exp_mo', 'card_exp_yr'); ?>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 tripplecolumn">
                        <label for="city">CVV</label>
                        <input type="text" id="card_cvv" name="card_cvv"  class="span4" maxlength="8" />
                    </div>


                </div>

                <div class="clearfix"></div>

                <div class="hrlinenew"></div>
                <h2>REVIEW PURCHASE</h2>


                <div class=" singlecolumn">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td>ITEM</td>
                                <td>Price</td>
                                <td>QUANTITY</td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td><span id="ptitle">N/A</span></td>
                                <td>$<span id="pprice">0.00</span></td>
                                <td><span id="pquan">0</span></td>
                                <td>$<span id="pamnt">0.00</span></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Sub-Total</td>
                                <td>$<span id="psubtotal">0.00</span></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Shipping</td>
                                <td>$<span id="pship">0.00</span></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Tax</td>
                                <td>$<span id="ptax">0.00</span></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>TOTAL</td>
                                <td>$<span id="ptotal">0.00</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="hrlinenew"></div>



                <h2>TERMS & CONDITIONS</h2>

                <div class="form-group singlecolumn tctext">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vestibulum lobortis metus vel vulputate. Maecenas imperdiet purus a velit egestas egestas. Nullam in velit vitae massa dapibus dignissim lacinia id urna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis id quam aliquet, porttitor magna at, mattis augue. Vestibulum pellentesque aliquet fermentum. Nulla facilisi. Pellentesque at fermentum metus. Integer pulvinar massa in ligula pulvinar, molestie mattis ligula gravida. Vivamus ut sem a nulla fringilla dictum ut eu tellus. Mauris non arcu facilisis, dignissim enim vitae, pellentesque sem.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vestibulum lobortis metus vel vulputate. Maecenas imperdiet purus a velit egestas egestas. Nullam in velit vitae massa dapibus dignissim lacinia id urna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis id quam aliquet, porttitor magna at, mattis augue. Vestibulum pellentesque aliquet fermentum. Nulla facilisi. Pellentesque at fermentum metus. Integer pulvinar massa in ligula pulvinar, molestie mattis ligula gravida. Vivamus ut sem a nulla fringilla dictum ut eu tellus. Mauris non arcu facilisis, dignissim enim vitae, pellentesque sem.</p>
                </div>
                <div class="clearfix"></div>
                <div class=" singlecolumn">
                    <h5 style="float: none; margin: 25px 0 0 0; padding:0px; font-weight: normal;">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="iagreetoit" name="check_terms" value="1" />
                                I agree to the terms & conditions
                            </label>
                        </div>
                    </h5>
                </div>

                <div class="clearfix"></div>

                <div class=" opportunity_btnwrapper">
<input type="submit" value="submit">
<input type="reset"value="cancel">

                    <div class="clearfix"></div>
                    </div>


                </form>


        </div>
    </div>
</div>