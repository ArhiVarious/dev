<?php

/**
 * Для корректной работы необходимо подготовить в Bootstrap реестр:
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
    protected $dbHost;              // хост базы данных
    protected $dbUser;              // пользователь  базы данных
    protected $dbPass;              // пароль пользователя
    protected $timePrepareSpend;    // время запроса
    protected $timeQuerySpend;      // время запроса
    protected $timeFetchSpend;      // время запроса
    protected $db;                  // массив подключений
    
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
    
    protected function getMicroTime(){
        $mtime = explode(" ", microtime()); 
        $res = $mtime[1] + $mtime[0]; 
    }

    // функция возвращает соединение с БД, если оно еще не создано - создает его
    public function getDbConnection($dbName){
        if($this->db[$dbName] == NULL)
        {
            $this->db[$dbName] = Zend_Db::factory('Pdo_Mssql',
                    array(
                        'host'      => $this->dbHost,
                        'username'  => $this->dbUser,
                        'password'  => $this->dbPass,
                        'dbname'    => $dbName,
                        'charset'   => 'UTF-8'
                    ));
        }
        return $this->db[$dbName];
    }

    // функция возвращает select для формирования выборки
    public function getSelect($dbName){
        return $this->getDbConnection($dbName)->select();
    }
    
    // функция выполнения запроса
    public function getResultQuery($dbName, $sqlQuery, $params = NULL){
                                                            /*
                                                             * $dbName - имя базы данных
                                                             * $sqlQuery - параметризированный запрос вида:
                                                             *                  select :field from :database where :wh1 = :con1
                                                             * $params - массив параметров для подстановки в запрос:
                                                             *                  array(':field'=>'id', ':database'=>'ch_site', ':wh1'=>'id', ':con1'=>$id)
                                                             * 
                                                             * функция возвращает массив результата запроса:
                                                             *                  $res[0]['fieldname']
                                                             * 
                                                             */
        $tstart = $this->getMicroTime(); 
        
        $this->getDbConnection($dbName);
        
        $this->timePrepareSpend = ($this->getMicroTime() - $tstart);
        $tstart = $this->getMicroTime(); 
        
        $stmt = $this->db[$dbName]->query($sqlQuery, $params);
        
        $this->timeQuerySpend = ($this->getMicroTime() - $tstart);
        $tstart = $this->getMicroTime(); 
        
        $res = $stmt->fetchAll();

        $this->timeFetchSpend = ($this->getMicroTime() - $tstart);
        return $res;
    }
}


?>
