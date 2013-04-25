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
class Med_Model_Dsmp {

    public function __construct() {
        $this->link = Zend_Registry::get('db');
        return $this;
    }

    public function setPresence($user, $mp, $presence) {
        $sql = "
            UPDATE ds_medical_product
            SET presence_id = ".$presence.",
                ds_mp_presence_tsdate = getdate(),
                                        ds_user_id = " . $user . "
            WHERE ds_medical_product_id = " . $mp . "    
        ";
        return mssql_query($sql, $this->link);
    }

}

?>
