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
	.profile {
		background-color: #d4f0f3;
		padding: 15px;
		margin-top: 100px;
	}
	.picture {
		float: left;
	}
	.introduction h1 {
		text-transform: capitalize;
		color: #1b4e49;
		font-weight: 500;
		padding: 0px;
	}
	.introduction h6 {
		color: #ae2d31;
		text-transform: capitalize;
	}
	ul.onlyinline li a {
		color: #000!important;
	}
	.imgset {
		width: 200px;
		margin-left: 25px;
		margin-top: 15px;
	}

	.threeportion img {
		width: 100%;
		height: 150px;
		object-fit: cover;
	}
	.introduction {
		float: right;
		width: 65%;
	}
	.ulmain{
		margin-left: -15px;
		margin-right: -15px;
	}
	.ulmain li{
		margin-left: 15px;
		margin-right: 15px;
		width: calc(33.3% - 34px);
		vertical-align: top;
		display: inline-block;
	}
	.onlyinline li {
		display: block!important;
		margin-left: 0px;
		width: 100%;
	}
	.contentlarge {
		margin-top: 15px;
		margin-left: 40px;
		margin-right: 15px;
	}
	.bio {
		float: left;
		width: 72%;
	}
	.software {
		float: right;
		width: 25%;
	}
	.grey {
		background: #efedee;
		padding: 25px;
		height: 750px;
	}
	.grey h5 {
		color: #000;
		font-weight: bold;
	}
	.bio h3 {
		color: #1b4e49;
		font-weight: 700;
	}
	.skillsmargin{
		margin: 15px 0px;
	}
	.skillsmargin h5 {
		color: #1b4e49;
		text-transform: capitalize;
		padding: 0px;
		font-weight: 600;
	}
	.skillsmargin p {
		margin: 0;
	}
	.portfolio {
		margin-left: 18px;
	}
	.portfolio p {
		color: #1b4e49;
	}
	.threeportion ul{
		margin-left: -15px;
		margin-right: -15px;
	}
	.threeportion ul li {
		margin-left: 15px;
		margin-right: 15px;
		width: calc(33.3% - 34px);
		vertical-align: top;
		display: inline-block;
		margin-bottom: 30px;
	}
	.grey a {
		text-align: center;
		display: block;
		padding: 10px;
		border-radius: 20px;
		background: #e78789;
		margin-top: 24px;
		width: 150px;
		color: #fff;
		margin-left: 50%;
		transform: translateX(-50%);
		font-weight: 600;
	}
	.threeportion img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    box-shadow: 0px 0px 10px #dcdada;
}
	li.margin {
    width: calc(50% - 34px)!important;
    }
li.margin iframe {
    width: 100%;
}
	@media (min-width: 768px) and (max-width: 1024px){
		.bio {
			float: left;
			width: 62%;
		}
		.software {
			float: right;
			width: 35%;
		}
		.imgset img {
			width: 100%;
			object-fit: cover;
			height: auto;
		}
		.grey {
			height: 600px!important;
		}
	}
	@media (min-width: 576px) and (max-width: 767px){
		.bio {
			float: left;
			width: 55%;
		}
		.imgset img {
			width: 100%;
			object-fit: cover;
			height: auto;
		}
		.software {
			float: right;
			width: 42%;
		}
	}
	@media (min-width: 320px) and (max-width: 575px){
		.picture {
			float: none;
			margin-bottom: 10px;
		}
		.imgset img {
			width: 100%;
			object-fit: cover;
			height: auto;
		}
		.imgset {
			margin-left: 0px;
			margin-top: 0px;
		}
		.introduction {
			float: none;
			width: 100%;
		}
		.bio {
			float: none;
			width: 100%;
		}
		.contentlarge {
			margin-left: 0px;
			margin-right: 0px;
		}
		.portfolio {
			margin-left: 0px;
		}
		.software {
			float: none;
			width: 100%;
		}
		.grey {
			background: #efedee;
			padding: 25px;
			height: auto;
		}
	}
	@media (min-width: 320px) and (max-width: 480px){
		.ulmain li {
			margin-left: 15px;
			margin-right: 15px;
			width: calc(50% - 34px);
			vertical-align: top;
			display: inline-block;
		}
		.imgset img {
			width: 100%;
			object-fit: cover;
			height: auto;
		}
		ul.onlyinline {
			margin-bottom: 15px;
		}
		ul.onlyinline li {
			width: 100%;
		}
		.imgset {
			width: 100px;
			margin-left: 0px;
			margin-top: 0px;
		}
		ul.ulmain li {
			margin-left: 7px;
		}
	}
</style>
@extends('layouts.master')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="container">
	<div class="profile">
		<div class="picture">
			<div class="imgset">
				<?php if(!empty($influencer_profile_data[0]->profile_image)):?>
					<img src="/public/images/profile_images/<?= $influencer_profile_data[0]->profile_image;?>">
					<?php else:?>
						<img src="https://www.gravatar.com/avatar/2fb1121670440f4cd693e7478111d453.jpg?s=80&d=https%3A%2F%2Fselfieym.com%2Fimages%2Fuser.jpg&r=g">

					<?php endif;?>

				</div>
			</div>
			<div class="introduction">

				<h1><?php if(!empty($influencer_profile_data[0]->name)):?><?= $influencer_profile_data[0]->name;?><?php endif;?>&nbsp;<?php if(!empty($influencer_profile_data[0]->catname)):?><?= $influencer_profile_data[0]->catname;?><?php endif;?></h1>
				<h6>my social media statiscs</h6>
				<div class="mainprofile">
					<ul class="ulmain">
						<li>


							<ul class="onlyinline">
								<li><a href="<?php if(!empty($influencer_profile_data[0]->facebook_url)):?><?= $influencer_profile_data[0]->facebook_url;?><?php else:?><?= '#';?><?php endif;?>">Facebook:</a></li>

								<li><a href="<?php if(!empty($influencer_profile_data[0]->youtube_url)):?><?= $influencer_profile_data[0]->youtube_url;?><?php else:?><?= '#';?><?php endif;?>">Youtube:</a></li>

								<li><a href="<?php if(!empty($influencer_profile_data[0]->twitter_url)):?><?= $influencer_profile_data[0]->twitter_url;?><?php else:?><?= '#';?><?php endif;?>">Twitter:</a></li>

								<li><a href="<?php if(!empty($influencer_profile_data[0]->tiktok_url)):?><?= $influencer_profile_data[0]->tiktok_url;?><?php else:?><?= '#';?><?php endif;?>">Tiktok:</a></li>

								<li><a href="<?php if(!empty($influencer_profile_data[0]->quora_url)):?><?= $influencer_profile_data[0]->quora_url;?><?php else:?><?= '#';?><?php endif;?>">Quora:</a></li>

							</ul>
						</li>
						<li>
							<ul class="onlyinline">
								<li><a href="#"><?php if(!empty($influencer_profile_data[0]->facebook_followers)):?><?= $influencer_profile_data[0]->facebook_followers;?><?php else:?><?= '-';?><?php endif;?></a></li>

								<li><a href="#"><?php if(!empty($influencer_profile_data[0]->youtube_subscribers)):?><?= $influencer_profile_data[0]->youtube_subscribers;?><?php else:?><?= '-';?><?php endif;?></a></li>

								<li><a href="#"><?php if(!empty($influencer_profile_data[0]->twitter_followers)):?><?= $influencer_profile_data[0]->twitter_followers;?><?php else:?><?= '-';?><?php endif;?></a></li>

								<li><a href="#"><?php if(!empty($influencer_profile_data[0]->tiktok_followers)):?><?= $influencer_profile_data[0]->tiktok_followers;?><?php else:?><?= '-';?><?php endif;?></a></li>

								<li><a href="#"><?php if(!empty($influencer_profile_data[0]->quora_followers)):?><?= $influencer_profile_data[0]->quora_followers;?><?php else:?><?= '-';?><?php endif;?></a></li>

							</ul>
						</li>
						<li>
							<ul class="onlyinline">
								<li><a href="<?php if(!empty($influencer_profile_data[0]->facebook_url)):?><?= $influencer_profile_data[0]->facebook_url;?><?php else:?><?= '#';?><?php endif;?>">View Profile</a></li>

								<li><a href="<?php if(!empty($influencer_profile_data[0]->youtube_url)):?><?= $influencer_profile_data[0]->youtube_url;?><?php else:?><?= '#';?><?php endif;?>">View Profile</a></li>

								<li><a href="<?php if(!empty($influencer_profile_data[0]->twitter_url)):?><?= $influencer_profile_data[0]->twitter_url;?><?php else:?><?= '#';?><?php endif;?>">View Profile</a></li>

								<li><a href="<?php if(!empty($influencer_profile_data[0]->tiktok_url)):?><?= $influencer_profile_data[0]->tiktok_url;?><?php else:?><?= '#';?><?php endif;?>">View Profile</a></li>

								<li><a href="<?php if(!empty($influencer_profile_data[0]->quora_url)):?><?= $influencer_profile_data[0]->quora_url;?><?php else:?><?= '#';?><?php endif;?>">View Profile</a></li>

							</ul>
						</li>
					</ul>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="contentlarge">
			<div class="bio">
				<h3>My Profile/Bio</h3>
				<?php if(!empty($influencer_profile_data[0]->biodata)):?>
					<p><?= $influencer_profile_data[0]->biodata;?></p>
				<?php endif;?>
				
				<div class="skillsmargin">
					<h5 >Skills/Expertise</h5>
					<p><?php if(!empty($influencer_profile_data[0]->skill_expertise)):?><?= $influencer_profile_data[0]->skill_expertise;?><?php endif;?></p>
				</div>
				<div class="skillsmargin">
					<h5 >Brand/Client i have worked</h5>
					<p><?php if(!empty($influencer_profile_data[0]->client_base)):?><?= $influencer_profile_data[0]->client_base;?><?php endif;?></p>
				</div>
				@if(count($user_portfolio_data) > 0)
				<div class="portfolio">
					<h3>My Portfolio Campaign</h3>
					<!-- <p>Instagram/Youtube</p> -->
					<div class="threeportion">
						<ul>
							@foreach($user_portfolio_data as $portfolios)

							<li><a data-fancybox="gallery" class="primary-btn" href="/public/images/user_portfolio_images/{{$portfolios->portfolio_image}}"><img src="/public/images/user_portfolio_images/{{$portfolios->portfolio_image}}"></a></li>
							@endforeach
						</ul>
					</div>
				</div>
				@endif
				<!-- YOUTUBE SECTION-->
				@if(count($user_youtube_data) > 0)
				<div class="portfolio">
					<h3>My Youtube Videos</h3>
					<!-- <p>Instagram/Youtube</p> -->
					<div class="threeportion">
						
						<ul class="iframeuniq">
							@foreach($user_youtube_data as $youtube_data)

							<li class="margin"><iframe
								src="<?= $youtube_data->youtube_url;?>">
							</iframe></li>
							@endforeach
						</ul>

					</div>
				</div>
				@endif
				<!-- YOUTUBE SECTION-->
			</div>
			<div class="software">
				<div class="grey">
					<h5>Industry: <?php if($influencer_profile_data[0]->catname):?><?= $influencer_profile_data[0]->catname;?><?php endif;?></h5>
					<h5>Location: <?php if($influencer_profile_data[0]->cityname):?><?= $influencer_profile_data[0]->cityname;?><?php endif;?></h5>
					<h5>Gender: <?php if($influencer_profile_data[0]->gender):?><?= ucfirst($influencer_profile_data[0]->gender);?><?php endif;?></h5>
					<h5>Age: <?php if($influencer_profile_data[0]->age):?><?= $influencer_profile_data[0]->age;?><?php endif;?></h5>
					<h5>Rate: Rs:<?php if($influencer_profile_data[0]->min_fee):?><?= $influencer_profile_data[0]->min_fee;?><?php endif;?></h5>
					<?php if($user_type_id == 1):?>
						<a href="#" data-attr-contact-id="{{$influencer_profile_data[0]->id}}" id="ContactInfluencer">Contact Me</a>
					<?php endif; ?>
					
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>


	<!------------CONTACT INFLUENCER MODAL------->
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				@if(session()->has('errormessage'))
				<script>
					$(document).ready(function()
					{
						var modal = document.getElementById("myModal");
						modal.style.display = "block";
						window.onclick = function(event) {
							if (event.target == modal) {
								modal.style.display = "none";
							}
						}

						$(document).on('click','a.close',function()
						{

							var modal = document.getElementById("myModal");
							modal.style.display = "none";

						});

					});

				</script>
				<div class="alert alert-danger">
					{{ session()->get('errormessage') }}
				</div>
				@endif

				@if(session()->has('message'))
				<script>
					$(document).ready(function()
					{
						var modal = document.getElementById("myModal");
						modal.style.display = "block";
						window.onclick = function(event) {
							if (event.target == modal) {
								modal.style.display = "none";
							}
						}

						$(document).on('click','a.close',function()
						{

							var modal = document.getElementById("myModal");
							modal.style.display = "none";

						});

					});

				</script>
				<div class="alert alert-success">
					{{ session()->get('message') }}
				</div>
				@endif
				@if(count($errors) > 0 )
				<script>
					$(document).ready(function()
					{
						alert('TAEST');
						var modal = document.getElementById("myModal");
						modal.style.display = "block";

						$(document).on('click','a.close',function()
						{
							
							var modal = document.getElementById("myModal");
							modal.style.display = "none";

						});

					});

				</script>
				<div class="alert alert-danger" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<ul class="p-0 m-0" style="list-style: none;">
						@foreach($errors->all() as $error)
						<li>{{$error}}</li>
						@endforeach
					</ul>
				</div>
				@endif
				<div class="modal-header">
					<h4 style="text-align:center;">Contact <?php if(!empty($influencer_profile_data[0]->name)):?><?= $influencer_profile_data[0]->name;?><?php endif;?></h4>
					<a href="javascript:void(0)" class="close">&times;</a>
				</div>
				<div class="modal-body">
					<form action="/messageinfluencer" method="POST">
						@csrf
						<input type="hidden" name="influencer_id" value = "<?php if($influencer_id): ?><?= encrypt($influencer_id);?><?php else: ?><?= '';?><?php endif; ?>">


						<input type="hidden" name="from_name" value = "<?php if($from_name): ?><?= $from_name;?><?php else: ?><?= '';?><?php endif; ?>">
						<input type="hidden" name="from_email" value = "<?php if($from_email): ?><?= $from_email;?><?php else: ?><?= '';?><?php endif; ?>">
						<input type="hidden" name="to_name" value = "<?php if($to_name): ?><?= $to_name;?><?php else: ?><?= '';?><?php endif; ?>">
						<input type="hidden" name="to_email" value = "<?php if($to_email): ?><?= $to_email;?><?php else: ?><?= '';?><?php endif; ?>">
						<input type="hidden" name="to_user_id" value = "<?php if($to_user_id): ?><?= $to_user_id;?><?php else: ?><?= '';?><?php endif; ?>">

						<input type="hidden" name="to_phone" value = "<?php if($to_phone): ?><?= $to_phone;?><?php else: ?><?= '';?><?php endif; ?>">

						<div class="form-group">
							<label for="message">Messages:</label>
							<textarea name="message" class="form-control">Default Message for the influencers..</textarea>
						</div>
						<button type="submit" class="btn btn-default">Submit</button>
					</form>
				</div>
				<div class="modal-footer">

				</div>
			</div>

		</div>
	</div>
	<!------------CONTACT INFLUENCER MODAL------->

	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

	<link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.0/jquery.fancybox.min.css" rel="stylesheet" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.0/jquery.fancybox.min.js"></script>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.0/jquery.fancybox.min.css">


	<script>
		(function($){
			$(document).ready(function(){
				$(document).on('click','#ContactInfluencer',function(event)
				{
					var base_url = '<?= URL::to('/'); ?>';
					var loggedin_userid = '<?= $loggedin_userid;?>';
					event.preventDefault();
					if(loggedin_userid == ''){
						var session_flag = '<?php Session::put('RedirectionFlagInfluencer', url()->current());?>';
						toastr.info('Redirecting.. Login to continue');
						window.location.href = base_url;
					}else{
						var modal = document.getElementById("myModal");


						modal.style.display = "block";
					}
					window.onclick = function(event) {
						if (event.target == modal) {
							modal.style.display = "none";
						}
					}

					var span = document.getElementsByClassName("close")[0];
					span.onclick = function() {
						modal.style.display = "none";
					}
				});


			});

			// When the user clicks anywhere outside of the modal, close it

		})(jQuery); 
	</script>