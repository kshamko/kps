<?php
class Kps_Amqp_Message_Producer extends Kps_Amqp_Message{

    public function __construct($message = null){
        $this->_mandatoryFields = array($this->_bodyKey);
        parent::__construct($message);
    }

    public function  get() {
        $message = array(
            $this->_bodyKey => $this->_body
        );

        return Zend_Json::encode($message);
    }
}