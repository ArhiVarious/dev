<?php

class Search_ResultController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->view->hide_right = true;
    }

    public function indexAction() {
        $o = new Med_Model_Search();
        $this->view->data = $o->getSearchDataForTable($this->getParam('mpn_id'),$this->getParam('area_id'));
        
    }
}