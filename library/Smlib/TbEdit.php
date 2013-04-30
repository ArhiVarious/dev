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
    protected $sqlQueries;   // массив sql-запросов на выборку, вставку, обновление, удаления (select, insert, update, delete)
    protected $dbConn;      // соединение с БД
    
    /* конструктор, принимаемые параметры:
     * $dbName - имя базы данных
     * $fieldsDescription - массив описания полей
     * $idFieldName - имя поля, содержащего id
     * $sqlQueryDesc - sql-запрос на получение информации об sql-запросах на выборку, вставку, обновление, удаление
     * $tbTitle - имя таблицы для отображения
     */
    public function __construct($dbName, $fieldsDescription, $idFieldName, $queryDesc, $tbTitle = NULL, $connName = 'default') {
        $this->dbConn = new Smlib_Db_MssqlConn($connName);
        $this->dbName = $dbName;
        $this->idFieldName = $idFieldName;
        $this->fieldsDesc = $fieldsDescription;
        $this->sqlQueries = $queryDesc;
        $this->tbTitle = $tbTitle;
    }
    
    public function getTableTitle(){
        return $this->tbTitle;
    }
    
    public function getTableData(){
        return $this->dbConn->getResultQuery($this->dbName, $this->sqlQueries['select']);
    }
    
    public function getFields(){
        return $this->fieldsDesc;
    }
}

?>
