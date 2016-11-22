
<?php
/**
 * User model
 * 
 * @author vincent
 * @final 2012-12-25
 */
class UserService
{	
	public function getInfo()
	{
		$affectedRows = TZ_Loader::model('User')->getInfo();
		
		return $affectedRows;
	}
	
	public function setPassword($password)
	{
		$this->db->transBegin();
		$sql = "SELECT * FROM test.yaf_demo_2 WHERE id = 1 LIMIT 1";
		$info = $this->db->query($sql)->fetchAll();
		var_dump($info);
		$sql = "UPDATE test.yaf_demo_2 SET password = '{$password}' WHERE id = 124";
		$rows = $this->db->query($sql)->affectedRows();
		var_dump($rows);
		var_dump($this->db->transStatus());
		//$this->db->transRollback();
		if ($this->db->transStatus()) {
			$this->db->commit();
		} else {
			$this->db->rollback();
		}
	}
}