<?php

global $AI;

if(!isset($_SESSION['vitality_landing']['form_data'])){

    if(isset($AI->MODS_INDEX['google_ad'])){
        require_once( ai_cascadepath( 'includes/modules/google_ad/includes/class.te_google_ad.php' ) );

        $te_google_ad = new C_te_google_ad();

        $te_google_ad->show_google_ad_value_traffic(5,util_rep_id());
    }
}


require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));

$landing_page = new C_landing_pages('vitality_landing');
$landing_page->next_step = 'vitality_checkout';
$landing_page->next_step_send_ses_key = true;
$landing_page->css_error_class = 'lp_error';

$landing_page->pp_create_campaign = true;

/*
$landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
$landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
$landing_page->add_validator('address', 'is_length', 3,'Invalid Address');
$landing_page->add_validator('city', 'is_length', 3,'Invalid City');
$landing_page->add_validator('country', 'is_length', 3,'Invalid Ccountry');
$landing_page->add_validator('state', 'is_length',1,'Invalid State');
$landing_page->add_validator('zip', 'is_length',1,'Invalid Zip Code');
$landing_page->add_validator('phone', 'is_phone','','Invalid Phone Number');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');*/

$landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
$landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');
$landing_page->add_validator('phone', 'is_phone','','Invalid Phone Number');
$landing_page->add_validator('zip', 'is_length',1,'Invalid Zip Code');




if(util_is_POST()) {
    $landing_page->validate();
    if($landing_page->has_errors()) { $landing_page->display_errors(); }
    else {


        /*$landing_page->load_user_data_to_session();*/

        if($AI->user->is_logged_in()){
            $landing_page->pp_drip_opt_in();

            $landing_page->goto_next_step();
        }else{
            if($landing_page->save_lead($AI->get_setting('owner_id')))
            {
                // Subscribe them to the drip campaign
                //$landing_page->pp_drip_opt_in();

                $landing_page->goto_next_step();
            }
        }


    }
}
$landing_page->refill_form();

?>

<div class="container-fluid multi_bg_example_top">

<div class="container-fluid block_top_wrapper">

    <div class="container">

        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 block_top_wrapper_left"> <img src="system/themes/vitality_landing/images/logo.png"></div>
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 block_top_wrapper_right"> <div class="block_top_wrapper_right_div"><img src="system/themes/vitality_landing/images/topimg.jpg"> <span>Customer Service: <a href="tel:(000) 000-0000">(000) 000-0000</a></span></div>        <div class="clearfix"></div></div>


        <div class="clearfix"></div>
    </div>



</div>

<div class="container-fluid block1_con_wrapper">
   <div class="container-fluid topleftbg">
       <div class="container vtl_topblock">
        <div class="row">

                <div class="block1_div1">
                    <h2>RID YOUR BODY OF SUGARS AND<br/>
                        INFLAMMATION
                    </h2>

                    <h3>Vitality is the premier package for your body’s<br/>
                        maintenance.
                    </h3>
                </div>
                <img src="system/themes/vitality_landing/images/top_img1.png" class="vtltopimg1">
                <img src="system/themes/vitality_landing/images/top_img2.png" class="vtltopimg2">
                <img src="system/themes/vitality_landing/images/top_img3.png" class="vtltopimg3">
                <img src="system/themes/vitality_landing/images/top_img4.png" class="vtltopimg4">
                <div class="vtltopblockarrow">
                    <h2 data-text="Get The Package NOW!">Get The Package NOW!</h2>
                </div>
                <div class="topblock_list">
                    <ul>
                        <li><span>Aids in Weight Loss</span></li>
                        <li><span>Balances Blood Sugar</span></li>
                        <li><span>Reduce Cravings</span></li>
                        <li><span>Reduces Inflammation</span></li>
                        <li><span>Reduces Pain</span></li>

                    </ul>


                </div>


            <div class="top_rightpart">
                <div class="topformblock">
                    <div class="topformblock_wrapper">

                        <div class="formheading1">
                            <h1>GET STARTED NOW  !</h1>

                        </div>

                        <img src="system/themes/vitality_landing/images/topform_heading2.png" class="topform_heading2">


                        <h2>GET THE VITALITY<br/>PROGRAM NOW!</h2>
                        <h3>Tell Us Where To Send Your<br/>Program</h3>


                    <form name="landing_page" id="landing_page" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                        <input type="hidden" name="country" value="US">
                        <input type="text" name="first_name" placeholder="First Name" class="form-control">
                        <input type="text" name="last_name" placeholder="Last Name" class="form-control">
                        <input type="email" name="email" placeholder="Email Address" class="form-control">
                        <input type="text" name="phone" placeholder="Phone Number" class="form-control">
                        <input type="text" name="zip" placeholder="Zip Code" class="form-control">



                        <input type="submit" class="topsubbtn" value="Rush my order">
                    </form>

                </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


</div>