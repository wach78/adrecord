<?php
namespace Simpleframework\Middleware;

abstract class Validate
{
    
    public static function validateBool($value,$flag = null)
    {
        if (is_null($flag))
        {
            return  filter_var($value,FILTER_VALIDATE_BOOLEAN);
        }
        else
        {
            return  filter_var($value,FILTER_VALIDATE_BOOLEAN,$flag);
        }
    }
    
    public static function validateEmail($value,$flag = null)
    {
        if (is_null($flag))
        {
            return  filter_var($value,FILTER_VALIDATE_EMAIL);
        }
        else
        {
            return  filter_var($value,FILTER_VALIDATE_EMAIL,$flag);
        }
    }
    
    public static function validateFloat($value,$flag = null)
    {
        if (is_null($flag))
        {
            return  filter_var($value,FILTER_VALIDATE_FLOAT);
        }
        else
        {
            return  filter_var($value,FILTER_VALIDATE_FLOAT,$flag);
        }
    }
    
    public static function validateInt($value,$flag = null)
    {
        if (is_null($flag))
        {
            return  filter_var($value,FILTER_VALIDATE_INT);
        }
        else
        {
            return  filter_var($value,FILTER_VALIDATE_INT,$flag);
        }
    }
    
    public static function validateIP($value,$flag = null)
    {
        if (is_null($flag))
        {
            return  filter_var($value,FILTER_VALIDATE_IP);
        }
        else
        {
            return  filter_var($value,FILTER_VALIDATE_IP,$flag);
        }
    }
    
    public static function validateURL($value,$flag = null)
    {
        if (is_null($flag))
        {
            return  filter_var($value,FILTER_VALIDATE_URL);
        }
        else
        {
            return  filter_var($value,FILTER_VALIDATE_URL,$flag);
        }
    }
    
    public static function validateMAC($value)
    {
        return  filter_var($value,FILTER_VALIDATE_MAC);
    }
    
    public static function validateRegex($value)
    {
        return  filter_var($value,FILTER_VALIDATE_REGEXP);
    }
    
    public static function validateDomain($value,$flag = null)
    {
        if (is_null($flag))
        {
            return  filter_var($value,FILTER_VALIDATE_DOMAIN);
        }
        else
        {
            return  filter_var($value,FILTER_VALIDATE_DOMAIN,$flag);
        }
    }
    
    
    public static function luhn($value)
    {
        $value =  str_replace('-','',$value);
        
        $len  = strlen($value);
        $mul = 0;
        $prodArr = [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [0, 2, 4, 6, 8, 1, 3, 5, 7, 9]];
        
        $sum = null;
        
        while ($len--)
        {
            $sum += $prodArr[$mul][$value[$len]];
            $mul ^= 1;
        }
        
        return ($sum % 10 === 0 && $sum > 0);
    }
    
    
    private static function checklen($value,$len)
    {
        return (count($value) == $len);
    }
    
    private static function checkExplodeValue($format)
    {
        $retval = '*';
        if (preg_match('/[-]/', $format))
        {
            $retval = '-';
        }
        elseif(preg_match('/[.]/', $format))
        {
            $retval = '.';
        }
        elseif(preg_match('/[\/]/', $format))
        {
            $retval = '/';
        }
        
        return $retval;
    }
    
    public static function validateDate($mydate,$format = 'YYYY-MM-DD')
    {
        $format = strtoupper($format);
        $year = $month = $day = '*';
        
        if ($format == 'YYYY-MM-DD' || $format == 'YYYY/MM/DD' || $format == 'YYYY.MM.DD')
        {
            $separator = self::checkExplodeValue($format);
            $value = explode($separator , $mydate);
            
            if (self::checklen($value,3))
            {
                list($year, $month, $day) =  $value;
            }  
        }
        elseif($format == 'DD-MM-YYYY' || $format == 'DD/MM/YYYY' || $format == 'DD.MM.YYYY')
        {
            $separator = self::checkExplodeValue($format);
            $value = explode($separator , $mydate);
            
            if (self::checklen($value,3))
            {
                list($day, $month, $year) = $value;
            }  
        }
        elseif($format == 'MM-DD-YYYY' || $format == 'MM/DD/YYYY' || $format == 'MM.DD.YYYY')
        {
            $separator = self::checkExplodeValue($format);
            $value = explode($separator , $mydate);
            
            if (self::checklen($value,3))
            {
                list($month, $day, $year) = $value;
            }
        }
       
     
        if (is_numeric($year) && is_numeric($month) && is_numeric($day))
        {
            return checkdate($month,$day,$year);
        }
        
        return false;
    }
    
    public static function zipcode($value)
    {
        $pattern = '/^(S-)?\d{3}\s?\d{2}$/i';
        return static::regex($pattern, $value);
    }
    
    public static function regex($pattern,$value)
    {
        $match = preg_match($pattern,$value);
        if ($match === 1)
        {
            return true;
        }
        
        return false;
    }
}




