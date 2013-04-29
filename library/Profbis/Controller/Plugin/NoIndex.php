<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NoIndex
 *
 * @author Зуфар
 */
class Profbis_Controller_Plugin_NoIndex extends Zend_Controller_Plugin_Abstract {
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('view');
        $model = new Med_Model_Main();
        $s = (bool)$model->isIndexedPage(Zend_Registry::get('host').$request->getRequestUri());
        $view->noIndex = rand(0,1);
    }
}
