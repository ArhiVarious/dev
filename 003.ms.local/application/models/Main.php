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
class Med_Model_Main {
    public function __construct()
    {
        $this->link = Zend_Registry::get('db');
        return $this;
    }
    
    
    public function getCityDataByHost($host)
    {
        mssql_select_db('ch_site', $this->link);
        $sql = "select 
                    a.area_id, a.area_FullName2, b.inet_domen_counter_code, b.inet_domen_counter_code2, b.inet_domen_counter_logo 
                from 
                    area a with(nolock) 
                inner join inet_domen b on a.inet_domen_id = b.inet_domen_id
                where '".$host."' like a.area_InetName + '.%'";
        $query = mssql_query($sql, $this->link);
        while ($row = mssql_fetch_assoc ($query)) {
            $data[] = $row;
        }
        return $data;
    }
    
    public function getDomainDataByHost($host)
    {
        $sql = "
            select 
                inet_domen_name, inet_domen_counter_code, inet_domen_counter_code2, inet_domen_counter_logo 
            from 
                inet_domen 
            where inet_domen_city_flag = cast(1 as bit) and '".$host."' like '%' + inet_domen_name2
        ";
        $query = mssql_query($sql, $this->link);
        return mssql_fetch_assoc ($query);
    }
    
    public function isIndexedPage($page)
    {
        $sql = "
            SELECT inet_sitemap_id FROM ch_site.dbo.inet_sitemap where page = '".$page."'
        ";
        $query = mssql_query($sql, $this->link);
        return mssql_fetch_assoc ($query);
    }
}

?>
