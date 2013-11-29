<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Filters
 *
 * @author awojtys
 */
class Application_Model_Filters {
    //put your code here
    public function returnFilters($inputs = array())
    {
        $outputs = array();
        foreach ($inputs as $id => $element)
        {
            if($element->getValue() != null)
            {
                $outputs[$id] = array(
                        'column' => $element->getName(),
                        'value' => $this->_elementToSql($element) == 'LIKE' ? '%' . $element->getValue() . '%' : $element->getValue(),
                        'type' => $this->_elementToSql($element)

                );
            }
        }
        return $outputs;
    }
    
    protected function _elementToSql($element) {
        $element = is_string($element) ? $this->getElement($element) : $element;
        
        $map = array(
            'Select' => '=', 
            'Text' => 'LIKE'
        );
        
        $key = substr(get_class($element),strrpos(get_class($element),'_')+1);
        return $map[$key];
    }
}

?>
