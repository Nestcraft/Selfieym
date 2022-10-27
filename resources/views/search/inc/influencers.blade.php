<style type="text/css">
	.influencer-intro {
    background: rgb(156,209,212);
    background: linear-gradient(90deg, rgba(156,209,212,1) 0%, rgba(196,221,216,1) 100%);
    margin-top: -18px!important;

}
.frontfeatre {
    position: absolute;
    right: 10px;
    top: 10px;
}
</style>
<link href="/public/css/custom.css" rel="stylesheet"/>
<div class="">
	<div class="container">

		<div class="row">

			<?php
			if (!isset($cacheExpiration)) {
				$cacheExpiration = (int)config('settings.optimization.cache_expiration');

			}
			?>
			@if (isset($paginator) and $paginator->getCollection()->count() > 0)
			<?php

			if (!isset($cats)) {
				$cats = collect([]);
			}
			echo '<style>.breadcrumb-list.text-center-xs{display:none;}</style>';
			foreach($paginator->getCollection() as $key => $post):
				if (!$countries->has($post->country_code)) continue;

		// Convert the created_at date to Carbon object
				$post->created_at = (new \Date($post->created_at))->timezone(config('timezone.id'));
				$post->created_at = $post->created_at->ago();

		// Category
				$cacheId = 'category.' . $post->category_id . '.' . config('app.locale');
				$liveCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
					$liveCat = \App\Models\Category::find($post->category_id);
					return $liveCat;
				});

		// Check parent
				if (empty($liveCat->parent_id)) {
					$liveCatParentId = $liveCat->id;
				} else {
					$liveCatParentId = $liveCat->parent_id;
				}

		// Check translation
				if ($cats->has($liveCatParentId)) {
					$liveCatName = $cats->get($liveCatParentId)->name;
				} else {
					$liveCatName = $liveCat->name;
				}

		// Get the Post's Type
		/*$cacheId = 'postType.' . $post->post_type_id . '.' . config('app.locale');
		$postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
			$postType = \App\Models\PostType::findTrans($post->post_type_id);
			return $postType;
		});*/
		/*if (empty($postType)) continue;
		*/
		// Get the Post's Salary Type
		/*$cacheId = 'salaryType.' . $post->salary_type_id . '.' . config('app.locale');
		$salaryType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
			$salaryType = \App\Models\SalaryType::findTrans($post->salary_type_id);
			return $salaryType;
		});*/
		/*if (empty($salaryType)) continue;
		*/
		// Get the Post's City
		$cacheId = config('country.code') . '.city.' . $post->city_id;
		$city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
			$city = \App\Models\City::find($post->city_id);
			return $city;
		});
		if (empty($city)) continue;
		?>
		
		<div class="col-md-4">
			<div class="card mb-4 box-shadow">
				<?php if (isset($post->is_featured) && $post->is_featured == 1): ?>

<div class="profile-featured frontfeatre"><span class="label badge-danger"> Featured</span></div>

<?php endif;?>
				<a href="/influencer-profile/{{$post->user_id}}">
					<img class="card-img-top" style=" height:160px;" img src="/images/profile_images/<?= $post->profile_image;?>" alt="<?= $post->name;?>">
				</a>
				<div class="card-body">
					<h3><?= $post->name;?></h3>
					<ul class="list-unstyled list-inline rating mb-0 review">
						<?php $influencer_rating = \App\Helpers\UrlGen::get_influencer_rating($post->user_id); ?>

						<?php
						
						for ($count=1;$count <= $influencer_rating;$count++) {
							
							echo '<li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>';
						 }
						?>

					</ul>
					<?php if(!empty($post->skill_expertise)):?>

						<?php 
						$i=1; 
						$skill_expertise = explode(',',$post->skill_expertise);
						if(!empty($skill_expertise)):?>
							 
							<?php foreach($skill_expertise as $skills):
								if($i<=3){ ?>
								<a href="javascript:void(0)"  class="label badge-dark" style="margin:2px;"><?= strtoupper($skills);?></a>
							<?php } $i++; endforeach;?>
						<?php endif; ?>
					<?php endif; ?>
<br><br>
					<b>City:</b> @if(!empty($city->name)){{ $city->name }}@endif
					<br>
					<b>Industry:</b> <?php if (!empty($post->catname)): ?><?=$post->catname;?><?php endif;?>  

				</div>
				<div class="bottomfooter">
					
					<p><b>STARTING AT:</b> @if(!empty($post->min_fee))<span class="badge badge-success"><?= \App\Helpers\Number::money($post->min_fee); ?></span> @endif
						<span class="fa-pull-right"><i class="fa fa-users" aria-hidden="true"></i> 
							<?php
							if(isset($post->facebook_followers) && $post->facebook_followers > 0)
							{

								$facebook_followers =  str_replace('K','',$post->facebook_followers);
							}else{

								$facebook_followers = 0;

							}

							if(isset($post->instagram_followers) && $post->instagram_followers > 0)
							{

								$instagram_followers =  str_replace('K','',$post->instagram_followers);
							}else{

								$instagram_followers = 0;

							}


							if(isset($post->quora_followers) && $post->quora_followers > 0)
							{

								$quora_followers =  str_replace('K','',$post->quora_followers);
							}else{

								$quora_followers = 0;

							}


							if(isset($post->twitter_followers) && $post->twitter_followers > 0)
							{

								$twitter_followers =  str_replace('K','',$post->twitter_followers);
							}else{

								$twitter_followers = 0;

							}

							if(isset($post->youtube_subscribers) && $post->youtube_subscribers > 0)
							{

								$youtube_subscribers =  str_replace('K','',$post->youtube_subscribers);
							}else{

								$youtube_subscribers = 0;

							}

							?>
							
							<b><?php echo (int)$facebook_followers + (int)$instagram_followers + (int)$twitter_followers + (int)$quora_followers + (int)$youtube_subscribers;?> K</b> 
						
				</span></p>
			</div>
		</div>
	</div>

	<!-- 	<div class="item-list job-item">
			<div class="row">
				<div class="col-md-1 col-sm-2 no-padding photobox">
					<div class="add-image">
						<a href="/influencer-profile/{{$post->user_id}}">
							<img class="img-thumbnail no-margin" src="/images/profile_images/<?= $post->profile_image;?>" alt="{{ $post->name }}">
						</a>
					</div>
				</div>
				
				<div class="col-md-8 col-sm-10 add-desc-box">
					<div class="add-details jobs-item">
						<h5 class="company-title">
							
						</h5>
						<h4 class="job-title">
							<a href="{{ \App\Helpers\UrlGen::post($post) }}"> {{ \Illuminate\Support\Str::limit($post->name, 70) }} </a>
						</h4>
						<span class="info-row">
							<span class="date"><i class="icon-clock"></i> {{ $post->created_at }}</span>
							<span class="item-location">
								<i class="icon-location-2"></i>&nbsp;
								<a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(request()->except(['l', 'location']), ['l'=>$post->city_id]), null, false) !!}">
									{{ $city->name }}
								</a>
								{{ (isset($post->distance)) ? '- ' . round($post->distance, 2) . getDistanceUnit() : '' }}
							</span>
							<span class="post_type"><i class="icon-tag"></i> </span>


							<div class="jobs-desc">

							</div>

							<div class="job-actions">
								<ul class="list-unstyled list-inline">
									@if (auth()->check())
									@if (\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() <= 0)
									<li id="{{ $post->id }}">
										<a class="save-job" id="save-{{ $post->id }}" href="javascript:void(0)">
											<span class="far fa-heart"></span>
											{{ t('Save Job') }}
										</a>
									</li>
									@else
									<li class="saved-job" id="{{ $post->id }}">
										<a class="saved-job" id="saved-{{ $post->id }}" href="javascript:void(0)">
											<span class="fa fa-heart"></span>
											{{ t('Saved Job') }}
										</a>
									</li>
									@endif
									@else
									<li id="{{ $post->id }}">
										<a class="save-job" id="save-{{ $post->id }}" href="javascript:void(0)">
											<span class="far fa-heart"></span>
											{{ t('Save Job') }}
										</a>
									</li>
									@endif
									<li>
										<a class="email-job" data-toggle="modal" data-id="{{ $post->id }}" href="#sendByEmail" id="email-{{ $post->id }}">
											<i class="fa fa-envelope"></i>
											{{ t('Email Job') }}
										</a>
									</li>
								</ul>
							</div>

						</div>
					</div>

					<div class="col-md-3 text-right"><h4>
						<span class="salary">
							<i class="icon-money"></i>&nbsp;
							@if ((isset($post->salary_min) && $post->salary_min > 0) or (isset($post->salary_min) && $post->salary_max > 0))
							@if ($post->salary_min > 0)
							{!! \App\Helpers\Number::money($post->salary_min) !!}
							@endif
							@if ($post->salary_max > 0)
							@if ($post->salary_min > 0)
							&nbsp;-&nbsp;
							@endif
							{!! \App\Helpers\Number::money($post->salary_max) !!}
							@endif
							@else
							{!! \App\Helpers\Number::money('--') !!}
							@endif
							@if (!empty($salaryType))
							{{ t('per') }} {{ $salaryType->name }}
							@endif
						</span>
					</span></h2>
				</div>
				
			</div>
		</div> -->
		<!--/.job-item-->
	<?php endforeach; ?>

	@else
	<div class="p-4" style="width: 100%;">
		@if (\Illuminate\Support\Str::contains(\Route::currentRouteAction(), 'Search\CompanyController'))
		{{ t('No result. Refine your search using other criteria.') }}
		@else
		{{ t('No result. Refine your search using other criteria.') }}
		@endif
	</div>
	@endif
</div>
</div>
</div>
<style>

	.topspace{
		margin-top: 100px;
	}

	.influencer-intro2 {
		background: rgb(183,218,223);
		background: linear-gradient(90deg, rgba(183,218,223,1) 0%, rgba(241,226,209,1) 100%);
	}

	.influencer-intro {background: rgb(156,209,212);
		background: linear-gradient(90deg, rgba(156,209,212,1) 0%, rgba(196,221,216,1) 100%);}

		.search-bar1{
			background-color:#cccccc; padding-top:10px;
		}

		.lgtitle { font-size:46px; font-family: "Roboto Condensed", Helvetica, Arial, sans-serif;  font-weight:thin;
		line-height: 1.4;color:#1d9066;
	}
	.stl {font-family:helvetica neue,Helvetica,Arial,sans-serif; font-weight:thin;
	}

	.bottomfooter {
		background: #f3f1f1;
		padding: 12px 10px;
		padding-bottom: 0px;
	}
	.review{color:#5fb48d; font-size1:16px;}

	.profile-featured-tag {
		position: absolute;
		top: 14px;
		right: 20px;
	}

</style>
@section('modal_location')
@parent
@include('layouts.inc.modal.send-by-email')
@endsection

@section('after_scripts')
@parent
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

	$(document).ready(function ()
	{
		/* Get Post ID */
		$('.email-job').click(function(){
			var postId = $(this).attr("data-id");
			$('input[type=hidden][name=post]').val(postId);
		});

		@if (isset($errors) and $errors->any())
		@if (old('sendByEmailForm')=='1')
		$('#sendByEmail').modal();
		@endif
		@endif
	})
</script>
@endsection
