<?php

class Application_Form_NewTicket extends Zend_Form
{
    protected $_form;
    public function init()
    {

        /* Form Elements & Other Definitions Here ... 
         * 
         * $config => array(
         *      'code' => array(
         *          'type' => 'text|select',
         *          'defVal' => 'string|int',
         *          'options' => array(),
         *          'filters' => array()
         *          )
         * )
         */
 
    }

    
    public function declareForm(Application_Model_Ticket $ticket)
    {
        $return_users = new Application_Model_UsersMapper();
        
        $author = new Zend_Form_Element_Hidden('author');
        $title = new Zend_Form_Element_Text('title');
        $Ticket_content = new Zend_Form_Element_Textarea('Ticket_content');
        $status = new Zend_Form_Element_Select('status');
        $priority = new Zend_Form_Element_Select('priority');
        $submit = new Zend_Form_Element_Submit('Dodaj');
        $hash = new Zend_Form_Element_Hash('hash'); 
        $attr = new Zend_Form_Element_Select('attribute');
        $user = new Zend_Session_Namespace('User_Data');
        
        $author->setValue(Zend_Auth::getInstance()->getStorage()->read()->ID);
        
        $title->setLabel('Tytuł')
              ->setRequired(true)
              ->addValidator(new Zend_Validate_StringLength(0, 255), true)
              ->addFilter(new Zend_Filter_StringTrim())
              ->addFilter(new Zend_Filter_StripTags())
              ->setValue($ticket->Title)
              ->setAttrib('MaxLength', '255');
        
        $Ticket_content->setLabel('Treść')
              ->setRequired(true)
              ->addValidator(new Zend_Validate_StringLength(0, 65000), true)
              ->addFilter(new Zend_Filter_StringTrim())
              ->setValue($ticket->Ticket_content);
              
         $status->setLabel('Status')
                ->addMultiOption('1', '1')
                ->addMultiOptions(Application_Model_Transators_Status::getOptionsArray())
                ->setValue($ticket->Status);
        
         $priority->setLabel('Priorytet')
                  ->addMultiOption('2', '2')
                  ->addMultiOptions(Application_Model_Transators_Priority::getOptionsArray())
                  ->setValue($ticket->Priority);
         
         $attr->setLabel('Przypisz do')
              ->addMultiOption('', 'Wybierz');
         foreach($return_users->userFetchAll() as $key)
         {
             $attr->addMultiOption($key->getID(), $key->getNickname());
         }
         
                 
  
        $hash->setIgnore(true);
        
        $submit ->setAttrib('onclick', 'this.disabled=true;this.form.submit();');
        
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
        
        $this->addElements(array($title, $Ticket_content, $status, $priority, $attr, $submit, $author));
        
        return $this;
        
    }
    
    public function setFormDecorators()
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
    }
    


}

