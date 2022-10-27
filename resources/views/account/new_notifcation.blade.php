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

<h3 class="title-2">Notifications</h3>

<!--- Notifications TABLE-->
<table class="table table-bordered table-striped display dt-responsive nowrap dataTable dtr-inline" id="table_awarded_projects" style="margin-top: 3%;">
<thead>
<tr>
<th class="text-center">Sr.No</th>
<th class="text-center">Notification</th>
<th class="text-center">Date</th>
</tr>
</thead>
<tbody>
<?php if (count($notification_info) > 0): ?>
<?php
$serial_number = 1;
foreach ($notification_info as $notification_information): ?>
<tr>
<td><?=$serial_number;?></td>
<td><?=$notification_information->notification_text;?></td>
<td><?=$notification_information->created_date;?></td>
</tr>

<?php $serial_number++;
endforeach;?>
<?php else: ?>
<tr>
<td>No Notification Found!</td>
</tr>
<?php endif;?>

</tbody>
</table>
<!--- Notifications TABLE-->

</div>
</div>

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

});
</script>


@section('after_scripts')
@endsection

