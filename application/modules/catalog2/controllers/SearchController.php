<?php
class Catalog2_SearchController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {  
        $r = $this->getRequest();
        $s = new Med_Model_Search();
        $data = $s->where("area_id",$r->getParam("area_id"))->where("host",$_SERVER["HTTP_HOST"])->getByMedicalProductCode($r->getParam('char').'/'.$r->getParam('name'));;
        $this->view->data = $data['result'];
	$this->view->mp = $data['meta'];
    }
    
    public function priceAction()
    {
        
    }
    
    public function mapAction()
    {
        $s = new Med_Model_Search();
        $r = $this->getRequest();
        $data = $s->where("area_id",$r->getParam("area_id"))->where("host",$_SERVER["HTTP_HOST"])->getMapCoordsByProductId($this->getRequest()->getParam('mpn'));
        $this->view->data = $data['result'];
	$this->view->mp = $data['meta'];
    }
}