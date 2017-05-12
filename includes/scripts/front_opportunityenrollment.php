<?php

//$p_res = db_query("SELECT `p`.`product_id`,`p`.`title`,`p`.`description`,`p`.`features`,`p`.`benefits`,`p`.`img_url`,`ps`.`stock_item_id`,`ps`.`price`,`ps`.`alt_prices` FROM `products` `p` INNER JOIN `products2folders` `pf` ON `p`.`product_id`=`pf`.`product_id` INNER JOIN `product_stock_items` `ps` ON `p`.`product_id`=`ps`.`product_id` WHERE `pf`.`folderID`=11 GROUP BY `ps`.`product_id` ORDER BY `p`.`product_id`");
$p_res = db_query("SELECT `p`.`product_id`,`p`.`title`,`p`.`description`,`p`.`features`,`p`.`benefits`,`p`.`img_url`,`ps`.`stock_item_id`,`ps`.`price`,`ps`.`alt_prices` FROM `products` `p` INNER JOIN `product_stock_items` `ps` ON `p`.`product_id`=`ps`.`product_id` WHERE `p`.`product_id` IN (9,25,26,27) GROUP BY `ps`.`product_id` ORDER BY `p`.`sort_order`");

$product_arr = array();

$price_arr = array();

while($p_res && $product = db_fetch_assoc($p_res)) {

    $img_path = $product['img_url'];

    if (!file_exists($img_path)) {
        $img_path = "system/themes/vivacity_frontend/images/defaultproduct.png";
    }

    $product['img_path'] = $img_path;
    $altprice=unserialize($product['alt_prices']);

    $product['price']=$product['price'];
    if($AI->user->account_type == 'Distributor' && $altprice[0]['price']!=0){
        $product['price']= $altprice[0]['price'];
    }

    $product_arr[] = $product;

    $price_arr[] = $product['price'];

}

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

//$landing_page->add_validator('card_name', 'is_length', 3,'Invalid Name on Card');
//$landing_page->add_validator('card_number', 'is_length', 14,'Invalid Card Number');
//$landing_page->add_validator('card_type', 'is_length', 2,'Invalid Card Type');
//$landing_page->add_validator('card_exp_mo', 'card_expire_check', '','Invalid Card Expiration');
//$landing_page->add_validator('card_cvv', 'is_length', 3,'Invalid Card Security Code (CVV)');

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
    $landing_page->set_shipping_amt($_POST['shipping']);
    $landing_page->validate();

    if(isset($landing_page->session['created_user'])){
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

            after_saveorder($created_user,$created_order);
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
                //$landing_page->save_user('Distributor');
                if($landing_page->has_errors()) { $landing_page->display_errors(); }
                else {
                    $landing_page->save_user('Customer');
                    //save oreder
                    $landing_page->save_order();

                    //~ db_query("update users set account_type='Distributor' where userID=".$landing_page->session['created_user']);
                    //~ ^Removed this, we should not be changing to Distributor - Jon 2017.04.06
                    if ($landing_page->has_errors()) {
                        $landing_page->display_errors();
                    } else {
                          $created_user = $landing_page->session['created_user'];
                        $created_order = $landing_page->session['created_order'];

                        $landing_page->clear_session();
                        unset($landing_page->session['lead_id']);
                        unset($landing_page->session['created_user']);
                        unset($landing_page->session['created_order']);

                        after_saveorder($created_user,$created_order);
                    }
                }
            }else{
                $landing_page->display_errors();
            }

        }

    }


}

$landing_page->refill_form();


function after_saveorder($userid,$orderid){


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




    util_redirect('opportunity-success?order='.$orderid);

}


$ship_str = '';
$ship_arr = array();




if(count($price_arr)){
    foreach ($price_arr as $val){
        $res = db_query("SELECT * FROM `shipping_by_price` WHERE `min` <".($val)." && `max` >=".($val));
        $ship_arr[$val] = 6;
        while($res && $row = db_fetch_assoc($res)) {
            $ship_arr[$val] = $row['cost'];
        }
    }

    $ship_str = json_encode($ship_arr);

}



?>

<script>
    $(function(){

        var is_chk = '<?php echo $is_chk; ?>';
        var pid = '<?php echo $pid; ?>';
        var shipstr = '<?php echo $ship_str;?>';
        var shiparr = JSON.parse(shipstr);

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

        $('#bill_region').change(function(){
            if(typeof($('#pid').val()) !='undefined' && $('#pid').val() != ''){
                productsel($('#bynowoppo'+$('#pid').val()));
            }
        });

        $('#ship_region').change(function(){
            if(typeof($('#pid').val()) !='undefined' && $('#pid').val() != ''){
                productsel($('#bynowoppo'+$('#pid').val()));
            }
        });

        $('.fieldcommon').blur(function(){
            if(typeof($('#pid').val()) !='undefined' && $('#pid').val() != ''){
                productsel($('#bynowoppo'+$('#pid').val()));
            }
        });

    });


    function productsel(obj){
        var shipstr = '<?php echo $ship_str;?>';
        var shiparr = JSON.parse(shipstr);


        var shipping = 0;
        var tax = 0;
        var stock_item_id = $(obj).attr('stock_item_id');
        var ptitle = $(obj).attr('ptitle');
        var price = $(obj).attr('price');
        price = parseFloat(price);
        shipping = shiparr[price];

        shipping = parseFloat(shipping);
        tax = parseFloat(tax);


        $('#pid').val(stock_item_id);
        $('.bynowoppo').text('buy now');
        $('.bynowoppo').removeClass('bynowopponew');
        $(obj).addClass('bynowopponew');
        $(obj).text('selected');



        var totalamnt = parseFloat(price+shipping+tax);
        //alert(totalamnt);

        $('#ptitle').text(ptitle);
        $('#pprice').text(price.toFixed(2));
        $('#pamnt').text(price.toFixed(2));
        $('#psubtotal').text((price).toFixed(2));
        $('#pship').text(shipping.toFixed(2));
        $('#shipping').val(shipping.toFixed(2));
        $('#ptax').text(tax.toFixed(2));
        $('#ptotal').text(totalamnt.toFixed(2));
        $('#pquan').text(1);

        $.post('gettax',{stock_item_id:stock_item_id,bill_first_name:$('#bill_first_name').val(),bill_last_name:$('#bill_last_name').val(),bill_address_line_1:$('#bill_address_line_1').val(),bill_city:$('#bill_city').val(),bill_region:$('#bill_region').val(),bill_country:$('#bill_country').val(),bill_postal_code:$('#bill_postal_code').val(),email:$('#bill_email').val(),phone:$('#bill_phone').val(),ship_first_name:$('#ship_first_name').val(),ship_last_name:$('#ship_last_name').val(),ship_address_line_1:$('#ship_address_line_1').val(),ship_city:$('#ship_city').val(),ship_region:$('#ship_region').val(),ship_country:$('#ship_country').val(),ship_postal_code:$('#ship_postal_code').val()},function(res){

            tax = res;
            tax = parseFloat(tax);
            var totalamnt = price+shipping+tax;
            $('#ptax').text(tax.toFixed(2));
            $('#ptotal').text(totalamnt.toFixed(2));

        });
        /*$.post('gettax',{stock_item_id:1,bill_first_name:$('#bill_first_name').val(),bill_last_name:$('#bill_last_name').val(),bill_address_line_1:$('#bill_address_line_1').val(),bill_city:$('#bill_city').val(),bill_region:$('#bill_region').val(),bill_country:$('#bill_country').val(),bill_postal_code:$('#bill_postal_code').val(),email:$('#bill_email').val(),phone:$('#bill_phone').val(),ship_first_name:$('#ship_first_name').val(),ship_last_name:$('#ship_last_name').val(),ship_address_line_1:$('#ship_address_line_1').val(),ship_city:$('#ship_city').val(),ship_region:$('#ship_region').val(),ship_country:$('#ship_country').val(),ship_postal_code:$('#ship_postal_code').val()},function(res1){

            tax = res1+tax;
            //alert(res1);
            tax = parseFloat(tax);
            var totalamnt = price+shipping+tax+29.55;
            $('#ptax').text(tax.toFixed(2));
            $('#ptotal').text(totalamnt.toFixed(2));

        });*/

    }


    function bill_ship_same(is_chk) {

        if(is_chk == 1){
            $('.shipbillcls').hide();
        }else {

            $('#ship_first_name').val($('#bill_first_name').val());
            $('#ship_last_name').val($('#bill_last_name').val());
            $('#ship_address_line_1').val($('#bill_address_line_1').val());
            $('#ship_address_line_2').val($('#bill_address_line_2').val());
            $('#ship_city').val($('#bill_city').val());
            $('#ship_region').val($('#bill_region').val());
            $('#ship_email').val($('#bill_email').val());
            $('#ship_phone').val($('#bill_phone').val());
            $('#ship_postal_code').val($('#bill_postal_code').val());


            $('.shipbillcls').show();
        }
    }

    $(window).load(function () {
        $('#bynowoppo11').click();
    });

</script>

<div class="container-fluid innerpagetitleblock text-center">
<div class="innerpagetitleblockwrapper">
    <h1>PREQUALIFY<!--PREQUALIFY--></h1>
</div>
</div>

<div class="container-fluid">
    <div class="container containerwrapper">
        <div class="row">
            <div style="display: none; width: 100%; float: none;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 aboutusblock1 aboutusblock1newtext">
                <div style="display: none; width: 100%; float: none;" class="newpdiv">

                    <span class="titleinfo">Vivacity’s Mission Statement</span>
                    <img src="system/themes/vivacity_frontend/images/aboutusimg1.jpg" class="img-responsive aboutimg1" style="display: block; margin: 0 auto; width: 100%; float:none;">

                    <span class="titleheadlinenew2" style="display: block;"><!--Get Pre-Qualified For Promotership!-->Enrollment</span>
                    We are spreading our message of vibrancy and wellness to every corner of the globe. Our goal is to develop high-character, global-market leaders and to encourage individual personal-development. You can live a life full of vitality, inspiration, and health.<br/> <br/>
                    <img src="system/themes/vivacity_frontend/images/aboutusimg2.jpg" class="img-responsive aboutimg3new">
                    <strong>Welcome to an Incredible Adventure</strong><br/><br/>

                    <strong style="color: #d90253;">Financial Freedom | Independent Business Ownership | Lifetime Security</strong><br/><br/>

                    How would you like an opportunity to promote all-natural wellness products with scientifically proven and lasting positive-results? Discover what it’s like to earn with our generous compensation plan, industry-changing, all-natural wellness products, and sure track to living life on your own terms.<br/>
                    <br/>

                    <i>Vivacity’s</i> success is deeply-rooted and intertwined with our most important business partner - You! We value the significance in watching you achieve financial freedom and personal success. Vivacity offers you an incredible opportunity to transform your life and accomplish your goals.
                    We put as much care and dedication into our promoters as we do our premium quality product line. Financial freedom, personal development, and a new take on life can all be yours when partner with Vivacity. Join the Vivacity team and let’s build our future together.

                    <br/><br/>

                    <strong>What we do for our Promoters is unmatched.</strong><br/><br/>
                    The core principles that make a company successful begin with the values held by every member of their team. If you seek a company that will train, encourage, and provide you with real guidance toward success in network marketing, then we are thrilled you discovered us!
                    <br/><br/>

                    Keys to our method of supporting promoter success:<br/><br/>



                <ul>
                    <li>Incredible field training on how to run your new Vivacity business
                    </li>

                    <li>Tireless dedication to your personal and financial development
                    </li>


                    <li>Strength in supporting independent leadership and presentation skills
                    </li>
                    <li> Sales and marketing education, created in-house, specific to the Vivacity product line
                    </li>

                    <li>Social media training giving you the ability to track an endless number of quality leads
                    </li>
                </ul>


                <strong>Our Super Saturday trainings are valuable and effective!</strong><br/><br/>
                Every weekend we hold our Super Saturday events to help ramp up your new business. Think of this offer as attending a University for Success in Network Marketing. We hold three major webinars covering over 3 hours of incredibly valuable information. Our main trainer, Beto Paredes, provides the most innovative programs and advice available.

                <br/><br/>

                <ul>

                    <li>The Vivacity Opportunity Webinar - Invite all your potential team members and customers to learn about the Vivacity business opportunity.</li>
                    <li>The Vivacity Core Training Webinar - This webinar is close to 2 hours long with a 20-minute break in the middle. Every single weekend we roll up our sleeves, right along with you, and cover the most important aspects for running your Vivacity business.</li>
                    <li>The Vivacity Leadership Webinar - Developing yourself as a leader and presenter, while building the confidence to create an incredible organization, is absolutely possible. Our leadership training focuses on your personal development and leadership skills.</li>
                </ul>


                Don’t Miss Out on the Opportunity of Your Life!<br/><br/>

                We have an amazing opportunity for you! When you buy one of our incredible Program packages you can accept a position as a Vivacity Promoter FOR FREE! Don’t miss out on this opportunity. Choose a Program that best fits your needs and get ready to begin a new way of life with Vivacity! With our all-natural, effective products, a powerful compensation plan, and a full network of support for our Promoters, you will quickly build your path to success. Start your future with Vivacity today!




                </div>
            </div>


            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 aboutusblock1">
                <span class="titleinfo2">CHOOSE ANY OF OUR DYNAMIC DUO PACKS AND PREQUALIFY TO BECOME A PROMOTER<!--CHOOSE ANY OF OUR PROGRAMS AND PRE-QUALIFY TO BECOME A PROMOTER--></span>



        </div>
    </div>
</div>

</div>



<div class="container containerwrapper containerwrapper_opportunity ">


    <div class="opportunity_block_con">
        <div class="opp_div1">
            <img src="<?php echo @$product_arr[0]['img_path']?>" >
        </div>
        <div class="opp_div2">
            <h2><?php echo @$product_arr[0]['title']?></h2>
            <h3><!--Live Well:  BALANCE Your Being.  BALANCE is the ultimate supplement formulated for amplified body-equilibrium. BALANCE works as an essential element to experiencing The Shift by assisting the necessary preparation of body and mind for experiencing lasting success in our VioPhaze and VioFit programs. That is why we established it as the foundational first-half of our 4-step total wellness philosophy!-->Live well and be balanced. Balance is the ultimate supplement formulated for amplified body-equilibrium. Balance works as an essential element to experiencing The Shift by assisting the necessary preparation of body and mind for experiencing lasting success in our VioPhaze and VioPhotonics programs. That is why it has become one of our most popular Duo Packs!

                <br/><br/>
               <!-- Now you can benefit more from your body’s natural 24-hour cycle.-->Now you can benefit more from your body’s natural 24-hour cycle with the powerful combination of Awaken and Recover.
            </h3>
        </div>
        <div class="opp_div3">
            <h4>Benefits</h4>
           <!-- <h5>Decrease pain from*</h5>-->
            <ul>
                <!--<li>Osteoarthritis</li>
                <li>Rheumatoid Arthritis</li>
                <li>Bursitis</li>
                <li>Gout</li>
                <li>Abnormal Posture</li>
                <li>Strains and Sprains</li>
                <li>Repetitive Motion</li>-->

               <!-- <li>Blood Sugar Control</li>
                <li>Increased Energy</li>
                <li>Boost Immune System</li>
                <li>Antioxidant</li>
                <li>Improved Attention Span</li>
                <li> Promotes Hormonal Balance</li>
                <li> Reduces Free Radicals</li>
                <li> Improves Sleep</li>-->
                <!--<li> Increases Benefits Sleep</li>
                <li>Increases Serotonin</li>-->
                <li>Controls Blood Sugar</li>
                <li>Increases Energy</li>
                <li>Boosts Immune System</li>
                <li>Provides Antioxidants</li>
                <li>Improves Attention Span</li>
                <li>Promotes Hormonal Balance</li>
                <li>Reduces Free Radicals</li>
                <li>Improves Sleep</li>
            </ul>
        </div>
        <div class="opp_div4">
            <img src="system/themes/vivacity_frontend/images/ad_opportunity_img1.jpg" class="opportunity_proimg">
            <a href="javascript:void(0)" class="opplink_pro1 hide" onclick="js:$('#programopModal1').modal('show');">More Info</a>
            <a href="javascript:void(0)" style="margin-right: 24%"  align="center" class="opplink_pro2 bynowoppo bynowopponew" id="bynowoppo<?php echo @$product_arr[0]['stock_item_id']?>" stock_item_id="<?php echo @$product_arr[0]['stock_item_id']?>" ptitle="<?php echo @$product_arr[0]['title']?>" price="<?php echo @$product_arr[0]['price']?>">buy now</a>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>

        <div class="modal fade programopModablock" id="programopModal1" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo @$product_arr[0]['title']?></h4>
                    </div>
                    <div class="modal-body">
                        <img src="system/themes/vivacity_frontend/images/logo-vivacity.png" class="opportunity_logoimg">

                        <h3>
                            <img src="<?php echo @$product_arr[0]['img_path']?>" >

                            <!--Live Well:  BALANCE Your Being.  BALANCE is the ultimate supplement formulated for amplified body-equilibrium. BALANCE works as an essential element to experiencing The Shift by assisting the necessary preparation of body and mind for experiencing lasting success in our VioPhaze and VioFit programs. That is why we established it as the foundational first-half of our 4-step total wellness philosophy!-->Live well and be balanced. Balance is the ultimate supplement formulated for amplified body-equilibrium. Balance works as an essential element to experiencing The Shift by assisting the necessary preparation of body and mind for experiencing lasting success in our VioPhaze and VioPhotonics programs. That is why it has become one of our most popular Duo Packs!
                            <br/><br/>
                            <!--Now you can benefit more from your body’s natural 24-hour cycle.-->Now you can benefit more from your body’s natural 24-hour cycle with the powerful combination of Awaken and Recover.
                        </h3>
                        <div class="clearfix"></div>

                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="opportunity_block_con">
        <div class="opp_div1">
            <img src="<?php echo @$product_arr[1]['img_path']?>" >
        </div>
        <div class="opp_div2">
            <h2><?php echo @$product_arr[1]['title']?></h2>
            <h3><!--The SYNERGY program has been formulated with your mental and emotional health in mind. SYNERGY is the ultimate upgrade to BALANCE for those who wish to get the most from the <i>24-Hour Core Dynamics System</i>. Say goodbye to stress and distraction with SYNERGY! One of the biggest reliefs Americans seek today is a reprieve from the effects of stress.-->The Synergy Dynamic Duo Pack has been formulated with your mental and physical health in mind. Synergy is the ultimate Dynamic Duo Pack for those who wish to get the most out of their day. Say goodbye to a foggy mind stressed by distraction and fatigue with Synergy! One of the biggest benefits Synergy provides is a clear and focused mind for a new you.<br />
                <br />
                Synergy combines Awaken with Acuity to provide the perfect balance of a healthy body, with a strong and clear mind.<!--SYNERGY combines BALANCE with ACUITY and SERENITY to provide the perfect balance of body, mind and emotions.--> <!--SYNERGY and aids BALANCE in helping your reach homeostasis. Feel clear and relaxed with SYNERGY.--></h3>
        </div>
        <div class="opp_div3">
            <h4>Benefits</h4>
            <!--<h5>Decrease pain from*</h5>-->
            <ul>
               <!-- <li>Osteoarthritis</li>
                <li>Rheumatoid Arthritis</li>
                <li>Bursitis</li>
                <li>Gout</li>
                <li>Abnormal Posture</li>
                <li>Strains and Sprains</li>
                <li>Repetitive Motion</li>-->
                <!--<li>Increases Clarity</li>
                <li>Promotes Focus</li>
                <li>Aids in Learning</li>
                <li>Increases Memory</li>
                <li>Increases Wellbeing</li>
                <li> Promotes Calm</li>
                <li>Relieves Stress</li>
                <li> Lowers Cortisol Levels</li>-->
                <li>Increases Clarity</li>
                <li>Promotes Focus</li>
                <li>Aids in Learning</li>
                <li>Increases Memory Function</li>
                <li>Improves Attention Span</li>
                <li>Provides Antioxidants</li>
                <li>Promotes Weight Loss</li>
                <li>Increases Energy</li>
            </ul>
        </div>
        <div class="opp_div4">
            <img src="system/themes/vivacity_frontend/images/ad_opportunity_img2.jpg" class="opportunity_proimg">
            <a href="javascript:void(0)" class="opplink_pro1 hide" onclick="js:$('#programopModal2').modal('show');">More Info</a>
            <a href="javascript:void(0)" style="margin-right: 24%" class="opplink_pro2 bynowoppo" id="bynowoppo<?php echo @$product_arr[1]['stock_item_id']?>" stock_item_id="<?php echo @$product_arr[1]['stock_item_id']?>" ptitle="<?php echo @$product_arr[1]['title']?>" price="<?php echo @$product_arr[1]['price']?>">buy now</a>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>

        <div class="modal fade programopModablock" id="programopModal2" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo @$product_arr[1]['title']?></h4>
                    </div>
                    <div class="modal-body">
                        <img src="system/themes/vivacity_frontend/images/logo-vivacity.png" class="opportunity_logoimg">

                        <h3> <img src="<?php echo @$product_arr[1]['img_path']?>" > The Synergy Dynamic Duo Pack has been formulated with your mental and physical health in mind. Synergy is the ultimate Dynamic Duo Pack for those who wish to get the most out of their day. Say goodbye to a foggy mind stressed by distraction and fatigue with Synergy! One of the biggest benefits Synergy provides is a clear and focused mind for a new you.<!--The SYNERGY program has been formulated with your mental and emotional health in mind. SYNERGY is the ultimate upgrade to BALANCE for those who wish to get the most from the <i>24-Hour Core Dynamics System</i>. Say goodbye to stress and distraction with SYNERGY! One of the biggest reliefs Americans seek today is a reprieve from the effects of stress.--><br />
                            <br />
                            Synergy combines Awaken with Acuity to provide the perfect balance of a healthy body, with a strong and clear mind.<!--SYNERGY combines BALANCE with ACUITY and SERENITY to provide the perfect balance of body, mind and emotions. SYNERGY and aids BALANCE in helping your reach homeostasis. Feel clear and relaxed with SYNERGY.--></h3>
                        <div class="clearfix"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="opportunity_block_con">
        <div class="opp_div1">
            <img src="<?php echo @$product_arr[2]['img_path']?>" >
        </div>
        <div class="opp_div2">
            <h2><?php echo @$product_arr[2]['title']?></h2>
            <h3>Vibrancy springboards you from great health to incredible vitality. Elevate your health to new heights! Regain the energy and mobility of your youth and amplify your athletic ability. Imagine if you could store the fountain of youth in a bottle and take it with you wherever you go!<!--VIBRANCY springboards you from great health to incredible vitality. Elevate your health to new heights! Uplift your wellbeing. Imagine if you could store the fountain of youth in a bottle and take it with you wherever you go!--><br />
                <br />

                <!--By combining the beneficial features of BALANCE (the foundation of our <i>24-Hour Core Dynamics System</i>), along with the powerful energy boost sparked by IGNITE EFC, and the additional benefits of REPLENISH…-->By combining the beneficial features of Mobility along with the powerful energy boost sparked by Ignite EFC, you will feel like a new you.</h3>
        </div>
        <div class="opp_div3">
            <h4>Benefits</h4>
           <!-- <h5>Decrease pain from*</h5>-->
            <ul>
                <!--<li>Osteoarthritis</li>
                <li>Rheumatoid Arthritis</li>
                <li>Bursitis</li>
                <li>Gout</li>
                <li>Abnormal Posture</li>
                <li>Strains and Sprains</li>
                <li>Repetitive Motion</li>-->
                <li>Increases Focus</li>
                <li>Promotes Mental Clarity</li>
                <li>Provides Lasting Energy</li>
                <li>Increases Memory Function</li>
                <li>Improves Attention Span</li>
                <li>Decreases Inflammation</li>
                <li>Promotes Joint Health</li>
                <li>Increases Energy</li>
                <!--<li> Enhances Mood</li>
                <li> Beautifies Skin</li>
                <li>Increases Total Body Health</li>
                <li>Reinvigorates Cells</li>
                <li>Promotes Cellular Healing</li>-->


            </ul>
        </div>
        <div class="opp_div4">
            <img src="system/themes/vivacity_frontend/images/ad_opportunity_img3.jpg" class="opportunity_proimg">
            <a href="javascript:void(0)" class="opplink_pro1 hide" onclick="js:$('#programopModal3').modal('show');">More Info</a>
            <a href="javascript:void(0)" style="margin-right: 24%" class="opplink_pro2 bynowoppo" id="bynowoppo<?php echo @$product_arr[2]['stock_item_id']?>" stock_item_id="<?php echo @$product_arr[2]['stock_item_id']?>" ptitle="<?php echo @$product_arr[2]['title']?>" price="<?php echo @$product_arr[2]['price']?>">buy now</a>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>

        <div class="modal fade programopModablock" id="programopModal3" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo @$product_arr[2]['title']?></h4>
                    </div>
                    <div class="modal-body">
                        <img src="system/themes/vivacity_frontend/images/logo-vivacity.png" class="opportunity_logoimg">

                        <h3>  <img src="<?php echo @$product_arr[2]['img_path']?>" > <!--VIBRANCY springboards you from great health to incredible vitality. Elevate your health to new heights! Uplift your wellbeing. Imagine if you could store the fountain of youth in a bottle and take it with you wherever you go!-->Vibrancy springboards you from great health to incredible vitality. Elevate your health to new heights! Regain the energy and mobility of your youth and amplify your athletic ability. Imagine if you could store the fountain of youth in a bottle and take it with you wherever you go!<br />
                            <br />

                           <!-- By combining the beneficial features of BALANCE (the foundation of our <i>24-Hour Core Dynamics System</i>), along with the powerful energy boost sparked by IGNITE EFC, and the additional benefits of REPLENISH…-->By combining the beneficial features of Mobility along with the powerful energy boost sparked by Ignite EFC, you will feel like a new you.</h3>

                   <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="opportunity_block_con">
        <div class="opp_div1">
            <img src="<?php echo @$product_arr[3]['img_path']?>" >
        </div>
        <div class="opp_div2">
            <h2><?php echo @$product_arr[3]['title']?></h2>
            <h3><!--VITALITY is the premier package for your body’s maintenance. When combined with BALANCE, VITALITY is fortified with <i>Vivacity’s 24-Hour Core Dynamics System</i>. Reach beyond your physical limits with VITALITY. VITALITY rids the body of sugar and removes inflammation. Upgrade to the VITALITY program and we know you will feel great! Your body will thank us for VITALITY.-->Vitality is the premier package for your body’s maintenance. When our incredible Awaken and Acuity products are combined your vital health is fortified and enhanced. Reach beyond your physical limits with Vitality! Vitality rids the body of toxins and decreases inflammation. Upgrade to a new and improved you with the Vitality Dynamic Duo Pack! Your body will thank you.<br />
                <br />

                <!--VITALITY aids in digestion, making your internal ecosystem work as it should.  Increase your performance and boost your energy with VITALITY.-->Vitality aids in the body to increase energy and promote healthy movement. Increase your performance and boost your energy with Vitality.</h3>
        </div>
        <div class="opp_div3">
            <h4>Benefits</h4>
           <!-- <h5>Decrease pain from*</h5>-->
            <ul>
               <!-- <li>Osteoarthritis</li>
                <li>Rheumatoid Arthritis</li>
                <li>Bursitis</li>
                <li>Gout</li>
                <li>Abnormal Posture</li>
                <li>Strains and Sprains</li>
                <li>Repetitive Motion</li>-->

               <!-- <li>Aids in Weight Loss</li>
                <li>Balances Blood Sugar</li>
                <li> Increase Insulin Production</li>
                <li>Reduce Cravings</li>
                <li>Reduces Inflammation</li>
                <li>Decreases Swelling</li>
                <li> Neutralizes Free Radicals</li>
                <li>Reduces Pain</li>-->
               <!-- <li>Soothes Arthritis</li>-->
                <li>Increases Energy</li>
                <li>Reduces Inflammation</li>
                <li>Provides Lasting Energy</li>
                <li>Neutralizes Free Radicals</li>
                <li>Fortifies Health</li>
                <li>Decreases Swelling</li>
                <li>Promotes Joint Health</li>
                <li>Provides Antioxidants</li>
            </ul>
        </div>
        <div class="opp_div4">
            <img src="system/themes/vivacity_frontend/images/ad_opportunity_img4.jpg" class="opportunity_proimg">
            <a href="javascript:void(0)" class="opplink_pro1 hide" onclick="js:$('#programopModal4').modal('show');">More Info</a>
            <a href="javascript:void(0)" style="margin-right: 24%" class="opplink_pro2 bynowoppo" id="bynowoppo<?php echo @$product_arr[3]['stock_item_id']?>" stock_item_id="<?php echo @$product_arr[3]['stock_item_id']?>" ptitle="<?php echo @$product_arr[3]['title']?>" price="<?php echo @$product_arr[3]['price']?>">buy now</a>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>

        <div class="modal fade programopModablock" id="programopModal4" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo @$product_arr[3]['title']?></h4>
                    </div>
                    <div class="modal-body">

                        <img src="system/themes/vivacity_frontend/images/logo-vivacity.png" class="opportunity_logoimg">

                        <h3> <img src="<?php echo @$product_arr[3]['img_path']?>" > <!--VITALITY is the premier package for your body’s maintenance. When combined with BALANCE, VITALITY is fortified with <i>Vivacity’s 24-Hour Core Dynamics System</i>. Reach beyond your physical limits with VITALITY. VITALITY rids the body of sugar and removes inflammation. Upgrade to the VITALITY program and we know you will feel great! Your body will thank us for VITALITY.-->Vitality is the premier package for your body’s maintenance. When our incredible Awaken and Acuity products are combined your vital health is fortified and enhanced. Reach beyond your physical limits with Vitality! Vitality rids the body of toxins and decreases inflammation. Upgrade to a new and improved you with the Vitality Dynamic Duo Pack! Your body will thank you.<br />
                            <br />

                            <!--VITALITY aids in digestion, making your internal ecosystem work as it should.  Increase your performance and boost your energy with VITALITY.-->Vitality aids in the body to increase energy and promote healthy movement. Increase your performance and boost your energy with Vitality.</h3>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>





</div>




<div class="checkoutblockwrapper checkoutblockwrappernew">

    <div class="container-fluid checkoutblock1">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 checkoutfromblock">
            <form name="landing_page" id="landing_page_chk" action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="form-inline">

                <input type="hidden" name="bill_country" id="bill_country" value="US">
                <input type="hidden" name="ship_country" id="ship_country" value="US">

                <input name="pid[]" type="hidden" value="" id="pid">
                <!--<input name="pid[]" type="hidden" value="1" id="pid1">-->
                <input name="shipping" type="hidden" value="0" id="shipping">



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
        <label for="company">Company </label>
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
    <div class="form-group">
        <label for="password"> Confirm Password  <span>*</span></label>
        <input type="password"  class="form-control" name="retype_password" id="retype_password"  />
    </div>


    <?php
}
?>

                <div class="clearfix"></div>






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
                    <textarea class="form-control fieldcommon" name="bill_address_line_1" id="bill_address_line_1"></textarea>
                </div>



                <div class="form-group">
                    <label for="bill_address_line_2">Address 2</label>
                    <textarea class="form-control" name="bill_address_line_2" id="bill_address_line_2"></textarea>
                </div>
                <div class="clearfix"></div>
                <div class="form-group">
                    <label for="bill_city">City <span>*</span></label>
                    <input type="text"  class="form-control fieldcommon"  name="bill_city" id="bill_city" />
                </div>


                <div class="form-group">
                    <label for="bill_state">State<span>*</span></label>
                    <?php $landing_page->draw_region_select('bill_'); ?>
                </div>

                <div class="form-group">
                    <label for="bill_postal_code">Zip Code<span>*</span></label>
                    <input type="text"  class="form-control fieldcommon" name="bill_postal_code" id="bill_postal_code"  />
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
                <div class="hrlinenew"></div>
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
                    <textarea class="form-control fieldcommon" name="ship_address_line_1" id="ship_address_line_1"></textarea>
                </div>
                <div class="form-group shipbillcls">
                    <label for="ship_address_line_2">Address 2</label>
                    <textarea class="form-control" name="ship_address_line_2" id="ship_address_line_2"></textarea>
                </div>
                <div class="clearfix"></div>
                <div class="form-group shipbillcls">
                    <label for="ship_city">City <span>*</span></label>
                    <input type="text"  class="form-control fieldcommon"  name="ship_city" id="ship_city" />
                </div>


                <div class="form-group shipbillcls">
                    <label for="ship_state">State<span>*</span></label>
                    <?php $landing_page->draw_region_select('ship_'); ?>
                </div>

                <div class="form-group shipbillcls">
                    <label for="ship_postal_code">Zip Code<span>*</span></label>
                    <input type="text"  class="form-control fieldcommon" name="ship_postal_code" id="ship_postal_code"  />
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
                            <!--<tr>
                                <td><span id="ptitle">Membership</span></td>
                                <td>$<span id="pprice">29.95</span></td>
                                <td><span id="pquan">1</span></td>
                                <td>$<span id="pamnt">29.95</span></td>
                            </tr>-->
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


                <h2>PAYMENT INFORMATION</h2>

                <div class="form-group singlecolumn">
                    <div class="paymentmode">
                        <?php echo get_cc_radio('card_type');?>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 tripplecolumn">
                        <label for="city">Name On Card</label>
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



                <!--<h2>TERMS & CONDITIONS</h2>

                <div class="form-group singlecolumn tctext hide">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vestibulum lobortis metus vel vulputate. Maecenas imperdiet purus a velit egestas egestas. Nullam in velit vitae massa dapibus dignissim lacinia id urna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis id quam aliquet, porttitor magna at, mattis augue. Vestibulum pellentesque aliquet fermentum. Nulla facilisi. Pellentesque at fermentum metus. Integer pulvinar massa in ligula pulvinar, molestie mattis ligula gravida. Vivamus ut sem a nulla fringilla dictum ut eu tellus. Mauris non arcu facilisis, dignissim enim vitae, pellentesque sem.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vestibulum lobortis metus vel vulputate. Maecenas imperdiet purus a velit egestas egestas. Nullam in velit vitae massa dapibus dignissim lacinia id urna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis id quam aliquet, porttitor magna at, mattis augue. Vestibulum pellentesque aliquet fermentum. Nulla facilisi. Pellentesque at fermentum metus. Integer pulvinar massa in ligula pulvinar, molestie mattis ligula gravida. Vivamus ut sem a nulla fringilla dictum ut eu tellus. Mauris non arcu facilisis, dignissim enim vitae, pellentesque sem.</p>
                </div>-->
                <div class="clearfix"></div>
                <div class=" singlecolumn">
                    <h5 style="float: none; margin: 0px 0 0 0; padding:0px; font-weight: normal;">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="iagreetoit" name="check_terms" value="1" />
                                <span style="color:#000000;">I agree to the</span> <br/>
                                <ul class="list-inline text-center tnc" style="color:#000000;margin-top: 20px">

                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#myModaltermsf">Terms of Use</a></li>
                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#myModalrefundandreturnsf">Refunds and Returns</a></li>
                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#myModalPrivacyPolicyf">Privacy Policy</a></li>

                                </ul>
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
