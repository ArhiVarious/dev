<?php

class Editor_EditorController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
        $tbEdit = new Smlib_TbEdit();
        $params = $this->getAllParams();
        $tbEdit->initFromBase($params['elName'], $params['uid'], $params['connName']);
        $this->view->tbEdit = $tbEdit;
    }

    public function addAction()
    {
        $this->_helper->layout->disableLayout();
        $tbEdit = new Smlib_TbEdit();
        $params = $this->getAllParams();
        $tbEdit->initFromBase($params['elName'], $params['uid'], $params['connName']);
        $this->view->tbEdit = $tbEdit;
    }


}



