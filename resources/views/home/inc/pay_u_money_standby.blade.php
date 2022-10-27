<style>
    form {
        margin-top: 150px;
    }
</style>

<div class="container">
    <h1 style="text-align:center;vertical-align: middle;line-height: 500px;">Please wait...</h1>

    <form action ='{{PAY_U_MONEY_TEST_URL}}' method = "POST" id="payuFormMoneyForm" name="payuForm">

        <input type="hidden" name="key" value ="{{$PAYU_MONYEY_MERCHANT_KEY}}" />

        <input type="hidden" name="hash" value="{{$hash}}"/>

        <input type="hidden" name="txnid" value="{{$txnid}}" />

        <input  type="hidden" class="form-control" name="amount" value="{{$product_amount}}"/>

        <input class="form-control" name="udf1" value="{{$udf1}}" hidden/>

        <input class="form-control" name="udf2" value="{{$udf2}}" hidden/>
        <input class="form-control" name="udf3" value="{{$udf3}}" hidden/>
        <input class="form-control" name="udf4" value="{{$udf4}}" hidden/>
        <input class="form-control" name="udf5" value="{{$udf5}}" hidden/>
        
        <input class="form-control" name="productinfo" value="{{$productinfo}}" hidden/>

        <input class="form-control" name="firstname" value="{{$user_name}}" hidden/>

        <input class="form-control" name="email" value="{{$user_email}}" hidden/>

        <input class="form-control" name="phone"  value="{{$user_phone}}" hidden/>

        <input name="surl" value="{{$surl}}" type="hidden" />

        <input name="furl" value="{{$furl}}" type="hidden" />
        <input name="service_provider" value="payu_paisa" type="hidden" />    

        <div class="generic_price_btn clearfix">
           <!-- <a class="" href="">BUY</a> -->
           <input type="submit" name="pay_u_money_submit" value="Buy" style="display:none;">
       </div>

   </form>

</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        setTimeout(function(){ 
            $('form#payuFormMoneyForm').submit(); 
        }, 1500);
    });
</script>