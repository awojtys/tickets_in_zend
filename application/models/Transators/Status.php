<?php
class Application_Model_Transators_Status {
    
    protected static $_status = array(1 => 'Nowy', 2 => 'Realizowany', 3 => 'Zakończny');
    
    public static function getOptionsArray(){
        $options = array();
        foreach(self::$_status as $status => $label)
        {
            $options[$status] = $label;
        }
        return $options;
    }
    
    public static function getOption($option){
        $option = self::$_status[$option];
        return $option;
    }
            
}