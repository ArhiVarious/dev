<?php

class Test_IndexController extends Zend_Controller_Action
{

    public function init()
    {
    $this->_helper->layout->setLayout('test');    
    
    }

    public function indexAction()
    {
        $con = new Smlib_MssqlConn();
        $dtz = new DateTimeZone('Europe/Moscow');
        $dd = new DateTime('2000-01-01', $dtz);
        Zend_Debug::dump($dd);
        $sql = "
declare @str_cnt int = (select count(*) from ch_d_1..aaa);
select @str_cnt as str_cnt, * from (
select row_number() over (order by aaa_name asc) as str_num, * from ch_d_1..aaa
) t where aaa_name = @name@ order by str_num
                ";
        Zend_Debug::dump($sql);
        $res = $con->getResultQuery('ch_d_1', $sql, array('@name@'=>$dd));
        if(!$res){
            Zend_Debug::dump($con->getError());
        }
        else{
           Zend_Debug::dump($res);
        }
    }

    public function demoAction()
    {
        $tbEdit = new Smlib_TbEdit();
        $tbEdit->initData(
                'test',
                'ch_d_1',
                array(
                    array('name' => 'aaa_id',
                        'title' => 'ИД',
                        'type' => 'integer',
                        'isVisible' => '1',
                        'isNeeded' => '1',
                        'isNull' => '0',
                        'isReadOnly' => '0',
                        'width' => '50px'),
                    array('name' => 'aaa_name',
                        'title' => 'Название',
                        'type' => 'string',
                        'isVisible' => '1',
                        'isNeeded' => '1',
                        'isNull' => '0',
                        'isReadOnly' => '0',
                        'width' => '500px')
                    ),
                'aaa_id',
                array('select' => 'select
                    cast(aaa_id as int) as aaa_id,
                    aaa_name
                    --into into ch_temp..xxx_temp_@uid@
                    from
                      ch_d_1..aaa with(nolock)
                    where 1=1
                    --id and aaa_id = @aaa_id@
                    --view1 and aaa_name like @usl1@
                    --view2 and aaa_name like @usl2@',
                    'insert'=>'insert into aaa (aaa_name) values (@aaa_name@)',
                    'update'=>'update aaa set aaa_name = @aaa_name where aaa_id = @aaa_id@',
                    'delete'=>'delete from aaa aaa_id = @aaa_id@'),
                array('aaa_name asc'),
                'Тестовая таблица'
                );
        $this->view->tbEdit = $tbEdit;
    }


}



