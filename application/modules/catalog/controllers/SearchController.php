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
        $data = $s->where("area_id",$r->getParam("area_id"))->where("host",$_SERVER["HTTP_HOST"])->getByMedicalProductCode($r->getParam('char').'/'.$r->getParam('name'));
        $this->view->data = $data['result']['stores'];
        $this->view->yandex_direct = $data['result']['yandex_direct']['html'];
	$this->view->mp = $data['meta'];
        
        $this->view->headTitle($this->view->mp['medical_product_name_name'] ." - где купить в " . $this->view->domain_data['area_FullName2']);
        Zend_Debug::dump('1111');
        $tbEditor = new Smlib_TbEdit('ch_site',
                array(
                        array(  'name'=>'aaa_id',
                                'title'=>'ИД',
                                'type'=>'int',
                                'isVisible'=>1,
                                'isNeeded'=>1,
                                'isNull'=>0,
                                'isReadOnly'=>0),
                        array(  'name'=>'aaa',
                                'title'=>'Наименование',
                                'type'=>'varchar',
                                'isVisible'=>1,
                                'isNeeded'=>1,
                                'isNull'=>0,
                                'isReadOnly'=>0)
                    ),
                'aaa_id',
                array("select"=>
                        "
                        select
                          cast(aaa_id as int) as aaa_id,
                          aaa_name
                          --into into ch_temp..:uid
                        from
                          ch_site..aaa with(nolock)
                        where 1=1
                        --id and aaa_id = @aaa_id
                        --view1 and aaa_name like 'А%'
                        --view2 and aaa_name like 'Б%'
                        ",
                    "insert"=>
                        "insert into aaa (aaa_name) values (@aaa_name)",
                    "update"=>
                        "update aaa set aaa_name = @aaa_name where aaa_id = @aaa_id",
                    "delete"=>
                        "delete from aaa aaa_id = @aaa_id"
                ),
                'Тестовая таблица');
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