<link href="/public/css/custom.css" rel="stylesheet" type="text/css" />

<style>

.fade:not(.show)
{
opacity: 1.9 !important;
}

/* The Modal (background) */
.modal {
display: none; /* Hidden by default */
position: fixed; /* Stay in place */
z-index: 1; /* Sit on top */
padding-top: 100px; /* Location of the box */
left: 0;
top: 0;
width: 100%; /* Full width */
height: 100%; /* Full height */
overflow: auto; /* Enable scroll if needed */
background-color: rgb(0,0,0); /* Fallback color */
background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
background-color: #fefefe;
margin: auto;
padding: 20px;
border: 1px solid #888;
width: 80%;
}

/* The Close Button */
.close {
color: #aaaaaa;
float: right;
font-size: 28px;
font-weight: bold;
}



.close:hover,
.close:focus {
color: #000;
text-decoration: none;
cursor: pointer;
}

.topspace{
margin-top: 150px;
}
.profile-featured {
position: absolute;
top: 14px;
right: 20px;
}

.nav-tabs li {font-size:20px; color:#51C0F4;}
.nav-tabs {border-top: 4px solid #86bb76; background-color:#eee;}


.bordered-tab-contents > .tab-content > .tab-pane {
border-left: 1px solid #ddd;
border-right: 1px solid #ddd;
border-bottom: 1px solid #ddd;
border-radius: 0px 0px 5px 5px;
padding: 16px; 
}

.bordered-tab-contents > .nav-tabs {
margin-bottom: 0px;
}




</style>
@extends('layouts.master')
<div class="topspace"></div>
<div class="container">
@if (\Session::has('RateMessageError'))
    <div class="alert alert-danger">
        <ul>
            <li>{!! \Session::get('RateMessageError') !!}</li>
        </ul>
    </div>
@endif

@if (\Session::has('RateMessageSuccess'))
    <div class="alert alert-success">
        <ul>
            <li>{!! \Session::get('RateMessageSuccess') !!}</li>
        </ul>
    </div>
@endif

<div class="row">
<div class="jumbotron w-100">
<div class="row">
<div class="col-md-3">

<?php

if (!empty($influencer_profile_data[0]->profile_image)): ?>
<img src="/public/images/profile_images/<?=$influencer_profile_data[0]->profile_image;?>" alt="<?php if (!empty($influencer_profile_data[0]->name)): ?><?=ucfirst($influencer_profile_data[0]->name);?><?php endif;?> -  <?php if (!empty($influencer_profile_data[0]->catname)): ?><?=$influencer_profile_data[0]->catname;?><?php endif;?>" title ="<?php if (!empty($influencer_profile_data[0]->name)): ?><?=ucfirst($influencer_profile_data[0]->name);?><?php endif;?> -  <?php if (!empty($influencer_profile_data[0]->catname)): ?><?=$influencer_profile_data[0]->catname;?><?php endif;?>">
<?php else: ?>
<img src="https://www.gravatar.com/avatar/2fb1121670440f4cd693e7478111d453.jpg?s=80&d=https%3A%2F%2Fselfieym.com%2Fimages%2Fuser.jpg&r=g" alt="Name of the influencer -  Title & Industry" title ="Name of the influencer -  Title & Industry">

<?php endif;?>
<?php if (isset($influencer_profile_data[0]->is_featured) && $influencer_profile_data[0]->is_featured == 1): ?>

<div class="profile-featured"><span class="label badge-danger"> Featured</span></div>

<?php endif;?>

<div style="text-align:center; color:#ffa000; margin-top:30px;">

<ul class="list-unstyled list-inline rating mb-3">

<?php $influencer_rating = \App\Helpers\UrlGen::get_influencer_rating($influencer_profile_data[0]->id);?>

<?php if (isset($influencer_rating) && $influencer_rating > 0): ?>
<?php
for ($count = 1; $count <= $influencer_rating; $count++) {
    echo '<li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>';
}
?>
<?php else: ?>
<p>Not rated yet</p>
<?php endif;?>


</ul>
<?php if ($user_type_id == 1): ?>
<p><a href="#" data-attr-contact-id="{{$influencer_profile_data[0]->id}}" id="ContactInfluencer" class="btn btn-primary">Contact Me</a></p>
<?php endif;?>

</div>


</div>

<div class="col-md-9">
<h1><?php if (!empty($influencer_profile_data[0]->name)): ?><?=ucfirst($influencer_profile_data[0]->name);?><?php endif;?> - <?php if (!empty($influencer_profile_data[0]->catname)): ?><?=$influencer_profile_data[0]->catname;?><?php endif;?></h1>
<div class="row"><div class="col-md-3"><h4><b>Industry:</b> <?php if (!empty($influencer_profile_data[0]->catname)): ?><?=$influencer_profile_data[0]->catname;?><?php endif;?></h4></div>

<div class="col-md-3"><h4><b>City:</b> <?php if (isset($influencer_profile_data[0]->cityname) && !empty($influencer_profile_data[0]->cityname)): ?><?=$influencer_profile_data[0]->cityname;?><?php endif;?></h4></div>


<div class="col-md-3"><h4><b>Strating At: </b><span class="badge badge-dark">{!! \App\Helpers\Number::money($influencer_profile_data[0]->min_fee) !!}</span></h4></div>
</div><hr>
<h3>Social Media Followers Statics:</h3>

<table class="table table-sm">
<thead>
<tr>
<th>Platform</th>
<th>Followers</th>
<th>Social Media Page Url</th>
</tr>
</thead>
<tbody>
<tr>

<td><button class="btn btn-fb" style="font-size:10px;"><i class="fab fa-facebook"></i></button> Facebook </td>
<td><span class="badge badge-secondary"><?php if (!empty($influencer_profile_data[0]->facebook_followers && $influencer_profile_data[0]->facebook_followers!='K')): ?><?=$influencer_profile_data[0]->facebook_followers;?><?php else: ?><?='0K';?><?php endif;?></span></td>
<td><a href="#" target="_blank"><?php if (!empty($influencer_profile_data[0]->facebook_url)): ?><?=$influencer_profile_data[0]->facebook_url;?><?php else: ?><?='#';?><?php endif;?></a></td>
</tr>
<tr>
<td><button class="btn btn-tw" style="font-size:10px;"><i class="fab fa-twitter"></i></button> Twitter </td>
<td><span class="badge badge-secondary"><?php if (!empty($influencer_profile_data[0]->twitter_followers && $influencer_profile_data[0]->twitter_followers!='K')): ?><?=$influencer_profile_data[0]->twitter_followers;?><?php else: ?><?='0K';?><?php endif;?></span></td>
<td><a href="#" target="_blank"><?php if (!empty($influencer_profile_data[0]->twitter_url)): ?><?=$influencer_profile_data[0]->twitter_url;?><?php else: ?><?='#';?><?php endif;?></a></td>
</tr>

<tr>
<td><button type="button" class="btn btn-yt btn-danger" style="font-size:10px;"><i class="fab fa-youtube"></i></button> Youtube </td>
<td><span class="badge badge-secondary"><?php if (!empty($influencer_profile_data[0]->youtube_subscribers && $influencer_profile_data[0]->youtube_subscribers!='K')): ?><?=$influencer_profile_data[0]->youtube_subscribers;?><?php else: ?><?='0K';?><?php endif;?></span></td>
<td><a href="#" target="_blank"><?php if (!empty($influencer_profile_data[0]->youtube_url)): ?><?=$influencer_profile_data[0]->youtube_url;?><?php else: ?><?='#';?><?php endif;?></a></td>
</tr>

<tr>
<td><button type="button" class="btn btn-ins btn" style="font-size:10px; background-color:#e3469b;"> <i class="fab fa-instagram"></i></button> Instagram</td>
<td><span class="badge badge-secondary"><?php if (!empty($influencer_profile_data[0]->instagram_followers && $influencer_profile_data[0]->instagram_followers!='K')): ?><?=$influencer_profile_data[0]->instagram_followers;?><?php else: ?><?='0K';?><?php endif;?></span></td>
<td><a href="#" target="_blank"><?php if (!empty($influencer_profile_data[0]->instagram_url)): ?><?=$influencer_profile_data[0]->instagram_url;?><?php else: ?><?='#';?><?php endif;?></a></td>
</tr>
<tr>
<td><button type="button" class="btn btn-ins btn" style="font-size:10px; background-color:#0ea634;"> <i class="fab fa-quora"></i></button> Quora</td>
<td><span class="badge badge-secondary"><?php if (!empty($influencer_profile_data[0]->quora_followers) && $influencer_profile_data[0]->quora_followers!='K' ): ?><?=$influencer_profile_data[0]->quora_followers;?><?php else: ?><?='0K';?><?php endif;?></span></td>
<td><a href="#" target="_blank"><?php if (!empty($influencer_profile_data[0]->quora_url)): ?><?=$influencer_profile_data[0]->quora_url;?><?php else: ?><?='#';?><?php endif;?></a></td>
</tr>

</tbody>
</table>
</div>

</div>
</div>
<div class="container">
<div class="row">
<div class="col-md-7 mr-0">
    
<h2>About Me/Bio</h2>
<hr>
<?php if (!empty($influencer_profile_data[0]->biodata)): ?>
<p style="font-size:120%;"><?=$influencer_profile_data[0]->biodata;?></p>
<?php endif;?>
<hr>
<h3>Skills & Expertise</h3>
<p>
<?php if (!empty($influencer_profile_data[0]->skill_expertise)): 
$skill=explode(',',$influencer_profile_data[0]->skill_expertise);

foreach ($skill as $skillData) { ?>
   
     <span style="padding:2px;"><a href="https://selfieym.com/tag/<?=$influencer_profile_data[0]->skill_expertise;?>" class="label badge-info">
<?=$skillData;?></a></span> <?php } endif;?> </p>
<hr>
<h3>Clients/Brands I have Worked</h3>
<?php if (!empty($influencer_profile_data[0]->client_base)): ?>
<p><?=$influencer_profile_data[0]->client_base;?></p><?php endif;?>
</div>

@if($errors->any())
<h4>{{$errors->first()}}</h4>
@endif

<!-- profile div end Side dive start-->
<div class="col-md-5">
<div class="bordered-tab-contents">
<ul class="nav nav-tabs">

<li class="nav-item">
<a href="#basic" class="nav-link active" data-toggle="tab">Basic</a>
</li>


<li class="nav-item">
<a href="#standard" class="nav-link" data-toggle="tab">Standard</a>
</li>


<li class="nav-item">
<a href="#premium" class="nav-link" data-toggle="tab">Premium</a>
</li>
</ul>
<div class="tab-content">
<div class="tab-pane fade show active" id="basic">
@if(isset($myrate->basic_package_price))

<h3 class="mt-3"><?php if (isset($myrate->basic_package_title)) {echo substr($myrate->basic_package_title, '1', '150');}?>: <span class="badge badge-dark">@if(isset($myrate->basic_package_price)){!! \App\Helpers\Number::money($myrate->basic_package_price) !!} @endif</span></h3>
<p><?php if (isset($myrate->basic_package_service)) {echo substr($myrate->basic_package_service, '1', '150');}?></p>

@if(isset($myrate->basic_package_price))
<form action = "/influencer-rate-packages" method = "post" name="payuForm">
@csrf

<input name="i_package_id" value="<?=encrypt($myrate->id);?>" type="hidden" />
<input name="package_type" value="<?=encrypt('basic');?>" type="hidden" />

<input type="hidden" name="influencer_id" value = "<?php if ($influencer_id): ?><?=encrypt($influencer_id);?><?php else: ?><?='';?><?php endif;?>">


<input type="submit" name="InfluencerRatePackages" value="Continue ({!! \App\Helpers\Number::money($myrate->basic_package_price) !!})" class="btn btn-success btn-lg">

</form>
<!--  <p><a href="#" target="_blank" class="btn btn-success btn-lg">Continew ({!! \App\Helpers\Number::money($myrate->basic_package_price) !!})</a></p> --> @endif
@else
<p>No Basic Package</p>
@endif
</div>

<div class="tab-pane fade" id="standard">
@if(isset($myrate->standard_package_price))
<h3 class="mt-2"><?php if (isset($myrate->standard_package_title)) {echo substr($myrate->standard_package_title, '0', '150');}?>:  <span class="badge badge-dark"> @if(isset($myrate->standard_package_price)){!! \App\Helpers\Number::money($myrate->standard_package_price) !!} @endif </span></h3>
<p><?php if (isset($myrate->standard_package_service)) {echo substr($myrate->standard_package_service, '0', '150');}?></p>
@if(isset($myrate->standard_package_price))
<form action = "/pay_u_money_myrate" method = "post" name="payuForm">
@csrf

<input name="i_package_id" value="<?=encrypt($myrate->id);?>" type="hidden" />
<input name="package_type" value="<?=encrypt('standard');?>" type="hidden" />
<input type="hidden" name="influencer_id" value = "<?php if ($influencer_id): ?><?=encrypt($influencer_id);?><?php else: ?><?='';?><?php endif;?>">

<input type="submit" name="pay_u_money_submit" data-p-id = "<?=encrypt($myrate->id);?>" data-p-amount = "<?=$myrate->id;?>"value="Continue ({!! \App\Helpers\Number::money($myrate->standard_package_price) !!})" class="btn btn-success btn-lg">

</form>
<!-- <p><a href="#" target="_blank" class="btn btn-success btn-lg">Continew &raquo; ({!! \App\Helpers\Number::money($myrate->standard_package_price) !!})</a></p> -->
@endif
@else
<p>No Standard Package</p>
@endif
</div>
<div class="tab-pane fade" id="premium">
@if(isset($myrate->premium_package_price))
<h3 class="mt-2"><?php if (isset($myrate->premium_package_title)) {echo substr($myrate->premium_package_title, '0', '150');}?>:  <span class="badge badge-dark"> @if(isset($myrate->premium_package_price)){!! \App\Helpers\Number::money($myrate->premium_package_price) !!} @endif</span></h3>
<p><?php if (isset($myrate->premium_package_service)) {echo substr($myrate->premium_package_service, '0','150');}?></p>
@if(isset($myrate->premium_package_price))
<form action = "/pay_u_money_myrate" method = "post" name="payuForm">
@csrf

<input name="i_package_id" value="<?=encrypt($myrate->id);?>" type="hidden" />

<input name="package_type" value="<?=encrypt('premium');?>" type="hidden" />

<input type="hidden" name="influencer_id" value = "<?php if ($influencer_id): ?><?=encrypt($influencer_id);?><?php else: ?><?='';?><?php endif;?>">

<input type="submit" name="pay_u_money_submit" data-p-id = "<?=encrypt($myrate->id);?>" data-p-amount = "<?=$myrate->id;?>"value="Continew ({!! \App\Helpers\Number::money($myrate->premium_package_price) !!})" class="btn btn-success btn-lg">

</form>
<!-- <p><a href="#" target="_blank" class="btn btn-success btn-lg">Continew &raquo; ({!! \App\Helpers\Number::money($myrate->premium_package_price) !!})</a></p> -->
@endif
@else
<p>No Standard Premium</p>
@endif
</div>
</div>
</div>



<div class="card sidebar-card mt-4">
<div class="card-header">Verifications Status</div>
<div class="card-content">
<div class="card-body text-left">
<ul class="list-check">
<?php if (!empty($influencer_profile_data[0]->is_featured) && $influencer_profile_data[0]->is_featured == '1') {?>
<li> Preferred Influencer</li>
<?php }if (!empty($jobpackagepayments) && $jobpackagepayments == 'yes') {?>
<li> Payment Method Verified </li>
<?php }if (!empty($influencer_profile_data[0]->verified_email) && $influencer_profile_data[0]->verified_email == '1') {?>
<li> Email Verified </li>
<?php }if (!empty($influencer_profile_data[0]->verified_phone) && $influencer_profile_data[0]->verified_phone == '1') {?>
<li> Phone Verified </li>
<?php }?>
</ul>
</div>
</div>
</div>
</div>
</div>



<!--End profile and side panel-->


<!-- image grid -->

<h2>Work Portfolio & Campaigns</h2>
<div class="row">

@foreach($user_portfolio_data as $portfolios)


<figure class="col-md-3">
<a data-fancybox="gallery" class="black-text" href="/public/images/user_portfolio_images/{{$portfolios->portfolio_image}}">
<img alt="picture" src="/public/images/user_portfolio_images/{{$portfolios->portfolio_image}}"
class="img-fluid">
<h4 class="text-center my-3">{{ucfirst($portfolios->portfolio_title)}}</h4>
</a>
</figure>

@endforeach

</div>
<!-- galery 2 type-->
<!-- youtube grid -->

<h2> YouTube Videos</h2>
<div class="row">

@foreach($user_youtube_data as $youtube_data)


<figure class="col-md-6">
<a data-fancybox="gallery" class="black-text" href="/public/images/user_portfolio_images/{{$youtube_data->youtube_url}}">
    <iframe src="{{$youtube_data->youtube_url}}" style="height:315px;width:540px;" title="Influencer marketing Platform"></iframe>

<!-- <img alt="picture" src="/public/images/user_portfolio_images/{{$portfolios->portfolio_image}}"
class="img-fluid"> -->

<!--<h4 class="text-center my-3">{{$youtube_data->youtube_url}}</h4>-->
</a>
</figure>

@endforeach

</div></div></div>
<!-- Review-->
<div class="border p-3 bg-light">
  <h2 class="text-center">Client's Reviews <b>(60)</b></h2>
<div class="container text-center">
  <div class="row">
    <div class="col-sm-4">
        <h3>Rahul Sharma</h3>
      <ul class="list-unstyled list-inline rating mb-3" style="color:#53A107;">

<?php $influencer_rating = \App\Helpers\UrlGen::get_influencer_rating($influencer_profile_data[0]->id);?>

<?php if (isset($influencer_rating) && $influencer_rating > 0): ?>
<?php
for ($count = 1; $count <= $influencer_rating; $count++) {
    echo '<li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>';
}
?>
<?php else: ?>
<p>Not rated yet</p>
<?php endif;?>


</ul>
   
      <p>Highly professional and responsive. The quality of design is superb. My first time using Fiverr and I am glad I met the right designer. Thanks!</p>
    <p>Delhi, SEP 27, 2020</p>
    </div>
    <div class="col-sm-4">
      <h3>Jojin George</h3>
       <ul class="list-unstyled list-inline rating mb-3" style="color:#53A107">

<?php $influencer_rating = \App\Helpers\UrlGen::get_influencer_rating($influencer_profile_data[0]->id);?>

<?php if (isset($influencer_rating) && $influencer_rating > 0): ?>
<?php
for ($count = 1; $count <= $influencer_rating; $count++) {
    echo '<li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>';
}
?>
<?php else: ?>
<p>Not rated yet</p>
<?php endif;?>


</ul>
      <p>Highly professional and responsive. The quality of design is superb. My first time using Fiverr and I am glad I met the right designer. Thanks!</p>
     <p>USA, AUG 12, 2020</p>
    </div>
    <div class="col-sm-4">
      <h3>Simon Singh</h3>
      <ul class="list-unstyled list-inline rating mb-3" style="color:#53A107;">

<?php $influencer_rating = \App\Helpers\UrlGen::get_influencer_rating($influencer_profile_data[0]->id);?>

<?php if (isset($influencer_rating) && $influencer_rating > 0): ?>
<?php
for ($count = 1; $count <= $influencer_rating; $count++) {
    echo '<li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>';
}
?>
<?php else: ?>
<p>Not rated yet</p>
<?php endif;?>


</ul>
      <p>Highly professional and responsive. The quality of design is superb. My first time using Fiverr and I am glad I met the right designer. Thanks!</p>
<p>Mumbai, APR 23, 2020</p>
    </div>
  </div>
</div>
</div>

<!-- #main div closing -->

</div>
</div>
<!------------CONTACT INFLUENCER MODAL------->
<div class="modal fade" id="myModal" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
@if(session()->has('errormessage'))
<script>
$(document).ready(function()
{
var modal = document.getElementById("myModal");
modal.style.display = "block";
window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}

$(document).on('click','a.close',function()
{

var modal = document.getElementById("myModal");
modal.style.display = "none";

});

});

</script>
<div class="alert alert-danger">
{{ session()->get('errormessage') }}
</div>
@endif

@if(session()->has('message'))
<script>
$(document).ready(function()
{
var modal = document.getElementById("myModal");
modal.style.display = "block";
window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}

$(document).on('click','a.close',function()
{

var modal = document.getElementById("myModal");
modal.style.display = "none";

});

});

</script>
<div class="alert alert-success">
{{ session()->get('message') }}
</div>
@endif
@if(count($errors) > 0 )
<script>
$(document).ready(function()
{

var modal = document.getElementById("myModal");
modal.style.display = "block";

$(document).on('click','a.close',function()
{

var modal = document.getElementById("myModal");
modal.style.display = "none";

});

});

</script>
<div class="alert alert-danger" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
<ul class="p-0 m-0" style="list-style: none;">
@foreach($errors->all() as $error)
<li>{{$error}}</li>
@endforeach
</ul>
</div>
@endif
<div class="modal-header">
<h4 style="text-align:center;">Contact <?php if (!empty($influencer_profile_data[0]->name)): ?><?=$influencer_profile_data[0]->name;?><?php endif;?></h4>
<a href="javascript:void(0)" class="close">&times;</a>
</div>
<div class="modal-body">
<form action="/messageinfluencer" method="POST">
@csrf
<input type="hidden" name="influencer_id" value = "<?php if ($influencer_id): ?><?=encrypt($influencer_id);?><?php else: ?><?='';?><?php endif;?>">


<input type="hidden" name="from_name" value = "<?php if ($from_name): ?><?=$from_name;?><?php else: ?><?='';?><?php endif;?>">
<input type="hidden" name="from_email" value = "<?php if ($from_email): ?><?=$from_email;?><?php else: ?><?='';?><?php endif;?>">
<input type="hidden" name="to_name" value = "<?php if ($to_name): ?><?=$to_name;?><?php else: ?><?='';?><?php endif;?>">
<input type="hidden" name="to_email" value = "<?php if ($to_email): ?><?=$to_email;?><?php else: ?><?='';?><?php endif;?>">
<input type="hidden" name="to_user_id" value = "<?php if ($to_user_id): ?><?=$to_user_id;?><?php else: ?><?='';?><?php endif;?>">

<input type="hidden" name="to_phone" value = "<?php if ($to_phone): ?><?=$to_phone;?><?php else: ?><?='';?><?php endif;?>">

<div class="form-group">
<label for="message">Messages:</label>
<textarea name="message" class="form-control">Default Message for the influencers..</textarea>
</div>
<button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
<div class="modal-footer">

</div>
</div>
</div>
</div>
<!------------CONTACT INFLUENCER MODAL------->

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.0/jquery.fancybox.min.css" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.0/jquery.fancybox.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.0/jquery.fancybox.min.css">


<script>
(function($){
$(document).ready(function(){
$(document).on('click','#ContactInfluencer',function(event)
{
var base_url = '<?=URL::to('/');?>';
var loggedin_userid = '<?=$loggedin_userid;?>';
event.preventDefault();
if(loggedin_userid == ''){
var session_flag = '<?php Session::put('RedirectionFlagInfluencer', url()->current());?>';
toastr.info('Redirecting.. Login to continue');
window.location.href = base_url;
}else{
var modal = document.getElementById("myModal");


modal.style.display = "block";
}
window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}

var span = document.getElementsByClassName("close")[0];
span.onclick = function() {
modal.style.display = "none";
}
});


});

// When the user clicks anywhere outside of the modal, close it

})(jQuery);
</script>
<script>
$(document).ready(function()
{
$(document).on('click', "input[name='pay_u_money_submit']",function()
{
event.preventDefault();


var logged_in_userid = '{{$loggedin_userid}}';
var base_url = '<?=URL::to('/');?>';
if(logged_in_userid == ''){
var session_flag = '<?php Session::put('RedirectionFlagInfluencer', url()->current());?>';
toastr.info('Redirecting.. Login to continue');
window.location.href = base_url;
return false;
}else{



var user_role = ($user_type_id == '' ? '' : $user_type_id);

if(user_role == '2')
{

toastr.error('Only employer can buy these packages.');
return false;

}

var user_role = ($user_type_id == '' ? '' : $user_type_id);
if(user_role == '2')
{
toastr.error('Only employer can buy these packages.');
return false;
}else
{

$('form').submit();
}


}
});
});
</script>