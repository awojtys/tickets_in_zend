<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zend_Action_Helper_UserData
 *
 * @author awojtys
 */
class Zend_Action_Helper_UserData extends Zend_Controller_Action_Helper_Abstract
{
    //put your code here
    
    protected $_view;
    
    public function preDispatch()
    {
        $this->_setUsersRole();
        //$this->_checkUsersPrivileges();
        var_dump(Zend_Registry::get('role'));
    }
    
    protected function _getView()
    {
        if(null === $this->_view)
        {
            $controller = $this->getActionController();
            $this->_view = $controller->view;
        }
        
        return $this->_view;
    }
    
    protected function _setUsersRole()
    {
        $auth = Zend_Auth::getInstance();
        $view = $this->_getView();
        $user = new Application_Model_Users();
        
        if(!$auth->hasIdentity())
        {
            $user->setRole('guest');
        }
        else
        {
            $user_data = new Zend_Session_Namespace('User_Data');
            $user->setRole($user_data->role);
            $auth->getIdentity($user_data->id);
        }
        
        Zend_Registry::set('role', $user->getRole());
        $view->user = $user;
        $view->logged = $auth->hasIdentity();
    }
    
    protected function _checkUsersPrivileges()
    {
        $role = Zend_Registry::get('role');
        $auth = Zend_Auth::getInstance();
        $acl = Zend_Registry::get('acl');
        
        $request = $this->getActionController()->getRequest();
        
        if(!$acl->isAllowed($role, $request->getControllerName(), $request->getActionName()) && !$auth->hasIdentity())
        {
            if($request->getActionName() != 'login')
            {
                //brak dostępu
            }
            else
            {
                //brak pozwolenia
            }
        }
        elseif(!$acl->isAllowed($role, $request->getControllerName(), $request->getActionName()) && $auth->hasIdentity())
        {
            //jeżeli nie ma dostepu
        }
    }
}

?>
