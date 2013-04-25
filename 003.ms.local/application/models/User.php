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
class Med_Model_User {

    public function __construct() {
        $this->link = Zend_Registry::get('db');
        $this->params = array();
    }

    public function where($k, $v) {
        $this->params[$k] = $v;
        return $this;
    }

    public function getParams($arParams) {
        $sParams='';
        foreach ($arParams as $k => $v)
            $sParams .= "\n and " . $k . " = '" . $v . "' \n";
        return $sParams;
    }

    public function getData() {
        $sql = 'SELECT ds_user_id
            ,ds_user_name
            ,ds_user_internal_flag
            ,ds_user_password
            ,ds_user_supplier_email
            ,ds_user_LoadByDD_Flag
            ,ds_user_color_scheme_name
            ,ds_user_mail
            ,dbf_convert_str
            ,ds_user_ftp_user
            ,ds_user_fullname
            ,franchise_id
        FROM ch_site..ds_user WHERE 1=1 ' . $this->getParams($this->params);
        
        mssql_select_db('ch_site', $this->link);
        $l = mssql_query($sql, $this->link);
        while ($row = mssql_fetch_assoc($l)) {

            $data[] = $row;
        }
        
        return $data;
    }
    
    public function editUser($userName,$arParams)
    {
        $sParams="";
        foreach ($arParams as $k => $v)
            $sParams .=  $k . " = '" . $v . "',";
        $sParams=rtrim($sParams,",");
        
        $sql = "UPDATE ds_user SET ".$sParams." WHERE ds_user_name='".$userName."'";
        mssql_select_db('ch_d_1', $this->link);
        mssql_query($sql, $this->link);
       
    }
    
    public function newPassword($sUserName)
    {
    //return substr(md5(uniqid(rand(),true)),1,10);   
        $sql = "SELECT ds_user_password FROM ds_user WHERE ds_user_name ='".$sUserName."'";
        //Zend_Debug::dump($sql);die;
         mssql_select_db('ch_d_1', $this->link);
        $l = mssql_query($sql, $this->link);
        $row = mssql_fetch_assoc($l);
        return $row["ds_user_password"];
    }

}

?>
