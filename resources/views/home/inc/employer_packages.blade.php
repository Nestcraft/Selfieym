<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    @import url(https://fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700italic,700,900italic,900);
    @import url(https://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,700,800,900);
    @import url(https://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,700,800,900);
    .topspace{
        margin-top: 150px;
    }
    .standard{
        margin-bottom: 1.5rem!important;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: .25rem;
    }
    .standheader {
        padding: .75rem 1.25rem;
        margin-bottom: 0;
        background-color: rgba(0,0,0,.03);
        border-bottom: 1px solid rgba(0,0,0,.125);
        text-align: center;
    }
    .standheader h4 {
        padding: 0px;
        font-size: 22px;
    }
    .card-content{
        padding: 1.25rem
    }
    .text-center{
        text-align: center;
        font-size: 30px;
        line-height: 35px;
    }
    .font-weight-bold {
        font-weight: 700!important;
    }
    .text-muted {
        color: #6c757d!important;
        font-size: 22px;
    }
    .list-border>li {
        border-top: 1px solid #ebebeb;
        line-height: 36px;
        font-size: 16px;
    }
    .list-border>li:first-child {
        border: none;
    }
    .btn-outline-primary {
        color: #311d74!important;
        border-color: #311d74!important;
        padding: 10px 18px;
        font-size: 1rem;
        width: 100%;
    }
    .btn-outline-primary:hover{
        background: #311d74!important;
        color: #fff!important;
    }
    .colorchange{
        border: 1px solid #311d74!important;
    }
    .colorchange .standheader{
        background: #311d74!important;
        color: #fff;

    }

    .colorchange .btn-outline-primary{
     background: #311d74!important;
     color: #fff!important; 
 }


</style>
@extends('layouts.master')
<div class="container">
   @if(session()->has('message'))
   <div class="alert alert-success">
    {{ session()->get('message') }}
</div>
@endif
<div class="row topspace">

    @foreach($employer_packages_data as $employer_packages_data)
    <div class="col-md-4 col-12">
     <div class="standard colorchange">
        <div class="standheader"><h4>{{$employer_packages_data->name}}</h4></div>
        <div class="card-content">
            <h1 class="text-center">
                <span class="font-weight-bold">Rs. {{$employer_packages_data->price}}</span>
                <small class="text-muted">/ {{$employer_packages_data->duration}} Days</small>
            </h1>
             <ul class="list list-border text-center mt-3 mb-4">
                    <li><b>Project Fees : {{$employer_packages_data->commission}}</b><b><?php if($employer_packages_data->commission_type=='1'){
                        echo ' Flat';

                    } elseif($employer_packages_data->commission_type=='2'){
                       echo '%'; 
                    }else{
                        echo '';  
                    }



                    ?></b> </li></ul>
            <ul class="list list-border text-center mt-3 mb-4">
                <li>{{$employer_packages_data->description}}</li>
            </ul>

            <!-- PAYU MONEY FORM-->
            <form action = "/payu-money-employer-packages" method = "post" name="payuForm">
                @csrf
                <input name="user_type" value="employer" type="hidden" /> 

                <input name="i_package_id" value="<?= encrypt($employer_packages_data->id); ?>" type="hidden" /> 
                <div class="generic_price_btn clearfix">
                   <!-- <a class="" href="">BUY</a> -->
                   <input type="submit" name="pay_u_money_submit" data-p-id = "<?= encrypt($employer_packages_data->id); ?>" data-p-amount = "<?= $employer_packages_data->price; ?>" value="Buy" class="btn btn-lg btn-block btn-outline-primary">
               </div>

           </form>
           <!-- PAYU MONEY FORM-->
       </div>
   </div>
</div>
@endforeach

</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
<script>
    $(document).ready(function()
    {
        $(document).on('click', "input[name='pay_u_money_submit']",function()
        {
            event.preventDefault();



            var logged_in_userid = '{{$logged_in_userid}}';
            var base_url = '<?= URL::to('/'); ?>';
            if(logged_in_userid == ''){
                var session_flag = '<?php Session::put('RedirectionFlagInfluencer', url()->current());?>';
                toastr.info('Redirecting.. Login to continue');
                window.location.href = base_url;
                return false;
            }else{
              var user_role = '{{$user_role}}';

              if(user_role == '2')
              {

               toastr.error('Only Employers can buy these packages.');
               return false;

           }
           if($(this).attr('data-p-amount') < '1.00')
           {
            var package_id = $(this).attr('data-p-id');
            $.ajax({
                url: "/buyfreepackage",
                method: "post",
                cache: false,
                data:{package_id:package_id},
                success: function(data){
                    if(data == 2)
                    {
                        toastr.error('You have already subscribe this package.');
                    }
                    if(data == 1)
                    {
                        toastr.success('Transaction successfully done.');
                    }
                }
            });
        }else{
            $('form').submit();
        }
    }
});
    });
</script>