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
        .bootstrap-tagsinput {
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            display: inline-block;
            padding: 4px 6px;
            color: #555;
            vertical-align: middle;
            border-radius: 4px;
            max-width: 100%;
            line-height: 22px;
            cursor: text;
        }
        .bootstrap-tagsinput input {
            border: none;
            box-shadow: none;
            outline: none;
            background-color: transparent;
            padding: 0 6px;
            margin: 0;
            width: auto;
            max-width: inherit;
        }
        .bootstrap-tagsinput.form-control input::-moz-placeholder {
            color: #777;
            opacity: 1;
        }
        .bootstrap-tagsinput.form-control input:-ms-input-placeholder {
            color: #777;
        }
        .bootstrap-tagsinput.form-control input::-webkit-input-placeholder {
            color: #777;
        }
        .bootstrap-tagsinput input:focus {
            border: none;
            box-shadow: none;
        }
        .bootstrap-tagsinput .tag {
            margin-right: 2px;
            color: red;
        }
        .bootstrap-tagsinput .tag [data-role="remove"] {
            margin-left: 8px;
            cursor: pointer;
        }
        .bootstrap-tagsinput .tag [data-role="remove"]:after {
            content: "x";
            padding: 0px 2px;
        }

        .slim .slim-btn-group {
            position: absolute;
            right: 0;
            bottom: 53px !important;
            left: 0;
            z-index: 3;
            pointer-events: none;
        }
        button.btn.btn-mwc.slim_custom_upload_btn {
            margin: 0 0 0 65px !important;
        }

        .conterimg {
            text-align: center;
            box-shadow: 0px 0px 5px #b3b1b1;
            border: 1px solid transparent;
            transition: 0.9s;
            padding-bottom: 12px;
            margin-bottom: 30px;
        }

        .conterimg:hover{
            border: 1px solid #7324bc;
        }
        .conterimg a {
            padding: 6px;
            display: inline-block;
            margin: 0px;
            width: 120px;
            background: #7324bc;
            margin-bottom: 5px;
            border-radius: 20px;
            margin-top: 2px;
            color: #ffff;
        }

        input.portfolio_submit {
            width: 150px;
            margin-left: 50%;
            transform: translateX(-50%);
            margin-bottom: 10px;
            border-radius: 14px;
            background: #7324bc!important;
            padding: 5px;
            color: #ffff;
            border: none;
        }

        .modal-footer button {
            background: #7324bc!important;
        }

        .modal-header {
            background: #7324bc!important;
            color: #fff!important;

        }

        .conterimg img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
    </style>

    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-md-3 page-sidebar">
                    @include('account.inc.sidebar')
                </div>
                <!--/.page-sidebar-->

                <div class="col-md-9 page-content">

                    @include('flash::message')

                    @if (isset($errors) and $errors->any())
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><strong>{{ t('Oops ! An error has occurred. Please correct the red fields in the form') }}</strong></h5>
                            <ul class="list list-check">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="inner-box default-inner-box">
                        <div class = "row">
                            <div class="col-md-7 col-xs-4 col-xxs-12">
                                <div class="welcome-msg">
                                    <h3 class="page-sub-header2 clearfix no-padding">{{ t('Hello') }} {{ $user->name }} ! </h3>
                                    <span class="page-sub-header-sub small">
										{{ t('You last logged in at') }}: {{ $user->last_login_at->formatLocalized(config('settings.app.default_datetime_format')) }}
									</span>
                                </div>
                            </div>
                            <div class="col-md-5 col-xs-4 col-xxs-12">
                                <div class="header-data text-center-xs">

                                    <div class="hdata">
                                        <div class="mcol-left">
                                            <i class="fas fa-wallet ln-shadow"></i></div>
                                        <div class="mcol-right">
                                            <!-- Number of messages -->
                                            <p>
                                                <a href="https://selfieym.com/account/wallet">
                                                    Rs.{{\App\Helpers\UrlGen::get_user_wallet(auth()->user()->id)}}</a>
                                            </p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="accordion" class="panel-group">

                            <!-- NEW SECTION FOR PORTFOLIO -->
                            <div class="card card-default">
                                <div class="card-header">
                                    <h4 class="card-title">Social Portfolio</h4>
                                </div>
                                <div class="panel-collapse collapse show" id="userPanel">
                                    <form action="{{url('/account/socialprofile/instagram')}}" method="POST"> {{csrf_field()}}
                                   <div class="card-body">

                                           <div class="row">
                                               <div class="col-md-6">
                                                   <div class="form-group">
                                                       <label for="instagram">Instagram</label>
                                                       <input type="text" name="instagram"  id="instagram" class="form-control" required placeholder="Enter your instagram username">
                                                   </div>

                                                   <div class="form-group">
                                                       <label for="twitter">Twitter</label>
                                                       <input type="text" name="twitter"  id="twitter" required class="form-control" placeholder="Enter your Twitter Url">
                                                   </div>
                                               </div>

                                               <div class="col-md-6">
                                                   <div class="form-group">
                                                       <label for="facebook">Facebook</label>
                                                       <input type="text" name="facebook" required  id="facebook" class="form-control" placeholder="Enter your facebook url">
                                                   </div>

                                                   <div class="form-group">
                                                       <label for="youtube">Youtube</label>
                                                       <input type="text" name="youtube" required  id="youtube" class="form-control" placeholder="Enter your Youtube Url">
                                                   </div>
                                               </div>
                                           </div>


                                   </div>

                                    <div class="card-footer">
                                        <input type="submit" value="Save" class="btn btn-danger" style="float: right; margin-bottom: 5px;" >
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <!-- NEW SECTION FOR PORTFOLIO -->


                        </div>
                        <!--/.row-box End-->
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

@section('after_styles')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
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
    {{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>--}}
    {{--<script src="{{asset('/js/jquery.instagramFeed.js')}}"></script>--}}


    <script>// bootstrap-tagsinput.js file - add in local


    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">

@endsection
