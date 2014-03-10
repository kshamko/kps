<?php

abstract class Kps_Controller extends Zend_Controller_Action {

    /**
     *
     * @var Kps_Messages
     */
    protected $_messages;
    protected $_config;
    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;
    
    /**
     *
     * @var Zend_Cache_Backend_Memcached
     */
    protected $_cache;

    /**
     *
     * @var Zend_Auth
     */
    protected $_auth;


    public function init() {
        $this->_messages = Kps_Messages::getInstance();
        $this->_cache = Zend_Registry::get('cache');
        
        if ($messages = $this->_messages->getMessages()) {
            $this->view->layout()->messages = $messages;
        }

        $this->_config = Kps_Application_Config::load();
                
        if ($this->_request->isXmlHttpRequest()) {
            $this->view->ajax = true;
            $this->_helper->layout()->disableLayout();
        }
        
        $this->_em = Zend_Registry::get('doctrine_em');
        
        $this->_auth = Zend_Auth::getInstance();
    }

    public function preDispatch() {
        $page = $this->view->navigation()->findActive(($this->view->navigation()->getContainer()));

        if (isset($page['page']) && $this->_request->getParam('module')) {
            $page = $page['page'];
            $this->view->headTitle($page->getLabel());
        }
    }
}