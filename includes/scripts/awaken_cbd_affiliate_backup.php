<?php
global $AI;
require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));

$landing_page = new C_landing_pages('xcellerate-affiliate');
$landing_page->next_step = 'xcellerate-affiliate-checkout';

$landing_page->next_step_send_ses_key = true;
$landing_page->css_error_class = 'lp_error';

$landing_page->pp_create_campaign = true;

$landing_page->add_validator('first_name', 'is_length', 3,'Invalid First Name');
$landing_page->add_validator('last_name', 'is_length', 3,'Invalid Last Name');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');
$landing_page->add_validator('phone', 'is_phone','','Invalid Phone Number');
$landing_page->add_validator('zip', 'is_length',1,'Invalid Zip Code');
/*
$landing_page->add_validator('address', 'is_length', 3,'Invalid Address');
$landing_page->add_validator('city', 'is_length', 3,'Invalid City');
$landing_page->add_validator('state', 'is_length',1,'Invalid State');
$landing_page->add_validator('zip', 'is_length',1,'Invalid Zip Code');
$landing_page->add_validator('phone', 'is_phone','','Invalid Phone Number');
$landing_page->add_validator('email', 'util_is_email','','Invalid Email Address');*/


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


<div class="container-fluid awakencbd_topblock">

    <div class="container awakencbd_topblockwrapper">

        <div class="cbd_top_left">

            <div class="toplogodiv"> <img src="system/themes/awaken_cbd/images/cbd_logo.png" class="toplogo"></div>

            <img src="system/themes/awaken_cbd/images/topblockimg1_new.png" class="topblockimg1">
          <!--  <img src="system/themes/awaken_cbd/images/topblockimg2.png" class="topblockimg2">-->

            <img src="system/themes/awaken_cbd/images/topblockimg3.png" class="topblockimg3">
            <img src="system/themes/awaken_cbd/images/topblockimg4.png" class="topblockimg4">
            <img src="system/themes/awaken_cbd/images/topblockimg9_new.png" class="topblockimg9">

            <div class="topblockimg9_text">
                <ul>
                   <!-- <li>Reduce Pain</li>
                    <li>Relieve Anxiety</li>
                    <li>Increase Health</li>
                    <li>Feel Amazing</li>-->

                    <li>Antioxidant</li>
                    <li>Anti-inflammatory</li>
                    <li>Overall Health Promoter</li>
                    <li> Promotes Emotional Wellbeing</li>
                    <li>  Relieve Pain</li>
                    <li>   Reduce Stress </li>
                    <li>    Increase Health </li>
                    <li>     Feel Amazing </li>

                </ul>

            </div>


            <img src="system/themes/awaken_cbd/images/xcellerate_new_toppro.png" class="topblockimg5">
         <!--   <img src="system/themes/awaken_cbd/images/topblockimg6.png" class="topblockimg6">-->

  <div class="top_arrow_div"> <img src="system/themes/awaken_cbd/images/topblockimg7.png" class="topblockimg7"></div>

            <div class="mobile_imagetext1"> RUSH MY Order now!</div>


        </div>

        <div class="cbd_top_right">

            <h2>Get Started Now!</h2>

            <h3>Please fill out the form<br/>
                below for more<br/>information</h3>

<div class="top_formwrappercbd">
<form name="landing_page" id="landing_page" action="<?=$_SERVER['REQUEST_URI']?>" method="post">

    <input type="hidden" name="country" value="US">

    <input type="text" name="first_name" placeholder="First Name" class="form-control">
    <input type="text" name="last_name" placeholder="Last Name" class="form-control">
    <!--<textarea name="address" class="form-control2" placeholder=" Address"></textarea>
    <input type="text" name="city" placeholder="City" class="form-control">
    --><?php /*$landing_page->draw_region_select('',true,'US','state','form-control'); */?>
    <input type="email" name="email" placeholder="Email Address" class="form-control">
    <input type="text" name="phone" placeholder="Phone Number" class="form-control">
    <input type="text" name="zip" placeholder="Zip Code" class="form-control">


    <!--<textarea name="admin_notes" class="form-control2" placeholder=" Comments"></textarea>-->
    <!--<input type="text" name="company" placeholder="Business Neme" class="form-control">-->
    <input type="submit" class="topsubbtn" value="Rush my order">

</form>
</div>

            <div class="formbottomimg"> <img src="system/themes/awaken_cbd/images/topblockimg11.png" class="topblockimg11"></div>


        </div>

        <div class="clearfix"></div>

    </div>
</div>

<div class="awakencbdblock2_bgwrapper">


    <div class="container-fluid awakencbdblock2">
        <div class="container block2wrapper">
            <h2><span>Xcellerate </span> Your Health !</h2>

            <h4>Xcellerate is the latest break-through in Cannabinoid Spectrum Whole Foods Supplement that is finally available in the US! This powerful supplement is an accelerant to any lifestyle health program or supplement and will put you on track for TOTAL health and wellbeing. Not feeling like yourself?<br>
                <br>


                Have you felt tired and in pain for what seems like years? Are you having trouble losing weight? Do you feel older beyond your years? Are you often anxious and have trouble concentrating? If any of these things sounds familiar, you are in the right place to finally feel better; to feel like the best you. </h4>

            <div class="block2_bottom_imgcon_wrapper">
                <div class=" block2_bottom_imgcon1">
                    <img src="system/themes/awaken_cbd/images/block1_img3.png" class="block1_img3">
                    <h6>Safe and Effective</h6>
                </div>

                <div class=" block2_bottom_imgcon1">
                    <img src="system/themes/awaken_cbd/images/block1_img4.png" class="block1_img4">
                    <h6>Clinically Validated</h6>
                </div>


                <div class=" block2_bottom_imgcon1">
                    <img src="system/themes/awaken_cbd/images/block1_img5.png" class="block1_img5">
                    <h6>100% All-Natural</h6>
                </div>



                <div class="clearfix"></div>
            </div>
        </div>

        <div class="block2_procon">
            <img src="system/themes/awaken_cbd/images/block1_img2.png" class="block2_proing">
            <a href="javascript:void(0)" class="block2_btn  getmybottle"> Get my Bottle</a>
        </div>

    </div>

<!--<div class="container-fluid awakencbdblock2">
    <div class="container block2wrapper">
        <h2><span>Not</span> Feeling Like Yourself ? </h2>

        <h4>Have you felt tired and in pain for what seems like years? Are you having trouble losing weight? Do you feel older beyond your years? Are you often anxious and have trouble concentrating? If any of these things sounds familiar, you are in the right place to finally feel better; to feel like the best you.<br>
            <br>


            Don't be discouraged by failed treatements you’ve tried in the past. You don’t know the true cause of what was wrong with your body. Our experts know that there are 8 important glands that keep you feeling happy, healthy, and balanced. You are not sick; your Endocannaboid System is out of balance. We can help you!  </h4>

<div class="block2_bottom_imgcon_wrapper">
        <div class=" block2_bottom_imgcon1">
            <img src="system/themes/awaken_cbd/images/block1_img3.png" class="block1_img3">
            <h6>Safe and Effective</h6>
        </div>

        <div class=" block2_bottom_imgcon1">
            <img src="system/themes/awaken_cbd/images/block1_img4.png" class="block1_img4">
            <h6>Clinically Validated</h6>
        </div>


        <div class=" block2_bottom_imgcon1">
            <img src="system/themes/awaken_cbd/images/block1_img5.png" class="block1_img5">
            <h6>non-psychoactive</h6>
        </div>



        <div class="clearfix"></div>
</div>
    </div>

    <div class="block2_procon">
        <img src="system/themes/awaken_cbd/images/block1_img2.png" class="block2_proing">
        <a href="javascript:void(0)" class="block2_btn  getmybottle"> Get my Bottle</a>
    </div>

</div>-->

    <div class="container-fluid awakencbdblock3">

        <div class="container block3wrapper">

            <h1>
                <img src="system/themes/awaken_cbd/images/block2_text1.png" class="block3_text1">
                <span>Benefits of AWAKEN cbd</span>
            </h1>

            <div class="block3_img_con">
                <img src="system/themes/awaken_cbd/images/block2_img1.png" class="block2_img1">
                <a href="javascript:void(0)" class="block3_btn getmybottle"> Get my Bottle</a>
            </div>

            <div class="block3_text_con">

                <h2><span>Accelerate</span> Your Health!</h2>

                <h4>Xcellerate is a top-of-the-line health supplement fortified with ground-breaking, scientifically-backed Cannabinoid Spectrum Whole Foods Supplement. Xcellerate fast-tracks the health benefits of the nutrients you consume by increasing your body’s ability to absorb more of the supplement.</h4>

                <h5>What Does Cannabinoid Whole Foods Supplement Do? </h5>

                <div class="block3_bottom_block">

                    <div class=" block3_bottom_imgcon1">
                        <img src="system/themes/awaken_cbd/images/block2_img2.png" class="block2_img2">
                        <h6>Reduce Inflammation</h6>
                    </div>


                    <div class=" block3_bottom_imgcon2">
                        <img src="system/themes/awaken_cbd/images/block2_img3.png" class="block2_img3">
                        <h6>Relieve Anxiety</h6>
                    </div>

                    <div class=" block3_bottom_imgcon3">
                        <img src="system/themes/awaken_cbd/images/block2_img4.png" class="block2_img4">
                        <h6>Stabilize Blood Sugar</h6>
                    </div>

                    <div class=" block3_bottom_imgcon4">
                        <img src="system/themes/awaken_cbd/images/block2_img5.png" class="block2_img5">
                        <h6>Reduce Nausea</h6>
                    </div>


                    <div class="clearfix"></div>
                </div>


            </div>






            <div class="clearfix"></div>




        </div>

    </div>

<!--<div class="container-fluid awakencbdblock3">

    <div class="container block3wrapper">

        <h1>
            <img src="system/themes/awaken_cbd/images/block2_text1.png" class="block3_text1">
        <span>Benefits of AWAKEN cbd</span>
        </h1>

        <div class="block3_img_con">
            <img src="system/themes/awaken_cbd/images/block2_img1.png" class="block2_img1">
            <a href="javascript:void(0)" class="block3_btn getmybottle"> Get my Bottle</a>
        </div>

        <div class="block3_text_con">

            <h2><span>AWAKEN CBD</span> has more benefits!</h2>

            <h4>Awaken CBD is our powerful Awaken supplement fortified with cannabidiol to enhance your health and balance your endocannaboid system. The world around us is toxic. We eat processed food and breathe polluted air. Awaken CBD not only has the benefits of our Awaken product, but the advantages CBD and all its plant power to help you become balanced and stay healthy.</h4>

            <h5>What Does Cannabidiol Do? </h5>

            <div class="block3_bottom_block">

                <div class=" block3_bottom_imgcon1">
                    <img src="system/themes/awaken_cbd/images/block2_img2.png" class="block2_img2">
                    <h6>Reduce Inflammation</h6>
                </div>


                <div class=" block3_bottom_imgcon2">
                    <img src="system/themes/awaken_cbd/images/block2_img3.png" class="block2_img3">
                    <h6>Relieve Anxiety</h6>
                </div>

                <div class=" block3_bottom_imgcon3">
                    <img src="system/themes/awaken_cbd/images/block2_img4.png" class="block2_img4">
                    <h6>Stabilize Blood Sugar</h6>
                </div>

                <div class=" block3_bottom_imgcon4">
                    <img src="system/themes/awaken_cbd/images/block2_img5.png" class="block2_img5">
                    <h6>Reduce Nausea</h6>
                </div>


                <div class="clearfix"></div>
            </div>


        </div>






        <div class="clearfix"></div>




    </div>

</div>-->

    <div class="container-fluid awakencbdblock4">

        <div class="container block4wrapper">

            <h1><img src="system/themes/awaken_cbd/images/block3_text1.png" class="block4_text1">
                <span>How Does Xcellerate Work?</span>

            </h1>

            <h4>Xcellerate can magnify your morning routine! Xcellerate contains a powerful probiotic to help your body absorb more nutrients. It is fortified with our exclusive Cannabinoid Spectrum Whole Foods Supplement that provides a wide range of health benefits and increases the effects of any other supplement in your regimen.</h4>

            <div class=" block4_conwrapper">
                <div class=" col-lg-4 col-md-12 col-sm-12 col-xs-12 block4_con1">
                    <div class="block2_imgdiv"><img src="system/themes/awaken_cbd/images/block3_img1.png"></div>
                    <h3>Buy Xcellerate Today!</h3>
                    <h6>You can buy Xcellerate here! We make it a simple, easy process that is 100% secure and satisfaction guaranteed.</h6>

                </div>

                <div class=" col-lg-4 col-md-12 col-sm-12 col-xs-12 block4_con2">
                    <div class="block2_imgdiv"><img src="system/themes/awaken_cbd/images/block3_img2.png"></div>
                    <h3>Follow the Program!</h3>
                    <h6>Check out our other amazing products in our 24-hour Core Dynamics System that work with Xcellerate to increase overall health and wellbeing.</h6>

                </div>

                <div class=" col-lg-4 col-md-12 col-sm-12 col-xs-12 block4_con3">
                    <div class="block2_imgdiv"><img src="system/themes/awaken_cbd/images/block3_img3.png"></div>
                    <h3>Enjoy the Benefits!</h3>
                    <h6>With regular use, you will notice an improvement in your energy throughout the day as well as reduced pain, anxiety, inflammation, and so much more!</h6>

                </div>

                <div class="clearfix"></div>


            </div>

        </div>

    </div>

<!--<div class="container-fluid awakencbdblock4">

    <div class="container block4wrapper">

        <h1><img src="system/themes/awaken_cbd/images/block3_text1.png" class="block4_text1">
        <span>How does AWAKEN CBD WORK?</span>

        </h1>

        <h4>Awaken CBD can magnify your morning routine! Awaken S-7 increases alertness
            without experiencing the inevitable crash that accompanies conventional morning
            wake-up stimulants. Awaken CBD combines the amazing benefits of Awaken S-7
            with all-natural cannabidiols to increase your overall health and wellbeing. </h4>

        <div class=" block4_conwrapper">
        <div class=" col-lg-4 col-md-12 col-sm-12 col-xs-12 block4_con1">
            <div class="block2_imgdiv"><img src="system/themes/awaken_cbd/images/block3_img1.png"></div>
            <h3>Buy Awaken CBD Online </h3>
            <h6>You can buy Awaken CBD here! We make it a simple, easy process that is 100% secure with a 100% satisfaction guarantee.</h6>

        </div>

        <div class=" col-lg-4 col-md-12 col-sm-12 col-xs-12 block4_con2">
            <div class="block2_imgdiv"><img src="system/themes/awaken_cbd/images/block3_img2.png"></div>
            <h3>Follow the Program  </h3>
            <h6>Check out our other amazing products in our 24 hour Core Dynamics program that work with Awaken CBD for overall health.</h6>

        </div>

        <div class=" col-lg-4 col-md-12 col-sm-12 col-xs-12 block4_con3">
            <div class="block2_imgdiv"><img src="system/themes/awaken_cbd/images/block3_img3.png"></div>
            <h3>Enjoy the Benefits  </h3>
            <h6>With regular use you will notice an improvement in your energy throughout the day as well as reduced pain, anxiety, inflammation, and so much more!</h6>

        </div>

        <div class="clearfix"></div>


    </div>

    </div>

</div>-->

</div>

<div class="container-fluid awakencbdblock5">



    <div class="container block5wrapper">

        <h1><img src="system/themes/awaken_cbd/images/block4_text1.png" class="block4_text1">

            <span>What Makes Xcellerate So Effective? </span>
        </h1>


        <div class=" block5_text_wrapper">


            <h4><span>Xcellerate </span> is a powerful new wellness supplement with ingredients that are proven by the latest in scientific research to make a real difference in total mind and body health. <span>Xcellerate</span> contains Cannabinoid Spectrum Whole Food Supplement, a single molecule that can do amazing things when connected to the rest of the cannabinoid spectrum. Combining full spectrum cannabinoids is the ultimate X-factor for enhancing and stabilizing your health. </h4>


            <div class="block5_left_list">
                <ul>

                    <li><span>100%</span> All-Natural Formula </li>
                    <li><span>Anti</span>-Stress </li>
                    <li><span>Anti</span>-Depressant </li>
                    <li><span>Pain</span>-Reliever </li>
                    <li><span>Anti</span>-Oxidant </li>
                </ul>


            </div>

            <h5><span>Xcellerate </span> is an effective Cannabinoid Spectrum Whole Foods Supplement that offers amazing benefits while being completely safe and legal in the United States. Order today to experience the amazing qualities of <span>Xcellerate </span>. </h5>

            <div class="block5_procon">
                <img src="system/themes/awaken_cbd/images/block5_img3.png" class="block5_proing">
                <a href="javascript:void(0)" class="block5_btn getmybottle"> Get my Bottle</a>
            </div>


            <div class="clearfix"></div>

        </div>



    </div>


</div>

<!--<div class="container-fluid awakencbdblock5">



    <div class="container block5wrapper">

        <h1><img src="system/themes/awaken_cbd/images/block4_text1.png" class="block4_text1">

        <span>WHAT MAKES AWAKEN CBD so successful? </span>
        </h1>


        <div class=" block5_text_wrapper">


            <h4><span>Awaken CBD</span> is an effective cannabidiol that offers these amazing benefits while
                being completely safe and legal in the United States. Order today to experience
                the amazing qualities of <span>Awaken CBD!</span> </h4>


            <div class="block5_left_list">
                <ul>

                    <li><span>100%</span> All-Natural Formula </li>
                    <li><span>Non</span>-Psychoactive </li>
                    <li><span>Anti</span>-Depressant </li>
                    <li><span>Anti</span>-Cancer </li>
                    <li><span>Anti</span>-Tumoral  </li>
                    <li><span>Anti</span>-Oxidant   </li>
                    <li><span>Anti</span>-Convulsant   </li>
                </ul>


            </div>

            <div class="block5_procon">
                <img src="system/themes/awaken_cbd/images/block5_img3.png" class="block5_proing">
                <a href="javascript:void(0)" class="block5_btn getmybottle"> Get my Bottle</a>
            </div>


            <div class="clearfix"></div>

            </div>



        </div>


    </div>-->

<div class="container-fluid awakencbdblock7">

    <div class="container block7wrapper">

        <h1>
            <img src="system/themes/awaken_cbd/images/block7_text1.png" class="block7_text1">
            <span><!--VIOSHIFT : KNOWLEDGE IS POWER-->VIOSHIFT – THE POWERFUL MIND AND BODY REVOLUTION</span>
        </h1>

        <h6>ENHANCE YOUR MIND, TRANSFORM YOUR BODY</h6>

        <div class="block7_text_con">
            <!--<h4>For lasting results with any of the Vivacity product lines, a total approach to wellness is required. Vivacity’s VioShift System expedites the experience of The Shift and guides you along the path of ultimate health and wellness. We believe so wholeheartedly in this approach, that when you purchase any product from Vivacity, you get instant access to all our VioShift programs! <span class="brbottom"></span>VioShift was developed as a progressive, 4/4 system and is designed to advance the levels of nutrition and mindfulness for any caliber of participant. VioPhaze provides education on proper nutrition and how to successfully <br/>re-train your dietary habits. VioPhotonics equips you with the mental <br/>tools required to harness your inner strength and change the way <br/>you think to change your life. <span class="brbottom"></span>Embrace the mind-body connection and experience total health <br/>and wellbeing! When combined with any Vivacity product program, <br/>the VioShift System is the premier method for obtaining maximum <br/>levels of vitality. Clear your mind. Nurture your body. <br/>Be vivacious. Live with Vivacity.<span class="brbottom"></span></h4>-->
<!--            <h4>For lasting results with any of the Vivacity product lines, a total approach to wellness is required. Vivacity’s <strong>VioShift</strong> System expedites the experience of <strong>The Shift</strong> and guides you along the path of ultimate health and wellness. We believe so wholeheartedly in this approach, that when you purchase any product from Vivacity, you get instant access to all our VioShift programs! <span class="brbottom"></span><strong>VioShift</strong> was developed as a progressive, 4/4 system and is designed to advance the levels of nutrition and mindfulness for any caliber of participant. <strong>VioPhaze</strong> provides education on proper nutrition and how to successfully re-train your dietary habits. <br/><strong>VioPhotonics</strong> equips you with the mental tools required to harness <br/>your inner strength and change the way you think to change <br/>your life. <span class="brbottom"></span>Embrace the mind-body connection and experience total health <br/>and wellbeing! When combined with any Vivacity product program, <br/>the <strong>VioShift</strong> System is the premier method for obtaining maximum <br/>evels of vitality. Clear your mind. Nurture your body. Be vivacious. <br/>Live with Vivacity.</h4>
-->

        <h4><p>Discover what you need to know to completely transform your  lifestyle and become the happy, vibrant, and successful person you want to be.<br><br></p>
            <p> Develop deep knowledge and receive effective trainings from <br>experts in the industry on how to manage your physical health through <br>nutrition and develop a powerful, positive mindset that will allow you to <br>achieve great success in an area of your life.<br><br></p>
            <p>Get instant access to both <strong>VioShift</strong> programs with any purchase of a <br><strong>Vivacity</strong> product!</p>
        </h4>

        </div>
        <img src="system/themes/awaken_cbd/images/cbdbg11.png" class="block11_imgtext2">
        <div class="clearfix"></div>

    </div>

</div>

<!--<div class="container-fluid awakencbdblock8">
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


<div class="container-fluid aff_block10">

</div>


<div class="container-fluid awakencbdblock6">

 <div class="container block6wrapper">

<div class="block6_div1">
    <img src="system/themes/awaken_cbd/images/cbd_logo.png" class="cbd_block6logo">
    <img src="system/themes/awaken_cbd/images/block6_img1.png" class="block6_img1">
    <img src="system/themes/awaken_cbd/images/block6_img2.png" class="block6_img2">

</div>

     <div class="block6_div2">
         <img src="system/themes/awaken_cbd/images/block6_img3.png" class="block6_img3">
         <a href="javascript:void(0)" class="block6_btn getmybottle"> Get my Bottle</a>
     </div>

     <div class="block6_div3">

         <img src="system/themes/awaken_cbd/images/block6_img4.png" class="block6_img4">
         <img src="system/themes/awaken_cbd/images/block6_img5.png" class="block6_img5">
         <img src="system/themes/awaken_cbd/images/block6_img6.png" class="block6_img6">
         <h2>100% all natural<br/>
             & organic</h2>

         <img src="system/themes/awaken_cbd/images/block6_img7.png" class="block6_img7">

     </div>

<div class="clearfix"></div>
        </div>

    </div>