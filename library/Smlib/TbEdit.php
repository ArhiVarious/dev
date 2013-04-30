<?php

/**
 * Описание класса TbEdit
 *
 * @автор Artem
 */
class TbEdit {
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
    protected $sqlQueries;   // массив sql-запросов на выборку, вставку, обновление, удаления (select, insert, update, delete)
    protected $dbConn;      // соединение с БД
    
    /* конструктор, принимаемые параметры:
     * $dbName - имя базы данных
     * $sqlFieldsDescription - sql-запрос на получение информации о полях (см. описание $fieldsDesc)
     * $idFieldName - имя поля, содержащего id
     * $sqlQueryDesc - sql-запрос на получение информации об sql-запросах на выборку, вставку, обновление, удаление
     * $tbTitle - имя таблицы для отображения
     */
    public function __construct($dbName, $sqlFieldsDescription, $idFieldName, $sqlQueryDesc, $tbTitle = NULL) {
        $this->dbConn = new Smlib_Db_MssqlConn();
        $this->dbName = $dbName;
        $this->idFieldName = $idFieldName;
        $this->fieldsDesc = $this->dbConn->getResultQuery($this->dbName, $sqlFieldsDescription);
        $this->sqlQueries = $this->dbConn->getResultQuery($this->dbName, $sqlQueryDesc);
        $this->tbTitle = $tbTitle;
    }
    
    public function getTableTitle(){
        return $this->tbTitle;
    }
    
    public function getTableData(){
        return $this->dbConn->getResultQuery($this->dbName, $this->sqlQueries['qselect']);
    }
    
    public function getFields(){
        return $this->fieldsDesc;
    }
}

?>
