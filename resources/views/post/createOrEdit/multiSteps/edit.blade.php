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

@section('wizard')
	@include('post.createOrEdit.multiSteps.inc.wizard')
@endsection

<?php
// Category
if ($post->category) {
    if ($post->category->parent_id == 0) {
        $postCatParentId = $post->category->id;
    } else {
        $postCatParentId = $post->category->parent_id;
    }
} else {
    $postCatParentId = 0;
}
?>
@section('content')
	@include('common.spacer')
	<div class="main-container">
		<div class="container">
			<div class="row">
				
				@include('post.inc.notification')

				<div class="col-md-9 page-content">
					<div class="inner-box category-content">
						<h2 class="title-2">
							<strong><i class="icon-docs"></i> {{ t('Update My Ad') }}</strong> -&nbsp;
							<a href="{{ \App\Helpers\UrlGen::post($post) }}" class="tooltipHere" title="" data-placement="top"
							   data-toggle="tooltip"
							   data-original-title="{!! $post->title !!}">
								{!! \Illuminate\Support\Str::limit($post->title, 45) !!}
							</a>
						</h2>
						
						<div class="row">
							<div class="col-sm-12">
								<form class="form-horizontal" id="postForm" method="POST" action="{{ url()->current() }}" enctype="multipart/form-data">
									{!! csrf_field() !!}
									<input name="_method" type="hidden" value="PUT">
									<input type="hidden" name="post_id" value="{{ $post->id }}">
									<fieldset>
										<!-- COMPANY -->
										<div class="content-subheading mt-0">
											<i class="icon-town-hall fa"></i>
											<strong>{{ t('Company Information') }}</strong>
										</div>
										
										<!-- company_id -->
										<?php $companyIdError = (isset($errors) and $errors->has('company_id')) ? ' is-invalid' : ''; ?>
										<div class="form-group row required">
											<label class="col-md-3 col-form-label{{ $companyIdError }}">
												{{ t('Select a Company') }} <sup>*</sup>
											</label>
											<div class="col-md-8">
												<select id="companyId" name="company_id" class="form-control selecter{{ $companyIdError }}">
													<option value="0" data-logo=""
															@if (old('company_id', 0)==0)
																selected="selected"
															@endif
													>[+] {{ t('New Company') }}</option>
													@if (isset($companies) and $companies->count() > 0)
														@foreach ($companies as $item)
															<option value="{{ $item->id }}" data-logo="{{ imgUrl($item->logo, 'small') }}"
																	@if (old('company_id', (isset($postCompany) ? $postCompany->id : 0))==$item->id)
																		selected="selected"
																	@endif
															>{{ $item->name }}</option>
														@endforeach
													@endif
												</select>
											</div>
										</div>
										
										<!-- logo -->
										<div id="logoField" class="form-group row">
											<label class="col-md-3 col-form-label">&nbsp;</label>
											<div class="col-md-8">
												<div class="mb10">
													<div id="logoFieldValue"></div>
												</div>
												<small id="" class="form-text text-muted">
													<a id="companyFormLink" href="{{ lurl('account/companies/0/edit') }}" class="btn btn-default">
														<i class="fa fa-pencil-square-o"></i> {{ t('Edit the Company') }}
													</a>
												</small>
											</div>
										</div>
									
										@include('account.company._form', ['originForm' => 'post'])
										
									
										<!-- POST -->
										<div class="content-subheading">
											<i class="icon-town-hall fa"></i>
											<strong>{{ t('Job Details') }}</strong>
										</div>
										
										<!-- parent_id -->
										<?php $parentIdError = (isset($errors) and $errors->has('category_id')) ? ' is-invalid' : ''; ?>
										<div class="form-group row required">
											<label class="col-md-3 col-form-label{{ $parentIdError }}">
												{{ t('Category') }} <sup>*</sup>
											</label>
											<div class="col-md-8">
												<select name="parent_id" id="parentId" class="form-control selecter{{ $parentIdError }}">
													<option value="0" data-type=""
															@if (old('parent_id', $postCatParentId)=='' or old('parent_id', $postCatParentId)==0)
																selected="selected"
															@endif
													>{{ t('Select a category') }}</option>
													@foreach ($categories as $cat)
														<option value="{{ $cat->tid }}" data-type="{{ $cat->type }}"
																@if (old('parent_id', $postCatParentId)==$cat->tid)
																	selected="selected"
																@endif
														>{{ $cat->name }}</option>
													@endforeach
												</select>
												<input type="hidden" name="parent_type" id="parentType" value="{{ old('parent_type') }}">
											</div>
										</div>
										
										<!-- category_id -->
										<?php $categoryIdError = (isset($errors) and $errors->has('category_id')) ? ' is-invalid' : ''; ?>
										<div id="subCatBloc" class="form-group row required">
											<label class="col-md-3 col-form-label{{ $categoryIdError }}">
												{{ t('Sub-Category') }} <sup>*</sup>
											</label>
											<div class="col-md-8">
												<select name="category_id" id="categoryId" class="form-control selecter{{ $categoryIdError }}">
													<option value="0"
															@if (old('category_id', $post->category_id)=='' or old('category_id', $post->category_id)==0)
																selected="selected"
															@endif
													>{{ t('Select a sub-category') }}</option>
												</select>
											</div>
										</div>

										<!-- title -->
										<?php $titleError = (isset($errors) and $errors->has('title')) ? ' is-invalid' : ''; ?>
										<div class="form-group row required">
											<label class="col-md-3 col-form-label" for="title">{{ t('Title') }} <sup>*</sup></label>
											<div class="col-md-8">
												<input id="title" name="title" placeholder="{{ t('Job title') }}" class="form-control input-md{{ $titleError }}"
													   type="text" value="{{ old('title', $post->title) }}">
												<small id="" class="form-text text-muted">
													{{ t('A great title needs at least 60 characters.') }}
												</small>
											</div>
										</div>

										<!-- description -->
										<?php $descriptionError = (isset($errors) and $errors->has('description')) ? ' is-invalid' : ''; ?>
										<div class="form-group row required">
											<?php
												$descriptionErrorLabel = '';
												$descriptionColClass = 'col-md-8';
												if (config('settings.single.simditor_wysiwyg')) {
													$descriptionColClass = 'col-md-12';
													$descriptionErrorLabel = $descriptionError;
												}
											?>
											<label class="col-md-3 col-form-label{{ $descriptionErrorLabel }}" for="description">
												{{ t('Description') }} <sup>*</sup>
											</label>
											<div class="{{ $descriptionColClass }}">
												<textarea class="form-control {{ $descriptionError }}"
														  id="description"
														  name="description"
														  rows="10"
														  required=""
												>{{ old('description', $post->description) }}</textarea>
												<small id="" class="form-text text-muted">{{ t('Describe what makes your ad unique') }}</small>
											</div>
										</div>

										<!-- post_type_id -->
										<?php $postTypeIdError = (isset($errors) and $errors->has('post_type_id')) ? ' is-invalid' : ''; ?>
										<div id="postTypeBloc" class="form-group row required">
											<label class="col-md-3 col-form-label{{ $postTypeIdError }}">
												{{ t('Job Type') }} <sup>*</sup>
											</label>
											<div class="col-md-8">
												<select name="post_type_id" id="postTypeId" class="form-control selecter{{ $postTypeIdError }}">
													@foreach ($postTypes as $postType)
														<option value="{{ $postType->tid }}"
																@if (old('post_type_id', $post->post_type_id)==$postType->tid)
																	selected="selected"
																@endif
														>{{ $postType->name }}</option>
													@endforeach
												</select>
											</div>
										</div>

										<!-- salary_min & salary_max -->
										<?php $salaryMinError = (isset($errors) and $errors->has('salary_min')) ? ' is-invalid' : ''; ?>
										<?php $salaryMaxError = (isset($errors) and $errors->has('salary_max')) ? ' is-invalid' : ''; ?>
										<div id="salaryBloc" class="form-group row">
											<label class="col-md-3 col-form-label" for="salary_max">{{ t('Salary') }}</label>
											<div class="col-md-4">
												<div class="row">
													<div class="input-group col-md-12">
														@if (config('currency')['in_left'] == 1)
															<div class="input-group-prepend">
																<span class="input-group-text">{!! config('currency')['symbol'] !!}</span>
															</div>
														@endif
														<input id="salary_min"
															   name="salary_min"
															   class="form-control tooltipHere{{ $salaryMinError }}"
															   data-toggle="tooltip"
															   data-original-title="{{ t('Salary (min)') }}"
															   placeholder="{{ t('Salary (min)') }}"
															   type="text"
															   value="{{ old('salary_min', $post->salary_min) }}"
														>
														@if (config('currency')['in_left'] == 0)
															<div class="input-group-append">
																<span class="input-group-text">{!! config('currency')['symbol'] !!}</span>
															</div>
														@endif
													</div>
													<div class="input-group col-md-12">
														@if (config('currency')['in_left'] == 1)
															<div class="input-group-prepend">
																<span class="input-group-text">{!! config('currency')['symbol'] !!}</span>
															</div>
														@endif
														<input id="salary_max"
															   name="salary_max"
															   class="form-control tooltipHere{{ $salaryMaxError }}"
															   data-toggle="tooltip"
															   data-original-title="{{ t('Salary (max)') }}"
															   placeholder="{{ t('Salary (max)') }}"
															   type="text"
															   value="{{ old('salary_max', $post->salary_max) }}"
														>
														@if (config('currency')['in_left'] == 0)
															<div class="input-group-append">
																<span class="input-group-text">{!! config('currency')['symbol'] !!}</span>
															</div>
														@endif
													</div>
												</div>
											</div>

											<!-- salary_type_id -->
											<?php $salaryTypeIdError = (isset($errors) and $errors->has('salary_type_id')) ? ' is-invalid' : ''; ?>
											<div class="col-md-4">
												<select name="salary_type_id" id="salaryTypeId" class="form-control selecter{{ $salaryTypeIdError }}">
													@foreach ($salaryTypes as $salaryType)
														<option value="{{ $salaryType->tid }}"
																@if (old('salary_type_id', $post->salary_type_id)==$salaryType->tid)
																	selected="selected"
																@endif
														>{{ t('per') . ' ' . $salaryType->name }}</option>
													@endforeach
												</select>
												<div class="form-check form-check-inline">
													<label class="form-check-label pt-2">
														<input id="negotiable"
															   name="negotiable"
															   type="checkbox"
															   value="1" {{ (old('negotiable', $post->negotiable)=='1') ? 'checked="checked"' : '' }}
														>&nbsp;{{ t('Negotiable') }}
													</label>
												</div>
											</div>
										</div>

										<!-- start_date -->
										<?php $startDateError = (isset($errors) and $errors->has('start_date')) ? ' is-invalid' : ''; ?>
										<div class="form-group row">
											<label class="col-md-3 col-form-label" for="start_date">{{ t('Start Date') }} </label>
											<div class="col-md-8">
												<input id="start_date" name="start_date" placeholder="{{ t('Start Date') }}" class="form-control input-md{{ $startDateError }}"
													   type="text" value="{{ old('start_date', $post->start_date) }}">
											</div>
										</div>

										<!-- contact_name -->
										<?php $contactNameError = (isset($errors) and $errors->has('contact_name')) ? ' is-invalid' : ''; ?>
										<div class="form-group row required">
											<label class="col-md-3 col-form-label" for="contact_name">{{ t('Contact Name') }} <sup>*</sup></label>
											<div class="input-group col-md-8">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="icon-user"></i></span>
												</div>
												<input id="contact_name" name="contact_name" placeholder="{{ t('Contact Name') }}"
												   class="form-control input-md{{ $contactNameError }}" type="text"
												   value="{{ old('contact_name', $post->contact_name) }}">
											</div>
										</div>

										<!-- email -->
										<?php $emailError = (isset($errors) and $errors->has('email')) ? ' is-invalid' : ''; ?>
										<div class="form-group row required">
											<label class="col-md-3 col-form-label" for="email"> {{ t('Contact Email') }} <sup>*</sup></label>
											<div class="input-group col-md-8">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="icon-mail"></i></span>
												</div>
												<input id="email" name="email" class="form-control{{ $emailError }}"
													   placeholder="{{ t('Email') }}" type="text"
													   value="{{ old('email', $post->email) }}">
											</div>
										</div>

										<!-- phone -->
										<?php $phoneError = (isset($errors) and $errors->has('phone')) ? ' is-invalid' : ''; ?>
										<div class="form-group row required">
											<label class="col-md-3 col-form-label" for="phone">{{ t('Phone Number') }}</label>
											<div class="input-group col-md-8">
												<div class="input-group-prepend">
													<span id="phoneCountry" class="input-group-text">{!! getPhoneIcon($post->country_code) !!}</span>
												</div>
												
												<input id="phone" name="phone"
													   placeholder="{{ t('Phone Number') }}" class="form-control input-md{{ $phoneError }}"
													   type="text" value="{{ phoneFormat(old('phone', $post->phone), $post->country_code) }}"
												>
												
												<div class="input-group-append">
													<span class="input-group-text">
														<input name="phone_hidden" id="phoneHidden" type="checkbox"
															   value="1" {{ (old('phone_hidden', $post->phone_hidden)=='1') ? 'checked="checked"' : '' }}>&nbsp;
														<small>{{ t('Hide') }}</small>
													</span>
												</div>
											</div>
										</div>
										
										<!-- country_code -->
										<input id="countryCode" name="country_code" type="hidden" value="{{ !empty($post->country_code) ? $post->country_code : config('country.code') }}">
									
										@if (config('country.admin_field_active') == 1 and in_array(config('country.admin_type'), ['1', '2']))
											<!-- admin_code -->
											<?php $adminCodeError = (isset($errors) and $errors->has('admin_code')) ? ' is-invalid' : ''; ?>
											<div id="locationBox" class="form-group row required">
												<label class="col-md-3 col-form-label{{ $adminCodeError }}" for="admin_code">
													{{ t('Location') }} <sup>*</sup>
												</label>
												<div class="col-md-8">
													<select id="adminCode" name="admin_code" class="form-control sselecter{{ $adminCodeError }}">
														<option value="0" {{ (!old('admin_code') or old('admin_code')==0) ? 'selected="selected"' : '' }}>
															{{ t('Select your Location') }}
														</option>
													</select>
												</div>
											</div>
										@endif
									
										<!-- city_id -->
										<?php $cityIdError = (isset($errors) and $errors->has('city_id')) ? ' is-invalid' : ''; ?>
										<div id="cityBox" class="form-group row required">
											<label class="col-md-3 col-form-label{{ $cityIdError }}" for="city_id">{{ t('City') }} <sup>*</sup></label>
											<div class="col-md-8">
												<select id="cityId" name="city_id" class="form-control sselecter{{ $cityIdError }}">
													<option value="0" {{ (!old('city_id') or old('city_id')==0) ? 'selected="selected"' : '' }}>
														{{ t('Select a city') }}
													</option>
												</select>
											</div>
										</div>
										<!-- Influencer required -->
										<div class="form-group row">
											<label class="col-md-3 col-form-label" for="required_influencer">{{ t('Influencer required') }}</label>
											<div class="col-md-8">
												<input id="" type="number" name="required_influencer" placeholder="{{ t('Influencer required') }}" class="form-control" value="{{ old('required_influencer',$post->required_influencer) }}">
											</div>
										</div>
										<!-- application_url -->
										<?php $applicationUrlError = (isset($errors) and $errors->has('application_url')) ? ' is-invalid' : ''; ?>
										<div class="form-group row">
											<label class="col-md-3 col-form-label" for="title">{{ t('Application URL') }}</label>
											<div class="col-md-8">
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="icon-reply"></i></span>
													</div>
													<input id="application_url" name="application_url"
														   placeholder="{{ t('Application URL') }}" class="form-control input-md{{ $applicationUrlError }}" type="text"
														   value="{{ old('application_url', $post->application_url) }}">
												</div>
												<small id="" class="form-text text-muted">
													{{ t('Candidates will follow this URL address to apply for the job.') }}
												</small>
											</div>
										</div>
										
										<!-- tags -->
										<?php $tagsError = (isset($errors) and $errors->has('tags')) ? ' is-invalid' : ''; ?>
										<div class="form-group row">
											<label class="col-md-3 col-form-label" for="tags">{{ t('Tags') }}</label>
											<div class="col-md-8">
												<input id="tags"
													   name="tags"
													   placeholder="{{ t('Tags') }}"
													   class="form-control input-md{{ $tagsError }}"
													   type="text"
													   value="{{ old('tags', $post->tags) }}"
												>
												<small id="" class="form-text text-muted">{{ t('Enter the tags separated by commas.') }}</small>
											</div>
										</div>

										<!-- file upload -->
										<div class="form-group row">
											<label class="col-md-3 col-form-label" for="">{{ t('uploadfilelable') }}</label>
											<div class="col-md-8">
												<input id="uploadfile" name="uploadfile" type="file" class="file" data-preview-file-type="text">
											</div>
										</div>
										<!-- Freelance Required -->
										<div class="content-subheading">
											<i class="icon-town-hall fa"></i> <strong>{{ t('Influencer Required') }}</strong>
										</div>

										<div class="form-group row">
											<label class="col-md-3 col-form-label" for="">{{ t('Age') }}:</label>
											
											<div class="col-md-4">
												<select name="influencer_age" id="" class="form-control">
													<option>{{ t('Select Age') }}</option>
													<option value="any" {{$post->influencer_age == 'any' ? 'selected' : ''}}>{{ t('Any') }}</option>
													<option value="less_25" {{$post->influencer_age == 'less_25' ? 'selected' : ''}}>{{ t('Bellow Age of 25') }}</option>
													<option value="less_40" {{$post->influencer_age == 'less_40' ? 'selected' : ''}}>{{ t('Bellow Age of 40') }}</option>
													<option value="less_50" {{$post->influencer_age == 'less_50' ? 'selected' : ''}}>{{ t('Bellow Age of 50') }}</option>
													<option value="great_50" {{$post->influencer_age == 'great_50' ? 'selected' : ''}}>{{ t('Above 50') }}</option>
												</select>	
											</div>
											<div class="col-md-4">
												<select name="influencer_gender" id="" class="form-control">
													<option value="male" {{$post->influencer_gender == 'male' ? 'selected' : ''}}>{{ t('Male') }}</option>
													<option value="female" {{$post->influencer_gender == 'female' ? 'selected' : ''}}>{{ t('Female') }}</option>
													<option value="any" {{$post->influencer_gender == 'any' ? 'selected' : ''}}>{{ t('Any') }}</option>
												</select>	
											</div>
										</div>
										<div class="form-group row">
											<label class="col-md-3 col-form-label">Platform for promotion:</label>
											<div class="col-md-4">
												<div class="form-check">
													<input name="instagram_chk" id="" class="form-check-input" value="1" type="checkbox" {{$post->instagram_followers ? 'checked="true"' : ''}}>
													<label class="form-check-label" for="">Instagram</label>
												</div>
											</div>
											<div class="col-md-2">
												<input id=""
												name="instagram_followers"
												type="number"
												class="form-control"
												value="{{ isset($post->instagram_followers) ? substr($post->instagram_followers, 0, -1) : '' }}" 
												 
												>
											</div>
											<div class="col-md-2">
												<select name="instagram_unit" id="" class="form-control">
													<option value="K" {{ isset($post->instagram_followers) && substr($post->instagram_followers, -1) == 'K'  ? 'selected' : '' }}>K</option>
													<option value="M" {{ isset($post->instagram_followers) && substr($post->instagram_followers, -1) == 'M'  ? 'selected' : '' }}>M</option>
												</select>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-md-3 col-form-label"></label>
											<div class="col-md-4">
												<div class="form-check">
													<input name="facebook_chk" id="" class="form-check-input" value="1" type="checkbox" {{$post->facebook_likes ? 'checked="true"' : ''}}>
													<label class="form-check-label" for="">Facebook</label>
												</div>
											</div>
											<div class="col-md-2">
												<input id=""
												name="facebook_likes"
												type="number"
												class="form-control"
												value="{{ isset($post->facebook_likes) ? substr($post->facebook_likes, 0, -1) : '' }}" 
												 
												>
											</div>
											<div class="col-md-2">
												<select name="facebook_unit" id="" class="form-control">
													<option value="K" {{ isset($post->facebook_likes) && substr($post->facebook_likes, -1) == 'K'  ? 'selected' : '' }}>K</option>
													<option value="M" {{ isset($post->facebook_likes) && substr($post->facebook_likes, -1) == 'M'  ? 'selected' : '' }}>M</option>
												</select>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-md-3 col-form-label"></label>
											<div class="col-md-4">
												<div class="form-check">
													<input name="twitter_chk" id="" class="form-check-input" value="1" type="checkbox" {{$post->twitter_followers ? 'checked="true"' : ''}}>
													<label class="form-check-label" for="">Twitter</label>
												</div>
											</div>
											<div class="col-md-2">
												<input id=""
												name="twitter_followers"
												type="number"
												class="form-control"
												value="{{ isset($post->twitter_followers) ? substr($post->twitter_followers, 0, -1) : '' }}" 
												 
												>
											</div>
											<div class="col-md-2">
												<select name="twitter_unit" id="" class="form-control">
													<option value="K" {{ isset($post->twitter_followers) && substr($post->twitter_followers, -1) == 'K'  ? 'selected' : '' }}>K</option>
													<option value="M" {{ isset($post->twitter_followers) && substr($post->twitter_followers, -1) == 'M'  ? 'selected' : '' }}>M</option>
												</select>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-md-3 col-form-label"></label>
											<div class="col-md-4">
												<div class="form-check">
													<input name="youtube_chk" id="" class="form-check-input" value="1" type="checkbox" {{$post->youtube_subscribers ? 'checked="true"' : ''}}>
													<label class="form-check-label" for="">Youtube</label>
												</div>
											</div>
											<div class="col-md-2">
												<input id=""
												name="youtube_subscribers"
												type="number"
												class="form-control"
												value="{{ isset($post->youtube_subscribers) ? substr($post->youtube_subscribers, 0, -1) : '' }}" 
												 
												>
											</div>
											<div class="col-md-2">
												<select name="youtube_unit" id="" class="form-control">
													<option value="K" {{ isset($post->youtube_subscribers) && substr($post->youtube_subscribers, -1) == 'K'  ? 'selected' : '' }}>K</option>
													<option value="M" {{ isset($post->youtube_subscribers) && substr($post->youtube_subscribers, -1) == 'M'  ? 'selected' : '' }}>M</option>
												</select>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-md-3 col-form-label"></label>
											<div class="col-md-4">
												<div class="form-check">
													<input name="tiktok_chk" id="" class="form-check-input" value="1" type="checkbox" {{$post->tiktok_likes ? 'checked="true"' : ''}}>
													<label class="form-check-label" for="">Tiktok</label>
												</div>
											</div>
											<div class="col-md-2">
												<input id=""
												name="tiktok_likes"
												type="number"
												class="form-control"
												value="{{ isset($post->tiktok_likes) ? substr($post->tiktok_likes, 0, -1) : '' }}" 
												 
												>
											</div>
											<div class="col-md-2">
												<select name="tiktok_unit" id="" class="form-control">
													<option value="K" {{ isset($post->tiktok_likes) && substr($post->tiktok_likes, -1) == 'K'  ? 'selected' : '' }}>K</option>
													<option value="M" {{ isset($post->tiktok_likes) && substr($post->tiktok_likes, -1) == 'M'  ? 'selected' : '' }}>M</option>
												</select>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-md-3 col-form-label"></label>
											<div class="col-md-4">
												<div class="form-check">
													<input name="web_chk" id="" class="form-check-input" value="1" type="checkbox" {{$post->web_followers ? 'checked="true"' : ''}}>
													<label class="form-check-label" for="">Website/Blog</label>
												</div>
											</div>
											<div class="col-md-2">
												<input id=""
												name="web_followers"
												type="number"
												class="form-control"
												value="{{ isset($post->web_followers) ? substr($post->web_followers, 0, -1) : '' }}" 
												 
												>
											</div>
											<div class="col-md-2">
												<select name="web_unit" id="" class="form-control">
													<option value="K" {{ isset($post->web_followers) && substr($post->web_followers, -1) == 'K'  ? 'selected' : '' }}>K</option>
													<option value="M" {{ isset($post->web_followers) && substr($post->web_followers, -1) == 'M'  ? 'selected' : '' }}>M</option>
												</select>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-md-3 col-form-label"></label>
											<div class="col-md-4">
												<div class="form-check">
													<input name="quora_chk" id="" class="form-check-input" value="1" type="checkbox" {{$post->quora_followers ? 'checked="true"' : ''}}>
													<label class="form-check-label" for="">Quora</label>
												</div>
											</div>
											<div class="col-md-2">
												<input id=""
												name="quora_followers"
												type="number"
												class="form-control"
												value="{{ isset($post->quora_followers) ? substr($post->quora_followers, 0, -1) : '' }}" 
												 
												>
											</div>
											<div class="col-md-2">
												<select name="quora_unit" id="" class="form-control">
													<option value="K" {{ isset($post->quora_followers) && substr($post->quora_followers, -1) == 'K'  ? 'selected' : '' }}>K</option>
													<option value="M" {{ isset($post->quora_followers) && substr($post->quora_followers, -1) == 'M'  ? 'selected' : '' }}>M</option>
												</select>
											</div>
										</div>




										<!-- Button -->
										<div class="form-group row">
											<div class="col-md-12 text-center">
												<a href="{{ \App\Helpers\UrlGen::post($post) }}" class="btn btn-default btn-lg"> {{ t('Back') }}</a>
												<button id="nextStepBtn" class="btn btn-success btn-lg submitPostForm"> {{ t('Update') }} </button>
											</div>
										</div>

									</fieldset>
								</form>

							</div>
						</div>
					</div>
				</div>
				<!-- /.page-content -->

				<div class="col-md-3 reg-sidebar">
					<div class="reg-sidebar-inner text-center">
						
						@if (getSegment(2) != 'create' && auth()->check())
							@if (auth()->user()->id == $post->user_id)
								<div class="card sidebar-card panel-contact-seller">
									<div class="card-header">{{ t('Author\'s Actions') }}</div>
									<div class="card-content user-info">
										<div class="card-body text-center">
											<a href="{{ \App\Helpers\UrlGen::post($post) }}" class="btn btn-default btn-block">
												<i class="icon-right-hand"></i> {{ t('Return to the Ad') }}
											</a>
											@if (isset($countPackages) and isset($countPaymentMethods) and $countPackages > 0 and $countPaymentMethods > 0)
												<a href="{{ lurl('posts/' . $post->id . '/payment') }}" class="btn btn-success btn-block">
													<i class="icon-ok-circled2"></i> {{ t('Make It Premium') }}
												</a>
											@endif
										</div>
									</div>
								</div>
							@endif
						@endif

						<div class="card sidebar-card">
							<div class="card-header">
								<small><strong>{{ t('How to find quickly a candidate?') }}</strong></small>
							</div>
							<div class="card-content">
								<div class="card-body text-left">
									<ul class="list-check">
										<li> {{ t('Use a brief title and description of the ad') }} </li>
										<li> {{ t('Make sure you post in the correct category') }}</li>
										<li> {{ t('Add a logo to your ad') }}</li>
										<li> {{ t('Put a min and max salary') }}</li>
										<li> {{ t('Check the ad before publish') }}</li>
									</ul>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('after_styles')
    @include('layouts.inc.tools.wysiwyg.css')
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
    @include('layouts.inc.tools.wysiwyg.js')
	
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
	@if (file_exists(public_path() . '/assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js'))
		<script src="{{ url('assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js') }}" type="text/javascript"></script>
	@endif

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
                'submit': "{{ t('Update') }}"
            }
		};
		
		var stepParam = 0;
		
		/* Company */
		var postCompanyId = {{ old('company_id', (isset($postCompany) ? $postCompany->id : 0)) }};
		getCompany(postCompanyId);
		
		/* Categories */
        var category = {{ old('parent_id', (int)$postCatParentId) }};
        var categoryType = '{{ old('parent_type') }}';
        if (categoryType == '') {
            var selectedCat = $('select[name=parent_id]').find('option:selected');
            categoryType = selectedCat.data('type');
        }
        var subCategory = {{ old('category_id', (int)$post->category_id) }};
		
		/* Locations */
        var countryCode = '{{ old('country_code', !empty($post->country_code) ? $post->country_code : config('country.code')) }}';
        var adminType = '{{ config('country.admin_type', 0) }}';
        var selectedAdminCode = '{{ old('admin_code', ((isset($admin) and !empty($admin)) ? $admin->code : 0)) }}';
        var cityId = '{{ old('city_id', (int)$post->city_id) }}';
		
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
		});
	</script>
	<script src="{{ url('assets/js/app/d.select.category.js') . vTime() }}"></script>
	<script src="{{ url('assets/js/app/d.select.location.js') . vTime() }}"></script>
@endsection
