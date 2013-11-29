<?php
class Application_Model_Config extends Application_Model_DbTable {
    //put your code here
    protected $table_name = 'Application_Model_DbTable_Config';
    protected $_config;
    protected $_password;
       
    protected function _returnConfig($name)
    {
        $query = $this->getSelect()->where('Name = ?', $name);
            $resultAll = $this->getDbTable()->fetchRow($query);
            if($resultAll == array())
            {
                return false;
            }
            else
            {
                return true;
            }
    }

    public function encryptPassword($password)
    {
        $this->_password = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_getCryptKey(), $password, MCRYPT_MODE_ECB);

        return base64_encode($this->_password);
    }
    
    public function decryptPassword($password)
    {
        $password = base64_decode($password);
        $this->_password = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->_getCryptKey(), $password, MCRYPT_MODE_ECB);
        return $this->_password;
    }
    
    public function getAllConfig()
    {
        $resultAll = $this->getDbTable()->fetchAll();
        $row = $resultAll;
        foreach ($row as $key => $value)
        {
            if(strstr($value->Name, 'Password'))
            {
                $value->Value = trim($this->decryptPassword($value->Value));
            }
            
            $data[$value->Name] = $value->Value;

        }

        return $data;
    }
    
    public function save($value)
    {
        foreach($value as $key => $element)
        {
            $data = array(
                'Name' => $key,
                'Value' => $element
            );
           
            if($data['Value'] == null)
            {
                unset($data['Name']);
            }
            else
            {
                if(strstr($data['Name'], 'Password'))
                {
                    $data['Value'] = $this->encryptPassword($data['Value']);
                }
            }
            
            if($this->_returnConfig($key) == false)
            {
                $this->getDbTable()->insert($data);
            }
            else{

                $this->getDbTable()->update($data, array('Name = ?' => $data['Name']));
            }           
        } 
    }
    
    protected function _prepareColumn()
    {
        $values = $this->getAllConfig();

        $config = array(
            '1' => array(
                'Name' => 'Host_Name',
                'Value' => $values['Host_Name'] == null ? 'smtp.gmail.com' : $values['Host_Name'],
                'Type' => 'Text',
                'Label' => 'Ustaw adres SMTP serwera pocztowego',
                'Required' => true
            ),
            '2' => array(
                'Name' => 'Set_Encryption',
                'Value' => $values['Set_Encryption'] == null ? array('tls', 'ssl') : $values['Set_Encryption'] == 'tls' ? array('tls', 'ssl') : array('ssl', 'tls'),
                'Type' => 'Select',
                'Label' => 'Wybierz sposób połączenia',
                'Required' => true
            ),
            '3' => array(
                'Name' => 'Set_Port',
                'Value' => $values['Set_Port'] == null ? '465' : $values['Set_Port'],
                'Type' => 'Text',
                'Label' => 'Podaj port dla SMTP serwera pocztowego',
                'Required' => true
            ),
            '4' => array(
                'Name' => 'Set_Mail_Username',
                'Value' => $values['Set_Mail_Username'] == null ? 'default' : $values['Set_Mail_Username'],
                'Type' => 'Text',
                'Label' => 'Podaj swój login do konta e-mailowego',
                'Required' => true
            ),
            '5' => array(
                'Name' => 'Set_Mail_Password',
                'Value' => '',
                'Type' => 'Password',
                'Label' => 'Podaj swoje hasło do konta e-mailowego',
                'Required' => false
            ),
            
            '6' => array(
                'Name' => 'Avatar_Width',
                'Value' => $values['Avatar_Width'] == null ? 'default' : $values['Avatar_Width'],
                'Type' => 'Text',
                'Label' => 'Podaj maksymalną szerokość avatara (w px)',
                'Required' => true
            ),
            
            '7' => array(
                'Name' => 'Avatar_Height',
                'Value' => $values['Avatar_Height'] == null ? 'default' : $values['Avatar_Height'],
                'Type' => 'Text',
                'Label' => 'Podaj maksymalną wysokość avatara (w px)',
                'Required' => true
            ),
        );
        return $this->_config = $config;
    }
    
    public function returnColumn()
    {
        $this->_prepareColumn();
        return $this->_config;
    }
    
    protected function _getCryptKey() {
        return Zend_Registry::get('config')->mcrypt->key;
    }
    
}

?>
