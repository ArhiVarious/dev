<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Stats
 *
 * @author Зуфар
 */
class Med_Model_Stats {
    
    public function __construct()
    {
        $this->link = Zend_Registry::get('db');
    }
    
    public function getStatByHost()
    {
        $sql = 'exec dbo.p_inet_city_stat_sq "003ms.ru"';
        mssql_select_db('ch_d_1', $this->link);
        $query = mssql_query($sql, $this->link);
        
        while ($row = mssql_fetch_assoc ($query)) {
            $rows .= $row['a'];
        }
        echo '<table>'.$rows.'</table';
    }
}

?>
