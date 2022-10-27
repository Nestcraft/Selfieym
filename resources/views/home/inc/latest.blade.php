<?php
if (!isset($cacheExpiration)) {
    $cacheExpiration = (int)config('settings.optimization.cache_expiration');
}
?>
<style>
    .feature {
        width: 100%;
        margin: 10px 15px;
    }
    .feature ul {
        margin-left: -15px;
        margin-right: -15px;
    }
    .wihtebox {
        box-shadow: 0px 0px 15px #dadada;
    }
    .feature ul li{
        margin-left: 15px;
        margin-right: 15px;
        width: calc(25% - 34px);
        vertical-align: top;
        display: inline-block;
    }
    .imagebox {
      background: url('/public/images/nature.jpg');
      /*  background: gray; */
      width: 100%;
      height: 150px;
      position: relative;
  }
.imagebox img {
    position: absolute;
    bottom: 18px;
    left: 18px;
    border-radius: 100%;
    width: 50px;
    height: 50px;
}

.contentprofile {
    margin-top: 7px;
    padding-left: 5px;
}

.contentprofile h2 {
    padding: 0px;
    color: #367aa6;
}

.contentprofile p {
    padding: 0px;
    color: #367aa6;
    margin: 0px;
}

.buttonsaco {
    margin-top: 4px;
}

.buttonsaco a {
    background: #ef2f0d;
    color: #ffff;
    padding: 2px 10px;
    margin-bottom: 5px;
    display: inline-block;
    text-transform: capitalize;
    border-radius: 5px;
}

.bottomfooter {
    background: #f3f1f1;
    padding: 12px 10px;
    padding-bottom: 0px;
}

.bottomfooter .left {
    float: left;
    color: #9b9b9b;
}

.bottomfooter .right {
    float: right;
    color: #9b9b9b;
}

.frontfeatre {
    position: absolute;
    right: 10px;
    top: 10px;
}


@media (min-width: 768px) and (max-width: 992px){
    .feature ul li {
        width: calc(50% - 34px);
        margin-bottom: 15px;
    }

}

@media (min-width: 320px) and (max-width: 767px){
    .feature ul li {
        width: calc(100% - 34px);
        margin-bottom: 15px;
    }

}
.mapicon {
    padding-top: 7px;
}
</style>
@if (isset($latest) and !empty($latest) and !empty($latest->posts) || !empty($influencer_details))
@include('home.inc.spacer')
<div class="container">
    <div class="col-xl-12 content-box layout-section">
        <div class="row row-featured row-featured-category">

            <div class="col-xl-12 box-title no-border">
                <div class="inner">
                    <h2>
                        <span class="title-3">Latest Jobs & Projects</span>
                        <a href="{{ $latest->link }}" class="sell-your-item">
                            {{ t('View more') }} <i class="icon-th-list"></i>
                        </a>
                    </h2>
                </div>
            </div>


            <!-- LATEST JOBS OLD SECTION  --->
            <div class="adds-wrapper jobs-list">
                <?php
                foreach($latest->posts as $key => $post):
                    if($post->dont_show_flag=='0'):

                        // Get the Post's City
                    $cacheId = config('country.code') . '.city.' . $post->city_id;
                    $city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                        $city = \App\Models\City::find($post->city_id);
                        return $city;
                    });
                    if (empty($city)) continue;

                        // Get the Post's Type
                    $cacheId = 'postType.' . $post->post_type_id . '.' . config('app.locale');
                    $postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                        $postType = \App\Models\PostType::findTrans($post->post_type_id);
                        return $postType;
                    });
                    if (empty($postType)) continue;

                        // Get the Post's Salary Type
                    $cacheId = 'salaryType.' . $post->salary_type_id . '.' . config('app.locale');
                    $salaryType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                        $salaryType = \App\Models\SalaryType::findTrans($post->salary_type_id);
                        return $salaryType;
                    });
                    if (empty($salaryType)) continue;

                        // Convert the created_at date to Carbon object
                    $post->created_at = (new \Date($post->created_at))->timezone(config('timezone.id'));
                    $post->created_at = $post->created_at->ago();
                    ?>
                    <div class="item-list job-item">
                        <div class="row">
                            <div class="col-md-1 col-sm-2 no-padding photobox">
                                <div class="add-image">
                                    <a href="{{ \App\Helpers\UrlGen::post($post) }}">
                                        <img class="img-thumbnail no-margin" alt="{{ $post->company_name }}" src="{{ imgUrl(\App\Models\Post::getLogo($post->logo), 'medium') }}">
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-10 col-sm-10 add-desc-box">
                                <div class="add-details jobs-item">
                                    <h5 class="company-title ">
                                        @if (!empty($post->company_id))
                                        <?php $attr = ['countryCode' => config('country.icode'), 'id' => $post->company_id]; ?>
                                        <a href="{{ lurl(trans('routes.v-search-company', $attr), $attr) }}">
                                            {{ $post->company_name }}
                                        </a>
                                        @else
                                        <strong>{{ $post->company_name }}</strong>
                                        @endif
                                    </h5>
                                    <h2 class="job-title">
                                        <a href="{{ \App\Helpers\UrlGen::post($post) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h2>
                                    <span class="info-row">
                                        <span class="date"><i class="icon-clock"></i> {{ $post->created_at }}</span>
                                        <span class="item-location">
                                            <i class="icon-location-2"></i>&nbsp;
                                            {{ $city->name }}
                                        </span>
                                        <span class="date"><i class="icon-clock"></i> {{ $postType->name }}</span>
                                        <span class="salary">
                                            <i class="icon-money"></i>&nbsp;
                                            @if ($post->salary_min > 0 or $post->salary_max > 0)
                                            @if ($post->salary_min > 0)
                                            {!! \App\Helpers\Number::money($post->salary_min) !!}
                                            @endif
                                            @if ($post->salary_max > 0)
                                            @if ($post->salary_min > 0)
                                            &nbsp;-&nbsp;
                                            @endif
                                            {!! \App\Helpers\Number::money($post->salary_max) !!}
                                            @endif
                                            @else
                                            {!! \App\Helpers\Number::money('--') !!}
                                            @endif
                                            @if (!empty($salaryType))
                                            {{ t('per') }} {{ $salaryType->name }}
                                            @endif
                                        </span>
                                    </span>

                                    <div class="jobs-desc">
                                        {!! \Illuminate\Support\Str::limit(strCleaner($post->description), 180) !!}
                                    </div>

                                    <div class="job-actions">
                                        <ul class="list-unstyled list-inline">
                                            @if (auth()->check())
                                            @if (\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() <= 0)
                                            <li id="{{ $post->id }}">
                                                <a class="save-job" id="save-{{ $post->id }}" href="javascript:void(0)">
                                                    <span class="far fa-heart"></span>
                                                    {{ t('Save Job') }}
                                                </a>
                                            </li>
                                            @else
                                            <li class="saved-job" id="{{ $post->id }}">
                                                <a class="saved-job" id="saved-{{ $post->id }}" href="javascript:void(0)">
                                                    <span class="fa fa-heart"></span>
                                                    {{ t('Saved Job') }}
                                                </a>
                                            </li>
                                            @endif
                                            @else
                                            <li id="{{ $post->id }}">
                                                <a class="save-job" id="save-{{ $post->id }}" href="javascript:void(0)">
                                                    <span class="far fa-heart"></span>
                                                    {{ t('Save Job') }}
                                                </a>
                                            </li>
                                            @endif
                                            <li>
                                                <a class="email-job" data-toggle="modal" data-id="{{ $post->id }}" href="#sendByEmail" id="email-{{ $post->id }}">
                                                    <i class="fa fa-envelope"></i>
                                                    {{ t('Email Job') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                      <?php endif; ?>

                <?php endforeach; ?>

            </div>
            <!-- LATEST JOBS OLD SECTION --->
            
            <div class="tab-box save-search-bar text-center">
                <?php $attr = ['countryCode' => config('country.icode')]; ?>
                <a class="text-uppercase" href="{{ $latest->link }}">
                    <i class="icon-briefcase"></i>View all jobs
                </a>
            </div>
        </div>

    </div>

</div>
<div class="h-spacer"></div>
<div class="container">

    <!--INFLUENCERS SECTION -->
    <div class="col-xl-12 content-box layout-section">
        <div class="row row-featured row-featured-category">

            <div class="col-xl-12 box-title no-border">
                <div class="inner">
                    <h2>
                        <span class="title-3">Featured Influencers</span>
                        <a href="{{ $latestinfluencers->link }}" class="sell-your-item">
                            {{ t('View more') }} <i class="icon-th-list"></i>
                        </a>
                    </h2>
                </div>
            </div>
            <!-- FEATURED INFLUENCERS SECTION --->

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="feature row">
            

                @foreach($influencer_details as $influencer_dtls)
                <!----new desgin-->
                <div class="col-md-3">

            <div class="card mb-3 box-shadow">
<?php if (isset($influencer_dtls->is_featured) && $influencer_dtls->is_featured == 1): ?>

<div class="profile-featured frontfeatre"><span class="label badge-danger"> Featured</span></div>

<?php endif;?>
                <a href="/influencer-profile/{{$influencer_dtls->id}}">
                    <img class="card-img-top" style=" height:160px;" img src="/images/profile_images/<?= $influencer_dtls->profile_image;?>" alt="<?= $influencer_dtls->name;?>" title="<?= $influencer_dtls->name;?>">
                </a>

                <div class="card-body">
                     <h3 class="card-title h-0"><?= substr($influencer_dtls->name,'0','15');?></h3>
                    <ul class="list-unstyled list-inline rating mb-0 review text-warning w-75">
                        <?php $influencer_rating = \App\Helpers\UrlGen::get_influencer_rating($influencer_dtls->id); ?>

                        <?php
                        for ($count=1;$count <=$influencer_rating;$count++) {
                            echo '<li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>';
                        }
                        ?>

                    </ul>
                    <?php if(!empty($influencer_dtls->skill_expertise)):?>

                        <?php 
                        $skill_expertise = explode(',',$influencer_dtls->skill_expertise);
                        if(!empty($skill_expertise)):?>
                            <?php
                            $i=1; 

                            foreach($skill_expertise as $skills):
                                if($i<=3){?>
                                <a href="javascript:void(0)"  class="label badge-dark" style="margin:2px;"><?= strtoupper($skills);?></a>
                            <?php } $i++; endforeach;?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class="mapicon">
                        <?php  $cacheId1 = config('country.code') . '.city.' . $influencer_dtls->city_id;
                    $city1 = \Illuminate\Support\Facades\Cache::remember($cacheId1, $cacheExpiration, function () use ($influencer_dtls) {
                        $city1 = \App\Models\City::find($influencer_dtls->city_id);
                        return $city1;
                    });
                    ?>
                    <i class="fa fa-map-marker" aria-hidden="true"></i> &nbsp;@if(!empty($city1->name)){{ $city1->name }}@endif
                        </div>
                      <b>Industry:</b> <?php if (!empty($influencer_dtls->catname)): ?><?=$influencer_dtls->catname;?><?php endif;?>  
                </div>
                <div class="bottomfooter">
                    
                    <p><b>STARTING AT:</b> @if(!empty($influencer_dtls->min_fee))<span class="badge badge-success"><?= \App\Helpers\Number::money($influencer_dtls->min_fee); ?></span> @endif
                        <span class="fa-pull-right"><i class="fa fa-users" aria-hidden="true"></i> 
                            <?php
                            if(isset($influencer_dtls->facebook_followers) && $influencer_dtls->facebook_followers > 0)
                            {

                                $facebook_followers =  str_replace('K','',$influencer_dtls->facebook_followers);
                            }else{

                                $facebook_followers = 0;

                            }

                            if(isset($influencer_dtls->instagram_followers) && $influencer_dtls->instagram_followers > 0)
                            {

                                $instagram_followers =  str_replace('K','',$influencer_dtls->instagram_followers);
                            }else{

                                $instagram_followers = 0;

                            }


                            if(isset($influencer_dtls->quora_followers) && $influencer_dtls->quora_followers > 0)
                            {

                                $quora_followers =  str_replace('K','',$influencer_dtls->quora_followers);
                            }else{

                                $quora_followers = 0;

                            }


                            if(isset($influencer_dtls->twitter_followers) && $influencer_dtls->twitter_followers > 0)
                            {

                                $twitter_followers =  str_replace('K','',$influencer_dtls->twitter_followers);
                            }else{

                                $twitter_followers = 0;

                            }

                            if(isset($influencer_dtls->youtube_subscribers) && $influencer_dtls->youtube_subscribers > 0)
                            {

                                $youtube_subscribers =  str_replace('K','',$influencer_dtls->youtube_subscribers);
                            }else{

                                $youtube_subscribers = 0;

                            }

                            ?>
                            
                            <b><?php echo (int)$facebook_followers + (int)$instagram_followers + (int)$twitter_followers + (int)$quora_followers + (int)$youtube_subscribers;?> K</b> 
                        
                </span></p>
            </div>
        </div>
    </div>
                <!----end new design----->
                @endforeach

               
</div>
         <!--    <div class="adds-wrapper jobs-list">
                <?php
                foreach($influencer_details as $influencer_dtls): ?>
                    <div class="item-list job-item">
                        <div class="row">
                            <div class="col-md-1 col-sm-2 no-padding photobox">
                                <div class="add-image">
                                    <a href="#">
                                        @if(!empty($influencer_dtls->profile_image))
                                        <img class="img-thumbnail no-margin" alt="" src="/images/profile_images/<?= $influencer_dtls->profile_image;?>">
                                        @else
                                        <img class="img-thumbnail no-margin" alt="" src="https://www.gravatar.com/avatar/27dd9f271a5733f68306f0d248d4dd1f.jpg?s=80&d=https%3A%2F%2Fselfieym.com%2Fimages%2Fuser.jpg&r=g">
                                        @endif
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-10 col-sm-10 add-desc-box">
                                <div class="add-details jobs-item">

                                    <h4 class="job-title">
                                        {{$influencer_dtls->name}}
                                         <span>@if($influencer_dtls->username){{$influencer_dtls->username}}@endif</span>
                                    </h4>
                                    <span class="info-row">
                                        <span>@if($influencer_dtls->catname){{$influencer_dtls->catname}}@endif</span>
                                    </span>
                                     <span class="info-row">
                                        <span>@if($influencer_dtls->cityname)<i class="icon-location-2"></i>{{$influencer_dtls->cityname}}@endif</span>
                                    </span>

                                    <div class="job-actions">
                                        <ul class="list-unstyled list-inline">
                                            @if($influencer_dtls->facebook_followers)
                                            <li>
                                                {{$influencer_dtls->facebook_followers}}<span><i class="fa fa-facebook"></i></span>
                                            </li>
                                            @endif
                                            @if($influencer_dtls->twitter_followers)
                                            <li>
                                                {{$influencer_dtls->twitter_followers}}<span><i class="fa fa-twitter"></i></span>
                                            </li>
                                            @endif
                                            @if($influencer_dtls->youtube_subscribers)
                                            <li>
                                                {{$influencer_dtls->youtube_subscribers}}<span><i class="fa fa-youtube"></i></span>
                                            </li>
                                            @endif
                                            @if($influencer_dtls->tiktok_followers)
                                            <li>
                                                {{$influencer_dtls->tiktok_followers}}<span><i class="fa fa-tiktok"></i></span>
                                            </li>
                                            @endif
                                            @if($influencer_dtls->quora_followers)
                                            <li>
                                               {{$influencer_dtls->quora_followers}}<span><i class="fa fa-quora"></i></span>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <div class="tab-box save-search-bar text-center">
                <?php $attr = ['countryCode' => config('country.icode')]; ?>
                <a class="text-uppercase" href="#">
                    <i class="icon-briefcase"></i>View all influencers
                </a>
            </div>

        </div> -->

        <!-- FEATURED INFLUENCERS SECTION --->


    </div>
</div>
<!--INFLUENCERS SECTION -->
</div>
@endif

@section('modal_location')
@parent
@include('layouts.inc.modal.send-by-email')
@endsection

@section('after_scripts')
@parent
<script>
    /* Favorites Translation */
    var lang = {
       labelSavePostSave: "{!! t('Save Job') !!}",
       labelSavePostRemove: "{{ t('Saved Job') }}",
       loginToSavePost: "{!! t('Please log in to save the Ads.') !!}",
       loginToSaveSearch: "{!! t('Please log in to save your search.') !!}",
       confirmationSavePost: "{!! t('Post saved in favorites successfully !') !!}",
       confirmationRemoveSavePost: "{!! t('Post deleted from favorites successfully !') !!}",
       confirmationSaveSearch: "{!! t('Search saved successfully !') !!}",
       confirmationRemoveSaveSearch: "{!! t('Search deleted successfully !') !!}"
   };

   $(document).ready(function ()
   {
    /* Get Post ID */
    $('.email-job').click(function(){
        var postId = $(this).attr("data-id");
        $('input[type=hidden][name=post]').val(postId);
    });

    @if (isset($errors) and $errors->any())
    @if (old('sendByEmailForm')=='1')
    $('#sendByEmail').modal();
    @endif
    @endif
});
</script>
@endsection