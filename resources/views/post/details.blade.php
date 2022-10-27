{{--
* JobClass - Job Board Web Application
* Copyright (c) BedigitCom. All Rights Reserved
*
* Website: http://www.bedigit.com
*
* LICENSE
* -------
* This software is furnished under a license and may be used and copied
* only in accordance with the terms of such license and with the inclusion
* of the above copyright notice. If you Purchased from Codecanyon,
* Please read the full License from here - http://codecanyon.net/licenses/standard
--}}
@extends('layouts.master')

@section('content')
{!! csrf_field() !!}
<input type="hidden" id="postId" name="post_id" value="{{ $post->id }}">

@if(Session::has('errormessage'))
<div class="alert alert-danger" style="text-align: center;">{{ Session::get('errormessage') }}</div>
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
<?php Session::forget('flash_notification.message'); ?>
@endif

<div class="main-container">

<?php if (\App\Models\Advertising::where('slug', 'top')->count() > 0): ?>
@include('layouts.inc.advertising.top', ['paddingTopExists' => (isset($paddingTopExists)) ? $paddingTopExists : false])
<?php
$paddingTopExists = false;
endif;
?>
@include('common.spacer')

<div class="container">
<div class="row">
<div class="col-md-12">

<nav aria-label="breadcrumb" role="navigation" class="pull-left">
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ lurl('/') }}"><i class="icon-home fa"></i></a></li>
<li class="breadcrumb-item"><a href="{{ lurl('/') }}">{{ config('country.name') }}</a></li>
@if (!empty($post->category->parent))
<li class="breadcrumb-item">
<a href="{{ \App\Helpers\UrlGen::category($post->category->parent) }}">
{{ $post->category->parent->name }}
</a>
</li>
@if ($post->category->parent->id != $post->category->id)
<li class="breadcrumb-item">
<a href="{{ \App\Helpers\UrlGen::category($post->category, 1) }}">
{{ $post->category->name }}
</a>
</li>
@endif
@else
<li class="breadcrumb-item">
<a href="{{ \App\Helpers\UrlGen::category($post->category) }}">
{{ $post->category->name }}
</a>
</li>
@endif
<li class="breadcrumb-item active">{{ \Illuminate\Support\Str::limit($post->title, 70) }}</li>
</ol>
</nav>

<div class="pull-right backtolist">
<a href="{{ rawurldecode(url()->previous()) }}"><i class="fa fa-angle-double-left"></i> {{ t('Back to Results') }}</a>
</div>

</div>
</div>
</div>

<div class="container">
<div class="row">
<div class="col-lg-9 page-content col-thin-right">
<div class="inner inner-box items-details-wrapper pb-0">
<h2 class="enable-long-words">
<strong>
<a href="{{ \App\Helpers\UrlGen::post($post) }}" title="{{ $post->title }}">
{{ $post->title }}
</a>
</strong>
<small class="label label-default adlistingtype">{{ t(':type Job', ['type' => $post->postType->name]) }}</small>
@if ($post->featured==1 and !empty($post->latestPayment))
@if (isset($post->latestPayment->package) and !empty($post->latestPayment->package))
<i class="icon-ok-circled tooltipHere" style="color: {{ $post->latestPayment->package->ribbon }};" title="" data-placement="right"
data-toggle="tooltip" data-original-title="{{ $post->latestPayment->package->short_name }}"></i>
@endif
@endif
</h2>
<span class="info-row">
<span class="date"><i class=" icon-clock"> </i> {{ $post->created_at_ta }} </span> -&nbsp;
<span class="category">{{ (!empty($post->category->parent)) ? $post->category->parent->name : $post->category->name }}</span> -&nbsp;
<span class="item-location"><i class="fas fa-map-marker-alt"></i> {{ $post->city->name }} </span> -&nbsp;
<span class="category">
<i class="icon-eye-3"></i>&nbsp;
{{ \App\Helpers\Number::short($post->visits) }} {{ trans_choice('global.count_views', getPlural($post->visits)) }}
</span>
</span>

<div class="items-details">
<div class="row pb-4">
<div class="items-details-info jobs-details-info col-md-8 col-sm-12 col-xs-12 enable-long-words from-wysiwyg">
<h5 class="list-title"><strong>{{ t('Job Details') }}</strong></h5>

<!-- Description -->
<div>
{!! transformDescription($post->description) !!}
</div>

@if (!empty($post->company_description))
<!-- Company Description -->
<h5 class="list-title mt-5"><strong>{{ t('Company Description') }}</strong></h5>
<div>
{!! nl2br(createAutoLink(strCleaner($post->company_description))) !!}
</div>
@endif

@if (!empty($post->required_influencer))
<!-- Influencer Required -->
<h5 class="list-title mt-5"><strong>Influencer Requirements</strong></h5>
<div><b>When applying you must meet the following criteria: </b>

<p></p><strong>{{ t('Influencer Gender') }}:</strong> {!! nl2br(createAutoLink(strCleaner($post->influencer_gender))) !!}</p>

<p><strong>{{ t('Influencer Age') }}:</strong> @if($post->influencer_age == 'any')
{!! nl2br(createAutoLink(strCleaner('Any'))) !!}
@elseif($post->influencer_age == 'less_25')
{!! nl2br(createAutoLink(strCleaner('Bellow Age of 25'))) !!}
@elseif($post->influencer_age == 'less_40')
{!! nl2br(createAutoLink(strCleaner('Bellow Age of 40'))) !!}
@elseif($post->influencer_age == 'less_50')
{!! nl2br(createAutoLink(strCleaner('Bellow Age of 50'))) !!}
@elseif($post->influencer_age == 'great_50')
{!! nl2br(createAutoLink(strCleaner('Above 50'))) !!}
@else
{!! nl2br(createAutoLink(strCleaner(''))) !!}
@endif

</p>


</div>


<h5 class="list-title mt-5"><strong>{{ t('Social Media') }}</strong></h5>
<div>
<label><i class="icon-instagram-filled"></i> Instagram:</label>
{!! nl2br(createAutoLink(strCleaner($post->instagram_followers))) !!}
</div>
<div>
<label><i class="icon-facebook-rect"></i>Facebook:</label>
{!! nl2br(createAutoLink(strCleaner($post->facebook_likes))) !!}
</div>
<div>
<label><i class="icon-twitter"></i>Twitter:</label>
{!! nl2br(createAutoLink(strCleaner($post->twitter_followers))) !!}
</div>
<div>
<label><i class="icon-play-circled"></i>Youtube:</label>
{!! nl2br(createAutoLink(strCleaner($post->youtube_subscribers))) !!}
</div>
<div>
<label><i class="icon-pencil"></i>Quora:</label>
{!! nl2br(createAutoLink(strCleaner($post->quora_followers))) !!}
</div>
<div>
<label><i class="icon-globe"></i>Website/Blog:</label>
{!! nl2br(createAutoLink(strCleaner($post->web_followers))) !!}
</div>
<div>
@if($post->document!='')
<label>Project Brief Document/Image:</label>
<a href="{{ URL::to('/') }}/document/{{$post->document}}" download>download file</a>
@else 
<p>No Attachment</p>
@endif
</div>

@endif

<!-- Tags -->
@if (!empty($post->tags))
<?php $tags = array_map('trim', explode(',', $post->tags)); ?>
@if (!empty($tags))
<div style="clear: both;"></div>
<div class="tags">
<h5 class="list-title"><strong>{{ t('Tags') }}</strong></h5>
@foreach($tags as $iTag)
<a href="{{ \App\Helpers\UrlGen::tag($iTag) }}">
{{ $iTag }}
</a>
@endforeach
</div>
@endif
@endif
</div>

<div class="col-md-4 col-sm-12 col-xs-12">
<aside class="panel panel-body panel-details job-summery">

<p class="no-margin">
<strong>{{ t('Salary') }}:</strong>&nbsp;
<h3>
@if ($post->salary_min > 0 or $post->salary_max > 0)
@if ($post->salary_min > 0)
{!! \App\Helpers\Number::money($post->salary_min) !!}
@endif
@if ($post->salary_max > 0)
@if ($post->salary_min > 0)
&nbsp;-&nbsp;
@endif
{!! \App\Helpers\Number::money($post->salary_max) !!}</h3>
@endif
@else
{!! \App\Helpers\Number::money('--') !!}
@endif
@if (!empty($post->salaryType))
{{ t('per') }} {{ $post->salaryType->name }}
@endif

@if ($post->negotiable == 1)
<small class="label badge-success"> {{ t('Negotiable') }}</small>
@endif

</p>


<ul>
@if (!empty($post->start_date))
<li>
<p class="no-margin">
<strong>{{ t('Start Date') }}:</strong>&nbsp;
{{ $post->start_date }}
</p>
</li>
@endif
<li>
<p class="no-margin">
<strong>{{ t('Company') }}:</strong>&nbsp;
@if (!empty($post->company_id))
<?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->company_id]; ?>
<a href="{!! lurl(trans('routes.v-search-company', $attr), $attr) !!}">
{{ $post->company_name }}
</a>
@else
{{ $post->company_name }}
@endif
</p>
</li>

<li>
<?php
$postType = \App\Models\PostType::findTrans($post->post_type_id);
?>
@if (!empty($postType))
<p class="no-margin">
<strong>{{ t('Job Type') }}:</strong>&nbsp;
<?php $attr = ['countryCode' => config('country.icode')]; ?>
<a href="{{ lurl(trans('routes.v-search', $attr), $attr) . '?type[]=' . $post->post_type_id }}">
{{ $postType->name }}
</a>
</p>
@endif
</li>
<li>
<p class="no-margin">
<strong>{{ t('Location') }}:</strong>&nbsp;
<a href="{!! \App\Helpers\UrlGen::city($post->city) !!}">
{{ $post->city->name }}
</a>
</p>
</li>
<li>
<p class="no-margin">
<strong>{{ t('Influencer required') }}:</strong>&nbsp;
{!! nl2br(createAutoLink(strCleaner($post->required_influencer))) !!}
</p>
</li>
</ul>
</aside>

<div class="ads-action">
<ul class="list-border">
@if (isset($post->company) and !empty($post->company))
<li>
<?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->company->id]; ?>
<a href="{{ lurl(trans('routes.v-search-company', $attr), $attr) }}">
<i class="fa icon-town-hall"></i> {{ t('More jobs by :company', ['company' => $post->company->name]) }}
</a>
</li>
@endif
@if (isset($user) and !empty($user))
<li>
<a href="{{ \App\Helpers\UrlGen::user($user) }}">
<i class="fa fa-user"></i> {{ t('More jobs by :user', ['user' => $user->name]) }}
</a>
</li>
@endif
<li id="{{ $post->id }}">
<a class="make-favorite" href="javascript:void(0)">
@if (auth()->check())
@if (\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() > 0)
<i class="fa fa-heart"></i> {{ t('Saved Job') }}
@else
<i class="far fa-heart"></i> {{ t('Save Job') }}
@endif
@else
<i class="far fa-heart"></i> {{ t('Save Job') }}
@endif
</a>
</li>
<li>
<a href="{{ lurl('posts/' . $post->id . '/report') }}">
<i class="fa icon-info-circled-alt"></i> {{ t('Report abuse') }}
</a>
</li>
</ul>
</div>
</div>
</div>

<div class="content-footer text-left">
@if (auth()->check())
@if (auth()->user()->id == $post->user_id)
<a class="btn btn-default" href="{{ \App\Helpers\UrlGen::editPost($post) }}">
<i class="fa fa-pencil-square-o"></i> {{ t('Edit') }}
</a>
@else
@if (in_array(auth()->user()->user_type_id, [2]))
{!! genEmailContactBtn($post) !!}
@endif
@endif
@else
{!! genEmailContactBtn($post) !!}
@endif
&nbsp;<small><?php /* or. Send your CV to: foo@bar.com */ ?></small>
</div>
</div>
</div>
<!--/.items-details-wrapper-->
</div>
<!--/.page-content-->

<div class="col-lg-3 page-sidebar-right">
<aside>
<div class="card sidebar-card card-contact-seller">
<div class="card-header">{{ t('Company Information') }}</div>
<div class="card-content user-info">
<div class="card-body text-center">
<div class="seller-info">
<div class="company-logo-thumb mb20">
@if (isset($post->company) and !empty($post->company))
<?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->company->id]; ?>
<a href="{{ lurl(trans('routes.v-search-company', $attr), $attr) }}">
<img alt="Logo {{ $post->company_name }}" class="img-fluid" src="{{ imgUrl($post->logo, 'big') }}">
</a>
@else
<img alt="Logo {{ $post->company_name }}" class="img-fluid" src="{{ imgUrl($post->logo, 'big') }}">
@endif
</div>
@if (isset($post->company) and !empty($post->company))
<h3 class="no-margin">
<?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->company->id]; ?>
<a href="{{ lurl(trans('routes.v-search-company', $attr), $attr) }}">
{{ $post->company->name }}
</a>
</h3>
@else
<h3 class="no-margin">{{ $post->company_name }}</h3>
@endif
<p>
{{ t('Location') }}:&nbsp;
<strong>
<a href="{!! \App\Helpers\UrlGen::city($post->city) !!}">
{{ $post->city->name }}
</a>
</strong>
</p>
@if (isset($user) and !empty($user) and !empty($user->created_at_ta))
<p> {{ t('Joined') }}: <strong>{{ $user->created_at_ta }}</strong></p>
@endif
@if (isset($post->company) and !empty($post->company))
@if (!empty($post->company->website))
<p>
{{ t('Web') }}:
<strong>
<a href="{{ $post->company->website }}" target="_blank" rel="nofollow">
{{ getHostByUrl($post->company->website) }}
</a>
</strong>
</p>
@endif
@endif
</div>
<div class="user-ads-action" id="placeBidButton">
@if (auth()->check())
@if (auth()->user()->id == $post->user_id)
<a href="{{ \App\Helpers\UrlGen::editPost($post) }}" class="btn btn-default btn-block">
<i class="fa fa-pencil-square-o"></i> {{ t('Update the Details') }}
</a>
@if (config('settings.single.publication_form_type') == '1')
@if (isset($countPackages) and isset($countPaymentMethods) and $countPackages > 0 and $countPaymentMethods > 0)
<a href="{{ lurl('posts/' . $post->id . '/payment') }}" class="btn btn-success btn-block">
<i class="icon-ok-circled2"></i> {{ t('Make It Premium') }}
</a>
@endif
@endif
@else
@if (in_array(auth()->user()->user_type_id, [2]))
{!! genEmailContactBtn($post, true) !!}
@endif
@endif
<?php
try {
if (auth()->user()->can(\App\Models\Permission::getStaffPermissions())) {
$btnUrl = admin_url('blacklists/add') . '?email=' . $post->email;

if (!isDemo($btnUrl)) {
$cMsg = trans('admin::messages.confirm_this_action');
$cLink = "window.location.replace('" . $btnUrl . "'); window.location.href = '" . $btnUrl . "';";
$cHref = "javascript: if (confirm('" . addcslashes($cMsg, "'") . "')) { " . $cLink . " } else { void('') }; void('')";

$btnText = trans("admin::messages.ban_the_user");
$btnHint = trans("admin::messages.ban_the_user_email", ['email' => $post->email]);
$tooltip = ' data-toggle="tooltip" title="' . $btnHint . '"';

$btnOut = '';
$btnOut .= '<a href="'. $cHref .'" class="btn btn-danger btn-block"'. $tooltip .'>';
$btnOut .= $btnText;
$btnOut .= '</a>';

echo $btnOut;
}
}
} catch (\Exception $e) {}
?>
@else

@endif
</div>
</div>
</div>
</div>

@if (config('settings.single.show_post_on_googlemap'))
<div class="card sidebar-card">
<div class="card-header">{{ t('Location\'s Map') }}</div>
<div class="card-content">
<div class="card-body text-left p-0">
<div class="ads-googlemaps">
<iframe id="googleMaps" width="100%" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src=""></iframe>
</div>
</div>
</div>
</div>
@endif

@if (isVerifiedPost($post))
@include('layouts.inc.social.horizontal')
@endif

<div class="card sidebar-card">
<div class="card-header">{{ t('Tips for candidates') }}</div>
<div class="card-content">
<div class="card-body text-left">
<ul class="list-check">
<li> {{ t('Check if the offer matches your profile') }} </li>
<li> {{ t('Check the start date') }} </li>
<li> {{ t('Meet the employer in a professional location') }} </li>
</ul>
<?php $tipsLinkAttributes = getUrlPageByType('tips'); ?>
@if (!\Illuminate\Support\Str::contains($tipsLinkAttributes, 'href="#"') and !\Illuminate\Support\Str::contains($tipsLinkAttributes, 'href=""'))
<p>
<a class="pull-right" {!! $tipsLinkAttributes !!}>
{{ t('Know more') }}
<i class="fa fa-angle-double-right"></i>
</a>
</p>
@endif
</div>
</div>
</div>
</aside>
</div>
</div>

</div>

@if (config('settings.single.similar_posts') == '1' || config('settings.single.similar_posts') == '2')
@include('home.inc.featured', ['firstSection' => false])
@endif

@include('layouts.inc.advertising.bottom', ['firstSection' => false])

@if (isVerifiedPost($post))
@include('layouts.inc.tools.facebook-comments', ['firstSection' => false])
@endif

</div>
@endsection

@section('modal_message')
@if (auth()->check() or config('settings.single.guests_can_contact_ads_authors')=='1')
@include('post.inc.compose-message')
@endif
@endsection

@section('after_styles')
@endsection

@section('after_scripts')
@if (config('services.googlemaps.key'))
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemaps.key') }}" type="text/javascript"></script>
@endif

<script>
/* Favorites Translation */
var lang = {
labelSavePostSave: "{!! t('Save Job') !!}",
labelSavePostRemove: "{{ t('Saved Job') }}",
loginToSavePost: "{!! t('Please log in to save the Ads.') !!}",
loginToSaveSearch: "{!! t('Please log in to save your search.') !!}",
confirmationSavePost: "{!! t('Post saved in favorites successfully !') !!}",
confirmationRemoveSavePost: "{!! t('Post deleted from favorites successfully !') !!}",
confirmationSaveSearch: "{!! t('Search saved successfully !') !!}",
confirmationRemoveSaveSearch: "{!! t('Search deleted successfully !') !!}"
};

$(document).ready(function () {
@if (config('settings.single.show_post_on_googlemap'))
/* Google Maps */
getGoogleMaps(
'{{ config('services.googlemaps.key') }}',
'{{ (isset($post->city) and !empty($post->city)) ? addslashes($post->city->name) . ',' . config('country.name') : config('country.name') }}',
'{{ config('app.locale') }}'
);
@endif


// AMOUNT CHECK 

$(document).on('blur','#bid_amount',function(event){
event.preventDefault();

var minimum_budget = parseInt($('#Minimum_Budget_Check').html());

var maximum_budget = parseInt($('#Maximum_Budget_Check').html());

var budget_value = parseInt($(this).val());

if(budget_value < minimum_budget)
{

toastr.error('Your bid amount is less than Estimated Budget!');

}

if(budget_value > maximum_budget)
{

toastr.error('Your bid amount is more than Estimated Budget!');

}


});
// AMOUNT CHECK 

/////////// APPLY ONLINE JOB /////////////
$(document).on('click','#ApplyOnlineJob',function(event){
event.preventDefault();
var base_url = '<?= URL::to('/');?>';
var loggedin_userid = '<?= $loggedin_userid; ?>';
if(loggedin_userid == ''){
var session_flag = '<?php Session::put('RedirectionFlagInfluencer', url()->current());?>';
toastr.info('Redirecting.. Login to continue');
window.location.href = base_url;
}else{

$.ajax({
url: "/check_influencer_packages",
cache: false,
success: function(data){
var obj = JSON.parse(data);

if(obj == 1){
$('#applyJob').modal('show');
}else{
bootbox.alert("You dont have any active package. <a href='/influencer-packages' class='btn btn-primary bootbox-accept'>Buy Package</a>");
}
}
});

}
});

/////////// APPLY ONLINE JOB /////////////

/// BUY PACKAGE CLICK
$(document).on('click','a.btn.btn-primary.bootbox-accept',function(event)
{
event.preventDefault();
var page_url = '/influencer-packages';
var session_flag = '<?php Session::put('RedirectionFlagInfluencer', url()->current());?>';
window.location.href = page_url;
});

/// BUY PACKAGE CLICK
});


</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
@endsection