<?php
/**
 * 存储服务
 * 
 * @author vincent <vincent@747.cn>
 * @final 2013-06-20
 */
class TZ_Storage
{
	/**
	 * @var string
	 */
	private $_baseDir;
	
	/**
	 * @var string
	 */
	private $_staticHost;
	
	/**
	 * 初始化
	 */
	public function __construct($dir = null, $staticHost = null)
	{
		$this->_baseDir = (null === $dir)
			? Yaf_Registry::get('config')->storage->dir : $dir;
		$this->_staticHost = (null === $staticHost)
			? Yaf_Registry::get('config')->storage->static_host : $staticHost;
	}
	
	/**
	 * 保存文件到本地 
	 * 
	 * @param string $file
	 * @param string $filename
	 * @return string
	 */
	public function save($file, $filename)
	{
		$fileHash = md5_file($file);
		$tail = strtolower(substr(strrchr($filename, "."), 1));
		$newFileName = "{$fileHash}.{$tail}";
		
		$newFile = $this->getHashFilePath($newFileName);
		if (!copy($file, $newFile))
			return false;

		return $this->_staticHost.str_replace($this->_baseDir,"",$newFile);
		
	}
	
	/**
	 * 获取哈希存储地址
	 *
	 * @param string $fileName
	 * @return string
	 */
	public function getHashFilePath($fileName)
	{
		$dir = $this->hashDir($this->md5Hash($fileName));
		if (!is_dir($dir))
			mkdir($dir, 0755, true);
		return "{$dir}/{$fileName}";
	}
	
	/**
	* 根据字符串计算哈希值
	 *
	 * @param string $str
	 * @return number
	 */
	 public function md5Hash($str)
	 {
	 	$hash = md5($str);
	 	$hash = $hash[0] | ($hash[1] << 8 ) | ($hash[2] << 16) | ($hash[3] << 24) | ($hash[4] << 32) | ($hash[5] << 40) | ($hash[6] << 48) | ($hash[7] << 56);
	 	return $hash % 701819;
	 }
	
	 /**
	 * 根据哈希值生成目录
	 *
	 * @param unknown_type $num
	 * @param unknown_type $file_num
	 * @param unknown_type $m
	 * @return unknown
	 */
	 public function hashDir($num, $fileNum = 1000, $m = 3)
	 {
	 	$dir = $this->_baseDir;
	 	for ($i=1; $i<$m; $i++) {
	 		$dir .= '/'.round($num / (pow($fileNum, $i)));
	 	}
	 	return $dir;
	 }
}