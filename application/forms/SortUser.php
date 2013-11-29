<?php

class Application_Form_SortUser extends Zend_Form
{
    protected $_inputs;
    
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        
        $return_roles = new Application_Model_UsersMapper();
        $return_func = new Application_Model_Users();
        
        $id = new Zend_Form_Element_Text('ID');
        $nickname = new Zend_Form_Element_Text('Nickname');
        $email = new Zend_Form_Element_Text('Email');
        $role = new Zend_Form_Element_Select('Role');
        $submit = new Zend_Form_Element_Submit('submit');
        
        $id->setLabel('Szukaj po ID');
        
        $nickname->setLabel('Szukaj po nicku');
        
        $email->setLabel('Szukaj po emailu');
        
        $role->setLabel('szukaj po uprawnieniach')
             ->addMultiOption('', '');
        foreach($return_roles->returnRole() as $key)
         {
             $role->addMultiOption($key->getRole(), $key->getRole());
         }
        
        $this->addElements(array($id, $nickname, $email, $role, $submit));
        
        return $this->_inputs = array($id, $nickname, $email, $role);
    }
    public function getInputs()
    {
        return $this->_inputs;
    }
}

