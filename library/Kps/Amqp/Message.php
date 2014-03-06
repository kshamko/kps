<?php
abstract class Kps_Amqp_Message {

    protected $_body;
    protected $_bodyKey = 'body';

    protected $_mandatoryFields = array();

    public function  __construct($message=null) {
        if($message) {
            if(count($this->_mandatoryFields)) {
                try{
                    $message = Zend_Json::decode($message);
                }catch(Exception $e){
                    throw new Kps_Amqp_Message_Exception('message is not in json format');
                }

                foreach($this->_mandatoryFields as $field) {
                    if(!array_key_exists($field, $message)) {
                        throw new Kps_Amqp_Message_Exception($field.' in message is not defined');
                    }else {
                        $fieldParts = explode('_', $field);
                        $method = 'set';
                        foreach($fieldParts as $part) {
                            $method .= ucfirst($part);
                        }
                        $this->$method($message[$field]);
                    }
                }
            }
        }
    }

    public function setBody($body) {
        $this->_body = $body;
        return $this;
    }

    public function getBody() {
        return $this->_body;
    }

    abstract public function get();

}