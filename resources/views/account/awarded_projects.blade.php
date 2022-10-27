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
		.modal-backdrop.fade.show{
			display:none;
		}
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

							<h3 class="title-2">Awarded Projects</h3>
							@if(session()->has('errormessage'))
							<div class="alert alert-danger" role="alert">
								{{ session()->get('errormessage') }}
							</div>
							@endif

							@if(session()->has('successmessage'))
							<div class="alert alert-success" role="alert">
								{{ session()->get('successmessage') }}
							</div>
							@endif
							<!--- TRANSACTIONS TABLE-->
							<table class="table table-bordered table-striped display dt-responsive nowrap dataTable dtr-inline" id="table_awarded_projects" style="margin-top: 3%;">
								<thead>
									<tr>
										<th class="text-center">Sr.No</th>
										<th class="text-center">Project Name</th>
										<th class="text-center">Employer Name</th>
										<th class="text-center">Bid Amount</th>
										<th class="text-center">Project Status</th>
										<!-- <th class="text-center">Rating</th> -->
										<!-- <th class="text-center">Review</th> -->
										<th class="text-center">Date</th>
										<th>Complete Flag</th>
									</tr>
								</thead>
								<tbody>
									<?php if (count($awarded_projects) > 0): ?>
										<?php
										$serial_number = 1;
										foreach ($awarded_projects as $project_awarded): ?>

											<!------------RATING REVIEW INFLUENCER MODAL------->
											<div class="modal fade" id="myModal<?=$project_awarded->jobprojectaward_id;?>" role="dialog" style="margin-top: 4%;">
												<div class="modal-dialog">

													<!-- Modal content-->
													<div class="modal-content">

														<div class="modal-header">
															<h4 style="text-align:center;">Rating / Review</h4>
															<a href="javascript:void(0)" data-modal-id="myModal<?=$project_awarded->jobprojectaward_id;?>" class="close">&times;</a>
														</div>
														<div class="modal-body">

															<form action="/account/rating_review" method="POST">
																@csrf

																<!-- STAR RATING -->
																<div class="form-group">
																	<label for="review">Rating:</label>
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
																<input type="hidden" name="employer_id" value="<?= encrypt($project_awarded->employer_id);?>">
																<input type="hidden" name="influencer_id" value="<?= encrypt($project_awarded->influencer_id);?>">
																<input type="hidden" name="post_id" value="<?= encrypt($project_awarded->post_id);?>">
																<input type="hidden" name="jobprojectaward_id" value="<?= encrypt($project_awarded->jobprojectaward_id);?>">
																<div class="form-group">
																	<label for="review">Review:</label>
																	<textarea name="review"  placeholder= "Write your review here..." class="form-control"></textarea>
																</div>
																<button type="submit" class="btn btn-default">Submit</button>
															</form>
														</div>
														<div class="modal-footer">

														</div>
													</div>

												</div>
											</div>
											<!------------RATING REVIEW INFLUENCER MODAL------->
											<tr>
												<td><?=$serial_number;?></td>
												<td><?=$project_awarded->title;?></td>
												<td><?=$project_awarded->name;?></td>
												<td>{!! \App\Helpers\Number::money($project_awarded->bid_amount)!!} </td>
												<td><?=ucfirst($project_awarded->project_status);?></td>
<!-- <td><?php if($project_awarded->review):?><?= $project_awarded->review;?>
<?php else:?>
<?= 'No review given.';?>
<?php endif;?></td> -->
<td><?=$project_awarded->created_date;?></td>
<?php if ($project_awarded->project_status == 'pending'): ?>
	<td><!-- <a href="/account/complete_project" data-post-id = "<?=encrypt($project_awarded->post_id);?>" class="btn btn-info btn-sm complete_project" data-employer-id="<?=encrypt($project_awarded->employer_id);?>" data-influencer-id="<?=encrypt($project_awarded->influencer_id);?>" data-hit-url="/account/conversations/<?=$project_awarded->conversation_id;?>/reply"style="font-size: 90%;">Request Payment</a> -->

		<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#replyTo<?=$project_awarded->conversation_id;?>"><i class="icon-reply"></i> Request Release Payment</a>
	</td>
	<?php elseif ($project_awarded->project_status == 'waiting'): ?>
		<td><a href="#" class="btn btn-info btn-md" style="font-size: 90%;">Waiting approval</a></td>
		<?php elseif ($project_awarded->project_status == 'completed'): ?>
			<?php if(isset($project_awarded->jobrating_review_id) && $project_awarded->jobprojectaward_id_review == $project_awarded->jobprojectaward_id):?>
				<?php if($project_awarded->rating_review_influencer == 1):?>
					<td><a href="javascript:void(0)" class="btn btn-success btn-md" style="font-size: 90%;">Review Given</a>
					</td>
					<?php else:?>
						<td><a href="#" class="btn btn-success btn-md project_completed" style="font-size: 90%;" data-employer-id = "<?=encrypt($project_awarded->employer_id);?>" data-influencer-id = "<?=encrypt($project_awarded->influencer_id);?>" data-post-id = "<?=encrypt($project_awarded->post_id);?>" data-record-id = "<?= $project_awarded->jobprojectaward_id; ?>">Write Review</a>
						</td>
					<?php endif;?>
					
					<?php else:?>

						<td><a href="#" class="btn btn-success btn-md project_completed" style="font-size: 90%;" data-employer-id = "<?=encrypt($project_awarded->employer_id);?>" data-influencer-id = "<?=encrypt($project_awarded->influencer_id);?>" data-post-id = "<?=encrypt($project_awarded->post_id);?>" data-record-id = "<?= $project_awarded->jobprojectaward_id; ?>">Write Review</a>
						</td>

					<?php endif;?>
				<?php endif;?>

			</tr>

			<!-- REPLY MODAL -->
			<div class="modal fade" id="replyTo<?=$project_awarded->conversation_id;?>" tabindex="-1" role="dialog" aria-labelledby="replyTo22Label" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="replyTo<?=$project_awarded->conversation_id;?>Label">
								Message
							</h4>

						</div>
						<form role="form" method="POST" action="/account/conversations/<?=$project_awarded->conversation_id;?>/reply">
							@csrf
							<div class="modal-body enable-long-words">
								<input type="hidden" name= "employer_id" value="<?=encrypt($project_awarded->employer_id);?>">
								<input type="hidden" name= "influencer_id" value="<?=encrypt($project_awarded->influencer_id);?>">
								<input type="hidden" name= "post_id" value="<?=encrypt($project_awarded->post_id);?>">
								<!-- message -->
								<div class="form-group required">
									<label for="message" class="control-label">
										Message <span class="text-count">(500 max)</span> <sup>*</sup>
									</label>
									<textarea name="message"
									class="form-control required"
									placeholder="Your message here..."
									rows="5"
									></textarea>
								</div>
							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary"><i class="icon-reply"></i>Submit Request</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- REPLY MODAL -->
			<?php $serial_number++;
		endforeach;?>
		<?php else: ?>
			<tr>
				<td>No Awarded Projects Found!</td>
			</tr>
		<?php endif;?>

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


@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
	$(document).ready(function(){

		$(document).on('click','.btn.btn-success.btn-md.project_completed',function(event)
		{
			event.preventDefault();
			var record_id = $(this).attr('data-record-id');

			var modal = document.getElementById("myModal"+record_id+"");
			modal.style.display = "block";


		});

		$(document).on('click','.close',function(event)
		{
			event.preventDefault();
			var modal_id = $(this).attr('data-modal-id');
			$('#'+modal_id).hide();
		});

	});
</script>


@section('after_scripts')
@endsection

