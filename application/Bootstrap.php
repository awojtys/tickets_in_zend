<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initSwiftMailer()
    {
        require_once 'lib/swift_required.php';
    }
    
    protected function _initView() {
        define("BP", dirname(__FILE__));
        $view = new Zend_View();
        $view->doctype('HTML5');
        $view->HeadTitle('System zgłaszania ticketów');
        $view->headLink()->appendStylesheet('http://' . $_SERVER['HTTP_HOST'] . '/zend/public/css/style.css');
    }

    protected function _initConfig()
    {
        $mcrypt_config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/mcryptkey.ini', $_SERVER['APPLICATION_ENV']); 
        Zend_Registry::set('config', $mcrypt_config);
        return $mcrypt_config;
   }
    
    protected function _initAutoload() {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => dirname(__FILE__),
        ));
        $autoloader->addResourceType('modules', 'module', 'Module_');

        return $autoloader;
    }

    protected function _initAcl() {
        //role
        $acl = new Zend_Acl();

        $acl
                ->addRole(new Zend_Acl_Role('guest'))
                ->addRole(new Zend_Acl_Role('user'), 'guest')
                ->addRole(new Zend_Acl_Role('admin'), 'user');


        //resurces
        $acl
                ->addResource(new Zend_Acl_Resource('index'))
                ->addResource(new Zend_Acl_Resource('ticket'))
                ->addResource(new Zend_Acl_Resource('error'))
                ->addResource(new Zend_Acl_Resource('config'))
                ->addResource(new Zend_Acl_Resource('users'));

        //allows and denial
        $acl
                ->allow(null, array('index', 'error'), null)
                ->allow(null, 'users', array('login'))
                ->allow(null, 'ticket', 'show')
                ->allow('user', 'ticket', null)
                ->allow('user', 'users', array('logout', 'show', 'edit', 'delete'))
                ->deny('user', 'users', array('login', 'register'))
                ->allow('admin', 'users', 'register')
                ->allow(null, null, null);


        Zend_Registry::set('acl', $acl);

        return $acl;
    }

    protected function _initActionHelpers() {
        Zend_Controller_Action_HelperBroker::addHelper(new Module_UserHelper());
    }

    protected function _initRoutering()
    {
        $frontController = $this->bootstrap('frontController');
        $router = $this->getResource('frontController')->getRouter();
        
        $route_config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/route.ini', $_SERVER['APPLICATION_ENV']);                         
        $router->addConfig($route_config, 'routes');
    }
}

