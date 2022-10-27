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
						<div class = "row">
							<div class="col-md-7 col-xs-4 col-xxs-12">
								<div class="welcome-msg">
									<h3 class="page-sub-header2 clearfix no-padding">{{ t('Hello') }} {{ $user->name }} ! </h3>
									<span class="page-sub-header-sub small">
										{{ t('You last logged in at') }}: {{ $user->last_login_at->formatLocalized(config('settings.app.default_datetime_format')) }}
									</span>
								</div>
							</div>
							<div class="col-md-5 col-xs-4 col-xxs-12">
								<div class="header-data text-center-xs">

									<div class="hdata">
										<div class="mcol-left">
											<i class="fas fa-wallet ln-shadow"></i></div>
											<div class="mcol-right">
												<!-- Number of messages -->
												<p>
													<a href="https://selfieym.com/account/wallet">
														Rs.{{\App\Helpers\UrlGen::get_user_wallet(auth()->user()->id)}}</a>
													</p>
												</div>
												<div class="clearfix"></div>
											</div>
										</div>
									</div>
								</div>

								<div id="accordion" class="panel-group">

									<!-- NEW SECTION FOR PORTFOLIO -->
									<div class="card card-default">
										<div class="card-header">
											<h4 class="card-title"><a href="#userPanel" data-toggle="collapse" data-parent="#accordion">Portfolio</a></h4>
										</div>
										<div class="panel-collapse collapse show" id="userPanel">
											<div class="card-body">

												<h2><a href="#" id="addScntPortfolio" class="btn btn-primary">Add Portfolio</a></h2>

												<form action="" method="POST" id="portfolio_embed_form">


													<div id="p_scents_portfolio">
														<?php if(count($user_portfolio_images) > 0):?>
															<div class="imgecotentset">
																<div class="row">
																	<?php $i=1;foreach($user_portfolio_images as $u_portfolio_images):?>

																	<div class="modal fade" id="EditPortfolioModal<?= $u_portfolio_images->jobuser_portfolio_id;?>" role="dialog">
																		<div class="modal-dialog">

																			<div class="modal-content">
																				<div class="modal-header">
																					<button type="button" class="close" data-dismiss="modal">&times;</button>

																				</div>
																				<div class="modal-body">
																					<p style="padding: 1%;"><label for="user_portfolio"><input type="text" id="p_scnt_1" size="50" name="user_portfolio_title[]" value="<?= $u_portfolio_images->portfolio_title;?>" placeholder="Portfolio Title" style="height: 50px;padding: .5rem .75rem;font-size: .85rem;color: #464a4c;background-color: #fff;background-image: none;background-clip: padding-box;border: 1px solid rgba(0,0,0,.15);border-radius: .2rem;"></label> 
																						<input type="file" name="user_portfolio_image[]" id="user_portfolio_image<?= $i; ?>" class="user_portfolio_image" value="">
																						<input type="hidden" name="user_portfolio_image_name[]" id="user_portfolio_image_name" value="<?= $u_portfolio_images->portfolio_image;?>">
																						<input type="submit" name="portfolio_submit" class = "portfolio_submit" value="Save" style="cursor:pointer;">
																					</div>
																					<div class="modal-footer">
																						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																					</div>
																				</div>


																			</div>
																		</div>						
																		<div class="col-md-4 col-12">
																			<div class="conterimg">
																				<img src="/public/images/user_portfolio_images/<?= $u_portfolio_images->portfolio_image;?>"  ><br><br>
																				<a href="#" id="remScntPortfolio" data-portfolio-id="<?= $u_portfolio_images->jobuser_portfolio_id;?>">Remove</a>
																				<a href="#" class="EditPortfolioRecord" data-edit-portfolio-id="<?= $u_portfolio_images->jobuser_portfolio_id;?>">Edit</a>
																			</div>
																		</div>

																		<?php
																		$i++; endforeach; ?>
																	</div>
																</div>
																<?php else: ?>
																	<p style="padding: 1%;"><label for="user_portfolio"><input type="text" id="p_scnt_1" size="50" name="user_portfolio_title[]" value="" placeholder="Portfolio Title" style="height: 50px;padding: .5rem .75rem;font-size: .85rem;color: #464a4c;background-color: #fff;background-image: none;background-clip: padding-box;border: 1px solid rgba(0,0,0,.15);border-radius: .2rem;"></label> <input type="file" name="user_portfolio_image[]" id="user_portfolio_image" class="user_portfolio_image"> <input type="hidden" name="user_portfolio_image_name[]" id="user_portfolio_image_name<?= rand();?>" value=""><br><a href="#" id="remScntPortfolio">Remove</a></p>
																<?php endif; ?>

															</div>
															<input type="submit" name = "portfolio_submit" class = "portfolio_submit" value = "Save" style="cursor:pointer;">
														</form>
													</div>
												</div>
											</div>
											<!-- NEW SECTION FOR PORTFOLIO -->
<!-- NEW SECTION FOR YOUTUBE -->
		<div class="card card-default">
			<div class="card-header">
				<h4 class="card-title"><a href="#userPanel" data-toggle="collapse" data-parent="#accordion">Youtube Embed URL</a></h4>
			</div>
			<div class="panel-collapse collapse show" id="userPanel">
				<div class="card-body">

					<h2><a href="#" id="addScnt" class="btn btn-primary">Add Youtube URL</a></h2>
					<form action="" method="POST" id="youtube_embed_form">
						<div id="p_scents">
							<?php if(count($user_youtube_urls) > 0):?>
								<?php 
								$i=1;
								foreach($user_youtube_urls as $user_y_urls): ?>
									<p>
										<label for="youtube_url"><input type="text" id="youtube_video_<?= $i; ?>" size="100" name="youtube_video_<?= $i; ?>" value="<?= $user_y_urls->youtube_url;?>" placeholder="Youtube Embed URL" class="youtube_embed_url" style="height: 50px;padding: .5rem .75rem;font-size: .85rem;color: #464a4c;background-color: #fff;background-image: none;background-clip: padding-box;border: 1px solid rgba(0,0,0,.15);border-radius: .2rem;"/></label>
										<a href="#" id="remScnt" data-y-u-id ="<?= $user_y_urls->youtube_url_id;?>">Remove</a>
									</p>
									<?php $i++;endforeach;?>
									<?php else: ?>
										<p>
											<label for="youtube_url"><input type="text" id="youtube_video_1" size="100" name="youtube_video_1" value="" placeholder="Youtube Embed URL" class="youtube_embed_url" style="height: 50px;padding: .5rem .75rem;font-size: .85rem;color: #464a4c;background-color: #fff;background-image: none;background-clip: padding-box;border: 1px solid rgba(0,0,0,.15);border-radius: .2rem;"/></label>
											<a href="#" id="remScnt">Remove</a>
										</p>
									<?php endif;?>
								</div>

								<input class="btn btn-primary" type="submit" name = "youtube_submit" id = "youtube_submit" value = "Save">
							</form>
						</div>
					</div>
				</div>
				<!-- NEW SECTION FOR YOUTUBE -->

											

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
