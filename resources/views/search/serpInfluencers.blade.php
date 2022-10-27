 <style type="text/css">
 	.horizonatal{
 		display: flex;
 	}
 </style>
 
<?php
	$fullUrl = rawurldecode(url(request()->getRequestUri()));
    $tmpExplode = explode('?', $fullUrl);
    $fullUrlNoParams = current($tmpExplode);
    
?>

@extends('layouts.master')

@section('search')
	@parent
	@include('search.inc.form2')
@endsection

@section('content')
	<div class="main-container">
		
		@include('search.inc.breadcrumbs')
		
		<?php if (\App\Models\Advertising::where('slug', 'top')->count() > 0): ?>
			@include('layouts.inc.advertising.top', ['paddingTopExists' => true])
		<?php
			$paddingTopExists = false;
		else:
			if (isset($paddingTopExists) and $paddingTopExists) {
				$paddingTopExists = false;
			}
		endif;
		?>

		@include('common.spacer')
		
		<div class="container">

			<div class="row">
				@if (Session::has('flash_notification'))
					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
			</div>

			<div class="row">
				
				<!-- Sidebar -->
				@include('search.inc.sidebar2')
				
				<!-- Content -->
				<div class="col-md-9 page-content col-thin-left">
					<div class="category-list">
						<div class="tab-box clearfix">

							<!-- Nav tabs -->
							<div class="col-xl-12 box-title no-border">
								<div class="inner">
									<h2>
										<small>{{ $count->get('all') }} {{ t('Influencers Found') }}</small>
									</h2>
								</div>
							</div>

							<!-- Mobile Filter bar -->
							<div class="col-xl-12 mobile-filter-bar">
								<ul class="list-unstyled list-inline no-margin no-padding">
									<li class="filter-toggle">
										<a class="">
											<i class="icon-th-list"></i> {{ t('Filters') }}
										</a>
									</li>
									<li>
										<div class="dropdown">
											<a data-toggle="dropdown" class="dropdown-toggle">{{ t('Sort by') }}</a>
											<ul class="dropdown-menu">
												<li>
													<a href="{!! qsurl($fullUrlNoParams, request()->except(['orderBy', 'distance']), null, false) !!}" rel="nofollow">
														{{ t('Sort by') }}
													</a>
												</li>
												<li>
													<a href="{!! qsurl($fullUrlNoParams, array_merge(request()->except('orderBy'), ['orderBy'=>'relevance']), null, false) !!}" rel="nofollow">
														{{ t('Relevance') }}
													</a>
												</li>
												<li>
													<a href="{!! qsurl($fullUrlNoParams, array_merge(request()->except('orderBy'), ['orderBy'=>'date']), null, false) !!}" rel="nofollow">
														{{ t('Date') }}
													</a>
												</li>
												@if (isset($isCitySearch) and $isCitySearch and isset($distanceRange) and !empty($distanceRange))
													@foreach($distanceRange as $key => $value)
													<li>
														<a href="{!! qsurl($fullUrlNoParams, array_merge(request()->except('distance'), ['distance' => $value]), null, false) !!}" rel="nofollow">
															{{ t('Around :distance :unit', ['distance' => $value, 'unit' => getDistanceUnit()]) }}
														</a>
													</li>
													@endforeach
												@endif
											</ul>

										</div>
									</li>
								</ul>
							</div>
							<div class="menu-overly-mask"></div>
							<!-- Mobile Filter bar End-->


							<div class="tab-filter hide-xs horizonatal">
								<!---date---->
								<!-- <select id="orderBy" class="niceselecter select-sort-by" data-style="btn-select" data-width="auto">
									<option value="{!! qsurl($fullUrlNoParams, request()->except(['orderBy', 'distance']), null, false) !!}">{{ t('Sort by') }}</option> -->
									<!-- <option{{ (request()->get('orderBy')=='relevance') ? ' selected="selected"' : '' }}
											value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('orderBy'), ['orderBy'=>'relevance']), null, false) !!}">
										{{ t('Relevance') }}
									</option>  -->
						<!-- 			<option{{ (request()->get('orderBy')=='date') ? ' selected="selected"' : '' }}
											value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('orderBy'), ['orderBy'=>'date']), null, false) !!}">
										{{ t('Date') }}
									</option>
									@if (isset($isCitySearch) and $isCitySearch and isset($distanceRange) and !empty($distanceRange))
										@foreach($distanceRange as $key => $value)
											<option{{ (request()->get('distance', config('settings.listing.search_distance_default', 100))==$value) ? ' selected="selected"' : '' }}
													value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('distance'), ['distance' => $value]), null, false) !!}">
												{{ t('Around :distance :unit', ['distance' => $value, 'unit' => getDistanceUnit()]) }}
											</option>
										@endforeach
									@endif
								</select> -->
								<!----end date--->
								<!---ageeee-->

								<select id="AgeBy" class="niceselecter select-sort-by" data-style="btn-select" data-width="auto">
									<option value="">Sort By Age</option>
									
									
													<!-- <option  {{ (request()->get('ageBy')=='any') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('ageBy'), ['ageBy'=>'any']), null, false) !!}">Any</option> -->
													<option  {{ (request()->get('ageBy')=='less_25') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('ageBy'), ['ageBy'=>'less_25']), null, false) !!}">Below Age of 25</option>
													<option  {{ (request()->get('ageBy')=='less_40') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('ageBy'), ['ageBy'=>'less_40']), null, false) !!}">Below Age of 40</option>
													<option  {{ (request()->get('ageBy')=='less_50') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('ageBy'), ['ageBy'=>'less_50']), null, false) !!}">Below Age of 50</option>
													<option  {{ (request()->get('ageBy')=='great_50') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('ageBy'), ['ageBy'=>'great_50']), null, false) !!}">Above 50</option>
												
									
								</select>
								<!----end --age--->
								<!---gender-->

								<select id="GenderBy" class="niceselecter select-sort-by" data-style="btn-select" data-width="auto">
									<option value="">Sort By Gender</option>
									
									
													
													<option  {{ (request()->get('genderBy')=='male') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('genderBy'), ['genderBy'=>'male']), null, false) !!}">Male</option>
													<option  {{ (request()->get('genderBy')=='female') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('genderBy'), ['genderBy'=>'female']), null, false) !!}">Female</option>
													<!-- <option  {{ (request()->get('genderBy')=='any') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('genderBy'), ['genderBy'=>'any']), null, false) !!}">Any</option> -->
													
												
									
								</select>
<!--end-gender-->
			<!---facebook follower-->

								<select id="faceebookFollow" class="niceselecter select-sort-by" data-style="btn-select" data-width="auto">
									<option value="">Sort by Followers</option>
									
									
													
													<option  {{ (request()->get('faceebookfollowBy')=='5k-10k') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'5k-10k']), null, false) !!}">5k-10k</option>
													<option  {{ (request()->get('faceebookfollowBy')=='10k-25k') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'10k-25k']), null, false) !!}">10k-25k</option>
													<option  {{ (request()->get('faceebookfollowBy')=='25k-50k') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'25k-50k']), null, false) !!}">25k-50k</option>
													<option  {{ (request()->get('faceebookfollowBy')=='50k-100k') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'50k-100k']), null, false) !!}">50k-100k</option>
													<option  {{ (request()->get('faceebookfollowBy')=='100k-500k') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'100k-500k']), null, false) !!}">100k-500k</option>
													<option  {{ (request()->get('faceebookfollowBy')=='500k-1000k') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'500k-1000k']), null, false) !!}">500k-1000k</option>
													<option  {{ (request()->get('faceebookfollowBy')=='1M-2M') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'1M-2M']), null, false) !!}">1M-2M</option>
													<option  {{ (request()->get('faceebookfollowBy')=='2M-5M') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'2M-5M']), null, false) !!}">2M-5M</option>
													<option  {{ (request()->get('faceebookfollowBy')=='5M-10M') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'5M-10M']), null, false) !!}">5M-10M</option>
													<option  {{ (request()->get('faceebookfollowBy')=='10-25M') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'1M-2M']), null, false) !!}">1M-2M</option>
													<option  {{ (request()->get('faceebookfollowBy')=='2M-5M') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'2M-5M']), null, false) !!}">2M-5M</option>
													<option  {{ (request()->get('faceebookfollowBy')=='5M-10M') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'5M-10M']), null, false) !!}">5M-10M</option>
													<option  {{ (request()->get('faceebookfollowBy')=='10-25M') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'10-25M']), null, false) !!}">10-25M</option>
													<option  {{ (request()->get('faceebookfollowBy')=='25M-50M') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'25M-50M']), null, false) !!}">25M-50M</option>
													<option  {{ (request()->get('faceebookfollowBy')=='50') ? ' selected="selected"' : '' }} value="{!! qsurl($fullUrlNoParams, array_merge(request()->except('faceebookfollowBy'), ['faceebookfollowBy'=>'50']), null, false) !!}">50M+</option>		
									
								</select>
<!--end-follower-->

							</div>
							

						</div>

						<div class="listing-filter hidden-xs">
							<div class="pull-left col-sm-6 col-xs-12">
								<div class="breadcrumb-list text-center-xs">
									{!! (isset($htmlTitle)) ? $htmlTitle : '' !!}
								</div>
							</div>
							<div class="pull-right col-sm-6 col-xs-12 text-right text-center-xs listing-view-action">
								@if (!empty(request()->all()))
									<?php $attr = ['countryCode' => config('country.icode')]; ?>
									<a class="clear-all-button text-muted" href="{!! lurl(trans('routes.i-search', $attr), $attr) !!}">{{ t('Clear all') }}</a>
								@endif
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="adds-wrapper jobs-list">
							@include('search.inc.influencers')
						</div>

						<div class="tab-box save-search-bar text-center">
							@if (request()->filled('q') and request()->get('q') != '' and $count->get('all') > 0)
								<a name="{!! qsurl($fullUrlNoParams, request()->except(['_token', 'location']), null, false) !!}" id="saveSearch" count="{{ $count->get('all') }}">
									<i class="icon-star-empty"></i> {{ t('Save Search') }}
								</a>
							@else
								<a href="#"> &nbsp; </a>
							@endif
						</div>
					</div>
					
					<nav class="pagination-bar mb-5 pagination-sm" aria-label="">
						{!! $paginator->appends(request()->query())->render() !!}
					</nav>

					@if (!auth()->check())
						<div class="post-promo text-center">
							<h2> {{ t('Looking for a job?') }} </h2>
							<h5> {{ t('Upload your Resume and easily apply to jobs from any device!') }} </h5>
							<a href="{{ lurl(trans('routes.register')) . '?type=2' }}" class="btn btn-border btn-post btn-add-listing">
								{{ t('Add your Resume') }} <i class="icon-attach"></i>
							</a>
						</div>
					@endif

				</div>
				
				<div style="clear:both;"></div>

				<!-- Advertising -->
				@include('layouts.inc.advertising.bottom')

			</div>

		</div>
	</div>
@endsection

@section('modal_location')
	@parent
	@include('layouts.inc.modal.location')
@endsection

@section('after_scripts')
	<script>
        $(document).ready(function () {
			$('#postType a').click(function (e) {
				e.preventDefault();
				var goToUrl = $(this).attr('href');
				redirect(goToUrl);
			});
			$('#orderBy').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});
			$('#AgeBy').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});
			$('#GenderBy').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});
			$('#faceebookFollow').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});

		});
	</script>
@endsection
