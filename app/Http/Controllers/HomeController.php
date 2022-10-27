<?php
/**
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
 */

namespace App\Http\Controllers;
use App\Helpers\ArrayHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\UrlGen;
use App\Http\Requests\SendMessageInfluencerRequest;
use App\Models\Category;
use App\Models\City;
use App\Models\Message;
use App\Models\Company;
use App\Models\HomeSection;
use App\Models\Post;
use App\Models\SubAdmin1;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Torann\LaravelMetaTags\Facades\MetaTag;
use Laravel\Socialite\Facades\Socialite;
use Session;

class HomeController extends FrontController
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Check Country URL for SEO
        $countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
        view()->share('countries', $countries);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        $data        = [];
        $countryCode = config('country.code');

        // Get all homepage sections
        $cacheId          = $countryCode . '.homeSections';
        $data['sections'] = Cache::remember($cacheId, $this->cacheExpiration, function () use ($countryCode) {
            $sections = collect([]);

            // Check if the Domain Mapping plugin is available
            if (config('plugins.domainmapping.installed')) {
                try {
                    $sections = \App\Plugins\domainmapping\app\Models\DomainHomeSection::where('country_code', $countryCode)->orderBy('lft')->get();
                } catch (\Exception $e) {
                }
            }

            // Get the entry from the core
            if ($sections->count() <= 0) {
                $sections = HomeSection::orderBy('lft')->get();
            }

            return $sections;
        });

        if ($data['sections']->count() > 0) {
            foreach ($data['sections'] as $section) {
                // Clear method name
                $method = str_replace(strtolower($countryCode) . '_', '', $section->method);

                // Check if method exists
                if (!method_exists($this, $method)) {
                    continue;
                }

                // Call the method
                try {
                    if (isset($section->value)) {
                        $this->{$method}($section->value);
                    } else {
                        $this->{$method}();
                    }
                } catch (\Exception $e) {
                    flash($e->getMessage())->error();
                    continue;
                }
            }
        }

        // Get SEO
        $this->setSeo();

        return view('home.index', $data);
    }

    /**
     * Get search form (Always in Top)
     *
     * @param array $value
     */
    protected function getSearchForm($value = [])
    {
        view()->share('searchFormOptions', $value);
    }

    /**
     * Get locations & SVG map
     *
     * @param array $value
     */
    protected function getLocations($value = [])
    {
        // Get the default Max. Items
        $maxItems = 14;
        if (isset($value['max_items'])) {
            $maxItems = (int) $value['max_items'];
        }

        // Get the Default Cache delay expiration
        $cacheExpiration = $this->getCacheExpirationTime($value);

        // Modal - States Collection
        $cacheId     = config('country.code') . '.home.getLocations.modalAdmins';
        $modalAdmins = Cache::remember($cacheId, $cacheExpiration, function () {
            return SubAdmin1::currentCountry()->orderBy('name')->get(['code', 'name'])->keyBy('code');
        });
        view()->share('modalAdmins', $modalAdmins);

        // Get cities
        $cacheId = config('country.code') . 'home.getLocations.cities';
        $cities  = Cache::remember($cacheId, $cacheExpiration, function () use ($maxItems) {
            return City::currentCountry()->take($maxItems)->orderBy('population', 'DESC')->orderBy('name')->get();
        });
        $cities = collect($cities)->push(ArrayHelper::toObject([
            'id'             => 999999999,
            'name'           => t('More cities') . ' &raquo;',
            'subadmin1_code' => 0,
        ]));

        // Get cities number of columns
        $numberOfCols = 4;
        if (file_exists(config('larapen.core.maps.path') . strtolower(config('country.code')) . '.svg')) {
            if (isset($value['show_map']) && $value['show_map'] == '1') {
                $numberOfCols = (isset($value['items_cols']) && !empty($value['items_cols'])) ? (int) $value['items_cols'] : 3;
            }
        }

        // Chunk
        $maxRowsPerCol = round($cities->count() / $numberOfCols, 0); // PHP_ROUND_HALF_EVEN
        $maxRowsPerCol = ($maxRowsPerCol > 0) ? $maxRowsPerCol : 1; // Fix array_chunk with 0
        $cities        = $cities->chunk($maxRowsPerCol);

        view()->share('cities', $cities);
        view()->share('citiesOptions', $value);
    }

    /**
     * Get sponsored posts
     *
     * @param array $value
     */
    protected function getSponsoredPosts($value = [])
    {
        $type = 'sponsored';

        // Get the default Max. Items
        $maxItems = 20;
        if (isset($value['max_items'])) {
            $maxItems = (int) $value['max_items'];
        }

        // Get the default orderBy value
        $orderBy = 'random';
        if (isset($value['order_by'])) {
            $orderBy = $value['order_by'];
        }

        // Get the Default Cache delay expiration
        $cacheExpiration = $this->getCacheExpirationTime($value);

        $sponsored = null;

        // Get Posts
        $cacheId = config('country.code') . '.home.getPosts.' . $type;
        $posts   = Cache::remember($cacheId, $cacheExpiration, function () use ($maxItems, $type) {
            return Post::getLatestOrSponsored($maxItems, $type);
        });

        if (!empty($posts)) {
            if ($orderBy == 'random') {
                ArrayHelper::shuffleAssoc($posts);
            }
            $attr      = ['countryCode' => config('country.icode')];
            $sponsored = [
                'title' => t('Home - Sponsored Jobs'),
                'link'  => lurl(trans('routes.e-search', $attr), $attr),
                'posts' => $posts,
            ];
            $sponsored = ArrayHelper::toObject($sponsored);
        }

        view()->share('featured', $sponsored);
        view()->share('featuredOptions', $value);
    }

    /**
     * Get latest posts
     *
     * @param array $value
     */
    protected function getLatestPosts($value = [])
    {
        $type = 'latest';

        // Get the default Max. Items
        $maxItems = 5;
        if (isset($value['max_items'])) {
            $maxItems = (int) $value['max_items'];
        }

        // Get the default orderBy value
        $orderBy = 'date';
        if (isset($value['order_by'])) {
            $orderBy = $value['order_by'];
        }

        // Get the Default Cache delay expiration
        $cacheExpiration = $this->getCacheExpirationTime($value);

        $latest = null;

        // Get Posts
        $cacheId = config('country.code') . '.home.getPosts.' . $type;
        $posts   = Cache::remember($cacheId, $cacheExpiration, function () use ($maxItems, $type) {
            return Post::getLatestOrSponsored($maxItems, $type);
        });

        if (!empty($posts)) {
            if ($orderBy == 'random') {
                $posts = ArrayHelper::shuffleAssoc($posts);
            }
            $attr = ['countryCode' => config('country.icode')];

            $latest = [
                'title' => t('Home - Latest Jobs'),
                'link'  => lurl(trans('routes.v-search', $attr), $attr),
                'posts' => $posts,
            ];

            $latest = ArrayHelper::toObject($latest);

        }

        $influencer_details = DB::table('social')
        ->leftJoin('cities', 'cities.id', '=', 'social.city_id')
        ->leftJoin('categories', 'categories.id', '=', 'social.category_id')
        ->leftJoin('users', 'users.id', '=', 'social.user_id')
        ->where('users.is_featured', 1)
        ->select('social.*', 'users.*','categories.name as catname', 'cities.name as cityname')
        ->inRandomOrder()->limit(5)->get();
      /*  echo '<pre>';
        print_r($influencer_details);
        die;*/


        if (!empty($influencer_details)) {
            if ($orderBy == 'random') {
                $influencer_details = ArrayHelper::shuffleAssoc($posts);
            }
            $attr = ['countryCode' => config('country.icode')];

            $latestinfluencers = [
                'title'              => t('Home - Latest Influencers'),
                'link'               => lurl(trans('routes.i-search', $attr), $attr),
                'influencer_details' => $influencer_details,
            ];

            $latestinfluencers = ArrayHelper::toObject($latestinfluencers);

        }

        view()->share('latest', $latest);
        view()->share('latestinfluencers', $latestinfluencers);
        view()->share('influencer_details', $influencer_details);
        view()->share('latestOptions', $value);
    }

    /**
     * Get featured ads companies
     *
     * @param array $value
     */
    private function getFeaturedPostsCompanies($value = [])
    {
        // Get the default Max. Items
        $maxItems = 12;
        if (isset($value['max_items'])) {
            $maxItems = (int) $value['max_items'];
        }

        // Get the default orderBy value
        $orderBy = 'random';
        if (isset($value['order_by'])) {
            $orderBy = $value['order_by'];
        }

        // Get the Default Cache delay expiration
        $cacheExpiration = $this->getCacheExpirationTime($value);

        $featuredCompanies = null;

        // Get all Companies
        $cacheId   = config('country.code') . '.home.getFeaturedPostsCompanies.take.limit.x';
        $companies = Cache::remember($cacheId, $cacheExpiration, function () use ($maxItems) {
            return Company::whereHas('posts', function ($query) {
                $query->currentCountry();
            })
            ->withCount([
                'posts' => function ($query) {
                    $query->currentCountry();
                },
            ])
            ->take($maxItems)
            ->orderByDesc('id')
            ->get();
        });

        if ($companies->count() > 0) {
            if ($orderBy == 'random') {
                $companies = $companies->shuffle();
            }
            $featuredCompanies = [
                'title'     => t('Home - Featured Companies'),
                'link'      => UrlGen::company(),
                'companies' => $companies,
            ];
            $featuredCompanies = ArrayHelper::toObject($featuredCompanies);
        }

        view()->share('featuredCompanies', $featuredCompanies);
        view()->share('featuredCompaniesOptions', $value);
    }

    /**
     * Get list of categories
     *
     * @param array $value
     */
    protected function getCategories($value = [])
    {
        // Get the default Max. Items
        $maxItems = 12;
        if (isset($value['max_items'])) {
            $maxItems = (int) $value['max_items'];
        }

        // Number of columns
        $numberOfCols = 3;

        // Get the Default Cache delay expiration
        $cacheExpiration = $this->getCacheExpirationTime($value);

        $cacheId = 'categories.parents.' . config('app.locale') . '.take.' . $maxItems;

        if (isset($value['type_of_display']) && in_array($value['type_of_display'], ['cc_normal_list', 'cc_normal_list_s'])) {

            $categories = Cache::remember($cacheId, $cacheExpiration, function () {
                return Category::trans()->orderBy('lft')->get();
            });
            $categories = collect($categories)->keyBy('translation_of');
            $categories = $subCategories = $categories->groupBy('parent_id');

            if ($categories->has(0)) {
                $categories    = $categories->get(0)->take($maxItems);
                $subCategories = $subCategories->forget(0);

                $maxRowsPerCol = round($categories->count() / $numberOfCols, 0, PHP_ROUND_HALF_EVEN);
                $maxRowsPerCol = ($maxRowsPerCol > 0) ? $maxRowsPerCol : 1;
                $categories    = $categories->chunk($maxRowsPerCol);
            } else {
                $categories    = collect([]);
                $subCategories = collect([]);
            }

            view()->share('categories', $categories);
            view()->share('subCategories', $subCategories);

        } else {

            $categories = Cache::remember($cacheId, $cacheExpiration, function () use ($maxItems) {
                $categories = Category::trans()->where('parent_id', 0)->take($maxItems)->orderBy('lft')->get();

                return $categories;
            });

            if (isset($value['type_of_display']) && $value['type_of_display'] == 'c_picture_icon') {
                $categories = collect($categories)->keyBy('id');
            } else {
                // $maxRowsPerCol = round($categories->count() / $numberOfCols, 0); // PHP_ROUND_HALF_EVEN
                $maxRowsPerCol = ceil($categories->count() / $numberOfCols);
                $maxRowsPerCol = ($maxRowsPerCol > 0) ? $maxRowsPerCol : 1; // Fix array_chunk with 0
                $categories    = $categories->chunk($maxRowsPerCol);
            }

            view()->share('categories', $categories);

        }

        view()->share('categoriesOptions', $value);
    }

    /**
     * Get mini stats data
     */
    protected function getStats()
    {
        // Count posts
        $countPosts = Post::currentCountry()->unarchived()->count();

        // Count cities
        $countCities = City::currentCountry()->count();

        // Count users
        $countUsers = User::count();

        // Share vars
        view()->share('countPosts', $countPosts);
        view()->share('countCities', $countCities);
        view()->share('countUsers', $countUsers);
    }

    /**
     * Set SEO information
     */
    protected function setSeo()
    {
        $title       = getMetaTag('title', 'home');
        $description = getMetaTag('description', 'home');
        $keywords    = getMetaTag('keywords', 'home');

        // Meta Tags
        MetaTag::set('title', $title);
        MetaTag::set('description', strip_tags($description));
        MetaTag::set('keywords', $keywords);

        // Open Graph
        $this->og->title($title)->description($description);
        view()->share('og', $this->og);
    }

    /**
     * @param array $value
     * @return int
     */
    private function getCacheExpirationTime($value = [])
    {
        // Get the default Cache Expiration Time
        $cacheExpiration = 0;
        if (isset($value['cache_expiration'])) {
            $cacheExpiration = (int) $value['cache_expiration'];
        }

        return $cacheExpiration;
    }

    /****Infulencer Full Profile******/
    /**** WEBC ******/

    public function viewInfluencerProfile($id)
    {

        $loggedin_userid = Auth::id();
        if($id=='1'){
          return Redirect::to(url('/'));
        }
        //\DB::enableQueryLog();
        $influencer_details = DB::table('social')
        ->leftJoin('cities', 'cities.id', '=', 'social.city_id')
        ->leftJoin('categories', 'categories.id', '=', 'social.category_id')
        ->leftJoin('users', 'users.id', '=', 'social.user_id')
        //->where('users.is_featured',1)
        ->where('users.id', $id)
        ->select('social.*', 'users.*', 'categories.name as catname', 'cities.name as cityname')
        ->get();
        $influencer_details_count = DB::table('social')
        ->leftJoin('cities', 'cities.id', '=', 'social.city_id')
        ->leftJoin('categories', 'categories.id', '=', 'social.category_id')
        ->leftJoin('users', 'users.id', '=', 'social.user_id')
        //->where('users.is_featured',1)
        ->where('users.id', $id)
        ->select('social.*', 'users.*', 'categories.name as catname', 'cities.name as cityname')
        ->count();
        //echo "<pre>";print_r($influencer_details);die;
        if ($loggedin_userid && $influencer_details) 
        {

            $user_details = DB::table('users')
            ->where('users.id', $loggedin_userid)
            ->select('users.*')
            ->first();

            $from_name    = $user_details->name;
            $from_email   = $user_details->email;
            $to_name      = "";
            $to_email     = "";
            $to_phone     = "";
            if(isset($influencer_details[0]->name)){
                $to_name      = $influencer_details[0]->name;
                $to_email     = $influencer_details[0]->email;
                $to_phone     = $influencer_details[0]->phone;
            }
            
            
            $to_user_id   = $id;
            $user_type_id = $user_details->user_type_id;

        } else {

            $from_name    = "";
            $from_email   = "";
            $to_name      = "";
            $to_email     = "";
            $to_phone     = "";
            $to_user_id   = "";
            $user_type_id = "";
        }

        $user_portfolio_data = DB::table('user_portfolio')->select()->where('user_portfolio.user_id', $id)->get();
        $rating_review = DB::table('rating_review')->select()->leftJoin('users', 'users.id', '=', 'rating_review.from_user_id_review')
        ->where('rating_review.to_user_id_review',$id)->limit('3')->get();
        $rating_review_count = DB::table('rating_review')->select()->leftJoin('users', 'users.id', '=', 'rating_review.from_user_id_review')
        ->where('rating_review.to_user_id_review',$id)->count();

        $user_youtube_data = DB::table('user_youtube_urls')->select()->where('user_youtube_urls.user_id', $id)->get();

        $payment = DB::table('user_bank_details')->select()->where('user_bank_details.user_id', $id)->count();
        if ($payment > 0) {
            $paymentverify = 'yes';
        } else {
            $paymentverify = 'no';
        }

        \DB::enableQueryLog();
        $myrate = DB::table('myrate')
        ->where('user_id', $id)
        ->select('*')
        ->first();

        if(count($influencer_details) == 0) {

     $influencer_details[0]='';
   
            //return abort(404);
        }
        $instagramCount = DB::table('sociallink')->where('user_id', $id)->select()->count();

        $instagramDetails = DB::table('sociallink')->where('user_id', $id)->select()->first();

return view('home.inc.influencer_profile', ['influencer_profile_data' => $influencer_details,'influencer_details_count' => $influencer_details_count, 'user_portfolio_data' => $user_portfolio_data, 'loggedin_userid' => $loggedin_userid, 'from_name' => $from_name, 'from_email' => $from_email, 'to_name' => $to_name, 'to_email' => $to_email, 'to_user_id' => $to_user_id, 'to_phone' => $to_phone, 'influencer_id' => $id, 'user_type_id' => $user_type_id, 'user_youtube_data' => $user_youtube_data, 'paymentverify' => $paymentverify, 'myrate' => $myrate, 'rating_review' => $rating_review,'rating_review_count' => $rating_review_count, 'instagramDetails' => $instagramDetails, 'instagramCount' => $instagramCount]);
}






    /*public function viewInfluencerProfile($id)
    {

    $loggedin_userid = Auth::id();
    //\DB::enableQueryLog();
    $influencer_details = DB::table('social')
    ->leftJoin('cities', 'cities.id', '=', 'social.city_id')
    ->leftJoin('categories', 'categories.id', '=', 'social.category_id')
    ->leftJoin('users', 'users.id', '=', 'social.user_id')
    //->where('users.is_featured',1)
    ->where('users.id',$id)
    ->select('social.*','users.*','categories.name as catname','cities.name as cityname')
    ->get();

    // echo '<pre>';
    // print_r($influencer_details);
    // die('well');

    // $query = \DB::getQueryLog();
    // print_r(end($query));
    // die('well');

    // // echo "<pre>";
    // print_r($influencer_details);
    if($loggedin_userid){

    $user_details = DB::table('users')
    ->where('users.id',$loggedin_userid)
    ->select('users.*')
    ->first();

    $from_name = $user_details->name;
    $from_email = $user_details->email;
    $to_name = $influencer_details[0]->name;
    $to_email = $influencer_details[0]->email;
    $to_phone = $influencer_details[0]->phone;
    $to_user_id = $id;
    $user_type_id = $user_details->user_type_id;
    }else{

    $from_name = "";
    $from_email = "";
    $to_name = "";
    $to_email = "";
    $to_phone = "";
    $to_user_id = "";
    $user_type_id = "";
    }

    $user_portfolio_data = DB::table('user_portfolio')->select()->where('user_portfolio.user_id',$id)->get();

    $user_youtube_data = DB::table('user_youtube_urls')->select()->where('user_youtube_urls.user_id',$id)->get();

    if(count($influencer_details) == 0){
    return abort(404);
    }

    return view('home.inc.influencer_profile', ['influencer_profile_data' => $influencer_details,'user_portfolio_data' => $user_portfolio_data,'loggedin_userid' => $loggedin_userid, 'from_name' =>$from_name, 'from_email' => $from_email, 'to_name' => $to_name, 'to_email' => $to_email,'to_user_id' =>$to_user_id, 'to_phone' => $to_phone, 'influencer_id' => $id,'user_type_id' => $user_type_id , 'user_youtube_data' => $user_youtube_data]);
    }
     */
    public function influencer_packages()
    {

        $logged_in_userid         = Auth::id();
        $influencer_packages_data = DB::table('packages_influencer')
        ->select('packages_influencer.*')
        ->get();

        $user_information = DB::table('users')
        ->select('users.*')
        ->where('users.id', $logged_in_userid)
        ->first();

        $user_role = $user_information->user_type_id;

        return view('home.inc.influencer_packages', ['influencer_packages_data' => $influencer_packages_data, 'logged_in_userid' => $logged_in_userid, 'user_role' => $user_role]);
    }

    public function employer_packages()
    {

        $logged_in_userid = Auth::id();

        $employer_packages_data = DB::table('packages')
        ->select('packages.*')
        ->get();

        $user_information = DB::table('users')
        ->select('users.*')
        ->where('users.id', $logged_in_userid)
        ->first();

        $user_role = $user_information->user_type_id;

        return view('home.inc.employer_packages', ['employer_packages_data' => $employer_packages_data, 'logged_in_userid' => $logged_in_userid, 'user_role' => $user_role]);
    }

    public function pay_u_money_employer_packages(Request $request)
    {
        $base_url = url('/');
        // PAY U MONEY CREDENTIALS

        $PAYU_MONEY_MERCHANT_KEY = PAY_U_MONEY_MERCHANT_KEY;
        $PAYU_MONEY_SALT         =PAY_U_MONEY_MERCHANT_SALT;

        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

        $surl = $base_url . '/paymentsuccess';
        $furl = $base_url . '/paymentsuccess';
        /*$furl = $base_url . '/paymentfailure';*/

        // PAY U MONEY CREDENTIALS

        $logged_in_userid = Auth::id();
        $user_name      = '';
        $user_email     = '';
        $user_phone     = '';
        $product_amount = '';
        $productinfo    = '';

        if (Auth::id()) {
            $user_info = DB::table('users')
            ->select('users.*')
            ->where('users.id', $logged_in_userid)
            ->get();

            $user_name  = $user_info[0]->name;
            $user_email = $user_info[0]->email;
            $user_phone = $user_info[0]->phone;

        }

        $employer_package_id = decrypt($request->input('i_package_id'));

        if ($employer_package_id) {

            $package_info = DB::table('packages')
            ->select('packages.*')
            ->where('packages.id', $employer_package_id)
            ->get();

            $product_amount = $package_info[0]->price;
            $productinfo    = $package_info[0]->name;
            $commission_type = $package_info[0]->commission_type;
            $commission = $package_info[0]->commission;
        }
        if($commission_type==1){
         /*$total_product_amount=$product_amount+$commission;*/
         $total_product_amount=$product_amount;
        }else{
      /*$total=$product_amount*$commission/100;*/
   /*  $total_product_amount=$product_amount+$total;*/
   $total_product_amount=$product_amount;
       }

        $udf1 = $logged_in_userid;
        $udf2 = $package_info[0]->id;
        $udf3 = $package_info[0]->id;
        $udf4 = $package_info[0]->id;
        $udf5 = 'employer';

        $hashstring = $PAYU_MONEY_MERCHANT_KEY . '|' . $txnid . '|' . $total_product_amount . '|' . $productinfo . '|' . $user_name . '|' . $user_email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $PAYU_MONEY_SALT;

        $hash = strtolower(hash('sha512', $hashstring));

        return view('home.inc.pay_u_money_standby', ['PAYU_MONYEY_MERCHANT_KEY' => $PAYU_MONEY_MERCHANT_KEY, 'txnid' => $txnid, 'product_amount' => $total_product_amount, 'productinfo' => $productinfo, 'user_name' => $user_name, 'user_email' => $user_email, 'udf1' => $udf1, 'udf2' => $udf2, 'udf3' => $udf3, 'udf4' => $udf4, 'udf5' => $udf5, 'PAYU_MONEY_SALT' => $PAYU_MONEY_SALT, 'hash' => $hash, 'user_phone' => $user_phone, 'surl' => $surl, 'furl' => $furl]);

    }

    public function pay_u_money_influencer_packages(Request $request)
    {
        $base_url = url('/');
        // PAY U MONEY CREDENTIALS

        $PAYU_MONEY_MERCHANT_KEY = PAY_U_MONEY_MERCHANT_KEY;
        $PAYU_MONEY_SALT         =PAY_U_MONEY_MERCHANT_SALT;

        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

        $surl = $base_url . '/paymentsuccess';
       /* $furl = $base_url . '/paymentfailure';*/
        $furl = $base_url . '/paymentsuccess';

        // PAY U MONEY CREDENTIALS

        $logged_in_userid = Auth::id();

        $user_name      = '';
        $user_email     = '';
        $user_phone     = '';
        $product_amount = '';
        $productinfo    = '';

        if (Auth::id()) {
            $user_info = DB::table('users')
            ->select('users.*')
            ->where('users.id', $logged_in_userid)
            ->get();

            $user_name  = $user_info[0]->name;
            $user_email = $user_info[0]->email;
            $user_phone = $user_info[0]->phone;

        }

        $influencer_package_id = decrypt($request->input('i_package_id'));


        if ($influencer_package_id) {

            $package_info = DB::table('packages_influencer')
            ->select('packages_influencer.*')
            ->where('packages_influencer.id', $influencer_package_id)
            ->get();
            $product_amount = $package_info[0]->price;
            $productinfo    = $package_info[0]->name;
            $commission_type    = $package_info[0]->commission_type;
            $commission    = $package_info[0]->commission;
        }
      
        if($commission_type==1){
       /*  $total_product_amount=$product_amount*/
       $total_product_amount=$product_amount;
        }else{
   /*   $total=$product_amount*$commission/100;*/
     $total_product_amount=$product_amount;
       }

        $udf1 = $logged_in_userid;
        $udf2 = $package_info[0]->id;
        $udf3 = $package_info[0]->id;
        $udf4 = $package_info[0]->id;
        $udf5 = $package_info[0]->id;

        $hashstring = $PAYU_MONEY_MERCHANT_KEY . '|' . $txnid . '|' . $total_product_amount . '|' . $productinfo . '|' . $user_name . '|' . $user_email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $PAYU_MONEY_SALT;

        $hash = strtolower(hash('sha512', $hashstring));

        return view('home.inc.pay_u_money_standby', ['PAYU_MONYEY_MERCHANT_KEY' => $PAYU_MONEY_MERCHANT_KEY, 'txnid' => $txnid, 'product_amount' => $total_product_amount, 'productinfo' => $productinfo, 'user_name' => $user_name, 'user_email' => $user_email, 'udf1' => $udf1, 'udf2' => $udf2, 'udf3' => $udf3, 'udf4' => $udf4, 'udf5' => $udf5, 'PAYU_MONEY_SALT' => $PAYU_MONEY_SALT, 'hash' => $hash, 'user_phone' => $user_phone, 'surl' => $surl, 'furl' => $furl]);

    }

    public function payumoney_paymentSuccess(Request $request)
    {

        $user_id               = $request->input('udf1');
        $package_id = $request->input('udf2');
        $txnid                 = $request->input('txnid');
        $product_amount        = $request->input('amount');
        $payment_status        = $request->input('status');
        $firstname             = $request->input('firstname');
        $mihpayid              = $request->input('mihpayid');

        $created_date = date('Y-m-d H:i:s');

        $payment_response = DB::table('packagepayments')->insert(
            ['user_id' => $user_id, 'package_id' => $package_id, 'txnid' => $txnid, 'product_amount' => $product_amount, 'payment_status' => $payment_status, 'created_date' => $created_date, 'mihpayid' => $mihpayid, 'transaction_type' => 'debit']
        );

        if ($payment_response) {


    $user_information = DB::table('users')
        ->select('users.*')
        ->where('users.id', $user_id)
        ->first();

        $user_role = $user_information->user_type_id;
        $user_name = $user_information->name;

        if ($user_role == 2) {
            $package_info = DB::table('packages_influencer')
            ->select('packages_influencer.*')
            ->where('packages_influencer.id', $package_id)
            ->first();
            $package_name = $package_info->name;
            $package_amount = $package_info->price;
            $number_of_bids           = $package_info->no_of_bids;
            $user_type      = 'influencer';
            $commission_type = $package_info->commission_type;
            /*$commission = $package_info->commission;*/

        } else {

            $package_info = DB::table('packages')
            ->select('packages.*')
            ->where('packages.id',$package_id)
            ->first();

            $package_name = $package_info->name;
            $package_amount = $package_info->price;
            $number_of_bids           = $package_info->no_of_contacts;
            $user_type      = 'employer';
            $commission_type = $package_info->commission_type;
            /*$commission = $package_info->commission;*/

        }
        /*if($commission_type==1){

         $adminCommission=$commission;
        }else{
        $total=$package_amount*$commission/100;
        $adminCommission=$commission;
       }*/
       $transaction_remarks = 'User ' . $user_name . ' Bought Package  - ' . $package_name;
        // Package Notification//
            DB::table('notifications')->insert(
                ['notification_to_user_id' => $user_id, 'notification_text' => $transaction_remarks, 'notification_is_read' => '0', 'created_date' => $created_date]
            );

            $bids_response = DB::table('bids')->insert(
                ['user_id' => $user_id, 'user_type' => 'influencer', 'no_of_bids' => $number_of_bids, 'total_bid' => $number_of_bids, 'created_date' => $created_date]
            );
            /* $commission_super_admin = DB::update('update jobwallet set wallet_amount = wallet_amount + '.$adminCommission.'  where user_id = ?', [1]);*/

             $user_update_package = \DB::table('users') ->where('id', $user_id) ->limit(1) ->update( [ 'package_id' => $package_id]); 

            $message = "";
            if ($payment_status=='success') {
                $message = 'Success Response DB Transactions Done';
                return view('home.inc.payu_money_failure', ['message' => $message, 'mihpayid' => $mihpayid]);
            } else {
                $message = 'DB Transactions Fail';
                return view('home.inc.payu_money_failure', ['message' => $message, 'mihpayid' => $mihpayid]);
            }

        } else {
            $message = 'DB Transactions Fail';
            return view('home.inc.payu_money_failure', ['message' => $message, 'mihpayid' => $mihpayid]);
        }

    }

    public function buyfreepackage(Request $request)
    {

        $package_id = decrypt($request->input('package_id'));
        //$package_id = ($request->input('package_id'));

        $user_id      = Auth::id();
        $created_date = date('Y-m-d H:i:s');

        // FREE PACKAGE CHECK FOR THE USER

        $free_package = DB::table('packagepayments')
        ->select('packagepayments.*')
        ->where('packagepayments.transaction_type', 'free_package')
        ->where('packagepayments.user_id', $user_id)
        ->first();

        if (!empty($free_package)) {
            echo json_encode(2);
            return false;
        }

        // CHECK USER ROLE

        $user_information = DB::table('users')
        ->select('users.*')
        ->where('users.id', $user_id)
        ->first();

        $user_role = $user_information->user_type_id;
        $user_name = $user_information->name;

        if ($user_role == 2) {
            $package_info = DB::table('packages_influencer')
            ->select('packages_influencer.*')
            ->where('packages_influencer.id', $package_id)
            ->first();
            $package_name = $package_info->name;
            $package_amount = $package_info->price;
            $bids           = $package_info->no_of_bids;
            $user_type      = 'influencer';
            
        } else {

            $package_info = DB::table('packages')
            ->select('packages.*')
            ->where('packages.id', $package_id)
            ->first();

            $package_name = $package_info->name;
            $package_amount = $package_info->price;
            $bids           = $package_info->no_of_contacts;
            $user_type      = 'employer';
            

        }

        //PACKAGE INFORMATION
       

        $package_payment_id = DB::table('packagepayments')->insertGetId(
            ['user_id' => $user_id, 'package_id' => $package_id, 'txnid' => '', 'product_amount' => $package_amount, 'payment_status' => 'success', 'created_date' => $created_date, 'mihpayid' => '', 'transaction_type' => 'free_package']
        );

        if ($package_payment_id) {

            DB::update('update jobusers set package_id =  ' . $package_id . '  where id = ?', [$user_id]);

            // TRANSACTION HISTORY DB QUERY
            $transaction_remarks = 'User ' . $user_name . ' Bought Package  - ' . $package_name;

            $transaction_history_response = DB::table('transactionhistory')->insert(
                ['package_payment_id' => $package_payment_id, 'user_id' => $user_id, 'amount' => $package_amount, 'transaction_type' => 'debit', 'remarks' => $transaction_remarks, 'package_id' => $package_id, 'created_date' => $created_date]
            );

            // Package Notification//
            DB::table('notifications')->insert(
                ['notification_to_user_id' => $user_id, 'notification_text' => $transaction_remarks, 'notification_is_read' => '0', 'created_date' => $created_date]
            );
            // Package Notification//

            if ($transaction_history_response) {

                $bids_response = DB::table('bids')->insert(
                    ['user_id' => $user_id, 'user_type' => $user_type, 'no_of_bids' => $bids,'total_bid' => $bids, 'created_date' => $created_date]
                );

            }

            echo json_encode(1);

        }

    }

    public function payumoney_paymentFailure(Request $request)
    {

        if (empty($request->input('udf1'))) {
            return Redirect::to(url('/'));
        }

        $user_id        = $request->input('udf1');
        $package_id     = $request->input('udf2');
        $txnid          = $request->input('txnid');
        $product_amount = $request->input('amount');
        $payment_status = $request->input('status');
        $firstname      = $request->input('firstname');
        $mihpayid       = $request->input('mihpayid');

        $created_date = date('Y-m-d H:i:s');

        // PACKAGE PAYMENT DB QUERY
        $package_payment_id = DB::table('packagepayments')->insertGetId(
            ['user_id' => $user_id, 'package_id' => $package_id, 'txnid' => $txnid, 'product_amount' => $product_amount, 'payment_status' => $payment_status, 'created_date' => $created_date, 'mihpayid' => $mihpayid, 'transaction_type' => '']
        );

        if ($package_payment_id) 
        {

            // TRANSACTION HISTORY DB QUERY
            // $transaction_remarks = 'User ' . $firstname . ' Bought Package ID - ' . $package_id;

            // $transaction_history_response = DB::table('transactionhistory')->insert(

            //     ['package_payment_id' => $package_payment_id, 'user_id' => $user_id, 'amount' => $product_amount, 'transaction_type' => 'debit', 'remarks' => $transaction_remarks, 'package_id' => $package_id, 'created_date' => $created_date]

            // );

            // GETTING NO OF BIDS FROM PACKAGE //

            if ($request->input('udf5') == 'employer') {

                $package_info = DB::table('packages')
                ->select('packages.no_of_contacts')
                ->where('packages.id', $package_id)
                ->get();

                $number_of_bids = $package_info[0]->no_of_contacts;

            } else {

                $package_info = DB::table('packages_influencer')
                ->select('packages_influencer.no_of_bids')
                ->where('packages_influencer.id', $package_id)
                ->get();

                $number_of_bids = $package_info[0]->no_of_bids;
            }

            ///SELECT EXISTING BIDS OF USER

            $user_bids = DB::table('bids')
            ->select('bids.no_of_bids')
            ->where('bids.user_id', $user_id)
            ->first();

            // if (!empty($user_bids->no_of_bids)) {

            //     $bids_response = DB::update('update jobbids set no_of_bids = no_of_bids + ' . $user_bids->no_of_bids . '  where user_id = ?', [$user_id]);

            // } else {

            //     $bids_response = DB::table('bids')->insert(
            //         ['user_id' => $user_id, 'user_type' => 'influencer', 'no_of_bids' => $number_of_bids, 'created_date' => $created_date]
            //     );

            // }

            ///SELECT EXISTING BIDS OF USER
            //code changed 11.12.2020

            $bids_response = 1;
            $message = "";

            $message = 'DB Transactions Fail';
            return view('home.inc.payu_money_failure', ['message' => $message, 'mihpayid' => $mihpayid]);
            // if ($bids_response) {

            //     $message = 'Success Response DB Transactions Done';
            //     return view('home.inc.payu_money_failure', ['message' => $message, 'mihpayid' => $mihpayid]);

            // } else {

            //     $message = 'DB Transactions Fail';
            //     return view('home.inc.payu_money_failure', ['message' => $message, 'mihpayid' => $mihpayid]);
            // }

        } else {
            $message = 'DB Transactions Fail';
            return view('home.inc.payu_money_failure', ['message' => $message, 'mihpayid' => $mihpayid]);
        }

    }

    public function paymentsuccesswallet(Request $request)
    {

        $firstname    = $request->input('firstname');
        $amount       = $request->input('amount');
        $user_id      = $request->input('udf1');
        $created_date = date('Y-m-d H:i:s');

        // TRANSACTION HISTORY DB QUERY
        $transaction_remarks = 'User ' . $firstname . ' Added Rs. ' . $amount . ' in the wallet.';

        $transaction_history_response = DB::table('transactionhistory')->insert(
            ['package_payment_id' => '', 'user_id' => $user_id, 'amount' => $amount, 'transaction_type' => 'credit', 'remarks' => $transaction_remarks, 'package_id' => '', 'created_date' => $created_date]
        );

        if ($transaction_history_response) {
            DB::update('update jobwallet set wallet_amount = wallet_amount + ' . $amount . '  where user_id = ?', [$user_id]);

        }

        return redirect('/account/wallet')->with('errormessage', 'Added Succesfully to wallet.');

    }

    public function paymentfailurewallet(Request $request)
    {

        $firstname    = $request->input('firstname');
        $amount       = $request->input('amount');
        $user_id      = $request->input('udf1');
        $created_date = date('Y-m-d H:i:s');

        // TRANSACTION HISTORY DB QUERY
        $transaction_remarks = 'Transaction failed {!! \App\Helpers\Number::money($amount) !!}';

        $transaction_history_response = DB::table('transactionhistory')->insert(
            ['package_payment_id' => '', 'user_id' => $user_id, 'amount' => $amount, 'transaction_type' => 'failed', 'remarks' => $transaction_remarks, 'package_id' => '', 'created_date' => $created_date]
        );

        /*if ($transaction_history_response) {
            DB::update('update jobwallet set wallet_amount = wallet_amount + ' . $amount . '  where user_id = ?', [$user_id]);

        }*/

        return redirect('/account/wallet')->with('errormessage', 'Payment Failed!');

    }
    public function sendmessage_influencer($postId = null, SendMessageInfluencerRequest $request)
    {

       // die("rahul gurdaspuria!!");

        $user_id = Auth::id();

        // CHECK EMPLOYER BIDS

        $employer_bids = DB::table('bids')
        ->select('bids.no_of_bids')
        ->where('bids.user_id', $user_id)
        ->first();

        if (empty($employer_bids->no_of_bids) || $employer_bids->no_of_bids < 1) {

            return redirect()->back()->with('errormessage', 'Buy package to contact influencer.');

        }

        // INLFUENCER INFORMATION

        $influencer_id = decrypt($request->input('influencer_id'));

        $influencer_information = DB::table('users')
        ->select('users.*')
        ->where('users.id', $influencer_id)
        ->first();

        $influencer_name = $influencer_information->name;

        // CREATING NEW POST FOR CONTACT INFLUENCER

        $created_at = date('Y-m-d H:i:s');

        $job_title = 'Job For ' . $influencer_name;

        $last_post = DB::table('posts')
        ->select('posts.*')
        ->where('posts.user_id', $user_id)
        ->where('posts.archived', 0)
        ->orderBy('id', 'DESC')->first();

        $post_id = DB::table('posts')->insertGetId(
            ['country_code' => $last_post->country_code, 'company_id' => $last_post->company_id, 'user_id' => $user_id, 'category_id' => $last_post->category_id, 'post_type_id' => $last_post->post_type_id, 'title' => $job_title, 'city_id' => $last_post->city_id, 'lon' => $last_post->lon, 'lat' => $last_post->lat, 'ip_addr' => $last_post->ip_addr, 'verified_email' => $last_post->verified_email, 'verified_phone' => $last_post->verified_phone]
        );

        $post = DB::table('posts')
        ->select('posts.*')
        ->where('posts.id', $post_id)
        ->first();

        $message = new Message();
        $input   = $request->only($message->getFillable());
        foreach ($input as $key => $value) {
            $message->{$key} = $value;
        }

        $message->post_id                 = $post->id;
        $message->message_influencer_flag = 1;
        $message->from_user_id            = auth()->check() ? auth()->user()->id : 0;
        $message->to_user_id              = ($request->input('to_user_id'));
        $message->to_name                 = ($request->input('to_name'));
        $message->to_email                = ($request->input('to_email'));
        $message->to_phone                = ($request->input('to_phone'));
        $message->subject                 = $job_title;
        // $message->subject = $post->title;

        $message->message = $request->input('message')
        . '<br><br>'
        . t('Related to the ad')
        . ': <a href="' . UrlGen::post($post) . '">' . t('Click here to see') . '</a>';

        $message->save();

        DB::update('update jobbids set no_of_bids = no_of_bids - 1  where user_id = ? and user_type = ?', [$user_id, 'employer']);

        return redirect()->back()->with('message', 'Message sent to the Influencer.');

    }

    public function check_influencer_packages()
    {
        $user_id = Auth::id();

        /*$influencer_package_check = DB::table('packagepayments')->select()->where('packagepayments.user_id', $user_id)->where('packagepayments.payment_status', 'success')->get();

        if (count($influencer_package_check) == 0) {

            echo json_encode(2);
        } else {
            echo json_encode(1);
        }*/
        $influencer_package_check = DB::table('users')->select()->where('users.id',$user_id)->first();
        //echo "<pre>";print_r($influencer_package_check);die;
        $package_id=$influencer_package_check->package_id;
        if ($package_id !='') {

            echo json_encode(1);
        } else {
            echo json_encode(2);
        }

    }

    public function demo_page(Request $request)
    {
        $data = [];
        return view('home.inc.demo_page', $data);
    }
    public function demo_page1(Request $request)
    {
        $data = [];
        return view('home.inc.demo_page1', $data);
    }
    // buy myrate
    public function pay_u_money_myrate(Request $request)
    {

        $base_url = url('/');
        // PAY U MONEY CREDENTIALS

        $PAYU_MONEY_MERCHANT_KEY = PAY_U_MONEY_MERCHANT_KEY;
        $PAYU_MONEY_SALT         =PAY_U_MONEY_MERCHANT_SALT;

        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

        $surl = $base_url . '/payumoney_payment_myrate_Success';
        $furl = $base_url . '/payumoney_payment_myrate_failure';

        // PAY U MONEY CREDENTIALS

        $logged_in_userid = Auth::id();

        $user_name      = '';
        $user_email     = '';
        $user_phone     = '';
        $product_amount = '';
        $productinfo    = '';

        if (Auth::id()) {
            $user_info = DB::table('users')
            ->select('users.*')
            ->where('users.id', $logged_in_userid)
            ->get();

            $user_name  = $user_info[0]->name;
            $user_email = $user_info[0]->email;
            $user_phone = $user_info[0]->phone;

        }

        $influencer_package_id = decrypt($request->input('i_package_id'));
        $package_type          = decrypt($request->input('package_type'));

        if ($influencer_package_id) {

            $package_info = DB::table('myrate')
            ->select('myrate.*')
            ->where('myrate.id', $influencer_package_id)
            ->first();
            if ($package_type == 'basic') {
                $product_amount = $package_info->basic_package_price;
                $productinfo    = $package_info->basic_package_title;

            }
            if ($package_type == 'standard') {
                $product_amount = $package_info->standard_package_price;
                $productinfo    = $package_info->standard_package_title;

            }
            if ($package_type == 'premium') {
                $product_amount = $package_info->premium_package_price;
                $productinfo    = $package_info->premium_package_title;

            }

        }

        $udf1 = $logged_in_userid;
        $udf2 = $package_info->id;
        $udf3 = $package_type;
        $udf4 = $package_info->user_id;
        $udf5 = $package_info->id;

        $hashstring = $PAYU_MONEY_MERCHANT_KEY . '|' . $txnid . '|' . $product_amount . '|' . $productinfo . '|' . $user_name . '|' . $user_email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $PAYU_MONEY_SALT;

        $hash = strtolower(hash('sha512', $hashstring));

        return view('home.inc.pay_u_money_myrateby', ['PAYU_MONYEY_MERCHANT_KEY' => $PAYU_MONEY_MERCHANT_KEY, 'txnid' => $txnid, 'product_amount' => $product_amount, 'productinfo' => $productinfo, 'user_name' => $user_name, 'user_email' => $user_email, 'udf1' => $udf1, 'udf2' => $udf2, 'udf3' => $udf3, 'udf4' => $udf4, 'udf5' => $udf5, 'PAYU_MONEY_SALT' => $PAYU_MONEY_SALT, 'hash' => $hash, 'user_phone' => $user_phone, 'surl' => $surl, 'furl' => $furl]);

    }

    public function influencerRatePackages(Request $request)
    {

        $rate_package_id = decrypt($request->input('i_package_id'));

        $loggedin_userid = Auth::id();

        $package_type = decrypt($request->input('package_type'));

        $package_info = DB::table('myrate')->select('*')->where('myrate.id', $rate_package_id)->first();

        if ($package_type == 'basic') {

            $package_price   = $package_info->basic_package_price;
            $package_title   = $package_info->basic_package_title;
            $rate_package_id = $package_info->id;

        } elseif ($package_type == 'standard') {
            $package_price   = $package_info->basic_package_price;
            $package_title   = $package_info->standard_package_title;
            $rate_package_id = $package_info->id;

        } elseif ($package_type == 'premium') {
            $package_price   = $package_info->basic_package_price;
            $package_title   = $package_info->premium_package_title;
            $rate_package_id = $package_info->id;
        }
        $influencer_id=$package_info->user_id;

        $user_info = DB::table('users')->select('user_type_id')->where('users.id', $loggedin_userid)->first();

        if ($user_info->user_type_id == 2) {

            return redirect()->back()->with('RateMessageError', 'Only Employer can buy this package.');

        }

        $user_wallet = DB::table('wallet')->select('wallet_amount')->where('wallet.user_id', $loggedin_userid)->first();

        if ($user_wallet->wallet_amount < $package_price) {

            return redirect()->back()->with('RateMessageError', 'Please recharge your wallet.You have ' . \App\Helpers\Number::money($user_wallet->wallet_amount) . '');
        } else {

            // DEDUCT WALLET AMOUNT FROM EMPLOYER ACCOUNT //
            $response = DB::update('update jobwallet set wallet_amount = wallet_amount - "' . $package_price . '" where user_id = ?', [$loggedin_userid]);

            $responseOne = DB::update('update jobwallet set blocked_amount = blocked_amount + "' . $package_price . '" where user_id = ?', [$loggedin_userid]);
            // DEDUCT WALLET AMOUNT FROM EMPLOYER ACCOUNT //

            if ($response && $responseOne) {

                $created_date = date('Y-m-d H:i:s');

                $transaction_history_response = DB::table('transactionhistory')->insert(
                    ['package_payment_id' => '', 'user_id' => $loggedin_userid,'influencer_id' => $influencer_id, 'amount' => $package_price, 'transaction_type' => 'debit', 'remarks' => 'For Package ' . $package_title . '','package_id' => $rate_package_id, 'created_date' => $created_date]
                );

                if ($transaction_history_response) {

                    /////// MESSAGE TO INFLUENCER TO INITIATE CONVERSATION

                    // INLFUENCER INFORMATION

                    $influencer_id = decrypt($request->input('influencer_id'));

                    $influencer_information = DB::table('users')
                    ->select('users.*')
                    ->where('users.id', $influencer_id)
                    ->first();

                    $influencer_name = $influencer_information->name;

                    // CREATING NEW POST FOR CONTACT INFLUENCER

                    $created_at = date('Y-m-d H:i:s');

                    $job_title = 'Job For ' . $influencer_name;

                    $last_post = DB::table('posts')
                    ->select('posts.*')
                    ->where('posts.user_id', $loggedin_userid)
                    ->where('posts.archived', 0)
                    ->orderBy('id', 'DESC')->first();

                    $post_id = DB::table('posts')->insertGetId(
                        ['country_code' => $last_post->country_code, 'company_id' => $last_post->company_id, 'user_id' => $loggedin_userid, 'category_id' => $last_post->category_id, 'post_type_id' => $last_post->post_type_id, 'title' => $job_title, 'city_id' => $last_post->city_id, 'lon' => $last_post->lon, 'lat' => $last_post->lat, 'ip_addr' => $last_post->ip_addr, 'verified_email' => $last_post->verified_email, 'verified_phone' => $last_post->verified_phone]
                    );

                    $post = DB::table('posts')
                    ->select('posts.*')
                    ->where('posts.id', $post_id)
                    ->first();

                    // INFLUENCER INFORMATION
                    $influencer_information = DB::table('users')->select('*')->where('users.id', decrypt($request->input('influencer_id')))->first();

                    // INFLUENCER INFORMATION

                    $message = new Message();
                    $input   = $request->only($message->getFillable());
                    foreach ($input as $key => $value) {
                        $message->{$key} = $value;
                    }

                    $message->post_id                 = $post->id;
                    $message->message_influencer_flag = 1;
                    $message->from_user_id            = auth()->check() ? auth()->user()->id : 0;
                    $message->to_user_id              = decrypt($request->input('influencer_id'));
                    $message->to_name                 = $influencer_information->name;
                    $message->to_email                = $influencer_information->email;
                    $message->to_phone                = $influencer_information->phone;
                    $message->subject                 = $job_title;
                    $message->rate_packages_flag      = '1';
                    $message->rate_packages_id        = $rate_package_id;
                    $message->rate_packages_type      = $package_type;
                    // $message->subject = $post->title;

                    $message->message = 'Message from employer correspoding to your package ' . $package_title . ''
                    . '<br><br>'
                    . t('Related to the ad')
                    . ': <a href="' . UrlGen::post($post) . '">' . t('Click here to see') . '</a>';

                    $message->save();
                    /////// MESSAGE TO INFLUENCER TO INITIATE CONVERSATION

                    return redirect()->back()->with('RateMessageSuccess', 'Your have successfully purchased the package.');

                } else {
                    return redirect()->back()->with('RateMessageError', 'Something want wrong.Please try again later.');
                }

            } else {

                return redirect()->back()->with('RateMessageError', 'Something want wrong.Please try again later.');
            }

        }

    }

    public function reject_rate_request_package_influencer($conversation_id,$jobprojectaward_id)
    {

        $decrypted_conversation_id = decrypt($conversation_id);

        $getConversationInformation = DB::select('select * from jobmessages where id = ' . $decrypted_conversation_id . '');

      /*  echo '<pre>';
        print_r($getConversationInformation);
        die;*/

        // Inserting the rejection message to the employer
        $rejection_message = $getConversationInformation[0]->to_name . " has rejected the package request";
        $message_response  = DB::table('messages')->insert(
            [
                'post_id'                 => $getConversationInformation[0]->post_id,
                'parent_id'               => $getConversationInformation[0]->id,
                'from_user_id'            => $getConversationInformation[0]->to_user_id,
                'from_name'               => $getConversationInformation[0]->to_name,
                'from_email'              => $getConversationInformation[0]->to_email,
                'from_phone'              => $getConversationInformation[0]->to_phone,
                'to_user_id'              => $getConversationInformation[0]->from_user_id,
                'to_name'                 => $getConversationInformation[0]->from_name,
                'to_email'                => $getConversationInformation[0]->from_email,
                'to_phone'                => $getConversationInformation[0]->from_phone,
                'subject'                 => $getConversationInformation[0]->subject,
                'message'                 => $rejection_message,
                'bid_amount'              => $getConversationInformation[0]->bid_amount,
                'project_delievery_days'  => $getConversationInformation[0]->project_delievery_days,
                'filename'                => $getConversationInformation[0]->filename,
                'is_read'                 => 0,
                'deleted_by'              => $getConversationInformation[0]->deleted_by,
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s'),
                'deleted_at'              => $getConversationInformation[0]->deleted_at,
                'message_influencer_flag' => $getConversationInformation[0]->message_influencer_flag,
                'rate_packages_flag'      => $getConversationInformation[0]->rate_packages_flag,
                'rate_packages_id'        => $getConversationInformation[0]->post_id,
                'rate_packages_type'      => $getConversationInformation[0]->rate_packages_type,

            ]
        );
        

        //GETTING RATE PACKAGE INFORMATION //
       /* echo '<pre>';
        print_r($getConversationInformation);
        die;*/
        $rate_package_details = DB::table('myrate')
        ->where('myrate.id', $getConversationInformation[0]->rate_packages_id)
        ->select('myrate.*')
        ->first();

        if ($getConversationInformation[0]->rate_packages_type == 'basic') {
            $package_id    = $rate_package_details->id;
            $package_price = $rate_package_details->basic_package_price;
            $package_title = $rate_package_details->basic_package_title;
        }

        if ($getConversationInformation[0]->rate_packages_type == 'standard') {
            $package_id    = $rate_package_details->id;
            $package_price = $rate_package_details->standard_package_price;
            $package_title = $rate_package_details->standard_package_title;
        }

        if ($getConversationInformation[0]->rate_packages_type == 'premium') {
            $package_id    = $rate_package_details->id;
            $package_price = $rate_package_details->premium_package_price;
            $package_title = $rate_package_details->premium_package_title;
        }

        //GETTING RATE PACKAGE INFORMATION //

        // Inserting the rejection message to the employer
        // $package_price='';
        // Amount Wallet reversal to the Employer//
        $response = DB::update('update jobwallet set wallet_amount = wallet_amount + "' . $package_price . '" where user_id = ?', [$getConversationInformation[0]->from_user_id]);

//         echo 'update jobwallet set wallet_amount = wallet_amount + "' . $package_price . '" where user_id = ?', [$getConversationInformation[0]->from_user_id];


//         echo '\n';
//         echo '\n';
//         echo '\n';
//         echo '\n';


//         echo 'update jobwallet set blocked_amount = blocked_amount - "' . $package_price . '" where user_id = ?', [$getConversationInformation[0]->from_user_id];
// echo '\n';

//         die("WOOOOOOOOOOOOOOOOOOOOOOOOOOOOO");

        $responseOne = DB::update('update jobwallet set blocked_amount = blocked_amount - "' . $package_price . '" where user_id = ?', [$getConversationInformation[0]->from_user_id]);

        // Amount Wallet reversal to the Employer//




        if ($response && $responseOne) {



            $created_date = date('Y-m-d H:i:s');

            $transaction_history_response = DB::table('transactionhistory')->insert(
                ['package_payment_id' => '', 'user_id' => $getConversationInformation[0]->from_user_id, 'amount' => $package_price, 'transaction_type' => 'credit', 'remarks' => 'Amount Reversal for the rejection of ' . $package_title . '', 'package_id' => $package_id, 'created_date' => $created_date]
            );

            //Creating notification

            DB::table('notifications')->insert(
                ['notification_to_user_id' => $getConversationInformation[0]->from_user_id, 'notification_text' => 'Amount Reversal for the rejection of ' . $package_title . '', 'notification_is_read' => '0', 'created_date' => $created_date]
            );

            //Creating notification

            // setting the reject request flag update

            $bids_response = DB::update('update jobmessages set rate_request_rejected_flag = "1"  where id = ?', [$getConversationInformation[0]->id]);
            $influencer_id=$getConversationInformation[0]->to_user_id;
            $employer_id=$getConversationInformation[0]->from_user_id;
            $package_id=$getConversationInformation[0]->rate_packages_id;


            $status_response = DB::update('update jobprojectaward set project_status = "rejected" where influencer_id = ? and employer_id = ? and package_id = ? and jobprojectaward_id = ? ', [$influencer_id,$employer_id,$package_id,$jobprojectaward_id]);
            

            return redirect()->back()->with('RateMessageSuccess', 'Rejected successfully.');
            // setting the reject request flag update

        }

    }

    public function accept_rate_request_package_influencer($conversation_id)
    {

        $decrypted_conversation_id = decrypt($conversation_id);

        $getConversationInformation = DB::select('select * from jobmessages where id = ' . $decrypted_conversation_id . '');

        // Inserting the rejection message to the employer
        $accept_message = $getConversationInformation[0]->to_name . " has accepted the package request";

        $message_response  = DB::table('messages')->insert(
            [
                'post_id'                 => $getConversationInformation[0]->post_id,
                'parent_id'               => $getConversationInformation[0]->id,
                'from_user_id'            => $getConversationInformation[0]->to_user_id,
                'from_name'               => $getConversationInformation[0]->to_name,
                'from_email'              => $getConversationInformation[0]->to_email,
                'from_phone'              => $getConversationInformation[0]->to_phone,
                'to_user_id'              => $getConversationInformation[0]->from_user_id,
                'to_name'                 => $getConversationInformation[0]->from_name,
                'to_email'                => $getConversationInformation[0]->from_email,
                'to_phone'                => $getConversationInformation[0]->from_phone,
                'subject'                 => $getConversationInformation[0]->subject,
                'message'                 => $accept_message,
                'bid_amount'              => $getConversationInformation[0]->bid_amount,
                'project_delievery_days'  => $getConversationInformation[0]->project_delievery_days,
                'filename'                => $getConversationInformation[0]->filename,
                'is_read'                 => 0,
                'deleted_by'              => $getConversationInformation[0]->deleted_by,
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s'),
                'deleted_at'              => $getConversationInformation[0]->deleted_at,
                'message_influencer_flag' => $getConversationInformation[0]->message_influencer_flag,
                'rate_packages_flag'      => $getConversationInformation[0]->rate_packages_flag,
                'rate_packages_id'        => $getConversationInformation[0]->rate_packages_id,
                'rate_packages_type'      => $getConversationInformation[0]->rate_packages_type

            ]
        );
        $package_title='';
        //GETTING RATE PACKAGE INFORMATION //
        $rate_package_details = DB::table('myrate')
        ->where('myrate.id', $getConversationInformation[0]->rate_packages_id)
        ->select('myrate.*')
        ->first();
        $package_price =0;
        if ($getConversationInformation[0]->rate_packages_type == 'basic') {
            $package_id    = $rate_package_details->id;
            $package_price = $rate_package_details->basic_package_price;
            $package_title = $rate_package_details->basic_package_title;
        }

        if ($getConversationInformation[0]->rate_packages_type == 'standard') {
            $package_id    = $rate_package_details->id;
            $package_price = $rate_package_details->standard_package_price;
            $package_title = $rate_package_details->standard_package_title;
        }

        if ($getConversationInformation[0]->rate_packages_type == 'premium') {
            $package_id    = $rate_package_details->id;
            $package_price = $rate_package_details->premium_package_price;
            $package_title = $rate_package_details->premium_package_title;
        }

        $created_date = date('Y-m-d H:i:s');

            //Creating notification

        DB::table('notifications')->insert(
            ['notification_to_user_id' => $getConversationInformation[0]->from_user_id, 'notification_text' => 'Accepted the proposal ' . $package_title . '', 'notification_is_read' => '0', 'created_date' => $created_date]
        );
            //Creating notification
            // setting the reject request flag update

        $bids_response = DB::update('update jobmessages set rate_request_rejected_flag = "1"  where id = ?', [$getConversationInformation[0]->id]);

        DB::update('update jobprojectaward set project_status = "accepted" where post_id = ? ', [$getConversationInformation[0]->post_id]);

        return redirect()->back()->with('RateMessageSuccess', 'Accepted successfully.');
            // setting the reject request flag update



    }
    public function payumoney_payment_myrate_Success(Request $request)
    {

        $user_id               = $request->input('udf1');
        $influencer_package_id = $request->input('udf2');
        $txnid                 = $request->input('txnid');
        $product_amount        = $request->input('amount');
        $payment_status        = $request->input('status');
        $firstname             = $request->input('firstname');
        $mihpayid              = $request->input('mihpayid');
        $pakcageTitle          = $request->input('udf3');
        $influencer_id         = $request->input('udf4');
        $employer_id           = $request->input('udf1');
        

        $created_date = date('Y-m-d H:i:s');

        $payment_response = DB::table('packagepayments')->insert(
            ['user_id' => $user_id, 'package_id' => $influencer_package_id, 'txnid' => $txnid, 'product_amount' => $product_amount, 'payment_status' => $payment_status, 'created_date' => $created_date, 'mihpayid' => $mihpayid, 'transaction_type' => 'debit','project_type'=>'purchased_package','package_type'=>$pakcageTitle]
        );

        if ($payment_response) {

           /* $package_info = DB::table('packages_influencer')
                ->select('packages_influencer.no_of_bids')
                ->where('packages_influencer.id', $influencer_package_id)
                ->get();

            $number_of_bids = $package_info[0]->no_of_bids;

            $bids_response = DB::table('bids')->insert(
                ['user_id' => $user_id, 'user_type' => 'influencer', 'no_of_bids' => $number_of_bids, 'created_date' => $created_date]
            );*/

            $message = "";
           /* if ($bids_response) {
                $message = 'Success Response DB Transactions Done';
                return view('home.inc.payu_money_failure', ['message' => $message, 'mihpayid' => $mihpayid]);*/
                //code for recived projects 

                // Notification//

                $notification_text = $pakcageTitle. ' Package buy of you .&nbsp;<a href="/account/recievedprojects">Project Recived</a>';
                DB::table('notifications')->insert(
                    ['notification_to_user_id' => $influencer_id,'notification_text' => $notification_text,'notification_is_read' => '0', 'created_date' => $created_date] 
                );

        // New Message
                $influencer_information = DB::table('users')->select('*')->where('users.id',$influencer_id)->first();
                $message = new Message();
                $input   = $request->only($message->getFillable());
                foreach ($input as $key => $value) {
                    $message->{$key} = $value;
                }

                $message->post_id      = $influencer_package_id;
                $message->from_user_id = auth()->check() ? auth()->user()->id : 0;
                $message->to_user_id   = $influencer_information->id;
                $message->to_name      = $influencer_information->name;
                $message->to_email     = $influencer_information->email;
                $message->to_phone     = $influencer_information->phone;
                $message->subject      = $pakcageTitle.' Package';
                $message->parent_id      ='0';
                $message->message = 'Your Package Purchased!';

        //$message->filename = $resume->filename;

        // Save
                $message->save();
    //Notification//

        // check employer wallet 
                $employer_wallet = DB::table('wallet')
                ->where('wallet.user_id',$employer_id)
                ->select('wallet.wallet_amount')
                ->first();
        // check employer wallet 

                $commision = $product_amount * COMMISION_EMPLOYER/100;

                $total_required_balance = $commision + $product_amount;



            // Deduct total amount with commision from the employer account

                $employer_wallet = DB::update('update jobwallet set wallet_amount = wallet_amount - '.$total_required_balance.'  where user_id = ?', [$employer_id]);

                $employer_wallet_block_amount = DB::update('update jobwallet set blocked_amount = blocked_amount + '.$total_required_balance.'  where user_id = ?', [$employer_id]);

                if($employer_wallet && $employer_wallet_block_amount)
                {

               // Transaction details for the employer deduction
                    $transaction_remarks = 'Amount '.$total_required_balance.' blocked for the Project Name -  '.$pakcageTitle;

                    $transaction_history_response = DB::table('transactionhistory')->insert(['package_payment_id' => '','user_id' =>  $employer_id,'amount' => $total_required_balance , 'transaction_type' => 'debit','remarks' => $transaction_remarks ,'package_id' => '', 'created_date' => $created_date,'project_type'=>'purchased_package','package_type'=>$pakcageTitle] 
                );




           // Credit commision amount to the SUPER ADMIN WALLET

                    $commission_super_admin = DB::update('update jobwallet set wallet_amount = wallet_amount + '.$commision.'  where user_id = ?', [1]);

                    if($commission_super_admin){

                  // Transaction details for the SUPER ADMIN commision

                        $transaction_remarks = 'Commission Amount '.$commision.' Is Earned '.$pakcageTitle.' Package';

                        $transaction_history_response = DB::table('transactionhistory')->insert(['package_payment_id' => '','user_id' =>  1,'amount' => $total_required_balance , 'transaction_type' => 'credit','remarks' => $transaction_remarks ,'package_id' => '', 'created_date' => $created_date,'project_type'=>'purchased_package','package_type'=>$pakcageTitle]);

                    // Project Award details for the inluencer
                    //jobprojectaward

                        $jobprojectaward_res = DB::table('projectaward')->insert(['employer_id' => $employer_id,'influencer_id' =>  $influencer_id,'bid_amount' => $product_amount , 'post_id' => $influencer_package_id,'is_read' => 0,'project_status' => 'pending','created_date' => $created_date,'conversation_id' =>$message->id,'project_type'=>'purchased_package','package_type'=>$pakcageTitle] 
                    );

                    // Project Award details for the inluencer

                  // $message = 'success_message';
                    }
                }
                //end recived project code
            } else {
                $message = 'DB Transactions Fail';
                return view('home.inc.payu_money_failure', ['message' => $message, 'mihpayid' => $mihpayid]);
            }
            $message = 'DB Transactions Fail';
            return view('home.inc.payu_money_success', ['message' => $message, 'mihpayid' => $mihpayid]);

        /*} else {
            $message = 'DB Transactions Fail';
            return view('home.inc.payu_money_success', ['message' => $message, 'mihpayid' => $mihpayid]);
        }*/

    }

    public function pay_with_wallet_myrate(Request $request)
    {

       $user_id               = Auth::id();
       if(empty($user_id)){
         Session::flash('RateMessageError', 'Please do login first!'); 
         return Redirect::back();
     }
     $pakcageTitle          = decrypt($request->input('package_type'));

     if (Auth::id()) {
        $user_info = DB::table('users')
        ->select('users.*')
        ->where('users.id', $user_id)
        ->get();

        $user_name  = $user_info[0]->name;
        $user_email = $user_info[0]->email;
        $user_phone = $user_info[0]->phone;
        if($user_info[0]->package_id!=''){
        $user_pcakage_id = $user_info[0]->package_id;
        }else{
        $user_pcakage_id ='1';
        }
        

    }
    $influencer_package_id = decrypt($request->input('i_package_id'));
    if ($influencer_package_id) {

        $package_info = DB::table('myrate')
        ->select('myrate.*')
        ->where('myrate.id',$influencer_package_id)
        ->first();
        if ($pakcageTitle == 'basic') {
            $product_amount = $package_info->basic_package_price;
            $productinfo    = $package_info->basic_package_title;
            $packageID    = $package_info->id;

        }
        if ($pakcageTitle == 'standard') {
            $product_amount = $package_info->standard_package_price;
            $productinfo    = $package_info->standard_package_title;
            $packageID    = $package_info->id;

        }
        if ($pakcageTitle == 'premium') {
            $product_amount = $package_info->premium_package_price;
            $productinfo    = $package_info->premium_package_title;
            $packageID    = $package_info->id;

        }

    }
    $userWallet= \App\Helpers\UrlGen::get_user_wallet(auth()->user()->id);
    $commision= \App\Helpers\UrlGen::get_employer_packageinfo($user_pcakage_id);
   /* echo $commision;
    die('ssssssss');*/
    $total_required_balance = $commision + $product_amount;

    if($total_required_balance > $userWallet){

        $url=lurl('account/wallet');
        $message='You may have insufficient wallet balance.Please add money in a wallet to buy this package </br> <a class="btn btn-success" href="'.$url.'"> Add Money </a>';
        return Redirect::back()->withErrors([$message]);

    }
    // Deduct total amount with commision from the employer account
    $created_date = date('Y-m-d H:i:s');


    $txnid                 = '0';
    $product_amount        = $product_amount;
    $payment_status        = '';
    $firstname             = $user_name;
    $mihpayid              = '';

    $influencer_id         = $package_info->user_id;
    $employer_id           = $user_id;




    $payment_response = DB::table('packagepayments')->insert(
        ['user_id' => $user_id, 'package_id' => $influencer_package_id, 'txnid' => $txnid, 'product_amount' => $product_amount, 'payment_status' => $payment_status, 'created_date' => $created_date, 'mihpayid' => $mihpayid, 'transaction_type' => 'debit','project_type'=>'purchased_package','package_type'=>$pakcageTitle]
    );

    if($payment_response) {


        $message = "";


        $notification_text = $pakcageTitle. ' Package buy of you .&nbsp;<a href="/account/recievedprojects">Project Recieved</a>';
        DB::table('notifications')->insert(
            ['notification_to_user_id' => $influencer_id,'notification_text' => $notification_text,'notification_is_read' => '0', 'created_date' => $created_date] 
        );
        //genrate new post
        $last_post = DB::table('posts')
        ->select('posts.*')
        ->where('posts.user_id', $user_id)
        ->where('posts.archived',0)
        ->orderBy('id', 'DESC')->first();
      /* echo '<pre>';
        print_r($last_post);
        die;*/

        if(!empty($last_post)){

            $post_id = DB::table('posts')->insertGetId(
                ['country_code' => $last_post->country_code, 'company_id' => $last_post->company_id, 'user_id' => $user_id, 'category_id' => $last_post->category_id, 'post_type_id' => $last_post->post_type_id, 'title' => $pakcageTitle, 'city_id' => $last_post->city_id, 'lon' => $last_post->lon, 'lat' => $last_post->lat, 'ip_addr' => $last_post->ip_addr, 'verified_email' => $last_post->verified_email, 'verified_phone' => $last_post->verified_phone,'reviewed' =>'1','dont_show_flag' =>'1']
            );
        }else{


            $post_id = DB::table('posts')->insertGetId(
                ['country_code' => 'IN', 'company_id' => '8', 'user_id' => $user_id, 'category_id' => '13', 'post_type_id' => '4', 'title' => $pakcageTitle, 'city_id' => '', 'lon' => '', 'lat' => '', 'ip_addr' => '', 'verified_email' => '1', 'verified_phone' => '1','reviewed' =>'1','dont_show_flag' =>'1']
            );
        }

        //end new post

        // New Message
        $influencer_information = DB::table('users')->select('*')->where('users.id',$influencer_id)->first();
         $employer_information = DB::table('users')->select('*')->where('users.id',auth()->user()->id)->first();
        $message = new Message();
        $input   = $request->only($message->getFillable());
        foreach ($input as $key => $value) {
            $message->{$key} = $value;
        }

        $message->post_id      = $post_id;
        $message->from_user_id = auth()->check() ? auth()->user()->id : 0;
        $message->from_name   = $employer_information->name;
        $message->from_email   = $employer_information->email;
        $message->from_phone   = $employer_information->phone;
        $message->to_user_id   = $influencer_information->id;
        $message->to_name      = $influencer_information->name;
        $message->to_email     = $influencer_information->email;
        $message->to_phone     = $influencer_information->phone;
        $message->subject      = $pakcageTitle.' Package';
        $message->parent_id      ='0';
        $message->message = 'Your Package Purchased!';
        $message->rate_packages_flag = 1;
        $message->rate_packages_id = $influencer_package_id;
        $message->rate_packages_type = $pakcageTitle;

        //$message->filename = $resume->filename;

        // Save
        $message->save();
    //Notification//

        // check employer wallet 
        $employer_wallet = DB::table('wallet')
        ->where('wallet.user_id',$employer_id)
        ->select('wallet.wallet_amount')
        ->first();
        // check employer wallet 

        



        // Deduct total amount with commision from the employer account

        $employer_wallet = DB::update('update jobwallet set wallet_amount = wallet_amount - '.$total_required_balance.'  where user_id = ?', [$employer_id]);

        $employer_wallet_block_amount = DB::update('update jobwallet set blocked_amount = blocked_amount + '.$total_required_balance.'  where user_id = ?', [$employer_id]);

        if($employer_wallet && $employer_wallet_block_amount)
        {

            // Transaction details for the employer deduction
            $transaction_remarks = $employer_information->name.' bought '.$pakcageTitle.' Package Price '.$total_required_balance.' of '.$influencer_information->name;

            $transaction_history_response = DB::table('transactionhistory')->insert(['package_payment_id' => '','user_id' =>  $employer_id,'amount' => $total_required_balance , 'transaction_type' => 'debit','remarks' => $transaction_remarks ,'package_id' => '', 'created_date' => $created_date,'project_type'=>'purchased_package','package_type'=>$pakcageTitle] 
        );

           // Credit commision amount to the SUPER ADMIN WALLET

            $commission_super_admin = DB::update('update jobwallet set wallet_amount = wallet_amount + '.$commision.'  where user_id = ?', [1]);

            if($commission_super_admin)
            {

            // Transaction details for the SUPER ADMIN commision

                $transaction_remarks = 'Commission Amount '.$commision.' Is Earned From '.$pakcageTitle. ' Package ';

                $transaction_history_response = DB::table('transactionhistory')->insert(['package_payment_id' => '','user_id' =>  1,'amount' => $total_required_balance , 'transaction_type' => 'credit','remarks' => $transaction_remarks ,'package_id' => $influencer_package_id, 'created_date' => $created_date,'project_type'=>'purchased_package','package_type'=>$pakcageTitle]);

            // Project Award details for the inluencer
                    //jobprojectaward

                $jobprojectaward_res = DB::table('projectaward')->insert(['employer_id' => $employer_id,'influencer_id' =>  $influencer_id,'bid_amount' => $product_amount , 'post_id' => $post_id,'is_read' => 0,'project_status' => 'pending','created_date' => $created_date,'conversation_id' =>$message->id,'project_type'=>'purchased_package','package_type'=>$pakcageTitle ,'package_id'=>$influencer_package_id] 
            );

            // Project Award details for the inluencer

                  // $message = 'success_message';
                Session::flash('RateMessageSuccess', 'You purchased successfully!'); 
                return Redirect::back();
            }
        }
                //end recived project code
    } else {
        $message = 'DB Transactions Fail';
        Session::flash('RateMessageError', 'DB Transactions Fail!'); 
        return Redirect::back();
    }
    $message = 'DB Transactions Fail';

    Session::flash('RateMessageError', 'DB Transactions Fail!'); 
    return Redirect::back();

}
public function pay_u_money_post_packages(Request $request)
{
    $base_url = url('/');
        // PAY U MONEY CREDENTIALS
        /*$PAYU_MONEY_MERCHANT_KEY = "gtKFFx";
        $PAYU_MONEY_SALT         = "eCwWELxi";*/
        $PAYU_MONEY_MERCHANT_KEY = PAY_U_MONEY_MERCHANT_KEY;
        $PAYU_MONEY_SALT         =PAY_U_MONEY_MERCHANT_SALT;

        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

        $surl = $base_url . '/paymentsuccess';
        $furl = $base_url . '/paymentfailure';

        // PAY U MONEY CREDENTIALS

        $logged_in_userid = Auth::id();

        $user_name      = '';
        $user_email     = '';
        $user_phone     = '';
        $product_amount = '';
        $productinfo    = '';

        if (Auth::id()) {
            $user_info = DB::table('users')
            ->select('users.*')
            ->where('users.id', $logged_in_userid)
            ->get();

            $user_name  = $user_info[0]->name;
            $user_email = $user_info[0]->email;
            $user_phone = $user_info[0]->phone;

        }

        $employer_package_id =$request->input('i_package_id');

        if ($employer_package_id) {

            $package_info = DB::table('packages')
            ->select('packages.*')
            ->where('packages.id', $employer_package_id)
            ->get();

            $product_amount = $package_info[0]->price;
            $productinfo    = $package_info[0]->name;
        }

        $udf1 = $logged_in_userid;
        $udf2 = $package_info[0]->id;
        $udf3 = $package_info[0]->id;
        $udf4 = $package_info[0]->id;
        $udf5 = 'employer';

        $hashstring = $PAYU_MONEY_MERCHANT_KEY . '|' . $txnid . '|' . $product_amount . '|' . $productinfo . '|' . $user_name . '|' . $user_email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $PAYU_MONEY_SALT;

        $hash = strtolower(hash('sha512', $hashstring));

        return view('home.inc.pay_u_money_standby', ['PAYU_MONYEY_MERCHANT_KEY' => $PAYU_MONEY_MERCHANT_KEY, 'txnid' => $txnid, 'product_amount' => $product_amount, 'productinfo' => $productinfo, 'user_name' => $user_name, 'user_email' => $user_email, 'udf1' => $udf1, 'udf2' => $udf2, 'udf3' => $udf3, 'udf4' => $udf4, 'udf5' => $udf5, 'PAYU_MONEY_SALT' => $PAYU_MONEY_SALT, 'hash' => $hash, 'user_phone' => $user_phone, 'surl' => $surl, 'furl' => $furl]);

    }


}
