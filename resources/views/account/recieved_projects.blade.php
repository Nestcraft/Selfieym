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
.modal-backdrop.fade.show
{

display:none;

}
.star-rating
{

border:solid 1px #ccc;
display:flex;
flex-direction: row-reverse;
font-size:1.5em;
justify-content:space-around;
padding:0 .2em;
text-align:center;
width:5em;

}
.star-rating input
{

display:none;

}
.star-rating label
{

color:#ccc;
cursor:pointer;

}
.star-rating :checked ~ label
{

color:#f90;

}

.star-rating label:hover,
.star-rating label:hover ~ label 
{

color:#fc0;

}

.fade:not(.show)
{

opacity: 1.9 !important;

}

.modal
{

display: none;
position: fixed;
z-index: 1;
padding-top: 100px;
left: 0;
top: 0;
width: 100%;
height: 100%;
overflow: auto;
background-color: rgb(0,0,0);
background-color: rgba(0,0,0,0.4);

}

.modal-content 
{

background-color: #fefefe;
margin: auto;
padding: 20px;
border: 1px solid #888;
width: 80%;

}

.close
{

color: #aaaaaa;
float: right;
font-size: 28px;
font-weight: bold;

}

.close:hover,
.close:focus
{

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
<div class="col-md-9 page-content">
<div class="inner-box">
<h3 class="title-2">Projects Received ( Pre-Paid)</h3>
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

<!--table design by anand-->
<?php if($user_type_id == 2):?>
	<table class="table table-bordered table-responsive bg-white">
<?php else:?>
	<table class="table table-bordered">
<?php endif;?>

<thead>
<tr class="table-info">
<th>#</th>
<th>Package</th>
<th>Project</th>
<th>Amount</th>
<th>Employer</th>
<th>Date</th>
<?php if($user_type_id == 2): ?>
<th>Status</th>
<?php endif;?>
<?php if($user_type_id == 2): ?>
<th>Action</th>
<?php endif;?>
</tr>
</thead>
<tbody>
<?php if (count($recieved_projects) > 0): ?>
<?php
$serial_number = 1;
foreach ($recieved_projects as $project_awarded): ?>
<tr>
<th scope="row">1</th>
<td><?php echo $project_awarded->package_type;?></td>
<?php 
if($project_awarded->package_type=='basic')
{

$titleProject=$project_awarded->basic_package_title;

}elseif ($project_awarded->package_type=='standard')
{

$titleProject=$project_awarded->standard_package_title;

}elseif ($project_awarded->package_type=='premium')
{

$titleProject=$project_awarded->premium_package_title;

}else
{

$titleProject='';

}

?>
<td><?php echo ucfirst($titleProject);?></td>
<td>{!! \App\Helpers\Number::money($project_awarded->bid_amount) !!}</td>
<td><?php echo $project_awarded->name;?></td>
<td><?php echo $project_awarded->created_date;?></td>

<?php 
if($project_awarded->project_status == 'pending')
{
$statusClass='text-primary';
}elseif ($project_awarded->project_status == 'process')
{
$statusClass='text-success';
}elseif ($project_awarded->project_status == 'premium')
{
$statusClass='text-danger';
}else
{
$statusClass='';
}
?>

<?php if($user_type_id == 2): ?>
<td class="<?php echo $statusClass;?>"><?php 
if($project_awarded->project_status=='waiting'){
	echo 'Release Payment';
}elseif($project_awarded->project_status=='accepted'){
echo 'On Progress';
}
else{
echo ucfirst($project_awarded->project_status); }?></td>

<?php endif;?>



<td><button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
<div class="dropdown-menu">
	<!-- <a href="#" class="dropdown-item influcerby complete_project" style="font-size: 90%;" data-employer-id = "<?=encrypt($project_awarded->employer_id);?>" data-influencer-id = "<?=encrypt($project_awarded->influencer_id);?>" data-post-id = "<?=encrypt($project_awarded->post_id);?>" data-record-id = "<?= $project_awarded->jobprojectaward_id; ?>">Project Complete</a> -->
<!-- <a class="dropdown-item" href="#">Project Complete</a> -->

	<?php 
if($project_awarded->project_status =='accepted')
	{?>
	<a class="dropdown-item" href="#" data-toggle="modal" data-target="#replyTo<?=$project_awarded->conversation_id;?>">Release Payment</a>

<?php } 
if($project_awarded->project_status =='completed')
	{?>
<a href="#" class="dropdown-item project_completed" style="font-size: 90%;" data-employer-id = "<?=encrypt($project_awarded->employer_id);?>" data-influencer-id = "<?=encrypt($project_awarded->influencer_id);?>" data-post-id = "<?=encrypt($project_awarded->post_id);?>" data-record-id = "<?= $project_awarded->jobprojectaward_id; ?>">Give Feedback</a>
<?php }?>

<!--<a href="#" class="dropdown-item btn-md project_completed" style="font-size: 90%;" data-employer-id = "<?=encrypt($project_awarded->employer_id);?>" data-influencer-id = "<?=encrypt($project_awarded->influencer_id);?>" data-post-id = "<?=encrypt($project_awarded->post_id);?>" data-record-id = "<?= $project_awarded->jobprojectaward_id; ?>">Send Message</a>-->

<a href="/account/conversations/<?= $project_awarded->conversation_id; ?>/messages" class="dropdown-item btn-md" style="font-size: 90%;">Send Message</a>


<a class="dropdown-item" href="/account/recievedprojects">Cancel</a>

</div></td>

</tr>
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

<!--table end-->

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

$(document).on('click','.dropdown-item.project_completed',function(event)
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

