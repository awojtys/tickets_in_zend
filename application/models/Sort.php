<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Application_Model_Sort
 *
 * @author awojtys
 */
class Application_Model_Sort {
    //put your code here
    protected $_sess_name;
    public function __construct($session_name) {
        return $this->_sess_name = $session_name;
    }
    
    public function sortAction($request)
    {
        $sort = new Zend_Session_Namespace($this->_sess_name);
        if($request->getParam('column'))
        {
            if($request->getParam('column') != $sort->column && $request->getParam('column') != 'clear')
            {
                $sort -> column = $request->getParam('column');
                $sort -> param = 'ASC';  
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');         
                $redirector->gotoUrl('/' . $request->getControllerName());
            }
            else
            {
                
                if($request->getParam('column') != 'clear')
                {

                    if($sort->param != 'ASC')
                    {
                        $sort->param = 'ASC';
                        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');         
                        $redirector->gotoUrl('/' . $request->getControllerName());
                    }
                    else
                    {
                        $sort->param = 'DESC';
                        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');         
                        $redirector->gotoUrl('/' . $request->getControllerName());
                    }
                }
                else
                {
                    Zend_Session::namespaceUnset($this->_sess_name);
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');         
                    $redirector->gotoUrl('/' . $request->getControllerName());
                }
            }
        }
    }
    
}

?>
