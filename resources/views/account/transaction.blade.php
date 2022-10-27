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
					<div class="inner-box">

						<h2 class="title-2"><i class="icon-money"></i>Transactions</h2>
						<div style="clear:both"></div>
						
						<!--- TRANSACTIONS TABLE-->
						<table class="table table-bordered table-striped display dt-responsive nowrap dataTable dtr-inline" id="table_wallet_user" style="margin-top: 3%;">
							<thead>
								<tr>
									<th class="text-center">Sr.No</th>
									<!-- <th>Influencer {{ t('Name') }}</th> -->
									<th class="text-center">Amount</th>
									<th class="text-center">Transaction Type</th>
									<th class="text-center">Transaction Details</th>
									<th class="text-center">Date</th>

								</tr>
							</thead>
							<tbody>
								<?php if(count($user_transaction_history) > 0): 
									?>
									<?php 
									$serial_number = 1;
									foreach($user_transaction_history as $transaction_history): 
										?>
										<tr>
											<td><?= $serial_number; ?></td>
											<!-- @if(!empty($transaction_history->influencer_id))
											<td><a href="/influencer-profile/{{$transaction_history->influencer_id}}"><?=$transaction_history->name; ?></a></td>
											@else
											<td>Null</td>
											@endif -->
											<td>{!! \App\Helpers\Number::money($transaction_history->amount) !!}</td>
											<td><?= ucfirst($transaction_history->transaction_type); ?></td>
											<td><?= $transaction_history->remarks; ?></td>
											<td><?= $transaction_history->created_date; ?></td>
										</tr>
										<?php $serial_number++;
									endforeach; ?>
									<?php else: ?>
										<tr>
											<td>No Transaction Details Found!</td>
										</tr>
									<?php endif; ?>

								</tbody>
							</table>
							<!--- TRANSACTIONS TABLE-->

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
		
		
		@section('after_scripts')
		@endsection

		