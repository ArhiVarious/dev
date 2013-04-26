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
        
        
        
      

            
/*  $oCache = Zend_Registry::get('cache');
        $sCacheId = md5(__CLASS__ . "_" . __FUNCTION__ . "_" . implode("_", func_get_args()));
        if ( ! $oCache->test( $sCacheId ) ) {
            */
//Zend_Debug::dump($data);die;
            
       /*     $oCache->save( $data, $sCacheId );
        } else {
            $data = $oCache->load( $sCacheId );
        }*/
        
    }
    
    public function getAdvertBanners($mp,$host)
    {
       /* $oCache = Zend_Registry::get('cache');
        $sCacheId = md5(__CLASS__ . "_" . __FUNCTION__ . "_" . implode("_", func_get_args()));
        if ( ! $oCache->test( $sCacheId ) ) {
            */
                $sql = 
                '
                declare @area_id int;
                declare @firm_id int;
                declare @inet_domen_id int;
                exec ch_site_code.dbo.p_inet_get_host_data "'.$host.'", @inet_domen_id out, @area_id out, @firm_id out
                exec ch_site_code.dbo.p_inet_advert_banner @area_id, "'.$mp.'", 0
                ';
                $query = mssql_query($sql, $this->link);
                while ($row = mssql_fetch_assoc ($query)) {
                    $data[] = $row;
                }
                
         /*       $oCache->save( $data, $sCacheId );
        } else {
            $data = $oCache->load( $sCacheId );
        }*/
        if(count($data)>1)shuffle($data);
        
        return $data;
    }
    
    public function getYandexDirect($area=0,$mp=0,$path='')
    {
        $sql = "select dbo.f_yandex_direct('".explode(':',$_SERVER["HTTP_HOST"])[0]."', '".$path."', ".$area.", ".$mp.") as html";
        mssql_select_db('ch_site_code', $this->link);
        $query = mssql_query($sql, $this->link);
        return mssql_fetch_assoc($query);
    }
    
    public function getHeadMenu()
    {
        $sql = "exec p_inet_menu_top2 '".explode(':',explode(':',$_SERVER["HTTP_HOST"])[0])[0]."'";
        mssql_select_db('ch_site_code_old', $this->link);
        $query = mssql_query($sql, $this->link);
        while($row = mssql_fetch_assoc($query))
            $data[] = $row;
        return $data;
    }
    
    public function getTopMenu($url)
    {
        $sql = "exec dbo.p_inet_menu_top '".explode(':',$_SERVER["HTTP_HOST"])[0]."', '".$url."'";
        mssql_select_db('ch_site_code_old', $this->link);
        $query = mssql_query($sql, $this->link);
        $data = null;
        while($row = mssql_fetch_assoc($query))
            $data[] = $row;
        return $data;
    }
    
    public function getInternationalGroups($path='',$id=-1)
    {
        $sql = "exec ch_site_code.dbo.p_inet_search_mpg_new '003ms.ru', '".$path."', ".$id;
         mssql_select_db('ch_site_code', $this->link);
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
