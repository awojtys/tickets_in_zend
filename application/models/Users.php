<?php

class Application_Model_Users
{
    protected $_nickname;
    protected $_password;
    protected $_confirmpassword;
    protected $_email;
    protected $_confirmemail;
    protected $_id;
    protected $_role;
    protected $_avatar;
    protected $_delete;
    protected $_checked;


    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function __set($name, $value) 
    {
        $method = 'set'. ucfirst($name);
        if(!method_exists($this, $method))
        {
            throw new Exception('Nie ma takiej właściwosci.');
        }
        
        return $this->$method($value);
    }
    
    public function __get($name)
    {
        $method = 'get'. ucfirst($name);
        if(!method_exists($this, $method))
        {
            throw new Exception('Nie ma takiej właściwosci.');
        }
        
        return $this->$method();
    }
    
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
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
    
    public function setNickname($nickname)
    {
        $this->_nickname = (string) $nickname;
        return $this;
    }
    
    public function getNickname()
    {
        return $this->_nickname;
    }
    
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }
    
    public function getPassword()
    {
        return $this->_password;
    }
    
    public function setConfirmPassword($password)
    {
        $this->_confirmpassword = (string) $password;
        return $this;
    }
    
    public function getConfirmPassword()
    {
        return $this->_confirmpassword;
    }
    
    public function setEmail($email)
    {
        $this->_email = (string) $email;
        return $this;
    }
     
    public function getEmail()
    {
        return $this->_email;
    }
    
        public function setConfirmEmail($email)
    {
        $this->_confirmemail = (string) $email;
        return $this;
    }
     
    public function getConfirmEmail()
    {
        return $this->_confirmemail;
    }
    
    public function setRole($role)
    {
        $this->_role = (string) $role;
        return $this;
    }
     
    public function getRole()
    {
        return $this->_role;
    }
    
    public function setAvatar($avatar)
    {
        $this->_avatar = (string) $avatar;
        return $this;
    }
    
    public function getAvatar()
    {
        return $this->_avatar;
    }
    
    public function setDelete($delete)
    {
        $this->_delete = (string) $delete;
        return $this;
    }
    
    public function getDelete()
    {
        return $this->_delete;
    }
    
    
    public function setCheck($check)
    {
        $this->_checked = (string) $check;
        return $this;
    }
    
    public function getCheck()
    {
        return $this->_checked;
    }
    
    
    public function hashPassword()
    {
        if (CRYPT_SHA512 == 1) {
            $this->_password = hash('sha512', $this->_password);
        }
        else
        {
            $this->_password = hash('md5', $this->_password);
        }
        
        return $this->_password;
    }
    
    public function ToJson()
    {
        return array(
            'ID' => $this->getID(),
            'Nickname' => '<a href="/users/show/id/' . $this->getID() . '">' . $this->getNickname() . '</a>',
            'Email' => $this->getEmail(),
            'Role' => $this->getRole(),
        );
    }
    

}

