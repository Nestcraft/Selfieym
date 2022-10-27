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

				@if (Session::has('flash_notification'))
				<div class="col-xl-12">
					<div class="row">
						<div class="col-xl-12">
							@include('flash::message')
						</div>
					</div>
				</div>
				@endif

				<div class="col-md-3 page-sidebar">
					@include('account.inc.sidebar')
				</div>
				<!--/.page-sidebar-->
				<style>

					.star-rating {
						border:solid 1px #ccc;
						display:flex;
						flex-direction: row-reverse;
						font-size:1.5em;
						justify-content:space-around;
						padding:0 .2em;
						text-align:center;
						width:5em;
					}

					.star-rating input {
						display:none;
					}

					.star-rating label {
						color:#ccc;
						cursor:pointer;
					}

					.star-rating :checked ~ label {
						color:#f90;
					}

					.star-rating label:hover,
					.star-rating label:hover ~ label {
						color:#fc0;
					}

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
					.modal-backdrop.fade.show{
						display:none;
					}
				</style>

				<div class="col-md-9 page-content">
					@if (\Session::has('RateMessageSuccess'))
					<div class="alert alert-success">
						<ul>
							<li>{!! \Session::get('RateMessageSuccess') !!}</li>
						</ul>
					</div>
					@endif
					<div class="inner-box">
						<h2 class="title-2">
							<i class="icon-mail"></i> {{ t('Conversations') }}
						</h2>
						<div id="reloadBtn" class="mb30" style="display: none;">
							<a href="" class="btn btn-primary" class="tooltipHere" title="" data-placement="{{ (config('lang.direction')=='rtl') ? 'left' : 'right' }}"
							data-toggle="tooltip"
							data-original-title="{{ t('Reload to see New Messages') }}"><i class="icon-arrows-cw"></i> {{ t('Reload') }}</a>
							<br><br>
						</div>

						<div style="clear:both"></div>

						<div class="table-responsive">
							<form name="listForm" method="POST" action="{{ lurl('account/'.$pagePath.'/delete') }}">
								{!! csrf_field() !!}
								<div class="table-action">
									<label for="checkAll">
										<input type="checkbox" id="checkAll">
										{{ t('Select') }}: {{ t('All') }} |
										<button type="submit" class="btn btn-sm btn-default delete-action">
											<i class="fa fa-trash"></i> {{ t('Delete') }}
										</button>
									</label>
									<div class="table-search pull-right col-sm-7">
										<div class="form-group">
											<div class="row">
												<label class="col-sm-5 control-label text-right">{{ t('Search') }} <br>
													<a title="clear filter" class="clear-filter" href="#clear">[{{ t('clear') }}]</a>
												</label>
												<div class="col-sm-7 searchpan">
													<input type="text" class="form-control" id="filter">
												</div>
											</div>
										</div>
									</div>
								</div>

								<table id="addManageTable" class="table table-striped table-bordered add-manage-table table demo" data-filter="#filter" data-filter-text-only="true">
									<thead>
										<tr>
											<th style="width:2%" data-type="numeric" data-sort-initial="true"></th>
											<th style="width:88%" data-sort-ignore="true">{{ t('Conversations') }}</th>
											<th style="width:10%">{{ t('Option') }}</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if (isset($conversations) && $conversations->count() > 0):
											foreach ($conversations as $key => $conversation):
/*echo '<pre>';
print_r($conversation);*/

?>

<tr>
	<td class="add-img-selector">
		<div class="checkbox">
			<label><input type="checkbox" name="entries[]" value="{{ $conversation->id }}"></label>
		</div>
	</td>
	<td>

		<span> 

			@if(!empty($conversation->profile_image) || !empty($conversation->profile_image_employer))

			<a href="#" target="_blank">
				@if(!empty($conversation->profile_image_employer) && $USERTYPE == 1)
					<img class="userImg" src="/public/images/profile_images/{{$conversation->profile_image}}" alt="user">
				@endif

				@if(!empty($conversation->profile_image) && $USERTYPE == 2)

				<img class="userImg" src="/public/images/profile_images/{{$conversation->profile_image_employer}}" alt="user">
				@endif
			</a>

			@else

			<a target="_blank" href="/influencer-profile/{{$conversation->user_id}}"><img class="userImg" src="https://www.gravatar.com/avatar/2fb1121670440f4cd693e7478111d453.jpg?s=80&amp;d=https%3A%2F%2Fselfieym.com%2Fimages%2Fuser.jpg&amp;r=g" alt="user"></a>

			@endif
		</span>
		<div style="word-break:break-all;">
			<strong>@if($user_role == 2) Applied at @endif @if($user_role == 1) {{ t('Received at') }} @endif:</strong>
			{{ $conversation->created_at->formatLocalized(config('settings.app.default_datetime_format')) }}
			@if (\App\Models\Message::conversationHasNewMessages($conversation))
			<i class="icon-flag text-primary"></i>
			@endif
			<br>
			<strong>{{ $conversation->subject }}</strong><br>
			@if($conversation->bid_amount!='')
			<strong>Bid Amount:</strong>&nbsp;@if($conversation->bid_amount ){!! \App\Helpers\Number::money($conversation->bid_amount) !!}
			@endif
			@endif
			<br>
			<strong> 
				<?php if($conversation->rate_packages_flag == 1){?>

					@if($user_role == 2) Employer
                 &nbsp;{{ \Illuminate\Support\Str::limit($conversation->from_name, 50) }}
				@endif 

				@if($user_role == 1) Influencers Name
                &nbsp;{{ \Illuminate\Support\Str::limit($conversation->to_name, 50) }}

				@endif :


				<?php } else{?>
				@if($user_role == 2) Job by 
                 &nbsp;{{ \Illuminate\Support\Str::limit($conversation->to_name, 50) }}
				@endif 

				@if($user_role == 1) Applied by 
                &nbsp;{{ \Illuminate\Support\Str::limit($conversation->from_name, 50) }}

				@endif :
			<?php }?>

			</strong>
			{!! (!empty($conversation->filename) and $disk->exists($conversation->filename)) ? ' <i class="icon-attach-2"></i> ' : '' !!}&nbsp;
			|&nbsp;
			<a href="{{ lurl('account/conversations/' . $conversation->id . '/messages') }}">
				{{ t('Click here to read the messages') }}
			</a>

		</div>
	</td>
	<td class="action-td">
		<div>
			<!-- NEW FUNCTIONALITY-->
			<?php if ($user_role == 1): ?>
				<?php
// echo 'conversation_id - '.$conversation->conversation_id;
// echo "<br>";
// echo 'message_id - '.$conversation->id;
				?>
				<?php if (isset($conversation->conversation_id_award) && ($conversation->id == $conversation->conversation_id_award) && ($conversation->post_id == $conversation->post_id_award) && $conversation->project_status == 'pending'): ?>

				<p>
					<a class="btn btn-info btn-sm award_projec_progress" href="#" data-post-id="<?=encrypt($conversation->post_id);?>" data-bid-amount="<?=encrypt($conversation->bid_amount);?>" data-influencer-id = "<?=encrypt($conversation->from_user_id);?>" data-employer-name = "<?=encrypt($conversation->to_name);?>" data-conversation-id = "<?=encrypt($conversation->id);?>">
						<i class="icon-wallet"></i>In Progress</a>
					</p>

					<?php elseif (isset($conversation->conversation_id_award) && ($conversation->id == $conversation->conversation_id_award) && ($conversation->post_id == $conversation->post_id_award) && $conversation->project_status == 'waiting'): ?>

					<p>
						<?php if($conversation->rate_packages_flag == 1){?>
							<a class="btn btn-info btn-sm award_project_release_package" data-bid-amount= "<?=encrypt($conversation->bid_amount);?>" data-employer-id = "<?=encrypt($conversation->employer_id);?>" data-influencer-id = "<?=encrypt($conversation->influencer_id);?>" data-post-id = "<?=encrypt($conversation->post_id);?>" data-post-id = "<?=encrypt($conversation->post_id);?>" data-conversation-id = "<?=encrypt($conversation->id);?>" data-rate_packages_flag = "<?=encrypt($conversation->rate_packages_flag);?>" data-rate_packages_id = "<?=encrypt($conversation->rate_packages_id);?>"
								data-rate_packages_type = "<?=encrypt($conversation->rate_packages_type);?>" href="">Release Payment
							</a>
						<?php } else{?>
							<a class="btn btn-info btn-sm" onclick="milestonemodalshow('<?=encrypt($conversation->employer_id);?>','<?=encrypt($conversation->influencer_id);?>','<?=encrypt($conversation->post_id);?>','<?=encrypt($conversation->id);?>')">
								<i class="icon-wallet"></i>Release Payment</a>
							<?php }?>
						</p>

						<?php elseif (isset($conversation->conversation_id_award) && ($conversation->id == $conversation->conversation_id_award) && ($conversation->post_id == $conversation->post_id_award) && $conversation->project_status == 'completed'): ?>
						<p>

							<!------------RATING REVIEW INFLUENCER MODAL------->
							<div class="modal fade" id="myModal<?= $conversation->conversation_id_award; ?>" role="dialog" style="margin-top: 4%;">
								<div class="modal-dialog">

									<!-- Modal content-->
									<div class="modal-content">

										<div class="modal-header">
											<h4 style="text-align:center;">Rating / Review</h4>
											<a href="javascript:void(0)" data-modal-id="myModal<?= $conversation->conversation_id; ?>" class="close">&times;</a>
										</div>
										<div class="modal-body">

											<!-- STAR RATING -->
											<div class="form-group">
												<label for="review">Ratingss:</label>
												<div class="star-rating">
													<input type="radio" id="5-stars" name="rating" value="5" />
													<label for="5-stars" class="star">&#9733;</label>
													<input type="radio" id="4-stars" name="rating" value="4" />
													<label for="4-stars" class="star">&#9733;</label>
													<input type="radio" id="3-stars" name="rating" value="3" />
													<label for="3-stars" class="star">&#9733;</label>
													<input type="radio" id="2-stars" name="rating" value="2" />
													<label for="2-stars" class="star">&#9733;</label>
													<input type="radio" id="1-star" name="rating" value="1" />
													<label for="1-star" class="star">&#9733;</label>
												</div>
											</div>
											<!-- STAR RATING -->
<!-- <input type="hidden" name="employer_id" value="<?= encrypt($conversation->employer_id);?>">
<input type="hidden" name="influencer_id" value="<?= encrypt($conversation->influencer_id);?>">
<input type="hidden" name="post_id" value="<?= encrypt($conversation->post_id);?>">
<input type="hidden" name="jobprojectaward_id" value="<?= encrypt($conversation->jobprojectaward_id);?>"> -->
<div class="form-group">
	<label for="review">Review:</label>
	<textarea name="review" placeholder= "Write your review here..." class="form-control"></textarea>
</div>
<button type="submit" data-jobprojectaward_id = "<?= encrypt($conversation->jobprojectaward_id);?>" data-post-id = "<?= encrypt($conversation->post_id);?>" data-influencer-id = "<?= encrypt($conversation->influencer_id);?>" data-employer-id = "<?= encrypt($conversation->employer_id);?>" data-conversation-id = "<?= encrypt($conversation->conversation_id_award); ?>" class="btn btn-default write_review">Submit</button>
</div>
<div class="modal-footer">

</div>
</div>

</div>
</div>
<!------------RATING REVIEW INFLUENCER MODAL------->
<!-- <?= $conversation->from_user_id_review; ?> -->
<a href="#" class="btn btn-success btn-sm project_completed" style="margin-bottom: 17%;" data-employer-id = "<?= $conversation->employer_id; ?>" data-influencer-id = "<?= $conversation->influencer_id; ?>" data-post-id = "<?= $conversation->post_id_award; ?>" data-record-id = "<?= $conversation->conversation_id_award; ?>"><i class="icon-eye"></i>Write Review</a><br>
<?php elseif ($conversation->post_id != $conversation->post_id): ?>

<!-- <p> <a class="btn btn-info btn-sm award_project" href="#" data-post-id="<?=encrypt($conversation->post_id);?>" data-bid-amount="<?=encrypt($conversation->bid_amount);?>" data-influencer-id = "<?=encrypt($conversation->from_user_id);?>" data-employer-name = "<?=encrypt($conversation->to_name);?>" data-conversation-id = <?=encrypt($conversation->id);?>>
<i class="icon-wallet"></i>Award Project</a>
</p> -->
<?php else: ?>
	<?php if($conversation->rate_packages_flag == 0):?>
		<p> <a class="btn btn-info btn-sm award_project" href="#" data-post-id="<?=encrypt($conversation->post_id);?>" data-bid-amount="<?= encrypt($conversation->bid_amount);?>" data-influencer-id = "<?=encrypt($conversation->from_user_id);?>" data-employer-name = "<?=encrypt($conversation->to_name);?>" data-conversation-id = <?=encrypt($conversation->id);?>>
			<i class="icon-wallet"></i>Award Project</a>
		</p>
	<?php endif;?>
<?php endif;?>
<?php endif;?>
<!-- NEW FUNCTIONALITY-->


<?php if($conversation->rate_packages_flag == 1 && $conversation->rate_request_rejected_flag == 0):?>
	<?php if ($user_role == 2): ?>

		<p>
			<a class="btn btn-default btn-sm" href="/reject-rate-request-package-influencer/{{encrypt($conversation->id)}}/{{encrypt($conversation->jobprojectaward_id)}}">
				<i class="icon-eye"></i>Reject offer 
			</a>
		</p>

		<p>
			<a class="btn btn-default btn-sm" href="/accept-rate-request-package-influencer/{{encrypt($conversation->id)}}">
				<i class="icon-eye"></i>Accept offer
			</a>
		</p>

	<?php endif; ?>
<?php endif; ?>



<p>
	<a class="btn btn-default btn-sm" href="{{ lurl('account/conversations/' . $conversation->id . '/messages') }}">
		<i class="icon-eye"></i> {{ t('View') }}
	</a>
</p>

<p>
	<a class="btn btn-danger btn-sm delete-action" href="{{ lurl('account/conversations/' . $conversation->id . '/delete') }}">
		<i class="fa fa-trash"></i> {{ t('Delete') }}
	</a>
</p>
</div>
</td>
</tr>
<?php endforeach;?>
<?php endif;?>
</tbody>
</table>
</form>
</div>

<nav class="" aria-label="">
	{{ (isset($conversations)) ? $conversations->links() : '' }}
</nav>

<div style="clear:both"></div>

</div>
</div>
<!--/.page-content-->

</div>
<!--/.row-->
</div>
<!--/.container-->
</div>
<!-- /.main-container -->

<!---milestone --->
<div class="modal fade" id="mileStonemodal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title">
					Your Milestones
				</h4>

				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">{{ t('Close') }}</span>
				</button>
			</div>

			<form role="form" method="POST"  id="bidFrom" action="{{ lurl('posts/' . 22 . '/contact') }}" enctype="multipart/form-data">
				{!! csrf_field() !!}
				<div class="modal-body">

					@if (isset($errors) and $errors->any() and old('messageForm')=='1')
					<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<ul class="list list-check">
							@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
					@endif

					<div id="milestoneData">

					</div>


				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ t('Cancel') }}</button>
					<button type="button" onclick="releasePayment()"class="btn btn-success pull-right">Release Payment</button>


				</div>
			</form>
		</div>
	</div>
</div>
<!---milestone --->
<div class="modal fade" id="lowbalancewallet" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title">
					Insufficient Balance
				</h4>

			</div>
			<div class="modal-body">

				<p>
					Please recharge your wallet.You have ₹0 
				</p><br><br>
			</div>
			<div class="modal-footer">
				<a href="{{lurl('account/wallet')}}"><button type="button" class="btn btn-success">
					Add Money
				</button></a>
			</div>

		</div>
	</div>
</div>

<!----end milestone--->

@endsection

@section('after_scripts')
<script src="{{ url('assets/js/footable.js?v=2-0-1') }}" type="text/javascript"></script>
<script src="{{ url('assets/js/footable.filter.js?v=2-0-1') }}" type="text/javascript"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" type="text/javascript"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">


<script type="text/javascript">
	$(function () {
		$('#addManageTable').footable().bind('footable_filtering', function (e) {
			var selected = $('.filter-status').find(':selected').text();
			if (selected && selected.length > 0) {
				e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
				e.clear = !e.filter;
			}
		});

		$('.clear-filter').click(function (e) {
			e.preventDefault();
			$('.filter-status').val('');
			$('table.demo').trigger('footable_clear_filter');
		});

		$('#checkAll').click(function () {
			checkAll(this);
		});

		$('a.delete-action, button.delete-action').click(function(e)
		{
			e.preventDefault(); /* prevents the submit or reload */
			var confirmation = confirm("{{ t('confirm_this_action') }}");

			if (confirmation) {
				if( $(this).is('a') ){
					var url = $(this).attr('href');
					if (url !== 'undefined') {
						redirect(url);
					}
				} else {
					$('form[name=listForm]').submit();
				}
			}

			return false;
		});
	});
</script>
<!-- include custom script for ads table [select all checkbox]  -->
<script>
	function checkAll(bx) {
		var chkinput = document.getElementsByTagName('input');
		for (var i = 0; i < chkinput.length; i++) {
			if (chkinput[i].type == 'checkbox') {
				chkinput[i].checked = bx.checked;
			}
		}
	}

	$(document).ready(function(){
		$(document).on('click','.close',function(event)
		{
			event.preventDefault();
			var modal_id = $(this).attr('data-modal-id');
			$('#'+modal_id).hide();
		});

// write review
$(document).on('click','.btn.btn-success.btn-sm.project_completed',function(event)
{
	event.preventDefault();

	var conversation_id = $(this).attr('data-record-id');
	var employer_id = $(this).attr('data-employer-id');
	var influencer_id = $(this).attr('data-influencer-id');
	var post_id = $(this).attr('data-post-id');

	$.ajax({
		url: "/account/check_employer_review",
		cache: false,
		method:'post',
		data:{post_id:post_id,conversation_id:conversation_id,influencer_id:influencer_id,employer_id:employer_id},
		context:this,
		success: function(data)
		{
			if(data == 1){
				toastr.error('Review already given.');
			}else{

				var record_id = $(this).attr('data-record-id');

				var modal = document.getElementById("myModal"+record_id+"");
				modal.style.display = "block";
			}
		}

	});



}); 
// write review 

$(document).on('click','button.btn.btn-default.write_review',function(event){
	event.preventDefault();

	var jobprojectaward_id = $(this).attr('data-jobprojectaward_id');
	var employer_id = $(this).attr('data-influencer-id');
	var influencer_id = $(this).attr('data-employer-id');
	var post_id = $(this).attr('data-post-id');
	var conversation_id = $(this).attr('data-conversation-id');

	var rating = $( "input[name='rating']:checked" ).val();
	var review = $.trim($("textarea[name='review']").val());

	$.ajax({
		url: "/account/rating_review_influencer",
		cache: false,
		method:'post',
		data:{post_id:post_id,jobprojectaward_id:jobprojectaward_id,influencer_id:influencer_id,employer_id:employer_id,review:review,rating:rating,conversation_id:conversation_id},
		success: function(data)
		{
			if(data == 1){
				toastr.success('Review successfully submitted.');
				location.reload();
			}else{
				toastr.error('Something went wrong. Please try again later.');
			}
		}

	});

});

$(document).on('click','a.btn.btn-info.btn-sm.award_project',function(event)
{

	event.preventDefault();

	var post_id = $(this).attr('data-post-id');
	var bid_amount = $(this).attr('data-bid-amount');
	var influencer_id = $(this).attr('data-influencer-id');
	var employer_name = $(this).attr('data-employer-name');
	var conversation_id = $(this).attr('data-conversation-id');

	bootbox.confirm({
		message: "Are you sure .You want to award this project to the influencer?",
		buttons: {
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function (result) {
			if(result == true)
			{

				$.ajax({
					url: "/account/awardproject",
					cache: false,
					method:'post',
					data:{post_id:post_id,bid_amount:bid_amount,influencer_id:influencer_id,employer_name:employer_name,conversation_id:conversation_id},
					success: function(data)
					{

						var jsonResponse = JSON.parse(data);

						if(jsonResponse == 'low_wallet_balance')
						{
							$('#lowbalancewallet').modal();
//toastr.error('Low wallet balance.Please recharge your wallet.');
}

if(jsonResponse == 'error_message')
{
	toastr.error('Something went wrong. Please try again later.');
}

if(jsonResponse == 'success_message')
{

	toastr.success('Project Awarded to the influencer.');
	location.reload();

}

}
});

			}
		}
	});

});

// RELEASE PAYMENT CODE

$(document).on('click','a.btn.btn-info.btn-sm.award_project_release',function(event)
{

	event.preventDefault();
	var enc_bid_amount = $(this).attr('data-bid-amount');
	var enc_employer_id = $(this).attr('data-employer-id');
	var enc_influencer_id = $(this).attr('data-influencer-id');
	var enc_conversation_id = $(this).attr('data-conversation-id');
	var enc_post_id = $(this).attr('data-post-id');

	bootbox.confirm({
		message: "Are you sure .You want to release the payment ?",
		buttons: {
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function (result) {
			if(result == true)
			{

				$('#mileStonemodal').modal();
			}
		}
	});

});

});

function milestonemodalshow(employer_id,influencer_id,post_id,conversation_id){

	$.ajax({
		url: "/account/conversation/milestoneget",
		cache: false,
		method:'post',
		data:{employer_id:employer_id,influencer_id:influencer_id,post_id:post_id,conversation_id:conversation_id},
		success: function(data)
		{
			$('#milestoneData').html(data);
			$('#mileStonemodal').modal();
		}
	});	
}
function releasePayment(){

//var enc_bid_amount = $(this).attr('data-bid-amount');
var milestone_id = $("input[name='milestone_id[]']:checked").map(function () {
	return this.value;
}).get();
var rate_packages_flag='0';
var enc_employer_id = $('#employer_id').val();
var enc_influencer_id = $('#influencer_id').val();
var enc_conversation_id =$('#conversation_id').val();
var enc_post_id = $('#post_id').val();

$.ajax({
	url: "/account/releasepayment",
	cache: false,
	method:'post',
	data:{milestone_id:milestone_id,employer_id:enc_employer_id,influencer_id:enc_influencer_id,conversation_id:enc_conversation_id,post_id:enc_post_id,rate_packages_flag:rate_packages_flag},
	success: function(data)
	{

		var jsonResponse = JSON.parse(data);


		if(jsonResponse == 'success')
		{
			toastr.success('Payment released successfully.');
			location.reload();
		}

		if(jsonResponse == 'error')
		{
			toastr.error('Something went wrong. Please try again later.');
		}

	}
});


}

</script>
<script type="text/javascript">
// RELEASE PAYMENT CODE

$(document).on('click','a.btn.btn-info.btn-sm.award_project_release_package',function(event)
{

	event.preventDefault();
	var enc_rate_packages_id = $(this).attr('data-rate_packages_id');
	var enc_rate_packages_type = $(this).attr('data-rate_packages_type');

	var enc_employer_id = $(this).attr('data-employer-id');
	var enc_influencer_id = $(this).attr('data-influencer-id');
	var enc_conversation_id = $(this).attr('data-conversation-id');
	var enc_post_id = $(this).attr('data-post-id');
	var rate_packages_flag ='1';

	bootbox.confirm({
		message: "Are you sure .You want to release the payment ?",
		buttons: {
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function (result) {
			if(result == true)
			{

				$.ajax({
					url: "/account/releasepaymentpackage",
					cache: false,
					method:'post',
					data:{employer_id:enc_employer_id,influencer_id:enc_influencer_id,conversation_id:enc_conversation_id,post_id:enc_post_id,rate_packages_flag:rate_packages_flag,enc_rate_packages_id:enc_rate_packages_id,enc_rate_packages_type:enc_rate_packages_type},
					success: function(data)
					{

						var jsonResponse = JSON.parse(data);

						if(jsonResponse == 'negative_balance_check')
						{
							toastr.error('Please check your wallet balance.');
							location.reload();
						}

						if(jsonResponse == 'success')
						{
							toastr.success('Payment released successfully.');
							location.reload();
						}

						if(jsonResponse == 'error')
						{
							toastr.error('Something went wrong. Please try again later.');
						}

					}
				});

			}
		}
	});

});


</script>
@endsection