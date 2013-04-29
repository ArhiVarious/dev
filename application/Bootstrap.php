<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    private $modules;
    private $registry;
    
    public function __construct($application) {
        parent::__construct($application);
        $this->registry = new Zend_Registry();
        Zend_Registry::setInstance($this->registry);
    }

    public function _initSrvVars() {
        
        $host_port = explode(':', $_SERVER["HTTP_HOST"]);
        Zend_Registry::set('host', $host_port[0]);
        Zend_Registry::set('port', $host_port[1]);
        
        $aConfig = $this->getOptions();
        ini_set('mssql.charset', 'UTF-8');
        $link = mssql_pconnect($aConfig['mssql']['host'], $aConfig['mssql']['user'], $aConfig['mssql']['pass']);
        Zend_Registry::set('db', $link);
        
        $dbconn = $this->getOption('dbconn');
        Zend_Registry::set('dbconn', $dbconn);
        
//        $d = new Smlib_Db_MssqlConn();
//        $sql = 'select
//  cast(aaa_id as int) as aaa_id,
//  aaa_name
//  /*into*/
//from
//  ch_site..aaa with(nolock)
//where 1=1';
//        $res = $d->getResultQuery('ch_site', $sql);
//        Zend_Debug::dump($res);
        
    }    
    
    public function _initModuleLoaders() {
        $this->bootstrap('Frontcontroller');
        $fc = $this->getResource('Frontcontroller');
        
        $this->modules = $fc->getControllerDirectory();

        foreach ($this->modules AS $module => $dir) {
            $moduleName = strtolower($module);
            $moduleName = str_replace(array('-', '.'), ' ', $moduleName);
            $moduleName = ucwords($moduleName);
            $moduleName = str_replace(' ', '', $moduleName);

            $loader = new Zend_Application_Module_Autoloader(array(
                        'namespace' => $moduleName,
                        'basePath' => realpath($dir . "/../"),
                    ));
        }
        return $loader;
    }

    public function _initRoutes() {
        $this->bootstrap('FrontController');
        $this->_frontController = $this->getResource('FrontController');
        $router = $this->_frontController->getRouter();
    }

    /*public function _initCache() {

        $oCache = $this->bootstrap('cachemanager')
                ->getResource('cachemanager')
                ->getCache('memcached');
        Zend_Registry::set('cache', $oCache);
    }*/
    
    public function _initSession() {
        Zend_Session::start();
    }
}

