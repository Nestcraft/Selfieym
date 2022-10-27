{{--

--}}
@extends('layouts.master')

@section('content')
	@include('common.spacer')
	<div class="main-container">
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

				<div class="col-xl-12 page-content">

					@if (Session::has('message'))
						<div class="inner-box category-content">
							<div class="row">
								<div class="col-xl-12">
									<div class="alert alert-success pgray alert-lg" role="alert">
										<h2 class="no-margin no-padding">&#10004; {{ t('Congratulations!') }}</h2>
										<p>{{ session('message') }} <a href="{{ lurl('/') }}">{{ t('Homepage') }}</a></p>
									</div>
								</div>
							</div>
						</div>
					@endif

				</div>
			</div>
		</div>
	</div>
@endsection
