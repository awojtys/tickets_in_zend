<?php

class UsersController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
                               

        $request = $this->getRequest();
        if($this->getRequest()->getParam('sort') == true)
        {
            $sort = new Application_Model_Sort('User_Sort');
            $sort ->sortAction($this->getRequest());
        }
        
        $mapper = new Application_Model_UsersMapper();
        $form = new Application_Form_SortUser();
        $filter_output = new Application_Model_Filters();
        
        
        $this->view->form = $form;
        $this->view->entries = $mapper->fetchUserList();
        $this->view->site_name = "Lista użytkowników";
        
        $sort = new Zend_Session_Namespace('User_Sort');
        
        if($request->isPost())
        {
            $data = $request->getPost();
            $sort->filterRequest = $data;
        }
        else {
            $data = is_array($sort->filterRequest) ? $sort->filterRequest : array();
        }
        
        if ($form->isValid($data)) {
            $filters = $filter_output->returnFilters($form->getInputs());
            $this->view->entries = $mapper->fetchUserList($filters);
        }
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->json->sendJson(array_map(array($this, '_userToJson'), $this->view->entries));
        }
        
    }

    public function registerAction()
    {
            $this->view->site_name = 'Rejestracja';
            $form = new Application_Form_Users();
            $form ->RegisterForm();
            $form ->setFormDecorators('true');
            $this->view->form = $form;

            $request = $this->getRequest();
            if($request->isPost())
            {
                if($form->isValid($request->getPost()))
                {
                    $user = new Application_Model_Users($form->getValues());
                    $mapper = new Application_Model_UsersMapper();
                    if($mapper -> userExists($user) == false)
                    {
                        $mapper -> save($user);
                        $this->redirect('/users');
                    }
                    else
                    {
                        $this->view->error = 'Podany użytkownik już isnieje';
                    }
                }
            }
    }

    public function loginAction()
    {   
        $this->view->site_name = "Logowanie";
        $form = new Application_Form_Users();
        $form ->LoginForm();
        $form ->setFormDecorators();
        $this->view->form = $form;

        $request = $this->getRequest();

        if($request->isPost())
        {
            if($form->isValid($request->getPost()))
            {
                $user = new Application_Model_Users($form->getValues());
                $mapper = new Application_Model_UsersMapper();
                if($result = $mapper->userReturn($user) != false)
                {
                    $this->redirect('/');
                }
                else
                {
                    $this->view->error = 'Podano błędny login lub hasło';
                }
            }
        }
    }
    

    public function logoutAction()
    {
        // action body
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::namespaceUnset('Sort');
        Zend_Session::namespaceUnset('User_Sort');
        $this->redirect('/');
    }
    
    public function showAction()
    {

        $id = (int)$this->getRequest()->getParam('id');
        if($id != 0)
        {
            if(Zend_Auth::getInstance()->getStorage()->read()->ID == $id || Zend_Auth::getInstance()->getStorage()->read()->Role == 'admin' )
            {
 
                $config = new Application_Model_Config();

                // action body
                $mapper = new Application_Model_UsersMapper();
                $user = new Application_Model_Users();
                $mapper_ticket = new Application_Model_TicketMapper();
                $mapper->findUserData($id, $user);
                if($user->check ==  0)
                {
                    $change = new Application_Model_ModImage();
                    $change ->resizeImage($id, 'true');
                    
                    $change = new Application_Model_UsersMapper();
                    $change ->changeCheck(1, $id);
                    $action = $this->getRequest()->getActionName();
                    $controller = $this->getRequest()->getControllerName();
                    $this->_helper->redirector->gotoSimple($action, $controller, array(), array('id' => $id));
                }
                
                $this->view->site_name = "Podgląd użytkownika " . $user->getNickname();
                $this->view->content = $mapper_ticket->returnTicketsOfUser($id);
                $this->view->assignee = $mapper_ticket->returnTicketsOfUser($id, true);
                $this->view->user_data = $user;
                $this->view->config = $config -> getAllConfig();
                $this->view->id = $id;
            }
            else
            {
                $this->redirect('/');
            }

        }
        else
        {
            $this->redirect('/users');
        }
    }
    
    public function editAction()
    {
        // action body
        $id = (int)$this->getRequest()->getParam('id');
        $modImage = new Application_Model_ModImage();
        if($id != 0)
        {
            $this->view->site_name = "Edycja użytkownika " . Zend_Auth::getInstance()->getStorage()->read()->Nickname;
            if(Zend_Auth::getInstance()->getStorage()->read()->ID == $id || Zend_Auth::getInstance()->getStorage()->read()->Role == 'admin' )
            {
                $form = new Application_Form_Users();
                $form ->RegisterForm('show');
                $form ->setFormDecorators();
                $this->view->form = $form;
                
                $request = $this->getRequest();
                
                if($request->isPost())
                {
                    if($form->Avatar->getFileName() != null)
                    {
                        $FileName = $modImage -> renameImage($id, $form->Avatar->getFileName());
                        $form->Avatar->addFilter('Rename', $FileName);
                    }
                    if($form->isValid($request->getPost()))
                    {
                        $user = new Application_Model_Users($form->getValues());

                        
                        if($form->Delete->checked)
                        {
                            $modImage -> delete($id);
                        }
                        
                        $mapper = new Application_Model_UsersMapper();
                        if($mapper -> userExists($user) == false)
                        {
                            $mapper ->save($user, $id);
                            
                            if($form->Avatar->getValue() != null)
                            {
                                $modImage ->resizeImage($id);
                            }

                            $this->redirect('/users/show/id/'.$id);
                        }
                        else
                        {
                            $this->view->error = 'Podany użytkownik już isnieje';
                        }
                    }
                }
            }
            else
            {
                $this->redirect('/');
            }
        }
        else
        {
            $this->redirect('/users');
        }
    }
    
    public function deleteAction()
    {
        
    }
    
    protected function _userToJson($object)
    {
        return is_a($object, 'Application_Model_Users') ? $object->ToJson() : null;
    }
    
}







