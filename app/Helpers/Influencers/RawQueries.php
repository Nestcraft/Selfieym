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

namespace App\Helpers\Influencers;

use App\Helpers\ArrayHelper;
use App\Helpers\DBTool;
use App\Helpers\Number;
use App\Models\PostType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Larapen\LaravelDistance\Distance;

class RawQueries
{

	protected static $cacheExpiration = 300; // 5mn (60s * 5)
	
	public        $country;
	public        $lang;
	public static $queryLength  = 1;   // Minimum query characters
	public static $distance     = 100; // km
	public static $maxDistance  = 500; // km
	public        $perPage      = 12;
	public        $currentPage  = 0;
	protected     $sqlCurrLimit;
	protected     $table        = 'social';
	protected     $searchable   = [
		'columns' => [
			/*'tSocial.user_id'       => 10,*/
			/*'tSocial.biodata' => 10,*/
			/*'tPost.	phone'        => 8,*/
			/*'lCategory.name'    => 5,
			'lParent.name'      => 2,*/ // Category Parent
		],
	];
	protected     $forceAverage = true; // Force relevance's average
	protected     $average      = 1;    // Set relevance's average
	
	// Pre-Search vars
	public $city  = null;
	public $admin = null;
	
	// Ban this words in query search
	// protected $banWords = ['sell', 'buy', 'vendre', 'vente', 'achat', 'acheter', 'ses', 'sur', 'de', 'la', 'le', 'les', 'des', 'pour', 'latest'];
	protected $banWords = [];
	
	// SQL statements building vars
	protected $arrSql   = [
		'select'  => [
			'tSocial.id',
			'tSocial.user_id',
			'tSocial.biodata',
			//'tSocial.category_id',
			'tSocial.age',
			'tSocial.dob',
			'tSocial.gender',
			'tSocial.min_fee',
			'tSocial.city_id',
			'tSocial.skill_expertise',
			'tSocial.profile_image',
			'tSocial.created_at',
			'tSocial.facebook_followers',
			'tSocial.instagram_followers',
			'tSocial.twitter_followers',
			'tSocial.quora_followers',
			'tSocial.youtube_subscribers',
			'tSocial.facebook_url',
			'tSocial.instagram_url',
			'tSocial.website_url',
			'tSocial.youtube_url',
			'tSocial.twitter_url',
			'tSocial.tiktok_url',
			'tSocial.category_id',
			'tSocial.facebook_followers_without_unit',
			'tSocial.facebook_followers_unit',
			'tUser.name',
			'tUser.country_code',
			'tUser.language_code',
			'tUser.user_type_id',
			'tUser.gender_id',
			'tUser.photo',
			'tUser.about',
			'tUser.phone',
			'tUser.email_verified_at',
			'tUser.is_featured',
			'tCategory.name as catname',
		],
		'join'    => [],
		'where'   => [],
		'groupBy' => [
			'tSocial.id',
		],
		'having'  => [],
		'orderBy' => [],
	];
	protected $bindings = [];
	
	// Non-primary request parameters
	protected $filterParametersFields = [
		'type'       => 'tSocial.category_id',
		//'minSalary'  => 'tSocial.min_fee',
		'postedDate' => 'tUser.created_at',
	];
	// OrderBy request parameters
	protected $orderByParametersFields = [
		/*'salaryAsc'  => ['name' => 'tSocial.age', 'order' => 'ASC'],
		'salaryDesc' => ['name' => 'tPost.salary_max', 'order' => 'DESC'],
		'relevance'  => ['name' => 'gender', 'order' => 'DESC'],*/
		'date'       => ['name' => 'tSocial.created_at', 'order' => 'DESC'],
	];
	
	
	/**
	 * RawQueries constructor.
	 *
	 * @param array $preSearch
	 */
	public function __construct($preSearch = [])
	{
		// Pre-Search
		if (isset($preSearch['city']) && !empty($preSearch['city'])) {
			$this->city = $preSearch['city'];
		}
		if (isset($preSearch['admin']) && !empty($preSearch['admin'])) {
			$this->admin = $preSearch['admin'];
		}
		
		// Distance (Max & Default distance)
		self::$maxDistance = config('settings.listing.search_distance_max', 0);
		self::$distance    = config('settings.listing.search_distance_default', 0);
		
		// Posts per page
		/*$this->perPage = (is_numeric(config('settings.listing.items_per_page'))) ? config('settings.listing.items_per_page') : $this->perPage;*/
		if ($this->perPage < 4) $this->perPage = 4;
		if ($this->perPage > 40) $this->perPage = 40;
		
		// Init.
		$this->arrSql = ArrayHelper::toObject($this->arrSql, 2);
		// If the MySQL strict mode is activated, ...
		// Append all the non-calculated fields available in the 'SELECT' in 'GROUP BY' to prevent error related to 'only_full_group_by'
		if (env('DB_MODE_STRICT')) {
			$this->arrSql->groupBy = $this->arrSql->select;
		}
		array_push($this->banWords, strtolower(config('country.name')));
		
		// Post category relation
		$this->arrSql->join[] = "INNER JOIN " . DBTool::table('categories') . " AS tCategory ON tCategory.id=tSocial.category_id AND tCategory.active=1";
		// Category parent relation
		$this->arrSql->join[] = "LEFT JOIN " . DBTool::table('categories') . " AS tParent ON tParent.id=tCategory.parent_id AND tParent.active=1";
		
		// Categories translation relation
		/*$this->arrSql->join[] = "LEFT JOIN " . DBTool::table('categories')
			. " AS lCategory ON lCategory.translation_of=tCategory.id AND lCategory.translation_lang = :translationLang";*/
	/*	$this->arrSql->join[] = "LEFT JOIN " . DBTool::table('categories')
			. " AS lParent ON lParent.translation_of=lCategory.id AND lParent.translation_lang = :translationLang";*/
		
		/*$this->bindings['translationLang'] = config('lang.abbr');*/
		// Post payment relation

		$this->arrSql->join[] = "INNER JOIN " . DBTool::table('users') . " AS tUser ON tUser.id=tSocial.user_id";
/*	$this->arrSql->select[] = "tUser.id, tSocial.lft";
		*/
	
		
		
		// Post payment relation
		/*$this->arrSql->select[] = "tPayment.package_id, tPackage.lft";
		
		$latestPayment = "(SELECT MAX(id) lid, post_id FROM " . DBTool::table('payments') . " WHERE active=1 GROUP BY post_id) latestPayment";
		
		$this->arrSql->join[] = "LEFT JOIN " . $latestPayment . " ON latestPayment.post_id = tPost.id AND tPost.featured=1";
		$this->arrSql->join[] = "LEFT JOIN " . DBTool::table('payments') . " AS tPayment ON tPayment.id=latestPayment.lid";
		$this->arrSql->join[] = "LEFT JOIN " . DBTool::table('packages') . " AS tPackage ON tPackage.id=tPayment.package_id";
		
		$this->arrSql->groupBy[] = "tPayment.package_id, tPackage.lft";*/
		
		// Default filters
		$this->arrSql->where = [
			"tUser.verified_email = 1",
			"tUser.deleted_at IS NULL",
		];
		
		$this->bindings['countryCode'] = config('country.code');
		
		// Check reviewed posts
		/*if (config('settings.single.posts_review_activation')) {
			$this->arrSql->where[] = "tPost.reviewed = 1";
		}*/
		
		// Priority settings
		if (request()->filled('distance') and is_numeric(request()->get('distance'))) {
			self::$distance = request()->get('distance');
			if (request()->get('distance') > self::$maxDistance) {
				self::$distance = self::$maxDistance;
			}
		} else {
			request()->merge(['distance' => self::$distance]);
		}
		if (request()->filled('orderBy')) {
			$this->setOrder(request()->get('orderBy'));
		}
		/*if (request()->filled('ageBy')) {
			$this->setAge(request()->get('ageBy'));
		}*/
		
		// Pagination Init.
		$this->currentPage  = (request()->get('page') < 0) ? 0 : (int)request()->get('page');
		$page               = (request()->get('page') <= 1) ? 1 : (int)request()->get('page');
		$this->sqlCurrLimit = ($page <= 1) ? 0 : $this->perPage * ($page - 1);
		
		// If Ad Type(s) is (are) filled, then check if the Ad Type(s) exist(s)
		if (request()->filled('type')) {
			if (!$this->checkIfPostTypeExists(request()->get('type'))) {
				abort(404, t('The requested job types do not exist.'));
			}
		}
	}
	
	/**
	 * Check if PostType exist(s)
	 *
	 * @param $postTypeIds
	 * @return bool
	 */
	private function checkIfPostTypeExists($postTypeIds)
	{
		$found = false;
		
		// If Ad Type(s) is (are) filled, then check if the Ad Type(s) exist(s)
		if (!empty($postTypeIds)) {
			if (is_string($postTypeIds)) {
				$postTypeIds = [$postTypeIds];
			}
			$cacheId   = 'search.postTypes.' . md5(serialize($postTypeIds)) . '.' . config('app.locale');
			$postTypes = Cache::remember($cacheId, self::$cacheExpiration, function () use ($postTypeIds) {
				return PostType::query()
					->whereIn('translation_of', $postTypeIds)
					->where('translation_lang', config('app.locale'))
					->get(['id']);
			});
			
			if ($postTypes->count() > 0) {
				$found = true;
			}
		} else {
			$found = true;
		}
		
		return $found;
	}
	
	/**
	 * Get the results
	 *
	 * @return array
	 */
	public function fetch()
	{
		// Apply primary filters
		$this->setPrimaryFilters();
		
		// Check & Set other requests filters
		$this->setNonPrimaryFilters();
		
		// Get the SQL statements
		$sql = $this->getSqlStatements();
		
		// Count the results
		$count = $this->countFetch($sql);
		
		// Paginated SQL query
		$sql = $sql . "\n" . "LIMIT " . (int)$this->sqlCurrLimit . ", " . (int)$this->perPage;
		
		// Execute the SQL query
		$posts = self::execute($sql, $this->bindings);
		
		// Count real query posts (request()->get('type') is an array in JobClass)
		$total = $count->get('all');
		
		// Paginate
		$posts = new LengthAwarePaginator($posts, $total, $this->perPage, $this->currentPage);
		$posts->setPath(request()->url());
		
		// Transform the collection attributes
		$posts->getCollection()->transform(function ($post) {
			$post->name = mb_ucfirst($post->name);
			
			return $post;
		});
		
		// Clear request keys
		$this->clearRequestKeys();
		
		return ['paginator' => $posts, 'count' => $count];
	}
	
	/**
	 * Count the results
	 *
	 * @param $sql
	 * @return \Illuminate\Support\Collection
	 */
	private function countFetch($sql)
	{
		$sql = "SELECT COUNT(*) AS total FROM (" . $sql . ") AS x";
		
		// Execute
		$all = self::execute($sql, $this->bindings);
		
		$count['all'] = (isset($all[0])) ? $all[0]->total : 0;
		
		return collect($count);
	}
	
	/**
	 * Execute the SQL
	 *
	 * @param $sql
	 * @param array $bindings
	 * @return mixed
	 */
	private static function execute($sql, $bindings = [])
	{
		// DEBUG
		// echo 'SQL<hr><pre>' . $sql . '</pre><hr>'; // exit();
		// echo 'BINDINGS<hr><pre>'; print_r($bindings); echo '</pre><hr>'; // exit();
		
		try {
			$result = DB::select(DB::raw($sql), $bindings);
		} catch (\Exception $e) {
			$result = null;
			
			// DEBUG
			// dd($e->getMessage());
		}
		
		return $result;
	}
	
	/**
	 * Get the SQL statements
	 *
	 * @param array $arrWhere
	 * @return string
	 */
	private function getSqlStatements($arrWhere = [])
	{
		// Set SELECT
		$select = 'SELECT DISTINCT ' . implode(', ', $this->arrSql->select);
		
		// Set JOIN
		$join = '';
		if (count($this->arrSql->join) > 0) {
			$join = "\n" . implode("\n", $this->arrSql->join);
		}
		
		// Set WHERE
		$arrWhere = ((count($arrWhere) > 0) ? $arrWhere : $this->arrSql->where);
		$where    = '';
		if (count($arrWhere) > 0) {
			foreach ($arrWhere as $value) {
				if (trim($value) == '') {
					continue;
				}
				if ($where == '') {
					$where .= "\n" . 'WHERE ' . $value;
				} else {
					$where .= ' AND ' . $value;
				}
			}
		}
		
		// Set GROUP BY
		$groupBy = '';
		if (count($this->arrSql->groupBy) > 0) {
			$groupBy = "\n" . 'GROUP BY ' . implode(', ', $this->arrSql->groupBy);
		}
		
		// Set HAVING
		$having = '';
		if (count($this->arrSql->having) > 0) {
			foreach ($this->arrSql->having as $value) {
				if (trim($value) == '') {
					continue;
				}
				if ($having == '') {
					$having .= "\n" . 'HAVING ' . $value;
				} else {
					$having .= ' AND ' . $value;
				}
			}
		}
		
		// Set ORDER BY
		$orderBy = '';
		/*$orderBy .= "\n" . 'ORDER BY tPackage.lft DESC';*/
		if (count($this->arrSql->orderBy) > 0) {
			foreach ($this->arrSql->orderBy as $value) {
				if (trim($value) == '') {
					continue;
				}
				if ($orderBy == '') {
					$orderBy .= "\n" . 'ORDER BY ' . $value;
				} else {
					$orderBy .= ', ' . $value;
				}
			}
		}
		
		if (count($this->arrSql->orderBy) > 0) {
			// Check if the 'created_at' column is already apply for orderBy
			$orderByCreatedAtFound = collect($this->arrSql->orderBy)->contains(function ($value, $key) {
				return Str::contains($value, 'tSocial.created_at');
			});
			
			// Apply the 'tPost.created_at' column for orderBy
			if (!$orderByCreatedAtFound) {
				$orderBy .= ', tSocial.created_at DESC';
			}
		} else {
			if ($orderBy == '') {
				$orderBy .= "\n" . 'ORDER BY tSocial.created_at DESC';
			} else {
				$orderBy .= ', tSocial.created_at DESC';
			}
		}
		
		// Get Query
	/*print_r($where);
		die;*/
		$sql = $select . "\n" . "FROM " . DBTool::table($this->table) . " AS tSocial" . $join . $where . $groupBy . $having . $orderBy;
/*print_r($sql);
		die;*/
		return $sql;
	}
	
	/**
	 * Apply primary filters
	 */
	public function setPrimaryFilters()
	{
		if (request()->filled('q')) {
			$this->setKeywords(request()->get('q'));
		}
		if (request()->filled('g') && !empty(request()->get('g')) ) {
			
				$this->setJobuser(request()->get('g'));
			
		}
		if (request()->filled('s')) {
			$this->setSocial(request()->get('s'));
		}
		if (request()->filled('r') && !empty($this->admin) && !request()->filled('l')) {
			$this->setLocationByAdminCode($this->admin->code);
		}
		if (request()->has('l') && !empty($this->city)) {
			
			$this->setLocationByCity($this->city);
			/*$this->setLocationByCity($this->city);*/
		}
		if (request()->has('minSalary') && request()->has('maxSalary')) {
			
			$this->setMinfee(request()->get('minSalary'),request()->get('maxSalary'));
			
		}
		if (request()->has('ageBy')) {
			
			$this->setAge(request()->get('ageBy'));
			
		}
		if (request()->has('genderBy')) {
			
			$this->setgender(request()->get('genderBy'));
			
		}
		if (request()->has('faceebookfollowBy')) {
			
			$this->setfaceebookfollow(request()->get('faceebookfollowBy'));
			
		}
		if (request()->has('orderBy')) {
			
			$this->setorderBydate(request()->get('orderBy'));
			
		}

		

	}
	
	/**
	 * Apply keyword filter
	 *
	 * @param $keywords
	 * @return bool
	 */
	public function setKeywords($keywords)
	{
		if (trim($keywords) == '') {
			return false;
		}
		$this->arrSql->where[]   = 'tUser.name LIKE "%'.$keywords.'%"';
		// Query search SELECT array
		$select = [];
		
		// Get all keywords in array
		$words_tab = preg_split('/[\s,\+]+/', $keywords);
		
		//-- If third parameter is set as true, it will check if the column starts with the search
		//-- if then it adds relevance * 30
		//-- this ensures that relevant results will be at top
		/*$select[] = "(CASE WHEN tUser.name LIKE :keywords THEN 300 ELSE 0 END) ";*/
		
		$this->bindings['keywords'] = $keywords . '%';
		
		
		foreach ($this->searchable['columns'] as $column => $relevance) {
			$tmp = [];
			foreach ($words_tab as $key => $word) {
				// Skip short keywords
				if (strlen($word) <= self::$queryLength) {
					continue;
				}
				// @todo: Find another way
				if (in_array(mb_strtolower($word), $this->banWords)) {
					continue;
				}
				$tmp[] = $column . " LIKE :word_" . $key;
				
				$this->bindings['word_' . $key] = '%' . $word . '%';
			}
			if (count($tmp) > 0) {
				$select[] = "(CASE WHEN " . implode(' || ', $tmp) . " THEN " . $relevance . " ELSE 0 END) ";
			}
		}
		if (count($select) <= 0) {
			return false;
		}
		
		$this->arrSql->select[] = "(" . implode("+\n", $select) . ") AS relevance";
		
		//-- Selects only the rows that have more than
		//-- the sum of all attributes relevances and divided by count of attributes
		//-- e.i. (20 + 5 + 2) / 4 = 6.75
		$average = array_sum($this->searchable['columns']) / count($this->searchable['columns']);
		$average = Number::toFloat($average);
		if ($this->forceAverage) {
			// Force average
			$average = $this->average;
		}
		
		$this->arrSql->having[]    = 'relevance >= :average';
		$this->bindings['average'] = $average;
		
		//-- Group By
		$this->arrSql->groupBy[] = "relevance";
		
		//-- Orders the results by relevance
		$this->arrSql->orderBy[] = 'relevance DESC';
	}
	
	/**
	 * Apply category filter
	 *
	 * @param $catId
	 * @param null $subCatId
	 * @return $this
	 */
	public function setCategory($catId,$subCatId = null)
	{
	if (trim($catId) == '') {
			return $this;
		}
/*if (empty($subCatId)) {
			// $this->arrSql->where[] = 'tParent.id = :catId';
			$this->arrSql->where[]   = ':catId IN (tCategory.id, tParent.id)';
			$this->bindings['catId'] = $catId;
		} // SubCategory
		else {*/			 
			$this->arrSql->where[] = 'tSocial.category_id ="'.$catId.'"';
			/*$this->arrSql->where[]   = ':genId IN (tSocial.gender_id=$gender_id)';*/
			$this->bindings['catId'] = $catId;
		/*}*/

		return $this;
	}
	/**
	 * Apply user filter
	 *
	 * @param $userId
	 * @return $this
	 */
	public function setSocial($socialId)
	{
		if (trim($socialId) == '') {
			return $this;
		}
		if (!empty($socialId)) {
			if ($socialId=='facebook') {
			 $this->arrSql->where[] = 'tSocial.facebook_url !=" "';
			$this->bindings['socialId'] = $socialId;
		}
		if ($socialId=='instagram') {
			 $this->arrSql->where[] = 'tSocial.instagram_url !=""';
			$this->bindings['socialId'] = $socialId;
		}
		if ($socialId=='website') {
			 $this->arrSql->where[] = 'tSocial.website_url !=""';
			$this->bindings['socialId'] = $socialId;
		}
		if ($socialId=='twitter') {
			 $this->arrSql->where[] = 'tSocial.twitter_url !=" "';
			$this->bindings['socialId'] = $socialId;
		}
		if ($socialId=='tiktok') {
			 $this->arrSql->where[] = 'tSocial.tiktok_url !=" "';
			$this->bindings['socialId'] = $socialId;
		}
		if ($socialId=='youtube') {
			 $this->arrSql->where[] = 'tSocial.youtube_url !=" "';
			$this->bindings['socialId'] = $socialId;
		}

		} // SubCategory

		return $this;
	}
	public function setJobuser($genId)
	{
		if (trim($genId) == '') {
			return $this;
		}

			 $this->arrSql->where[] = 'tUser.gender_id ="'.$genId.'"';
			/*$this->arrSql->where[]   = ':genId IN (tSocial.gender_id=$gender_id)';*/
			$this->bindings['genId'] = $genId;
		

		return $this;
	}
	
	public function setUser($userId)
	{
		if (trim($userId) == '') {
			return $this;
		}
		
		$this->arrSql->where[]    = 'tSocial.user_id = :userId';
		$this->bindings['userId'] = $userId;
		
		return $this;
	}
	public function setMinfee($minFee,$maxFee)
	{
		if (trim($minFee) == '') {
			return $this;
		}

			/* $this->arrSql->where[] = 'tSocial.min_fee >="'.$minFee.'" AND tSocial.min_fee <="'.$maxFee.'"';*/
			 $this->arrSql->where[] = 'tSocial.min_fee BETWEEN '.$minFee.' AND '.$maxFee;


			$this->bindings['minFee'] = $minFee;
		

		return $this;
	}
	/**
	 * Apply company filter
	 *
	 * @param $companyId
	 * @return $this
	 */
	public function setCompany($companyId)
	{
		if (trim($companyId) == '') {
			return $this;
		}
		$this->arrSql->where[] = 'tPost.company_id = :companyId';
		
		$this->bindings['companyId'] = $companyId;
		
		return $this;
	}
	
	/**
	 * Apply tag filter
	 *
	 * @param $tag
	 * @return $this
	 */
	public function setTag($tag)
	{
		if (trim($tag) == '') {
			return $this;
		}
		
		$tag = rawurldecode($tag);
		
		$this->arrSql->where[] = 'FIND_IN_SET(:tag, LOWER(tPost.tags)) > 0';
		$this->bindings['tag'] = mb_strtolower($tag);
		
		return $this;
	}
	
	/**
	 * Apply administrative division filter
	 * Search including Administrative Division by adminCode
	 *
	 * @param $adminCode
	 * @return $this
	 */
	public function setLocationByAdminCode($adminCode)
	{
		if (in_array(config('country.admin_type'), ['1', '2'])) {
			// Get the admin. division table info
			$adminType       = config('country.admin_type');
			$adminTable      = 'subadmin' . $adminType;
			$adminForeignKey = 'subadmin' . $adminType . '_code';
			
			// Query
			$this->arrSql->join[]  = "INNER JOIN " . DBTool::table('cities') . " AS tCity ON tCity.id=tPost.city_id";
			$this->arrSql->join[]  = "INNER JOIN " . DBTool::table($adminTable) . " AS tAdmin ON tAdmin.code=tCity." . $adminForeignKey;
			$this->arrSql->where[] = 'tAdmin.code = :adminCode';
			
			$this->bindings['adminCode'] = $adminCode;
			
			return $this;
		}
		
		return $this;
	}
	
	/**
	 * Apply city filter (Using city's coordinates)
	 * Search including City by City Coordinates (lat & lon)
	 *
	 * @param $city
	 * @return $this
	 */
	public function setLocationByCity($city)
	{
		if (!isset($city->id) || !isset($city->longitude) || !isset($city->latitude)) {
			return $this;
		}
		
		if ($city->longitude == 0 || $city->latitude == 0) {
			return $this;
		}
		
		// Set city globally
		$this->city = $city;
		
		// OrderBy priority for location
		$this->arrSql->orderBy[] = 'tUser.created_at DESC';
		
		// If extended search is disabled...
		// Use the Cities Standard Searches
		if (!config('settings.listing.cities_extended_searches')) {
			return $this->setLocationByCityId($city->id);
		}
		
		// Use the Cities Extended Searches
		config()->set('distance.functions.default', config('settings.listing.distance_calculation_formula'));
		config()->set('distance.countryCode', config('country.code'));
		
		/*$sql = Distance::select('tPost.lon', 'tPost.lat', ':longitude', ':latitude');
		if ($sql) {
			$this->arrSql->select[]  = $sql;
			$this->arrSql->having[]  = Distance::having(self::$distance);
			$this->arrSql->orderBy[] = Distance::orderBy('ASC');
			
			$this->bindings['longitude'] = $city->longitude;
			$this->bindings['latitude']  = $city->latitude;
		} else {*/
			return $this->setLocationByCityId($city->id);
		/*}*/
		
		return $this;
	}
	
	/**
	 * Apply city filter (Using city's Id)
	 * Search including City by City Id
	 *
	 * @param $cityId
	 * @return $this
	 */
	public function setLocationByCityId($cityId)
	{

		if (trim($cityId) == '') {
			return $this;
		}
		
		$this->arrSql->where[] = 'tSocial.city_id ="'.$cityId.'"';
		
		$this->bindings['cityId'] = $cityId;
		
		return $this;
	}
	
	/**
	 * Apply non-primary filters
	 *
	 * @return $this
	 */
	public function setNonPrimaryFilters()
	{
		$parameters = request()->all();
		if (count($parameters) == 0) {
			return $this;
		}
		
		foreach ($parameters as $key => $value) {
			if (!isset($this->filterParametersFields[$key])) {
				continue;
			}
			if (!is_array($value) and trim($value) == '') {
				continue;
			}
			
			// Special parameters
			$specParams = [];
			if ($key == 'minSalary') { // Min. Salary
				$this->arrSql->where[] = $this->filterParametersFields[$key] . ' >= ' . $value;
				$specParams[]          = $key;
			}
			if ($key == 'maxSalary') { // Max. Salary
				$this->arrSql->where[] = $this->filterParametersFields[$key] . ' <= ' . $value;
				$specParams[]          = $key;
			}
			if ($key == 'postedDate') { // Date
				$this->arrSql->where[]        = $this->filterParametersFields[$key] . ' BETWEEN DATE_SUB(NOW(), INTERVAL :postedDate DAY) AND NOW()';
				$this->bindings['postedDate'] = $value;
				$specParams[]                 = $key;
			}
			
			// No-Special parameters
			if (!in_array($key, $specParams)) {
				if (is_array($value)) {
					$tmpArr = [];
					foreach ($value as $k => $v) {
						if (is_array($v)) continue;
						if (!is_array($v) && trim($v) == '') continue;
						
						$tmpArr[$k] = $v;
					}
					if (!empty($tmpArr)) {
						$this->arrSql->where[] = $this->filterParametersFields[$key] . ' IN (' . implode(',', $tmpArr) . ')';
					}
				} else {
					$this->arrSql->where[] = $this->filterParametersFields[$key] . ' = ' . $value;
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * Apply order
	 *
	 * @param $field
	 */
	public function setOrder($field)
	{
		if (!isset($this->orderByParametersFields[$field])) {
			return;
		}
		
		// Check if the 'relevance' column is already apply for orderBy
		$orderByRelevanceFound = collect($this->arrSql->orderBy)->contains(function ($value, $key) {
			return Str::contains($value, 'relevance');
		});
		
		// Check essential field
		if ($field == 'relevance' && !$orderByRelevanceFound) {
			return;
		}
		
		$this->arrSql->orderBy[] = $this->orderByParametersFields[$field]['name'] . ' ' . $this->orderByParametersFields[$field]['order'];
	}
		public function setAge($age)
	{
		if (trim($age) == '') {
			return $this;
		}
		if($age=='any'){
			return $this;
		}elseif($age=='less_25'){
			$this->arrSql->where[] = 'tSocial.age < 25';
		}elseif($age=='less_40'){
			$this->arrSql->where[] = 'tSocial.age < 40';
		}elseif($age=='less_50'){
			$this->arrSql->where[] = 'tSocial.age < 20';
		}elseif($age=='great_50'){
			$this->arrSql->where[] = 'tSocial.age > 50';
		}
		
		
		
		$this->bindings['age'] = $age;
		
		return $this;
	}
	public function setgender($gender)
	{
		if (trim($gender) == '') {
			return $this;
		}
		if($gender=='any'){
			return $this;
		}else{
			$this->arrSql->where[] = 'tSocial.gender ="'.$gender.'"';
		}
		
		
		
		$this->bindings['gender'] = $gender;
		
		return $this;
	}
	public function setorderBydate($date)
	{
		if (trim($date) == '') {
			return $this;
		}
		
			//$this->arrSql->where[] = 'tSocial.created_at DESC';
	
		
		
		
		$this->bindings['date'] = $date;
		
		return $this;
	}

	public function setfaceebookfollow($faceebookfollow)
	{
		if (trim($faceebookfollow) == '') {
			return $this;
		}
		;
		if($faceebookfollow=='5k-10k'){
		
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 5 AND 10';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="K"';
		}elseif($faceebookfollow=='10k-25k'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 10 AND 25';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="K"';
		}elseif($faceebookfollow=='25k-50k'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 25 AND 50';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="K"';
		}elseif($faceebookfollow=='100k-500k'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 100 AND 500';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="K"';
		}elseif($faceebookfollow=='500k-1000k'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 500 AND 1000';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="K"';
		
		}elseif($faceebookfollow=='1M-2M'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 1 AND 2';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="M"';
		
		}elseif($faceebookfollow=='2M-5M'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 2 AND 5';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="M"';
	
		}elseif($faceebookfollow=='5M-10M'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 5 AND 10';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="M"';
	
		}elseif($faceebookfollow=='10-25M'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 10 AND 25';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="M"';
		
		}elseif($faceebookfollow=='25M-50M'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit BETWEEN 25 AND 50';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="M"';
		
		}elseif($faceebookfollow=='50'){
			$this->arrSql->where[] = 'tSocial.facebook_followers_without_unit > 50';
			$this->arrSql->where[] ='tSocial.facebook_followers_unit="M"';
		}
	
		
			
		
		
		
		$this->bindings['faceebookfollow'] = $faceebookfollow;
		
		return $this;
	}
	/**
	 * Clear request keys
	 */
	private function clearRequestKeys()
	{
		$input = request()->all();
		
		// (If it's not necessary) Remove the 'distance' parameter from request()
		if (!config('settings.listing.cities_extended_searches') || empty($this->city)) {
			if (in_array('distance', array_keys($input))) {
				unset($input['distance']);
				request()->replace($input);
			}
		}
	}
}
