<?php

/**
 * Описание класса TbEdit
 *
 * @автор Artem
 */
class Smlib_TbEdit {
    // данные для таблиц
    protected $dbName;      // имя базы данных
    protected $tbTitle;     // Название таблицы для отображения
    protected $idFieldName; // имя поля с id
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
    protected $dbConn;      // соединение с БД
    protected $preloadSql;  // sql-запрос на выборку из временной таблицы
    protected $uid;         // uid временной таблицы
    protected $order;       // сортировка
    protected $recPerPage;  // количество записей на страницу
    protected $elName;      // имя-префикс вставляемого блока
    
    /* конструктор, принимаемые параметры:
     * $elName - имя-префикс вставляемого блока
     * $dbName - имя базы данных
     * $fieldsDescription - массив описания полей
     * $idFieldName - имя поля, содержащего id
     * $sqlQueryDesc - sql-запрос на получение информации об sql-запросах на выборку, вставку, обновление, удаление
     * $order - массив порядка сортировки
     * $tbTitle - имя таблицы для отображения
     */
    public function __construct($elName, $dbName, $fieldsDescription, $idFieldName, $queryDesc, $order, $tbTitle = NULL, $connName = 'default', $recPerPage = 100) {
        $this->dbConn = new Smlib_MssqlConn($connName);
        $this->dbName = $dbName;
        $this->elName = $elName;
        $this->idFieldName = $idFieldName;
        $this->fieldsDesc = $fieldsDescription;
        $this->sqlQueries = $queryDesc;
        $this->tbTitle = $tbTitle;
        $this->recPerPage = $recPerPage;
        $this->uid = str_replace('-', '_' , $this->dbConn->getResultQuery($this->dbName, 'select convert(nvarchar(50), newid()) as newuid')[0]['newuid']);
        $this->order = $order;
        $sqlTemp = str_replace('@uid@', $this->uid, $this->sqlQueries['select']);
        $sqlTemp = str_replace('--into', '', $sqlTemp);
        $this->dbConn->getResultQuery($this->dbName, $sqlTemp);
    }
    
    public function getTableTitle(){
        return $this->tbTitle;
    }
    
    public function getTableData($recFirst, $recPerPage = 0){
        if($recPerPage > 0){
            $recLast = $recFirst + $recPerPage;
        }
        else{
            $recLast = $recFirst + $this->recPerPage;
        }
        $sqlTemp = "
            declare @str_cnt int = (select count(*) from ch_temp..xxx_temp_@uid@);
            select @str_cnt as str_cnt, * from (
            select row_number() over (order by @order@) as str_num, * from ch_temp..xxx_temp_@uid@
            ) t where str_num between @recFirst@ and @recLast@ order by str_num
            ";
//Zend_Debug::dump($this->uid);        
        $sqlTemp = str_replace('@uid@', $this->uid, $sqlTemp);
        $sqlTemp = str_replace('@order@', implode(', ', $this->order), $sqlTemp);
//Zend_Debug::dump($sqlTemp);      
        return $this->dbConn->getResultQuery($this->dbName, $sqlTemp, array('@recFirst@'=>$recFirst, '@recLast@'=>$recLast));
    }
    
    public function getFields(){
        return $this->fieldsDesc;
    }
    
    public function getElName(){
        return $this->elName;
    }
            
    function __destruct(){
        //$sql = "drop table ch_temp..xxx_temp_@uid";
        //$sql = str_replace('@uid', $uid, $sql);
        //$this->dbConn->getResultQuery('ch_temp', $sql);
    }
}
?>