<?php

class Profbis_Controller_Action_Ajax extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->disableLayout();
        $bootstrap = $this->getInvokeArg('bootstrap');
        $this->blocks = $bootstrap->getOption('blocks');
        $this->ajax = $this->blocks['ajax'] && !$this->getParam('noajax', false);
        if ($this->ajax && !$this->getParam('module')) {
            $request = $this->getRequest();
            $this->view->module = $request->getModuleName();
            $this->view->controller = $request->getControllerName();
            $this->view->action = $request->getActionName();
            $this->view->params = $this->getAllParams();
            echo $this->view->render('ajax_action_load.phtml');
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function preDispatch() {
        if($this->ajax && !$this->getParam('module'))
        {
            $request = $this->getRequest();
            $request->setDispatched(false);   
        }
        else if($this->ajax && !$this->getRequest()->isXmlHttpRequest())
        {
            throw new Zend_Controller_Action_Exception('Error exception isXmlHttpRequest', 404); 
        }
        else if(!$this->ajax && $this->getParam('module'))
            throw new Zend_Controller_Action_Exception('Error exception', 404); 
    }
}