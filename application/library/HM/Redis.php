<?php

class HM_Redis
{
     protected $redis = null;
     private $host = "127.0.0.1";
     private $port = 6379;
     private $db = 0;
     
     public function __construct($host,$port,$db)
     {
          $this->host = $host;
          $this->port = $port;
          $this->db = $db;
     }

     public function open()
     {
          if (!extension_loaded('redis'))
          {
               throw new Exception('无法使用内存数据库');
          }
          $redis = new Redis();
          $connection = $redis->connect($this->host, $this->port);
		  if (!$connection)
          {
               throw new Exception('无法连接内存数据库');
          }

          $this->redis = $redis;
          return $redis;

     }

     public function close()
     {
          if (null != $this->redis)
          {
               try
               {
                    $this->redis->close();
               }
               catch(Exception $e)
               {
                    $this->redis = null;
               }
          }
     }

     public function __destruct()
     {
          $this->close();
     }
}
