<?php
class Catalog_DocController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->hide_left = true;
        /* Initialize action controller here */
    }

    public function indexAction()
    {  
        $r = $this->getRequest();
        $s = new Med_Model_Search();
        $data = $s->getArticleByProductCode($r->getParam('char').'/'.$r->getParam('name'));
        $this->view->data = $data;
        $this->view->h1 = $data['medical_product_name_name']." - Инструкция, показания, противопоказания";
        $this->view->headTitle($this->view->h1);
        
        $y = new Med_Model_Blocks();
        $this->view->yandex_direct = $y->getYandexDirect(0, $data['medical_product_name_id'], '/doc/'.$r->getParam('char').'/'.$r->getParam('name').'.aspx');
    }
}