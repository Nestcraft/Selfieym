{{--

	--}}
	@extends('layouts.master')

	@section('content')
	@include('common.spacer')
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
									<h4 class="card-title"><a href="#userPanel" data-toggle="collapse" data-parent="#accordion">Professional Headline</a></h4>
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
												{{isset($details->biodata)? $details->biodata:''}}
											</div>
										</div>
										<!-- parent_id -->
										<?php $parentIdError = (isset($errors) and $errors->has('category_id')) ? ' is-invalid' : ''; ?>
										<div class="form-group row required">
											<label class="col-md-3 col-form-label{{ $parentIdError }}">{{ t('Category') }} <sup>*</sup></label>
											<div class="col-md-8">
												{{isset($details->catname)? $details->catname:''}}
												</div>
											</div>
											<!-- age -->
											<?php $ageError = (isset($errors) and $errors->has('age')) ? ' is-invalid' : ''; ?>
											<div class="form-group row required">
												<label class="col-md-3 col-form-label" for="">{{ t('Age') }} <sup>*</sup></label>
												<div class="input-group col-md-9">
													{{isset($details->age)? $details->age:''}}
												</div>
											</div>

											<!-- DOB -->
											<?php $dobError = (isset($errors) and $errors->has('dob')) ? ' is-invalid' : ''; ?>
											<div class="form-group row required">
												<label class="col-md-3 col-form-label" for="">{{ t('DOB') }} <sup>*</sup></label>
												<div class="input-group col-md-9">
													{{isset($details->dob)?$details->dob:''}}
												</div>
											</div>

											<!-- Gender -->
											<?php $genderError = (isset($errors) and $errors->has('gender')) ? ' is-invalid' : ''; ?>
											<div class="form-group row required">
												<label class="col-md-3 col-form-label" for="">{{ t('Gender') }} <sup>*</sup></label>
												<div class="input-group col-md-9">
													@if(!empty($details->gender))
													{{ $details->gender == 'male' ?  'Male' : ''}}
													{{ $details->gender == 'female' ?  'Female' : ''}}
													@endif
												</div>
											</div>
											<!-- city_id -->
											<?php $cityIdError = (isset($errors) and $errors->has('city_id')) ? ' is-invalid' : ''; ?>
											<div id="cityBox" class="form-group row required">
												<label class="col-md-3 col-form-label{{ $cityIdError }}" for="city_id">
													{{ t('City') }} <sup>*</sup>
												</label>
												<div class="input-group col-md-9">
													{{isset($details->cityname)?$details->cityname:''}}
												</div>
											</div>
											<!-- min fee -->
											<?php $min_feeError = (isset($errors) and $errors->has('min_fee')) ? ' is-invalid' : ''; ?>
											<div class="form-group row required">
												<label class="col-md-3 col-form-label" for="">{{ t('Minimum Fee') }} <sup>*</sup></label>
												<div class="input-group col-md-9">
													{{ isset($details->min_fee) ? $details->min_fee : '' }}
												</div>
											</div>
											<!-- facebook -->
											<?php $companyFacebookError = (isset($errors) and $errors->has('facebook_url')) ? ' is-invalid' : '';if(!empty($details->facebook_url)){
											?>
											<div class="form-group row">
												<label class="col-md-3 col-form-label" for="facebook_url">Facebook Url</label>
												<div class="input-group col-md-5">
													{{isset($details->facebook_url) ? $details->facebook_url : ''}}
												</div>
												<?php if($details->facebook_followers!='K'){?>
												<div class="input-group col-md-4">
													{{isset($details->facebook_followers) ? $details->facebook_followers : ''}}
												</div>
											<?php } ?>
											</div>
										<?php }  ?>

											<!-- twitter -->
											<?php $companyTwitterError = (isset($errors) and $errors->has('twitter')) ? ' is-invalid' : ''; ?>
											<?php if(!empty($details->twitter_url)){?>
											<div class="form-group row">
												<label class="col-md-3 col-form-label" for="twitter_url">Twitter Url</label>
												<div class="input-group col-md-5">
													{{isset($details->twitter_url) ? $details->twitter_url : ''}}
												</div>
												<?php if($details->twitter_followers!='K'){?>
												<div class="input-group col-md-4">
													{{isset($details->twitter_followers) ? $details->twitter_followers : ''}}
												</div>
											<?php } ?>
											</div>
										<?php } ?>
											<!-- youtube -->
											<?php $companyYoutubeError = (isset($errors) and $errors->has('youtube_url')) ? ' is-invalid' : ''; ?>
											<?php if(!empty($details->youtube_url)){?>
											<div class="form-group row">
												<label class="col-md-3 col-form-label" for="youtube_url">Youtube Url</label>
												<div class="input-group col-md-5">
													{{isset($details->youtube_url) ? $details->youtube_url : ''}}
												</div>
												<?php if($details->youtube_subscribers!='K'){?>
												<div class="input-group col-md-4">
													{{isset($details->youtube_subscribers) ? $details->youtube_subscribers : ''}}
												</div>
											<?php } ?>
											</div>
										<?php }?>
											<!-- tiktok -->
											<?php $companyTiktokError = (isset($errors) and $errors->has('instagram_url')) ? ' is-invalid' : ''; ?>
											<?php if(!empty($details->instagram_url)){?>
											<div class="form-group row">
												<label class="col-md-3 col-form-label" for="tiktok_url">Instagram Url</label>
												<div class="input-group col-md-5">
													{{isset($details->instagram_url) ? $details->instagram_url : ''}}
												</div>
												<?php if($details->instagram_followers!='K'){?>
												<div class="input-group col-md-4">
													{{isset($details->instagram_followers) ? $details->instagram_followers : ''}}
												</div>
												<?php } ?>
											</div>
										<?php } ?>
											<!-- quora -->
											<?php $companyquoraError = (isset($errors) and $errors->has('quora_url')) ? ' is-invalid' : ''; ?>
											<?php if(!empty($details->quora_url)){ ?>
											<div class="form-group row">
												<label class="col-md-3 col-form-label" for="quora_url">quora</label>
												<div class="input-group col-md-5">
													{{isset($details->quora_url) ? $details->quora_url : ''}}
												</div>
												<?php if($details->quora_followers!='K'){?>
												<div class="input-group col-md-4">
													{{isset($details->quora_followers) ? $details->quora_followers : ''}}
												</div>
											<?php } ?>
											</div>
										<?php } ?>
											@endif

											<div class="form-group row">
												<div class="offset-md-3 col-md-9"></div>
											</div>

											<!-- Button -->
											<div class="form-group row">
												<div class="offset-md-3 col-md-9">
													<a href="{{ lurl('account/socialprofile/edit') }}" class="btn btn-success">{{ t('Edit') }}</a>
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
	<script src="{{ url('assets/plugins/bootstrap-fileinput/js/plugins/sortable.min.js') }}" type="text/javascript"></script>
	<script src="{{ url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js') }}" type="text/javascript"></script>
	<script src="{{ url('assets/plugins/bootstrap-fileinput/themes/fa/theme.js') }}" type="text/javascript"></script>
	@if (file_exists(public_path() . '/assets/plugins/bootstrap-fileinput/js/locales/'.ietfLangTag(config('app.locale')).'.js'))
	<script src="{{ url('assets/plugins/bootstrap-fileinput/js/locales/'.ietfLangTag(config('app.locale')).'.js') }}" type="text/javascript"></script>
	@endif
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
	<script src="{{ url('assets/js/app/d.select.category.js') . vTime() }}"></script>
	<script src="{{ url('assets/js/app/d.select.location.js') . vTime() }}"></script>
	@endsection
