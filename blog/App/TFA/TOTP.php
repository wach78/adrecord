<?php
namespace Simpleframework\TFA;

class TOTP
{
	const DEFAULTHASH = "sha512";
	const DEFAULTEXPIRATION = 60 *3;
	const DEFAULTDIGITSNR = 7;

	private $time;
	private $addChecksum;
	private $doubleDigits;
	private $secretKey;
	private $expiration;

	function __construct($secretKey = '', $expiration = 0,$digits = 0)
	{
		$this->doubleDigits = array (0 => "0", 1 => "2", 2 => "4", 3 => "6", 4 => "8", 5 => "1", 6 => "3", 7 => "5", 8 => "7", 9 => "9");
		$this->time = time();
		$this->addChecksum = false;

		$this->setSecretKey($secretKey);
		$this->setExpirationTime($expiration);
		$this->setDigitsNumber($digits);
	}

	public function setSecretKey($secretKey)
	{
		if (empty($secretKey) || $secretKey == '')
		{
			return false;
		}
		else
		{
			$this->secretKey = $secretKey;
			return true;
		}
	}

	public function setExpirationTime($expiration = self::DEFAULTEXPIRATION)
	{
		$expiration = (int)$expiration;


		if ($expiration > 0)
		{
			$this->expiration = $expiration;
			return true;
		}
		else
		{
			$this->expiration = self::DEFAULTEXPIRATION;
			return false;
		}
	}

	public function setDigitsNumber ($digits = self::DEFAULT_DIGITSNR)
	{
		$digits = (int)$digits;
		if ($digits > 0 && $digits <= count($this->doubleDigits))
		{
			$this->codeDigitsNr = $digits;
			return true;
		}
		else
		{
			$this->codeDigitsNr = self::DEFAULTDIGITSNR;
			return false;
		}
	}
	public function addChecksum ($checksum = false)
	{
		if (filter_var($checksum,FILTER_VALIDATE_BOOLEAN))
		{
			$this->addChecksum = $checksum;
			return true;
		}
		else
		{
			return false;
		}

	}

	public function calcChecksum($num, $digits)
	{
		$doubleDigit = true;
		$total = 0;
		while ($digits-- > 0)
		{
			$digit = (int)($num %10);
			$num /= 10;
			if ($doubleDigit)
			{
				$digit = $this->doubleDigits[$digit];
			}
			$total += $digit;
			$doubleDigit = !$doubleDigit;
		}
		return 10 - $total %10;
	}

	private function hmac($data, $hashFunct = self::DEFAULTHASH, $rawOutput = true)
	{

		if (!in_array($hashFunct, hash_algos()))
		{
			$hashFunct = self::DEFAULTHASH;
		}

		return hash_hmac($hashFunct, $data, $this->secretKey, $rawOutput);
	}

	protected function calcOTP($movingFactor)
	{

		$movingFactor = floor($movingFactor);
		$digits = $this->addChecksum ? ($this->codeDigitsNr + 1) : $this->codeDigitsNr;

		$text = array();
		for($i = 7; $i >= 0; $i--)
		{
			$text[] = ($movingFactor & 0xff);
			$movingFactor >>= 8;
		}

		$text = array_reverse($text);
		foreach ($text as $index=>$value)
		{
			$text[$index] = chr($value);
		}

		$text = implode("", $text);
		//$text = implode($text,"");

		$hash = $this->hmac($text);
		$hashLenght = strlen($hash);
		$offset = ord($hash[$hashLenght-1]) & 0xf;

		$hash = str_split($hash);

		foreach ($hash as $index=>$value)
		{
			$hash[$index] = ord($value);
		}

		$binary = ( ($hash[$offset] & 0x7f) << 24) | (($hash[$offset + 1] & 0xff) << 16) | (($hash[$offset + 2] & 0xff) << 8) | ($hash[$offset + 3] & 0xff);

		$otp = $binary % pow(10, $this->codeDigitsNr);
		if ($this->addChecksum)
		{
			$otp = ($otp * 10) + $this->calcChecksum($otp, $this->codeDigitsNr);
		}

		$this->generatedCode = str_pad($otp, $digits, "0", STR_PAD_LEFT);;
		return $this->generatedCode;
	}

	public function generateCode()
	{
		return $this->calcOTP($this->time/$this->expiration);
	}

	public function validateCode($code)
	{

		if ($code == $this->calcOTP($this->time/$this->expiration))
		{
			return true;
		}
		else
		{
			$movingFactor = ($this->time-floor($this->expiration/(2)))/$this->expiration;
			return ($code == $this->calcOTP($movingFactor));
		}
	}
}//class