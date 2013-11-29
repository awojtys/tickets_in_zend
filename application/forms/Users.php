<?php

class Application_Form_Users extends Zend_Form
{
    protected $_action;
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
    }
    
    public function LoginForm()
    {
            $login = new Zend_Form_Element_Text('Nickname');
            $password = new Zend_Form_Element_Password('Password');
            $submit = new Zend_Form_Element_Submit('Zaloguj');

            $login->setLabel('Nickname: ')
                  ->setRequired(true)
                  ->addFilter(new Zend_Filter_StringTrim())
                  ->addFilter(new Zend_Filter_StripTags());


            $password->setLabel('Hasło: ')
                     ->setRequired(true);


            $this->addElements(array($login, $password, $submit));
    }
    
    public function RegisterForm($action = null)
    {
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $login = new Zend_Form_Element_Text('Nickname');
        $password = new Zend_Form_Element_Password('Password');
        $confirm_password = new Zend_Form_Element_Password('ConfirmPassword');
        $email = new Zend_Form_Element_Text('Email');
        $confirm_email = new Zend_Form_Element_Text('ConfirmEmail');
        
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('id'))
        {
            $user_func = new Application_Model_Users();
            $mapper = new Application_Model_UsersMapper();
            $mapper->findUserData($id, $user_func);
            
            if($user_func->getRole() != 'admin' && Zend_Auth::getInstance()->getStorage()->read()->Role == 'admin')
            {
                $role = new Zend_Form_Element_Select('Role');
                $role ->addMultiOption('user', 'user')
                      ->addMultiOption('admin', 'admin');
            }
            
        }
        
        $login->setLabel('Podaj swoj nick')
              ->addFilter(new Zend_Filter_UcWords())
              ->addFilter(new Zend_Filter_StringTrim())
              ->addValidator(new Zend_Validate_StringLength(array('min' => 5, 'max' => 60), false))
              ->setAttrib('MaxLength', '60');
        
        $password->setLabel('Podaj hasło')
                 ->addValidator(new Zend_Validate_StringLength(array('min' => 8,'max' => 128), true))
                 ->setAttrib('MaxLength', '128');
        
        $confirm_password->setLabel('Powtórz hasło')
                 ->addValidator(new Zend_Validate_StringLength(array('min' => 8,'max' => 128), true))
                 ->setAttrib('MaxLength', '128')
                 ->addValidator(new Zend_Validate_Identical('Password'));
        
        $email->setLabel('Podaj email')
              ->addValidator(new Zend_Validate_StringLength(array(0,128), true))
              ->addValidator(new Zend_Validate_EmailAddress)
              ->setAttrib('MaxLength', '100');
              
        
        $confirm_email->setLabel('Powtórz email')
                 ->addValidator(new Zend_Validate_StringLength(array(0,128), true))
                 ->addValidator(new Zend_Validate_EmailAddress)
                 ->setAttrib('MaxLength', '100')
                 ->addValidator(new Zend_Validate_Identical('Email'));
        
 
        
        if($action == null)
        {
            $login->setRequired(true);
            $password->setRequired(true);
            $confirm_password->setRequired(true);
            $email->setRequired(true);
            $confirm_email->setRequired(true);
            
            $captcha = new Zend_Form_Element_Captcha('Captcha', array(
                'label' => "Przepisz tekst z obrazka",
                'captcha' => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300,
                ),
            ));
            $submit = new Zend_Form_Element_Submit('Stwórz');
            
            $role = new Zend_Form_Element_Select('Role');
            $role ->addMultiOption('user', 'user')
                  ->addMultiOption('admin', 'admin');
        }
        else
        {
            $submit = new Zend_Form_Element_Submit('Edytuj');
            
            $file = new Zend_Form_Element_File('Avatar');
            $file -> setLabel('Zmień avatar')
                  -> setDestination(APPLICATION_PATH . '/../public/avatars/original/')
                  -> addValidator('Size', false, 502400)
                  -> addValidator('Extension', false, 'jpg,png,gif');
            
            $delete = new Zend_Form_Element_Checkbox('Delete');
            $delete -> setLabel('Usuń avatar');
            
        }
       $this->addElement('hash', 'csrf', array(
           'ignore' => true,
       ));
        
        
        $this->addElements(array($login, $password, $confirm_password, $email, $confirm_email, $captcha, $role, $file, $delete, $submit));
        return $this->_action = $action;
        
    }
    
    public function setFormDecorators($captcha = null)
    {
        
        
        $this->clearDecorators();
        $this->addDecorator('FormElements')
             ->addDecorator('HtmlTag', array('tag' => '<table>'))
             ->addDecorator('Form');

        $this->setElementDecorators(array(
            'ViewHelper',
            array('Description', array('tag' => 'span')),
            array('Errors'),
            array('HtmlTag', array('tag' => 'td')),
            array('Label', array('tag' => 'th', 'requiredSuffix' => ' *')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        
        $this->setSubFormDecorators(array(
            'FormElements',
                array('HtmlTag', array('tag'=>'tr')),
        ));
        if($this->_action == null && isset($captcha))
        {
            $this->getElement('Captcha')->removeDecorator("viewhelper");
        }
        elseif($this->_action != null)
        {
            $this->getElement('Avatar')->setDecorators(
                array(
                    'File',
                    'Errors',
                    array(array('data' => 'HtmlTag'), array('tag' => 'td')),
                    array('Label', array('tag' => 'th')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
                )
            );
        }
        
    }
    
    

}

