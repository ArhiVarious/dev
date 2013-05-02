<?php

class Test_IndexController extends Zend_Controller_Action
{

    public function init()
    {
    $this->_helper->layout->setLayout('test');    
    
    }

    public function indexAction()
    {
        // action body
        $con = new Smlib_MssqlConn();
        $sql = "
declare @str_cnt int = (select count(*) from ch_d_1..aaa);
select @str_cnt as str_cnt, * from (
select row_number() over (order by aaa_name asc) as str_num, * from ch_d_1..aaa
) t where str_num between 1 and 100 order by str_num
                ";
        $res = $con->getResultQuery('ch_d_1', $sql);
        if(!$res){
            Zend_Debug::dump($con->getError());
        }
        else{
            Zend_Debug::dump($res);
        }
    }


}

