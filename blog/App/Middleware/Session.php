<?php
namespace Simpleframework\Middleware;
class Session
{
    private CONST HTTP_USER_AGENT = 'HTTP_USER_AGENT';
    private CONST IPADR = 'ipadr';
    private const OBSOLETE = 'OBSOLETE';
    private const EXPIRES = 'EXPIRES';
    
    private static function sessionOptions()
    {
      return [
                /*'read_and_close' => true,*/
                'sid_length' =>32,
                'sid_bits_per_character' => 6,
                'cookie_httponly' => true,
                'use_only_cookies' => true,
                'use_trans_sid' => false,
                'cookie_lifetime' => 0,
                'use_strict_mode' => true
            ];   
    }
    static private function preventHijacking()
    {
        if (!isset($_SESSION[self::HTTP_USER_AGENT]) || !isset($_SESSION[self::IPADR]))
        {
           return false;
        }
        else
        {
            $http = ($_SESSION[self::HTTP_USER_AGENT] == hash_hmac('whirlpool',$_SERVER[self::HTTP_USER_AGENT],'secret'));
            $ip = ($_SESSION[self::IPADR] == hash_hmac('whirlpool',self::IPADR,'secret'));
            
            return ($http && $ip);
        }
    }
    
    static private function validateSession()
    {
        if( isset($_SESSION[self::OBSOLETE]) || !isset($_SESSION[self::EXPIRES]) )
        {
            return false;
        }
        if(isset($_SESSION[self::EXPIRES]) && $_SESSION[self::EXPIRES] < time())
        {
            return false;
        }
        
        return true;
    }
    
    public static function basicSession()
    {
        session_start(self::sessionOptions());
    }
    
    public static function sessionStart($name, $limit = 0, $path = '/', $domain = null, $secure = null)
    {
        session_name($name);
        $https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);
        
        session_set_cookie_params($limit, $path, $domain, $https, true);
        session_start(self::sessionOptions());
        
        if(self::validateSession())
        {
            if (!self::preventHijacking())
            {
                $_SESSION = array(); 
                $_SESSION[self::HTTP_USER_AGENT] = hash_hmac('whirlpool',$_SERVER[self::HTTP_USER_AGENT],'secret');
                $_SESSION[self::IPADR] = hash_hmac('whirlpool',self::IPADR,'secret');
                
                self::regenerateSession();
            }
            elseif(rand(1, 100) <= 5)
            {
                self::regenerateSession();
            }
        }
        else 
        {
            $_SESSION = array();
            self::destroySession();
            session_start(self::sessionOptions());
        }
    }
    
    static function regenerateSession()
    {
        
        if(isset($_SESSION[self::OBSOLETE]) && $_SESSION[self::OBSOLETE])
        {
            return false;
        }
        
        $_SESSION[self::OBSOLETE] = true;
        $_SESSION[self::EXPIRES] = time() + 10;
        
        session_regenerate_id(false);
        
        $newSession = session_id();
        session_write_close();
        
        session_id($newSession);
        session_start(self::sessionOptions());
        
        unset($_SESSION[self::OBSOLETE]);
        unset($_SESSION[self::EXPIRES]);
    }
    
    
    
    
    public static function display()
    {
        echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';
    }
    public static function destroySession()
    {
        $_SESSION = array();
        session_destroy();
    }
    
    static function set($key,$value)
    {
        $_SESSION[$key] = $value;
    }
    
    static function get($key, $secundKey = null)
    {
        if (is_null($secundKey))
        {
            if (isset($_SESSION[$key]))
            {
                return $_SESSION[$key];
            }
        }
        else
        {
            if (isset($_SESSION[$key][$secundkey]))
            {
                return $_SESSION[$key][$secundkey];
            }
        }
        
        return false;
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
}


