<?php
namespace skygoose\backend_framework\network;

use skygoose\backend_framework\routes\http\exceptions\HttpException;

/**
 * @deprecated Используйте класс VK!
*/
class VKHandler
{
    /**
     * @deprecated Используйте класс VK!
     */
    private static function checkoutConfig() {
        if(!isset(__VK_API_DATA__["APP_ID"])) {
            HttpException::print(500, "Конфигурация VK API не настроена!");
            exit();
        }
    }

    /**
     * @deprecated Используйте класс VK!
     */
    public static function getToken() {
        self::checkoutConfig();

        return (new HttpQueryBuilder(
            "https://oauth.vk.com/access_token?client_id=".
            __VK_API_DATA__['APP_ID']."&redirect_uri=".__VK_API_DATA__['domain'].
            "&code=".__VK_API_DATA__['code']."&client_secret=".__VK_API_DATA__['APP_SECRET'].
            "&scope=".__VK_API_DATA__['scope']
        ))->build() ?: [];
    }


    /**
     * @deprecated Используйте класс VK!
     */
    public static function getUserData () {
        self::checkoutConfig();

        return (new HttpQueryBuilder(
            "https://api.vk.com/method/users.get?fields=photo_200&v=5.131&access_token=" . self::getToken()
        ))->build() ?: [];
    }
}