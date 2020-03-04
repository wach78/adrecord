<?php
namespace Simpleframework\Middleware;
/**
 * Sanitize.php
 * @au
 *
 */

abstract class Sanitize
{

	public static function cleanString($value,$flag = null)
	{
	    if (is_null($flag))
	    {
	       return trim(filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
	    }
	    else
	    {
	        return trim(filter_var($value, FILTER_SANITIZE_STRING,$flag));
	    }
	}

	public static function cleanInt($value)
	{
	    return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}

	public static function cleanFloat($value,$flag = null)
	{
	    if (is_null($flag))
	    {
	        return filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT);
	    }
	    else
	    {
	        return filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT,$flag);
	    }
	}

	public static function cleanEmail($value,$flag = null)
	{
	    return filter_var($value,FILTER_SANITIZE_EMAIL);
	}

	public static function cleanUrl($value)
	{
	    return filter_var($value,FILTER_SANITIZE_URL);
	}

	public static function cleanInputArray($input,$flag = null)
	{
	    if (is_null($flag))
	    {
	        return filter_input_array($input,FILTER_SANITIZE_STRING);
	    }
	    else
	    {
	        return filter_input_array($input,FILTER_SANITIZE_STRING,$flag);
	    }
	}

	public static function cleanOutputHtmlentities($input, $encoding = 'UTF-8')
	{
	    return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
	}

	public static function cleanOutput($input, $encoding = 'UTF-8')
	{
	    return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, $encoding);
	}

	public static function cleanOutputInt($input)
	{
	    return (int)Sanitize::cleanInt($input);
	}

}

