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
class Profbis_Controller_Plugin_InitCountry extends Zend_Controller_Plugin_Abstract {
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $data = (new Med_Model_Main())->getDomainDataByHost($_SERVER["HTTP_HOST"]);
        $view = Zend_Controller_Front::getInstance()
        ->getParam('bootstrap')
        ->getResource('view');
        $view->domain_data = $data;
    }
}

?>
