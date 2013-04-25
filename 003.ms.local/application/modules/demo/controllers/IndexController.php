<?php

class Demo_IndexController extends Zend_Controller_Action {

    public function init() {
        $this->user = Zend_Session::namespaceGet("User");
        $this->view->child_menu = (new Med_Model_Cabinet)->getFirmsByUID($this->user['data']['ds_user_id']);
        /* Initialize action controller here */
    }

    public function indexAction() {
        $this->view->demo_data = (new Med_Model_Demo)->getData();
    }


}