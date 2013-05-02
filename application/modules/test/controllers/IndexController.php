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


}

