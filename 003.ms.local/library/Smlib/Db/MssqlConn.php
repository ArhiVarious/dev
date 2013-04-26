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
    protected $timePrepareSpend;   // время запроса
    protected $timeQuerySpend;   // время запроса
    protected $timeFetchSpend;   // время запроса
    protected $db;          // массив подключений
    
    // конструктор
    public function __construct($connName = 'default') {
        $dbConfig = Zend_Registry::get('dbconn');
        $this->dbHost = $dbConfig[$connName]['host'];
        $this->dbUser = $dbConfig[$connName]['user'];
        $this->dbPass = $dbConfig[$connName]['pass'];
    }
    
    // функции
    public function getHost(){
        return $this->dbHost;
    }

    public function getUser(){
        return $this->dbUser;
    }

    public function getPass(){
        return $this->dbPass;
    }

    public function getTimeFetchSpend(){
        return $this->timeFetchSpend*1000;
    }

    public function getTimePrepareSpend(){
        return $this->timePrepareSpend*1000;
    }

    public function getTimeQuerySpend(){
        return $this->timeQuerySpend*1000;
    }
    
    public function getDbConnection($dbName){
        return $this->db[$dbName];
    }

    public function getResultQuery($dbName, $sqlQuery, $params = NULL){ //$dbName - имя базы данных
        $mtime = microtime(); 
        $mtime = explode(" ",$mtime); 
        $mtime = $mtime[1] + $mtime[0]; 
        $tstart = $mtime; 
        if($this->db[$dbName] == NULL)
        {
            $this->db[$dbName] = Zend_Db::factory('Pdo_Mssql',
                    array(
                        'host'      => $this->dbHost,
                        'username'  => $this->dbUser,
                        'password'  => $this->dbPass,
                        'dbname'    => $dbName
                    ));
        }
        $mtime = microtime(); 
        $mtime = explode(" ",$mtime); 
        $mtime = $mtime[1] + $mtime[0]; 
        $tend = $mtime; 
        $this->timePrepareSpend = ($tend - $tstart);
        $stmt = $this->db[$dbName]->query($sqlQuery, $params);
        $mtime = microtime(); 
        $mtime = explode(" ",$mtime); 
        $mtime = $mtime[1] + $mtime[0]; 
        $tend = $mtime; 
        $this->timeQuerySpend = ($tend - $tstart);
        $res = $stmt->fetchAll();
        $mtime = microtime(); 
        $mtime = explode(" ",$mtime); 
        $mtime = $mtime[1] + $mtime[0]; 
        $tend = $mtime; 
        $this->timeFetchSpend = ($tend - $tstart);
        return $res;
    }
}

?>
