<?php

global $AI;


if(!isset($_SESSION['awaken-and-revive-promoter']['form_data'])){

    if(isset($AI->MODS_INDEX['google_ad'])){
        require_once( ai_cascadepath( 'includes/modules/google_ad/includes/class.te_google_ad.php' ) );

        $te_google_ad = new C_te_google_ad();

        $te_google_ad->show_google_ad_value_traffic(3,util_rep_id());
    }
}


require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));

$landing_page = new C_landing_pages('awaken-and-revive-promoter');
$landing_page->next_step = 'awaken-and-revive-promoter-checkout';
$landing_page->next_step_send_ses_key = true;
$landing_page->css_error_class = 'lp_error';

$landing_page->pp_create_campaign = true;


$landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
$landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
$landing_page->add_validator('address', 'is_length', 3,'Invalid Address');
$landing_page->add_validator('city', 'is_length', 3,'Invalid City');
$landing_page->add_validator('state', 'is_length',1,'Invalid State');
$landing_page->add_validator('zip', 'is_length',1,'Invalid Zip Code');
$landing_page->add_validator('phone', 'is_phone','','Invalid Phone Number');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');



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

<div class="container-fluid topleftbg">

    <div class="container-fluid toprightbg">

        <div class="container AFF_topblock">


            <div class="row">
                 <div class="logo_div"><img src="system/themes/awaken_and_revive/images/landing1_logo.png"></div>
                <div class="toptext1"> The vital essence of<br/>
                    an inspired life</div>

                <div class="clearfix"></div>
            </div>

            <div class="top_textpart1">

                <h2>LOSE WEIGHT <span>&</span> KEEP IT OFF FOR LIFE!  </h2>

                    <img src="system/themes/awaken_and_revive/images/landing1_top_text1.png">
              </div>


            <div class="row">
                <div class="top_leftpart">
                    <img src="system/themes/awaken_and_revive/images/landing1_top_text4.png" class="righttext1">

                    <img src="system/themes/awaken_and_revive/images/topblockimg12.png" class="topblockimg12">

                    <div class="topblockimg12_text">
                        <ul>
                            <li>Increase Energy</li>
                            <li>Balances Blood Sugar</li>
                            <li>Appetite Suppressant</li>
                            <li>Antioxidant</li>
                            <li>Boots Immunity</li>
                            <li>Balance Hormones</li>
                        </ul>
                    </div>
                    <!--<div class="top_listcon">
                        <span></span>                <span></span>                  <span></span>
                         <span></span>               <span></span>                <span></span>
                    </div>-->

                    <img src="system/themes/awaken_and_revive/images/topgirlimg.png" class="topgirlimg">
                    <img src="system/themes/awaken_and_revive/images/topproductbox.png" class="topproductbox">

                    <img src="system/themes/awaken_and_revive/images/topimg3.png" class="topimg3">

                    <img src="system/themes/awaken_and_revive/images/topimg4.png" class="topimg4">


                    <div class="topleft_arrow_text">
                        <img src="system/themes/awaken_and_revive/images/topimg2.png" class="topimg2">
                        <div class="arrowmaintext">
                        <h2> Get The Wellness Package NOW!</h2>
                        <img src="system/themes/awaken_and_revive/images/landing1_top_text5.png" class="arrowtext5">

                        <h4>100% all-natural ingredients</h4>
                        </div>

                        <div class="clearfix"></div>

                    </div>
                    <img src="system/themes/awaken_and_revive/images/topimg1.png" class="topimg1">
                    <img src="system/themes/awaken_and_revive/images/topimg5.png" class="topimg5">
                </div>

                <div class="top_rightpart">
                    <h3>We Cracked the Code </h3>
                    <h3> to Permanent Weight Loss!</h3>

                    <img src="system/themes/awaken_and_revive/images/landing1_top_text2.png" class="lefttext1">
                    <img src="system/themes/awaken_and_revive/images/landing1_top_text3.png" class="lefttext2">

                    <div class="top_formwrapper">
                        <img src="system/themes/awaken_and_revive/images/landing1_top_formarrow.png" class="formarrow">
                    <h2>Get Started<br/>Now!</h2>

                        <h4>Please fill out the form below for<br/>more information</h4>

                        <form name="landing_page" id="landing_page" action="<?=$_SERVER['REQUEST_URI']?>" method="post">

                        <input type="hidden" name="country" value="US">

                        <input type="text" name="first_name" placeholder="First Name" class="form-control">
                        <input type="text" name="last_name" placeholder="Last Name" class="form-control">
                        <textarea name="address" class="form-control2" placeholder=" Address"></textarea>
                        <input type="text" name="city" placeholder="City" class="form-control">
                        <?php $landing_page->draw_region_select('',true,'US','state','form-control'); ?>
                        <input type="text" name="zip" placeholder="Zip Code" class="form-control">
                        <input type="text" name="phone" placeholder="Telephone Number" class="form-control">
                        <input type="email" name="email" placeholder="Email Address" class="form-control">
                        <!--<input type="text" name="company" placeholder="Business Neme" class="form-control">-->
                        <!--<textarea name="admin_notes" class="form-control2" placeholder=" Comments"></textarea>-->
                        <input type="submit" class="topsubbtn" value="Rush my order">

                        </form>

                    </div>



                </div>


                <div class="clearfix"></div>
                </div>





        </div>



    </div>


</div>


<div class="container-fluid aff_block2">

  <div class="container affblock2wrapper">
      <div class="affblock2_heading">
       <h2>THE <span>24-HOUR</span> CORE DYNAMICS SYSTEM</h2>

          <h3> An amazing introductory price! Experience what Balance can do for you.</h3>
      </div>
          <img src="system/themes/awaken_and_revive/images/block_2img3.png" class="block_2img3">



          <div class="block2_textcon">
             <h2>Upgrade to a Healthier Lifestyle!</h2>
              <img src="system/themes/awaken_and_revive/images/block2_text1.png" class="block2_text1">

              <h3>Make permanent change with Balance, the essential program to Vivacity’s 24-Hour Core Dynamics System. Balance provides the cutting-edge, all-natural supplements your body craves, day and night. But great health takes more than great supplementation. Our amazing VioShift System is now included for FREE with all purchases of Balance! Take the first step to total wellness with Balance by adding <br/>the VioShift System and fitness to your wellness routine. Feel good and fall in love with your body with Vivacity!</h3>

              <h4>Progressive systems easily increase your levels of nutrition and mental clarity!<br/><br/>

                  Lose weight. Maintain great health. Balance your body.</h4>

              <div class="block2devider"></div>


              <h5>How Do You REALLY Lose Weight?</h5>

              <img src="system/themes/awaken_and_revive/images/block2_text2.png" class="block2_text2">

              <h6>No more yo-yo’s! Permanent weight loss starts with our groundbreaking supplements. Balance brings attention to the internal clock and rhythm of your body's natural, 24-hour cycle. For results that last, get educated on the lifestyle and eating changes that need to happen with Vivacity's VioShift System.</h6>


          </div>


          <div class="block2_right_con">


              <img src="system/themes/awaken_and_revive/images/block_2img2.png" class="block_2img2">
              <img src="system/themes/awaken_and_revive/images/block_2img4.png" class="block_2img4">
              <img src="system/themes/awaken_and_revive/images/block2_arrow1.png" class="block2_arrow1">

              <a href="javascript:void(0)" class="block2btn getmybottle">rush my order</a>


          </div>
         <div class="clearfix"></div>




      </div>


  </div>


<div class="container-fluid aff_block3">

    <div class="container affblock3wrapper">

        <div class="affblock3_heading">
            <h2>The <span>Awaken</span> complete daytime metabolic formula</h2>

            <h3>Increased energy and enhanced mental clarity for a new you.</h3>


        </div>


        <img src="system/themes/awaken_and_revive/images/block3_img1.png" class="block3_img1">
        <img src="system/themes/awaken_and_revive/images/block3_img3.png" class="block3_img3">

        <div class="block3_textwrapper">


            <h2>True weight loss stems from consistent and small changes made to your supplemental, nutritional, and overall wellness lifestyle. Give your body the right amount of <br/>each ingredient needed to maintain a steady burn all day. As part of our <br/><span>24-Hour Core Dynamics</span> System, <strong class="textbold">Awaken</strong> keeps your body and mind <br/>active and alert while providing the daytime supplementation needed <br/>to experience <strong class="textbold">the Shift</strong>.</h2>

            <h3>Green Coffee Bean, Rhodiola Rosea, Hoodia,Garcinia Cambogia,<br/>African Mango Seed Extract</h3>

            <img src="system/themes/awaken_and_revive/images/block3_text1.png" class="block3_text1">

        </div>

        <img src="system/themes/awaken_and_revive/images/block3_img2.png" class="block3_img2">

        <a href="javascript:void(0)" class="block3btn">GET THE 24-HOUR CORE DYNAMICS<br/>
            SYSTEM RIGHT AWAY!</a>

        </div>
</div>

<div class="container-fluid aff_block4">

    <div class="container affblock4wrapper">


        <div class="affblock4_heading">AWAKEN: 100% all-natural ingredients</div>


        <div class="affblock4_con_wrapper">

            <div class="blok4_text1">
                <h2>Cacao</h2>

                <img src="system/themes/awaken_and_revive/images/block4_text1.png" class="block4_text1">
                <img src="system/themes/awaken_and_revive/images/block4_img1.png" class="block4_img1">
                <img src="system/themes/awaken_and_revive/images/block4_img1_sml.png" class="block4_img1_sml">
                <h3>Converts fatty acids into increased energy. Stimulates serotonin, acts as an appetite suppressant, and enhances the benefits from cannabinoid spectrum whole foods supplements.</h3>

            </div>

            <div class="blok4_text2">
                <h2>Panax Gingseng</h2>
                <img src="system/themes/awaken_and_revive/images/block4_img2left.png" class="block4_img2left">
                <img src="system/themes/awaken_and_revive/images/block4_text2.png" class="block4_text2">
                <img src="system/themes/awaken_and_revive/images/block4_img2.png" class="block4_img2">
                <h3>Increases energy and<br>stamina and gives the<br>immune system a boost.<br>Improves<br>
                    circulation.</h3>

            </div>

            <div class="clearfix"></div>

            <div class="blok4_text4">
                <h2>Cordyceps</h2>

                <img src="system/themes/awaken_and_revive/images/block4_text4.png" class="block4_text4">
                <img src="system/themes/awaken_and_revive/images/block4_img4.png" class="block4_img4">
                <h3>Fights free radicals, infections, <br>and inflammation. Reduces<br>
                    symptoms of respiratory<br>
                    disorders, coughs, and<br>
                    colds, and has<br>
                    anti-aging<br>
                    benefits.</h3>

            </div>
            <div class="blok4_text5">
                <h2>Capsaican</h2>

                <img src="system/themes/awaken_and_revive/images/block4_text5.png" class="block4_text5">
                <img src="system/themes/awaken_and_revive/images/block4_img5.png" class="block4_img5">


                <h3>Stimulates endothelial<br/>
                    nitric oxide synthase (eNOS)<br/>
                    activity, thereby, creating<br/>
                    enhanced energy.  </h3>

            </div>
            <div class="blok4_text6">
                <h2>DMG</h2>

                <img src="system/themes/awaken_and_revive/images/block4_text6.png" class="block4_text6">
                <img src="system/themes/awaken_and_revive/images/block4_img6.png" class="block4_img6">

                <h3>Enhances aerobic efficiency, physical stamina, mental clarity, and alertness. Increases vigor, energy, and cellular
                    vitality.</h3>

            </div>

            <div class="clearfix"></div>

            <a href="javascript:void(0)" class="block4btn getmybottle">rush my order</a>

        </div>



        </div>

    </div>



<div class="container-fluid aff_block5">

    <div class="container affblock5wrapper">

        <div class="affblock5_heading">
            <h2>WAKE UP COMPLETELY <span>REFRESHED WITH RECOVER</span></h2>

            <h3> Assist weight loss with better sleep and heightened vitality!</h3>


        </div>

        <div class=" affblock5main_wrapper">

        <div class="block5_textwrapper">


            <h2>Did you know that sleep deprivation causes cortisol to add inches to your waist line? Great sleep is the secret to immediate weight loss and maintenance. Without proper sleep, your body cannot control your appetite. Vivacity’s nighttime supplement, Recover, provides you with an incredible night’s sleep and enough energy to jump out of bed each morning. Wake up refreshed and enthusiastic to face the challenges of a new day!</h2>

            <h3>Aloe Vera, Zeolite, Melatonin, Collagen, <br/>Raspberry Ketones,  5 HTP, Crystalloids Electrolytes</h3>

            <img src="system/themes/awaken_and_revive/images/block5_text.png" class="block5_text">

        </div>

        <img src="system/themes/awaken_and_revive/images/block5_img1.png" class="block5_img1">

        <a href="javascript:void(0)" class="block5btn">GET THE 24-HOUR CORE DYNAMICS<br/>
            SYSTEM RIGHT AWAY!</a>
        </div>
    </div>
</div>



<div class="container-fluid aff_block6">

    <div class="container affblock6wrapper">


        <div class="affblock6_heading">RECOVER: 100% all-natural ingredients</div>


        <div class="affblock6_con_wrapper">

            <div class="blok6_text1">
                <h2>Aloe Vera</h2>

                <img src="system/themes/awaken_and_revive/images/block6_text1.png" class="block6_text1">
                <img src="system/themes/awaken_and_revive/images/block6_img1.png" class="block6_img1">
                <h3>Promotes a gentle detoxification while boosting the body’s natural immunity. Reduces inflammation and boosts the body’s natural ability to resist illness.</h3>

            </div>


            <div class="blok6_text2">
                <h2>Zeolite </h2>
                <img src="system/themes/awaken_and_revive/images/block6_text2.png" class="block6_text2">
                <img src="system/themes/awaken_and_revive/images/block6_img2.png" class="block6_img2">
                <h3>
                    <label class="h3text4">Removes toxins, free radicals, and heavy metals from the body. Boosts the immune system
                        without side effects. This mineral balances the body’s pH. Foreign cells cannot grow in a balanced pH environment.</label>
                </h3>
            </div>


            <div class="blok6_text3">
                <h2>Melatonin </h2>

                <img src="system/themes/awaken_and_revive/images/block6_text3.png" class="block6_text3">
                <img src="system/themes/awaken_and_revive/images/block6_img3.png" class="block6_img3">
                <h3>
                    Prolongs the REM sleep cycle.
                    Anti-aging effects, antioxidant,
                    promotes hormonal balance,
                    and helps relieve
                    headaches.

                </h3>
                 <div class="clearfix"></div>
            </div>
            <!--<div class="blok6_text4">
                <div class="blok6_text4_wrapper">
                <h2>Collagen </h2>

                <img src="system/themes/awaken_and_revive/images/block6_text4.png" class="block6_text4">

                <h3>
                    acts as building blocks for skin, bones, teeth, cartilage, tendons, and other connective tissue. It helps reduce wrinkles, can decrease the symptoms of arthritis and has anti-aging
                    properties.

                </h3>
                <img src="system/themes/awaken_and_revive/images/block6_img5.png" class="block6_img5">


            </div>
            </div>-->

            <div class="blok6_text5_6_wrapper">
            <div class="blok6_text5">
                <h2>Raspberry Ketones </h2>

                <img src="system/themes/awaken_and_revive/images/block6_text5.png" class="block6_text5">
                <img src="system/themes/awaken_and_revive/images/block6_img4.png" class="block6_img4">

                <h3>
                    An amazing antioxidant that regulates glucose and breaks down body fat for increased weight loss.

                </h3>

            </div>
            <!--<div class="blok6_text6">
                <h2>Crystalloids Electrolytes </h2>

                <img src="system/themes/awaken_and_revive/images/block6_text7.png" class="block6_text7">
                <img src="system/themes/awaken_and_revive/images/block6_img6.png" class="block6_img6">

                <h3>
                    are carriers and make the ingredients in <span>RE-VIVE</span> more effective by helping
                    to transport them more efficiently to cells.

                </h3>

            </div>-->

            </div>


            <div class="blok6_text7">
                <h2>5 HTP </h2>

                <img src="system/themes/awaken_and_revive/images/block6_text6.png" class="block6_text6">
                <img src="system/themes/awaken_and_revive/images/block6_img7.png" class="block6_img7">

                <h3>
                    Increases levels of Serotonin for better sleep and a <br>healthier appetite. Aids in weight loss, relieves
                    <br>migraines, reduces anxiety/depression, and helps <br>eliminate addictive <br>behaviors</h3>


            </div>

            <a href="javascript:void(0)" class="block6btn getmybottle">rush my order</a>

        </div>



    </div>

</div>



<!--<div class="container-fluid aff_block7">

    <div class="container affblock7wrapper">
        <div class="affblock7_heading">
            <h2><span> Real Science</span> Real Results </h2>

            <h3>  Testimonials from Satisfied Users</h3>
        </div>


        <div class=" affblock7_body_wrapper">

            <div class="block7_conleft">

                <h3>" i lost 12lbs in 30 days.
                    the results are awesome"</h3>



                <h4>
                    <img src="system/themes/awaken_and_revive/images/block7_text1.png" class="block7_text1">
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.

                    <span>REVA J AGE 33</span>

                    <img src="system/themes/awaken_and_revive/images/block7_img1.png" class="block7_img1">
                </h4>

                <div class="clearfix"></div>




                <div class="block7devider"></div>


                <h3>"effectively reduces
                    my desire to
                    nack and munch"</h3>



                <h4>
                    <img src="system/themes/awaken_and_revive/images/block7_text2.png" class="block7_text2">
                    Lorem Ipsum is simply dummy text of the
                    printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.

                    <span>REVA J AGE 31</span>

                    <img src="system/themes/awaken_and_revive/images/block7_img2.png" class="block7_img2">
                </h4>

                <div class="clearfix"></div>



                <img src="system/themes/awaken_and_revive/images/block7_arrow.png" class="block7_arrow">
            </div>

            <div class="block7_conright">


                <h4>"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."

                <br/>
                    <span>EARINA AGE 52</span>
                </h4>

                <img src="system/themes/awaken_and_revive/images/block7_arrow2.png" class="block7_arrow2">

                <img src="system/themes/awaken_and_revive/images/block7_img6.png" class="block7_img6">
                <img src="system/themes/awaken_and_revive/images/block7_img3.png" class="block7_img3">
                <img src="system/themes/awaken_and_revive/images/block7_img4.png" class="block7_img4">
                <img src="system/themes/awaken_and_revive/images/block7_img5.png" class="block7_img5">

                </div>

            <div class="clearfix"></div>


            <a href="javascript:void(0)" class="block7btn getmybottle">rush my order</a>
            </div>


        <div class="clearfix"></div>




    </div>


</div>-->

<div class="container-fluid aff_block10">
    <div class="container affblock10wrapper">
        <div class="aff_block10_heading">
            <h2>VIOSHIFT : <span>KNOWLEDGE IS POWER</span></h2>
        </div>
        <div class="aff_block10main_wrapper">
            <div class="block10_textwrapper">
                <h2>For lasting results with any of the Vivacity product lines, a total approach to wellness is required. Vivacity’s VioShift System expedites the experience of The Shift and guides you along the path of ultimate health and wellness. We believe so wholeheartedly in this approach, that when you purchase any product from Vivacity, you get instant access to all our VioShift programs! <span class="brbottom"></span>VioShift was developed as a progressive, 4/4 system and is designed to <br/>advance the levels of nutrition and mindfulness for any caliber of <br/>participant. VioPhaze provides education on proper nutrition and how <br/>to successfully re-train your dietary habits. VioPhotonics  equips you <br/>with the mental tools required to harness your inner strength and change the way <br/>you think to change your life. <span class="brbottom"></span>Embrace the mind-body connection and experience total health <br/>and wellbeing! When combined with any Vivacity product program, <br/>the VioShift System is the premier method for obtaining maximum <br/>evels of vitality. Clear your mind. Nurture your body. Be vivacious. <br/>Live with Vivacity.<span class="brbottom"></span></h2>
                <div class="clearfix"></div>
            </div>
        </div>
        <img src="system/themes/awaken_and_revive/images/cbdbg11.png" class="block10_imgtext1">
    </div>
</div>

<div class="container-fluid aff_block11">
    <div class="container affblock11wrapper">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 affblock11wrapperleft">
                <div class="affblock11_heading">
                    <h2>VIOPHAZE</h2>
                </div>
                <div class="aff_block11main_wrapper">
                    <div class="block11_textwrapper">

                        <div class="topblockimg8_text">
                            <ul>
                                <li>IMPROVE YOUR DIET</li>
                                <li>LOSE WEIGHT</li>
                                <li>NURTURE YOUR BODY</li>
                                <li>NUTRITIONAL EDUCATION</li>
                            </ul>
                        </div>
                        <img src="system/themes/awaken_and_revive/images/block8_text2left.png" class="block8_text2left">
                        <img src="system/themes/awaken_and_revive/images/cbdbg8left.png" class="block8_text2smlleft">

                        <!-- <div class="topblockimg11_text">
                             <ul>
                                 <li>IMPROVE YOUR DIET</li>
                                 <li>LOSE WEIGHT</li>
                                 <li>NURTURE YOUR BODY</li>
                                 <li>NUTRITIONAL EDUCATION</li>
                             </ul>
                         </div>-->
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 affblock11wrapperright">
                <div class="affblock11_heading">
                    <h2>VIOPHOTONICS</h2>
                </div>
                <div class="aff_block11main_wrapper">
                    <div class="block11_textwrapper">
                        <!-- <div class="topblockimg11_text">
                             <ul>
                                 <li>GAIN ENLIGHTENMENT</li>
                                 <li>EXPERIENCE MENTAL CLARITY</li>
                                 <li>CONNECT WITH THE FIELD</li>
                                 <li>STRENGTHEN WILLPOWER</li>
                             </ul>
                         </div>-->
                        <div class="topblockimg8_text">
                            <ul>
                                <li>GAIN ENLIGHTENMENT</li>
                                <li>EXPERIENCE MENTAL CLARITY</li>
                                <li>CONNECT WITH THE FIELD</li>
                                <li>STRENGTHEN WILLPOWER</li>
                            </ul>
                        </div>
                        <img src="system/themes/awaken_and_revive/images/block8_text2right.png" class="block8_text2right">
                        <img src="system/themes/awaken_and_revive/images/cbdbg8.png" class="block8_text2smlright">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--<div class="container-fluid awakencbdblock7">

    <div class="container block7wrapper">

        <h1>
            <img src="system/themes/awaken_cbd/images/block7_text1.png" class="block7_text1">
            <span>VIOSHIFT : KNOWLEDGE IS POWER</span>
        </h1>

        <div class="block7_text_con">
            <h4>For lasting results with any of the Vivacity product lines, a total approach to wellness is required. Vivacity’s VioShift System expedites the experience of The Shift and guides you along the path of ultimate health and wellness. We believe so wholeheartedly in this approach, that when you purchase any product from Vivacity, you get instant access to all our VioShift programs! <span class="brbottom"></span>VioShift was developed as a progressive, 4/4 system and is designed to advance the levels of nutrition and mindfulness for any caliber of participant. VioPhaze provides education on proper nutrition and how to successfully re-train your dietary habits. VioPhotonics <br/>equips you with the mental tools required to harness your inner strength <br/>and change the way you think to change your life. <span class="brbottom"></span>Embrace the mind-body connection and experience total health <br/>and wellbeing! When combined with any Vivacity product program, <br/>the VioShift System is the premier method for obtaining maximum <br/>levels of vitality. Clear your mind. Nurture your body. <br/>Be vivacious. Live with Vivacity.<span class="brbottom"></span></h4>
        </div>

        <div class="clearfix"></div>

    </div>

</div>


<div class="container-fluid awakencbdblock8">
    <div class="container block8wrapper">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 block8_con1">
                <h1>
                    <img src="system/themes/awaken_cbd/images/block8_text1.png" class="block8_text1">
                    <span>VIOPHAZE</span>
                </h1>
                <div class="topblockimg8_text">
                    <ul>
                        <li>IMPROVE YOUR DIET</li>
                        <li>LOSE WEIGHT</li>
                        <li>NURTURE YOUR BODY</li>
                        <li>NUTRITIONAL EDUCATION</li>
                    </ul>
                </div>
                <img src="system/themes/awaken_cbd/images/block8_text2left.png" class="block8_text2left">
            </div>

            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 block8_con2">
                <h1>
                    <img src="system/themes/awaken_cbd/images/block8_text2.png" class="block8_text2">
                    <span>VIOPHOTONICS</span>
                </h1>
                <div class="topblockimg8_text">
                    <ul>
                        <li>GAIN ENLIGHTENMENT</li>
                        <li>EXPERIENCE MENTAL CLARITY</li>
                        <li>CONNECT WITH THE FIELD</li>
                        <li>STRENGTHEN WILLPOWER</li>
                    </ul>
                </div>
                <img src="system/themes/awaken_cbd/images/block8_text2right.png" class="block8_text2right">
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
</div>-->



<div class="container-fluid aff_block8">

    <div class="container affblock8wrapper">

        <div class="affblock8_heading">
            <h2>WE ARE <span>OFFERING</span></h2>

            <h3>A 100% 30-Day, No-Risk, MONEY-BACK GUARANTEE!</h3>


        </div>

        <div class="block8_textwrapper">


            <div class="block8_textcon1">

                <h2><label class="labelclass">Our products are manufactured with the highest quality, all-natural ingredients at the clinically proven effective amounts. All Vivacity products are produced at highly respected <span>cGMP</span> certification (Current Good Manufacturing Practices) facilities.</label>



                    <label>Ensure satisfaction by trying our products completely risk-free! Vivacity products come with a <span>100% MONEY-BACK GUARANTEE</span>. Experience the difference that premium quality, science-backed formulations<br/>can make in your own health.</label></h2>



            </div>


            <img src="system/themes/awaken_and_revive/images/block8_img1.png" class="block8_img1">
            <img src="system/themes/awaken_and_revive/images/block8_img4.png" class="block8_img4">
            <img src="system/themes/awaken_and_revive/images/block8_img3.png" class="block8_img3">

            <a href="javascript:void(0)" class="block8btn block8_block8btn">RUSH MY ORDER TODAY!</a>
        </div>



        </div>

    </div>



<div class="container-fluid aff_block9">

    <div class="container affblock9wrapper">


        <h2>Fall in love with your body!</h2>
        <h3>LOSE WEIGHT <span>&</span> KEEP IT OFF FOR LIFE!</h3>
        <img src="system/themes/awaken_and_revive/images/block9_img1.png" class="block9_img1">

<h4>Maintain and Manage your Sexy   with this incredible new system!</h4>

        <img src="system/themes/awaken_and_revive/images/block9_img6.png" class="block9_img6">

        <img src="system/themes/awaken_and_revive/images/block9_img2.png" class="block9_img2">
        <img src="system/themes/awaken_and_revive/images/block9_img4.png" class="block9_img4">
        <img src="system/themes/awaken_and_revive/images/block9_img3.png" class="block9_img3">
        <img src="system/themes/awaken_and_revive/images/block9_img7.png" class="block9_img7">




        <div class="bottom9_arrow_text">
            <img src="system/themes/awaken_and_revive/images/block9_img5.png" class="block9_img5">
            <div class="arrowmaintext9">
                <h2> Get The Wellness Package NOW!</h2>
                <img src="system/themes/awaken_and_revive/images/landing1_top_text5.png" class="arrowtext5">

                <h6>100% all-natural ingredients</h6>
            </div>

            <div class="clearfix"></div>

        </div>
        <a href="javascript:void(0)" class="block9btn getmybottle">rush my order</a>

        </div>

    </div>