<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Med_Model_Search
 *
 * @author Зуфар
 */
class Med_Model_Search {

    public function __construct() {
        $this->link = Zend_Registry::get('db');
        $this->params = array();
    }

    public function where($k, $v) {
        $this->params[$k] = $v;
        return $this;
    }

    public function getProductByCode($code) {
        $sql = "select medical_product_name_id, medical_product_name_name, medical_product_name_latin from ch_site..f_medical_product_name where medical_product_name_latin = '" . $code . "'";
        mssql_select_db('ch_site', $this->link);
        $query = mssql_query($sql, $this->link);
        return mssql_fetch_assoc($query);
    }

    public function getByMedicalProductCode($code) {
        $data = $this->getProductByCode($code);
        $params = array_merge($this->params, array(
            "medical_product_name_id" => $data["medical_product_name_id"],
            "path" => "/catalog/" . $code . ".aspx"
                )
        );
        return array(
            'meta' => $data,
            'result' => $this->getResultByParams($params)
        );
    }

    public function getResultByParams($arParams, $type = '') {
        foreach ($arParams as $k => $v)
            $sParams .= "\nset @" . $k . " = '" . $v . "';\n";
        return $this->getResult($sParams, $type);
    }

    public function getResult($sParams = '', $type = '') {

        $sql = "
            declare @area_id                 int = 1;
            declare @complex_id              int = 0;
            declare @complex2_id             int = 0;
            declare @medical_product_name_id int = 261;
            declare @medical_product_id      int = 0;
            declare @ds_medical_product_id   int = 0;
            declare @firm_id                 int = 0;
            declare @dd_id                   int = 0;
            declare @new_query               int = 1;
            declare @path                    varchar(250) = '/catalog/a/analgin.aspx';
            declare @brouser_str             varchar(250) = 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)';
            declare @host                    varchar(250) = 'kazan.003ms.ru';
            declare @url                     varchar(250) = 'http://kazan.003ms.ru/catalog/a/analgin.aspx';
            
            select 'yandex_direct' as sql_result_type
            select ch_site_code.dbo.f_yandex_direct(@host, @path, @area_id, @medical_product_name_id) as html
            
            exec ch_site_code..p_inet_search_result
                @area_id,
                @complex_id,
                @complex2_id,
                @medical_product_name_id,
                @medical_product_id,
                @ds_medical_product_id,
                @firm_id,
                @dd_id,
                @new_query,
                @path,
                @brouser_str,
                @host,
                @url; 
        ";
        
        if ($type == '') {
            $data = $this->getMultiRowSearchResultData(mssql_query($sql, $this->link));
        } elseif ($type == 'map') {
            $data = $this->getMultiRowMapResultData(mssql_query($sql, $this->link));
        }

        return $data;
    }

    private function getMultiRowSearchResultData($query) {
        $data = null;
        $type = null;
        do {
            $rows = false;
            while ($row = mssql_fetch_assoc($query)) {
                if ($row['sql_result_type']) {
                    $type = $row['sql_result_type'];
                } else {
                    if ($type == 'stores') {
                        $data['stores'][$row['drugstore_department_id']] = $row;
                    } elseif ($type == 'tovars') {
                        $data['stores'][$row['drugstore_department_id']]['tovars'][$row['ds_medical_product_id']] = $row;
                    } elseif ($type == 'pod_strs') {
                        $data['stores'][$row['drugstore_department_id']]['tovars'][$row['ds_medical_product_id']]['pod_str'] = $row;
                    } elseif ($type == 'yandex_direct') {
                        $data['yandex_direct'] = $row;
                    }
                }
            }
        } while (mssql_next_result($query));
        mssql_free_result($query);
        return $data;
    }

    private function getMultiRowMapResultData($query) {
        $data = null;
        $type = null;
        do {
            $rows = false;
            while ($row = mssql_fetch_assoc($query)) {
                if ($row['sql_result_type']) {
                    $type = $row['sql_result_type'];
                } else {
                    $data[$type][] = $row;
                }
            }
        } while (mssql_next_result($query));
        mssql_free_result($query);
        return $data;
    }

    public function getMapCoordsByProductId($mpid) {
        $sql = "select medical_product_name_id, medical_product_name_name from ch_site.dbo.medical_product_name where medical_product_name_id = " . $mpid . "";
        mssql_select_db('ch_site', $this->link);
        $query = mssql_query($sql, $this->link);
        $data = mssql_fetch_assoc($query);
        $params = array_merge($this->params, array(
            "medical_product_name_id" => $data["medical_product_name_id"],
            "path" => "/catalog/search_map.aspx"
                )
        );
        return array(
            'meta' => $data,
            'result' => $this->getResultByParams($params, $type = 'map')
        );
        return $data;
    }

    public function getArticleByProductCode($code) {
        $data = $this->getProductByCode($code);
        $sql = "
            select 
              iaf.iaf_scode as sort,
              ia_.ia_title,
              cast(ia.inet_article_text as varchar(max)) as ia_text
            from 
              ch_site..inet_article ia with(nolock) 
                left join ch_site..inet_article_format iaf with(nolock) on iaf.inet_article_format_id = ia.inet_article_format_id
                left join ch_site..f_inet_article(" . $data["medical_product_name_id"] . ", 1) ia_ on ia_.inet_article_id = ia.inet_article_id
            where
              ia.medical_product_name_id = " . $data["medical_product_name_id"] . " 
            and 
              iaf.inet_article_group_id = 1
            order by 
              sort asc
        ";
        mssql_select_db('ch_d_1', $this->link);
        $query = mssql_query($sql, $this->link);
        while ($row = mssql_fetch_assoc($query))
            $data['data'][] = $row;
        return $data;
    }
    
    public function getSearchMedicalProductNames($query = ''){
        $sql = "exec ch_site.dbo.p_inet_search_mpn_fast '".explode(':',$_SERVER["HTTP_HOST"])[0]."', '/', '".$query."'";
        mssql_select_db('ch_site', $this->link);
        $query = mssql_query($sql, $this->link);
        while ($row = mssql_fetch_assoc($query))
            $data[]=$row['medical_product_name_iname'];
        return $data;
    }
    
    public function getSearchDataByMpn($s = '', $id=-1)
    {
        $sql = "
            exec dbo.p_inet_search_mpn_new ".$id.",  '".$s."',  '".explode(':',$_SERVER["HTTP_HOST"])[0]."',  ''
        ";
        mssql_select_db('ch_d_1', $this->link);
        $query = mssql_query($sql, $this->link);
        while ($row = mssql_fetch_assoc($query))
            $data[]=$row['href'];
        return $data;
    }
    
    public function getSearchDataForTable($mpn_id,$city)
    {
        $sql = "
            DECLARE @area_id int;
            DECLARE @mpn_id int;
            SET @area_id = ".$city.";
            SET @mpn_id = ".$mpn_id."; 
           -- SET @mpn_id = 33476; --врач стоматолог (подгруппа)
            
            -- определяю, является ли данные товар подгруппой
            DECLARE @ig2_id int;


            SET @ig2_id =
            (
                SELECT mpn.international_group2_id
                FROM ch_d_1..medical_product_name mpn with(nolock)
                LEFT JOIN ch_d_1..international_group2 ig2 with(nolock) ON ig2.international_group2_id = mpn.international_group2_id
                WHERE mpn.medical_product_name_id = @mpn_id
                AND ig2.medical_product_name_id = @mpn_id 
            )
            IF (@ig2_id IS NULL) BEGIN
                SET @ig2_id = 0;
            END
           
            SELECT lhs.id,
                   dd.dd_inet_name,
                   -- название филиала
                   dd.dd_longitude,
                   -- долгота и широта филиала
                   dd.dd_latitude,
                   -- у выбранного complex_id тоже есть долгота и широта и по формуле можно посчитать расстояние м/у центром комплекса и филиалом, отсортировав данные по удаленности от центра комплекса
                   a.area_FullName,
                   -- город
                   lhs.medical_product_name_name,
                   -- название лекарства в нашем справочнике
                   lhs.medical_product_name,
                   -- название дозировки в нашем спрвочнике
                   lhs.lhs_name,
                   -- оригинальное название в аптеке
                   lhs.load_hs_price_min,
                   -- минимальная цена оригинального названия в аптеке (например лежит в аптеке 5 партий все по разным ценам, я здесь показываю мниимальную цену от всех 5 партий)
                   lhs.inet_flag1,
                   -- доставка по городу если = 1
                   lhs.inet_flag2,
                   -- доставка по россии если = 1 (не имеет смысла для услуг)
                   lhs.doctor_fio,
                   -- ФИО врача
                   lhs.doctor_hours,
                   -- часы работа врача
                   lhs.doctor_phone,
                   -- телефон врача
                   lhs.load_hs_date_max,
                   dd.drugstore_department_adress,
                   dd.drugstore_department_tel,
                   dd.drugstore_department_note
            FROM
              ( SELECT *
               FROM ch_site..ds_medical_product_inet_lhs with(nolock,INDEX=ix_a_mpn_dd)
               WHERE @ig2_id = 0
                 AND area_id = @area_id
                 AND medical_product_name_id = @mpn_id
               UNION ALL SELECT *
               FROM ch_site..ds_medical_product_inet_lhs with(nolock,INDEX=ix_a_ig2_dd)
               WHERE @ig2_id <> 0
                 AND area_id = @area_id
                 AND international_group2_id = @ig2_id ) lhs
            LEFT JOIN ch_site..drugstore_department dd ON dd.drugstore_department_id = lhs.drugstore_department_id
            LEFT JOIN ch_site..area a ON a.area_id = lhs.area_id
        ";
        //Zend_Debug::dump($sql);die;
        mssql_select_db('ch_d_1', $this->link);
        $query = mssql_query($sql, $this->link);
        while ($row = mssql_fetch_assoc($query))
            $data[]=$row;
        
        return $data;
    }
}

?>
