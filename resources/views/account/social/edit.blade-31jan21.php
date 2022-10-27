{{--

--}}
@extends('layouts.master')

@section('content')
@include('common.spacer')

<style>
.bootstrap-tagsinput {
background-color: #fff;
border: 1px solid #ccc;
box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
display: inline-block;
padding: 4px 6px;
color: #555;
vertical-align: middle;
border-radius: 4px;
max-width: 100%;
line-height: 22px;
cursor: text;
}
.bootstrap-tagsinput input {
border: none;
box-shadow: none;
outline: none;
background-color: transparent;
padding: 0 6px;
margin: 0;
width: auto;
max-width: inherit;
}
.bootstrap-tagsinput.form-control input::-moz-placeholder {
color: #777;
opacity: 1;
}
.bootstrap-tagsinput.form-control input:-ms-input-placeholder {
color: #777;
}
.bootstrap-tagsinput.form-control input::-webkit-input-placeholder {
color: #777;
}
.bootstrap-tagsinput input:focus {
border: none;
box-shadow: none;
}
.bootstrap-tagsinput .tag {
margin-right: 2px;
color: red;
}
.bootstrap-tagsinput .tag [data-role="remove"] {
margin-left: 8px;
cursor: pointer;
}
.bootstrap-tagsinput .tag [data-role="remove"]:after {
content: "x";
padding: 0px 2px;
}

.slim .slim-btn-group {
position: absolute;
right: 0;
bottom: 53px !important;
left: 0;
z-index: 3;
pointer-events: none;
}
button.btn.btn-mwc.slim_custom_upload_btn {
margin: 0 0 0 65px !important;
}

.conterimg {
    text-align: center;
    box-shadow: 0px 0px 5px #b3b1b1;
    border: 1px solid transparent;
    transition: 0.9s;
    padding-bottom: 12px;
    margin-bottom: 30px;
}

.conterimg:hover{
border: 1px solid #7324bc;
}
.conterimg a {
padding: 6px;
display: inline-block;
margin: 0px;
width: 120px;
background: #7324bc;
margin-bottom: 5px;
border-radius: 20px;
margin-top: 2px;
color: #ffff;
}

input.portfolio_submit {
width: 150px;
margin-left: 50%;
transform: translateX(-50%);
margin-bottom: 10px;
border-radius: 14px;
background: #7324bc!important;
padding: 5px;
color: #ffff;
border: none;
}

.modal-footer button {
background: #7324bc!important;
}

.modal-header {
background: #7324bc!important;
color: #fff!important;

}

.conterimg img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}
</style>

<div class="main-container">
<div class="container">
<div class="row">
<div class="col-md-3 page-sidebar">
@include('account.inc.sidebar')
</div>
<!--/.page-sidebar-->

<div class="col-md-9 page-content">

@include('flash::message')

@if (isset($errors) and $errors->any())
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h5><strong>{{ t('Oops ! An error has occurred. Please correct the red fields in the form') }}</strong></h5>
	<ul class="list list-check">
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

<div class="inner-box default-inner-box">
	<div class="welcome-msg">
		<h3 class="page-sub-header2 clearfix no-padding">{{ t('Hello') }} {{ $user->name }} ! </h3>
		<span class="page-sub-header-sub small">
			{{ t('You last logged in at') }}: {{ $user->last_login_at->formatLocalized(config('settings.app.default_datetime_format')) }}
		</span>
	</div>

	<div id="accordion" class="panel-group">
		<!-- USER -->
		<div class="card card-default">
			<div class="card-header">
				<h4 class="card-title"><a href="#userPanel" data-toggle="collapse" data-parent="#accordion">{{ t('Profile Details') }}</a></h4>
			</div>
			<div class="panel-collapse collapse {{ (old('panel')=='' or old('panel')=='userPanel') ? 'show' : '' }}" id="userPanel">
				<div class="card-body">
					<form name="details" class="form-horizontal" role="form" method="POST" action="{{url('account/socialprofile/create') }}" enctype="multipart/form-data">
						{!! csrf_field() !!}
						@if (empty($user->user_type_id) or $user->user_type_id == 0)

						<!-- user_type_id -->
						<?php $userTypeIdError = (isset($errors) and $errors->has('user_type_id')) ? ' is-invalid' : ''; ?>
						<div class="form-group row required">
							<label class="col-md-3 col-form-label{{ $userTypeIdError }}">{{ t('You are a') }} <sup>*</sup></label>
							<div class="col-md-9">
								<select name="user_type_id" id="userTypeId" class="form-control selecter{{ $userTypeIdError }}">
									<option value="0"
									@if (old('user_type_id')=='' or old('user_type_id')==0)
									selected="selected"
									@endif
									>
									{{ t('Select') }}
								</option>
								@foreach ($userTypes as $type)
								<option value="{{ $type->id }}"
									@if (old('user_type_id', $user->user_type_id)==$type->id)
									selected="selected"
									@endif
									>
									{{ t($type->name) }}
								</option>
								@endforeach
							</select>
						</div>
					</div>

					@else



					<!-- bio -->
					<input type="hidden" name="user_id" value="{{$user->id}}">
					<?php $bioError = (isset($errors) and $errors->has('bio')) ? ' is-invalid' : ''; ?>
					<div class="form-group row required">
						<label class="col-md-3 col-form-label">{{ t('Profile Bio') }} <sup>*</sup></label>
						<div class="col-md-9">
							<textarea class="form-control{{ $bioError }}" name="profile_bio">{{ old('bio', isset($details->biodata)? $details->biodata:'') }}</textarea>
						</div>
					</div>
					<!-- parent_id -->
					<?php $parentIdError = (isset($errors) and $errors->has('category_id')) ? ' is-invalid' : ''; ?>
					<div class="form-group row required">
						<label class="col-md-3 col-form-label{{ $parentIdError }}">{{ t('Category')}} <sup>*</sup></label>
						<div class="col-md-8">
							<select name="parent_id" id="parentId" class="form-control selecter{{ $parentIdError }}">
								<option value="0" data-type=""
								@if (old('parent_id')=='' or old('parent_id')==0)
								selected="selected"
								@endif
								> {{ t('Select a category') }} </option>
								
								@foreach ($categories as $cat)
								<option value="{{ $cat->tid }}" data-type="{{ $cat->type }}"
									<?php if (isset($details->category_id)  &&  $details->category_id!='' && $details->category_id== $cat->tid){?>
								selected="selected"
								<?php }?>
									> {{ $cat->name }} </option>
									@endforeach
									
								</select>
								<input type="hidden" name="parent_type" id="parent_type" value="{{ old('parent_type') }}">
							</div>
						</div>

						<!-- category_id -->
						<?php $categoryIdError = (isset($errors) and $errors->has('category_id')) ? ' is-invalid' : ''; ?>
						<div id="subCatBloc" class="form-group row required">
							<label class="col-md-3 col-form-label{{ $categoryIdError }}">{{ t('Sub-Category') }} <sup>*</sup></label>
							<div class="col-md-8">
								@if(isset($details->category_id))
								<select name="category_id" id="categoryId" class="form-control selecter{{ $categoryIdError }}">
									<option value="0"
									@if (old('category_id')== isset($details->category_id)? $details->category_id:'')
									selected="selected"
									@endif
									> {{ t('Select a sub-category') }} </option>
								</select>
								@endif
							</div>
						</div>
						<!-- age -->
						<?php $ageError = (isset($errors) and $errors->has('age')) ? ' is-invalid' : ''; ?>
						<div class="form-group row required">
							<label class="col-md-3 col-form-label" for="">{{ t('Age') }} <sup>*</sup></label>
							<div class="input-group col-md-9">
								<input id=""
								name="age"
								type="text"
								class="form-control{{ $ageError }}"
								placeholder="{{ t('Age') }}"
								value="{{ old('age', isset($details->age)? $details->age:'' ) }}"
								required=""
								>
							</div>
						</div>

						<!-- DOB -->
						<?php $dobError = (isset($errors) and $errors->has('dob')) ? ' is-invalid' : ''; ?>
						<div class="form-group row required">
							<label class="col-md-3 col-form-label" for="">{{ t('DOB') }} <sup>*</sup></label>
							<div class="input-group col-md-9">
								<input id=""
								name="dob"
								type="date"
								class="form-control{{ $dobError }}"
								placeholder="{{ t('Date of Birth') }}"
								value="{{ old('dob', isset($details->dob)?$details->dob:'') }}"
								required="" 
								>
							</div>
						</div>

						<!-- Gender -->
						<?php $genderError = (isset($errors) and $errors->has('gender')) ? ' is-invalid' : ''; ?>
						<div class="form-group row required">
							<label class="col-md-3 col-form-label" for="">{{ t('Gender') }} <sup>*</sup></label>
							<div class="input-group col-md-9">
								<select class="form-control{{ $genderError }}" required="">
									<option value="male" {{isset($details) && $details->gender == 'male' ? 'selected' : ''}}>{{ t('Male') }}</option>
									<option value="felmale" {{isset($details) && $details->gender == 'female' ? 'selected' : ''}}>{{ t('Female') }}</option>
								</select>
							</div>
						</div>
						<!-- city_id -->
						<input type="hidden" name="pareent_cityid" value="@if(isset($details->city_id)){{$details->city_id}}@endif">
						<?php $cityIdError = (isset($errors) and $errors->has('city_id')) ? ' is-invalid' : ''; ?>
						<div id="cityBox" class="form-group row required">
							<label class="col-md-3 col-form-label{{ $cityIdError }}" for="city_id">
								{{ t('City') }} <sup>*</sup>
							</label>
							<div class="input-group col-md-9">
								<select id="cityId" name="city_id" class="form-control sselecter{{ $cityIdError }}" required="">
									<option value="0" {{ (!old('city_id') or old('city_id')==0) ? 'selected="selected"' : '' }}>
										{{ t('Select a city') }}
									</option>
								</select>
							</div>
						</div>
						<!-- min fee -->
						<?php $min_feeError = (isset($errors) and $errors->has('min_fee')) ? ' is-invalid' : ''; ?>
						<div class="form-group row required">
							<label class="col-md-3 col-form-label" for="">{{ t('Minimum Fee') }} <sup>*</sup></label>
							<div class="input-group col-md-9">
								<input id=""
								name="min_fee"
								type="text"
								class="form-control{{ $min_feeError }}"
								placeholder="{{ t('Minimum Fee') }}"
								value="{{ old('min_fee', isset($details->min_fee) ? $details->min_fee : '') }}"
								required="" 
								>
							</div>
						</div>
						<!-- facebook -->
						<?php $companyFacebookError = (isset($errors) and $errors->has('facebook_url')) ? ' is-invalid' : ''; ?>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="facebook_url">Facebook Url</label>
							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="icon-facebook_url"></i></span>
								</div>
								<input name="facebook_url" type="text"
								class="form-control{{ $companyFacebookError }}" placeholder=""
								value="{{ old('facebook_url', (isset($details->facebook_url) ? $details->facebook_url : 'https://')) }}">
							</div>
							<div class="input-group col-md-2">
								<input id=""
								name="facebook_followers"
								type="number"
								class="form-control{{ $companyFacebookError }}"
								value="{{ isset($details->facebook_followers) ? substr($details->facebook_followers, 0, -1) : '' }}"
								
								>
							</div>
							<div class="input-group col-md-2">
								<select name="facebook_unit" id="" class="form-control">
									<option value="K" {{ isset($details->facebook_followers) && substr($details->facebook_followers, -1) == 'K'  ? 'selected' : '' }}>K</option>
									<option value="M" {{ isset($details->facebook_followers) && substr($details->facebook_followers, -1) == 'M'  ? 'selected' : '' }}>M</option>
								</select>
							</div>
						</div>

						<!-- twitter -->
						<?php $companyTwitterError = (isset($errors) and $errors->has('twitter')) ? ' is-invalid' : ''; ?>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="twitter_url">Twitter Url</label>
							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="icon-twitter"></i></span>
								</div>
								<input name="twitter_url" type="text"
								class="form-control{{ $companyTwitterError }}" placeholder=""
								value="{{ old('twitter_url', (isset($details->twitter_url) ? $details->twitter_url : 'https://')) }}">
							</div>
							<div class="input-group col-md-2">
								<input id=""
								name="twitter_followers"
								type="number"
								class="form-control{{ $companyTwitterError }}"
								value="{{ isset($details->twitter_followers) ? substr($details->twitter_followers, 0, -1) : '' }}"
								
								>
							</div>
							<div class="input-group col-md-2">
								<select name="twitter_unit" id="" class="form-control">
									<option value="K" {{ isset($details->twitter_followers) && substr($details->twitter_followers, -1) == 'K'  ? 'selected' : '' }}>K</option>
									<option value="M" {{ isset($details->twitter_followers) && substr($details->twitter_followers, -1) == 'M'  ? 'selected' : '' }}>M</option>
								</select>
							</div>
						</div>
						<!-- youtube -->
						<?php $companyYoutubeError = (isset($errors) and $errors->has('youtube_url')) ? ' is-invalid' : ''; ?>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="youtube_url">Youtube Url</label>
							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="icon-youtube"></i></span>
								</div>
								<input name="youtube_url" type="text"
								class="form-control{{ $companyYoutubeError }}" placeholder=""
								value="{{ old('youtube_url', (isset($details->youtube_url) ? $details->youtube_url : 'https://')) }}" style="height: 50px;padding: .5rem .75rem;font-size: .85rem;color: #464a4c;background-color: #fff;background-image: none;background-clip: padding-box;border: 1px solid rgba(0,0,0,.15);border-radius: .2rem;">
							</div>
							<div class="input-group col-md-2">
								<input id=""
								name="youtube_suscribers"
								type="number"
								class="form-control{{ $companyYoutubeError }}"
								value="{{ isset($details->youtube_subscribers) ? substr($details->youtube_subscribers, 0, -1) : '' }}"
								
								>
							</div>
							<div class="input-group col-md-2">
								<select name="youtube_unit" id="" class="form-control{{ $companyYoutubeError }}">
									<option value="K" {{ isset($details->youtube_subscribers) && substr($details->youtube_subscribers, -1) == 'K'  ? 'selected' : '' }}>K</option>
									<option value="M" {{ isset($details->youtube_subscribers) && substr($details->youtube_subscribers, -1) == 'M'  ? 'selected' : '' }}>M</option>
								</select>
							</div>
						</div>
						<!-- tiktok -->
						<?php $companyInstagramError = (isset($errors) and $errors->has('instagram_url')) ? ' is-invalid' : ''; ?>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="instagram_url">Instgram Url</label>
							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="icon-tiktok"></i></span>
								</div>
								<input name="instagram_url" type="text"
								class="form-control{{ $companyInstagramError }}" placeholder=""
								value="{{ old('instagram_url', (isset($details->instagram_url) ? $details->instagram_url : 'https://')) }}">
							</div>
							<div class="input-group col-md-2">
								<input id=""
								name="instagram_followers"
								type="number"
								class="form-control{{ $companyInstagramError }}"
								value="{{ isset($details->instagram_followers) ? substr($details->instagram_followers, 0, -1) : '' }}"
								
								>
							</div>
							<div class="input-group col-md-2">
								<select name="instagram_unit" id="" class="form-control{{ $companyInstagramError }}">
									<option value="K" {{ isset($details->instagram_followers) && substr($details->instagram_followers, -1) == 'K'  ? 'selected' : '' }}>K</option>
									<option value="M" {{ isset($details->instagram_followers) && substr($details->instagram_followers, -1) == 'M'  ? 'selected' : '' }}>M</option>
								</select>
							</div>
						</div>
						<!-- quora -->
						<?php $companyquoraError = (isset($errors) and $errors->has('quora_url')) ? ' is-invalid' : ''; ?>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="quora_url">quora</label>
							<div class="input-group col-md-5">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="icon-quora"></i></span>
								</div>
								<input name="quora_url" type="text"
								class="form-control{{ $companyquoraError }}" placeholder=""
								value="{{ old('quora_url', (isset($details->quora_url) ? $details->quora_url : 'https://')) }}">
							</div>
							<div class="input-group col-md-2">
								<input id=""
								name="quora_followers"
								type="number"
								class="form-control{{ $companyquoraError }}"
								value="{{ isset($details->quora_followers) ? substr($details->quora_followers, 0, -1) : '' }}"
								
								>
							</div>
							<div class="input-group col-md-2">
								<select name="quora_unit" id="" class="form-control{{ $companyquoraError }}">
									<option value="K" {{ isset($details->quora_followers) && substr($details->quora_followers, -1) == 'K'  ? 'selected' : '' }}>K</option>
									<option value="M" {{ isset($details->quora_followers) && substr($details->quora_followers, -1) == 'M'  ? 'selected' : '' }}>M</option>
								</select>
							</div>
						</div>
						@endif

						<!-- ADD SKILLS -->
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="quora_url">Skills/Expertise</label>
							<div class="input-group col-md-5">
								<input type="text"  value="<?php if(isset($details->skill_expertise) && !empty($details->skill_expertise)):?><?= $details->skill_expertise;?><?php endif;?>" data-role="tagsinput" name="skill_expertise" placeholder="Add skills"/>
							</div>

						</div>
						<!-- ADD SKILLS -->

						<!-- ADD CLIENTS -->
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="quora_url">Clients</label>
							<div class="input-group col-md-5">
								<input type="text"  value="<?php if(isset($details->client_base) && !empty($details->client_base)):?><?= $details->client_base;?><?php endif;?>" data-role="tagsinput" name="client_base" placeholder="Add Clients"/>
							</div>

						</div>
						<!-- ADD SKILLS -->

						<!-- ADD SKILLS -->

						<!-- ADD SKILLS -->


						<!----PROFILE PIC UPLOAD (WEBC)---->
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="quora_url">Profile Image</label>
							<div class="input-group col-md-5">
								<?php if(isset($details->profile_image)):?>
								<img src="/public/images/profile_images/<?= $details->profile_image;?>" width="50" height="50" style="margin: 2%;">
							<?php endif;?>
								<input type="file" name="profile_image" id="profile_image"/>
							</div>

						</div>

						<!----PROFILE PIC UPLOAD (WEBC)---->

						<div class="form-group row">
							<div class="offset-md-3 col-md-9"></div>
						</div>

						<!-- Button -->
						<div class="form-group row">
							<div class="offset-md-3 col-md-9">
								<button type="submit" class="btn btn-primary">{{ t('Update') }}</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		
                 

				</div>
				<!--/.row-box End-->
			</div>
		</div>
		<!--/.page-content-->
	</div>
	<!--/.row-->
</div>
<!--/.container-->
</div>
<!-- /.main-container -->
@endsection

@section('after_styles')
<link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
@if (config('lang.direction') == 'rtl')
<link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput-rtl.min.css') }}" rel="stylesheet">
@endif
<style>
.krajee-default.file-preview-frame:hover:not(.file-preview-error) {
	box-shadow: 0 0 5px 0 #666666;
}
</style>
@endsection

@section('after_scripts')
<script src="https://cdn.jsdelivr.net/bootbox/4.4.0/bootbox.min.js"></script>
<script src="{{ url('assets/plugins/bootstrap-fileinput/js/plugins/sortable.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/plugins/bootstrap-fileinput/themes/fa/theme.js') }}" type="text/javascript"></script>
@if (file_exists(public_path() . '/assets/plugins/bootstrap-fileinput/js/locales/'.ietfLangTag(config('app.locale')).'.js'))
<script src="{{ url('assets/plugins/bootstrap-fileinput/js/locales/'.ietfLangTag(config('app.locale')).'.js') }}" type="text/javascript"></script>
@endif

<script>// bootstrap-tagsinput.js file - add in local

(function ($) {

"use strict";

var defaultOptions = {
tagClass: function(item) {
return 'label label-info';
},
itemValue: function(item) {
return item ? item.toString() : item;
},
itemText: function(item) {
return this.itemValue(item);
},
itemTitle: function(item) {
return null;
},
freeInput: true,
addOnBlur: true,
maxTags: 5,
maxChars: undefined,
confirmKeys: [13, 44],
delimiter: ',',
delimiterRegex: null,
cancelConfirmKeysOnEmpty: true,
onTagExists: function(item, $tag) {
$tag.hide().fadeIn();
},
trimValue: false,
allowDuplicates: false
};

/**
* Constructor function
*/
function TagsInput(element, options) {
this.itemsArray = [];

this.$element = $(element);
this.$element.hide();

this.isSelect = (element.tagName === 'SELECT');
this.multiple = (this.isSelect && element.hasAttribute('multiple'));
this.objectItems = options && options.itemValue;
this.placeholderText = element.hasAttribute('placeholder') ? this.$element.attr('placeholder') : '';
this.inputSize = Math.max(1, this.placeholderText.length);

this.$container = $('<div class="bootstrap-tagsinput"></div>');
this.$input = $('<input type="text"  placeholder="' + this.placeholderText + '"/>').appendTo(this.$container);

this.$element.before(this.$container);

this.build(options);
}

TagsInput.prototype = {
constructor: TagsInput,

/**
* Adds the given item as a new tag. Pass true to dontPushVal to prevent
* updating the elements val()
*/
add: function(item, dontPushVal, options) {
var self = this;

if (self.options.maxTags && self.itemsArray.length >= self.options.maxTags)
return;

// Ignore falsey values, except false
if (item !== false && !item)
return;

// Trim value
if (typeof item === "string" && self.options.trimValue) {
item = $.trim(item);
}

// Throw an error when trying to add an object while the itemValue option was not set
if (typeof item === "object" && !self.objectItems)
throw("Can't add objects when itemValue option is not set");

// Ignore strings only containg whitespace
if (item.toString().match(/^\s*$/))
return;

// If SELECT but not multiple, remove current tag
if (self.isSelect && !self.multiple && self.itemsArray.length > 0)
self.remove(self.itemsArray[0]);

if (typeof item === "string" && this.$element[0].tagName === 'INPUT') {
var delimiter = (self.options.delimiterRegex) ? self.options.delimiterRegex : self.options.delimiter;
var items = item.split(delimiter);
if (items.length > 1) {
for (var i = 0; i < items.length; i++) {
this.add(items[i], true);
}

if (!dontPushVal)
self.pushVal();
return;
}
}

var itemValue = self.options.itemValue(item),
itemText = self.options.itemText(item),
tagClass = self.options.tagClass(item),
itemTitle = self.options.itemTitle(item);

// Ignore items allready added
var existing = $.grep(self.itemsArray, function(item) { return self.options.itemValue(item) === itemValue; } )[0];
if (existing && !self.options.allowDuplicates) {
// Invoke onTagExists
if (self.options.onTagExists) {
var $existingTag = $(".tag", self.$container).filter(function() { return $(this).data("item") === existing; });
self.options.onTagExists(item, $existingTag);
}
return;
}

// if length greater than limit
if (self.items().toString().length + item.length + 1 > self.options.maxInputLength)
return;

// raise beforeItemAdd arg
var beforeItemAddEvent = $.Event('beforeItemAdd', { item: item, cancel: false, options: options});
self.$element.trigger(beforeItemAddEvent);
if (beforeItemAddEvent.cancel)
return;

// register item in internal array and map
self.itemsArray.push(item);

// add a tag element

var $tag = $('<span class="tag ' + htmlEncode(tagClass) + (itemTitle !== null ? ('" title="' + itemTitle) : '') + '">' + htmlEncode(itemText) + '<span data-role="remove"></span></span>');
$tag.data('item', item);
self.findInputWrapper().before($tag);
$tag.after(' ');

// add <option /> if item represents a value not present in one of the <select />'s options
if (self.isSelect && !$('option[value="' + encodeURIComponent(itemValue) + '"]',self.$element)[0]) {
var $option = $('<option selected>' + htmlEncode(itemText) + '</option>');
$option.data('item', item);
$option.attr('value', itemValue);
self.$element.append($option);
}

if (!dontPushVal)
self.pushVal();

// Add class when reached maxTags
if (self.options.maxTags === self.itemsArray.length || self.items().toString().length === self.options.maxInputLength)
self.$container.addClass('bootstrap-tagsinput-max');

self.$element.trigger($.Event('itemAdded', { item: item, options: options }));
},

/**
* Removes the given item. Pass true to dontPushVal to prevent updating the
* elements val()
*/
remove: function(item, dontPushVal, options) {
var self = this;

if (self.objectItems) {
if (typeof item === "object")
item = $.grep(self.itemsArray, function(other) { return self.options.itemValue(other) ==  self.options.itemValue(item); } );
else
item = $.grep(self.itemsArray, function(other) { return self.options.itemValue(other) ==  item; } );

item = item[item.length-1];
}

if (item) {
var beforeItemRemoveEvent = $.Event('beforeItemRemove', { item: item, cancel: false, options: options });
self.$element.trigger(beforeItemRemoveEvent);
if (beforeItemRemoveEvent.cancel)
return;

$('.tag', self.$container).filter(function() { return $(this).data('item') === item; }).remove();
$('option', self.$element).filter(function() { return $(this).data('item') === item; }).remove();
if($.inArray(item, self.itemsArray) !== -1)
self.itemsArray.splice($.inArray(item, self.itemsArray), 1);
}

if (!dontPushVal)
self.pushVal();

// Remove class when reached maxTags
if (self.options.maxTags > self.itemsArray.length)
self.$container.removeClass('bootstrap-tagsinput-max');

self.$element.trigger($.Event('itemRemoved',  { item: item, options: options }));
},

/**
* Removes all items
*/
removeAll: function() {
var self = this;

$('.tag', self.$container).remove();
$('option', self.$element).remove();

while(self.itemsArray.length > 0)
self.itemsArray.pop();

self.pushVal();
},

/**
* Refreshes the tags so they match the text/value of their corresponding
* item.
*/
refresh: function() {
var self = this;
$('.tag', self.$container).each(function() {
var $tag = $(this),
item = $tag.data('item'),
itemValue = self.options.itemValue(item),
itemText = self.options.itemText(item),
tagClass = self.options.tagClass(item);

// Update tag's class and inner text
$tag.attr('class', null);
$tag.addClass('tag ' + htmlEncode(tagClass));
$tag.contents().filter(function() {
return this.nodeType == 3;
})[0].nodeValue = htmlEncode(itemText);

if (self.isSelect) {
var option = $('option', self.$element).filter(function() { return $(this).data('item') === item; });
option.attr('value', itemValue);
}
});
},

/**
* Returns the items added as tags
*/
items: function() {
return this.itemsArray;
},

/**
* Assembly value by retrieving the value of each item, and set it on the
* element.
*/
pushVal: function() {
var self = this,
val = $.map(self.items(), function(item) {
return self.options.itemValue(item).toString();
});

self.$element.val(val, true).trigger('change');
},

/**
* Initializes the tags input behaviour on the element
*/
build: function(options) {
var self = this;

self.options = $.extend({}, defaultOptions, options);
// When itemValue is set, freeInput should always be false
if (self.objectItems)
self.options.freeInput = false;

makeOptionItemFunction(self.options, 'itemValue');
makeOptionItemFunction(self.options, 'itemText');
makeOptionFunction(self.options, 'tagClass');

// Typeahead Bootstrap version 2.3.2
if (self.options.typeahead) {
var typeahead = self.options.typeahead || {};

makeOptionFunction(typeahead, 'source');

self.$input.typeahead($.extend({}, typeahead, {
source: function (query, process) {
function processItems(items) {
	var texts = [];

	for (var i = 0; i < items.length; i++) {
		var text = self.options.itemText(items[i]);
		map[text] = items[i];
		texts.push(text);
	}
	process(texts);
}

this.map = {};
var map = this.map,
data = typeahead.source(query);

if ($.isFunction(data.success)) {
// support for Angular callbacks
data.success(processItems);
} else if ($.isFunction(data.then)) {
// support for Angular promises
data.then(processItems);
} else {
// support for functions and jquery promises
$.when(data)
.then(processItems);
}
},
updater: function (text) {
self.add(this.map[text]);
return this.map[text];
},
matcher: function (text) {
return (text.toLowerCase().indexOf(this.query.trim().toLowerCase()) !== -1);
},
sorter: function (texts) {
return texts.sort();
},
highlighter: function (text) {
var regex = new RegExp( '(' + this.query + ')', 'gi' );
return text.replace( regex, "<strong>$1</strong>" );
}
}));
}

// typeahead.js
if (self.options.typeaheadjs) {
var typeaheadConfig = null;
var typeaheadDatasets = {};

// Determine if main configurations were passed or simply a dataset
var typeaheadjs = self.options.typeaheadjs;
if ($.isArray(typeaheadjs)) {
typeaheadConfig = typeaheadjs[0];
typeaheadDatasets = typeaheadjs[1];
} else {
typeaheadDatasets = typeaheadjs;
}

self.$input.typeahead(typeaheadConfig, typeaheadDatasets).on('typeahead:selected', $.proxy(function (obj, datum) {
if (typeaheadDatasets.valueKey)
self.add(datum[typeaheadDatasets.valueKey]);
else
self.add(datum);
self.$input.typeahead('val', '');
}, self));
}

self.$container.on('click', $.proxy(function(event) {
if (! self.$element.attr('disabled')) {
self.$input.removeAttr('disabled');
}
self.$input.focus();
}, self));

if (self.options.addOnBlur && self.options.freeInput) {
self.$input.on('focusout', $.proxy(function(event) {
// HACK: only process on focusout when no typeahead opened, to
//       avoid adding the typeahead text as tag
if ($('.typeahead, .twitter-typeahead', self.$container).length === 0) {
self.add(self.$input.val());
self.$input.val('');
}
}, self));
}


self.$container.on('keydown', 'input', $.proxy(function(event) {
var $input = $(event.target),
$inputWrapper = self.findInputWrapper();

if (self.$element.attr('disabled')) {
self.$input.attr('disabled', 'disabled');
return;
}

switch (event.which) {
// BACKSPACE
case 8:
if (doGetCaretPosition($input[0]) === 0) {
var prev = $inputWrapper.prev();
if (prev.length) {
self.remove(prev.data('item'));
}
}
break;

// DELETE
case 46:
if (doGetCaretPosition($input[0]) === 0) {
var next = $inputWrapper.next();
if (next.length) {
self.remove(next.data('item'));
}
}
break;

// LEFT ARROW
case 37:
// Try to move the input before the previous tag
var $prevTag = $inputWrapper.prev();
if ($input.val().length === 0 && $prevTag[0]) {
$prevTag.before($inputWrapper);
$input.focus();
}
break;
// RIGHT ARROW
case 39:
// Try to move the input after the next tag
var $nextTag = $inputWrapper.next();
if ($input.val().length === 0 && $nextTag[0]) {
$nextTag.after($inputWrapper);
$input.focus();
}
break;
default:
// ignore
}

// Reset internal input's size
var textLength = $input.val().length,
wordSpace = Math.ceil(textLength / 5),
size = 3;
$input.attr('size', Math.max(this.inputSize, $input.val().length));
}, self));

self.$container.on('keypress', 'input', $.proxy(function(event) {
var $input = $(event.target);

if (self.$element.attr('disabled')) {
self.$input.attr('disabled', 'disabled');
return;
}

var text = $input.val(),
maxLengthReached = self.options.maxChars && text.length >= self.options.maxChars;
if (self.options.freeInput && (keyCombinationInList(event, self.options.confirmKeys) || maxLengthReached)) {
// Only attempt to add a tag if there is data in the field
if (text.length !== 0) {

self.add(maxLengthReached ? text.substr(0, self.options.maxChars) : text);
$input.val('');
}

// If the field is empty, let the event triggered fire as usual
if (self.options.cancelConfirmKeysOnEmpty === false) {
event.preventDefault();
}
}

// Reset internal input's size
var textLength = $input.val().length,
wordSpace = Math.ceil(textLength / 5),
size = 3;
$input.attr('size', Math.max(this.inputSize, $input.val().length));
}, self));

// Remove icon clicked
self.$container.on('click', '[data-role=remove]', $.proxy(function(event) {
if (self.$element.attr('disabled')) {
return;
}
self.remove($(event.target).closest('.tag').data('item'));
}, self));

// Only add existing value as tags when using strings as tags
if (self.options.itemValue === defaultOptions.itemValue) {
if (self.$element[0].tagName === 'INPUT') {
self.add(self.$element.val());
} else {
$('option', self.$element).each(function() {
self.add($(this).attr('value'), true);
});
}
}
},

/**
* Removes all tagsinput behaviour and unregsiter all event handlers
*/
destroy: function() {
var self = this;

// Unbind events
self.$container.off('keypress', 'input');
self.$container.off('click', '[role=remove]');

self.$container.remove();
self.$element.removeData('tagsinput');
self.$element.show();
},

/**
* Sets focus on the tagsinput
*/
focus: function() {
this.$input.focus();
},

/**
* Returns the internal input element
*/
input: function() {
return this.$input;
},

/**
* Returns the element which is wrapped around the internal input. This
* is normally the $container, but typeahead.js moves the $input element.
*/
findInputWrapper: function() {
var elt = this.$input[0],
container = this.$container[0];
while(elt && elt.parentNode !== container)
elt = elt.parentNode;

return $(elt);
}
};

/**
* Register JQuery plugin
*/
$.fn.tagsinput = function(arg1, arg2, arg3) {
var results = [];

this.each(function() {
var tagsinput = $(this).data('tagsinput');
// Initialize a new tags input
if (!tagsinput) {
tagsinput = new TagsInput(this, arg1);
$(this).data('tagsinput', tagsinput);
results.push(tagsinput);

if (this.tagName === 'SELECT') {
$('option', $(this)).attr('selected', 'selected');
}

// Init tags from $(this).val()
$(this).val($(this).val());
} else if (!arg1 && !arg2) {
// tagsinput already exists
// no function, trying to init
results.push(tagsinput);
} else if(tagsinput[arg1] !== undefined) {
// Invoke function on existing tags input
if(tagsinput[arg1].length === 3 && arg3 !== undefined){
var retVal = tagsinput[arg1](arg2, null, arg3);
}else{
var retVal = tagsinput[arg1](arg2);
}
if (retVal !== undefined)
results.push(retVal);
}
});

if ( typeof arg1 == 'string') {
// Return the results from the invoked function calls
return results.length > 1 ? results : results[0];
} else {
return results;
}
};

$.fn.tagsinput.Constructor = TagsInput;

/**
* Most options support both a string or number as well as a function as
* option value. This function makes sure that the option with the given
* key in the given options is wrapped in a function
*/
function makeOptionItemFunction(options, key) {
if (typeof options[key] !== 'function') {
var propertyName = options[key];
options[key] = function(item) { return item[propertyName]; };
}
}
function makeOptionFunction(options, key) {
if (typeof options[key] !== 'function') {
var value = options[key];
options[key] = function() { return value; };
}
}
/**
* HtmlEncodes the given value
*/
var htmlEncodeContainer = $('<div />');
function htmlEncode(value) {
if (value) {
return htmlEncodeContainer.text(value).html();
} else {
return '';
}
}

/**
* Returns the position of the caret in the given input field
* http://flightschool.acylt.com/devnotes/caret-position-woes/
*/
function doGetCaretPosition(oField) {
var iCaretPos = 0;
if (document.selection) {
oField.focus ();
var oSel = document.selection.createRange();
oSel.moveStart ('character', -oField.value.length);
iCaretPos = oSel.text.length;
} else if (oField.selectionStart || oField.selectionStart == '0') {
iCaretPos = oField.selectionStart;
}
return (iCaretPos);
}

/**
* Returns boolean indicates whether user has pressed an expected key combination.
* @param object keyPressEvent: JavaScript event object, refer
*     http://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
* @param object lookupList: expected key combinations, as in:
*     [13, {which: 188, shiftKey: true}]
*/
function keyCombinationInList(keyPressEvent, lookupList) {
var found = false;
$.each(lookupList, function (index, keyCombination) {
if (typeof (keyCombination) === 'number' && keyPressEvent.which === keyCombination) {
found = true;
return false;
}

if (keyPressEvent.which === keyCombination.which) {
var alt = !keyCombination.hasOwnProperty('altKey') || keyPressEvent.altKey === keyCombination.altKey,
shift = !keyCombination.hasOwnProperty('shiftKey') || keyPressEvent.shiftKey === keyCombination.shiftKey,
ctrl = !keyCombination.hasOwnProperty('ctrlKey') || keyPressEvent.ctrlKey === keyCombination.ctrlKey;
if (alt && shift && ctrl) {
found = true;
return false;
}
}
});

return found;
}

/**
* Initialize tagsinput behaviour on inputs and selects which have
* data-role=tagsinput
*/
$(function() {
$("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
});
})(window.jQuery);

</script>
<script>
/* Translation */
var lang = {
'select': {
'category': "{{ t('Select a category') }}",
'subCategory': "{{ t('Select a sub-category') }}",
'country': "{{ t('Select a country') }}",
'admin': "{{ t('Select a location') }}",
'city': "{{ t('Select a city') }}"
},
'price': "{{ t('Price') }}",
'salary': "{{ t('Salary') }}",
'nextStepBtnLabel': {
'next': "{{ t('Next') }}",
'submit': "{{ t('Submit') }}"
}
};

/* Company */
var postCompanyId = {{ old('company_id', (isset($postCompany) ? $postCompany->id : 0)) }};
getCompany(postCompanyId);

/* Categories */
var category = {{ old('parent_id', 0) }};
var categoryType = '{{ old('parent_type') }}';
if (categoryType=='') {
var selectedCat = $('select[name=parent_id]').find('option:selected');
categoryType = selectedCat.data('type');
}
var subCategory = {{ old('category_id', 0) }};

/* Locations */
var countryCode = '{{ old('country_code', config('country.code', 0)) }}';
var adminType = '{{ config('country.admin_type', 0) }}';
var selectedAdminCode = '{{ old('admin_code', (isset($admin) ? $admin->code : 0)) }}';
var cityId = '{{ old('city_id', (isset($post) ? $post->city_id : 0)) }}';

/* Packages */
var packageIsEnabled = false;
@if (isset($packages) and isset($paymentMethods) and $packages->count() > 0 and $paymentMethods->count() > 0)
packageIsEnabled = true;
@endif
</script>
<script>
$(document).ready(function() {
/* Company */
$('#companyId').bind('click, change', function() {
postCompanyId = $(this).val();
getCompany(postCompanyId);
});

$('#tags').tagit({
fieldName: 'tags',
placeholderText: '{{ t('add a tag') }}',
caseSensitive: true,
allowDuplicates: false,
allowSpaces: false,
tagLimit: {{ (int)config('settings.single.tags_limit', 15) }},
singleFieldDelimiter: ','
});
$("#uploadfile").fileinput({browseLabel: '{!! t("Browse") !!}','showUpload':false, 'previewFileType':'any'});
});
</script>
<script>



$('.EditPortfolioRecord').on('click', function(event)
{

event.preventDefault();
var portfolio_id = $(this).attr('data-edit-portfolio-id');

$('#EditPortfolioModal'+portfolio_id+'').modal('show');

});

var scntDiv = $('#p_scents');
var i = $('#p_scents p').size() + 1;

var p_scents_portfolio = $('#p_scents_portfolio');
var i_p_scents_portfolio = $('#p_scents_portfolio p').size() + 1;

$('#addScnt').on('click', function(event)
{
event.preventDefault();
if(i >= 6){
toastr.error('You can add only 5 Youtube URLS');
return false;
}
$('<p><label for="youtube_video"><input type="text" id="p_scnt_' + i +'" size="100" name="youtube_video_' + i +'" value="" placeholder="Youtube Embed URL" class="youtube_embed_url" style="height: 50px;padding: .5rem .75rem;font-size: .85rem;color: #464a4c;background-color: #fff;background-image: none;background-clip: padding-box;border: 1px solid rgba(0,0,0,.15);border-radius: .2rem;"/></label> <a href="#" id="remScnt">Remove</a></p>').appendTo(scntDiv);
i++;
return false;
});

$('#addScntPortfolio').on('click', function(event)
{
event.preventDefault();
if(i_p_scents_portfolio >= 10){
toastr.error('You can add only 10 Portfolio');
return false;
}
$('<p style="padding: 1%;"><label for="user_portfolio"><input type="text" id="p_scnt_'+i_p_scents_portfolio+'" size="50" name="user_portfolio_title[]" value="" placeholder="Portfolio Title" style="height: 50px;padding: .5rem .75rem;font-size: .85rem;color: #464a4c;background-color: #fff;background-image: none;background-clip: padding-box;border: 1px solid rgba(0,0,0,.15);border-radius: .2rem;"/></label> <input type="file" name="user_portfolio_image[]" id="user_portfolio_image'+i_p_scents_portfolio+'" class="user_portfolio_image"><input type="hidden" name="user_portfolio_image_name[]" id="user_portfolio_image_name" value=""><br><a href="#" id="remScntPortfolio">Remove</a></p>').appendTo(p_scents_portfolio);
i_p_scents_portfolio++;
return false;
});


$(document).on("change",'input[name="user_portfolio_image[]"]',function()
{
$("input[name='portfolio_submit']").val('Please wait...');
$("input[name='portfolio_submit']").attr('disabled',true);
data = new FormData();
data.append('user_portfolio_image', $('#'+$(this).attr('id')+'')[0].files[0]);

	// alert($('#'+$(this).attr('id')+'')[0].files[0]);

	$.ajax({
		url: "/account/socialprofile/save_user_portfolio_image",
		method: 'POST',
		data : data,
		processData: false,
		contentType: false,
		cache: false,
		context: this,
		success: function(data){
			var returnedData = JSON.parse(data);
			var image_name = returnedData.image_name;

			var status = returnedData.status;

			$(this).next().val(image_name);

			if(status){
				$("input[name='portfolio_submit']").val('Save');
				$("input[name='portfolio_submit']").attr('disabled',false);
				
				//toastr.info('Image uploaded!');
			}else{
				toastr.error('Unsupported Image Format');
			}

		}
	});     

});


$(document).on("click","#remScntPortfolio",function(event){
event.preventDefault();
var portfolio_id = $(this).attr('data-portfolio-id');

if(portfolio_id)
{

bootbox.confirm("Do you want to delete?", function(result){ 
if(result){

$.ajax({
	url: "/account/socialprofile/remove_portfolio",
	method: 'POST',
	data : {'jobuser_portfolio_id':portfolio_id},
	cache: false,
	context: this,
	success: function(data){
		if(data == 1){
			toastr.success('Deleted successfully!');
			location.reload();
		}else{
			toastr.error('Please try again later!');
		}

	}
}); 

}
});

}else{
if( i_p_scents_portfolio > 1 ) {
$(this).parents('p').remove();
i_p_scents_portfolio--;
}
return false;
}

});

$(document).on("click","#remScnt",function(event){
event.preventDefault();
var youtube_url_id = $(this).attr('data-y-u-id');

if(youtube_url_id)
{
bootbox.confirm("Do you want to delete?", function(result){ 
if(result){
$.ajax({
	url: "/account/socialprofile/remove_youtube_url",
	method: 'POST',
	data : {'youtube_url_id':youtube_url_id},
	cache: false,
	context: this,
	success: function(data){
		if(data == 1){
			toastr.success('Deleted successfully!');
			location.reload();
		}else{
			toastr.error('Please try again later!');
		}

	}
}); 
}
});



}else{

if( i > 1 ) {
$(this).parents('p').remove();
i--;
}
return false;	
}

});

$(document).ready(function(){

$(document).on("click",".portfolio_submit",function(event){
event.preventDefault();
$("input[name='portfolio_submit']").val('Please wait...');
$("input[name='portfolio_submit']").attr('disabled',true);

var formData = new FormData($('#portfolio_embed_form')[0]);

    // PORTFOLIO TITLE EMPTY VALIDATION
    $("input[name='user_portfolio_title[]']")
    .map(function(){

    	if($(this).val() == ''){
    		toastr.error('Please enter portfolio title!');
    	}
    	return false;
    }).get();
       // PORTFOLIO TITLE EMPTY VALIDATION

       // PORTFOLIO IMAGE VALIDATION
       $("input[name='user_portfolio_image_name[]']")
       .map(function(){

       	if($(this).val() == ''){
       		toastr.error('Please upload portfolio image!');
       		return false;
       	}

       }).get();
       // PORTFOLIO IMAGE VALIDATION

       $.ajax({
       	url: "/account/socialprofile/save_user_portfolio",
       	method: 'POST',
       	data : formData,
       	processData: false,
       	contentType: false,
       	cache: false,
       	success: function(data){

       		if(data == 1){
       			$("input[name='portfolio_submit']").val('Save');
				$("input[name='portfolio_submit']").attr('disabled',false);
       			toastr.success('Success!');
       		}else{
       			toastr.error('Please try again later!');
       		}
       		location.reload();
       	}
       });


   });


$(document).on("click","#youtube_submit",function(event){
event.preventDefault();
var formData = new FormData($('#youtube_embed_form')[0]);

		   // PORTFOLIO IMAGE VALIDATION
		   $("input[class='youtube_embed_url']")
		   .map(function(){

		   	if($(this).val() == ''){
		   		toastr.error('Please enter URL!');
		   		return false;
		   	}

		   }).get();


		   $.ajax({
		   	url: "/account/socialprofile/save_youtube_url",
		   	method: 'POST',
		   	data : formData,
		   	processData: false,
		   	contentType: false,
		   	cache: false,
		   	success: function(data){
		   		if(data == 1){
		   			toastr.success('Success!');
		   		}else{
		   			toastr.error('Please try again later!');
		   		}
		   		location.reload();
		   	}
		   });
		});
});

</script>
<script src="{{ url('assets/js/app/d.select.category.js') . vTime() }}"></script>
<script src="{{ url('assets/js/app/d.select.location.js') . vTime() }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">

@endsection
