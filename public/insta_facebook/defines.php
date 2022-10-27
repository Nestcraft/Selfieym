<?php
	session_start();

	define( 'FACEBOOK_APP_ID', '322355403301518' );
	define( 'FACEBOOK_APP_SECRET', 'f6a7d5b3dee7a3b707c05c1a2e8c02d9' );
	define( 'FACEBOOK_REDIRECT_URI', 'https://selfieym.com/public/insta_facebook/obtaining_access_token.php' );
	define( 'ENDPOINT_BASE', 'https://graph.facebook.com/v13.0/' );

	// accessToken
	$accessToken = 'EAAElLjdOIo4BALeeHan5ZC1jXQzBipgQP1eC88NGUhnCQ0gPRGcKzYi8YDKMcCRq9ZBKpPmCmyKOBdJ7qetEpqfmtpUvo0tkunKcOn4JMe6b3L7ylkQnXUAXkPmvVCvpQ2k2SOtA4oe5Uj2Sc97sKVCGOox77wJj9gESO7iAhowwYU1ZCPP1SaFZBV5jCvUd2aj1XV779Vz3rywQHZBMrzWHgcDqZBTDvYgcFZB1L4OZCQZDZD';

	// page id
	$pageId = '100378242682119';

	// instagram business account id
	$instagramAccountId = '17841448295352642';