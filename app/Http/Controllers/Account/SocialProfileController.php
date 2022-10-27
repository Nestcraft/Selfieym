<?php
namespace App\Http\Controllers\Account;

use App\Helpers\Localization\Country as CountryLocalization;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Models\Category;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\User;
use App\Models\UserType;
use Creativeorange\Gravatar\Facades\Gravatar;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Session;
use Torann\LaravelMetaTags\Facades\MetaTag;

class SocialProfileController extends AccountBaseController
{
	public function index()
	{
		$data               = [];
		$data['countries']  = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
		$data['userTypes']  = UserType::all();
		$data['userPhoto']  = (!empty(auth()->user()->email)) ? Gravatar::fallback(url('images/user.jpg'))->get(auth()->user()->email) : null;
		$cacheId            = 'categories.parentId.0.with.children' . config('app.locale');
		$data['categories'] = Cache::remember($cacheId, $this->cacheExpiration, function () {
			return Category::trans()->where('parent_id', 0)->with([
				'children' => function ($query) {
					$query->trans();
				},
			])->orderBy('lft')->get();
		});
		$data['details'] = DB::table('social')
		->leftJoin('cities', 'cities.id', '=', 'social.city_id')
		->leftJoin('categories', 'categories.id', '=', 'social.category_id')
		->where('social.user_id', auth()->user()->id)
		->select('social.*', 'categories.name as catname', 'cities.name as cityname')
		->first();

        // Mini Stats
		$data['countPostsVisits'] = DB::table('posts')
		->select('user_id', DB::raw('SUM(visits) as total_visits'))
		->where('country_code', config('country.code'))
		->where('user_id', auth()->user()->id)
		->groupBy('user_id')
		->first();
		$data['countPosts'] = Post::currentCountry()
		->where('user_id', auth()->user()->id)
		->count();
		$data['countFavoritePosts'] = SavedPost::whereHas('post', function ($query) {
			$query->currentCountry();
		})->where('user_id', auth()->user()->id)
		->count();
        // Meta Tags
		MetaTag::set('title', t('My account'));
		MetaTag::set('description', t('My account on :app_name', ['app_name' => config('settings.app.app_name')]));

		return view('account.social.details', $data);
	}
	public function edit()
	{
		$data               = [];
		$data['countries']  = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
		$data['userTypes']  = UserType::all();
		$data['userPhoto']  = (!empty(auth()->user()->email)) ? Gravatar::fallback(url('images/user.jpg'))->get(auth()->user()->email) : null;
		$cacheId            = 'categories.parentId.0.with.children' . config('app.locale');
		$data['categories'] = Cache::remember($cacheId, $this->cacheExpiration, function () {
			return Category::trans()->where('parent_id', 0)->with([
				'children' => function ($query) {
					$query->trans();
				},
			])->orderBy('lft')->get();
		});

		$data['details'] = DB::table('social')
		->leftJoin('cities', 'cities.id', '=', 'social.city_id')
		->leftJoin('categories', 'categories.id', '=', 'social.category_id')
		->where('social.user_id', auth()->user()->id)
		->select('social.*', 'categories.name as catname', 'cities.*')
		->first();
        /*    echo '<pre>';
        print_r($data['details']);
        die;*/
        // YOUTUBE URLS
        $data['user_youtube_urls'] = DB::table('user_youtube_urls')
        ->where('user_youtube_urls.user_id', auth()->user()->id)
        ->select('user_youtube_urls.*')
        ->get();
        // YOUTUBE URLS

        // USER PORTFOLIO IMAGES
        $data['user_portfolio_images'] = DB::table('user_portfolio')
        ->where('user_portfolio.user_id', auth()->user()->id)
        ->select('user_portfolio.*')
        ->get();

        // USER PORTFOLIO IMAGES

        // Mini Stats
        $data['countPostsVisits'] = DB::table('posts')
        ->select('user_id', DB::raw('SUM(visits) as total_visits'))
        ->where('country_code', config('country.code'))
        ->where('user_id', auth()->user()->id)
        ->groupBy('user_id')
        ->first();
        $data['countPosts'] = Post::currentCountry()
        ->where('user_id', auth()->user()->id)
        ->count();
        $data['countFavoritePosts'] = SavedPost::whereHas('post', function ($query) {
        	$query->currentCountry();
        })->where('user_id', auth()->user()->id)
        ->count();
        // Meta Tags
        MetaTag::set('title', t('My account'));
        MetaTag::set('description', t('My account on :app_name', ['app_name' => config('settings.app.app_name')]));
        return view('account.social.edit', $data);
    }
    public function create(Request $request)
    {

    	$user_id = $request->user_id;
    	$exist   = DB::table('social')
    	->where('user_id', $user_id)
    	->first();

    	if ($exist !== null) {
    		$this->validate($request, [
    			'skill_expertise' => 'required',
    			'pareent_cityid'  => 'required|numeric|min:0|not_in:0',

    		]);
    	} else {
    		$this->validate($request, [
    			'skill_expertise' => 'required',
    			'city_id'         => 'required|numeric|min:0|not_in:0',

    		]);
    	}

    	$name = '';
    	/******* UPLOAD PROFILE IMAGE CODE (WEBC) *****/
    	if ($request->hasFile('profile_image')) {
    		$image           = $request->file('profile_image');
    		$name            = time() . '.' . $image->getClientOriginalExtension();
    		$destinationPath = public_path('/images/profile_images/');
    		$image->move($destinationPath, $name);
    	}
    	/******* UPLOAD PROFILE IMAGE CODE (WEBC) *****/
    	$db_profile_image = DB::table('social')
    	->leftJoin('cities', 'cities.id', '=', 'social.city_id')
    	->leftJoin('categories', 'categories.id', '=', 'social.category_id')
    	->where('social.user_id', auth()->user()->id)
    	->select('social.profile_image', 'categories.name as catname', 'cities.name as cityname')
    	->first();

    	if ($name == '') {
    		$profile_image_user = $db_profile_image->profile_image;
    	} else {
    		$profile_image_user = $name;
    	}

    	$array = array(
    		'user_id'                         => $request->user_id,
    		'biodata'                         => $request->profile_bio,
    		'category_id'                     => $request->category_id == 0 ? $request->parent_id : $request->category_id,
    		'age'                             => $request->age,
    		'dob'                             => $request->dob,
    		'city_id'                         => $request->city_id == 0 ? $request->pareent_cityid : $request->city_id,
    		'min_fee'                         => $request->min_fee,
    		'facebook_followers'              => $request->facebook_followers . $request->facebook_unit,
    		'twitter_followers'               => $request->twitter_followers . $request->twitter_unit,
    		'youtube_subscribers'             => $request->youtube_suscribers . $request->youtube_unit,
    		'instagram_followers'             => $request->instagram_followers . $request->instagram_unit,
    		'quora_followers'                 => $request->quora_followers . $request->quora_unit,
    		'profile_image'                   => $profile_image_user,
    		'skill_expertise'                 => $request->skill_expertise,
    		'client_base'                     => $request->client_base,
    		'facebook_followers_without_unit' => $request->facebook_followers,
    		'facebook_followers_unit'         => $request->facebook_unit,
    	);
    	if ($request->facebook_url != '') {
    		$array['facebook_url'] = $this->prep_url($request->facebook_url);
    	} else {
    		$array['facebook_url'] = '';
    	}
        //twitter_url
    	if ($request->twitter_url != '') {
    		$array['twitter_url'] = $this->prep_url($request->twitter_url);
    	} else {
    		$array['twitter_url'] = '';
    	}
        //youtube_url
    	if ($request->youtube_url != '') {
    		$array['youtube_url'] = $this->prep_url($request->youtube_url);
    	} else {
    		$array['youtube_url'] = '';
    	}
        //instagram_url
    	if ($request->instagram_url != '') {
    		$array['instagram_url'] = $this->prep_url($request->instagram_url);
    	} else {
    		$array['instagram_url'] = '';
    	}
        //quora_url
    	if ($request->quora_url != '') {
    		$array['quora_url'] = $this->prep_url($request->quora_url);
    	} else {
    		$array['quora_url'] = '';
    	}

    	$exist = DB::table('social')
    	->where('user_id', $request->user_id)
    	->first();

    	if ($exist !== null) {
    		DB::table('social')->where('user_id', $user_id)->update($array);
    	} else {
    		DB::table('social')->insert($array);
    	}
    	return redirect('account/socialprofile');
    }
    public function prep_url($str = '')
    {
    	if ($str === 'http://' or $str === '') {
    		return '';
    	}
    	$url = parse_url($str);
    	if (!$url or !isset($url['scheme'])) {
    		return 'http://' . $str;
    	}
    	return $str;
    }

    // SAVING USER YOUTUBE / PORTFOLIO FUNCTIONS (WEBC)
    public function save_youtube_url(Request $request)
    {
    	$user_id = auth()->user()->id;

    	DB::table('user_youtube_urls')->where('user_id', '=', $user_id)->delete();

    	foreach ($request->all() as $urls) {
    		DB::table('user_youtube_urls')->insert(
    			['youtube_url' => $urls, 'user_id' => $user_id, 'created_at' => date('Y-m-d H:i:s')]
    		);
    	}

    	echo json_encode(1);
    	die;

    }

    public function save_user_portfolio(Request $request)
    {

    	$user_id = auth()->user()->id;

    	DB::table('user_portfolio')->where('user_id', '=', $user_id)->delete();

        // if($request->hasFile('user_portfolio_image')){
    	$success_query_flag = 0;
    	foreach (array_combine($request->input('user_portfolio_title'), $request->input('user_portfolio_image_name')) as $portfolio_title => $portfolio_image) {

    		$query = DB::table('user_portfolio')->insert(
    			['user_id' => $user_id, 'portfolio_title' => $portfolio_title, 'portfolio_image' => $portfolio_image, 'created_at' => date('Y-m-d H:i:s')]
    		);

    		$success_query_flag = 'success';

    	}
        // }

    	if ($success_query_flag == 'success') {

    		echo json_encode(1);
    	} else {
    		echo json_encode(0);
    	}

    }

    public function save_user_portfolio_image(Request $request)
    {

    	if ($request->hasFile('user_portfolio_image') && $this->validate($request, [
    		'user_portfolio_image' => 'required|image|mimes:jpeg,png,jpg',
    	])) {

    		/******* UPLOAD PROFILE IMAGE CODE (WEBC) *****/
    		if ($request->hasFile('user_portfolio_image')) {
    			$image           = $request->file('user_portfolio_image');
    			$name            = rand() . '-portfolio' . '.' . $image->getClientOriginalExtension();
    			$destinationPath = public_path('/images/user_portfolio_images/');
    			$image->move($destinationPath, $name);
    		}
    		/******* UPLOAD PROFILE IMAGE CODE (WEBC) *****/

    		echo json_encode(array('image_name' => $name, 'status' => true));
    	} else {
    		echo json_encode(array('image_name' => '', 'status' => false));
    	}

    	die;

    }

    public function remove_portfolio(Request $request)
    {

    	$inputs = $request->all();

    	DB::table('user_portfolio')->where('jobuser_portfolio_id', '=', $inputs['jobuser_portfolio_id'])->delete();

    	echo json_encode(1);

    }

    public function remove_youtube_url(Request $request)
    {

    	$inputs = $request->all();

    	DB::table('user_youtube_urls')->where('youtube_url_id', '=', $inputs['youtube_url_id'])->delete();

    	echo json_encode(1);

    }

    public function portfolio()
    {

        // USER PORTFOLIO IMAGES
    	$data['user_portfolio_images'] = DB::table('user_portfolio')
    	->where('user_portfolio.user_id', auth()->user()->id)
    	->select('user_portfolio.*')
    	->get();
        // USER PORTFOLIO IMAGES
        // YOUTUBE URLS
    	$data['user_youtube_urls'] = DB::table('user_youtube_urls')
    	->where('user_youtube_urls.user_id', auth()->user()->id)
    	->select('user_youtube_urls.*')
    	->get();
        // YOUTUBE URLS

    	return view('account.social.portfolio', $data);

    }
    public function myrate()
    {

        // USER PORTFOLIO IMAGES
    	$data['details'] = DB::table('myrate')
    	->where('myrate.user_id', auth()->user()->id)
    	->select('*')
    	->first();

        // USER PORTFOLIO IMAGES

    	return view('account.social.myrate', $data);

    }

    public function myrateupdate(Request $request)
    {

    	$user_id = auth()->user()->id;

    	$this->validate($request, [
    		'basic_package_title'   => 'required|max:150',
    		'basic_package_service' => 'required|max:150',
    		'basic_package_price'   => 'required|numeric|min:0|not_in:0',
    	]);

    	$array = array(
    		'user_id' => $user_id,
    		'status'  => '1',

    	);

    	if (!empty($request->basic_package_title)) {
    		$array['basic_package_title'] = $request->basic_package_title;

    	} else {
    		$array['basic_package_title'] = '';
    	}
    	if (!empty($request->basic_package_price)) {
    		$array['basic_package_price'] = $request->basic_package_price;

    	} else {
    		$array['basic_package_price'] = '';
    	}
    	if (!empty($request->basic_package_service)) {
    		$array['basic_package_service'] = $request->basic_package_service;

    	} else {
    		$array['basic_package_service'] = '';
    	}

    	$exist = DB::table('myrate')
    	->where('user_id', $user_id)
    	->first();

    	if ($exist !== null) {
    		$array['updated_at'] = date('Y-m-d H:i:s');
    		DB::table('myrate')->where('user_id', $user_id)->update($array);
    	} else {
    		$array['created_at'] = date('Y-m-d H:i:s');
    		DB::table('myrate')->insert($array);
    	}

    	Session::flash('message', 'Successfully submitted!');

    	return redirect('account/socialprofile/myrate')->with('successmessage', 'Successfully submitted.');
    }
    public function myratepremiumupdate(Request $request)
    {

    	$user_id = auth()->user()->id;

    	$this->validate($request, [
    		'premium_package_title'   => 'required|max:150',
    		'premium_package_service' => 'required|max:150',
    		'premium_package_price'   => 'required|numeric|min:0|not_in:0',
    	]);

    	$array = array(
    		'user_id' => $user_id,
    		'status'  => '1',

    	);

        //premium
    	if (!empty($request->premium_package_title)) {
    		$array['premium_package_title'] = $request->premium_package_title;

    	} else {
    		$array['premium_package_title'] = '';
    	}
    	if (!empty($request->premium_package_price)) {
    		$array['premium_package_price'] = $request->premium_package_price;

    	} else {
    		$array['premium_package_price'] = '';
    	}
    	if (!empty($request->premium_package_service)) {
    		$array['premium_package_service'] = $request->premium_package_service;

    	} else {
    		$array['premium_package_service'] = '';
    	}

    	$exist = DB::table('myrate')
    	->where('user_id', $user_id)
    	->first();

    	if ($exist !== null) {
    		$array['updated_at'] = date('Y-m-d H:i:s');
    		DB::table('myrate')->where('user_id', $user_id)->update($array);
    	} else {
    		$array['created_at'] = date('Y-m-d H:i:s');
    		DB::table('myrate')->insert($array);
    	}

    	Session::flash('message3', 'Successfully submitted!');

    	return redirect('account/socialprofile/myrate')->with('successmessage', 'Successfully submitted.');
    }
    public function myratestandardupdate(Request $request)
    {

    	$user_id = auth()->user()->id;

    	$this->validate($request, [
    		'standard_package_title'   => 'required|max:150',
    		'standard_package_service' => 'required|max:150',
    		'standard_package_price'   => 'required|numeric|min:0|not_in:0',
    	]);

    	$array = array(
    		'user_id' => $user_id,
    		'status'  => '1',

    	);

        //standard
    	if (!empty($request->standard_package_title)) {
    		$array['standard_package_title'] = $request->standard_package_title;

    	} else {
    		$array['standard_package_title'] = '';
    	}
    	if (!empty($request->standard_package_price)) {
    		$array['standard_package_price'] = $request->standard_package_price;

    	} else {
    		$array['standard_package_price'] = '';
    	}
    	if (!empty($request->standard_package_service)) {
    		$array['standard_package_service'] = $request->standard_package_service;

    	} else {
    		$array['standard_package_service'] = '';
    	}

    	$exist = DB::table('myrate')
    	->where('user_id', $user_id)
    	->first();

    	if ($exist !== null) {
    		$array['updated_at'] = date('Y-m-d H:i:s');
    		DB::table('myrate')->where('user_id', $user_id)->update($array);
    	} else {
    		$array['created_at'] = date('Y-m-d H:i:s');
    		DB::table('myrate')->insert($array);
    	}

    	Session::flash('message2', 'Successfully submitted!');

    	return redirect('account/socialprofile/myrate')->with('successmessage', 'Successfully submitted.');
    }

    public function instagram(Request $request)
    {
    	if ($request->isMethod('post')) {
    		$data = $request->all();

    		$id = auth()->user()->id;

    		if ($data['instagram'] == '') {
    			$data['instagram'] = '';
    		}

    		if ($data['facebook'] == '') {
    			$data['facebook'] = '';
    		}

    		if ($data['twitter'] == '') {
    			$data['twitter'] = '';
    		}

    		if ($data['youtube'] == '') {
    			$data['youtube'] = '';
    		}
//            echo "<pre>"; print_r($id); die();

    		$array = array(
    			'user_id'   => $id,
    			'instagram' => $data['instagram'],
    			'facebook'  => $data['facebook'],
    			'twitter'   => $data['twitter'],
    			'youtube'   => $data['youtube'],

    		);

    		$exist = DB::table('sociallink')
    		->where('user_id', $id)
    		->first();

    		if ($exist !== null) {
    			DB::table('sociallink')->where('user_id', $id)->update($array);
    		} else {
    			DB::table('sociallink')->insert($array);
    		}

    		Session::flash('message2', 'Successfully submitted!');
    		return redirect('/account/socialprofile/instagram');

    	}
    	return view('account.social.instagram');
    }
    function new () {
    	$data               = [];
    	$data['countries']  = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
    	$data['userTypes']  = UserType::all();
    	$data['userPhoto']  = (!empty(auth()->user()->email)) ? Gravatar::fallback(url('images/user.jpg'))->get(auth()->user()->email) : null;
    	$cacheId            = 'categories.parentId.0.with.children' . config('app.locale');
    	$data['categories'] = Cache::remember($cacheId, $this->cacheExpiration, function () {
    		return Category::trans()->where('parent_id', 0)->with([
    			'children' => function ($query) {
    				$query->trans();
    			},
    		])->orderBy('lft')->get();
    	});

    	$data['details'] = DB::table('social')
    	->leftJoin('cities', 'cities.id', '=', 'social.city_id')
    	->leftJoin('categories', 'categories.id', '=', 'social.category_id')
    	->where('social.user_id', auth()->user()->id)
    	->select('social.*', 'categories.name as catname', 'cities.*')
    	->first();
        /*    echo '<pre>';
        print_r($data['details']);
        die;*/
        // YOUTUBE URLS
        $data['user_youtube_urls'] = DB::table('user_youtube_urls')
        ->where('user_youtube_urls.user_id', auth()->user()->id)
        ->select('user_youtube_urls.*')
        ->get();
        // YOUTUBE URLS

        // USER PORTFOLIO IMAGES
        $data['user_portfolio_images'] = DB::table('user_portfolio')
        ->where('user_portfolio.user_id', auth()->user()->id)
        ->select('user_portfolio.*')
        ->get();

        // USER PORTFOLIO IMAGES

        // Mini Stats
        $data['countPostsVisits'] = DB::table('posts')
        ->select('user_id', DB::raw('SUM(visits) as total_visits'))
        ->where('country_code', config('country.code'))
        ->where('user_id', auth()->user()->id)
        ->groupBy('user_id')
        ->first();
        $data['countPosts'] = Post::currentCountry()
        ->where('user_id', auth()->user()->id)
        ->count();
        $data['countFavoritePosts'] = SavedPost::whereHas('post', function ($query) {
        	$query->currentCountry();
        })->where('user_id', auth()->user()->id)
        ->count();
        // Meta Tags
        MetaTag::set('title', t('My account'));
        MetaTag::set('description', t('My account on :app_name', ['app_name' => config('settings.app.app_name')]));
        return view('account.social.newedit', $data);
    }
    public function redirectToProvider($provider)
    {

    	return Socialite::driver($provider)->redirect();
    }
    public function handleFacebookCallback()
    {
    	$user = Socialite::driver('facebook')->user();
    	print_r($user);
    	die;

        // $user->token;
    }
    public function redirectToInstagramProvider()
    {
        /*$appId = config('services.instagram.client_id');
        $redirectUri = urlencode(config('services.instagram.redirect'));
        return redirect()->to("https://api.instagram.com/oauth/authorize?app_id={$appId}&redirect_uri={$redirectUri}&response_type=code");*/
        return Socialite::driver('instagram')->redirect();
/*     'client_id'     => '1015148278894711',
'client_secret' => '42eff5c9009da31ca6446a2218abeb73',*/
        /*$curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.instagram.com/oauth/access_token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => array('client_id' => '1015148278894711','client_secret' => '42eff5c9009da31ca6446a2218abeb73','grant_type' => 'authorization_code','redirect_uri' => 'https://selfieym.com/account/socialprofile/callback','code' => '{code}'),
    CURLOPT_HTTPHEADER => array(
    "Content-Type: multipart/form-data; boundary=--------------------------780367731654051340650991"
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    print_r($response);*/

}

public function instagramProviderCallback(Request $request)
{
	die('dddd');
	$code = $request->code;
	if (empty($code)) {
		return redirect()->route('home')->with('error', 'Failed to login with Instagram.');
	}

	$appId       = config('services.instagram.client_id');
	$secret      = config('services.instagram.client_secret');
	$redirectUri = config('services.instagram.redirect');

	$client = new Client();

        // Get access token
	$response = $client->request('POST', 'https://api.instagram.com/oauth/access_token', [
		'form_params' => [
			'app_id'       => $appId,
			'app_secret'   => $secret,
			'grant_type'   => 'authorization_code',
			'redirect_uri' => $redirectUri,
			'code'         => $code,
		],
	]);

	if ($response->getStatusCode() != 200) {
		return redirect()->route('home')->with('error', 'Unauthorized login to Instagram.');
	}

	$content = $response->getBody()->getContents();
	$content = json_decode($content);

	$accessToken = $content->access_token;
	$userId      = $content->user_id;

        // Get user info
	$response = $client->request('GET', "https://graph.instagram.com/me?fields=id,username,account_type&access_token={$accessToken}");

	$content = $response->getBody()->getContents();
	$oAuth   = json_decode($content);

        // Get instagram user name
	$username = $oAuth->username;

        // do your code here
}
public function get_facebook_info()
{
/*$ch = curl_init('https://graph.facebook.com/114190657373445?access_token=EAAEAzzndCMQBAJz37BSZC7aLR9PZAzlxyVgt8aTEuVkMZBfw0nUDZBzhfsDjzpBBIY0HcfG1usXm4gw4dHZBoXKpTxSEYSAsRCT4Y3kcMvg9KoHM87kdHal7Fa7pj08ql5OLbalsL1PXtXwimcLTo1RrirWkhx21w58afKBJqSSDPdmpFo6EYLm7AT5zozmsZD');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
$response = curl_exec($ch);
var_dump($response);
var_dump(json_decode($response));*/
/*$json_url ='https://graph.facebook.com/114190657373445?access_token=282365005727940|b282d5fa06499f422cffd5e8e2aa7167&fields=fan_count';
 *//* $json_url ='https://graph.facebook.com/'.$id.'?access_token='.$appid.'|'.$appsecret.'&fields=fan_count';*/

$json        = file_get_contents($json_url);
$json_output = json_decode($json);
        //Extract the likes count from the JSON object
if ($json_output->fan_count) {
	return $fan_count = $json_output->fan_count;
} else {
	return 0;
}

echo fbLikeCount('coregenie', '___APPID___', '___APPSECRET___');

}

public function twitterinformation(Request $request)
{

	$userID = auth()->user()->id;
	$twitterUsername = $request->input("twitter_username");

	$curl = curl_init();

	$bearerToken = 'AAAAAAAAAAAAAAAAAAAAAMbfcwEAAAAAY6KEUdlFMmMxEQtzcB8P5qmDBKg%3D2L2xNzvJ0nnJGANwy4c5zg4ZosnOOEWZ9aSDGXneMiEwmWDGJ7';

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.twitter.com/2/tweets/search/recent?query=from:'.$twitterUsername.'&max_results=20',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Authorization: Bearer '.$bearerToken.''
		),
	));

	$response = curl_exec($curl);

	if(isset($response))
    {
        $responseArray = json_decode($response,TRUE);

        if(is_array($responseArray))
        {

         DB::delete('delete from jobtwitter_feeds where user_id = ?',[$userID]);

         foreach($responseArray["data"] as $tweetInformation)
         {


            DB::table('twitter_feeds')->insert(
                array(
                    'user_id'      => $userID,
                    'tweet_id'     => $tweetInformation["id"],
                    'tweet_text'   => $tweetInformation["text"],
                    'is_visible'   => 'yes',
                    'created_date' => date("Y-m-d H:i:s")
                )
            );

        }

        echo json_encode(['status' => true,'message' => 'Account Linked Successfully.']);
        die;

    }else{

        echo json_encode(['status' => false,'message' => 'Something is wrong.Please try again later.']);
        die;
    }

}else{

    echo json_encode(['status' => false,'message' => 'Something is wrong.Please try again later.']);
    die;
}
die("twitter");

}

}
