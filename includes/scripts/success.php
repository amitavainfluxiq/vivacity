<?php

global $AI;
require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));
$landing_page = new C_landing_pages('prelauch');

$orderid = $landing_page->session['created_order'];
$username = $landing_page->session['form_data']['username'];
$replcated_url = 'http://'.$username.'.vivacitygo.com';

$o_res = db_query("SELECT `o`.`userID`,`o`.`date_added`,`o`.`billing_addr`,`o`.`shipping_addr`,`o`.`shipping`,`o`.`tax`,`o`.`total`,`od`.* FROM `orders` `o` INNER JOIN `order_details` `od` ON `o`.`order_id` = `od`.`order_id` WHERE `o`.`order_id` = ".$orderid);

$total_amnt = 0.00;
$shipping = 0.00;
$tax = 0.00;
$subtotal = 0.00;

$product_html = '<table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tbody><tr>
                <th style="background:#069d1f;" width="2%" valign="middle" align="left">&nbsp;</th>
                <th style="background:#069d1f; font-size:14px; color:#fff; font-weight:normal; " width="47%" valign="middle" align="left">Item Description</th>
                <th style="background:#069d1f; font-size:14px; color:#fff; font-weight:normal; " width="5%" valign="middle" align="left"><img src="system/themes/prelaunch_lp/images/arrowimgupdate.png" alt="#"></th>
                <th style="background:#069d1f; font-size:14px; color:#fff; font-weight:normal; " width="9%" valign="middle" align="center"> Price</th>
                <th style="background:#069d1f; font-size:14px; color:#fff; font-weight:normal; " width="5%" valign="middle" align="left"><img src="system/themes/prelaunch_lp/images/arrowimgupdate.png" alt="#"></th>
                <th style="background:#069d1f; font-size:14px; color:#fff; font-weight:normal; " width="8%" valign="middle" align="center">Qty. </th>
                <th style="background:#069d1f; font-size:14px; color:#fff; font-weight:normal; " width="5%" valign="middle" align="left"><img src="system/themes/prelaunch_lp/images/arrowimgupdate.png" alt="#"></th>
                <th style="background:#069d1f; font-size:14px; color:#fff; font-weight:normal; " width="16%" valign="middle" align="center">Total </th>
                <th style="background:#069d1f;" width="2%" valign="middle" align="left">&nbsp;</th>
            </tr>';


while($o_res && $order = db_fetch_assoc($o_res)) {

    $billing_addr = $order['billing_addr'];
    $billing_addr = unserialize($billing_addr);

    $total_amnt = $order['total'];
    $shipping = $order['shipping'];
    $tax = $order['tax'];

    $p_total = ($order['price'] * $order['qty']);
    $p_total = number_format($p_total, 2, '.', '');

    $subtotal += $p_total;

    $product_html .= '<tr>
                <td style="border-bottom:solid 2px #51b517;" valign="middle" align="left">&nbsp;</td>
                <td style="font-size:14px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b" valign="middle" align="left">'.$order['title'].'</td>
                <td style="border-bottom:solid 2px #9e9b9b;" valign="middle" align="left">&nbsp;</td>
                <td style="font-size:14px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b" valign="middle" align="center">$'.$order['price'].'</td>
                <td style="border-bottom:solid 2px #9e9b9b;" valign="middle" align="left">&nbsp;</td>
                <td style="font-size:14px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b" valign="middle" align="center">'.$order['qty'].' </td>
                <td style="border-bottom:solid 2px #9e9b9b;" valign="middle" align="left">&nbsp;</td>
                <td style="font-size:14px; color:#111; font-weight:normal;  border-bottom:solid 2px #9e9b9b" valign="middle" align="center">$'.$p_total.' </td>
                <td style="border-bottom:solid 2px #51b517;" valign="middle" align="left">&nbsp;</td>
            </tr>';


}



$product_html .= '</tbody></table>';


?>



<div class="container-fluid toplogoblock text-center">
    <img class="img-responsive" src="system/themes/prelaunch_lp/images/logo-enrollment.png">
</div>

<div class="container-fluid toptitleblock">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h2 class="titlebar">
                <span>Vivacity is set to hit the world stage with an incredible launch in 2017!</span>
            </h2>
        </div>
    </div>
</div>

<div class="success_wrapper">

    <div class="order_heading">Order Successful</div>

    <img src="system/themes/prelaunch_lp/images/success_img.jpg" alt="#" class="success_img">
    <h2>Your Order Has Been Successfully Placed </h2>

    <h3>Thank you for placing your order. We will get back to you soon</h3>

    <h4>Your Replicated URL is: <a href="<?php echo $replcated_url.'?ai_bypass=true';?>"><?php echo $replcated_url;?></a></h4>
    <div class="success_table_block">




        <?php echo $product_html;?>








        <div class="grandtotal_block">
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tbody>

                <tr>
                    <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f">&nbsp;</td>
                    <td width="70%" valign="middle" align="right" style="border-bottom:solid 2px #9e9b9b"><div class="td_text">Subtotal</div></td>
                    <td width="20%" valign="middle" align="right" style="border-bottom:solid 2px #9e9b9b"><div class="td_valu">$<?php echo $subtotal;?></div></td>
                    <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f">&nbsp;</td>
                </tr>
                <tr>
                    <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f">&nbsp;</td>
                    <td width="70%" valign="middle" align="right" style="border-bottom:solid 2px #9e9b9b"><div class="td_text">Shipping</div></td>
                    <td width="20%" valign="middle" align="right" style="border-bottom:solid 2px #9e9b9b"><div class="td_valu">$<?php echo $shipping;?></div></td>
                    <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f">&nbsp;</td>
                </tr>
                <tr><td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f">&nbsp;</td>
                    <td width="70%" valign="middle" align="right" style="border-bottom:solid 2px #9e9b9b"><div class="td_text">Tax</div></td>
                    <td width="20%" valign="middle" align="right" style="border-bottom:solid 2px #9e9b9b"><div class="td_valu">$<?php echo $tax;?></div></td>
                    <td width="5%" align="left" valign="middle" style="border-bottom:solid 2px #069d1f">&nbsp;</td>
                </tr>
                <tr>
                    <td width="5%" align="left" valign="middle" style="background:#069d1f;">&nbsp;</td>
                    <td width="70%" style="color:#fff; background:#069d1f;" valign="middle" align="right"><div class="td_text td_text1">Grand Total</div></td>
                    <td width="20%" style="color:#fff; background:#069d1f;" valign="middle" align="right"><div class="td_valu">$<?php echo $total_amnt;?></div></td>
                    <td width="5%" align="left" valign="middle" style="background:#069d1f;">&nbsp;</td>

                </tr>


                </tbody></table>


        </div>


        <div class="clearfix"></div>
    </div>


</div>


<div class="container ad_footerblock text-center">

    <div class="footer_div1">
        <a href="javascript:void(0)"><img src="system/themes/prelaunch_lp/images/ficon.png" alt="#"></a>
        <a href="javascript:void(0)"><img src="system/themes/prelaunch_lp/images/ticon.png" alt="#"></a>
        <a href="javascript:void(0)"><img src="system/themes/prelaunch_lp/images/bicon.png" alt="#"></a>
        <a href="javascript:void(0)"><img src="system/themes/prelaunch_lp/images/gicon.png" alt="#"></a>
        <a href="javascript:void(0)"><img src="system/themes/prelaunch_lp/images/yicon.png" alt="#"></a>
        <a href="javascript:void(0)"><img src="system/themes/prelaunch_lp/images/iicon.png" alt="#"></a>
        <a href="javascript:void(0)"><img src="system/themes/prelaunch_lp/images/picon.png" alt="#"></a>

    </div>

    <div class="footer_div2">
        <a href="javascript:void(0)">Privacy Policy</a> · <a href="javascript:void(0)">Terms & Conditions</a> . <a href="javascript:void(0)">Shipping & Return</a>

        <span>© Copyright 2016 Vivacity - All Rights Reserved </span>
    </div>

</div>

