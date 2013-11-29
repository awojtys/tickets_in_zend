<?php

class TicketController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */   
    }

    public function addAction()
    {
        Application_Model_Log::log('add action');
        // action body
        $this->view->site_name = "Dodaj nowe zgloszenie";
        $form = new Application_Form_NewTicket();
        $ticket = new Application_Model_Ticket();
        $form ->declareForm($ticket);
        $form ->setFormDecorators();
        $this->view->form = $form;
        $request = $this->getRequest();
        Application_Model_Log::log($request->isPost());
        if($request->isPost())
        {
            if($form->isValid($request->getPost()))
            {
                $ticket = new Application_Model_Ticket($form->getValues());
                $mapper = new Application_Model_TicketMapper();
                $mapper->save($ticket);
                $this->redirect('/');
            }
        }
        
    }
    
    public function showAction()
    {
        $request = $this->getRequest();
        $id = (int) $request->getParam('id');
        if ($id > 0) {
            $this->view->site_name = "Podgląd zgłoszenia ID " . $id;

            $entry = new Application_Model_TicketMapper();
            $ticket = new Application_Model_Ticket();

            $entry->find($id, $ticket);

            $this->view->ticket = $ticket;            
        }
        else
        {

            $this->redirect('/');
            exit;
        }
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $id = (int) $request->getParam('id');
        $ticket = new Application_Model_Ticket();
        if ($id > 0) 
        {

            
            $entry = new Application_Model_TicketMapper();
            $entry->find($id, $ticket);
            
            $form = new Application_Form_NewTicket();
            $form ->declareForm($ticket);
            $form ->setFormDecorators();
            $this->view->form = $form;
            
            if($ticket->getAuthor() == Zend_Auth::getInstance()->getStorage()->read()->Nickname)
            {
                $this->view->site_name = "Edycja zgłoszenia ID " . $id;

                

                if($this->getRequest()->isPost())
                {
                    if($form->isValid($request->getPost()))
                    { 
                        $ticket = new Application_Model_Ticket($form->getValues());
                        $mapper = new Application_Model_TicketMapper();
                        $mapper->save($ticket, $id);
                        $this->redirect('/');
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
            $this->redirect('/');
        }
    }

    public function deleteAction()
    {
        // action body
        $request = $this->getRequest();
        $id = (int) $request->getParam('id');
        $delete = new Application_Model_TicketMapper();
        $delete ->delete($id);
        $this->redirect('/');
    }


}









