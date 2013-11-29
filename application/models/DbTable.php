<?php

abstract class Application_Model_DbTable
{
   protected $_dbTable;
   protected $table_name = null;
   
    public function __construct() {
        $this->setDbTable($this->table_name);
        return $this;
    }
   
    
    public function setDbTable($dbTable)
    {
        if(is_string($dbTable))
        {
            $dbTable = new $dbTable();
        }
        if(!$dbTable instanceof Zend_Db_Table_Abstract)
        {
            throw new Exception('Nie można utworzyć obiektu Zend_Db_Table_Abstract');
        }
        $this->_dbTable = $dbTable;
    }
    
    public function getDbTable()
    {
        if(null === $this->_dbTable)
        {
            $this->setDbTable($this->table_name);
        }
        return $this->_dbTable;
    }
    
    public function getSelect() {
        return new Zend_Db_Table_Select($this->getDbTable());
    }
}

