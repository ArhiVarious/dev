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
class Profbis_Controller_Plugin_InitCity extends Zend_Controller_Plugin_Abstract {
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $data = (new Med_Model_Main())->getCityDataByHost(explode(':',$_SERVER["HTTP_HOST"])[0]);
        $view = Zend_Controller_Front::getInstance()
        ->getParam('bootstrap')
        ->getResource('view');
        $request->setParam("area_id", $data[0]["area_id"]);
        $view->domain_data = $data[0];
    }
}

?>
