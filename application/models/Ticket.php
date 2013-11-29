<?php

class Application_Model_Ticket
{
    protected $_author;
    protected $_title;
    protected $_content;
    protected $_status;
    protected $_priority;
    protected $_id;
    protected $_date;    
    protected $_attr;

    public function __construct(array $options = null) {
        if(is_array($options))
        {
            $this->setOptions($options);
        }
    }
    
    public function __set($name, $value) {
        $method = 'set' . $name;
        if(('mapper' == $name) || !method_exists($this, $method))
        {
            throw new Exception('Błędna właściwość');
        }
        
        return $this->$method($value);
    }
    
    public function __get($name) {
        
        $method = 'get' . $name;
        if(('mapper' == $name) || !method_exists($this, $method))
        {
            throw new Exception('Błędna właściwość');
        } 
        return $this->$method();
    }
    
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value)
        {
            $method = 'set' . ucfirst($key);
            if(in_array($method, $methods))
            {
                $this->$method($value);
            }
        }
        
        return $this;
    }
    
    public function setAuthor($author)
    {
        $this -> _author = (string) $author;
        return $this;
    }
    
    public function getAuthor()
    {
        return $this->_author;
    }
    
    public function setTitle($title)
    {
        $this->_title = (string) $title;
        return $this;
    }
    
    public function getTitle()
    {
        return $this->_title;
    }
    
    public function setTicket_content($content)
    {
        $this->_content = (string) $content;
        return $this;
    }
    
    public function getTicket_content()
    {
        return $this->_content;
    }
    
    public function setStatus($status)
    {
        $this->_status = (string) $status;
        return $this;
    }
    
    public function getStatus()
    {
        return $this->_status;
    }
    
    public function setPriority($priority)
    {
        $this->_priority = (string) $priority;
        return $this;
    }
    
    public function getPriority()
    {
        return $this->_priority;
    }
    
    public function setID($id)
    {
        $this->_id = (string) $id;
        return $this;
    }
    
    public function getID()
    {
        return $this->_id;
    }
    
    public function setDate($date)
    {
        $this->_date = (string) $date;
        return $this;
    }
    
    public function getDate()
    {
        return $this->_date;
    }
    
    public function setAttribute($attribute)
    {
        $this->_attr = (string) $attribute;
        return $this;
    }
    
    public function getAttribute()
    {
        return $this->_attr;
    }
    

    public function toJson() {
        return array(
            'id' => $this->getId(),
            'Author' => $this->getAuthor(),
            'Title' => '<a href="/ticket/show/id/' . $this->getID() . '">' . $this->getTitle() . '</a>',
            'Status' => Application_Model_Transators_Status::getOption($this->getStatus()),
            'Priority' => Application_Model_Transators_Priority::getOption($this->getPriority()),
            'Assignee' => $this->getAttribute(),
            'Date' => $this->getDate(),
       );
    }
}

