<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Main
 *
 * @author Зуфар
 */
class Med_Model_Drugstore {
    public function __construct()
    {
        $this->link = Zend_Registry::get('db');
        return $this;
    }
    
    
    public function getCityByDDId($id)
    {
        //mssql_select_db('ch_', $this->link);
        $sql = "
        select 
          area_id 
        from 
          ch_site..complex with(nolock)
        where
          complex_id = (select complex_id from ch_site..drugstore_department with(nolock) where drugstore_department_id = ".$id.")
        ";
        $query = mssql_query($sql, $this->link);
        $data = mssql_fetch_assoc ($query);
        return $data['area_id'];
    }
}

?>
