<?php

class Editor_EditorController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
        $params = $this->getAllParams();
//Zend_Debug::dump($params);        
        if(!isset($params['name'])){
            die('Не задано имя элемента');
        }
        $tbEdit = Zend_Registry::get($params['name'].'_tbEdit');
        $this->view->tbEdit = $tbEdit;
    }

    public function addAction()
    {
        $params = $this->getAllParams();
Zend_Debug::dump($params);
        if(!isset($params['name'])){
            die('Не задано имя элемента');
        }
        die('erwer');
        //$tbEdit = Zend_Registry::get($params['name'].'_tbEdit');
        //$this->view->tbEdit = $tbEdit;
    }


}



