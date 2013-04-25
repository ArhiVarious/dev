<?php

class Cabinet_IndexController extends Zend_Controller_Action {

    public function init() {
        $this->user = Zend_Session::namespaceGet("User");
        $this->view->child_menu = (new Med_Model_Cabinet)->getFirmsByUID($this->user['data']['ds_user_id']);
        /* Initialize action controller here */
    }

    public function indexAction() {
        
    }

    public function reportsAction() {
        
    }

    public function firmAction() {
        $this->view->headScript()->appendFile('/spina/js/cabinet.js');
        $this->view->firm_id = $this->getParam('firm');
    }

}