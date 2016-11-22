<?php
/**
 * mysql驱动文件
 *
 * @author vincent <pysery@gmail.com>
 * @final 2012-12-01
 */
class TZ_Db_Driver_Pdo implements TZ_Db_Driver
{
	/**
	 * 连接数据库
	 *
	 * @param	string	$host
	 * @param	string	$port
	 * @param	string	$username
	 * @param	string	$password
	 * @param	string	$default
	 * @param	string 	$charset
	 * @return	resource
	 */
	public function connect($host, $port, $username, $password, $default, $charset)
	{
		$dsn = "mysql:host=".$host.";port=".$port.";dbname=".$default."";
		$connection = new PDO($dsn, $username, $password);
		if (!$connection) {
			return false;
		}
//		if (!empty($default)) {
//			$connection -> select_db($default);
//		}
		if (!empty($charset)) {
			$sql = "SET NAMES {$charset}";
			$connection -> query($sql);	
		}
		return $connection;
	}

	/**
	 * 关闭数据库连接
	 *
	 * @param	string	$connection
	 * @return	int
	 */
	public function close($connection)
	{
		return $this->_connection=null;
	}

	/**
	 * 执行SQL
	 *
	 * @param	string		$sql
	 * @param	resource	$connection
	 * @return	mixed
	 */
	public function query($sql, $connection) 
	{
		$this->_connection = $connection;
		//print_r($this->_connection);die;
		$this->_result = $this->_connection -> query($sql);			
		$this->_result->setFetchMode(PDO::FETCH_ASSOC);
		return $this->_result;
	}

	/**
	 * 获取所有结果集数据
	 *
	 * @param	string	$object
	 * @return	mixed	
	 */
	public function fetchAll($object = false)
	{
		$data = array();
		return $this->_result->fetchAll($object);
	}

	/**
	 * 从结果集中获取一行数据
	 *
	 * @param	int		$index
	 * @param	bool	$object
	 * @return	array
	 */
	public function fetchRow($column = null, $object = false)
	{
		$row=$this->_result->fetch($object);
		if (empty($row))
			return array();

		if (null !== $column) {
			return isset($row[$column]) ? $row[$column] : null;
		} else {
			return $row;
		}
	}

	/**
	 * 返回上一次INSERT操作插入的id
	 *
	 * @return	int
	 */
	public function insertId()
	{
		 return $this->_connection->lastInsertId();	
		
	}

	/**
	 * 返回上一次操作影响记录行数
	 *
	 * @return	int
	 */
	public function affectedRows()
	{
		return $this->_result->rowCount();
	}

	/**
	 * 返回查询结果集行总数，仅对SELECT语句有效
	 *
	 * @return	int
	 */
	public function rowCount()
	{
		return $this->_result->rowCount();
	}

	/**
	 * 本次query的连接实例
	 *
	 * @var	resource
	 */
	private $_connection;
	/**
	 * 本次查询结果集
	 *
	 * @var	resource
	 */
	private $_result;

}
