<?php
namespace Simpleframework\Helpers;


class Util
{

    public static function redirect($page)
    {
        $str = URLROOT .$page;
        $url = filter_var($str,FILTER_SANITIZE_URL);
        header('location: '.$url);
        exit();
    }

    public static function isPOST()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public static function isGET()
    {
       return  $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    public static function flash($name = '', $message = '', $class = 'alert alert-success')
    {
        if (!empty($name))
        {
            if (!empty($message) && empty($_SESSION[$name]))
            {
                if (!empty($_SESSION[$name]))
                {
                    unset($_SESSION[$name]);
                }

                if (!empty($_SESSION[$name .'_class']))
                {
                    unset($_SESSION[$name.'_class']);
                }

                $_SESSION[$name] = $message;
                $_SESSION[$name.'_class'] = $class;
            }
            elseif(empty($message) && !empty($_SESSION[$name]))
            {
                $class = !empty($_SESSION[$name.'_class']) ? $_SESSION[$name.'_class'] : '';

                echo '<div class="'.$class.'" id="tmpdata">'. $_SESSION[$name];
                echo '<span class="ml-2 float-right pointer deltmpdata"><i class="fas fa-times"></i></span>';
                echo '</div>';
                unset($_SESSION[$name]);
                unset($_SESSION[$name.'_class']);
            }
        }
    }

    public static function tmpdata($name = '', $message = '', $class = 'alert alert-success')
    {
        if (!empty($name))
        {
            if (!empty($message) && empty($_SESSION[$name]))
            {
                if (!empty($_SESSION[$name]))
                {
                    unset($_SESSION[$name]);
                }

                if (!empty($_SESSION[$name .'_class']))
                {
                    unset($_SESSION[$name.'_class']);
                }

                $_SESSION[$name] = $message;
                $_SESSION[$name.'_class'] = $class;
            }
            elseif(empty($message) && !empty($_SESSION[$name]))
            {
                $class = !empty($_SESSION[$name.'_class']) ? $_SESSION[$name.'_class'] : '';

                $str = '<div class="'.$class.'" id="tmpdata">'. $_SESSION[$name] .
                       '<span class="ml-2 float-right pointer deltmpdata"><i class="fas fa-times"></i></span>' .
                       '</div>';
                unset($_SESSION[$name]);
                unset($_SESSION[$name.'_class']);

                return $str;
            }
        }
    }




    private static function sessionOptions()
    {
        return [
            /*'read_and_close' => true,*/
            'sid_length' =>32,
            'sid_bits_per_character' => 6,
            'cookie_httponly' => true,
            'use_cookies' => true,
            'use_only_cookies' => true,
            'use_trans_sid' => false,
            'cookie_lifetime' => 0,
            'use_strict_mode' => true,
            'cookie_samesite' => "Strict"
        ];
        //'cookie_secure' => 1,

    }

    public static function startSession()
    {
        @session_start(self::sessionOptions());
        self:: checkCanerySession();
    }


    private static function checkCanerySession()
    {
        $ip = self::getClientIP();
        $time = time();
        $userAgent = $_SERVER['HTTP_USER_AGENT']  ?? '';
        $userAgent_hash = hash_hmac('sha3-512', $userAgent , 'tJNMBL5Ad0gJQhz1uWSI8SKMbBTS3jcyDG/M20JNcco=');

        if (!isset($_SESSION['canary']))
        {
            session_regenerate_id(true);
            $_SESSION['canary'] = [
                'birth' => $time,
                'IP' => $ip,
                'useragent' => $userAgent_hash
            ];
        }

        if (isset($_SESSION['userlogin']) && ($_SESSION['canary']['IP'] !== $ip || $_SESSION['canary']['useragent'] !== $userAgent_hash))
        {
            session_regenerate_id(true);

            $_SESSION = array();
            $_SESSION['canary'] = [
                'birth' => $time,
                'IP' => $ip,
                'useragent' => $userAgent_hash
            ];
        }

        if ($_SESSION['canary']['birth'] < $time - (60 * 5))
        {
            session_regenerate_id(true);
            $_SESSION['canary']['birth'] = $time;
        }
    }

    public static function destroySession()
    {
        $_SESSION = array();
        session_destroy();
    }

    private static function getClientIP() :string
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif(isset($_SERVER['HTTP_X_FORWARDED']))
        {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        }
        elseif(isset($_SERVER['HTTP_FORWARDED_FOR']))
        {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        elseif(isset($_SERVER['HTTP_FORWARDED']))
        {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        }
        elseif(isset($_SERVER['REMOTE_ADDR']))
        {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            $ipaddress = 'UNKNOWN';
        }

        if (!filter_var($ipaddress, FILTER_VALIDATE_IP))
        {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public static function compareTwoInt($x,$y)
    {
        $a = (int)Sanitize::cleanInt($x);
        $b = (int)Sanitize::cleanInt($y);
        return $a <=> $b;
    }

    public static function checkBlogID()
    {
        $userblogID = $_SESSION['userblogID']  ?? 0;
        $currentblogID =$_SESSION['currentblogID']  ?? -1;
        return $userblogID == $currentblogID;
    }
}
