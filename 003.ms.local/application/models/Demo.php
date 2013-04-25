<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Зуфар
 */
class Med_Model_Demo {

    public function __construct() {
        $this->link = Zend_Registry::get('db');
    }

    public function getData() {
        $sql = 'select area_name from ch_d_1..area order by area_name';
        
        mssql_select_db('ch_site', $this->link);
        $l = mssql_query($sql, $this->link);
        while ($row = mssql_fetch_assoc($l)) {

            $data[] = $row;
        }
        
        return $data;
    }
    

}

?>
