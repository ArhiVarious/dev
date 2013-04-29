<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cabinet
 *
 * @author Зуфар
 */
class Med_Model_Cabinet {
    public function __construct()
    {
        $this->link = Zend_Registry::get('db');
        return $this;
    }
    
    public function getFirmsByUID($uid)
    {
        /*$oCache = Zend_Registry::get('cache');
        $sCacheId = md5(__CLASS__ . "_" . __FUNCTION__ . "_" . implode("_", func_get_args()));
        if ( ! $oCache->test( $sCacheId ) ) {*/
            mssql_select_db('ch_site', $this->link);
            $sql = "
                select 
                  dd.drugstore_department_id as id,
                  dd.drugstore_department_name as name
                from
                  ch_site..f_ds_user_dd udd with(nolock)
                    left join ch_site..drugstore_department dd with(nolock) on dd.drugstore_department_id = udd.drugstore_department_id
                    left join ch_site..drugstore            d  with(nolock) on d.drugstore_id             = dd.drugstore_id
                where
                  ds_user_id = ". intval($uid) ."
                order by
                  d.drugstore_name,
                  dd.drugstore_department_name
            ";
            $query = mssql_query($sql, $this->link);
            while ($row = mssql_fetch_assoc ($query)) {
                $data[] = $row;
            }
           /* $oCache->save( $data, $sCacheId, array(), 3600 );
        } else {
            $data = $oCache->load( $sCacheId );
        }*/
        
        return $data;
    }
}

?>
