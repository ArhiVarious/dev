<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


class Cabinet_DsmpController extends Zend_Controller_Action {

    public function init()
    {
        $this->user = Zend_Session::namespaceGet("User");
        if ($this->getParam('json')) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }
    
    
    public function setpresenceAction()
    {
        $o = new Med_Model_Dsmp();
        $o->setPresence($this->user['data']['ds_user_id'], $this->getParam('mpid'),(int)!$this->getParam('presence'));
        if ($this->getParam('json'))
            $this->_helper->json(array('data'=>(int)!$this->getParam('presence')?'Есть':'Нет'));
    }
}
?>
