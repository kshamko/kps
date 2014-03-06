<?php
class Kps_Amqp_Message_Consumer extends Kps_Amqp_Message{

    protected $_status;
    protected $_statusKey = 'status';

    protected $_reason;
    protected $_reasonKey = 'reason';

    public function __construct($message=null){
        $this->_mandatoryFields = array($this->_statusKey, $this->_reasonKey, $this->_bodyKey);
        parent::__construct($message);
    }

    public function setStatus($status){
        $this->_status = $status;
        return $this;
    }

    public function getStatus(){
        return $this->_status;
    }

    public function setReason($reason){
        $this->_reason = $reason;
        return $this;
    }

    public function getReason(){
        return $this->_reason;
    }

    public function  get() {
        if(!$this->_status){
            throw new Kps_Amqp_Message_Exception('status is not defined');
        }

        if(!$this->_reason){
            throw new Kps_Amqp_Message_Exception('reason is not defined');
        }

        if(!$this->_body){
            //throw new Kps_Amqp_Message_Exception('body is not defined');
        }

        $message = array(
            $this->_statusKey => $this->_status,
            $this->_reasonKey => $this->_reason,
            $this->_bodyKey => $this->_body
        );

        return Zend_Json::encode($message);
    }

}