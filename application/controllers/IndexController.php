<?php
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $session = new Zend_Session_Namespace('User_Data');
        $this->view->user = $session;
        
        
    }

    public function indexAction()
    {        
        $request = $this->getRequest();
        if($this->getRequest()->getParam('sort') == true)
        {
            $sort = new Application_Model_Sort('Sort');
            $sort ->sortAction($this->getRequest());
        }
        $tickets = new Application_Model_TicketMapper();
        $filter_output = new Application_Model_Filters();
        
        $form = new Application_Form_Searcher();

        $this->view->form = $form;
        $this->view->site_name = "Lista zgłoszeń";


        $sort = new Zend_Session_Namespace('Sort');
        
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
            $this->view->entries = $tickets->fetchAll($filters);
        }
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->json->sendJson(array_map(array($this, '_ticketToJson'), $this->view->entries));
        }
    }
    
    protected function _ticketToJson($object) {
        return is_a($object, 'Application_Model_Ticket') ? $object->toJson() : null;  
    }
}

/*
 * array (
 *  0 => 3
 *  1 => array,
 *  2 => object,
 *  3 => object
 * )
 * 
 * 
 * 
 */

/**
 *  $filters = array(
 *      array(
 *          'column' => $name,
 *          'value' => $value,
 *          'type' => 'eq | like | lt | gt '
 *      ),
 *      array()
 *  )
 * 
 */




