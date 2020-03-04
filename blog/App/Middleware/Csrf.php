<?php
namespace Simpleframework\Middleware;
use Simpleframework\Helpers\Util;


Util::startSession();

abstract class Csrf
{
    private const TOKENVALUE = 'tokenvalue';
    static private function random($len)
    {
        return base64_encode(openssl_random_pseudo_bytes($len));
    }

    static private function getToken()
    {
        if(isset($_SESSION[self::TOKENVALUE]))
        {
            return $_SESSION[self::TOKENVALUE];
        }
        else
        {
            $token = hash_hmac('whirlpool', self::random(500),'tsecret'); //TODO const in config for key
            $_SESSION[self::TOKENVALUE] = $token;
            return $token;
        }
    }

    static private function csrfTokenIsVaild($postToken)
    {

        if($postToken)
        {
            $storedToken = $_SESSION[self::TOKENVALUE];
            return hash_equals($storedToken,$postToken);
        }
        else
        {
            return false;
        }
    }

    static public function csrfTokenTag()
    {
        $token = self::getToken();
        return  "<input type=\"hidden\" name=\"csrf_token\" value=\"".$token."\">";
    }

    static public function exitOnCsrfTokenFailure()
    {
        $postToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : -1;
        if(!self::csrfTokenIsVaild($postToken))
        {
            exit("CSRF token validation failed.");
        }
        return true;
    }

    static public function destroyCsrfToken()
    {
        $_SESSION[self::TOKENVALUE] = null;
        unset($_SESSION[self::TOKENVALUE]);
    }
}
