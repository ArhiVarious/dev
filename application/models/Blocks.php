<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Med_Model_Blocks
 *
 * @author Зуфар
 */
class Med_Model_Blocks {
    public function __construct()
    {
        $this->link = Zend_Registry::get('db');
    }
    
    public function getPartners($host)
    {
        
        //ini_set('mssql.charset', 'UTF-8');
        //mssql_connect($config['servername'], $config['username'], $config['password']);
        
        mssql_select_db('ch_site_code', $this->link);
        $sql = 
        '
        declare @area_id int;
        declare @firm_id int;
        declare @inet_domen_id int;
        exec ch_site_code.dbo.p_inet_get_host_data "'.$host.'", @inet_domen_id out, @area_id out, @firm_id out
        exec ch_site_code.dbo.p_inet_advert_partners @inet_domen_id, @area_id, 10
        ';
        $query = mssql_query($sql, $this->link);
        while ($row = mssql_fetch_assoc ($query)) {
            $data[] = $row;
        }
        
        if(count($data)>1)shuffle($data);
        return $data;
                
    }
    
    public function getAdvertBanners($mp)
    {
        $sql = 
        '
        declare @area_id int;
        declare @firm_id int;
        declare @inet_domen_id int;
        exec ch_site_code.dbo.p_inet_get_host_data "'.Zend_Registry::get('host').'", @inet_domen_id out, @area_id out, @firm_id out
        exec ch_site_code.dbo.p_inet_advert_banner @area_id, "'.$mp.'", 0
        ';
        $query = mssql_query($sql, $this->link);
        //Zend_Debug::dump($sql);die;
        
        while ($row = mssql_fetch_assoc ($query)) {
            $data[] = $row;
        }        
        if(count($data)>1)shuffle($data);
        return $data;
    }
    
    public function getYandexDirect($area=0,$mp=0,$path='')
    {
        $sql = "select dbo.f_yandex_direct('".Zend_Registry::get('host')."', '".$path."', ".$area.", ".$mp.") as html";
        mssql_select_db('ch_site_code', $this->link);
        $query = mssql_query($sql, $this->link);
        return mssql_fetch_assoc($query);
    }
    
    public function getHeadMenu()
    {
        $sql = "exec p_inet_menu_top2 '".Zend_Registry::get('host')."'";
        mssql_select_db('ch_site_code_old', $this->link);
        $query = mssql_query($sql, $this->link);
        while($row = mssql_fetch_assoc($query))
            $data[] = $row;
        return $data;
    }
    
    public function getTopMenu($url)
    {
        $sql = "exec ch_site_code_old.dbo.p_inet_menu_top '".Zend_Registry::get('host')."', '".$url."'";
        //Zend_Debug::dump($sql);die;
        $query = mssql_query($sql, $this->link);
        $data = null;
        while($row = mssql_fetch_assoc($query))
            $data[] = $row;
        return $data;
    }
    
    public function getInternationalGroups($path='',$id=-1)
    {
        $sql = "exec ch_site_code.dbo.p_inet_search_mpg_new '003ms.ru', '".$path."', ".$id;
        $query = mssql_query($sql, $this->link);
        $data = null;
        while($row = mssql_fetch_assoc($query))
        {
            $row['active'] = $id==$row['code'];
            $data[] = $row;
        }
        return $data;
    }
}

?>
