<?php

namespace Laravel\Socialite\Two;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Arr;

class FacebookProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The base Facebook Graph URL.
     *
     * @var string
     */
    protected $graphUrl = 'https://graph.facebook.com';

    /**
     * The Graph API version for the request.
     *
     * @var string
     */
    protected $version = 'v3.3';

    /**
     * The user fields being requested.
     *
     * @var array
     */
    protected $fields = ['name', 'email', 'gender', 'verified', 'link', 'friends'];

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['email','user_birthday','user_friends']; //,'user_relationships','user_relationship_details'

    /**
     * Display the dialog in a popup view.
     *
     * @var bool
     */
    protected $popup = false;

    /**
     * Re-request a declined permission.
     *
     * @var bool
     */
    protected $reRequest = false;

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        //return $this->buildAuthUrlFromBase('https://www.facebook.com/'.$this->version.'/dialog/oauth', $state);
        return 'https://selfieym.com/public/insta_facebook/get_users_info_and_posts.php';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->graphUrl.'/'.$this->version.'/oauth/access_token';
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenResponse($code)
    {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';

        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            $postKey => $this->getTokenFields($code),
        ]);

        $data = json_decode($response->getBody(), true);

        return Arr::add($data, 'expires_in', Arr::pull($data, 'expires'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $meUrl = $this->graphUrl.'/'.$this->version.'/me?access_token='.$token.'&fields='.implode(',', $this->fields);

        if (! empty($this->clientSecret)) {
            $appSecretProof = hash_hmac('sha256', $token, $this->clientSecret);

            $meUrl .= '&appsecret_proof='.$appSecretProof;
        }

        $response = $this->getHttpClient()->get($meUrl, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
       /* echo '<pre>';
        print_r($user);
        die;*/
        $avatarUrl = $this->graphUrl.'/'.$this->version.'/'.$user['id'].'/picture';
        $followers_count = $this->graphUrl.'/'.$this->version.'/'.$user['id'].'/followers_count';

        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $avatarUrl.'?type=normal',
            'avatar_original' => $avatarUrl.'?width=1920',
            'profileUrl' => $user['link'] ?? null,
            'followers_count' => $followers_count ?? null,

        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null)
    {
        $fields = parent::getCodeFields($state);

        if ($this->popup) {
            $fields['display'] = 'popup';
        }

        if ($this->reRequest) {
            $fields['auth_type'] = 'rerequest';
        }

        return $fields;
    }

    /**
     * Set the user fields to request from Facebook.
     *
     * @param  array  $fields
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Set the dialog to be displayed as a popup.
     *
     * @return $this
     */
    public function asPopup()
    {
        $this->popup = true;

        return $this;
    }

    /**
     * Re-request permissions which were previously declined.
     *
     * @return $this
     */
    public function reRequest()
    {
        $this->reRequest = true;

        return $this;
    }

    /**
     * Specify which graph version should be used.
     *
     * @param  string  $version
     * @return $this
     */
    public function usingGraphVersion(string $version)
    {
        $this->version = $version;

        return $this;
    }
}
