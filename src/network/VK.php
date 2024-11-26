<?php

namespace skygoose\backend_framework\network;

use skygoose\backend_framework\utils\BasicConstants;
use skygoose\backend_framework\utils\Singletone;

class VK extends Singletone implements ISocialNetwork
{
    private static self|null $VK = null;
    private const VK_ACCESS_TOKEN_URL = "https://oauth.vk.com/access_token";
    private const VK_AUTHORIZE_URL = "https://oauth.vk.com/authorize";
    private string $APP_URL = "";
    private string $APP_REDIRECT_URL = "";
    private string $VK_AUTH_PAGE_URL = "";
    private array $scope = ['nohttps', 'groups', 'photos', 'friends', 'offline', 'email'];

    private function __construct(
        private array $config
    )
    {}

    public static function init(array $configuration) : self {
        if(self::$VK == null) {
            self::$VK = new self($configuration);
            self::$VK->APP_URL = BasicConstants::getProtocol() . $_SERVER['HTTP_HOST'] . '/';
        }

        return self::$VK;
    }

    public static function getInstance(): ?static
    {
        if(self::$VK == null) return null;
        return static::$VK;
    }

    public function setRedirectURL(string $url) : void {
        $this->APP_REDIRECT_URL = $url;
        $this->VK_AUTH_PAGE_URL = self::VK_AUTHORIZE_URL .
            '?' . 'client_id=' . $this->config['app_id'] .
            '&redirect_uri=' . $url .
            '&response_type=code&display=page&scope=' . implode(',', $this->scope);
    }

    public function setScope(array $scope): void {
        $this->scope = $scope;
    }
    public function login(): bool
    {

        return true;
    }


    function logout(): bool
    {
        return false;
        // TODO: Implement logout() method.
    }

    function getUserData() {
        session_start();
        $token = $this->getToken()['token'];
        $url = "https://api.vk.com/method/account.getProfileInfo?v=5.199&access_token=$token";

        $query = (new HttpQueryBuilder($url))
        ->build();

        return json_decode($query);
    }

    function getToken(): array|false
    {
        session_start();
        if(!isset($_GET['code'])) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".self::$VK->VK_AUTH_PAGE_URL);
        }
        else {
            $getAccessTokenParams =
                '?client_id=' . $this->config['app_id'] .
                '&client_secret=' . $this->config['secret'] .
                '&code=' . $_GET['code'] .
                '&redirect_uri=' . $this->APP_REDIRECT_URL;

            $query = (new HttpQueryBuilder(self::VK_ACCESS_TOKEN_URL . $getAccessTokenParams))
                ->build();

            $data = json_decode($query);

            if(property_exists($data, 'access_token')) {
                $email = property_exists($data, 'email') ? $data->email : 'null';
                $_SESSION['vk_email'] = $email;

                return [
                    'token' => $data->access_token,
                    'email' => $email
                ];
            }

            return false;
        }

        return false;
    }
}