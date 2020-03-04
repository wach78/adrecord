<?php
namespace Simpleframework\Helpers;

use Exception;

class Json
{
    public static function toJSON($str)
    {
        try {

            if (! function_exists( 'json_encode' ))
            {
                throw new Exception( 'Missing a json encoder for data so we are breaking this.' );
            }
            else
            {
                return self::safe_json_encode( $str );
            }
        } catch( Exception $ex )
        {
            echo 'Error with JSON conversion, ' . $ex->getMessage(); 
        }
        return false;
    }

    /**
     * http://stackoverflow.com/questions/9098507/why-is-this-php-call-to-json-encode-silently-failing-inability-to-handle-singl
     *
     * @param  $value
     */
   public static function safe_json_encode($value)
    {

        $encoded = json_encode( $value );

        switch(json_last_error()) {
            case JSON_ERROR_NONE :
                return $encoded;
            case JSON_ERROR_DEPTH :
                return 'JSON Error: Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_STATE_MISMATCH :
                return 'JSON Error: Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_CTRL_CHAR :
                return 'JSON Error: Unexpected control character found';
            case JSON_ERROR_SYNTAX :
                return 'JSON Error: Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_UTF8 :
                $clean = self::utf8ize( $value );
                return self::safe_json_encode( $clean );
            default :
                return 'JSON Error: Unknown error'; // or trigger_error() or throw new  Exception();
        }
    }

    public static function utf8ize($mixed)
    {
        if (is_array( $mixed ))
        {
            foreach ( $mixed as $key => $value )
            {
                $mixed[$key] = utf8ize( $value );
            }
        }
        elseif (is_string( $mixed ))
        {
            return utf8_encode( $mixed );
        }
        return $mixed;
    }
}