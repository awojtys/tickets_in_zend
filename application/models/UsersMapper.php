<?php

class Application_Model_UsersMapper extends Application_Model_DbTable
{
    protected $table_name = 'Application_Model_DbTable_User';
    
    public function save(Application_Model_Users $user, $id = null)
    {   
        $data = array(
            'Nickname' => $user->getNickname(),
            'Password' => $user->getPassword() != null ? $user->hashPassword() : null,
            'Email' => $user->getEmail(),
            'Role' => $user->getRole(),
            'Avatar' => $user->getDelete() == true || Zend_Controller_Front::getInstance()->getRequest()->getActionName() == 'register'  ? 'none.png' : $user->getAvatar() ,
       );
        if($id == null)
        {
            $this->getDbTable($this->table_name)->insert($data);
        }
        else
        {
            $output_data = array();
            foreach($data as $element => $value)
            {
                $output_data[$element] = $value;
                if($output_data[$element] == null)
                {
                    unset($output_data[$element]);
                }
            }
            if (count($output_data)) {
                $this->getDbTable($this->table_name)->update($output_data, array('id = ?' => $id));           
            }
        }
    }
    
    public function userExists(Application_Model_Users $set_user)
    {
        $select = $this->getSelect()->where('Nickname = ?', $set_user->getNickname());
        $result = $this->getDbTable()->fetchRow($select);

        if(0 != count($result))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function userReturn(Application_Model_Users $user)
    {

        $db = Zend_Db_Table::getDefaultAdapter();
        
        $adapter = new Zend_Auth_Adapter_DbTable(
				$db,
				'User',
                                'Nickname',
                                'Password',
                                '?');
        
        $adapter->setIdentity($user->getNickname())
                ->setCredential($user->hashPassword());
                

        
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate(($adapter));
        $data = $adapter->getResultRowObject();
        if($result->isValid())
        {
            $auth ->getStorage()->write($data);
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function userFetchAll()
    {

        $resultAll = $this->getDbTable() -> fetchAll();
        
        $entries  = array();
        foreach($resultAll as $row)
        {
            $entry = new Application_Model_Users();
            $entry->setID($row->ID)
                  ->setNickname($row->Nickname);

            $entries[] = $entry;
        }
        
        return $entries;
    }
    
    public function findUserData($id, Application_Model_Users $user)
    {
        $query = $this->getSelect()->where('User.ID = ?', $id);
        echo $query;
        $result = $this->getDbTable()->fetchRow($query);
        $user ->setID($result->ID)
              ->setNickname($result->Nickname)
              ->setEmail($result->Email)
              ->setRole($result->Role)
              ->setAvatar($result->Avatar)
              ->setCheck($result->check);
        
        return $user;
    }
    
    public function fetchUserList($filters = array())
    {
        $sort = new Zend_Session_Namespace('User_Sort');
        $query = $this->getSelect();
        
        if($sort->column != null)
        {
            $query->order('User.' . $sort->column .' ' . $sort->param);
        }

        if($filters != null)
        {
            foreach($filters as $id => $element)
            {
                $query ->where('User.' . $element['column'] . ' ' . $element['type'] .' ?', $element['value']);
            }
        }
        
        
        $resultAll = $this->getDbTable()->fetchAll($query);
        $entries = array();
        foreach($resultAll as $row)
        {
            //var_dump($row);
            $entry = new Application_Model_Users();
            $entry->setID($row->ID)
                  ->setNickname($row->Nickname)
                  ->setEmail($row->Email)
                  ->setRole($row->Role);
            
            $entries[$row->ID] = $entry;
        }
        return $entries;
    }
    
    public function returnRole()
    {
        $query = $this->getSelect()->distinct('Role');
        $resultAll = $this->getDbTable() -> fetchAll($query);
        
        
        $roles  = array();
        $i = 0;
        foreach($resultAll as $row)
        {
            $role = new Application_Model_Users();
            $role->setRole($row->Role);
            $roles[$i] = $role;
            $i++;
        }       

        return $roles;
    }
    
    public function changeCheck($decision, $id = null)
    {
        if($id == null)
        {
            $this->getDbTable($this->table_name)->update(array('check' => $decision));
        }
        else
        {
            $this->getDbTable($this->table_name)->update(array('check' => $decision), array('id = ?' => $id));
        }
    }

}

