<?php
return [
	
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */
	
	/*
	 * Mail providers
	 */
    'mailgun' => [
        'domain'   => null,
        'secret'   => null,
		'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'guzzle' => [
            'verify' => false,
        ],
    ],
	
	'postmark' => [
		'token' => env('POSTMARK_TOKEN', ''),
	],
	
    'ses' => [
        'key'    => null,
        'secret' => null,
        'region' => null,
    ],
	
	'mandrill' => [
		'secret' => null,
		'guzzle' => [
			'verify' => false,
		],
	],
	
    'sparkpost' => [
        'secret' => null,
        'guzzle' => [
            'verify' => false,
        ],
    ],
	
	/*
	 * Social login providers (OAuth)
	 */
    'facebook' => [
        'client_id'     => '322355403301518',
        'client_secret' => 'f6a7d5b3dee7a3b707c05c1a2e8c02d9',
        'redirect'      => 'https://selfieym.com/public/insta_facebook/obtaining_access_token.php',//env('APP_URL') .'/auth/facebook/callback',
    ],
	
	'linkedin' => [
		'client_id'     => null,
		'client_secret' => null,
		'redirect'      => env('APP_URL') . '/auth/linkedin/callback',
	],
	
	'twitter' => [
		'client_id'     => null,
		'client_secret' => null,
		'redirect'      => env('APP_URL') . '/auth/twitter/callback',
	],
	
    'google' => [
        'client_id'     => null,
        'client_secret' => null,
        'redirect'      => env('APP_URL') . '/auth/google/callback',
    ],
    'youtube' => [
        'client_id'     => null,
        'client_secret' => null,
        'redirect'      => env('APP_URL') . '/auth/youtube/callback',
    ],
   'instagram' => [  
     'client_id' => env('INSTAGRAM_CLIENT_ID'),  
     'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),  
     'redirect' => env('INSTAGRAM_REDIRECT_URI'),  
],
	    
	/*
	 * Payment gateways
	 */
    'paypal' => [
        'mode'      => env('PAYPAL_MODE', 'sandbox'),
        'username'  => env('PAYPAL_USERNAME', ''),
        'password'  => env('PAYPAL_PASSWORD', ''),
        'signature' => env('PAYPAL_SIGNATURE', ''),
    ],
	
    'stripe' => [
        'model'  => App\Models\User::class,
        'key'    => env('STRIPE_KEY', ''),
        'secret' => env('STRIPE_SECRET', ''),
		'webhook' => [
			'secret' => env('STRIPE_WEBHOOK_SECRET'),
			'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
		],
    ],
	
    'checkout' => [
        'advanced'        => env('TWOCHECKOUT_ADVANCED', true),
        'mode'            => env('TWOCHECKOUT_MODE', ''),
        'publishable_key' => env('TWOCHECKOUT_PUBLISHABLE_KEY', ''),
        'private_key'     => env('TWOCHECKOUT_PRIVATE_KEY', ''),
        'seller_id'       => env('TWOCHECKOUT_SELLER_ID', ''),
        'secret_word'     => env('TWOCHECKOUT_SECRET_WORD', ''),
    ],
	
    'payu' => [
        'mode'                => env('PAYU_MODE', 'sandbox'),
        'pos_id'              => env('PAYU_POS_ID', ''),
        'second_key'          => env('PAYU_SECOND_KEY', ''),
        'oauth_client_secret' => env('PAYU_OAUTH_CLIENT_SECRET', ''),
    ],
	
	'paystack' => [
		'publicKey'  => env('PAYSTACK_PUBLIC_KEY', ''),
		'secretKey'  => env('PAYSTACK_SECRET_KEY', ''),
		'paymentUrl' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
		'currencies' => env('PAYSTACK_CURRENCIES', 'NGN'), // "NGN" or "NGN,USD" related to your Paystack account configuration. (Optional)
	],
	
	/*
	 * SMS providers
	 */
    'nexmo' => [
        'key'      => env('NEXMO_KEY', ''),
        'secret'   => env('NEXMO_SECRET', ''),
        'sms_from' => env('NEXMO_FROM', ''),
    ],
	
    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID', ''),
        'auth_token'  => env('TWILIO_AUTH_TOKEN', ''),
        'from'        => env('TWILIO_FROM', ''), // optional
    ],
	
	/*
	 * Other
	 */
	'googlemaps' => [
		'key' => null, //-> for Google Map JavaScript & Embeded
	],
	
];
