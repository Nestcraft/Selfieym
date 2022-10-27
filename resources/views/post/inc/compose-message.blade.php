<div class="modal fade" id="applyJob" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title">
					<i class="icon-mail-2"></i>Place a Bid on this Project
				</h4>

				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">{{ t('Close') }}</span>
				</button>
			</div>

			<form role="form" method="POST"  id="bidFrom" action="{{ lurl('posts/' . $post->id . '/contact') }}" enctype="multipart/form-data">
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

					@if (auth()->check())
					<input type="hidden" name="from_name" value="{{ auth()->user()->name }}">
					@if (!empty(auth()->user()->email))
					<input type="hidden" name="from_email" value="{{ auth()->user()->email }}">
					@else
					<!-- from_email -->
					<?php $fromEmailError = (isset($errors) and $errors->has('from_email')) ? ' is-invalid' : '';?>
					<div class="form-group required">
						<label for="from_email" class="control-label">{{ t('E-mail') }}
							@if (!isEnabledField('phone'))
							<sup>*</sup>
							@endif
						</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="icon-mail"></i></span>
							</div>
							<input id="from_email" name="from_email" type="text" placeholder="{{ t('i.e. you@gmail.com') }}"
							class="form-control{{ $fromEmailError }}" value="{{ old('from_email', auth()->user()->email) }}">
						</div>
					</div>
					@endif
					@else
					<!-- from_name -->
					<?php $fromNameError = (isset($errors) and $errors->has('from_name')) ? ' is-invalid' : '';?>
					<div class="form-group required">
						<label for="from_name" class="control-label">{{ t('Name') }} <sup>*</sup></label>
						<input id="from_name"
						name="from_name"
						class="form-control{{ $fromNameError }}"
						placeholder="{{ t('Your name') }}"
						type="text"
						value="{{ old('from_name') }}"
						>
					</div>

					<!-- from_email -->
					<?php $fromEmailError = (isset($errors) and $errors->has('from_email')) ? ' is-invalid' : '';?>
					<div class="form-group required">
						<label for="from_email" class="control-label">{{ t('E-mail') }}
							@if (!isEnabledField('phone'))
							<sup>*</sup>
							@endif
						</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="icon-mail"></i></span>
							</div>
							<input id="from_email" name="from_email" type="text" placeholder="{{ t('i.e. you@gmail.com') }}"
							class="form-control{{ $fromEmailError }}" value="{{ old('from_email') }}">
						</div>
					</div>
					@endif

					<!-- from_phone -->
					<?php $fromPhoneError = (isset($errors) and $errors->has('from_phone')) ? ' is-invalid' : '';?>
					<div class="form-group required">

						<!-- PROJECT TITLE-->
						@if (!empty($post->title))
						<p>{{$post->title}}</p>
						@endif
						<!-- PROJECT TITLE-->

					</div>

					<!-- BID AMOUNT --->
					<?php $bidAmountError = (isset($errors) and $errors->has('bid_amount')) ? ' is-invalid' : '';?>


					<div class="form-group required">

						<label for="bid_amount" class="control-label">
							<span class="text-count">Your Bid Amount</span> <sup>*</sup>
						</label>
						<input id="bid_amount" name="bid_amount" type="text" placeholder="Enter your bid amount" class="form-control{{ $bidAmountError }}" value="{{ old('bid_amount') }}">
					</div>
					<!-- BID AMOUNT --->

					<!-- PROJECT DELIVERY SECTION --->
					<?php $projectDelieveryError = (isset($errors) and $errors->has('project_delievery_days')) ? ' is-invalid' : '';?>
					<div class="form-group required">
						<label for="project_delievery_days" class="control-label">
							<span class="text-count">Project will be delivered in how many days?</span> <sup>*</sup>
						</label>
						<input id="project_delievery_days" name="project_delievery_days" type="text" placeholder="Please enter days here..." class="form-control{{ $projectDelieveryError }}" value="{{ old('project_delievery_days') }}">
					</div>
					<!-- PROJECT DELIVERY SECTION --->

					<!-- BUDGET SECTION JOB-->
					<p id="Minimum_Budget_Check" style="display: none;">@if ($post->salary_min > 0){{$post->salary_min}}@endif</p>
					<p id="Maximum_Budget_Check" style="display: none;">@if ($post->salary_max > 0){{$post->salary_max}}@endif</p>
					<p class="no-margin">
						<strong>{{ t('Salary') }}:</strong>&nbsp;
						@if ($post->salary_min > 0 or $post->salary_max > 0)
						@if ($post->salary_min > 0)
						<span id="maxbudgetmin">{!! \App\Helpers\Number::money($post->salary_min) !!}</span>
						@endif
						@if ($post->salary_max > 0)
						@if ($post->salary_min > 0)
						
						@endif
						<span id="maxbudgetmax">{!! \App\Helpers\Number::money($post->salary_max) !!}</span>
						@endif
						@else
						<span id="maxbudget">{!! \App\Helpers\Number::money('--') !!}</span>
						@endif
						@if ($post->negotiable == 1)
						<br><small class="label badge-success"> {{ t('Negotiable') }}</small>
						@endif
					</p>
					<br>
					<!-- BUDGET SECTION JOB-->

					<!-- (description of influencer) message -->
					<?php $messageError = (isset($errors) and $errors->has('message')) ? ' is-invalid' : '';?>
					<div class="form-group required">
						<label for="message" class="control-label">
							Describe your proposal<span class="text-count">(500 max)</span> <sup>*</sup>
						</label>
						<textarea id="message"
						name="message"
						class="form-control required{{ $messageError }}"
						placeholder="What makes you the best Influencer for this project..."
						rows="5"
						>{{ old('message') }}</textarea>
					</div>

					<!-- ADDING MILESTONES MODULE CODE -->
					<a href="#" id="addMoreMilestone"  class="btn btn-primary" style="float: right;">Add Milestone</a>
					<div class="form-group topspace1">
						<!-- <label for="milestones" class="control-label">Milestones
						</label> -->
						<!-- <div class="row miletsone_number" id="1">
							<div class="col-sm-5">
								<input type="text" class="form-control" name="milestone_title[]" value="" placeholder="Title">
							</div>
							<div class="col-sm-5 input-group">
								@if (config('currency')['in_left'] == 1)
								<div class="input-group-prepend">
									<span class="input-group-text">{!! config('currency')['symbol'] !!}</span>
								</div>
								@endif
								<input type="number" class="form-control milestone_amount" name="milestone_amount[]" value="Amount">
							</div>
							
						</div>
					</div>
					<!-- ADDING MILESTONES MODULE CODE -->

					@include('layouts.inc.tools.recaptcha', ['label' => true])

					<input type="hidden" name="country_code" value="{{ config('country.code') }}">
					<input type="hidden" name="post_id" value="{{ $post->id }}">
					<input type="hidden" name="messageForm" value="1">
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ t('Cancel') }}</button>
					<button type="button" onclick="amountCheck()"class="btn btn-success pull-right">{{ t('Send message') }}</button>

					
				</div>
			</form>
		</div>
	</div>
</div>
@section('after_styles')
@parent
<link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
@if (config('lang.direction') == 'rtl')
<link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput-rtl.min.css') }}" rel="stylesheet">
@endif
<style>
	.krajee-default.file-preview-frame:hover:not(.file-preview-error) {
		box-shadow: 0 0 5px 0 #666666;
	}
</style>
@endsection

@section('after_scripts')
@parent

<script src="{{ url('assets/plugins/bootstrap-fileinput/js/plugins/sortable.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js') }}" type="text/javascript"></script>
@if (file_exists(public_path() . '/assets/plugins/bootstrap-fileinput/js/locales/'.ietfLangTag(config('app.locale')).'.js'))
<script src="{{ url('assets/plugins/bootstrap-fileinput/js/locales/'.ietfLangTag(config('app.locale')).'.js') }}" type="text/javascript"></script>
@endif
<script>
	var i_p_scents_miletsone = $('.row.miletsone_number').size() + 1;
	/* Resume */
	var lastResumeId = {{ old('resume_id', ((isset($lastResume) and $disk->exists($lastResume->filename)) ? $lastResume->id : 0)) }};
	getResume(lastResumeId);

	$(document).ready(function () {
		@if (isset($errors) and $errors->any())
		@if ($errors->any() and old('messageForm')=='1')
		$('#applyJob').modal();
		@endif
		@endif

		/* Resume */
		$('#resumeId input').bind('click, change', function() {
			lastResumeId = $(this).val();
			getResume(lastResumeId);
		});
		var counter = 1;
		$('#addMoreMilestone').on('click', function(event)
		{ 
			counter++;
			event.preventDefault();

			$(this).after('<div class="row miletsone_number spacetop" id="'+counter+'"><span> </span><div class="col-sm-6"><input type="text" class="form-control" name="milestone_title[]" value="" placeholder="Title"></div><div class="col-sm-6 input-group"><div class="input-group-prepend"><span class="input-group-text">₹</span></div><input type="number" class="form-control milestone_amount" name="milestone_amount[]" value="Amount"><div class="input-group-append"><a href="#" onclick="removemilestones('+counter+')" class="btn btn-danger" style="float: right;"><i class="fa fa-trash"></i></a> </div></div></div>');

			// i_p_scents_miletsone++;
			return false;
		});
		/*$("#theCount").text(counter);*/

	});
	function removemilestones(key){
		$( "#"+key+"" ).remove();
	}
	function amountCheck(){
		var budgetmin = $('#maxbudgetmin').text();
		var newbudgetmin = budgetmin.replace(/[^0-9\.]/g,'');
		var budgetmax = $('#maxbudgetmax').text();
		var newbudgetmax = budgetmax.replace(/[^0-9\.]/g,'');
		var bid_amount= parseFloat($("#bid_amount").val());
		var bid_amount1= $("#bid_amount").val();
		var message=$("#message").val();
		var project_delievery_days=$("#project_delievery_days").val();
		
		var total = 0;
		$('.milestone_amount').each(function (index, element) {
			total = total + parseFloat($(element).val());
		});
		var mileStn=$('.milestone_amount').val();
		//alert(mileStn);
		if(mileStn !=null){

			/*if(total>newbudgetmax || total<newbudgetmin){
				toastr.error('Estimated Budget amount is'+newbudgetmin+'to '+newbudgetmax+'.');	
				return false;
			}*/


			if(bid_amount!=total){
				toastr.error('Your milestones amount not matched with bid amount.');	
				return false;
			}


		}
		if(bid_amount1==''){
			toastr.error('Bid amount is required.');	
			return false;

		}
		if(project_delievery_days==''){
			toastr.error('Delivery days are required.');	
			return false;

		}
		if(message==''){
			toastr.error('Content is required.');	
			return false;

		}

		if(bid_amount>newbudgetmax || bid_amount<newbudgetmin){
			toastr.error('Estimated Budget amount is'+newbudgetmin+'to '+newbudgetmax+'.');	
			return false;
		}

		$('#bidFrom').submit();

	}
</script>
@endsection