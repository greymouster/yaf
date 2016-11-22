<?php
/**
 * 参数验证类
 * 
 * @author vincent <vincent@747.cn>
 * @final 2013-06-03
 */
class TZ_Validator
{	
	/**
	 * 验证方法，必须为空
	 * 
	 * @param string $name
	 * @param string $type
	 * @param array $rules
	 */
	static public function check($name, $type = 'get', $rules = false)
	{
		if (empty($name)) 
			throw new Exception('param name not found.');
			
		$params = self::_getParams($type);
		if (!isset($params[$name]))
			TZ_Response::error(400, "缺少参数{$name}");
			
		if ($params[$name] == '')
			TZ_Response::error(401, "参数{$name}不能为空");
		
		$param = $params[$name];
		
		if (false !== $rules) {
			if (is_string($rules)) {
				if (!self::_valid($param, $rules))
					TZ_Response::error(402, "参数{$name}验证失败");
			} else if (is_array($rules)) {
				foreach ($rules as $op => $validValue) {
					if (!self::_valid($param, $op, $validValue))
						TZ_Response::error(402, "参数{$name}验证失败");
				}
			} else {
				throw new Exception('rule type error.');
			}
		}
		
		return self::clean($param);
	}
	
	/**
	 * 获取参数集合
	 * 
	 * @param string $paramType
	 * @return mixed
	 */
	static private function _getParams($paramType)
	{
		return $paramType == 'get' ? $_GET : $_POST;
	}
	
	/**
	 * 条件验证
	 * 
	 * @param mixed $param
	 * @param string $op
	 * @param mixed $value
	 * @return void
	 */
	static private function _valid($param, $op, $validValue = false)
	{
		$status = false;
		switch ($op) {
			case 'eq':
				$status = ($param === $validValue);
				break;
				
			case 'in':
				$status = is_array($validValue) && in_array($param, $validValue);
				break;
				
			case 'number':
				$status = is_numeric($param);
				break;
				
			case 'reg':
				$status = preg_match_all($validValue, $param);
				break;
				
			default:
				throw new Exception('op undefined.');
		}
		
		return $status;
	}
	
	/**
	 * 安全过滤
	 * 
	 * @param string $str
	 * @return string
	 */
	static public function clean($str)
	{
		return addslashes(self::_xssClean($str));
	}
	
	/**
	 * 去掉js和html
	 * 
	 * @param string $str
	 * @return string
	 */
	static private function _xssClean($str)
	{
		//$str = trim($str);
		if (strlen($str) <= 0)
			return $str;
		return @preg_replace(self::$_search, self::$_replace, $str);
	}
	
	
	/**
	 * rules
	 * 
	 * @var string
	 */
	static private $_op = array(
		'eq', 'in', 'number', 'reg'
	);
	
	/**
	 * seperator
	 * 
	 * @var string
	 */
	static private $_sep = ':';
	
	/**
	 * js && html reg
	 * 
	 * @var array
	 */
	static private 	$_search = array(
			"'<script[^>]*?>.*?</script>'si",  			// 去掉 javascript
			"'<[\/\!]*?[^<>]*?>'si",          			// 去掉 HTML 标记
			"'([\r\n])[\s]+'",                			// 去掉空白字符
			"'&(quot|#34);'i",                			// 替换 HTML 实体
			"'&(amp|#38);'i",
			"'&(lt|#60);'i",
			"'&(gt|#62);'i",
			"'&(nbsp|#160);'i"
	);
	
	/**
	 * replace reg
	 * 
	 * @var array
	 */
	static private $_replace = array(				    // 作为 PHP 代码运行
			'',
			'',
			"\\1",
			"\"",
			"&",
			"<",
			">",
			''
	);
}