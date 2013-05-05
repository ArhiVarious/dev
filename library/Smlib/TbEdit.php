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
        $this->curPage = 0;
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
        $sqlTemp = "select count(*) as str_cnt from ch_temp..xxx_temp_@uid@";
        $sqlTemp = str_replace('@uid@', $this->uid, $sqlTemp);
        $this->recsTotal = $this->dbConn->getResultQuery($this->dbName, $sqlTemp)[0][str_cnt];
//Zend_Debug::dump($this->recsTotal);      
        if(($this->curPage*$this->recPerPage+1) > $this->recsTotal){
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
//Zend_Debug::dump($sqlTemp);      
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
            
    function __destruct(){
        //$sql = "drop table ch_temp..xxx_temp_@uid";
        //$sql = str_replace('@uid', $uid, $sql);
        //$this->dbConn->getResultQuery('ch_temp', $sql);
    }
}
?>