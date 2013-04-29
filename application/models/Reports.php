<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Reports
 *
 * @author Зуфар
 */
class Med_Model_Reports {

    public function __construct() {
        $this->link = Zend_Registry::get('db');
        return $this;
    }

    public function getDemandAndDirection($drugstore, $area, $user, $page = 0, $np = 10, $order = array(), $arFilter = array()) {
        if (!count($order))
            $order = array(
                'answer_count_dd' => 'desc',
                'query_count' => 'desc',
                'medical_product_name_name' => 'asc',
                'medical_product_name' => 'asc'
            );
        $i = 0;
        $y = count($order);
        $orderBy = '';
        foreach ($order as $key => $type) {
            $i++;
            $orderBy .= ' ' . $key . ' ' . $type . (($y - $i) ? ',' : '');
        }

        $orderBy = $y ? " order by" . $orderBy : "";

        $sFilter = '';
        foreach ($arFilter as $filter) {
            $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . " LIKE ''%" . $filter['value'] . "%''";
        }
        $sFilter = $sFilter ? " where " . $sFilter : "";

        $start_str = (($page - 1) * $np) + 1;
        $end_str = $page * $np;
        $sql = "
                declare @sql varchar(max);
                set @sql =
                '
                select
                /*id*/
                  medical_product_name_name ,
                  medical_product_name ,
                  query_count,
                  answer_count,
                  answer_count_dd,
                  presence_count
                  /*into*/
                from
                  ch_site.dbo.f_repspros($drugstore, $area, $user)
                where
                  query_count <> 0 or
                  answer_count <> 0 or
                  answer_count_dd <> 0
                ';

                select 'data' as sql_result_type
                exec ch_site_code.dbo.p_cashed_sql  @sql, 'select medical_product_name_name,
                  medical_product_name ,
                  query_count,
                  answer_count,
                  answer_count_dd,
                  presence_count from (select row_number() over ( " . $orderBy . ") as str_num, * from /*table*/" . $sFilter . ") t where str_num between " . $start_str . " and " . $end_str . "', 10
                select 'cnt' as sql_result_type
                exec ch_site_code.dbo.p_cashed_sql_page @sql, 'select max(id) as id, count(*) as cnt, sum(query_count) as query_count, sum(answer_count) as answer_count, sum(answer_count_dd) as answer_count_dd from /*table*/" . $sFilter . "', 'order by cnt asc', 10, 1, 2
                
            ";
        //var_dump($sql);die;
        $query = mssql_query($sql, $this->link);

        $type = null;
        do {
            while ($row = mssql_fetch_assoc($query)) {
                if (isset($row['sql_result_type'])) {
                    $type = $row['sql_result_type'];
                } else {
                    $data[$type][] = $row;
                }
            }
        } while (mssql_next_result($query));
        return $data;
    }

    public function getDirectonsOnFirm($drugstore, $area, $user, $page = 0, $np = 10, $order = array(), $arFilter = array()) {
        if (!count($order))
            $order = array(
                'tsdate' => 'desc',
                'medical_product_name_name' => 'asc',
                'medical_product_name' => 'asc'
            );
        $i = 0;
        $y = count($order);
        $orderBy = '';
        foreach ($order as $key => $type) {
            $i++;
            $orderBy .= ' ' . $key . ' ' . $type . (($y - $i) ? ',' : '');
        }

        $orderBy = $y ? " order by" . $orderBy : "";

        $sFilter = '';
        foreach ($arFilter as $filter) {
            if ($filter['name'] == 'date_start') {
                if (($filter['value']) != null) {
                    $filter['name'] = 'tsdate';
                    list($d, $m, $y) = explode('.', $filter['value']);
                    $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . " >= ''" . $y . '-' . $m . '-' . $d . "''";
                }
            } elseif ($filter['name'] == 'date_end') {
                if (($filter['value']) != null) {
                    $filter['name'] = 'tsdate';
                    list($d, $m, $y) = explode('.', $filter['value']);
                    $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . " <= cast(''" . $y . '-' . $m . '-' . $d . "'' as datetime)+1";
                }
            }
            else
                $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . " LIKE ''%" . $filter['value'] . "%''";
        }
        $sFilter = $sFilter ? " where " . $sFilter : "";
        //var_dump($sFilter);die;
        $start_str = (($page - 1) * $np) + 1;
        $end_str = $page * $np;

        $sql = "exec ch_site_code..p_report_napr_reestr $user, $drugstore, '$sFilter', '$orderBy', $start_str, $end_str"; 
        //var_dump($sql); die;

        $query = mssql_query($sql, $this->link);

        $type = null;
        do {
            while ($row = mssql_fetch_assoc($query)) {
                if (isset($row['sql_result_type'])) {
                    $type = $row['sql_result_type'];
                } else {
                    $data[$type][] = $row;
                }
            }
        } while (mssql_next_result($query));
        return $data;
    }

    public function getPresence($drugstore, $area, $user, $page = 0, $np = 10, $order = array(), $arFilter = array()) {
        if (!count($order))
            $order = array(
                'presence_id' => 'asc',
                'medical_product_name_name' => 'asc',
                'medical_product_name' => 'asc'
            );
        $i = 0;
        $y = count($order);
        $orderBy = '';
        foreach ($order as $key => $type) {
            $i++;
            $orderBy .= ' ' . $key . ' ' . $type . (($y - $i) ? ',' : '');
        }

        $orderBy = $y ? " order by" . $orderBy : "";

        $sFilter = '';
        foreach ($arFilter as $filter) {
            $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . " LIKE '%" . $filter['value'] . "%'";
        }
        $sFilter = $sFilter ? " and " . $sFilter : "";
        
        $start_str = (($page - 1) * $np) + 1;
        $end_str = $page * $np;
        $sql = "

select 'data' as sql_result_type

select
  ds_medical_product_id,
  presence_id,
  convert(varchar,ds_mp_presence_tsdate,104) as ds_mp_presence_tsdate,
  medical_product_name_name,
  medical_product_name,
  ds_medical_product_price,
  doctor_hours,
  doctor_fio,
  doctor_phone,
  [Кнопка_записать]
from
  (select 
     ROW_NUMBER() over( " . $orderBy . ") str_num ,*
   from
     (select
        ds_mp.ds_medical_product_id,
        case when ds_mp.presence_id = 1 then 1 else 0 end as presence_id,
        ds_mp.ds_mp_presence_tsdate,
        mp.medical_product_name_name,
        medical_product_name,
        isnull(ds_medical_product_price, '0') ds_medical_product_price,
        doctor_hours,
        doctor_fio,
        doctor_phone,
        '' [Кнопка_записать]
      from
        ch_d_1.v_medical_product mp WITH (NOLOCK) 
          left  join ch_d_1.dbo.medical_product_name mpn   WITH (NOLOCK) on mpn.medical_product_name_id   = mp.medical_product_name_id
          left  join ch_d_1.dbo.international_group  ig    WITH (NOLOCK) on ig.international_group_id     = mpn.international_group_id
          left  join ch_d_1.dbo.ds_medical_product   ds_mp WITH (NOLOCK) on ds_mp.medical_product_id      = mp.medical_product_id and 
                                                                            ds_mp.drugstore_department_id = $drugstore
          left  join ch_d_1.dbo.drugstore_department dd    WITH (NOLOCK) on dd.drugstore_department_id    = $drugstore
          left  join ch_d_1.dbo.drugstore            d     WITH (NOLOCK) on d.drugstore_id                = dd.drugstore_id
          inner join ch_d_1.dbo.drugstore_ig         dig   WITH (NOLOCK) on dig.drugstore_id              = d.drugstore_id and
                                                                            dig.international_group_id    = ig.international_group_id
      where
        (d.drugstore_code = 1 and not(ds_mp.presence_id is null) or d.drugstore_code = 2) and dd.drugstore_department_id in (select drugstore_department_id from ch_d_1.dbo.f_dd_by_user($user)) " . $sFilter . ") t1 ) t2 where str_num between " . $start_str . " and " . $end_str . "
                      
      select 'cnt' as sql_result_type
      select count(*) as cnt
      from
        ch_d_1.dbo.v_medical_product mp WITH (NOLOCK) 
          left  join ch_d_1.dbo.medical_product_name mpn   WITH (NOLOCK) on mpn.medical_product_name_id   = mp.medical_product_name_id
          left  join ch_d_1.dbo.international_group  ig    WITH (NOLOCK) on ig.international_group_id     = mpn.international_group_id
          left  join ch_d_1.dbo.ds_medical_product   ds_mp WITH (NOLOCK) on ds_mp.medical_product_id      = mp.medical_product_id and 
                                                                            ds_mp.drugstore_department_id = $drugstore
          left  join ch_d_1.dbo.drugstore_department dd    WITH (NOLOCK) on dd.drugstore_department_id    = $drugstore
          left  join ch_d_1.dbo.drugstore            d     WITH (NOLOCK) on d.drugstore_id                = dd.drugstore_id
          inner join ch_d_1.dbo.drugstore_ig         dig   WITH (NOLOCK) on dig.drugstore_id              = d.drugstore_id and
                                                                            dig.international_group_id    = ig.international_group_id
      where
        (d.drugstore_code = 1 and not(ds_mp.presence_id is null) or d.drugstore_code = 2) and dd.drugstore_department_id in (select drugstore_department_id from ch_d_1.dbo.f_dd_by_user($user)) " . $sFilter . "
";
        //var_dump($sql);die;
        $query = mssql_query($sql, $this->link);

        $type = null;
        do {
            while ($row = mssql_fetch_assoc($query)) {
                if (isset($row['sql_result_type'])) {
                    $type = $row['sql_result_type'];
                } else {
                    $data[$type][] = $row;
                }
            }
        } while (mssql_next_result($query));
        return $data;
    }

    public function getOuts($drugstore, $area, $user, $page = 0, $np = 10, $order = array(), $arFilter = array()) {
        if (!count($order))
            $order = array(
                'presence_val' => 'desc',
                'query_count' => 'desc',
                'medical_product_name_name' => 'asc',
                'medical_product_name' => 'asc'
            );
        if (!count($arFilter))
            $arFilter = array(
                array(
                    'name' => 'presence_val',
                    'value' => 'city'
                )
            );
        $i = 0;
        $y = count($order);
        $orderBy = '';
        foreach ($order as $key => $type) {
            $i++;
            $orderBy .= ' ' . $key . ' ' . $type . (($y - $i) ? ',' : '');
        }

        $orderBy = $y ? " order by" . $orderBy : "";

        $sFilter = '';
        foreach ($arFilter as $filter) {
            if ($filter['name'] == 'query_count') {
                switch ($filter['value']) {
                    case '1':
                        $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . " > 0";
                        break;
                    case '2':
                        $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . " = 0";
                        break;
                }
            } elseif ($filter['name'] == 'presence_val') {
                switch ($filter['value']) {
                    case 'district':
                        $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . "_c = 0";
                        break;
                    case 'drugstore':
                        $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . "_dd = 0";
                        break;
                    default:
                        $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . " = 0";
                        break;
                }
            }
            else
                $sFilter .= ($sFilter ? " AND " : " ") . $filter['name'] . " LIKE ''%" . $filter['value'] . "%''";
        }
        $sFilter = $sFilter ? " where " . $sFilter : "";
        $start_str = (($page - 1) * $np) + 1;
        $end_str = $page * $np;
        
        $sql = "exec ch_site_code..p_report_napr_reestr $user, $drugstore, '$filter', '$orderBy', $start_str, $end_str"; var_dump($sql);die;
        
        
        $query = mssql_query($sql, $this->link);

        $type = null;
        do {
            while ($row = mssql_fetch_assoc($query)) {
                if (isset($row['sql_result_type'])) {
                    $type = $row['sql_result_type'];
                } else {
                    $data[$type][] = $row;
                }
            }
        } while (mssql_next_result($query));
        return $data;
    }

}

?>
