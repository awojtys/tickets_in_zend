<?php

class Application_Model_TicketMapper extends Application_Model_DbTable
{
    protected $table_name = 'Application_Model_DbTable_Ticket';
    protected $_log;
    protected $_where;
    
    public function save(Application_Model_Ticket $ticket, $id = null)
    {
        $data = array(
            'Author' => $ticket->getAuthor(),
            'Title' => $ticket->getTitle(),
            'Ticket_content' => $ticket->getTicket_content(),
            'Status' => $ticket->getStatus(),
            'Priority' => $ticket->getPriority(),
            'Attribute' => $ticket->getAttribute(),
            'Date' => date('Y-m-d'),
        );
        Application_Model_Log::log($ticket);        
        if(null === ($id))
        {
            if(!isset($one_insert) || $one_insert != true)
            {
                $this->getDbTable($this->table_name)->insert($data);
                $one_insert = true;
            }
        }
        else
        {

            $this->getDbTable($this->table_name)->update($data, array('id = ?' => $id));
        }
        
        if($data['Attribute'] != null)
        {
            new Application_Model_Mailer($data);
        }
        
    }
    
    public function find($id, Application_Model_Ticket $ticket)
    {
        $query = $this->getSelect()
                ->from('ticket')
                ->setIntegrityCheck(false)
                ->joinLeft(array('Author' => 'User'), 'ticket.Author = Author.ID', array('Author' => 'Nickname'))
                ->joinLeft(array('Assignee' => 'User'), 'ticket.Attribute = Assignee.ID', array('Assignee' => 'Nickname'))
                ->where('ticket.ID = ?', $id);
        $result = $this->getDbTable($this->table_name)->fetchRow($query);
        
        if(0 == count($result))
        {
            return;
        }
        $row = $result;
        
        $ticket->setId($row->ID)
               ->setAuthor($row->Author)
               ->setTitle($row->Title)
               ->setTicket_content($row->Ticket_content)
               ->setStatus($row->Status)
               ->setPriority($row->Priority)
               ->setAttribute($row->Assignee)
               ->setDate($row->Date);
        return $ticket;
        
    }
    
    public function fetchAll($filters = array())
    {
        $sort = new Zend_Session_Namespace('Sort');
        $query = $this->getSelect()
                ->from('ticket')
                ->setIntegrityCheck(false)
                ->joinLeft(array('Author' => 'User'), 'ticket.Author = Author.ID', array('Author' => 'Nickname'))
                ->joinLeft(array('Assignee' => 'User'), 'ticket.Attribute = Assignee.ID', array('Assignee' => 'Nickname'));
        

        
        if($filters != null)
        {
            $sort->filters = $filters;
        }
        if($sort->column != null)
        {
            $query->order('ticket.' . $sort->column .' ' . $sort->param);
        }
        if($sort->filters != null)
        {
            foreach($sort->filters as $id => $element)
            {
                $query ->where('ticket.' . $element['column'] . ' ' . $element['type'] .' ?', $element['value']);
            }
        }
        
        $resultAll = $this->getDbTable() -> fetchAll($query);
        $entries  = array();
        foreach($resultAll as $row)
        {
            $entry = new Application_Model_Ticket();
            $entry->setId($row->ID)
                  ->setAuthor($row->Author)
                  ->setTitle($row->Title)
                  ->setTicket_content($row->Ticket_content)
                  ->setStatus($row->Status)
                  ->setPriority($row->Priority)
                  ->setAttribute($row->Assignee)
                  ->setDate($row->Date);
            $entries[$row->ID] = $entry;
        }
        return $entries;
    }

    
    public function delete($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $where = $db->quoteInto('id = ?', $id);
        $db->delete('ticket', $where);
    }
    
    public function returnDate()
    {
        $query = $this->getSelect()->distinct('date');
        $resultAll = $this->getDbTable() -> fetchAll($query);
        
        
        $dates  = array();
        $i = 0;
        foreach($resultAll as $row)
        {
            $date = new Application_Model_Ticket();
            $date->setDate($row->Date);
            $dates[$i] = $date;
            $i++;
        }       

        return $dates;
    }
    
    public function returnTicketsOfUser($id, $assignee = false)
    {
        if($assignee == false)
        {
            $query = $this->getSelect()->where('ticket.Author = ?', $id);
        }
        else
        {
            $query = $this->getSelect()->where('ticket.Attribute = ?', $id);
        }
        
        $resultAll = $this->getDbTable()->fetchAll($query);
        
        $entries = array();
        foreach($resultAll as $row)
        {
            $entry = new Application_Model_Ticket;
            $entry
                    ->setID($row->ID)
                    ->setDate($row->Date)
                    ->setPriority($row->Priority)
                    ->setStatus($row->Status)
                    ->setTicket_content($row->Ticket_content)
                    ->setTitle($row->Title);
            
            $entries[$row->ID] = $entry;
        }
        return $entries;
    }
    
}

