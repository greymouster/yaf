<?php
class HM_ID
{
     private function randConf($confs)
     {
          if(empty($confs))
          {
               return null;
          }
          $key =  array_rand($confs);
          $conf = $confs[$key];
          return explode(":",$conf);
     }

     private function get($confs)
     {
          if (!extension_loaded('thrift_protocol'))
               throw new Exception('Thrift_protocol extension not found.');
          $conf = $this->randConf($confs);
          if(null === $conf || empty($conf))
          {
               return -1;
          }
          try {
               $socket = new Thrift\Transport\TSocket($conf[0], $conf[1]);
               $transport = new Thrift\Transport\TFramedTransport($socket);
               $protocol = new Thrift\Protocol\TBinaryProtocol($transport);
               Yaf_Loader::import(__DIR__.'/IDStub/generator_service.php');
               $client = new generator_serviceClient($protocol);
               $transport->open();
               $uid = $client->gen_id();
               $transport->close();
               return $uid;
          } catch (Thrift\Exception\TException $tx) {
               return -1;
          }
     }

     public function gen_id()
     {
          $strConfs = Yaf_Registry::get('config')->generators;
          $confs = explode(",",$strConfs);
          $len = count($confs);
          if(0 === $len)
          {
               throw new Exception("核心服务失效");
          }
          $id = -1;
          for($i = 0 ; $i < $len ; $i++)
          {
               $id = $this->get($confs);
               if($id !== -1)
               {
                    break;
               }
          }
          return $id;
     }
}