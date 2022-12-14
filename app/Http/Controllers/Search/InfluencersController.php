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

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Search\Traits\PreSearchTrait;
use Torann\LaravelMetaTags\Facades\MetaTag;
use Illuminate\Http\Request;
use DB;
class InfluencersController extends BaseController
{
	use PreSearchTrait;
	
	public $isIndexSearch = true;
	
	protected $cat    = null;
	protected $subCat = null;
	protected $city   = null;
	protected $admin  = null;
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index(Request $request)
	{
		

		view()->share('isIndexSearch', $this->isIndexSearch);


		// Pre-Search
	
		if (request()->filled('g')) {
		$gender = $this->getGender(request()->get('g')); 	
		}
		if (request()->filled('l') || request()->filled('location')) {
			$city = $this->getCity(request()->get('l'), request()->get('location'));
		}

		if (request()->filled('r') && !request()->filled('l')) {

			$admin = $this->getAdmin(request()->get('r'));
		}
		
		// Pre-Search values
		$preSearch = [
			'city'  => (isset($city) && !empty($city)) ? $city : null,
			'admin' => (isset($admin) && !empty($admin)) ? $admin : null,
		];
		
		// Search
		$search = new $this->influencersClass($preSearch);
		$data   = $search->fetch();
	// echo'<pre>';
	// 	print_r($data['count']);
	// 	die;
		
		
		// Export Search Result
		view()->share('count', $data['count']);
		view()->share('paginator', $data['paginator']);

		
		// Get Titles
		$title='';
		$title = $this->getTitle();
		$this->getBreadcrumb();
		$this->getHtmlTitle();
		
		// Meta Tags
		// MetaTag::set('title', $title);
		// MetaTag::set('description', $title);
		$this->setSeo();
		return view('search.serpInfluencers');
	}

	protected function setSeo()
	{
		$title       = getMetaTag('title', 'Influencers');
		$description = getMetaTag('description', 'Influencers');
		$keywords    = getMetaTag('keywords', 'Influencers');
		
		// Meta Tags
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);
		
		// Open Graph
		$this->og->title($title)->description($description);
		view()->share('og', $this->og);
	}
	


}
