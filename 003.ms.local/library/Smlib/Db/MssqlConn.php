<?php

/**
 * Для корректной работы необходимо подготовить в реестре
 *       $dbConfig = $this->getOption('dbconn');
 *       Zend_Registry::set('dbconn', $dbConfig);
 *  
 * 
 * Описание класса Smlib_Db_MssqlConn
 * 
 * Класс реализует функцию getResultQuery, которая принимает параметризированные запросы вида:
 * select :field from :database where :wh1 = :con1
 * 
 * вторым параметром задается массив параметров:
 * array(':field'=>'id', ':database'=>'ch_site', ':wh1'=>'id', ':con1'=>$id)
 * 
 * функция возвращает
 *
 * @автор Artem
 */
class Smlib_Db_MssqlConn {
    // данные
    protected $dbHost;      // хост базы данных
    protected $dbUser;      // пользователь  базы данных
    protected $dbPass;      // пароль пользователя
    protected $dbName;      // имя базы данных
    protected $timeSpend;   // время запроса
    //protected $db[];        
    
    
    // конструктор
    public function __construct($connName = 'default') {
        $dbConfig = Zend_Registry::get('dbconn');
        $this->dbHost = $dbConfig[$connName]['host'];
        $this->dbUser = $dbConfig[$connName]['user'];
        $this->dbPass = $dbConfig[$connName]['pass'];
    }
    
    // функции
    public function getResultQuery($dbName, $sqlQuery, $params = NULL){
        $this->dbName = $dbName;
        $db = Zend_Db::factory('Pdo_Mssql',
                array(
                    'host'      => $this->dbHost,
                    'username'  => $this->dbUser,
                    'password'  => $this->dbPass,
                    'dbname'    => $this->dbName
                ));
        $stmt = $db->query($sqlQuery, $params);
        $res = $stmt->fetchAll();
        return $res;
    }
}

?>
