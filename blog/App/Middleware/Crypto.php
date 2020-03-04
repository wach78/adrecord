<?php
namespace Simpleframework\Middleware;
class Crypto
{
	private $key;

	private $s_key;
	private $s_iv;
	private $s_ciphermode;

	private const CIPERMODE = 'aes-256-cbc';

	function __construct()
	{
		$base64key = '4wwTeKZIUlQsSELjWRTBFVQVkc7ZVe6WP5gmwFl4Yz8=';
		$this->key = base64_decode($base64key);
		//$this->key = $this->randomBase64Key();
	}

	public function randomBase64Key($len = 32)
	{
		return base64_encode(openssl_random_pseudo_bytes($len));
	}

	public function base64Decode($data)
	{
		return  base64_decode($data);
	}

	public function base64Encode($data)
	{
		return  base64_encode($data);
	}

	/**
	 * Encypt  a string
	 * @param  string $plaintext
	 * @param  based64 string $key
	 * @return string
	 */

	public function encryptAES256($plaintext, $key = null)
	{
		if (is_null($key))
		{
			$key = $this->key;
		}

		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPERMODE));
		$encrypted = openssl_encrypt($plaintext, self::CIPERMODE, $key, 0, $iv);

		return $this->base64Encode($encrypted . '::' . $iv);
	}
	/* Decrypt encrypted string
	 * $param string $ciphertext
	 * $param string $key
	 */
	public function decryptAES256($ciphertext, $key = null)
	{
		if (is_null($key))
		{
			$key = $this->key;
		}

		list($encrypted_data, $iv) = explode('::', $this->base64Decode($ciphertext), 2);
		return openssl_decrypt($encrypted_data,self::CIPERMODE, $key, 0, $iv);
	}


	/**
	 * Used this when AES with same iv is nedded
	 */
	public function initSameIV($key = null, $iv = null)
	{
	    $this->s_ciphermode = self::CIPERMODE;

	    if (is_null($key))
	    {
	        $this->s_Key = hash('sha256','lkirwf897a22cbbtrm8814z5qqv498j5',true);
	    }
	    else
	    {
	        $this->s_Key = hash('sha256',$key,true);
	    }

	    if (is_null($iv))
	    {
	        $this->s_iv = 'aslbrqortjlacnip';
	    }
	    else
	    {
	        $this->s_iv = $iv;
	    }
	}

	/**
	 * Used this when AES with same iv is nedded
	 * @param string $plaintext
	 * @return string
	 */
	public function encrypt($plaintext)
	{
	    $retvalue = openssl_encrypt($plaintext, $this->s_ciphermode, $this->s_key, 0, $this->s_iv);
	    return base64_encode($retvalue);
	}

	/**
	 * Used this when AES with same iv is nedded
	 * @param string $ciphertext
	 * @return string
	 */
	public function decrypt($ciphertext)
	{
	    $str = base64_decode($ciphertext);
	    return openssl_decrypt($str, $this->s_ciphermode, $this->s_key, 0, $this->s_iv);
	}
}
