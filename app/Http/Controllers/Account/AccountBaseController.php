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

namespace App\Http\Controllers\Account;

use App\Http\Controllers\FrontController;
use App\Models\Company;
use App\Models\Post;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Resume;
use App\Models\SavedPost;
use App\Models\SavedSearch;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use Illuminate\Support\Facades\DB;

abstract class AccountBaseController extends FrontController
{
	public $countries;
	public $myPosts;
	public $archivedPosts;
	public $favouritePosts;
	public $pendingPosts;
	public $conversations;
	public $transactions;
	public $companies;
	public $resumes;
	
	/**
	 * AccountBaseController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware(function ($request, $next) {
			$this->leftMenuInfo();
			return $next($request);
		});
		
		view()->share('pagePath', '');
	}
	
	public function leftMenuInfo()
	{
		// Get & Share Countries
		$this->countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
		view()->share('countries', $this->countries);
		
		// Share User Info
		view()->share('user', auth()->user());
		
		// My Posts
		$this->myPosts = Post::currentCountry()
			->where('user_id', auth()->user()->id)
			->verified()
			->unarchived()
			->reviewed()
			->with(['city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
			->orderByDesc('id');
		view()->share('countMyPosts', $this->myPosts->count());
		
		// Archived Posts
		$this->archivedPosts = Post::currentCountry()
			->where('user_id', auth()->user()->id)
			->archived()
			->with(['city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
			->orderByDesc('id');
		view()->share('countArchivedPosts', $this->archivedPosts->count());
		
		// Favourite Posts
		$this->favouritePosts = SavedPost::whereHas('post', function ($query) {
			$query->currentCountry();
		})
			->where('user_id', auth()->user()->id)
			->with(['post.city'])
			->orderByDesc('id');
		view()->share('countFavouritePosts', $this->favouritePosts->count());
		
		// Pending Approval Posts
		$this->pendingPosts = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
			->currentCountry()
			->where('user_id', auth()->user()->id)
			->unverified()
			->with(['city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
			->orderByDesc('id');
		view()->share('countPendingPosts', $this->pendingPosts->count());
		
		// Save Search
		$savedSearch = SavedSearch::currentCountry()
			->where('user_id', auth()->user()->id)
			->orderByDesc('id');
		view()->share('countSavedSearch', $savedSearch->count());
		
		// Conversations
		DB::enableQueryLog(); // Enable query log
		$this->conversations = Message::with('latestReply')
			->whereHas('post', function ($query) {
				$query->currentCountry();
			}) 
			->select('messages.*','projectaward.jobprojectaward_id','projectaward.employer_id','projectaward.influencer_id','projectaward.project_status','projectaward.post_id as post_id_award','projectaward.conversation_id as conversation_id_award','social.profile_image','users.profile_image_employer')
			->leftJoin('social', 'social.user_id', '=', 'messages.from_user_id')
			->leftJoin('users', 'users.id', '=', 'messages.to_user_id')
			->leftJoin('projectaward','projectaward.conversation_id', '=', 'messages.id')
			
			// ->leftJoin('rating_review', 'rating_review.post_id_review', '=', 'messages.id', 'rating_review.from_user_id_review', '=', auth()->user()->id)
			->byUserId(auth()->user()->id)
			->where('parent_id', 0)
			->orderByDesc('messages.id');
		view()->share('countConversations', $this->conversations->count());
      
		// echo '<pre>';
		// print_r($this->conversations);
		// die('ssssssssssssss');

		/*$queries = DB::getQueryLog();
		print_r($queries);
		 die("welll");*/
		// Payments
		$this->transactions = Payment::whereHas('post', function ($query) {
			$query->currentCountry()->whereHas('user', function ($query) {
				$query->where('user_id', auth()->user()->id);
			});
		})
			->with(['post', 'paymentMethod', 'package' => function ($builder) { $builder->with(['currency']); }])
			->orderByDesc('id');
		view()->share('countTransactions', $this->transactions->count());
		
		// Companies
		$this->companies = Company::where('user_id', auth()->user()->id)->orderByDesc('id');
		view()->share('countCompanies', $this->companies->count());
		
		// Resumes
		$this->resumes = Resume::where('user_id', auth()->user()->id)->orderByDesc('id');
		view()->share('countResumes', $this->resumes->count());


		// USER TYPE 
          $user_type = DB::table('users')
        ->where('users.id', auth()->user()->id)
        ->select('users.user_type_id')
        ->first();

        view()->share('USERTYPE', $user_type->user_type_id);


	}
}
