<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mailer
 *
 * @author awojtys
 */
class Application_Model_Mailer {
    //put your code here
    public function __construct($data) 
    {
        $mapper = new Application_Model_UsersMapper();
        $config = new Application_Model_Config();
        $configs = $config -> getAllConfig();
        $user = new Application_Model_Users();
        $mapper->findUserData($data['Attribute'], $user);
        $transport = Swift_SmtpTransport::newInstance()
            ->setHost($configs['Host_Name'])
            ->setEncryption($configs['Set_Encryption'])
            ->setPort($configs['Set_Port'])
            ->setUsername($configs['Set_Mail_Username'])
            ->setPassword($configs['Set_Mail_Password']);
 
        //Create mailer
        $mailer = Swift_Mailer::newInstance($transport);

        //Create the message
        $message = Swift_Message::newInstance()
            ->setSubject('Przydzielono Ci nowy ticket')
            ->setFrom(array('szewczenko006@gmail.com' => 'TicketServ'))
            ->setTo($user->getEmail())
            ->setBody('Przydzielono Ci nowy ticket w systemie TicketServ <br />
                Data: ' . $data['Date'] . '<br />
                Autor: ' . $data['Author'] . '<br />
                Tytuł: ' . $data['Title'] . '<br />
                Treść: ' . $data['Ticket_content'] . '<br />
                ', 'text/html');
        //Send the message
        $mailer->send($message);
            
        
        
    }
    
}

?>
