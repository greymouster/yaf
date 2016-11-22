<?php

class HM_Mcrypt
{
	
	private $_key;
	
	private $_td;
	
	private $_iv;
	
	
	const CIPHER = MCRYPT_DES;
	const MODE = MCRYPT_MODE_ECB;
	const BLOCK_SIZE = 8;
	

	public function __construct($key)
	{
		if (!extension_loaded('mcrypt'))
			die('mcrypt extension not found.');
		
		$this->_key = substr($key, 0, self::BLOCK_SIZE);
		
		$this->_td = mcrypt_module_open(self::CIPHER, '', self::MODE, '');
		$this->_iv = mcrypt_create_iv(mcrypt_get_iv_size(self::CIPHER, self::MODE), MCRYPT_RAND);
		mcrypt_generic_init($this->_td, $this->_key, $this->_iv);
	}

	public function crypt($content)
	{
        return mcrypt_generic($this->_td, $this->_pkcs7Pad($content));
	}
	
	public function decrypt($content)
	{
        return $this->_pkcs7Unpad(mdecrypt_generic($this->_td, $content));
	}

    public function encode($content)
    {
         return base64_encode(mcrypt_generic($this->_td, $this->_pkcs7Pad($content)));
    }
	
    public function decode($content)
    {
         return $this->_pkcs7Unpad(mdecrypt_generic($this->_td, base64_decode($content)));
    }
	
	private function _pkcs7Pad($content)
	{
		$pad = self::BLOCK_SIZE - (strlen($content) % self::BLOCK_SIZE);
		return $content.str_repeat(chr($pad), $pad);
	}
	
	
	private function _pkcs7Unpad($content)
	{
		$pad = ord($content{strlen($content) - 1});
		if ($pad > strlen($content))
			return false;
		if (strspn($content, chr($pad), strlen($content) - $pad) != $pad)
			return false;
		return substr($content, 0, -1 * $pad);
	}
	

	public function __destruct()
	{
		mcrypt_generic_deinit($this->_td);
		mcrypt_module_close($this->_td);
	}
}