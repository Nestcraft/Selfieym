<?php
// Search parameters
$queryString = (request()->getQueryString() ? ('?' . request()->getQueryString()) : '');

// Get the Default Language
$cacheExpiration = (isset($cacheExpiration)) ? $cacheExpiration : config('settings.optimization.cache_expiration', 86400);
$defaultLang = Cache::remember('language.default', $cacheExpiration, function () {
    $defaultLang = \App\Models\Language::where('default', 1)->first();
    return $defaultLang;
});

// Check if the Multi-Countries selection is enabled
$multiCountriesIsEnabled = false;
$multiCountriesLabel = '';
if (config('settings.geo_location.country_flag_activation')) {
	if (!empty(config('country.code'))) {
		if (\App\Models\Country::where('active', 1)->count() > 1) {
			$multiCountriesIsEnabled = true;
			$multiCountriesLabel = 'title="' . t('Select a Country') . '"';
		}
	}
}

// Logo Label
$logoLabel = '';
if (getSegment(1) != trans('routes.countries')) {
	$logoLabel = config('settings.app.app_name') . ((!empty(config('country.name'))) ? ' ' . config('country.name') : '');
}
?>
<div class="header">
	<nav class="navbar fixed-top navbar-site navbar-light bg-light navbar-expand-md" role="navigation">
		<div class="container">
			
			<div class="navbar-identity">
				{{-- Logo --}}
				<a href="{{ lurl('/') }}" class="navbar-brand logo logo-title">
					<img src="{{ imgUrl(config('settings.app.logo'), 'logo') }}"
						 alt="{{ strtolower(config('settings.app.app_name')) }}" class="tooltipHere main-logo" title="" data-placement="bottom"
						 data-toggle="tooltip"
						 data-original-title="{!! isset($logoLabel) ? $logoLabel : '' !!}"/>
				</a>
				{{-- Toggle Nav (Mobile) --}}
				<button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggler pull-right" type="button">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30" height="30" focusable="false">
						<title>{{ t('Menu') }}</title>
						<path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M4 7h22M4 15h22M4 23h22"></path>
					</svg>
				</button>
				{{-- Country Flag (Mobile) --}}
				@if (getSegment(1) != trans('routes.countries'))
					@if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled)
						@if (!empty(config('country.icode')))
							@if (file_exists(public_path().'/images/flags/24/' . config('country.icode') . '.png'))
								<button class="flag-menu country-flag d-block d-md-none btn btn-secondary hidden pull-right" href="#selectCountry" data-toggle="modal">
									<img src="{{ url('images/flags/24/' . config('country.icode') . '.png') . getPictureVersion() }}" style="float: left;">
									<span class="caret hidden-xs"></span>
								</button>
							@endif
						@endif
					@endif
				@endif
			</div>
			
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-left">
					{{-- Country Flag --}}
					@if (getSegment(1) != trans('routes.countries'))
						@if (config('settings.geo_location.country_flag_activation'))
							@if (!empty(config('country.icode')))
								@if (file_exists(public_path() . '/images/flags/32/' . config('country.icode') . '.png'))
									<li class="flag-menu country-flag tooltipHere hidden-xs nav-item" data-toggle="tooltip" data-placement="{{ (config('lang.direction') == 'rtl') ? 'bottom' : 'right' }}" {!! $multiCountriesLabel !!}>
										@if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled)
											<a href="#selectCountry" data-toggle="modal" class="nav-link">
												<img class="flag-icon" src="{{ url('images/flags/32/' . config('country.icode') . '.png') . getPictureVersion() }}">
												<span class="caret hidden-sm"></span>
											</a>
										@else
											<a style="cursor: default;">
												<img class="flag-icon no-caret" src="{{ url('images/flags/32/' . config('country.icode') . '.png') . getPictureVersion() }}">
											</a>
										@endif
									</li>
								@endif
							@endif
						@endif
					@endif
				<!-- 	<li class="nav-item">
						<?php $attr = ['countryCode' => config('country.icode')]; ?>
						<a href="/latest-influencers" class="nav-link">
							<i class="icon-th-list-2 hidden-sm"></i>Influencers
						</a>
					</li>

					<li class="nav-item">
						<?php $attr = ['countryCode' => config('country.icode')];
                           $alljoburl=lurl(trans('routes.v-search', $attr), $attr)
						?>
						<a href="<?php echo $alljoburl; ?>" class="nav-link">
							<i class="icon-th-list-2 hidden-sm"></i>Jobs
						</a>
					</li> -->
					<li class="nav-item">
						<?php $attr = ['countryCode' => config('country.icode')]; ?>
						<a href="/latest-influencers" class="nav-link">
							<i class="icon-group" hidden-sm"></i>Influencers
						</a>
					</li>

					<li class="nav-item">
						<?php $attr = ['countryCode' => config('country.icode')]; ?>
						<a href="/latest-jobs" class="nav-link">
							<i class="icon-th-list-2 hidden-sm"></i>Projects
						</a>
					</li>
					<li class="nav-item">
						<?php $attr = ['countryCode' => config('country.icode')]; ?>
						<a href="https://insight.selfieym.com" class="nav-link">
							<i class="icon-heart hidden-sm"></i>Blog
						</a>
					</li>

				</ul>
				
				<ul class="nav navbar-nav ml-auto navbar-right">
					@if (!auth()->check())
						<li class="nav-item">
							@if (config('settings.security.login_open_in_modal'))
								<a href="#quickLogin" class="nav-link" data-toggle="modal"><i class="icon-user fa"></i> {{ t('Log In') }}</a>
							@else
								<a href="{{ lurl(trans('routes.login')) }}" class="nav-link"><i class="icon-user fa"></i> {{ t('Log In') }}</a>
							@endif
						</li>
						<li class="nav-item">
							<a href="{{ lurl(trans('routes.register')) }}" class="nav-link"><i class="icon-user-add fa"></i> {{ t('Register') }}</a>
						</li>
					@else
						<li class="nav-item">
							@if (app('impersonate')->isImpersonating())
								<a href="{{ route('impersonate.leave') }}" class="nav-link">
									<i class="icon-logout hidden-sm"></i> {{ t('Leave') }}
								</a>
							@else
								<a href="{{ lurl(trans('routes.logout')) }}" class="nav-link">
									<i class="icon-logout hidden-sm"></i> {{ t('Log Out') }}
								</a></li>
								<?php if(Auth::user()->user_type_id==2){?>
								<li class="nav-item">
								<?php $id = Auth::user()->id;?>
								<a href="/influencer-profile/{{$id}}" class="nav-link">
									<i class="icon-star hidden-sm"></i> {{ t('My Profile') }}
								</a>
							
						</li>
						<?php } ?>
						@endif

						<li class="nav-item dropdown no-arrow">
							<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
								<i class="icon-user fa hidden-sm"></i>
								<span>{{ auth()->user()->name }}</span>
								<span class="badge badge-pill badge-important count-conversations-with-new-messages hidden-sm">0</span>
								<i class="icon-down-open-big fa hidden-sm"></i>
							</a>
							<ul id="userMenuDropdown" class="dropdown-menu user-menu dropdown-menu-right shadow-sm">
								<li class="dropdown-item active"><a href="{{ lurl('account') }}"><i class="icon-home"></i> {{ t('Personal Home') }}</a></li>
								
								<li class="dropdown-item <?php if($_SERVER['REQUEST_URI'] == '/account/wallet'): ?><?= 'active';?><?php endif; ?>"><a style="margin-left: 4px;" href="{{ lurl('account/wallet')}}"><i class="fa fa-wallet"></i>Wallet {!! \App\Helpers\UrlGen::get_user_wallet(auth()->user()->id) !!} </a></li>
								@if (in_array(auth()->user()->user_type_id, [1]))
									<li class="dropdown-item"><a href="{{ lurl('account/companies') }}"><i class="icon-town-hall"></i> {{ t('My companies') }} </a></li>
									<li class="dropdown-item"><a href="{{ lurl('account/my-posts') }}"><i class="icon-th-thumb"></i> {{ t('My ads') }} </a></li>
									<li class="dropdown-item"><a href="{{ lurl('account/pending-approval') }}"><i class="icon-hourglass"></i> {{ t('Pending approval') }} </a></li>
									<li class="dropdown-item"><a href="{{ lurl('account/archived') }}"><i class="icon-folder-close"></i> {{ t('Archived ads') }}</a></li>
									<li class="dropdown-item">
										<a href="{{ lurl('account/conversations') }}">
											<i class="icon-mail-1"></i> {{ t('Conversations') }}
											<span class="badge badge-pill badge-important count-conversations-with-new-messages">0</span>
										</a>
									</li>
									<li class="dropdown-item"><a href="{{ lurl('account/transactions') }}"><i class="icon-money"></i> {{ t('Transactions') }}</a></li>
								@endif
								@if (in_array(auth()->user()->user_type_id, [2]))
								<li class="dropdown-item"><a href="{{ lurl('account/socialprofile/edit') }}"><i class="icon-star"></i> {{ t('My Social Profile') }} </a></li>
									<li class="dropdown-item"><a href="{{ lurl('account/resumes') }}"><i class="icon-attach"></i> {{ t('My resumes') }} </a></li>
									<li class="dropdown-item"><a href="{{ lurl('account/favourite') }}"><i class="icon-heart"></i> {{ t('Favourite jobs') }} </a></li>
									<li class="dropdown-item"><a href="{{ lurl('account/saved-search') }}"><i class="icon-star-circled"></i> {{ t('Saved searches') }} </a></li>
									<li class="dropdown-item">
										<a href="{{ lurl('account/conversations') }}">
											<i class="icon-mail-1"></i> {{ t('Conversations') }}
											<span class="badge badge-pill badge-important count-conversations-with-new-messages">0</span>
										</a>
									</li>
								@endif
								<!-- NEW NOTIICATION BAR-->
								<li class="dropdown-item">
										<a href="{{ lurl('account/notifications') }}">
											<i class="icon-mail-1"></i>Notifications
											<span class="badge badge-pill badge-important count-conversations-with-new-notifications">0</span>
										</a>
									</li>
								<!-- NEW NOTIICATION BAR-->
							</ul>
						</li>
					@endif
					
					@if (!auth()->check())
						<li class="nav-item postadd">
							@if (config('settings.single.guests_can_post_ads') != '1')
								<a class="btn btn-block btn-post btn-add-listing" href="#quickLogin" data-toggle="modal">
									<i class="fa fa-plus-circle"></i> {{ t('Create a Job ad') }}
								</a>
							@else
								<a class="btn btn-block btn-post btn-add-listing" href="{{ \App\Helpers\UrlGen::addPost() }}">
									<i class="fa fa-plus-circle"></i> {{ t('Create a Job ad') }}
								</a>
							@endif
						</li>
					@else
						@if (in_array(auth()->user()->user_type_id, [1]))
							<li class="nav-item postadd">
								<a class="btn btn-block btn-post btn-add-listing" href="{{ \App\Helpers\UrlGen::addPost() }}">
									<i class="fa fa-plus-circle"></i> {{ t('Create a Job ad') }}
								</a>
							</li>
						@endif
					@endif
					
					@include('layouts.inc.menu.select-language')
					
				</ul>
				<meta name="csrf-token" content="{{ csrf_token() }}">
			</div>
		</div>
	</nav>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" type="text/javascript"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">

<script type="text/javascript">
	function checkNewNotifications() {

	var t = $("span.badge.badge-pill.badge-important.count-conversations-with-new-notifications").html();

	$.ajax({
		method: "POST",
		url: siteUrl + "/ajax/messages/notification",
		data: {
			languageCode: languageCode,
			oldValue: 0,
			_token: $("input[name=_token]").val()
		}
	}).done(function(t) {

		if(t.countConversationsWithNewMessages > 0){
           toastr.success('New Notification .Check notification menu!');
           $('a.dropdown-toggle.nav-link').trigger('click');
		}

		return void 0 !== t.logged && ("0" != t.logged && (t.countConversationsWithNewMessages > 0 ? (t.countConversationsWithNewMessages >= t.countLimit ? $("span.badge.badge-pill.badge-important.count-conversations-with-new-notifications").html(t.countConversationsWithNewMessages + "+") : $("span.badge.badge-pill.badge-important.count-conversations-with-new-notifications").html(t.countConversationsWithNewMessages), $("span.badge.badge-pill.badge-important.count-conversations-with-new-notifications").show(), t.oldValue > 0 && document.getElementById("reloadBtn") && t.countConversationsWithNewMessages != t.oldValue && $("#reloadBtn").show()) : ($("span.badge.badge-pill.badge-important.count-conversations-with-new-notifications").html("0"), $("span.badge.badge-pill.badge-important.count-conversations-with-new-notifications").hide()), !1));

	})
}

	$(document).ready(function(){
		checkNewNotifications();
	});
</script>