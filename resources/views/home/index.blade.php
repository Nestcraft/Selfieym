
	<style>

 .topspace{
  margin-top: 10px;
}

.banner-intro { background-color:#271962; 
 background1: rgb(183,218,223);
padding-top:25px;height1:540px; height1:auto;
}

.focus-intro {background:#ff0f69; background1:#ccc; padding:20px; background1: linear-gradient(90deg, rgba(156,209,212,1) 0%, rgba(196,221,216,1) 100%); color:white;}
.focus-intro h3 {font-weight:bold;} .focus-intro p {font-size:14px; color:white;}

  .lgtitle { font-family: "Roboto Condensed", Helvetica, Arial, sans-serif;  font-weight:thin;
  line-height: 1.4;color:#78EFAB;
}



</style>	
	
	@extends('layouts.master')

	@section('search')
	@parent
	@endsection
	@section('content')
	<div class="main-container" id="homepage">
	    
<!-- custom banner-->
	    
	    <section class="banner-intro text-left shadow">
   <div class="container mt-5">
   <div class="row">
    <div class="col-md-6 mb-5">
       
      <h1 class="lgtitle">Find & Hire the Trusted <i><b>Social Media Influencer</b></i> for Your Brand Marketing</h1>
       <h3 class="color-success">CONNECT > ENGAGE > GET PAID</h3><br>
     <h2 class="color-white lead">Join our Influencers Community and Work with the World's Top Brands<b> Start Earning from your Social Media Page</b></h2>

      <a href="/register/" class="btn btn-success btn-lg" role="button">Influencer Signup</a> &nbsp; &nbsp; &nbsp;

     <a href="/posts/create/" class="btn btn-secondary btn-lg " role="button">Post Free Job</a>

    </div>
    <div class="col-md-6">
     <div id="carouselSlides" class="carousel slide carousel-fade" data-ride="carousel">
  <div class="carousel-inner">
      <div class="carousel-item active">
      <img class="shadow" src="/public/images/profile_images/influncers-fitness.jpg" alt="Social Media Influencer for Fitness Industry" title="Social Media Influencer for Fitness Industry">
    </div>
      <div class="carousel-item">
      <img class="d-block" src="/public/images/profile_images/tech-influncer.jpg" alt="Hire Social Media Influncer in India" title="Hire Social Media Influencer in India" >
    </div>
    <div class="carousel-item">
      <img class="shadow" src="/public/images/profile_images/influncers-blogger.jpg" alt="Social Media Influencer for Fitness Industry" title="Social Media Influencer for Fitness Industry" >
    </div>
    
    <div class="carousel-item">
      <img class="d-block1" src="/public/images/profile_images/youtuber-influncer.jpg" alt="Social Media Influencer Marketing Website" title="Social Media Influencer Marketing Website" >
    </div>
     <div class="carousel-item">
      <img class="d-block1" src="/public/images/profile_images/influencers-fashion.jpg" alt="Social Media Beauty Influencer Platform in india" title="Social Media Beauty Influencer Platform in india" >
    </div>
    
  </div>
</div>
    </div>
  </div>
  </div>
</section>  
<section class="focus-intro">
    <div class="container">
   <div class="row">
    <div class="col"><h2> Hello Influencer!</h2> <h3>At Selfieym Get Paid for every shoutouts - Start Earning in 3 Simple Steps</h3></div>
    <div class="col"><h3>1. Create Profile</h3>
    <p>Create Account, Add Your Social Media Pages, Add Followers Counts, Add Your Price for Per Post</p>
    
    </div>
    <div class="col"> <h3>2. Bid Project & Get Hired</h3>
    <p>Bid Project, Directly Hired by Employer, Finish the Project, Ask for Release Payment to your Wallet</p> </div>
    <div class="col"> <h3>3. Receive Payment</h3>
    <p>Withdraw Money from wallet to direct your bank Account via Secure Payumoney Payment Gateway, Google Pay or UPI :)</p></div>
    
    </section>
<div class="mt-5"></div>	    
	    <!-- banner end-->
	    
		@if (Session::has('message'))
		@include('common.spacer')
		<?php $paddingTopExists = true; ?>
		<div class="container">
			<div class="row">

				<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					{{ session('message') }}
				</div>
			</div>
		</div>
		
	
		@endif
		
		@if (Session::has('flash_notification'))
		@include('common.spacer')
		<?php $paddingTopExists = true; ?>
		<div class="container">
			<div class="row">
				<div class="col-xl-12">
					@include('flash::message')
				</div>
			</div>
		</div>
		@endif
		
		@if (isset($sections) and $sections->count() > 0)
		@foreach($sections as $section)
		@if (view()->exists($section->view))
		@include($section->view, ['firstSection' => $loop->first])
		@endif
		@endforeach
		@endif
		
	</div>
	@endsection

	@section('after_scripts')

	@endsection
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	
	<script>
		$(document).ready(function(){
			var session_login_check = '<?= Session::get('RedirectionFlagInfluencer');?>';
			if(session_login_check != ''){
				$('#quickLogin').modal('show');
			}
		})
	</script>
