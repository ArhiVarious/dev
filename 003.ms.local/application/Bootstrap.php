<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    private $modules;
    
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

    public function _initDb() {
        $registry = new Zend_Registry();
        Zend_Registry::setInstance($registry);
        $aConfig = $this->getOptions();
        ini_set('mssql.charset', 'UTF-8');
        $link = mssql_pconnect($aConfig['mssql']['host'], $aConfig['mssql']['user'], $aConfig['mssql']['pass']);
        Zend_Registry::set('db', $link);
        
        $dbConfig = $this->getOption('dbconn');
        Zend_Registry::set('dbconn', $dbConfig);
        
        $d = new Smlib_Db_MssqlConn();
        $sql = 'select top 1000 * from site_a_mpn';
        $res = $d->getResultQuery('ch_site', $sql);
//        Zend_Debug::dump($res);
        Zend_Debug::dump('PrepareTime = '.$d->getTimePrepareSpend().', QueryTime = '.$d->getTimeQuerySpend().', FetchTime = '.$d->getTimeFetchSpend());
        $res = $d->getResultQuery('ch_site', $sql);
//        Zend_Debug::dump($res);
        Zend_Debug::dump('PrepareTime = '.$d->getTimePrepareSpend().', QueryTime = '.$d->getTimeQuerySpend().', FetchTime = '.$d->getTimeFetchSpend());
        //Zend_Debug::dump('res[0] = '.$res[1][0]['bbb']);
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

