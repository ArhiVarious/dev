<?php
class Catalog2_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->getRequest()->getParams();
        /* Initialize action controller here */
    }

    public function indexAction()
    {  
        
    }    
    public function testAction()
    {
        $s = new Med_Model_Search();
        Zend_Debug::dump($s->getResult());
    }
    
    public function htmlAction()
    {
        
    }
    
    public function postDispatch() {
        $this->view->headTitle($this->title);
        $this->view->headLink()->appendStylesheet("/template/template2.css");
        parent::postDispatch();
    }
}