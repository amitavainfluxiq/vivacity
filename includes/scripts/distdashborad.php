<?php
global $AI;
require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));
require_once(ai_cascadepath('includes/modules/mlmsignup/class.enrollment_lp.php'));

require_once(ai_cascadepath('includes/modules/genealogy/class.genealogy.php'));

$gene            = new C_genealogy( $AI->get_setting('structure_show_genealogy') ? C_genealogy::GENEALOGY_TREE : C_genealogy::ENROLLER_TREE );
$is_logged_in    = $AI->user->isLoggedIn();
$is_admin        = $AI->get_access_group_perm('Administrators');
$is_in_genealogy = $is_logged_in ? $gene->is_descendant(AI_STRUCTURE_NODE_ROOT, util_affiliate_id()) : false;

$userId = $AI->user->userID;
$emailid = $AI->user->email;



$res12 = db_query("SELECT * FROM `users` WHERE `userID` = ".$userId);

while($res12 && $row12 = db_fetch_assoc($res12)) {
    $accept_term = $row12['accept_term'];
}

if($accept_term == 0) {


    if (count($_POST)) {
        $userId = $AI->user->userID;
        $util_rep_id = 100;

        $res = db_query("SELECT * FROM `users` WHERE `userID` = " . $userId);

        while ($res && $row = db_fetch_assoc($res)) {
            $parent = $row['parent'];

            if ($parent) {
                $util_rep_id = $parent;
            }
        }

        // add user at geneology tree

        $gene = new C_genealogy(C_genealogy::GENEALOGY_TREE);
        try {
            $gene->insert_node($userId, $util_rep_id, null, 'stop', true);
        } catch (NodeAlreadyInTreeException $naite) {
            $data = $naite->get_data();
            if ($data['parent'] != $util_rep_id) {
                $gene->move_sub_tree($userId, $util_rep_id, 0);
            }
        }

        // add user at enrollment tree

        $gene = new C_genealogy(C_genealogy::ENROLLER_TREE);
        try {
            $gene->insert_node($userId, $util_rep_id, null, 'stop', true);
        } catch (NodeAlreadyInTreeException $naite) {
            $data = $naite->get_data();
            if ($data['parent'] != $util_rep_id) {
                $gene->move_sub_tree($userId, $util_rep_id, 0);
            }
        }


        db_query("UPDATE `users` SET `accept_term`=1 WHERE `userID` = " . $userId);


        $email_name = 'Accept terms';
        $send_to = $emailid;
        $send_from = 'iftekarkta@gmail.com';

        $vars = array();
        $vars['name'] = 'Samsuj Jaman';

        $defaults = array();
        $defaults['email_subject'] = 'Enrollment email';
        $defaults['email_msg'] = getmailbody($AI->user->username);

        $se = new C_system_emails($email_name);
        $se->set_from($send_from);
        $se->set_defaults_array($defaults);
        $se->set_vars_array($vars);
        $se->send($send_to);

        util_redirect('dashboard');

    }




    ?>

    <script>
        function validate() {
            if ($('#iagreetoit').is(':checked')) {
                return true;
            } else {
                alert('Please accept terms and conditions.');
                return false;
            }
        }
    </script>

    <div class="dl_content_block">

    <form action="" method="post" onsubmit="return validate()">
        <div>
            <h2>Terms & Conditions</h2>
            <textarea>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.

    It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).

    Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.

</textarea>
        </div>



          <!--  <input type="checkbox" name="iagreetoit" id="iagreetoit" value="Y"/>
            I agree to your Terms &amp; Conditions.
           -->



        <div class="ckbox2">
            <input type="checkbox" name="iagreetoit" id="iagreetoit" value="Y"/>
            I agree to your Terms &amp; Conditions.
        </div>

        <div>
            <input type="submit" value="Submit">
        </div>

    </form>
    </div>
    <?php
}




function getmailbody($username = '')
{
    return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Enrollment</title>
</head>

<body>

<table width="100%" border="0">
  <tr>
    <td align="center">
        <table width="600" border="0" style="font-family:Arial, Helvetica, sans-serif;">
  <tr>
    <td align="left" valign="middle" style="padding:15px; padding-bottom:0px;"><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/logo-enrollment.png"  alt="#" style="width:230px;"/></td>
    
    <td align="right" valign="middle" style="padding:15px; padding-bottom:0px; font-family:Arial, Helvetica, sans-serif; font-size:15px; color:#3c3c3b; line-height:20px; font-weight:bold;">Financial Freedom.<br />

Premium Quality Products.<br />
Generous Comp Plan.<br />
Be Your Own Boss Now!</td>
  </tr>
  
  <tr>
    <td colspan="2" align="center" valign="middle"><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/mailpagebanner1.jpg"  alt="#"/>
    
    </td>
    </tr>
    
    <tr>
    <td colspan="2" align="center" valign="middle" style="padding:15px 40px; font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:15px; color:#3c3c3b; line-height:20px;">
    
    
    We understand our sucess is deeply rooted and intertwined with our most important businesspartner - YOU! <br />
<br />


Lets live a life full of vitality, inspiration, and health. By join Vivacity, you’ve taking a steptowards <span style="text-transform:uppercase; color:#13a716;">the shift</span> towards permanent 
transformation in mind, body, and soul.  <br />
<br />


Congrats on taking the first step towards experiencing the vital essence of an inspired life.  
    
    </td>
    </tr>
    
    
    <tr>
    <td colspan="2" align="center" valign="middle" style="padding:25px;">
    
    
     <div style="background:#ec2e64; border-radius:5px; padding:15px;">
     <h1 style="font-family:Arial, Helvetica, sans-serif; font-size:36px; color:#ffffff; text-transform:uppercase; margin:0; padding:0;">A Total Wellness</h1>
 
   <h1 style="font-family:Arial, Helvetica, sans-serif; font-size:30px; color:#ffffff; text-transform:uppercase; margin:0; padding:0;">Philosophy with You in Mind</h1>

<h2 style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:normal; border-bottom:solid 1px #c2204e; margin:0; padding:15px 20px; line-height:24px;">Our programs are easy to follow, short in time, and simple to use. Whether 
you are a brand-new participant to the health 
industry or a seasoned athlete, Vivacity has a program level to 
effectively suit your needs. </h2>

<h3 style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#e6e6e6; margin:0; padding:12px 0 0 0;">Commit. Choose a package. Feel the results.<br />
Upgrade to your new, vital life now!</h3>
     
     </div>
    
    </td>
    </tr>
    
    <tr>
    <td colspan="2" align="center" valign="middle">
    
    
    
     <h1 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:45px; color:#51b517; text-transform:uppercase; margin:0; padding:8px 0 0 0;">Account Information</h1>

<h2 style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#5e5e5e; font-weight:normal; line-height:24px; margin:0; padding:10px 0 25px 0;">Full backoffice access will be available during our official launch date, <br />

JANUARY 23, 2016. <br />

You can still access the backend (limited view) now!  </h2>

<h3 style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#3c3c3b; margin:0; padding:0; font-weight:normal;">Username:   <span style="color:#2e7aec; padding-left:10px;">' . $username . '</span></h3>

<a href="http://www.vivacitygo.com/login?ai_bypass=true" style="display:block; margin:26px auto; width:120px; height:31px; background:#51b517; font-size:16px; color:#fff; text-align:center; text-transform:uppercase; font-weight:bold; line-height:33px; text-decoration:none;">Login Now</a>
     
   
    
    </td>
    </tr>
    
    <tr>
    <td colspan="2" align="center" valign="middle" style="background:#51b517; padding:10px 2px;">
    
    
    
     <h1 style="font-family:Arial, Helvetica, sans-serif; font-size:30px; color:#fff; text-transform:uppercase; margin:0; padding:0; font-weight:bold;">we\'re here to help!</h1>

<h2 style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#ffffff; font-weight:bold; margin:0; padding:5px 0;">If you run into any problems contact us and our team will be sure to take care of you. </h2>

<h3 style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#193906; line-height:18px; margin:0; padding:10px 0 0 0;">210 E. Tennessee Street Florence, AL 35630<br />

 info@makewaywellness.com<br />
Phone: 800.928.9401<br />
Fax: 615.861.8955<br /></h3>


     
   
    
    </td>
    </tr>
    <tr>
    <td colspan="2" align="center" valign="middle">
    
    
    
    <img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/logo-enrollment.png"  alt="#"  style="width:170px; display:block; margin:10px auto;"/>

     
   
    
    </td>
    </tr>
    
</table>

    
    
    </td>
  </tr>
</table>


</body>
</html>
';
}
?>