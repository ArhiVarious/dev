<?php
class Catalog_SearchController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {  
        $r = $this->getRequest();
        $s = new Med_Model_Search();
        $data = $s->where("area_id",$r->getParam("area_id"))->where("host",explode(':',$_SERVER["HTTP_HOST"])[0])->getByMedicalProductCode($r->getParam('char').'/'.$r->getParam('name'));
        $this->view->data = $data['result']['stores'];
        $this->view->yandex_direct = $data['result']['yandex_direct']['html'];
	$this->view->mp = $data['meta'];
        
        $this->view->headTitle($this->view->mp['medical_product_name_name'] ." - где купить в " . $this->view->domain_data['area_FullName2']);
    }
    
    public function priceAction()
    {
        
    }
    
    public function mapAction()
    {
        $s = new Med_Model_Search();
        $r = $this->getRequest();
        $data = $s->where("area_id",$r->getParam("area_id"))->where("host",explode(':',$_SERVER["HTTP_HOST"])[0])->getMapCoordsByProductId($this->getRequest()->getParam('mpn'));
        $this->view->data = $data['result'];
	$this->view->mp = $data['meta'];
    }
}