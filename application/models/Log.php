<?php

class Application_Model_Log
{
    protected static $_instance;
    
    private function __construct() {
            
    }
    
    public static function getInstance()
    {
        if(!isset(self::$_instance))
        {
            $model = get_class($this);
            self::$_instance = new $model;
        }
        
        return self::$_instance;
    }
    
    public function __clone() {
		die($this . ' class can\'t be instantiated. Please use the method called
    getInstance.');
    }
    
    public function createLog($message)
    {
        $writer = new Zend_Log_Writer_Stream(BP . '/log/log.txt');
        $logger = new Zend_Log($writer);
        
        $logger ->log(var_export($message,true), Zend_Log::DEBUG);
        
        
    }

    public static function log($message) {
        self::getInstance()->createLog($message);
    }
}

