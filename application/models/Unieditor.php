<?php

/**
 * Описание класса Unieditor
 *
 * @автор Artem
 */
class Med_Model_Unieditor {
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
        //$this->sqlQueries = $this->dbConn->getResultQuery($this->dbName, $sqlQueryDesc);
        $this->tbTitle = $tbTitle;
        $this->view->tbTitle = $this->tbTitle;
    }
    
    public function getDataTable(){
        //$this->view->
    }
    
}

?>
