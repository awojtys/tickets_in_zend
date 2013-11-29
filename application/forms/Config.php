<?php

class Application_Form_Config extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        $elements = new Application_Model_Config();
        $elements = $elements->returnColumn();
        $i = 0;
        foreach($elements as $element => $key)
        {
                $type = 'Zend_Form_Element_' . $key['Type'];
                $i = new $type($key['Name']);
                $i->setLabel($key['Label'])
                  ->setRequired($key['Required']);
                
                if($key['Type'] == 'Text')
                {
                    $i ->setValue($key['Value']);
                }
                else
                {
                    foreach ($key['Value'] as $value)
                    {
                        $i->addMultiOption($value, $value);
                    }
                }
                $this->addElement($i);
                $i++;
        }

        $submit = new Zend_Form_Element_Submit('submit');
        
        $this->addElement($submit);
        
        
        //$this->addElements($elements);
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
        if(isset($captcha))
        {
            $this->getElement('Captcha')->removeDecorator("viewhelper");
        }
    }
    
    

}

