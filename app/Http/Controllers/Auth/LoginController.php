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

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\FrontController;
use App\Http\Requests\LoginRequest;
use App\Events\UserWasLogged;
use App\Models\Permission;
use App\Models\User;
use App\Helpers\Auth\Traits\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Torann\LaravelMetaTags\Facades\MetaTag;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
class LoginController extends FrontController
{
	use AuthenticatesUsers;
	
	/**
	 * Where to redirect users after login / registration.
	 *
	 * @var string
	 */
	// If not logged in redirect to
	protected $loginPath = 'login';
	
	// The maximum number of attempts to allow
	protected $maxAttempts = 5;
	
	// The number of minutes to throttle for
	protected $decayMinutes = 15;
	
	// After you've logged in redirect to
	protected $redirectTo = 'account';
	
	// After you've logged out redirect to
	protected $redirectAfterLogout = '/';
	
	/**
	 * LoginController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware('guest')->except(['except' => 'logout']);
		
		// Set default URLs
		$isFromLoginPage = Str::contains(url()->previous(), '/' . trans('routes.login'));
		$this->loginPath = $isFromLoginPage ? config('app.locale') . '/' . trans('routes.login') : url()->previous();
		$this->redirectTo = $isFromLoginPage ? config('app.locale') . '/account' : url()->previous();
		$this->redirectAfterLogout = config('app.locale');
		
		// Get values from Config
		$this->maxAttempts = (int)config('settings.security.login_max_attempts', $this->maxAttempts);
		$this->decayMinutes = (int)config('settings.security.login_decay_minutes', $this->decayMinutes);
	}
	
	// -------------------------------------------------------
	// Laravel overwrites for loading JobClass views
	// -------------------------------------------------------
	
	/**
	 * Show the application login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showLoginForm()
	{
		// Remembering Login
		if (auth()->viaRemember()) {
			return redirect()->intended($this->redirectTo);
		}
		
		// Meta Tags
		MetaTag::set('title', getMetaTag('title', 'login'));
		MetaTag::set('description', strip_tags(getMetaTag('description', 'login')));
		MetaTag::set('keywords', getMetaTag('keywords', 'login'));
		
		return view('auth.login');
	}
	
	/**
	 * @param LoginRequest $request
	 * @return $this|\Illuminate\Http\RedirectResponse|void
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function login(LoginRequest $request)
	{
		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		if ($this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);
			
			return $this->sendLockoutResponse($request);
		}
		
		// Get the right login field
		$loginField = getLoginField($request->input('login'));
		
		// Get credentials values
		$credentials = [
			$loginField => $request->input('login'),
			'password'  => $request->input('password'),
			'blocked'   => 0,
		];
		if (in_array($loginField, ['email', 'phone'])) {
			$credentials['verified_' . $loginField] = 1;
		} else {
			$credentials['verified_email'] = 1;
			$credentials['verified_phone'] = 1;
		}
		
		// Auth the User
		if (auth()->attempt($credentials)) {
			$user = User::find(auth()->user()->getAuthIdentifier());
			
			// Update last user logged Date
			Event::dispatch(new UserWasLogged(User::find(auth()->user()->id)));

			// CREATING USER WALLET RECORD FOR THE USERS

			$user_id = auth()->user()->id;

			$user_wallet_record = DB::table('wallet')
			->where('wallet.user_id',$user_id)
			->select('wallet.*')
			->get();

			$created_date = date('Y-m-d H:i:s');

			if(count($user_wallet_record) == 0)
			{
				DB::table('wallet')->insert(
					['user_id' =>  $user_id, 'wallet_amount' => '0','created_date' => $created_date]
				);
			}

			
			// Redirect admin users to the Admin panel
			if (auth()->check()) {
				if ($user->hasAllPermissions(Permission::getStaffPermissions())) {
					return redirect(admin_uri());
				}
			}

			

			// Redirect normal users
			if(Session::get('RedirectionFlagInfluencer') != ''){

				return redirect(Session::get('RedirectionFlagInfluencer'));
			}else{
				return redirect('account');
				//return redirect()->intended($this->redirectTo);
			}


			

		}
		
		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);
		
		// Check and retrieve previous URL to show the login error on it.
		if (session()->has('url.intended')) {
			$this->loginPath = session()->get('url.intended');
		}
		
		return redirect($this->loginPath)->withErrors(['error' => trans('auth.failed')])->withInput();
	}
	
	/**
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function logout(Request $request)
	{
		// Get the current Country
		if (session()->has('country_code')) {
			$countryCode = session('country_code');
		}
		
		// Remove all session vars
		$this->guard()->logout();
		$request->session()->flush();
		$request->session()->regenerate();
		
		// Retrieve the current Country
		if (isset($countryCode) && !empty($countryCode)) {
			session(['country_code' => $countryCode]);
		}
		
		$message = t('You have been logged out.') . ' ' . t('See you soon.');
		flash($message)->success();
		
		return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
	}
	public function redirectToInstagramProvider()
	{
		$appId = config('services.instagram.client_id');

		$redirectUri = urlencode(config('services.instagram.redirect'));
		return redirect()->to("https://api.instagram.com/oauth/authorize?app_id={$appId}&redirect_uri={$redirectUri}&scope=user_profile,user_media&response_type=code");
	}

	public function instagramProviderCallback(Request $request)
	{

		$code = $request->code;
		if (empty($code)) return redirect()->route('home')->with('error', 'Failed to login with Instagram.');

		$appId = config('services.instagram.client_id');
		$secret = config('services.instagram.client_secret');
		$redirectUri = config('services.instagram.redirect');

		$client = new Client();

    // Get access token
		$response = $client->request('POST', 'https://api.instagram.com/oauth/access_token', [
			'form_params' => [
				'app_id' => '728276848367720',
				'app_secret' => '424097cb7ffd63f079a670727ce185f0',
				'grant_type' => 'authorization_code',
				'redirect_uri' => $redirectUri,
				'code' => $code,
			]
		]);

		if ($response->getStatusCode() != 200) {
			return redirect()->route('home')->with('error', 'Unauthorized login to Instagram.');
		}

		$content = $response->getBody()->getContents();
		$content = json_decode($content);


		$accessToken = $content->access_token;
		$userId = $content->user_id;

    // Get user info
		$response = $client->request('GET', "https://graph.instagram.com/me?fields=id,username,account_type,media_count&access_token={$accessToken}");

		$content = $response->getBody()->getContents();
		$oAuth = json_decode($content);

		echo '<pre>';
		print_r($response);
		die;


    // Get instagram user name 
		$username = $oAuth->username;

    // do your code here
	}

}
