<?php


namespace SocialiteProviders\Casdoor;


use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Verifier;
use MiladRahimi\Jwt\Cryptography\Keys\RsaPublicKey;
use MiladRahimi\Jwt\Parser;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'CASDOOR';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['read'];

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['url', 'jwt_key'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getUrl().'/login/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->getUrl().'/api/login/oauth/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $signer = new RS256Verifier(new RsaPublicKey($this->getConfig('jwt_key')));
        $parser = new Parser($signer);
        return $parser->parse($token);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        $data = [];
        foreach ($user as $key => $valueObj){
            $data[$key] = $valueObj;
        }
        return (new User())->setRaw($user)->map($data);
    }

    /**
     * @return url of casdoor
     */
    protected function getUrl(){
        return rtrim($this->getConfig('url'),'/');
    }

}
