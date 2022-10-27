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

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class UrlGen
{
	/**
	 * @param $entry
	 * @param null $locale
	 * @param bool $encoded
	 * @return string
	 */
	public static function postPath($entry, $locale = null, $encoded = false)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (is_array($entry)) {
			$entry = ArrayHelper::toObject($entry);
		}
		
		if (isset($entry->id) && isset($entry->title)) {
			$preview = !isVerifiedPost($entry) ? '?preview=1' : '';
			
			$attr = ['slug' => ($encoded) ? rawurlencode(slugify($entry->title)) : slugify($entry->title), 'id' => $entry->id];
			$path = trans('routes.v-post', $attr, $locale) . $preview;
		} else {
			$path = '#';
		}
		
		return $path;
	}
	
	/**
	 * @param $entry
	 * @param null $locale
	 * @param bool $encoded
	 * @return string
	 */
	public static function postUri($entry, $locale = null, $encoded = false)
	{
		$path = self::postPath($entry, $locale, $encoded);
		
		$uri = $locale . '/' . $path;
		
		return $uri;
	}
	
	/**
	 * @param $entry
	 * @param null $locale
	 * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|mixed|string|null
	 */
	public static function post($entry, $locale = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (is_array($entry)) {
			$entry = ArrayHelper::toObject($entry);
		}
		
		$path = self::postPath($entry, $locale);
		
		if (isset($entry->id) && isset($entry->title)) {
			$attr = ['slug' => slugify($entry->title), 'id' => $entry->id];
			$url = lurl($path, $attr, $locale);
		} else {
			$url = '#';
		}
		
		return $url;
	}
	
	/**
	 * @param bool $httpError
	 * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|mixed|string|null
	 */
	public static function addPost($httpError = false)
	{
		if (!$httpError) {
			$url = (config('settings.single.publication_form_type') == '2')
			? lurl('create')
			: lurl('posts/create');
		} else {
			$url = (config('settings.single.publication_form_type') == '2')
			? url(config('app.locale') . '/create')
			: url(config('app.locale') . '/posts/create');
		}
		
		return $url;
	}
	
	/**
	 * @param $entry
	 * @param null $locale
	 * @return false|\Illuminate\Contracts\Routing\UrlGenerator|string|null
	 */
	public static function editPost($entry, $locale = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (is_array($entry)) {
			$entry = ArrayHelper::toObject($entry);
		}
		
		if (isset($entry->id)) {
			$url = (config('settings.single.publication_form_type') == '2')
			? lurl('edit/' . $entry->id, [], $locale)
			: lurl('posts/' . $entry->id . '/edit', [], $locale);
		} else {
			$url = '#';
		}
		
		return $url;
	}
	
	/**
	 * @param $entry
	 * @param int $level
	 * @param null $locale
	 * @param null $countryCode
	 * @return false|\Illuminate\Contracts\Routing\UrlGenerator|string|null
	 */
	public static function category($entry, $level = 0, $locale = null, $countryCode = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		$countryCode = strtolower($countryCode);
		
		if (is_array($entry)) {
			$entry = ArrayHelper::toObject($entry);
		}
		
		if ($level == 1) {
			if (isset($entry->parent) && isset($entry->parent->slug) && isset($entry->slug)) {
				$attr = [
					'countryCode' => $countryCode,
					'catSlug'     => $entry->parent->slug,
					'subCatSlug'  => $entry->slug,
				];
				$url = lurl(trans('routes.v-search-subCat', $attr, $locale), $attr, $locale);
			} else {
				$url = '#';
			}
		} else {
			if (isset($entry->slug)) {
				$attr = [
					'countryCode' => $countryCode,
					'catSlug'     => $entry->slug,
				];
				$url = lurl(trans('routes.v-search-cat', $attr, $locale), $attr, $locale);
			} else {
				$url = '#';
			}
		}
		
		return $url;
	}
	public static function influencer($entry, $level = 0, $locale = null, $countryCode = null)
	{

		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		$countryCode = strtolower($countryCode);
		
		if (is_array($entry)) {
			$entry = ArrayHelper::toObject($entry);
		}
		
		if ($level == 1) {
			if (isset($entry->parent) && isset($entry->parent->slug) && isset($entry->slug)) {
				$attr = [
					'countryCode' => $countryCode,
					'catSlug'     => $entry->parent->slug,
					'subCatSlug'  => $entry->slug,
				];
				$url = lurl(trans('routes.e-search-subCat', $attr, $locale), $attr, $locale);
			} else {
				$url = '#';
			}
		} else {
			if (isset($entry->slug)) {
				$attr = [
					'countryCode' => $countryCode,
					'catSlug'     => $entry->slug,
				];
				$url = lurl(trans('routes.e-search-cat', $attr, $locale), $attr, $locale);
					//print_r($url).'/';
				//die;
			} else {
				$url = '#';
			}
		}
		
		return $url;
	}
	/**
	 * @param $entry
	 * @param null $locale
	 * @param null $countryCode
	 * @return false|\Illuminate\Contracts\Routing\UrlGenerator|string|null
	 */
	public static function city($entry, $locale = null, $countryCode = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (empty($countryCode)) {
			if (isset($entry->country_code) && !empty($entry->country_code)) {
				$countryCode = $entry->country_code;
			} else {
				$countryCode = config('country.code');
			}
		}
		$countryCode = strtolower($countryCode);
		
		if (is_array($entry)) {
			$entry = ArrayHelper::toObject($entry);
		}
		
		if (isset($entry->id) && isset($entry->name)) {
			if (isFromAdminPanel()) {
				if (config('settings.seo.multi_countries_urls')) {
					$uri = trans('routes.v-search-city', [
						'countryCode' => $countryCode,
						'city'        => slugify($entry->name),
						'id'          => $entry->id,
					]);
				} else {
					$uri = trans('routes.v-search-city', [
						'city' => slugify($entry->name),
						'id'   => $entry->id,
					]);
				}
				
				if (!currentLocaleShouldBeHiddenInUrl()) {
					$uri = $locale . '/' . $uri;
				}
				
				$url = localUrl($entry->country_code, $uri);
			} else {
				$attr = [
					'countryCode' => $countryCode,
					'city'        => slugify($entry->name),
					'id'          => $entry->id,
				];
				$url = lurl(trans('routes.v-search-city', $attr, $locale), $attr, $locale);
			}
		} else {
			$url = '#';
		}
		
		return $url;
	}
	
	/**
	 * @param $entry
	 * @param null $locale
	 * @param null $countryCode
	 * @return false|\Illuminate\Contracts\Routing\UrlGenerator|string|null
	 */
	public static function user($entry, $locale = null, $countryCode = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		$countryCode = strtolower($countryCode);
		
		if (is_array($entry)) {
			$entry = ArrayHelper::toObject($entry);
		}
		
		if (isset($entry->username) && !empty($entry->username)) {
			$attr = [
				'countryCode' => $countryCode,
				'username'    => $entry->username,
			];
			$url = lurl(trans('routes.v-search-username', $attr, $locale), $attr, $locale);
		} else {
			if (isset($entry->id)) {
				$attr = [
					'countryCode' => $countryCode,
					'id'          => $entry->id,
				];
				$url = lurl(trans('routes.v-search-user', $attr, $locale), $attr, $locale);
			} else {
				$url = '#';
			}
		}
		
		return $url;
	}
	
	/**
	 * @param $tag
	 * @param null $locale
	 * @param null $countryCode
	 * @return false|\Illuminate\Contracts\Routing\UrlGenerator|string|null
	 */
	public static function tag($tag, $locale = null, $countryCode = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		$countryCode = strtolower($countryCode);
		
		$attr = [
			'countryCode' => $countryCode,
			'tag'         => $tag,
		];
		$url = lurl(trans('routes.v-search-tag', $attr, $locale), $attr, $locale);
		
		return $url;
	}
	
	/**
	 * @param null $locale
	 * @param null $countryCode
	 * @return false|\Illuminate\Contracts\Routing\UrlGenerator|string|null
	 */
	public static function company($locale = null, $countryCode = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		$countryCode = strtolower($countryCode);
		
		$attr = [
			'countryCode' => $countryCode,
		];
		$url = lurl(trans('routes.v-companies-list', $attr, $locale), $attr, $locale);
		
		return $url;
	}
	
	/**
	 * @param array $queryArr
	 * @param array $exceptArr
	 * @param bool $currentUrl
	 * @param null $countryCode
	 * @return mixed|string
	 */
	public static function search($queryArr = [], $exceptArr = [], $currentUrl = false, $countryCode = null)
	{
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		$countryCode = strtolower($countryCode);
		
		if ($currentUrl) {
			$fullUrl = rawurldecode(url(request()->getRequestUri()));
			$tmp = explode('?', $fullUrl);
			$url = current($tmp);
		} else {
			$attr = ['countryCode' => $countryCode];
			// $url = lurl(trans('routes.v-search', $attr), $attr);
			$url = config('app.locale') . '/' . trans('routes.v-search', $attr);
		}
		
		$url = qsurl($url, array_merge(request()->except($exceptArr + array_keys($queryArr)), $queryArr), null, false);
		
		return $url;
	}
	
	/**
	 * @param $entry
	 * @param null $locale
	 * @param null $countryCode
	 * @return false|\Illuminate\Contracts\Routing\UrlGenerator|string|null
	 */
	public static function page($entry, $locale = null, $countryCode = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		$countryCode = strtolower($countryCode);
		
		if (is_array($entry)) {
			$entry = ArrayHelper::toObject($entry);
		}
		
		if (isset($entry->slug)) {
			$attr = ['slug' => $entry->slug];
			if (isFromAdminPanel()) {
				$path = trans('routes.v-page', $attr);
				if (!currentLocaleShouldBeHiddenInUrl()) {
					$path = config('app.locale') . '/' . $path;
				}
				$url = url($path);
			} else {
				$url = lurl(trans('routes.v-page', $attr, $locale), $attr, $locale);
			}
		} else {
			$url = '#';
		}
		
		return $url;
	}
	
	/**
	 * @param null $locale
	 * @param null $countryCode
	 * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|mixed|string|null
	 */
	public static function sitemap($locale = null, $countryCode = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		if (empty($countryCode)) {
			$countryCode = config('country.code');
		}
		$countryCode = strtolower($countryCode);
		
		$attr = [
			'countryCode' => $countryCode,
		];
		$url = lurl(trans('routes.v-sitemap', $attr, $locale), $attr, $locale);
		
		return $url;
	}

	public static function get_user_wallet($user_id){

		$user_wallet = DB::select('select wallet_amount from jobwallet where user_id = ?',[$user_id]);
		$currency_symbol = \DB::table('users')->where('id', $user_id)
            ->value('currency_symbol');
	
		if(isset($user_wallet[0]->wallet_amount))
		{
			return $currency_symbol.$user_wallet[0]->wallet_amount;

		}else{

           return '0';
           
		}

		
	}

	public static function get_influencer_rating($user_id){

		$influencer_rating = DB::select('select rating from jobrating_review where to_user_id_review = ?',[$user_id]);

		if(count($influencer_rating) > 0)
		{

			$total_rating = 0;
			$average_rating = 0;

			foreach($influencer_rating as $rating){

				$total_rating += $rating->rating;
			}

			$average_rating = $total_rating/count($influencer_rating); 
			return $average_rating;

		}

	}
	public static function get_user_package($user_id){

		$user_package = \DB::table('packagepayments')->where('user_id', '<=', $user_id)
            ->count();
		
		return $user_package;
		
		

		
	}
	public static function get_employer_packageinfo($package_id){
		
            $user_packages_info = DB::select('select * from jobpackages where id = ?',[$package_id]);
     
         if($user_packages_info){
         	if($user_packages_info)
         		$price=$user_packages_info[0]->price;
         		$commission_type=$user_packages_info[0]->commission_type;
         		$commission=$user_packages_info[0]->commission;
         		if($commission_type==1){
         			$commissionAmount=$commission;
         		}elseif($commission_type==2){
					$commissionAmount=$price*$commission/100;
         		}else{
         			$commissionAmount='0';
         		}
         		return $commissionAmount;
         	
         }else{
         	 return '0';
         }
}
public static function get_influencer_packageinfo($package_id){

            $user_packages_info = DB::select('select * from jobpackages_influencer where id = ?',[$package_id]);
         if($user_packages_info){
         	if($user_packages_info)
         		$price=$user_packages_info[0]->price;
         		$commission_type=$user_packages_info[0]->commission_type;
         		$commission=$user_packages_info[0]->commission;
         		if($commission_type==1){
         			$commissionAmount=$commission;
         		}elseif($commission_type==2){
					$commissionAmount=$price*$commission/100;
         		}else{
         			$commissionAmount='0';
         		}
         		return $commissionAmount;
         	
         }else{
         	 return '0';
         }

        

		
	}
	
}
