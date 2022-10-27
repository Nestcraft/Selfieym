<?php
if (isset($title)) {
	$title = strip_tags($title);
}
?>
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	{{-- Encrypted CSRF token for Laravel, in order for Ajax requests to work --}}
	<meta name="csrf-token" content="{{ csrf_token() }}" />

	<title>
		{!! isset($title) ? $title . ' :: ' . config('app.name').' Admin' : config('app.name').' Admin' !!}
	</title>

	@yield('before_styles')

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.5 -->
	<link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

	<link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/dist/css/AdminLTE.min.css">
	<!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/dist/css/skins/_all-skins.min.css">

	<link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/plugins/pace/pace.min.css">
	<link rel="stylesheet" href="{{ asset('vendor/admin/pnotify/pnotify.custom.min.css') }}">

	<!-- Admin Global CSS -->
	<link rel="stylesheet" href="{{ asset('vendor/admin/style.css') . vTime() }}">

	@yield('after_styles')

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body class="hold-transition {{ config('larapen.admin.skin') }} sidebar-mini">
	<!-- Site wrapper -->
	<div class="wrapper">

		<header class="main-header">
			<!-- Logo -->
			<a href="{{ url('') }}" class="logo">
				<!-- mini logo for sidebar mini 50x50 pixels -->
				<span class="logo-mini">{!! config('larapen.admin.logo_mini') !!}</span>
				<!-- logo for regular state and mobile devices -->
				<span class="logo-lg">
					<strong>{!! strtoupper(\Illuminate\Support\Str::limit(config('app.name'), 15, '.')) !!}</strong>
				</span>
			</a>
			<!-- Header Navbar: style can be found in header.less -->
			<nav class="navbar navbar-static-top" role="navigation">
				<!-- Sidebar toggle button-->
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">{{ trans('admin::messages.toggle_navigation') }}</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>

				@include('admin::inc.menu')
			</nav>
		</header>

		<!-- =============================================== -->

		@include('admin::inc.sidebar')

		<!-- =============================================== -->

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			@yield('header')

			<!-- Main content -->
			<section class="content">
				
				<table class="table table-bordered table-striped display dt-responsive nowrap dataTable dtr-inline" id="table">
					<thead>
						<tr>
							<th class="text-center">Sr No</th>
							<th class="text-center">User Type</th>
							<th class="text-center">First Name</th>
							<th class="text-center">Email</th>
							<th class="text-center">Requested Amount</th>
							<th class="text-center">Wallet Balance</th>
							<th class="text-center">Payment Method</th>
							<th class="text-center">Date</th>
							<th class="text-center">Status</th>
							<th class="text-center">Remarks</th>
							
						</tr>
					</thead>
					<tbody>
						<?php 
						$count = 1;
						foreach($withdraw_request as $users): ?>
							<tr>
								<td><?= $count; ?></td>
								<td><?php if($users->user_type_id==1){echo "Employer";}else{echo "Influencer";} ?></td>
								<td><?= $users->name; ?></td>
								<td><?= $users->email; ?></td>
								<td>Rs. <?= $users->amount; ?></td>
								<td>Rs. <?= $users->wallet_amount; ?></td>
								<td>
									<b>
										<?php 
if($users->payment_method=='online'){
		echo 'Online';
	}elseif($users->payment_method=='bank_neft'){
		echo 'Bank NEFT';
	}elseif($users->payment_method=='check_draft'){
		echo 'Check/Draft';
	}else{
		echo ucfirst($users->payment_method);
	}
echo '<br>';
$span = "";
								if($users->front_payment_method=='bank'){
									$span .= "Name: ".decrypt($users->first_name)." ".decrypt($users->last_name);
									$span .= "<br/>";
									$span .= "Bank Name: ".decrypt($users->bank_name);
									$span .= "<br/>";
									$span .= "Branch Name: ".decrypt($users->branch_address);
									$span .= "<br/>";
									$span .= "Name: IFSC".decrypt($users->ifsc_swift_code);
									$span .= "<br/>";
									$span .= "Phone Number: ".decrypt($users->phone_number);
								}else{
									$span .= "Paypal Email: ".decrypt($users->paypal_email);
								}
							echo $span;
								?>






									</b>
								</td>
								<td> <?= $users->created_date; ?></td>
								<td> <?php 
								if($users->status=='approve'){
									echo "<span style='color:#46df50'>".ucfirst($users->status)."</span>";
								}else{
									echo "<span style='color:#df4646'>".ucfirst($users->status)."</span>";
								}
								 ?>
									
								</td>
								<td> <?= $users->remarks; ?></td>
							</tr>
							<?php $count++; ?>
						<?php endforeach; ?>
					</tbody>
				</table>


				@yield('content')

			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

		<footer class="main-footer">
			@if (config('larapen.admin.show_powered_by'))
			<div class="pull-right hidden-xs">
				@if (config('settings.footer.powered_by_info'))
				{{ trans('admin::messages.powered_by') }} {!! config('settings.footer.powered_by_info') !!}
				@else
				{{ trans('admin::messages.powered_by') }} <a target="_blank" href="http://www.bedigit.com">Bedigit</a>.
				@endif
			</div>
			@endif
			Version {{ config('app.version') }}
		</footer>
	</div>
	<!-- ./wrapper -->

	<div class="modal fade" id="myModalWithdrawRequest" role="dialog" style="margin-top: 4%;">
		<div class="modal-dialog">

			<div class="modal-content">

				<div class="modal-header">
					<h4 style="text-align:center;">User Transaction</h4>
					<h4 style="text-align:center;" class="requested_amount"></h4>
					<a href="javascript:void(0)" data-modal-id="myModalWithdrawRequest" class="close"  data-dismiss="modal">&times;</a>
				</div>
				<div class="modal-body">
					<form action="/admin/update_user_wallet_transaction_request" method="post">
						<input type="hidden" name="payment_type" value="bank">
						<div class="form-group">
							<select name="payment_method" id="payment_method" class="form-control">
								<option value="">Select Payment Mode</option>
								<option value="online">Online</option>
								<option value="bank_neft">Bank NEFT</option>
								<option value="check_draft">Check/Draft</option>
								<option value="other">Other</option>
							</select>
						</div>
						<div class="form-group">
							<label for="review">Remarks:</label>
							<input type="hidden" name="enc_user_id" value="">
							<input type="hidden" name="request_id" value="">
							<input type="hidden" name="amount" value="">
							<input type="hidden" name="trasaction_status" value="">
							<textarea class="form-control" name="remarks" value="" required>
							</textarea>
						</div>
						
						<!-- <div class="form-group">
							<select name="payment_mode" value="" class="form-control">
								<option value="">Select Status</option>
								<option value="progress">Progress</option>
								<option value="complete">Complete</option>
							</select>
						</div> -->

						<button type="submit" class="btn btn-default transaction_request">Submit</button>
					</div>
					<div class="modal-footer">

					</div>
				</form>
			</div>

		</div>
	</div>
	<!-------PAYPAL DETAILS FORM------->


	@yield('before_scripts')

	<script>
		var siteUrl = '<?php echo url('/'); ?>';
	</script>

	<!-- jQuery 2.2.0 -->
	<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
	<script>window.jQuery || document.write('<script src="{{ asset('vendor/adminlte') }}/plugins/jQuery/jquery-2.2.0.min.js"><\/script>')</script>
	<!-- Bootstrap 3.3.5 -->
	<script src="{{ asset('vendor/adminlte') }}/bootstrap/js/bootstrap.min.js"></script>
	<script src="{{ asset('vendor/adminlte') }}/plugins/pace/pace.min.js"></script>
	<script src="{{ asset('vendor/adminlte') }}/plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<script src="{{ asset('vendor/adminlte') }}/plugins/fastclick/fastclick.js"></script>
	<script src="{{ asset('vendor/adminlte') }}/dist/js/app.min.js"></script>

	<script src="{{ asset('vendor/admin/script.js') }}"></script>

	<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script
	src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>

	<script	src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
	<script	src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
	<script	src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script	src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script	src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script	src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
	<script	src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
	<link rel="stylesheet"
	href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet"
	href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet"
	href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">

	<!-- page script -->
	<script type="text/javascript">
		/* To make Pace works on Ajax calls */
		$(document).ajaxStart(function() { Pace.restart(); });
		/* Ajax calls should always have the CSRF token attached to them, otherwise they won't work */
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		/* Set active state on menu element */
		var current_url = "{{ url(Route::current()->uri()) }}";
		$("ul.sidebar-menu li a").each(function() {
			if ($(this).attr('href').startsWith(current_url) || current_url.startsWith($(this).attr('href')))
			{
				$(this).parents('li').addClass('active');
			}
		});
	</script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" type="text/javascript"></script>

	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
	<script>
		$(document).ready(function()
		{

			$(document).on('click','.btn.btn-default.transaction_request',function(event)
			{
				event.preventDefault();
				var request_id = $( "input[name='request_id']" ).val();
				var trasaction_status = $( "input[name='trasaction_status']" ).val();
				var enc_user_id = $( "input[name='enc_user_id']" ).val();
				var remarks = $("input[name='remarks']" ).val();
				var amount = $( "input[name='amount']" ).val();
				var payment_method = $('#payment_method').find(":selected").val();

				if(remarks == '')
				{
					toastr.error('Please give remarks.');
					return false;

				}


				$.ajax({
					url: "/admin/update_user_wallet_transaction_request",
					cache: false,
					method:'post',
					data:{request_id:request_id,trasaction_status:trasaction_status,remarks:remarks,enc_user_id:enc_user_id,amount:amount,payment_method:payment_method},
					success: function(data)
					{
						var jsonResponse = JSON.parse(data);

						if(jsonResponse == '1'){
							toastr.success('Details updated successfully');
							location.reload();
						}else{
							toastr.error('Something went wrong. Please try again later.');
						}
					}

				});

			});

			$(document).on('click', 'a.btn.btn-xs.btn-success.approve', function(event)
			{
				event.preventDefault();

				var user_id = $(this).attr('data-enc-id');
				var amount = $(this).attr('data-amount');
				var request_id = $(this).attr('data-request-id');

				$('#myModalWithdrawRequest').modal('show');
				$( "input[name='amount']" ).val(amount);
				$( "input[name='enc_user_id']" ).val(user_id);
				$( "input[name='request_id']" ).val(request_id);
				$( "input[name='trasaction_status']" ).val('approve');
				$(".requested_amount").html('Rs. '+amount);

				
			});


			$(document).on('click', 'a.btn.btn-xs.btn-danger.reject', function(event)
			{
				event.preventDefault();

				var user_id = $(this).attr('data-enc-id');
				var amount = $(this).attr('data-amount');
				var request_id = $(this).attr('data-request-id');
				$('#myModalWithdrawRequest').modal('show');
				$( "input[name='amount']" ).val(amount);
				$( "input[name='enc_user_id']" ).val(user_id);
				$( "input[name='trasaction_status']" ).val('reject');
				$( "input[name='request_id']" ).val(request_id);
			});



	$('#table').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    } );
			/* Send an ajax update request */
			$(document).on('click', '.ajax-request', function(e)
			{
				e.preventDefault(); /* prevents the submit or reload */
				var confirmation = confirm("<?php echo trans('admin::messages.confirm_this_action'); ?>");

				if (confirmation) {
					saveAjaxRequest(siteUrl, this);
				}
			});
		});

		function saveAjaxRequest(siteUrl, el)
		{
			if (isDemo()) {
				return false;
			}

			var $self = $(this); /* magic here! */

			/* Get database info */
			var _token = $('input[name=_token]').val();
			var dataTable = $(el).data('table');
			var dataField = $(el).data('field');
			var dataId = $(el).data('id');
			var dataLineId = $(el).data('line-id');
			var dataValue = $(el).data('value');

			/* Remove dot (.) from var (referring to the PHP var) */
			dataLineId = dataLineId.split('.').join("");


			$.ajax({
				method: 'POST',
				url: siteUrl + '/<?php echo admin_uri(); ?>/ajax/' + dataTable + '/' + dataField + '',
				context: this,
				data: {
					'primaryKey': dataId,
					'_token': _token
				}
			}).done(function(data) {
				/* Check 'status' */
				if (data.status != 1) {
					return false;
				}

				/* Decoration */
				if (data.table == 'countries' && dataField == 'active')
				{
					if (!data.resImport) {
						new PNotify({
							text: "{{ trans('admin::messages.Error - You can\'t install this country.') }}",
							type: "error"
						});

						return false;
					}

					if (data.isDefaultCountry == 1) {
						new PNotify({
							text: "{{ trans('admin::messages.You can not disable the default country') }}",
							type: "warning"
						});

						return false;
					}

					/* Country case */
					if (data.fieldValue == 1) {
						$('#' + dataLineId).removeClass('fa fa-toggle-off').addClass('fa fa-toggle-on');
						$('#install' + dataId).removeClass('btn-default').addClass('btn-success').empty().html('<i class="fa fa-download"></i> <?php echo trans('admin::messages.Installed'); ?>');
					} else {
						$('#' + dataLineId).removeClass('fa fa-toggle-on').addClass('fa fa-toggle-off');
						$('#install' + dataId).removeClass('btn-success').addClass('btn-default').empty().html('<i class="fa fa-download"></i> <?php echo trans('admin::messages.Install'); ?>');
					}
				}
				else
				{
					/* All others cases */
					if (data.fieldValue == 1) {
						$('#' + dataLineId).removeClass('fa fa-toggle-off').addClass('fa fa-toggle-on');
					} else {
						$('#' + dataLineId).removeClass('fa fa-toggle-on').addClass('fa fa-toggle-off');
					}
				}

				return false;
			}).fail(function(xhr, textStatus, errorThrown) {
                /*
                 console.log('FAILURE: ' + textStatus);
                 console.log(xhr);
                 */

                 /* Show an alert with the result */
                 /* console.log(xhr.responseText); */
                 if (typeof xhr.responseText !== 'undefined') {
                 	if (xhr.responseText.indexOf("{{ trans('admin::messages.unauthorized') }}") >= 0) {
                 		new PNotify({
                 			text: xhr.responseText,
                 			type: "error"
                 		});

                 		return false;
                 	}
                 }

                 /* Show an alert with the standard message */
                 new PNotify({
                 	text: xhr.responseText,
                 	type: "error"
                 });

                 return false;
             });

			return false;
		}

		function isDemo()
		{
			<?php
			$varJs = isDemo() ? 'var demoMode = true;' : 'var demoMode = false;';
			echo $varJs . "\n";
			?>
			var msg = '{{ addcslashes(t('demo_mode_message'), "'") }}';

			if (demoMode) {
				new PNotify({title: 'Information', text: msg, type: "info"});
				return true;
			}

			return false;
		}
	</script>

	@include('admin::inc.alerts')
	@include('admin::inc.maintenance')

	<script>
		$(document).ready(function () {
			@if (isset($errors) and $errors->any())
			@if ($errors->any() and old('maintenanceForm')=='1')
			$('#maintenanceMode').modal();
			@endif
			@endif
		});
	</script>

	@yield('after_scripts')

</body>
</html>
