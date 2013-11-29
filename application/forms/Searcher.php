<?php

class Application_Form_Searcher extends Zend_Form
{
    protected $_inputs;
    public function init()
    {
        $return_users = new Application_Model_UsersMapper();
        $return_date = new Application_Model_TicketMapper();
        
        
        $id = new Zend_Form_Element_Text('id');
        $author = new Zend_Form_Element_Select('author');
        $title = new Zend_Form_Element_Text('title');
        $status = new Zend_Form_Element_Select('status');
        $priority = new Zend_Form_Element_Select('priority');
        $assignee = new Zend_Form_Element_Select('attribute');
        $date = new Zend_Form_Element_Select('date');
        $submit = new Zend_Form_Element_Submit('submit');
        
        $id
                ->setLabel('Szukaj po ID');
        
        $author
                ->setLabel('Szukaj po autorze')
                ->addMultiOption('', '');
        foreach($return_users->userFetchAll() as $key)
         {
             $author->addMultiOption($key->getID(), $key->getNickname());
         }
        
        $title
                ->setLabel('Szukaj tytułu');

        
        $status
                ->setLabel('Pokaż ze statusem')
                ->addMultiOption('', '')
                ->addMultiOptions(Application_Model_Transators_Status::getOptionsArray());
        
        $priority
                ->setLabel('Pokaż z priorytetem')
                ->addMultiOption('', '')
                ->addMultiOptions(Application_Model_Transators_Priority::getOptionsArray());
        
        $assignee
                ->setLabel('Do kogo przydzielono')
                ->addMultiOption('', '');
        foreach($return_users->userFetchAll() as $key)
         {
             $assignee->addMultiOption($key->getID(), $key->getNickname());
         }

        
        $date
                ->setLabel('Według daty')
                ->addMultiOption('', '');
        foreach($return_date->returnDate() as $key)
         {
             $date->addMultiOption($key->getDate(),$key->getDate());
         }
        $this->addElements(array($id, $author, $title, $status, $priority, $assignee, $date, $submit));
        
        return $this->_inputs = array($id, $author, $title, $status, $priority, $assignee, $date);
    }
    
    public function getInputs()
    {
        return $this->_inputs;
    }


}

