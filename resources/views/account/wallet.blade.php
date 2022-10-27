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

	<div class="main-container">
		<div class="container">
			<div class="row">

				<div class="col-md-3 page-sidebar">
					@include('account.inc.sidebar')
				</div>
				<!--/.page-sidebar-->

				<div class="col-md-9 page-content">

					<div class="jumbotron">
						<div class="row">
							<div class="col-md ">

								<?php if (!empty($package_information)) {?>
									<div class="" role="alert">
										<h2>Package Information</h2>
										<h4><b>Package Type</b>: <?=$package_information->name;?></h4>
										<h4><b>Duration</b>: <?=$package_information->duration;?> Days</h4>
										<h4><b>Price</b>: <?=$package_information->price;?></h4>
									</div>

								<?php } else {?>
									<div class="" role="alert">
										<h4><b>No Package Information Available</b></h4>
									</div>
									 <?php $userPackage= \App\Helpers\UrlGen::get_user_package($user->id);?>
            
            @if (!empty($user->user_type_id) and $user->user_type_id == 1)
             @if($userPackage < 0)
          <p>Buy your Plan to bid and receive more projects.Paid members's profile get more visibilty</p>
              <a {!! ($pagePath=='') ? 'class="active btn btn-primary"' : '' !!} href="{{ lurl('employer-packages') }}" >
                 Buy Package
              </a>
           
            @else
            <p>Upgrade your Plan to bid and receive more projects.Paid members's profile get more visibilty</p>
           
              <a {!! ($pagePath=='') ? 'class="active btn btn-primary"' : '' !!} href="{{ lurl('employer-packages') }}" >
               Upgrade Package
              </a>
            
            @endif
            @endif

            @if (!empty($user->user_type_id) and $user->user_type_id == 2)
              @if($userPackage < 0)
              <p>Buy your Plan to bid and receive more projects.Paid members's profile get more visibilty</p>
           
              <a {!! ($pagePath=='') ? 'class="active btn btn-primary"' : '' !!} href="{{ lurl('influencer-packages') }}">
                Buy Package
              </a>
          
            @else
          <p>Upgrade your Plan to bid and receive more projects.Paid members's profile get more visibilty</p>
              <a {!! ($pagePath=='') ? 'class="active btn btn-primary"' : '' !!} href="{{ lurl('influencer-packages') }}">
                 Upgrade Package
              </a>
           

            @endif
            @endif
            <br>
            <br>
            <?php }?>



									<?php 
									if (isset($total_bids)): ?>

										<?php if (!empty($total_bids->no_of_bids)): ?>
											<div class="row">
												<div class="col-md"> <h4><b>Total Bids</b>: <span class="badge badge-info"><?=$total_bids->total_bid;?></span>&nbsp;&nbsp;
													<b>Bids Left</b>: <span class="badge badge-dark"><?=$total_bids->no_of_bids;?></span></h4></div>
												</div>
											<?php endif;?>
										<?php endif;?>
									




								</div>
								<div class="col-md jumbotron bg-light border mb-0 p-4"><h2><i class="icon-money"></i> Wallet Balance: <span class="color-success"><b> <?php if (!empty($wallet_amount)): ?><!-- {!! \App\Helpers\Number::money($wallet_amount) !!} -->
									<!-- {!! \App\Helpers\Number::money(\App\Helpers\UrlGen::get_user_wallet(auth()->user()->id)) !!} -->
									{!! \App\Helpers\UrlGen::get_user_wallet(auth()->user()->id) !!}
									<?php else: ?>{!! $wallet_amount !!}<?php endif;?></b></span></h2>
									<h4>Deposit Funds to Wallet:</h4>
									@if(Session::has('errormessage'))
									<div class="alert alert-success">{{ Session::get('errormessage') }}</div>
									@endif

									@if($errors->any())
									<div class="alert alert-danger">
										<p><strong>Opps Something went wrong</strong></p>
										<ul>
											@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
											@endforeach
										</ul>
									</div>
									@endif
									<form method="POST" class="form-inline" name="wallet_transactions2" action="/account/wallettransaction">
										@csrf
										<input type="hidden" name="enc_hash" value="<?=encrypt($user_id);?>">
										<div class="form-group mb-2">

											<input type="number" class="form-control"  name="amount" id="amount" placeholder="Enter Amount">
										</div>
										<button type="submit" class="btn btn-primary mb-2">Add Money</button>
									</form></div>
								</div>


							</div>

					<!-- <div class="inner-box">

This box will have only transaction related to - Fund credited by bank & Debited by requesting release fund


						<div style="clear:both"></div>

						<h2 class="title-2"><i class="icon-money"></i>Wallet </h2>

						<h3 class="title-2">Total Wallet Amount - <?php if (!empty($wallet_amount)): ?>Rs. <?=$wallet_amount;?><?php else: ?>Rs. 0.00<?php endif;?></h3>
						<div style="clear:both"></div>


						@if(Session::has('errormessage'))
						<div class="alert alert-success">{{ Session::get('errormessage') }}</div>
						@endif

						@if($errors->any())
						<div class="alert alert-danger">
							<p><strong>Opps Something went wrong</strong></p>
							<ul>
								@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
						@endif
						<!-- WALLET FORM-->
						<!---form method="POST" name="wallet_transactions" action="/account/wallettransaction">
							@csrf

							<div class="form-group col-md-6">
								<input type="hidden" name="enc_hash" value="<?=encrypt($user_id);?>">

							</div>

							<div class="form-group col-md-6">Amount</div>

							<div class="form-group col-md-6">
								<input type="number" class="form-control" name="amount" value="">
							</div>

							<div class="form-group col-md-6">
								<input type="submit" name="wallet_transactions" class="btn btn-default"/>
							</div>

						</form>

						 WALLET FORM

						</div> -->
					</div>
					<!--/.page-content-->

				</div>
				<!--/.row-->
			</div>
			<!--/.container-->
		</div>
		<!-- /.main-container -->
		@endsection


		@section('after_scripts')
		@endsection

