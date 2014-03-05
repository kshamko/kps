<?php

class Kps_Controller_Request_Http extends Zend_Controller_Request_Http {

    public function isPost() {
        
        $params = $this->getParams();

        if(!isset($params[$this->_moduleKey])
                && !isset($params[$this->_controllerKey])
                && !isset($params[$this->_actionKey])) {

            return false;

        }

        return parent::isPost();
    }
}
