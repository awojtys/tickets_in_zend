<?php

class ConfigController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //$test = new Application_Model_Config();
        //var_dump($test->returnConfig());
        
        // action body
        $form = new Application_Form_Config();
        $this->view->form = $form;
        $this->view->site_name = "Ustawienia configu";
        $request = $this->getRequest();
        $mapper = new Application_Model_Config();
        $size = $mapper->getAllConfig();
        if($request->isPost())
        {
            if($form->isValid($request->getPost()))
            {
                if($form->Avatar_Width->getValue() != $size['Avatar_Width'] || $size['Avatar_Height'] != $form->Avatar_Height->getValue())
                {
                    $change = new Application_Model_UsersMapper();
                    $change ->changeCheck(0);
                }
                $mapper->save($form->getValues());
                $this->redirect('/config');
            }
        }
    }
    


}

