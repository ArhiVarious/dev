<?php

/**
 * Описание класса TbEdit
 *
 * @автор Artem
 */
class Smlib_TbEdit {
    // данные для таблиц
    protected $fieldsDesc;  /* описание полей
                            *  name - имя поля
                            *  title - название (для отображения)
                            *  type - тип поля
                            *  isVisible (1/0) - видимость
                            *  isNeeded (1/0) - необходимость 
                            *  isNull (1/0) - может ли быть NULL
                            *  isReadOnly (1/0) - только для чтения
                            *  width - ширина
                            */ 
    protected $sqlQueries;  // массив sql-запросов на выборку, вставку, обновление, удаления (select, insert, update, delete)
    protected $dbName;      // имя базы данных
    protected $tbTitle;     // Название таблицы для отображения
    protected $idFieldName; // имя поля с id
    protected $dbConn;      // соединение с БД
    protected $preloadSql;  // sql-запрос на выборку из временной таблицы
    protected $uid;         // uid временной таблицы
    protected $order;       // сортировка
    protected $recPerPage;  // количество записей на страницу
    protected $elName;      // имя-префикс вставляемого блока
    protected $recsTotal;   // количество записей в выборке
    protected $curPage;     // последняя запрошенная страница
    protected $connName;    // имя соединения
    
    public function __construct() {
    }
    
    /* инициализация, принимаемые параметры:
     * $elName - имя-префикс вставляемого блока
     * $dbName - имя базы данных
     * $fieldsDescription - массив описания полей
     * $idFieldName - имя поля, содержащего id
     * $sqlQueryDesc - sql-запрос на получение информации об sql-запросах на выборку, вставку, обновление, удаление
     * $order - массив порядка сортировки
     * $tbTitle - имя таблицы для отображения
     */
    public function initData($elName, $dbName, $fieldsDescription, $idFieldName, $queryDesc, $order, $tbTitle = NULL, $connName = 'default', $recPerPage = 100){
        $this->dbConn = new Smlib_MssqlConn($connName);
        $this->connName = $connName;
        $this->dbName = $dbName;
        $this->elName = $elName;
        $this->idFieldName = $idFieldName;
        $this->fieldsDesc = $fieldsDescription;
        $this->sqlQueries = $queryDesc;
        $this->tbTitle = $tbTitle;
        $this->recPerPage = $recPerPage;
        $res = $this->dbConn->getResultQuery($this->dbName, 'select convert(nvarchar(50), newid()) as newuid');
        $this->uid = str_replace('-', '_' , $res[0]['newuid']);
        $this->order = $order;
        $this->prepareOrderArray();
//Zend_Debug::dump($this->order);      
        $sqlTemp = str_replace('@uid@', $this->uid, $this->sqlQueries['select']);
        $sqlTemp = str_replace('--into', '', $sqlTemp);
        $this->dbConn->getResultQuery($this->dbName, $sqlTemp);
        $sqlTemp = "select count(*) as str_cnt from ch_temp..xxx_temp_@uid@";
        $sqlTemp = str_replace('@uid@', $this->uid, $sqlTemp);
        $this->recsTotal = $this->dbConn->getResultQuery($this->dbName, $sqlTemp)[0][str_cnt];
        $this->curPage = 0;
        $this->createTables();
    }
    
    public function initFromBase($elName, $uid, $connName){
        $this->dbConn = new Smlib_MssqlConn($connName);
        $this->uid = $uid;
        $this->elName = $elName;
        $this->connName = $connName;
        $sql = '
            select * from ch_temp..xxx_temp_params_'.$this->elName.'_@uid@
            ';
        $sql = str_replace('@uid@', $this->uid, $sql);
        $res = $this->dbConn->getResultQuery('ch_temp', $sql);
        $this->dbName = $res[0]['dbName'];
        $this->idFieldName = $res[0]['idFieldName'];
        $this->tbTitle = $res[0]['tbTitle'];
        $this->recPerPage = $res[0]['recPerPage'];
        $this->curPage = $res[0]['curPage'];
        $sql = 'select * from ch_temp..xxx_temp_fields_'.$this->elName.'_@uid@';
        $sql = str_replace('@uid@', $this->uid, $sql);
        $this->fieldsDesc = $this->dbConn->getResultQuery('ch_temp', $sql);
        $sql = 'select * from ch_temp..xxx_temp_queries_'.$this->elName.'_@uid@';
        $sql = str_replace('@uid@', $this->uid, $sql);
        $res = $this->dbConn->getResultQuery('ch_temp', $sql);
        $this->sqlQueries = $res[0];
        $sql = 'select * from ch_temp..xxx_temp_order_'.$this->elName.'_@uid@';
        $sql = str_replace('@uid@', $this->uid, $sql);
        $this->order = $this->dbConn->getResultQuery('ch_temp', $sql);
        $this->prepareOrderArray();
        $sqlTemp = "select count(*) as str_cnt from ch_temp..xxx_temp_@uid@";
        $sqlTemp = str_replace('@uid@', $this->uid, $sqlTemp);
        $this->recsTotal = $this->dbConn->getResultQuery($this->dbName, $sqlTemp)[0][str_cnt];
//Zend_Debug::dump($this->order);      
    }
    
    protected function prepareOrderArray(){
        if(is_array($this->order[0])){
            $neworder = array();
            foreach($this->order as $order){
                $neworder[] = $order['order'];
            }
            $this->order = $neworder;
        }
    }

    protected function createTables(){
        $sql = 'create table ch_temp..xxx_temp_params_'.$this->elName.'_@uid@
            (dbName varchar(255),
            elName varchar(255),
            idFieldName varchar(255),
            tbTitle varchar(255),
            recPerPage int,
            uid varchar(255),
            curPage int)';
        $sql = str_replace('@uid@', $this->uid, $sql);
        $this->dbConn->getResultQuery($this->dbName, $sql);
        $sql = "insert into ch_temp..xxx_temp_params_".$this->elName."_@uid@
            (dbName,
            elName,
            idFieldName,
            tbTitle,
            recPerPage,
            uid,
            curPage)
            values
                ('$this->dbName',
                '$this->elName',
                '$this->idFieldName',
                '$this->tbTitle',
                $this->recPerPage,
                '$this->uid',
                $this->curPage)";
        $sql = str_replace('@uid@', $this->uid, $sql);
        $this->dbConn->getResultQuery($this->dbName, $sql);
        $sql = 'create table ch_temp..xxx_temp_fields_'.$this->elName.'_@uid@
            (name varchar(255),
            title varchar(255),
            type varchar(255),
            isVisible varchar(255),
            isNeeded varchar(255),
            isNull varchar(255),
            isReadOnly varchar(255),
            width varchar(255))';
        $sql = str_replace('@uid@', $this->uid, $sql);
        $this->dbConn->getResultQuery($this->dbName, $sql);
        foreach($this->fieldsDesc as $field){
            $sql = "insert into ch_temp..xxx_temp_fields_".$this->elName."_@uid@
                (name,
                title,
                type,
                isVisible,
                isNeeded,
                isNull,
                isReadOnly,
                width)
                values
                ('".$field['name']."',
                '".$field['title']."',
                '".$field['type']."',
                '".$field['isVisible']."',
                '".$field['isNeeded']."',
                '".$field['isNull']."',
                '".$field['isReadOnly']."',
                '".$field['width']."')";
            $sql = str_replace('@uid@', $this->uid, $sql);
            $this->dbConn->getResultQuery($this->dbName, $sql);
        }
        $sql = 'create table ch_temp..xxx_temp_queries_'.$this->elName.'_@uid@
            ([select] varchar(1024),
            [insert] varchar(1024),
            [update] varchar(1024),
            [delete] varchar(1024))';
        $sql = str_replace('@uid@', $this->uid, $sql);
        $this->dbConn->getResultQuery($this->dbName, $sql);
        $sql = "insert into ch_temp..xxx_temp_queries_".$this->elName."_@uid@
            ([select],
            [insert],
            [update],
            [delete])
            values
            ('".$this->sqlQueries['select']."',
            '".$this->sqlQueries['insert']."',
            '".$this->sqlQueries['update']."',
            '".$this->sqlQueries['delete']."')";
        $sql = str_replace('@uid@', $this->uid, $sql);
        $this->dbConn->getResultQuery($this->dbName, $sql);
        $sql = 'create table ch_temp..xxx_temp_order_'.$this->elName.'_@uid@
            ([order] varchar(255))';
        $sql = str_replace('@uid@', $this->uid, $sql);
        $this->dbConn->getResultQuery($this->dbName, $sql);
        foreach($this->order as $field){
            $sql = "insert into ch_temp..xxx_temp_order_".$this->elName."_@uid@
                ([order])
                values
                ('".$field."')";
            $sql = str_replace('@uid@', $this->uid, $sql);
            $this->dbConn->getResultQuery($this->dbName, $sql);
        }
    }
    
    protected function updateTableData(){
        $sql = "update ch_temp..xxx_temp_params_".$this->elName."_@uid@
            set
            dbName = '$this->dbName',
            elName = '$this->elName',
            idFieldName = '$this->idFieldName',
            tbTitle = '$this->tbTitle',
            recPerPage = $this->recPerPage,
            uid = '$this->uid',
            curPage = $this->curPage
            where
            uid = '$this->uid'";
        $sql = str_replace('@uid@', $this->uid, $sql);
        $this->dbConn->getResultQuery($this->dbName, $sql);
    }

    public function getTableTitle(){
        return $this->tbTitle;
    }
    
    public function getCurPage(){
        return $this->curPage;
    }
    
    public function getTableData(){
        $recFirst = $this->curPage*$this->recPerPage + 1;
        $recLast = $recFirst + $this->recPerPage - 1;
//Zend_Debug::dump($this->recsTotal);      
        if(($recFirst) > $this->recsTotal){
            return NULL;
        }
        $this->curPage++;
        $sqlTemp = "
            select @str_cnt@ as str_cnt, * from (
            select row_number() over (order by @order@) as str_num, * from ch_temp..xxx_temp_@uid@
            ) t where str_num between @recFirst@ and @recLast@ order by str_num
            ";
        $sqlTemp = str_replace('@uid@', $this->uid, $sqlTemp);
        $sqlTemp = str_replace('@order@', implode(', ', $this->order), $sqlTemp);
//Zend_Debug::dump($this->order);      
        $this->updateTableData();
        return $this->dbConn->getResultQuery($this->dbName, $sqlTemp, array('@str_cnt@' => $this->recsTotal, '@recFirst@'=>$recFirst, '@recLast@'=>$recLast));
    }
    
    public function getFields(){
        return $this->fieldsDesc;
    }
    
    public function getElName(){
        return $this->elName;
    }
    
    public function getUID(){
        return $this->uid;
    }
    
    public function getRecsTotal(){
        return $this->recsTotal;
    }
    
    public function getConnName(){
        return $this->connName;
    }
            
    function __destruct(){
        //$sql = "drop table ch_temp..xxx_temp_@uid";
        //$sql = str_replace('@uid', $uid, $sql);
        //$this->dbConn->getResultQuery('ch_temp', $sql);
    }
}
?>