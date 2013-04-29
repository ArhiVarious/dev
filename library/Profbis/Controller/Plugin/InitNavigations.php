<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InitNavigations
 *
 * @author Зуфар
 */
class Profbis_Controller_Plugin_InitNavigations extends Zend_Controller_Plugin_Abstract {
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $model = new Med_Model_Blocks();
        $top = $model->getTopMenu($request->getRequestUri());
        $head = $model->getHeadMenu();
        $view = Zend_Controller_Front::getInstance()
        ->getParam('bootstrap')
        ->getResource('view');
        $view->top_menu = $top;
        $view->head_menu = $head;
    }
}
