<?php

/**
 * bootstrap file
 *
 * @author vincent <vincent@747.cn>
 * @final 2013-5-10
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{

    /**
     * data
     */
    private $_config = null;

    /**
     * config init
     */
    public function _initConfig()
    {
        $this->_config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $this->_config);
    }

    /**
     * loader config
     */
    public function _initLoader()
    {
        $loader = new TZ_Loader;
        Yaf_Registry::set('loader', $loader);
    }

    /**
     * plug config
     */
    public function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        $routerPlugin = new RouterPlugin();
        $dispatcher->registerPlugin($routerPlugin);
    }

    /**
     * view config
     */
    public function _initView(Yaf_Dispatcher $dispatcher)
    {
        defined('STATIC_SERVER') or define('STATIC_SERVER', $this->_config->static->server);
        defined('STATIC_VERSION') or define('STATIC_VERSION', md5(date('Ymd')));
        defined('STATIC_PATH') or define('STATIC_PATH', $this->_config->application->baseUri);
        $dispatcher->disableView();
    }

    /**
     * db config
     */
    public function _initDb()
    {
        //flow_center_db
        $flow_serviceDb = $this->_config->database->xiubao_flow_center_db;
        $flow_serviceMaster = $flow_serviceDb->master->toArray();
        $flow_serviceSlave = !empty($flow_serviceDb->slave) ? $flow_serviceDb->slave->toArray() : null;
        $flow_serviceDb = new TZ_Db($flow_serviceMaster, $flow_serviceSlave, $flow_serviceDb->driver);
        Yaf_Registry::set('xiubao_flow_center_db', $flow_serviceDb);

        //userCenterDb
        $userCenterDb = $this->_config->database->xiubao_user_center_db;
        $userCenterMaster = $userCenterDb->master->toArray();
        $userCenterSlave = !empty($userCenterDb->slave) ? $userCenterDb->slave->toArray() : null;
        $userCenterDb = new TZ_Db($userCenterMaster, $userCenterSlave, $userCenterDb->driver);
        Yaf_Registry::set('xiubao_user_center_db', $userCenterDb);


        $deviceCenterDb = $this->_config->database->xiubao_device_center_db;
        $deviceCenterMaster = $deviceCenterDb->master->toArray();
        $deviceCenterSlave = !empty($deviceCenterDb->slave) ? $deviceCenterDb->slave->toArray() : null;
        $deviceCenterDb = new TZ_Db($deviceCenterMaster, $deviceCenterSlave, $deviceCenterDb->driver);
        Yaf_Registry::set('xiubao_device_center_db', $deviceCenterDb);


        $adDb = $this->_config->database->xiubao_ad_db;
        $adMaster = $adDb->master->toArray();
        $adSlave = !empty($adDb->slave) ? $adDb->slave->toArray() : null;
        $adDb = new TZ_Db($adMaster, $adSlave, $adDb->driver);
        Yaf_Registry::set('xiubao_ad_db', $adDb);


        $msgDb = $this->_config->database->xiubao_msg_db;
        $msgMaster = $msgDb->master->toArray();
        $msgSlave = !empty($msgDb->slave) ? $msgDb->slave->toArray() : null;
        $msgDb = new TZ_Db($msgMaster, $msgSlave, $msgDb->driver);
        Yaf_Registry::set('xiubao_msg_db', $msgDb);


        $xiubao_rebateDb = $this->_config->database->xiubao_rebate_db;
        $xiubao_rebateMaster = $xiubao_rebateDb->master->toArray();
        $xiubao_rebateSlave = !empty($xiubao_rebateDb->slave) ? $xiubao_rebateDb->slave->toArray() : null;
        $xiubao_rebateDb = new TZ_Db($xiubao_rebateMaster, $xiubao_rebateSlave, $xiubao_rebateDb->driver);
        Yaf_Registry::set('xiubao_rebate_db', $xiubao_rebateDb);

    }

    /**
     * Init library
     *
     * @return void
     */
    public function _initLibrary()
    {

    }

}

/**
 * RouterPlugin.php
 */
class RouterPlugin extends Yaf_Plugin_Abstract
{

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

    }

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $view = new TZ_View();
        $view->setCacheEnable(true);
        $view->setScriptPath(APP_PATH . '/application/modules/' . $request->getModuleName() . '/views');
        Yaf_Dispatcher::getInstance()->setView($view);
    }

    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        //过滤一下get 和post 请求的数据　
        $_GET = array_map('TZ_Request::clean', $_GET);
        $_POST = array_map('TZ_Request::clean', $_POST);
    }

    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

    }

}

/**
 * 返回商品数据
 * @return type
 */
function getProductList()
{
    return array(
        1 => '手由宝一台',
        2 => '200元京东店铺代金劵',
        3 => '200元微商城店铺代金劵',
        4 => '100元京东店铺代金劵',
        5 => '100元微商城店铺代金劵',
        6 => '20元微商城店铺代金劵'
    );
}


//tools
function d($params)
{
    echo '<pre>';
    var_dump($params);
    echo '</pre>';
}

function error_404()
{
    die(header('Location:/error/notfound'));
}

//数组转成对象
function array2object($d)
{
    if (is_array($d)) {
        return (object)array_map('array2object', $d);
    } else {
        return $d;
    }
}
