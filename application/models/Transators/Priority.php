<?php
class Application_Model_Transators_Priority {
    
    protected static $_priorities = array(1 => 'Niski',2 => 'Średni',3 => 'Wysoki',4 => 'Natychmiastowy');
    
    
    public static function getOptionsArray() {
        $options = array();
        foreach(self::$_priorities as $priority => $label) {
            $options[$priority] = $label;
        }
        return $options;
    }
    
    public static function getOption($option){
        $option = self::$_priorities[$option];
        return $option;
    }
}