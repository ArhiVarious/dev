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
        $tbEditor = new Smlib_TbEdit('ch_d_1',
                array(
                        array(  'name'=>'aaa_id',
                                'title'=>'ИД',
                                'type'=>'int',
                                'isVisible'=>1,
                                'isNeeded'=>1,
                                'isNull'=>0,
                                'isReadOnly'=>0),
                        array(  'name'=>'aaa_name',
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
                          --into into ch_temp..xxx_temp_@uid
                        from
                          ch_d_1..aaa with(nolock)
                        where 1=1
                        --id and aaa_id = @aaa_id
                        --view1 and aaa_name like 'А%'
                        --view2 and aaa_name like 'Б%'
                        --order order by @order
                        ",
                    "insert"=>
                        "insert into aaa (aaa_name) values (@aaa_name)",
                    "update"=>
                        "update aaa set aaa_name = @aaa_name where aaa_id = @aaa_id",
                    "delete"=>
                        "delete from aaa aaa_id = @aaa_id"
                ),
                array('aaa_name asc'),
                'Тестовая таблица',
                'mssql2');
        
        $tbEditor->prepareTable();
        $this->view->t = $tbEditor;
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