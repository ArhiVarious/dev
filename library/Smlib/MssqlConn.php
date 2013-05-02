<?php

/**
 * Для корректной работы необходимо подготовить в Bootstrap реестр:
 *       $dbConfig = $this->getOption('dbconn');
 *       Zend_Registry::set('dbconn', $dbConfig);
 *  
 * 
 * Описание класса Smlib_MssqlConn
 * 
 * Класс реализует функцию getResultQuery, которая принимает параметризированные запросы вида:
 * select @field from @database where @wh1 = :con1
 * 
 * вторым параметром задается массив параметров:
 * array(':field'=>'id', ':database'=>'ch_site', ':wh1'=>'id', ':con1'=>$id)
 * 
 * функция возвращает
 *
 * @автор Artem
 */
class Smlib_MssqlConn {
    // данные
    protected $dbHost;              // хост базы данных
    protected $dbUser;              // пользователь  базы данных
    protected $dbPass;              // пароль пользователя
    protected $timePrepareSpend;    // время запроса
    protected $timeQuerySpend;      // время запроса
    protected $timeFetchSpend;      // время запроса
    protected $pconn;               // подключение к серверу
    protected $error;               // ошибка работы с БД
    protected $dtFormat;            // формат даты/времени
    
    // конструктор
    public function __construct($connName = 'default') {
        $dbConfig = Zend_Registry::get('dbconn');
        $this->dbHost = $dbConfig[$connName]['host'];
        $this->dbUser = $dbConfig[$connName]['user'];
        $this->dbPass = $dbConfig[$connName]['pass'];
        $this->dtFormat = $dbConfig[$connName]['dtformat'];
        $this->pconn = mssql_pconnect($dbConfig[$connName]['host'], $dbConfig[$connName]['user'], $dbConfig[$connName]['pass']);
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
    
    public function getError(){
        return $this->error;
    }


    protected function getMicroTime(){
        $mtime = explode(" ", microtime()); 
        $res = $mtime[1] + $mtime[0]; 
    }

    public function getDbConnection($dbName){
        if($this->pconn){
            if(mssql_select_db($dbName, $this->pconn)){
                return $this->pconn;
            }
            else{
                $this->error = "Unable to select database $dbName";
                return FALSE;
            }
        }
        $this->error = "Unable to set connection with host: $$this->dbHost";
        return FALSE;
    }

    // функция выполнения запроса
    public function getResultQuery($dbName, $sqlQuery, $params = array()){
                                                            /*
                                                             * $dbName - имя базы данных
                                                             * $sqlQuery - параметризированный запрос вида:
                                                             *                  select field from database where field = @con1@
                                                             * $params - массив параметров для подстановки в запрос:
                                                             *                  array("@con1@'=>'id')
                                                             * 
                                                             * функция возвращает массив результата запроса:
                                                             *                  $res[0]['fieldname']
                                                             * 
                                                             */
        
        //Zend_Debug::dump($sqlQuery);
        $this->error = "OK";
        $tstart = $this->getMicroTime(); 
        
        if($this->getDbConnection($dbName)){
            if(!empty($params)){
                // обработка параметров
                if(is_array($params)){
                    foreach ($params as $key => $value) {
                        if($value instanceof DateTime){
                            // дата
//Zend_Debug::dump($this->dtFormat);
                            $value = "'".$value->format($this->dtFormat)."'";
//Zend_Debug::dump($value);
                        }
                        else{
                            switch (gettype($value)){
                                case 'integer':
                                case 'double':
                                case 'boolean':
                                    break;
                                case 'string':
                                    $res_words = array('declare', 'select', 'truncate', 'delete', 'insert', 'create', 'drop');
                                    $replace_to = array('dec<!---->lare', 'sel<!---->ect', 'trun<!---->cate', 'del<!---->ete', 'ins<!---->ert', 'cre<!---->ate', 'dr<!---->op');
                                    $value = str_ireplace($res_words, $replace_to, $value);
                                    $value = "'".$value."'";
                                    break;
                                default :
                                    $this->error = "Wrong parameter type in $sqlQuery";
                                    return FALSE;
                                    break;
                            }
                        }
                        $sqlQuery = str_replace($key, $value, $sqlQuery);
                    }
                }
                else{
                    $this->error = "Params must be an array";
                    return FALSE;
                }
            }
            // выполняем подготовленный запрос
//Zend_Debug::dump($sqlQuery);
            $this->timePrepareSpend = ($this->getMicroTime() - $tstart);
            $tstart = $this->getMicroTime(); 
            $query = mssql_query($sqlQuery, $this->pconn);
            if(!$query){
                $this->error = "Unable to prepare query: $sqlQuery";
                return FALSE;
            }
            $this->timeQuerySpend = ($this->getMicroTime() - $tstart);
            $tstart = $this->getMicroTime(); 
            while($row = mssql_fetch_assoc($query)){
                $res[] = $row;
            }
            $this->timeFetchSpend = ($this->getMicroTime() - $tstart);
            return $res;
        }
        //Zend_Debug::dump($res);
        return FALSE;
    }
}


?>
