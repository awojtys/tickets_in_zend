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
class Module_UserHelper extends Zend_Controller_Action_Helper_Abstract
{
    //put your code here
    
    protected $_view;
    public $data;
    
    public function preDispatch()
    {
        $this->_setUsersRole();
        $this->_checkUsersPrivileges();
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
            $user->setRole($auth->getStorage()->read()->Role)
                 ->setID($auth->getStorage()->read()->ID)
                 ->setNickname($auth->getStorage()->read()->Nickname)
                 ->setEmail($auth->getStorage()->read()->Email);
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
                $this->getActionController()->redirect('/');
            }
            else
            {
                //brak pozwolenia
                $this->getActionController()->redirect('/');
            }
        }
        elseif(!$acl->isAllowed($role, $request->getControllerName(), $request->getActionName()) && $auth->hasIdentity())
        {
            //jeżeli nie ma dostepu
            $this->getActionController()->redirect('/');
        }
    }
}

?>
