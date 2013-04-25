<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InitCity
 *
 * @author Зуфар
 */
class Profbis_Controller_Plugin_InitRoutePlugins extends Zend_Controller_Plugin_Abstract {
    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        // Register router plugins 
        $front = Zend_Controller_Front::getInstance();
        $options = $front->getParam('bootstrap')->getOption("resources");
        $routeName = $front->getRouter()->getCurrentRouteName();
        $router = $options["router"]["routes"];
        foreach(explode("-", $routeName) as $k)
        {
            if( isset($router["params"]["plugin"]))
                $front->registerPlugin(new $router["params"]["plugin"]);
            $router = isset($router[$k])?$router[$k]:isset($router["chains"][$k]);
        }
    }
}

?>
