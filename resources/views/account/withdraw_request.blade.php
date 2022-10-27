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
		.fade:not(.show)
		{
			opacity: 1.9 !important;
		}

		/* The Modal (background) */
		.modal {
			display: none; /* Hidden by default */
			position: fixed; /* Stay in place */
			z-index: 1; /* Sit on top */
			padding-top: 100px; /* Location of the box */
			left: 0;
			top: 0;
			width: 100%; /* Full width */
			height: 100%; /* Full height */
			overflow: auto; /* Enable scroll if needed */
			background-color: rgb(0,0,0); /* Fallback color */
			background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
		}

		/* Modal Content */
		.modal-content {
			background-color: #fefefe;
			margin: auto;
			padding: 20px;
			border: 1px solid #888;
			width: 80%;
		}

		/* The Close Button */
		.close {
			color: #aaaaaa;
			float: right;
			font-size: 28px;
			font-weight: bold;
		}

		.close:hover,
		.close:focus {
			color: #000;
			text-decoration: none;
			cursor: pointer;
		}
	</style>
</style>

<div class="main-container">
	<div class="container">
		<div class="col-md-12">
			<div class="row">

				<div class="col-md-3 page-sidebar">
					@include('account.inc.sidebar')
				</div>
				<!--/.page-sidebar-->

				<div class="col-md-9 page-content">
					<div class="inner-box">

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

						@if(Session::has('error_amount'))
						<div class="alert alert-danger">{{ Session::get('error_amount') }}</div>
						@endif
						@if(Session::has('withdraw_success_request'))
						<div class="alert alert-success">{{ Session::get('withdraw_success_request') }}</div>
						@endif

						<h3 class="title-2">Withdraw Request</h3>

						<!--WITHDRAW REQUEST-->
						<form method="POST" name="user_wallet_transactions" action="/account/userwithdrawrequest">
							@csrf

							<div class="form-group col-md-6">
								<input type="hidden" name="enc_hash" value="<?= encrypt($user_id); ?>">

							</div>	

							<div class="form-group col-md-6">Amount</div>

							<div class="form-group col-md-6">
								<input type="number" class="form-control" name="amount" value="">
							</div>

							<div class="form-group col-md-6">Payment Method</div>
							<div class="form-group col-md-6">
								<div class="form-check form-check-inline pt-2">
									<input name="payment_method" id="Paypal" value="paypal" class="form-check-input" type="radio">
									<label class="form-check-label" for="Paypal">
										Paypal
									</label>
								</div>
								<div class="form-check form-check-inline pt-2">
									<input name="payment_method" value="bank" class="form-check-input" type="radio">
									<label class="form-check-label" for="gender_id">
										Bank
									</label>
								</div>
							</div>
							
							<div class="form-group col-md-6">
								<input type="submit" name="wallet_transaction_request" class="btn btn-default"/>
							</div>

						</form>

						<!-- WALLET FORM-->
						<!--WITHDRAW REQUEST-->

						<div style="clear:both"></div>
						<h3 class="title-2">Withdraw Request</h3>

						<!--- TRANSACTIONS TABLE-->
						<table class="table table-bordered table-striped display dt-responsive nowrap dataTable dtr-inline" id="user_withdraw_request" style="margin-top: 3%;">
							<thead>
								<tr>
									<th class="text-center">Sr.No</th>
									<th class="text-center">Amount</th>
									<th class="text-center">Status</th>
									<th class="text-center">Payment Method</th>
									<th class="text-center">Remarks</th>
									<th class="text-center">Date</th>

								</tr>
							</thead>
							<tbody>
								<?php if(count($user_wallet_requests) > 0): ?>
									<?php 
									$serial_number = 1;
									foreach($user_wallet_requests as $wallet_requests): ?>
										<tr>
											<td><?= $serial_number; ?></td>
											<td>{!! \App\Helpers\Number::money($wallet_requests->amount) !!}</td>
											<td><?= ucfirst($wallet_requests->status); ?>
											</td>
											<td>
<?php
if($wallet_requests->status=='approve'){
	if($wallet_requests->payment_method=='online'){
		echo 'Online';
	}elseif($wallet_requests->payment_method=='bank_neft'){
		echo 'Bank NEFT';
	}elseif($wallet_requests->payment_method=='check_draft'){
		echo 'Check/Draft';
	}else{
		echo ucfirst($wallet_requests->payment_method);
	}
}else{
if(!empty($wallet_requests->front_payment_method)){
	if($wallet_requests->front_payment_method=='online'){
		echo 'Online';
	}elseif($wallet_requests->front_payment_method=='bank_neft'){
		echo 'Bank NEFT';
	}elseif($wallet_requests->front_payment_method=='check_draft'){
		echo 'Check/Draft';
	}else{
		echo ucfirst($wallet_requests->front_payment_method);
	}

}
}
											 ?>
											 	
											 </td>
											
											<td><?= $wallet_requests->remarks; ?></td>
											<td><?= $wallet_requests->created_date; ?></td>
										</tr>
										<?php $serial_number++;
									endforeach; ?>
									<?php else: ?>
										<tr>
											<td>No Request Found!</td>
										</tr>
									<?php endif; ?>

								</tbody>
							</table>
							<!--- TRANSACTIONS TABLE-->

						</div>
					</div>
					<!--/.page-content-->

				</div>
			</div>
			<!--/.row-->
		</div>
		<!--/.container-->
	</div>
	<!-- /.main-container -->

	<!-------PAYPAL DETAILS FORM------->
	
	<div class="modal fade" id="myModalPaypal" role="dialog" style="margin-top: 4%;">
		<div class="modal-dialog">

			<div class="modal-content">

				<div class="modal-header">
					<h4 style="text-align:center;">Paypal</h4>
					<a href="javascript:void(0)" data-modal-id="myModalPaypal" class="close">&times;</a>
				</div>
				<div class="modal-body">
					<form action="/account/save_paypal_details" method="post">
						<input type="hidden" name="payment_type" value="bank">
						<div class="form-group">
							<label for="review">Paypal Email:</label>
							<input type="text" class="form-control" name="paypal_information" value="<?php if(!empty($user_paypal_details->paypal_email)):?><?= decrypt($user_paypal_details->paypal_email) ;?><?php endif;?>">
						</div>
						<button type="submit" class="btn btn-default paypal_submit">Submit</button>
					</div>
					<div class="modal-footer">

					</div>
				</form>
			</div>

		</div>
	</div>
	<!-------PAYPAL DETAILS FORM------->

	<!------------BANK DETAILS FORM ------->
	<div class="modal fade" id="myModalBank" role="dialog" style="margin-top: 4%;">
		<div class="modal-dialog">

			<div class="modal-content">

				<div class="modal-header">
					<h4 style="text-align:center;">Bank Information</h4>
					<a href="javascript:void(0)" data-modal-id="myModalBank" class="close">&times;</a>
				</div>
				<div class="modal-body">
					<form action="/account/save_bank_details" method="post">
						<input type="hidden" name="payment_type" value="bank">

						<div class="form-group">
							<input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php if(!empty($user_bank_details->first_name)):?><?= decrypt($user_bank_details->first_name) ;?><?php endif;?>">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?php if(!empty($user_bank_details->last_name)):?><?= decrypt($user_bank_details->last_name) ;?><?php endif;?>">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="bank_name" placeholder="Bank Name" value="<?php if(!empty($user_bank_details->bank_name)):?><?= decrypt($user_bank_details->bank_name) ;?><?php endif;?>">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="branch_address" placeholder="Branch Address" value="<?php if(!empty($user_bank_details->branch_address)):?><?= decrypt($user_bank_details->branch_address);?><?php endif;?>">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="ifsc_swift_code" placeholder="IFSC/SWIFT Code" value="<?php if(!empty($user_bank_details->ifsc_swift_code)):?><?= decrypt($user_bank_details->ifsc_swift_code) ;?><?php endif;?>">
						</div>

						<div class="form-group">
							<input type="text" class="form-control" name="phone_number" placeholder="Phone Number" value="<?php if(!empty($user_bank_details->phone_number)):?><?= decrypt($user_bank_details->phone_number);?><?php endif;?>">
						</div>
						<button type="submit" class="btn btn-default bank_details_submit">Submit</button>
					</div>
					<div class="modal-footer">

					</div>
				</form>
			</div>

		</div>
	</div>
	<!------------BANK DETAILS FORM ------->
	@endsection

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script>
		$(document).ready(function()
		{

			

			$(document).on('click','.btn.btn-default.bank_details_submit',function(event)
			{
				event.preventDefault();

				var first_name = $( "input[name='first_name']" ).val();
				var last_name = $( "input[name='last_name']" ).val();
				var bank_name = $( "input[name='bank_name']" ).val();
				var branch_address = $( "input[name='branch_address']" ).val();
				var ifsc_swift_code = $( "input[name='ifsc_swift_code']" ).val();
				var phone_number = $( "input[name='phone_number']" ).val();
				
				if(first_name == '')
				{
					toastr.error('First name is required.');
					return false;
				}

				if(last_name == '')
				{
					toastr.error('Last name is required.');
					return false;
				}

				if(bank_name == '')
				{
					toastr.error('Bank name is required.');
					return false;
				}

				if(branch_address == '')
				{
					toastr.error('Branch address is required.');
					return false;
				}

				if(ifsc_swift_code == '')
				{
					toastr.error('IFSC/SWIFT code is required.');
					return false;
				}

				if(phone_number == '')
				{
					toastr.error('Phone number is required.');
					return false;
				}

				$.ajax({
					url: "/account/save_bank_details",
					cache: false,
					method:'post',
					data:{first_name:first_name,last_name:last_name,bank_name:bank_name,branch_address:branch_address,ifsc_swift_code:ifsc_swift_code,phone_number:phone_number},
					success: function(data)
					{
						var jsonResponse = JSON.parse(data);

						if(jsonResponse == '1'){
							toastr.success('Details updated successfully');
						}else{
							toastr.error('Something went wrong. Please try again later.');
						}
						$('#myModalBank').hide();

					}

				});

			});

			$(document).on('click','.btn.btn-default.paypal_submit',function(event)
			{
				event.preventDefault();

				var paypal_email = $( "input[name='paypal_information']" ).val();

				if(paypal_email == '')
				{

					toastr.error('Email is required.');
					return false;

				}

				$.ajax({
					url: "/account/save_paypal_details",
					cache: false,
					method:'post',
					data:{paypal_email:paypal_email},
					success: function(data)
					{
						var jsonResponse = JSON.parse(data);

						if(jsonResponse == '1'){
							toastr.success('Details updated successfully');
						}else{
							toastr.error('Something went wrong. Please try again later.');
						}
						$('#myModalPaypal').hide();

					}

				});

			});

			
			$(document).on('click','#myModalPaypal a.close',function(event)
			{
				event.preventDefault();
				
				$('#myModalPaypal').hide();
			});

			$(document).on('click','#myModalBank a.close',function(event)
			{
				event.preventDefault();
				
				$('#myModalBank').hide();
			});

			$(document).on('click',"input[name='payment_method']",function()
			{

				var payment_method = $("input[name='payment_method']:checked").val();

				if(payment_method == 'paypal')
				{

					var modal_paypal = document.getElementById("myModalPaypal");
					modal_paypal.style.display = "block";

				}

				if(payment_method == 'bank')
				{
					var modal_bank = document.getElementById("myModalBank");
					modal_bank.style.display = "block";
				}

					// return false;
				});

		});
	</script>

	@section('after_scripts')
	@endsection

