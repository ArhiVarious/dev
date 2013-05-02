<?php

class Test_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $con = new Smlib_MssqlConn();
        $sql = "
            select
                cast(aaa_id as int) as aaa_id,
                aaa_name
              from
                ch_site..aaa with(nolock)
              where 1=1
              --id and aaa_id = @aaa_id
              --view1 and aaa_name like 'А%'
              --view2 and aaa_name like 'Б%'
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

