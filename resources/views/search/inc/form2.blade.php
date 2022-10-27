<?php
// Keywords
$keywords = rawurldecode(request()->get('q'));

// Category
/*$qCategory = (isset($gen) and !empty($gen)) ? $gen->id : request()->get('g');
*/
// Location
if (isset($city) and !empty($city)) {
	$qLocationId = (isset($city->id)) ? $city->id : 0;
	$qLocation = $city->name;
	$qAdmin = request()->get('r');
} else {
	$qLocationId = request()->get('l');
	$qLocation = (request()->filled('r')) ? t('area:') . rawurldecode(request()->get('r')) : request()->get('location');
	$qAdmin = request()->get('r');
}
?>
<!-- <div class="container">
	<div class="search-row-wrapper">
		<div class="container">
			<?php $attr = ['countryCode' => config('country.icode')]; ?>
			<form id="seach" name="search" action="{{ lurl(trans('routes.i-search', $attr), $attr) }}" method="GET">
				<div class="row m-0">
					
					
					<div class="col-xl-4 col-md-4 col-sm-12 col-xs-12">
						<input name="q" class="form-control keyword" type="text" placeholder="{{ t('What?') }}" value="{{ $keywords }}">
					</div>
					<div class="col-xl-3 col-md-3 col-sm-12 col-xs-12">
						<select name="g" id="gensearch" class="form-control selecter">
							<option value=""> Select gender</option>
							<option value="1"> Male</option>
							<option value="2">Female</option>
							
						</select>
					</div>
					<div class="col-xl-3 col-md-3 col-sm-12 col-xs-12">
						<select name="s" id="socialsearch" class="form-control selecter">
							<option value=""> Select social media profile</option>
							<option value="instagram"> Instagram</option>
							<option value="facebook">Facebook</option>
							<option value="twitter">Twitter</option>
							<option value="website">Website</option>
							<option value="tiktok">Tiktok</option>
							
						</select>
					</div>
					
					<div class="col-xl-3 col-md-3 col-sm-12 col-xs-12 search-col locationicon">
						<i class="icon-location-2 icon-append"></i>
						<input type="text" id="locSearch" name="location" class="form-control locinput input-rel searchtag-input has-icon tooltipHere"
							   placeholder="{{ t('Where?') }}" value="{{ $qLocation }}" title="" data-placement="bottom"
							   data-toggle="tooltip"
							   data-original-title="{{ t('Enter a city name OR a state name with the prefix ":prefix" like: :prefix', ['prefix' => t('area:')]) . t('State Name') }}">
					</div>
	
					<input type="hidden" id="lSearch" name="l" value="{{ $qLocationId }}">
					<input type="hidden" id="rSearch" name="r" value="{{ $qAdmin }}">
					
					<div class="col-xl-2 col-md-2 col-sm-12 col-xs-12">
						<button class="btn btn-block btn-primary">
							<i class="fa fa-search"></i> <strong>{{ t('Find') }}</strong>
						</button>
					</div>
					{!! csrf_field() !!}
				</div>
			</form>
		</div>
	</div>
</div> -->

<section class="jumbotron jumbotron-fluid text-left influencer-intro mt-0">
	
	<div class="container ">
	    <h2>Influencer Marketing Platform - Pay when you’re satisfied.</h2>
		<h1 class="lgtitle">Find & Hire the best <i><b>Influencer</b></i> for your brand marketing</h1>
		<p class="lead text">Connect & Collaborate with amazing influencers from around the world on our secure,
		flexible and cost-effective platform. Start your next social media marketing campaign with Influencers, Content Creators, Youtubers & Bloggers</p>
		<p>
			<form id="seach" name="search" action="{{ lurl(trans('routes.i-search', $attr), $attr) }}" method="GET">
				<div class="row">

					<div class="col-md-3 mb-3">
						{!! csrf_field() !!}
						<select class="custom-select d-block w-100" id="category" name="s" >
							<option value="">Social Platform</option>
							<option value="instagram" <?php if(isset($_GET['s']) && $_GET['s'] == 'instagram'):?><?= 'selected';?><?php endif; ?>>Instagram</option>
							<option value="facebook" <?php if(isset($_GET['s']) && $_GET['s'] == 'facebook'):?><?= 'selected';?><?php endif; ?>>Facebook</option>
								<option value="youtube" <?php if(isset($_GET['s']) && $_GET['s'] == 'youtube'):?><?= 'selected';?><?php endif; ?>>Youtube</option>
							<option value="twitter" <?php if(isset($_GET['s']) && $_GET['s'] == 'twitter'):?><?= 'selected';?><?php endif; ?>>Twitter</option>
							<option value="tiktok" <?php if(isset($_GET['s']) && $_GET['s'] == 'tiktok'):?><?= 'selected';?><?php endif; ?>>Tiktok</option>
						

						</select>

					</div>
					<div class="col-md-3 mb-3">

						<div class="input-group mb-3">
							<input type="text" id="locSearch" name="location" class="form-control locinput input-rel searchtag-input has-icon tooltipHere"
							   placeholder="{{ t('Where?') }}" value="{{ $qLocation }}" title="" data-placement="bottom"
							   data-toggle="tooltip"
							   data-original-title="{{ t('Enter a city name OR a state name with the prefix ":prefix" like: :prefix', ['prefix' => t('area:')]) . t('State Name') }}">
						</div>

					</div>
					<input type="hidden" id="lSearch" name="l" value="{{ $qLocationId }}">
					<input type="hidden" id="rSearch" name="r" value="{{ $qAdmin }}">
					
					<div class="col-md-3 mb-3">
						<input name="q" class="form-control keyword" type="text" placeholder="{{ t('What?') }}" value="{{ $keywords }}" placeholder="Search anything..." >
					</div>
					<div class="col-md-2 mb-3">
						<button class="btn btn-block btn-primary">
							<i class="fa fa-search"></i> <strong>{{ t('Find') }}</strong>
						</button>

					</div>
				</div>
			</div>
		</form>
	</section>
	@section('after_scripts')
	@parent
	<script>
		$(document).ready(function () {
			$('#locSearch').on('change', function () {
				if ($(this).val() == '') {
					$('#lSearch').val('');
					$('#rSearch').val('');
				}
			});
		});
	</script>
	
	@endsection
