<?php
class Search_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->headScript()->appendFile('/js/jquery.autocomplete.js');
        $this->view->headScript()->appendFile('/js/main.js');
    }

    public function indexAction()
    {  
        $this->view->international_groups = (new Med_Model_Blocks())->getInternationalGroups(urldecode($_SERVER['REQUEST_URI']),  $this->getParam('mpg_c', -1));
        $str = $this->getParam('s');
        $this->view->search_string = $str;
        $this->view->data = (new Med_Model_Search())->getSearchDataByMpn($str,  $this->getParam('mpg_c', -1));
    }
    
    public function autocompliteAction()
    {
        $r = $this->getRequest();
        $o = new Med_Model_Search();
        $data = $o->getSearchMedicalProductNames($r->getParam('query'));
        $x = array(
            'query'=>$r->getParam('query'), // Оригинальный запрос
            'suggestions'=>$data, // Список подсказок
        );
        $this->_helper->json($x);
    }
}