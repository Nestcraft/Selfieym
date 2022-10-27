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

namespace App\Http\Requests;

use App\Helpers\Files\Storage\StorageDisk;
use App\Models\Resume;
use App\Rules\BetweenRule;
use App\Rules\EmailRule;

class SendMessageInfluencerRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{

		$rules = [
			'from_name' => ['required'],
			'from_email' => ['required'],
			'to_name' => ['required'],
			'to_email' => ['required'],
			'to_user_id' => ['required'],
			'to_phone' => ['required'],
			'message' => ['required'],
		];
		
		// reCAPTCHA
		//$rules = $this->recaptchaRules($rules);
		
		// Email
		if ($this->filled('from_email')) {
			$rules['from_email'][] = 'email';
			$rules['from_email'][] = new EmailRule();
		}
		if (isEnabledField('email')) {
			if (isEnabledField('phone') && isEnabledField('email')) {
				$rules['from_email'][] = 'required_without:from_phone';
			} else {
				$rules['from_email'][] = 'required';
			}
		}
		
		// Phone
		if (config('settings.sms.phone_verification') == 1) {
			if ($this->filled('phone')) {
				$countryCode = $this->input('country_code', config('country.code'));
				if ($countryCode == 'UK') {
					$countryCode = 'GB';
				}
				$rules['from_phone'][] = 'phone:' . $countryCode;
			}
		}
		if (isEnabledField('phone')) {
			if (isEnabledField('phone') && isEnabledField('email')) {
				$rules['from_phone'][] = 'required_without:from_email';
			} else {
				$rules['from_phone'][] = 'required';
			}
		}
		
		return $rules;
	}
}
