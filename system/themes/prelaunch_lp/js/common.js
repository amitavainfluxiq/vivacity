$( document ).ready(function() {

    if($('#productformcon').length){
        setoffsetpos();
    }

    if($('#prelaunch_landing_page').length){
        $('.btnjoinnow').click(function(){
            $("html, body").animate({ scrollTop: $('#prelaunch_landing_page').offset().top }, 1000);
            $('#prelaunch_landing_page').find('input[name="first_name"]').focus();
        })
    }

    $('#package_name').text('No Product');
    $('#package_subtotal').text('$0.00');
    $('#package_shipping').text('$0.00');
    $('#package_tax').text('$0.00');
    $('#package_total').text('$0.00');

    if($('#pid').length){
        setTimeout(function(){
            var sel_pro = $('#pid').val();
            if(sel_pro == 11){
                $('#selbtn11').click();
            }
            if(sel_pro == 12){
                $('#selbtn12').click();
            }
        },5000);
    }


    $('#bill_region').change(function(){
        var taxstr = $('#taxarr').val();
        var taxarr = JSON.parse(taxstr);
        var bill_region = $(this).val();

        var tax = 0;
        if(typeof(bill_region) !='undefined' && bill_region != ''){
            tax = taxarr[bill_region];
        }

        if(typeof (tax) == 'undefined'){
            tax = 0;
        }

        var sel_pro = $('#pid').val();

        if(sel_pro == 11 || sel_pro == 12){

            var pcost = $('#landing_page_chk').find('#pcost').val();
            var shippingcost = $('#landing_page_chk').find('#shippingcost').val();
            var taxcost = 0.00;

            pcost = parseFloat(pcost);
            shippingcost = parseFloat(shippingcost);

            if(parseFloat(tax) > 0){
                taxcost = ((parseFloat(pcost)*parseFloat(tax))/100);
                taxcost = parseFloat(taxcost);
                taxcost = taxcost.toFixed(2);
            }

            var total = parseFloat(pcost)+parseFloat(shippingcost)+parseFloat(taxcost);

            pcost = pcost.toFixed(2);
            shippingcost = shippingcost.toFixed(2);
            total = total.toFixed(2);


            $('#landing_page_chk').find('#taxcost').val(taxcost);
            $('#package_tax').text('$'+taxcost);
            $('#package_total').text('$'+total);

        }

    })


});


function setoffsetpos(){

    var formheight = $('#productformcon').height();

    $('.vcform_block').height(formheight+20);


    $("#productformcon").css('position','absolute');

    var x = $(".vcform_block").offset();
    $( "#productformcon" ).offset({ top: x.top, left: x.left });
    //$("#productformcon").css('margin-left','-58px');


    setTimeout(function () {
        setoffsetpos();
    },2000);
}

function selProduct(pid,obj) {
    $('#landing_page_chk').find('#pid').val(pid);
    $('#landing_page_chk').find('#testpid').val(pid);

    $('.select_btn').removeClass('active_select_btn');
    $('.select_btn').val('select this package');
    $(obj).addClass('active_select_btn');
    $(obj).val('selected');


    var taxstr = $('#taxarr').val();
    var taxarr = JSON.parse(taxstr);
    var shipstr = $('#shiparr').val();
    var shiparr = JSON.parse(shipstr);
    var bill_region = $('#bill_region').val();

    console.log(bill_region);

    var tax = 0;
    if(typeof(bill_region) !='undefined' && bill_region != ''){
        tax = taxarr[bill_region];
    }

    if(typeof (tax) == 'undefined'){
        tax = 0;
    }


    if(pid == 11){
        var pcost = 99;
        var shippingcost = shiparr[99];
        shippingcost = parseFloat(shippingcost);
        var taxcost = 0;

        if(parseFloat(tax) > 0){
            taxcost = ((parseFloat(pcost)*parseFloat(tax))/100);
            taxcost = parseFloat(taxcost);
            taxcost = taxcost.toFixed(2);
        }

        var total = parseFloat(pcost)+parseFloat(shippingcost)+parseFloat(taxcost);

        pcost = pcost.toFixed(2);
        shippingcost = shippingcost.toFixed(2);
        total = total.toFixed(2);


        $('#landing_page_chk').find('#pcost').val(pcost);
        $('#landing_page_chk').find('#shippingcost').val(shippingcost);
        $('#landing_page_chk').find('#taxcost').val(taxcost);
        $('#package_name').text('Balance Program');
        $('#package_subtotal').text('$'+pcost);
        $('#package_shipping').text('$'+shippingcost);
        $('#package_tax').text('$'+taxcost);
        $('#package_total').text('$'+total);
    }
    if(pid == 12){
        var pcost = 199;
        var shippingcost = shiparr[199];
        shippingcost = parseFloat(shippingcost);
        var taxcost = 0;

        if(parseFloat(tax) > 0){
            taxcost = ((parseFloat(pcost)*parseFloat(tax))/100);
            taxcost = parseFloat(taxcost);
            taxcost = taxcost.toFixed(2);
        }

        var total = parseFloat(pcost)+parseFloat(shippingcost)+parseFloat(taxcost);

        pcost = pcost.toFixed(2);
        shippingcost = shippingcost.toFixed(2);
        total = total.toFixed(2);


        $('#landing_page_chk').find('#pcost').val(pcost);
        $('#landing_page_chk').find('#shippingcost').val(shippingcost);
        $('#landing_page_chk').find('#taxcost').val(taxcost);
        $('#package_name').text('Balance Program');
        $('#package_subtotal').text('$'+pcost);
        $('#package_shipping').text('$'+shippingcost);
        $('#package_tax').text('$'+taxcost);
        $('#package_total').text('$'+total);
    }
}


function formchkvalidate(){
    var pid = $('#landing_page_chk').find('#testpid').val();

    if(pid == 0){
        jonbox_alert('Please choose product.');
    }else{
        $('#landing_page_chk').submit();
    }
}