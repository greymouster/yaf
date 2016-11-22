<?php
/**
 * TZ_Db_Table test
 * 
 * @author vincent <piaoqingbin@maxvox.com.cn>
 * @final 2013-1-29
 */
class TestModel extends TZ_Db_Table
{
	public function __construct()
	{
		parent::__construct('test.yaf_demo_1', 'id');
	}
	
}